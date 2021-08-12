
<?php

include_once '../../config/lib.global.php';

session_start();

if(isset($_SESSION['is_open']))
{
    header("location:".DOL_HTTP."/index.php?view=inicio");
}

?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <link rel="stylesheet" href="../../../public/bower_components/bootstrap/dist/css/bootstrap.css">
    <!--sweetarlert2 -->
    <link rel="stylesheet" href=" <?php echo DOL_HTTP .'/public/lib/sweetalert2/sweetalert2.css'?> ">
    <link rel="stylesheet" href=" <?php echo DOL_HTTP .'/public/css/css_global/lib_glob_style.css'?> ">
    <!--Font Awesome -->
    <link rel="stylesheet" href="<?php echo DOL_HTTP .'/public/bower_components/font-awesome/css/font-awesome.min.css'?>">
    <!--Ionicons -->
    <link rel="stylesheet" href="<?php echo DOL_HTTP .'/public/bower_components/Ionicons/css/ionicons.min.css'?>">
    <!--<link href="https://fonts.googleapis.com/css2?family=Roboto&display=swap" rel="stylesheet">-->
    <link rel="shortcut icon" href="<?=DOL_HTTP.'/application/system/login/img/odontic_ico.png';?>" type="image/x-icon" >
    <script src="../../../public/bower_components/jquery/dist/jquery.js"></script>
    <script src="../../../public/bower_components/bootstrap/dist/js/bootstrap.js"></script>
    <script src="<?php echo DOL_HTTP .'/public/lib/sweetalert2/sweetalert2.js'?>" ></script>
    <!--javascript global-->
    <script src="<?php echo DOL_HTTP .'/public/js/lib_glob.js' ?>"></script>
    <title>Odontic</title>
</head>
<style>
    *{
        font-family: Cambria;
        font-size: small;
    }
</style>
    <body >
        <?php include_once 'view/viewlogin.php'; ?>
    </body>
</html>
