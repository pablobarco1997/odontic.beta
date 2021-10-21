<?php



if(isset($_GET['ajaxSend']) || isset($_POST['ajaxSend'])){

    session_start();
    require_once '../../../../config/lib.global.php';
    require_once DOL_DOCUMENT. '/application/config/main.php'; //el main contiene la sesion iniciada
    require_once DOL_DOCUMENT.'/application/config/conneccion_entidad.php'; //Coneccion entidad

    require_once DOL_DOCUMENT.'/application/system/operacion/class/Class.operacion.php';
    $diarioClinico = new operacion($db);

    global $db, $log, $user, $msg_error;

    $accion = GETPOST('accion');

    switch ($accion){

        case "list_transacciones_clinicas":

            $permisos = true;
            if(!PermitsModule("Transacciones Clinicas", "consultar")){ //si el permiso es false se deniega
                $permisos = false;
            }

            if($permisos==true){
                $response = transacciones_clinicas_list();
                $data  = $response['data'];
                $total = $response['total'];
            }else{
                $data  = [];
                $total = 0;
            }

            $output = [
                "data"            => $data,
                "recordsTotal"    => $total,
                "recordsFiltered" => $total

            ];

            echo json_encode($output);
            break;


        case "crear_transaccion":

            $error = "";

            $valor      = GETPOST('valor');
            $operacion  = GETPOST('operacion');
            $descp      = GETPOST('descp');
            $cuenta     = GETPOST('cuenta');
            $cuenta_a   = GETPOST('cuenta_a');
            $datef      = GETPOST('datef');
            $mov        = GETPOST('mov');
            $subaccion  = GETPOST('subaccion');

            if($mov=="I"){
                $ope="Ingreso";
                $tipo_mov   = 1;
            }
            if($mov=="G"){
                $ope="Egreso";
                $valor = ($valor * -1); // si egreso
                $tipo_mov   = 2;
            }

            $items = array();
            $items['cuenta']     = $cuenta;
            $items['cuenta_a']   = $cuenta_a;
            $items['operacion']  = $operacion;
            $items['descp']      = $descp;
            $items['datef']      = $datef;
            $items['mov']        = $mov;
            $items['subaccion']  = $subaccion;

            $items["ope"]        = $ope;
            $items['valor']      = $valor;
            $items['tipo_mov']   = $tipo_mov;

            if(!PermitsModule('Transacciones Clinicas','agregar')){
                $permits = false;
            }else {
                $permits = true;
            }


            if($permits){

                if($subaccion==1){ //transaccion de una sola cuenta cuenta
                    transaccionClinicaCuentas($items, $subaccion);
                }

                if($subaccion==2){ //transaccion entre cuentas | cuenta desde | cuentas hacia
                    transaccionClinicaCuentas($items, $subaccion);
                }

//                $result = $diarioClinico->diarioClinico($datos);
//                if($result<0){
//                    $error = "Ocurrio un error con la Operación";
//                }else{
//                    $id = $db->lastInsertId('tab_ope_diario_admin_clinico_cab');
//                    $descrip_log = "Se ha creado nueva transacción Clinica  | $ope | Cuenta clinica: ".$objectCuenta->nom." | Descripción: ".$descp;;
//                    $log->log($id, $log->crear, $descrip_log, 'tab_ope_diario_admin_clinico_cab');
//                }

            }else{
                $error = "Ud. No tiene permiso para esta Operación";
            }

            $output = [
                "error" => $error,
            ];
            echo json_encode($output);
            break;


        case 'list_transacciones':

            $error = "";
            $data  = [];

            $emitido_date     = GETPOST('emitido_date');
            $valor_trasn      = GETPOST('valor_trasn');
            $cuenta           = GETPOST('cuenta');
            $formap           = GETPOST('formap');


            if(!empty($emitido_date)){
                $emitido_date = explode('-', $emitido_date);
                $date0 = date("Y-m-d", strtotime( $emitido_date[0]));
                $date1 = date("Y-m-d", strtotime( $emitido_date[1]));

                $between = " and cast(t.date_ff as date) between '$date0' and  '$date1' ";
            }else{
                $between="";
            }

            if(!empty($valor_trasn)){
                $valor = " and t.valor like '%$valor_trasn%' ";
            }else
                $valor="";


            if(!empty($formap)){
                $formpag = " and b.rowid = ".$formap;
            }else{
                $formpag = "";
            }

            if(!empty($cuenta)){
                $acuent = " and (t.id_cuenta_ini = $cuenta || t.id_cuenta_fin=$cuenta) ";
            }else{
                $acuent = "";
            }

            if(!PermitsModule('Transacciones Clinicas', 'consultar')){
                $permits = false;
            }else{
                $permits = true;
            }



            $Total          = 0;
            $start          = $_POST["start"];
            $length         = $_POST["length"];

            if($permits){

                $sql = "select 
                        t.numero , 
                        cast(t.date_ff as date) as dateff, 
                        (select concat(c.n_cuenta, ' ',c.name_acount ) from tab_ope_declare_cuentas c where c.rowid = t.id_cuenta_ini) cuenta_ini , 
                        (select concat(c.n_cuenta, ' ',c.name_acount ) from tab_ope_declare_cuentas c where c.rowid = t.id_cuenta_fin) cuenta_fin , 
                        case t.subaccion
                            when 1 then 'Operación transaccion entre una cuenta'
                            when 2 then 'Operación transaccion entre una cuenta dos cuentas'
                        end as accion , 
                        b.nom as f_pago_cobro, 
                        t.desc as descp,
                        t.valor, 
                        t.subaccion, 
                        case 
                            when valor < 0 then 'Egreso' 
                            when valor > 0 then 'Ingreso' 
                        end as mov
                    from 
                        tab_ope_transaccion_clinicas t
                            inner join
                        tab_bank_operacion b on b.rowid = t.fk_payement
                        ";

                $sql .= "where 1 ";
                $sql .= $between;
                $sql .= $valor;
                $sql .= $formpag;
                $sql .= $acuent;

                $sql .= " order by t.rowid desc ";

                $resultTotal = $db->query($sql);
                if($start || $length){
                    $sql.=" LIMIT $start,$length ";
                }
//                print_r($sql); die();

                $Total = $resultTotal->rowCount();
                $result = $db->query($sql);
                if($result){
                    if($result->rowCount()>0){
                        $array = $result->fetchAll(PDO::FETCH_ASSOC);
                        foreach($array as $key => $item){

                            if($item['cuenta_fin']!=0 && $item['subaccion']==2){
                                $account  = "<small style='display: block'>".strtoupper($item['cuenta_ini'])."</small>";
                                $account .= "<hr class='no-margin'>";
                                $account .= "<small style='display: block'>".strtoupper($item['cuenta_fin'])."</small>";
                            }else{
                                $account = "<small style='display: block'>".strtoupper($item['cuenta_ini'])."</small>";
                            }

                            $rows = [];
                            $rows[] = $item['mov']."<small class='text-blue' style='display: block'>".date('Y/m/d', strtotime($item['dateff']))."</small>"."<small style='display: block'>".$item['numero']."</small>";
                            $rows[] = $account."<small class='text-blue' style='display: block'>".$item['f_pago_cobro']."</small>";
                            $rows[] = "<small class='text-blue' style='display: block'>".($item['descp'])."</small>";

                            if($item['subaccion']==1){
                                if((double)$item['valor']<0) //egreso
                                    $rows[] = "<small class='text-sm' style='color: red'>".$item['valor']."</small>";
                                if((double)$item['valor']>=0) //ingreso
                                    $rows[] = "<small class='text-sm' style='color: green'>".$item['valor']."</small>";
                            }
                            //transaccion entre cuentas
                            if($item['subaccion']==2){
                                $rows[] = "<small class='text-sm text-blue text-bold' style='color: green'>".(str_replace('-','',$item['valor']))."</small>";
                            }

                            $data[] = $rows;
                        }
                    }
                }

            }else{
                $error = "Ud. No tiene permiso para consultar Información";
            }


            $output = array(
                "data"            => $data,
                "recordsTotal"    => $Total,
                "recordsFiltered" => $Total,
                "errorTable"      => $error
            );
            echo json_encode($output);
            break;

    }


}


function transacciones_clinicas_list(){

    global $db, $user;

    $total = 0;
    $data  = [];

    $start          = GETPOST('start');
    $length         = GETPOST('length');

    $date_cc    = GETPOST('datecc');
    $formp      = GETPOST('formp');
    $valor      = GETPOST('valor_tc');
    $desc       = GETPOST('desc');
    $cuenta_tc  = GETPOST('cuenta_tc');

    $where = "";
    if(!empty($date_cc)){
        $fecha = array();
        $fecha = explode('-', $date_cc);
        $fech1 = $fecha[0];
        $fech2 = $fecha[1];
        $where = " and cast( d.datec as date) between cast('$fech1' as date) and cast('$fech2' as date) ";
    }if(!empty($formp)){
        $where .= " and b.rowid = ".$formp;
    }if(!empty($valor)){
        $where .= " and d.value like '%$valor%' ";
    }if(!empty($desc)){
        $where .= " and d.label like '%$desc%' ";
    }if(!empty($cuenta_tc)){
        $where .= " and c.rowid = $cuenta_tc ";
    }

    $query = "select 
            d.id_diario_admin_cab , 
            cast( d.datec as date) as date_cc, 
            
            concat(c.n_cuenta ,' ', c.name_acount, ' ',
                if(c.to_caja=1,concat('| Dir. ',c.to_caja_direccion ),'')) as nomb_cuenta,
                 
            d.value, 
            b.nom as operacion,
            d.label , 
            d.estado
        from 
        tab_ope_diario_admin_clinico_det d 
            inner join 
        tab_ope_declare_cuentas c on c.rowid = d.id_cuenta
            inner join 
        tab_bank_operacion b on b.rowid = d.fk_type_payment
        where 1=1 ".$where;

    $total = $db->query($query)->rowCount();

    $query .= " order by d.rowid desc ";
    if($start || $length){
        $query .=" LIMIT $start,$length;";
    }


//    print_r($query); die();
    $result = $db->query($query);
    if($result && $result->rowCount() > 0){
        $fetch = $result->fetchAll(PDO::FETCH_ASSOC);
        foreach ($fetch as $value){

            $anulado = "";

            if($value['estado'] == 'A'){ //estado Activo
                $n_acount = "<small style='display: block'>".$value['nomb_cuenta']."</small> <small style='display: block; color: #0866a5'>".$value['operacion']."</small>";
                $desc = "<small class='' style='color: #0866a5'>".$value['label']."</small>";
            }
            if($value['estado'] == 'N'){ //estado Anulado
                $n_acount = " <strike> <small style='display: block'>".$value['nomb_cuenta']."</small> <small style='display: block; color: #0866a5'>".$value['operacion']."</small> </strike>";
                $desc = " <strike> <small class='' style='color: #0866a5'>".$value['label']."</small> </strike>" ;
                $anulado = "<small style='display: block; color: red; '>Anulado</small>";
            }

            $row = [];
            $row[] = date("Y/m/d", strtotime($value['date_cc'])).$anulado;
            $row[] = $n_acount;
            $row[] = $desc;
            if((double)$value['value']<0){
                $row[] = "<span style='color: red; padding: 6px 12px'>".$value['value']."</span>";
            }else{
                $row[] = "<span style='color: green; padding: 6px 12px'>".$value['value']."</span>";
            }

            $data[] = $row;
        }
    }else{
        $data  = [];
        $total = 0;
    }


    $salida = [
        'data'  => $data,
        'total' => $total
    ];

    return $salida;


}

function transaccionClinicaCuentas($items=array(), $subaccion=""){

    global $db, $log, $user;


    //fetch object de la cuenta
    $objectCuenta = getnombreCuenta($items['cuenta']);

    $numero = $db->query("(SELECT CONCAT('TX_',SUBSTR(CONCAT('000000', CAST(SUBSTR(c.numero, 4) AS SIGNED) + 1), - 6)) secuencial FROM tab_ope_transaccion_clinicas c WHERE c.rowid = (SELECT  MAX(b.rowid) FROM tab_ope_transaccion_clinicas b))");
    if($numero){
        if($numero->rowCount()>0){
            $numero = $numero->fetchObject()->secuencial;
        }else{
            $numero = 'TX_000001';
        }
    }else{
        $numero = 'TX_000001';
    }

    if($subaccion==2){
        //si la subaccion es una transaccion entre cuentas el valor va positivo
        $items['valor'] = str_replace('-','', $items['valor']);
    }


    $array   =  array();
    $array[] =  date("Y-m-d", strtotime($items['datef']));
    $array[] =  $items['cuenta']; //cuenta inicio
    $array[] =  $items['descp'];
    $array[] =  $items['operacion']; //forma de pago/cobro
    $array[] =  $items['valor'];
    $array[] =  $numero;

    $items['numero'] = $numero;

    if($subaccion==2){
        $array[] =  2; //transaccion entre cuentas
    }
    else{
        $array[] =  1; //transaccion de una sola cuenta
    }


    $array[] = (!empty($items['cuenta_a'])==""?0:$items['cuenta_a']);

    $sql     =  "INSERT INTO `tab_ope_transaccion_clinicas` (`date_ff`, `id_cuenta_ini`, `desc`, `fk_payement`, `valor`, `numero`, `subaccion`, `id_cuenta_fin`) VALUES (?,?,?,?,?,?,?,?);";
    $stmt    =  $db->prepare($sql);
    $result  =  $stmt->execute($array);

    if($result){

        $id = $db->lastInsertId("tab_ope_transaccion_clinicas");
        $log->log($id, $log->crear, "Se ha registrado nueva transacción clinica $numero ".$items['ope']." | ".$objectCuenta->nom, "tab_ope_transaccion_clinicas");

        if($subaccion==1){ //operacion de una cuenta
            $result = addDiarioClinicoTransaccion($id, $items, $objectCuenta);
        }

        if($subaccion==2){ //operacion entre cuentas

            $result = addDiarioClinicoTransaccionTXBetAccount($id, $items, $objectCuenta);
        }

        if($result<0){
            $error = "Ocurrio un error con la Operación";
        }else{
            $error       = "";
            $id          = $db->lastInsertId('tab_ope_diario_admin_clinico_cab');
            $descrip_log = "Se ha creado nueva transacción Clinica $numero  | ".($items['ope'])." | Cuenta clinica: ".$objectCuenta->nom." | Descripción: ".$items['descp'];;
            $log->log($id, $log->crear, $descrip_log, 'tab_ope_diario_admin_clinico_cab');
        }

        return $error;

    }else{

        $log->log(0, $log->error, "Ha ocurrido un error con la operación crear Transaccion clinica ".$items['ope']." | Cuenta: ".$objectCuenta->nom, "tab_ope_transaccion_clinicas", $stmt->errorInfo()[2]);
        $error = "Ocurrio un error con la Operación";

        return $error;
    }

//    print_r($datos); die();

}

function addDiarioClinicoTransaccionTXBetAccount($id,$items=array(), $objecttabCuenta){

    global  $db, $user;

    require_once DOL_DOCUMENT.'/application/system/operacion/class/Class.operacion.php';
    $diarioClinico = new operacion($db);
    //agrego la transaccion clinica en el diario clinico
    $datos = array();
    $label            = "Transaccion Entre cuentas | Descripción: ".$items['descp'];
    $datos['label']   = $label;
    $datos['date_c']  = "now()";
    $datos['detalle'] = array();


    //cuenta de -1
    if($items['cuenta']>0){//salida del saldo

        $valor              = ($items['valor'] *-1);
        $datos['detalle'][] = [
            'datec'             => "now()",
            'id_cuenta'         => $items['cuenta'],
            'id_user_author'    => $user->id ,
            'tipo_mov'          => $items['tipo_mov'] , //ingreso
            'amount_ingreso'    => 0, //monto de ingreso a la cuenta
            'amount_egreso'     => (double)$valor ,
            'value'             => $valor ,
            'id_documento'      => $id,
            'tipo_documento'    => 'transacciones_clinicas', //tipo de documento y/o modulo que genero esta transaccion
            'fk_type_payment'   => $items['operacion'], //medio de pago
            'table'             => 'tab_ope_transaccion_clinicas', //informacion opcional para saber a que table pertenece el id_documento
            'label'             => "Egreso: ".$items['descp']." | Cuenta: ".$objecttabCuenta->nom." | ".$items['numero'],
        ];
    }

    //cuenta a +1
    if($items['cuenta_a']>0){//entrada del saldo

        $valor              = $items['valor'];
        $datos['detalle'][] = [
            'datec'             => "now()",
            'id_cuenta'         => $items['cuenta_a'],
            'id_user_author'    => $user->id ,
            'tipo_mov'          => $items['tipo_mov'] , //ingreso
            'amount_ingreso'    => (double)$valor , //monto de ingreso a la cuenta
            'amount_egreso'     => 0 ,
            'value'             => (double)$valor ,
            'id_documento'      => $id,
            'tipo_documento'    => 'transacciones_clinicas', //tipo de documento y/o modulo que genero esta transaccion
            'fk_type_payment'   => $items['operacion'], //medio de pago
            'table'             => 'tab_ope_transaccion_clinicas', //informacion opcional para saber a que table pertenece el id_documento
            'label'             => "Ingreso: ".$items['descp']." | Cuenta: ".$objecttabCuenta->nom." | ".$items['numero'],
        ];
    }


    $result = $diarioClinico->diarioClinico($datos);
    if($result<0){
       return $result;
    }else{
        return "";
    }

}

function addDiarioClinicoTransaccion($id, $items=array(), $objecttabCuenta){

    global  $db, $user;

    require_once DOL_DOCUMENT.'/application/system/operacion/class/Class.operacion.php';
    $diarioClinico = new operacion($db);

    //agrego la transaccion clinica en el diario clinico
    $datos = array();
    $label            = $items['ope']." | Descripción: ".$items['descp'];
    $datos['label']   = $label;
    $datos['date_c']  = "now()";
    $datos['detalle'] = array();

    $datos['detalle'][] = [
        'datec'             => "now()",
        'id_cuenta'         => $items['cuenta'],
        'id_user_author'    => $user->id ,
        'tipo_mov'          => $items['tipo_mov'] , //ingreso
        'amount_ingreso'    => (double)($items['tipo_mov']==1?$items['valor']:0) , //monto de ingreso a la cuenta
        'amount_egreso'     => (double)($items['tipo_mov']==2?$items['valor']:0) ,
        'value'             => $items['valor'] ,
        'id_documento'      => $id,
        'tipo_documento'    => 'transacciones_clinicas', //tipo de documento y/o modulo que genero esta transaccion
        'fk_type_payment'   => $items['operacion'], //medio de pago
        'table'             => 'tab_ope_transaccion_clinicas', //informacion opcional para saber a que table pertenece el id_documento
        'label'             => $label." | Cuenta: ".$objecttabCuenta->nom." | ".$items['numero'],
    ];

    //    print_r($datos); die();

    $result = $diarioClinico->diarioClinico($datos);

    return $result;

}

?>