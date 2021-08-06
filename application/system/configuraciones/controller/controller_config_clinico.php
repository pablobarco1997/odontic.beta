
<?php


if(isset($_GET['ajaxSend']) || isset($_POST['ajaxSend'])){

    session_start();
    require_once '../../../config/lib.global.php';
    require_once DOL_DOCUMENT. '/application/config/main.php'; //el main contiene la sesion iniciada
    require_once DOL_DOCUMENT.'/application/config/conneccion_entidad.php'; //Coneccion entidad


    //obtengo las clases de las entidades y conecciones
    $entidades = new CONECCION_ENTIDAD();

    global   $db, $conf, $user, $global, $log;


    $accion = GETPOST('accion');

    switch ($accion){

        case 'list_doctores':

            $resultado = doctor_list();

            $output = array(
                "data"            => $resultado['datos'],
                "recordsTotal"    => $resultado['total'],
                "recordsFiltered" => $resultado['total']
            );

            echo json_encode($output);
            break;

        case 'crear_odontologo':

            $error = "";
            $id = GETPOST('id');
            if(GETPOST('datos')!=""){
                $fetch = (json_decode(GETPOST('datos')));
            }

            if($id){
                $result = doctor_update($id, $fetch);
                if($result==-1){
                    $error = "Ocurrio un error con la Operación Modificar. Consulte con Soporte";
                }
            }else{
                $result = doctor_crear($fetch);
                if($result==-1){
                    $error = "Ocurrio un error con la Operación Crear. Consulte con Soporte";
                }
            }
            $output=array(
              "error" => $error
            );
            echo json_encode($output);
            break;

        case 'fetchDatosDoctor':

            $error = "";
            $doctor = array();
            $id = GETPOST("id");

            $sql = "select nombre_doc, apellido_doc, cedula, telefono_convencional, email, ciudad, celular, direccion, fk_especialidad, estado from tab_odontologos where rowid = ".$id;
            $result = $db->query($sql);
            if($result && $result->rowCount()>0){
                $result = $result->fetchAll(PDO::FETCH_ASSOC);
                $item   = $result[0];
                if($item['estado']=='A'){
                    $doctor = array($item['nombre_doc'], $item['apellido_doc'], $item['telefono_convencional'], $item['direccion'], $item['celular'], $item['email'],  $item['ciudad'], $item['cedula'], $item['fk_especialidad']);
                }else{
                    if($item['estado']=='E'){
                        $error = "No puede modificar este Doctor(a) ".$item['nombre_doc']." ".$item['apellido_doc']. ". Se encuentra en estado Eliminado";
                    }
                }
            }else{
                $error = "Ocurrio un error con la operación. NO se detecto parametros de entrada";
            }

            $output=array(
                "error"   => $error,
                "doctor"  => $doctor
            );
            echo json_encode($output);
            break;

        case 'createUsers':

            $error = "";
            $id    = GETPOST("id");
            $datos = GETPOST("datos");
            if(GETPOST("datos")!=""){
                $datos = json_decode($datos); //string a JSON

                if(is_object($datos)){
                    if($datos->usuario!=""){
                        //valido e inserto el usuario para luego registrarlo en
                        //en la tabla local de la clinica
                        $valid = $entidades->validar_usuarios_clinicos($id, $datos);
                        if($valid==0){
                            //function
                            $success =  registrarUsers($id, $datos);
                            //si es 0
                            if(!$success['success']){

                                $error = $success['error'];
                                if($error!=""){
                                    if($success['idLast']!=0){
                                        $db->query("DELETE FROM `tab_login_users` WHERE `rowid`='".$success['idLast']."';");
                                    }
                                }
                            }else{

                                $getUsers = getnombreUsuario($success['idLast']);
                                if($datos->doctor!=0){
                                    $getDoctor = getnombreDentiste($datos->doctor);
                                    $nomdoct   = $getDoctor->nombre_doc;
                                    $apellido  = $getDoctor->apellido_doc;
                                    $email     = $getDoctor->email;
                                    $idcedula  = $getDoctor->cedula;
                                }else{
                                    $nomdoct   = "";
                                    $apellido  = "";
                                    $email     = "";
                                    $idcedula  = "";
                                }

                                $fetch = array(
                                    "nombre_user"               => $getUsers->usuario,
                                    "password_user"             => md5(base64_decode($getUsers->passwor_abc)) ,
                                    "email"                     => $email ,
                                    "fk_entidad"                => $conf->EMPRESA->ID_ENTIDAD,
                                    "nombre"                    => $nomdoct ,
                                    "apellido"                  => $apellido ,
                                    "id_usuario"                => $getUsers->rowid , //id usuario base local clinica
                                    "estado"                    => "A",
                                    "idcedula"                  => $idcedula ,
                                    "fk_perfiles"               => 0,
                                    "entity"                    => $conf->EMPRESA->ENTIDAD,
                                    "password_abc"              => $getUsers->passwor_abc,  //base 64
                                    "session"                   => 0,
                                    "login_idusers_entity"      => $getUsers->login_idusers_entity,
                                    "admin"                     => $datos->admin
                                );

                                if($id){
                                    //modificar
                                    $error =  $entidades->ActualizarRegistroUser($id, $fetch, false, true);
                                }else{
                                    //nuevo
                                    $error =  $entidades->ActualizarRegistroUser(0, $fetch, true, false);
                                }

//                                print_r($error); die();
                            }
//                            print_r($success); die();
//                          $entidades->ActualizarRegistroUser($datos);

                        }else{
                            $error = "Usuario en Uso";
                        }
                    }else{
                        $error = "Ocurrio un error de parametros de entrada, Consulte con Soporte";
                    }
                }
            }else{
                $error = "Ocurrio un error de parametros de entrada, Consulte con Soporte";
            }


            $output=array(
                "error"   => $error,
            );
            echo json_encode($output);
            break;


        case 'fetchDatosUsurs':

            $error="";
            $fetch=[];
            $id = GETPOST('id');

            $sql = "SELECT 
                    rowid id_user, 
                    usuario , 
                    passwor_abc, 
                    passwords , 
                    fk_doc, 
                    estado, 
                    fk_perfil_entity as fk_perfil, 
                    login_idusers_entity
                FROM
                    tab_login_users 
                    where rowid = $id";
            $result = $db->query($sql);
            if($result){
                if($result->rowCount()==1){
                    $fetch  = $result->fetchAll(PDO::FETCH_ASSOC);
                    $fetch  = $fetch[0];
                    $fetch['admin'] = ((validSuperAdmin($fetch['login_idusers_entity']))?1:0);
                }else{
                    $error = "Ocurrio un error con la Operación. Consulte con soporte";
                }
            }

            $output=array(
                "error"   => $error,
                "users"   => $fetch
            );
            echo json_encode($output);
            break;

        case 'fetchModulosPermisos':

            $fetchpermisos = fetchModulePermissions();

            $output=array(
                "datos" => $fetchpermisos
            );
            echo json_encode($output);
            break;


        case 'nuevoPerfilpermits':

            $error = "";
            $id               = GETPOST('id');
            $permModule       = GETPOST("fetchpermitsModule");
            $permChild        = GETPOST("fetchpermitsChild");
            $datos['name']    = GETPOST("name");
            $datos['desc']    = GETPOST("desc");
            $datos['permits']       = ($permChild!="")?json_decode($permChild):array();
            $datos['permitsModule'] = ($permModule!="")?json_decode($permModule):array();

//            print_r($datos); die();

            if($id=='' || $id == '0'){
                //nuevo
                $response = Perfiles(null, true, false, $datos);
                if($response==-1){
                    $error = "Ocurrio error con los parametros de entrada. Consulte con Soporte";
                }
            }else{
                //actualizar
                $response = Perfiles($id, false, true, $datos);
            }

            $output = [
                'error' => $error ,
            ];

            echo json_encode($output);
            break;

        case 'fetchModperfiles':

            $datos = [];
            $id = GETPOST('id');

            $result = $db->query("select nom from tab_login_perfil_name where rowid = '$id' ")->fetchAll(PDO::FETCH_ASSOC);
            if(count($result)>0){
                $datos['name'] = $result[0]['nom'];
            }

            $datos['permits'] = array();
            $result = $db->query("select concat(id_module,'_',id_permissions) as key_id from tab_login_users_permissions where fk_perfiles = '$id' ")->fetchAll(PDO::FETCH_ASSOC);
            if(count($result)>0){
                $datos['permits'] = $result;
            }

            $datos['permitsModule'] = array();
            $result = $db->query("select id_modulo from tab_login_users_permissions_modulos where fk_perfil = '$id' ")->fetchAll(PDO::FETCH_ASSOC);
            if(count($result)>0){
                $datos['permitsModule'] = $result;
            }

            $output = [
               'error' => '',
               'success'  => $datos
            ];

            echo json_encode($output);
            break;


        case 'list_perfiles_permisos':

            $resultado = perfiles_lis();

            $output = array(
                "data"            => $resultado['datos'],
                "recordsTotal"    => $resultado['total'],
                "recordsFiltered" => $resultado['total']
            );

            echo json_encode($output);
            break;

    }


}


function doctor_crear($fetch){

    global $db, $log;

    $nom = $fetch->nombre." ".$fetch->apellido;

    if($fetch->especialidad==""){
        $fetch->especialidad = 0;
    }

    $sql  = "INSERT INTO `tab_odontologos` (`nombre_doc`, `apellido_doc`, `celular`, `telefono_convencional`, `email`, `ciudad`, `direccion`, `fk_especialidad` , `cedula` ) ";
    $sql .= "VALUES (";
    $sql .= "'".$fetch->nombre."',";
    $sql .= "'".$fetch->apellido."',";
    $sql .= "'".$fetch->celular."',";
    $sql .= "'".$fetch->telefono."',";
    $sql .= "'".$fetch->email."',";
    $sql .= "'".$fetch->ciudad."',";
    $sql .= "'".$fetch->direccion."',";
    $sql .= " ".$fetch->especialidad." ,";
    $sql .= "'".$fetch->cedula_ruc."'";
    $sql .= ")";
    $result = $db->query($sql);
    if($result){
        $idlast = $db->lastInsertId("tab_odontologos");
        $log->log($idlast,$log->crear, "Se ha registrado un nuevo registro Doctor(a) $nom ", "tab_odontologos");

    }else{
        $log->log(0,$log->error, "Ha ocurrido un error con la creacción Doctor(a): $nom ", "tab_odontologos", $sql);
        return -1;
    }
    return "";
}

function doctor_update($id, $fetch){
    global $db, $log;
    if(!$id){
        return -1;
    }

    $entidades = new CONECCION_ENTIDAD();


    $nom = $fetch->nombre.' '.$fetch->apellido;
    $sql_a     = "  UPDATE `tab_odontologos` SET `nombre_doc` = '$fetch->nombre', `apellido_doc` = '$fetch->apellido', `celular` = '". $fetch->celular ."', ";
    $sql_a    .= "   `telefono_convencional` = '$fetch->telefono', `email` = '$fetch->email' ";
    $sql_a    .= " , `ciudad` = '$fetch->ciudad' ";
    $sql_a    .= " , `direccion` = '$fetch->direccion'";
    $sql_a    .= " , `fk_especialidad` = $fetch->especialidad ";
    $sql_a    .= " , `cedula` = '$fetch->cedula_ruc'";
    $sql_a    .= " WHERE (`rowid` = '$id') ";
    $result_a  = $db->query($sql_a);
    if($result_a){
        $log->log($id,$log->modificar, "Se ha Actualizado el registro. Doctor(a) $nom ", 'tab_odontologos');
//        $entidades->ActualizarRegistroUser($id, $fetch);
    }else{
        $log->log($id,$log->error, "Ha ocurrido un error con la Operacion Modificar el registro. Doctor(a): $nom ", 'tab_odontologos', $sql_a);
    }

    return "";
}

function doctor_list($estado = ""){

    global $db, $conf, $user, $log;


    $PermisoConsultar = (!PermitsModule(12,1))?" and 1<>1 ":"";
    $Total          = 0;
    $start          = $_POST["start"];
    $length         = $_POST["length"];


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
            IFNULL((SELECT  e.nombre_especialidad FROM tab_especialidades_doc e WHERE e.rowid = s.fk_especialidad), 0) especialidad, 
            s.tms
        FROM
            tab_odontologos s
        WHERE
            s.rowid > 0 ";

    if(!empty($estado)){
        $sql .= " and s.estado = '$estado' ";
    }

    $sql .= $PermisoConsultar;

    $sql .= " and (select count(*) from tab_login_users g where g.rowid = ".$user->id." and  g.fk_doc = s.rowid) = 0 " ;
    $sql .= " order by s.rowid desc ";

    //total de registros
    $Total = $db->query($sql)->rowCount();

    if($start || $length)
        $sql.=" LIMIT $start,$length;";


    $res = $db->query($sql);
    if($res->rowCount() > 0){

        while ($obj = $res->fetchObject()){

            $nomUsuario = "";
            $row = array();
            //obtendre el id del Usuario Creado
            $sql_ab = "SELECT rowid, usuario FROM tab_login_users s WHERE s.fk_doc = $obj->rowid";
            $result_ab   = $db->query($sql_ab);
            if($result_ab->rowCount()>0){
                $array_ab     = $result_ab->fetchAll(PDO::FETCH_ASSOC);
                foreach ($array_ab as $value){
                    $nomUsuario .= "<a  style='color: #488cd5;'> <small  title='usuario asociado' style='display: block'> <i class=\"fa fa-user\"></i> ".$value['usuario']." </small> </a>  ";
                }
            }else{
                $nomUsuario = "";
            }

            //Link de Usuarios
            $LinkUsuario    = (!empty($nomUsuario))?'<small style="display: block;">'. $nomUsuario .'</small>':'';

            if($obj->estado =='A')
                $estado_odont = '<small  style=" display: block; color: green"> Activo </small>';
            if($obj->estado =='E')
                $estado_odont = '<small  style=" display: block; color: red"> Desactivado </small>';

            $row[] = $obj->nombre_doc.' '.$obj->apellido_doc.' <a href="'.DOL_HTTP.'/application/system/configuraciones/?view=odontologos&v=add&id='.$obj->rowid.'" data-id="'.$obj->rowid.'"><span style="display: inline-block" class="fa fa-edit"></span></a>' . $LinkUsuario . $estado_odont;
            $row[] = $obj->cedula; #Cedula del odontolog@
            $row[] = $obj->direccion;
            $row[] = $obj->email;
            $row[] = ( $obj->especialidad == '0') ? 'General' :  $obj->especialidad;

            if($obj->estado == 'A'){
                $row[] = '<a class="btn btn-xs" style="background-color: #fadbd8; color: red; font-weight: bolder; " onclick="DoctorEstados('.$obj->rowid.', \'E\')"> <i class="fa fa-mouse-pointer"></i> Desactivar</a>';
            }
            if($obj->estado == 'E'){
                $row[] = '<a class="btn btn-xs" style="background-color: #D5F5E3; color: green; font-weight: bolder" onclick="DoctorEstados('.$obj->rowid.', \'A\')"> <i class="fa fa-mouse-pointer"></i> Activar</a>';
            }

            $row[] = $obj->rowid;
            $row[] = $obj->estado;

            $data[] = $row;
        }
    }

    return [
        'datos' => $data,
        'total' => $Total
    ];
}

function registrarUsers($id = 0, $datos){
    global $db, $conf, $log;

    //value retornar
    $success = ['success'=>0,'error'=>'','idLast'=>0,'id_Unique'=>0];

    if($datos->doctor!=0){
        $ci = getnombreDentiste($datos->doctor)->cedula;
    }else{
        $ci = "";
    }

    $usuario    = $datos->usuario;
    $pass       = base64_decode($datos->pass);
    $pass64     = $datos->pass;
    $doctor     = $datos->doctor;
    $ci_a       = $ci;
    $entity     = $conf->EMPRESA->ENTIDAD;

//    print_r($doctor); die();
//    print_r(array($usuario, $pass, $doctor, $pass64, md5($pass), $ci_a, $entity, $id)); die();
    if($id!=0){
        //modificar login

        $sql_ab    = "UPDATE `tab_login_users` SET `usuario` = ?,`passwords` = ?,`fk_doc` = ?,`passwor_abc` = ? , passwords = ? ,`cedula` = ?,`entity` = ? , `fk_perfil_entity` = ? WHERE `rowid` = ?;";
        $stmt_ab   = $db->prepare($sql_ab);
        $result_ab = $stmt_ab->execute(array($usuario, $pass, $doctor, (string)$pass64, (string)md5($pass), (string)$ci_a, (string)$entity, $datos->perfil, $id));

        if($result_ab){

            $success['idLast'] = $id;
            $success['success'] = 1;
            $log->log($id, $log->modificar, "Se ha Modificado el registro Usuario: ".$usuario." ", "tab_login_users");
        }else{

            $errinfo            = $stmt_ab->errorInfo();
            $success['success'] = 0;
            $success['error']   = "Ocurrio un error de Actualización Modificar registro ".$usuario." Code: ".$errinfo[0]." ".$errinfo[2] ."<br> Consulte con soporte Tecnico";
            $log->log($id, $log->error, $success['error'], "tab_login_users", $errinfo[2]);
        }

        return $success;
    }else{
        //nuevo

        $sql_a = "INSERT INTO `tab_login_users` (`usuario`, `passwords` ,`fk_doc`, `tipo_usuario`, `passwor_abc`, `cedula`, `fk_perfil_entity`, `entity`) VALUES(?,?,?,?,?,?,?,?)";
        $stmt = $db->prepare($sql_a);
        $result =  $stmt->execute(array($usuario, md5($pass), $doctor, 0, $pass64, $ci_a, $datos->perfil, $entity));
        if($result){

            $idLast = $db->lastInsertId("tab_login_users");
            $log->log($idLast, $log->crear, "Se ha Creado nuevo registro Usuario: ".$usuario." ", "tab_login_users");

            $id_Unique_users = "USUID_".$entity."_".$idLast;

            $sql_b = "UPDATE `tab_login_users` SET `login_idusers_entity`='".$id_Unique_users."' WHERE `rowid`='$idLast';";
            $stmt_b = $db->prepare($sql_b);
            $result_b = $stmt_b->execute();
            if($result_b){
                $success['success']    = 1;
                $success['idLast']     = $idLast;
                $success['id_Unique']  = $id_Unique_users;
            }else{
                $errinfo = $stmt_b->errorInfo();
                $success['success'] = 0;
                $success['error']   = "Ocurrio un error de Actualización Code: ".$errinfo[0]." ".$errinfo[2] ."<br> Consulte con soporte Tecnico";
            }
        }else{
            $success['success'] = 0;
            $log->log(0, $log->error, "Ocurrio un error con la creacion de un registro Usuario: ".$usuario, "tab_login_users", $stmt->errorInfo()[2]);
            $success['error']   = "Ocurrio un error con la Operación Crear Usuario Code: ".$stmt->errorInfo()[0]."  " .base64_encode($stmt->errorInfo()[2])."<br> Consulte con soporte Tecnico";
        }

        return $success;
    }

    return $success;


}

function fetchModulePermissions(){

    global $db, $user, $conf;

    //padre
    $sql_a = "select rowid, name, estado, id_padre  from tab_modulos_clinicos where estado = 'A' and id_padre = 0";
    $result_a = $db->query($sql_a);
    if($result_a){
        $fetch_father = $result_a->fetchAll(PDO::FETCH_ASSOC);

        $children = [];
        //hijos
        $sql_b  = "select rowid, name, estado, id_padre  from tab_modulos_clinicos where estado = 'A' and id_padre <> 0;";
        $result_b = $db->query($sql_b);
        if($result_b){
            $fetch_children = $result_b->fetchAll(PDO::FETCH_ASSOC);
            foreach ($fetch_children as $item){
                $children[$item['id_padre']][] = $item;
            }
        }

        $datos = [];
        foreach ($fetch_father as $value){
            if(array_key_exists($value['rowid'], $children)){
                $datos[] = array($value['rowid'], $value['name'], 'father', $children[$value['rowid']]);
            }else{
                $datos[] = array($value['rowid'], $value['name'], 'son');
            }
        }

        return $datos;

    }

}

function Perfiles($id, $new=false, $update=false, $datos=array()){

    global  $db, $user, $conf, $log;

    if(count($datos)==0){
        return -1;
    }

    $name = $datos['name'];
    $desc = $datos['desc'];

    if($new==true){
        $sql_a = "INSERT INTO `tab_login_perfil_name` (`nom`, `desc`) VALUES ('$name', '$desc');";
    }
    if($update==true){
        $sql_a = "UPDATE  tab_login_perfil_name p SET p.nom = '$name' , p.desc = '$desc' WHERE p.rowid = $id; ";
    }

//    print_r($sql_a); die();
    //si no existe esa variable -6 error code 555 consulte con soporte
    if(!isset($sql_a)){
        return -6;
    }

    $result_a = $db->query($sql_a);
    if($result_a){

        $idlast = $db->lastInsertId("tab_login_perfil_name");

        if($new == true){
            $log->log($idlast, $log->crear, "Se ha creado un nuevo Perfil de usuario: ".$datos['name'], 'tab_login_perfil_name');
        }

        if($update == true){
            $log->log($id, $log->modificar, "Se ha Actualizado el Perfil : ".$datos['name'], 'tab_login_perfil_name');
            $idlast = $id;
        }

        //si no hay datos se retorna vacio
        if(count($datos['permits'])==0){
            return "";
        }

        /*Permisos Perfiles*/
        //elimino los registros para agregarlos de nuevo
        $db->query("DELETE FROM tab_login_users_permissions WHERE rowid > 0 and fk_perfiles = $id");
        $db->query("DELETE FROM tab_login_users_permissions_modulos WHERE rowid > 0 and fk_perfil = $id");

        $parametrs = array();
        foreach ($datos['permits'] as $value){
            $fetch = explode('_',$value);
            $value_mod     = $fetch[0]; //modulo
            $value_permits = $fetch[1]; //permisos
            $parametrs[] = array($value_mod, $value_permits, $idlast);
        }
        $sql_b  = "INSERT INTO `tab_login_users_permissions` (`id_module`, `id_permissions`, `fk_perfiles`) VALUES (?, ?, ?);";
        $stmt_b = $db->prepare($sql_b);
        foreach ($parametrs as $item){
            $stmt_b->execute($item);
        }

        /*Permisos Modulos por Perfil*/
        $parametrsMod = array();
        foreach ($datos['permitsModule'] as $itemMod){
            $parametrsMod[] = array($idlast, $itemMod);
        }
        $sql_c = "INSERT INTO `tab_login_users_permissions_modulos` (`fk_perfil`, `id_modulo`) VALUES (?, ?);";
        $stmt_c = $db->prepare($sql_c);
        foreach ($parametrsMod as $item){
            $stmt_c->execute($item);
        }

        return "";
    }else{
        $log->log(0, $log->error, "Ha ocurrido un error con la creación del perfil : ".$datos['name'], 'tab_login_perfil_name');
    }

}

function perfiles_lis(){

    global  $db;

    $Total          = 0;
    $start          = $_POST["start"];
    $length         = $_POST["length"];


    $data = [];
    $sql = "select 
            p.rowid , p.nom , p.desc, p.estado
            from tab_login_perfil_name p";

    $Total = $db->query($sql)->rowCount();

    if($start || $length){
        $sql.=" LIMIT $start,$length;";
    }

    $result = $db->query($sql);
    if($result){
        if($result->rowCount()>0){
            while ($object = $result->fetchObject()){
                $rows = [];

                $link_perfil = "<a  href='".DOL_HTTP."/application/system/configuraciones/?view=admin_users&v=perfiles&id=".$object->rowid."'  class='text-black'  >".$object->nom."</a>";

                $rows[] = "";
                $rows[] = $link_perfil.'<small class="text-sm" style="display: block; color: #488cd5 ">'.$object->desc.'</small>';

                if($object->estado == "A")
                    $rows[] = "<label class=\"label\" style=\"background-color: #D5F5E3; color: green; font-weight: bolder\">ACTIVO</label>";
                if($object->estado == "E")
                    $rows[] = "<label class=\"label \" style=\"background-color: #FADBD8; color: red; font-weight: bolder\">INACTIVO</label>";

                $rows['id'] = $object->rowid;
                $data[] = $rows;
            }
        }
    }

    $resultFinal = [
        'datos' => $data,
        'total' => $Total
    ];

    return $resultFinal;
}

?>