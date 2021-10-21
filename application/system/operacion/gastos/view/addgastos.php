<?php

$id = GETPOST('id');

?>


<div class="form-group col-xs-12 col-md-12 col-lg-12 no-padding">
    <div  class="form-group col-md-12 col-xs-12">

    </div>


    <div class="form-group col-md-10 col-lg-11 col-centered">
        <div class="form-horizontal">
            <div id="form_gastos_clinicos">

                <div class="form-group">
                    <label for="" class="control-label col-sm-3">Cuenta Gasto:</label>
                    <div class="col-sm-7">
                        <select name="cuentas_gastos" id="cuentas_gastos" class="form-control" style="width: 100%" onchange="FormValidationGastos()">
                            <option value=""></option>
                            <?php
                                $sql_g = "select rowid, n_cuenta, name_acount, description from tab_ope_declare_cuentas where tipo_operacion = 'GASTOS' ";
                                $result_g = $db->query($sql_g);
                                if($result_g){
                                    if($result_g->rowCount()>0){
                                        $cuentas = $result_g->fetchAll(PDO::FETCH_ASSOC);
                                        foreach ($cuentas as $item){
                                            print '<option value="'.$item['rowid'].'">'.$item['n_cuenta'].' '.$item['name_acount'].'</option>';
                                        }
                                    }
                                }
                            ?>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="" class="control-label col-sm-3">Categoria:</label>
                    <div class="col-sm-7">
                        <div class="input-group">
                            <select name="categoria_gastos" id="categoria_gastos" class="form-control" style="width: 100%" onchange="FormValidationGastos();">
                                <option value=""></option>
                            </select>
                            <span class="input-group-addon" style="cursor: pointer" data-toggle="modal" data-target="#CategoriaGastosModal"><i class="fa fa-plus"></i></span>
                            <span class="input-group-addon disabled_link3" style="cursor: pointer"  onclick="deleteGastosClinicos()"><i class="fa fa-minus"></i></span>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="" class="control-label col-sm-3">Detalle:</label>
                    <div class="col-sm-7">
                        <textarea name="detalle_gastos" id="detalle_gastos" cols="30" rows="3" class="form-control" style="resize: vertical" onkeyup="FormValidationGastos();"></textarea>
                    </div>
                </div>

                <div class="form-group">
                    <label for="" class="control-label col-sm-3">Monto $:</label>
                    <div class="col-sm-7">
                        <input type="text" name="monto_gastos" id="monto_gastos" class="form-control" value="" onkeyup="FormValidationGastos();">
                    </div>
                </div>

                <div class="form-group">
                    <label for="" class="control-label col-sm-3">Medio de Pago:</label>
                    <div class="col-sm-7">
                        <select name="medio_pago_gastos" id="medio_pago_gastos" class="form-control" onchange="FormValidationGastos();" style="width: 100%">
                            <option value=""></option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="" class="control-label col-sm-3">Fecha Factura:</label>
                    <div class="col-sm-7">
                        <div class="input-group form-group rango" style="margin: 0">
                            <input type="text" class="form-control dateGatos" readonly="" id="date_facture" value="" style="font-size: small" onchange="FormValidationGastos();">
                            <span class="input-group-addon" style="border-radius: 0"><i class="fa fa-calendar"></i></span>
                        </div>
                    </div>
                </div>

                <div class="form-group date_pago_content">
                    <label for="" class="control-label col-sm-3">Fecha Pago:</label>
                    <div class="col-sm-7">
                        <label for="" style="color: #0866a5"><i class="fa fa-info-circle"> <small style="color: #0866a5">
                                    Fecha de pago:
                                        El gasto se genera dentro de la fecha de Pago seleccionada
                                </small> </i></label>
                        <div class="input-group form-group rango" style="margin: 0">
                            <input type="text" class="form-control dateGatos" readonly="" id="date_pago" value="" style="font-size: small" onchange="FormValidationGastos();">
                            <span class="input-group-addon" style="border-radius: 0"><i class="fa fa-calendar"></i></span>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="" class="control-label col-sm-3">(opcional) Asociar a Caja:</label>
                    <div class="col-sm-7">
                        <label for="" style="color: #0866a5"><i class="fa fa-info-circle"> <small style="color: #0866a5">
                                    Asociar gasto a una caja clínica Abierta. El gasto se genera cuando se haya cerrado caja
                                </small> </i></label>
                        <select name="asociar_caja" id="asociar_caja" class="form-control" style="width: 100%">
                            <option value=""></option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="" class="control-label col-sm-1">&nbsp;</label>
                    <div class="col-sm-9">
                        <button class="btn btnhover pull-right" style="font-weight: bolder; color: green; " id="guardarGastos">
                            Guardar
                            <span class="fa fa-refresh btnSpinner hide"></span>
                        </button>
                        <button class="btn btnhover pull-right" style="font-weight: bolder;color: green;margin-right: 10px;" id="GenerarGastos">
                            Generar Gasto
                            <span class="fa fa-refresh btnSpinner hide"></span>
                        </button>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>


<!--Modal Agregar Modificar Categoria Gatos-->
<div class="modal fade" id="CategoriaGastosModal" data-backdrop="static" data-keyboard="false" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header modal-diseng">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><span>Crear Categoria</span> <span> nuevo</span> </h4>
            </div>
            <div class="modal-body">
                <div style="padding: 10px">
                    <div class="form-group">
                        <small style="color:#0866a5; font-weight: bolder "> <i class="fa fa-info-circle"></i> Crear Categoria de Gastos Clinicos</small>
                    </div>
                    <div class="form-group">
                        <label for="">Nombre</label>
                        <input type="text" id="clasificacion_nomb_modal" class="form-control input-sm">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn" style="font-weight: bold; color: green" onclick="GuardarCategoria()" id="guardarCategoria">Guardar
                    <span class="fa fa-refresh btnSpinner hide"></span>
                </button>
            </div>
        </div>

    </div>
</div>


<script>

    var idGasto = "<?= (isset($_GET['id'])?$_GET['id']:"")?>";

    var FormValidationGastos = function () {

        var Errores         = [];
        var Categoria       = $("#categoria_gastos");
        var detalle_gastos  = $("#detalle_gastos");
        var date_facture    = $("#date_facture");
        var date_pago       = $("#date_pago");
        var asociar_caja    = $("#asociar_caja");
        var monto_gastos    = $("#monto_gastos");
        var cuentas_gastos  = $("#cuentas_gastos");
        var medio_pago_gastos  = $("#medio_pago_gastos")

        if(medio_pago_gastos.find(":selected").val()==""){
            Errores.push({
                document: medio_pago_gastos,
                text: "Campo obligatorio"
            });
        }
        if(monto_gastos.val()==""){
            Errores.push({
                document: monto_gastos,
                text: "Campo obligatorio"
            });
        }else{
            if(parseFloat(monto_gastos.val())==0){
                Errores.push({
                    document: monto_gastos,
                    text: "No puede ser 0"
                });
            }
        }
        if(cuentas_gastos.find(":selected").val()==""){
            Errores.push({
                document: cuentas_gastos,
                text: "Campo obligatorio"
            });
        }
        if(Categoria.find(":selected").val()==""){
            Errores.push({
                document: Categoria,
                text: "Campo obligatorio"
            });
        }if(detalle_gastos.val()==""){
            Errores.push({
                document: detalle_gastos,
                text: "Campo obligatorio"
            });
        }if(date_facture.val()==""){
            Errores.push({
                document: date_facture,
                text: "Campo obligatorio"
            });
        }if(asociar_caja.find(":selected").val()==""){
            if(date_pago.val()==""){
                Errores.push({
                    document: date_pago,
                    text: "Campo obligatorio"
                });
            }
        }else{

        }

        if($(".err_msg_gastos").length>0)
            $(".err_msg_gastos").remove();

        for (var i=0; i<= Errores.length -1; i++){
                var element = Errores[i].document;
                var text    = Errores[i].text;
                var msg     = document.createElement('small');

                console.log(element);
                $(msg).addClass('err_msg_gastos').text(text).css('color','red');
                element.parents('.col-sm-7').append($(msg));
        }
        if(Errores.length>0)
            return false;
        else
            return true;
    };

    function fetchGastos(gastos_id=""){
        boxloading($boxContentGastos, true);
        var url = $DOCUMENTO_URL_HTTP + '/application/system/operacion/gastos/controller/controller.php';
        $.get(url, { accion:'fetchGastos', ajaxSend:'ajaxSend' } )
            .done(function (data) {
                var fetch = $.parseJSON(data);
                var div = $("#form_gastos_clinicos");
                var select = div.find("#categoria_gastos");
                select.empty();
                select.append('<option></option>');

                select.select2({
                    placeholder:'buscar Categoria',
                    language:languageEs,
                    allowClear: true,
                    data: fetch['fetch']
                });


                if(gastos_id != ""){
                    select.val(gastos_id).trigger('change');
                }

                console.log(fetch);
            })
            .always(function () {
                boxloading($boxContentGastos, false, 1000);
            });
    }

    function GuardarCategoria(){

        button_loadding($("#guardarCategoria"), true);
        var id  = $('[name="categoria_gastos"]').find(":selected").val();
        var nom = $("#clasificacion_nomb_modal").val();
        var url = $DOCUMENTO_URL_HTTP + '/application/system/operacion/gastos/controller/controller.php';
        $.get(url, { accion:'GuardarCategoria', ajaxSend:'ajaxSend', id:id, nom: nom } )
            .done(function (data) {
                var fetch = $.parseJSON(data);
                if(fetch.error == ""){
                    fetchGastos(fetch.return_id);
                    $("#CategoriaGastosModal").modal('hide');
                }else{
                    notificacion(fetch.error, 'error');
                }
            })
            .always(function () {
                button_loadding($("#guardarCategoria"), false);
            });
    }

    var deleteGastosClinicos = function () {
        var id = $('[name="categoria_gastos"]').find(':selected').val();
        var object = {
            id: id,
            callback: function () {
                boxloading($boxContentGastos, true);
                var paramtrs = {
                    accion   : 'delete_categ_gastos',
                    ajaxSend : 'ajaxSend',
                    id : $('[name="categoria_gastos"]').find(':selected').val()};
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
        notificacionSIoNO("Eliminar Gasto Clinico","La categoria Gasto Clinico no debe estar asociado a ningun Documento Anteior", object);
    };

    $("#CategoriaGastosModal").on('show.bs.modal', function () {

        var modal = $(this);
        var id = $('[name="categoria_gastos"]').find(":selected").val();
        if(id==""){//nuevo
            modal
                .find("h4.modal-title span:eq(1)").text('nuevo');
            modal
                .find('#clasificacion_nomb_modal')
                .val(null);
        }else{//modificar
            modal
                .find("h4.modal-title span:eq(1)").text('modificar');

            var object = $('[name="categoria_gastos"]').select2('data');
            modal
                .find('#clasificacion_nomb_modal')
                .val(object[0].text);
        }
    });

    $('[name="categoria_gastos"]').change(function () {
        if($(this).find(":selected").val()!="")
            $(this).parent().find('span.input-group-addon').eq(1).removeClass('disabled_link3');
        else
            $(this).parent().find('span.input-group-addon').eq(1).addClass('disabled_link3');
    });

    $('[name="asociar_caja"]').change(function () {
        if( $('[name="asociar_caja"]').find(':selected').val() ==""){
            $(".date_pago_content").removeClass('hide');
            $("#GenerarGastos").removeClass('disabled_link3').attr('disabled', false);
        }else{
            $(".date_pago_content").addClass('hide');
            $("#GenerarGastos").addClass('disabled_link3').attr('disabled', true);
        }
        $(".date_pago_content").find('#date_pago').val(null);

    });

    $("#guardarGastos").click(function () {
        if(!FormValidationGastos()){
            return false;
        }

        if($("#date_pago").val()==""){
            notificacion('Debe selecionar una Fecha de Pago')
        }

        var btn = $("#guardarGastos");
        Guardar(btn, idGasto);
    });

    $("#GenerarGastos").click(function () {
        if(!FormValidationGastos()){
            return false;
        }

        if($('#date_pago').val()==""){
            notificacion('Debe selecionar una Fecha de Pago', 'question');
            return false;
        }

        var btn = $("#guardarGastos");
        Guardar(btn, idGasto, 'G'); //se genera el gasto automatico
    });


    function Guardar(btn, idGasto, otra_accion = '') {

        var paramtrs = {
            accion              : 'GuardarGastosClinicos',
            ajaxSend            : 'ajaxSend',
            id                  : idGasto,
            categoria           : $("#categoria_gastos").find(":selected").val(),
            detalleGastos       : $("#detalle_gastos").val(),
            date_facture        : $("#date_facture").val(),
            date_pago           : $("#date_pago").val(),
            asociar_caja        : $("#asociar_caja").find(":selected").val(),
            monto_gastos        : $("#monto_gastos").val(),
            medio_pago_gastos   : $("#medio_pago_gastos").find(':selected').val(),
            fk_acount           : $("#cuentas_gastos").find(':selected').val(),
            otra_accion         : otra_accion
        };

        $.ajax({
            url: $DOCUMENTO_URL_HTTP + '/application/system/operacion/gastos/controller/controller.php',
            delay:1000,
            type: 'POST',
            data: paramtrs ,
            async:true,
            cache:false,
            dataType:'json',
            beforeSend: function(){
                boxloading($boxContentGastos, true);
                button_loadding(btn, true);
            },
            complete: function (xhr, status) {
                button_loadding(btn, false);
                boxloading($boxContentGastos, false, 1000);
            },
            success: function (response) {
                if(response.error == ""){
                    notificacion('Información Actualizada', 'success');
                    setTimeout(()=>{
                        window.location = $DOCUMENTO_URL_HTTP+"/application/system/operacion/gastos/index.php?view=listgatos&key="+$keyGlobal;
                    }, 300);
                }else{
                    notificacion(response.error, 'error');
                }
            }
        })
    }
    
    function typePayement(){

        $("#t_pagos").empty().html('<option value=""></option>');

        var url = $DOCUMENTO_URL_HTTP + '/application/system/pacientes/pacientes_admin/pagos_pacientes/controller_pagos/controller_pag.php';
        $.get(url , {'accion':'fetchTiposPagos','ajaxSend':'ajaxSend'}, function(data) {
            var respuesta = $.parseJSON(data);
            if(respuesta['object'].length > 0){
                $('[name="medio_pago_gastos"]').select2({
                    placeholder:'Seleccione un tipo de pago',
                    allowClear: true,
                    language:'es',
                    data: respuesta['object'],
                    dropdownParent: $('.wrapper'),
                });
            }
        });

    }

    $(document).ready(function () {

        typePayement();
    });

    $(window).on('load', function () {

        var locales = {
            format: 'YYYY/MM/DD' ,
                daysOfWeek: ["Dom", "Lun", "Mar", "Mie", "Jue", "Vie", "Sáb"],
                monthNames: ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"],
        };

        fetchGastos();

        $('[name="cuentas_gastos"]').select2({
            placeholder: 'selecione una cuenta Gastos',
            language: languageEs,
            allowClear:false,
        });

        $("#asociar_caja").select2({
            placeholder: 'buscar una cuenta caja',
            language: languageEs,
            minimumInputLength: 2 ,
            allowClear:true,
            delay: 500,
            dropdownParent: $('body'),
            ajax:{
                url: $DOCUMENTO_URL_HTTP + '/application/system/operacion/gastos/controller/controller.php',
                type:'POST',
                async:true,
                cache:false,
                dataType:'json',
                data: function (params) {
                    var query = {
                        'ajaxSend'  : 'ajaxSend',
                        'accion'    : 'buscar_caja_clinica_ape',
                        'buscar'    : params.term
                    };
                    return query;
                },
                delay: 500,
                processResults: function (results) {
                    // console.log(results);
                    return results;
                }
            }
        });


        $('#date_pago').daterangepicker({
            locale: locales,
            singleDatePicker: true,
            showDropdowns: true,
            autoclose: false,
            // "opens": "center",
            "drops": "up",
            pickerPosition: "bottom-left"
        }).val(null);


        $('#date_facture').daterangepicker({
            locale: locales,
            singleDatePicker: true,
            showDropdowns: true,
            autoclose: false,
            // "opens": "center",
            "drops": "auto",
            pickerPosition: "bottom-left"
        }).val(null);


        $('[name="monto_gastos"]').maskMoney({precision:2,thousands:'', decimal:'.',allowZero:true,allowNegative:true, defaultZero:true,allowEmpty: true});
    });

</script>
