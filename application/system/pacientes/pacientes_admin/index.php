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
$Paciente_id_Hexadecimal = 0;
if(isset($_GET['id']))
{

    PERMISO_ACCESO_ADMIN_PACIENTES(GETPOST('key'));  #permisos

    $idPaciente  = decomposeSecurityTokenId($_GET['id']); #id del paciente
    $Paciente_id_Hexadecimal = $_GET['id'];

    #VISTAS FORMULARIOS ------------------------------------------------------------------------------------------------
    include_once 'view/vistas_mod.php';


}else{

    echo 'Error No se encontraron paramatros esenciales Consultar con soporte';
    die();

}


#controller administrador de paciente
require_once DOL_DOCUMENT.'/application/system/pacientes/pacientes_admin/controller/controller_adm_paciente.php';


//echo '<pre>'; print_r($conf); die();

?>

<script>

    $id_paciente               = "<?=  $idPaciente ?>"; //ID DE PACIENTE
    $DOCUMENTO_URL_HTTP        = "<?=  DOL_HTTP ?>"; //URL  HTTP DOCUMENTO
    $HTTP_DIRECTORIO_ENTITY    = "<?=  $conf->NAME_DIRECTORIO ?>";  //ENTIDAD DE LA EMPRESA PARA JAVASCRIPT
    $keyGlobal                 = "<?=  KEY_GLOB ?>"; //KEY GLOBAL
    $Paciente_id               = "<?=  $Paciente_id_Hexadecimal ?>"; //Paciente_id

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

            <!--SCRIPT BOX PRINCIPAL-->
            <script>
                $boxContentViewAdminPaciente = $("#boxViewadminPrincipal");
            </script>

            <div class="box box-solid">
                <div class="box-header with-border">
                    <div id="tituloInfo" style="display: block" class="no-margin">
                        <div class="form-group col-xs-3 col-sm-3  no-margin" style="padding: 5px 0px 0px 0px"> <p  class="no-margin"><b><?= $NAME_MODULO ?></b></p> </div>
                        <div class="form-group col-xs-3 col-sm-3 no-padding no-margin " style="float: right">
                            <p style="text-align: right" class="no-margin">
                                <?php if(is_file(DOL_DOCUMENT."/logos_icon/icon_logos_".$conf->EMPRESA->ENTIDAD."/".getnombrePaciente($idPaciente)->icon)){
                                    print "<img src='".DOL_HTTP."/logos_icon/icon_logos_".$conf->EMPRESA->ENTIDAD."/".getnombrePaciente($idPaciente)->icon."'  alt='".getnombrePaciente($idPaciente)->nombre." ".getnombrePaciente($idPaciente)->apellido."' style='vertical-align: middle;width: 30px;height: 30px;border-radius: 50%;border:1px solid black;box-shadow: 0px 4px 3px #ccc;' >";
                                }else{
                                    print "<img src='".DOL_HTTP."/logos_icon/logo_default/avatar_none.ico'  alt='".getnombrePaciente($idPaciente)->nombre." ".getnombrePaciente($idPaciente)->apellido."' style='vertical-align: middle;width: 30px;height: 30px;border-radius: 50%;border:1px solid black;box-shadow: 0px 4px 3px  #ccc;'  >";
                                } ?>
                                &nbsp;&nbsp;&nbsp;<b><?= getnombrePaciente($idPaciente)->nombre .' ' .getnombrePaciente($idPaciente)->apellido ?></b></p> </div>
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
    </div>
</div>


<?php include_once DOL_DOCUMENT.'/public/view/modal_search_paciente.php'?>
<?php include_once DOL_DOCUMENT .'/public/view/footer_principal.php';?>

<!--modales glob admin pacientes-->
<?php include_once  DOL_DOCUMENT .'/application/system/pacientes/pacientes_admin/view/menu_admin.php'; ?>

<!--import los script js  modulos independientes -->
<?php include_once  'view/script_javascrip_mod.php'; ?>


