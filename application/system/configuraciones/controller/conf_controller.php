<?php

if(isset($_GET['ajaxSend']) || isset($_POST['ajaxSend']))
{
    session_start();
    require_once '../../../config/lib.global.php';
    require_once DOL_DOCUMENT. '/application/config/main.php'; //el main contiene la sesion iniciada
    require_once DOL_DOCUMENT.'/application/config/conneccion_entidad.php'; //Coneccion entidad

    global   $db, $conf, $user, $global;


    $accion = GETPOST('accion');

    switch ($accion)
    {

    /** Nuevo o modificar Odontologo*/
    
        case 'crear_odontologo':

            $error = '';

            $subaccion  = GETPOST('subaccion');
            $objeto     = GETPOST('datos');
            $datos      = (json_decode($objeto));

            $nombre         = $datos->nombre;
            $apellido       = $datos->apellido;
            $telefono       = $datos->telefono;
            $direccion      = $datos->direccion;
            $celular        = $datos->celular;
            $email          = $datos->email;
            $ciudad         = $datos->ciudad;
            $especialidad   = $datos->especialidad;
            $cedula_ruc     = $datos->cedula_ruc;
            $TieneImg       = $datos->TieneImagen;

            $aux_idcedula   = GETPOST("aux_idcedula");

//            die();
            if($subaccion == 'nuevo')
            {
                $rs  = nuevoUpdateOdontologos($nombre, $apellido, $celular, $telefono, $email, $direccion, $ciudad, $especialidad, $cedula_ruc);
                if($rs == false){
                    $error = "Ocurrió un error con la Operación";
                }

                $ultimo_id = $db->lastInsertId('tab_odontologos');
                $name_icon = "";

                if( $rs == true )
                {
                    if(isset($_FILES['icon']))
                    {
                        $type = "";
                        if($_FILES['icon']['type'] == 'image/png'){
                            $type = ".png";
                        }

                        if($type != ""){
                            $name_icon = 'conf_odont_icon_'.$ultimo_id.'_'.$conf->EMPRESA->ENTIDAD.$type;
                            $urlFile = UploadFicherosLogosEntidadGlob($name_icon, $_FILES['icon']['type'], $_FILES['icon']['tmp_name']);
                            if($urlFile == false){
                                if(file_exists($conf->DIRECTORIO.'/'.$name_icon)){
                                    $sqlUpdateIcon = "UPDATE `tab_odontologos` SET `icon`='$name_icon' WHERE `rowid`= $ultimo_id;";
                                    $rUp = $db->query($sqlUpdateIcon);
                                    if(!$rUp){
                                        $error = "Ocurrió un error con la Operación, No se pudo subir el icon";
                                    }
                                }else{
                                    unlink($conf->DIRECTORIO.'/'.$name_icon);
                                }
                            }
                        }
                    }
                }
            }

            if($subaccion == 'modificar')
            {
                $name_icon = "";
                $id = GETPOST('id');
                if(isset($_FILES['icon']))
                {
                    $type = "";
                    if($_FILES['icon']['type'] == 'image/png'){
                        $type = ".png";
                    }

                    if($type != ""){
                        $name_icon = 'conf_odont_icon_'.$id.'_'.$conf->EMPRESA->ENTIDAD.$type; #name del fichero SE MUEVA A LA CARPETA QUE SE CREA POR DEFAUL PARA ESTA EMPRESA
                        $urlFile = UploadFicherosLogosEntidadGlob($name_icon, $_FILES['icon']['type'], $_FILES['icon']['tmp_name']);
                        if($urlFile == ""){
                            if(file_exists($conf->DIRECTORIO.'/'.$name_icon)){
                                $sqlUpdateIcon = "UPDATE `tab_odontologos` SET `icon`='$name_icon' WHERE `rowid`= $id;";
                                $rUp = $db->query($sqlUpdateIcon);
                                if(!$rUp){
                                    $error = "Ocurrió un error con la Operación. No se pudo subir el icon";
                                }
                            }else{
                                unlink($conf->DIRECTORIO.'/'.$name_icon);
                            }
                        }
                    }
                }



                #Update login Entity idcedula
                $rs = UpdateOdontologos($nombre, $apellido, $celular, $telefono, $email, $direccion, $ciudad, $especialidad, $id, $cedula_ruc, $aux_idcedula);

                if($rs == false){
                    $error = "Ocurrió un error con la Operación";
                }

                if($TieneImg==0)
                    $db->query("UPDATE `tab_odontologos` SET `icon`='$name_icon' WHERE `rowid`= $id;");

                //si no tiene imagen asociada
                if($TieneImg==0){
                    if(file_exists($conf->DIRECTORIO.'/'.'conf_odont_icon_'.$id.'_'.$conf->EMPRESA->ENTIDAD.'.png') ){
                        unlink($conf->DIRECTORIO.'/'.'conf_odont_icon_'.$id.'_'.$conf->EMPRESA->ENTIDAD.'.png');
                    }
                }


            }

//            die();
            $output = [
                'error' => $error
            ];

            echo json_encode($output);
            break;

        case 'list_odontologos':

            $error = '';
            $estado = GETPOST('estado');
            $error = list_odontolosGention($estado);

            $output = [
                'data' => $error
            ];

            echo json_encode($output);
            break;

        case 'fetch_odontologos':

            $error = '';

            $obj = getnombreDentiste(GETPOST('id'));

//            print_r($obj);

            $output = [
                'error' => $obj
            ];
            echo json_encode($output);
            break;

        case 'actualizar_estados':

            $error = '';

            $estado = GETPOST('estado');
            $id     = GETPOST('id'); #id odontologos

            if(!validSuperAdmin("USUID_".$conf->EMPRESA->ENTIDAD."_".$id)){
                $sql = 'UPDATE `tab_odontologos` SET `estado` = TRIM(\''.$estado.'\') WHERE (`rowid` = '.$id.');';
                $rs = $db->query($sql);

                if($rs){
                    $sqllogi2 = "UPDATE `tab_login_users` SET `estado`='$estado' WHERE `rowid` != 0 and fk_doc =".$id." ;";
                    $rs12 = $db->query($sqllogi2);
                    if($rs12){
                        #verifica si doctor(a) tiene login asociado
                        $sqllogin = "SELECT login_idusers_entity FROM tab_login_users WHERE fk_doc =".$id."  ";
                        $rs1  = $db->query($sqllogin);
                        if($rs1->rowCount() == 1){

                            #se actualiza el login glob
                            $idUsuarioEnitityLogin = $rs1->fetchObject()->login_idusers_entity;
                            $Entidad_Login = new CONECCION_ENTIDAD();
                            $Entidad_Login->login_status($estado, $idUsuarioEnitityLogin, $conf->EMPRESA->ID_ENTIDAD);
                        }

                    }else{
                        $error = 'Ocurrió un error con la Operación, consulte con soporte Técnico';
                    }
                }

                if(!$rs){
                    $error = 'Ocurrió un error con la Operación, consulte con soporte Técnico';
                }

            }else{
                $error = 'Ud. No tiene permiso para Desactivar este Odontolog@';
            }


            $output = [
                'error' => $error
            ];
            echo json_encode($output);
            break;

        case 'consultar_usuario':

            $error          = "";
//            $iddoctUsuario  = GETPOST('');
            $subaccion      = GETPOST("subaccion");
            $usuario_name   = GETPOST("nameUsuario");
            $idEntidad      = $conf->EMPRESA->ID_ENTIDAD; #id de la entidad de la empresa de los usuarios Administrado de empresas

            $Entidad_Login = new CONECCION_ENTIDAD(); //OBTENGO LAS FUNCIONES DE LA FUNCION PRINCIPAL
            $Tieneusuario = $Entidad_Login->COMPROBAR_USUARIO_REPETIDO($usuario_name, $idEntidad );  #compruebo el usuario global

            if(!empty($Tieneusuario)){
                $error = '<b>usuario en uso: </b> '.$usuario_name;
            }

            $output = [
                'error' => $error
            ];

            echo json_encode($output);
            break;

        case 'odontolusuario':

            $odontolo       = GETPOST("odontol");

            $query = "SELECT count(rowid) as counts FROM tab_login_users 	WHERE fk_doc = ".(($odontolo=="")?0:$odontolo);
            $rs = $db->query($query)->fetchObject();
            if($rs->counts>0){
                $error = "Ya se encuentra Asociado";
            }else{
                $error = "";
            }

            $output = [
                'error' => $error
            ];

            echo json_encode($output);
            break;

        case 'fech_usuariodoct':

            #CONNECION A LA ENTIDAD DE LAS EMPRESAS
            $connecionEntidad = new CONECCION_ENTIDAD();

            $error = "";
            $datauser = [];
            $idusuario = GETPOST('id');
            $idUsuarioEntity = 0;
            $idPerfilAsociadoEntity = 0;

            if($idusuario>0)
            {

                $datauser = getnombreUsuario( $idusuario ); #obtengo un objeto

                $sqlentity = "select rowid as idlogin_entidad, nombre_user, fk_perfiles  from tab_login_entity where login_idusers_entity = '".$datauser->login_idusers_entity."' limit 1 ";
                $rsentity = $connecionEntidad::CONNECT_ENTITY()->query($sqlentity);

                if($rsentity)
                {
                    if( $rsentity->rowCount() == 1 )
                    {
                        $objLoginEntity  = $rsentity->fetchObject();

                        $idUsuarioEntity        = $objLoginEntity->idlogin_entidad; // login entity id de usuario
                        $idPerfilAsociadoEntity = $objLoginEntity->fk_perfiles; // id de perfil entity
                    }

                }else{
                    $error = "Ocurrio un error con el ususario no puede modificar este usuario consulte con soporte";
                }

                if($idUsuarioEntity==0)
                    $error = "Ocurrio un error con el ususario no puede modificar este usuario consulte con soporte";


            }else{
                $error = "Ocurrio un error con el ususario no puede modificar este usuario consulte con soporte";
            }


            $output = [

                "object"         =>  $datauser ,
                "error"          =>  $error ,
                "identity"       =>  ($idUsuarioEntity!=0)?base64_encode($idUsuarioEntity):$idUsuarioEntity, //se encryta en base 64
                "idPerfilEntity" =>  $idPerfilAsociadoEntity ,
            ];

//            print_r($output); die();

            echo json_encode($output);
            break;


        case 'nuevoUpdateUsuarioData':

            $error='';

            $subaccion           = GETPOST('subaccion');

            $idCajaAccount       = GETPOST('idcajaAccount');
            $doctor              = GETPOST('doctor'); //id doctor
            $usuario             = GETPOST('usuario');  //usuario name
            $passd               = GETPOST('passwords'); //password en base64
            $tipoUsuario         = GETPOST('tipoUsuario');
            $permisos            = GETPOST('permisos');
            $fk_perfil_entity    = GETPOST('fk_perfil_entity');

            $idEntidad      = $conf->EMPRESA->ID_ENTIDAD; #id de la entidad de la empresa de los usuarios

            $objOdontolo    = getnombreDentiste($doctor);  #Obtengo el objeto del odontologo completo


            if($subaccion=='nuevo')
            {

                #El sistema no permite tener dos usuario a un mismo odontologo
                #SE VALIDA EL USUARIO DE NUEVO - SI YA TIENE EL USUSARIO REGISTRADO
                $sqlinvalic = "SELECT * FROM tab_login_users where fk_doc = $doctor";
                $rsinvalic  = $db->query($sqlinvalic);
                #si tiene un usuario asignado
                if($rsinvalic->rowCount() > 0) {
                    $error = '<b>odontolog@ '.$objOdontolo->nombre_doc.' '.$objOdontolo->apellido_doc.'</b><br>Ya tiene Usuario asignado';
                }
                else{

                    $Entidad_Login = new CONECCION_ENTIDAD(); //OBTENGO LAS FUNCIONES DE LA FUNCION PRINCIPAL
                    $error = $Entidad_Login->COMPROBAR_USUARIO_REPETIDO( GETPOST('usuario') , $idEntidad );  #compruebo el usuario global

                    if(empty($error))
                    {

                        $sql = "INSERT INTO `tab_login_users` (`usuario`, `passwords` ,`fk_doc`, `tipo_usuario`, `passwor_abc`, `cedula`, `fk_perfil_entity`, `entity`, `id_caja_account`) ";
                        $sql .= "VALUES(";
                        $sql .= "'$usuario',";
                        $sql .= " md5('".base64_decode($passd)."'),"; #encrypt md5
                        $sql .= "'$doctor',";
//                        $sql .= "'',"; //permisos
                        $sql .= "'".$fk_perfil_entity."',";
                        $sql .= "'".$passd."' ,"; #encryt base64
                        $sql .= "'".$objOdontolo->cedula."' ,"; #cedula
                        $sql .= " ".$fk_perfil_entity." ,";  #fk perfil entity relacionado directamento con la clinicas  global
                        $sql .= "'".$conf->EMPRESA->ENTIDAD."' ,"; #para poder comprobar a que entidad pertenece
                        $sql .= " ".(empty($idCajaAccount)?0:$idCajaAccount)." ";
                        $sql .= ");";

                        $rs = $db->query($sql);
                        if($rs){

                            #datos para USUARIO GLOBAL
                            $datos = [];

                            #SE CREA EL USUARIO EL LA BASE GLOBAL
                            $idusuarioCreado = $db->lastInsertId('tab_login_users');

                            $USER_ENTITY = "USUID_".$conf->EMPRESA->ENTIDAD."_".$idusuarioCreado;//IDENTIFICADOR DE USUARIO

                            $result = $db->query("UPDATE `tab_login_users` SET `login_idusers_entity`='".$USER_ENTITY."' WHERE `rowid`= $idusuarioCreado ;");

                            if($result){

                                $ob = getnombreDentiste($doctor);

                                $datos['nombre']       = $ob->nombre_doc;
                                $datos['apellido']     = $ob->apellido_doc;
                                $datos['celular']      = $ob->celular;
                                $datos['pass']         = $passd;
                                $datos['email']        = $ob->email;
                                $datos['usuario']      = $usuario;
                                $datos['id_usuario']   = $idusuarioCreado;
                                $datos['idcedula']     = $ob->cedula;
                                $datos['fk_perfil_entity'] = $fk_perfil_entity;

                                $error = GenerarUsuarioGlob($datos, $subaccion);

                            }else{
                                $error = 'Ocurrió un error con la Operación crear Usuario, consulte con soporte Técnico';
                            }
                        }

                        if(!$rs){
                            $error = 'Ocurrió un error con la Operación crear Usuario, consulte con soporte Técnico';
                        }

                    }

                }

            }

            if($subaccion=='modificar')
            {

                $idUsuariolink = GETPOST('idUsuario'); #id usuario de la tab_login_users
                $idEntityusers = GETPOST("idEntityusers"); #id usuario de la entity clinicas

                if( $idUsuariolink != ""  || $idUsuariolink != "0")
                {
                    $objUsuario = getnombreUsuario($idUsuariolink);

                    if($objUsuario->admin == 0){ //el modificar usario no sea administrador

                        $sql1  = " UPDATE `tab_login_users` SET ";
                        $sql1 .= " `usuario`='$usuario' ,";
                        $sql1 .= " `passwor_abc`='".$passd."' ,";
                        $sql1 .= " `passwords`= md5('".base64_decode($passd)."') ,";
                        $sql1 .= " `tipo_usuario`='".$tipoUsuario."' ,";
                        $sql1 .= " `fk_perfil_entity`=".$fk_perfil_entity." ,";
                        $sql1 .= " `id_caja_account`=".(empty($idCajaAccount)?0:$idCajaAccount)." ";
                        $sql1 .= " WHERE `rowid`= '$idUsuariolink' ";
                        $rs1 = $db->query($sql1);

                        if(!$rs1){
                            $error = 'Ocurrió un error con la Operación Modificar el Usuario, consulte con soporte Técnico';
                        }

                        if($rs1){

                            $datos = [];
                            $ob = getnombreDentiste($doctor); #obtengo el dastos del dentista

                            $datos['nombre']            = $ob->nombre_doc;
                            $datos['apellido']          = $ob->apellido_doc;
                            $datos['celular']           = $ob->celular;
                            $datos['pass']              = $passd; #pass se encrypt md5
                            $datos['email']             = $ob->email;
                            $datos['usuario']           = $usuario;
                            $datos['id_usuario']        = $idUsuariolink;
                            $datos['idcedula']          = $ob->cedula;
                            $datos['fk_perfil_entity']  = $fk_perfil_entity;
                            $datos['idEntityusers']     = GETPOST("idEntityusers"); #id usuario de la entity clinicas

                            $error = GenerarUsuarioGlob($datos, $subaccion);
                        }
                    }else{

                        $error = "Ud. no puede modificar un usuario administrador <br>
                                <b> El usuario administrado solo puede ser modificado por el mismo </b>";
                    }

                }else{
                    $error = 'Ocurrió un error con la identidad del Usuario al modificar, consulte con soporte Técnico';
                }
            }

            $output = [
                'error' => $error
            ];

            echo json_encode($output);
            break;


        case 'fetchPerfilesUsuariosEntity':

            $error = "";
            $id = GETPOST("id");
            $datos = [];


            $datos = fetchPermisosPerfil();

            if(is_string($datos) ){
                $error = $datos;
            }


//            die();

            $output = [
                'error'           => $error ,
                'permisos_perfil' => $datos
            ];

            echo json_encode($output);
            break;

        case 'nuevo_update_especialidad':

            $error = '';
            $nomespecialidad = GETPOST('especialidad');
            $descrip         = GETPOST('descrip');

            $sql = "INSERT INTO `tab_especialidades_doc` (`nombre_especialidad`, `fk_user`, `descripcion`) VALUES ('$nomespecialidad', '$user->id', '$descrip');";
            $rs = $db->query($sql);
            if(!$rs){
                $error = 'Ocurrió un error al crear la especialidad, consulte con Soporte Técnico';
            }
            $output = [
                'error' => $error
            ];

            echo json_encode($output);
            break;

        case 'list_especialidades':

            $permisoConsultar = (!PermitsModule(13,1))?" limit 0 ":"";

            $data = array();
            $sql = "SELECT * FROM tab_especialidades_doc";

            $sql .= $permisoConsultar;
            $rs = $db->query($sql);
            if($rs->rowCount()>0)
            {
                while ($ob = $rs->fetchObject()){

                    $row = array();
                    $row[] = date('Y/m/d', strtotime($ob->tms));
                    $row[] = $ob->nombre_especialidad;
                    $row[] = $ob->descripcion;
                    $row[] = "<a  class='btn' style='font-size: 1.7rem; color: #9f191f;' title='eliminar' onclick='eliminar_especialidad($ob->rowid)'>  <i class='fa  fa-trash-o'></i> </a> ";

                    $data[] = $row;

                }
            }

            $output = [
                'data' => $data
            ];

            echo json_encode($output);

            break;


            //// Perfil crear //////

        case 'nuevoPerfil':

            /*obtengo la connecion de la entidad*/

            $error = "";

            $data            = [];
            $Modperfiles     = json_decode(GETPOST("permisosM"));
            $Perfilenom      = GETPOST("perfil_nom");
            $idEntityUsers   = GETPOST("idEntity");
            $idPerfilEntity  = GETPOST("idPerfilSelect");
            $subaccion       = GETPOST("subaccion");

            #print_r($Modperfiles); die();
            if($subaccion=="Nuevo"){

                $error = addPerfil($Modperfiles, $idEntityUsers , $Perfilenom);
            }
            if($subaccion=="Modificar"){

                $error = ModificarPerfil($Modperfiles, $idEntityUsers , $Perfilenom, $idPerfilEntity);
            }

            $output = [
                'error' => $error
            ];

            echo json_encode($output);
            break;

        case 'delete_especialidad':

            $error= '';
            $id  = GETPOST('id'); #id especialidad
            $idCitasAsociadas = [];


            #CONSULTAR CITAS EN CASO DE HABER CITAS ASOCIADAS ENTONCES SE ACTUALIZA A 0 EL FK_ESPECIALIDAD => ESPECIALIDAD GENERAL
            $sqlCitasEspecialidad = "SELECT rowid FROM tab_pacientes_citas_det where fk_especialidad = $id";
            $rscitas = $db->query($sqlCitasEspecialidad);
            if($rscitas && $rscitas->rowCount()>0){
                while($ci = $rscitas->fetchObject()){
                    $idCitasAsociadas[] = $ci->rowid;
                }
            }


            $sql = "DELETE FROM tab_especialidades_doc WHERE `rowid`='$id';";
            $rs = $db->query($sql);

            if($rs){

                $iddoct = array();
                $sql1 = "Select * from tab_odontologos where fk_especialidad = $id";
                $rs1 = $db->query($sql1);
                if($rs1->rowCount()>0){
                    while ($ob1 = $rs1->fetchObject()){
                        $iddoct[] = $ob1->rowid; #obtengo cadenas de id del doctor con esta especialidad
                    }
                }

                if(count($iddoct)>0){
                    $sql2 = "UPDATE `tab_odontologos` SET `fk_especialidad`= 0 WHERE `rowid` in( ". implode(',', $iddoct) ." );";
                    $db->query($sql2);
                }

                #Actualiza a estado 0 fk_especialidad
                if( count($idCitasAsociadas) > 0){
                    $sql3 = "UPDATE `tab_pacientes_citas_det` SET `fk_especialidad` = '0' WHERE `rowid` in( ". (implode(',', $idCitasAsociadas)) ." );";
                    $db->query($sql3);

                }
            }
            if(!$rs){
                $error = 'Ocurrió un error con la Operación Eliminar especialidad, Consulte con Soporte Técnico';
            }
            $output = [
                'error' => $error
            ];

            echo json_encode($output);
            break;

//            PRESTACION ============================================

        case 'nuevoUpdatePrestacion':

            $error = '';
            $prestacion          = GETPOST('label_prestacion');
            $cat_prestacion      = GETPOST('cat_prestacion');
            $cantprestacion      = GETPOST('cat_prestacion');
            $costo_prestacion    = GETPOST('costo_prestacion');
            $convenio            = !empty(GETPOST('convenio')) ? GETPOST('convenio') : 0;
            $explicaion          = GETPOST('explicacion');
            $subaccion           = GETPOST('subaccion');

            if($subaccion == 'nuevo'){

                $sql = "INSERT INTO `tab_conf_prestaciones` (`descripcion`, `tms` ,`fk_user`, `fk_convenio`, `fk_categoria`, `fk_laboratorio`, `valor`, `explicacion`)";
                $sql .= "VALUES(";
                $sql .= "'$prestacion',";
                $sql .= "now(),";
                $sql .= "'$user->id',";
                $sql .= "'".$convenio."',";
                $sql .= "'$cat_prestacion',";
                $sql .= "0,"; #laboratorio
                $sql .= "$costo_prestacion , ";
                $sql .= " '".$explicaion."'  ";
                $sql .= ")";
                $rs = $db->query($sql);


                if(!$rs){
                    $error = "Ocurrió un error , con la Operación crear Prestación";
                }
//                echo '<pre>';
//                print_r($sql);
            }

            if($subaccion == 'modificar'){

                $id = GETPOST('id');

                $con = !empty($convenio) ? $convenio : 0;
                $Update = 'UPDATE tab_conf_prestaciones ';
                $Update .= 'SET descripcion = '."'$prestacion' , ";
                $Update .= 'fk_user = '.$user->id.', ';
                $Update .= 'valor = '.$costo_prestacion.', ';
                $Update .= 'fk_categoria = '.$cat_prestacion .' , ';
                $Update .= 'fk_convenio = '.$con.' , explicacion = '."'$explicaion'".' WHERE `rowid`= '.$id;

                $rs1 = $db->query($Update);

                if(!$rs1){
                    $error = "Ocurrió un error , con la Operación Actualizar Prestación";
                }
            }
            $output = [
                'error' => $error
            ];

            echo json_encode($output);
            break;

        case 'list_prestaciones':

            $data = array();

            $Total          = 0;
            $start          = $_POST["start"];
            $length         = $_POST["length"];

            $permisoConsultar = (!PermitsModule(8,1)) ? " and 1<>1 ":"";

            $sql = "SELECT 
                    d.rowid,
                    d.descripcion,
                    cast(d.tms as date) AS fecha,
                    IFNULL((SELECT c.nombre_conv FROM tab_conf_convenio_desc c WHERE c.rowid = d.fk_convenio), 'no asignado') AS convenio,
                    IFNULL((SELECT  ct.nombre_cat FROM tab_conf_categoria_prestacion ct WHERE ct.rowid = d.fk_categoria), 'no asignado') AS cat,
                    d.valor , 
                    d.estado
                FROM
                    tab_conf_prestaciones d 
                where d.rowid != 0 ";

            if(isset($_POST['search'])){
                if(!empty($_POST['search']['value'])){
                    $sql .= " and replace(descripcion,' ','') like '%".str_replace(' ','', $_POST['search']['value'])."%' ";
                }
            }

            $sql .= $permisoConsultar;
            $sql .= " order by d.rowid desc";

            $sqlTotal = $sql;

            if($start || $length)
                $sql.=" LIMIT $start,$length;";

            $Total = $db->query($sqlTotal)->rowCount();

            $rs = $db->query($sql);
            if( $rs ){
                while ($ob = $rs->fetchObject()){

                    $row = array();

                    if($ob->estado == 'A'){
                        $labSatus = ' <small style="color: green">(habilitado)</small>';
                        $statusServicio = "<a class=\"label btn btn-xs\" style=\"background-color: #FADBD8; color: red; font-weight: bolder\" onclick='ActivarDesactivarServicios($ob->rowid, $(this))' title='Desactivar Prestación' data-status='E'>DESACTIVAR</a>";
                    }
                    else{
                        $labSatus = ' <small style="color: red">(Deshabilitado) </small>';
                        $statusServicio = "<a class=\"label btn btn-xs\" style=\"background-color: #D5F5E3; color: green; font-weight: bolder\" onclick='ActivarDesactivarServicios($ob->rowid, $(this))' title='Activar Prestación' data-status='A'>ACTIVO</a>";
                    }

                    $row[] = str_replace('-','/', $ob->fecha);
                    $row[] = $ob->descripcion.' '.$labSatus.' ';
                    $row[] = $ob->cat;
//                    $row[] = $ob->convenio;


//                    $costo = "<span class=\"\" style=\"padding: 3px; border-radius: 5px; font-weight: bolder; background-color: #66CA86\"> <i class=\"fa fa-dollar\"></i> ".$ob->valor." </span>";
                    $row[] = "<span class=\"\" style=\"padding: 1px; border-radius: 3px; font-weight: bolder; background-color: #66CA86\"> $&nbsp;". number_format($ob->valor,2,".", ",") ." </span>";

                    $row[] = " 
                    <table>
                        <td><a href='".DOL_HTTP."/application/system/configuraciones/index.php?view=form_prestaciones&act=mod&id=$ob->rowid'  style='cursor: pointer; color: green; font-size: 1.8rem'  class='btn btnhover btn-xs pull-right' ><i class='fa fa-edit'></i></a></td>
                        <td>&nbsp; $statusServicio</td>
                    </table>  
                       ";

                    $row[] = $ob->rowid;
                    $data[] = $row;
                }
            }

            $output = [
                'data' => $data,
                'draw' => $_POST['draw'],
                'recordsTotal' => $Total,
                'recordsFiltered' => $Total,
            ];

            echo json_encode($output);
            break;

        case 'fecth_update_prestacion':

            $objeto  = array();
            $id = GETPOST('id');
            $prestacion = array();

            $sql = "SELECT  *  FROM
                        tab_conf_prestaciones
                    WHERE
                        rowid = $id";
            $rs = $db->query($sql);

            if($rs->rowCount()>0){

                while ($ob = $rs->fetchObject()){
                    $objeto[] = $ob;
                }
            }
            $output = [
                'obj' => $objeto
            ];

            echo json_encode($output);
            break;

        case 'eleminar_prestacion':

            $error = '';
            $idprestacion = GETPOST("id");
            $Status = GETPOST("statusPrestacion");

            if($Status=='A')
                $StaLabel = "Activar";
            else
                $StaLabel = "Desactivar";

            $tieneAsociado = 0;

            $sqlConsult = "SELECT * FROM tab_conf_prestaciones WHERE rowid = '$idprestacion' ";
            $rsConsult = $db->query($sqlConsult);
            if($rsConsult && $rsConsult->rowCount()>0){
                $result = $db->query("UPDATE `tab_conf_prestaciones` SET `estado`='".$Status."' WHERE `rowid`=$idprestacion;");
                if(!$result){
                    $error = 'Ocurrion un error con la Operación: '.$StaLabel.' Prestación';
                }
            }else{
                $error = 'Ocurrio un error con la Operación: No se encontro la prestación Asignada';
            }

            $output = [
                'error' => $error
            ];

            echo json_encode($output);
            break;

        case 'nuevoCategoriaPrestacion':

            $error     ='';
            $idCat     = GETPOST('idCat');
            $nombeCat  = GETPOST('label');
            $descrip   = GETPOST('descrip');
            $subaccion = GETPOST('subaccion');

//            print_r($subaccion); die();
            if($subaccion == 'nuevo'){

                if(!empty(trim($nombeCat))){
                    $sql = "INSERT INTO `tab_conf_categoria_prestacion` (`nombre_cat`, `descrip`) VALUES ('$nombeCat', '$descrip');";
                    $rs = $db->query($sql);
                    if(!$rs){
                        $error = 'Ocurrio un error con la Operacion crear Categoria';
                    }
                }else{
                    $error = 'Ocurrio un error, no recibo ningun nombre de la categoria';
                }

            }


            if($subaccion == 'modificar'){

                if(!empty(trim($nombeCat))){
                    $sql = "UPDATE `tab_conf_categoria_prestacion` SET `nombre_cat`='$nombeCat', `descrip`='$descrip' WHERE `rowid`='$idCat';";
                    $rs = $db->query($sql);
                    if(!$rs){
                        $error = 'Ocurrio un error con la Operacion Modificar Categoria';
                    }
                }else{
                    $error = 'Ocurrio un error, no recibo ningun nombre de la categoria ';
                }
            }

            $datos = array();
            if($subaccion == 'consultar'){

                $sql = "SELECT * FROM tab_conf_categoria_prestacion WHERE rowid = $idCat";
                $rs  = $db->query($sql);

                if($rs && $rs->rowCount() > 0){

                    while ($obj = $rs->fetchObject()){
                        $datos = $obj;
                    }

                }

                if(!$rs){
                    $error = 'Ocurrio un error al consultar la categoria , a Modificar';
                }

            }

            $output = [
                'error' => $error,
                'datos' => $datos
            ];

            echo json_encode($output);
            break;

        case 'eliminar_conf_categoria_desc':

            $error = '';
            $subaccion = GETPOST('subaccion');

            #id categoria
            #id descuento convenio
            $id = GETPOST('id');

            if($subaccion != "" && $id != ""){

                //eliminar categoria
                if($subaccion = 'categoria'){

                    $estaAsociado = 0;
                    $sqlconsult = "select * from tab_conf_prestaciones where fk_categoria = $id";
                    $rsconsult = $db->query($sqlconsult);
                    if($rsconsult->rowCount() > 0 ){
                        $estaAsociado++;
                        $error = '<b>Error</b>: Esta Categoría se encuentra Asociada con una prestación <br> <b>solo puede modificarla</b>';
                    }

                    #si asociado es 0 puede eliminar caso contrario no ?
                    if($estaAsociado==0){

                        $sql = "DELETE FROM `tab_conf_categoria_prestacion` WHERE `rowid`= '$id' ;";
                        $rs  = $db->query($sql);

                        if(!$rs){
                            $error = '<b>Error</b>:  Al Eliminar, Consulte con soporte Técnico';
                        }
                    }

//                    print_r($subaccion);
//                    die();
                }

                if($subaccion == ''){

                }

            }else{
                $error = 'Error, no se encuentran los parametros, Consulte con Soporte Técnico';
            }

            $output = [
                'error' => $error
            ];

            echo json_encode($output);
            break;

        case 'nuevoConvenio':

            $error = '';

            $subaccion  = GETPOST('subaccion');

            $nombre         = GETPOST("nombre");
            $valor          = GETPOST("valor");
            $descri         = GETPOST("descrip");
            $iddescConve    = GETPOST('id');

            if($subaccion == 'nuevo'){

                $sql = "INSERT INTO `tab_conf_convenio_desc` (`nombre_conv`, `descrip`, `valor`)";
                $sql .= "VALUES (";
                $sql .= "'$nombre' ,";
                $sql .= "'$descri' ,";
                $sql .= "'$valor'  ";
                $sql .= ");";
                $rs = $db->query($sql);
                if(!$rs){
                    $error = 'Ocurrrió un error con la Operación , crear convenio';
                }
            }

            if($subaccion == 'modificar'){

                $sqlM = "UPDATE `tab_conf_convenio_desc` SET `nombre_conv`='$nombre', `descrip`='$descri', `valor`='$valor' WHERE `rowid`= ".$iddescConve;
                $rs = $db->query($sqlM);
                if(!$rs){
                    $error = 'Ocurrrió un error con la Operación , Modificar Convenio';
                }
            }

            if($subaccion == 'eliminar')
            {

                $puedeEliminar = 0;
                $msg_error = "";

                $sqlcomp = "SELECT * FROM  tab_conf_prestaciones  WHERE  fk_convenio = $iddescConve";
                $rsConv = $db->query($sqlcomp);

                if(  $rsConv->rowCount()>0 ){
                    $error = "No puede Eliminar esta Descuento tiene asociado  prestaciones <br>";
                    $puedeEliminar++;
                }

                $sqlcomp2 = "SELECT * FROm tab_admin_pacientes where fk_convenio = $iddescConve";
                $rsCom2 = $db->query($sqlcomp2);
                if( $rsCom2->rowCount()>0){
                    $error = "No puede Eliminar esta Descuento esta asociado a un paciente <b> confirme el directorio de paciente </b> <br>";
                    $puedeEliminar++;
                }


                if( $puedeEliminar == 0){
                    $del = "DELETE FROM `tab_conf_convenio_desc` WHERE `rowid`='$iddescConve';";
                    $db->query($del);
//                    print_r($del); die();
                }
            }
//            print_r($sqlM); die();

//            print_r($error);
//            die();
            $output = [
                'error' => $error,
            ];

            echo json_encode($output);
            break;

        case 'list_convenios':

            $resp = list_convenios("", false);

            $output = [
              'data' => $resp
            ];
            echo json_encode($output);
            break;

        case 'fetch_modificar_convenio':

            $error = "";
            $respuesta = [];
            $id = GETPOST('id');

            if($id != ""){
                $respuesta['datos'] = list_convenios($id, true);
            }

            #print_r($resp); die();

            if(count($respuesta['datos']) == 0 ){
                $error = 'Ocurrió un error , Consulte con soporte Técnico';
            }

//            print_r($respuesta);
//            die();
            $output = [
                'error'     => $error,
                'respuesta' => $respuesta['datos']
            ];
            echo json_encode($output);
            break;


        #USUARIOS ODONTOLOGOS
        case 'infoUsuarioOdontic':

            $data = [];
            $obtenerUsuario = array();
            $idusuMod = GETPOST('idmodusu');

            $cual  = GETPOST('cual');
            $error ="";

            if($cual == 'list'){
                $permisoConsultar = (!PermitsModule(14,1))?0:1;

                if($permisoConsultar){
                    $data = infolistUsuarios($cual,$idusuMod);

                    $output = [
                        "draw"            => $_POST["draw"],
                        "data"            => $data['data'],
                        "recordsTotal"    => $data['total'],
                        "recordsFiltered" => $data['total'],
                        'error'           => $error,
                    ];

                    echo json_encode($output);
                    die();
                }
            }
            if( $cual == 'objecto'){
                $data = infolistUsuarios($cual,$idusuMod);
            }


            $output = [
                'error'     => $error,
                'data'      => $data,
            ];


            echo json_encode($output);

            break;

        case 'valid_ci_odontolog':

            $object_err = array();

            $connecionEntidad = new CONECCION_ENTIDAD();

            $err = "";


            $id_cedula = GETPOST('id_cedula');

            $sql    = "select count(cedula) as numcedul from tab_odontologos where cedula != 'NULL' and replace(cedula,'-','') = '$id_cedula'   ";
            $rsced  = $db->query($sql);
            if($rsced){
                if($rsced->rowCount() > 0){
                    if($rsced->fetchObject()->numcedul > 0){
                        $err = 'C.I ya se encuentra registrada';
                        $object_err[] = 'C.I ya se encuentra registrada';
                    }
                }
            }


            //valida contra la entidad
            $sqlentity  = "select nombre_user ,idcedula from tab_login_entity where replace(idcedula,'-','') = '$id_cedula' ";
            $rsulEntity = $connecionEntidad::CONNECT_ENTITY()->query($sqlentity);
            if($rsulEntity&&$rsulEntity->rowCount()>0){
                $err = 'C.I ya se encuentra registrada entity';
                $object_err[] = 'C.I ya se encuentra registrada entity';
            }

            $output = [
                'error'     => $err,
                'error_object' => $object_err
            ];


            echo json_encode($output);
            break;

        case "status_update_users":

            $err              = "";
            $idloginusers     = GETPOST("idlogin");
            $objectUser       = getnombreUsuario($idloginusers);
            $status           = GETPOST("status");

            #print_r($objectUser); die();
            if($status!=""){
                if($objectUser->admin == 0){
                    $err = status_update_usuario($idloginusers, $objectUser->login_idusers_entity, $status);
                }else{
                    $err = "Ud. no puede modificar un usuario administrador <br>
                                <b> El usuario administrado solo puede ser modificado por el mismo </b>";
                }
            }

            $output = [
                'error'     => $err ,
            ];
            echo json_encode($output);
            break;

        case "list_laboratorios":

            $data = array();
            $Total          = 0;
            $start          = $_POST["start"];
            $length         = $_POST["length"];
            $search         = $_POST["search"];

            $permisoConsultar = (!PermitsModule(15,1)?" limit 0 ":"");

            $sql = "SELECT  
                        l.rowid, 
                        l.name , 
                        l.direccion , 
                        l.telefono , 
                        l.info_adicional ,
                        ifnull( (select 
                            round(sum(d.amount), 2) amount
                        FROM tab_pagos_independ_pacientes_det d where month(d.feche_create) = month(now()) and (select count(*) from tab_conf_prestaciones p where p.fk_laboratorio = l.rowid and p.rowid = d.fk_prestacion) > 0
                            and (select count(*) from tab_plan_tratamiento_det t where t.fk_prestacion = d.fk_prestacion and t.estadodet = 'R') ) ,0
                        ) as total_prest_realizadas , 
                        CASE
                            WHEN l.estado = 'A' THEN 'ACTIVO'
                            WHEN l.estado = 'E' THEN 'DESACTIVADO'
                            ELSE ''
                        END AS estado ,
                        l.estado as estadoInd     
                    FROM tab_conf_laboratorios_clinicos l where rowid > 0 ";


            if(!empty($search)){
                $sql .= " and replace((concat(name,direccion,info_adicional)),' ','') like '%".(str_replace(' ','', $search['value']))."%' ";
            }
            $sql .= $permisoConsultar;
            $sqlTotal = $sql;

            if($start || $length)
                $sql.=" LIMIT $start,$length;";

            $resultTotal = $db->query($sqlTotal);
            $resul = $db->query($sql);

            if($resul){

                $Total = $resultTotal->rowCount();

                while ($object = $resul->fetchObject()){

                    $rows = array();

                        if($object->estadoInd=='A')
                            $estado  = "<label class=\"label\" style=\"background-color: #D5F5E3; color: green; font-weight: bolder\">".$object->estado."</label>";
                        else
                            $estado  = "<label class=\"label \" style=\"background-color: #FADBD8; color: red; font-weight: bolder\">".$object->estado."</label>";

                        $rows[]         = "";
                        $rows[]         = $object->name;
                        $rows[]         = $object->info_adicional;
                        $rows[]         = number_format($object->total_prest_realizadas, 2,'.','');
                        $rows[]         = $estado;
                        $rows['estado'] = $object->estadoInd;
                        $rows['idlab']  = $object->rowid;

                    $data[] = $rows;
                }
            }


            $output = array(
                "data"            => $data,
                "recordsTotal"    => $Total,
                "recordsFiltered" => $Total
            );

            echo json_encode($output);
            break;

        case "fetchModificarLaboratorio":

            $err = "";
            $ArrayLaboratorio = [];
            $idLab = GETPOST("idLab");
            $sqlLabor = "select * from tab_conf_laboratorios_clinicos where rowid = $idLab limit 1";
            $result = $db->query($sqlLabor);
            if($result){
                if($result->rowCount()>0){
                    $ArrayLaboratorio = $result->fetchObject();
                }else{
                    $err = "Ocurrio un error No se encontro el Laboratorio";
                }
            }

            $output = array(
                "error"        => $err,
                "information"  => $ArrayLaboratorio,

            );

            echo json_encode($output);
            break;

        case "validtitulo_perfil":

            $err = "";

            $connecionEntity = new CONECCION_ENTIDAD();
            $textPerfil = GETPOST("namePerfil");

            $sql    = "SELECT count(*) as count_n FROM tab_perfiles_add where replace(text, ' ', '') =  replace('".$textPerfil."', ' ', '')";
            $result =  $connecionEntity::CONNECT_ENTITY()->query($sql);
            if($result){
                $repit = $result->fetchObject();
                if($repit->count_n > 0){
                    $err = "Nombre del Perfil repetido";
                }
            }

            $output = array(
                'err' => $err
            );
            echo json_encode($output);
            break;

        case "nuevoUpdateLaboratorio":

            $error = "";

            $idlaboratorio  = GETPOST("idLaboratorio");
            $subaccion      = GETPOST("subaccion");
            $object         = (empty(GETPOST("datos")))?array():json_decode(GETPOST("datos"));


            $datos = array(
                'nombre_laboratorio'        => $object->nombre_laboratorio ,
                'direccion_laboratorio'     => $object->direccion_laboratorio ,
                'telefono_laboratorio'      => $object->telefono_laboratorio ,
                'infoAdicional_laboratorio' => $object->infoAdicional_laboratorio ,
            );

            if($subaccion=="nuevo"){
                $error = crearUpdateLaboratorio($subaccion, $datos, 0);
            }
            if($subaccion=="modificar"){
                $error = crearUpdateLaboratorio($subaccion, $datos, $idlaboratorio);
            }

            $output = array(
                'error' => $error
            );
            echo json_encode($output);
            break;


        case "nuevoModificarPrestacionLab":

            $error = "";

            $idlab      = GETPOST("idlab");
            $subaccion  = GETPOST("subaccion");
            $datos      = GETPOST("datos");

            if($subaccion=="nuevo"){

                $qu  = "INSERT INTO `tab_conf_prestaciones` (`descripcion`, `fk_user`, `fk_categoria`, `fk_laboratorio`, `valor`, `costo_x_clinica`, `precio_paciente`, `date_cc`) ";
                $qu .= " values(";
                $qu .= " '".$datos['nameprestacion']."' ";
                $qu .= " , ".$user->id." ";
                $qu .= " , ".$datos['catprestacion']." ";
                $qu .= " , ".$idlab." ";
                $qu .= " , ".$datos['precio']." ";
                $qu .= " , ".$datos['costo']." ";
                $qu .= " , ".$datos['precio']." ";
                $qu .= " , now() ";
                $qu .= "";
                $qu .= ")";

                $result = $db->query($qu);
                if(!$result){
                    $error = "Ocurrio un error con l a Operación Crear Prestación";
                }

            }

            if($subaccion=="modificar") {

                $idp = GETPOST('idp');

                $que = " UPDATE `tab_conf_prestaciones` SET `descripcion`='".$datos['nameprestacion']."', `fk_categoria`=".$datos['catprestacion'].", `valor`=".$datos['precio'].", `costo_x_clinica`=".$datos['costo'].", `precio_paciente`=".$datos['precio']." ";
                $que .= " WHERE `rowid`=".$idp. " and fk_laboratorio = ".$idlab;
                $result = $db->query($que);
                if(!$result){
                    $error = "Ocurrio un error con la Operación Modificar Prestación";
                }

            }

            $output = array(
                'error' => $error
            );
            echo json_encode($output);
            break;

        case "tableDinamicPrestacion":

            $data = [];
            $Total = 0;

            $respuesta = [
                'data' => array(),
                'total' => 0
            ];

            $sqlTotal = "";
            $table = GETPOST("table");
            $idlab = GETPOST("idlab");

            $idtratamiento = GETPOST("idtratamiento");
            $idpacit       = GETPOST("idpacit");
            $statusTramiento  = GETPOST("statusTratam");

            $objecFiltro = (object)array(
                "idtratamiento"     => $idtratamiento ,
                "idpacient"         => $idpacit ,
                "statusTratamint"   => $statusTramiento,
                "fecha"             => GETPOST('fecha'),
            );

            $searchLab = GETPOST("searchLab");

            if($table=='PrestacionesXlaboratorio'){
                $respuesta = listaPrestacionesLaboratorioDinamic($table, $idlab, $searchLab, $objecFiltro);
            }

            if($table=='PagosRealizado'){
                $respuesta = listaPrestacionesLaboratorioDinamic($table, $idlab, $searchLab, $objecFiltro);
            }

            if($table=='tratamientosPrestaciones'){
                $respuesta = listaPrestacionesLaboratorioDinamic($table, $idlab, $searchLab, $objecFiltro);
            }

            $output = array(
                "data"            => $respuesta['data'],
                "recordsTotal"    => $respuesta['total'],
                "recordsFiltered" => $respuesta['total']
            );
            echo json_encode($output);
            break;


        case "estadosLaboratorio":

            $error = '';
            $idlab = GETPOST('idlab');
            $type  = GETPOST('type');

            if($idlab){

                if($type=='desactivar')
                    $result = $db->query("UPDATE `tab_conf_laboratorios_clinicos` SET `estado`='E' WHERE `rowid`=".$idlab);
                if($type=='activar')
                    $result = $db->query("UPDATE `tab_conf_laboratorios_clinicos` SET `estado`='A' WHERE `rowid`=".$idlab);

                if(!$result){
                    $error = 'Ocurrio un error con la Operación';
                }
                
            }else{
                $error = 'Ocurrio un error con la Operación';
            }

            $output = [
                'error' => $error
            ];
            echo json_encode($output);
            break;

        case "fetchPrestacionxLaboratorio":

            $error = '';
            $idprest  = GETPOST('idprestLab');
            $idLab    = GETPOST('idlab');
            $dataPrestacionxLaboratorio = [];

            $que = "SELECT fk_categoria , descripcion , costo_x_clinica , precio_paciente FROM tab_conf_prestaciones c where c.rowid = ".$idprest." and c.fk_laboratorio = ".$idLab;
            $result = $db->query($que);
            if($result&&$result->rowCount()>0){
                $dataPrestacionxLaboratorio = $result->fetchAll();
            }else{
                $error = 'Ocurrio un error';
            }

            #print_r($dataPrestacionxLaboratorio); die();

            $output = [
                'error' => $error ,
                'data'  => $dataPrestacionxLaboratorio
            ];
            echo json_encode($output);
            break;

        case "listaSolicitudesinfo":

            $data = [];

            $Total          = 0;
            $start          = $_POST["start"];
            $length         = $_POST["length"];

            $nomPrestacion = GETPOST('nomprest');
            $paciente      = GETPOST('paciente');
            $odontocargo   = GETPOST('odontocargo');

            $idLab         = GETPOST("idLab");

            $sql = "SELECT
	                cast(pc.fecha_create as date ) as dateff_tratam , 
                    lb.name as laboratorio , 
                    
                    if(pc.edit_name != '' , pc.edit_name  , 
                            (select concat('Plan de Tratamiento',' #',pc.numero) from tab_plan_tratamiento_cab pc where pc.rowid = pd.fk_plantratam_cab) ) as trata_num ,
                            
                    cp.descripcion as prestacion, 
                    (select concat(ap.nombre,' ', ap.apellido) from tab_admin_pacientes ap where ap.rowid = pc.fk_paciente) as paciente , 
                    if(pc.fk_doc != 0 , 
								(select concat(o.nombre_doc, ' ', o.apellido_doc) from tab_odontologos o where o.rowid = pc.fk_doc) , 'No asignado') as odontolog_acargo , 
                    if(pd.fk_diente=0,'',pd.fk_diente) as pieza , 
                    case
                        when pd.estadodet = 'A' then 'Pendiente'
						when pd.estadodet = 'P' then 'Proceso'
                        when pd.estadodet = 'R' then 'Realizado'
                        else ''
                    end as estado , 
                    pd.total as totalTratamientoPrestacion, 
                    round(ifnull((select sum(amount) from tab_pagos_independ_pacientes_det p where (p.fk_plantram_det = pd.rowid and p.fk_plantram_cab = pc.rowid) and p.fk_prestacion = cp.rowid ),0), 2) as amount,
                    pd.date_recepcion_status_tramient as fecha_recepcion ,
                    pd.estadodet , 
                    pd.rowid as iddet_tratam ,
                    pc.rowid as idcab_tratam ,
                    pc.fk_paciente as idpacientetram,
                    pd.fk_diente as idpieza
                From 
                    tab_conf_prestaciones cp , 
                    tab_plan_tratamiento_det pd , 
                    tab_plan_tratamiento_cab pc , 
                    tab_conf_laboratorios_clinicos lb 
                where 
                cp.rowid = pd.fk_prestacion 
                and cp.fk_laboratorio = lb.rowid
                and pc.rowid = pd.fk_plantratam_cab
                and lb.rowid = ".$idLab;

            if($nomPrestacion!=""){
                $sql .= " and  replace(cp.descripcion,' ','') like '%".str_replace(' ','', $nomPrestacion)."%' ";
            }
            if($paciente!=""){
                $sql .= " and  pc.fk_paciente =  ".$paciente;
            }
            if($odontocargo!=""){
                $sql .= " and  pc.fk_doc = ".$odontocargo;
            }

            $sql .= " order by pd.rowid desc";
            $sqlTotal = $sql;

            if($start || $length)
                $sql.=" LIMIT $start,$length;";

//            print_r($sql); die();
            $resultTotal = $db->query($sqlTotal);
            $Total = $resultTotal->rowCount();

            $result = $db->query($sql);
            if($result && $result->rowCount()>0){
                while ($object = $result->fetchObject()){

                    if($object->estadodet == 'A')
                        $label = '<label class="label" style="background-color: #F6E944; color: #B88B1C; font-weight: bolder">PENDIENTE</label>';
                    if($object->estadodet == 'P')
                        $label = '<label class="label" style="background-color: #7BA5E1; color: #114DA4; font-weight: bolder">EN PROCESO</label>';
                    if($object->estadodet == 'R')
                        $label = '<label class="label" style="background-color: #D5F5E3; color: green; font-weight: bolder">REALIZADO</label>';

                    $datasetSolicitud = [
                        'tramdet'     => $object->iddet_tratam,
                        'tramcab'     => $object->idcab_tratam,
                        'idpaciente'  => $object->idpacientetram,
                        'idpieza'     => $object->idpieza
                    ];

                    $row = [];
                    $row[] = "<input type='checkbox' class='SolicitudChecked' name='SolicitudChecked' onchange='validarMarcarSolicitud()' data-datasolicitud='".(json_encode($datasetSolicitud))."' data-iddettramient='$object->iddet_tratam' value='$object->iddet_tratam' >"; #id plan de tratamiento detalle
                    $row[] = str_replace('-','/', $object->dateff_tratam);
                    $row[] = $object->trata_num;
                    $row[] = $object->paciente;
                    $row[] = $object->prestacion;
                    $row[] = $object->odontolog_acargo;
                    $row[] = str_replace('-','/', $object->fecha_recepcion);
                    $row[] = $label;
                    $row["id_tratam_det"] = $object->fecha_recepcion;

                    $data[] = $row;
                }
            }

            $output = array(
                "data"            => $data,
                "recordsTotal"    => $Total,
                "recordsFiltered" => $Total
            );
            echo json_encode($output);

            break;




        ////////////////////////////**************
        case 'statusUpdateSolicitudes':

            $error = "";
            $question = "";
            $iddettratamiento = GETPOST("iddettratamient");
            $statusActual = GETPOST("statusActual");

//            die();

            if(!empty($iddettratamiento) && $iddettratamiento != 0)
            {

                $resul = $db->query("select estadodet from tab_plan_tratamiento_det where rowid = ".$iddettratamiento)->fetchObject();
                if(!empty($resul->estadodet))
                {
                    $status = $resul->estadodet;

                    if($statusActual=='R'){ //realizado
                        if($status=='A' || $status=='P'){
                            $comment = "Cambio de estado desde Solicitud (REALIZADO) de la Prestación por el Usuario: ".$user->name;
                            $r = $db->query("UPDATE `tab_plan_tratamiento_det` SET `estadodet`='R', comment_laboratorio_auto = '$comment', date_recepcion_status_tramient = now() WHERE `rowid`=$iddettratamiento;");
                            if(!$r)
                                $error = 'Ocurrio un error con la Operación Estado: REALIZADO';
                        }else{
                            if($status == $statusActual){
                                $question = 'Ya se encuentra en estado <b>REALIZADO</b>';
                            }
                        }
                    }
                    if($statusActual=='P'){ //realizado

                        if($status=='A'){
                            $comment = "Cambio de estado desde Solicitud (EN PROCESO) de la Prestación por el Usuario: ".$user->name;
                            $r = $db->query("UPDATE `tab_plan_tratamiento_det` SET `estadodet`='P', comment_laboratorio_auto = '$comment', date_recepcion_status_tramient = now() WHERE `rowid`=$iddettratamiento;");
                            if(!$r)
                                $error = 'Ocurrio un error con la Operación Estado: EN PROCESO';
                        }else{
                            if($status == $statusActual){
                                $question = 'Ya se encuentra en estado <b>EN PROCESO</b>';
                            }else{
                                $question = 'No puede cambiar de estado de REALIZADO A EN PROCESO';
                            }
                        }
                    }
                }

            }else{
                $error = 'Ocurrion un error con la Operación <b>Estado de Solicitud</b>';
            }

            $output = array(
                "error"       => $error,
                "question"    => $question,
            );
            echo json_encode($output);
            break;


            //eliminar perfil de la entidad
        case 'delete_perfil_users':

            $error = '';
            $idPerfilEntity = GETPOST("idPerfilEntity");

            $cn = new CONECCION_ENTIDAD();

            $resultquestion = $cn::CONNECT_ENTITY()->query("select count(*) as count from tab_login_entity where fk_perfiles = $idPerfilEntity")->fetchObject();

            if($resultquestion->count == 0){

                $sql = "DELETE FROM `tab_perfiles_add` WHERE `rowid`= $idPerfilEntity and `numero_entity`= '".$conf->EMPRESA->ENTIDAD."' ";
                $result = $cn::CONNECT_ENTITY()->query($sql);

                $cn::CONNECT_ENTITY()->query("DELETE FROM `tab_permisos_user` WHERE `fk_perfil_module`= $idPerfilEntity and `numero_entity`='".$conf->EMPRESA->ENTIDAD."' ;");

            }else{

                $error = "Registro asociado a uno o varios Usuario";

            }

            $output = array(
                "error"       => $error,
            );
            echo json_encode($output);
            break;
    }


}

function nuevoUpdateOdontologos($nombre, $apellido, $celular, $telefo_conve, $email, $direccion, $ciudad, $especialidad, $cedula_ruc)
{
    global $db, $conf;

    $error = false;

    $sql  = "INSERT INTO `tab_odontologos` (`nombre_doc`, `apellido_doc`, `celular`, `telefono_convencional`, `email`, `ciudad`, `direccion`, `fk_especialidad` , `cedula` ) ";
    $sql .= "VALUES (";
    $sql .= "'$nombre',";
    $sql .= "'$apellido',";
    $sql .= "'$celular',";
    $sql .= "'$telefo_conve',";
    $sql .= "'$email',";
    $sql .= "'$ciudad',";
    $sql .= "'$direccion',";
    $sql .= " ".(!empty($especialidad) ? 0 : $especialidad )."".',';
    $sql .= "'$cedula_ruc'";
    $sql .= ")";

//    print_r($sql); die();
    $rs = $db->query($sql);

    if($rs){ $error = true;  }

    return $error;
}

function UpdateOdontologos($nombre, $apellido, $celular, $telefo_conve, $email, $direccion, $ciudad, $especialidad, $id, $cedula_ruc, $aux_idcedula)
{

    global  $db, $conf;

    $connecionEntity = new CONECCION_ENTIDAD();

    $error = false;

    $fk_especi = !empty($especialidad) ? $especialidad : 0 ;
    $sql1  = "  UPDATE `tab_odontologos` SET `nombre_doc` = '$nombre', `apellido_doc` = '$apellido', `celular` = '". (str_replace(' ','', $celular)) ."', ";
    $sql1 .= "   `telefono_convencional` = '$telefo_conve', `email` = '$email' ";
    $sql1 .= " , `ciudad` = '$ciudad' ";
    $sql1 .= " , `direccion` = '$direccion'";
    $sql1 .= " , `fk_especialidad` = $fk_especi ";
    $sql1 .= " , `cedula` = '$cedula_ruc'";
    $sql1 .= "    WHERE (`rowid` = '$id') ";
    $rs = $db->query($sql1);

    if($rs)
    {
        /**SE ACTUALIZA EL USUSAIO SI ES NECESARIO O ESTA ASOCIADO*/
        $error= true;
        $upd = $db->query("UPDATE tab_login_users SET cedula = '$cedula_ruc' WHERE fk_doc = $id  and rowid > 0");
        if($upd)
        {
            $sqlusu = "SELECT * FROM tab_login_users WHERE fk_doc = $id ";
            $usu = $db->query($sqlusu);

            if($usu->rowCount()==1)
            {
                $us = $usu->fetchObject(); #Object Usuario

                $idEntityusers = null;

                #OBTENGO EL ID ENTITY tab_login_entity
                $quentity = "select rowid as id_Entity_users from tab_login_entity where login_idusers_entity = '".$us->login_idusers_entity."' and fk_entidad  = ".$conf->EMPRESA->ID_ENTIDAD." and entity = '".$conf->EMPRESA->ENTIDAD."'  ";
                $rsidEnusrs = $connecionEntity::CONNECT_ENTITY()->query($quentity);
                if($rsidEnusrs){
                    if($rsidEnusrs->rowCount()==1){
                        $idEntityusers = $rsidEnusrs->fetchObject()->id_Entity_users;
                    }
                }


                if($idEntityusers != null)
                {

                    $datos = [];

                    $ob = getnombreDentiste($id); #obtengo el dastos del dentista ultima actualizacion

                    $datos['nombre']            = $ob->nombre_doc;
                    $datos['apellido']          = $ob->apellido_doc;
                    $datos['celular']           = $ob->celular;
                    $datos['pass']              = $us->passwor_abc;
                    $datos['email']             = $ob->email;
                    $datos['usuario']           = $us->usuario;
                    $datos['id_usuario']        = $us->rowid;
                    $datos['idcedula']          = $cedula_ruc;
                    $datos["fk_perfil_entity"]  = $us->fk_perfil_entity;    #id perfil entity Filtrado x Clinica
                    $datos["idEntityusers"]     = base64_encode($idEntityusers);  #id perfil entity Filtrado x Clinica

                    $resul = GenerarUsuarioGlob($datos, 'modificar');


                    if($resul != ""){
                        $error = false;
                    }

                }else{
                    $error = false;
                }

            }
        }else{
            $error = false;
        }


    }else{
        $error = false;
    }

    return $error;

}

function GenerarUsuarioGlob($datos = array(), $subaccion)
{

    global  $conf, $user, $dbConectar;

    $error = '';
    $nombreUsuario      = $datos["nombre"];
    $apellidoUsuario    = $datos["apellido"];
    $celularUsuario     = $datos["celular"];
    $passUsuario        = $datos["pass"]; #password
    $usuUsuario         = $datos["usuario"]; #Usuario
    $id_usuario         = $datos["id_usuario"];
    $email              = $datos["email"]; #Email
    $idcedula           = $datos["idcedula"]; #cedula
    $fk_perfil_entity   = $datos["fk_perfil_entity"];


    /* Parametros Nuevo Update Users Entity */
    $object = new stdClass();
    $object->nombreuser         = $nombreUsuario;
    $object->apelluser          = $apellidoUsuario;
    $object->pass               = $passUsuario;
    $object->nameusers          = $usuUsuario;
    $object->email              = $email;
    $object->id_users_clinica   = $id_usuario;
    $object->usu_cedula         = $idcedula;
    $object->fk_perfil_entity   = $fk_perfil_entity;


    $Entidad_Login = new CONECCION_ENTIDAD(); //OBTENGO LAS FUNCIONES DE LA FUNCION PRINCIPAL

    if($subaccion == "nuevo") {
        $error = $Entidad_Login::LOGIN_USUARIO_ENTITY("nuevo", $object, $dbConectar);
    }

    if($subaccion=="modificar") {

        $object->idEntityusers = $datos['idEntityusers']; //id usuario entity clinica
        $error = $Entidad_Login::LOGIN_USUARIO_ENTITY("modificar", $object, $dbConectar);
    }

    #print_r($object); die();

    if($error==1){
        return '';
    }else{
        return 'Ocurrió un problema con el proceso, consulte con soporte Técnico';
    }

}

function IconOdontologo($id, $Icon)
{
    global  $db, $conf;


    $type="";
    $datos = [];

    switch ($Icon['type'])
    {
        case 'image/png':
            $type = '.png';
            break;

        case 'image/jpeg':
            $type = '.jpeg';
            break;

    }

    $tmp_name = $Icon["tmp_name"]; #ruta temporal del fichero
    $name_archv = "conf_odont_icon_$id"."_".$conf->EMPRESA->ENTIDAD."".$type; #label del fichero

    $datos["nomb_archv"] = $name_archv;

    $link = UploadFicherosLogosEntidadGlob($name_archv, $type, $tmp_name);

    if($link!=false)
    {
        //datos es mayor a 0 guardo
        if(count($datos)>0)
        {
            $sqlUp = "UPDATE `tab_odontologos` SET `icon` = '".$datos['nomb_archv']."' WHERE (`rowid` = '$id')";
            $rs = $db->query($sqlUp);
            if (!$rs)
            {
                unlink($link); //elimino en caso no se llega a guardar
            }
        }
    }

    return 1;

//    print_r($conf->DIRECTORIO);
//    die();
}

function list_odontolosGention($estado){

    global $db, $conf, $user;


    $PermisoConsultar = (!PermitsModule(12,1))?" and 1<>1 ":"";

    $data = array();

    $sql = "SELECT 
            s.nombre_doc,
            s.apellido_doc,
            s.celular,
            s.direccion,
            s.email,
            s.direccion,
            s.rowid,
            s.estado,
            s.cedula, 
            IFNULL((SELECT 
                            e.nombre_especialidad
                        FROM
                            tab_especialidades_doc e
                        WHERE
                            e.rowid = s.fk_especialidad),
                    0) especialidad, 
            s.tms
        FROM
            tab_odontologos s
        WHERE
            s.rowid > 0";

    if(!empty($estado)){
        $sql .= " and s.estado = '$estado' ";
    }

    $sql .= $PermisoConsultar;

    $sql .= " and (select count(*) from tab_login_users g where g.rowid = ".$user->id." and  g.fk_doc = s.rowid) = 0 " ;
    $sql .= " order by tms desc ";

//    print_r($conf);

    $res = $db->query($sql);

    if($res->rowCount() > 0){

        while ($obj = $res->fetchObject()){

            $row = array();

            $tieneUsuario=0; #obtendre el id del Usuario Creado

            $nomUsuario = "";
            $sqlUsu = "SELECT * FROM tab_login_users s WHERE s.fk_doc = $obj->rowid";
            $rs     = $db->query($sqlUsu);
            if($rs->rowCount()>0)
            {
                $ob = $rs->fetchObject();
                $tieneUsuario = $ob->rowid;
                $nomUsuario = "<a href='#' class=' btnhover ' style='color: #1e282c;'><i class=\"fa fa-user\"></i> <b>usuario:&nbsp;</b> $ob->usuario </a>  ";
            }


            #Link de Odontologos
            $NomOdontologos = '  <a href="#" class="btnhover" style="color:#333333"  data-idodont="'.$obj->rowid.'" onclick="modificarOdontologo('.$obj->rowid.')"> '. $obj->nombre_doc .' '. $obj->apellido_doc.' </a>';

            #Link de Usuarios
            $LinkUsuario    = (!empty($nomUsuario))?'<small style="display: block;">'. $nomUsuario .'</small>':'';

            $row[] = $NomOdontologos . $LinkUsuario;
            $row[] = $obj->cedula; #Cedula del odontolog@
            $row[] = $obj->direccion;
            $row[] = $obj->email;
            $row[] = ( $obj->especialidad == '0') ? 'General' :  $obj->especialidad;

            if($obj->estado == 'A'){
                $row[] = '<a class="btn btn-xs" style="background-color: #fadbd8; color: red; font-weight: bolder; " onclick="UpdateEstadoOdontologos('.$obj->rowid.', \'E\')"> Desactivar</a>';
            }
            if($obj->estado == 'E'){
                $row[] = '<a class="btn btn-xs" style="background-color: #D5F5E3; color: green; font-weight: bolder" onclick="UpdateEstadoOdontologos('.$obj->rowid.', \'A\')">Activar</a>';
            }

            $row[] = $obj->rowid;
            $row[] = $obj->estado;

            $data[] = $row;
        }
    }

    return $data;
}

#list convenios
function list_convenios($id = "", $uno)
{

    global $db;

    $permisoConsultar = (!PermitsModule(11,1))?" and 1<>1 ":"";

    $data = array();

    $sql = "SELECT rowid , nombre_conv , descrip , round(valor, 2) valor FROM tab_conf_convenio_desc WHERE rowid > 0";

    if($id != ""){
        $sql .= " and rowid = $id";
    }
    $sql .= $permisoConsultar;

    $sql .= " order by rowid desc";
    $rs  = $db->query($sql);
    if($rs){

        while ($obj = $rs->fetchObject())
        {

            $row = array();

            $row[] = $obj->nombre_conv;
            $row[] = $obj->descrip;
            $row[] = $obj->valor. " %";

            #pido con el id
            if($uno == true){
                $row[] = $obj->valor;
                return $row;
            }else{
                $row[] = "
                    <table>
                        <tr>
                            <td> <a href='#modal_conf_convenio' class='btn btnhover btn-xs' data-toggle='modal' style='cursor: pointer; display: inline-block; text-align: right; color: #008000; font-size: 1.8rem' onclick='fetch_modificar_convenio($obj->rowid)'><i class='fa fa-edit'></i> </a> </td>
                            <td> <a href='#' class='btn btnhover btn-xs'  style='color: #9f191f;  display: inline-block; cursor: pointer; text-align: right; font-size: 1.8rem' onclick='nuevoUpdateConvenio(\"eliminar\", $obj->rowid);'><i class='fa fa-trash'></i></a> </td>
                        </tr>
                    </table>";
            }

            $data[] = $row;
        }

    }

    return $data;
}

function infolistUsuarios($cual, $idusuMod)
{
    global $db, $conf, $user;

    $permisos = "1=1";

    $Total          = 0;
    $start          = GETPOST("start");
    $length         = GETPOST("length");

    $objetoUsuario = array();

    $data = array();
    $sql = "SELECT 
                us.rowid as id , 
                us.usuario , 
                us.passwor_abc , 
                us.estado , 
                us.cedula ,
                concat(od.nombre_doc ,' ', od.apellido_doc) as nomdoc , 
                us.tipo_usuario as tipusuarioNum, 
                if(us.tipo_usuario=1,'administrador','normal') as tipoUsuario , 
                us.fk_perfil_entity, 
                us.fk_doc, 
                us.login_idusers_entity as login_unique
                    FROM
                        tab_login_users us
                        left join
                        tab_odontologos od on od.rowid = us.fk_doc
                        WHERE 
                        ".$permisos;

    $sql .= " and  us.rowid !=  ".$user->id;
    $Total = $db->query($sql)->rowCount();

    if($start || $length){
        $sql.=" LIMIT $start,$length;";
    }

    $rsUs = $db->query($sql);
    if($rsUs && $rsUs->rowCount()>0){
        while ($usdoc =  $rsUs->fetchObject())
        {
            $estado = "";

            if($cual == 'objecto'){
                $objetoUsuario = $usdoc;
            }

            if($usdoc->estado == 'A')
                $estado = "<label class='label' style='background-color: #D5F5E3; color: green; font-weight: bolder'>ACTIVO</label>";
            if($usdoc->estado == 'E')
                $estado = "<label class='label ' style='background-color: #FADBD8; color: red; font-weight: bolder'>INACTIVO</label>";


            $Docd  = ($usdoc->fk_doc!=0)?"<a href='#' style='display: block'><small>Doctor(a): &nbsp;&nbsp; $usdoc->nomdoc </small></a>":"";
            $admin = (validSuperAdmin($usdoc->login_unique))?"<a href='#' style='display: block' > <span class='fa fa-unlock-alt'></span> <small>administrador</small></a> ":"";


            $row = array();
            $row[] = "";
            $row[] = $usdoc->usuario ."". $Docd;
            $row[] = $admin;
            $row[] = $estado;

            $row[] = $usdoc->estado;
            $row[] = "";
            $row[] = $usdoc->id;

            $row['id_users'] = $usdoc->id;
            $row['estado']   = $usdoc->estado;

            $data[] = $row ;
        }
    }

    if($cual=="list"){
        return array(
            'data'  => $data,
            'total' => $Total
        );
    }
    if($cual=="objecto"){
        return $objetoUsuario;
    }

}

function addPerfil( $Modperfiles, $idEntityUsers , $Perfilenom, $Modificar_Id = 0 )
{


    global $db, $conf, $user;

    $counError = 0;
    $connecionEntidad = new CONECCION_ENTIDAD();

    if($Modificar_Id!=0)
        $idPerfil = $Modificar_Id; //modificar Perfil
    else
        $idPerfil = null; //nuevo Perfil


    if($idPerfil==null){

        $sql = "INSERT INTO `tab_perfiles_add` (`text`,`fk_entity`,`numero_entity`) VALUES ('".$Perfilenom."', ".$conf->EMPRESA->ID_ENTIDAD." , '".$conf->EMPRESA->ENTIDAD."' );";
        $resultEntityPerfil   = $connecionEntidad::CONNECT_ENTITY()->query($sql);

        if($resultEntityPerfil){

            $queryLastidPerfil = "select ifnull(max(rowid),0) as idperfildata from tab_perfiles_add where text = '".$Perfilenom."' and fk_entity = ".$conf->EMPRESA->ID_ENTIDAD." and numero_entity =".$conf->EMPRESA->ENTIDAD;
            $rp = $connecionEntidad::CONNECT_ENTITY()->query($queryLastidPerfil);

            if($rp){
                $idPerfil = $rp->fetchObject()->idperfildata;
            }

            if(($idPerfil==null||$idPerfil==0)==true){
                $counError++;
            }

        }else{
            $counError++;
        }

    }

    #print_r($counError); die();
    if( count($Modperfiles) > 0 && ($idPerfil==null || $idPerfil==0) == false ){

        $insertPermiss = [];

        $sqluserpermms = "INSERT INTO `tab_permisos_user` (`fk_modulo`, `fk_user_author`, `fk_action_permisos`, `fk_perfil_module`, `fk_entity`, `numero_entity`) VALUES ";
        foreach ($Modperfiles as $value){

            $idModule  = $value->idmodule;
            $idPermiso = $value->idPermiso;

            $insertPermiss[] = "( $idModule , ".$user->id_entidad_login.", $idPermiso, $idPerfil, ".$conf->EMPRESA->ID_ENTIDAD." , '".$conf->EMPRESA->ENTIDAD."')";

        }
        $sqluserpermms .= "".(count($insertPermiss)>0) ? implode(',', $insertPermiss): "";
        $rspermis = $connecionEntidad::CONNECT_ENTITY()->query($sqluserpermms);

        if(!$rspermis){
            $counError++;
        }

//        print_r( $sqluserpermms ); die();

    }else{
        $counError++;
    }


    if($counError>0)
        return "Ocurrio un error con la Operacion consulte con soporte";
    else
        return "";


}


function fetchPermisosPerfil(){

    global $conf;

    $data = [];

    $coneccionEntity = new CONECCION_ENTIDAD();

    $sql = "select p.rowid , p.text from tab_perfiles_add p where p.fk_entity = ".$conf->EMPRESA->ID_ENTIDAD." and p.numero_entity = '".$conf->EMPRESA->ENTIDAD."'  ";
    $result = $coneccionEntity::CONNECT_ENTITY()->query($sql);
    if($result && $result->rowCount() > 0 )
    {
        while ( $objEnd = $result->fetchObject() )
        {

            $dataPermisos = array();

            $sqlpermesosUser = "select * from tab_permisos_user where fk_entity = ".$conf->EMPRESA->ID_ENTIDAD." and numero_entity = '".$conf->EMPRESA->ENTIDAD."'  and fk_perfil_module =".$objEnd->rowid;
            $resultUser = $coneccionEntity::CONNECT_ENTITY()->query($sqlpermesosUser);

            if($resultUser && $resultUser->rowCount()>0)
            {

                while ( $objperm = $resultUser->fetchObject() ){

                    $dataPermisos[] = array(
                        "idModulo"   =>  $objperm->fk_modulo ,
                        "idPermiso"  =>  $objperm->fk_action_permisos ,
                    );
                }

            }

            $data[] = array(
                "idPerfil"  =>  $objEnd->rowid,
                "Perfil"    =>  $objEnd->text,
                "select_perfil" => array("id"=> $objEnd->rowid , "text" => $objEnd->text) ,
                "Permiss"   =>  $dataPermisos ,
            );

        }

        return $data;

    }else{

        return "Ocurrio un error con la funcion fetchPermisosPerfil consulte con soporte";
    }


//    print_r($data); die();

}

function ModificarPerfil($Modperfiles, $idEntityUsers , $Perfilenom, $idPerfilEntity){

    global $db, $conf, $user;

    $counError = 0;
    $connecionEntidad = new CONECCION_ENTIDAD();
    $cn = $connecionEntidad::CONNECT_ENTITY();

    if($idPerfilEntity==""||$idPerfilEntity==0){
        return "Ocurrio un error, no puede modificar el perfil consulte con soporte Tecnico";
    }

    $AsociadoPerfil = $cn->query("SELECT rowid , text FROM tab_perfiles_add where fk_entity = ".$conf->EMPRESA->ID_ENTIDAD." and numero_entity = '".$conf->EMPRESA->ENTIDAD."' and rowid = $idPerfilEntity ");

    if($AsociadoPerfil && $AsociadoPerfil->rowCount() == 1){

        $idPerfilobj = $AsociadoPerfil->fetchObject()->rowid;

        #Elimino el Perfil todo de lo del perfil y lo vuelvo a crear
        $sqlperfil = "UPDATE `tab_perfiles_add` set  `text`= '".$Perfilenom."'  WHERE `rowid`= ".$idPerfilobj. " and  fk_entity = ".$conf->EMPRESA->ID_ENTIDAD." and numero_entity = '".$conf->EMPRESA->ENTIDAD."'  ";
        $resultPerfil = $cn->query($sqlperfil);

        if($resultPerfil){

            $sqlPermissUser  = " DELETE FROM `tab_permisos_user` WHERE fk_perfil_module = ".$idPerfilobj." and fk_entity = ".$conf->EMPRESA->ID_ENTIDAD." and numero_entity = '".$conf->EMPRESA->ENTIDAD."'; ";
            $resulPermmsUser = $cn->query($sqlPermissUser);

            if(!$resulPermmsUser){
                return "Ocurrio un error con la operacion Modificar perfil (<small><b>ope 2</b></small>)";

            }else{

                $respuesta =  addPerfil($Modperfiles, $idEntityUsers, $Perfilenom, $idPerfilobj );

                return $respuesta;
            }

        }else{
            return "Ocurrio un error con la operacion Modificar perfil (<small><b>ope 1</b></small>)";
        }

    }else{

        return " Ocurrio un error de Perfil consulta con soporte (<small><b>ope 0</b></small>)";
    }



}


function status_update_usuario($idusersClinica , $identificador_entity_users, $status ){

    global $db, $conf;

    $coneccionEntity = new CONECCION_ENTIDAD();

    $err = 0;
    if($idusersClinica!="" && $identificador_entity_users!=""){

        $sqlclinica = "UPDATE `tab_login_users` SET `estado`='".$status."' WHERE `rowid`= ".$idusersClinica;
        $rsclin     = $db->query($sqlclinica);
        if(!$rsclin){
            $err++;
        }


        $sqlUpdatEntity = "UPDATE `tab_login_entity` SET `estado`='".$status."' WHERE login_idusers_entity = '".$identificador_entity_users."' and rowid != 0 and fk_entidad = ".$conf->EMPRESA->ID_ENTIDAD." and entity = '".$conf->EMPRESA->ENTIDAD."' ";
        $rsEntity   = $coneccionEntity::CONNECT_ENTITY()->query($sqlUpdatEntity);
        if(!$rsEntity){
            $err++;
        }


//        print_r($sqlUpdatEntity); echo '<pre>';
//        print_r($sqlclinica); echo '<pre>';
//        die();

        if($err>0)
            return "Ocurrio un error con la Operacion, consulte con soporte";
        else
            return "";

    }else{
        return "Ocurrio un error con la Operacion, consulte con soporte";
    }

}


function crearUpdateLaboratorio($subaccion, $datos = array(), $idLaboratorio){


    global $db, $conf, $user;

    $error  = "";

    if($subaccion=="")
        return "Ocurrio un error con la Operación <small>ope 0</small>";


    if($subaccion!="")
        if($subaccion=="modificar" && (empty($idLaboratorio) || $idLaboratorio==0) )
            return "Ocurrio un error con la Operación Modificar  <small>ope 1 <b>El sistema no identifico el parametro enviado</b> consulte con soporte</small>";


    if($subaccion=="nuevo"){

        $LaboraSql  = " INSERT INTO `tab_conf_laboratorios_clinicos` (`name`, `direccion`, `telefono`, `info_adicional`, `estado`) ";

        $LaboraSql .= " VALUES(";

        $LaboraSql .= "  '".$datos['nombre_laboratorio']."' ";

        $LaboraSql .= " ,'".$datos['direccion_laboratorio']."' ";

        $LaboraSql .= " ,'".$datos['telefono_laboratorio']."' ";

        $LaboraSql .= " ,'".$datos['infoAdicional_laboratorio']."' ";

        $LaboraSql .= " ,'A' ";

        $LaboraSql .= ")";

        $result = $db->query($LaboraSql);

        if(!$result){

            $error = "Ocurrio un error con la Operación Nuevo Laboratorio";
        }

    }

    if($subaccion=="modificar" && !empty($idLaboratorio) ){


        $LaboraSqlUpdate  = "UPDATE tab_conf_laboratorios_clinicos SET";

        $LaboraSqlUpdate .= "   `name`='".$datos['nombre_laboratorio']."' ";

        $LaboraSqlUpdate .= " , `direccion`='".$datos['direccion_laboratorio']."' ";

        $LaboraSqlUpdate .= " , `info_adicional`= '".$datos['infoAdicional_laboratorio']."' ";

        $LaboraSqlUpdate .= " , `telefono`= '".$datos['telefono_laboratorio']."' ";

        $LaboraSqlUpdate .= " WHERE `rowid`='$idLaboratorio' ";


        $result = $db->query($LaboraSqlUpdate);

        if(!$result){
            $error = "Ocurrio un error con la Operación Modificar Laboratorio";
        }

    }


    return $error;

}


function listaPrestacionesLaboratorioDinamic($table, $idlab, $searchLab = '', $objecFiltro){

    global $db, $conf, $user;

    $start          = $_POST["start"];
    $length         = $_POST["length"];
    $sqlTotal       = "";

    $data = array();
    $Total = 0;

    if($table=="PrestacionesXlaboratorio"){

        $sql = "select * from tab_conf_prestaciones where fk_laboratorio = $idlab";
        if(!empty($searchLab)){
            $sql .= " and descripcion like '%".$searchLab."%' ";
        }

        $sqlTotal = $sql;

        if($start || $length)
            $sql.=" LIMIT $start,$length;";


        $result = $db->query($sql);
        if($result&&$result->rowCount()){
            while ($object = $result->fetchObject()){

                $rows = array();

                $rows[] = "<input type='checkbox' id='modificarPrestacionLab' name='modificarPrestacionLab' data-idprestalab='".$object->rowid."' value='".$object->rowid."' >";
                $rows[] = $object->descripcion;
                $rows[] =  number_format($object->costo_x_clinica, 2,'.',',');
                $rows[] =  number_format($object->precio_paciente, 2,'.',',');
                $rows['prestaciones'] = true;

                $data[] = $rows;
            }
        }
    }

    if($table=="PagosRealizado"){

        $sqlp = "select 
                    p.fk_pago_cab as idpagocab, 
                    cast(p.feche_create as date) as dateff_pago, 
                    
                    (select ifnull(t.edit_name , concat('Plan de Tratamiento: # ',t.numero)) as nom from tab_plan_tratamiento_cab t where t.rowid = p.fk_plantram_cab) as p_tratamiento , 
                    
                    c.descripcion as prestacion, 
                    
                    (select concat(d.nombre,' ',d.apellido) from tab_admin_pacientes d where d.rowid = 
							(select t.fk_paciente from tab_plan_tratamiento_cab t where t.rowid = p.fk_plantram_cab)) as paciente , 
							
                    (select s.usuario from tab_login_users s where s.rowid = p.fk_usuario) as users , 
                    round(p.amount, 2) as  amount , 
                    round(c.costo_x_clinica, 2) as costo_x_clinica, 
                    round(c.precio_paciente, 2) as precio_paciente
             FROM
                tab_pagos_independ_pacientes_det p , 
                tab_conf_prestaciones c 
                where 
                p.fk_prestacion = c.rowid
                and c.fk_laboratorio = $idlab ";
        if(!empty($searchLab)){
            $sqlp .= " and c.descripcion like '%".$searchLab."%' ";
        }
        if(!empty($objecFiltro->idtratamiento)){
            $sqlp .= " and p.fk_plantram_cab = ".$objecFiltro->idtratamiento;
        }
        if(!empty($objecFiltro->idpacient)){
            $sqlp .= " and (select d.rowid from tab_admin_pacientes d where d.rowid = 
							(select t.fk_paciente from tab_plan_tratamiento_cab t where t.rowid = p.fk_plantram_cab)) = ".$objecFiltro->idpacient;
        }
        if(!empty($objecFiltro->fecha)){
            $sqlp .= " and cast(p.feche_create as date) = '".$objecFiltro->fecha."'";
        }

        #print_r($sqlp); die();
        $sqlTotal = $sqlp;

        if($start || $length)
            $sqlp.=" LIMIT $start,$length;";

        $result = $db->query($sqlp);
        if($result&&$result->rowCount()>0){
            while ($object = $result->fetchObject()){

                $rows = array();
                $rows[] = "";
                $rows[] = date("Y/m/d", strtotime($object->dateff_pago));
                $rows[] = 'PAG_'.str_pad($object->idpagocab, 6, "0", STR_PAD_LEFT);
                $rows[] = $object->p_tratamiento;
                $rows[] = $object->paciente;
                $rows[] = $object->prestacion;
                $rows[] = $object->users;
                $rows[] = $object->costo_x_clinica; 
                $rows[] = $object->precio_paciente;
                $rows[] = $object->amount;

                $data[] = $rows;
            }
        }

    }

    if($table=="tratamientosPrestaciones"){

        $sqltra = "Select
	                cast(pc.fecha_create as date ) as dateff_tratam , 
                    lb.name as laboratorio , 
                    if(pc.edit_name != '' , pc.edit_name  , 
                            (select concat('Plan de Tratamiento',' #',pc.numero) from tab_plan_tratamiento_cab pc where pc.rowid = pd.fk_plantratam_cab) ) as trata_num ,
                    cp.descripcion as prestacion, 
                    (select concat(ap.nombre,' ', ap.apellido) from tab_admin_pacientes ap where ap.rowid = pc.fk_paciente) as paciente , 
                    if(pd.fk_diente=0,'',pd.fk_diente) as pieza , 
                    case
                        when pd.estadodet = 'A' then 'Pendiente'
						when pd.estadodet = 'P' then 'Proceso'
                        when pd.estadodet = 'R' then 'Realizado'
                        else ''
                    end as estado , 
                    pd.total as totalTratamientoPrestacion, 
                    round(ifnull((select sum(amount) from tab_pagos_independ_pacientes_det p where (p.fk_plantram_det = pd.rowid and p.fk_plantram_cab = pc.rowid) and p.fk_prestacion = cp.rowid ),0), 2) as amount,
                    pd.estadodet 
                From 
                    tab_conf_prestaciones cp , 
                    tab_plan_tratamiento_det pd , 
                    tab_plan_tratamiento_cab pc , 
                    tab_conf_laboratorios_clinicos lb 
                where 
                cp.rowid = pd.fk_prestacion 
                and cp.fk_laboratorio = lb.rowid
                and pc.rowid = pd.fk_plantratam_cab
                and lb.rowid = $idlab ";

        if(!empty($searchLab)){
            $sqltra .= " and cp.descripcion like '%".$searchLab."%' ";
        }
        if(!empty($objecFiltro->idtratamiento)){
            $sqltra .= " and pd.fk_plantratam_cab = ".$objecFiltro->idtratamiento;
        }
        if(!empty($objecFiltro->idpacient)){
            $sqltra .= " and pc.fk_paciente = ".$objecFiltro->idpacient;
        }
        if(!empty($objecFiltro->statusTratamint)){
            $sqltra .= " and pd.estadodet = '".$objecFiltro->statusTratamint."' ";
        }
        if(!empty($objecFiltro->fecha)){
            $sqltra .= " and cast(pc.fecha_create as date ) = '".$objecFiltro->fecha."'";
        }

        #print_r($sqltra); die();
        $sqlTotal = $sqltra;

        if($start || $length)
            $sqltra .=" LIMIT $start,$length;";

        $result = $db->query($sqltra);
        if($result&&$result->rowCount()>0){
            while ($object = $result->fetchObject()){

                if($object->estadodet == 'A')
                    $label = '<label class="label" style="background-color: #F6E944; color: #B88B1C; font-weight: bolder">PENDIENTE</label>';
                if($object->estadodet == 'P')
                    $label = '<label class="label" style="background-color: #7BA5E1; color: #114DA4; font-weight: bolder">EN PROCESO</label>';
                if($object->estadodet == 'R')
                    $label = '<label class="label" style="background-color: #D5F5E3; color: green; font-weight: bolder">REALIZADO</label>';

                $rows = array();
                $rows[] = "";
                $rows[] = str_replace('-','/', $object->dateff_tratam);
                $rows[] = $object->trata_num;
                $rows[] = $object->prestacion;
                $rows[] = $object->paciente;
                $rows[] = $object->pieza;
                $rows[] = $label;
                $rows[] = $object->totalTratamientoPrestacion; // valor del paciente 
                $rows[] = $object->amount;


                $data[] = $rows;
            }
        }

    }


    $Total = $db->query($sqlTotal)->rowCount();

    $datos = [
        'data' => $data,
        'total' => $Total
    ];
    return $datos;
}


?>