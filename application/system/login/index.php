
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

    <!--    font google-->
<!--    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto|Varela+Round">-->
<!--    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">-->
    <link href="https://fonts.googleapis.com/css2?family=Baloo+Da+2&display=swap" rel="stylesheet">

    <link rel="shortcut icon" href=" <?= DOL_HTTP .'/application/system/login/img/dental_icon.png' ?>" type="image/x-icon" >

    <script src="../../../public/bower_components/jquery/dist/jquery.js"></script>
    <script src="../../../public/bower_components/bootstrap/dist/js/bootstrap.js"></script>
    <script src="<?php echo DOL_HTTP .'/public/lib/sweetalert2/sweetalert2.js'?>" ></script>

    <title>login</title>

</head>
<style>

    *{
        font-family: 'Baloo Da 2', cursive;
    }
    body{
        /*background-image: url("*/<?php //echo DOL_HTTP .'/application/system/login/img/photo_main.jpg'?>/*");*/
        background-size: 670px 669px;
        background-repeat: no-repeat;
    }

    input {
        width: 100%;
        /*background-color: #EBEDEF;*/
        padding: 5px;
        border: none;
        /*font-weight: bolder;*/
    }
    .form-uic{
        width: 100%;
        -webkit-box-shadow: 10px 10px 5px -9px rgba(0,0,0,0.75);
        -moz-box-shadow: 10px 10px 5px -9px rgba(0,0,0,0.75);
        box-shadow: 10px 10px 5px -9px rgba(0,0,0,0.75);
        border-bottom-left-radius: 30px;
        border-top-right-radius: 30px ;


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


    .col-3{margin: 40px 3%; position: relative;}

    body{
        background-color: #f3f4f7;
    }

    .btnlogin{
        border-bottom-left-radius: 30px;
        border-top-left-radius: 0px;
        border-top-right-radius: 0px;
        border-bottom-right-radius: 0px;
    }
    .btnlogin:focus{
        outline: 0;
        outline: none; !important;
    }

    .outlogintext{
        /*border-radius: 15px;*/
        border: solid 1px #cccccc;
        padding: 10px;
    }

</style>

    <body>

            <div class="container">
                <div class="row">
                    <?php include_once 'view/viewlogin.php'; ?>
                </div>
            </div>


    </body>
</html>
