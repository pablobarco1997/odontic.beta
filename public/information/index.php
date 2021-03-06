
<?php

require_once '../../application/config/lib.global.php';
require_once DOL_DOCUMENT .'/application/controllers/controller.php';
require_once 'conneccion/connection_info.php';

?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>

    <script src="<?php echo DOL_HTTP.'/public/bower_components/jquery/dist/jquery.js'?>"></script>

    <link rel="stylesheet" href="<?php echo  DOL_HTTP.'/public/bower_components/bootstrap/dist/css/bootstrap.css' ?>">
    <script src="<?php echo DOL_HTTP .'/public/bower_components/bootstrap/dist/js/bootstrap.js'?>"></script>

    <link rel="stylesheet" href="<?php echo  DOL_HTTP.'/public/css/css_global/lib_glob_style.css' ?>">

    <link rel="stylesheet" href="<?php echo  DOL_HTTP.'/public/information/css/noti.css' ?>">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="<?php echo DOL_HTTP .'/public/bower_components/font-awesome/css/font-awesome.min.css'?>">

    <!--    font google -->
    <link href="https://fonts.googleapis.com/css?family=Hind&display=swap" rel="stylesheet">

    <!--    btn bts perzonalizados-->
    <link rel="stylesheet" href="<?php echo  DOL_HTTP.'/public/information/css/btn_bts/buttons.css' ?>" >
    <link rel="stylesheet" href="<?php echo  DOL_HTTP.'/public/information/css/btn_bts/style.css' ?>" >

    <!--    alert-->
    <link rel="stylesheet" href=" <?php echo DOL_HTTP .'/public/lib/sweetalert2/sweetalert2.css'?> ">
    <script src="<?php echo DOL_HTTP .'/public/lib/sweetalert2/sweetalert2.all.js'?>" ></script>

    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Baloo+Da+2&display=swap" rel="stylesheet">

</head>
<style>
    *{
        font-family: 'Baloo Da 2', cursive;
    }

</style>
<body>


        <div class="container">

            <?php

                $view = GETPOST('v');

                switch ($view){

                    case 'confirm_cita':
                        include_once DOL_DOCUMENT .'/public/information/view/confirmar_cita.php';
                        break;
                }


            ?>

        </div>

</body>
</html>