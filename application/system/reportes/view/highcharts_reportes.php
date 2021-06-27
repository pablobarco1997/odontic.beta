
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
        max-width: 650px;
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



<div class="form-group col-md-12 col-xs-12">

    <div class="row">

        <div class="col-md-6 col-xs-12">
            <figure class="highcharts-figure" >
                <div id="container_pagos_recibidos" ></div>
            </figure>
<!--            <a href="#recaudacion" id="recaudacion">&nbsp;</a>-->
        </div>

        <div class="col-md-6 col-xs-12">
            <figure class="highcharts-figure" >
                <div id="container_prestaciones_mas_realizadas" ></div>
            </figure>
<!--            <a href="#recaudacion" id="recaudacion">&nbsp;</a>-->
        </div>

    </div>

</div>

<script>

    var $dataMes = [
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

    function chartBrowzerPrestacionesR(data, anual) {

        Highcharts.chart('container_prestaciones_mas_realizadas', {
            chart: {
                plotBackgroundColor: null,
                plotBorderWidth: null,
                plotShadow: false,
                type: 'pie'
            },
            title: {
                text: 'Prestaciones Realizadas '+anual
            },
            tooltip: {
                pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'+' <br> '+'<small><b>$ {point.saldo}</b></small>'
            },
            accessibility: {
                point: {
                    valueSuffix: '%'
                }
            },
            plotOptions: {
                pie: {
                    allowPointSelect: true,
                    cursor: 'pointer',
                    dataLabels: {
                        enabled: true,
                        format: '<b>{point.name}</b>: {point.percentage:.1f} %'
                    }
                }
            },
            series: [{
                name: anual,
                colorByPoint: true,
                data: data
            }]
        });

    }
    
    function chartBrowzerPagosRecibidos(Series){

        // console.log( [
        //     {name:'prueba', data:[0,1,2,3,4,5,6,7,8,9,10,11]},
        //     {name:'prueba2', data:[0,1,2,3,4,5,6,7,8,9,10,11]},
        //     {name:'prueba3', data:[0,1,2,3,4,5,6,7,8,9,10,11]},
        // ] );
        //
        // console.log(Series);

        Highcharts.chart('container_pagos_recibidos', {
            chart:{
                type:'column'
            },
            title: {
                // text: '<b>TOTAL '+ YearText+' </b> <span style="font-weight: bold ;color: green" >$'+Total+'</span>'
                text: 'Recaudaciones'
            },
            xAxis: {
                categories: $dataMes,
                crosshair: false
            },
            yAxis: {
                title: {
                    text: ''
                },
                labels: {
                    formatter: function () {
                        return this.value + ' $';
                    }
                }
            },
            // tooltip: {
            //     // headerFormat: '<span>{point.x}</span>',
            //     // pointFormat: '<span style="color:{point.color}" >{point.name}</span> <small><b>$ {point.y}</b></small>'
            // },
            // plotOptions: {
            //     column: {
            //         pointPadding: 0.2,
            //         borderWidth: 0
            //     }
            // },

            // series:[
            //     {name:'prueba', data:[0,1,2,3,4,5,6,7,8,9,10,11]},
            //     {name:'prueba2', data:[0,1,2,3,4,5,6,7,8,9,10,11]},
            //     {name:'prueba3', data:[0,1,2,3,4,5,6,7,8,9,10,11]},
            // ],

            series: Series,

            // responsive: {
            //     rules: [{
            //         condition: {
            //             maxWidth: '650px'
            //         },
            //         chartOptions: {
            //             legend: {
            //                 layout: 'horizontal',
            //                 align: 'center',
            //                 verticalAlign: 'bottom'
            //             }
            //         }
            //     }]
            // }
        });
    }

    function loadcharRecaudacionesPagosRecibidos(focus = false){

        $.ajax({
            url: $url_reportes,
            type: 'POST',
            data:{
                'accion' : 'fechPagosRecibidosMensuales',
                'ajaxSend' : 'ajaxSend',
                'year': $("#report_anual").find(":selected").val(),
                'mes' : $("#report_meses").find(":selected").val(),
            },
            dataType: 'json',
            cache: false, 
            async:true ,
            success: function (response) {
                var respuesta = response;
                if(respuesta['error']==''){
                    chartBrowzerPagosRecibidos(respuesta['data']);
                }
            }
        });

        // $.get($url_reportes, {
        //         'ajaxSend':'ajaxSend',
        //         'accion':'fechPagosRecibidosMensuales',
        //         'year':$("#report_anual").find(":selected").val(),
        //         'mes': $("#report_meses").find(":selected").val()
        //     },
        //     function(data) {
        //         var respuesta = $.parseJSON(data);
        //         if(respuesta['error']==''){
        //             chartBrowzerPagosRecibidos(respuesta['err'],respuesta['totalAnual'],$Mes, $("#report_anual").find(":selected").text());
        //         }
        // });

        if(focus==true){

        }
    }

    function loadCharPrestacionesRealizadas(){

        $.ajax({
            url: $url_reportes,
            type: 'POST',
            data:{
                'accion': 'Charts_prestaciones_realizadas',
                'ajaxSend' : 'ajaxSend',
            },
            dataType: 'json',
            cache: false,
            async:true ,
            success: function (response) {
                var respuesta = response;
                var count = respuesta['result']['data'].left;
                var data  = respuesta['result']['data'];
                var anual = respuesta['result']['anual'];
                console.log(data);
                chartBrowzerPrestacionesR(data, anual);
            }
        });
        
    }



    $(window).on('load', function() {

        $("#report_anual").select2({
            placeholder:'Busqueda Anual',
            allowClear: false,
            language:languageEs
        });

        $("#report_meses").select2({
            placeholder:'Busqueda por Mes',
            allowClear: true,
            language:languageEs
        });

        $("#report_anual").val(<?= $Year ?>).trigger("change");

        loadcharRecaudacionesPagosRecibidos();
        loadCharPrestacionesRealizadas();

        // chartBrowzerPrestacionesR();
    });


</script>