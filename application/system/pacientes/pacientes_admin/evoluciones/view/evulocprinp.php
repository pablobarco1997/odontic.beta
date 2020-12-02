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
        #Evoluciones Principal
        if(isset($_GET['v']) && $_GET['v'] == 'list_evul'){ ?>

        <div class="form-group col-md-12 col-xs-12">
            <label for="">LISTA DE COMPORTAMIENTOS</label>
            <ul class="list-inline" style="border-bottom: 0.6px solid #333333; padding: 3px">
                <li> <a data-toggle="collapse" data-target="#contentFilter" style="color: #333333" class="btnhover btn btn-sm " id="fitrar_document"> <b>  ▼ &nbsp;Filtrar <i></i> </b> </a> </li>
                <li> <a href="#" style="color: #333333" class="btnhover btn btn-sm " id="imprimirEvolucion" onclick="AppExporPrint()"> <i class="fa fa-print"></i> <b> Imprimir <i></i> </b> </a> </li>
            </ul>

            <div class="form-group col-xs-12 col-md-12 col-lg-12 contentFilter collapse " id="contentFilter" style="background-color: #f4f4f4; padding-top: 15px">

                <div class="form-group col-md-12 col-xs-12 col-lg-12"> <h3 class="no-margin"><span>Filtrar Evoluciones</span></h3> </div>

                <div class="form-group col-md-8 col-xs-12 col-sm-12">
                    <label>Planes de Tratamiento</label>
                    <select class="form-control" id="filt_plantram" style="width: 100%">
                        <option value=""></option>
                            <?php
                              $sqldoctPlantram = "SELECT 
                                                c.rowid , 
                                                ifnull(c.edit_name, concat('Plan de Tratamiento ', 'N. ', c.numero)) plantram ,
                                                concat('Doc(a) ', ' ', ifnull( (select concat( od.nombre_doc , ' ', od.apellido_doc ) as nomb from tab_odontologos od where od.rowid = c.fk_doc), 'No asignado')) as encargado
                                                FROM tab_plan_tratamiento_cab c where c.fk_paciente = $idPaciente and c.estados_tratamiento not in('E') ";
                              $rsdoctPlantram = $db->query($sqldoctPlantram);
                              if( $rsdoctPlantram && $rsdoctPlantram->rowCount()>0)
                              {
                                  while ( $obdoct = $rsdoctPlantram->fetchObject() ){
                                      echo '<option value="'.$obdoct->rowid.'"> '. $obdoct->plantram .' - '. $obdoct->encargado .' </option>';
                                  }
                              }
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
                <table class="table table-striped" id="list_evoluprinpl" width="100%">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Plan de Tratamiento</th>
                            <th>Prestación</th>
                            <th>Pieza</th>
                            <th>Estado de Pieza</th>
                            <th>Odontolog@ Encargado</th>
                            <th>observación</th>
                            <th>Caras</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>

    <?php }?>


</div>