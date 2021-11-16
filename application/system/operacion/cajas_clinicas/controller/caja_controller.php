<?php

if(isset($_GET['ajaxSend']) || isset($_POST['ajaxSend'])){

    session_start();
    require_once '../../../../config/lib.global.php';
    require_once DOL_DOCUMENT. '/application/config/main.php'; //el main contiene la sesion iniciada
    require_once DOL_DOCUMENT.'/application/config/conneccion_entidad.php'; //Coneccion entidad

    global $db, $log, $user, $msg_error;

    $accion = GETPOST('accion');


    switch ($accion){


        case 'buscar_caja_clinica_ape':

            $search  = GETPOST('buscar');
            $search  = string_comillas_delet($search);

//            print_r($search); die();

            if($search!=""){
                $search = " and concat(n_cuenta, ' ',name_acount) like '%$search%' ";
            }else{
                $search="";
            }
            $data=[];
            $query = "select rowid as id_cuenta , n_cuenta , name_acount, to_caja_direccion from tab_ope_declare_cuentas where to_caja = 1 ".$search." limit 10";
            $result_account_ape_caja = $db->query($query);
            if($result_account_ape_caja){
                if($result_account_ape_caja->rowCount()>0){
                    $all = $result_account_ape_caja->fetchAll(PDO::FETCH_ASSOC);
                    foreach ($all as $value){
                        $label = $value['n_cuenta'] .' '.$value['name_acount'].' Dirección: '.$value['to_caja_direccion'];
                        $data[] = array( "id" =>$value['id_cuenta'], "text" => $label) ;
                    }
                }
            }

            $output = [
                'results'  => $data
            ];
            echo json_encode($output);
            break;


        case 'apertura_caja':

//            sleep(4);
            $error = "";
            $id_cuenta_caja = GETPOST('id_cuenta_caja');
            $id_user_caja   = GETPOST('id_user_caja');
            $saldoInicial   = GETPOST('saldoInicial');

            $datos['id_cuenta_caja'] = $id_cuenta_caja;
            $datos['id_user_caja'] = $id_user_caja;
            $datos['saldoInicial'] = $saldoInicial;

            //validador la do controlador
            $valid = 0;

            $usuario_en_uso = $db->query("select count(*) as valid from tab_ope_cajas_clinicas where id_user_caja = $id_user_caja and (date_cierre is null)")->fetchObject()->valid;
            if($usuario_en_uso != ""){
                if($usuario_en_uso != 0){ //usuario ya asignado una caja
                    $error = "Este usuario ya se encuentra asignada a una caja";
                }
            }

            /*
            $caja_abierta = $db->query("select count(*) as valid from tab_ope_cajas_clinicas where id_caja_cuenta = '$id_cuenta_caja' and estado = 'A'")->fetchObject()->valid;
            if($caja_abierta != ""){
                if($caja_abierta != 0){ //usuario ya asignado una caja
                    $error = "Actualmente Caja Abierta\n";
                }
            }*/

            if($error==""){
                $error = nueva_apertura_caja($datos);
            }

            $output = [
                'results'  => array(
                    'error' => $error,
                )
            ];
            echo json_encode($output);
            break;


        case 'list_cajas_abiertas':

            $permitData = false;
            if(!PermitsModule('Cajas Clinicas','consultar')){
                $PermisoConsultar = " 1<>1 ";
                $permitData = false;
            }
            else{
                $PermisoConsultar = " 1=1 ";
                $permitData = true;
            }


            $users              = GETPOST('users');
            $apertura           = GETPOST('apertura');
            $cierre             = GETPOST('cierre');
            $estado             = GETPOST('estado');
            $acumulado          = GETPOST('acumulado');
            $id_tratamiento     = GETPOST('id_ptratamiento');

            $where = "";

            if($apertura!=""){
                $date = explode('-', $apertura);
                $where .= " and cast(c.date_apertura as date) between '".(date('Y-m-d', strtotime($date[0])))."' and '".(date('Y-m-d', strtotime($date[1])))."' ";
            }
            if($cierre!=""){
                $date = explode('-', $cierre);
                $where .= " and cast(c.date_cierre as date) between '".(date('Y-m-d', strtotime($date[0])))."' and '".(date('Y-m-d', strtotime($date[1])))."' ";
            }
            if($acumulado!=""){
                $where .= " and ROUND((ifnull(d.saldo_acumulado, 0) - ifnull(g.monto, 0)), 2) = ROUND($acumulado, 2) ";
            }
            if($users!=""){
                $where .= " and c.id_user_caja = $users";
            }
            if($estado!=""){
                $where .= " and c.estado = '$estado' ";
            }

            if($id_tratamiento!=""){
                $InnerJoinTratamiento = " 
                        inner join 
                    (select tdm.id_ope_caja_cab from tab_ope_cajas_clinicas_det as tdm where tdm.fk_plan_tratam_cab = $id_tratamiento group by tdm.id_ope_caja_cab) as tdm on tdm.id_ope_caja_cab = c.rowid";
            }else{
                $InnerJoinTratamiento = "";
            }

            $Total          = 0;
            $start          = $_POST["start"];
            $length         = $_POST["length"];

            $data = [];
            $estado = GETPOST('estado');

            $query  = "SELECT 
                    c.rowid as id_ope_caja, 
                    c.rowid, 
                    concat(dc.n_cuenta, ' ', dc.name_acount) as cuenta, 
                    dc.to_caja_direccion, 
                    c.id_caja_cuenta,
                    c.date_registro,
                    c.date_apertura,
                    c.date_cierre,
                    c.id_user_caja,
                    us.usuario,
                    c.saldo_inicial, 
                    ifnull(g.monto, 0) as montoGasto, 
                    ifnull(d.saldo_acumulado, 0) as saldo_acumulado, 
                    ifnull(g.monto, 0) as montoGasto, 
                    (ifnull(d.saldo_acumulado, 0) - ifnull(g.monto, 0)) as total
                FROM
                      tab_ope_cajas_clinicas c
                    left join
                      (select ifnull(round(sum(g.monto),2), 0) as monto , g.id_ope_caja as id_ope_caja_gst from tab_ope_cajas_det_gastos g where 1=1 and g.estado <> 'E' group by g.id_ope_caja) as g on g.id_ope_caja_gst = c.rowid
                    left join
                      (select ifnull(round(sum(d.amount), 2), 0) as saldo_acumulado, id_ope_caja_cab from tab_ope_cajas_clinicas_det d where d.estado <> 'E' group by d.id_ope_caja_cab) as d on d.id_ope_caja_cab = c.rowid
                    inner join
                       tab_ope_declare_cuentas dc on dc.rowid = c.id_caja_cuenta
                    inner join 
                       tab_login_users us on us.rowid = c.id_user_caja 
                    ".$InnerJoinTratamiento."
                    where  ".$PermisoConsultar." ".$where;
            $query .= " order by c.rowid desc";
//            print_r($query); die();
            $Total = $db->query($query)->rowCount();
            if($start || $length){
                $query.=" LIMIT $start,$length;";
            }

            $result = $db->query($query);
            if($result){
                if($result->rowCount()>0){
                    $array_datos = $result->fetchAll(PDO::FETCH_ASSOC);
                    foreach ($array_datos  as $item) {

                        $secu = str_pad($item['id_ope_caja'], 5, "0", STR_PAD_LEFT);
                        $secu = "<small style='display: block;' class='text-blue' title='Secuencial CJA_$secu'>CJA_$secu</small>";

                        if($item['to_caja_direccion'] != ""){
                            $to_direccion = "Dir.".$item['to_caja_direccion'];
                        }else{
                            $to_direccion = ""; 
                        }

                        /*
                        //consultar saldo actual
                        $saldo_actual = 0;
                        $sql = "select round(sum(d.amount), 2) as saldo_acumulado from tab_ope_cajas_clinicas_det d where cast(date_apertura as date) = cast('".date("Y-m-d", strtotime($item["date_apertura"]))."' as date)  and id_ope_caja_cab = ".$item['id_ope_caja']."  ";
                        $resultsald = $db->query($sql);
                        $saldo_acumulado_caja = $resultsald->fetchObject()->saldo_acumulado;
                        //se resta el saldo de gastos de caja
                        $saldo_acumulado_caja += (($item['total']) * -1); */

                        $list = [];
                        $list[] = $item['usuario'];
                        $list[] = strtoupper($item['cuenta'])." | <small style='display: inline-block' class='text-sm text-blue' >".$to_direccion."</small>".$secu;
                        $list[] = date('Y/m/d  H:m:s', strtotime($item['date_apertura']));

                        if($item['date_cierre']!=""){
                            $list[] = date('Y/m/d  H:m:s', strtotime($item['date_cierre']));
                        }
                        else{
                            $list[] = "";
                        }

//                        $list[] = round("0.00", 2); //saldo anterior
                        $list[] = number_format($item['saldo_inicial'], 2, '.', '');
                        $list[] = number_format($item['total'], 2, '.', '');//acumulado entre el saldo anterior y +  el saldo actual
                        $list[] = "";
                        $list['datos'] = base64_encode(json_encode($item));
//                        print_r($item); die();

                        $data[] = $list;
                    }
                }
            }


            $output = [
                'draw' => $_POST['draw'],
                'data' => $data,
                'recordsTotal'    => $Total,
                'recordsFiltered' => $Total,
                'permiso' => $permitData

            ];
            echo json_encode($output);
            break;



        case 'fetch_recursos_caja':

            $date_apertura = GETPOST("date_apertura");
            $id_ope_caja = GETPOST("id_ope_caja");

            $Saldo_caja_efectivo = Saldo_caja_efectivo(19, 'Efectivo', $id_ope_caja, $date_apertura);
            $Recaudado_caja      = Saldo_recaudado($id_ope_caja, $date_apertura);
            $GastosCaja          = Saldo_Gastos($id_ope_caja);


            $output = [
                'Saldo_caja_efectivo' => number_format($Saldo_caja_efectivo, 2, '.', ''),
                'Recaudado_caja' => number_format($Recaudado_caja, 2, '.', ''),
                'Gastos_caja' => number_format($GastosCaja, 2, '.', ''),
            ];
            echo json_encode($output);

            break;

        case 'lista_recaudaciones':

            $date_apertura = GETPOST("date_apertura");
            $id_ope_caja = GETPOST("id_ope_caja");

            $Total = 0;
            $data = [];

            $respuesta = list_transaccion_caja_tratamientos($id_ope_caja, $date_apertura);

            $Total = $respuesta['total'];

            $output = [
                'draw' => $_POST['draw'],
                'data' => $respuesta['data'],
                'recordsTotal'    => $Total,
                'recordsFiltered' => $Total

            ];
            echo json_encode($output);
            break;

        case 'gastos_list_caja':

            $data = array();
            $id_ope_caja = GETPOST('id_ope_caja');
            $Total = 0;

            $start          = $_POST["start"];
            $length         = $_POST["length"];

            $sql = "select 
                    g.rowid as id_gasto_caja,
                    g.date_cc , 
                    gn.nom, 
                    g.detalle, 
                    gc.date_facture, 
                    b.nom as mediop, 
                    round(g.monto, 2) as monto, 
                    g.rowid  as idcajadet , 
                    c.estado as status_caja_cab
                from 
				   tab_ope_cajas_clinicas c
						inner join 
                   tab_ope_cajas_det_gastos g on g.id_ope_caja = c.rowid 
                      inner join 
                   (select gc.rowid, gc.id_nom_gastos, gc.desc, gc.amount, gc.date_facture, gc.estado from tab_ope_gastos_clinicos gc where gc.on_caja_clinica = 1) as gc on gc.rowid = g.id_gasto
                      inner join
                   tab_ope_gastos_nom gn on gn.rowid = gc.id_nom_gastos
                      inner join 
                   tab_bank_operacion b on b.rowid = g.fk_medio_pago
                where gc.estado <> 'E' and g.id_ope_caja = $id_ope_caja";

            $Total = $db->query($sql)->rowCount();

            if($start || $length){
                $sql .= " limit $start,$length ";
            }

//            print_r($sql)
            $result = $db->query($sql);
            if($result){
                if($result->rowCount()>0){
                    $array = $result->fetchAll(PDO::FETCH_ASSOC);
                    foreach ($array as $item){
                        $fetch = array();
                            $fetch[] = date("Y/m/d", strtotime($item['date_cc'])); //emitido
                            $fetch[] = $item['nom'];
                            $fetch[] = $item['detalle'];
                            $fetch[] = date("Y/m/d", strtotime($item['date_facture']));
                            $fetch[] = $item['mediop'];
                            $fetch[] = $item['monto'];
                            $fetch['idcajadet']       = $item['idcajadet']; //id de caja de gasto
                            $fetch['status_caja_cab'] = $item['status_caja_cab']; //estado de caja cabezera
                        $data[] = $fetch;
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

        case 'cerrar_caja':

            if(!PermitsModule('Cajas Clinicas', 'modificar')){
                $permits = false;
            }else{
                $permits = true;
            }

            $error = "";
            $id_ope_caja = GETPOST('id_ope_caja');

            if($id_ope_caja==""){
                $error = "Ocurrio un error con los parametros de entrada\nConsulte con soporte";
            }
            if($id_ope_caja==0){
                $error = "Ocurrio un error con los parametros de entrada\nConsulte con soporte";
            }

            if($permits==false){
                $error = "Ud. No tiene permiso para esta Operación";
            }

            $question = "";

            if($error==""){
                $query = "SELECT 
                        c.rowid AS id_ope_caja,
                        c.id_caja_cuenta,
                        c.id_caja_cuenta,
                        c.id_user_caja, 
                        CONCAT(d.n_cuenta, ' CAJA  DIRECCIÓN: ', d.to_caja_direccion) AS label_caja,
                        (SELECT  u.usuario FROM tab_login_users u WHERE u.rowid = c.id_user_caja) AS usuario_caja
                    FROM
                        tab_ope_cajas_clinicas c
                            INNER JOIN
                        tab_ope_declare_cuentas d ON d.rowid = c.id_caja_cuenta
                    WHERE
                        c.estado <> 'E' AND c.estado <> 'C'  ";
                $query .= " and c.rowid = ".$id_ope_caja . " limit 1";
                $result = $db->query($query);
                if($result->rowCount()>0){

                    $object_caja = $result->fetchObject();

                    //se valida si no es super administrador
                    $admin = validSuperAdmin($user->users_unique_id);
                    if(!$admin){
                        if($user->id != $object_caja->id_user_caja){
                            $error = "Ud. no tiene permiso para cerrar esta caja<br>Solo usuario asignado o administrador";
                        }
                    }

                    //solo el usuario asignado puede cerrar caja
                    if($error==""){
                        $query_c = "UPDATE tab_ope_cajas_clinicas SET `date_cierre`= now(), estado = 'C' WHERE rowid = '$id_ope_caja';";
                        $resul = $db->query($query_c);
                        if($resul){
                            $valid=0;

                            //planes de tratamiento
                            $query_a = "UPDATE tab_ope_cajas_clinicas_det SET `date_cierre`= now(), estado = 'C' WHERE id_ope_caja_cab = '$id_ope_caja' and estado <> 'E' ;";
                            $resul= $db->query($query_a);
                            if(!$resul){
                                $error = "Ocurrio un error con la Operacion consulte con soporte";
                                $log->log(0, $log->error, 'Ocurrio un error con la operacion cerrar caja detalles .'.$object_caja->label_caja, 'tab_ope_cajas_clinicas_det', $query_c);
                            }else{

                                $log->log($id_ope_caja, $log->modificar, "Se actualizo los registros caja detalles Planes de Tratamientos | ".$object_caja->label_caja." ", 'tab_ope_cajas_clinicas_det');

                                //Gastos Clinicos
                                //se actualiza la tabla gastos Clinicos a estado Generado por que se cerro caja
                                //se valida si el gasto se encuentra anulado no se actualiza a estado GENERADO
                                $sql_ab = "UPDATE tab_ope_gastos_clinicos SET estado='A' WHERE rowid in( (SELECT id_gasto FROM tab_ope_cajas_det_gastos where estado <> 'E' and id_ope_caja = $id_ope_caja) );";
                                $db->query($sql_ab);
                                $valid++;

                                //Si el gasto se encuentra anulado no se realiza ninguna operacion
                                //gastos Clinicos de caja
                                $query_a = "UPDATE `tab_ope_cajas_det_gastos` SET `estado`='C' WHERE `rowid`> 0 and id_ope_caja = $id_ope_caja and estado <> 'E' ;";
                                $resul= $db->query($query_a);
                                if(!$resul){
                                    $error = "Ocurrio un error con la Operacion consulte con soporte";
                                    $log->log(0, $log->error, 'Ocurrio un error con la operacion cerrar caja detalles .'.$object_caja->label_caja, 'tab_ope_cajas_clinicas_det', $query_c);
                                }else{
                                    $log->log($id_ope_caja, $log->modificar, "Se actualizo los registros caja detalles Gastos |".$object_caja->label_caja." ", 'tab_ope_cajas_det_gastos');
                                    $valid++;
                                }
                            }

                            if($valid > 0){
                                $die = operacion_cierre_caja($id_ope_caja);
                            }
                        }else{
                            $error = "Ocurrio un error con la Operacion consulte con soporte";
                        }

                        //create log
                        if($error==""){
                            $descrip = 'Se actualizo registro .Operación CERRAR CAJA .'.$object_caja->label_caja.' .Usuario: '.$object_caja->usuario_caja;
                            $log->log($id_ope_caja, $log->modificar, $descrip, 'tab_ope_cajas_clinicas');
                        }else{
                            $log->log($id_ope_caja, $log->error, 'Ocurrio un error con la operacion cerrar caja .'.$object_caja->label_caja, 'tab_ope_cajas_clinicas', $query_c);
                        }
                    }

                }else {
                    $error = "Ud. no puede cerrar la caja"."\n";
                }
            }

            $output =[
              'error'    => $error ,
              'question' => $question ,
            ];
            echo json_encode($output);
            break;

        case 'anulacion':

            $error = "";
            $id_recaudado                = GETPOST('id_recaudado');
            $datos['id_recaudado']       = $id_recaudado; //id plan de tratamiento
            $datos['id_ope_caja']        = GETPOST('id_ope_caja');
            $datos['idcajadet']          = GETPOST('idcajadet');
            $datos['proceso']            = GETPOST('proceso');


            //validar usuario solo usuario administrado o usuario de caja puede realizar operaciones dentro de una caja
            //se valida si no es super administrador
            $admin = validSuperAdmin($user->users_unique_id);
            if(!$admin){

                $id_user_caja = $db->query("select count(*) count from tab_ope_cajas_clinicas where rowid = ".($datos['id_ope_caja'])." and id_user_caja = ".($user->id)." ")->fetchObject()->count;
                if($id_user_caja==0){ //no tiene permiso para realizar ninguna operacion
                    $error = "Ud. no tiene permiso. Solo usuario administrador o usuario a cargo de la caja";
                }
            }

            if(!PermitsModule('Cajas Clinicas', 'eliminar')){
                $error = "Ud. No tiene permiso para realizar esta operación";
            }

            //anular plan de tratamiento
            if(empty($error)){

                //anulacion de recaudacion | pagos de pacientes plan tratamientos
                if($datos['proceso'] == 'anulacion_recaudacion'){
                    $error  = anulacion_desde_caja($datos);
                }

                //anulacion de gastos
                if($datos['proceso'] == 'anular_gasto'){
                    $error  = anulacion_desde_caja($datos);
                }

            }

            $output =[
                'error'    => $error ,
            ];
            echo json_encode($output);
            break;

    }

}


function nueva_apertura_caja($datos){

    global  $db, $msg_error, $log;

    if(count($datos) == 0){
        return "Ocurrio un error \nparametros de entrada";
    }

    $id_cuenta_caja = $datos['id_cuenta_caja']; //id de la caja cuentas
    $id_user_caja   = $datos['id_user_caja'];
    $saldoInicial   = $datos['saldoInicial'];


    if($id_user_caja!=""){
        if($id_user_caja!=0){
            $sql = "UPDATE `tab_login_users` SET `id_caja_account`= '$id_cuenta_caja' WHERE `rowid`= $id_user_caja; ";
            $result = $db->query($sql);
            if(!$result){
                $error = "Se ha producido un error durante la operación";
                return $error;
            }
        }
    }else{
        $error = "Se ha producido un error durante la operación";
        return $error;
    }

    $query  = "INSERT INTO `tab_ope_cajas_clinicas` (`id_caja_cuenta`, `date_registro`, `id_user_caja`,`date_apertura` , `estado`, saldo_inicial)";
    $query .= " VALUES ";
    $query .= " ( ";
    $query .= " $id_cuenta_caja , ";
    $query .= " now() , ";
    $query .= " $id_user_caja , ";
    $query .= " now() , ";
    $query .= " 'A' ,   ";
    $query .= " round($saldoInicial,2)  ";
    $query .= " ); ";

    $result = $db->query($query);
    if(!$result){
        $errordb = $query;
        $log->log(-1, $log->error, 'Ocurrio un error con la creación de apertura de caja ', 'tab_ope_cajas_clinicas', $errordb);
        $error = $msg_error;
    }else{
        $lastId = $db->lastInsertId('tab_ope_cajas_clinicas');
        $log->log($lastId, $log->crear, 'Se Registro nueva apertura de caja fecha de apertura '.date("Y/m/d"), 'tab_ope_cajas_clinicas');
        $error = '';
    }

    return $error;

}

function Saldo_recaudado($id_ope_caja, $date_apertura){

    global  $db;

    $date_apertura= date("Y-m-d", strtotime($date_apertura));

    $sql = "SELECT 
            round(SUM(d.amount), 2) as amount , 
            b.nom, 
            b.rowid
        FROM
            tab_ope_cajas_clinicas_det d
            inner join
            tab_bank_operacion b on b.rowid = d.fk_tipo_pago
        WHERE
            d.id_ope_caja_cab = $id_ope_caja
            and cast(d.date_apertura as date) = '$date_apertura'
            and d.estado <> 'E'
            limit 1";

    $result = $db->query($sql);
    if($result){
        if($result->rowCount()>0){
            $saldo = $result->fetchObject()->amount;
            $saldo = (double)$saldo;
            return $saldo;
        }else{
            return "0.00";
        }
    }else{

        return "0.00";
    }

}

function Saldo_Gastos($id_ope_caja){

    global  $db;

    $sql = "select 
		round(sum(a.monto), 2) as monto
from
	(select * from tab_ope_cajas_det_gastos a) as a
		left join 
    (select 
		g.rowid as gasto_id , g.date_facture , n.nom as categoria , g.estado, g.on_caja_clinica
	 from 
		tab_ope_gastos_clinicos g , tab_ope_gastos_nom n where n.rowid = g.id_nom_gastos and g.on_caja_clinica = 1
    ) as g on g.gasto_id = a.id_gasto
WHERE 
   a.id_ope_caja = $id_ope_caja
   and a.estado <> 'E' ";

    $result = $db->query($sql);
    if($result){
        if($result->rowCount()>0){
            $saldo = $result->fetchObject()->monto;
            $saldo = (double)$saldo;
            return $saldo;
        }else{
            return "0.00";
        }
    }else{

        return "0.00";
    }

}

function Saldo_caja_efectivo($id_type_pago, $name, $id_ope_caja, $date_apertura){

    global $db;

    $date_apertura= date("Y-m-d", strtotime($date_apertura));

    $query = "SELECT 
            round(SUM(d.amount),2 ) as amount , 
            b.nom, 
            b.rowid
        FROM
            tab_ope_cajas_clinicas_det d
            inner join
            tab_bank_operacion b on b.rowid = d.fk_tipo_pago
        WHERE
            d.id_ope_caja_cab = $id_ope_caja
            and b.nom = '$name' and b.rowid = $id_type_pago
            and cast(d.date_apertura as date)  = '$date_apertura'
            and d.estado <> 'E' 
            limit 1";
//    print_r($query); die();
    $result = $db->query($query);
    if($result){
        if($result->rowCount()>0){
            $saldo = $result->fetchObject()->amount;
            $saldo = (double)$saldo;
            return $saldo;
        }else{
            return "0.00";
        }
    }else{

        return "0.00";
    }

}

function list_transaccion_caja_tratamientos($id_ope_caja, $date_apertura){

    global $db;

    $date_apertura = date("Y-m-d", strtotime($date_apertura));

    $query = "  SELECT 
        pg.fecha AS date_emitido_cobro,
        c.rowid AS id_ope_caja,
        c.id_caja_cuenta,
        c.date_apertura,
        c.date_cierre,
        CONCAT('Plan de Tratamiento', ' N.', td.numero) AS n_tratamiento,
        td.edit_name as edit_name_tratamiento, 
        CONCAT(p.nombre, ' ', p.apellido) AS paciente,
        ps.descripcion AS prestacion_servicio,
        ROUND(d.amount, 2) AS amount,
        b.nom as medio_pago,
        pg.n_fact_boleta,
        d.id_cobro_recaudado,
        d.rowid as idcajadet,
        tdd.fk_diente as Pieza, 
        c.estado as estado_caja_cab, 
        d.estado as estado_caja_det
    FROM
        tab_ope_cajas_clinicas c
            INNER JOIN
        tab_ope_cajas_clinicas_det d ON c.rowid = d.id_ope_caja_cab 
            INNER JOIN
        tab_bank_operacion b ON b.rowid = d.fk_tipo_pago
            INNER JOIN
        tab_plan_tratamiento_cab td ON td.rowid = d.fk_plan_tratam_cab
            INNER JOIN
        tab_admin_pacientes p ON p.rowid = d.fk_paciente
            INNER JOIN
        tab_conf_prestaciones ps ON ps.rowid = d.fk_prestacion_servicio
            INNER JOIN
        tab_pagos_independ_pacientes_cab pg ON pg.rowid = d.fk_pago_cab
            INNER JOIN
		tab_plan_tratamiento_det tdd on tdd.rowid = d.fk_plan_tratam_det
    WHERE
        -- d.estado <> 'E'
        c.estado <> 'E'
        and d.id_ope_caja_cab = ".$id_ope_caja.
            " and cast(d.date_apertura as date)  = '$date_apertura' ";

//    print_r($query); die();
    $start          = GETPOST('start');
    $length         = GETPOST('length');
    $total = $db->query($query)->rowCount();
    if($start || $length){
        $query .= " limit $start,$length";
    }

    $result = $db->query($query);
    if($result){
        if($result->rowCount() > 0){
            $data = [];
            $resultado = $result->fetchAll(PDO::FETCH_ASSOC);

            foreach ($resultado as $k => $value){

//                if($value['edit_name_tratamiento'] != "")
//                    $edit_name_trataminto  = "<span class='text-sm' style='display: block'>".$value['edit_name_tratamiento']."</span>";
//                else
//                    $edit_name_trataminto  = "";

                if(!empty($value['Pieza'])){
                    $Pieza = "<small style='display: block' class='text-blue'>Pieza: ".$value['Pieza']."</small>";
                }else{
                    $Pieza = "";
                }

                //se valida el estado
                if($value['estado_caja_det']=='E'){
                    $Docs_estado = "<strike> <small style='display: block;' class='text-blue' >Doc. #: ".$value['n_fact_boleta']."</small> </strike> <small style='display: block; color: red;'>Detalle Anulado</small>";
                }else{
                    $Docs_estado = "<small style='display: block;' class='text-blue' >Doc. #: ".$value['n_fact_boleta']."</small>";
                }


                $row = [];
                $row[]  = date("Y/m/d", strtotime($value['date_emitido_cobro']));
                $row[]  = $value['paciente'];
                $row[]  = $value['n_tratamiento'].$Docs_estado;
                $row[]  = $value['prestacion_servicio'].$Pieza;
                $row[]  = $value['medio_pago'];
                $row[]  = number_format($value['amount'], 2,'.','');
                $row[]  = "";
                $row['idcobro_recaudado']   = $value['id_cobro_recaudado'];
                $row['idcajadet']           = $value['idcajadet'];
                $row['status_caja_cab']     = $value['estado_caja_cab']; //estado de caja cabezera
                $row['status_caja_det']     = $value['estado_caja_det']; //estado de caja detalle
                $data[] = $row;
            }
        }else{
            $data = [];
        }
    }else{
        $data = [];
    }

    return array(
        'data' => $data,
        'total' => $total
    );


}

//esta operacion saca el saldo que hay en caja y manda a la cuenta 40102 PRESTACIÓN DE SERVICIOS
function operacion_cierre_caja($id_ope_caja){

    global $db, $user;

    $id_cuenta_principal = 0; //id de la cuenta donde se dirige el ingreso  PRESTACIÓN DE SERVICIOS
    $die_errores = new stdClass();
    $die_errores->error    = "";
    $die_errores->question = "";

    //detecto si la cuenta del systema existe
    //PRESTACIÓN DE SERVICIOS
    //40102
    //UFJFU1RBQ0lPTl9ERV9TRVJWSUNJT1M=
    $cons_cuenta_system = $db->query("select rowid as id_cuenta from tab_ope_declare_cuentas where codigo = 'UFJFU1RBQ0lPTl9ERV9TRVJWSUNJT1M=' ");
    if($cons_cuenta_system){
        if($cons_cuenta_system->rowCount()==0){
            $die_errores->error = "Cuenta principal no detectada. Consulte con soporte";
            return $die_errores->error;
        }else{
            $id_cuenta_principal = $cons_cuenta_system->fetchObject()->id_cuenta;
        }
    }else{
        $die_errores->error = "Cuenta principal no detectada. Consulte con soporte";
        return $die_errores->error;
    }

    require_once DOL_DOCUMENT.'/application/system/operacion/class/Class.operacion.php';
    $operacion = new operacion($db);

    //todo caja clinicas asignada con la operacion cerrar caja
    $sql = "SELECT 
            c.rowid AS id_ope_caja,
            c.id_caja_cuenta,
            c.id_user_caja,
            CONCAT(d.n_cuenta, ' CAJA  DIRECCIÓN: ',  d.to_caja_direccion) AS label_caja,
            (SELECT  u.usuario FROM tab_login_users u WHERE u.rowid = c.id_user_caja) AS usuario_caja, 
            c.estado, 
            c.saldo_inicial
        FROM
            tab_ope_cajas_clinicas c
                INNER JOIN
            tab_ope_declare_cuentas d ON d.rowid = c.id_caja_cuenta
        WHERE
            c.estado <> 'E' 
            -- AND c.estado <> 'C'
                AND c.rowid = $id_ope_caja  limit 1";
    $result = $db->query($sql);
    if($result){
        if($result->rowCount()>0){

            //todo se valida el tipo de operacion que se va a realizar sea un ingreso de plan de tratamiento de pacientes o se un gasto o costo
            $fetch            = $result->fetchObject();

            if($fetch->estado=='E'){
                $die_errores->error = 'Operación denegada! Esta caja se encuentra Eliminada';
                return $die_errores->error;
            }

            //se fetch los datos de la cabezera Operacion cierre de caja ' se realiza todas las transacciones una vez se cierra la caja sea ingreso o egreso'
            $amount_caja = 0; //monto de caja
            $label            = "Cierre de caja: ".strtoupper($fetch->label_caja). ' | CJA_'.str_pad($fetch->id_ope_caja,5, "0", STR_PAD_LEFT);
            $datos['label']   = $label;
            $datos['date_c']  = "now()";
            $datos['detalle'] = array();

            //cobros por planes de tratamiento
            //Plan de tratamiento
            $sql_a = "select * from tab_ope_cajas_clinicas_det where id_ope_caja_cab = ".$fetch->id_ope_caja." and estado = 'C' ";
            $result_a = $db->query($sql_a);
            if($result_a){
                if($result_a->rowCount()>0){
                    while ($fetch_a = $result_a->fetchObject()){
                        /*$datos['detalle'][] = [
                            'datec'             => "now()",
                            'id_cuenta'         => $id_cuenta_principal,   //cuenta del systema
                            'id_user_author'    => $user->id ,
                            'tipo_mov'          => 1 , //ingreso
                            'amount_ingreso'    => $fetch_a->amount , //monto de ingreso a la cuenta
                            'amount_egreso'     => 0 ,
                            'id_documento'      => $fetch_a->fk_plan_tratam_det,
                            'tipo_documento'    => 'plan_tratamiento', //tipo de documento y/o modulo que genero esta transaccion
                            'fk_type_payment'   => $fetch_a->fk_tipo_pago, //medio de pago
                            'table'             => 'tab_plan_tratamiento_det', //informacion opcional para saber a que table pertenece el id_documento
                            'label'             => $fetch_a->label." | ".$fetch->label_caja,

                        ];*/
                        $amount_caja += (double)$fetch_a->amount; //Monto que saldra de caja cobros de plan de tratamiento
                    }
                }
            }

            $nomCaja = strtoupper($fetch->label_caja);

            //gastos Clinico desde caja en estado Cerrada
            $sql_b = "select 
                        g.id_gasto,
                        gc.fk_medio_pago , 
                        gc.fk_acount , 
                        round(g.monto, 2) as monto, 
						concat('Gasto Clinico Generado por ".$nomCaja." | Detalle de Gasto ',gc.desc,' | ',' ',n.nom	) as label
                    from 
                        tab_ope_cajas_det_gastos g
                            inner join 
                        tab_ope_gastos_clinicos gc on gc.rowid = g.id_gasto
                            inner join 
                        tab_ope_gastos_nom n on n.rowid = gc.id_nom_gastos
                    where g.estado = 'C' and g.id_ope_caja = ".$fetch->id_ope_caja."";
            $result_b = $db->query($sql_b);
            if($result_b){
                if($result_b->rowCount()>0){
                    while($item = $result_b->fetchObject()){
                        $datos['detalle'][] = [
                            'datec'             => "now()",
                            'id_cuenta'         => $item->fk_acount ,   //cuenta de gasto creada por el usuario
                            'id_user_author'    => $user->id ,
                            'tipo_mov'          => 1 , //ingreso a la cuenta de gastos
                            'amount_ingreso'    => $item->monto , //monto de ingreso a la cuenta de gastos
                            'amount_egreso'     => 0 ,
                            'value'             => $item->monto,
                            'id_documento'      => $item->id_gasto,
                            'tipo_documento'    => 'Gastos_Clinico', //tipo de documento y/o modulo que genero esta transaccion
                            'fk_type_payment'   => $item->fk_medio_pago, //medio de pago
                            'table'             => 'tab_ope_gastos_clinicos', //informacion opcional para saber a que table pertenece el id_documento
                            'label'             => $item->label.' | CJA_'.str_pad($fetch->id_ope_caja,5, "0", STR_PAD_LEFT),
                        ];

                        $amount_caja -= ((double)$item->monto); //Monto que saldra de caja
                    }
                }
            }


            //saldo del egreso
            if(count($datos['detalle'])!=0){
            }

            //detalle principal
            $datos['detalle'][] = [
                'datec'             => "now()",
                'id_cuenta'         => $fetch->id_caja_cuenta,
                'id_user_author'    => $user->id ,
                'tipo_mov'          => 2 , //egreso
                'amount_ingreso'    => 0 , //monto de ingreso a la cuenta
                'amount_egreso'     => $amount_caja ,
                'id_documento'      => $id_ope_caja, //id de la tabla
                'value'             => ($amount_caja * -1),
                'tipo_documento'    => 'cajas_clinicas', //tipo de documento y/o modulo que genero esta transaccion
                'fk_type_payment'   => 3, //tab_bank_operacion id 3  egreso
                'table'             => 'tab_ope_cajas_clinicas', //informacion opcional para saber a que table pertenece el id_documento
                'label'             => 'Cierre de caja: '.$fetch->label_caja .' '.date("Y/m/d H:m:s").' | CJA_'.str_pad($fetch->id_ope_caja,5, "0", STR_PAD_LEFT),
            ];


            //Genera el diario Clinico transaccional
            $result_b =  $operacion->diarioClinico($datos);
            if($result_b<0){
                $die_errores->error = 'Ocurrió un error con la Operación, Consulte con Soporte';
                return $die_errores->error;
            }

        }
    }
    return $die_errores;
}



//anulacion de cobros de planes de tratamiento
// anulacion de Gastos
function anulacion_desde_caja($datos){

    global $db, $user, $log;

    if($datos['id_ope_caja']==""){
        return 'Error parámetros de entrada no detectado. Consulte con soporte';
    }


    /**Anulacion de recaudacion*/
    if($datos['proceso']=='anulacion_recaudacion'){

        if($datos['idcajadet']=="" || $datos['id_recaudado']==""){

            return "Ocurrió un error inesperado con los parametros de entrada consulté con soporte";
        }

        //se comprueba el estado de la caja para la anulacion del registro
        $cerrada = $db->query("select count(*) as count_n from tab_ope_cajas_clinicas where rowid = ".$datos['id_ope_caja']." and estado = 'C' ")->fetchObject()->count_n;
        if($cerrada==1){ //caja cerrada
            return 'Caja se encuentra cerrada no puede realizar esta Operación. Para realizar la anulación de un plan de tratamiento puede dirigirse al módulo de (Pagos Realizados) de dicho paciente ';
        }

        //caja abierta  A
        //consulto solamente los detalles de la estado A
        $sql_a = "SELECT 
                        pg.fecha AS date_emitido_cobro,
                        c.rowid AS id_ope_caja,
                        c.id_caja_cuenta,
                        c.date_apertura,
                        c.date_cierre,
                        CONCAT('Plan de Tratamiento', ' N.', td.numero) AS n_tratamiento,
                        td.edit_name as edit_name_tratamiento, 
                        CONCAT(p.nombre, ' ', p.apellido) AS paciente,
                        ps.descripcion AS prestacion_servicio,
                        ROUND(d.amount, 2) AS amount,
                        b.nom as medio_pago,
                        pg.n_fact_boleta, 
                        d.id_cobro_recaudado,
                        d.rowid as idcajadet,
                        concat(od.n_cuenta,' ',od.name_acount, ' ',od.to_caja_direccion) as nam_cuenta, 
                        b.rowid as idmediopago,
                        d.estado
                        
                        ,d.fk_plan_tratam_cab
                        ,d.fk_plan_tratam_det
                   FROM
                        tab_ope_cajas_clinicas c
                            INNER JOIN
                        tab_ope_cajas_clinicas_det d ON c.rowid = d.id_ope_caja_cab
                            INNER JOIN
                        tab_bank_operacion b ON b.rowid = d.fk_tipo_pago
                            INNER JOIN
                        tab_plan_tratamiento_cab td ON td.rowid = d.fk_plan_tratam_cab
                            INNER JOIN
                        tab_admin_pacientes p ON p.rowid = d.fk_paciente
                            INNER JOIN
                        tab_conf_prestaciones ps ON ps.rowid = d.fk_prestacion_servicio
                            INNER JOIN
                        tab_pagos_independ_pacientes_cab pg ON pg.rowid = d.fk_pago_cab
                            INNER JOIN
                        tab_ope_declare_cuentas od ON od.rowid = c.id_caja_cuenta
                   WHERE
                        d.rowid = ".$datos['idcajadet']." and d.id_cobro_recaudado = ".$datos['id_recaudado']." and d.estado in('A','C') limit 1 ";

        $result_a = $db->query($sql_a);
        if($result_a){
            if($result_a->rowCount()>0){

                $result_ab = $result_a->fetchAll(PDO::FETCH_ASSOC);
                $result_ab = $result_ab[0];

                if(count($result_ab)>0){

                    if($result_ab['estado']=='E'){
                        return 'No puede realizar la anulación verifiqué el estado de la caja';
                    }

                    //Proceso de egreso diario clinico
                    require_once DOL_DOCUMENT.'/application/system/operacion/class/Class.operacion.php';
                    $operacion = new operacion($db);


                    $label            = "Anulación de Documento ".$result_ab['n_fact_boleta']." |  caja: ".strtoupper($result_ab['nam_cuenta']);
                    $datos['label']   = $label;
                    $datos['date_c']  = "now()";
                    $datos['detalle'] = array();

                    $secuencial = ' CJA_'.str_pad($result_ab['id_ope_caja'],5, "0", STR_PAD_LEFT);

                    $datos['detalle'][] = [
                        'datec'             => "now()",
                        'id_cuenta'         => $result_ab['id_caja_cuenta'] ,
                        'id_user_author'    => $user->id ,
                        'tipo_mov'          => 1 ,
                        'amount_ingreso'    => 0,
                        'amount_egreso'     => $result_ab['amount'],
                        'value'             => ($result_ab['amount'] * -1),
                        'id_documento'      => $result_ab['idcajadet'],
                        'tipo_documento'    => 'Gastos_Clinico', //tipo de documento y/o modulo que genero esta transaccion
                        'fk_type_payment'   => $result_ab['idmediopago'], //medio de pago
                        'table'             => 'tab_ope_cajas_clinicas_det', //informacion opcional para saber a que table pertenece el id_documento
                        'label'             => $label." | ".$secuencial." | ".$result_ab['n_tratamiento']." | Paciente: ".$result_ab['paciente']." | Prestacion/Servicio: ".$result_ab['prestacion_servicio'].' | CJA_'.str_pad($datos['id_ope_caja'], 5, "0", STR_PAD_LEFT)
                    ];

//                    print_r($result_ab); die();

                    $result_e = $db->query("UPDATE `tab_ope_cajas_clinicas_det` SET `estado`='E' WHERE `rowid`='".($datos['idcajadet'])."';");
                    if($result_e){

                        $log->log($datos['idcajadet'], $log->eliminar, $datos['detalle'][0]['label'], 'tab_ope_cajas_clinicas_det');

                        //update relacionados
                        //modulos de pagos del paciente
                        //anulacion de pago de paciente de prestaciones de tratamiento

                        $iddetpayment = $result_ab['id_cobro_recaudado'];
                        $result_c     = $db->query("UPDATE `tab_pagos_independ_pacientes_det` SET `estado`='E' WHERE `rowid`='$iddetpayment';");
                        if($result_c){

                            $log->log($iddetpayment, $log->eliminar, 'Anulación de pago de paciente: '.$result_ab['paciente'].' desde la caja: CJA_'.str_pad($datos['id_ope_caja'], 5, "0", STR_PAD_LEFT), 'tab_pagos_independ_pacientes_det');

                            //$result_ope = $operacion->diarioClinico($datos);
                            //se anula el registro del diario clinico directamente a la table
                            $result_ope = $operacion->AnulacionDiDeClinico($result_ab['idcajadet'], 'cajas_clinicas');

                            //una vez realizado la anulacion se comprueba los estados de planes de tratamiento y los detalles
                            $sql_a = "SELECT 
                                        ifnull(SUM(ROUND(a.amount, 2)),0) AS amount , b.total as total_tto 
                                    FROM
                                        tab_pagos_independ_pacientes_det as a  inner join tab_plan_tratamiento_det as b on b.rowid = a.fk_plantram_det 
                                    WHERE
                                        a.fk_plantram_det = ".$result_ab['fk_plan_tratam_det']."
                                        and a.estado = 'A' ";
                            $result_a_detalle = $db->query($sql_a)->fetchObject();
                            if((double)$result_a_detalle->amount==0){
                                //se actualiza a PE
                                $db->query("UPDATE `tab_plan_tratamiento_det` SET `estado_pay`='PE' WHERE `rowid`='".$result_ab['fk_plan_tratam_det']."';");
                            }else{
                                //si el monto no es 0
                                if((double)$result_a_detalle->amount != (double)$result_a_detalle->total_tto){
                                    $db->query("UPDATE `tab_plan_tratamiento_det` SET `estado_pay`='PS' WHERE `rowid`='".$result_ab['fk_plan_tratam_det']."';");
                                } //saldo completo
                                if((double)$result_a_detalle->amount == (double)$result_a_detalle->total_tto){
                                    $db->query("UPDATE `tab_plan_tratamiento_det` SET `estado_pay`='PA' WHERE `rowid`='".$result_ab['fk_plan_tratam_det']."';");
                                }
                            }


                            //se verifica plan de tratamiento cabezera
                            $sql_b = "SELECT 
                                         ifnull(SUM(ROUND(a.amount, 2)),0) AS amount
                                    FROM
                                        tab_pagos_independ_pacientes_det as a inner join tab_plan_tratamiento_det as b on b.rowid = a.fk_plantram_det 
                                    WHERE
                                        a.fk_plantram_cab = ".$result_ab['fk_plan_tratam_cab']." and a.estado = 'A'";
                            $amount_cab_tto = $db->query($sql_b)->fetchObject()->amount;
                            if($amount_cab_tto!=""){
                                if((double)$amount_cab_tto==0){
                                    $db->query("UPDATE `tab_plan_tratamiento_cab` SET `estados_tratamiento`='A' , situacion= 'DIAGNÓSTICO' WHERE `rowid`='".$result_ab['fk_plan_tratam_cab']."';");
                                }
                            }

                            if($result_ope!=-1){
                                return "";
                            }else{
                                return "Ocurrió un error con la Operación | Anulación de caja CJA_".str_pad($datos['id_ope_caja'], 5, "0", STR_PAD_LEFT)." <small>diario Clinico</small>. Consulte con Soporte ";
                            }

                        }else{

                        }
                    }else{
                        return "Ocurrió un error con la Operación anulacion de registro de caja";
                    }

                }else{
                    return "No se encontraron datos del registro. Compruebe el estado del registro";
                }
            }else{
                return "No se encontraron datos del registro. Compruebe el estado del registro";
            }
        }else{
            return "Ocurrió un error con obteniendo los datos. Consulte con Soporte";
        }
    }


    /**Anulacion de Gastos asociados*/
    if($datos['proceso']=='anular_gasto'){

        //se comprueba el estado de la caja para la anulacion del registro
        $cerrada = $db->query("select count(*) as count_n from tab_ope_cajas_clinicas where rowid = ".$datos['id_ope_caja']." and estado = 'C' ")->fetchObject()->count_n;
        if($cerrada==1){ //caja cerrada
            return 'Caja se encuentra cerrada no puede realizar esta Operación';
        }

        $query = "
            SELECT 
                nom.nom , 
                cgd.id_ope_caja, 
                cgd.estado , 
                g.desc,
                g.rowid as id_gasto_cab, 
                cgd.rowid as detalle_caja_gasto 
                ,concat('G_', lpad('0',(5-length(g.rowid)),'0'),g.rowid) as number_gasto
            FROM 
                tab_ope_cajas_clinicas as c 
                  inner join 
                tab_ope_cajas_det_gastos as cgd on cgd.id_ope_caja = c.rowid
                  inner join 
                tab_ope_gastos_clinicos as g on g.rowid = cgd.id_gasto and g.on_caja_clinica = 1
                  inner join 
                tab_ope_gastos_nom as nom on nom.rowid = g.id_nom_gastos
            where cgd.estado in('A','C') and cgd.id_ope_caja =  ".$datos['id_ope_caja']." and cgd.rowid = ".$datos['idcajadet'];

        $result = $db->query($query);
        if($result){
            if($result->rowCount()>0){

                $fetch = $result->fetchAll(PDO::FETCH_ASSOC);
                $fetch = $fetch[0];

                if($fetch['detalle_caja_gasto'] > 0 && $fetch['id_gasto_cab'] > 0){

                    $id_gasto_cab = $fetch['id_gasto_cab'];
                    $detalle_id   = $fetch['detalle_caja_gasto'];

                    //numero de gasto clinico
                    $number_gasto = $fetch['number_gasto'];

                    //numero de caja clinica
                    $number_caja = "CAJ_".str_pad($datos['id_ope_caja'], '5', '0', STR_PAD_LEFT);

                    //modulo cajas gastos detalle
                    $sql_b = "UPDATE `tab_ope_cajas_det_gastos` SET `estado` = 'E' WHERE (`rowid` = '".$detalle_id."');";
                    $result_b = $db->query($sql_b);
                    if($result_b){
                        $log->log($detalle_id, $log->eliminar, 'Se ha actualizado un registro caja clinica '.$number_caja.' detalle Anulación de Gasto: '.$fetch['nom'], 'tab_ope_cajas_det_gastos');
                    }

                    //modulo gastos
                    $sql_a = "UPDATE `tab_ope_gastos_clinicos` SET `estado` = 'E' WHERE (`rowid` = '".$id_gasto_cab."');";
                    $result_a = $db->query($sql_a);
                    if($result_a){
                        $log->log($id_gasto_cab, $log->eliminar, 'Se ha actualizado un registro Modulo de Gastos Anulación de Gasto: '.$fetch['nom'].' | '.$number_gasto, 'tab_ope_cajas_det_gastos');
                    }

                    return "";

                }else{
                    return 'Ocurrio un error con la Operación ';
                }
                
            }else{
                return 'No se detecto registros asociados';
            }
        }else{
            return 'Ocurrio un error Consulte con Soporte';
        }

    }

}



?>