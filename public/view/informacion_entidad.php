

<!--INFOMACION DE CLINICA -->

<div id="Informacion_clinica_modal" class="modal fade" role="dialog" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header modal-diseng">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title" id="info_clinica"><span>Clinica </span></h4>
            </div>
            <div class="modal-body">

                <form action="">

                    <div class="row">

                        <!--form horizontal-->
                        <div class="form-group form-horizontal col-md-12 col-xs-12 col-centered">


                            <div class="form-group">
                                <div class="col-md-3 col-centered">
                                    <div class="col-md-3 col-centered" style="width: 60%;display: block; overflow: hidden">
                                        <img src=" <?= !empty($conf->EMPRESA->INFORMACION->logo) ? DOL_HTTP.'/logos_icon/'.$conf->NAME_DIRECTORIO.'/'.$conf->EMPRESA->INFORMACION->logo :  DOL_HTTP .'/logos_icon/logo_default/icon_software_dental.png'?>" style="width: 100%" alt="" id="imgLogo">

                                        <table width="100%" style="margin-top: 10px">
                                            <tr>
                                                <td align="center">
                                                    <label for="subirLogo" class="btn-xs btn btn-block" style="background-color: #ABEBC6;cursor: pointer" >Cambiar</label>
                                                    <input type="file" id="subirLogo" style="display: none" accept="image/png" >
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label col-sm-3" for="email">Clinica:</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" id="entidad_clinica" value="<?= $conf->EMPRESA->INFORMACION->nombre ?>">
                                </div>
                            </div>

                                <div class="form-group">
                                    <label class="control-label col-sm-3" for="entidad_pais">Pais:</label>
                                    <div class="col-sm-3">
                                        <input type="text" class="form-control " id="entidad_pais" value="<?= $conf->EMPRESA->INFORMACION->pais ?>">
                                    </div>

                                    <label class="control-label col-sm-2" for="entidad_ciudad">Ciudad:</label>
                                    <div class="col-sm-3">
                                        <input type="text" class="form-control " id="entidad_ciudad" value="<?= $conf->EMPRESA->INFORMACION->ciudad ?>">
                                    </div>
                                </div>
                            
                            <div class="form-group">
                                <label class="control-label col-sm-3" for="entidad_direccion">Dirección:</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" id="entidad_direccion" value="<?= $conf->EMPRESA->INFORMACION->direccion ?>">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label col-sm-3" for="entidad_telefono">Telefono:</label>
                                <div class="col-sm-3">
                                    <input type="text" class="form-control " id="entidad_telefono" value="<?= $conf->EMPRESA->INFORMACION->telefono ?>">
                                </div>

                                <label class="control-label col-sm-2" for="entidad_celular">Celular:</label>
                                <div class="col-sm-3">
                                    <input type="text" class="form-control " id="entidad_celular" value="<?= $conf->EMPRESA->INFORMACION->celular ?>">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label col-sm-3" for="entidad_email">E-mail:</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" id="entidad_email" value="<?= $conf->EMPRESA->INFORMACION->email ?>">
                                </div>
                            </div>

                            <div class="hide">
                                <h2><span>Configurar e-mail</span></h2>
                                <div class="form-group">
                                    <label for="">E-mail</label>
                                    <input type="text" class="form-control input-sm" id="conf_email_entidad" value="<?= $conf->EMPRESA->INFORMACION->conf_email ?>" >
                                </div>
                                <div class="form-group">
                                    <label for="">Password</label>
                                    <input type="password" class="form-control input-sm" id="conf_password_entidad" value="<?= $conf->EMPRESA->INFORMACION->conf_password ?>" >
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label col-sm-3">&nbsp;</label>
                                <div class="col-sm-8">
                                    <a href="#" class="btn btnhover pull-right" style="font-weight: bolder; color: green" id="Update_entidad">Aceptar</a>
                                </div>
                            </div>

                        </div>

                    </div>

                </form>

            </div>
        </div>
    </div>
</div>