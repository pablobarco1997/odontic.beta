<style>
    .callbox{
        border-left: 2px solid #212f3d;
    }
</style>

<?php

    $url_breadcrumb = ""; #Obtengo la url
    $module         = "";

    if(isset($_GET['list']) && $_GET['list'] == 'diaria'){

        $module = true;
        $url_breadcrumb = $_SERVER['REQUEST_URI'];
        $titulo         = 'Agenda Diaria';

    }

?>

<div class="form-group col-md-6 col-xs-12 pull-left">
    <?php echo Breadcrumbs_Mod($titulo, $url_breadcrumb, $module) ?>
</div>

<?php $ContInvalic = 0; ?>

<div class="form-group col-md-12 col-xs-12 col-sm-12 col-lg-12">
    <!--Lista diaria-->
    <?php
        if(isset($_GET['list']) && $_GET['list'] == 'diaria') {
            include_once DOL_DOCUMENT .'/application/system/agenda/view/list_diaria.php';
            $ContInvalic++;
        }
    ?>

    <!--                Lista global-->
    <div class="list-diariaGlobal" style="width: 100%">
        <?php

        if(isset($_GET['list']) && $_GET['list'] == 'diariaglob')
        {
            include_once DOL_DOCUMENT .'/application/system/agenda/view/list_globaldiaria.php';
            $ContInvalic++;
        }

        ?>
    </div>

    <?php
    #Error no se encontro la vista - esto es cuando modifican la url
    if($ContInvalic==0){

        echo '<div class="alert alert-red">
                                       <h2 class="text-bold text-center" style="color: red;">Ocurrió un error no se encuentra la vista - consulte con soporte tecnico</h2>
                                   </div>';
        die();
    }

    ?>

</div>




<!--modales-->
<?php
    include_once DOL_DOCUMENT .'/application/system/agenda/view/status_modal.php';
?>