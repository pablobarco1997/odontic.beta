<?php

include_once '../../../application/config/lib.global.php';

class db_and_procesos{

    private $hosting   = "a03be9415be4179e42b7d2aff6032911";
//    private $hosting = "";

    var $db;
    var $Connection;

    function __construct(){
        $this->Connection = $this->Connection();
    }

    function Connection(){

        if($this->hosting=='a03be9415be4179e42b7d2aff6032911'){

            #REMOTO
            $conexion = null;
            $host     = 'localhost'; #ip o nombre del servidor remoto o local
            $database = 'adminnub_sch_dental_entity_login'; //SE ENCUENTRA TODAS LAS ENTIDADES REGISTRADAS
            $username = 'adminnub_entidad_dental'; //Usuario de la Base de datos todos los privilegios
            $password = '740631f8cd06c9b56f1190b29db9ec54'; #PASSWIRD #PASSWORD SERVIDOR REMOTO ==> Pablo_1997
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

    function dbConectar($dbname){
        if($this->hosting=='a03be9415be4179e42b7d2aff6032911'){
            #REMOTO
            $conexion = null;
            $host     = 'localhost'; #ip o nombre del servidor remoto o local
            $database = $dbname; //SE ENCUENTRA TODAS LAS ENTIDADES REGISTRADAS
            $username = 'adminnub_entidad_dental'; //Usuario de la Base de datos todos los privilegios
            $password = '740631f8cd06c9b56f1190b29db9ec54'; #PASSWIRD #PASSWORD SERVIDOR REMOTO ==> Pablo_1997
            $utf8mb4  = 'utf8mb4';
        }else{
            #LOCAL
            $conexion = null;
            $host     = 'localhost'; #ip o nombre del servidor remoto o local
            $database = $dbname; //SE ENCUENTRA TODAS LAS ENTIDADES REGISTRADAS
            $username = 'root';
            $password = ''; #PASSWIRD #PASSWORD SERVIDOR REMOTO ==> Pablo_1997
            $utf8mb4  = 'utf8mb4';
        }


        $conexion = new PDO("mysql:host=$host;dbname=$database;charset=$utf8mb4",$username, $password );

        if($conexion){
            return $conexion;
        }else{
            return false;
        }
    }

    function fetchClinicas($db, $clinicadb = ''){

        if($clinicadb==""){
            return array();
        }

        $fetchClinicas=[];
        $sql = "SELECT 
                    d.rowid, d.nombre_db_entity AS db_name, d.numero_entity AS entity, 
                    d.conf_email as mail_service, 
                    d.conf_password as password_service
                 FROM
                    tab_entidades_dental d where d.rowid = $clinicadb ";

        $result = $db->query($sql);
        if($result && $result->rowCount()>0){
            $fetchClinicas = $result->fetchAll(PDO::FETCH_ASSOC);
        }

        return $fetchClinicas;
    }

    function obtener_clinica($name_db){

        $data = [];
        $db = $this->Connection();

        $name  = md5($name_db);
        $query  = "SELECT nombre_db_entity, numero_entity, nombre, ciudad, telefono, celular, logo, email FROM tab_entidades_dental where md5(nombre_db_entity) = '$name' limit 1";
        $result = $db->query($query);

        if($result){
            if($result->rowCount() > 0){
                $data = $result->fetchObject();
            }
        }

        return $data;

    }

}


?>