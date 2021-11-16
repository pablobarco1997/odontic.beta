
<?php

class send_email_program{

    var $db;

    //true  servidor remoto
    //false servidor local
    private $cronlinuxServer = true;

    var $service_Email      = "";
    var $service_Password   = "";

    var $id_noti;

    var $idpaciente;
    var $id_odontolog;
    var $id_cita_agendada;

    var $asunto;
    var $from;
    var $to;
    var $subject='';
    var $message='';
    var $fecha_cita;
    var $hora_cita;
    var $nombpaciente='';
    var $nombodontolog='';

    var $celular;
    var $telefono;
    var $direccion;

    var $datosClinica;

    public function __construct($db){
        $this->db = $db;
    }

    private function btnEmailToken( $datosEmail = array() ){

        $Mensaje        = $datosEmail['mess'];
        $name_clinica   = $datosEmail['name_clinica'];
        $recordatorio   = $datosEmail['recordatorio'];
        $token          = $datosEmail['token'];
        $telefono       = $datosEmail['telefono'];
        $direccion      = $datosEmail['direccion'];
        $odontolog      = $datosEmail['odontolog'];


        $url_noti_icon = 'https://adminnube.com/odontic.beta/logos_icon/logo_default/dental_noti_.png';

        $box = '<div style="width: 100%; padding: 20px">
              <table align="center" style="border: 1px solid #d2d6de; width: 500px; padding: 30px; ">
                <tr>
                  <td align="center" colspan="2">
                    <p>
                      <img
                        src="'.$url_noti_icon.'"
                        alt=""
                        width="90px"
                        height="90px"
                      />
                    </p>
                    <br>
                  </td>
                </tr>
                <tr>
                  <td align="center" colspan="2"><h3 style="border: 1px solid #0078d7; padding: 2px;border-radius: 5px; color:#0078d7;">'.($name_clinica).'</h3></td>
                </tr>
        
        
                <tr style="padding-bottom: 15px;">
                    <td colspan="2" style="border-bottom: 1px solid #d2d6de;" ></td>
                </tr>
                <tr >
                    <td align="center" colspan="2" style="padding-bottom: 15px;color: #6a737d;">
                         Le recordamos que tiene una cita agendada para la fecha asignada<br> '.($recordatorio).'
                         <br>
                         <b>Odontólogo/a:</b> &nbsp; '.$odontolog.' 
                    </td>
                </tr>
        
                <tr>
                    <td colspan="2" style="border-bottom: 1px solid #d2d6de; padding-bottom: 15px;"></td>
                </tr>
                <tr>
                    <td align="center" colspan="2" style="padding-bottom: 15px;color: #6a737d;">
                            Recuerde que es importante que acuda a su cita con el tiempo establecido de anticipación, si por 
                            cualquier motivo no va a asistir por favor comuníquese <b>'.($telefono).'</b>
                    </td>
                </tr>';

                if(!empty($Mensaje)){
                    $box .= '
                         <tr>
                            <td colspan="2" style="border-bottom: 1px solid #d2d6de; padding-bottom: 15px;"></td>
                         </tr>
                         <tr>
                            <td align="center" colspan="2" style="padding-bottom: 15px; color: #6a737d;">
                                <small><b>'.($Mensaje).'</b></small>
                            </td>
                         </tr>
                    ';
                }

                $box .= '
                <tr>
                    <td colspan="2" style="border-bottom: 1px solid #d2d6de; padding-bottom: 15px;"></td>
                </tr>
                <tr> 
                    <td align="right"> <br> <small style="border: 1px solid #0078d7; padding: 2px;border-radius: 5px; color:#0078d7; font-weight: bolder;">Teléfono:  '.($telefono).'</small></td>
                </tr>
                <tr> 
                    <td align="right"> <br> <small style="border: 1px solid #0078d7; padding: 2px;border-radius: 5px; color:#0078d7;font-weight: bolder;">Dirección: '.($direccion).'</small></td>
                </tr>
        
                <tr>
                    <td colspan="2" align="center">
                        <br>
                        '.($token).'
                    </td>
                </tr>
                <tr> <td></td> </tr>
        
              </table>
            </div>';

//    print_r($box); die();
        return $box;

    }

    private function btn_token_confirmacion(){

        require_once  '../../../application/config/class.log.php';

        $log = new log($this->db,0);

        $idcita = $this->id_cita_agendada;
        $resultTokenDel = $this->db->query("DELETE FROM `tab_noti_token_confirmacion` WHERE `fk_cita_agendada`= $idcita; ");

        if($resultTokenDel){
            $log->log(0, $log->eliminar, 'Eliminacion de registros Token de confirmación Asociado a N. cita id: '.$idcita, 'tab_noti_token_confirmacion' );
        }

        $idToken =  $this->db->query("INSERT INTO `tab_noti_token_confirmacion` (`fk_cita_agendada`, `token`) VALUES ($idcita, 'NULL') ");

        if($idToken){

            $idToken = $this->db->lastInsertId("tab_noti_token_confirmacion");

            $log->log($idToken, $log->crear, "Se registro un nuevo Token de confirmación id: $idToken Asociado a N. cita id: ".$idcita, 'tab_noti_token_confirmacion' );

            $create_token_confirm_citas = [$idcita,md5($this->datosClinica->nombre_db_entity),md5($this->datosClinica->numero_entity),$this->datosClinica->nombre,$this->datosClinica->logo, $idToken];
            $result  =  $this->db->query("UPDATE `tab_noti_token_confirmacion` SET `token`= '".((tokenSecurityId(json_encode($create_token_confirm_citas))))."'  WHERE `rowid`= $idToken;");

            $labelQueryErr="UPDATE `tab_noti_token_confirmacion` SET `token`= '".((tokenSecurityId(json_encode($create_token_confirm_citas))))."'  WHERE `rowid`= $idToken;";
            if($result){
                $log->log($idToken, $log->modificar, 'Se Modifico Token de confirmación Asociado a N. cita id: '.$idcita, 'tab_noti_token_confirmacion' );
            }else{
                $log->log($idToken, $log->error, "Ocurrio un error al Modificar el Token de confirmación id: $idToken Asociado a N. cita id: ".$idcita, 'tab_noti_token_confirmacion', $labelQueryErr );
            }

        }else{

            $result=false;
        }

        if($result==false){
            return -1;
        }else{

            $token              = tokenSecurityId(json_encode($create_token_confirm_citas));
            $buttonConfirmacion = ConfirmacionEmailHTML( $token, $this->cronlinuxServer );

            return $buttonConfirmacion;
        }

    }

    public function send_confirmacion(){

        $Update="";

        require_once  '../../../application/controllers/controller.php';
        require_once  '../../../public/lib/PHPMailer/PHPMailerAutoload.php';
        require_once  '../../../application/config/class.log.php';
        $log = new log($this->db, 0);

        //obtengo la fecha spanish
        $spanishxDate = GET_DATE_SPANISH(date('Y-m-d', strtotime($this->fecha_cita))) ." - hora ".$this->hora_cita;

        $datosEmail['mess']         = (!empty($this->message))?utf8_decode($this->message):"";
        $datosEmail['name_clinica'] = $this->datosClinica->nombre;
        $datosEmail['recordatorio'] = $spanishxDate;
        $datosEmail['token']        = $this->btn_token_confirmacion();
        $datosEmail['telefono']     = $this->celular;
        $datosEmail['direccion']    = $this->direccion;
        $datosEmail['odontolog']    = $this->nombodontolog;



        $FormHtml                   = "<br><div style='font-size: 18px'> <b>Estimado/a:</b>&nbsp;".$this->nombpaciente."  <br><br> </div>".$this->btnEmailToken($datosEmail);

        $mail = new PHPMailer();

        $mail->IsSMTP();
        $mail->Mailer = "smtp";
        $mail->CharSet = 'UTF-8';
        $mail->Host = "smtp.gmail.com";
        $mail->SMTPDebug = 0;
        $mail->SMTPAuth = true;
        $mail->Port = 587;
        $mail->SMTPAutoTLS = TRUE;
        $mail->SMTPSecure = "tls";

        //servidor de correo SMTP DE GOOGLE CUENTA EMIL
        $mail->Username = $this->service_Email;//correo del servidor
        $mail->Password = $this->service_Password;//password de servidor de correo

        $mail->Subject = "Clinica dental ".$this->datosClinica->nombre; //nombre de la clinica
        $mail->addCustomHeader("'Reply-to:".$this->service_Email."'");
        $mail->isHTML(TRUE);
        $mail->msgHTML("Notificación Clinica ".$this->datosClinica->nombre);
        $mail->setFrom($this->service_Email, $this->datosClinica->nombre, true);
        $mail->addAddress($this->to);
        $mail->Body = $FormHtml;

        if($mail->send()){
             $log->log($this->id_noti, $log->CronLinux, 'Se realizo el envio de E-mail Tareas Cron Ejecutado Correctamente', 'tab_notificacion_email' );
             if($this->Actualizar_notificacion_email()==-1){
                 $Update = "Ocurrio un error con la actualización del envio ";
             }
        }else{

            $Update='Ocurrio un error con el envio del e-mil';
            $err_send_main = "Ocurrio un problema con el servidor no pudo enviar el correo, consulte con soporte  Tecnico \n ".$mail->ErrorInfo;
            $log->log($this->id_noti, $log->CronLinux, $err_send_main, 'tab_notificacion_email', 'Ocurrio un problema con el servidor para envio de e-mail ( PROCESO CRON )');

            $this->db->query("UPDATE `tab_notificacion_email` SET `estado`='A', program=0, error_send_program='".$err_send_main."' WHERE `rowid`=".$this->id_noti. " and fk_cita = ".$this->id_cita_agendada);
            $desc = "Se Modifico el registro E-mail Asociados id: ".$this->id_noti." Cambio de Estado `No confirmado`\n(Error de envio de E-mail)\nAsociado de N. cita Agendada:  ".$this->id_cita_agendada;
            $log->log($this->id_noti, $log->error, $desc, 'tab_notificacion_email');
        }

        return $Update;


    }

    private function Actualizar_notificacion_email(){

        require_once  '../../../application/config/class.log.php';

        $log = new log($this->db,0);

        $err_count = 0;
        $result = $this->db->query("UPDATE `tab_notificacion_email` SET `estado`='A', program=0 WHERE `rowid`=".$this->id_noti. " and fk_cita = ".$this->id_cita_agendada);
        if(!$result){

            $desc = "Ocurrio un error al Modificar E-mail Asociados id: ".$this->id_noti. "\nAsociado de N. cita Agendada:  ".$this->id_cita_agendada;
            $log->log($this->id_noti, $log->error, $desc, 'tab_notificacion_email',  "UPDATE `tab_notificacion_email` SET `estado`='A', program=0 WHERE `rowid`=".$this->id_noti. " and fk_cita = ".$this->id_cita_agendada );
            $err_count++;
        }else{

            $desc = "Se Modifico el registro E-mail Asociados id: ".$this->id_noti." Cambio de Estado `No confirmado`\nAsociado de N. cita Agendada:  ".$this->id_cita_agendada;
            $log->log($this->id_noti, $log->modificar, $desc, 'tab_notificacion_email');
        }

        $estado = $this->db->query("select text as label from tab_pacientes_estado_citas where rowid = 1")->fetchObject()->label;

        $result = $this->db->query("UPDATE `tab_pacientes_citas_det` SET `fk_estado_paciente_cita`= 1 WHERE `rowid`='".$this->id_cita_agendada."'; ");
        if(!$result){

            $desc = "Ocurrio un error al Modificar la cita id: ".$this->id_cita_agendada. " Cambio de estado a: ".$estado;
            $log->log($this->id_cita_agendada, $log->modificar, $desc, 'tab_pacientes_citas_det');
            $err_count++;
        }else{

            $desc = "Se Modifico el registro de una cita id: ".$this->id_cita_agendada. " Cambio de estado a: ".$estado;
            $log->log($this->id_cita_agendada, $log->modificar, $desc, 'tab_pacientes_citas_det');
        }

        if($err_count!=0){
            return -1;
        }
        return "";

    }


}

?>