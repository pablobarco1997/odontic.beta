

<!--MODIFICAR PERFIL ================================================================================================-->
<!--Modal modificar perfil de Usuario con SESSION INCIADA-->
<div id="ModificarPerfilUsuario" class="modal fade" role="dialog" data-backdrop="static" data-keyboard="false" style="display: none; padding-right: 17px">
    <div class="modal-dialog ">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header modal-diseng">
                <button type="button" class="close" data-dismiss="modal">×</button>
                <h4 class="modal-title"  id="obinfoperfil" data-idperfil="<?= $user->id ?>" data-usuario="<?= $user->name ?>"> <i class="fa fa-user"></i> </h4>
            </div>
            <div class="modal-body">
               <div class="row">
                   <div class="form-group col-md-12">
                       <div class="form-horizontal">
                           <!--Datos Usuarios-->
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

                           <label for="" class="control-label control-label col-sm-4 col-md-4 col-xs-12 hide">Caja</label>
                           <div class="col-sm-6 col-md-8 col-xs-12 hide">
                               <select name="perf_cajaUsers" id="perf_cajaUsers" class="form-control hide">
                                   <option value=""></option>
                                   <?php
//                                       $resulCajasPerfil = $db->query("select rowid, concat('Caja #',rowid) as nom from tab_cajas_clinicas")->fetchAll(PDO::FETCH_ASSOC);
//                                       foreach ($resulCajasPerfil as $value){
//                                           print '<option value="'.$value['rowid'].'">'.$value['nom'].'</option>';
//                                       }
                                   ?>
                               </select>
                           </div>


                           <div class="boxDatosOdontologos">

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
            </div>
            <div class="modal-footer">
                <a href="#" class="btn btnhover pull-right" data-dismiss="modal" style="font-weight: bolder">Cerrar</a>
                <a href="#" class="btn btnhover pull-right" style="font-weight: bolder; color: green" id="guadarPerfil" onclick="GuardarPerfilGlob()">Aceptar</a>
            </div>
        </div>

    </div>
</div>