
<?php

#breadcrumbs  -----------------------------------------------
$url_breadcrumbs = $_SERVER['REQUEST_URI'];
$titulo = "Estado de resultado";
$modulo = true;

?>

<div class="form-group col-xs-12 col-md-12 col-lg-12 no-padding">

    <div  class="form-group col-md-12 col-xs-12">
        <?= Breadcrumbs_Mod($titulo, $url_breadcrumbs, $modulo); ?>
        <label for="">LISTA DE COMPORTAMIENTOS</label>
        <ul class="list-inline" style="background-color: #f4f4f4; border-bottom: 0.6px solid #333333; padding: 3px; margin-left: 0px">
            <li><a href="#contentFilter" data-toggle="collapse" style="color: #333333" class="btnhover btn btn-sm " id="fitrar_document"> <b>  ▼ &nbsp;Filtrar <i></i> </b> </a></li>
            <li><a href="#" class="btnhover btn btn-sm excel <?= (!PermitsModule('Reporte estado de clinica', 'consultar')?'disabled_link3':'')?> " style="color: #333333" onclick="exports($(this))"><b>EXCEL</b> <i class="fa fa-print"></i> </a></li>
            <li>
                <a id="refresh_resultlist" class="btn" style="color: #333333" onclick="estadoResultadoList()">
                    <i class="fa fa-refresh "></i>
                </a>
            </li>
        </ul>
    </div>

    <div class="form-group col-xs-12 col-md-12 col-lg-12 collapse contentFilterEstadoResultClinica" id="contentFilter" aria-expanded="true" style="margin-bottom: 0px">
        <div class="col-md-12 col-xs-12 col-lg-12" style="background-color: #f4f4f4; padding-top: 15px">
            <div class="form-group col-md-12 col-xs-12 col-lg-12"> <h3 class="no-margin"><span>Filtrar </span></h3> </div>


            <div class="form-group col-md-4 col-xs-12 col-lg-3">
                <label for="">Fecha</label>
                <div class="input-group form-group rango" style="margin: 0">
                    <input type="text" class="form-control dateGasto" readonly="" id="emitido" value="" style="font-size: small">
                    <span class="input-group-addon" style="border-radius: 0"><i class="fa fa-calendar"></i></span>
                </div>
            </div>



            <div class="form-group col-md-12 col-xs-12">
                <ul class="list-inline pull-right">
                    <li>  <button class="limpiar btn   btn-block  btn-default" id="limpiarEsResultFiltros" style="float: right; padding: 10px" onclick="filtro($(this))"> &nbsp; &nbsp; Limpiar &nbsp; &nbsp;</button> </li>
                    <li>  <button class="aplicar btn   btn-block  btn-success" id="aplicarEsResultFiltros" style="float: right; padding: 10px" onclick="filtro($(this))"> &nbsp;  &nbsp;Aplicar busqueda &nbsp;</button> </li>
                </ul>
            </div>
        </div>
    </div>



    <div class="form-group col-xs-12 col-md-12 " style="margin-top: 15px" >
        <div class="table-responsive">
            <table class="table table-condensed table-hover" width="100%"  id="all_resultadoClinica" >
                <thead style="background-color: #f4f4f4; ">
                    <tr style="background-color: ">
                        <th>CUENTA</th>
                        <th>SALDO</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>


</div>


<script>

    function filtro(Element){

        var form = $(".contentFilterEstadoResultClinica");

        if(Element.hasClass('limpiar')){
            form
                .find('input').val(null)
                .trigger('change');
        }
        if(Element.hasClass('aplicar')){

        }

        estadoResultadoList();
    }

    function  exports(Element) {

        var parametros   = "?export=1";
        parametros      += "&emitido="+$("#emitido_gasto").val();

        // alert($("#cuentas_gastos").find(':selected').val());
        if(Element.hasClass('excel')){
            var excel = $DOCUMENTO_URL_HTTP+'/application/system/estado_clinica/export/export_estado_clinica.php'+parametros;
            window.open(excel, '_blank');
        }

    }
    
    function  estadoResultadoList() {

        var tableload = $("#all_resultadoClinica");
        var table = $("#all_resultadoClinica").DataTable({
            searching: false,
            "ordering":false,
            "serverSide": false,
            // responsive: true,
            destroy:true,
            scrollX: false,
            // scrollY: 500,
            lengthChange: false,
            fixedHeader: true,
            paging:false,
            processing: true,
            lengthMenu:[ 10 ],
            ajax:{
                url: $DOCUMENTO_URL_HTTP + '/application/system/estado_clinica/controller/controller.php',
                type:'POST',
                data: {
                    'ajaxSend'   : 'ajaxSend',
                    'accion'     : 'listresultadoClinica',
                    'emitido'    : $("#emitido").val(),
                },
                dataType:'json',
                cache: false,
                beforeSend: function(){
                    boxTableLoad(tableload, true);
                    $("#refresh_resultlist").find('i').addClass('btnSpinner');
                },
                complete: function(xhr, status) {
                    boxTableLoad(tableload, false);
                    $("#refresh_resultlist").find('i').removeClass('btnSpinner');

                },
            },

            createdRow: function (row, data, index) {
                var color = data['color'] || '';
                if(color!=''){
                    $(row).attr('style','background-color: '+color);
                    $(row).find('td:eq(0)').addClass('text-bold');
                    // console.log($(row));
                }
            },
            "language": {
                "sProcessing":     "Procesando...",
                "sLengthMenu":     "Mostrar _MENU_ registros",
                "sZeroRecords":    "No se encontraron resultados",
                "sEmptyTable":     "Ningún dato disponible en esta tabla",
                "sInfo":           "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                "sInfoEmpty":      "Mostrando registros del 0 al 0 de un total de 0 registros",
                "sInfoFiltered":   "(filtrado de un total de _MAX_ registros)",
                "sInfoPostFix":    "",
                "sSearch":         "Buscar:",
                "sUrl":            "",
                "sInfoThousands":  ",",
                "oPaginate": {
                    "sFirst":    "Primero",
                    "sLast":     "Último",
                    "sNext":     "Siguiente",
                    "sPrevious": "Anterior"
                },
                "oAria": {
                    "sSortAscending":  ": Activar para ordenar la columna de manera ascendente",
                    "sSortDescending": ": Activar para ordenar la columna de manera descendente"
                }
            },
        });

    }

    $(window).on('load', function () {

        estadoResultadoList();

        $("#emitido").daterangepicker({

            locale: {
                format: 'YYYY/MM/DD' ,
                daysOfWeek: [
                    "Dom",
                    "Lun",
                    "Mar",
                    "Mie",
                    "Jue",
                    "Vie",
                    "Sáb"
                ],
                monthNames: [
                    "Enero",
                    "Febrero",
                    "Marzo",
                    "Abril",
                    "Mayo",
                    "Junio",
                    "Julio",
                    "Agosto",
                    "Septiembre",
                    "Octubre",
                    "Noviembre",
                    "Diciembre"
                ],
            },

            startDate: moment().startOf('month'),
            endDate: moment().endOf('month'),
            ranges: {
                'Hoy': [moment(), moment()],
                'Ayer': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                'Últimos 7 Dias': [moment().subtract(6, 'days'), moment()],
                'Últimos 30 Dias': [moment().subtract(29, 'days'), moment()],
                'Mes Actual': [moment().startOf('month'), moment().endOf('month')],
                'Mes Pasado': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
                'Año Actual': [moment().startOf('year'), moment().endOf('year')],
                'Año Pasado': [moment().subtract(1, 'year').startOf('year'), moment().subtract(1, 'year').endOf('year')],
            }
        }).val(null);

        if(!ModulePermission('Reporte estado de clinica','consultar')){

            notificacion('Ud. No tiene permiso para consultar', 'error');
        }
    });

</script>