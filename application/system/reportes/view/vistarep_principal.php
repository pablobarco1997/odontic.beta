

<?php

    $Year = date("Y");

    require_once DOL_DOCUMENT.'/application/system/reportes/view/modal_views_reporte.php';

?>

<script>
    var YearDinamic = <?= $Year ?>;
</script>

<div class="form-group col-xs-12 col-md-12">
    <br>
    <div class=" col-xs-12 col-md-6 col-sm-12 col-centered " style="border-bottom: 1px solid #e8e8e8; border-top: 1px solid #e8e8e8">
        <h4 class="text-center text-bold"><span>Agenda</span></h4>
    </div>

    <br>
    <div class="form-group col-xs-12 col-md-12 col-lg-12">
        <div class="form-group col-md-5 col-lg-5 col-sm-8 col-xs-12 col-centered">
            <label for="">Filtro x Fecha</label>
            <div class="input-group form-group rango" style="margin: 0">
                <input type="text" class="form-control filtroFecha  " id="startDate" value="" readonly>
                <span class="input-group-addon" style="border-radius: 0"><i class="fa fa-calendar"></i></span>
            </div>
        </div>
    </div>

    <div class="form-group col-xs-12 col-md-12">
        <div class="col-lg-3 col-xs-6">
            <div class="small-box bg-blue-active">
                <div class="inner">
                    <h3 id="nu_paciente">0</h3>
                    <p>Pacientes Registrados</p>
                </div>
                <div class="icon" style="top: 5px;">
                    <i class="ion ion-person-add iconShadow"></i>
                </div>
                <a href="#" class="small-box-footer">Mas Información <i class="fa fa-arrow-circle-right"></i></a>
            </div>
        </div>

        <div class="col-lg-3 col-xs-6">
            <div class="small-box bg-blue-active">
                <div class="inner">
                    <h3 id="nu_citasAnuladaCancel">0</h3>
                    <p>Citas Anuladas o canceladas</p>
                </div>
                <div class="icon" style="top: 5px;">
                    <i class="fa fa-calendar-times-o iconShadow"></i>
                </div>
                <a href="#" class="small-box-footer">Mas Información <i class="fa fa-arrow-circle-right"></i></a>
            </div>
        </div>

        <div class="col-lg-3 col-xs-6">
            <div class="small-box bg-blue-active">
                <div class="inner">
                    <h3 id="nu_plantActivoAbonad">0</h3>
                    <p>Tratamientos Activos y Abonados</p>
                </div>
                <div class="icon" style="top: 5px;">
                    <i class="ion ion-stats-bars iconShadow"></i>
                </div>
                <a href="#" class="small-box-footer">Mas Información <i class="fa fa-arrow-circle-right"></i></a>
            </div>
        </div>

        <div class="col-lg-3 col-xs-6">
            <div class="small-box bg-blue-active">
                <div class="inner">
                    <h3 id="nu_citasAtendidas">0</h3>
                    <p>Atendidos</p>
                </div>
                <div class="icon" style="top: 5px;">
                    <i class="fa fa-calendar-check-o"></i>
                </div>
                <a href="#" class="small-box-footer">Mas Información <i class="fa fa-arrow-circle-right"></i></a>
            </div>
        </div>

    </div>

</div>

<div class="form-group col-xs-12 col-md-12">
    <div class="col-xs-12 col-md-6 col-sm-12 col-centered " style="border-bottom: 1px solid #e8e8e8; border-top: 1px solid #e8e8e8">
        <h4 class="text-center text-bold"><span>Recaudaciones</span></h4>
    </div>
</div>

<div class="form-group col-xs-12 col-md-12">
    <div class="form-group col-xs-12 col-md-12">
        <?php require_once DOL_DOCUMENT.'/application/system/reportes/view/highcharts_reportes.php'?>
    </div>
</div>