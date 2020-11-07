<?php

$accionDoctorEspicialidad = "";
if(isset($_GET['v']))
{
    $accionDoctorEspicialidad = $_GET['v'];
}


$option1 = "<option></option>";
$sql = "SELECT * FROM tab_odontologos WHERE estado = 'A'";
$rs = $db->query($sql);
if($rs->rowCount()>0)
{
    while ($ob = $rs->fetchObject()){
        $option1 .= "<option value='$ob->rowid'> ".$ob->nombre_doc."  ".$ob->apellido_doc." </option>";
    }
}


$v = null;
if(isset($_GET['v'])){
    $v = $_GET['v'];
}

?>

<script>
    //accion si es especialidad o Doctor     MODULO
    $accion = "<?= $accionDoctorEspicialidad ?>";
</script>

<style>

</style>

<div class="box box-solid">

    <div class="box-header with-border">
        <div class="form-group col-xs-12 col-sm-12 col-md-12 no-margin">
            <h4 class="no-margin"><span><b>
                        <?php
                            if($v=='dentist')
                                echo 'Odontologos';
                            if($v=='specialties')
                                echo 'Especialidades';
                            if($v=='users')
                                echo 'Usuarios Asociados';
                        ?>
                    </b></span></h4>
        </div>
    </div>

    <div class="box-body">

        <br>

        <div class="form-group col-centered col-xs-12 col-md-10 col-lg-10">

            <div class="form-group col-md-12 " style="padding: 0px">
                <ul class="list-inline" style="">
                    <li><a class="btn " href="<?= DOL_HTTP .'/application/system/configuraciones/index.php?view=form_gestion_odontologos_especialidades&v=dentist'; ?>" style="border-left: 2px solid #212f3d; color: #333333"> <i class="fa fa-user-md"></i> &nbsp; Odontólogos</a> </li>
                    <li>&nbsp;&nbsp;</li>
                    <li><a class="btn " href="<?= DOL_HTTP .'/application/system/configuraciones/index.php?view=form_gestion_odontologos_especialidades&v=specialties'; ?>" style="border-left: 2px solid #212f3d; color: #333333" > <i class="fa fa-align-center"></i> &nbsp; Especialidades</a></li>
                    <li>&nbsp;&nbsp;</li>
                    <li><a class="btn " href="<?= DOL_HTTP .'/application/system/configuraciones/index.php?view=form_gestion_odontologos_especialidades&v=users&list=true'; ?>" style="border-left: 2px solid #212f3d; color: #333333" > <i class="fa fa-user"></i> &nbsp; Usuarios</a></li>

                </ul>
                <br>
            </div>

            <?php


            if($v==null){

                die();
            }


            #DENTISTA   ---------------------------------------------------------------------------------------------------------------------------------------------------------------
            if(isset($v) && $v == "dentist")
            {

            ?>

            <div class="row">
                <div class="form-group col-md-12 col-lg-12 col-xs-12">
                    <ul class="list-inline" style="border-bottom: 1px solid #333333; border-top: 1px solid #333333; height: 30px; ">
                        <li><a  style="font-weight: bolder; color: #333333; " class="btnhover "  data-toggle="modal" data-target="#modal_conf_doctor"  onclick="cambiarattr()"> &nbsp;&nbsp;<i class="fa fa-user-md"></i> &nbsp; crear odontólogos</a></li>
<!--                        <li><a style="cursor: pointer; font-weight: bolder; color: #333333" class="btn btnhover  hide" data-toggle="modal" data-target="#ModalCrearUsuario"  onclick="NuevoEditUsario(0,0,'0')" > &nbsp;&nbsp;<i class="fa fa-user"></i> &nbsp;&nbsp; Crear Usuario</a></li>-->
                        <li>
                            <div class="checkbox " style="margin: 0px; padding: 5px; ">
                                <label for="desabilitado_doctores" style=" font-weight: bolder"><input type="checkbox" id="desabilitado_doctores">
                                <i class="fa fa-user-times"></i> Ver lista de odontolog@ desabilitados</label>
                            </div>
                        </li>
                    </ul>
                </div>

                <br>
                <br>

                <div class="col-md-12">
                    <div class="table-responsive">
                        <table width="100%" class="table table-striped" id="gention_odontologos_list">
                            <thead>
<!--                                <th width="5%"></th>-->
                                <th width="18%">Nombre</th>
                                <th width="15%">Cedula</th>
                                <th width="15%">Dirección</th>
                                <th width="18%">E-mail</th>
                                <th width="15%">Especialidad</th>
                                <th width="5%"></th>
                            </thead>
                        </table>
                    </div>
                </div>

            </div>

            <?php } ?>

            <?php

                #ESPECIALIDAD   <------------------------------------------------------------------------------------------------------------------------------------------------------------->
                if(isset($v) && $v == "specialties")
                {

            ?>

                <div class="row">
                    <div class="form-group col-md-12">
                        <ul class="list-inline" style="border-bottom: 1px solid #333333; border-top: 1px solid #333333; height: 30px; padding: 3.5px">
                            <li><a  class="btnhover "  data-toggle="modal" data-target="#ModalConfEspecialidades" style="font-weight: bolder; color: #333333; "  > &nbsp;&nbsp;<i class="fa fa-list"></i> &nbsp;  crear especialidad</a></li>
                        </ul>
                    </div>

                    <div class="form-group col-md-12">
                        <div class="form-group col-md-12 ">
                        <span style=" color: #eb9627">
                        <i class="fa fa-info-circle"></i>
                            Tener en cuenta que si elimina una especialidad, aquellos Odontólogos
                            relacionados con esta, se actualizaran a especialidad General incluyendo todas las citas asociadas con la especialidad
                            eliminada
                        </span>
                        </div>
                    </div>

                    <div class="form-group col-md-12">
                        <div class="table-responsive">
                            <table width="100%" class="table table-striped" id="gention_especialidades">
                                <thead>
                                <th width="30%">Fecha Creación</th>
                                <th width="30%">Especialidad</th>
                                <th width="30%">Descripción</th>
                                <th ></th>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>


            <?php } ?>

            <?php

                #USUARIOS ASOCIADOS A UN ODONTOLOGOS  ---------------------------------------------------------------------------------------------
                if( isset($v) && $v == "users")
                {
            ?>

                <div class="row">
                    <div class="form-group col-md-12">
                        <ul class="list-inline" style="border-bottom: 1px solid #333333; border-top: 1px solid #333333; height: 30px; padding: 3.5px">
                            <!--                              <li><a class="btnhover btn" data-toggle="modal" data-target="#ModalCrearUsuario"  style="font-weight: bolder; color: #333333"> <i class="fa fa-user-plus"></i> &nbsp; Crear Usuario </a> </li>-->
                            <li><a class="btnhover"  href="<?= DOL_HTTP .'/application/system/configuraciones/index.php?view=form_gestion_odontologos_especialidades&v=users&creat=true'?>"  style="font-weight: bolder; color: #333333; padding: 1px"> <i class="fa fa-user-plus"></i> &nbsp; Crear Usuario </a> </li>
                        </ul>
                    </div>

                <?php if( isset($_GET['list']) ){?>
                      <div class="form-group col-md-12 col-xs-12">
                          <div class="table-responsive">
                              <table class="table table-striped" id="usuariolistinfo" width="100%">
                                  <thead>
                                        <tr>
                                            <th></th>
                                            <th>Usuario</th>
                                            <th>Perfil</th>
                                            <th>Estado</th>
                                        </tr>
                                  </thead>
                              </table>
                          </div>
                      </div>
                <?php  } ?>

                <?php
                    #Crear O Modificar usuariologin
                    if( isset($_GET['creat']) ||  isset($_GET['mod']) )
                    {
                ?>

                    <style>
                        .noneMod{
                            display: none;
                        }
                    </style>


                    <div class="form-horizontal">

                        <div class="form-group">
                            <label class="control-label col-sm-2" for="">Doctor</label>
                            <div class="col-sm-8">
                                <select name="usu_doctor" id="usu_doctor" class="form-control select2_max_ancho" onchange="FormValidacion($(this),true)" style="width: 100%">
                                    <?= $option1 ?>
                                </select>
                                <small style="color: #9f191f" id="msg_doctorUsuario"></small>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-sm-2" for="">Usuario:</label>
                            <div class="col-sm-8">
                                <input type="text" name="usu_usuario" class="form-control " id="usu_usuario" onkeyup="FormValidacion($(this),true)" autocomplete="off">
                                <small style="color: #9f191f" id="msg_usuario_repit"></small>
                                <span class="hidden" id="idcedusu"></span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="" class="control-label col-sm-2">Password:</label>
                            <div class="col-sm-8">
                                <div class="input-group">
                                    <input type="password" class="form-control input-sm" id="usu_password" name="usu_password" onkeyup="FormValidacion($(this), true);">
                                    <div class="input-group-addon btn" onclick="passwordMostrarOcultar('mostrar');"><i class="fa fa-eye"></i></div>
                                    <div class="input-group-addon btn" onclick="passwordMostrarOcultar('ocultar');"><i class="fa fa-eye-slash"></i></div>
                                </div>
                                <small style="color: #9f191f" id="msg_password_d2"></small>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="" class="control-label col-sm-2" >Confirmar Password:</label>
                            <div class="col-sm-8">
                                <input type="password" class="form-control input-sm" id="usu_confir_password" name="usu_confir_password" onkeyup="FormValidacion($(this), true);">
                                <small style="color: #9f191f" id="msg_password"></small>
                            </div>
                        </div>


                        <div class="form-group col-xs-12 col-md-12">
                            <hr>
                            <label class="bold">ASIGNAR PERMISOS</label>
                            <div class="table-responsive">

                                <table class="table" width="100%">
                                    <thead style="background-color: #e9edf2">
                                        <tr>
                                            <th width="20%" style="font-size: 1.3rem">PERFIL</th>
                                            <th width="30%" style="font-size: 1.3rem">CREAR PERFIL</th>
                                            <th width="30%" style="font-size: 1.3rem">ELIMINAR PERFIL</th>
<!--                                            <th width="15%" style="font-size: 1.3rem">CONSULTAR</th>-->
<!--                                            <th width="15%" style="font-size: 1.3rem">AGREGAR</th>-->
<!--                                            <th width="15%" style="font-size: 1.3rem">MODIFICAR</th>-->
<!--                                            <th width="15%" style="font-size: 1.3rem">ELIMINAR</th>-->
                                            <th width="30%" style="font-size: 1.3rem">MODIFICAR PERFIL</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr style="background-color: #f4f4f4">
                                            <td width="100%">
                                                <select  id="tipoUsuarioPerfil" name="tipoUsuarioPerfil" class="form-control" style="width: 100%">
                                                    <option value=""></option>
                                                </select>
                                                <small style="color: #9f191f; display: block" id="msg_permisos"></small>
                                            </td>

                                            <td> <button class="btn" style="background-color: #D5F5E3; color: green; font-weight: bolder" data-toggle="modal" data-target="#modalCreatePerfil" id="addPerfilUsers">Agregar  Perfil</button> </td>
                                            <td> <button class="btn" style="background-color: #FADBD8; color: red; font-weight: bolder" id="deletePerfilUsers" >Eliminar Perfil</button> </td>
                                            <td> <button class="btn" style="background-color: #D5F5E3; color: green; font-weight: bolder"  id="modificarPerfilUsers">Modificar  Perfil</button> </td>

<!--                                            <td  width="100%" class="text-center" style="font-size: 4rem"> <input type="checkbox" id="chek_consultar" disabled class="disabled_link3"> </td>-->
<!--                                            <td  width="100%" class="text-center" style="font-size: 4rem"> <input type="checkbox" id="chek_agregar" disabled class="disabled_link3">   </td>-->
<!--                                            <td  width="100%" class="text-center" style="font-size: 4rem"> <input type="checkbox" id="chek_modificar" disabled class="disabled_link3"> </td>-->
<!--                                            <td  width="100%" class="text-center" style="font-size: 4rem"> <input type="checkbox" id="chek_eliminar" disabled class="disabled_link3">  </td>-->

                                        </tr>
                                    </tbody>

                                </table>
                                <br>
                            </div>
                            <br>
                        </div>

                        <div class="form-group col-xs-12 col-md-12">
                            <div class="pull-right">
                                <a class="btn btnhover " style="font-weight: bolder; color: green" id="nuevoUpdateUsuario"> Guardar </a>
                            </div>
                        </div>

                    </div>


                    <!--modal create perfil-->

                    <div id="modalCreatePerfil"  class="modal fade" role="dialog" data-backdrop="static" data-keyboard="false" >
                        <div class="modal-dialog" style="width: 60%; margin: 1% auto">

                            <div class="modal-content" >

                                <div class="modal-header modal-diseng">
                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                    <h4 class="modal-title" ><span name="titleperfil" >PERFIL</span></h4>
                                    <input type="text" class="hidden" name="idperfilUsersEntity">
                                </div>

                                <div class="modal-body">
                                    <div class="row">
                                        <div class="form-group col-md-12 col-lg-12">
                                            <div class="form-horizontal">
                                                <div class="form-group">
                                                    <label for="" class="control-label col-sm-2">Perfil</label>
                                                    <div class="col-sm-9">
                                                        <input type="text" class="form-control" name="nomPerfil" id="nomPerfil" onkeyup="FormValidationPerfil(null)">
                                                    </div>
                                                </div>
                                                <div class="form-group" style="margin-bottom: 5px">
                                                    <div class="col-md-12">
                                                        <p style="border-bottom: 1px solid #f0f0f0; font-weight: bolder">Modulos</p>
                                                        <div class="elemenErrorPermisosModule"></div>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <div class="col-md-12">
                                                        <div class="col-centered" style="width: 100%" >
                                                            <div class="table-responsive" style="width: 100%; padding: 5px">
                                                                <table class="table" width="100%">
                                                                    <thead>
                                                                        <tr>
                                                                            <th width="40%" rowspan="2">Modulo</th>
                                                                            <th width="20%" rowspan="2" class="text-center">Permiso</th>
                                                                            <th width="80%" rowspan="1" colspan="4" style="text-align: center">accion</th>
                                                                        </tr>
                                                                        <tr>
                                                                            <th width="10%" class="text-center" >Consultar</th>
                                                                            <th width="10%" class="text-center" >Agregar</th>
                                                                            <th width="10%" class="text-center" >Modificar</th>
                                                                            <th width="10%" class="text-center" >Eliminar</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>

                                                                        <?php

                                                                            $cn        = new CONECCION_ENTIDAD(); //connecion a la empresa entida
                                                                            $td        = "";
                                                                            $tieneHijo = 0;
                                                                            $idPadre   = 0;

                                                                            $idindex  = [];
                                                                            $puedo    = true;
                                                                            $espacio  = "&nbsp;";
                                                                            $padre    = 0;
                                                                            $nivel = 0;

                                                                            while ($puedo)
                                                                            {


                                                                                $query2 = "select s.rowid , s.name , s.id_padre, s.status , 
                                                                                                  (select count(*) from tab_modulos_status h where h.id_padre = s.rowid) as tienehijos , 		  
                                                                                                  ifnull((select h.name from tab_modulos_status h where h.rowid = s.id_padre limit 1),'') as Padre 
                                                                                           from tab_modulos_status s 
                                                                                           where s.rowid > 0 ";
                                                                                if(count($idindex)>0){
                                                                                    $query2 .= " and s.rowid not in(".implode(',', $idindex).") ";
                                                                                }
                                                                                if($tieneHijo==1){
                                                                                    $query2 .= " and s.id_padre = ". $idPadre;
                                                                                    $nivel++;
                                                                                }else{
                                                                                    $nivel=0;
                                                                                }

//                                                                                print_r($query2); echo '<pre>'; echo '<br>';
                                                                                $rsmod    = $cn::CONNECT_ENTITY()->query($query2);

                                                                                if($rsmod && $rsmod->rowCount()>0)
                                                                                {
                                                                                    while ($objModPadre = $rsmod->fetchObject())
                                                                                    {

                                                                                        /**Parametros id principal*/
                                                                                        $idmod = $objModPadre->rowid;



                                                                                        /**Parametros*/
                                                                                        $modulos    = "";
                                                                                        $noneblock  = "";
                                                                                        $TienePadre = "";
                                                                                        $TieneHijo  = "";
                                                                                        $nameP      = "";

                                                                                        $insertGlob = "";

                                                                                        if($objModPadre->tienehijos>0){
                                                                                            $insertGlob = " data-idmodule='".$objModPadre->rowid."' ";
                                                                                        }


                                                                                        $active = 0;
                                                                                        $action_user = [];
                                                                                        $nameidModPadre = str_replace(' ','_', $objModPadre->name);


                                                                                        $espacioshijosPadres = "&nbsp;";
                                                                                        for($i = 0; $i <= $nivel; $i++){
                                                                                            $espacioshijosPadres .= "&nbsp&nbsp&nbsp&nbsp";
                                                                                        }

                                                                                        #se valida si el modulo tiene asociado hijos (sub-modulos)
                                                                                        if($objModPadre->tienehijos > 0 ){
                                                                                            $TieneHijo = "font-weight: bolder;";
                                                                                        }

                                                                                        #se valida si el modulo tiene asociado Padres (sub-modulos)
                                                                                        if($objModPadre->Padre != ""){

                                                                                            $noneblock  =  "noneMod";
                                                                                            $TienePadre =  "Mod_".(str_replace(' ','_', $objModPadre->Padre))." ";
                                                                                            $nameP      =  "Mod_".str_replace(' ','_', $objModPadre->Padre);
                                                                                        }

                                                                                        /*
                                                                                         * consultar 1
                                                                                         * agregar 2
                                                                                         * modificar 3
                                                                                         * eliminar 4
                                                                                         * */


                                                                                        if(isset($_GET['mod']) && $_GET['mod'] == "true")
                                                                                            $checked = true;

                                                                                        if(isset($_GET['creat']) && $_GET['creat'] == "true")
                                                                                            $checked = false;


                                                                                        $chechedActive = "";
                                                                                        if($checked==true){
                                                                                            for ($i=1;$i<=4;$i++){
//                                                                                                $chechedActive += (($checked == true)?((checkPermissModule($idmod,$i, (isset($_GET['id'])?$_GET['id']:0)) == true)?1:0):0);
                                                                                            }
                                                                                        }


                                                                                        $modulos = "
                                                                                            <tr style='cursor: pointer' class='".$noneblock."'  data-submodule='".$nameP."'  > 
                                                                                                <td id='Mod_".$nameidModPadre."' class='".$TienePadre."'  onclick='viewactionpermsis($(this),true)' data-thijo='".(($TieneHijo!="")?"1":"0")."' style='".$TieneHijo."'  ".(($TieneHijo!="")?"colspan='5'":"")." > $espacioshijosPadres".$objModPadre->name."</td>";


                                                                                        if((($TieneHijo!="")?1:0)==0){

                                                                                            $modulos .="
                                                                                                <td class='text-center'><input type='checkbox' id='active_".$nameidModPadre."'    ".(($chechedActive > 0)?"checked":"")."    class='active' onclick='viewactionpermsis($(this),false)'></td>
                                                                                                
                                                                                                <td class='text-center'><input type='checkbox' id='consultar_".$nameidModPadre."'   class='consultar checked_module' data-idpermiso='1' data-moduleid='".$objModPadre->rowid."' ></td>
                                                                                                <td class='text-center'><input type='checkbox' id='agregar_".$nameidModPadre."'     class='agregar checked_module' data-idpermiso='2' data-moduleid='".$objModPadre->rowid."'></td>
                                                                                                <td class='text-center'><input type='checkbox' id='modificar_".$nameidModPadre."'   class='modificar checked_module' data-idpermiso='3' data-moduleid='".$objModPadre->rowid."' ></td>
                                                                                                <td class='text-center'><input type='checkbox' id='eliminar_".$nameidModPadre."'    class='eliminar checked_module' data-idpermiso='4' data-moduleid='".$objModPadre->rowid."' ></td>
                                                                                                
                                                                                            </tr>
                                                                                   
                                                                                            ";

                                                                                        }


                                                                                        $td .= $modulos;

                                                                                        if($objModPadre->tienehijos != 0){
                                                                                            $tieneHijo     = 1;
                                                                                            $idPadre       = $objModPadre->rowid; //id principal
                                                                                        }
                                                                                        else{
                                                                                            $tieneHijo    = 0;
                                                                                            $idPadre      = 0;
                                                                                        }



                                                                                        $idindex[] = $objModPadre->rowid;

                                                                                        if($tieneHijo == 1) //si tiene 1 salta del ciclo
                                                                                        {
                                                                                            break;
                                                                                        }

                                                                                    }

                                                                                }else{
                                                                                    $puedo = false;
                                                                                }

                                                                            }

                                                                            print $td;

                                                                            //print_r($td); die();

                                                                        ?>

                                                                    </tbody>

                                                                </table>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="modal-footer" >
                                    <a class="btn btnhover " style="font-weight: bolder; " data-dismiss="modal" > Cerrar </a>
                                    <a class="btn btnhover " style="font-weight: bolder; color: green" id="guardarPerfil"> Guardar </a>
                                </div>
                            </div>

                        </div>
                    </div>


                <?php  } ?>

                </div>

            <?php

                }

            ?>

        </div>

    </div>
</div>



<!--MODAL CREAR ODONTOLOGO ---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------->
<div id="modal_conf_doctor" class="modal fade" role="dialog" data-backdrop="static">
    <div class="modal-dialog modal-lg">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header modal-diseng">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"> <span data-id="0" id="accion">NUEVO</span> ODONTÓLOGO </h4>
            </div>
            <div class="modal-body">

                <div class="margin">

                    <div class="tab-content">
                        <div class="row">
                            <div class="form-group col-md-12 no-margin">

                                <div class="form-group no-margin" >
                                    <p class="text-center">
                                        <label for="icon_doct">
                                            <img id="icon_usuario_doct"  src="<?= DOL_HTTP .'/logos_icon/logo_default/doct-icon.ico' ;?>" class="img-circle" width="100px" height="100px" alt="" style="cursor:pointer;">
                                            <input type="file" id="icon_doct" style="display: none">
                                        </label>
                                        <span style="display: block"> </span>
                                        <input type="text" class="hidden" id="valid_ico">
                                    </p>
                                </div>

                                <div class="form-horizontal">
                                    <div class="form-group">
                                        <label class="control-label col-sm-3" for="">Epecialidad:</label>
                                        <div class="col-sm-8">
                                            <select name="" id="especialidad_doct" style="width: 100%" class="form-control  input-sm">
                                                <option value="0">General</option>
                                                <?php
                                                    $sql35 = "select es.rowid,  es.tms as fecha ,es.nombre_especialidad , es.descripcion from tab_especialidades_doc es;";
                                                    $rs35 = $db->query($sql35);
                                                    if($rs35->rowCount()>0) {
                                                        while ($rows35 = $rs35->fetchObject()) {
                                                            echo "<option value='$rows35->rowid'>$rows35->nombre_especialidad</option>";
                                                        }
                                                    }
                                                ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="control-label col-sm-3" for="">Nombre:</label>
                                        <div class="col-sm-8">
                                            <input type="text" id="nombre_doct" class="form-control  nombre_doct" onkeyup="FormValidationOdontolotoMod()">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-sm-3" for="">Apellido:</label>
                                        <div class="col-sm-8">
                                            <input type="text" name="" class="form-control apellido_doct" id="apellido_doct" onkeyup="FormValidationOdontolotoMod()">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-sm-3" for="">Cedula:</label>
                                        <div class="col-sm-8">
                                            <input type="text" name="" class="form-control rucedula_doct" id="rucedula_doct" onkeyup="FormValidationOdontolotoMod(false, $(this))" data-idcedula >
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-sm-3" for="">Telef. Cel:</label>
                                        <div class="col-sm-8">
                                            <input type="text" name="" class="form-control celular_doct" id="celular_doct" onkeyup="FormValidationOdontolotoMod()">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-sm-3" for="">Fax:</label>
                                        <div class="col-sm-8">
                                            <input type="text" name="" class="form-control TelefonoConvencional_doct" id="TelefonoConvencional_doct" onkeyup="FormValidationOdontolotoMod()">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-sm-3" for="">E-mail:</label>
                                        <div class="col-sm-8">
                                            <input type="text" name="" class="form-control email_doct" id="email_doct" onkeyup="FormValidationOdontolotoMod()">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-sm-3" for="">Ciudad:</label>
                                        <div class="col-sm-8">
                                            <input type="text" name="" class="form-control ciudad_doct" id="ciudad_doct" onkeyup="FormValidationOdontolotoMod()">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-sm-3" for="">Dirección:</label>
                                        <div class="col-sm-8">
                                            <input type="text" name="" class="form-control direccion_doct" id="direccion_doct" onkeyup="FormValidationOdontolotoMod()">
                                        </div>
                                    </div>

                                </div>


                                <div class="form-group col-md-12 no-margin">
                                    <div class="form-group no-margin pull-right">
                                        <a class="btn btnhover " style="font-weight: bolder" data-dismiss="modal" > Cerrar </a>
                                        <a class="btn btnhover " style="font-weight: bolder; color: green" id="guardar_informacion_odontologos" > Aceptar </a>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<!--MODAL ESPECIALIDADES ---------------------------------------------------------------------------------------------->
<div id="ModalConfEspecialidades" class="modal fade" role="dialog" data-backdrop="static">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header modal-diseng">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">  <span data-id="0" id="accion_especialidad">NUEVO</span> <span>ESPECIALIDAD</span> </h4>
            </div>
            <div class="modal-body">
                <div class="form-horizontal">

                    <div class="form-group">
                        <label class="control-label col-sm-2" >Especialidad:</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="especialidad_nombre" >
                            <small style="color: #9f191f" id="msg_especialidad"></small>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label col-sm-2" >Descripción:</label>
                        <div class="col-sm-10">
                            <textarea id="especialidad_descripcion" class="form-control" cols="30" rows="5"></textarea>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
<!--                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>-->
                <a class="btn btnhover " style="font-weight: bolder; color: green" id="guardar_conf_especialidad"> Aceptar </a>
            </div>
        </div>

    </div>
</div>



<!--JAVASCRIPT DE ESPECIALIDADES-->
<?php if(isset($_GET['view']) && GETPOST("v") == 'specialties'){?>
    <script src="<?= DOL_HTTP .'/application/system/configuraciones/js/odontespecialidades.js'; ?>"></script>
<?php }?>

<!--JAVASCRIPT DE ODONTOLOGOS-->
<?php if(isset($_GET['view']) && GETPOST("v") == 'dentist'){?>
    <script src="<?= DOL_HTTP .'/application/system/configuraciones/js/NewEditoOdontUsuario.js'; ?>"></script>
<?php }?>

<!--JAVASCRIPT DE USUARIOS-->
<?php if(isset($_GET['view']) && GETPOST("v") == 'users'){?>
    <script src="<?= DOL_HTTP .'/application/system/configuraciones/js/UsuariosOdonti.js'; ?>"></script>
<?php }?>
