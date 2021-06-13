
<?php

include_once '../application/config/lib.global.php';
require_once 'class_connecion_and_process.php';



//El proceso que ejecutara el email
function ProccessCronSendEmail(){

    $dbconectar       = new db_and_procesos();

    //obtengos la clinicas registradas
    $fetchClinicas    = $dbconectar->fetchClinicas($dbconectar->Connection);

//    echo '<pre>'; print_r($fetchClinicas); die();
    $ArraySendProgram = array();

    //recorro las database existentes
    foreach ($fetchClinicas as $k => $value){
        //comprueba las bases existentes para realizar el proceso cron de los envios email
        if( $dbconectar->Connection->query("SHOW DATABASES LIKE '".$value['db_name']."' ")->rowCount() ){

            $db = $dbconectar->dbConectar($value['db_name']);//conexion a diferentes clinicas

            $ArrayToFileSend=[];
            $sqlfile = "select name, name64, path_to_file, type_to_file, fk_send_email_program from tab_send_email_programa_to_file;";
            $resulltF = $db->query($sqlfile);
            if($resulltF && $resulltF->rowCount()>0){
                while ($objf = $resulltF->fetchObject()){
                    $ArrayToFileSend[$objf->fk_send_email_program] = $objf;
                }
            }

            $ArraySendProgram['clinica_'.$value['db_name']]['name_db']                          = $value['db_name'];
            $ArraySendProgram['clinica_'.$value['db_name']]['info_clinica']                     = $dbconectar->obtener_clinica($value['db_name']);
            $ArraySendProgram['clinica_'.$value['db_name']]['info_clinica']->mail_service       = (empty($value['mail_service'])?'':$value['mail_service']);
            $ArraySendProgram['clinica_'.$value['db_name']]['info_clinica']->password_service   = (empty($value['password_service'])?'':$value['password_service']);
            $ArraySendProgram['clinica_'.$value['db_name']]['info_clinica']->disabled_mail      = (empty($value['disabled_mail'])?'':$value['disabled_mail']);

            //email programados
            $sql = "select 
                    rowid as idsendp,
                    date_cc, 
                    fk_paciente, 
                    destinario, 
                    asunto, 
                    message,
                    cast(date_program as date) as date_program,
                    'Programado' as status
                from tab_send_email_programa where estado='C' 
                and cast(now() as date) <= cast(date_program as date)
                order by rowid desc";
            $result = $db->query($sql);
            if($result){
                if($result && $result->rowCount()>0){
                    while ($object =$result->fetchObject()){
                        $ArraySendProgram['clinica_'.$value['db_name']]['tab_send_email_programa'][$object->idsendp]['send']   = $object;
                        $ArraySendProgram['clinica_'.$value['db_name']]['tab_send_email_programa'][$object->idsendp]['tofile'] = (!empty($ArrayToFileSend[$object->idsendp]))?$ArrayToFileSend[$object->idsendp]:"";
                    }
                }
            }


            //email Asociado programados
            $query = "SELECT 
                        e.rowid AS id_noti,
                        CONCAT(p.nombre, ' ', p.apellido) AS nom,
                        CAST(e.fecha AS DATE) emitido,
                        e.asunto,
                        e.from,
                        e.to,
                        e.subject,
                        e.message,
                        e.estado,
                        e.fk_paciente,
                        e.fk_cita,
                        CAST(CONCAT(CAST(e.program_date AS DATE),' 23:00:00') AS DATETIME) AS program_date,
                        CAST(cita.fecha_cita AS DATETIME) AS fecha_cita,
                        
                        cita.fk_doc as id_odontologo,
                        (select concat(o.nombre_doc,' ',o.apellido_doc) from tab_odontologos o where o.rowid = cita.fk_doc) as odontol, 
                        cast(concat(cast(cita.fecha_cita AS DATE),' ',cita.hora_inicio) as datetime) AS fecha_cita_ini
                    FROM
                        tab_notificacion_email e
                            INNER JOIN
                        tab_pacientes_citas_det cita ON cita.rowid = e.fk_cita
                            INNER JOIN
                        tab_admin_pacientes p ON p.rowid = e.fk_paciente
                    WHERE
                            e.program = 1 
                            AND e.estado = 'P'
                            AND CAST(e.program_date AS DATE) = CAST(NOW() AS DATE)
                            AND CAST(CONCAT(CAST(e.program_date AS DATE),' 23:00:00') AS DATETIME) <= CAST(cita.fecha_cita AS DATETIME)
                    ORDER BY e.rowid asc";
            $result = $db->query($query);
            if($result){
                if($result->rowCount()){
                    while ($obj = $result->fetchObject()){
                        $ArraySendProgram['clinica_'.$value['db_name']]['tab_notificacion_email'][$obj->id_noti]['send'] = $obj;
                    }
                }
            }


        }
    }


//    echo '<pre>'; print_r($ArraySendProgram);  die();

    return $ArraySendProgram;
}


?>
