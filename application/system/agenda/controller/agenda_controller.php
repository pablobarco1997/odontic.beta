<?php

if(isset($_GET['ajaxSend']) || isset($_POST['ajaxSend']))
{

    session_start();

    require_once '../../../config/lib.global.php';
    require_once DOL_DOCUMENT. '/application/config/main.php'; //el main contiene la sesion iniciada
    require_once DOL_DOCUMENT .'/application/system/agenda/class/class_agenda.php';


    global  $db , $conf;

    $agenda = new admin_agenda($db);


    $accion = GETPOST('accion');

    switch ($accion)
    {
        case 'create_cita_paciente':

            #die();
            $error = "";

            $row = GETPOST('datos');

            $agenda->fk_paciente    = $row['fk_paciente'];
            $agenda->comentario     = $row['comment'];
            $agenda->fk_login_users = $conf->login_id; #USUARIO LOGEADO
            $agenda->detalle        = $row['detalle'];

//            print_r($agenda); die();
            $error = $agenda->GenerarCitas();

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

            $fechaInicio                        ="";
            $fechaFin                           ="";

            if(!empty($fecha)) {
                $fecha       = explode('-',GETPOST("fecha"));
                $fechaInicio = date("Y-m-d", strtotime( str_replace("/", "-", trim($fecha[0]))));
                $fechaFin    = date("Y-m-d", strtotime( str_replace("/", "-", trim($fecha[1]))));
            }

            $resultado = list_citas( $doctor, $estados, $fechaInicio, $fechaFin, $MostrarCitasCanceladasEliminadas, $pacientes, $n_citas );

            $output = array(
//                "draw"            => $_POST['draw'],
                "data"            => $resultado['datos'],
                "recordsTotal"    => $resultado['total'],
                "recordsFiltered" => $resultado['total']
            );

            echo json_encode($output);

            break;

            /*Esta funcion se usa globalmente para actualizar el estado de la citas */
        case 'EstadoslistCitas':


            $idestado   = GETPOST('idestado'); //ID ESTADO
            $idcita_det = GETPOST('idcita'); // ID DE LA CITA
            $textEstado = GETPOST('estadoText'); //text estado

            $error = "";
            $errmsg = "";

            $sqlUpdateEstado = "UPDATE `tab_pacientes_citas_det` SET `fk_estado_paciente_cita` = $idestado WHERE (`rowid` = $idcita_det);";
            $rs = $db->query($sqlUpdateEstado);

            if($rs)
            {
                $error = "Estado $textEstado: información Actualizada";
            }else{
                $errmsg = 'Ocurrio un error con Update ' .'Status'.$textEstado;
            }

            $output = [
                'resp' => $error,
                'errmsg' => $errmsg #variable aux para notimificaciones
            ];

            echo json_encode($output);
            break;

            /*valida la fecha y hora asignada del odontologo disponible*/
        case 'validacionFechasCitas':

            $resp = true;

            $fecha = "";
            $duracion = "";
            $hora = "";
            $fk_doc = "";
            $fechaFin = "";
            $fechaInicio = "";
            $horaFin = "";
            $horaInicio = "";

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

//            print_r($sqlCount); die();
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
            $idcita                = ( GETPOST('idcitadet') == "") ? 0: GETPOST('idcitadet'); #EL ID DE LA CITA PUEDE SER 0 O MAYOR A 0
            $iddoctor              = GETPOST('iddoct');
            $idplantramAsociarCita = GETPOST('idplantramAsociar');  #PARA ASOCIAR CITA DE UN PLAN DE TRATAMIENTO YA REALIZADO

            $subaccion = GETPOST('subaccion');

            #SE VALIDA LA CITA EN CASO DE REPETIR LA ASOCIACION AL PLAN DE TRATAMIENTO
            if($idcita != 0)
            {
                # 0 NO ESTA ASOCIADA A NINGUN PLAN DE TRATAMIENTO
                # SI LA CITA ES MAYOR A 0 ESTA ASOCIADA A PLAN DE TRATAMIENTO
                $list_plantramAsociados = [];
                $sqlCitaAsociada = "SELECT * FROM tab_plan_tratamiento_cab where fk_cita = $idcita";
                $rslCita = $db->query($sqlCitaAsociada);
                if($rslCita->rowCount() > 0)
                {
                    while($objtram = $rslCita->fetchObject())
                    {
                        $label = "";
                        if( $objtram->edit_name != "" ){
                            $label = $objtram->edit_name.  "\n";
                        }else{
                            $label = "Plan de Tratamiento ". $objtram->numero. "\n";
                        }
                        $list_plantramAsociados[] = $label;
                    }
                    $error = "<p> Esta Cita ya se encuentra asociada con el plan de tratamiento  numero : <b>" . implode(',', $list_plantramAsociados) ."</b> </p>";
                }
            }

            if($subaccion == 'ASOCIAR_CITAS' && empty($error))
            {
                $sqlP = "UPDATE `tab_plan_tratamiento_cab` SET `fk_doc`= $iddoctor, `fk_cita`= $idcita WHERE `rowid`= $idplantramAsociarCita;";
                $rsP = $db->query($sqlP);
                if(!$rsP){
                    $error = 'Ocurrio un error no se pudo asociar la cita a este plan de tratamiento';
                }
            }

            #CREA EL PLAN DE TRATAMIENTO SEA CON UNA CITA ASOCIADA O INDEPENDIENTE
            if($subaccion == "CREATE")
            {

                if($error == ''){

                    $sql1 = "SELECT ifnull(MAX(rowid) + 1, 1) as numero FROM tab_plan_tratamiento_cab";
                    $rs = $db->query($sql1)->fetchObject();

                    $obj1 = $conf->ObtenerPaciente($db, $idpaciente, true);

                    $numero = str_pad($rs->numero, 6, "0", STR_PAD_LEFT);

                    $agenda->tratam_numero  = $numero;
                    $agenda->tratam_fk_doc  = ( $iddoctor == 0 ) ? 0 : $iddoctor;
                    $agenda->tratam_fk_cita = ( $idcita == 0 ) ? 0 : $idcita; #CITA ID
                    $agenda->tratam_fk_paciente = $idpaciente;
                    $agenda->tratam_fk_convenio = $obj1->fk_convenio;
                    $agenda->tratam_ultimacita = "now()"; //FECHA DE CREACION DE LA CITA POR EL MOMENTO
                    $agenda->tratam_detencion = '';
                    $agenda->tratam_estado_tratamiento = 'A'; #ESTADO DEL TRATAMIENTO ACTIVO O INACTIVO
                    $agenda->tratam_situaccion = 'DIAGNÓSTICO';

                    $error = $agenda->create_plantratamientocab();

                    if($error == ''){

                        $idtratamiento = $db->lastInsertId('tab_plan_tratamiento_cab');

                    }

                }
            }


//            echo '<pre>';
//            print_r($idtratamiento);
//            print_r($idcita);
//            die();


            $output = [
                'error'         => $error,
                'idtratamiento' => tokenSecurityId(($idplantramAsociarCita == 0) ? $idtratamiento : $idplantramAsociarCita), #convert id token plan de tratamiento
                'idpacientetoken' => tokenSecurityId($idpaciente)
            ];

//            print_r($output);
//            die();

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


            if( $idplantratamiento != "" || $idplantratamiento > 0 && $idpaciente != 0)
            {
//                print_r($idplantratamiento);
//                echo '<br>';
//                print_r($idpaciente);
//                die();

                #nuevo detalle
                if( $subaccion == 'create' ){

                    foreach ($datos as $key => $item)
                    {

                        $agenda->tramdet_fk_tramcab = $idplantratamiento;
                        $agenda->tramdet_fk_prestacion  = $item['prestacion'];
                        $agenda->tramdet_fk_diente      = $item['iddiente']; # id diente
                        $agenda->tramdet_jsoncaras      = $item['pieza']; # caras seleccionadas matris de caras seleccionadas
                        $agenda->tramdet_subtotal    = $item['subtotal'];
                        $agenda->tramdet_desconvenio = $item['descConvenio'];
                        $agenda->tramdet_descadicional  = $item['descAdicional'];
                        $agenda->tramdet_total          = $item['total'];
                        $agenda->tramdet_cantidad   = $item['cantidad'];
                        $agenda->tramdet_detencion  = $detencion; #DETENCION TEMPORAL O PERMANENTE
                        $agenda->tramdet_fk_usuario = $conf->login_id; #EL USUARIO QUE LA CREO

                        #Obtengo el id del laboratorio
                        $idLab                      = 0;
                        $queLab   = "select * from tab_conf_laboratorios_clinicos l , tab_conf_prestaciones p where p.fk_laboratorio = l.rowid and p.rowid = ".$item['prestacion']." limit 1";
                        $resulLab = $db->query($queLab);
                        if($resulLab && $resulLab->rowCount()>0){
                            $objectLab = $resulLab->fetchObject();
                            $idLab = $objectLab->rowid;
                        }

                        $error = $agenda->create_plantratamientodet($idLab);

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
                $error = 'Ocurrió un error , no se pudo obtener los parametros asignados para crear el detalle de este tratamiento, Consulte con soporte Técnico';
            }

//            print_r($error); die();

            $output = [
                'error'         => $error,
            ];

            echo json_encode($output);
            break;

        case 'envio_email_notificacion':

            $error = '';

            $idpaciente     = GETPOST("idpaciente");
            $idcita         = GETPOST('idcita');  #id de la cita detalle
            $asunto         = GETPOST("asunto");
            $from           = GETPOST("from");
            $to             = GETPOST("to");
            $subject        = GETPOST("subject");
            $message        = GETPOST("message");


            $sqlCitadet     = "SELECT * FROM tab_pacientes_citas_det WHERE rowid = $idcita limit 1";
            $rsuCita = $db->query($sqlCitadet)->fetchObject();

            #Obtengo el objeto conpleto de la cita
            $rowCitasObject = $rsuCita;

            #GENERAR TOKEN E INFORMACION DE LA CLINICA

            /*
            'name_db'      => $conf->EMPRESA->INFORMACION->nombre_db_entity  , 0
            'entity'       => $conf->EMPRESA->INFORMACION->numero_entity        , 1
            'name_clinica' => $conf->EMPRESA->INFORMACION->nombre            , 2
            'logo'         => $conf->EMPRESA->INFORMACION->logo              , 3*/

            $create_token_confirm_citas = [$idcita,$conf->EMPRESA->INFORMACION->nombre_db_entity,$conf->EMPRESA->INFORMACION->numero_entity,$conf->EMPRESA->INFORMACION->nombre,$conf->EMPRESA->INFORMACION->logo];



            $token = tokenSecurityId(json_encode($create_token_confirm_citas));
            $buttonConfirmacion = ConfirmacionEmailHTML( $token );


            $datos = (object)array(
               'idpaciente' =>   !empty($idpaciente) ? $idpaciente : 0,
               'idcita'    => !empty($idcita) ? $idcita : 0,
               'asunto' =>   $asunto,
               'from' =>   $from,
               'to' =>   $to,
               'subject' =>   $subject,
               'message' =>   $message,

                'feche_cita' => $rowCitasObject->fecha_cita ,
                'horaInicio' => $rowCitasObject->hora_inicio ,

                /*INFORMACION DE LA CLINICA*/

                'email'        => $conf->EMPRESA->INFORMACION->email             ,
                'direccion'    => $conf->EMPRESA->INFORMACION->direccion         ,
                'celular'      => $conf->EMPRESA->INFORMACION->celular           ,
            );

//            echo '<pre>';
//            print_r($datos); die();

            $error = notificarCitaEmail($datos, $buttonConfirmacion);

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

            #busca los pacientes habilitados y desavilitados
        case 'pacientes_activodesact':

            $hablitados    = GETPOST('habilitado');
            $desabilitado  = GETPOST('desabilitado');

            $result = [];
            $sqlpaciente = "SELECT rowid , concat(nombre,' ',apellido) as nom FROM tab_admin_pacientes estado where rowid  > 0";
            if($hablitados=="true"||$desabilitado=="true")
            {
                if($hablitados=="true"){
                    $sqlpaciente .= " and estado = 'A' ";
                }
                if($desabilitado=="true"){
                    $sqlpaciente .= " and estado = 'E' ";
                }
            }else{
                $sqlpaciente .= " and rowid = 0";
            }

//            print_r($sqlpaciente);
            $rs = $db->query($sqlpaciente);
            if($rs && $rs->rowCount()>0)
            {
                while ($obj = $rs->fetchObject() )
                {
                    $result[] = array( 'id' => $obj->rowid , 'text' => $obj->nom );
                }
            }

//            print_r($result); die();
            echo json_encode($result);
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
                         CONCAT('Atrazada ',
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
            if($rsatrzada)
            {
                $ob = $rsatrzada->fetchObject();

                if( $ob->cita_atrazada != "" )
                {
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
                    DATE_FORMAT(NOW(), '%Y-%m-%d %H:%i') now_,
                    DATE_FORMAT('".$DateHour."', '%Y-%m-%d %H:%i') AS dateadd,
                    IF(DATE_FORMAT('".$DateHour."', '%Y-%m-%d %H:%i') < DATE_FORMAT(NOW(), '%Y-%m-%d %H:%i'),
                    'Fecha Menor a la Actual',
                    '') AS valid_dateadd 
                    LIMIT 1";
            $result = $db->query($Date);

            #echo '<pre>'; print_r($Date); die();
            if($result){
                $object  = $result->fetchObject();
                if($object->valid_dateadd != ""){
                    $error  = "La Fecha Agregada no puede ser menor a la Fecha Actual <br>  ";
                    $error .= " <b>Fecha Actual: </b>".(date('Y/m/d H:m', strtotime($object->now_)))."<br>";
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
    }

}

function list_citas($doctor, $estado = array(),  $fechaInicio, $fechaFin, $MostrarCitasCanceladasEliminadas, $paciente , $n_citas)
{

    global $db, $permisos;


    if(!PermitsModule(2,1 ))
        $PermisoConsultar = " 1<>1 ";
    else
        $PermisoConsultar = "";

    $Total          = 0;
    $start          = $_POST["start"];
    $length         = $_POST["length"];
//    $colum_ord      = $_POST["order"][0]['column'];
//    $direcc_ord     = $_POST["order"][0]['dir'];


    $data = array();

    $fecha_hoy = date("Y-m-d");

    $sql = "SELECT 
            d.fecha_cita  as fecha_cita,         
            d.hora_inicio , 
            d.hora_fin ,
            d.rowid  as id_cita_det,
            (select concat(p.nombre ,' ',p.apellido) from tab_admin_pacientes p where p.rowid = c.fk_paciente) as paciente,
            (select rowid from tab_admin_pacientes p where p.rowid = c.fk_paciente) as idpaciente,                   
            (select telefono_movil from tab_admin_pacientes p where p.rowid = c.fk_paciente) as telefono_movil,
            (select concat(o.nombre_doc,' ', o.apellido_doc) from tab_odontologos o where o.rowid = d.fk_doc) as doct ,
            (select concat(s.text) from tab_pacientes_estado_citas s where s.rowid = d.fk_estado_paciente_cita) as estado,
            (select s.color from tab_pacientes_estado_citas s where s.rowid = d.fk_estado_paciente_cita) as color,
            d.fk_estado_paciente_cita , 
            c.comentario ,
            ifnull((select es.nombre_especialidad FROM tab_especialidades_doc es where es.rowid = d.fk_especialidad),'General') as especialidad,
            (select p.telefono_movil from tab_admin_pacientes p where p.rowid = c.fk_paciente) as telefono_movil,
            d.fk_doc as iddoctor , 
            (select p.email from tab_admin_pacientes p where p.rowid = c.fk_paciente) as email, 
            d.comentario_adicional as comentario_adicional,
            c.fk_paciente as idpaciente  ,
             -- validaciones
             -- citas atrazados con estado no confirmado
             IF( now() > CAST(d.fecha_cita AS DATETIME)  
                        && d.fk_estado_paciente_cita in(2,1,3,4,7,8,9,10,5)  , 
                            concat('Atrazada ', (select concat(s.text) from tab_pacientes_estado_citas s where s.rowid = d.fk_estado_paciente_cita) , 
                                    '<br> Fecha : ' , date_format(d.fecha_cita, '%Y/%m/%d') , '<br>Hora: ' , d.hora_inicio ,' a ' , d.hora_fin) , ''
                                    ) as cita_atrazada
									            
         FROM 
            tab_pacientes_citas_cab c , tab_pacientes_citas_det d
            where c.rowid = d.fk_pacient_cita_cab
         ";

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

    $sql .= $PermisoConsultar;
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


    $sql .= " order by d.fecha_cita desc ";
    $sqlTotal = $sql;

    if($start || $length)
        $sql.=" LIMIT $start,$length;";


    #echo '<pre>';print_r($sql); die();

    $resultTotal = $db->query($sqlTotal);
    $rs = $db->query($sql);

    if( $rs && $rs->rowCount() > 0 )
    {
        $Total = $resultTotal->rowCount();

        $iu = 0; #acumulador

        while ($acced = $rs->fetchObject())
        {

            $row = array();
            #checked box
            $row[] = "<span class='custom-checkbox-myStyle'>
								<input type='checkbox' id='checked-detalleCitas-$iu' class='checked_detalleCitas'>
								<label for='checked-detalleCitas-$iu' ></label>
                      </span>";



            #numero o codigo de cita
            $numeroCita = "<table>
                                <tr>
                                    <td> <img  src='". DOL_HTTP. "/logos_icon/logo_default/cita-medica.ico' class='img-rounded' style='width: 25px; height: 25px' >  - </td>
                                    <td> ".(str_pad($acced->id_cita_det, 5, "0", STR_PAD_LEFT))." </td>
                                </tr>
                           </table>";

            $row[] = $numeroCita;

            $html1 = "";
            $html1 .= "<p class='text-center' >".date('Y/m/d', strtotime($acced->fecha_cita))."</p>";
            $html1 .= "<div style='background-color: $acced->color; padding: 3px'>";
                $html1 .= "<p class='text-center'>$acced->hora_inicio</p>";
                $html1 .= "<p class='text-center'><i class='fa fa-arrow-circle-o-down'></i></p>";
                $html1 .= "<p class='text-center'>$acced->hora_fin</p>";
            $html1 .= "</div>";

            $row[] = $html1;

            //PACIENTES - JUNTO CON DROPDONW

            #ID IMPORTANTE YA QUE ES UN TOKEN CREADO COMO UN ID DE LA CITAS GENERADO EN UN BINARIO HEXADECIMAL
            $token = tokenSecurityId( $acced->idpaciente); #ME RETORNA UN TOKEN
            $view  = "dop"; #view vista de datos personales admin pacientes

            #url datos personales
            $Url_datospersonales = DOL_HTTP ."/application/system/pacientes/pacientes_admin?view=$view&key=".KEY_GLOB."&id=$token";

            $html2 = "";
            $html2 .= "<div class='form-group col-md-12 col-xs-12 col-lg-12 col-sm-12' >";

            $html2 .= "<div class='col-xs-10 col-md-10 '> <i class='fa fa-user'></i> $acced->paciente </div>";
                $html2 .= "
                        <div class='col-xs-2 col-md-2 no-padding pull-right '>
                            <div class='dropdown pull-right'>
                                <button class='btn btnhover  btn-xs dropdown-toggle' type='button' data-toggle='dropdown' style='height: 100%'> <i class='fa fa-ellipsis-v'></i> </button>
                                <ul class='dropdown-menu'>";

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

                $html4 .= '<span class="text-sm text-justify" title="' .$acced->comentario. '">  
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
                $html4 .= '<small style="color: red; display: block"  class="" title="'. $acced->cita_atrazada .'"> '. $acced->cita_atrazada .' </small>';
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

            #DROPDOWN -------------------------------------------------------------------------------------------------
            $html3 = "";
            $html3 .= "<div class='form-group col-md-12 col-xs-12'>
                        <div class='col-xs-12 col-ms-10 col-md-10 no-padding'> 
                            <label class='text-justify' title='$acced->estado' >$acced->estado</label> 
                        </div>";

            $html3 .= "<div class='col-xs-12 col-ms-2 col-md-2 no-padding no-margin'>
                            <div class='dropdown pull-right'>";
//            onclick='menuDropdownCita($(this), 0)'
                $html3 .= "    <button class='btn btnhover  dropdown-toggle btn-xs ' id='estadoDropw' type='button' data-toggle='dropdown' style='height: 100%'> <i class='fa fa-ellipsis-v'></i> </button>";
                        $html3 .= " <ul class='dropdown-menu pull-right'>";

                        $sqlMenuDrowpdown = "SELECT * FROM tab_pacientes_estado_citas";
                        $rsdrown = $db->query($sqlMenuDrowpdown);

                        if($rs->rowCount() > 0)
                        {
                            while ($rowxs = $rsdrown->fetchObject())
                            {
                                $todosdata = "";
                                $dataTelefono = "";
                                $dataEmailPaciente = "";
                                $addclases = "";

                                if($rowxs->rowid == 8) //whatsapp
                                {
                                    $telefono = substr($acced->telefono_movil, 1, 9);
                                    $dataTelefono = "data-telefono='593$telefono'";
                                }

                                if($rowxs->rowid == 1) //notificar por email
                                {
                                    $dataEmailPaciente = "data-email='$acced->email'";
                                }
                                if($rowxs->rowid == 10) //no debe verse el estado confirmado e-mail x paciente - este estado solo lo confirma el paciente
                                {
                                    $addclases .= " hide "; //oculto este estado
                                }

                                    $todosdata .= " ".
                                    $dataTelefono." ".
                                    $dataEmailPaciente." ";

                                if($acced->fk_estado_paciente_cita == $rowxs->rowid )//muestra la cita con el estado seleccionado
                                {
                                    $html3 .= "<li> <a class='activeEstadoCita' $todosdata   style='cursor: pointer; ' >$rowxs->text</a> </li>";
                                }
                                else{

                                    $html3 .= "<li> <a class=' $addclases '  data-text='$rowxs->text' $todosdata  onclick='EstadosCitas($rowxs->rowid, $acced->id_cita_det, $(this), $acced->idpaciente)' style='cursor: pointer; ' >$rowxs->text</a> </li>";

                                }
                            }
                        }

                         $html3 .= " </ul>"; #dropdown end

                $html3 .= "</div>";
            $html3 .= "</div> 
            </div>";

            #END DROPDOWN-----------------------------------------------------------------------------------------------
            $row[] = $html3;

            #DIAGNOSTICO O OTROS ESTADOS
            $html6 = "<div class='col-md-12 col-xs-12'>
                        <p class='text-bold'  style='text-align: center !important; color: #333333; font-size: 1.4rem; background-color: #E5E7E9; padding: 3px; border-radius: 3px'> Diagnostico </p>
                    </div>";

            $row[] = $html6;

            #estado id
            $row[] = $acced->fk_estado_paciente_cita;

            $data[] = $row;

            $iu++; #recorrido
        }

    }

    $resultFinal = [
        'datos' => $data,
        'total' => $Total
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


    $src_logo = !empty($conf->EMPRESA->INFORMACION->logo) ? DOL_HTTP.'/logos_icon/'.$conf->NAME_DIRECTORIO.'/'.$conf->EMPRESA->INFORMACION->logo :  DOL_HTTP .'/logos_icon/logo_default/icon_software_dental.png';

    $card = "
    <table style=\"border-collapse: collapse; width: 100%; border: 1px;\" width=\"100%\">
                <tr style=\"background-color: #2980b9;\">
                    <td  style=\"width: 10%; \">
                        <p style=\"margin: 0px; \">
                            <img src='".$src_logo."' style=\"height: 100px;\" alt=\"\">
                        </p>
                    </td>
                    <td  style=\"width: 100%; text-align: center; font-weight: bolder;\"> <h2 style=\"font-weight: bolder; color: azure; margin: 0px; font-size: 50px; \">Dental Diente Felix</h2> </td>
                </tr>
                <tr style=\"box-shadow: inset 0 3px 6px rgba(0, 0, 0, 0.16), 0 3px 6px rgba(0, 0, 0, 0.23); background-color: #7fb3d5;\">
                    <td style=\"padding: 30px; text-align: center;\" colspan=\"2\">
                        
                        <table style=\"width: 100%;\">
                            <tr>
                                <td style=\"padding: 10px; font-size: 20px \">
                                    Le recordamos que tiene una cita agendada para la fecha - <b>". GET_DATE_SPANISH(date('Y-m-d', strtotime($datos->feche_cita))) ." - hora ". $datos->horaInicio . "</b>
                                </td>
                            </tr>
                            <tr>
                                <td style=\"padding: 10px; font-size: 15px\">".$messabody."</td>
                            </tr>
                            <tr>
                                <td style=\"width: 100%; \">
                                    <br>
                                    ".$token_confirmacion."
                                </td>
                            </tr>
                        </table>

                    </td>
                </tr>

                <tr style=\"background-color: #2980b9;\">
                    <td style=\"padding: 20px; width: 100%;\" colspan=\"2\">
                        <table>
                            <tr>
                                <td style='font-size: 15px'><b>".utf8_decode('Teléfono:')."</b> <b>".$datos->celular."</b></td>
                            </tr>
                            <tr>
                                <td style='font-size: 15px'><b>".utf8_decode('Dirección:')."</b> <b>".$datos->direccion."</b> </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
    ";


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
    $mail->Username = "odontic@adminnube.com";//correo del servidor
    $mail->Password = "1e!j5eKlhpXH";//password de servidor de correo
    $mail->Subject = "Clinica dental ".$conf->EMPRESA->INFORMACION->nombre; //nombre de la clinica
    $mail->addCustomHeader("'Reply-to:".$conf->EMPRESA->INFORMACION->conf_email."'");
    $mail->isHTML(TRUE);
    $mail->msgHTML("Notificación Clinica");
    $mail->setFrom($conf->EMPRESA->INFORMACION->conf_email, $conf->EMPRESA->INFORMACION->nombre);
    $mail->addAddress($to);
    #$mail->msgHTML("");
    #$mail->headerLine($headerLine);

    $mail->Body = $htmlSend."".$card;


    $error_insert_notific_email = "";#Se usa para comprobar el registro

    if($conf->EMPRESA->INFORMACION->conf_email != ""){

        if(!$mail->send()){
            $error = 0; #Correo no enviado
            if($error=0){
                $error = 'Ocurrio un problema con el servidor no pudo enviar el correo, intentelo de nuevo o consulte con soporte  Tecnico' .'<br> <b> '. $mail->ErrorInfo .' </b>';
            }
        }else{
            $error = 1; #Correo enviado
        }


        #SI EL $error = 1 -- EL EMAIL SE ENVIO CORRECTAMENTE
        if($error == 1 )
        {


            $sql = "INSERT INTO `tab_notificacion_email` (`asunto`, `from`, `to`, `subject`, `message`, `estado`, `fk_paciente`, `fk_cita`, `fecha`) ";
            $sql .= "VALUES (";
            $sql .= "'$asunto' ,";
            $sql .= "'$from' ,";
            $sql .= "'$to' ,";
            $sql .= "'$subject' ,";
            $sql .= "'$message' ,";
            $sql .= "'A' ,";
            $sql .= "'$datos->idpaciente' ,";
            $sql .= "'$datos->idcita' ,";
            $sql .= " now() ";
            $sql .= ");";
            
            $rs = $db->query($sql);

            if(!$rs){
                $error_insert_notific_email = 'Ocurrio un error, el sistema no logro registrar el correo enviado';
            }

            if($rs)
            {

                $fk_notifi_id = $db->lastInsertId('tab_notificacion_email');

                $queryDel   = " DELETE FROM tab_noti_confirmacion_cita_email where rowid > 0 and fk_cita = $datos->idcita ";
                $r1 = $db->query($queryDel);

                if($r1)
                {
                    $queryNoti  = " INSERT INTO `tab_noti_confirmacion_cita_email` (`fk_paciente`, `fk_cita`, `estado` , `fk_noti_email`) ";
                    $queryNoti .= " VALUES(";
                    $queryNoti .= " $datos->idpaciente ,";
                    $queryNoti .= " $datos->idcita ,";
                    $queryNoti .= " 1 ,"; #notificar x email
                    $queryNoti .= " $fk_notifi_id ";
                    $queryNoti .= " )";
                    $db->query($queryNoti);
//                  print_r($queryNoti);
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

    $Ouput = [

        'registrar'   => $error_insert_notific_email ,
        'error_email' => ($error==1)?"":$error

    ];

//    print_r($Ouput);
//    die();
    return $Ouput;

}

?>