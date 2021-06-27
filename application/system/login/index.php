
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
    <!--    sweetarlert2 -->
    <link rel="stylesheet" href=" <?php echo DOL_HTTP .'/public/lib/sweetalert2/sweetalert2.css'?> ">

    <link rel="stylesheet" href=" <?php echo DOL_HTTP .'/public/css/css_global/lib_glob_style.css'?> ">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="<?php echo DOL_HTTP .'/public/bower_components/font-awesome/css/font-awesome.min.css'?>">
    <!-- Ionicons -->
    <link rel="stylesheet" href="<?php echo DOL_HTTP .'/public/bower_components/Ionicons/css/ionicons.min.css'?>">

    <link href="https://fonts.googleapis.com/css2?family=Roboto&display=swap" rel="stylesheet">

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
        font-family: 'Roboto', sans-serif;
        font-size: small;
    }

    input {
        width: 100%;
        /*background-color: #EBEDEF;*/
        padding: 5px;
        border: none;
        /*font-weight: bolder;*/
    }
    .form-uic{
        background-color: rgba(245,249,252,0.5);
    }

    .effect-2 ~ .focus-border{
        position: absolute;
        bottom: 0;
        left: 50%;
        width: 0;
        height: 2px;
        background: rgb(2,0,36);
        background: linear-gradient(90deg, rgba(2,0,36,1) 0%, rgba(33,47,61,1) 17%, rgba(8,122,145,1) 100%);
        transition: 0.4s;
    }
    .effect-2:focus ~ .focus-border{
        width: 100%;
        transition: 0.4s;
        left: 0;
    }

    .effect-2:{
        border: 0;
        padding: 7px 0;
        border-bottom: 1px solid #ccc;
    }

    input[type="text"]{ color: #333; width: 100%; box-sizing: border-box; }
    input[type="password"]{ color: #333; width: 100%; box-sizing: border-box;}
    :focus{outline: none;}


    .col-3{position: relative;}

    body{
        background-color: #f3f4f7;
    }




    .outlogintext{
        /*border-radius: 15px;*/
        border-bottom: solid 1px #cccccc;
        padding: 10px;
    }

    body{
        background-image: url("https://www.udla.edu.ec/wp-content/uploads/2013/08/150872418.jpg");
        background-size: 100%;
        background-repeat: no-repeat;
        background-position-x: right;
        /*background-attachment: fixed;*/
        background-position: center;
        /*background-origin: content-box;*/
    }

</style>

<body >
    <div class="container">
        <div class="row ">
            <?php include_once 'view/viewlogin.php'; ?>
        </div>
    </div>
</body>
</html>
