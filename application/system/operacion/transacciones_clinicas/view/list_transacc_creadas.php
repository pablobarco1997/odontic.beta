
<?php

$url_breadcrumbs = $_SERVER['REQUEST_URI'];
$titulo = "Transacciones creadas";
$modulo = true;


?>


<div class="form-group col-md-12 col-xs-12">
    <?= Breadcrumbs_Mod($titulo, $url_breadcrumbs, $modulo); ?>
    <label for="">LISTA DE COMPORTAMIENTOS</label>
    <ul class="list-inline" style="background-color: #f4f4f4; border-bottom: 0.6px solid #333333; padding: 3px; margin-left: 0px">
        <li><a href="#contentFilter" data-toggle="collapse" style="color: #333333" class="btnhover btn btn-sm " id="fitrar_document"> <b>  ▼ &nbsp;Filtrar <i></i> </b> </a></li>
        <li><a href="#" style="color: #333333" class="btnhover btn btn-sm " id="crearTransaccion" onclick="to_crear_trans()"> <b> Crear Transaccion <i></i> </b> </a></li>
        <li><a id="refresh_list_transsa" class="btn btn-sm" style="color: black" title="refresh" onclick="transaccionClinica_list()">
                <span class="fa fa-refresh"></span>
            </a>
        </li>
    </ul>
</div>


<div class="form-group col-xs-12 col-md-12 col-lg-12 collapse " id="contentFilter" aria-expanded="true" style="margin-bottom: 0px;">
    <div class="col-md-12 col-xs-12 col-lg-12" style="background-color: #f4f4f4; padding-top: 15px">
        <div class="form-group col-md-12 col-xs-12 col-lg-12"> <h3 class="no-margin"><span>Filtrar Transacciones Clinicas Creadas
    </span></h3> </div>


        <div class="form-group col-xs-12 col-md-4 ">
            <label for="">Emitido</label>
            <div class="input-group form-group rango" style="margin: 0">
                <input type="text" class="form-control " readonly="" id="date_emitido" name="date_emitido" value="">
                <span class="input-group-addon" style="border-radius: 0"><i class="fa fa-calendar"></i></span>
            </div>
        </div>

        <div class="form-group col-xs-12 col-md-4 ">
            <label for="">valor</label>
            <input type="text" class="form-control "  id="valor_trasn" name="valor_trasn" value="">
        </div>

        <div class="form-group col-xs-12 col-md-4 ">
            <label for="">Cuenta</label>
            <select name="cuenta_trasn" id="cuenta_trasn" class="form-control" style="width: 100%">
                <option value=""></option>
                <?php
                    $resultp = $db->query("select rowid ,  concat(n_cuenta,' ', name_acount) nom from tab_ope_declare_cuentas where estado = 'A';");
                    if($resultp){
                        if($resultp->rowCount()>0){
                            $formas = $resultp->fetchAll(PDO::FETCH_ASSOC);
                            foreach ($formas as $value){
                                print "<option value='".$value['rowid']."'>".$value['nom']."</option>";
                            }
                        }
                    }
                ?>
            </select>
        </div>

        <div class="form-group col-xs-12 col-md-3 ">
            <label for="">Operación</label>
            <select name="formapgs_tc" id="formapgs_tc" class="form-control" style="width: 100%">
                <option value=""></option>
                    <?php
                        $resultp = $db->query("select * from tab_bank_operacion");
                        if($resultp){
                            if($resultp->rowCount()>0){
                                $formas = $resultp->fetchAll(PDO::FETCH_ASSOC);
                                foreach ($formas as $value){
                                    print "<option value='".$value['rowid']."'>".$value['nom']."</option>";
                                }
                            }
                        }
                    ?>
            </select>
        </div>

        <div class="form-group col-md-12 col-xs-12">
            <ul class="list-inline pull-right">
                <li>  <button class="limpiar btn   btn-block  btn-default" id="limpiar_tc" onclick="applicarBusq($(this))" style="float: right; padding: 10px"> &nbsp; &nbsp; Limpiar &nbsp; &nbsp;</button> </li>
                <li>  <button class="aplicar btn   btn-block  btn-success" id="" onclick="applicarBusq($(this))" style="float: right; padding: 10px"> &nbsp;  &nbsp;Aplicar busqueda &nbsp;</button> </li>
            </ul>
        </div>

    </div>
</div>


<div class="form-group col-md-12 col-xs-12 " style="margin-top: 15px" >
    <div class="table-responsive">
        <table class="table table-condensed table-hover" id="transaccion_clinicas" width="100%">
            <thead>
                <tr style="background-color: #f4f4f4">
                    <th width="10%">Movimiento</th>
                    <th width="15%">Cuenta</th>
                    <th width="30%">Descripción</th>
                    <th width="3%">valor</th>
                </tr>
            </thead>
        </table>
    </div>
</div>

<script>

    
    function applicarBusq(Element) {

        if(Element.hasClass('aplicar')){
            transaccionClinica_list();
        }
        if(Element.hasClass('limpiar')){

            $('#contentFilter')
                .find('input,select')
                .val(null)
                .trigger('change');

            transaccionClinica_list();
        }

    }
    
    function transaccionClinica_list() {
        var table = $("#transaccion_clinicas").DataTable({
            searching: false,
            "ordering":false,
            "serverSide": true,
            destroy:true,
            scrollX: false,
            lengthChange: false,
            fixedHeader: true,
            paging:true,
            processing: true,
            lengthMenu:[ 10 ],
            ajax:{
                "url": $DOCUMENTO_URL_HTTP + '/application/system/operacion/transacciones_clinicas/controller/controller.php',
                "type":'POST',
                "data": {
                    'ajaxSend'             : 'ajaxSend',
                    'accion'               : 'list_transacciones',
                    'emitido_date'         : $('#date_emitido').val(),
                    'valor_trasn'          : $('#valor_trasn').val(),
                    'cuenta'               : $('#cuenta_trasn').find(':selected').val(),
                    'formap'               : $("#formapgs_tc").find(':selected').val(),
                },
                "dataType":'json',
                "cache": false,
                "beforeSend": function () {
                    boxTableLoad($("#transaccion_clinicas"), true);
                    $("#refresh_list_transsa").find('span').addClass('btnSpinner');
                },
                "complete": function(xhr, status) {
                    boxTableLoad($("#transaccion_clinicas"), false);
                    $("#refresh_list_transsa").find('span').removeClass('btnSpinner');

                    if(xhr.responseJSON.errorTable != ""){
                        notificacion(xhr.responseJSON.errorTable, 'error');
                    }
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


    function to_crear_trans(){

        var objectLoad = {
            onload:function () {
                boxloading($boxContenTransacciones, true);
            },
            offload: function () {
                boxloading($boxContenTransacciones, false, 1000);
            }
        };

        if(!ModulePermission('Transacciones Clinicas','agregar', objectLoad)){
            notificacion('Ud. No tiene permiso para realizar esta Operación','error');
            return false;
        }

        window.location = $DOCUMENTO_URL_HTTP+"/application/system/operacion/transacciones_clinicas?view=crear_transaccion&key="+$keyGlobal+"&crear=1";

    }

    window.onload =  boxloading($boxContenTransacciones, true);

    $(window).on("load", function () {
        transaccionClinica_list();
        boxloading($boxContenTransacciones, false, 1000);


        $("#date_emitido").daterangepicker({

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
        });

        $('.rango span').click(function() {
            $(this).parent().find('input').click();
        });

        $("#date_emitido").val(null);

        $("#cuenta_trasn").select2({
            placeholder: 'Seleccione una opción',
            allowClear:true,
            language:languageEs
        });
        $("#formapgs_tc").select2({
            placeholder: 'Seleccione una opción',
            allowClear:true,
            language:languageEs
        });

    });
</script>