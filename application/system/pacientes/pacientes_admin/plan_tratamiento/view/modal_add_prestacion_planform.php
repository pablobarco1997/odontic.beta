

<?php

    $prestacion = '<option></option>';
    $objectServicios =   fetchPrestacionGroupLab();


    foreach ($objectServicios as $key => $value){
        $prestacion .= '<optgroup label="Laboratorio: '.$key.'">';
        foreach ($value as $key2 => $val){
            $prestacion .= '<option value="'.$val['id'].'">'.$val['text'].'</option>';
        }
        $prestacion .= '</optgroup>';
    }

?>

<style>


</style>
<!-- Modal Add Plan tratamiento-->
<div id="detdienteplantram" class="modal fade" role="dialog" data-backdrop="static">
    <div class="modal-dialog modal-lg" style="width: 80% ">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header modal-diseng">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><span>Agregar Prestación</span></h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="form-group col-xs-12 col-md-12" style="margin: 0px">
                        <ul class="list-inline" >
                            <li >
                                <div class="checkbox btn btn-block btn-sm" style="border-left: 1.5px solid #202a33">
                                    <label>
                                        <input type="checkbox" id="detencionPermanente">
                                        <img  src=" <?= DOL_HTTP .'/logos_icon/logo_default/diente.png';?>" width="12px" height="14px" alt=""> &nbsp;
                                        &nbsp;DENTICIÓN PERMANENTE
                                    </label>
                                </div>
                            </li>
                            <li>
                                <div class="checkbox btn btn-block btn-sm" style="border-left: 1.5px solid #202a33">
                                    <label>
                                        <input type="checkbox" id="detencionTemporal">
                                        <img  src=" <?= DOL_HTTP .'/logos_icon/logo_default/diente.png';?>" width="12px" height="14px" alt=""> &nbsp; &nbsp;
                                        DENTICIÓN TEMPORAL
                                    </label>
                                </div>
                            </li>
<!--                            id del detalle de plan de tratamiento-->
                            <li>
                                <p id="detallemod" data-iddet="0"></p>
                            </li>
                        </ul>
                    </div>
                </div>
                <hr style="margin: 5px; background-color: #e2e2e2">
                <div class="row">
                    <div class="col-md-12 col-xs-12">
                        <div class="table-responsive">
                            <?php
                                    #caras pieza animaciones
                                    include_once DOL_DOCUMENT.'/application/system/pacientes/pacientes_admin/plan_tratamiento/view/plan_odontograma.php';
                            ?>
                        </div>
                    </div>
                </div>

                <!--PRESTACIOANES CATEGORIZADA  -->
                <hr style="margin: 5px; background-color: #e2e2e2">
                <div class="row">

                    <div class="form-group col-md-7 col-xs-12">
                        <div class="box_prestaciones">
                            <label for="prestacion_planform">Todas las prestaciones</label>
                            <select id="prestacion_planform" class="form-control " style="width: 100%">
                                <?= $prestacion;  ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group col-md-3 col-xs-12">
                        <label style="display: block">&nbsp;</label>
                        <a href="#" class="btnhover btn" id="addprestacionPlantram" style="background-color: #efefef; color: #333333">Agregar Prestación</a>
                    </div>
                    <div class="form-group col-md-5 col-xs-12">
                        <label style="display: block">&nbsp;</label>
                        <small style="color: red" id="errores_msg_addplantram"></small>
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
                                <thead>
                                    <tr id="prestacionesDetalles">
                                        <th width="5%"></th>
                                        <th title="DESCRIPCION DE LA PRESTACIÓN">Prestación</th>
                                        <th title="SUB-TOTAL">Subtotal</th>
                                        <th title="DESCUENTO DE CONVENIO">Desc. Conv</th>
                                        <th title="CANTIDAD DE LA PRESTACIÓN">Cantidad</th>
                                        <th title="DESCUENTO DE ADICIONAL">Desc. Adicional</th>
                                        <th title="TOTAL">Total</th>
                                    </tr>
                                </thead>
                                <tbody id="detalle-prestacionesPlantram" style="height: 1232px; overflow-y: auto">
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
                <a href="#" class="btn btnhover" style="font-weight: bolder; color: green" id="guardarPrestacionPLantram">Guardar</a>
                <a href="#" class="btn btnhover" data-dismiss="modal" style="font-weight: bolder">Cerrar</a>
<!--                <button type="button" class="btn btn" data-dismiss="modal">Close</button>-->
            </div>
        </div>

    </div>
</div>