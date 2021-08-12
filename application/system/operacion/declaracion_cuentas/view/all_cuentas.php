
<?php

#breadcrumbs  -----------------------------------------------
$url_breadcrumbs = $_SERVER['REQUEST_URI'];
$titulo = "Cuentas";
$modulo = true;

?>


<div class="form-group col-xs-12 col-md-12 col-lg-12 no-padding">
    <div  class="form-group col-md-12 col-xs-12">
        <?= Breadcrumbs_Mod($titulo, $url_breadcrumbs, $modulo); ?>
        <label for="">LISTA DE COMPORTAMIENTOS</label>
        <ul class="list-inline" style="background-color: #f4f4f4; border-bottom: 0.6px solid #333333; padding: 3px; margin-left: 0px">
            <li><a href="#contentFilter" data-toggle="collapse" style="color: #333333" class="btnhover btn btn-sm " id="fitrar_document"> <b>  ▼ &nbsp;Filtrar <i></i> </b> </a></li>
            <li><a href="<?= DOL_HTTP.'/application/system/operacion/declaracion_cuentas/index.php?view=add_declarar_cuenta&key='.KEY_GLOB ?>" style="color: #333333" class="btnhover btn btn-sm  "> <b> Crear cuenta </b> </a></li>
        </ul>
    </div>

    <div class="form-group col-xs-12 col-md-12 col-lg-12 collapse" id="contentFilter" aria-expanded="true" style="margin-bottom: 0px">
        <div class="col-md-12 col-xs-12 col-lg-12" style="background-color: #f4f4f4; padding-top: 15px">
            <div class="form-group col-md-12 col-xs-12 col-lg-12"> <h3 class="no-margin"><span>Filtrar Cuentas</span></h3> </div>

            <div class="form-group col-xs-12 col-md-3 ">
                <label for="">Emitido</label>
                <div class="input-group form-group rango" style="margin: 0">
                    <input type="text" class="form-control filtroFecha  " readonly="" id="Fn_emitido" value="">
                    <span class="input-group-addon" style="border-radius: 0"><i class="fa fa-calendar"></i></span>
                </div>
            </div>

            <div class="form-group col-xs-12 col-md-3 ">
                <label for="">Tipo</label>
                <select name="Fn_tipo" id="Fn_tipo" class="form-control" style="width: 100%">
                    <option value=""></option>
                    <option value="0">Cuentas</option>
                    <option value="1">Cuenta Ahorro</option>
                    <option value="2">Cuenta Corriente</option>
                    <option value="3">Cuenta Caja</option>
                </select>
            </div>

            <div class="form-group col-xs-12 col-md-2 ">
                <label for="">Saldo</label>
                <input type="text" id="Fn_saldo" class="form-control">
            </div>

            <div class="form-group col-md-12 col-xs-12">
                <ul class="list-inline pull-right">
                    <li>  <button class="limpiar btn   btn-block  btn-default" id="LimpiarFn" style="float: right; padding: 10px"> &nbsp; &nbsp; Limpiar &nbsp; &nbsp;</button> </li>
                    <li>  <button class="aplicar btn   btn-block  btn-success" id="FiltrarFn" style="float: right; padding: 10px"> &nbsp;  &nbsp;Aplicar busqueda &nbsp;</button> </li>
                </ul>
            </div>

        </div>
    </div>

    <div class="form-group col-xs-12 col-md-12" style="margin-top: 10px">
        <div class="table-responsive">
            <table class="table table-condensed " width="100%"  id="all_Cuenta" >
                <thead style="background-color: #f4f4f4; ">
                    <tr>
                        <th>Nombre</th>
                        <th>Tipo</th>
                        <th>Operación</th>
                        <th>Número</th>
                        <th>Saldo</th>
                        <th>Estado</th>
                        <th></th>
                    </tr>
                </thead>
            </table>
        </div>

    </div>


    <!-- Modal -->
    <div class="modal fade" id="modal_modificar_name_cuenta" role="dialog">
        <div class="modal-dialog modal-sm" style="margin: 2% auto; width: 30%">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header modal-diseng">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title"><span>Editar Nombre cuenta</span></h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-xs-12 col-md-12">
                            <label for="">Agrege un nombre</label>
                            <textarea class="form-control" id="fn_editar_name_cuenta" maxlength="500" style="resize: vertical"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn text-bold " style="color: green" id="guardarEditNameCuen" onclick="">
                        Guardar
                        <span class="fa fa-refresh btnSpinner hide"></span>
                    </button>
                </div>
            </div>

        </div>
    </div>


</div>


<script>

    function lits_all_cuentas() {

        var TableCuenta = $("#all_Cuenta").DataTable({
            searching: false,
            destroy:true,
            "ordering":false,
            "serverSide": true,
            scrollX: false,
            // scrollY: 350,
            lengthChange: false,
            fixedHeader: true,
            paging:true,
            processing: true,
            lengthMenu:[ 10 ],
            "ajax":{
                "url": $DOCUMENTO_URL_HTTP + '/application/system/operacion/declaracion_cuentas/controller/controller.php',
                "type":'POST',
                "data": {
                    'ajaxSend'             : 'ajaxSend',
                    'accion'               : 'list_all_cuentas',
                    'datecc'               : $('#Fn_emitido').val(),
                    'tipo'                 : $('#Fn_tipo').find(':selected').val(),
                    'saldo'                : $('#Fn_saldo').val(),

                },
                "cache": false,
                "async": true,
                "dataType":'json',
                "complete": function(xhr, status) {

                }
            },
            "columnDefs":[
                {
                    "targets":6,
                    "render": function (data, type, row) {

                        //CUENTA PRESTACIÓN DE SERVICIOS
                        if(row['fetch']['codigo']=="UFJFU1RBQ0lPTl9ERV9TRVJWSUNJT1M="){
                            return "";
                        }


                        var menu = "<div class='dropdown pull-right'> ";

                            menu += "<input type='text' class='hidden link_pacientes_id' data-idpac='"+row['id_paciente']+"' >";
                            menu += "<div class='btn btnhover  btn-xs dropdown-toggle' type='button' data-toggle='dropdown' aria-expanded='false'> <i class='fa fa-ellipsis-v'></i> </div>";
                                menu += "<ul class='dropdown-menu'>";
                                    menu += "<li> <a href='#modal_modificar_name_cuenta' data-toggle='modal' style='cursor: pointer; ' data-idcuenta='"+row['fetch']['cuenta_id']+"' data-name='"+row['fetch']['name_acount']+"' > Editar nombre de cuenta </a> </li>";

                                    if(row['fetch']['estado'] == "E"){
                                        menu += "<li> <a href='#' style='cursor: pointer; ' data-idcuenta='"+row['fetch']['cuenta_id']+"' data-name='"+row['fetch']['name_acount']+"' onclick='desactivarActivarCuenta($(this),true)'> Activar  </a> </li>";
                                    }
                                    if(row['fetch']['estado'] == "A"){
                                        menu += "<li> <a href='#' style='cursor: pointer; ' data-idcuenta='"+row['fetch']['cuenta_id']+"' data-name='"+row['fetch']['name_acount']+"' onclick='desactivarActivarCuenta($(this),false)'> Eliminar  </a> </li>";
                                    }

                                menu += "</ul>";
                        menu += "</div>";
                        menu += "";
                        return menu;
                    }
                }
            ],
            "createdRow":function (row, data, index) {
                console.log(data);

                if(data['fetch']['system'] == 1){
                    $(row).css('font-weight', 'bolder')
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

        }).on( 'length.dt', function ( e, settings, len ) { // cambiar
            // boxTableLoad(ElemmentoContentload, true);
        }).on( 'page.dt', function ( e, settings, len ) { // cambiar
            // boxTableLoad(ElemmentoContentload, true);
        });

        new $.fn.dataTable.FixedHeader( TableCuenta,
            {
                // headerOffset: 50
            }
        );

    }


    $("#FiltrarFn").click(function () {

        lits_all_cuentas();
    });

    $("#LimpiarFn").click(function () {

        $('#Fn_saldo').val(null);
        $('#Fn_emitido').val(null);
        $('#Fn_tipo').val(null).trigger('change');
        lits_all_cuentas();
    });


    var cuenta_id = 0, name_edit_cuenta = "";
    $("#modal_modificar_name_cuenta").on("show.bs.modal", function (event) {
        cuenta_id = $(event.relatedTarget).prop('dataset').idcuenta;
        $("#fn_editar_name_cuenta").val($(event.relatedTarget).prop('dataset').name);
        name_edit_cuenta = $("#fn_editar_name_cuenta").val();
    });


    $("#guardarEditNameCuen").click(function () {

        if(!ModulePermission('Declarar Cuentas', 'modificar')){
             notificacion('Ud. No tiene permiso para realizar esta Operación', 'error');
            return false;
        }

        if(cuenta_id == 0 || name_edit_cuenta == ""){
            notificacion("Ocurrio un error parámetros de búsqueda\n Consulte con soporte");
            return false;
        }else{

            button_loadding( $("#guardarEditNameCuen"), true);
            $.ajax({
                url:$DOCUMENTO_URL_HTTP + '/application/system/operacion/declaracion_cuentas/controller/controller.php',
                type:'POST',
                data:{
                    'accion'    :'edit_name_cuenta',
                    'ajaxSend'  :'ajaxSend',
                    'cuenta_id' : cuenta_id,
                    'nom' : $("#fn_editar_name_cuenta").val(),
                    "nom_anterior" : name_edit_cuenta,
                },
                dataType:'json',
                cache:false,
                async:true,
                complete:function(xhr, status){
                    button_loadding( $("#guardarEditNameCuen"), false);
                },
                success:function (responce) {
                    if(responce['results']['error']!=""){
                        notificacion(responce['results']['error'], "error");
                    }else{
                       $("#modal_modificar_name_cuenta").modal("hide");
                       setTimeout(()=>{
                           notificacion("Actualizado", "success");
                               var DataTable = $("#all_Cuenta").DataTable();
                               DataTable.ajax.reload(null, false);
                       },500);
                    }
                }
            });

        }
    });

    function desactivarActivarCuenta(Elemento, ad) {

        if(!ModulePermission('Declarar Cuentas', 'eliminar')){
            notificacion('Ud. No tiene permiso para realizar esta Operación', 'error');
            return false;
        }

        var subaccion = "";
        var cuenta_id = Elemento.prop("dataset").idcuenta;
        var nom = Elemento.prop("dataset").name;

        if (ad == true) {
            subaccion = "activar";
        }
        if (ad == false) {
            subaccion = "desactivar";
        }

        if (cuenta_id == 0) {
            notificacion("Ocurrio un error parámetros de búsqueda\n Consulte con soporte", "error");
            return false;
        }


        $.ajax({
            url:$DOCUMENTO_URL_HTTP + '/application/system/operacion/declaracion_cuentas/controller/controller.php',
            type:'POST',
            data:{
                'accion'    :'actualizar_estados_cuenta',
                'ajaxSend'  :'ajaxSend',
                'subaccion' : subaccion,
                'cuenta_id' : cuenta_id,
                'nom' : nom
            },
            dataType:'json',
            complete:function(xhr, status){

            },
            success:function (resp) {
                console.log(resp);
                if(resp['results']['error'] != ""){
                    notificacion(resp['results']['error'], "error");
                }else{
                    notificacion("Actualizado", "success");
                    var DataTable = $("#all_Cuenta").DataTable();
                    DataTable.ajax.reload(null, false);
                }
            }
        });
    }

    $(window).on('onload', function () {
        boxloading($boxContentCuentasDeclaracion, true);
    });

    $(window).on('load', function () {

            $("#Fn_emitido").daterangepicker({

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

        $("#Fn_emitido").val(null);

        lits_all_cuentas();

        $("#Fn_tipo").select2({
            placeholder: 'Seleccione una opción',
            allowClear:true
        });

        boxloading($boxContentCuentasDeclaracion, false, 1000);

    });

</script>