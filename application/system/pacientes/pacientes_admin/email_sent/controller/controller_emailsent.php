
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
    }
}


function obtenerCitasSendNoti($idPaciente, $Fecha, $Status, $n_citas){

    global  $db;


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

            if($obj->noti_confirm_status!=""){

                if($obj->noti_confirm_status=='ASISTIR')
                    $confipaciente = "<label class=' text-sm' style='background-color: rgba(30, 132, 73 , 0.9); margin-top: 3%; padding: 5px' >Confirmado por Paciente <b>(Asistir)</b>  </label>";

                if($obj->noti_confirm_status=='NO_ASISTIR')
                    $confipaciente = "<label class='text-sm' style='background-color: rgba(192, 57, 43, 0.9);margin-top: 3%; padding: 5px' >Confirmado por Paciente <b>(No Asistir)</b>  </label>";

            }else if($obj->estado == 'P' && $obj->DateProgramEmail == 'Pendiente'){ //si esta programada
                $confipaciente= "<label class=' text-sm' style='background-color: rgba(218, 98, 74, 0.8);margin-top: 3%; padding: 5px' >Pendiente Programado &nbsp; ".(date("Y/m/d", strtotime($obj->program_date)))."</label>";

            }else{
                $confipaciente= "<label class='text-sm' style='background-color: rgba(212, 172, 13, 0.9);margin-top: 3%; padding: 5px' >No confirmado </label>";
            }

            $numCita = "<table>
                            <tr>
                                <td><img src='data:image /*; base64, ". base64_encode(file_get_contents(DOL_DOCUMENT.'/logos_icon/logo_default/cita-medica.ico'))."' alt='' class=' img-rounded' style='width: 25px; height: 25px'>-</td>
                                <td>".(str_pad($obj->fk_cita, 5, "0", STR_PAD_LEFT))."</td>
                            </tr>
                        </table>";

            $row[] = date("Y/m/d", strtotime($obj->date_send));
            $row[] = $confipaciente;
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