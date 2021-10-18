
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <title>Odontic</title>

    <?php $url = DOL_HTTP.'/application/system/login/img/odontic_ico.png';?>

    <link rel="shortcut icon" href="<?= $url ?>" type = "image/x-icon">
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <!--    bootstrap-->
    <link rel="stylesheet" href="<?php echo  DOL_HTTP.'/public/bower_components/bootstrap/dist/css/bootstrap.min.css' ?>">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="<?php echo DOL_HTTP .'/public/bower_components/font-awesome/css/font-awesome.min.css'?>">
    <!-- Ionicons -->
    <link rel="stylesheet" href="<?php echo DOL_HTTP .'/public/bower_components/Ionicons/css/ionicons.min.css'?>">

    <link rel="stylesheet" href="<?php echo DOL_HTTP .'/public/css/css_global/breadcrumb.css'?>">
    <!--    datatable-->
    <link rel="stylesheet" href="<?php echo DOL_HTTP .'/public/bower_components/datatable/datatable.code/datatables.min.css'?>">
    <!--    select2-->
    <link rel="stylesheet" href="<?= DOL_HTTP .'/public/bower_components/select2/dist/css/select2.min.css'?>">
    <!--    sweetarlert2 -->
    <link rel="stylesheet" href=" <?php echo DOL_HTTP .'/public/lib/sweetalert2/sweetalert2.css'?> ">
    <!--    input search css-->
    <link rel="stylesheet" href=" <?php echo DOL_HTTP .'/public/css/inputSearch.css'?> ">
    <!-- AdminLTE Skins. Choose a skin from the css/skins
           folder instead of downloading all of them to reduce the load. -->
    <link rel="stylesheet" href="<?php echo DOL_HTTP .'/public/css/AdminLTE.min.css'?>">
    <link rel="stylesheet" href="<?php echo DOL_HTTP .'/public/css/skins/skin-blue.min.css'?>">
    <!--Datepicker js-->
    <link rel="stylesheet" type="text/css" href="<?php echo  DOL_HTTP.'/public/bower_components/daterangepicker-master/daterangepicker.css' ?>" />
    <!-- css globales -->
    <link rel="stylesheet" href="<?php echo DOL_HTTP .'/public/css/css_global/lib_glob_style.css'?>">
    <!-- Bootstrap Color Picker -->
    <link rel="stylesheet" href="<?= DOL_HTTP .'/public/bower_components/bootstrap-colorpicker/dist/css/bootstrap-colorpicker.min.css'?>">

    <!--script import-->
    <!--socket-->
    <script src="<?php echo DOL_HTTP.'/public/js/socket_web_notify.js' ?>"></script>
    <!-- jQuery 3 -->
    <script src="<?php echo DOL_HTTP.'/public/bower_components/jquery/dist/jquery.js'?>"></script>
    <!-- Bootstrap 3.4 -->
    <script src="<?php echo DOL_HTTP .'/public/bower_components/bootstrap/dist/js/bootstrap.js'?>"></script>
    <!--popover-->
    <script src="<?php echo DOL_HTTP .'/public/bower_components/bootstrap/js/popover.js'?>"></script>
    <!-- AdminLTE App -->
    <script src="<?php echo DOL_HTTP .'/public/js/adminlte.min.js'?>"></script>
    <!--datatable-->
    <script src="<?php echo DOL_HTTP .'/public/bower_components/datatable/datatable.code/datatables.min.js'?>"></script>

    <!--select2-->
    <script src="<?= DOL_HTTP .'/public/bower_components/select2/dist/js/select2.full.min.js'?>"></script>
    <!-- sweetalert2 -->
    <script src="<?php echo DOL_HTTP .'/public/lib/sweetalert2/sweetalert2.all.js'?>" ></script>
    <!--    mask-->
    <script src="<?php echo DOL_HTTP .'/public/lib/jquery.mask.min.js'?> "></script>
    <script src="<?php echo DOL_HTTP .'/public/lib/jquery.maskMoney.js'?> "></script>
    <!--javascript global-->
    <script src="<?php echo DOL_HTTP .'/public/js/lib_glob.js' ?>"></script>
    <!--daterangepicker-->
    <script src="<?php echo  DOL_HTTP.'/public/bower_components/daterangepicker-master/moment.min.js' ?>"></script>
    <script src="<?php echo  DOL_HTTP.'/public/bower_components/daterangepicker-master/daterangepicker.js' ?>"></script>

    <!--Notificaiones lib-->
    <script src="<?php echo DOL_HTTP .'/public/js/notificaciones___lib.js' ?>"></script>
    <!--color picker-->
    <script src="<?php echo DOL_HTTP .'/public/bower_components/bootstrap-colorpicker/dist/js/bootstrap-colorpicker.min.js'?>"></script>


    <!--google Font 2-->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@500&display=swap" rel="stylesheet">

    <style>

        @font-face {
            font-family: 'AmazonEmber_Rg';
            src: local('AmazonEmber_Rg'), url('<?= DOL_HTTP ?>/public/font/AmazonEmber_Rg.ttf');
        }

        *{

            font-family: 'AmazonEmber_Rg';
            /*font-family: 'Roboto', sans-serif;*/
            /*font-family: 'Baloo Da 2', cursive;*/
            font-size: small;
        }

        /*Filtro Contenedor*/
        #ContenFiltroDocumentos > div > h3 > span {
            font-size: large;
            font-weight: bold;
        }#FiltrarAgenda > div > h3 > span {
             font-size: large;
             font-weight: bold;
         }#contentFilter > div > div > h3 > span {
              font-size: large;
              font-weight: bold;
          }#contentFilter > div  > h3 > span {
               font-size: large;
               font-weight: bold;
           }#FiltrarPagoPacientes > div  > h3 > span {
                font-size: large;
                font-weight: bold;
            }

        .swal2-title{
            font-family: 'AmazonEmber_Rg', sans-serif !important;
            font-size: small;
            /*font-family: 'Roboto', sans-serif !important;*/
            /*font-family: 'Baloo Da 2', cursive;*/
        }
        h3{
            font-family: 'AmazonEmber_Rg', sans-serif !important;
            font-size: small;
            /*font-family: 'Roboto', sans-serif !important;*/
            /*font-family: 'Baloo Da 2', cursive;*/
        }
        h5{
            font-family: 'AmazonEmber_Rg', sans-serif !important;
            font-size: small;
            /*font-family: 'Roboto', sans-serif !important;*/
            /*font-family: 'Baloo Da 2', cursive;*/
        }
        div{
            font-family: 'AmazonEmber_Rg', sans-serif !important;
            font-size: small;
            /*font-family: 'Roboto', sans-serif !important;*/
            /*font-family: 'Baloo Da 2', cursive;*/
        }


    </style>
</head>


<body class="skin-blue sidebar-mini sidebar-collapse" style="padding-right:0px !important; ">

<?php include_once DOL_DOCUMENT .'/public/view/informacion_entidad.php'; ?>

<!--MODAL GLOBALES-->
<?php include_once DOL_DOCUMENT .'/public/view/modal_glob.php'; ?>



