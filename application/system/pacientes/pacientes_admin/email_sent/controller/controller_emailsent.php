
<?php


if(isset($_GET['ajaxSend']) || isset($_POST['ajaxSend']))
{
    session_start();
    include_once '../../../../../config/lib.global.php';
    require_once DOL_DOCUMENT .'/application/config/main.php';
    global  $db , $conf;

    $accion = GETPOST('accion');

    switch ( $accion )
    {

        case 'list_mail_sent':

            $data  = [];

            $Fecha       = !empty(GETPOST('fecha'))?explode('-',GETPOST('fecha')):"";
            $idPaciente  = GETPOST('idpaciente');
            $status      = GETPOST('status');
            $n_citas     = GETPOST('n_citas');

            $resultado = obtenerCitasSendNoti( $idPaciente, $Fecha, $status, $n_citas );

            $output = array(
                "data"            => $resultado['datos'],
                "recordsTotal"    => $resultado['total'],
                "recordsFiltered" => $resultado['total']
            );

            echo json_encode($output);

            break;


        case 'anular_program_email':

            $error   = "";
            $success   = "";
            $id_noti = GETPOST('id');

            $query = "SELECT 
                 e.estado, e.program, e.program_date
                ,cd.fk_estado_paciente_cita as estado_cita
                ,cd.rowid as id_cita
            FROM
                tab_notificacion_email e
                inner join
                tab_pacientes_citas_det cd on cd.rowid = e.fk_cita
            WHERE
                e.rowid = ".$id_noti ." order by e.rowid desc limit 1";

//            print_r($query); die();
            $result = $db->query($query);
            if($result){
                if($result->rowCount()==1){

                    $object = $result->fetchObject();

                    //E-mail de confirmación Programado
                    //se verifica que la cita se encuentre en el estado 11 para poder liberarla
                    if($object->estado_cita == 11){
                        if($object->estado == 'P'){
                            $valor = $db->query("UPDATE `tab_notificacion_email` SET `estado`='E' WHERE `fk_cita`='$object->id_cita';");
                            if($valor){
                                $success = "ok";
                            }
                            $valor = $db->query("UPDATE `tab_pacientes_citas_det` SET `fk_estado_paciente_cita`='2' WHERE `rowid`='$object->id_cita';");
                            if($valor){
                                $success = "ok";
                            }
                        }
                    }
                }
            }

            $output = [
                'error' => $error,
                'success' => $success
            ];
            echo json_encode($output);
            break;
    }
}


function obtenerCitasSendNoti($idPaciente, $Fecha, $Status, $n_citas){

    global  $db;

    if(!PermitsModule('E-mail Asociados', 'consultar')){
        $permits = " and 1<>1 ";
    }else{
        $permits = " and 1=1 ";
    }

    $fechaInicio  = "";
    $fechafin     = "";
    if($Fecha!=""){
        $fechaInicio = str_replace("/","-",$Fecha[0]);
        $fechafin    = str_replace("/","-",$Fecha[1]);
    }

    $Total          = 0;

    $start          = $_POST["start"];
    $length         = $_POST["length"];

    $data = [];

    $sql = "SELECT
                n.rowid as id_noti, 
                CAST(n.fecha AS DATE) AS date_send,
                n.from,
                n.to,
                n.message,
                n.estado,
                n.fk_cita ,
                n.program_date,
                n.program, 
                if(program=1 && estado='P', if(cast(now() as date) > cast(program_date as date),'Caducado','Pendiente'),'') as DateProgramEmail,
                (select count(*) from tab_noti_confirmacion_cita_email nc where nc.fk_noti_email = n.rowid) as noti_confirma , 
                ifnull((select nc.action from tab_noti_confirmacion_cita_email nc where nc.fk_noti_email = n.rowid  limit 1),'') as noti_confirm_status, 
                cast(concat(cast(n.program_date as date),' 23:00:00') as datetime) as program_date, 
                cast(now() as datetime) as dateNow
            FROM tab_notificacion_email n where n.fk_paciente = $idPaciente";

    if(!empty($Fecha))
        $sql .= " and cast(n.fecha as date) between '$fechaInicio' and '$fechafin' ";

    if($Status=='ConfirmadoAsistir'){
        $sql .= " and (select nc.action from tab_noti_confirmacion_cita_email nc where nc.fk_noti_email = n.rowid  limit 1) = 'ASISTIR' ";
    }

    if($Status=='ConfirmadoNoAsistir'){
        $sql .= " and (select nc.action from tab_noti_confirmacion_cita_email nc where nc.fk_noti_email = n.rowid  limit 1) = 'NO_ASISTIR' ";
    }

    if($Status=='NoConfirmado'){
        $sql .= " and ifnull((select nc.action from tab_noti_confirmacion_cita_email nc where nc.fk_noti_email = n.rowid  limit 1),'') = '' ";
    }

    if($n_citas!=""){
        $sql .= " and n.fk_cita like '%$n_citas%' ";
    }

    $sql .= $permits; //asignar permiso consultar

    $sql .= " order by n.fecha desc ";
    $sqlTotal = $sql;

    if($start || $length){
        $sql.=" LIMIT $start,$length;";
    }


    #print_r($sql); die();
    $resultTotal = $db->query($sqlTotal);
    $rs = $db->query($sql);

    if($rs&&$rs->rowCount()>0)
    {
        $Total = $resultTotal->rowCount();

        while ($obj = $rs->fetchObject()){

            $row = [];
            //se valida la fecha programada < fecha actual ejemplo : 2021-06-02 < 2021-06-03 se cumple la condicion
            // solo asta las 23Horas
            if($obj->estado == 'P' && $obj->program == 1){
                if( date("Y-m-d H:m:s", strtotime($obj->program_date)) < date("Y-m-d H:m:s", strtotime($obj->dateNow)) ){
                    $db->query("UPDATE `tab_notificacion_email` SET `estado`='A' , `program`= 0 WHERE `rowid`=".$obj->id_noti);
                }
            }

            $arrayStatus = array('text' => '' , 'color' => '');

            if($obj->noti_confirm_status!=""){

                if($obj->noti_confirm_status=='ASISTIR'){
                    $arrayStatus['color'] = "color: #0E6655";
                    $arrayStatus['text']  = "Paciente ha confirmado que asistirá a la cita";
                }
                if($obj->noti_confirm_status=='NO_ASISTIR'){
                    $arrayStatus['color'] = "color: #C0392B";
                    $arrayStatus['text']  = "Paciente ha confirmado que no asistirá a la cita";
                }

            }else if($obj->estado == 'P' && $obj->DateProgramEmail == 'Pendiente'){ //si esta programada

                $arrayStatus['color'] = "color: #CA6F1E";
                $arrayStatus['text']  = "Email Pendiente estado programado ".(date("Y/m/d", strtotime($obj->program_date)));

            }else{

                $arrayStatus['color'] = "color: #9A7D0A";
                $arrayStatus['text']  = "Email enviado. No Confirmado";
            }

            $numCita = "<b>C_".(str_pad($obj->fk_cita, 5, "0", STR_PAD_LEFT))."</b>";
            $statusEmail = "<label class=\"\" style=\" ".$arrayStatus['color']." ;font-weight: bolder; font-size:1.3rem \">".$arrayStatus['text']."</label>";

            $row[] = date("Y/m/d", strtotime($obj->date_send))."<div style='display: block'>$statusEmail</div>";
            $row[] = $obj->from;
            $row[] = $obj->to;
            $row[] = $obj->message;
            $row[] = $numCita;
            $row[] = "";
            $row["id_noti"] = $obj->id_noti;
            $row["program"] = $obj->program;
            $row["estado"]  = $obj->estado;

            $data[] = $row;

        }
    }

    $resultFinal = [
        'datos' => $data,
        'total' => $Total
    ];

    return $resultFinal;

}

?>