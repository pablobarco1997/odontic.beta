<?php

include_once '../../config/lib.global.php';

session_start();

if(!isset($_SESSION['is_open']))
{
    header('location:'.DOL_HTTP.'/application/system/login');
}

include_once DOL_DOCUMENT .'/application/config/main.php';

$view = "";
$Active = "";
if(isset($_GET['view']))
{
    $view = $_GET['view'];
    $Active = "configuraciones";

}else{
    $Active = "configuraciones";
}

?>

<style>
    .itemConf li{
        cursor: pointer;
        padding: 10px 15px;
    }

    .itemConf li:hover{
        background-color: rgba(128, 139, 150,0.2);
    }

    #confulprest li{
        float: right;
        margin-left: 3px;
    }

</style>

<!--script glob configuracion-->
<script>
    $DOCUMENTO_URL_HTTP      = "<?php echo DOL_HTTP ?>";
    $DIRECTORIO              = "<?php echo $conf->NAME_DIRECTORIO ?>"; //DIRECTORIO DE LA CARPETA ESPECIAL CREADA PARA ESTA ENTIDAD
</script>

    <!--header principal-->
<?php include_once $DOL_DOCUMENT .'/public/view/header_principal.php'; ?>

<div class="wrapper">

    <!-- Main Header -->
    <?php include_once $DOL_DOCUMENT.'/public/view/header.php'?>
         <?php include_once $DOL_DOCUMENT.'/public/view/menu.php'?>


    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <section class="content-header">
            <?= $NavSearchPacientes ?>
        </section>
        <!-- Main content -->
        <section class="content container-fluid" id="boxPrincipalConfiguracionModule">

<!--            LISTA DE CONFIGURACIONES-->
            <?php
            if($view=='principal')
            {
            ?>

                <div class="box box-solid">
                    <div class="box-header with-border">
                        <div class="form-group col-xs-12 col-sm-12 col-md-12 no-margin">
                            <i class="fa fa-2x fa-wrench" ></i>
                        </div>
                    </div>
                    <div class="box-body">
                        <br>
                        <div class="row">
                            <div class="form-group col-md-8 col-lg-9 col-sm-12 col-xs-12 col-centered">
                                <?php include_once DOL_DOCUMENT . '/application/system/configuraciones/view/view_configuration.php';?>
                            </div>
                        </div>
                    </div>
                </div>

        <?php }?>

            <!-- Script  Global  Configuration-->
            <script>
                $boxContentConfiguracion = $("#boxPrincipalConfiguracionModule");
            </script>

            <?php

            if(!empty($view) && $view != 'principal')
            {
                switch ($view)
                {
                    case 'form_prestaciones':
                        include_once DOL_DOCUMENT.'/application/system/configuraciones/view/form_configuraciones_prestaciones.php';
                        break;
                    case 'form_convenios_desc':
                        include_once DOL_DOCUMENT.'/application/system/configuraciones/view/form_convenios_desc.php';
                        break;
                    case 'form_laboratorios_conf':
                        include_once DOL_DOCUMENT.'/application/system/configuraciones/view/form_laboratorios_conf.php';
                        break;
                    case 'form_gestion_odontologos_especialidades':
                        include_once DOL_DOCUMENT.'/application/system/configuraciones/view/form_gestion_odontologos_especialidades.php';
                    break;
                    case 'document_assoct':
                        include_once DOL_DOCUMENT.'/application/system/configuraciones/view/document_assoct.php';
                    break;
                    case 'add_perfil_users':
                        include_once DOL_DOCUMENT.'/application/system/configuraciones/view/add_perfil_users.php';
                        break;

                    //new
                    case 'odontologos':
                        include_once DOL_DOCUMENT.'/application/system/configuraciones/view/add_doctor_dentist.php';
                        break;

                    case 'admin_users':
                        include_once DOL_DOCUMENT.'/application/system/configuraciones/view/add_usuarios.php';
                        break;

                    default:

                        break;
                }
            }

            ?>
        </section>
    </div>


</div>

<?php include_once DOL_DOCUMENT.'/public/view/modal_search_paciente.php'?>
<?php include_once DOL_DOCUMENT .'/public/view/footer_principal.php';?>


<script src="<?= DOL_HTTP .'/application/system/configuraciones/js/configuraciones.js'; ?>"></script>


<?php if(isset($_GET['view']) && GETPOST("view") == 'form_convenios_desc'){?>
    <script src="<?= DOL_HTTP .'/application/system/configuraciones/js/convenios_config.js'; ?>"></script>
<?php }?>
