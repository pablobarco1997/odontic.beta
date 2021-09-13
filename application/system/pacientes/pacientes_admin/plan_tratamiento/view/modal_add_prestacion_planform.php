<style>
    input{
        border: none;
        outline: none;
    }
</style>

<!-- Modal Add Plan tratamiento-->
<div id="detdienteplantram" class="modal fade" role="dialog" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg" style="width: 80% ">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header modal-diseng">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><span>Agregar Prestación</span></h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="form-group col-xs-12 col-md-12" style="margin: 0px;">
                        <div class="checkbox" style="">
                            <label>
                                <input type="checkbox" id="detencionPermanente">
                                <img  src=" <?= DOL_HTTP .'/logos_icon/logo_default/diente.png';?>" width="12px" height="14px" alt=""> &nbsp; DENTICIÓN PERMANENTE
                            </label>
                        </div>
                        <div class="checkbox" style="">
                            <label>
                                <input type="checkbox" id="detencionTemporal">
                                <img  src=" <?= DOL_HTTP .'/logos_icon/logo_default/diente.png';?>" width="12px" height="14px" alt=""> &nbsp; DENTICIÓN TEMPORAL
                            </label>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="form-group col-md-12 col-xs-12">
                        <div class="table-responsive">
                            <?php
                                    #caras pieza animaciones
                                    include_once DOL_DOCUMENT.'/application/system/pacientes/pacientes_admin/plan_tratamiento/view/plan_odontograma.php';
                            ?>
                        </div>
                    </div>

                    <div class="form-group col-md-12 col-xs-12" >
                        <div class="box_prestaciones">
                            <div class="input-group">
                                <select id="prestacion_planform" class="form-control " style="width: 100%"></select>
                                <div class="input-group-addon btn btnaddtrans_loadding" id="addprestacionPlantram" >
                                    <span >
                                        <i  class="fa fa-plus-square " id="btn_refresh_addService"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <style>
                    tr#prestacionesDetalles th{
                        box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);
                    }
                </style>

                <div class="row">
                    <div class="col-md-12 col-xs-12">

                        <div class="table-responsive">
                            <table class="table" >
                                <thead style="background-color:#f4f4f4 ">
                                    <tr id="prestacionesDetalles">
                                        <th width="5%"></th>
                                        <th title="DESCRIPCION DE LA PRESTACIÓN" width="30%">Prestación</th>
                                        <th title="precio">Precio</th>
<!--                                        <th title="DESCUENTO DE CONVENIO hide">Desc. Conv</th>-->
                                        <th title="Cantidad Prestacion/Servicio">Cantidad</th>
                                        <th title="Descuento adicional">Desc. Adicional %</th>
                                        <th title="iva incluido">Iva</th>
                                        <th title="sub total">Sub total</th>
                                    </tr>
                                </thead>
                                <tbody id="detalle-prestacionesPlantram" style="">
                                    <tr rowspan="5">
                                        <td class="text-center" colspan="6">NO HAY DETALLE</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button href="#" class="btn btnhover" style="font-weight: bolder; color: green" id="guardarPrestacionPLantram">
                    Guardar <span class="fa fa-refresh btnSpinner hide"> </span>
                </button>
                <button href="#" class="btn btnhover" data-dismiss="modal" style="font-weight: bolder">Cerrar</button>
<!--                <button type="button" class="btn btn" data-dismiss="modal">Close</button>-->
            </div>
        </div>

    </div>
</div>