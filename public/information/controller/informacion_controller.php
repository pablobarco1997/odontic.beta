<?php


if(isset($_POST['dbname']))
{
    require_once '../../../application/config/lib.global.php';
    include_once DOL_DOCUMENT .'/public/information/conneccion/connection_info.php';
    require_once DOL_DOCUMENT .'/application/controllers/controller.php';
    require_once DOL_DOCUMENT .'/application/config/conneccion_entidad.php'; //connecion a todas las entidades

    global  $db;

    $dbtoken  = trim(decomposeSecurityTokenId($_POST['dbname']));
    $dbentity = $_POST['dbentity'];

    $QUERY   = "select nombre_db_entity, numero_entity from tab_entidades_dental where md5(nombre_db_entity) = '$dbtoken' and md5(numero_entity)='$dbentity' ";
    $RESULT  = CONECCION_ENTIDAD::CONNECT_ENTITY()->query($QUERY)->fetchObject();

    $db = connection( trim($RESULT->nombre_db_entity) );

//    print_r($db);  die();
    if(isset($_POST['ajaxSend']) || isset($_GET['ajaxSend']))
    {

        $Datequery    = "SELECT NOW() dateCurrent";
        $DateNow = $db->query($Datequery)->fetch_object()->dateCurrent;

        $accion = GETPOST('accion');

        switch (  $accion  )
        {

            case 'asistir_confim':

//                die();
                $error = '';
                $iddetcita      = GETPOST('idcita'); #id de la cita detalle
                $action_cita    = GETPOST('action_cita');
                $Token_id       = GETPOST('token_id');

                //se valida el formulario antes de validar el proceso
                $Token_valid = $db->query("SELECT count(*) as validcount  FROM tab_noti_token_confirmacion where token = '$Token_id' and  fk_cita_agendada = $iddetcita ")->fetch_object()->validcount;
                if($Token_valid!=1){
                    $error = 'Formulario de confirmación expirado';
                }

                if($iddetcita != '' && $iddetcita > 0 && empty($error))
                {
                    $data_citas = [];
                    $errores = [];

                    $obtenerCita = "SELECT 
                                    d.rowid,
                                    d.fecha_cita,
                                    d.fk_estado_paciente_cita,
                                    c.fk_paciente,
                                    d.fk_cita_email_noti
                                FROM
                                    tab_pacientes_citas_det d,
                                    tab_pacientes_citas_cab c
                                WHERE
                                    d.fk_pacient_cita_cab = c.rowid
                                    and d.rowid = $iddetcita limit 1";
                    $rsCita      = $db->query($obtenerCita);
                    if($rsCita && $rsCita->num_rows>0)
                    {

                        $obcita = $rsCita->fetch_object();

                        #FECHAS ASIGNADAS FECHA DE LA CITA ACTUAL
                        $FechaCitas  = $obcita->fecha_cita;
                        $FechaActual = $DateNow;
                        $noti_confirmacion_email = $obcita->fk_cita_email_noti;

                        $puedoConfirmar = false;
                        $sql = "";

                        #en esta validacion de fecha toma encuenta FECHA Y HORA
                        if( $FechaActual <= $FechaCitas  )
                        {
                            #echo '<pre>';  print_r( $FechaCitas .' >= '. $DateNow ); die();
                            if( $obcita->fk_estado_paciente_cita == 1 )
                            {
                                #SE ACTUALIZA A ESTADO CONFIRMADO X PACIENTE EMAIL
                                $sqlUpdatConfirmPacient = "UPDATE `tab_pacientes_citas_det` SET `fk_estado_paciente_cita` = 10 WHERE `rowid`= $iddetcita ;";
                                $rs = $db->query($sqlUpdatConfirmPacient);
                                if(!$rs)
                                {
                                    $error = 'Ocurrio un error intentelo de nuevo';
                                }

                                if($rs){

                                    if($iddetcita!="")
                                    {

                                        $consult = " SELECT rowid FROM tab_noti_confirmacion_cita_email where fk_cita = $iddetcita";
                                        $rsulConsult = $db->query($consult);
                                        if( $rsulConsult )
                                        {

                                            #El paciente Confirma la cita (si en caso asiste ) o no (no asiste)
                                            if($rsulConsult->num_rows > 0)
                                            {
                                                $sqlUpdatConfirmPacient = " UPDATE `tab_noti_confirmacion_cita_email` SET `date_confirm`= now(), `estado`='10', `fecha_cita`= '$FechaCitas', `comment`='', `action`='".$action_cita."' WHERE `rowid` > 0 and fk_cita = $iddetcita ";
                                                $rsConfirm = $db->query($sqlUpdatConfirmPacient);

                                                if( $rsConfirm ){
                                                    #
                                                }

                                                if(!$rsConfirm){
                                                    $error = 'Ocurrio un error con la Operacion - <b> No puede confirmar esta cita por favor intentarlo mas tarde </b> ';
                                                }

                                            }else{
                                                $error = 'No puede confirmar esta Cita';
                                            }

                                        }else{

                                            $error = 'Ocurrio un error con la Operacion';

                                        }
                                    }
                                }

                            }elseif ( $obcita->fk_estado_paciente_cita == 10 ){

                                $error = 'Ya se encuentra confirmada esta cita';

                            } else{

                                $error = 'Ya no puede confirmar esta cita, La cita se encuentra con un estado diferente';
                            }

                        }else{

                            $error = 'Ya no puede confirmar esta cita la fecha ya se encuentra <b>Atrasada</b>';
                        }

                    }else{
                        $error = 'Ocurrio un error no puede confirmar esta cita';
                    }
                }

                $output = [
                    'error' => $error,
                ];

                echo json_encode($output);
                break;
        }
    }


}else{
    echo json_encode(['error' => 'Ocurrio un error']);
}




?>