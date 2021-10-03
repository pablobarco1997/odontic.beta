<?php

if(isset($_GET['ajaxSend']) || isset($_POST['ajaxSend']))
{
    session_start();
    require_once '../../../../config/lib.global.php';
    require_once DOL_DOCUMENT. '/application/config/main.php'; //el main contiene la sesion iniciada
    require_once DOL_DOCUMENT.'/application/config/conneccion_entidad.php'; //Coneccion entidad

    global $db, $log, $user, $messErr;

    $accion = GETPOST('accion');

    switch ($accion){

        case 'fetchGastos':

            $fetch = array();
            $error = "";

            $sql = "select rowid as id , nom as text from tab_ope_gastos_nom ";
            $result = $db->query($sql);
            if($result){
                $fetch = $result->fetchAll(PDO::FETCH_ASSOC);
            }

            $output = array(
                'error' => $error,
                'fetch' => $fetch,
            );

            echo json_encode($output);
            break;


        case 'buscar_caja_clinica_ape':

            $search  = GETPOST('buscar');

//            print_r($search); die();

            if($search!=""){
                $search = " and concat(n_cuenta, ' ',name_acount) like '%".($search)."%' ";
            }else{
                $search="";
            }
            $data=[];
            $sql_a = "Select s.usuario, cj.rowid as  id_ope_caja, c.rowid as id_cuenta , c.n_cuenta , c.name_acount, c.to_caja_direccion, cj.date_cierre
                        From tab_ope_declare_cuentas as c 
                            inner join
                        (Select * From tab_ope_cajas_clinicas cj ) as cj on cj.id_caja_cuenta = c.rowid 
							inner join 
						(select s.rowid , s.usuario from tab_login_users s) as s on s.rowid = cj.id_user_caja
                    Where c.to_caja = 1 
                      and cj.date_cierre is null
                      ".$search."
                    ";
//            print_r($sql_a); die();
            $result_account_ape_caja = $db->query($sql_a);
            if($result_account_ape_caja){
                if($result_account_ape_caja->rowCount()>0){
                    $all = $result_account_ape_caja->fetchAll(PDO::FETCH_ASSOC);
                    foreach ($all as $value){
                        $label = $value['n_cuenta'] .' '.$value['name_acount'].' Dirección: '.$value['to_caja_direccion']." Usuario: ".$value['usuario'];
                        $data[] = array( "id" =>$value['id_ope_caja'], "text" => $label) ;
                    }
                }
            }

            $output = [
                'results'  => $data
            ];
            echo json_encode($output);
            break;

        case 'GuardarCategoria':

            $error  = "";
            $nomb   = GETPOST('nom');
            $id     = GETPOST('id');
            $return_id = "";

            if($id==""){ //nuevo
                $array  = array($nomb);
                $sql    = "INSERT INTO `tab_ope_gastos_nom` (`nom`) VALUES (?);";
                $stmt   = $db->prepare($sql);
                $result = $stmt->execute($array);
                if($result){
                    $idlast     = $db->lastInsertId('tab_ope_gastos_nom');
                    $return_id  = $idlast;
                    $log->log($idlast, $log->crear, 'Se ha registrado nuevo Gasto Clinico: '.$nomb, 'tab_ope_gastos_nom');
                }else{
                    $log->log(0, $log->error, 'Ocurrió un error con el registro del Gasto '.$nomb, 'tab_ope_gastos_nom');
                    $error = "Ocurrió un error con el registro del Gasto $nomb";
                }
            }else{//modificar
                $array  = array($nomb, $id);
                $sql    = "UPDATE `tab_ope_gastos_nom` SET `nom`= ? WHERE `rowid`= ?;";
                $stmt   = $db->prepare($sql);
                $result = $stmt->execute($array);
                if($result){
                    $idlast = $id;
                    $return_id = $id;
                    $log->log($idlast, $log->modificar, 'Se ha Modificado el registro. Gasto Clinico: '.$nomb, 'tab_ope_gastos_nom');
                }else{
                    $desc = 'Ocurrió un error con el registro del Gasto '.$nomb.' (Operación Modificar)';
                    $log->log(0, $log->error, $desc, 'tab_ope_gastos_nom');
                    $error = $desc;
                }

            }

            $output = array(
                'error' => $error,
                'return_id' => $return_id
            );
            echo json_encode($output);
            break;


        case 'delete_categ_gastos':

            $error = "";
            $id = GETPOST('id');

            //valido si no esta asociado algun documento
            $sql_a = "select count(id_nom_gastos) as id_nom_gastos from tab_ope_gastos_clinicos where id_nom_gastos = $id";
            $result_a = $db->query($sql_a);
            if($result_a && $result_a->rowCount()>0){ //hay gastos asociados
                $idCount =  $result_a->fetchObject()->id_nom_gastos;
                $error = "";

                //No esta asociado
                //Gasto Eliminado
                if($idCount==0){
                    $result = $db->query("DELETE FROM `tab_ope_gastos_nom` WHERE `rowid` = $id;");
                }else{
                    $error = "No tiene permitido eliminar. Este gasto se encuentra asociado a un Documento";
                }

            }else{
                $error = "No se encontro datos Asociados. Compruebe la Información";
            }

            $output = array(
                'error' => $error,
            );
            echo json_encode($output);
            break;

        case 'GuardarGastosClinicos':

            $error  = "";
            $id     = GETPOST('id');

            $datos['categoria']         = GETPOST('categoria');
            $datos['detalleGastos']     = GETPOST('detalleGastos');
            $datos['date_facture']      = GETPOST('date_facture');
            $datos['date_pago']         = GETPOST('date_pago');
            $datos['asociar_caja']      = GETPOST('asociar_caja');
            $datos['monto_gastos']      = GETPOST('monto_gastos');
            $datos['medio_pago_gastos'] = GETPOST('medio_pago_gastos');
            $datos['fk_acount']         = GETPOST('fk_acount');
            $datos['otras_accion']      = GETPOST('otra_accion');


//            die();

            if ($id==""){ //nuevo

                if(!PermitsModule("Gastos", "agregar")){
                    $permits = false;
                }else{
                    $permits = true;
                }
                if($permits==true){
                    $result =  GuardarGastos($id, $datos, true, false);
                }else{
                    $error = "Ud. No tiene permiso para esta Operación";
                }
            }else{
                if(!PermitsModule("Gastos", "modificar")){
                    $permits = false;
                }else{
                    $permits = true;
                }
                if($permits==true){
                    if(is_int($id)){ //modificar
                        $result =  GuardarGastos($id, $datos, false, true);
                    }
                }else{
                    $error = "Ud. No tiene permiso para esta Operación";
                }
            }

            if(!empty($result))
                $error = $result;

            $output = array(
                'error' => $error,
            );
            echo json_encode($output);
            break;

        case 'listCuentasGastos':

            if(!PermitsModule("Gastos", "consultar")){
                $permits = " 1<>1";
            }else{
                $permits = " 1=1 ";
            }

            $data = array();

            $Total          = 0;
            $start          = $_POST["start"];
            $length         = $_POST["length"];

            $emitido    = GETPOST("emitido");
            $facture    = GETPOST("facture");
            $pago       = GETPOST("pago");
            $estado     = GETPOST("estado");
            $cuenta     = GETPOST("cuenta");  //cuenta caja

            $filtro = "";
            if($emitido!=""){
                $emitido = explode('-', $emitido);
                $date_one = date('Y-m-d', strtotime($emitido[0]));
                $date_two = date('Y-m-d', strtotime($emitido[1]));
                $filtro .= " and cast(gc.tms as date) between '$date_one' and '$date_two'  ";
            }if($facture!=""){
                $facture = explode('-', $facture);
                $date_one = date('Y-m-d', strtotime($facture[0]));
                $date_two = date('Y-m-d', strtotime($facture[1]));
                $filtro .= " and cast(gc.date_facture as date) between '$date_one' and '$date_two'  ";
            }if($pago!=""){
                $pago = explode('-', $pago);
                $date_one = date('Y-m-d', strtotime($pago[0]));
                $date_two = date('Y-m-d', strtotime($pago[1]));
                $filtro .= " and  cast(gc.date_facture as date) between '$date_one' and '$date_two'  ";
            }if($estado!=""){
                $filtro .= " and  gc.estado = '$estado' ";
            }if($cuenta!=""){
                $filtro .= " and cgc.id_caja_cuenta = '$cuenta' ";
            }

            $sql_ab = "
                    SELECT 
                        *, gc.rowid AS id
                         , CASE
                                when gc.estado = 'P' then 'PENDIENTE'
                                when gc.estado = 'E' then 'ANULADO'
                                when gc.estado = 'A' then 'GENERADO'
						   END as estado_gasto 
						   ,concat('CJA_', lpad('0',(5-length(cgc.id_ope_caja)),'0'),cgc.id_ope_caja) as n_abierta_caja
						   ,concat(dc.n_cuenta,' ',dc.name_acount) as acount_name
                    FROM
                        (SELECT * FROM tab_ope_gastos_clinicos n) AS gc
                            INNER JOIN
                        (SELECT m.rowid, m.nom FROM tab_ope_gastos_nom m) AS m ON m.rowid = gc.id_nom_gastos
                            LEFT JOIN
						tab_ope_declare_cuentas dc on dc.rowid = gc.fk_acount
                            LEFT JOIN 
                        ( select cg.id_ope_caja, cg.id_gasto , dc.n_cuenta , dc.name_acount , dc.to_caja_direccion ,  c.id_caja_cuenta , 
							 (select u.usuario from tab_login_users u where u.rowid = c.id_user_caja) as usuario  FROM
							 tab_ope_cajas_det_gastos cg
							  inner join 
							 tab_ope_cajas_clinicas c on c.rowid = cg.id_ope_caja 
							  inner join 
							 tab_ope_declare_cuentas dc on dc.rowid = c.id_caja_cuenta ) 
                         cgc on cgc.id_gasto = gc.rowid";

            $sql_ab .= " where ".$permits.$filtro ;
            $sql_ab .= " order by gc.rowid desc ";

            $Total = $db->query($sql_ab)->rowCount();
            if($start || $length){
                $sql_ab.=" LIMIT $start,$length;";
            }

//            print_r($sql_ab);
            $result_ab = $db->query($sql_ab);
            if($result_ab){
                $array = $result_ab->fetchAll(PDO::FETCH_ASSOC);
                foreach ($array as $key => $item){

                    if($item["name_acount"]){ //name de las cuentas caja
                        $nom = $item['n_cuenta']." ".$item["name_acount"]." ".$item['to_caja_direccion']. " | <i class='fa fa-user'></i> ".$item['usuario']." | ".$item['n_abierta_caja'];
                        $caja = " <small class='text-blue' style='display: block'>".$nom."</small>";
                    }else{
                        $caja = "";
                    }

                    $cuentaGasto = "<small class='text-blue' style='display: block' >".$item['acount_name']."</small>";


                    if($item['estado']=='A')
                        $stadoGastos = "<span class=\"text-sm\" style=\"background-color: #D5F5E3; color: green; font-weight: bolder; padding: 1px 5px\">".$item['estado_gasto']."</span>";
                    if($item['estado']=='E')
                        $stadoGastos = "<span class=\"text-sm\" style=\"background-color: #FADBD8; color: red; font-weight: bolder; padding: 1px 5px\">".$item['estado_gasto']."</span>";
                    if($item['estado']=='P')
                        $stadoGastos = "<span class=\"text-sm\" style=\"background-color: #f6e944; color: #b88b29; font-weight: bolder; padding: 1px 5px\">".$item['estado_gasto']."</span>";

                    $rows = array();
                    $rows[] = date("Y/m/d", strtotime($item['tms']));
                    $rows[] = $item['nom'].$caja.$cuentaGasto;
                    $rows[] = "<span class='text-sm' style='color: #0866a5; display: block'> ".$item['desc']." </span>";
                    $rows[] = date("Y/m/d", strtotime($item['date_facture']));
                    $rows[] = (($item['date_payent']=="0000-00-00") ? "" :date("Y/m/d", strtotime($item['date_payent'])));
                    $rows[] = number_format($item['amount'], 2, '.', '');
                    $rows[] = $stadoGastos;
                    $rows['id']      = $item['id'];
                    $rows['estado']  = $item['estado'];
                    $rows['to_caja'] = $item['on_caja_clinica'];
                    $data[] = $rows;
                }
            }

            $output = array(
                "data"            => $data,
                "recordsTotal"    => $Total,
                "recordsFiltered" => $Total
            );
            echo json_encode($output);
            break;

        case 'anularGasto':

            $error = "";

            //se verifica que tipo de gasto es si de caja o por fecha de pago
            // de caja
            // de fecha de pago

            //Y se varifica la fecha de pago

            $id = GETPOST('id');

            $query = "select * from tab_ope_gastos_clinicos  where rowid = $id";
            $result = $db->query($query);

            if($result ){
                if($result ->rowCount()>0){

                    $object   = $result->fetchObject();
                    $id_gasto = $object->rowid;

                    //Si es diferente a gasto Generado
                    //O anulado
                    if($object->estado!='G' && $object->estado!='E'){

                        //caja
                        if($object->on_caja_clinica == 1){
                            //valido caja Para Eliminar Gasto
                            $estado = $db->query("select cg.id_ope_caja, cg.id_gasto, cg.estado from tab_ope_cajas_det_gastos cg where  cg.rowid > 0 and  cg.id_gasto = $id_gasto")->fetchObject()->estado;
                            $error = "";
                            if($estado=='A'){ //Caja Abierta
                                $result_a = $db->query("UPDATE `tab_ope_cajas_det_gastos` SET `estado`='E' WHERE rowid>0 and `id_gasto`= $id_gasto;");
                                if($result_a){
                                    $log->log($id_gasto, $log->eliminar, 'Se ha anulado un registro desde caja. ( Gasto Clinico id b64: ' .base64_encode($id_gasto).' )', 'tab_ope_cajas_det_gastos');
                                }
                            }else if ($estado == 'C'){ //Caja cerrada
                                $error = "Esta caja Se encuentra Cerrada no puede anular el gasto";
                            }else {
                                $error = "";
                            }

                            if(empty($error)){
                                $result_b = $db->query("UPDATE `tab_ope_gastos_clinicos` SET `estado`='E' WHERE `rowid`='$id_gasto';");
                                if($result_b){
                                    $log->log($id_gasto, $log->eliminar, 'Se ha anulado un registro desde Modulo de Gastos Clinicos. ( Gasto Clinico id b64: ' .base64_encode($id_gasto).' )', 'tab_ope_cajas_det_gastos');
                                }
                            }

                        }
                        else{ // gasto asignado por fecha de pago

                            //no puede anular el gasto si mi fecha actual es menor o igual a la de pago
                            $date_payment = date("Y-m-d", strtotime($object->date_payent));

//                            if( date("Y-m-d", strtotime($object->date_payent)) >= date("Y-m-d") ){
//                                $result_c = $db->query("UPDATE `tab_ope_gastos_clinicos` SET `estado`='E' WHERE `rowid`='$id_gasto';");
//                                if($result_c){
//                                    $log->log($id_gasto, $log->eliminar, 'Se ha anulado un registro desde Modulo de Gastos Clinicos. ( Gasto Clinico id b64: ' .base64_encode($id_gasto).' )', 'tab_ope_cajas_det_gastos');
//                                }
//                            }else{
//                                $error = "No puede anular el gasto. La fecha actual no puede ser mayor a la de pago ";
//                            }

                            $result_c = $db->query("UPDATE `tab_ope_gastos_clinicos` SET `estado`='E' WHERE `rowid`='$id_gasto';");

                        }

                    }else{
                        if($object->estado=='G')
                            $error="Gasto se encuentra Generado";
                        if($object->estado=='E')
                            $error="Gasto se encuentra Anulado";
                    }

                }else{
                    $error = "No hay datos";
                }
            }else{
                $error = "No hay datos. Consulte con Soporte";
            }



            $output = array(
                "error"            => $error,
            );
            echo json_encode($output);
            break;


        case 'GenerarGasto':


            //no puede generar un gasto si el gasto proviene de una caja
            //no puede generar el gasto si mi fecha actual es menor o igual a la de pago

            $error = "";
            $id = GETPOST('id');
            $result = $db->query("select * from tab_ope_gastos_clinicos where rowid = $id");
            if($result){
                if($result->rowCount()>0){
                    $object   = $result->fetchObject();
                    $id_gasto = $object->rowid;
                    $caja     = $db->query("select count(*) as caja  from tab_ope_cajas_det_gastos where id_gasto=".$id_gasto)->fetchObject()->caja;

                    if($caja!=0){
                        $error = "No puede generar un gasto de caja. Este se genera una vez que la caja haya cerrada";
                    }else{

                        //mi fecha de pago es meyor o == a la actual
                        if( date("Y-m-d", strtotime($object->date_payent)) >= date("Y-m-d") ){
                            //se Genera el Gasto
                            $result_c = $db->query("UPDATE `tab_ope_gastos_clinicos` SET `estado`='A' WHERE `rowid`='$id_gasto';");
                            if($result_c){
                                $log->log($id_gasto, $log->eliminar, 'Se ha Generado un Gasto registro desde Modulo de Gastos Clinicos. ( Gasto Clinico id b64: ' .base64_encode($id_gasto).' ) | Detalle: '.$object->desc, 'tab_ope_cajas_det_gastos');

                                GenerarGastoDirecto(array(),  $id_gasto);
                            }
                        }else{
                            $error = "No puede Generar el gasto. La fecha actual no puede ser mayor a la de pago ";
                        }
                    }

//                    if($error==""){
//                        $result_c = $db->query("UPDATE `tab_ope_gastos_clinicos` SET `estado`='E' WHERE `rowid`='$id_gasto';");
//                        if($result_c){
//                            $log->log($id_gasto, $log->eliminar, 'Se ha anulado un registro desde Modulo de Gastos Clinicos. ( Gasto Clinico id b64: ' .base64_encode($id_gasto).' )', 'tab_ope_cajas_det_gastos');
//                        }
//                    }

                }else {
                    $error = "No hay Datos";
                }
            }else{
                $error = $messErr;
            }

            $output = array(
                "error"            => $error,
            );
            echo json_encode($output);
            break;

    }

}


function GuardarGastos($id, $datos,  $create=false,  $update=false){

    global $db, $messErr, $log;

    $id_medpago_gastos       = $datos['medio_pago_gastos']; //medio de pago para modulo de gastos
    $id_nom_gastos           = $datos['categoria'];  //el nombre del gasto de la categoria
    $desc                    = $datos['detalleGastos'];
    $date_facture            = date('Y-m-d', strtotime($datos['date_facture']));
    if(!empty($datos['date_pago'])){
        $date_pago           = date('Y-m-d', strtotime($datos['date_pago']));
    }else{
        $date_pago           = "000-00-00";
    }
    $asociar_caja            = $datos['asociar_caja'];
    $monto_gastos            = $datos['monto_gastos'];
    $fk_acount               = $datos['fk_acount']; //fk cuenta gasto

    if($asociar_caja!=""){
        $on_caja = 1;
    }else{
        $on_caja = 0;
    }

//    print_r($on_caja); die();
    if($create){

        $array = array($id_nom_gastos, $desc, $monto_gastos, $date_facture, $date_pago, $on_caja, $id_medpago_gastos, $fk_acount);
        $sql_a  = "INSERT INTO `tab_ope_gastos_clinicos`(`id_nom_gastos`,`desc`,`amount`,`date_facture`,`date_payent`,`on_caja_clinica`, `fk_medio_pago`, `fk_acount`)";
        $sql_a .= " VALUES (?, ?, ?, ?, ?, ?, ?, ?) ";
        $stmt   = $db->prepare($sql_a);
        $result = $stmt->execute($array);
        if($result){
            $idlast = $db->lastInsertId('tab_ope_gastos_clinicos');
            if($on_caja!=0){ // si tiene asociado caja
                guardarEnCajaGastos($idlast, $datos);
            }
            if($datos['otras_accion']=='G'){ //se genera un gasto directo desde Gasto
                if($datos['fk_acount']!=""){ //cuenta de gasto
                    $result_bx = GenerarGastoDirecto($datos, $idlast);
                    if(!empty($result_bx)){
                        return $result_bx;
                    }
                }else{
                    return $messErr;
                }
            }
            $log->log($idlast, $log->crear, 'Se ha creado nuevo Gasto Clinico Detalle: '.$desc, 'tab_ope_gastos_clinicos');
            return "";
        }else{
            $log->log(0, $log->crear, 'Ocurrió un error con la creación del registro. Gasto Clinico Detalle: '.$desc, 'tab_ope_gastos_clinicos', $stmt->errorInfo()[2]);
            //ocurrio un error con la operacion
            return $messErr;
        }

    }

    if($update){
        $sql_b  = "  UPDATE `tab_ope_gastos_clinicos` ";
        $sql_b .= "  SET ";
        $sql_b .= " `id_nom_gastos`     = $id_nom_gastos, "; //el nombre del gasto de la categoria
        $sql_b .= " `desc`              = '$desc', ";
        $sql_b .= " `fk_medio_pago`       = '$id_medpago_gastos', ";
        $sql_b .= " `amount`            = $monto_gastos, ";
        $sql_b .= " `date_facture`      = $date_facture, ";
        $sql_b .= " `date_payent`       = $date_pago ,";
        $sql_b .= " `on_caja_clinica`   = $on_caja , ";
        $sql_b .= " `fk_acount`         = $fk_acount ";
        $sql_b .= " WHERE `rowid`       = $id; ";
        $result_b = $db->query($sql_b);
        if($result_b){
            $log->log($id, $log->modificar, 'Se ha Actualizado el registro Gasto Clínico: '.$desc, 'tab_ope_gastos_clinicos');
            return "";
        }else{
            return $messErr;
        }

    }

    return "";

}

function guardarEnCajaGastos($id, $fetch){

    global $db, $messErr, $log;
    require_once DOL_DOCUMENT .'/application/system/operacion/class/Class.operacion.php';

    $datos['id_ope_caja']   = $fetch['asociar_caja']; //id caja estado abierta (tab_ope_cajas_clinicas)
    $datos['id_categoria']  = $fetch['categoria'];
    $datos['detalle']       = $fetch['detalleGastos'];
    $datos['monto']         = $fetch['monto_gastos'];
    $datos['medio_pago_gastos'] = $fetch['medio_pago_gastos'];
    $datos['id_gasto']      = $id;

    $ope = new operacion($db);

    //ingreso a caja el gasto
    $result = $ope->new_trasaccion_caja_gastos($datos);
    if($result == -1){
        return "Ocurrio un error con la Operación. Consulte con soporte";
    }else{
        return "";
    }

}



function GenerarGastoDirecto($fetch = array(), $id){


    global $db, $user;
    require_once DOL_DOCUMENT .'/application/system/operacion/class/Class.operacion.php';

    $datos['label']   = "Gasto Clinico Generado";
    $datos['date_c']  = "now() ";
    $datos['detalle'] = array();

    //Busco por id el gasto que se va a generar y realizar un movimiento
    $sql_a = "select 
            gc.fk_medio_pago , 
            gc.fk_acount , 
            round(gc.amount, 2) as monto, 
            concat('Gasto Clinico Generado | Detalle de Gasto: ',gc.desc,' | ',' ',n.nom	) as label 
        from 
            tab_ope_gastos_clinicos gc
            inner join 
            tab_ope_gastos_nom n on n.rowid = gc.id_nom_gastos
        where gc.rowid = ".$id ." limit 1 ";
    $result = $db->query($sql_a)->fetchObject();
    $desc = $result->label;

    if(!empty($desc)){
        $datos['detalle'][] = [
            'datec'             => "now()",
            'id_cuenta'         => $result->fk_acount,   //cuenta del systema
            'id_user_author'    => $user->id ,
            'tipo_mov'          => 1 , //ingreso
            'amount_ingreso'    => $result->monto, //monto de ingreso a la cuenta
            'amount_egreso'     => 0 ,
            'id_documento'      => $id,
            'tipo_documento'    => 'Gasto Clinicos', //tipo de documento y/o modulo que genero esta transaccion
            'fk_type_payment'   => (double)$result->fk_medio_pago, //medio de pago id
            'table'             => 'tab_ope_gastos_clinicos', //informacion opcional para saber a que table pertenece el id_documento
            'label'             => $desc,
            'value'             => $result->monto

        ];

        $operacion = new operacion($db);
        $result_b =  $operacion->diarioClinico($datos);

        if($result_b<0){
            return "Ocurrió un error con la Operación, Consulte con Soporte";
        }else{
            if($fetch['otras_accion']=="G"){ //se genera desde el modulo de GASTOS
                $result_e = $db->query("UPDATE `tab_ope_gastos_clinicos` SET `estado`='A' WHERE `rowid`='$id';");
                if($result_e){

                }
            }
        }
        return "";
    }else{
        return "Ocurrio un error con la Operacion Generar Gasto";
    }

}



?>