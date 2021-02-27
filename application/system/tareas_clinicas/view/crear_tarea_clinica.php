


<?php

#breadcrumbs  -----------------------------------------------
$url_breadcrumbs = $_SERVER['REQUEST_URI'];
$titulo = "Agregar Nueva Tarea";
$modulo = false;

?>

<div class="form-group col-xs-12 col-md-12 col-lg-12 no-padding">
    <div  class="form-group col-md-12 col-xs-12">
        <?= Breadcrumbs_Mod($titulo, $url_breadcrumbs, $modulo); ?>

    </div>
</div>