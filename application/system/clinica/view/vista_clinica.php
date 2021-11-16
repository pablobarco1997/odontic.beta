
<?php
$infoModsrc='';
if(!empty($conf->EMPRESA->INFORMACION->logo)){ //si existe el logo de la clinica
    $infoModsrc = 'data:image/png; base64, '.base64_encode(file_get_contents(DOL_HTTP.'/logos_icon/'.$conf->NAME_DIRECTORIO.'/'.$conf->EMPRESA->INFORMACION->logo));
}else{
}
?>

<div class="form-group form-horizontal col-md-12 col-xs-12 col-lg-12">

    <div class="form-group">
        <div class="col-md-6 col-lg-4 col-centered">
            <div class="col-md-6 col-centered" style="width: 60%;display: block; overflow: hidden">
                <img src=" <?= !empty($conf->EMPRESA->INFORMACION->logo) ? $infoModsrc :  $infoModsrc ?>" style="width: 100%" alt="" id="imgLogoClinica">

                <table width="100%" style="margin-top: 10px">
                    <tr>
                        <td align="center">
                            <label for="subirLogoClinica" class=" btn btn-block" style="background-color: #ecf0f5 ;cursor: pointer" > <i class="fa fa-upload"></i> </label>
                            <input type="file" id="subirLogoClinica" style="display: none" accept="image/png" >
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <div class="form-group">
        <label class="control-label col-sm-3" for="entidad_clinica">Clinica:</label>
        <div class="col-sm-7">
            <input type="text" class="form-control" id="entidad_clinica" value="<?= $conf->EMPRESA->INFORMACION->nombre ?>">
        </div>
    </div>

    <div class="form-group">
        <label class="control-label col-sm-3" for="entidad_pais">Pais:</label>
        <div class="col-sm-7">
            <input type="text" class="form-control" id="entidad_pais" value="<?= $conf->EMPRESA->INFORMACION->pais ?>" >
        </div>
    </div>

    <div class="form-group">
        <label class="control-label col-sm-3" for="entidad_ciudad">Ciudad:</label>
        <div class="col-sm-7">
            <input type="text" class="form-control" id="entidad_ciudad" value="<?= $conf->EMPRESA->INFORMACION->ciudad ?>" >
        </div>
    </div>

    <div class="form-group">
        <label class="control-label col-sm-3" for="entidad_direccion">Dirección:</label>
        <div class="col-sm-7">
            <input type="text" class="form-control" id="entidad_direccion" value="<?= $conf->EMPRESA->INFORMACION->direccion ?>" >
        </div>
    </div>

    <div class="form-group">
        <label class="control-label col-sm-3" for="entidad_telefono">Telef.:</label>
        <div class="col-sm-7">
            <input type="text" class="form-control" id="entidad_telefono" value="<?= $conf->EMPRESA->INFORMACION->telefono ?>" >
        </div>
    </div>

    <div class="form-group">
        <label class="control-label col-sm-3" for="conf_email_entidad">Email.:</label>
        <div class="col-sm-7">
            <input type="text" class="form-control" id="conf_email_entidad" value="<?= $conf->EMPRESA->INFORMACION->conf_email ?>" >
        </div>
    </div>

    <div class="form-group">
        <label class="control-label col-sm-3" for="conf_password_entidad">Password:</label>
        <div class="col-sm-7">
            <input type="text" class="form-control" id="conf_password_entidad" value="<?= $conf->EMPRESA->INFORMACION->conf_password ?>" >
        </div>
    </div>

    <div class="form-group">
        <label class="control-label col-sm-3">&nbsp;</label>
        <div class="col-sm-8">
            <a href="#" class="btn btnhover pull-right" style="font-weight: bolder; color: green" onclick="Guardar()">Aceptar</a>
        </div>
    </div>


</div>


<script>


    $("#subirLogoClinica").change(function(event){
        // console.log(this.files);
        SubirImagenes( this, $("#imgLogoClinica"), $DOCUMENTO_URL_HTTP + '/logos_icon/logo_default/icon_software_dental.png');
    });

    function  Guardar() {

        var form = new FormData();
        var input = document.getElementById('subirLogoClinica');

        // console.log(input.files[0]);
        form.append('accion',   'UpdateEntidad');
        form.append('ajaxSend', 'ajaxSend');

        form.append('nombre'    , $("#entidad_clinica").val());
        form.append('pais'      , $("#entidad_pais").val() );
        form.append('ciudad'    , $("#entidad_ciudad").val() );
        form.append('direccion' , $("#entidad_direccion").val() );
        form.append('telefono'  , $("#entidad_telefono").val());
        // form.append('celular'   , $("#entidad_celular").val());
        form.append('email'     , $("#entidad_email").val());
        form.append('logo'      , input.files[0]);

        //configuracion de email para envios de correo
        form.append('conf_emil'      ,     $("#conf_email_entidad").val());
        form.append('conf_password'      , $("#conf_password_entidad").val());

        $.ajax({
            url:  $DOCUMENTO_URL_HTTP + '/application/controllers/controller_peticiones_globales.php',
            type:'POST',
            data: form,
            dataType:'json',
            async:false,
            contentType: false,
            processData:false,
            success:function(resp) {
                if(resp.error == 1) {
                    location.reload();
                }
                if(resp.error != 1){
                    notificacion('Ocurrio un error con la actualización de Información consulte con soporte', 'error');
                }
            }
        });

    }


</script>