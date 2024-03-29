<?php

session_start();

require_once '../../config/lib.global.php';

if(!isset($_SESSION['is_open']))
{
    header('location:'.DOL_HTTP.'/application/system/login');
}

require_once DOL_DOCUMENT. '/application/config/main.php'; //el main contiene la sesion iniciada

//$cn = new ObtenerConexiondb();
//$db = $cn::conectarEmpresa($_SESSION['db_name']);

//print_r($url_breadcrumb); die();

$titulo = "";
$view = "";
$Active = "";
if(isset($_GET['view']))
{
    $view = $_GET['view'];
    $Active = 'agenda';

}

global $db, $user;

?>


<script>

    $DOCUMENTO_URL_HTTP        = "<?= DOL_HTTP ?>";
    $keyGlobal                 = "<?= KEY_GLOB ?>"; //KEY GLOBAL
    $ID_PERFIL                 = "<?= $user->idPerfil ?>"; //Varible global para controlar los permisos

</script>


<!--header principal-->
<?php include_once DOL_DOCUMENT .'/public/view/header_principal.php';?>
<link rel="stylesheet" href="<?= DOL_HTTP .'/application/system/agenda/css/dropdown_hovereffect.css'?>">


<!--cdn.datatables.net  fixedheader-->
<!--<link rel="stylesheet" href="https://cdn.datatables.net/fixedheader/2.1.1/css/dataTables.fixedHeader.min.css">-->
<!--<script src="https://cdn.datatables.net/fixedheader/2.1.1/js/dataTables.fixedHeader.min.js"></script>-->



<div class="wrapper">
    <!-- Main Header -->
    <?php include_once DOL_DOCUMENT.'/public/view/header.php'?>
    <?php include_once DOL_DOCUMENT.'/public/view/menu.php'?>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <section class="content-header">
            <?= $NavSearchPacientes ?>
        </section>
        <!-- Main content -->
        <section class="content container-fluid">

            <div class="row">
                <div class="col-md-12 col-xs-12 col-sm-12" id="boxprincipalAgenda">
                    <div class="box box-solid">
                        <div class="box-header with-border">
                            <div class="form-group col-xs-12 col-sm-12 col-md-12 no-margin">
                                <h4 class="no-margin"><span><b>Agenda</b></span></h4>
                            </div>
                        </div>

                        <script>
                            $boxContent = $("#boxprincipalAgenda"); //elemento del contenedor principal
                        </script>

                        <div class="box-body">

                            <?php
                            accessoModule("Agenda");
                            if(!empty($view)) {
                                switch ($view) {
                                    case 'principal':

                                        if(isset($_GET['list'])){
                                            require_once DOL_DOCUMENT.'/application/system/agenda/view/principal.php';
                                        }else{
                                            echo '<h2 style="color: red; font-weight: bolder; text-align: center"> No se encontro la vista , Consulte con soporte tecnico </h2>';
                                            die();
                                        }
                                        break;
                                    case 'agendadd':
                                        require_once DOL_DOCUMENT.'/application/system/agenda/view/agendadd.php';
                                        break;
                                    default:
                                        #Este es cuando modifican la url
                                        echo '<h2 style="color: red; font-weight: bolder; text-align: center"> No se encontro la vista , Consulte con soporte tecnico </h2>';
                                        die();
                                        break;
                                }
                            }

                            ?>

                        </div>
                    </div>
                </div>
            </div>

        </section>
    </div>

</div>

<?php include_once DOL_DOCUMENT.'/public/view/modal_search_paciente.php'?>
<?php include_once DOL_DOCUMENT .'/public/view/footer_principal.php';?>
