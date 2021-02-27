<?php

class  ObtenerConexiondb{

    public static function conectarEmpresa($name_db){

        if(md5($_SERVER['SERVER_NAME'])=='068234a2d85a5233fd17f6d0507d3454'){

            $Conexion_entidad = null;
            $host     = 'localhost';
            $database = "$name_db";
            $username = 'adminnub_entidad_dental'; #USUARIO
            $password = '740631f8cd06c9b56f1190b29db9ec54'; #PASSWORD
            $utf8mb4  = 'utf8mb4'; //mysql utf8

        }else{

            $Conexion_entidad = null;
            $host     = 'localhost';
            $database = "$name_db";
            $username = 'root'; #USUARIO
            $password = ''; #PASSWORD
            $utf8mb4  = 'utf8mb4'; //mysql utf8
        }

        try{
            $Conexion_entidad = new PDO("mysql:host=$host;dbname=$database;charset=$utf8mb4",$username, $password );
        }catch (PDOException $e){
            echo $e;
        }

        return $Conexion_entidad;
    }
}

?>