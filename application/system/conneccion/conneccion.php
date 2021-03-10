<?php

class  ObtenerConexiondb{

    public static function conectarEmpresa($name_db){

        if(md5($_SERVER['SERVER_NAME'])=='068234a2d85a5233fd17f6d0507d3454'){

            $Server = json_decode( file_get_contents( DOL_DOCUMENT.'/application/config/privileges.json') , true );

            $Conexion_entidad = null;
            $host     = 'localhost';
            $database = "$name_db";
            $username = $Server->usuario_server; #USUARIO
            $password = $Server->password_server; #PASSWORD
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