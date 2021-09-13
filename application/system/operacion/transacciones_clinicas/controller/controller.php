<?php



if(isset($_GET['ajaxSend']) || isset($_POST['ajaxSend'])){

    session_start();
    require_once '../../../../config/lib.global.php';
    require_once DOL_DOCUMENT. '/application/config/main.php'; //el main contiene la sesion iniciada
    require_once DOL_DOCUMENT.'/application/config/conneccion_entidad.php'; //Coneccion entidad

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

    }


}


function transacciones_clinicas_list(){

    global $db, $user;

    $total = 0;
    $data  = [];

    $start          = GETPOST('start');
    $length         = GETPOST('length');


    $query = "select 
            d.id_diario_admin_cab , 
            cast( d.datec as date) as date_cc, 
            
            concat(c.n_cuenta ,' ', c.name_acount, ' ',
                if(c.to_caja=1,concat('| Dir. ',c.to_caja_direccion ),'')) as nomb_cuenta,
                 
            case
                when d.tipo_mov = 1 then round(d.amount_ingreso, 2) 
                when d.tipo_mov = 2 then round((d.amount_egreso* -1), 2) 
            end  as value , 
            b.nom as operacion,
            d.label 
        from 
        tab_ope_diario_admin_clinico_det d 
            inner join 
        tab_ope_declare_cuentas c on c.rowid = d.id_cuenta
            inner join 
        tab_bank_operacion b on b.rowid = d.fk_type_payment
        where 1=1 ";

    $total = $db->query($query)->rowCount();

    $query .= " order by d.rowid desc ";
    if($start || $length){
        $query .=" LIMIT $start,$length;";
    }


    $result = $db->query($query);
    if($result && $result->rowCount() > 0){
        $fetch = $result->fetchAll(PDO::FETCH_ASSOC);
        foreach ($fetch as $value){
            $row = [];
            $row[] = date("Y/m/d", strtotime($value['date_cc']));
            $row[] = "<small class='' style='color: #0866a5'>".$value['nomb_cuenta']."</small>";
            $row[] = $value['operacion'];
            $row[] = "<small class='' style='color: #0866a5'>".$value['label']."</small>";
            if((double)$value['value']<0){
                $row[] = "<span style='color: red; font-weight: bold;padding: 6px 12px'>".$value['value']."</span>";
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

?>