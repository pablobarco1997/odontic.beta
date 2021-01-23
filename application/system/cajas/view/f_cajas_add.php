<?php

#breadcrumbs  -----------------------------------------------
$url_breadcrumbs = $_SERVER['REQUEST_URI'];
$titulo = "Nueva Caja";
$modulo = false;

?>

<div class="form-group col-xs-12 col-md-12 col-lg-12 no-padding">
    <div  class="form-group col-md-12 col-xs-12">
        <?= Breadcrumbs_Mod($titulo, $url_breadcrumbs, $modulo); ?>
    </div>

    <div class="form-group col-md-12 col-xs-12">
        <h3 class="no-margin pull-right">NUEVA CAJA</h3>
    </div>

    <div class="form-group col-md-12 col-xs-12">
        <div class="form-horizontal">
            <div class="form-group">
                <label class="control-label col-sm-3" for="nom_caja">Nombre:</label>
                <div class="col-sm-7">
                    <div class="input-group">
                        <span class="input-group-addon">CAJA_</span>
                            <input type="text" class="form-control" id="nom_caja" placeholder="Nombre de la Caja" name="nom_caja" onkeyup="FormValidAddCaja()">
                        <span class="input-group-addon"><i class="fa fa-bar-chart"></i></span>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-sm-3" for="direccion_caja">Dirección:</label>
                <div class="col-sm-7">
                    <input type="text" class="form-control" id="direccion_caja" placeholder="Direccion de caja" name="direccion_caja">
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-sm-3" for="saldo_ini_caja">Saldo Inicial <small>(opcional)</small>:</label>
                <div class="col-sm-7">
                    <div class="input-group">
                        <span class="input-group-addon"><i class="fa fa-dollar"></i></span>
                            <input type="text" class="form-control" id="saldo_ini_caja" placeholder="Ingrese un saldo inicial de caja (opcional)" name="saldo_ini_caja">
                    </div>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="col-md-8 col-xs-12 col-centered">
                <input type="button" class="btn btnhover btn-block" style="font-weight: bolder; color: green" id="nuevoModCaja" value="Guardar">
            </div>
        </div>
    </div>

</div>


<script>

    var FormValidAddCaja = function(){

        var Errores = [];
        var name = $("#nom_caja");

        if(name.val() == ""){
            Errores.push({
                "documento" :   name.parent('.input-group'),
                "mesg" :  "Campo Obligatorio",
            });
        }

        $(".error_caja").remove();

        for (var i=0; i<=Errores.length-1;i++ ){

            var menssage =  document.createElement("small");
            menssage.setAttribute("style","display: block; color:blue;");
            menssage.setAttribute("class","error_caja");
            menssage.appendChild(document.createTextNode(Errores[i]['mesg']));
            var documentoDol        = Errores[i]['documento'];

            $(menssage).insertAfter(documentoDol);
        }

        if(Errores.length>0){
            return false;
        }else{
            return true;
        }
    };

    $("#nuevoModCaja").on("click", function() {

        if(!ModulePermission(25,2)){
            notificacion('Ud. no tiene permiso para crear', 'question');
            return false;
        }

        if(!FormValidAddCaja()){
            return false;
        }

        var parametros = {
            'accion':'nueva_caja',
            'ajaxSend':'ajaxSend',
            'nam_caja':$('#nom_caja').val(),
            'direccion_caja':$('#direccion_caja').val(),
            'saldo_ini_caja':$('#saldo_ini_caja').val(),
        };

        $.ajax({
            url: $DOCUMENTO_URL_HTTP + '/application/system/cajas/controller/controller_caja.php',
            type:'POST',
            data: parametros,
            dataType:'json',
            async:false,
            cache:false,
            complete:function(xhr, status) {

                if(xhr['status']=='200'){
                    boxloading($boxContentCajasModule,true,1000);
                }else{
                    if(xhr['status']=='404'){
                        notificacion("Ocurrió un error con la <b>accion crear caja</b> <br> <b>xhr: "+xhr['status']+" <br> Consulte con Soporte </b>");
                    }
                    boxloading($boxContentCajasModule,true,1000);
                }
            },
            success:function(respuesta) {

                boxloading($boxContentCajasModule,false,1000);

                if(respuesta['error']!=''){
                    notificacion(respuesta['error'], 'error');
                }else{

                    notificacion('Información Actualizada', 'success');
                    window.location = $DOCUMENTO_URL_HTTP + '/application/system/cajas/index.php?view=principal_cajas';
                }
            }
        });

    });

</script>
