<?php

if(isset($_GET['ajaxSend']) || isset($_POST['ajaxSend']))
{

    session_start();

    require_once '../../../config/lib.global.php';
    require_once DOL_DOCUMENT. '/application/config/main.php'; //el main contiene la sesion iniciada
    require_once DOL_DOCUMENT .'/application/system/agenda/class/class_agenda.php';


    global  $db , $conf, $log, $user;

    $agenda = new admin_agenda($db);

    $accion = GETPOST('accion');

    switch ($accion)
    {
        case 'create_cita_paciente':

            if(!PermitsModule("Agenda","agregar")){
                $permits = false;
            }else{
                $permits = true;
            }

            $error = "";

            if($permits==true){
                $array = GETPOST('datos');
                $agenda->fk_paciente    = $array['fk_paciente'];
                $agenda->comentario     = $array['comment'];
                $agenda->fk_login_users = $user->id; #USUARIO LOGEADO
                $agenda->detalle        = $array['detalle'];

                $result = $agenda->GenerarCitas();
                if($result > 0){ //me retorna el id
                    $log->log($result, $log->crear, 'Se registro una cita Numero: '.$result, 'tab_pacientes_citas_det');
                }else{ //caso contrario query
                    $log->log(0, $log->crear, 'Ocurrió un error para el registro de una cita', 'tab_pacientes_citas_det', $result);
                    $error = "Ocurrió un error al  generar la cita , consulte con soporte tecnico";
                }
            }else{
                $error = "Ud. No tiene permiso para esta Operación";
            }


            $output = [
              'error' => $error
            ];
            echo json_encode($output);

            break;


        case 'listCitas':

            $estados                            = GETPOST("estados");
            $doctor                             = GETPOST("doctor");
            $fecha                              = GETPOST("fecha");
            $MostrarCitasCanceladasEliminadas   = GETPOST('eliminada_canceladas');
            $citasFuturas                       = GETPOST('citas_futuras'); #muesta citas futuras

            $pacientes                          = GETPOST('buscar_xpaciente');
            $n_citas                            = GETPOST('search_ncita');

            $star2                              = GETPOST('start2');
            $fechaInicio                        ="";
            $fechaFin                           ="";

            if(!empty($fecha)) {
                $fecha       = explode('-',GETPOST("fecha"));
                $fechaInicio = date("Y-m-d", strtotime( str_replace("/", "-", trim($fecha[0]))));
                $fechaFin    = date("Y-m-d", strtotime( str_replace("/", "-", trim($fecha[1]))));
            }

            $resultado = list_citas( $doctor, $estados, $fechaInicio, $fechaFin, $MostrarCitasCanceladasEliminadas, $pacientes, $n_citas, $star2 );

            $output = array(
                "draw"            => $_POST['draw'],
                "data"            => $resultado['datos'],
                "recordsTotal"    => $resultado['total'],
                "recordsFiltered" => $resultado['total'],
                "permiso"         => $resultado['permiso'],
            );

            echo json_encode($output);

            break;

            /*Esta funcion se usa globalmente para actualizar el estado de la citas */
        case 'EstadoslistCitas':

            $countError = 0;
            $output = ['error'=>"",'errmsg'=>"", 'success'=>""];

            $idestado   = GETPOST('idestado');
            $idcita_det = GETPOST('idcita');
            $textEstado = GETPOST('estadoText');


            //valida si tiene permiso para realizar esta accion modificar
            if(!PermitsModule('Agenda', 'modificar')){
                $output['errmsg'] = "Ud. No tiene permiso para realizar esta operación";
                echo json_encode($output);
                $countError++;
                die();
            }


            //validar fecha atrazada para estos estados
            $is_status = array(
                '4'=>'En sala de espera',
                '5'=>'Atendiéndose',
                '6'=>'Atendido'
            );
            if(array_key_exists($idestado, $is_status)==1){
                if(!ValidarFechaActualCita($idcita_det)){
                    $output['errmsg'] = "Esta cita se encuentra atrasada no puede cambiar a estado <b>".$is_status[$idestado]."</b>";
                    $countError++;
                }
            }


            //Consulto el estado para validar si se encuentra en estado E-mail de confirmacion Programdo
            //Si esta en estado E-mail de confirmacion programado para poder cambiar de estado tendra que librar la cita de dicho estado ubicarse en el modulo E-mail Asociado y eliminar dicho email progrmado
            $consultarStatus = "SELECT fk_estado_paciente_cita as estado_id , 
                                       IF( now() > CAST(fecha_cita AS DATETIME) , 
                                            concat('Atrasada ', (select concat(s.text) from tab_pacientes_estado_citas s where s.rowid = fk_estado_paciente_cita) , 
                                                    'Fecha : ' , date_format(fecha_cita, '%Y/%m/%d') , 'Hora: ' , hora_inicio ,' h ' , hora_fin) , ''
                                                    ) as vencidad_estados
                                    FROM tab_pacientes_citas_det where (rowid = $idcita_det)";
            $result = $db->query($consultarStatus);
            if($result && $result->rowCount()>0){
                $object_sts = $result->fetchObject();

                //id 11 E-mail de confirmación Programado
                if($object_sts->estado_id==11){
                    $output['errmsg']  = "No puede actualizar esta cita, se encuentra en estado <b>E-mail de confirmacion programado</b>  
                                                <br> <small style='font-weight: bold'> Verifique la información antes de actualizar para continuar y liberar la cita agendada diríjase al modulo E-mail Asociados del paciente para desactivar el registro E-mail Programado </small>";
                    $countError++;
                }

                //id 6 Atendido si el paciente se encuentra atendido y validar la fecha
                //validar que no puede cambiar el estado Atendido si encaso la fecha es vencida
                if($object_sts->estado_id==6){
                    if($object_sts->vencidad_estados!=""){
                        $output['errmsg'] = "Esta cita #".$idcita_det." .Ya se encuentra en estado Atendido no puede actualizar la información";
                        $countError++;
                    }
                }
            }

            if($countError==0){
                $sqlUpdateEstado = "UPDATE `tab_pacientes_citas_det` SET `fk_estado_paciente_cita` = $idestado WHERE (`rowid` = $idcita_det);";
                $result_u        = $db->query($sqlUpdateEstado);
                $estado          = getnombreEstadoCita($idestado);
                if($result_u){
                    $output['success'] = "Estado $textEstado: información Actualizada";
                    $log->log($idcita_det, $log->modificar, 'Se ha Actualizado un registro | Cita N.'.$idcita_det.' actualizo estado: '.$estado->nom, 'tab_pacientes_citas_det');
                }else{
                    $output['error'] = 'Ocurrio un error con Update ' .'Status'.$textEstado;
                    $log->log($idcita_det, $log->error, 'Ha ocurrido un error con la actualización de estado | Cita N.'.$idcita_det.' estado: '.$estado->nom, 'tab_pacientes_citas_det', $sqlUpdateEstado);
                }
            }

            echo json_encode($output);
            break;

            /*valida la fecha y hora asignada del odontologo disponible*/
        case 'validacionFechasCitas':

            $resp = true;

            $fecha       = "";
            $duracion    = "";
            $hora        = "";
            $fk_doc      = "";
            $fechaFin    = "";
            $fechaInicio = "";
            $horaFin     = "";
            $horaInicio  = "";

            $fecha     = date("Y-m-d", strtotime(str_replace('/','-', GETPOST("fecha"))));
            $hora      = GETPOST("hora");
            $duracion  = GETPOST("duracion");
            $fk_doc    = GETPOST("fk_doc");

            $fechaInicio = "$fecha $hora:00";
            $fechaFin    = strtotime("+$duracion minute", strtotime($fechaInicio));

            $horaFin     = date("H:i:s", $fechaFin);
            $horaInicio  = $hora.":00";

            $sql = "SELECT rowid,  fk_doc, fecha_cita, hora_cita, hora_inicio, hora_fin  
                         FROM tab_pacientes_citas_det WHERE fk_doc = $fk_doc 
                    and cast(fecha_cita as date)   = '$fecha'
                    and hora_inicio <= '$horaFin'
                    and hora_fin    >= '$horaInicio'
                    and fk_estado_paciente_cita not in(7,9) ";

            #echo '<br><pre>';print_r($sql);die();

            $rs = $db->query($sql);

            if($rs->rowCount() > 0) //ESTE doctor TIENE ASIGANADA YA ESTA FECHA Y HORA DE CITA
            {
                $resp = false;
            }

            $output = [
                'respuesta' => $resp
            ];

            echo json_encode($output);
            break;

        case "numero_citas_pacientes_hoy":

            $numero = 0;

            #NUMEROS DE CITAS PARA LA FECHA ACTUAL CON ESTADO NO CONFIRMADO - ID DEL ESTADO == 2
            $sqlCount = "SELECT 
                            COUNT(*) AS numeros_de_citas
                        FROM
                            tab_pacientes_citas_cab    c,
                            tab_pacientes_citas_det    d,
                            tab_pacientes_estado_citas s
                        WHERE
                            c.rowid = d.fk_pacient_cita_cab
                                AND d.fk_estado_paciente_cita = s.rowid
                                AND c.rowid > 0 ";
            $sqlCount .= "  AND CAST(d.fecha_cita AS date) = CAST(NOW() AS date)";
            $sqlCount .= "  AND CAST(d.fecha_cita as TIME) >= CAST(NOW() AS time)";
            $sqlCount .= "  AND d.fk_estado_paciente_cita not in(9,6,7,9) ";
            $sqlCount .= "  LIMIT 1 ";

            #print_r($sqlCount); die();
            $rs1 = $db->query($sqlCount);
            if($rs1->rowCount() > 0)
            {
                $numero = $rs1->fetchObject()->numeros_de_citas;
            }

            echo json_encode( array('result' => $numero) );

            break;

        case "consul_hora_fecha_listglobal":

            $puedo   = false;
            $detalle = [];

            #se declara las fechas y horas
            $fecha   = GETPOST("fecha");
            $hora    = GETPOST("hora");
            $doctor  = GETPOST("doctor");
            $estados = !empty(GETPOST("estados")) ? GETPOST("estados") : [];

            $error = fecth_diariaHorasGlobal($fecha, $hora, $doctor, false, $estados);

            $output = [
                'data' => $error
            ];

            echo json_encode($output);
            break;


            /* CREAR PLAN DE TRATAMIENTO */
        case "nuevoUpdatePlantratamiento":

            $msg  = '';
            $error = '';
            $idtratamiento = 0;

            $idpaciente = GETPOST('idpaciente');

            #ASOCIAR CITA A UN PLAN DE TRATAMIENTO
            #CUANDO LA CITA SEA
            $idcita         = ( GETPOST('idcitadet') == "") ? 0: GETPOST('idcitadet'); #EL ID DE LA CITA PUEDE SER 0 O MAYOR A 0
            if($idcita!=0){
                $iddoctor   = $db->query("select fk_doc from tab_pacientes_citas_det where rowid = ".$idcita)->fetchObject()->fk_doc;
            }else{
                $iddoctor=0;
            }

            $idplantramAsociarCita  = GETPOST('idplantramAsociar');  #PARA ASOCIAR CITA DE UN PLAN DE TRATAMIENTO YA REALIZADO
            $subaccion              = GETPOST('subaccion');

            //die();
            #SE VALIDA LA CITA EN CASO DE REPETIR LA ASOCIACION AL PLAN DE TRATAMIENTO
            if($idcita!=0 && $subaccion!="CREATE")
            {
                # 0 NO ESTA ASOCIADA A NINGUN PLAN DE TRATAMIENTO
                # VALIDA SI LA CITA YA SE ENCUENTRA ASOCIADA AL PLAN DE TRATAMIENTO
                $list_plantramAsociados = [];

                $sqlCitaAsociada = "SELECT fk_cita , fk_tratamiento , fk_paciente 
                              FROM tab_plan_asoc_tramt_citas where fk_cita = $idcita and fk_tratamiento = $idplantramAsociarCita and fk_paciente = $idpaciente";

                $rslCita = $db->query($sqlCitaAsociada);
                if($rslCita->rowCount() > 0) {
//                    while($objtram = $rslCita->fetchObject()) {
//
//                        $label = "";
//                        if( $objtram->edit_name != "" ){
//                            $label = $objtram->edit_name.  "\n";
//                        }else{
//                            $label = "Plan de Tratamiento ". $objtram->numero. "\n";
//                        }
//                        $list_plantramAsociados[] = $label;
//                    }

                    $error = "Documento Asociado";
                }
            }

            //se asocia la citas al plan de tratamiento
            if($subaccion == "ASOCIAR_CITAS" && empty($error))
            {
                $sql = "INSERT INTO `tab_plan_asoc_tramt_citas` (`fk_paciente`, `fk_cita`, `fk_tratamiento`) VALUES ($idpaciente, $idcita, $idplantramAsociarCita);";
                $result = $db->query($sql);
                if(!$result){
                    $error = 'Ocurrio un error no se pudo asociar la cita a este plan de tratamiento';
                }else{
                    $id = $db->lastInsertId("tab_plan_asoc_tramt_citas");
                    $log->log($id, $log->crear, 'Se ha creado un registro | Se asociado la  Cita N.'.$idcita.' a un Plan de Tratamiento: N. '.str_pad($idplantramAsociarCita, 6,"0", STR_PAD_LEFT), "tab_plan_asoc_tramt_citas");
                }

                /*$sqlP = "UPDATE `tab_plan_tratamiento_cab` SET `fk_doc`= $iddoctor, `fk_cita`= $idcita WHERE `rowid`= $idplantramAsociarCita;";
                $rsP = $db->query($sqlP);
                if(!$rsP){
                    $error = 'Ocurrio un error no se pudo asociar la cita a este plan de tratamiento';
                }*/

            }

            #CREA EL PLAN DE TRATAMIENTO SEA CON UNA CITA ASOCIADA O INDEPENDIENTE
            if($subaccion == "CREATE")
            {

                if(!PermitsModule('Agenda', 'agregar')){
                    $error = 'Ud. No tiene permiso para realizar esta Operación';
                }

                if(empty($error)){

                    $sql1 = "SELECT ifnull(MAX(rowid) + 1, 1) as numero FROM tab_plan_tratamiento_cab";
                    $rs = $db->query($sql1)->fetchObject();

                    $obj1 = $conf->ObtenerPaciente($db, $idpaciente, true);

                    $numero = str_pad($rs->numero, 6, "0", STR_PAD_LEFT);

                    $agenda->tratam_numero              = $numero;
                    $agenda->tratam_fk_doc              = ( $iddoctor == 0 ) ? 0 : $iddoctor;
                    $agenda->tratam_fk_cita             = ( $idcita == 0 ) ? 0 : $idcita; #CITA ID
                    $agenda->tratam_fk_paciente         = $idpaciente;
                    $agenda->tratam_fk_convenio         = $obj1->fk_convenio;
                    $agenda->tratam_ultimacita          = "now()"; //FECHA DE CREACION DE LA CITA POR EL MOMENTO
                    $agenda->tratam_detencion           = '';
                    $agenda->tratam_estado_tratamiento  = 'A'; #ESTADO DEL TRATAMIENTO ACTIVO O INACTIVO
                    $agenda->tratam_situaccion          = 'DIAGNÓSTICO';

                    $error = $agenda->create_plantratamientocab();

                    //retorna un entero el id del plan de tratamiento
                    if((int)$error && is_int($error)){
                        if((int)$error>0){
//                            $idtratamiento = $db->lastInsertId('tab_plan_tratamiento_cab');
                            $idtratamiento         = (int)$error;
                            $idplantramAsociarCita = $idtratamiento;
                        }

                        $sql = "INSERT INTO `tab_plan_asoc_tramt_citas` (`fk_paciente`, `fk_cita`, `fk_tratamiento`) VALUES ($idpaciente, $idcita, $idtratamiento);";
                        $result = $db->query($sql);
                        if(!$result){
                            $error = 'Ocurrio un error no se pudo asociar la cita a este plan de tratamiento';
                        }else{
                            $error = '';
                        }

                    }

                }


            }

            $output = [
                'error'           => $error,
                'idtratamiento'   => tokenSecurityId(($idplantramAsociarCita == 0) ? $idtratamiento : $idplantramAsociarCita), #convert id token plan de tratamiento
                'idpacientetoken' => tokenSecurityId($idpaciente)
            ];

            echo json_encode($output);
            break;

        /*CREAR PLAN DE TRATAMIENTO DETALLE*/
        case "nuevoUpdatePlanTratamientoDetalle":

            $error = '';
            #id del plan de tratamiento cabezera
            $idplantratamiento = GETPOST("idtratamiento");

            #parametros
            $iddetalleplan     = GETPOST('nuevoUpdatedetId'); #id del detalle del tratamiento
            $idpaciente        = GETPOST('idpaciente');
            $datos             = (object)GETPOST('datos');
            $subaccion         = GETPOST('subaccion');
            $detencion         = GETPOST('detencion');


            if( $idplantratamiento != "" || $idplantratamiento > 0 && $idpaciente != 0){

                #nuevo detalle
                if( $subaccion == 'create' ){

                    foreach ($datos as $key => $item)
                    {

                        $prestacion = getnombrePrestacionServicio($item['prestacion']);

                        $agenda->tramdet_fk_tramcab     = $idplantratamiento;
                        $agenda->tramdet_fk_prestacion  = $item['prestacion'];
                        $agenda->tramdet_fk_diente      = $item['iddiente']; # id diente
                        $agenda->tramdet_jsoncaras      = $item['pieza']; # caras seleccionadas matris de caras seleccionadas
                        $agenda->tramdet_subtotal       = $item['subtotal'];
                        $agenda->tramdet_desconvenio    = $item['descConvenio'];
                        $agenda->tramdet_descadicional  = $item['descAdicional'];
                        $agenda->tramdet_total          = $item['total'];
                        $agenda->tramdet_cantidad       = $item['cantidad'];
                        $agenda->tramdet_detencion      = $detencion; #DETENCION TEMPORAL O PERMANENTE
                        $agenda->tramdet_fk_usuario     = $user->id; #EL USUARIO QUE LA CREO
                        $agenda->tramdet_costo_serv     = $prestacion->costo_x_clinica;
                        $agenda->tramdet_precio_serv    = $prestacion->precio_paciente;
                        $agenda->tramdet_iva_serv       = $prestacion->iva;

                        #Obtengo el id del laboratorio
                        $idLab                      = 0;
                        $queLab   = "select * from tab_conf_laboratorios_clinicos l , tab_conf_prestaciones p where p.fk_laboratorio = l.rowid and p.rowid = ".$item['prestacion']." limit 1";
                        $resulLab = $db->query($queLab);
                        if($resulLab && $resulLab->rowCount()>0){
                            $objectLab = $resulLab->fetchObject();
                            $idLab = $objectLab->rowid;
                        }

                        $nomServicio = $prestacion->descripcion;
                        $error = $agenda->create_plantratamientodet($idLab, $nomServicio);
                    }
//                    die();
                }

//                die();
                #modificar detalle
                if( $iddetalleplan > -1 && 1 == 0)
                {

                    foreach ($datos as $key => $item){

                        $agenda->tramdet_fk_tramcab = $idplantratamiento;
                        $agenda->tramdet_fk_prestacion  = $item['prestacion'];
                        $agenda->tramdet_fk_diente      = $item['pieza']['diente'];
                        $agenda->tramdet_jsoncaras      = $item['pieza']['caras']; #matris de caras seleccionadas
                        $agenda->tramdet_subtotal    = $item['subtotal'];
                        $agenda->tramdet_desconvenio = $item['descConvenio'];
                        $agenda->tramdet_descadicional  = $item['descAdicional'];
                        $agenda->tramdet_total          = $item['total'];
                        $agenda->tramdet_cantidad  = $item['cantidad'];

//                        $iddetalleplan = $db->query("select ifnull(l.rowid,'0') as idlab from tab_conf_laboratorios_clinicos l , tab_conf_prestaciones p where p.fk_laboratorio = l.rowid and p.rowid = ".$item['prestacion']." limit 1")->fetchObject()->idlab;

                        $error = $agenda->Updateplantratmdetalle();
                    }

                }


            }else{
                $error = 'Ocurrió un error , no se pudo obtener los parámetros asignados para crear el detalle de este tratamiento, Consulte con soporte Técnico';
            }

//            print_r($error); die();

            $output = [
                'error'         => $error,
            ];

            echo json_encode($output);
            break;

        case 'envio_email_notificacion':

            $error = '';

            $idpaciente            = GETPOST("idpaciente");
            $idcita                = GETPOST('idcita');  //id de la cita detalle
            $asunto                = GETPOST("asunto");
            $from                  = GETPOST("from");
            $to                    = GETPOST("to");
            $subject               = GETPOST("subject");
            $message               = GETPOST("message");

            $programar_email       = json_decode(GETPOST('programar_email'));

            $EmailProgramConfirmar = $programar_email->confirmar;
            $dateProgram           = date('Y-m-d', strtotime(str_replace('/','-',$programar_email->date_program)));


            //validar si tiene permisos
            if(!PermitsModule("Agenda", "modificar")){
                $output = [
                    'registrar'   => '',
                    'error_email' => 'Ud. No tiene permiso para realizar esta Operación'
                ];
                echo json_encode($output);
                die();
            }

            //se valida que la cita no este atrazada
            if(!ValidarFechaActualCita($idcita)){
                $output = [
                    'registrar'   => '',
                    'error_email' => 'Cita Atrazada'
                ];
                echo json_encode($output);
                break;
            }

            //obtengo el objeto conpleto de la cita
            $sqlCitadet     = "SELECT * FROM tab_pacientes_citas_det WHERE rowid = $idcita limit 1";
            $rsuCita = $db->query($sqlCitadet)->fetchObject();
            $rowCitasObject = $rsuCita;
            //Generar token de Formulario Clinica

            /*
                'id_cita'      => id de la cita                                     , 0
                'name_db'      => $conf->EMPRESA->INFORMACION->nombre_db_entity     , 1
                'entity'       => $conf->EMPRESA->INFORMACION->numero_entity        , 2
                'name_clinica' => $conf->EMPRESA->INFORMACION->nombre               , 3
                'logo'         => $conf->EMPRESA->INFORMACION->logo                 , 4
                'token'        => Token para validar los formularios                , 5
            */

            //Solo correos de envios al instante
            //se utiliza para validar los formularios que se envian
            //elimino todos los token asociados a esta citas para volver a recrearlos
            $db->query("DELETE FROM `tab_noti_token_confirmacion` WHERE `fk_cita_agendada`= $idcita; ");

            $idToken =  $db->query("INSERT INTO `tab_noti_token_confirmacion` (`fk_cita_agendada`, `token`) VALUES ($idcita, 'NULL') ");
            if($idToken){
                $idToken = $db->lastInsertId("tab_noti_token_confirmacion");
                $create_token_confirm_citas = [$idcita,md5($conf->EMPRESA->INFORMACION->nombre_db_entity),md5($conf->EMPRESA->INFORMACION->numero_entity),$conf->EMPRESA->INFORMACION->nombre,$conf->EMPRESA->INFORMACION->logo, $idToken];
                $result  =  $db->query("UPDATE `tab_noti_token_confirmacion` SET `token`= '".((tokenSecurityId(json_encode($create_token_confirm_citas))))."'  WHERE `rowid`= $idToken;");
            }else{
                $result=false;
            }

            $token              = tokenSecurityId(json_encode($create_token_confirm_citas));
            $buttonConfirmacion = ConfirmacionEmailHTML( $token );

            //verifico que el token no este repetido
            $resultToken = $db->query("select count(*) as token_count from tab_noti_token_confirmacion where token = '".(tokenSecurityId(json_encode($create_token_confirm_citas)))."' ")->fetchObject()->token_count;

            if($result && $resultToken == 1){

                //obtengo los datos para enviar
                $odontologo         = $db->query("select concat(nombre_doc,' ',apellido_doc) as odontolo  from tab_odontologos where rowid =".$rowCitasObject->fk_doc)->fetchObject()->odontolo;
                $datos = (object)array(
                    'idpaciente' => !empty($idpaciente) ? $idpaciente : 0,
                    'idcita'     => !empty($idcita) ? $idcita : 0,
                    'asunto'     => $asunto,
                    'from'       => $from,
                    'to'         => $to,
                    'subject'    => $subject,
                    'message'    => $message,
                    'feche_cita' => $rowCitasObject->fecha_cita  ,
                    'horaInicio' => $rowCitasObject->hora_inicio ,
                    'odontolog'  => $odontologo ,

                    //Informacion de la clinica
                    'email'     => $conf->EMPRESA->INFORMACION->email,
                    'direccion' => $conf->EMPRESA->INFORMACION->direccion,
                    'celular'   => $conf->EMPRESA->INFORMACION->celular,
                );

                if($EmailProgramConfirmar==1){
                    $error = Email_confirmacion_programDate($datos, $dateProgram, $idcita);
                }else{
                    $error = notificarCitaEmail($datos, $buttonConfirmacion);
                }
            }else{

                $Ouput = [
                    'registrar'   => 'Ocurrio un error al Generar el Token de confirmación <br> <b>Consulte con soporte</b>   ',
                    "error_email" => ""
                ];

                $error = $Ouput;
            }

            echo json_encode($error);
            break;

        case 'UpdateComentarioAdicional':

            $error = '';
            $iddetcita       = GETPOST('iddetcita');
            $comment_addicnl = GETPOST('commentAdicional');

            $sql = "UPDATE `tab_pacientes_citas_det` SET `comentario_adicional`='$comment_addicnl' WHERE `rowid`='$iddetcita';";
            $rs = $db->query($sql);

            if(!$rs)
            {
                $error = 'Ocurrio un error al momento de agregar el comentario Adicional';
            }

            $output = [
                'error'         => $error,
            ];

            echo json_encode($output);
            break;

        //busca los pacientes habilitados y desavilitados
        case 'pacientes_activodesact':
            $search = GETPOST('search');
            $items = [];
            $sqlpaciente = "SELECT rowid , concat(nombre,' ',apellido) as nom FROM tab_admin_pacientes estado where rowid  > 0 ";
            if($search!=""){
                $sqlpaciente .= " and concat(nombre,' ',apellido) like '%$search%' ";
            }
            $sqlpaciente .= " limit 10";
            $result = $db->query($sqlpaciente);
            if($result && $result->rowCount()>0){
                while ($obj = $result->fetchObject() ){
                    $items[] = array( 'id' => $obj->rowid , 'text' => $obj->nom );
                }
            }

            $output = [
                'items' => $items
            ];
            echo json_encode($output);
            break;


        //buscar doctores(a)
        case 'search_doctor':

            $search = GETPOST('search');
            $sql = "select rowid as id,  concat(nombre_doc,' ',apellido_doc) as name_doctor from tab_odontologos where concat(nombre_doc,' ',apellido_doc) like '%".$search."%' and estado='A' ";
            $sql .= " limit 10";
            $result = $db->query($sql);
            if($result){
                if($result->rowCount()>0){
                    $array = $result->fetchAll(PDO::FETCH_ASSOC);
                    foreach ($array as $value){
                        $item[] = array('id' => $value['id'], 'text' => $value['name_doctor'] );
                    }
                }else{
                    $item = [];
                }
            }else{
                $item = [];
            }

            $output = [
                'items' => $item
            ];
            echo json_encode($output);
            break;

        case 'consultar_estado_cita_atrazada':

            $idcita = GETPOST('idcita');
            $result = "";

            $sqlcitaAtrzada = "
                    SELECT 
                     -- validaciones
                     -- citas atrazados con estado no confirmado
                     d.rowid,
                     
                     IF(NOW() > CAST(d.fecha_cita AS DATETIME),
                         CONCAT('Atrasada ',
                                      (SELECT 
                                           CONCAT(s.text)
                                        FROM
                                            tab_pacientes_estado_citas s
                                        WHERE
                                        s.rowid = d.fk_estado_paciente_cita
                                      ),
                                '<br> Fecha : ',
                                DATE_FORMAT(d.fecha_cita, '%Y/%m/%d'),
                                '<br>Hora: ',
                                d.hora_inicio,
                                ' a ',
                                d.hora_fin
                                ),
                                
                            '') AS cita_atrazada
                    FROM 
                    tab_pacientes_citas_cab c , 
                    tab_pacientes_citas_det d
                    WHERE 
                    c.rowid = d.fk_pacient_cita_cab 
                    AND d.rowid = $idcita limit 1";

//            echo '<pre>'; print_r($sqlcitaAtrzada); die();
            $rsatrzada = $db->query($sqlcitaAtrzada);
            if($rsatrzada){
                $ob = $rsatrzada->fetchObject();
                if( $ob->cita_atrazada != "" ){
                    $result = "atrazada";
                }
            }

            echo json_encode(array( 'result' => $result ));
            break;

        case 'validDateNow':

            $error = "";

            $Fecha = GETPOST("dateadd");
            $Hours = GETPOST("hour");

            $DateHour = str_replace("/","-", $Fecha)." ".$Hours;

            $Date = "SELECT 
                    DATE_FORMAT(NOW(), '%Y-%m-%d %H:%i:%s') Now,
                    DATE_FORMAT('".$DateHour."', '%Y-%m-%d %H:%i') AS dateadd,
                    IF(DATE_FORMAT('".$DateHour."', '%Y-%m-%d %H:%i') < DATE_FORMAT(NOW(), '%Y-%m-%d %H:%i'),
                    'Fecha Menor a la Actual',
                    '') AS valid_dateadd 
                    LIMIT 1";
            $result = $db->query($Date);
            if($result){
                $object  = $result->fetchObject();
                $date_cita = strtotime($DateHour);
                $now_date  = strtotime($object->Now);
//                $date_cita = new DateTime(date("Y-m-d H:m:s", strtotime($DateHour)));
//                $now_date  = new DateTime(date("Y-m-d H:m:s", strtotime($object->Now)));


                if($object->valid_dateadd != ""){
                    $error  = "La Fecha Agregada no puede ser menor a la Fecha Actual <br>  ";
                    $error .= " <b>Fecha Actual: </b>".(date('Y/m/d H:m', strtotime($object->Now)))."<br>";
                    $error .= " <b>Fecha de la Cita: </b>".(date('Y/m/d H:m', strtotime($object->dateadd)))."<br>";
                }
            }

            $output =[
                'error' => $error
            ];
            echo json_encode($output);
            break;

        case 'addObservacion':

            $error = "";
            $iddetcita = GETPOST("iddetcita");

            if($iddetcita!=''){
                $result = $db->query("select if((ifnull(comentario_adicional,'')) = '', '', 'Ya tiene asignado una Observación' ) as observacion from tab_pacientes_citas_det where rowid = $iddetcita")->fetchObject();
                if($result->observacion != ""){
                    $error = $result->observacion;
                }
            }else{
                $error = "Ocurrio un error con la Operación";
            }

            $output =[
                'error' => $error
            ];
            echo json_encode($output);
            break;


        case 'addnewSatusCitas':

            $error = '';

            $textSatus    = GETPOST('statusCitas');
            $colorSatus   = GETPOST('colorSatus');

            $qu = "INSERT INTO `tab_pacientes_estado_citas` (`text`, `color`) VALUES ('".$textSatus."', '".$colorSatus."');";
            $result = $db->query($qu);
            if($result){
                $error = "";
            }else{
                $error = "Ocurrion un error con la Operación, Consulte con Soporte";
            }
            $output =[
                'error' => $error
            ];
            echo json_encode($output);
            break;

        case 'statusList2':
            $error = '';
            $data = [];
            $que = "select rowid , text, color , system, comment from tab_pacientes_estado_citas where system = 0";
            $result = $db->query($que);
            if($result && $result->rowCount()>0){
                $result_arr = $result->fetchAll();
                foreach ($result_arr as $key => $arr){
                    $row = array();
                    $row[] = $arr['text'];
//                    $row[] = $arr['comment'];
                    $row[] = "<div  class='text-center' style='background-color: ".$arr['color']."; width: 40px; height: 10px'></div>";
                    $row[] = "<a href='#' onclick='EliminarStatus(".$arr['rowid'].")' title='Eliminar Estado'> <i class='fa fa-trash-o' style='color: darkred'></i> </a>";
                    $row['idstatus'] = $arr['rowid'];
                    $data[] = $row;
                }
            }
            $output =[
                'data' => $data
            ];
            echo json_encode($output);
            break;

        case 'EliminaStatus':

            $error = "";

            $idStatus = GETPOST("id");
            if($idStatus!=""||$idStatus!=0){
                $citasAsocidas = $db->query("select fecha_cita, fk_estado_paciente_cita, fk_especialidad from tab_pacientes_citas_det where fk_estado_paciente_cita = $idStatus");
                if($citasAsocidas && $citasAsocidas->rowCount()>0){
                    $error = "Se detecto registro Asociado";
                }else{
                    $result = $db->query("DELETE FROM `tab_pacientes_estado_citas` WHERE `rowid`= $idStatus;");
                    if(!$result){
                        $error = "Ocurrió un error con la Operación Eliminar";
                    }
                }
            }else{
                $error = "Ocurrió un error con la Operación No se detectaron Parámetros Asignados, Consulte con soporte";
            }
            $output =[
                'error' => $error
            ];
            echo json_encode($output);
            break;



        case 'fetch_cita_now':

            $error = "";
            $fetch = array();
            $idCita = GETPOST("idCita");

            $sql = "SELECT 
                    rowid,
                    fk_pacient_cita_cab AS id_paciente,
                    fk_doc as id_doc, 
                    fk_especialidad AS id_especialidad,
                    duracion,
                    fecha_cita,
                    hora_cita,
                    hora_inicio,
                    hora_fin
                FROM
                    tab_pacientes_citas_det
                WHERE
                    rowid = ".$idCita;
            $result = $db->query($sql);
            if($result){
                if($result->rowCount()>0){
                    $fetch = $result->fetchAll(PDO::FETCH_ASSOC);
                    $fetch = $fetch[0];
                }else{
                    $error = "Ocurrió un error con la Operación. No se pudo obtener información";
                }
            }else{
                $error = "Ocurrió un error con la Operación. No se pudo obtener información";
            }

            $output = array(
                'error' => $error,
                'fetch' => $fetch
            );
            echo json_encode($output);
            break;

        case 'reagendar_cita_paciente':

            $error = "";
            if(!PermitsModule("Agenda", "modificar")){
                $permits = false;
            }else{
                $permits = true;
            }

            $fecha     = date("Y-m-d", strtotime(str_replace('/','-', GETPOST("fecha"))));
            $hora      = GETPOST("hora");
            $duracion  = GETPOST("duracion");
            $fk_doc    = GETPOST("fk_doc");

            $fechaInicio = date("Y-m-d H:i:s", strtotime("$fecha $hora:00"));
            $fechaFin    = strtotime("+$duracion minute", strtotime($fechaInicio));

            $horaFin     = date("H:i:s", $fechaFin);
            $horaInicio  = $hora.":00";

//            print_r(date("Y-m-d H:i:s", strtotime($fecha." ".$horaFin)) ); die();
            if($permits){
                $idCita             = GETPOST("idCita");
                $validarHoraFecha   = validarFechaHoraCitaReagendamiento($idCita);
                if($validarHoraFecha){ //true

                    if(date("Y-m-d H:i:s", strtotime($fechaInicio)) < date("Y-m-d H:i:s")){
                        $error = "Fecha menor a la Actual ".date("Y/m/d H:i:s", strtotime($fechaInicio));
                    }

                    if(empty($error)){

                         $fechaCita = date("Y-m-d H:i:s", strtotime($fecha." ".$horaFin));
                         $sql    = "UPDATE `tab_pacientes_citas_det` SET `fecha_cita`='$fechaCita', `hora_cita`='$horaInicio', `hora_inicio`='$horaInicio', `hora_fin`='$horaFin' WHERE `rowid`='$idCita';";
                         $result = $db->query($sql);
                         if($result){
                             $date = date("Y/m/d H:i:s", strtotime($fechaCita));
                             $log->log($idCita, $log->modificar, "Se a actualizado la cita Numero: $idCita | Nueva fecha de Agendamiento ".$date, 'tab_pacientes_citas_det');
                         }
                    }
                }else{
                    $error = "Ya se encuentra agendada una cita con esta fecha seleccionada ".GETPOST('fecha')." hora: ".GETPOST('hora');
                }
            }else{
                $error = "Ud. No tiene permiso para realizar esta Operación";
            }

            $output = array(
                'error' => $error,
            );
            echo json_encode($output);
            break;
    }

}

function list_citas($doctor, $estado = array(),  $fechaInicio, $fechaFin, $MostrarCitasCanceladasEliminadas, $paciente , $n_citas, $start2)
{

    global $db, $permisos;


    if(!PermitsModule("Agenda", "consultar"))
        $PermisoConsultar = " 1<>1 ";
    else
        $PermisoConsultar = "";

    $Total          = 0;
    if(GETPOST('validSatus')==1)
        $start  = $start2;
    else
        $start  = $_POST["start"];

    $length         = $_POST["length"];
//    $colum_ord      = $_POST["order"][0]['column'];
//    $direcc_ord     = $_POST["order"][0]['dir'];


//    print_r($start); die();
    $data = array();

    $fecha_hoy = date("Y-m-d");

    $sql = "SELECT 
            date_format(d.fecha_cita, '%Y/%m/%d')  as fecha_cita,         
            d.hora_inicio , 
            d.hora_fin ,
            d.rowid  as id_cita_det,
            concat(p.nombre ,' ',p.apellido) as paciente, 
            p.rowid as idpaciente,                   
            p.telefono_movil, 
            concat(o.nombre_doc,' ', o.apellido_doc) as doct, 
            s.text as estado,
            s.color as color,
            d.fk_estado_paciente_cita , 
            c.comentario ,
            ifnull(es.nombre_especialidad,'General') as especialidad,
            p.telefono_movil as telefono_movil,
            d.fk_doc as iddoctor , 
            p.email as email, 
            d.comentario_adicional as comentario_adicional,
            c.fk_paciente as idpaciente  ,
             -- validaciones
             -- citas atrazados con estado no confirmado
             IF( now() > CAST(d.fecha_cita AS DATETIME)  
                        && d.fk_estado_paciente_cita in(2,1,3,4,7,8,9,10,11,5,  (select statusc.rowid from tab_pacientes_estado_citas statusc where statusc.system=0) )  , 
                            concat('Atrasada ', (select concat(s.text) from tab_pacientes_estado_citas s where s.rowid = d.fk_estado_paciente_cita) , 
                                    '\n Fecha : ' , date_format(d.fecha_cita, '%Y/%m/%d') , '\n Hora: ' , d.hora_inicio ,' h ' , d.hora_fin) , ''
                                    ) as cita_atrazada
									            
         FROM 
            tab_pacientes_citas_cab c 
				inner join
            tab_pacientes_citas_det d on d.fk_pacient_cita_cab = c.rowid
				inner join 
			tab_admin_pacientes p on c.fk_paciente = p.rowid
				inner join
			tab_pacientes_estado_citas s on s.rowid = d.fk_estado_paciente_cita
				inner join
			tab_odontologos o on o.rowid = d.fk_doc
				left join
			tab_especialidades_doc es on es.rowid = d.fk_especialidad
            where 
         ";

    if(!empty($PermisoConsultar))
        $sql .= $PermisoConsultar;
    else
        $sql .= " 1=1 ";

    if(!empty($doctor)) {
        $sql .= " and d.fk_doc in(".$doctor.")";
    }

    if(!empty($estado)) {
        $sql .= " and d.fk_estado_paciente_cita in(".$estado.") ";
    }

    if(!empty($fechaInicio) && !empty($fechaFin)) {
        $sql .= " and cast(d.fecha_cita as date) between cast('$fechaInicio' as date) and cast('$fechaFin' as date) ";
    }

    if(!empty($MostrarCitasCanceladasEliminadas)) {
        $sql .= " and d.estado = 'E' or  d.fk_estado_paciente_cita = 9 ";
    }

    if(!empty($paciente)) {
        $sql .= " and c.fk_paciente in($paciente) ";
    }

    if(!empty($n_citas)) {
        $sql .= " and d.rowid like '%$n_citas%' ";
    }

    /*
    if ($colum_ord == 3) {
        $sql .= " order by 2 $direcc_ord";
    }else if ($colum_ord == 4) {
        $sql .= " order by 3 $direcc_ord";
    }else if ($colum_ord == 5) {
        $sql .= " order by 2  $direcc_ord";
    }else if ($colum_ord == 6) {
        $sql .= " order by 5 $direcc_ord";
    }else if ($colum_ord == 7) {
        $sql .= " order by 6 $direcc_ord";
    }else{
        $sql .= " order by d.fecha_cita desc ";
    }*/


    $sql .= " order by d.rowid desc ";
    $sqlTotal = $sql;

    if($start || $length){
        $sql.=" LIMIT $start,$length;";
    }


//    echo '<pre>';print_r($sql); die();

    $resultTotal = $db->query($sqlTotal);
    $rs = $db->query($sql);

    if( $rs && $rs->rowCount() > 0 ){

        $Total = $resultTotal->rowCount();
        $iu  = 0; #acumulador
        while ($acced = $rs->fetchObject()){

            $row = array();
            //checked box
            $row[] = "<span class='custom-checkbox-myStyle' style='margin-left: 3px'>
								<input type='checkbox' id='checked-detalleCitas-$iu' class='checked_detalleCitas' name='checkedCitas' data-idcitadet='$acced->id_cita_det'>
								<label for='checked-detalleCitas-$iu' ></label>
                      </span>";

            //numero o codigo de cita
            $numeroCita = "<table style='font-weight: bold'>
                                <tr>
                                    <td> C_".(str_pad($acced->id_cita_det, 5, "0", STR_PAD_LEFT))." </td>
                                </tr>
                           </table>";

            $row[] = $numeroCita;

            $html1 = "";
            $html1 .= "<p class='text-center no-margin' style='font-weight: bold'>".date('Y/m/d', strtotime($acced->fecha_cita))."</p>";
            $html1 .= "<div style='background-color: $acced->color; padding: 3px; font-weight: bold'>";
                $html1 .= "<p class='text-center no-margin'>$acced->hora_inicio</p>";
                $html1 .= "<p class='text-center no-margin'><i class='fa fa-arrow-circle-o-down'></i></p>";
                $html1 .= "<p class='text-center no-margin'>$acced->hora_fin</p>";
            $html1 .= "</div>";

            $row[] = $html1;

            //PACIENTES - JUNTO CON DROPDONW

            //ID IMPORTANTE YA QUE ES UN TOKEN CREADO COMO UN ID DE LA CITAS GENERADO EN UN BINARIO HEXADECIMAL
            $token = tokenSecurityId( $acced->idpaciente); #ME RETORNA UN TOKEN
            $view  = "dop"; #view vista de datos personales admin pacientes

            //url datos personales
            $Url_datospersonales = DOL_HTTP ."/application/system/pacientes/pacientes_admin?view=$view&key=".KEY_GLOB."&id=$token";

            $html2 = "";
            $html2 .= "<div class='form-group col-md-12 col-xs-12 col-lg-12 col-sm-12' >";

            $html2 .= "<div class='col-xs-10 col-md-10 '> <i class='fa fa-user'></i> $acced->paciente </div>";
                $html2 .= "
                        <div class='col-xs-2 col-md-2 no-padding pull-right ' style='position: relative'>
                            <div class='dropdown pull-right' >
                                <button class='btn btnhover  btn-xs dropdown-toggle' type='button' data-toggle='dropdown' style='height: 100%'> <i class='fa fa-ellipsis-v'></i> </button>
                                <ul class='dropdown-menu' style='z-index: +2000'>";

                                $tienePlanTratamiento = "";
                                $tieneComentarioadicional = "";

                                $sql2 = "SELECT * FROM tab_plan_tratamiento_cab where fk_cita =  $acced->id_cita_det";
                                $rs2 = $db->query($sql2);
                                if($rs2->rowCount()>0){
                                    $tienePlanTratamiento = "disabled_link3";
                                }

                                if($acced->comentario_adicional != ""){
                                    $tieneComentarioadicional = "disabled_link3";
                                }

                                $html2 .= "<li>   <a style='cursor: pointer; ' class='$tienePlanTratamiento' onclick='create_plandetratamiento($acced->idpaciente, $acced->id_cita_det, $acced->iddoctor , $(this));'  >Plan de Tratamiento</a> </li>";
                                $html2 .= "<li>   <a style='cursor: pointer; ' href='". DOL_HTTP ."/application/system/pacientes/pacientes_admin/?view=pagospaci&key=".KEY_GLOB."&id=". tokenSecurityId($acced->idpaciente) ."&v=paym' >Recaudación</a> </li>";
                                $html2 .= "<li>   <a style='cursor: pointer; ' href='". $Url_datospersonales ."' >Datos personales</a> </li>";
                                $html2 .= "<li class='hide'>   <a style='cursor: pointer; ' >Cambiar  fecha/cita</a> </li>";
                                $html2 .= "<li>   <a  style='cursor: pointer; ' data-toggle=\"modal\" data-target=\"#modal_coment_adicional\" onclick='clearModalCommentAdicional($acced->id_cita_det, $(this))' class='$tieneComentarioadicional'  >Agregar Comentario Adicional</a> </li>";

                         $html2 .= "</ul>";
                    $html2 .= "</div> 
                            </div>";
            $html2 .= "</div>";

            #comentario y numero de telefonos
            $html4  = "";
            $html4 .= "<div class='form-group col-md-12 col-xs-12'>
                             <div class='col-xs-12 col-md-12'>";

            #COMENTARIOS OPCIONAL - PACIENTE
            if(!empty($acced->comentario))
            {

                $html4 .= '<span class="text-sm text-justify "    title="' .$acced->comentario. '">  
                                <i class="fa fa-x3 fa-comment" style="cursor: pointer" ></i> '. $acced->comentario .'
                            </span> <br>';

            }

            #TELEFONO DEL PACIENTE
            if(!empty($acced->telefono_movil))
            {

                $html4 .= '<span> <i class="fa fa-phone-square" style="cursor: pointer" title=" '. $acced->telefono_movil .' "></i> '. $acced->telefono_movil .'  </span>';

            }

            #CITAS ATRAZADAS CON ESTADO NO CONFIRMADO - ID DEL ESTADO = 2
            if(!empty($acced->cita_atrazada))
            {
                $html4 .= '<small style="white-space: pre-wrap;  color: red; display: block; font-weight: bold"  class="" title="'. $acced->cita_atrazada .'"> '. $acced->cita_atrazada .' </small>';
            }

            $html4 .= "</div>
                </div>";

            $row[] = $html2 ."".$html4;

            //DOCTOR
            $html5 = "<div class='form-group col-md-12 col-xs-12 col-sm-12'>";
                $html5 .= "<span class='text-left'>Doc(a). $acced->doct</span> <br>";
                $html5 .= "<span class='trunc'> <i class='fa fa-user-md'></i> &nbsp;&nbsp; $acced->especialidad </span>";

                if($acced->comentario_adicional){
                    $html5 .= "<br><small class=' text-sm' title='$acced->comentario_adicional'> <i class='fa fa-comment'></i> &nbsp;&nbsp; $acced->comentario_adicional </small>";
                }
            $html5 .= "</div>";
            $row[] = $html5;

            #DROPDOWN  ESTADOS DE CITAS AGENDADAS-------------------------------------------------------------------------------------------------

            #se realiza una validacion para el id del estado numero 10 ( confirmado por paciente )
            $msg_confirmacion_estado10 = "";
            if($acced->fk_estado_paciente_cita==10){ //si el estado es confirmado por paciente se valida el tipo
                $query  = "SELECT fk_cita, estado, action as accion_confirm FROM tab_noti_confirmacion_cita_email WHERE fk_cita = ".$acced->id_cita_det." and estado = 10 and action <> '' ";
                $resultconfirm = $db->query($query);
                if($resultconfirm && $resultconfirm->rowCount()==1){
                    $obj = $resultconfirm->fetchObject();
                    if($obj->accion_confirm == 'ASISTIR'){
                        $msg_confirmacion_estado10 = " <br><small class=' text-sm' style='font-weight: bold !important; color: green;'>Este paciente a notificado que si asistirá a la cita</small>";
                    }
                    if($obj->accion_confirm == 'NO_ASISTIR'){
                        $msg_confirmacion_estado10 = " <br><small class=' text-sm' style='font-weight: bold !important; color: red'>Este paciente a notificado que no asistirá a la cita</small>";
                    }
                }
            }

            $html3 = "";
            $html3 .= "<div class='form-group col-md-12 col-xs-12'>
                        <div class='col-xs-12 col-ms-10 col-md-10 no-padding'> 
                            <label class='' title='$acced->estado' >$acced->estado 
                            ".$msg_confirmacion_estado10." 
                            </label> 
                        </div>";

            $html3 .= "<div class='col-xs-12 col-ms-2 col-md-2 no-padding no-margin '>
                            <div class='dropdown pull-right' >";

                $html3 .= "    <button class='btn btnhover  dropdown-toggle btn-xs ' id='estadoDropw' type='button' data-toggle='dropdown' style='height: 100%'> <i class='fa fa-ellipsis-v'></i> </button>";
                        $html3 .= " <ul class='dropdown-menu pull-right' style='position: absolute!important;'>";

                        $sqlMenuDrowpdown = "SELECT rowid,text,comment,system,color FROM tab_pacientes_estado_citas where rowid not in(11)";
                        $rsdrown = $db->query($sqlMenuDrowpdown);

                        if($rs->rowCount() > 0) {
                            while ($rowxs = $rsdrown->fetchObject()) {

                                $todosdata = "";
                                $dataTelefono = "";
                                $dataEmailPaciente = "";
                                $addclases = "";
                                $statusAdd = "";

                                //whatsapp
                                if($rowxs->rowid == 8){
                                    $telefono = substr($acced->telefono_movil, 1, 9);
                                    $dataTelefono = "data-telefono='593$telefono'";
                                }
                                //notificar por email
                                if($rowxs->rowid == 1){
                                    $dataEmailPaciente = "data-email='$acced->email'";
                                }
                                //no debe verse el estado confirmado e-mail x paciente - este estado solo lo confirma el paciente
                                if($rowxs->rowid == 10){
                                    $addclases .= " hide "; //oculto este estado
                                }
                                //system 0 Creados x el usuario
                                if($rowxs->system == 0){
                                    $statusAdd = "color: blue;";
                                }

                                    $todosdata .= " ".
                                    $dataTelefono." ".
                                    $dataEmailPaciente." ";

                                //muestra la cita con el estado seleccionado
                                if($acced->fk_estado_paciente_cita == $rowxs->rowid ) {
                                    $html3 .= "<li> <a class='activeEstadoCita' $todosdata   style='cursor: pointer; ' >$rowxs->text</a> </li>";
                                }
                                else{
                                    $html3 .= "<li> <a class=' $addclases '  data-text='$rowxs->text' $todosdata  onclick='AplicarStatusAgendada($rowxs->rowid, $acced->id_cita_det, $(this), $acced->idpaciente)' style='cursor: pointer;$statusAdd' >$rowxs->text</a> </li>";
                                }
                            }
                        }
                            $html3 .= "<li> <a href='#addStatusCitas' data-toggle='modal'  style='cursor: pointer;color: grey; ' title='Agregar un nuevo estado '><b>Agrega Nuevo Estado</b></a> </li>";
                        $html3 .= " </ul>"; #dropdown end
                $html3 .= "</div>";
            $html3 .= "</div> 
            </div>";

            #END DROPDOWN-----------------------------------------------------------------------------------------------
            $row[] = $html3;

            #DIAGNOSTICO O OTROS ESTADOS
//            $html6 = "<div class='col-md-12 col-xs-12'>
//                        <p class='text-bold'  style='text-align: center !important; color: #333333; font-size: 1.4rem; background-color: #E5E7E9; padding: 3px; border-radius: 3px'> Diagnostico </p>
//                    </div>";
//
//            $row[] = $html6;

            #estado id
            $row[] = $acced->fk_estado_paciente_cita;

            $data[] = $row;

            $iu++; #recorrido
        }

    }

    $resultFinal = [
        'datos'   => $data,
        'total'   => $Total,
        'permiso' => (empty($PermisoConsultar)?'':'no_permiso')
    ];

    return $resultFinal;
}

//Validar fechas
function hourIsBetween($from, $to, $input)
{
    $dateFrom = DateTime::createFromFormat('!H:i', $from);
    $dateTo = DateTime::createFromFormat('!H:i', $to);
    $dateInput = DateTime::createFromFormat('!H:i', $input);
    if ($dateFrom > $dateTo) $dateTo->modify('+1 day');
    return ($dateFrom <= $dateInput && $dateInput <= $dateTo) || ($dateFrom <= $dateInput->modify('+1 day') && $dateInput <= $dateTo);

}

function fecth_diariaHorasGlobal($fecha, $hora, $doctor, $export, $estados)
{
    global $db, $permisos;

    $detalle = array();

    $fechaInicio = date("Y-m-d", strtotime($fecha));

    $sql = "SELECT 
        
        (SELECT 
                CONCAT(p.nombre, ' ', p.apellido)
            FROM
                tab_admin_pacientes p
            WHERE
                p.rowid = c.fk_paciente) AS paciente,
        (SELECT 
                CONCAT(o.nombre_doc, ' ', o.apellido_doc)
            FROM
                tab_odontologos o
            WHERE
                o.rowid = d.fk_doc) AS doctor,
        (SELECT 
                p.rut_dni
            FROM
                tab_admin_pacientes p
            WHERE
                p.rowid = c.fk_paciente) AS rudcedula,
        (SELECT 
                p.telefono_movil
            FROM
                tab_admin_pacientes p
            WHERE
                p.rowid = c.fk_paciente) AS telefonoMobil,
        c.comentario AS observacion,
        d.duracion,
        d.fecha_cita,
        d.hora_cita,
        d.hora_fin,
        (SELECT 
                s.nombre_especialidad
            FROM
                tab_especialidades_doc s
            WHERE
                s.rowid = d.fk_especialidad) AS especialidad
        FROM
        tab_pacientes_citas_det d,
        tab_pacientes_citas_cab c
        WHERE
        d.fk_pacient_cita_cab = c.rowid  
        ";

    if( $fecha != ''){
        $sql .= " and date_format(d.fecha_cita, '%Y-%m-%d') = '$fechaInicio' ";
    }

    if($doctor != ''){
        $sql .= " and d.fk_doc = $doctor";
    }

    if(count($estados) > 0 || !empty($estados)){
        $sql .= " and d.fk_estado_paciente_cita in( ". implode(',', $estados) . ") ";
    }

//    print_r($estados);
    $sql .= " ".$permisos->consultar;
    $rs = $db->query($sql);
//    print_r($sql);
    if($rs && $rs->rowCount()>0)
    {
        while ($row = $rs->fetchObject())
        {
            if($export==true)
            {
                $detalle[] = $row;
            }else{

                $row12 = array();

                $row12[] = $row->hora_cita;
                $row12[] = $row->doctor;
                $row12[] = $row->paciente;
                $row12[] = $row->rudcedula;
                $row12[] = $row->telefonoMobil;
                $row12[] = $row->especialidad;

                $info = "";
                if($row->observacion != ""){
                    $info = "<a href='#' style='display:inline-block' title='información'> <i class='fa fa-info-circle'></i>";
                }
                $row12[] = "<p class='trunc' style='width: 220px; display:inline-block'> $info </a> $row->observacion</p>" ;

                $detalle[] = $row12;
            }

        }

    }

    return $detalle;
}

function notificarCitaEmail($datos, $token_confirmacion)
{

    global $db, $conf, $user;

//    require_once DOL_DOCUMENT .'/public/lib/PHPMailer2/src/Exception.php';
//    require_once DOL_DOCUMENT .'/public/lib/PHPMailer2/src/PHPMailer.php';
//    require_once DOL_DOCUMENT .'/public/lib/PHPMailer2/src/SMTP.php';


    require_once DOL_DOCUMENT .'/public/lib/PHPMailer/PHPMailerAutoload.php';

//    require_once DOL_DOCUMENT .'/public/lib/PHPMailer/PHPMailerAutoload.php';
//    require_once DOL_DOCUMENT .'/public/lib/PHPMailer/class.smtp.php';

    $error = '';

    $asunto     = $datos->asunto;
    $from       = $datos->from;
    $to         = $datos->to;
    $message    = $datos->message;
    $subject    = $datos->subject;

    $labelPaciente = getnombrePaciente($datos->idpaciente)->nombre.' '.getnombrePaciente($datos->idpaciente)->apellido;

//    $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
    $mail = new PHPMailer();

    $messabody = "";
    if($message!=""){
        $messabody = "<br><b>Mensaje:</b>&nbsp; ". utf8_decode($message) ." <br>";
    }

    $spanishxDate = GET_DATE_SPANISH(date('Y-m-d', strtotime($datos->feche_cita))) ." - hora ".$datos->horaInicio;
    $src_logo = !empty($conf->EMPRESA->INFORMACION->logo) ? DOL_HTTP.'/logos_icon/'.$conf->NAME_DIRECTORIO.'/'.$conf->EMPRESA->INFORMACION->logo :  DOL_HTTP .'/logos_icon/logo_default/icon_software_dental.png';

    $datosEmail['mess']         = (!empty($message))?utf8_decode($message):"";
    $datosEmail['name_clinica'] = $conf->EMPRESA->INFORMACION->nombre;
    $datosEmail['recordatorio'] = $spanishxDate;
    $datosEmail['token']        = $token_confirmacion;
    $datosEmail['telefono']     = $datos->celular;
    $datosEmail['direccion']    = $datos->direccion;
    $datosEmail['odontolog']    = $datos->odontolog;

    $card = boxsizingMenssaje($datosEmail);

    $htmlSend = "<br><div style='font-size: 18px'> <b>Estimado/a:</b>&nbsp;$labelPaciente  <br><br> </div>";


    /*
    $headerLine  = "";
    $headerLine .= $this->HeaderLine("Organization" , SITE);
    $headerLine .= $this->HeaderLine("Content-Transfer-encoding" , "8bit");
    $headerLine .= $this->HeaderLine("Message-ID" , "<".md5(uniqid(time()))."@{$_SERVER['SERVER_NAME']}>");
    $headerLine .= $this->HeaderLine("X-MSmail-Priority" , "Normal");
    $headerLine .= $this->HeaderLine("X-Mailer" , "Microsoft Office Outlook, Build 11.0.5510");
    $headerLine .= $this->HeaderLine("X-MimeOLE" , "Produced By Microsoft MimeOLE V6.00.2800.1441");
    $headerLine .= $this->HeaderLine("X-Sender" , $this->Sender);
    $headerLine .= $this->HeaderLine("X-AntiAbuse" , "This is a solicited email for - ".SITE." mailing list.");
    $headerLine .= $this->HeaderLine("X-AntiAbuse" , "Servername - {$_SERVER['SERVER_NAME']}");
    $headerLine .= $this->HeaderLine("X-AntiAbuse" , $this->Sender); */


    $mail->IsSMTP();
    $mail->Mailer = "smtp";
    $mail->CharSet = 'UTF-8';
    $mail->Host = "mail.adminnube.com";
    $mail->SMTPDebug = 0;
    $mail->SMTPAuth = true;
    $mail->Port = 465;
    $mail->SMTPAutoTLS = TRUE;
    $mail->SMTPSecure = "ssl";

    $mail->Username = $conf->EMPRESA->INFORMACION->correo_service;//correo del servidor
    $mail->Password = $conf->EMPRESA->INFORMACION->password_service;//password de servidor de correo

    $mail->Subject = "Clinica dental ".$conf->EMPRESA->INFORMACION->nombre; //nombre de la clinica
    $mail->addCustomHeader("'Reply-to:".$conf->EMPRESA->INFORMACION->correo_service."'");
    $mail->isHTML(TRUE);
    $mail->msgHTML("Notificación Clinica ".$conf->EMPRESA->INFORMACION->nombre);
    $mail->setFrom($conf->EMPRESA->INFORMACION->correo_service, $conf->EMPRESA->INFORMACION->nombre);
    $mail->addAddress($to);

    #$mail->msgHTML("");
    #$mail->headerLine($headerLine);

    $mail->Body = $htmlSend."".$card;
    $error_insert_notific_email = "";#Se usa para comprobar el registro


    if($conf->EMPRESA->INFORMACION->conf_email != ""){

        if(!$mail->send()){
            //Correo no enviado
            $error = 'Ocurrio un problema con el servidor no pudo enviar el correo, intentelo de nuevo o consulte con soporte  Tecnico' .'<br> <b> '. $mail->ErrorInfo .' </b>';
        }else{
            $error = 1; #Correo enviado
        }


        #SI EL $error = 1 -- EL EMAIL SE ENVIO CORRECTAMENTE
        if($error == 1 ) {

            $sql = "INSERT INTO `tab_notificacion_email` (`asunto`, `from`, `to`, `subject`, `message`, `estado`, `fk_paciente`, `fk_cita`, `fecha`, user_athor) ";
            $sql .= "VALUES (";
            $sql .= " '$asunto' ,";
            $sql .= " '$from' ,";
            $sql .= " '$to' ,";
            $sql .= " '$subject' ,";
            $sql .= " '$message' ,";
            $sql .= " 'A' ,";
            $sql .= " '$datos->idpaciente' ,";
            $sql .= " '$datos->idcita' ,";
            $sql .= "  now() ,";
            $sql .= "  $user->id ";
            $sql .= " );";
            $rs = $db->query($sql);

            if(!$rs){
                $error_insert_notific_email = 'Ocurrio un error  <b style="color: green">E-mail enviado</b> pero no se registro la confirmación <br>Consulte con soporte';
            }

            if($rs) {

                $fk_notifi_id = $db->lastInsertId('tab_notificacion_email');

                $queryDel   = " DELETE FROM tab_noti_confirmacion_cita_email where rowid > 0 and fk_cita = $datos->idcita ";
                $r1 = $db->query($queryDel);
                if($r1)
                {

                    //se elimina la lista de confirmaciones asociado a dicha cita para eviar la duplicida de confirmacion
                    $resultConfNoti = $db->query("DELETE FROM `tab_noti_confirmacion_cita_email` WHERE `rowid`>0 and `fk_cita`= ".$datos->idcita);

                    $queryNoti  = " INSERT INTO `tab_noti_confirmacion_cita_email` (`fk_paciente`, `fk_cita`, `estado` , `fk_noti_email`) ";
                    $queryNoti .= " VALUES(";
                    $queryNoti .= " $datos->idpaciente ,";
                    $queryNoti .= " $datos->idcita ,";
                    $queryNoti .= " 1 ,"; #notificar x email
                    $queryNoti .= " $fk_notifi_id ";
                    $queryNoti .= " )";
                    $db->query($queryNoti);

                    $idnotiConfirmacion = $db->lastInsertId('tab_noti_confirmacion_cita_email'); #id de la notificaion de insert confirmacion

                    if(!empty($idnotiConfirmacion) )
                    {
                        $Update = " UPDATE `tab_pacientes_citas_det` SET `fk_cita_email_noti` = $idnotiConfirmacion WHERE `rowid` = '$datos->idcita' ";
                        $db->query($Update);
                    }
                }
            }

        }

    }else{
        $error = 'No esta asignado el acceso de e-mail';
    }

    $output = [
        'registrar'   => $error_insert_notific_email ,
        'error_email' => ($error==1)?"":$error
    ];

    return $output;

}

//confirmacion de email programado por fecha
function Email_confirmacion_programDate($datos=array(), $fecha_programa, $id_cita){

    global $db, $user;

    $error="";

    $fecha_programa .= " 23:00:00";

    $query = "select 
                  cast(concat(cast(fecha_cita as date),' ',hora_inicio) as datetime) as fecha_cita_datetime,
                  cast(fecha_cita as date) as fecha_cita , 
                  (hour(now())) as NowTime ,
                  now() as NowDateTime
              from tab_pacientes_citas_det 
              where rowid = $id_cita";
    $result = $db->query($query)->fetchObject();

    //solo puede realizar esta operacion si la hora actual es menor a las 22horas
    //solo en caso si la fecha programada es para el mismo dia
    if(date("Y-m-d", strtotime($result->NowDateTime)) == date('Y-m-d', strtotime($fecha_programa)) ){
        if(($result->NowTime >= 22)){
            $ouput = [
                "registrar"   => "No puede realizar esta Operación <br> Esta funcionalidad solo esta disponible hasta las 22horas <br> <small> <b>Para más información consulte con soporte</b> </small>",
                "error_email" => ""
            ];
            return $ouput;
        }
    }

    if( date('Y-m-d H:m:s', strtotime($result->fecha_cita_datetime)) < date('Y-m-d H:m:s', strtotime($fecha_programa)) ){
        $error = "Fecha de programación Invalidad";
        $ouput = [
            "registrar"   => $error,
            "error_email" => ""
        ];
        return $ouput;
    }

//    die();
//    print_r(date('Y-m-d H:m:s', strtotime($result->fecha_cita_datetime)));
//    echo '<br>';
//    print_r(date('Y-m-d H:m:s', strtotime($fecha_programa)));
//    die();

    $asunto     = $datos->asunto;
    $from       = $datos->from;
    $to         = $datos->to;
    $message    = $datos->message;
    $subject    = $datos->subject;
    $odontolog  = $datos->odontolog;

    //los email de citas de confirmacion programados no pueden estar duplicadas
    //se realiza una validacion para eliminar los email programados y crea uno nuevo

    //P email Programado
    $result = $db->query("select count(*) as count from tab_notificacion_email where fk_cita = '".$datos->idcita."' and program=1 and estado='P' and program_date!='' ")->fetchObject()->count;
    if($result>0){
        $db->query("DELETE FROM `tab_notificacion_email` WHERE `fk_cita`='".$datos->idcita."';");
    }

    $id_notificacion_email = 0;

    $sql = "INSERT INTO `tab_notificacion_email` (`asunto`, `from`, `to`, `subject`, `message`, `estado`, `fk_paciente`, `fk_cita`, `fecha`, `program`, `program_date`, `user_athor` ) ";
    $sql .= "VALUES (";
    $sql .= " '$asunto' ,";
    $sql .= " '$from' ,";
    $sql .= " '$to' ,";
    $sql .= " '$subject' ,";
    $sql .= " '$message' ,";
    $sql .= " 'P' ,";
    $sql .= " '$datos->idpaciente' ,";
    $sql .= " '$datos->idcita' ,";
    $sql .= " now() ,";
    $sql .= " 1 ,";
    $sql .= " '$fecha_programa',  ";
    $sql .= " $user->id ";
    $sql .= ");";

    $result = $db->query($sql);
    if(!$result){
        $error = 'Ocurrio un error con la Operación. Consulte con soporte <br> <small><b>operación Programar Confirmación e-mail</b></small>';
    }else{
        $id_notificacion_email = $db->lastInsertId("tab_notificacion_email");
    }

    if($datos->idcita!=0){

        //se elimina la lista de confirmaciones asociado a dicha cita para eviar la duplicida de confirmacion
        $result = $db->query("DELETE FROM `tab_noti_confirmacion_cita_email` WHERE `rowid`>0 and `fk_cita`= ".$datos->idcita);

        $queryNoti  = " INSERT INTO `tab_noti_confirmacion_cita_email` (`fk_paciente`, `fk_cita`, `estado` , `fk_noti_email`) ";
        $queryNoti .= " VALUES(";
        $queryNoti .= " $datos->idpaciente ,";
        $queryNoti .= " $datos->idcita ,";
        $queryNoti .= " 1 ,"; #estado de la cita ==> notificar x email
        $queryNoti .= " $id_notificacion_email ";
        $queryNoti .= " )";
        $db->query($queryNoti);
        $idnotiConfirmacion = $db->lastInsertId('tab_noti_confirmacion_cita_email'); #id de la notificaion de insert confirmacion

        if(!empty($idnotiConfirmacion) )
        {
            $Update = " UPDATE `tab_pacientes_citas_det` SET `fk_cita_email_noti` = $idnotiConfirmacion WHERE `rowid` = '$datos->idcita' ";
            $db->query($Update);
        }

        $db->query("UPDATE `tab_pacientes_citas_det` SET `fk_estado_paciente_cita`= 11, fk_cita_email_noti = $idnotiConfirmacion WHERE `rowid`='".$datos->idcita."'; ");
    }

    $ouput = [
        'registrar'   => $error,
        "error_email" => ""
    ];

    return $ouput;

}


//html contenedor del mensaje de confirmacion del email
function boxsizingMenssaje( $datosEmail = array() ){

    $Mensaje        = $datosEmail['mess'];
    $name_clinica   = $datosEmail['name_clinica'];
    $recordatorio   = $datosEmail['recordatorio'];
    $token          = $datosEmail['token'];
    $telefono       = $datosEmail['telefono'];
    $direccion      = $datosEmail['direccion'];
    $odontolog      = $datosEmail['odontolog'];


    $url_noti_icon = DOL_HTTP.'/logos_icon/logo_default/dental_noti_.png';

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

function ValidarFechaActualCita($cita_id){
    global $db;
    $sql = "select count(*) as count from tab_pacientes_citas_det where cast(fecha_cita as datetime) > now() and rowid = ".$cita_id;
    $result = $db->query($sql);
    $count_rows = $result->fetchObject()->count;
    if($count_rows!=0){
        return true;
    }else{
        return false;
    }
}

function validarFechaHoraCitaReagendamiento($idCita){

    global $db;

    $fecha       = "";
    $duracion    = "";
    $hora        = "";
    $fk_doc      = "";
    $fechaFin    = "";
    $fechaInicio = "";
    $horaFin     = "";
    $horaInicio  = "";

    $fecha     = date("Y-m-d", strtotime(str_replace('/','-', GETPOST("fecha"))));
    $hora      = GETPOST("hora");
    $duracion  = GETPOST("duracion");
    $fk_doc    = GETPOST("fk_doc");

    $fechaInicio = "$fecha $hora:00";
    $fechaFin    = strtotime("+$duracion minute", strtotime($fechaInicio));

    $horaFin     = date("H:i:s", $fechaFin);
    $horaInicio  = $hora.":00";

    $sql = "SELECT rowid,  fk_doc, fecha_cita, hora_cita, hora_inicio, hora_fin  
                         FROM tab_pacientes_citas_det WHERE fk_doc = $fk_doc 
                    and cast(fecha_cita as date)   = '$fecha'
                    and hora_inicio <= '$horaFin'
                    and hora_fin    >= '$horaInicio'
                    and fk_estado_paciente_cita not in(7,9)
             and rowid not in($idCita)";

    $result = $db->query($sql);
    if($result->rowCount() > 0) {
        $response = false;
    }else{
        $response = true;
    }

    return $response;

}

?>