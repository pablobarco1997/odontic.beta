

<?php

    $Year = date("Y");

    require_once DOL_DOCUMENT.'/application/system/reportes/view/modal_views_reporte.php';

?>

<script>
    var YearDinamic = <?= $Year ?>;
</script>

<style>

    .filtroFecha{
        border: 0;
        font-weight: bolder;
        font-size: 1.3rem;
    }

    p.textboxInformation{
        font-size: 1.2rem !important;
    }

</style>

<div class="form-group col-xs-12 col-md-12">
    <br>
    <div class=" col-xs-12 col-md-6 col-sm-12 col-centered " style="border-bottom: 1px solid #e8e8e8; border-top: 1px solid #e8e8e8">
        <h4 class="text-center text-bold"><span style="font-size: 1.8rem">Agenda</span></h4>
    </div>

    <br>
    <div class="form-group col-xs-12 col-md-12 col-lg-12">
        <div class="form-group col-md-5 col-lg-5 col-sm-8 col-xs-12 col-centered">
<!--            <label for="">Filtro x Fecha</label>-->
            <input type="text" class="form-control filtroFecha  " id="startDate" value="" readonly>
            <h4 class="text-center" style="" id="labelSpanishSatrDtae" ><span style="margin-top: 10px; font-size: 1.3rem!important; font-weight:bold ">1 de enero de 2021 hasta 1 de enero de 2022</span></h4>
        </div>
    </div>

    <div class="form-group col-xs-12 col-md-12">
        <div class="col-lg-3 col-xs-6">
            <div class="small-box bg-blue-active">
                <div class="inner">
                    <h3 id="nu_paciente">0</h3>
                    <p class="textboxInformation">PACIENTES REGISTRADOS</p>
                </div>
                <div class="icon" style="top: 5px;">
                    <i class="ion ion-person-add iconShadow" style="font-size:100px !important;"></i>
                </div>
                <a href="#" class="small-box-footer" id="reportes_pacientes_anulados">Mas Información <i class="fa fa-arrow-circle-right"></i></a>
            </div>
        </div>

        <div class="col-lg-3 col-xs-6">
            <div class="small-box bg-blue-active">
                <div class="inner">
                    <h3 id="nu_citasAnuladaCancel">0</h3>
                    <p class="textboxInformation">CITAS CANCELADAS</p>
                </div>
                <div class="icon" style="top: 5px;">
                    <i class="fa fa-calendar-times-o iconShadow" style="font-size:90px !important;"></i>
                </div>
                <a href="#" class="small-box-footer">Mas Información <i class="fa fa-arrow-circle-right"></i></a>
            </div>
        </div>

        <div class="col-lg-3 col-xs-6">
            <div class="small-box bg-blue-active">
                <div class="inner">
                    <h3 id="nu_plantActivoAbonad">0</h3>
                    <p class="textboxInformation">TRATAMIENTOS ACTIVOS O ABONADOS</p>
                </div>
                <div class="icon" style="top: 5px;">
                    <i class="ion ion-stats-bars iconShadow" style="font-size:100px !important;"></i>
                </div>
                <a href="#" class="small-box-footer">Mas Información <i class="fa fa-arrow-circle-right"></i></a>
            </div>
        </div>

        <div class="col-lg-3 col-xs-6">
            <div class="small-box bg-blue-active">
                <div class="inner">
                    <h3 id="nu_citasAtendidas">0</h3>
                    <p class="textboxInformation">ATENDIDOS</p>
                </div>
                <div class="icon" style="top: 5px;">
                    <i class="fa fa-calendar-check-o" style="font-size:90px !important;"></i>
                </div>
                <a href="#" class="small-box-footer">Mas Información <i class="fa fa-arrow-circle-right"></i></a>
            </div>
        </div>

    </div>

</div>

<div class="form-group col-xs-12 col-md-12">
    <div class="col-xs-12 col-md-6 col-sm-12 col-centered " style="border-bottom: 1px solid #e8e8e8; border-top: 1px solid #e8e8e8">
        <h4 class="text-center text-bold"><span style="font-size: 1.8rem">Recaudaciones</span></h4>
    </div>
</div>

<div class="form-group col-xs-12 col-md-12">
    <div class="form-group col-xs-12 col-md-12">
        <?php require_once DOL_DOCUMENT.'/application/system/reportes/view/highcharts_reportes.php'?>
    </div>
</div>

<script src="<?= DOL_HTTP ?>/application/system/reportes/js/peticiones_reportes.js"></script>