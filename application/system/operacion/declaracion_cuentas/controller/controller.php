
<?php

if(isset($_GET['ajaxSend']) || isset($_POST['ajaxSend']))
{

    session_start();
    require_once '../../../../config/lib.global.php';
    require_once DOL_DOCUMENT. '/application/config/main.php'; //el main contiene la sesion iniciada
    require_once DOL_DOCUMENT.'/application/config/conneccion_entidad.php'; //Coneccion entidad
    require_once '../class/ClassOperacionCuentasDeclare.php';

    global $db, $log, $user, $msg_error;

    $accion = GETPOST('accion');

    switch ($accion)
    {
        case 'NuevaDeclaracionCuenta';


            $label = "";
            $errores = [];

            $cuenta         = new OperacionCuentasDeclare($db);

            $n_cuenta        = GETPOST('n_cuenta');
            $name_cuenta     = GETPOST('name');
            $description     = GETPOST('description_acount');
            $tipo_operacion  = GETPOST('tipo_operacion');
            $tipoAcoutBC     = GETPOST('tipoAcoutBC');
            $bancoCaja       = GETPOST('cuenta');
            $direccionCaja   = GETPOST('direccionCaja');

            $cuenta->n_cuenta       = $n_cuenta;
            $cuenta->name_cuenta    = strtoupper((string_comillas_delet($name_cuenta)));//eliminar las comillas de las cuentas
            $cuenta->description    = $description;
            $cuenta->tipo_ope       = $tipo_operacion;


            //Cuentas bancos o Cajas
            if($bancoCaja=='cuentas_bancos'){

                $typeBanco = $db->query("select name as name_type from tab_ope_type_bancos_caja where rowid = $tipoAcoutBC")->fetchObject()->name_type;

                if($tipoAcoutBC==3){

                    //caja
                    $cuenta->to_caja = 1;
                    $label = ' Caja '.$typeBanco;
                    $cuenta->to_bancoCaja = $tipoAcoutBC;
                    $cuenta->direccionCaja = $direccionCaja;
                }else{

                    //bancos
                    $cuenta->to_banco=1;
                    $cuenta->to_bancoCaja = $tipoAcoutBC;
                    $label = ' Banco '.$typeBanco.' '.$name_cuenta;
                }
            }

            $validacion = 0;
            $ncuenta = "select n_cuenta from tab_ope_declare_cuentas  where n_cuenta = '$n_cuenta' ";
            $resultNCuenta = $db->query($ncuenta)->rowCount();
            if($resultNCuenta>0){
                $errores[] = '-Numero de Cuenta Repetido';
                $validacion++;
            }

            if($validacion==0){
                $valor = $cuenta->addAcount();

                if($valor != -1){
                    $log->log($valor, $log->crear, 'Se ha creado una Cuenta N.'.$n_cuenta. ' '.$label, 'tab_ope_declare_cuentas');
                }else{
                    $log->log($valor, $log->error, 'Ocurrio un error con la Operación Cuenta N.'.$n_cuenta. ' '.$label, 'tab_ope_declare_cuentas');
                }
            }

            $output = [
               'error' =>  implode('<br>', $errores),
               'validacionError' => $validacion
            ];
            echo json_encode($output);
            break;


        case 'list_all_cuentas':


            if(!PermitsModule('Declarar Cuentas','consultar' ))
                $PermisoConsultar = " 1<>1 ";
            else
                $PermisoConsultar = " 1=1 ";

            $data           = [];

            $date  = GETPOST('datecc');
            $tipo  = GETPOST('tipo');
            $saldo = GETPOST('saldo');

            $Total          = 0;
            $start          = $_POST["start"];
            $length         = $_POST["length"];

            $query = "SELECT 
                        d.rowid as cuenta_id, 
                        d.name_acount, 
                        d.n_cuenta, 
                        d.tipo_operacion, 
                        t.name as tipo, 
                        d.to_banco, 
                        d.to_caja,
                        d.estado, 
                        -- d.saldo_current, 
                        cs.valor, 
                        cast(d.date_c as date) date_c,
                        d.to_caja_direccion,
                        d.to_banco_caja, 
                        d.codigo,
                        d.system
                    FROM
                        tab_ope_declare_cuentas d
                          left join
                        tab_ope_type_bancos_caja t on t.rowid = d.to_banco_caja 
                          left join 
	                    (select concat(c.n_cuenta, ' ', c.name_acount) as nom, sum(round(value,2)) as valor , d.id_cuenta as cuenta_id from  tab_ope_diario_admin_clinico_det d inner join tab_ope_declare_cuentas c on c.rowid = d.id_cuenta group by d.id_cuenta) as cs on cs.cuenta_id = d.rowid     
                        where  ";

            $query .= $PermisoConsultar;

            if(!empty($date)){
                $f1 = trim(explode('-',$date)[0]);
                $f2 = trim(explode('-',$date)[1]);
                $query .= " and cast(d.date_c as date) between '$f1' and '$f2' ";
            }

            if($tipo!=""){
                $query .= " and d.to_banco_caja = $tipo";
            }

            $Total = $db->query($query)->rowCount();
            if($start || $length){
                $query.=" LIMIT $start,$length;";
            }

            $result = $db->query($query);
            if($result){
                if($result->rowCount()>0){

                    while ($object = $result->fetchObject()){

                        $description = strtoupper((($object->to_caja==1)?$object->to_caja_direccion:''));

                        $row = [];
                        $row[] = strtoupper($object->name_acount)."".(($object->to_caja_direccion!="")?" <small style='display: block; color: #2C7BBA' class='text-sm'><b>Dirección caja: </b>$object->to_caja_direccion</small>":"");
                        $row[] = strtoupper($object->tipo);
                        $row[] = $object->tipo_operacion;
                        $row[] = $object->n_cuenta;
                        $row[] = number_format((double)$object->valor, 2, '.', '');

                        if($object->estado == "A"){
                            $row[] = "<label class=\"label\" style=\"background-color: #D5F5E3; color: green; font-weight: bolder\">ACTIVO</label>";
                        }
                        if($object->estado == "E"){
                            $row[] = "<label class=\"label \" style=\"background-color: #FADBD8; color: red; font-weight: bolder\">INACTIVO</label>";
                        }
                        $row[] = "";
                        $row['fetch'] = $object;

                        $data[] = $row;

                    }
                }
            }

            $output = [
                'draw' => $_POST['draw'],
                'data' => $data,
                'recordsTotal'    => $Total,
                'recordsFiltered' => $Total

            ];
            echo json_encode($output);
            break;


        case 'edit_name_cuenta':

            $error = "";
            $cuenta_id  = GETPOST("cuenta_id");
            $nom = GETPOST("nom");
            $nomAnterior = GETPOST("nom_anterior");

            $nom = string_comillas_delet($nom);
            $nomAnterior = string_comillas_delet($nomAnterior);

            if($cuenta_id!=""){
                $query   = "UPDATE `tab_ope_declare_cuentas` SET `name_acount`='".$nom."' WHERE `rowid`= ".$cuenta_id;
                $results = $db->query($query);
                if(!$results){
                    $error = "".$msg_error;
                    $log_error = "query: $query"."  "."\n no ejecuto la query ";
                    $log->log($cuenta_id, $log->error, 'Ocurrio un error con la operacion modificar cuenta'.$nom.' campo de la cuenta', 'tab_ope_declare_cuentas',$log_error);
                }else{
                    $log->log($cuenta_id, $log->modificar, 'se actualizo el nombre de la cuenta '.$nomAnterior." a ".$nom , 'tab_ope_declare_cuentas');
                }
            }

            $output = [
                "results" => array(
                    "error" => $error
                )
            ];
            echo json_encode($output);
            break;


        case 'actualizar_estados_cuenta':

            $error = "";
            $subaccion  = GETPOST("subaccion");
            $cuenta_id  = GETPOST("cuenta_id");
            $nom        = GETPOST("nom");

            if($subaccion=="activar")
                $stado="A";
            if($subaccion=="desactivar")
                $stado="E";

            $que_u = "UPDATE `tab_ope_declare_cuentas` SET `estado`='$stado' WHERE `rowid`= $cuenta_id;";
            $result = $db->query($que_u);
            if(!$result){

                $error = " ".$msg_error;
                $log_error = "query: $que_u"."  "."\n no ejecuto la query ";
                $log->log($cuenta_id, $log->error, 'Ocurrio un error con la operacion cambiar de estado la cuenta'.$nom.' campo de la cuenta', 'tab_ope_declare_cuentas',$log_error);

            }else{

                $log->log($cuenta_id, $log->modificar, 'Se actualizo la cuenta '.$nom.' a estado '.$subaccion.' campo de la cuenta', 'tab_ope_declare_cuentas');

            }

            $output = [
                "results" => array(
                    "error" => $error
                )
            ];
            echo json_encode($output);
            break;

        default:
            print_r('Ocurrio un error no se reconoce la accion de la Operación');  die();
            break;

    }

}


?>