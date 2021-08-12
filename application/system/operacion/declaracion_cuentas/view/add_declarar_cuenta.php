
<?php

#breadcrumbs  -----------------------------------------------
$url_breadcrumbs = $_SERVER['REQUEST_URI'];
$titulo = "Nueva Cuenta";
$modulo = false;

?>

<div class="form-group col-xs-12 col-md-12 col-lg-12 no-padding">
    <div  class="form-group col-md-12 col-xs-12">
        <?= Breadcrumbs_Mod($titulo, $url_breadcrumbs, $modulo); ?>
    </div>

    <br>

    <div class="form-group col-md-12 col-xs-12">

        <div class="form-group col-md-9 col-centered">
            <div class="form-horizontal">

                <div id="Plan_Cuentas_Financiero_Banco_Caja">

                    <div class="form-group">
                        <label for="" class="control-label col-sm-3 ">Cuenta:</label>
                        <div class="col-sm-7">
                            <div class="radio">
                                <label>
                                    <input type="radio" name="cuentasFinanciero" id="plan_de_cuentas" value="plan_de_cuentas" > Plan de Cuentas
                                </label>
                            </div>
                            <div class="radio">
                                <label>
                                    <input type="radio" name="cuentasFinanciero" id="cuentas_bancos" value="cuentas_bancos" > Bancos o Cajas
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="form-group bancoCuenta hide" >
                        <label for="" class="control-label col-sm-3">Tipo Cuenta:</label>
                        <div class="col-sm-7">
                            <select name="Fn_tipo_cuenta" id="Fn_tipo_cuenta" class="form-control" style="width: 100%">
                                <?php
                                    $result = $db->query("select * from tab_ope_type_bancos_caja")->fetchAll(PDO::FETCH_ASSOC);
                                    foreach ($result as $value){
                                        print "<option value='".$value['rowid']."' data-id='".$value['rowid']."' data-tocaja='".$value['to_caja']."' data-tobanco='".$value['to_banco']."'  >".$value['name']."</option>";
                                    }
                                ?>

                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="" class="control-label col-sm-3">Nombre:</label>
                        <div class="col-sm-7">
                            <input type="text" class="form-control" id="Fn_nombre_cuenta">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="" class="control-label col-sm-3">Número de Cuenta:</label>
                        <div class="col-sm-7">
                            <input type="text" class="form-control" id="Fn_numero_cuenta">
                        </div>
                    </div>

                    <div class="form-group DireccionCaja hide">
                        <label for="" class="control-label col-sm-3">Dirección:</label>
                        <div class="col-sm-7">
                            <input type="text" class="form-control" id="Fn_direccion" placeholder="Dirección de caja">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="" class="control-label col-sm-3">Descripción:</label>
                        <div class="col-sm-7">
                            <input type="text" class="form-control" id="Fn_descriptcion" placeholder="opcional" >
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="" class="control-label col-sm-3">Tipo Operación:</label>
                        <div class="col-sm-7">
                            <select name="" class="form-control" id="Fn_tipo_operacion" style="width: 100%">
                                <option value=""></option>
                                <option value="INGRESOS">INGRESOS</option>
                                <option value="GASTOS">GASTOS</option>
                                <option value="COSTO">COSTO</option>

                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="" class="control-label col-sm-1">&nbsp;</label>
                        <div class="col-sm-9">
                            <button class="btn btnhover pull-right" style="font-weight: bolder; color: green; " id="nuevoGuardarCuentaFinanciero" >
                                Guardar
                                <span class="fa fa-refresh btnSpinner hide"></span>
                            </button>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div>
</div>


<script>

    $('input[name="cuentasFinanciero"]').change(function () {
        var Element = $(this);
        $("#Fn_tipo_cuenta").val(null).trigger('change');
        if(Element.attr('id') == 'cuentas_bancos'){
            $('.bancoCuenta').removeClass('hide');
            $("#Fn_tipo_operacion").val('INGRESOS').trigger('change').attr('disabled', true);
        }else{
            $('.bancoCuenta').addClass('hide');
            $("#Fn_tipo_operacion").val('INGRESOS').trigger('change').attr('disabled', false);
        }
    });

    var validationCuentas = function(revalidate){

        var Errores                 = [];
        var nombre                  = $("#Fn_nombre_cuenta");
        var numero                  = $("#Fn_numero_cuenta");
        var tipoCuentaBC            = $("#Fn_tipo_cuenta");
        var Cajadireccion           = $("#Fn_direccion");

        if(nombre.val() == "" || (!/^\s/.test(nombre.val())) == false){
            Errores.push({
                "documento" :   nombre,
                "mesg" :  "Campo Obligatorio",
            });
        }
        if(numero.val() == "" || (!/^\s/.test(numero.val())) == false){
            Errores.push({
                "documento" :   numero,
                "mesg" :  "Campo Obligatorio",
            });
        }else{
            if((!/[^0-9]/g.test(numero.val()))==false){
                Errores.push({
                    "documento" :   numero,
                    "mesg" :  "solo numeros",
                });
            }
        }



        //validaciones cuentas banco o cajas
        if($('input[name="cuentasFinanciero"]:checked').val() == 'cuentas_bancos'){
            console.log(tipoCuentaBC.find(':selected').val());
            if(tipoCuentaBC.find("option:selected").val()==''){
                Errores.push({
                    "documento" :   tipoCuentaBC,
                    "mesg" :  "Debe selecionar un tipo de Cuenta",
                });
            }else{
                //si es cuenta caja
                if(tipoCuentaBC.find("option:selected").val()=='3'){

                    if(Cajadireccion.val() == "" || (!/^\s/.test(Cajadireccion.val())) == false){
                        Errores.push({
                            "documento" :   Cajadireccion,
                            "mesg" :  "Campo Obligatorio",
                        });
                    }
                }
            }
        }



        var valid = true;
        $(".error_msg_Fn_cuentas").remove();
        if(Errores.length>0){
            for (var i=0; i<=Errores.length-1;i++ ){
                var menssage =  document.createElement("small");
                menssage.setAttribute("style","display: block; color:blue;");
                menssage.setAttribute("class","error_msg_Fn_cuentas");
                menssage.appendChild(document.createTextNode(Errores[i]['mesg']));
                var documentoDol        = Errores[i]['documento'];
                $(menssage).insertAfter(documentoDol);
            }
            valid = false;
        }else{
            valid = true;
        }

        return valid;

    };

    $("#nuevoGuardarCuentaFinanciero").click(function () {

        if(!ModulePermission('Declarar Cuentas', 'agregar')){
            notificacion('Ud. No tiene permiso para realizar esta Operación', 'error');
            return false;
        }

        if(!validationCuentas()){
            return false;
        }

        var name                 = $("#Fn_nombre_cuenta").val();
        var n_cuenta             = $("#Fn_numero_cuenta").val();
        var description_acount   = $("#Fn_descriptcion").val();
        var tipo_operacion       = $("#Fn_tipo_operacion").find(":selected").val();
        var tipoAcoutBC          = $("#Fn_tipo_cuenta").find(":selected").val(); //tipo de cuenta Banco o Caja
        var cuenta               = $('input[name="cuentasFinanciero"]:checked').val();
        var direccionCaja        = $("#Fn_direccion").val();

        var parametros = {
            'ajaxSend' : 'ajaxSend' ,
            'accion' : 'NuevaDeclaracionCuenta' ,
            'name' : name,
            'n_cuenta' : n_cuenta,
            'description_acount': description_acount,
            'tipo_operacion': tipo_operacion, // ingreso - egreso - gastos
            'tipoAcoutBC': tipoAcoutBC, //tipo banco  caja
            'cuenta': cuenta, //tipo banco  caja
            'direccionCaja' : direccionCaja
        };

        $.ajax({
            url: $DOCUMENTO_URL_HTTP + '/application/system/operacion/declaracion_cuentas/controller/controller.php',
            type:'POST',
            data:parametros,
            dataType:'json',
            cache: false,
            async:false,
            success:function (response) {
                if(response.validacionError == 0){
                    notificacion('Información Actualizada', 'success');
                    setTimeout(function () {
                        window.location = $DOCUMENTO_URL_HTTP + '/application/system/operacion/declaracion_cuentas/index.php?view=all_cuentas&key='+$keyGlobal;
                    },1000);
                }else{
                    if(response.error.length != 0){
                        notificacion('<b>Se detectaron los siguientes errores</b> <br>'+response.error , 'error');
                    }
                }
            }
        });


    });

    $("#Fn_tipo_cuenta").change(function () {

        $("#Fn_direccion").val(null); //direccion caja

        if( $(this).find(":selected").val() == 3 || $(this).find(":selected").val() == 3 ){
            $("#Fn_nombre_cuenta").val("Caja").attr('disabled', true);
            $(".DireccionCaja").removeClass('hide');
        }else{
            $("#Fn_nombre_cuenta").val(null).attr('disabled', false);
            $(".DireccionCaja").addClass('hide');
        }
    });

    $("input[type=text]").keyup(function () {
        validationCuentas();
    });
    $("select").change(function () {
        validationCuentas();
    });

    $(window).on('load', function () {

        $("#plan_de_cuentas").prop('checked', true);

        $('select').select2({
            placeholder: 'seleccione una opción'
        })
    });

</script>
