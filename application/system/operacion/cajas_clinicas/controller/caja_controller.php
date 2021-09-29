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


            $users        = GETPOST('users');
            $apertura     = GETPOST('apertura');
            $cierre       = GETPOST('cierre');
            $estado       = GETPOST('estado');
            $acumulado    = GETPOST('acumulado');

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

        case 'recaudacion_planestratamiento':

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
                    round(g.monto, 2) as monto
                from tab_ope_cajas_det_gastos g
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
                            $query_a = "UPDATE tab_ope_cajas_clinicas_det SET `date_cierre`= now(), estado = 'C' WHERE id_ope_caja_cab = '$id_ope_caja';";
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
        pg.n_fact_boleta
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
    WHERE
        c.estado <> 'E'
        and d.id_ope_caja_cab = ".$id_ope_caja.
            " and cast(d.date_apertura as date)  = '$date_apertura' ";

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

                $row = [];
                $row[]  = date("Y/m/d", strtotime($value['date_emitido_cobro']));
                $row[]  = $value['paciente'];
                $row[]  = $value['n_tratamiento']."<small style='display: block; color: #337ab7' >Doc. #: ".$value['n_fact_boleta']."</small>";
                $row[]  = $value['prestacion_servicio'];
                $row[]  = $value['medio_pago'];
                $row[]  = number_format($value['amount'], 2,'.','');
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
            $label            = "Cierre de caja: ".strtoupper($fetch->label_caja);
            $datos['label']   = $label;
            $datos['date_c']  = "now()";
            $datos['detalle'] = array();




            //Plan de tratamiento
            $sql_a = "select * from tab_ope_cajas_clinicas_det where id_ope_caja_cab = ".$fetch->id_ope_caja;
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
                            'label'             => $item->label,
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
                'label'             => 'Cierre de caja: '.$fetch->label_caja .' '.date("Y/m/d H:m:s"),
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


?>