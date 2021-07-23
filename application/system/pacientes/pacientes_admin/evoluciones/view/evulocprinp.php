<?php

    $accion = "";

    if(isset($_GET['v']) && $_GET['v'] == 'list_evul'){
        $accion = 'evol_listprincipal';
    }

?>

<script>

    $accion_evol = "<?= $accion ?>";

</script>

<div class="form-group col-xs-12 col-md-12 col-lg-12">


    <?php

        //Evoluciones Principal
        if(isset($_GET['v']) && $_GET['v'] == 'list_evul')
        {
            ?>

        <div class="form-group col-md-12 col-xs-12">

            <label for="">LISTA DE COMPORTAMIENTOS</label>
            <ul class="list-inline" style="border-bottom: 0.6px solid #333333; padding: 3px; background-color: #f4f4f4; margin-left: 0px">
                <li> <a data-toggle="collapse" data-target="#contentFilter" style="color: #333333" class="btnhover btn btn-sm " id="fitrar_document"> <b>  ▼ &nbsp;Filtrar <i></i> </b> </a> </li>
                <li> <a href="#" style="color: #333333" class="btnhover btn btn-sm " id="imprimirEvolucion" onclick="AppExporPrint()"> <i class="fa fa-print"></i> <b> Imprimir <i></i> </b> </a> </li>
            </ul>
            <br>

            <div class="form-group col-xs-12 col-md-12 col-lg-12 contentFilter no-margin collapse  " id="contentFilter" style="background-color: #f4f4f4; ">

                <div class="form-group col-md-12 col-xs-12 col-lg-12"> <h3 class="no-margin" style="padding-top: 15px"><span>Filtrar Evoluciones</span></h3> </div>

                <div class="form-group col-md-3 col-xs-12 col-sm-12">
                    <label>Fecha</label>
                    <div class="input-group form-group rango" style="margin: 0">
                        <input type="text" class="form-control filtroFecha  " readonly="" id="startDateEvoluciones" value="" style="font-size: small">
                        <span class="input-group-addon" style="border-radius: 0"><i class="fa fa-calendar"></i></span>
                    </div>
                </div>

                <div class="form-group col-md-8 col-xs-12 col-sm-12">
                    <label>Planes de Tratamiento</label>
                    <select class="form-control" id="filt_plantram" style="width: 100%">
                        <option value=""></option>
                            <?php

                            ?>
                        </select>
                </div>

                <div class="form-group col-md-12 col-xs-12">
                    <ul class="list-inline pull-right">
                        <li>  <button class="limpiar btn   btn-block  btn-default" id="limpiar" style="float: right; padding: 10px"> &nbsp; &nbsp; Limpiar &nbsp; &nbsp;</button> </li>
                        <li>  <button class="aplicar btn   btn-block  btn-success" id="filtrar_evoluc" style="float: right; padding: 10px"> &nbsp;  &nbsp;Aplicar busqueda &nbsp;</button> </li>
                    </ul>
                </div>

            </div>
        </div>

<!--        LIST PRINCIPAL-->

        <div class="form-group col-xs-12 col-md-12">
            <div class="table-responsive">
                <table class="table" id="list_evoluprinpl" width="100%">
                    <thead style="background-color: #f4f4f4">
                        <tr>
                            <th>Emitido</th>
                            <th>Plan de Tratamiento</th>
                            <th>Prestación</th>
                            <th>Pieza</th>
                            <th>Estado de Pieza</th>
                            <th>Doctor(a) Encargado</th>
                            <th>observación</th>
                            <th>Caras</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>

    <?php }?>


</div>