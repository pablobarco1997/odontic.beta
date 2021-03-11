
<?php
    $Year = date('Y');
?>

<script src="<?=DOL_HTTP?>/application/system/reportes/js/lib.highcharts_reportes/code/highcharts.js"></script>
<script src="<?=DOL_HTTP?>/application/system/reportes/js/lib.highcharts_reportes/code/modules/exporting.js"></script>
<script src="<?=DOL_HTTP?>/application/system/reportes/js/lib.highcharts_reportes/code/modules/export-data.js"></script>
<script src="<?=DOL_HTTP?>/application/system/reportes/js/lib.highcharts_reportes/code/modules/accessibility.js"></script>

<style>
    .highcharts-figure, .highcharts-data-table table {
        min-width: 310px;
        max-width: 800px;
        margin: 1em auto;
    }

    .highcharts-data-table table {
        font-family: Verdana, sans-serif;
        border-collapse: collapse;
        border: 1px solid #EBEBEB;
        margin: 10px auto;
        text-align: center;
        width: 100%;
        max-width: 500px;
    }
    .highcharts-data-table caption {
        padding: 1em 0;
        font-size: 1.2em;
        color: #555;
    }
    .highcharts-data-table th {
        font-weight: 600;
        padding: 0.5em;
    }
    .highcharts-data-table td, .highcharts-data-table th, .highcharts-data-table caption {
        padding: 0.5em;
    }
    .highcharts-data-table thead tr, .highcharts-data-table tr:nth-child(even) {
        background: #f8f8f8;
    }
    .highcharts-data-table tr:hover {
        background: #f1f7ff;
    }
</style>

<div class="form-group col-xs-12 col-md-7 col-centered">
    <div class="form-group col-md-6 col-xs-12">
        <label for="">Busqueda x Mes</label>
        <select name="report_meses" id="report_meses" class="form-control" style="width: 100%">
            <option value=""></option>
            <?php
            $Meses = ['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];
            $i = 1;
            foreach ($Meses as $value){
                print "<option value='".$i."'>".$value."</option>";
                $i++;
            }
            ?>
        </select>
    </div>
    <div class="form-group col-md-6 col-xs-12">
        <label for="">Busqueda x Año</label>
        <select name="report_anual" id="report_anual" class="form-control" style="width: 100%">
            <?php
            for ($u = 2000; $u <= date("Y"); $u++){
                print "<option value='".$u."'>".$u."</option>";
            }
            ?>
        </select>
    </div>
</div>


<div class="form-group col-md-12 col-xs-12">
    <h5 class="text-center text-bold"><span>Pagos recibidos</span></h5>
</div>

<div class="form-group col-md-12 col-xs-12">
    <figure class="highcharts-figure" style="width: 100%">
        <div id="container_pagos_recibidos"></div>
    </figure>
    <a href="#recaudacion" id="recaudacion">&nbsp;</a>
</div>

<script>

    $dataMes = [
        'Ene',
        'Feb',
        'Mar',
        'Abr',
        'May',
        'Jun',
        'Jul',
        'Ago',
        'Sep',
        'Oct',
        'Nov',
        'Dec'
    ];

    $url_reportes = $DOCUMENTO_URL_HTTP + '/application/system/reportes/controller/controller_reporte.php';

    function chartBrowzerPagosRecibidos(dataMensual, Total, meses, YearText){

        Highcharts.chart('container_pagos_recibidos', {
            chart:{
                type:'column'
            },
            title: {
                text: 'Pago Total de lo que lleva del año '+ YearText + ' <b>$'+Total+'</b>'
            },
            xAxis: {
                categories: meses,
                crosshair: true
            },
            yAxis: {
                min: 0,
                title: {
                    text: ' '
                }
            },
            // tooltip: {
            //     headerFormat: '<span style="font-size:11px">Pagos recibidos</span><br>',
            //     pointFormat: '<span style="color:{point.color}">{point.name}</span>: <b>$ {point.y}</b><br/>'
            // },
            // plotOptions: {
            //     column: {
            //         pointPadding: 0.2,
            //         borderWidth: 0
            //     }
            // },
            series:[
                {
                    name: 'Pago total del Mes',
                    data: dataMensual
                }
            ],
            responsive: {
                rules: [{
                    condition: {
                        maxWidth: 1000
                    },
                    chartOptions: {
                        legend: {
                            layout: 'horizontal',
                            align: 'center',
                            verticalAlign: 'bottom'
                        }
                    }
                }]
            }
        });
    }

    function loadcharRecaudacionesPagosRecibidos(focus = false){
        var $Mes = [];
        var u = 0;
        $.each($dataMes, function (i, item) {
            var m = $("#report_meses").find(":selected").val();
            if( m !=""){
                if(m==(u+1)){
                    $Mes.push($dataMes[u]);
                }
            }
            else{
                $Mes.push($dataMes[u]);
            }
            u++;
        });

        $.get($url_reportes, {
                'ajaxSend':'ajaxSend',
                'accion':'fechPagosRecibidosMensuales',
                'year':$("#report_anual").find(":selected").val(),
                'mes': $("#report_meses").find(":selected").val()
            },
            function(data) {
                var respuesta = $.parseJSON(data);
                if(respuesta['error']==''){
                    chartBrowzerPagosRecibidos(respuesta['err'],respuesta['totalAnual'],$Mes, $("#report_anual").find(":selected").text());
                }
        });
        if(focus==true){

        }
    }


    $("#report_anual, #report_meses").change(function () {
        loadcharRecaudacionesPagosRecibidos(true);
    });

    $(window).on('load', function() {

        $("#report_anual").select2({
            placeholder:'Selecione un año',
            allowClear: false,
            language:'es'
        });

        $("#report_meses").select2({
            placeholder:'Selecione un Mes',
            allowClear: true,
            language:'es'
        });

        $("#report_anual").val(<?= $Year ?>).trigger("change");

    });


</script>