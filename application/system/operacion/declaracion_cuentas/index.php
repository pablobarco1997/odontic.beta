
<?php

session_start();

require_once '../../../config/lib.global.php';

if(!isset($_SESSION['is_open']))
{
    header('location:'.DOL_HTTP.'/application/system/login');
}

require_once DOL_DOCUMENT. '/application/config/main.php'; //el main contiene la sesion iniciada

$KeyPermisos = null;
$view = "";
$Active = "";
if(isset($_GET['view']))
{
    $view = $_GET['view'];
    $Active = 'Declaracion_Cuentas';
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
        <section class="content container-fluid" id="boxContentCuentasDeclaracion">
            <div class="box box-solid">
                <div class="box-header with-border">
                    <h4 class="no-margin"><span><b><?= 'Declaración de Cuentas' ?></b></span></h4>
                </div>

                <script>
                    $boxContentCuentasDeclaracion = $("#boxContentCuentasDeclaracion");
                </script>

                <div class="box-body">
                    <div class="form-group form-group col-xs-12 col-md-12">
                        <?php

                            accessoModule('Declarar Cuentas');

                            if(isset($view)) {
                                if($view != ""){
                                    include_once DOL_DOCUMENT.'/application/system/operacion/declaracion_cuentas/view/'.$view.'.php';
                                }
                            }
                        ?>
                    </div>
                </div>
            </div>
        </section>
    </div>

</div>