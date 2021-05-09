
<?php

    class configuration{

        var $PERFIL     = "";
        var $EMPRESA    = "";
        var $PACIENTES  = "";
        var $NOTIFICACIONES = "";
        var $DIRECTORIO = "";
        var $NAME_DIRECTORIO = "";

        var $Entidad    = "";
        var $db_schema  = "";
        var $login_user = "";
        var $login_id   = "";

        var $service_Email      = "odontic2@adminnube.com";
        var $service_Password   = "7))UK[zmjVn$";

        public  function __construct()
        {
            $this->PACIENTES                 = array();
            $this->EMPRESA                   = new stdClass();
            $this->DIRECTORIO                = new stdClass();
            $this->NAME_DIRECTORIO           = new stdClass();
            $this->PERFIL                    = new stdClass();
            $this->NOTIFICACIONES            = (object)array( 'Glob_Notificaciones' => [] , 'Numero' => 0 );
        }

        public function ObtenerPaciente($db, $id, $tipo)
        {
            $obj = array();
            $sql1  = "SELECT * FROM tab_admin_pacientes WHERE rowid > 0 ";

            if($tipo==true)
            {
                if(!empty($id))
                {
                    $sql1 .= " and rowid = $id";
                }
            }

            $resp = $db->query($sql1);
            if($resp->rowCount())
            {
                while ($row = $resp->fetchObject())
                {
//                   $this->pacientesObj[] = array("id" => $row->rowid, "nombre" => $row->nombre .' '.$row->apellido);

                     if($tipo==true)
                     {
                         $obj = $row;
                     }
                     if($tipo==false)
                     {
                         $this->PACIENTES[]= $row;
                     }

                }
            }

            return $obj;
        }

        #OBTENER NOTIFICACIONES DEL SISTEMA
        function ObtnerNoficaciones($db, $puedoAxu)
        {

                $fecha_time_db=[]; //obtengo el tiempo y la fecha de cada notificacion que entra
                $GlobNotificacion = array();

                #Esta variable captura el numero de citas que existe
                $numeroNotificaciones = 0;

                $ConsultarCitas = "
                        SELECT 
                            d.rowid AS id_detalle_cita,
                            c.fecha_create,
                            d.hora_inicio,
                            d.hora_fin,
                            CONCAT(d.hora_inicio, ' A ', d.hora_fin) AS cita_desde,
                            CONCAT(p.nombre, ' ', p.apellido) AS nombre,
                            c.comentario,
                            (SELECT CONCAT(o.nombre_doc, ' ', o.apellido_doc) FROM tab_odontologos o WHERE o.rowid = d.fk_doc) AS doctor_cargo,
                            s.text,
                            p.rowid AS idpaciente,
                            d.fk_doc AS iddoctorcargo,
                            p.fk_convenio AS convenio,
                            IFNULL((SELECT  cv.nombre_conv FROM tab_conf_convenio_desc cv WHERE cv.rowid = p.fk_convenio),'sin convenio') AS nomconvenio ,
                            p.icon,
                            c.tms
                        FROM
                            tab_pacientes_citas_cab c,
                            tab_pacientes_citas_det d,
                            tab_admin_pacientes p,
                            tab_pacientes_estado_citas s
                        WHERE
                                c.fk_paciente = p.rowid
                                AND c.rowid = d.fk_pacient_cita_cab
                                AND d.fk_estado_paciente_cita = s.rowid 
                                
                        -- alerta la notificacion de la cita con fecha hasta la hora fin 
                        AND date_format(d.fecha_cita , '%Y-%m-%d') = date_format( now() , '%Y-%m-%d') 
                        AND TRIM(SUBSTRING(NOW(), 11, 17)) <= TRIM(d.hora_fin) 
                        -- Solo las citas que estan confirmadas
                        AND s.rowid not in(9,7,6,7) ";

                #Notificacion de citas - cada vez que se agenda una nueva cita
                $rsConsultCitas = $db->query($ConsultarCitas);
                if($rsConsultCitas->rowCount() > 0){
                    while ( $CitasConsult = $rsConsultCitas->fetchObject() ){

                        //Fecha Time de citas
                        $fecha_time_db[base64_encode($CitasConsult->tms)] = $CitasConsult->tms;

                        //Notificaiones object Global de citas
                        $this->NOTIFICACIONES->Glob_Notificaciones[] = (object)array(
                            'tipo_notificacion'     =>  'NOTIFICAIONES_CITAS_PACIENTES' ,
                            'fecha'                 =>  date('Y-m-d', strtotime( str_replace('-', '/', $CitasConsult->fecha_create))),
                            'horaIni'               =>  $CitasConsult->hora_inicio,
                            'horafin'               =>  $CitasConsult->hora_fin ,
                            'nombe_paciente'        =>  $CitasConsult->nombre,
                            'comment'               =>  $CitasConsult->comentario,
                            'doctor_cargo'          =>  $CitasConsult->doctor_cargo,
                            'icon'                  =>  $CitasConsult->icon,
                            'id_detalle_cita'       =>  $CitasConsult->id_detalle_cita,
                            'idpaciente'            =>  $CitasConsult->idpaciente,
                            'iddoctorcargo'         =>  $CitasConsult->iddoctorcargo,
                        );

                        //Arreglo de retornar (Notificacion de Citas)
                        $GlobNotificacion[] = (object)array(
                            'tipo_notificacion'     => 'NOTIFICAIONES_CITAS_PACIENTES' ,
                            'fecha'                 =>  date('Y-m-d', strtotime( str_replace('-', '/', $CitasConsult->fecha_create))),
                            'horaIni'               =>  $CitasConsult->hora_inicio,
                            'horafin'               =>  $CitasConsult->hora_fin ,
                            'nombe_paciente'        =>  $CitasConsult->nombre,
                            'comment'               =>  $CitasConsult->comentario,
                            'doctor_cargo'          =>  $CitasConsult->doctor_cargo,
                            'icon'                  =>  $CitasConsult->icon,
                            'id_detalle_cita'       =>  $CitasConsult->id_detalle_cita,
                            'idpaciente'            =>  $CitasConsult->idpaciente,
                            'iddoctorcargo'         =>  $CitasConsult->iddoctorcargo,
                        );
                        $numeroNotificaciones++;
                    }
                }

                #Confirmacion de citas x paciente via email
                $ConsultarCitasConfirmadas = "SELECT 
                                                (select concat( p.nombre , ' ' , p.apellido)  from tab_admin_pacientes p where p.rowid = e.fk_paciente) as paciente  , 
                                                (select p.icon  from tab_admin_pacientes p where p.rowid = e.fk_paciente) as icon_paciente , 
                                                e.action ,
                                                e.noti_aceptar ,
                                                e.date_confirm , 
                                                e.rowid,
                                                e.date_confirm as tms
                                            FROM tab_noti_confirmacion_cita_email e  , tab_pacientes_citas_det d WHERE e.fk_cita = d.rowid and e.action != '' and e.noti_aceptar = 0 
                                            AND  now() <= cast(e.fecha_cita as datetime)";
                $rsCitasConfirmadas        = $db->query($ConsultarCitasConfirmadas);
                if($rsCitasConfirmadas && $rsCitasConfirmadas->rowCount() > 0){
                    while ( $NotiConfirmPacientes = $rsCitasConfirmadas->fetchObject() ){

                        //Fecha Time de citas x paciente via email
                        $fecha_time_db[base64_encode($NotiConfirmPacientes->tms)] = $NotiConfirmPacientes->tms;

                        $confirmacion = "Consultando";
                        $EstadoConfirmado = "";

                        if($NotiConfirmPacientes->action == 'ASISTIR'){
                            $confirmacion       = 'Este paciente confirmo el e-mail';
                            $EstadoConfirmado   = 'El paciente confirmó que si asistirá a la consulta';
                        }
                        if($NotiConfirmPacientes->action == 'NO_ASISTIR'){
                            $confirmacion       = 'Este paciente confirmo el e-mail';
                            $EstadoConfirmado   = 'El paciente confirmó que no asistirá a la consulta';
                        }

                        $this->NOTIFICACIONES->Glob_Notificaciones[] = (object)array(
                            'tipo_notificacion'      => 'NOTIFICACION_CONFIRMAR_PACIENTE' ,
                            'paciente'               => $NotiConfirmPacientes->paciente ,
                            'icon_paciente'          => $NotiConfirmPacientes->icon_paciente ,
                            'accion'                 => $confirmacion   ,
                            'estado_confirmado'      => $EstadoConfirmado,
                            'tab'                    => 'tab_noti_confirmacion_cita_email',
                            'id'                     => $NotiConfirmPacientes->rowid
                        );

                        $GlobNotificacion[] = (object)array(
                            'tipo_notificacion'      => 'NOTIFICACION_CONFIRMAR_PACIENTE' ,
                            'paciente'               => $NotiConfirmPacientes->paciente ,
                            'icon_paciente'          => $NotiConfirmPacientes->icon_paciente ,
                            'accion'                 => $confirmacion   ,
                            'estado_confirmado'      => $EstadoConfirmado,
                            'tab'                    => 'tab_noti_confirmacion_cita_email',
                            'id'                     => $NotiConfirmPacientes->rowid
                        );

                        $numeroNotificaciones++;
                    }
                }

                #NUMERO DE NOTIFICACIONES
                $this->NOTIFICACIONES->Numero = (object)array(
                    'NumeroNotificaciones'      => $numeroNotificaciones
                );

//                $GlobNotificacion = (object)array('NumeroNotificaciones' => $numeroNotificaciones);

                $output = array(
                    'data'          => $GlobNotificacion ,
                    'numero'        => $numeroNotificaciones,
                    'fecha_time_db' => $fecha_time_db
                );

                return $output;


        }

        function perfil($db, $idUsuario, $url, $directorio_url)
        {
            $sql = "SELECT * FROM tab_odontologos WHERE rowid = $idUsuario limit 1";
            $rs = $db->query($sql);

            if($rs->rowCount() > 0)
            {
                $Obj = $rs->fetchObject();

                $img = $url."/logos_icon/logo_default/doct-icon.ico"; //Icon por default
                if(!empty($Obj->icon))
                {
                    $img = $url."/logos_icon/".$directorio_url."/".$Obj->icon;
                }

                $tieneUsuario = [];
                $sqlusu = "SELECT usuario , passwords , passwor_abc , fk_doc , cedula FROM tab_login_users where fk_doc = ".$Obj->rowid;
                $rsusu  = $db->query($sqlusu);
                if($rsusu && $rsusu->rowCount())
                {
                    while ($obus = $rsusu->fetchObject()){
                        $tieneUsuario = array(
                            "name"        => $obus->usuario,
                            "passwords"   => $obus->passwords,
                            "passwor_abc" => $obus->passwor_abc,
                            "cedula"      => $obus->cedula
                        );
                    }
                }

                $data = (object)array(
                    "id"        => $Obj->rowid,
                    "nombre"    => $Obj->nombre_doc,
                    "apellido"  => $Obj->apellido_doc,
                    "icon"      => $img,
                    "AssciUsu"  => $tieneUsuario
                );

                $this->PERFIL = $data;
            }
        }

    }


?>