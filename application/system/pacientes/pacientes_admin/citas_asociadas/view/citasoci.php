

<?php
    accessoModule('Citas Asociadas');
?>

<div class="form-group col-lg-12 col-md-12">

    <div class="form-group col-md-12 col-lg-12 col-xs-12 ">
        <label for="">LISTA DE COMPORTAMIENTOS</label>
        <ul class="list-inline" style="border-bottom: 0.6px solid #333333; padding: 3px; background-color: #f4f4f4; margin-left: 0px">
            <li><a data-toggle="collapse" data-target="#contentFilter" class="btnhover btn btn-sm <?= (!PermitsModule('Citas Asociadas', 'consultar'))?'disabled_link3':'' ?> " style="color: #333333" aria-expanded="true"> <b>   ▼  Filtrar  </b>  </a> </li>
            <li>
                <a href="<?= DOL_HTTP?>/application/system/pacientes/pacientes_admin/citas_asociadas/export/exportpdf_historialcitas.php?idpaciente=<?=$idPaciente?>"  style="color: #333333" target="_blank" class="btnhover btn btn-sm <?= (!PermitsModule('Citas Asociadas', 'consultar'))?'disabled_link3':'' ?>" id="exportCitasPaciente"><b> <i class="fa fa-print"></i> PDF </b> </a>
                <a href="<?= DOL_HTTP?>/application/system/pacientes/pacientes_admin/citas_asociadas/export/export_excel_agendada_xpaciente.php?pacientes=<?=$idPaciente?>"  style="color: #333333" target="_blank" class="btnhover btn btn-sm <?= (!PermitsModule('Citas Asociadas', 'consultar'))?'disabled_link3':'' ?>" id="exportCitasExcel"><b> <i class="fa fa-print"></i> EXCEL </b> </a>
            </li>
            <li>
                <a href="<?= DOL_HTTP?>/application/system/agenda/index.php?view=agendadd" style="color: #333333" class="btnhover btn btn-sm <?= (!PermitsModule('Citas Asociadas', 'agregar'))?'disabled_link3':'' ?>" id=""><b> <i class="fa fa-calendar-check-o"></i> Agendar</b> </a>
            </li>
        </ul>
    </div>


    <div class="form-group col-xs-12 col-md-12 col-lg-12 collapse no-margin" id="contentFilter" aria-expanded="true" style="">
        <div class="form-group col-md-12 col-xs-12 col-lg-12" style="background-color: #f4f4f4; padding-top: 15px">

            <div class="form-group col-md-12 col-xs-12 col-lg-12"> <h3 class="no-margin"><span>Filtrar Citas</span></h3> </div>

            <div class="form-group col-md-3 col-xs-3">
                <label>Fecha</label>
                <div class="input-group form-group rango" style="margin: 0">
                    <input type="text" class="form-control filtroFecha  " readonly="" id="startDate" value="">
                    <span class="input-group-addon" style="border-radius: 0"><i class="fa fa-calendar"></i></span>
                </div>
            </div>

            <div class="form-group col-md-3 col-xs-3">
                <label>#. Cita</label>
                <input type="text" class="form-control" id="filtra_citas" name="filtra_citas">
            </div>

            <div class=" form-group col-md-6 col-xs-6">
                <label>Seleccione uno o varios Estados</label>
                <select name="filtrar_estados" id="filtrar_estados" multiple class="form-control" style="width: 100%">
                    <?php
                        $sql = "select rowid , text from tab_pacientes_estado_citas";
                        $result = $db->query($sql);
                        if($result&&$result->rowCount()>0){
                            while ($object = $result->fetchObject()){
                                print "<option value='$object->rowid'>$object->text</option>";
                            }
                        }
                    ?>
                </select>
            </div>

            <div class="form-group col-md-12 col-xs-12">
                <ul class="list-inline pull-right">
                    <li>  <button class="limpiar btn   btn-block  btn-default" id="limpiarFiltro" style="float: right; padding: 10px"> &nbsp; &nbsp; Limpiar &nbsp; &nbsp;</button> </li>
                    <li>  <button class="aplicar btn   btn-block  btn-success" id="filtrar" style="float: right; padding: 10px"> &nbsp;  &nbsp;Aplicar busqueda &nbsp;</button> </li>
                </ul>
            </div>
        </div>
    </div>

    <br>
    <div class="form-group col-xs-12 col-md-12">
        <label for="">LISTA DE CITAS ASOCIADAS</label>

        <div class="table-responsive">
            <div class="table-responsive">
                <br>
                <table class="table table-condensed" width="100%" id="list_citasAsociadas">
                    <thead>
                        <tr style="background-color: #f4f4f4">
                            <th width="15%">Emitido</th>
                            <th width="17%">Especialidad</th>
                            <th width="9%"># Cita</th>
                            <th width="27%">Información Adicional</th>
<!--                            <th width="20%">Plan de tratamiento asociado</th>-->
                            <th width="17%">Estado de la Cita</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

</div>