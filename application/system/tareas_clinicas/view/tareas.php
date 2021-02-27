
<?php

#breadcrumbs  -----------------------------------------------
$url_breadcrumbs = $_SERVER['REQUEST_URI'];
$titulo = "Tareas Clinicas";
$modulo = true;

?>

<div class="form-group col-xs-12 col-md-12 col-lg-12 no-padding">
    <div  class="form-group col-md-12 col-xs-12">
        <?= Breadcrumbs_Mod($titulo, $url_breadcrumbs, $modulo); ?>
        <label for="">LISTA DE COMPORTAMIENTOS</label>
        <ul class="list-inline" style="border-bottom: 0.6px solid #333333; padding: 3px">
            <li> <a href="#ContenFiltroDocumentos" data-toggle="collapse" style="color: #333333" class="btnhover btn btn-sm " id="fitrar_document"> <b>  ▼ &nbsp;Filtrar <i ></i> </b> </a> </li>
            <li> <a href="#"  style="color: #333333" class="btnhover btn btn-sm " id="crear_nueva_caja" onclick="CrearTareaClinica()"> <b>  Crear Tarea </b> </a> </li>
        </ul>
    </div>
</div>



<script>
    



    var CrearTareaClinica = function(){

        if(!ModulePermission(26,2)){

            notificacion('Ud. No tiene permiso para esta Operación', 'error');
            return false;
        }

        window.location = $DOCUMENTO_URL_HTTP + '/application/system/tareas_clinicas/index.php?view=crear_tarea_clinica';

    };


    $(document).ready(function() {

    });


    $(window).on('load', function() {



    });

</script>