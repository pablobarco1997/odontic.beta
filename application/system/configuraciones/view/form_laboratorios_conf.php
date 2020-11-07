
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
                        Laboratorios Clinicos
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
                    <li><a class="btn " href="#" style="border-left: 2px solid #212f3d; color: #333333"> <i class="fa fa-list-ul"></i> &nbsp; Prestaciones de Laboratorio</a></li>
                    <li>&nbsp;&nbsp;</li>
                    <li><a class="btn " href="#" style="border-left: 2px solid #212f3d; color: #333333"> <i class="fa fa-sticky-note-o"></i> &nbsp; Solicitudes</a></li>
                </ul>
                <br>
            </div>


            <?php if($v=='laboratorios') { ?>

            <div id="cont_laboratorio" class="row">

                <div class="form-group col-md-12 col-lg-12 col-xs-12">
                    <ul class="list-inline" style="border-bottom: 1px solid #333333; border-top: 1px solid #333333; height: 30px; padding: 3.5px">
                        <li><a href="#addModificarLaboratorio" class="btnhover " id="crearLaboratorio" data-toggle="modal"  style="font-weight: bolder; color: #333333; "> &nbsp;&nbsp;<i class="fa fa-list"></i> &nbsp;  crear Laboratorio</a></li>
                        <li> </li>
                    </ul>
                </div>


                <div class="form-group col-md-12 col-lg-12 col-xs-12">
                    <div class="table-responsive">
                        <table class="table table-striped" id="laboratorio_list" width="100%">
                            <thead>
                                <th>Laboratorio</th>
                                <th>Información Adicional</th>
                                <th>Prestaciones Realizadas</th>
                            </thead>
                        </table>
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

            <?php } ?>

        </div>
    </div>
</div>