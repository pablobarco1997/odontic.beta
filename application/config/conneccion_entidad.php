<?php

class CONECCION_ENTIDAD{

    private $dblocal =  null;

    public function __construct(){
        $this->dblocal = self::CONNECT_ENTITY();
    }

    public static function CONNECT_ENTITY(){

        $ServerWeb = json_decode( file_get_contents( DOL_DOCUMENT.'/application/config/connmd5pass.json') , true );

        if(md5($_SERVER['SERVER_NAME'])=='068234a2d85a5233fd17f6d0507d3454'){

            //Remoto | Produccion

            $host            = $ServerWeb['ServerProduccion']['server'];
            $database        = $ServerWeb['ServerProduccion']['database'];
            $usuario_server  = $ServerWeb['ServerProduccion']['usuario_server'];
            $password_server = $ServerWeb['ServerProduccion']['password_server'];

            $conexion = null;
            $host     = $host; #ip o nombre del servidor remoto o local
            $database = $database; //SE ENCUENTRA TODAS LAS ENTIDADES REGISTRADAS
            $username = $usuario_server; //Usuario de la Base de datos todos los privilegios
            $password = $password_server; #PASSWIRD #PASSWORD SERVIDOR REMOTO ==> Pablo_1997
            $utf8mb4  = 'utf8mb4';

        }else{

            //Localhost | Serve local


            $host            = $ServerWeb['ServerLocal']['server'];
            $database        = $ServerWeb['ServerLocal']['database'];
            $usuario_server  = $ServerWeb['ServerLocal']['usuario_server'];
            $password_server = $ServerWeb['ServerLocal']['password_server'];

            $conexion = null;
            $host     = $host; #ip o nombre del servidor remoto o local
            $database = $database; //SE ENCUENTRA TODAS LAS ENTIDADES REGISTRADAS
            $username = $usuario_server;
            $password = $password_server; #PASSWIRD #PASSWORD SERVIDOR REMOTO ==> Pablo_1997
            $utf8mb4  = 'utf8mb4';
        }

        try{

            $conexion = new PDO("mysql:host=$host;port=3306;dbname=$database;charset=$utf8mb4",$username, $password );

        }catch (PDOException $e){

            echo $e;
        }

        return $conexion;
    }

    public static function UPDATE_ENTIDAD($nombre, $direccion, $telefono, $celular, $email, $logo, $pais, $ciudad, $id,  $conf_email, $conf_password)
    {
        $error = 0;

        #CONECCIONA ENTIDADES 
        $cn = new CONECCION_ENTIDAD();

        $sql = "";

        $sql  = " UPDATE tab_entidades_dental SET";
        $sql .= " nombre    = '$nombre' ,";
        $sql .= " direccion = '$direccion' ,";
        $sql .= " telefono = '$telefono' ,";
        $sql .= " celular  = '$celular' ,";
        $sql .= " email    = '$email' ,";
        $sql .= " conf_email    = '$conf_email' ,";
        $sql .= " conf_password    = '$conf_password' ,";
        if($logo != "") {
            $sql .= " logo     = '$logo' , ";
        }
        $sql .= " pais = '$pais', ciudad = '$ciudad'";
        $sql .= " where rowid = $id ";

//        print_r($sql); die();
        $resp = $cn::CONNECT_ENTITY()->query($sql);

        if($resp) { $error = 1; }else{ $error = 0; }

        return $error;
    }

    public static function LOGIN_USUARIO_ENTITY($status, $object, $cn )
    {
        global $conf;

        $error = 0;
        $sql = "";

        if($status=="nuevo")
        {
            $USERS_ENTITY = "USUID_".$conf->EMPRESA->ENTIDAD."_".$object->id_users_clinica;

            $sql  = " INSERT INTO `tab_login_entity` ( `nombre_user`, `password_user` , `email`, `fk_entidad`, `nombre`, `apellido`, `id_usuario`, `idcedula`, `fk_perfiles`, `entity`, `login_idusers_entity`, `password_abc` ) ";
            $sql .= "VALUES (";
            $sql .= " '".$object->nameusers."',";
            $sql .= "  md5('".base64_decode($object->pass)."'),";
            $sql .= " '".$object->email."',";
            $sql .= " ".$conf->EMPRESA->ID_ENTIDAD.",";
            $sql .= " '".$object->nombreuser."',";
            $sql .= " '".$object->apelluser."', ";
            $sql .= " ".$object->id_users_clinica.", ";
            $sql .= " '".$object->usu_cedula."' , ";
            $sql .= " ".$object->fk_perfil_entity." , ";
            $sql .= " '".$conf->EMPRESA->ENTIDAD."', ";
            $sql .= " '".$USERS_ENTITY."' ,";
            $sql .= " '".$object->pass."' "; #base 64 password abc
            $sql .= ")";
            $resp = $cn->query($sql);

            if($resp){
                $error = 1;
            }else{
                $error = 0;
            }
        }

        if($status=="modificar")
        {
            $USERS_ENTITY = "USUID_".$conf->EMPRESA->ENTIDAD."_".$object->id_users_clinica;

            $sql = "UPDATE `tab_login_entity` SET `nombre_user` = '".$object->nameusers."' , `email` = '".$object->email."' ";
            $sql .= " , `password_user` = md5('".base64_decode($object->pass)."') , `password_abc` = '".$object->pass."' ";
            $sql .= " , `nombre` = '".$object->nombreuser."', `apellido` = '".$object->apelluser."' , `idcedula` = '".$object->usu_cedula."' ";
            $sql .= " , `fk_perfiles` = ".$object->fk_perfil_entity." ";
            $sql .= "    WHERE fk_entidad = '".$conf->EMPRESA->ID_ENTIDAD."' and rowid != 0 and entity = '".$conf->EMPRESA->ENTIDAD."' and fk_entidad = ".$conf->EMPRESA->ID_ENTIDAD." ";
            $sql .= " and login_idusers_entity = '".$USERS_ENTITY."'  ";

            $rs  = $cn->query($sql);
            if($rs){
                $error = 1;
            }else{
                $error = 0;
            }
        }

        return $error;
    }

    public static function INFORMACION_EMPRESA_GLOB($idEntidad, $cn)
    {
        $datos = new stdClass();

        $sql = "SELECT * FROM tab_entidades_dental WHERE rowid = $idEntidad";
        $result = $cn->query($sql);
        if($result->rowCount()>0) {
            while ($obj = $result->fetchObject()) {

                $datos  = $obj;

                $correroService = $cn->query("select correo, password_email, disabled from tab_cuentas_correos_clinica where entidad_clinica_id=".$obj->rowid." and entity=".$obj->numero_entity)->fetchObject();
                $datos->correo_service          = $correroService->correo;
                $datos->password_service        = $correroService->password_email;
                $datos->correo_service_disabled = $correroService->disabled;
            }
        }



        return $datos;
    }

    public function COMPROBAR_USUARIO_REPETIDO($usuario, $idEntidad )
    {

        global $conf;

        #SE VALIDA TODAS LAS ENTIDADES QUE NO USEN EL MISMO USUARIO
        $error = "";
        $cn = new CONECCION_ENTIDAD();
        $sql = "SELECT * FROM tab_login_entity ";
        $result = $cn::CONNECT_ENTITY()->query($sql);

        if($result->rowCount()>0) #Se Encuentra el Usuario
        {
            $exiteUsuario = 0;
            while ($ob = $result->fetchObject())
            {
                #se comprueba que no valide el mismo usuario con su entidad
                if( trim($usuario) == trim($ob->nombre_user) ){
                    $exiteUsuario++;
                }
            }

            if($exiteUsuario>0){
                $error = 'Este Usuario ya se encuentra en Uso, Ingrese un Usuario que no este en Uso';
            }
        }

        return $error;

    }

    public  function  login_status($status, $idusuario, $fk_entity)
    {
        global  $conf ;

        $error = '';
        $cn = new CONECCION_ENTIDAD();
        $sql = "UPDATE `tab_login_entity` SET `estado`='$status' WHERE `rowid`!=0 AND login_idusers_entity = '".$idusuario."' AND fk_entidad = $fk_entity and entity = '".$conf->EMPRESA->ENTIDAD."' ;";
        $rs = $cn->CONNECT_ENTITY()->query($sql);

        if(!$rs){
            $error = "Ocurrió un error al momento del Actualizar la informacion , Consulte con soporte Técnico";
        }

        return $error;
    }

    //valido el usuario que no existe
    public function validar_usuarios_clinicos($id = 0, $datos){

        global $log, $user;

        if($id!=0){ // se modifica no se valida
            return 0; // no se valida el usuario
        }
        $username = $datos->usuario;
        $sql_a = "select count(*) as valid_usu from tab_login_entity where estado = 'A' and nombre_user = '$username'  ";
        $result_a = $this->dblocal->query($sql_a);
        return $result_a->fetchObject()->valid_usu;
    }

    //actualizo el usuario De la Entidad
    public function ActualizarRegistroUser($id = 0, $fetch, $insert=false, $update=false){

        global $log, $user;
        $msgerror = "";

        if($insert){
            $sql_a  = "INSERT INTO `tab_login_entity`(`nombre_user`,`password_user`,`email`,`fk_entidad`,`nombre`,`apellido`,`id_usuario`,`estado`,`idcedula`,`fk_perfiles`,`entity`,`password_abc`,`session`,`login_idusers_entity`,`admin`)";
            $sql_a .= " VALUES (";
            $sql_a .= " :nombre_user ,";
            $sql_a .= " :password_user ,";
            $sql_a .= " :email ,";
            $sql_a .= " :fk_entidad ,";
            $sql_a .= " :nombre ,";
            $sql_a .= " :apellido ,";
            $sql_a .= " :id_usuario ,";
            $sql_a .= " :estado ,";
            $sql_a .= " :idcedula ,";
            $sql_a .= " :fk_perfiles ,";
            $sql_a .= " :entity ,";
            $sql_a .= " :password_abc ,";
            $sql_a .= " :session ,";
            $sql_a .= " :login_idusers_entity ,";
            $sql_a .= " :admin ";
            $sql_a .= " ) ";
            $stmt = $this->dblocal->prepare($sql_a);
            $result_a = $stmt->execute($fetch);
            if($result_a){
                $lasid = $this->dblocal->lastInsertId("tab_login_entity");
                $desc = "Se ha registrado en la base Principal un nuevo Usuario : ".$fetch['nombre_user']." | code64 id users: ".base64_encode($fetch['login_idusers_entity']);
                $log->log($lasid, $log->crear, $desc, "tab_login_entity");
            }else{
                $msgerror = $stmt->errorInfo()[2];
            }

        }

        if($update){

            if($fetch['login_idusers_entity']==""){
                return "Ocurrio un error de parámetros de entrada  code error: ".base64_encode("Update id users: vacio");
            }

            $sql_b  = " UPDATE `tab_login_entity` SET ";
            $sql_b .= " `nombre_user`           = :nombre_user, ";
            $sql_b .= " `password_user`         = :password_user, ";
            $sql_b .= " `email`                 = :email, ";
            $sql_b .= " `fk_entidad`            = :fk_entidad, ";
            $sql_b .= " `nombre`                = :nombre, ";
            $sql_b .= " `apellido`              = :apellido, ";
            $sql_b .= " `id_usuario`            = :id_usuario, ";
            $sql_b .= " `estado`                = :estado, ";
            $sql_b .= " `idcedula`              = :idcedula, ";
            $sql_b .= " `fk_perfiles`           = :fk_perfiles, ";
            $sql_b .= " `entity`                = :entity, ";
            $sql_b .= " `password_abc`          = :password_abc, ";
            $sql_b .= " `session`               = :session, ";
            $sql_b .= " `login_idusers_entity`  = :login_idusers_entity, ";
            $sql_b .= " `admin`                 = :admin ";
            $sql_b .= " WHERE  `rowid` > 0 and `entity` = :entity and  `fk_entidad` = :fk_entidad and `login_idusers_entity` = :login_idusers_entity ";
            $stmt_b = $this->dblocal->prepare($sql_b);
            $result_b = $stmt_b->execute($fetch);
            if($result_b){
                $desc = "Se ha Modificado el registro en la base Principal del Usuario: ".$fetch['nombre_user']." | code64 id users: ".base64_encode($fetch['login_idusers_entity']);
                $log->log($id, $log->modificar, $desc, "tab_login_entity");
            }else{
                $msgerror = $stmt_b->errorInfo()[2];
            }
        }


        if(!empty($msgerror)){
            $msgerror = "Ocurrio un error con la Operación de Usuario: ".base64_encode($msgerror);
        }

        return $msgerror;
    }

}

?>
