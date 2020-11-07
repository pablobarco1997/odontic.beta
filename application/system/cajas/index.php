<?php

require_once  '../../config/lib.global.php';
session_start();

if(!isset($_SESSION['is_open']))
{
    header("location:".DOL_HTTP."/application/system/login");
}

require_once DOL_DOCUMENT.'/application/config/main.php';

$NAME_MODULO = "Cajas Clinicas";
global $conf, $db;

$view = "";
if(isset($_GET['view'])){
    $view = $_GET['view'];
}
$Active="module_cajas";


?>

<script>
    $DOCUMENTO_URL_HTTP        = "<?=  DOL_HTTP ?>"; //URL  HTTP DOCUMENTO
    $HTTP_DIRECTORIO_ENTITY    = "<?=  $conf->NAME_DIRECTORIO ?>";  //ENTIDAD DE LA EMPRESA PARA JAVASCRIPT
    $keyGlobal                 = "<?=  KEY_GLOB ?>"; //KEY GLOBAL
</script>


<!--header principal-->
<?php include_once DOL_DOCUMENT .'/public/view/header_principal.php';?>

    <div class="wrapper">
        <!-- Main Header -->
        <?php include_once DOL_DOCUMENT.'/public/view/header.php'?>
        <?php include_once DOL_DOCUMENT.'/public/view/menu.php'?>

        <div class="content-wrapper">
            <section class="content-header">
                <?= $NavSearchPacientes ?>
            </section>

            <section  class="content container-fluid" id="boxContentCajasModule">
                <div class="box box-solid">
                    <div class="box-header with-border">
                        <h4 class="no-margin"><span><b><?= $NAME_MODULO ?></b></span></h4>
                    </div>

                    <script>
                        $boxContentCajasModule = $("#boxContentCajasModule");
                    </script>

                    <div class="box-body">
                        <div class="form-group form-group col-xs-12 col-md-12">
                            <?php
                            if(isset($view)) {
                                if($view != ""){
                                    include_once DOL_DOCUMENT.'/application/system/cajas/view/'.$view.'.php';;
                                }
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>


<?php include_once DOL_DOCUMENT .'/public/view/footer_principal.php';?>