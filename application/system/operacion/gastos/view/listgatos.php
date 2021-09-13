
<?php

#breadcrumbs  -----------------------------------------------
$url_breadcrumbs = $_SERVER['REQUEST_URI'];
$titulo = "Gastos";
$modulo = true;


?>

<div class="form-group col-xs-12 col-md-12 col-lg-12 no-padding">
    <div  class="form-group col-md-12 col-xs-12">
        <?= Breadcrumbs_Mod($titulo, $url_breadcrumbs, $modulo); ?>
        <label for="">LISTA DE COMPORTAMIENTOS</label>
        <ul class="list-inline" style="background-color: #f4f4f4; border-bottom: 0.6px solid #333333; padding: 3px; margin-left: 0px">
            <li><a href="#contentFilter" data-toggle="collapse" style="color: #333333" class="btnhover btn btn-sm " id="fitrar_document"> <b>  ▼ &nbsp;Filtrar <i></i> </b> </a></li>
            <li><a href="<?= DOL_HTTP.'/application/system/operacion/gastos/index.php?view=addgastos&key='.KEY_GLOB ?>" style="color: #333333" class="btnhover btn btn-sm  "> <b> Crear Gasto </b> </a></li>
        </ul>
    </div>

    <div class="form-group col-xs-12 col-md-12 col-lg-12 collapse contentFilterGastos" id="contentFilter" aria-expanded="true" style="margin-bottom: 0px">
        <div class="col-md-12 col-xs-12 col-lg-12" style="background-color: #f4f4f4; padding-top: 15px">
            <div class="form-group col-md-12 col-xs-12 col-lg-12"> <h3 class="no-margin"><span>Filtrar Gatos</span></h3> </div>

            <div class="form-group col-md-4 col-xs-12 col-lg-3">
                <label for="">Emitido</label>
                <div class="input-group form-group rango" style="margin: 0">
                    <input type="text" class="form-control dateGasto" readonly="" id="emitido_gasto" value="" style="font-size: small">
                    <span class="input-group-addon" style="border-radius: 0"><i class="fa fa-calendar"></i></span>
                </div>
            </div>

            <div class="form-group col-md-4 col-xs-12 col-lg-3">
                <label for="">Factura</label>
                <div class="input-group form-group rango" style="margin: 0">
                    <input type="text" class="form-control dateGasto" readonly="" id="emitido_factura" value="" style="font-size: small">
                    <span class="input-group-addon" style="border-radius: 0"><i class="fa fa-calendar"></i></span>
                </div>
            </div>

            <div class="form-group col-md-4 col-xs-12 col-lg-3">
                <label for="">Pago</label>
                <div class="input-group form-group rango" style="margin: 0">
                    <input type="text" class="form-control dateGasto" readonly="" id="emitido_pago_gasto" value="" style="font-size: small">
                    <span class="input-group-addon" style="border-radius: 0"><i class="fa fa-calendar"></i></span>
                </div>
            </div>

            <div class="form-group col-md-3 col-xs-12 col-lg-3">
                <label for="">Estado</label>
                <select name="estado_gasto" id="estado_gasto" class="form-control" style="width: 100%">
                    <option value=""></option>
                    <option value="A">GENERADO</option>
                    <option value="E">ANULADO</option>
                    <option value="P">PENDIENTE</option>
                </select>
            </div>

            <div class="form-group col-md-5 col-xs-12 col-lg-5">
                <label for="">Gastos asociado a cajas clinicas</label>
                <select name="cuentas_gastos" id="cuentas_gastos" class="form-control" style="width: 100%">
                    <option value=""></option>
                    <?php
                        $sql_g = "select rowid, n_cuenta, name_acount, description, to_caja_direccion from tab_ope_declare_cuentas where to_caja = 1 ";
                        $result_g = $db->query($sql_g);
                        if($result_g){
                            if($result_g->rowCount()>0){
                                $cuentas = $result_g->fetchAll(PDO::FETCH_ASSOC);
                                foreach ($cuentas as $item){
                                    print '<option value="'.$item['rowid'].'">'.$item['n_cuenta'].' '.$item['name_acount'].' '. $item['to_caja_direccion'] .' </option>';
                                }
                            }
                        }
                    ?>
                </select>
            </div>

            <div class="form-group col-md-12 col-xs-12">
                <ul class="list-inline pull-right">
                    <li>  <button class="limpiar btn   btn-block  btn-default" id="limpiarGastosFiltros" style="float: right; padding: 10px"> &nbsp; &nbsp; Limpiar &nbsp; &nbsp;</button> </li>
                    <li>  <button class="aplicar btn   btn-block  btn-success" id="aplicarGastosFiltros" style="float: right; padding: 10px"> &nbsp;  &nbsp;Aplicar busqueda &nbsp;</button> </li>
                </ul>
            </div>


        </div>
    </div>

    <div class="form-group col-xs-12 col-md-12 " style="margin-top: 15px" >
        <div class="table-responsive">
            <table class="table table-condensed table-hover" width="100%"  id="all_Cuenta_de_aperturas" >
                <thead style="background-color: #f4f4f4; ">
                    <tr>
                        <th width="8%">Emitido</th>
                        <th width="20%">Categoria</th>
                        <th width="30%">Detalle</th>
                        <th width="8%">Fecha de Factura</th>
                        <th width="8%">Fecha de Pago</th>
                        <th width="8%">Monto</th>
                        <th width="15%">Estado</th>
                        <th width="5%"></th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

</div>

<script>

    function gastosList(){
        var tableload = $("#all_Cuenta_de_aperturas");
        boxTableLoad(tableload, false);
        var table = $("#all_Cuenta_de_aperturas").DataTable({
            searching: false,
            "ordering":false,
            "serverSide": true,
            // responsive: true,
            destroy:true,
            scrollX: false,
            // scrollY: 500,
            lengthChange: false,
            fixedHeader: true,
            paging:true,
            processing: true,
            lengthMenu:[ 10 ],
            ajax:{
                url: $DOCUMENTO_URL_HTTP + '/application/system/operacion/gastos/controller/controller.php',
                type:'POST',
                data: {
                    'ajaxSend'   : 'ajaxSend',
                    'accion'     : 'listCuentasGastos',
                    'emitido'    : $("#emitido_gasto").val(),
                    'facture'    : $("#emitido_factura").val(),
                    'pago'       : $("#emitido_pago_gasto").val(),
                    'estado'     : $("#estado_gasto").find(':selected').val(),
                    'cuenta'     : $("#cuentas_gastos").find(':selected').val(),
                },
                dataType:'json',
                cache: false,
                complete: function(xhr, status) {
                    boxTableLoad(tableload, false);
                },

            },
            columnDefs:[
                {
                    targets:7,
                    render: function (data, type, full, meta) {

                        var Generado = "";
                        var disabled = "";

                        //generado
                        if(full['estado'] == 'G'){
                            disabled = 'disabled_link3';
                        }

                        //Anulado
                        if(full['estado'] == 'E'){
                            disabled = 'disabled_link3';
                        }

                        //Pendiente
                        if(full['estado'] != 'E' && full['estado'] != 'G' && full['to_caja'] == 0){
                            Generado = "<li>   <a href='#' onclick='GenerarGasto("+full['id']+")' >Generar</a> </li>";
                        }

                        var btn = "" +
                            "<div class='dropdown pull-right'>";
                            btn += "<button class=\"btn btnhover  btn-xs dropdown-toggle\" type=\"button\" data-toggle=\"dropdown\" style=\"height: 100%\" aria-expanded=\"false\"> <i class=\"fa fa-ellipsis-v\"></i> </button>";
                            btn += "<ul class='dropdown-menu' style='z-index: +2000'>";
                                btn += "<li>   <a href='#' onclick='anulargasto("+full['id']+")'  class=' "+disabled+"  '>Anular</a> </li>";
                                btn += Generado;
                            btn += "</ul>";
                        btn += "</div>";


                        return btn;

                    }
                }
            ],
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

    var anulargasto = function (idanular) {
        var id = idanular;
        var object = {
            id: id,
            callback: function () {
                boxloading($boxContentGastos, true);
                var paramtrs = {
                    accion   : 'anularGasto',
                    ajaxSend : 'ajaxSend',
                    id : idanular
                };
                $.ajax({
                    url: $DOCUMENTO_URL_HTTP + '/application/system/operacion/gastos/controller/controller.php',
                    delay:1000,
                    type: 'POST',
                    data: paramtrs ,
                    async:true,
                    cache:false,
                    dataType:'json',
                    complete: function(xhr, status){
                        boxloading($boxContentGastos, false, 1000);
                    },
                    success:function (response) {
                        boxloading($boxContentGastos, false, 1000);
                        if(response.error!=""){
                            notificacion(response.error, 'error');
                        }else{
                            notificacion("Información Actualizado", 'success');
                            fetchGastos();
                        }
                    }
                });
            }
        };
        notificacionSIoNO("Anular Gasto","No puede anular un gasto si este se encuentra asociado a una caja cerrada", object);
    };

    var GenerarGasto = function (idGenerar) {
        var id = idGenerar;
        var object = {
            id: id,
            callback: function () {
                boxloading($boxContentGastos, true);
                var paramtrs = {
                    accion   : 'GenerarGasto',
                    ajaxSend : 'ajaxSend',
                    id       : idGenerar
                };
                $.ajax({
                    url: $DOCUMENTO_URL_HTTP + '/application/system/operacion/gastos/controller/controller.php',
                    delay:1000,
                    type: 'POST',
                    data: paramtrs ,
                    async:true,
                    cache:false,
                    dataType:'json',
                    complete: function(xhr, status){
                        boxloading($boxContentGastos, false, 1000);
                    },
                    success:function (response) {
                        boxloading($boxContentGastos, false, 1000);
                        if(response.error!=""){
                            notificacion(response.error, 'error');
                        }else{
                            notificacion("Información Actualizado", 'success');
                            fetchGastos();
                        }
                    }
                });
            }
        };
        notificacionSIoNO("Desea Generar el gasto ? ","Una vez generado el gasto no padra Anularlo", object);
    };

    function datePagoGasto(value, id){
        $('.dateGasto').val(null);
        $(id).val(value);
    }

    $("#emitido_gasto").change(function () {
        if($(this).val()!=""){
            var id = "#"+$(this).attr('id');
            var value = $(this).val();
            datePagoGasto(value, id);
        }
    });

    $("#emitido_factura").change(function () {
        if($(this).val()!=""){
            var id = "#"+$(this).attr('id');
            var value = $(this).val();
            datePagoGasto(value, id);
        }
    });

    $("#emitido_pago_gasto").change(function () {
        if($(this).val()!=""){
            var id = "#"+$(this).attr('id');
            var value = $(this).val();
            datePagoGasto(value, id);
        }
    });

    $("#limpiarGastosFiltros").click(function () {

        $(".contentFilterGastos")
            .find('input').val(null);
        $(".contentFilterGastos")
            .find('select').val(null).trigger('change');

        gastosList();
    });

    $("#aplicarGastosFiltros").click(function () {
        gastosList();
    });

    $(window).on("load", function () {

        gastosList();

        $(".dateGasto").daterangepicker({

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

        $("#cuentas_gastos").select2({
            placeholder: "Seleccione una opción",
            allowClear: true,
            language:languageEs
        });

        $("#estado_gasto").select2({
            placeholder: "Seleccione una opción",
            allowClear: true,
            language: languageEs
        });

    });

</script>