
<?php

if(isset($_GET['v'])){


}else{
    echo "<h3> Ocurrio un Error con la Vista Principal</h3>";
    die();
}


$v = null;
if(isset($_GET['v']) && !empty($_GET['v'])){
    $v = $_GET['v'];
}else{

    echo "<h3> Ocurrio un Error con la Vista Principal</h3>";
    die();
}

?>

<div class="box box-solid">
    <div class="box-header with-border">
        <div class="form-group col-xs-12 col-sm-12 col-md-12 no-margin">
            <h4 class="no-margin"><span><b>
                        <?php
                            if($v=="laboratorios")
                                echo "Laboratorios Clinicos";

                            if($v=="prestacionlab")
                                echo "Prestaciones de Laboratorios Clinicos";
                        ?>
                    </b></span></h4>
        </div>
    </div>

    <div class="box-body">
        <br>

        <div class="form-group col-centered col-xs-12 col-md-10 col-lg-10">

            <div class="form-group col-md-12 " style="padding: 0px">
                <ul class="list-inline">
                    <li><a class="btn " href="<?= DOL_HTTP?>/application/system/configuraciones/index.php?view=form_laboratorios_conf&v=laboratorios" style="border-left: 2px solid #212f3d; color: #333333"> <i class="fa fa-building-o"></i> &nbsp; Laboratorios</a> </li>
                    <li>&nbsp;&nbsp;</li>
                    <li><a class="btn " href="<?= DOL_HTTP?>/application/system/configuraciones/index.php?view=form_laboratorios_conf&v=prestacionlab" style="border-left: 2px solid #212f3d; color: #333333"> <i class="fa fa-list-ul"></i> &nbsp; Prestaciones de Laboratorio</a></li>
                    <li>&nbsp;&nbsp;</li>
                    <li><a class="btn " href="#" style="border-left: 2px solid #212f3d; color: #333333"> <i class="fa fa-sticky-note-o"></i> &nbsp; Solicitudes</a></li>
                </ul>
                <br>
            </div>


            <?php if($v=='laboratorios') { ?>

            <div id="cont_laboratorio" class="row">

                <div class="form-group col-md-12 col-lg-12 col-xs-12">
                    <ul class="list-inline" style="border-bottom: 1px solid #333333; border-top: 1px solid #333333; padding: 3.5px">
                        <li><a href="#addModificarLaboratorio" class="btnhover " id="crearLaboratorio" data-toggle="modal"  style="font-weight: bolder; color: #333333; "> &nbsp;&nbsp;<i class="fa fa-list"></i> &nbsp;  crear Laboratorio</a></li>
                        <li> </li>
                    </ul>
                </div>


                <div class="form-group col-md-12 col-lg-12 col-xs-12">
                    <div class="table-responsive">
                        <table class="table table-striped" id="laboratorio_list" width="100%">
                            <thead>
                                <th>&nbsp;</th>
                                <th>Laboratorio</th>
                                <th>Información Adicional</th>
                                <th>Prestaciones Realizadas x Mes <?= date('M')?></th>
                            </thead>
                        </table>
                        <br><br><br>
                    </div>
                </div>
            </div>


            <!--modal add modificar Laboratorio-->
            <div class="modal fade" id="addModificarLaboratorio" data-backdrop="static" role="dialog">
                <div class="modal-dialog">

                    <!-- Modal content-->
                    <div class="modal-content" >
                        <div class="modal-header modal-diseng"">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h4 class="modal-title"><span>Laboratorio</span></h4>
                            <input type="text" class="hidden" id="InputLaboratorio" data-idlaboratorio="0" data-subaccion="nuevo">
                        </div>

                        <div class="modal-body">
                            <div class="row">
                                <div class="form-group col-md-12 col-xs-12 col-lg-12">
                                    <div class="form-horizontal">
                                        <div class="form-group">
                                            <label class="control-label col-sm-3">Nombre</label>
                                            <div class="col-sm-7">
                                                <input type="text" class="form-control" id="nombre_laboratorio" onkeyup="FormaValidLaboratorio()">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-sm-3">Dirección</label>
                                            <div class="col-sm-7">
                                                <input type="text" class="form-control" id="direccion_laboratorio" onkeyup="FormaValidLaboratorio()">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-sm-3">Teléfono</label>
                                            <div class="col-sm-7">
                                                <input type="text" class="form-control" id="telefono_laboratorio" onkeyup="FormaValidLaboratorio()">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-sm-3">Información Adicional</label>
                                            <div class="col-sm-7">
                                                <textarea class="form-control" id="infoAdicional_laboratorio"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-success" id="nuevoUpdateLaboratorio"> Guardar </button>
                        </div>
                    </div>

                </div>
            </div>

            <script src="<?= DOL_HTTP ?>/application/system/configuraciones/js/laboratorios_main.js"></script>

            <script>
                $(window).on("load", function() {
                    listLaboratorios();
                });
            </script>

            <?php } ?>


            <?php if($v=='prestacionlab') { ?>


                <div id="cont_Prestacionlaboratorio" class="row">

                    <div class="form-group col-md-12 col-lg-12 col-xs-12">
                        <ul class="list-inline" style="border-bottom: 1px solid #333333; border-top: 1px solid #333333; padding: 3.5px">
                            <li><a href="#ModalPrestacion_LaboratorioClinico" data-toggle="modal" class="btnhover " id=""  style="font-weight: bolder; color: #333333; "> &nbsp;&nbsp;<i class="fa fa-clipboard"></i> &nbsp;  Agregar o Modificar Prestación </a></li>
                            <li><a href="#" class="btnhover " id=""  style="font-weight: bolder; color: #333333; " onclick="tableDinamicPrestacion('PrestacionesXlaboratorio')" > &nbsp;<i class="fa fa-files-o"></i> Prestaciones x Laboratorio</a> </li>
                            <li><a href="#" class="btnhover " id=""  style="font-weight: bolder; color: #333333; " onclick="tableDinamicPrestacion('PagosRealizado')" > &nbsp; <i class="fa fa-bar-chart"></i> (Pagos o Abonos) Realizados</a> </li>
                            <li><a href="#" class="btnhover hidden" id=""  style="font-weight: bolder; color: #333333; " onclick="tableDinamicPrestacion('PagosRealizado')" > &nbsp; <i class="fa fa-bar-chart"></i> Por Pagar (Tratamientos)</a> </li>
                            <li><a href="#FiltrarAgenda" class="btnhover " id=""  style="font-weight: bolder; color: #333333; " data-toggle="collapse" > &nbsp;<i class="fa fa-search"></i> Filtrar Información </a> </li>
                        </ul>
                    </div>

                    <div class="form-group col-md-8 col-lg-8 col-xs-12 margin-bottom">
                        <table id="informacionPrestacion" class="none" style="display: none;" width="100%">
                            <tr style="border-top: 1px solid #e2e2e2">
                                <td style="width: 25%; font-weight: bolder">Nombre:</td>
                                <td id="nameLab" style="padding-left: 10px"></td>
                            </tr>
                            <tr style="border-top: 1px solid #e2e2e2">
                                <td style="width: 25%; font-weight: bolder">Dirección:</td>
                                <td id="DirecLab" style="padding-left: 10px"></td>
                            </tr>
                            <tr style="border-top: 1px solid #e2e2e2">
                                <td style="width: 25%; font-weight: bolder">Teléfono:</td>
                                <td id="telefLab" style="padding-left: 10px"></td>
                            </tr>
                            <tr style="border-top: 1px solid #e2e2e2">
                                <td style="width: 25%; font-weight: bolder">Información Adicional:</td>
                                <td id="infoLab" style="padding-left: 10px"></td>
                            </tr>
                        </table>
                        <br>
                    </div>

                    <div id="FiltrarAgenda" class="form-group col-xs-12 col-md-12 collapse" aria-expanded="true" style="">
                        <div class="form-group col-md-12 col-xs-12" style="background-color: #f4f4f4; padding: 25px">
                            <h3 class="no-margin"><span>Filtrar Prestaciones x Laboratorio</span></h3>
                            <div class="row">
                                <div class="form-group col-md-3 col-sm-12 col-xs-12">
                                    <label for="">Fecha</label>
                                    <div class="input-group form-group rango" style="margin: 0">
                                        <input type="text" class="form-control filtroFecha  " readonly="" id="startDate" value="">
                                        <span class="input-group-addon" style="border-radius: 0"><i class="fa fa-calendar"></i></span>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group col-md-12 no-margin">
                                    <ul class="list-inline pull-right no-margin">
                                        <li>  <button class="limpiar btn   btn-block  btn-default" style="float: right; padding: 10px"> &nbsp; &nbsp; Limpiar &nbsp; &nbsp;</button> </li>
                                        <li>  <button class="aplicar btn   btn-block  btn-success" style="float: right; padding: 10px"> &nbsp;  &nbsp;Aplicar busqueda &nbsp;</button> </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>


                    <!--table prestaciones de laboratorios-->
                    <div class="form-group col-md-12 col-lg-12 col-xs-12 margin-bottom">
                        <div class="table-responsive">
                            <table id="prestacionLaboratorio" class="table" width="100%"></table>
                        </div>
                        <br><br>
                    </div>

                </div>



                <div class="modal fade" id="selecioneLaboratorio" data-backdrop="static" role="dialog">
                    <div class="modal-dialog">

                        <div class="modal-content" >
                            <div class="modal-header modal-diseng"">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                <h4 class="modal-title"><span>Laboratorios</span></h4>
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="form-group col-md-12 col-xs-12 col-sm-12">
                                        <div class="form-group">
                                            <label for="">Selecione un Laboratorio</label>
                                            <select id="laboratorioPrestSeleccion" class="form-control" style="width: 100%">
                                                <option value=""></option>
                                                <?php
                                                    $sql = "select rowid , name , direccion , telefono , info_adicional, cast(datecc as date) as datecc  from tab_conf_laboratorios_clinicos";
                                                    $result = $db->query($sql);
                                                    if($result){
                                                        if($result->rowCount()>0){
                                                            while ($object = $result->fetchObject()){

                                                                $arr = new stdClass();
                                                                $arr = $object;
                                                                $Lab = $object->name.' &nbsp; <b>telf: '.$object->telefono.'</b>';


                                                                print "<option value='".$object->rowid."' data-arraylab='".json_encode($arr)."'>".$Lab."</option>";
                                                            }
                                                        }
                                                    }
                                                ?>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <button type="button" class="btn btn-success pull-right" id="selecionLab"> Aceptar </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>



                <!-- Laboratorio Clinico Prestaciones-->
                <div class="modal fade" id="ModalPrestacion_LaboratorioClinico" data-backdrop="static" role="dialog">
                    <div class="modal-dialog">

                        <div class="modal-content" >
                            <div class="modal-header modal-diseng"">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h4 class="modal-title"><span>Prestación</span></h4>
                            <input type="text" id="Labprestacion" data-subaccion="nuevo" class="hidden">
                        </div>

                        <div class="modal-body">
                            <div class="row">
                                <div class="form-group col-md-12 col-xs-12 col-sm-12">
                                    <div class="form-group">
                                        <div class="form-horizontal">
                                            <div class="form-group">
                                                <label class="control-label col-sm-3">Categoria</label>
                                                <div class="col-sm-7">
                                                    <select name="catprestacion" id="catprestacion" class="" style="width: 100%" onchange="FormValidarPrestacion()">
                                                        <option value=""></option>
                                                        <?php
                                                        $sql = "SELECT * FROM tab_conf_categoria_prestacion;";
                                                        $rs = $db->query($sql);
                                                        if($rs->rowCount() > 0 )
                                                        {
                                                            while ($row =  $rs->fetchObject())
                                                            {
                                                                print  "<option value='$row->rowid'>$row->nombre_cat</option>";
                                                            }
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label col-sm-3">prestación</label>
                                                <div class="col-sm-7">
                                                    <input type="text" class="form-control" id="name_prestacion" onkeyup="FormValidarPrestacion()">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label col-sm-3">costo x clinica</label>
                                                <div class="col-sm-7">
                                                    <input type="text" class="form-control" id="costo_prestacion" onkeyup="FormValidarPrestacion()">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label col-sm-3">Precio</label>
                                                <div class="col-sm-7">
                                                    <input type="text" class="form-control" id="precio_prestacion" onkeyup="FormValidarPrestacion()">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <button type="button" class="btn btn-success pull-right" id="crearPresatacionAsoLabo"> Guardar </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        </div>
                    </div>
                </div>

                <script src="<?= DOL_HTTP ?>/application/system/configuraciones/js/laboratorios_main.js"></script>

                <script>

                    $selecioneLaboratorio = <?= (GETPOST("idlabora")=="") ? "null" : GETPOST("idlabora") ?>;

                    $("#selecionLab").click(function () {

                        $selecioneLaboratorio =   $("#laboratorioPrestSeleccion").find(":selected").val();

                        fetchLaboratorioPrestaciones($selecioneLaboratorio,  $("#laboratorioPrestSeleccion"));

                        $("#selecioneLaboratorio").modal("hide");

                    });

                    $("#selecioneLaboratorio").on("hidden.bs.modal", function() {

                        if($selecioneLaboratorio!= null && $selecioneLaboratorio != ""){
                            if($selecioneLaboratorio!="" && $selecioneLaboratorio != 0){
                                $("#selecioneLaboratorio").modal("hide");
                            }else{
                                $("#selecioneLaboratorio").modal("hide");
                            }
                        }
                        else{
                            $("#selecioneLaboratorio").modal("show");
                        }

                    });

                    $(window).on("load", function() {

                        $("#laboratorioPrestSeleccion").select2({
                            placeholder:"Selecione una opción",
                            language:"es",
                            allowClear:true
                        });

                        if($selecioneLaboratorio!=null){
                            $("#selecioneLaboratorio").modal("hide");
                            $("#laboratorioPrestSeleccion").val($selecioneLaboratorio).trigger("change");
                            // alert($selecioneLaboratorio);
                            fetchLaboratorioPrestaciones($selecioneLaboratorio,  $("#laboratorioPrestSeleccion"));

                        }if($selecioneLaboratorio==null){
                            $("#selecioneLaboratorio").modal("show");
                        }

                        listLaboratorios();
                    });
                </script>

            <?php } ?>

        </div>
    </div>
</div>