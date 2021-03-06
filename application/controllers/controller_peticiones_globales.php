
<?php
//if( !headers_sent() && '' == session_id() ) {
//    session_start();
//}
?>

<?php

session_start();

if(isset($_GET['ajaxSend']) || isset($_POST['ajaxSend']))
{


    require_once '../config/lib.global.php';
    require_once DOL_DOCUMENT.'/application/config/main.php';

    global $db, $conf, $user;

    $accion = GETPOST("accion");

    switch($accion)
    {

        case "UpdateEntidad":

            $logo            = "";
            $type            = "";
            $name_fichero    = "";
            $link            = false;

            if(isset($_FILES["logo"])) {
                $logo = $_FILES["logo"];
                switch ($logo["type"])
                {
                    case "image/jpeg":
                        $type = ".jpeg";
                        break;

                    case "image/png":
                        $type = ".png";
                        break;
                }

                $tmp_name          =  $logo["tmp_name"];
                $name_fichero = "entidad_logo_".$conf->EMPRESA->ID_ENTIDAD."_".$conf->EMPRESA->ENTIDAD."".$type;
                $link = UploadFicherosLogosEntidadGlob($name_fichero,$type,$tmp_name, '', true);
            }

            $clinica      = GETPOST("nombre");
            $pais         = GETPOST("pais");
            $ciudad       = GETPOST("ciudad");
            $direccion    = GETPOST("direccion");
            $telefono     = GETPOST("telefono");
            $celular      = GETPOST("celular");
            $email        = GETPOST("email");

            #configuracion de acceso de email
            $conf_email    = GETPOST('conf_emil');
            $conf_password = GETPOST('conf_password');


            $UpdateEntidad = new CONECCION_ENTIDAD();

            #SE ACTUALIZA LA ENTIDAD DE LA EMPRESA
            $rs = $UpdateEntidad::UPDATE_ENTIDAD(
                        $clinica,
                        $direccion,
                        $telefono,
                        $celular,
                        $email,
                        $name_fichero,
                        $pais,
                        $ciudad,
                        $conf->EMPRESA->ID_ENTIDAD,
                        $conf_email,
                        $conf_password
                );

            //No se Update
            if($rs==0) {//Si el link me retorna un false entonces se envia
                if($link == false) {
                    $rs=-1;
                    unlink($link);
                }
            }else{
                $_SESSION['nombreClinica']    = $clinica;
                $_SESSION['direccionClinica'] = $direccion;
                $_SESSION['emailClinica']     = $conf_email;
            }


            $output = [
                'error' => $rs,
                'link'  => $link
            ];

            echo json_encode($output);

            break;


            case 'pacientesxDate':

                $data = [];

                $object     = GETPOST("object");
                $date       = GETPOST("date");
                $arr_date   = explode('-', $date);
                $dateInicio = str_replace('/','-',$arr_date[0]);
                $dateFin    = str_replace('/','-',$arr_date[1]);

                $count = "";
                if($object==0){
                    $count = "count(*) as fetchpaciente";
                }else{
                    $count = "*";
                }
                $sql    = " SELECT $count FROM tab_admin_pacientes WHERE estado = 'A' and rowid > 0";
                if($date!=""){
                    $sql .= " and tms between '$dateInicio' and '$dateFin' ";
                }

                if($object==0){
                    $resul  = $db->query($sql)->fetchObject()->fetchpaciente;
                    $out = array('pacientesxDate' => $resul);
                }
                else{

                    $Total          = 0;
                    $start          = $_POST["start"];
                    $length         = $_POST["length"];

                    $Total  = $db->query($sql)->rowCount();

                    if($start || $length){
                        $sql .=" LIMIT $start,$length;";
                    }

                    $resul  = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
                    if (count($resul) > 0 )
                    {
                        foreach ($resul as $k => $arr){

                            $telefono  = "";
                            $email     = $arr['email'];

                            if($arr['telefono_movil']!=""){
                                $telefono = $arr['telefono_movil'];
                            }

                            $row = array();
                            $row[] = $arr['nombre'].' '.$arr['apellido'];
                            $row[] = $arr['direccion'];
                            $row[] = $arr['ruc_ced'];
                            $row[] = $email;
                            $row[] = ' <i class="fa fa-phone-square"></i> '.$telefono;

                            $data[] = $row;
                        }
                    }

                    $out = array(
                        "data" => $data,
                        "recordsTotal"    => $Total,
                        "recordsFiltered" => $Total

                    );
                }

                $output = $out;

                echo json_encode($output);
                break;


            case 'citasCanceladaxDate':

                $arr=[];

                $table      = GETPOST("citas_atendidas");
                $date       = GETPOST("date");
                $arr_date   = explode('-', $date);
                $dateInicio = str_replace('/','-',$arr_date[0]);
                $dateFin    = str_replace('/','-',$arr_date[1]);

                $sqltotal = "SELECT count(*) count_cita
                     FROM 
                        tab_pacientes_citas_cab c , 
                        tab_pacientes_citas_det d ,
                        tab_admin_pacientes p
                        where 
                        c.rowid = d.fk_pacient_cita_cab
                        and p.rowid = c.fk_paciente
                        and cast(d.fecha_cita as date) between '$dateInicio' and '$dateFin'
                        ";
                if($table==1){
                    $sqltotal .= " and d.fk_estado_paciente_cita in(6) ";
                }else{
                    $sqltotal .= " and d.fk_estado_paciente_cita in(7,9) ";
                }
                $sqltotal .= " order by d.rowid desc";
                $Total = $db->query($sqltotal)->fetchObject()->count_cita;

                $sql = "SELECT 
                        concat('', lpad('0',(5-length(d.rowid)),'0'),d.rowid) as numberCitas,	
                        d.fecha_cita  as fecha_cita,         
                        d.hora_inicio , 
                        d.hora_fin ,
                        p.nombre as paciente,
                        (select concat(o.nombre_doc,' ', o.apellido_doc) from tab_odontologos o where o.rowid = d.fk_doc) as doct ,
                        (select concat(s.text) from tab_pacientes_estado_citas s where s.rowid = d.fk_estado_paciente_cita) as estado,
                        d.fk_estado_paciente_cita , 
                        c.comentario ,
                        ifnull((select es.nombre_especialidad FROM tab_especialidades_doc es where es.rowid = d.fk_especialidad),'General') as especialidad,
                        c.fk_paciente as idpaciente  ,
                         -- validaciones
                         -- citas atrazados con estado no confirmado
                         IF( now() > CAST(d.fecha_cita AS DATETIME)  
                                    && d.fk_estado_paciente_cita in(2,1,3,4,7,8,9,10,11,5,  (select statusc.rowid from tab_pacientes_estado_citas statusc where statusc.system=0) )  , 
                                        concat('Atrasada ', (select concat(s.text) from tab_pacientes_estado_citas s where s.rowid = d.fk_estado_paciente_cita) , 
                                                '<br> Fecha : ' , date_format(d.fecha_cita, '%Y/%m/%d') , '<br>Hora: ' , d.hora_inicio ,' a ' , d.hora_fin) , ''
                                                ) as cita_atrazada   , 
                         (select s.color from tab_pacientes_estado_citas s where s.rowid = d.fk_estado_paciente_cita) as color_stado
                     FROM 
                        tab_pacientes_citas_cab c , 
                        tab_pacientes_citas_det d ,
                        tab_admin_pacientes p
                        where 
                        c.rowid = d.fk_pacient_cita_cab
                        and p.rowid = c.fk_paciente
                        and cast(d.fecha_cita as date) between '$dateInicio' and '$dateFin'
                        ";

                if($table==1){
                    $sql .= " and d.fk_estado_paciente_cita in(6) ";
                }else{
                    $sql .= " and d.fk_estado_paciente_cita in(7,9) ";
                }
                $sqltotal .= " order by d.rowid desc";

                $start          = $_POST["start"];
                $length         = $_POST["length"];

                if($start || $length){
                    $sql .= " limit $start,$length";
                }

//                print_r($sql); die();
                $iconcita = "data: image/png; base64, ".base64_encode(file_get_contents(DOL_HTTP.'/logos_icon/logo_default/cita-medica.ico'));

                $result = $db->query($sql);
                if($result && $result->rowCount()>0){
                    while ($object = $result->fetchObject()){
                        $row = [];
                        $row[] = "<span style='font-weight: bold'>"."<img src='$iconcita' width='25px' height='25px'>"." - ".$object->numberCitas."</span>";
                        $row[] = date("Y/m/d", strtotime($object->fecha_cita));
                        $row[] = $object->hora_inicio.' h '.$object->hora_fin;
                        $row[] = $object->paciente;
                        $row[] = $object->doct;
                        $row[] = "<label class='control-label' style='background-color: $object->color_stado !important;  color: #333333; margin-top: 3%; padding: 5px;' > $object->estado </label>";

                        $arr[] = $row;
                    }
                }

                $output = [
                    "data" => $arr,
                    "recordsTotal"    => $Total,
                    "recordsFiltered" => $Total
                ];

                echo  json_encode($output);
                break;

            case 'tratamientosActivosyFinalizados':
                $arr=[];

                $date       = GETPOST("date");
                $arr_date   = explode('-', $date);
                $dateInicio = str_replace('/','-',$arr_date[0]);
                $dateFin    = str_replace('/','-',$arr_date[1]);

                $sqltotal = "SELECT 
                        count(*) tratam_num
                    FROM tab_plan_tratamiento_cab tc , tab_admin_pacientes ap
                        where 
                        tc.fk_paciente = ap.rowid 
						and tc.estados_tratamiento in('A', 'S', 'F') 
                        and cast(tc.fecha_create as date) between '$dateInicio' and '$dateFin'
                        order by tc.rowid desc";
                $Total = $db->query($sqltotal)->fetchObject()->tratam_num;

                $sql = "SELECT 
                        tc.rowid,
                        tc.numero,
                        tc.fk_paciente,
                        CONCAT(ap.nombre, ' ', ap.apellido) nombre,
                        tc.fk_paciente,
                        tc.fk_doc fkdoc,
                        IFNULL((SELECT CONCAT(od.nombre_doc, ' ', od.apellido_doc) FROM tab_odontologos od WHERE od.rowid = tc.fk_doc),'No asignado') AS nombre_doc,
                        tc.estados_tratamiento,
                        tc.ultima_cita,
                        tc.situacion,
                        tc.edit_name AS edit_name,
                        tc.fk_paciente AS idpaciente,
                        tc.fk_cita AS idCitas,
                        cast(tc.fecha_create as date) as fecha_create , 
                        ifnull((select round(sum(pd.amount),2) as saldoAbonado from tab_pagos_independ_pacientes_det pd where pd.fk_plantram_cab = tc.rowid),0) as saldo_abonado
                        
                    FROM 
                      tab_plan_tratamiento_cab tc , 
                      tab_admin_pacientes ap
                        where 
                        tc.fk_paciente = ap.rowid 
						and tc.estados_tratamiento in('A', 'S', 'F') 
						and cast(tc.fecha_create as date) between '$dateInicio' and '$dateFin'
                        order by tc.rowid desc";

                $start          = $_POST["start"];
                $length         = $_POST["length"];

                if($start || $length){
                    $sql .= " limit $start,$length";
                }

                $result = $db->query($sql);
                if($result && $result->rowCount()>0){
                    while ($object = $result->fetchObject()){
                        $edit_name = "Plan de Tratamiento: #".$object->numero;
                        if($object->edit_name!=''){
                            $edit_name = $object->edit_name;
                        }



                        $row = [];
                        $row[] = date('Y/m/d', strtotime($object->fecha_create));
                        $row[] = $edit_name;
                        $row[] = date('Y/m/d H:m:s', strtotime($object->ultima_cita));
                        $row[] = $object->nombre; //paciente
                        $row[] = 'Doctor(a) '.$object->nombre_doc; // encargado
//                        $row[] = $object->situacion;

                        if($object->estados_tratamiento=='S')
                            $row[] = '<span style="font-weight: bold">SALDO ASOCIADO <small class="no-margin" style="color: green">$'.number_format($object->saldo_abonado,2,'.','').'</small></span>';
                        if($object->estados_tratamiento=='A')
                            $row[] = '<span style="font-weight: bold">DIAGNÓSTICO</span>';
                        if($object->estados_tratamiento=='F')
                            $row[] = '<span style="font-weight: bold" >FINALIZADO <i class="fa fa-flag"></i></span>';


                        $row['paciente_id']     = tokenSecurityId($object->idpaciente);
                        $row['tratamiento_id']  = tokenSecurityId($object->rowid);

                        $arr[] = $row;
                    }
                }

                $output = [
                    "data" => $arr,
                    "recordsTotal"    => $Total,
                    "recordsFiltered" => $Total
                ];
                echo  json_encode($output);
                break;

            case 'CitasAnuladaxDate_Atendidos':

                $estat      = GETPOST('estat');
                $date       = GETPOST('date');
                $arr_date   = explode("-", $date);
                $dateInicio = str_replace('/','-',$arr_date[0]);
                $dateFin    = str_replace('/','-',$arr_date[1]);

                $sql1    = "select count(*) as numero_citas_canceladas from tab_pacientes_citas_det where fk_estado_paciente_cita in(9) and cast(fecha_cita as date) between '$dateInicio' and '$dateFin'";
                $resul1  = $db->query($sql1)->fetchObject();

                $sql2    = "select count(*) as numero_citas_Atendidas from tab_pacientes_citas_det where fk_estado_paciente_cita in(6) and cast(fecha_cita as date) between '$dateInicio' and '$dateFin'";
                $resul2  = $db->query($sql2)->fetchObject();

                $output = [
                    'citasAnulaxDate' => $resul1->numero_citas_canceladas,
                    'citasAtendidas'  => $resul2->numero_citas_Atendidas,
                ];

                echo json_encode($output);
                break;

            case 'Npresupuestos':

                $date       = GETPOST('date');
                $arr_date   = explode("-", $date);
                $dateInicio = str_replace('/','-',$arr_date[0]);
                $dateFin    = str_replace('/','-',$arr_date[1]);

                $sql    = "select count(*) as presupuestos from tab_plan_tratamiento_cab where estados_tratamiento in('A','S') and fecha_create between '$dateInicio' and '$dateFin' ";
                $resul  = $db->query($sql)->fetchObject();

//                print_r($sql); die();
                $output = [
                    'presupuestos' => $resul->presupuestos,
                ];

                echo json_encode($output);
                break;


        case 'accept_noti_confirm_pacient':

            $error = "";

            $table = GETPOST('table');
            $id = GETPOST('id');

            if($table == 'confirmacion_email_cita'){
                $query = "UPDATE `tab_noti_confirmacion_cita_email` SET `noti_aceptar`='1' WHERE `rowid`= $id;";
                $rs = $db->query($query);
                if(!$rs){
                    $error = 'Ocurrio un error';
                }
            }

            if($table == 'cita_agendada'){
                $query = "UPDATE `tab_pacientes_citas_det` SET `noti_aceptar`='1' WHERE `rowid`= $id;";
                $rs = $db->query($query);
                if(!$rs){
                    $error = 'Ocurrio un error';
                }
            }


            $output = [
                'error' => $error,
            ];

            echo json_encode($output);
            break;

        case 'notification_':

            $info           = [];
            $notification   = [];
            $validTime      = GETPOST('validTime');
            $subaccion      = GETPOST('subaccion');
            if($validTime == 0 ){
//                set_time_limit(0); //se establese el numero de segundo que se permite la ejecucion de un script
//                usleep(3000000);
            }
            clearstatcache();
            $error = "";
            if( isset($_SESSION['is_open']) ){

                if($subaccion=='noti_numero'){

                    $push           = 0;
                    $numero_notify  = (int)GETPOST('numero_notify');//vista
                    $count_notify   = $conf->numero_de_notificaiones($db);


                    if(isset($_SESSION['noti_count_number'])){

                        if($count_notify['result'] < $_SESSION['noti_count_number']){
                            $_SESSION['noti_count_number'] = $count_notify['result'];
                        }

                        if($count_notify['result'] == 0){
                            $_SESSION['noti_count_number'] = 0;
                        }else{
                            if($numero_notify == 0){
                                if($count_notify['result'] > $_SESSION['noti_count_number']){
                                    $_SESSION['noti_count_number'] =  $count_notify['result'];
                                    $push++;
                                }
                            }else{
                                if($_SESSION['noti_count_number'] == $numero_notify){
                                    //si no hay notificaciones en la sesion
                                    //nada
                                }else{
                                    if($count_notify['result'] > $numero_notify){
                                        $_SESSION['noti_count_number'] =  $count_notify['result'];
                                        $push++;
                                    }
                                    if($_SESSION['noti_count_number'] > $numero_notify){
                                        $push++;
                                    }
                                }
                            }
                        }
                    }else{
                        $_SESSION['noti_count_number'] = $count_notify['result']; //si en caso es 0
                        if($_SESSION['noti_count_number'] > 0){
                            $push++;
                        }
                    }

                    $output = [
                        'n_notify'          => $count_notify,
                        'notificacion_push' => (($push>0)?$_SESSION['noti_count_number']:0)
                    ];

                    echo json_encode($output);
                    die();

                }else{
                    //lista de notificaciones data
                    $fecha_time     = GETPOST('fecha_time');
                    $fecha_php      = !empty(GETPOST('fecha_time'))?GETPOST('fecha_time'):date("Y-m-d H:m:s");
                    $notification   = $conf->ObtnerNoficaciones($db, false);
                    $fechadb        = $notification['fecha_time_db'];
                }

//                $info         = info_noti( $notification );
//                print_r($notification); die();

            }else{
                $error          = "Ocurrio un error";
            }

            $output = [
              'data'   => ((count($notification)>0)?$notification:array()),
              'N_noti' => $notification['numero'],
              'error'  => $error
            ];

            echo json_encode($output);
            break;


        /*USUARIO PERFIL PETICIONES*/
        case 'perfil_glb':

            $data = array();

            $fk_doct = GETPOST('idperfil');
            $usuario = GETPOST('usuario');

            $sql = "SELECT 
                    lu.cedula  as cedulalogin, 
                    lu.tipo_usuario , 
                    lu.usuario , 
                    concat(od.nombre_doc,' ',od.apellido_doc) as nom , 
                    od.nombre_doc, 
                    od.apellido_doc,
                    lu.passwords , 
                    lu.passwor_abc , 
                    od.email , 
                    od.fk_especialidad ,
                    od.cedula as cedula_odontologo, 
                    od.celular,
                    lu.id_caja_account
                 FROM tab_login_users lu , tab_odontologos od 
                 where 
                 lu.fk_doc = od.rowid 
                 and lu.rowid = ".$user->id;

            $rs = $db->query($sql);
            if($rs && $rs->rowCount()>0){
                $data = $rs->fetchObject();
            }

            $output = [
                'objPerfil' =>   $data,
            ];

            echo json_encode($output);
            break;

        case 'UpdatePerfilLogin';


            $respuesta['error'] = "";
            $respuesta['msg'] = "";
            $respuesta['refrescar'] = "";

            $paramts        = GETPOST("params");
            $UsuarioCurrent = GETPOST("usuarioActual");

            $objectUsuario  = getnombreUsuario($user->id);

            #print_r($objectUsuario); die();

            // si el usuario que esta modificando el perfil no es administrador
            if($objectUsuario->admin==0){
                $respuesta['error'] = "Ud. No tiene permiso para modificar el usuario <b> Esta acción solo la puede realizar un usuario admin</b>";
            }


            if($respuesta['error']==""){
                $respuesta = UpdatePerfilOdont($paramts, $UsuarioCurrent);
            }

            $output = [
                'error'       =>   $respuesta['error'],
                'msg'         =>   $respuesta['msg'],
                'refrescar'   =>   $respuesta['refrescar'],
            ];

            echo json_encode($output);
            break;


        case "valid_usuario_perfil_glob":

            $errror = "";
            $subaccion = GETPOST("subaccion");

            if($subaccion == "usesr"){
                $nameusers = GETPOST("nameUsers");
                if(!empty($nameusers)){
                    $error = (validususarioGlob($nameusers)==true)?"":"err";
                }else{
                    $error = "error";
                }

            }

            if($subaccion == "odontol"){
                $nu_cedula = GETPOST("nu_cedula");
                if(!empty($nu_cedula)){
                    $error = (validususarioOdontGlob($nu_cedula)==true)?"":"err";
                }else{
                    $error = "error";
                }
            }

            $output = [
                'error' => $error
            ];

            echo json_encode($output);
            break;

        case 'ObtenerPacienteslistaSearch':

            $data = [];

            $label = GETPOST('label');
            $valid = false;


            if( !empty($label) )
            {
                $searchType = " and concat(replace(ps.ruc_ced,' ',''),'',replace(ps.nombre,' ',''),'',replace(ps.apellido,' ','')) like '%".str_replace(' ', '', $label)."%'  limit 10";
                #busqueda de paciente se concat search type
                $sql = "SELECT * FROM tab_admin_pacientes ps WHERE ps.rowid > 0 ";
                $sql .= $searchType;

                $rs = $db->query($sql);
                if($rs &&  $rs->rowCount() > 0 &&  !empty($searchType) )
                {
                    while( $obPaciente =  $rs->fetchObject() ) {

                        $nom = '<b>C. I. &nbsp; '.$obPaciente->ruc_ced.'</b>'.' '.$obPaciente->nombre .' '.$obPaciente->apellido .' &nbsp;&nbsp;'.' <b>telf:</b>'.$obPaciente->telefono_movil;

                        $url_link = "<a href='".DOL_HTTP."/application/system/pacientes/pacientes_admin/?view=dop&key=".KEY_GLOB."&id=".tokenSecurityId($obPaciente->rowid)."' 
                                        style='border-bottom: 1px solid #e9edf2; display: block; margin-bottom: 10px; color: #333333'>".$nom."</a>";

                        $data[] = array(
                            'name'  => $url_link,
                            'id'    => tokenSecurityId( $obPaciente->rowid ),
                        );
                    }

                    $valid = true;

                }else{

                    $valid = false;
                }
                

            }

            $output = [
                'data' => $valid ,
                'object' => $data
            ];
            echo json_encode($output);
            break;

        case 'ConsultarTypePermisos':


            $error     = '';
            $idaction  = GETPOST('actionPermiso');
            $IdModule  = GETPOST('idModule');

            $valid = PermitsModule($IdModule, $idaction);

            $output = [
                'valid' => $valid
            ];

            echo json_encode($output);
            break;

    }

}


function info_noti( $data = array() )
{

    global $conf;
    $HTML = "";
    foreach ($data['data'] as $key => $v){

        #notificaciones de citas
        if( $v->tipo_notificacion == 'NOTIFICAIONES_CITAS_PACIENTES' ){

            $ico = "data: image/*; base64, ".base64_encode(file_get_contents(DOL_HTTP."/logos_icon/logo_default/cita-medica.ico"));
            $hora_desde_A = substr($v->horaIni, 0, 5 ) ." A " . substr($v->horafin, 0, 5 ); //Corto

            $HTML_CITAS_PACIENTES = "
                <li style='margin-bottom: 2px; padding: 5px' class='listNotificacion' >
                
                    <div class='form-group col-md-12 col-xs-12 no-margin no-padding'>
                        
                        <div class='media' style='border-top: 1px solid #f4f4f4; padding:10px 10px'>
                            <a class='pull-left'> <img src='".$ico."' class='img-rounded img-md' alt=''> </a>
                            <div class='media-body'>
                            
                                <div class='text-justify' style='font-size: 1.2rem; ' >
                                    <b>Doctor:   &nbsp;</b><span title='$v->doctor_cargo'>".(($v->doctor_cargo))."</span><br>
                                    <b>Paciente: &nbsp;</b><span title='$v->nombe_paciente'>".(($v->nombe_paciente))."</span><br>
                                    <b>Fecha:    &nbsp;</b><span title='$v->fecha'>$v->fecha</span><br>
                                    <b>Hora:     &nbsp;</b><span title='$hora_desde_A'>$hora_desde_A</span><br>
                                                                    
                                    ";
            $HTML_CITAS_PACIENTES               .= ($v->comment!='')?"<b>Comentario: </b>&nbsp;&nbsp; <span title='$v->comment'>$v->comment</span>":"";

            $HTML_CITAS_PACIENTES .=    "
                                    <button class='btn-sm btn btn-block btnhover' onclick='Actulizar_notificacion_citas($v->id_detalle_cita)' style='font-weight: bolder; color: green'>EN SALA DE ESPERA</button>
                                </div>
                               
                            </div>
                        </div>
                    </div>
                    
                </li>
                ";

            $HTML .= $HTML_CITAS_PACIENTES;
        }

        #notificaiones x pacientes - confirmaciones de pacientes via email
        if( $v->tipo_notificacion == 'NOTIFICACION_CONFIRMAR_PACIENTE' ){
            $icon2 = "";
            if(!empty($v->icon_paciente) && file_exists(DOL_DOCUMENT."/logos_icon/".$conf->NAME_DIRECTORIO."/".$v->icon_paciente)){
                $icon2 = "data: image/*; base64, ".base64_encode(file_get_contents(DOL_HTTP."/logos_icon/".$conf->NAME_DIRECTORIO."/".$v->icon_paciente));
            }
            else{
                $icon2 = "data: image/*; base64, ".base64_encode(file_get_contents(DOL_HTTP."/logos_icon/logo_default/avatar-user.png"));
            }

            $HTML_NOTIFICACION_X_PACIENTES_EMAIL = "
                    <li style='margin-bottom: 2px; padding: 5px' class='listNotificacion' >
                        <div class='form-group col-md-12 col-xs-12 no-margin no-padding'>
                            <div class='media'>
                                <a class='pull-left'> <img src='".$icon2."' class='img-rounded img-md' alt=''> </a>    
                                <div class='media-body'>
                                    <div class='text-justify' style='font-size: 1.2rem;'>
                                        <b>Paciente: </b> &nbsp;&nbsp;   <span title='$v->paciente'>$v->paciente</span> <br>
                                        <b>Comentario: </b> &nbsp;&nbsp;   <span title='$v->accion'>$v->accion <i class=\"fa fa-bell\"></i>  </span> <br>
                                        <b>Estado Confirmado: </b> &nbsp;&nbsp;   <span title='$v->estado_confirmado' > 
                                        <span style='font-weight: bold; color: blue'>
                                                <small>$v->estado_confirmado</small> </span>
                                        </span>   <br>
                                        <button class='btn-sm btn btn-block btnhover'  onclick='to_accept_noti_confirmpacient($v->id)' style='font-weight: bolder; color: green'>ACEPTAR</button> 
                                    </div>
                                </div>
                            </div>
                        </div>
                    </li>
            ";

            $HTML .= $HTML_NOTIFICACION_X_PACIENTES_EMAIL;
        }
    }

    return $HTML;

}


/*Actualiza el perfil de usuario */
function UpdatePerfilOdont($datos = array(), $UsuarioCurrent = "")
{

    global $db , $user, $conf;

        $msg="";
        $error="";
        $updateRefres=0;

        $dbentity = new CONECCION_ENTIDAD(); #coneccion a todas las entidades

        #SE DETECTA CAMBIOS ODONTOGLOGOS
        #compruebo si el Perfil se Actualizo
        #Si en caso realaizo un cambio

        $cambioOdont = 0; #Usuo esta variable para detectar los cambios
        $objOdont    = getnombreDentiste($user->id);

        #si las varibles GET son iguales a la informacion guardad del Perfil logeado no se acumula no hay cambios
        #Si son diferentes a la informacion guarda hay cambios que realizar se acumula

        if($objOdont->nombre_doc != $datos['nombreOdont'])
            $cambioOdont++;
        if($objOdont->apellido_doc != $datos['apellidoOdont'])
            $cambioOdont++;
        if($objOdont->email != $datos['email'])
            $cambioOdont++;
        if($objOdont->fk_especialidad  != $datos['perfilEspecialidad'])
            $cambioOdont++;
        if($objOdont->cedula != $datos['cedula'])
            $cambioOdont++;
        if($objOdont->fk_especialidad != $datos['perfilEspecialidad'])
            $cambioOdont++;
        if($objOdont->celular != $datos['celularPerfil'])
            $cambioOdont++;



        #Se se actualiza el perfil se tiene q refrescar la pagina e inicar sesion de nuevo
        if($cambioOdont > 0)
        {
            $sqlup  = "  UPDATE `tab_odontologos` SET  ";
            $sqlup .= " `nombre_doc`='".$datos['nombreOdont']."',";
            $sqlup .= " `apellido_doc`='".$datos['apellidoOdont']."',";
            $sqlup .= " `email`='".$datos['email']."',";
            $sqlup .= " `fk_especialidad`='".$datos['perfilEspecialidad']."', ";
            $sqlup .= " `cedula`='".$datos['cedula']."' ,";
            $sqlup .= " `celular`='".$datos['celularPerfil']."' ";
            $sqlup .= "  WHERE `rowid`=$user->id";
            $resul = $db->query($sqlup);

            if(!$resul){
                $error = "Ocurrio un error Update Perfil";
            }else{

                if($datos['cedula'] != $objOdont->cedula){
                    $sqlloginEnti = "UPDATE tab_login_entity SET idcedula = '".$datos['cedula']."'  WHERE rowid = ".$user->id_entidad_login."  ";
                    $dbentity::CONNECT_ENTITY()->query($sqlloginEnti);
                    $updateRefres++;
                }
            }

        }


            #Se comprueba la informacion al loguearse
            $queryInvalic = "SELECT 
                              lnt.rowid as id_entidad_login ,  
                              lnt.nombre_user , concat(lnt.nombre,'',lnt.apellido) as nomodoct, 
                              lnt.idcedula 
                          FROM tab_login_entity lnt 
                          where lnt.rowid > 0 ";
            $queryInvalic .= " and  ((nombre_user = '$UsuarioCurrent' and fk_entidad = ".$conf->EMPRESA->ID_ENTIDAD." ) != true) ";
            $rsInvalic = $dbentity::CONNECT_ENTITY()->query($queryInvalic);

            if($rsInvalic && $rsInvalic->rowCount()>0)
            {

                $usuarioYaExiste = 0;
                while ($objlogin = $rsInvalic->fetchObject())
                {
                    //se busca el usuario que esta en uso
                    //si en caso se repite
                    if($objlogin->nombre_user == $datos['usuario']){
                        $usuarioYaExiste++;
                    }
                }

                    //si el usuario ya exite
                    if($usuarioYaExiste > 0) {
                        $error = "Este usuario se encuentra asignado, Ingrese un usuario que no este repetido";
                    }

                    //si no existe ese usuario - y se puede update
                    if($usuarioYaExiste==0)
                    {
                        $sqldblogin = "UPDATE `tab_login_users` SET `usuario`='".$datos['usuario']."', `passwor_abc`='".$datos['passwd']."' , `passwords` = md5('".(base64_decode($datos['passwd']))."') , `cedula` =  '".$datos['cedula']."' , `id_caja_account` = ".$datos['CajaUsers']."   WHERE `rowid`>0 and fk_doc = $user->id;";
                        $rslogin = $db->query($sqldblogin);
                        if($rslogin)
                        {
                            #base principal update login Entity
                            $sqlloginEntity = "UPDATE tab_login_entity SET nombre_user = '".$datos['usuario']."' , password_user =  md5('".(base64_decode($datos['passwd']))."') , password_abc = '".$datos['passwd']."' WHERE rowid = ".$user->id_entidad_login."  ";
                            $dbentity::CONNECT_ENTITY()->query($sqlloginEntity);
                            $updateRefres++;
                        }
                    }
            }


        return [
          'error' => $error ,
          'msg'   => $msg ,
          'refrescar' => $updateRefres,
        ];


}

function validususarioGlob($username)
{
    $err_asoc_users = 0;

    $connecionEntity = new CONECCION_ENTIDAD();

    $sql    = "select nombre_user from tab_login_entity ";
    $result = $connecionEntity::CONNECT_ENTITY()->query($sql);
    if($result){
        if($result->rowCount()>0){
            while ( $object = $result->fetchObject() ){
                if($object->nombre_user == $username){
                    $err_asoc_users++;
                }
            }
        }
    }

    if($err_asoc_users>0)
        return false;
    else
        return true;

}

function validususarioOdontGlob($nu_cedula)
{
    $err_asoc_idcedula = 0;

    $connecionEntity = new CONECCION_ENTIDAD();

    $sql    = "select idcedula from tab_login_entity ";
    $result = $connecionEntity::CONNECT_ENTITY()->query($sql);
    if($result){
        if($result->rowCount()>0){
            while ( $object = $result->fetchObject() ){
                if($object->idcedula == $nu_cedula){
                    $err_asoc_idcedula++;
                }
            }
        }
    }

    if($err_asoc_idcedula>0)
        return false;
    else
        return true;

}


?>