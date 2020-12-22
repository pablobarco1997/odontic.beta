<?php

    $idprestacion = 0;
    $accion       = "nuevo"; #nueva prestacion

    if(isset($_GET['act']))
    {
        if( $_GET['act'] == 'mod') #modificar prestacion
        {
            $accion = 'modificar';
            $idprestacion = (isset($_GET['id'])) ? $_GET['id'] : 0; #id de la prestacion
        }
    }


    $optionPrestacion = "";
    $sql = "SELECT * FROM tab_conf_categoria_prestacion;";
    $rs = $db->query($sql);
    if($rs->rowCount() > 0 )
    {
        while ($row =  $rs->fetchObject())
        {
            $optionPrestacion .= "<option value='$row->rowid'>$row->nombre_cat</option>";
        }
    }


?>
<!--VARIABLES GLOBALES DE LOS SUBMODULOS-->
<script>

    $accion_prestacion       = "<?= $accion ?>";
    $idprestacion_prestacion = "<?= $idprestacion ?>";

</script>

<style>

</style>

<div class="box box-solid">
        <div class="box-header with-border">
            <div class="form-group col-xs-12 col-sm-12 col-md-12 no-margin">
                <h4 class="no-margin"><span><b>Prestaciones</b></span></h4>
            </div>
        </div>

        <div class="box-body">

            <div class="form-group col-centered col-md-8 col-lg-8 col-xs-12 col-sm-12" >

                <div class="form-group col-xs-12 col-md-12 ">
                    <ul id="confulprest" style="width: 600px; float: right; list-style: none">
                        <?php if($accion == 'modificar'){ ?>
                            <li><a href="<?= DOL_HTTP ?>/application/system/configuraciones/index.php?view=form_prestaciones"  class="btn btnhover" >Nueva Prestación</a></li>
                        <?php }?>
                        <li>  <a href="#modal_list_prestacion" id="masInformacion" data-toggle="modal" style="text-decoration-line: underline" class="btn btnhover <?= (!PermitsModule(8,1)?"disabled_link3":"") ?>" onclick="load_table_prestaciones()" > <i class="fa fa-info"></i> Información</a></li>
                    </ul>
                </div>

                <div class="form-group col-xs-12 col-md-12">
                    <div class="form-horizontal">

                        <div class="form-group">
                            <label class="control-label col-sm-3" for="conf_cat_prestaciones">Categoría:</label>
                            <div class="col-sm-8 col-xs-12">
                                <div class="input-group">
                                    <select name="conf_cat_prestaciones" id="conf_cat_prestaciones" class=" invalic_prestaciones" style="width: 100%" onchange="validNuevUpdate()">
                                        <option value=""></option>
                                        <?= $optionPrestacion ?>
                                    </select>
                                    <span class="input-group-addon" style="cursor: pointer" data-toggle="modal" data-target="#modal_conf_categoria" onclick="nuevoUpdateCategoria()"><i class="fa fa-plus"></i></span>
                                    <span class="input-group-addon" style="cursor: pointer" data-toggle="modal" data-target="#ModaleliminarConfCatDesc" onclick="eliminar_categoria_desc_prestacion('categoria')"><i class="fa fa-minus"></i></span>
                                </div>
                                <small style="color: red; display: block;" id="msg_categoria"></small>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-sm-3 " for="prestacion_descr">nombre de Prestación:</label>
                            <div class="col-sm-8">
                                <input type="email" class="form-control invalic_prestaciones" id="prestacion_descr" name="prestacion_descr" onkeyup="validNuevUpdate()">
                                <small style="color: red;" id="msg_prestaciones"></small>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-sm-3" for="valorPrestacion">Costo $:</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control invalic_prestaciones" id="valorPrestacion" autocomplete="off" name="valorPrestacion" onkeyup="validNuevUpdate()">
                                <small style="color: red;" id="msg_valor"></small>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-sm-3" for="explicacionInfo">Explicación (<small>opcional</small>):</label>
                            <div class="col-sm-8">
                                <textarea name="explicacionInfo" id="explicacionInfo" class="form-control" cols="30" rows="4"></textarea>
<!--                                <small style="color: red;" id="msg_valor"></small>-->
                            </div>
                        </div>

                        <div class="form-group hide"> <!-- Laboratorio -->
                            <label class="control-label col-sm-2" for="laboratorioConf">Laboratorio &nbsp; <i class="fa fa-dollar"></i>:</label>
                            <div class="col-sm-8">
                                <div class="input-group">
                                    <select name="laboratorioConf" id="laboratorioConf" class="form-control">
                                        <option value=""></option>
                                    </select>
                                    <div class="input-group-addon" style="cursor: pointer">
                                        <i class="fa fa-plus"></i>
                                    </div>
                                    <div class="input-group-addon" style="cursor: pointer">
                                        <i class="fa fa-minus"></i>
                                    </div>
                                </div>
                                <small style="color: red;" id="msg_laboratorio"></small>
                            </div>
                        </div>

                        <div class="form-group hide"> <!--Asociar Descuento-->
                            <label class="control-label col-sm-2" for="convenioConf">Asociar Descuento <i class="fa fa-dollar"></i></label>
                            <div class="col-sm-8">
                                <div class="input-group">
                                    <select name="convenioConf" id="convenioConf" class="select2_max_ancho">
                                        <option value=""></option>
                                        <?php

                                        $sql = "SELECT * FROM tab_conf_convenio_desc;";
                                        $rs = $db->query($sql);
                                        if($rs->rowCount() > 0 )
                                        {
                                            while ($row =  $rs->fetchObject())
                                            {
                                                echo "<option value='$row->rowid'>$row->nombre_conv</option>";
                                            }
                                        }

                                        ?>
                                    </select>
                                    <div class="input-group-addon" style="cursor: pointer" data-toggle="modal" data-target="#modal_conf_convenio">
                                        <i class="fa fa-plus"></i>
                                    </div>
                                    <div class="input-group-addon" style="cursor: pointer" data-toggle="modal" data-target="#Modaldescuento">
                                        <i class="fa fa-minus"></i>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                 </div>
                <div class="row">
                    <div class="col-xs-12">
                        <button class="btn btnhover btn-block" id="guardar_prestacion" style="color: green;"> <b>GUARDAR</b></button>
                    </div>
                </div>

            </div>
            <br>


        </div>
</div>

<!--MODAL CONFIGURACION CONVENIO-->
<div id="modal_conf_convenio" class="modal fade" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header modal-diseng">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">AGREGAR DESCUENTO</h4>
            </div>
            <div class="modal-body">

                <div style="padding: 10px">

                    <div class="form-group">
                        <small style="color:#eb9627; font-weight: bolder "> <i class="fa fa-info-circle"></i> El descuento se calculara en porcentage - Tener en cuanta que el descuento solo si aplicara si la prestacion tiene asociado dicho descuento </small>
                    </div>

                    <div class="form-group">
                        <label for="">Nombre</label>
                        <input type="text" id="nomb_conv" class="form-control input-sm">
                    </div>
                    <div class="form-group">
                        <label for="">Descripción</label>
                        <textarea name="" class="form-control input-sm" id="descrip_conv" rows="3"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="">valor</label>
                        <input type="text" id="valor_conv" class="form-control input-sm mask">
                        <small id="msg_descuento" style="color: red"></small>
                    </div>

                </div>

            </div>
            <div class="modal-footer">
                <a href="#" class="btn btnhover" style="font-weight: bolder; color: green" onclick="nuevoUpdateConvenio('nuevo')" id="guardar_convenio_conf">Aceptar</a>
                <a href="#" class="btn btnhover" style="font-weight: bolder;"  data-dismiss="modal">Close</a>
            </div>
        </div>

    </div>
</div>

<!-- MODAL CONFIGURACION CATEGORIA -->
<div id="modal_conf_categoria" class="modal fade" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header modal-diseng">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title" ><span>CATEGORIA DE PRESTACIÓN</span></h4>
            </div>
            <div class="modal-body">

                <div style="padding: 10px">
                    <div class="form-group">
                        <small style="color:#eb9627; font-weight: bolder "> <i class="fa fa-info-circle"></i> Crear Categoria </small>
                    </div>
                    <div class="form-group">
                        <label for="">Nombre</label>
                        <input type="text" id="nomb_cat" class="form-control input-sm" onclick="validCategoria()">
                    </div>
                    <div class="form-group">
                        <label for="">Descripción</label>
                        <textarea name="" class="form-control input-sm" id="descrip_cat" rows="3"></textarea>
                    </div>

                </div>

            </div>
            <div class="modal-footer">
                <a href="#" class="btn btnhover" style="font-weight: bolder; color: green" id="guardar_categoria_conf">Aceptar</a>
                <a href="#" class="btn btnhover" style="font-weight: bolder;"  data-dismiss="modal">Cerrar</a>
            </div>
        </div>

    </div>
</div>


<!-- LISTA INFORMATIVA DE LAS PRESTACIONES -->
<div id="modal_list_prestacion" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg" style="margin: 2% auto; width: 70%" >

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header modal-diseng">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><span>Lista de Prestaciones</span></h4>
            </div>
            <div class="modal-body">
                   <div class="form-group">
                       <div class="table-responsive">
                           <table class="table table-striped" id="listprestacionestable" width="100%">
                               <thead>
                                    <tr>
                                        <th WIDTH="5%">Fecha creación</th>
                                        <th WIDTH="35%">Descripción</th>
                                        <th WIDTH="15%">Categoría</th>
                                        <th WIDTH="10%">Costo de Paciente $</th>
                                        <th width="5%">Acciones</th>
                                    </tr>
                               </thead>
                           </table>
                       </div>
                   </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!--MODAL ELIMINAR CATEGORIA DE LA PRESTACION-->
<div class="modal fade" id="ModaleliminarConfCatDesc" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header modal-diseng">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title" id="tomar" data-id="0" data-subaccion="">ELIMNAR</h4>
            </div>
            <div class="modal-body">
                <p>Seguro desea <b>Eliminar este registro seleccionado ?</b> </p>
            </div>
            <div class="modal-footer">
                <a href="#" class="btn btnhover" style="font-weight: bolder; color: green" id="eliminarConfCategoriaDescuento">Aceptar</a>
                <a href="#" class="btn btnhover" style="font-weight: bolder;"  data-dismiss="modal">Close</a>
            </div>
        </div>

    </div>
</div>

<div class="modal fade" id="Modaldescuento" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header modal-diseng">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title" id="tomar" data-id="0" data-subaccion="">ELIMNAR</h4>
            </div>
            <div class="modal-body">
                <p>Seguro desea <b>Eliminar este registro seleccionado ?</b> </p>
                <p> <b> <small style="color: #eb9627; font-weight: bolder "> <i class="fa fa-info-circle"></i> Tener en cuenta que no puede eliminar un descuento si se encuentra asociado a una prestacion o a un paciente</small> </b> </p>
            </div>
            <div class="modal-footer">
                <a href="#" class="btn btnhover" style="font-weight: bolder; color: green" id="eliminarDescuento" onclick="nuevoUpdateConvenio('eliminar')">Aceptar</a>
                <a href="#" class="btn btnhover" style="font-weight: bolder;"  data-dismiss="modal">Close</a>
            </div>
        </div>

    </div>
</div>


<!--JAVASCRIPT-->

<?php if(isset($_GET['view']) && GETPOST("view") == 'form_prestaciones'){?>
    <script src="<?= DOL_HTTP .'/application/system/configuraciones/js/prestaciones.js'; ?>"></script>
<?php }?>