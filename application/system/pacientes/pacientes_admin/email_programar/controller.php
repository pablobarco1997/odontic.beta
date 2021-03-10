

<?php



    if(isset($_POST['ajaxSend']) || isset($_GET['ajaxSend']))
    {
        global $db, $conf, $user;

        session_start();
        require_once '../../../../config/lib.global.php';
        require_once DOL_DOCUMENT .'/application/config/main.php';


        $accion = GETPOST('accion');

        switch ($accion)
        {
            case 'ProgramarEnvioCorreos':

                $error['errorMail'] = "";
                $error['errorFile'] = "";

                $DateProgramCorreo = GETPOST('DateProgramCorreo');
                $idPaciente = GETPOST('idPaciente');
                $asunto = GETPOST('asunto');
                $email_destinario = GETPOST('email_destinario');
                $mensage_mail = GETPOST('mensage_mail');
                $File = (isset($_FILES["files"]))?$_FILES["files"]:[];
                $estado = GETPOST('estado');

                #to para registrar
                $datos["DateProgramCorreo"] = $DateProgramCorreo;
                $datos["idpaciente"] = $idPaciente;
                $datos["asunto"] = $asunto;
                $datos["email_destinario"] = $email_destinario;
                $datos["mensage_mail"] = $mensage_mail;
                $datos["File"] = $File;
                $datos["estado"] = $estado;

                $contarErroresFile = 0;
                $error = [];

//                die();
                //verfico el error de los archivos
                if(count($File)>0){
                    foreach ($File['error'] as $k => $value){
                        if($value!=0){
                            $contarErroresFile++;
                        }
                    }
                }

                if($contarErroresFile>0){
                    $error['errorFile'] = "Ocurrio un error con los archivos adjuntos \n Compruebe los archivos y vuelva a intentarlo";
                }
//              print_r($datos); die();
                if(count($error) == 0){
                    //Si el parametro de fecha
                    //El correo se programa para un fecha determinada
                    if($estado=='C'){
                        if(!empty($datos["DateProgramCorreo"])){
                            //move los File
                            $FileNames = CopyFileAddAttachment($File, false, false);
                            //Programar Envio Date
                            $error['errorMail'] = ToRegistroEmailProgram($datos ,$FileNames );
                        }else{
                            $error['errorMail']="No se encontro fecha asignada";
                        }
                    }
                    //envio guardado Pendiente
                    if($estado=='P'){
                        //move los File
                        $FileNames = CopyFileAddAttachment($File, false, false);
                        //Programar Envio Date
                        $error['errorMail'] = ToRegistroEmailProgram($datos , $FileNames);
                    }
                    //envio now
                    if($estado=='A'){
                        //move los File
                        $FileNames = CopyFileAddAttachment($File, false, false);
                        //Enviar email
                        $error['errorMail'] =  EnviarEmail($datos, $FileNames);
                    }
                }

                $output = [
                    'error' => $error
                ];

                echo json_encode($output);
                break;

            case 'listdateCorreosProgram':

                $data = array();
                $idPaciente = GETPOST('idpaciente');

                $Total          = 0;
                $start          = $_POST["start"];
                $length         = $_POST["length"];


                $sql = "select 
                        c.rowid,
                        c.date_cc, 
                        c.destinario, 
                        c.asunto,
                        c.message,
                        c.to_file,
                        cast(c.date_program as date) as date_program,
                        c.estado
                    from tab_send_email_programa c
                    where 
                    c.fk_paciente=$idPaciente
                   ";
                $sql.= " order by c.date_cc desc";


                $Total = $db->query($sql)->rowCount();

                if($start || $length)
                $sql.=" LIMIT $start,$length;";

                $result = $db->query($sql);
                if($result && $result->rowCount() > 0){
                    $arr = $result->fetchAll(PDO::FETCH_ASSOC);

//                    print_r($arr); die();
                    foreach ($arr as $k => $value){

                        $arrURL = $db->query("select * from tab_send_email_programa_to_file where fk_send_email_program =".$value['rowid'])->fetchAll(PDO::FETCH_ASSOC);

                        $File = array();
                        $File[] = "";
                        foreach ($arrURL as $kf => $f){

                            $icon = "";
                            $type = explode('.', $f['name'])[1];

                            if($type=='pdf')
                                $icon = "<i class='fa fa-file-pdf-o'></i>";

                            if($type=='xlsx')
                                $icon = "<i class='fa fa-file-excel-o'></i>";

                            if($type=='docx')
                                $icon = "<i class='fa fa-file-word-o'></i>";

                            $File[] = "<small> <a href='".DOL_HTTP.$f['path_to_file']."' download=".$f['name'].">".$icon."  ".$f['name']."</a> </small>";
                        }

                        $labelSatus = "";
                        $color = "";
                        if($value['estado']=="P"){
                            $labelSatus="correo pendiente "; $color="blue";
                        }
                        if($value['estado']=="A"){
                            $labelSatus="correo enviado o procesado"; $color="green";
                        }
                        if($value['estado']=="C"){
                            $labelSatus="correo Programado"; $color="#d58806";
                        }
                        if($value['estado']=="E"){
                            $labelSatus="Ocurrio un Error de envio Consulte con soporte"; $color="";
                        }
                        if($value['estado']=="N"){
                            $labelSatus="Correo Anulado"; $color="red";
                        }

                        $row = [];
                        $row[] = date('Y/d/m', strtotime($value['date_cc']));
                        $row[] = $value['destinario'];
                        $row[] = $value['asunto'];
                        $row[] = "<p class='trunc' title='".$value['message']."'>".$value['message']."</p>"." ".(implode(" ", $File));
                        $row[] = $value['date_program'];
                        $row[] = "<small style='font-weight: bolder; color: $color'>".$labelSatus."</small>";
                        $row[] = "";
                        $row['dataCorreo'] = base64_encode(json_encode($value));
                        $row['idcorreo']      = $value['rowid'];
                        $row['estado']        = $value['estado'];
                        $data[] = $row;
                    }
                }


                if($idPaciente==""||$idPaciente==0){
                    $data = array();
                }

                $output = [
                    'data' => $data,
                    'recordsTotal' => $Total,
                    'recordsFiltered' => $Total,
                ];

                echo json_encode($output);
                break;

            case 'deleteCorreo':

                $error="";

                $correo_id = GETPOST('idcorreo');

                if($correo_id!="" && $correo_id != 0){

                    #Compruebo si el correo se encuentra en estado pendiente o anulado
                    $stauts = $db->query("select count(*) as count from tab_send_email_programa where (estado = 'P' or estado = 'N') and rowid = $correo_id")->fetchObject()->count;

                    if($stauts!=0){
                        $sql = "DELETE FROM `tab_send_email_programa` WHERE `rowid`=".$correo_id;
                        $result = $db->query($sql);
                        if($result){
                            $sql = "select * from tab_send_email_programa_to_file where fk_send_email_program = $correo_id";
                            $result = $db->query($sql);
                            if($result && $result->rowCount()>0){
                                $arr_archv = $result->fetchAll(PDO::FETCH_ASSOC);
                                foreach ($arr_archv as $value){
                                    $path_unlink = DOL_DOCUMENT.$value['path_to_file'];
                                    unlink($path_unlink);
                                }
                                $db->query("DELETE FROM `tab_send_email_programa_to_file` WHERE `fk_send_email_program`= $correo_id and rowid > 0");
                            }
                        }else{
                            $error = "Ocurrio un error consulte con soporte o compruebe la información";
                        }
                    }else{
                        $error = "Compruebe el estado antes de eliminar <b> Solo puede Eliminar si el correo se encuentra en estado Anulado o Pendiente";
                    }

                }else{
                    $error = "No se pudo obtener los parametros asignados Consulte con soporte Tecnico";
                }

                $output = [
                    'error' => $error,
                ];

                echo json_encode($output);
                break;

            case 'estadoAnular':

                $error = "";
                $correo_id = GETPOST('idcorreo');

                if($correo_id!="" && $correo_id != 0){

                    #Compruebo si el correo se encuentra en estado pendiente o anulado
                    $stauts = $db->query("select count(*) as count from tab_send_email_programa where (estado = 'C') and rowid = $correo_id")->fetchObject()->count;

                    if($stauts!=0){
                        $sql = "UPDATE `tab_send_email_programa` SET `estado`='N' WHERE `rowid`=$correo_id;";
                        $result = $db->query($sql);
                        if(!$result){
                            $error = "Ocurrio un error consulte con soporte o compruebe la información";
                        }
                    }else{
                        $error = "Compruebe el estado antes de Anular <b> Solo puede Anular si el correo se encuentra en estado Programado";
                    }

                }else{
                    $error = "No se pudo obtener los parametros asignados Consulte con soporte Tecnico";
                }

                $output = [
                    'error' => $error,
                ];
                echo json_encode($output);
                break;

        }
    }

    function EnviarEmail($info = array(), $FileNames=array()){

        global $db, $conf, $user;

        require_once DOL_DOCUMENT .'/public/lib/PHPMailer/PHPMailerAutoload.php';

        $error      = "";
        $asunto     = $info['asunto']; //asunto
        $from       = $conf->EMPRESA->INFORMACION->email; //desde
        $to         = $info['email_destinario']; //destinario
        $message    = $info['mensage_mail'];
        $subject    = "Clínica Dental: ".$conf->EMPRESA->INFORMACION->nombre;

        $mail = new PHPMailer();

        $mail->IsSMTP();
        $mail->Mailer = "smtp";
        $mail->CharSet = 'UTF-8';
        $mail->Host = "mail.adminnube.com";
        $mail->SMTPDebug = 0;
        $mail->SMTPAuth = true;
        $mail->Port = 465;
        $mail->SMTPAutoTLS = TRUE;
        $mail->SMTPSecure = "ssl";

        $mail->Username = $conf->service_Email;//correo del servidor
        $mail->Password = $conf->service_Password;//password de servidor de correo

        $mail->FromName = $subject;
        $mail->Subject = $subject; //nombre de la clinica
        $mail->addCustomHeader("'Reply-to:".$conf->EMPRESA->INFORMACION->email."'");
        $mail->isHTML(TRUE);
        $mail->msgHTML("Notificación Clinica ".$conf->EMPRESA->INFORMACION->nombre);
        $mail->setFrom($conf->service_Email, "Clínica Dental ". $conf->EMPRESA->INFORMACION->nombre);
        $mail->addAddress($to);

        $mail->Body = "<h3>".($asunto)."</h3> <br> <p style='white-space: pre-wrap'>".($message)."</p>";

        if(count($FileNames)>0){
            foreach ($FileNames as $k => $value){
                $path  = file_get_contents(DOL_HTTP.$value['path']);
                $type  = $value['type'];
                $label = $value['label'];
                $mail->addStringAttachment($path, $label, 'base64', $type);
            }
        }

        if(!$mail->send()){
            #Correo no enviado
            $error   =  'Ocurrio un problema con el servidor no pudo enviar el correo, intentelo de nuevo o consulte con soporte  Tecnico' .'<br> <b> '. $mail->ErrorInfo .' </b>';
//            print_r($error); die();
        }else{
            $error   = ToRegistroEmailProgram($info, $FileNames);
        }

        return $error;
    }

    function CopyFileAddAttachment($Array_File = array(), $unlink=false){
        global  $conf;

        $AuxNameFiles=array(); //solo funciona con nameFiles=true
        $entidad = $conf->EMPRESA->ENTIDAD.'_'.$conf->EMPRESA->ID_ENTIDAD;
        $mkdir = "CarpetaAddAttachment_".base64_encode($entidad);
        $document = '/application/system/pacientes/pacientes_admin/email_programar/adjunto/'.$mkdir;
        $path = DOL_DOCUMENT.$document; //path url

        if(count($Array_File)==0){
            return [];
        }

        if($unlink==true){ // se utiliza para remover los elementos si encaso el email no se envia correctamente

        }

        $error = 0;
        //comprobar si existe la carpeta
        if(!file_exists($path)){
            //crear carpeta
            if(!mkdir($path, 0777, true)) {
                die('Fallo al crear las carpetas...');
            }
        }

        //Se mueve el fichero a la carpeta ../email_programar/adjunto/
        //cuento el total de archivo que se encuentra dentro de la carpeta adjunto/
        $totalFile = count(glob($path.'/{*.pdf,*.xlsx,*.docx}',GLOB_BRACE));
        foreach ($Array_File['name'] as $k => $value){
            $tmp       = $Array_File['tmp_name'][$k];
            $extencion = (explode('.', $value)[1]); //extencion File
            $label     = (explode('.', $value)[0]); //nombre actual del archivo

            //name = file _ totalArchivos _ _nameFile en base64 _ (entidad)
            $name = basename('file_'.base64_encode(''.$totalFile.''.$label).'_'.$conf->EMPRESA->ENTIDAD);
            if(!move_uploaded_file($tmp, $path.'/'.$name.'.'.$extencion)){
                die("Ocurrio un error al mover el fichero");
            }else{
                $AuxNameFiles[] = array(
                    'path'  => '/application/system/pacientes/pacientes_admin/email_programar/adjunto/'.$mkdir.'/'.$name.'.'.$extencion,
                    'name'  => $name.'.'.$extencion,
                    'label' => $value,
                    'type'  => $Array_File['type'][$k]
                );
            }

        }
        return $AuxNameFiles;

    }

    function ToRegistroEmailProgram($datos = array(), $nameFiles = array()){

        /*
         *   P     PENDIENTE          (correo creado sin nimguna accion)
         *   C     PROGRAMADO         (correo programado para una fecha especifica)
         *   A     ENVIADO EXECUTADO  (correo enviado o procesado)
         *
         * */

        global $db;

        $err = "";

        $sql = "INSERT INTO `tab_send_email_programa`";
        $sql .= "(";
        $sql .= " `date_cc`,";
        $sql .= " `fk_paciente`, ";
        $sql .= " `destinario`, ";
        $sql .= " `asunto`, ";
        $sql .= " `message`, ";
        $sql .= " `to_file` ";

        if($datos['DateProgramCorreo']!=""){
            $sql .= " , `date_program` ";
        }
        $sql .= " ,`estado` ";
        $sql .= ")";
        $sql .= "VALUES (";
        $sql .= " now() ,";
        $sql .= " ".$datos['idpaciente']." , ";
        $sql .= " '".$datos['email_destinario']."' , ";
        $sql .= " '".$datos['asunto']."' , ";
        $sql .= " ".(($db->quote($datos['mensage_mail'])))." , ";
        $sql .= " ".(count($nameFiles))." ";

        if($datos['DateProgramCorreo']!=""){ //date program   :  el correo queda programado para un envio con una fecha determinada
            $sql .= " , '".$datos['DateProgramCorreo']."'  ";
        }
        $sql .= " , '".$datos['estado']."' ";
        $sql .= ")";

        $idsendProgram = 0;
        $result = $db->query($sql);
        if($result){
            $idsendProgram = $db->lastInsertId("tab_send_email_programa");
        }else{
            $err = "Ocurrio un error con el registro del correo";
        }

        if(count($nameFiles)>0 && $idsendProgram!=0){
            foreach ($nameFiles as $k => $value){
                $sqlFile = "INSERT INTO `tab_send_email_programa_to_file`";
                $sqlFile .= " (";
                $sqlFile .= " `fk_send_email_program`,";
                $sqlFile .= " `name`,";
                $sqlFile .= " `name64`,";
                $sqlFile .= " `path_to_file`,";
                $sqlFile .= " `type_to_file` )";
                $sqlFile .= " VALUES (";
                $sqlFile .= " $idsendProgram ,";
                $sqlFile .= " '".$value['label']."' ,";
                $sqlFile .= " '".$value['name']."' ,";
                $sqlFile .= " '".$value['path']."' ,";
                $sqlFile .= " '".$value['type']."' ";
                $sqlFile .= " )";
                $db->query($sqlFile);
//                print_r($sqlFile); die();
            }
        }

        return $err;

    }



?>