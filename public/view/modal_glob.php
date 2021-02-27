
<!--NOTIFICACIONES NOI =================================================================================================-->
<div class="modal fade" id="ModalInfoamcionNotificaicion" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
<!--                <h4 class="modal-title">Modal Header</h4>-->
            </div>
            <div class="modal-body">

                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group col-md-4">
                                <img src="<?php echo DOL_HTTP .'/dist/img/user2-160x160.jpg'; ?>" alt="" class="img-circle img-lg">
                        </div>

                        <div class="form-group col-md-8">
                            <div class="form-group col-md-12">
                                <div class="form-group col-md-6">
                                    <p id="modalnotifi_nombre" class="notifi_nombre text-bold">
<!--                                        nombre del paciente-->
                                    </p>
                                </div>
                                <div class="form-group col-md-6">
                                    <i class="fa fa-x2 fa-clock-o"></i>
                                    &nbsp; <span id="modalnotifi_horario" class="notifi_horario text-bold">
<!--                                        horario ejemplo 09:00 a 10:00-->
                                    </span>
                                </div>
                            </div>
                            <div class="form-group col-md-12">
                                <div class="form-group col-md-12">
                                    <p class="text-bold">Observación</p>
                                    <p class="text-justify text-sm notifi_observacion" id="modalnotifi_observacion">
<!--                                       observacion-->
                                    </p>
                                </div>
                            </div>

                        </div>

                    </div>

                </div>

            </div>


<!--            <div class="modal-footer">-->
<!--                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>-->
<!--            </div>-->

        </div>

    </div>
</div>


<!--MODIFICAR PERFIL ================================================================================================-->


<!--Modal modificar perfil de Usuario con SESSION INCIADA-->
<div id="ModPerfilModal3" class="modal fade" role="dialog" style="padding-right: 10px;">
    <div class="modal-dialog modal-lg">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header modal-diseng">
                <button type="button" class="close" data-dismiss="modal">×</button>
                <h4 class="modal-title"  id="obinfoperfil" data-idperfil="<?= $user->id ?>" data-usuario="<?= $user->name ?>"> <i class="fa fa-user"></i> </h4>
            </div>
            <div class="modal-body">

                <br>
                <div class="row">
                    <div class="col-centered col-lg-8 col-md-10 col-xs-12">
                        <div class="form-horizontal">

<!--                            Datos Usuarios-->
                            <div  style="border-bottom: 1px solid #e9edf2;" class="col-xs-12 col-md-12 margin-bottom"> <small><b>DATOS DE USUARIO</b></small> </div>

                            <label for="" class="control-label control-label col-sm-4 col-md-4 col-xs-12">usuario</label>
                            <div class="col-sm-6 col-md-8 col-xs-12">
                                <input type="text" class="form-control" id="perf_usu" onkeyup="FormValidationPerfilGlobal()">
                                <br>
                            </div>

                            <label for="" class="control-label control-label col-sm-4 col-md-4 col-xs-12">password</label>
                            <div class="col-sm-6 col-md-8 col-xs-12">
                                <div class="input-group">
                                    <input type="password" class="form-control input-sm" id="perf_passd" onkeyup="FormValidationPerfilGlobal()">
                                    <div class="input-group-addon btn" onclick="passwordMostrarOcultarPERFIL('mostrar', $('#perf_passd'));"><i class="fa fa-eye"></i></div>
                                    <div class="input-group-addon btn" onclick="passwordMostrarOcultarPERFIL('ocultar', $('#perf_passd'));"><i class="fa fa-eye-slash"></i></div>
                                </div>
                                <small style="color: #9f191f" id="msg_password_d"></small>
                                <br>
                            </div>

                            <label for="" class="control-label control-label col-sm-4 col-md-4 col-xs-12">Caja</label>
                            <div class="col-sm-6 col-md-8 col-xs-12">
                                <select name="perf_cajaUsers" id="perf_cajaUsers" class="form-control">
                                    <option value=""></option>
                                    <?php
                                        $resulCajasPerfil = $db->query("select rowid, concat('Caja #',rowid) as nom from tab_cajas_clinicas")->fetchAll(PDO::FETCH_ASSOC);
                                        foreach ($resulCajasPerfil as $value){
                                                print '<option value="'.$value['rowid'].'">'.$value['nom'].'</option>';
                                        }
                                    ?>
                                </select>
                            </div>


<!--                            Datos Odontologos-->
                            <div  style="border-bottom: 1px solid #e9edf2" class="col-xs-12 col-md-12 margin-bottom"> <small><b>DATOS DE ODONTOLOGOS</b></small> </div>

<!--                            nombre-->
                            <label for="" class="control-label control-label col-sm-4 col-md-4 col-xs-12">nombre</label>
                            <div class="col-sm-6 col-md-8 col-xs-12">
                                <input type="text" class="form-control" id="perf_nom" onkeyup="FormValidationPerfilGlobal()">
                                <br>
                            </div>
<!--                            apellido-->
                            <label for="" class="control-label control-label col-sm-4 col-md-4 col-xs-12">apellido</label>
                            <div class="col-sm-6 col-md-8 col-xs-12">
                                <input type="text" class="form-control" id="perf_apell" onkeyup="FormValidationPerfilGlobal()">
                                <br>
                            </div>
<!--                            cedula-->
                            <label for="" class="control-label control-label col-sm-4 col-md-4 col-xs-12">cedula</label>
                            <div class="col-sm-6 col-md-8 col-xs-12">
                                <input type="text" class="form-control" id="perf_cedula" onkeyup="FormValidationPerfilGlobal()">
                                <br>
                            </div>
<!--                            email-->
                            <label for="" class="control-label control-label col-sm-4 col-md-4 col-xs-12">email</label>
                            <div class="col-sm-6 col-md-8 col-xs-12">
                                <input type="text" class="form-control" id="perf_email" onkeyup="FormValidationPerfilGlobal()">
                                <br>
                            </div>
<!--                            Celular-->
                            <label for="" class="control-label control-label col-sm-4 col-md-4 col-xs-12">Celular</label>
                            <div class="col-sm-6 col-md-8 col-xs-12">
                                <input type="text" class="form-control" id="perf_celular" >
                                <br>
                            </div>
                            <label for="" class="control-label control-label col-sm-4 col-md-4 col-xs-12">especialidad</label>
                            <div class="col-sm-6 col-md-8 col-xs-12">
                                <select  id="especialidadPerfil" class="form-control " style="width: 100%" onchange="FormValidationPerfilGlobal()">
                                    <option value="0">General</option>
                                    <?php

                                        $sql = "select nombre_especialidad , rowid from tab_especialidades_doc";
                                        $rs  = $db->query($sql);
                                        if($rs&&$rs->rowCount()>0){
                                            while ($obj = $rs->fetchObject()){
                                                print "<option value='".$obj->rowid."' >".$obj->nombre_especialidad."</option>";
                                            }
                                        }

                                    ?>
                                </select>
                                <br>
                            </div>

                        </div>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <a href="#" class="btn btnhover pull-right" data-dismiss="modal" style="font-weight: bolder">Close</a>
                <a href="#" class="btn btnhover pull-right" style="font-weight: bolder; color: green" id="guadarPerfil" onclick="GuardarPerfilGlob()">Aceptar</a>
            </div>
        </div>

    </div>
</div>