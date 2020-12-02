
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


function obtenerCitasSendNoti( $idPaciente, $Fecha, $Status, $n_citas ){

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
                CAST(n.fecha AS DATE) AS date_send,
                n.from,
                n.to,
                n.message,
                n.estado,
                n.fk_cita ,
                (select count(*) from tab_noti_confirmacion_cita_email nc where nc.fk_noti_email = n.rowid) as noti_confirma , 
                ifnull((select nc.action from tab_noti_confirmacion_cita_email nc where nc.fk_noti_email = n.rowid  limit 1),'') as noti_confirm_status
            FROM tab_notificacion_email n where n.fk_paciente = $idPaciente";

    if(!empty($Fecha))
        $sql .= " and cast(n.fecha as date) between '$fechaInicio' and '$fechafin' ";

    if($Status=='ConfirmadoAsistir')
        $sql .= " and (select nc.action from tab_noti_confirmacion_cita_email nc where nc.fk_noti_email = n.rowid  limit 1) = 'ASISTIR' ";

    if($Status=='ConfirmadoNoAsistir')
        $sql .= " and (select nc.action from tab_noti_confirmacion_cita_email nc where nc.fk_noti_email = n.rowid  limit 1) = 'NO_ASISTIR' ";

    if($Status=='NoConfirmado')
        $sql .= " and ifnull((select nc.action from tab_noti_confirmacion_cita_email nc where nc.fk_noti_email = n.rowid  limit 1),'') = '' ";

    if($n_citas!="")
        $sql .= " and n.fk_cita like '%$n_citas%' ";


    $sql .= " order by n.fecha desc ";
    $sqlTotal = $sql;

    if($start || $length)
        $sql.=" LIMIT $start,$length;";


    #print_r($sql); die();
    $resultTotal = $db->query($sqlTotal);
    $rs = $db->query($sql);

    if($rs&&$rs->rowCount()>0)
    {
        $Total = $resultTotal->rowCount();

        while ($obj = $rs->fetchObject()){

            $row = [];


            if($obj->noti_confirm_status!=""){

                if($obj->noti_confirm_status=='ASISTIR')
                    $confipaciente = "<span class='label text-sm' style='background-color: rgba(30, 132, 73 , 0.9)' >Confirmado por Paciente <b>(Asistir)</b>  </span>";
                if($obj->noti_confirm_status=='NO_ASISTIR')
                    $confipaciente = "<span class='label text-sm' style='background-color: rgba(192, 57, 43, 0.9)' >Confirmado por Paciente <b>(No Asistir)</b>  </span>";

            }else{
                $confipaciente= "<span class='label text-sm' style='background-color: rgba(212, 172, 13, 0.9)' >No confirmado </span>";
            }

            $numCita = "<table>
                            <tr>
                                <td><img src='".DOL_HTTP."/logos_icon/logo_default/cita-medica.ico' alt='' class=' img-rounded' style='width: 25px; height: 25px'>-</td>
                                <td>".(str_pad($obj->fk_cita, 5, "0", STR_PAD_LEFT))."</td>
                            </tr>
                        </table>";

            $row[] = date("Y/m/d", strtotime($obj->date_send));
            $row[] = $confipaciente;
            $row[] = $obj->from;
            $row[] = $obj->to;
            $row[] = $obj->message;
            $row[] = $numCita;

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