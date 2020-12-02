<?php

require_once '../../../config/lib.global.php';
session_start();
if(!isset($_SESSION['is_open']))
{
    header("location:".DOL_HTTP."/application/system/login");
}

require_once '../../../../application/config/main.php';


#declaro las variables globales
$VIEW_GLOB_ADMIN_PACIENTES =  ""; #VISTA DEL VIEW
$DIRECTORIO_ADMIN          =  ""; #BUSCAR DIRECTORIO
$_JS_DOCMENT               =  ""; #BUSCAR JAVASCRIPT
$NAME_MODULO               =  "";

$idPaciente = 0;  #ID PACIENTES ----------------------------------------------------------------------------------------

if(isset($_GET['id']))
{

    PERMISO_ACCESO_ADMIN_PACIENTES(GETPOST('key'));  #permisos

    $idPaciente  = decomposeSecurityTokenId($_GET['id']); #id del paciente

    #VISTAS FORMULARIOS ------------------------------------------------------------------------------------------------
    include_once 'view/vistas_mod.php';


}else{

    echo 'Error No se encontraron paramatros esenciales Consultar con soporte';
    die();

}


#controller administrador de paciente
require_once DOL_DOCUMENT.'/application/system/pacientes/pacientes_admin/controller/controller_adm_paciente.php';


//print_r($idPaciente);

?>

<script>

    $id_paciente               = "<?=  $idPaciente ?>"; //ID DE PACIENTE
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

        <section class="content container-fluid" id="boxViewadminPrincipal">

            <div class="box box-solid">
                <div class="box-header with-border">
                    <div id="tituloInfo" style="display: block" class="no-margin">
                        <div class="form-group col-xs-3 col-sm-3 no-padding no-margin"> <span style="font-weight: bolder"> <?= $NAME_MODULO ?> </span> </div>
                        <div class="form-group col-xs-3 col-sm-3 no-padding no-margin " style="float: right">  <p style="text-align: right" class="no-margin"> <i class="fa fa-user"></i> <?= getnombrePaciente($idPaciente)->nombre .' ' .getnombrePaciente($idPaciente)->apellido ?></p> </div>
                    </div>
                </div>
                <div class="box-body">
                        <div class="form-group col-md-12 col-xs-12 no-margin">
                            <ul class="list-inline">
                                <li>
                                    <a href="#menu_admin" data-toggle="modal"  class="btn btnhover" style="font-weight: bolder; color: #333333">  <i class="fa fa-bars"></i>  </a>
                                </li>
                            </ul>
                            <div  style="width: 100%; border-bottom: 1px solid #e8e8e8"></div>
                            <br>
                        </div>

                        <div class="form-group col-xs-12 col-md-12">
                            <?php

                                if(!empty($VIEW_GLOB_ADMIN_PACIENTES))
                                {
                                    include_once DOL_DOCUMENT.'/application/system/pacientes/pacientes_admin/'.$DIRECTORIO_ADMIN.'/view/'.$VIEW_GLOB_ADMIN_PACIENTES.'.php';
                                }
                                else{
                                    echo "OCURRIO UN ERROR";
                                }

                            ?>
                        </div>
                </div>
            </div>
        </section>

        <!--SCRIPT BOX PRINCIPAL-->
        <script>
            $boxContentViewAdminPaciente = $("#boxViewadminPrincipal");
        </script>

    </div>
</div>


<?php include_once DOL_DOCUMENT.'/public/view/modal_search_paciente.php'?>
<?php include_once DOL_DOCUMENT .'/public/view/footer_principal.php';?>

<!--modales glob admin pacientes-->
<?php include_once  DOL_DOCUMENT .'/application/system/pacientes/pacientes_admin/view/menu_admin.php'; ?>

<!--import los script js  modulos independientes -->
<?php include_once  'view/script_javascrip_mod.php'; ?>

