
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



$optionPaciente = "<option></option>";
$sql = "select * from tab_admin_pacientes where estado = 'A'";
$rs = $db->query($sql);
if($rs&&$rs->rowCount()>0){
    while ($rows = $rs->fetchObject()){
        $optionPaciente .= '<option value="'.$rows->rowid.'">'.$rows->nombre.' '.$rows->apellido.'</option>';
    }
}


$optionTratamiento = "<option></option>";
$sqlOptionPlantCab = "SELECT 
                            c.rowid , 
                            ifnull(c.edit_name, concat('Plan de Tratamiento ', 'N. ', c.numero)) plantram ,
                            concat('Doc(a) ', ' ', ifnull( (select concat( od.nombre_doc , ' ', od.apellido_doc ) as nomb from tab_odontologos od where od.rowid = c.fk_doc), 'No asignado')) as encargado,
                            (select concat(p.nombre,' ',p.apellido) from tab_admin_pacientes p where c.fk_paciente = p.rowid) as paciente
                          FROM tab_plan_tratamiento_cab c where c.estados_tratamiento != 'E' ";

$rsOption = $db->query($sqlOptionPlantCab);
if($rsOption && $rsOption->rowCount()>0){
    while ($obOption = $rsOption->fetchObject()){
        $optionTratamiento .= "<option value='$obOption->rowid'> $obOption->plantram  &nbsp;&nbsp; $obOption->encargado &nbsp;&nbsp; Paciente: $obOption->paciente </option>";
    }
}

$optionODontolog = "<option></option>";
$sqlOdont = "select rowid, concat(nombre_doc, ' ', apellido_doc) as odont from tab_odontologos where estado = 'A'";
$result = $db->query($sqlOdont);
if($result && $result->rowCount()>0){
    while ($obOdon = $result->fetchObject()){
        $optionODontolog .= "<option value='$obOdon->rowid'>$obOdon->odont</option>";
    }
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

                            if($v=="solicitudes_lab"){
                                if(isset($_GET['idlabora'])){
                                    if(!empty($_GET['idlabora'])){
                                        $objectLab = $db->query("select * from tab_conf_laboratorios_clinicos c where c.rowid = ".($_GET['idlabora'])." limit 1 ")->fetchObject();
                                    }
                                }
                                echo "Solicitudes de (LAB) ".$objectLab->name;
                            } 
                        ?>
                    </b></span></h4>
        </div>
    </div>

    <div class="box-body">
        <br>

        <div class="form-group col-centered col-xs-12 col-md-10 col-lg-10">

            <div class="form-group col-md-12 " style="padding: 0px">
                <ul class="list-inline">
                    <li><a class="btn " href="<?= DOL_HTTP?>/application/system/configuraciones/index.php?view=form_laboratorios_conf&v=laboratorios" style="border-left: 2px solid #212f3d; color: #333333"> <i class="fa fa-flask"></i> &nbsp; Laboratorios</a> </li>
                    <li>&nbsp;&nbsp;</li>
                    <li><a class="btn " href="<?= DOL_HTTP?>/application/system/configuraciones/index.php?view=form_laboratorios_conf&v=prestacionlab" style="border-left: 2px solid #212f3d; color: #333333"> <i class="fa fa-list-ul"></i> &nbsp; Prestaciones de Laboratorio</a></li>
                    <li>&nbsp;&nbsp;</li>
                </ul>
                <br>
            </div>


            <?php if( $v=='laboratorios') { ?>

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
                                <th>Prestaciones Realizadas x Mes ( <?= DateSpanish("", date('m')); ?> ) </th>
                                <th>Estado</th>
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
                        <div class="modal-header modal-diseng">
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


            <?php if($v=='prestacionlab') 
            { ?>


                <div id="cont_Prestacionlaboratorio" class="row">

                    <div class="form-group col-md-12 col-lg-12 col-xs-12">
                        <ul class="list-inline" style="border-bottom: 1px solid #333333; border-top: 1px solid #333333; padding: 3.5px">
                            <li><a href="#ModalPrestacion_LaboratorioClinico" data-toggle="modal" class="btnhover " id=""  style="font-weight: bolder; color: #333333; "> &nbsp;&nbsp;<i class="fa fa-clipboard"></i> &nbsp;  Agregar o Modificar Prestación &nbsp;</a></li>
                            <li><a href="#" class="btnhover " id=""  style="font-weight: bolder; color: #333333; " onclick="tableDinamicPrestacion('PrestacionesXlaboratorio')" > &nbsp;<i class="fa fa-files-o"></i> Prestaciones x Laboratorio &nbsp;</a> </li>
                            <li><a href="#" class="btnhover " id=""  style="font-weight: bolder; color: #333333; " onclick="tableDinamicPrestacion('PagosRealizado')" > &nbsp; <i class="fa fa-bar-chart"></i> (Pagos o Abonos) Realizados &nbsp;</a> </li>
                            <li><a href="#" class="btnhover " id=""  style="font-weight: bolder; color: #333333; " onclick="tableDinamicPrestacion('tratamientosPrestaciones')" > &nbsp; <i class="fa fa-bar-chart"></i>Pedidos del Laboratorio</a> </li>
                            <li><a href="#FiltrarAgenda" class="btnhover " id=""  style="font-weight: bolder; color: #333333; " data-toggle="collapse" > &nbsp;<i class="fa fa-search"></i> Filtrar Información &nbsp;</a> </li>
                        </ul>
                    </div>

                    <div class="form-group col-md-8 col-lg-8 col-xs-12 margin-bottom">
                        <table id="informacionPrestacion" class="none" style="display: none;" width="100%">
                            <tr style="border-top: 1px solid #e2e2e2">
                                <td style="width: 25%; font-weight: bolder">Nombre Laboratorio:</td>
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

                                <div class="form-group col-md-4 col-sm-12 col-xs-12">
                                    <label for="">Nombre de Prestación</label>
                                    <input type="text" class="form-control" name="nam_prestacion" id="nam_prestacion">
                                </div>

                                <div class="form-group col-md-8 col-sm-12 col-xs-12" id="busqx_tratamiento">
                                    <label for="">busqueda x Tratamiento</label>
                                    <select name="busxTratamiento" id="busxTratamiento" class="form-control" style="width: 100%">
                                        <?= $optionTratamiento; ?>
                                    </select>
                                </div>

                            </div>

                            <div class="row">
                                <div class="form-group col-md-4 col-sm-12 col-xs-12" id="busqx_paciente">
                                    <label for="">busqueda x Paciente</label>
                                    <select name="busxPacientes" id="busxPacientes" class="form-control" style="width: 100%">
                                        <?= $optionPaciente;  ?>
                                    </select>
                                </div>

                                <div class="form-group col-md-4 col-sm-12 col-xs-12" id="busqx_estadoTratamiento">
                                    <label for="">busqueda x Estado de Tratmiento</label>
                                    <select name="busxEstadoTratamiento" id="busxEstadoTratamiento" class="form-control" style="width: 100%">
                                        <option value=""></option>
                                        <option value="A">Pendiente</option>
                                        <option value="P">En Proceso</option>
                                        <option value="R">Realizado</option>
                                    </select>
                                </div>
                                <div class="form-group col-md-4 col-sm-12 col-xs-12" id="busqx_xFecha">
                                    <label for="">busqueda x Fecha</label>
                                    <input type="text" class="form-control" id="busqx_xFechaInput" readonly>
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
                            <div class="modal-header modal-diseng">
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

                        $("#laboratorioPrestSeleccion, #busxTratamiento, #busxPacientes, #busxEstadoTratamiento").select2({
                            placeholder:"Selecione una opción",
                            allowClear:true,
                            language:'es'
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


<!--        Mod Solicitudes de lavoratorios-->
            <?php if($v=='solicitudes_lab')
            { ?>


                <div id="cont_SolicitudLaboratorio" class="row">

                    <div class="form-group col-md-12 col-lg-12 col-xs-12">
                        <ul class="list-inline" style="border-bottom: 1px solid #333333; border-top: 1px solid #333333; padding: 3.5px">
                            <li><a href="#FiltrarAgenda" class="btnhover " id=""  style="font-weight: bolder; color: #333333; " data-toggle="collapse" > &nbsp;<i class="fa fa-search"></i> Filtrar Información &nbsp;</a> </li>
                            <li><a href="#ModalMarcarSolicitud" data-toggle="modal" class="btnhover " id="MarcarSolicitudShow"  style="font-weight: bolder; color: #333333; " > <i class="fa fa-ticket"></i> Marcar una Solicitud</a></li>
                        </ul>
                    </div>

                    <div class="form-group col-md-8 col-lg-8 col-xs-12 margin-bottom">
                        <table id="informacionPrestacion" class="none" style="display: none;" width="100%">
                            <tr style="border-top: 1px solid #e2e2e2">
                                <td style="width: 25%; font-weight: bolder">Nombre Laboratorio:</td>
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
                                <div class="form-group col-md-4 col-sm-12 col-xs-12">
                                    <label for="">Nombre de Prestación</label>
                                    <input type="text" class="form-control" name="nam_prestacion" id="nam_prestacion">
                                </div>
                                <div class="form-group col-md-4 col-sm-12 col-xs-12">
                                    <label for="">Paciente</label>
                                    <select name="selectPacientes" id="selectPacientes" class="form-control" style="width: 100%">
                                        <?= $optionPaciente; ?>
                                    </select>
                                </div>
                                <div class="form-group col-md-4 col-sm-12 col-xs-12">
                                    <label for="">Odontolog@ a cargo</label>
                                    <select name="odontolCargad" id="odontolCargad" class="form-control" style="width: 100%" >
                                        <?= $optionODontolog; ?>
                                    </select>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group col-md-12 no-margin">
                                    <ul class="list-inline pull-right no-margin">
                                        <li>  <button class="limpiar btn   btn-block  btn-default" style="float: right; padding: 10px" id="LimpiarFiltroSoli"> &nbsp; &nbsp; Limpiar &nbsp; &nbsp;</button> </li>
                                        <li>  <button class="aplicar btn   btn-block  btn-success" style="float: right; padding: 10px" id="aplicarFiltroSoli"> &nbsp;  &nbsp;Aplicar busqueda &nbsp;</button> </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group col-sm-12 col-lg-12 col-xs-12 col-lg-12">
                        <div class="table-responsive">
                            <table class="table" id="Solicitudes_info" width="100%">
                                <thead>
                                    <tr>
                                        <th>&nbsp;</th>
                                        <th width="20%">Fecha de P. Tratamiento</th>
                                        <th width="20%">Tratamiento</th>
                                        <th width="20%">Paciente</th>
                                        <th width="20%">Prestación</th>
                                        <th width="20%">Odontolog@ Encargado</th>
                                        <th width="20%">Recepción</th>
                                        <th width="20%">Estado</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>

                <script src="<?= DOL_HTTP ?>/application/system/configuraciones/js/laboratorios_main.js"></script>

                <script>

                    var idLab      = <?php echo ( (isset($_GET['idlabora']) && $_GET['idlabora'] > 0 ) ? $_GET['idlabora'] : 0 ) ?>;
                    var ElementLab = <?php echo  json_encode($objectLab); ?>;


                    var tableloadLaboratorio = function(){

                        $("#Solicitudes_info").DataTable({

                            searching: false,
                            ordering:false,
                            destroy:true,
                            serverSide:true,
                            processing:true,

                            ajax:{
                                url: $DOCUMENTO_URL_HTTP + '/application/system/configuraciones/controller/conf_controller.php',
                                type:'POST',
                                data:{
                                    'ajaxSend':'ajaxSend',
                                    'accion': 'listaSolicitudesinfo',
                                    'idLab' : idLab
                                } ,
                                dataType:'json',
                            },
                            language: {
                                "sProcessing": "Procesando...",
                                "sLengthMenu": "Mostrar _MENU_ registros",
                                "sZeroRecords": "No se encontraron resultados",
                                "sEmptyTable": "Ningún dato disponible en esta tabla",
                                "sInfo": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                                "sInfoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
                                "sInfoFiltered": "(filtrado de un total de _MAX_ registros)",
                                "sInfoPostFix": "",
                                "sSearch": "Buscar:",
                                "sUrl": "",
                                "sInfoThousands": ",",
                                "sLoadingRecords": "Cargando...",
                                "oPaginate": {
                                    "sFirst": "Primero",
                                    "sLast": "Último",
                                    "sNext": "Siguiente",
                                    "sPrevious": "Anterior"
                                },
                                "oAria": {
                                    "sSortAscending": ": Activar para ordenar la columna de manera ascendente",
                                    "sSortDescending": ": Activar para ordenar la columna de manera descendente"
                                }
                            },
                        });
                    };

                    var validarMarcarSolicitud = function(){

                        if($("[name='SolicitudChecked']:checked").length > 1){
                            notificacion('Solo puede selecionar una opción', 'question');
                        }

                        if( $("[name='SolicitudChecked']:checked").length == 1 ){
                            $("#MarcarSolicitudShow").removeClass('disabled_link3');
                        }else{
                            $("#MarcarSolicitudShow").addClass('disabled_link3');
                        }
                    };

                    $(window).on('load', function() {

                        $("#MarcarSolicitudShow").addClass('disabled_link3');

                        MostrarInformacionPrestaLabo(ElementLab);
                        tableloadLaboratorio();

                        $('#ModalMarcarSolicitud').on("show.bs.modal", function() {

                            if( $("[name='SolicitudChecked']:checked").length == 0){
                                notificacion('Tiene que selecionar una opción','question');
                                setTimeout(()=>{$('#ModalMarcarSolicitud').modal('hide');},1000);
                            }

                            $("#SelectStatusSolicitud").select2({
                                placeholder:'Selecione una Opción',
                                allowClear:true
                            });

                        });


                        //change Status
                        $("#statusSolicitudPrestacion").click(function() {

                            if( $("[name='SolicitudChecked']:checked").length == 1 ){

                                if($("#SelectStatusSolicitud").find(":selected").val()==""){
                                    notificacion("Debe selecionar una opción", "question");
                                    return false;
                                }

                                $("#MarcarSolicitudShow").removeClass('disabled_link3');

                                var url = $DOCUMENTO_URL_HTTP + '/application/system/configuraciones/controller/conf_controller.php';
                                var Parametros = {'ajaxSend':'ajaxSend','accion':'statusUpdateSolicitudes','iddettratamient': $("[name='SolicitudChecked']:checked").val()
                                                       ,'statusActual' : $('#SelectStatusSolicitud').find('option:selected').val()     };

                                $.get(url, Parametros, function(data) {
                                    var resp = $.parseJSON(data);
                                    if(resp['error'] == ''){
                                         if(resp['question'] != ''){
                                             $('#SelectStatusSolicitud').val(null).trigger('change');
                                             notificacion(resp['question'] , 'question');
                                         }else{
                                             $('#SelectStatusSolicitud').val(null).trigger('change');
                                             notificacion('Información Actualizado', 'success');
                                             setTimeout(()=>{$('#ModalMarcarSolicitud').modal('hide');},1000);
                                         }
                                    }else{

                                    }
                                });

                            }else{
                                notificacion('Tiene que selecionar una opción','question');
                                setTimeout(()=>{$('#ModalMarcarSolicitud').modal('hide');},1000);
                            }

                        });


                        //busqueda x solicitud prestaciones
                        $("#aplicarFiltroSoli").on("click", function() {
                            FiltroSolicitud();
                        });

                        $("#LimpiarFiltroSoli").on("click", function() {

                            $("#nam_prestacion").val(null);
                            $("#selectPacientes").val(null).trigger('change');
                            $("#odontolCargad").val(null).trigger('change');

                            FiltroSolicitud();
                        });


                        var FiltroSolicitud = function() {

                            var table = $("#Solicitudes_info").DataTable();

                            var nomPrestacion  = $("#nam_prestacion").val();
                            var Pacientes      = $("#selectPacientes").find(':selected').val();
                            var odontoCarg     = $("#odontolCargad").find(':selected').val();

                            var accion       = "listaSolicitudesinfo";
                            var ajaxSend     = "ajaxSend";
                            var urlparametos = "&nomprest="+nomPrestacion+"&paciente="+Pacientes+"&odontocargo="+odontoCarg;

                            var url = $DOCUMENTO_URL_HTTP + '/application/system/configuraciones/controller/conf_controller.php';
                            var newUrl = url + '?' +
                                'accion='+accion+
                                '&ajaxSend='+ajaxSend+
                                urlparametos;

                            table.ajax.url(newUrl).load();

                        };

                        $('#selectPacientes, #odontolCargad').select2({
                           placeholder:'Selecione una opción' ,
                           allowClear:true
                        });

                    });

                </script>


                <div class="modal fade" id="ModalMarcarSolicitud" data-backdrop="static">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header modal-diseng">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">×</span></button>
                                <h4 class="modal-title"><span>Marcar Solicitud</span></h4>
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="form-group col-md-12 col-xs-12">
                                        <span style=" color: #eb9627">
                                            <i class="fa fa-info-circle"></i>
                                                Si desea Solicitar un trabajo debe ser desde el plan de tratamiento del Paciente
                                        </span>
                                        <span style=" color: #eb9627; display: block">
                                            <i class="fa fa-info-circle"></i>
                                                Puede cambiar el estado de la Prestacion del Plan de Tratamiento desde las solicitudes. Esto depende en que estado se encuentre la prestación
                                        </span>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-xs-12 col-md-12">
                                        <label for=""></label>
                                        <select name="SelectStatusSolicitud" id="SelectStatusSolicitud" class="form-control" style="width: 100%">
                                            <option value=""></option>
<!--                                            <option value="A">Pendiente</option>-->
                                            <option value="P">En Proceso</option>
                                            <option value="R">Realizado</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <a  id="statusSolicitudPrestacion" name="statusSolicitudPrestacion" class="btn btnhover pull-right" style="color: green; font-weight: bolder" > Aceptar </a>
                            </div>
                        </div>
                        <!-- /.modal-content -->
                    </div>
                    <!-- /.modal-dialog -->
                </div>
    
            <?php }?>
        
        </div>
    </div>
</div>