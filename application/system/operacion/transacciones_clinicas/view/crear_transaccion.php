<?php
$url_breadcrumbs = $_SERVER['REQUEST_URI'];
$titulo = "nueva Transacciones Clinica";
$modulo = false;

if(!PermitsModule('Transacciones Clinicas', 'agregar')){
    errorAccessoDenegado();
}

?>


<div class="form-group col-md-12 col-xs-12 " style="margin-top: 15px" >
    <div class="margin-bottom" style="display: block">
        <?= Breadcrumbs_Mod($titulo, $url_breadcrumbs, $modulo); ?>
    </div>
    <div class="margin-bottom" style="display: block">
       <span style=" color: #eb9627">
                        <i class="fa fa-info-circle"></i>
                                La opción <b>Transaccion entre cuentas </b> le permite al usuario pasar saldo de una cuenta a otra
                        </span>
    </div>

    <div class="form-group col-md-8 col-centered ">
        <div class="form-horizontal" id="form_trasnc">
            <div class="form-group">
                <label for="" class="control-label col-sm-3">Transaccion entre cuentas</label>
                <div class="col-sm-9">
                    <input type="checkbox" style="margin-top: 10px" id="entre_cuentas" onchange="EntreCuentas($(this))">
                </div>
            </div>
            <div class="form-group">
                <label for="" class="control-label col-sm-3">Movimiento</label>
                <div class="col-sm-9">
                    <select name="add_trs_movimiento" id="add_trs_movimiento" class="form-control" style="width: 100%" onchange="FormValidationTrasnc()">
                        <option value=""></option>
                        <option value="I" selected>Ingreso</option>
                        <option value="G">Egreso</option>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label for="" class="control-label col-sm-3">Fecha</label>
                <div class="col-sm-9">
                    <div class="input-group date" data-provide="datepicker">
                        <input type="text" class="form-control " name="add_trs_fecha" id="add_trs_fecha" readonly="" onchange="FormValidationTrasnc()">
                        <div class="input-group-addon">
                            <span class="fa fa-calendar"></span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label for="" class="control-label col-sm-3">Cuenta de</label>
                <div class="col-sm-9">
                    <select name="add_trs_cuenta_d" id="add_trs_cuenta_d" class="form-control cuentasDA" style="width: 100%" onchange="FormValidationTrasnc()">
                        <option value=""></option>
                        <?php
                            $result = $db->query("select rowid , concat(n_cuenta,' ',name_acount, ' ',if(to_caja=1,concat('Dir. ',to_caja_direccion),'')) as nom from tab_ope_declare_cuentas where estado = 'A' ");
                            if($result){
                                if($result->rowCount()>0){
                                    $array = $result->fetchAll(PDO::FETCH_ASSOC);
                                    foreach ($array as $value){
                                        print "<option value='".$value['rowid']."'>".$value['nom']."</option>";
                                    }
                                }
                            }
                        ?>
                    </select>
                </div>
            </div>

            <div class="form-group hide">
                <label for="" class="control-label col-sm-3">Cuenta a</label>
                <div class="col-sm-9">
                    <select name="add_trs_cuenta_a" id="add_trs_cuenta_a" class="form-control cuentasDA" style="width: 100%" onchange="FormValidationTrasnc()">
                        <option value=""></option>
                        <?php
                        $result = $db->query("select rowid , concat(n_cuenta,' ',name_acount, ' ',if(to_caja=1,concat('Dir. ',to_caja_direccion),'')) as nom from tab_ope_declare_cuentas where estado = 'A' ");
                        if($result){
                            if($result->rowCount()>0){
                                $array = $result->fetchAll(PDO::FETCH_ASSOC);
                                foreach ($array as $value){
                                    print "<option value='".$value['rowid']."'>".$value['nom']."</option>";
                                }
                            }
                        }
                        ?>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label for="" class="control-label col-sm-3">Operación:</label>
                <div class="col-sm-9">
                    <select name="add_trs_operacion" id="add_trs_operacion" class="form-control" style="width: 100%" onchange="FormValidationTrasnc(); ">
                        <option value=""></option>
                        <?php
                        $result = $db->query("select rowid, nom from tab_bank_operacion where rowid not  in(1,2,3,4)");
                        if($result){
                            if($result->rowCount()>0){
                                $array = $result->fetchAll(PDO::FETCH_ASSOC);
                                foreach ($array as $value){
                                    print "<option value='".$value['rowid']."'>".$value['nom']."</option>";
                                }
                            }
                        }
                        ?>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label for="" class="control-label col-sm-3">Descripción</label>
                <div class="col-sm-9">
                    <textarea class="form-control" style="resize: vertical" id="add_trs_descrip" onkeyup="FormValidationTrasnc(); "></textarea>
                </div>
            </div>

            <div class="form-group">
                <label for="" class="control-label col-sm-3">valor</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" id="add_trs_valor" style="width: 100%" onkeyup="FormValidationTrasnc()" value="0.00">
                </div>
            </div>
            <button class="btn pull-right" style="color: green; font-weight: bolder" onclick="GuardarTransaccion($(this))" id="guardartransaccion">
                Guardar
                <span class="fa fa-refresh btnSpinner hide"></span>
            </button>
        </div>
    </div>
</div>


<script>

    $sub_type = ($("#entre_cuentas").is(':checked')?2:1);

    function  EntreCuentas(Element) {

        $(".cuentasDA").val(null).trigger('change');

        console.log($('#add_trs_operacion').parents('.col-sm-9').parent());
        if(Element.is(':checked')){
            $('#add_trs_cuenta_a').parents('.col-sm-9').parent().removeClass('hide');
            $('#add_trs_movimiento').parents('.col-sm-9').parent().addClass('hide');
        }else{
            $('#add_trs_cuenta_a').parents('.col-sm-9').parent().addClass('hide');
            $('#add_trs_movimiento').parents('.col-sm-9').parent().removeClass('hide');
        }

    }
    
    var  FormValidationTrasnc = function() {
        var Errores  = [];
        var form     = $("#form_trasnc");
        var mov      = form.find("#add_trs_movimiento");
        var datef    = form.find("#add_trs_fecha");
        var cuenta   = form.find("#add_trs_cuenta_d");
        var descrp   = form.find("#add_trs_descrip");
        var ope      = form.find("#add_trs_operacion");
        var valor    = form.find("#add_trs_valor");


        if(datef.val()=="")
            Errores.push({document:datef, mesg:'Campo Obligatorio'});
        if(cuenta.find(":selected").val()=="")
            Errores.push({document:cuenta, mesg:'Campo Obligatorio'});

        if(!($("#entre_cuentas").is(':checked'))){
            if(mov.find(":selected").val()=="")
                Errores.push({document:mov, mesg:'Campo Obligatorio'});
        }

        if(ope.find(":selected").val()=="")
            Errores.push({document:ope, mesg:'Campo Obligatorio'});

        if(valor.val()=="" || valor.val()==0 )
            Errores.push({document:valor, mesg:'Campo Obligatorio, No puede ser 0'});
        if(descrp.val()=="")
            Errores.push({document:descrp, mesg:'Campo Obligatorio'});


        //entre cuentas
        var cuenta_a    = form.find("#add_trs_cuenta_a");
        if($("#entre_cuentas").is(':checked')){
            if(cuenta_a.find(':selected').val()==""){
                Errores.push({document:cuenta_a, mesg:'Campo Obligatorio'});
            }
            else{ }
        }

        $('.mesg_error_trs').remove();

        if(Errores.length>0){
            for (var i=0;i<=Errores.length-1; i++){
                    var Element  = Errores[i].document;
                    var mesg     = Errores[i].mesg;
                    var small    = document.createElement('small');
                    $(small).text(mesg).addClass('mesg_error_trs').css('color', 'red');
                    Element.parents('.col-sm-9').append($(small));
            }

            $("#guardartransaccion").addClass('disabled_link3');
            return false;
        }else{
            $("#guardartransaccion").removeClass('disabled_link3');
            return true;
        }

    };
    
    function  GuardarTransaccion(Element) {

        if(!FormValidationTrasnc()){
            return false;
        }

        button_loadding($(Element), true);
        
        var parametrs = {
            "accion"      : "crear_transaccion",
            "ajaxSend"    : "ajaxSend",
            "mov"         :$("#add_trs_movimiento").find(':selected').val(),
            "datef"       :$("#add_trs_fecha").val(),
            "cuenta"      :$("#add_trs_cuenta_d").find(':selected').val(), //de la cuenta
            "cuenta_a"    :$("#add_trs_cuenta_a").find(':selected').val(), //hacia la cuenta
            "descp"       :$("#add_trs_descrip").val(),
            "operacion"   :$("#add_trs_operacion").find(':selected').val(),
            "valor"       :$("#add_trs_valor").val(),
            "subaccion"   :($("#entre_cuentas").is(':checked')?2:1),
        };

        $.ajax({
            url: $DOCUMENTO_URL_HTTP + '/application/system/operacion/transacciones_clinicas/controller/controller.php',
            type:"POST",
            data:parametrs,
            dataType:"json",
            cache:false,
            async:true,
            beforeSend: function () {
                boxloading($boxContenTransacciones, true);
            },complete: function (xhr, status) {
                boxloading($boxContenTransacciones, false, 1000);
                button_loadding($(Element), false);
            },success: function (response) {
                console.log(response);
                if(response.error != ""){
                    notificacion(response.error, 'error');
                }else{
                    notificacion('Información Actualizada', 'success');
                    boxloading($boxContenTransacciones, true, 2000);
                    window.location = $DOCUMENTO_URL_HTTP + "/application/system/operacion/transacciones_clinicas/index.php?view=list_transacc_creadas&key="+$keyGlobal;

                }
            }
        });
    }

    $(window).on('onload', function () {
        boxloading($boxContenTransacciones, true);
    });

    $(window).on('load', function () {
        boxloading($boxContenTransacciones, false, 1000);

        var Dateadd = new Date();

        $('#add_trs_fecha').daterangepicker({
            // drops: 'up',
            // minDate : new Date(Dateadd.getFullYear(), Dateadd.getMonth(), Dateadd.getDate()),
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
            singleDatePicker: true,
            showDropdowns: true,
            autoclose: true,
            // "drops": "up",
            pickerPosition: "bottom-left"
        });

        $('select').select2({
            placeholder: 'seleccione una opción',
            allowClear:true,
            language:languageEs
        });

        FormValidationTrasnc();
        $("#add_trs_valor").maskMoney({precision:2,thousands:'', decimal:'.',allowZero:true,allowNegative:true, defaultZero:true,allowEmpty: true});
    });

</script>