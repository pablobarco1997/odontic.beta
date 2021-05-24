
<?php

    //coneecion a la base principal de la clinica
    function connection($db_name)
    {

        //remoto
        if(md5($_SERVER['SERVER_NAME'])=='068234a2d85a5233fd17f6d0507d3454'){

            $host     = "localhost";
            $username = "adminnub_entidad_dental";
            $password = "740631f8cd06c9b56f1190b29db9ec54";
            $database = $db_name;
        }else{

            //local
            $host     = "localhost";
            $username = "root";
            $password = "";
            $database = $db_name;
        }

        $cn = mysqli_connect($host, $username, $password, $database);

        if($cn)
        {
            return $cn;
        }else{

            echo '<h1 style="color: red">Error no se pudo conectar a la red - consulte con soporte tecnico</h1>';
        }



    }


?>