<div class="row">
    <div class="col-md-12">
            <div class="box box-solid">
                    <div class="box-header with-border">
                        <h4 class="no-margin"><span>
                            <img src="<?= DOL_HTTP.'/logos_icon/logo_default/modificar-icon.png' ?>" width="20px" height="20px">
                            <b>Registrar Nuevo Paciente</b></span>
                        </h4>
                    </div>
                    <div class="box-body">
                            <div class="row">
                                <div class="form-group col-lg-12 col-md-12 col-xs-12">
<!--                                    LISTA DE COMPORTAMIENTOS-->
                                    <div class="form-group col-xs-12 col-md-12">
                                        <div class="form-group">
                                            <label for="">LISTA DE COMPORTAMIENTOS</label>
                                            <ul class="list-inline pull-right col-md-12 col-xs-12" style="border-bottom: 0.6px solid #333333; padding: 3px">
                                                <li>
                                                    <a class="btnhover btn btn-sm " id="carga_masv_pasiente" style="color: #333333" title="carga. Subida Masiva de Pacientes (excel)">
                                                        <b><img src="<?= DOL_HTTP.'/logos_icon/logo_default/Excel_2013_23480.png' ?>" width="15px" height="15px"> Cargar Paciente Masivo</b>
                                                    </a>
                                                </li>
                                                <li> <a href="<?= DOL_HTTP.'/application/system/pacientes/nuevo_paciente/export/carga_masiva_pasientes.xlsx' ?>" title="Descarga de Plantilla (excel)" data-target="_blank" class="btnhover btn btn-sm " style="color: #333333">
                                                        <b> <i class="fa fa-cloud-download control-label" style="font-size: 15px; color: #2b794d "></i> &nbsp; Descargar Plantilla</b>
                                                    </a>
                                                </li>
                                            </ul>
                                            <input type="file" id="subida_masiva_pasiente" style="display: none">
                                        </div>
                                    </div>

<!--                        FORM DE REGISTRO PACIENTE-->
                                            <div class="form-group col-md-12  col-sm-12 ">

                                                    <div class="form-group col-md-8 col-sm-12 col-xs-12 col-lg-8 col-centered">

                                                            <form class="form-horizontal" >

                                                                        <div class="form-group">
                                                                            <label for="" class="control-label col-sm-2">Nombre</label>
                                                                            <div class="col-sm-10">
                                                                                <input type="text" class="form-control input-sm" id="nombre" onkeyup="invalic_paciente();">
                                                                                <small id="noti_nombre" style="color: red"></small>
                                                                            </div>
                                                                        </div>

                                                                        <div class="form-group">
                                                                            <label for="" class="control-label col-sm-2">Apellido</label>
                                                                            <div class="col-sm-10">
                                                                                <input type="text" class="form-control input-sm" id="apellido" onkeyup="invalic_paciente();">
                                                                                <small id="noti_apellido" style="color: red"></small>
                                                                            </div>
                                                                        </div>

                                                                        <div class="form-group">
                                                                            <label for="" class="control-label col-sm-2">cedula</label>
                                                                            <div class="col-sm-10">
                                                                                <input type="text" class="form-control input-sm" id="rud_dni" onkeyup="invalicrucCedula(this, true); invalic_paciente(true)">
                                                                                <small id="noti_ruddni" style="color: red"></small>
                                                                            </div>
                                                                        </div>

                                                                        <div class="form-group">
                                                                            <label for="" class="control-label col-sm-2" >E-mail</label>
                                                                            <div class="col-sm-10">
                                                                                <input type="text" class="form-control input-sm" id="email" onkeyup="invalicEmailText(this, true); invalic_paciente();">
                                                                                <small id="noti_email" style="color: red"></small>
                                                                            </div>
                                                                        </div>

<!--                                                                            Descuento o convenio-->
                                                                        <div class="form-group hidden">
                                                                            <label for="" class="control-label col-sm-2" >Descuento</label>
                                                                            <div class="col-sm-10">
                                                                                <select name="convenio" id="convenio" class="form-control ">
                                                                                    <option value="0"> Ninguno </option>
                                                                                    <?php
                                                                                    $sql = "select * from tab_conf_convenio_desc";
                                                                                    $rs  = $db->query($sql);
                                                                                    if($rs->rowCount()>0)
                                                                                    {   while ($rowxs = $rs->fetchObject())
                                                                                        { print "<option value='$rowxs->rowid'> $rowxs->nombre_conv </option>"; }
                                                                                    }
                                                                                    ?>
                                                                                </select>
                                                                            </div>
                                                                        </div>

                                                                        <div class="form-group hidden">
                                                                            <label for="" class="control-label col-sm-2">Numero interno</label>
                                                                            <div class="col-sm-10">
                                                                                <input type="number" class="form-control" id="n_interno">
                                                                            </div>
                                                                        </div>

                                                                        <div class="form-group">
                                                                            <label for="" class="control-label col-sm-2">Genero</label>
                                                                            <div class="col-sm-10">
                                                                                <select name="" id="sexo" class="form-control input-sm" >
                                                                                    <option value="masculino">masculino</option>
                                                                                    <option value="femenino">femenino</option>
                                                                                </select>
                                                                                <small id="noti_sexo" style="color: red"></small>
                                                                            </div>
                                                                        </div>

                                                                        <div class="form-group">
                                                                            <label for="" class="control-label col-sm-2">Fecha de Nacimiento</label>
                                                                            <div class="col-sm-10">
                                                                                <div class="input-group date" data-provide="datepicker" >
                                                                                    <input type="text" class="form-control input-sm" id="fech_nacimit"  readonly>
                                                                                    <div class="input-group-addon">
                                                                                        <span class="fa fa-calendar"></span>
                                                                                    </div>
                                                                                    <small id="noti_date_nacimiento" style="color: red"></small>
                                                                                </div>
                                                                            </div>
                                                                        </div>

                                                                        <div class="form-group">
                                                                            <label for="" class="control-label col-sm-2">Ciudad</label>
                                                                            <div class="col-sm-10">
                                                                                <input type="text" class="form-control input-sm" id="ciudad">
                                                                            </div>
                                                                        </div>

                                                                        <div class="form-group hidden">
                                                                            <label for="" class="control-label col-sm-2">Comuna</label>
                                                                            <div class="col-sm-10">
                                                                                <input type="text" class="form-control" id="comuna">
                                                                            </div>
                                                                        </div>

                                                                        <div class="form-group">
                                                                            <label for="" class="control-label col-sm-2">Dirección</label>
                                                                            <div class="col-sm-10">
                                                                                <input type="text" class="form-control input-sm" id="direcc" onkeyup="invalic_paciente()">
                                                                                <small id="noti_direccion" style="color: red"></small>
                                                                            </div>
                                                                        </div>

                                                                        <div class="form-group">
                                                                            <label for="" class="control-label col-sm-2">Teléfono Fijos</label>
                                                                            <div class="col-sm-10">
                                                                                <input type="number" class="form-control input-sm" id="t_fijo">
                                                                            </div>
                                                                        </div>

                                                                        <div class="form-group">
                                                                            <label for="" class="control-label col-sm-2">Teléfono celular</label>
                                                                            <div class="col-sm-10">
                                                                                <input type="number" class="form-control input-sm" id="t_movil">
                                                                            </div>
                                                                        </div>

                                                                        <div class="form-group">
                                                                            <label for="" class="control-label col-sm-2">Actividad Profecional</label>
                                                                            <div class="col-sm-10">
                                                                                <input type="text" class="form-control input-sm" id="act_profec">
                                                                            </div>
                                                                        </div>

                                                                        <div class="form-group">
                                                                            <label for="" class="control-label col-sm-2">Referencia</label>
                                                                            <div class="col-sm-10">
                                                                                <input type="text" class="form-control input-sm" id="refer">
                                                                            </div>
                                                                        </div>

                                                                        <div class="form-group hidden">
                                                                            <label for="">Empleador</label>
                                                                            <input type="text" class="form-control" id="empleado">
                                                                        </div>

                                                                        <div class="form-group">
                                                                            <label for="" class="control-label col-sm-2">Observacion</label>
                                                                            <div class="col-sm-10">
                                                                                <textarea name=""  cols="30"
                                                                                          rows="5" class="form-control input-sm" id="obsrv"></textarea>
                                                                            </div>
                                                                        </div>

<!--                                                                    Apoderado-->
                                                                        <div class="form-group hidden">
                                                                            <label for="">Apoderado</label>
                                                                            <input type="text" class="form-control" id="apoderado">
                                                                        </div>


                                                                        <div class="form-group">
                                                                            <input type="button" class="btn btnhover btn-block" style="font-weight: bolder; color: green" id="guardar" value="Guardar">
                                                                        </div>
                                                            </form>
                                                        <br>
                                                    </div>
                                            </div>
                                    </div>
                            </div>
                    </div>
            </div>
    </div>
</div>