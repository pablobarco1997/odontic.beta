<?php

class CONECCION_ENTIDAD{

    public static function CONNECT_ENTITY(){

        if(md5($_SERVER['SERVER_NAME'])=='068234a2d85a5233fd17f6d0507d3454'){

            #REMOTO
            $conexion = null;
            $host     = 'localhost'; #ip o nombre del servidor remoto o local
            $database = 'adminnub_sch_dental_entity_login'; //SE ENCUENTRA TODAS LAS ENTIDADES REGISTRADAS
            $username = 'adminnub_entidad_dental'; //Usuario de la Base de datos todos los privilegios
            $password = 'Pablo_1997'; #PASSWIRD #PASSWORD SERVIDOR REMOTO ==> Pablo_1997
            $utf8mb4  = 'utf8mb4';

        }else{

            #LOCAL
            $conexion = null;
            $host     = 'localhost'; #ip o nombre del servidor remoto o local
            $database = 'schema_dental_entity_login'; //SE ENCUENTRA TODAS LAS ENTIDADES REGISTRADAS
            $username = 'root';
            $password = ''; #PASSWIRD #PASSWORD SERVIDOR REMOTO ==> Pablo_1997
            $utf8mb4  = 'utf8mb4';
        }

        try{

            $conexion = new PDO("mysql:host=$host;dbname=$database;charset=$utf8mb4",$username, $password );

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

        if($logo != "")
        {
            $sql .= " logo     = '$logo' , ";
        }

        $sql .= " pais = '$pais', ciudad = '$ciudad'";
        $sql .= " where rowid = $id ";

//        print_r($sql); die();
        $resp = $cn::CONNECT_ENTITY()->query($sql);

        if($resp) { $error = 1; }else{ $error = 0; }

        return $error;
    }

    public static function LOGIN_USUARIO_ENTITY($status, $object )
    {
        global $conf;

        $error = 0;

        $cn = new CONECCION_ENTIDAD();

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
            $resp = $cn::CONNECT_ENTITY()->query($sql);

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

            $rs  = $cn::CONNECT_ENTITY()->query($sql);
            if($rs){
                $error = 1;
            }else{
                $error = 0;
            }
        }

        return $error;
    }

    public static function INFORMACION_EMPRESA_GLOB($idEntidad)
    {
        $datos = array();
        $cn = new CONECCION_ENTIDAD();

        $sql = "SELECT * FROM tab_entidades_dental WHERE rowid = $idEntidad";
        $result = $cn::CONNECT_ENTITY()->query($sql);

        if($result->rowCount()>0)
        {
            while ($Obj = $result->fetchObject())
            {
                $datos = $Obj;
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

}

?>
