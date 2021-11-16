<?php

clearstatcache();

define('PC_MEMORY_LIMIT', '1024M');
ini_set('memory_limit',PC_MEMORY_LIMIT);
set_time_limit(20000);


require_once 'class_connecion_and_process.php';
require_once 'tareas_cron.php';
require_once 'class_email.php';


function Execute($db_clinica){

    $result = ProccessCronSendEmail($db_clinica);
    //recorrer la base de datos
    foreach ($result as $db_name => $value){


        //nombre de la base datos de cada clinica
        $db_name_base    = $value['name_db'];
        $clinica_datos   = $value['info_clinica']; //object

        //conexion a diferentes clinicas
        $dbconectar         = new db_and_procesos();
        $db                 = $dbconectar->dbConectar($db_name_base);//conexion a diferentes clinicas

        if($db){
            $send_email_program = new send_email_program($db);
        }

        /*
            clinica
            telf celular
            ciudad
        */

        //email programable notificaciones E-mail de confirmacion del paciente
        if( (isset($value['tab_notificacion_email'])?1:0) == 1 ){

            if(count($value['tab_notificacion_email']) == 0 ){
                continue;
            }

            $send_email_program->celular                 = $clinica_datos->celular;
            $send_email_program->direccion               = $clinica_datos->ciudad;

            $send_email_program->datosClinica = new stdClass();
            $send_email_program->datosClinica->nombre             = $clinica_datos->nombre;
            $send_email_program->datosClinica->nombre_db_entity   = $clinica_datos->nombre_db_entity;
            $send_email_program->datosClinica->numero_entity      = $clinica_datos->numero_entity;
            $send_email_program->datosClinica->logo               = $clinica_datos->logo;
            $send_email_program->datosClinica->email              = $clinica_datos->email;

            $send_email_program->service_Email                    = $clinica_datos->mail_service;
            $send_email_program->service_Password                 = $clinica_datos->password_service;

            $array_noti_confirm_paciente = $value['tab_notificacion_email'];

            foreach ($array_noti_confirm_paciente as $key_noti_id => $value_noti){

                //object data tab_notificacion_email
                $send_email_program->id_noti            = $key_noti_id;
                $object_noti = $value_noti['send'];

                $send_email_program->idpaciente         = $object_noti->fk_paciente;
                $send_email_program->id_odontolog       = $object_noti->id_odontologo;
                $send_email_program->id_cita_agendada   = $object_noti->fk_cita;

                $send_email_program->nombpaciente       = $object_noti->nom;
                $send_email_program->nombodontolog      = $object_noti->odontol;

                $send_email_program->asunto             = $object_noti->asunto;
                $send_email_program->from               = $object_noti->from;
                $send_email_program->to                 = $object_noti->to;
                $send_email_program->subject            = $object_noti->subject;
                $send_email_program->message            = $object_noti->message;
                $send_email_program->fecha_cita         = date('Y/m/d', strtotime($object_noti->fecha_cita_ini));
                $send_email_program->hora_cita          = date('H:m:s', strtotime($object_noti->fecha_cita_ini));


                $send_email_program->send_confirmacion();

            }

        }

        //una ejecutado el proceso se limpia la cache que usa el PHP
        clearstatcache();

    }
}


?>