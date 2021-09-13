
<style>

    #headplantram th{
        box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);
    }

</style>


<!--        breadcrumbs-->
<div class="form-group col-md-6 col-xs-12 col-lg-6 pull-left">
    <?= Breadcrumbs_Mod($titulo, $url_breadcrumbs, $modulo); ?>
</div>

<!--FORMULARIO  PLAN DE TRATAMIENTO-->
<div class="form-group col-xs-12 col-md-12 no-padding">

     <div class="form-group col-md-6 col-xs-12">
         <h3 style="font-size: 2rem; margin-top: 0px" id="nomb_plantram">Finalizar Plan de tratamiento</h3>
         <table >
             <tr>
                 <td><b><i class="fa fa-user"></i> Profecional a cargo: </b></td>
                 <td class="pull-right" id="profecional"></td>
             </tr>
             <tr>
                 <td><b><i class="fa fa-folder-open"></i> convenio: </b></td>
                 <td class="pull-right" id="convenio"></td>
             </tr>
         </table>
     </div>

    <!--DETALLES -->
    <div class="form-group col-xs-12 col-md-12">

        <div class="table-responsive">
            <table class="table table-hover" width="100%" id="detalles_plantram">
                <thead id="headplantram">
                    <tr>
                        <th colspan="6"></th>
                        <th><button class="btn btn-sm btn-block  <?= !PermitsModule("Planes de Tratamientos", "consultar")?"disabled_link3":"" ?>" onclick="ExportDetalleTTO()" title="Exportar la información detallada de esta Plan de tratamiento en excel" id="exportar_detalle_Tratamiento"><i class="fa fa-print"></i> Excel</button></th>
                    </tr>
                    <tr style="background-color: #f4f4f4">
                        <th width="40%">
                            <label  style="float: left;  padding-top: 5px; font-size: 1.4rem" class="control-label" >Prestación</label>
                            <a href="#detdienteplantram" id="asociarPrestacion" data-toggle="modal" onclick="clearModalDetalle('todo')" class="btnhover btn-sm btn" style="color: #00a157; cursor: pointer; float: right; font-weight: bold; font-size: 1.4rem "> Cargar Prestaciones</a>
                        </th>
                        <th width="10%" style="text-align: right; ">
                            <label for="" style="font-size: 1.4rem; padding-right: 10px" >Estado</label>
                        </th>
                        <th width="10%" style="text-align: right; ">
                            <label for="" style="font-size: 1.4rem; padding-right: 10px">Precio</label>
                        </th>
                        <th width="10%" style="text-align: right; ">
                            <label for="" style="font-size: 1.4rem; padding-right: 10px">Cantidad</label>
                        </th>
                        <th width="10%" style="text-align: right; ">
                            <label for="" style="font-size: 1.4rem; padding-right: 10px">Dcto Adicional</label>
                        </th>
                        <th width="10%" style="text-align: right; ">
                            <label for="" style="font-size: 1.4rem; padding-right: 10px">Iva</label>
                        </th>
                        <th width="10%" style="text-align: right; ">
                            <label for="" style="font-size: 1.4rem; padding-right: 10px"> Sub. Total</label>
                        </th>
                    </tr>
                <tr>
                    <th colspan="6" style="font-size: 1.4rem; cursor: pointer; vertical-align: center" >Acciones Clinicas</th>
                    <th colspan="1"  >
                        <button class="btn  btn-block  btn-sm" title="Refresh detalle" id="refresh_detalle_table"> Actualizar <span class="fa fa-refresh btnSpinner hide"> </span> </button>
                    </th>
                </tr>
                </thead>


                <!--            detalle-->
                <tbody id="detalle-body"></tbody>

            </table>
        </div>

    </div>

    <div class="form-group col-xs-12 col-sm-7 col-md-7 col-lg-4 pull-right">
            <table class="table">
                <tr>
                    <td class="text-bold">TOTAL PRESUPUESTO</td>
                    <td id="Presu_totalPresu" style="font-weight: bold; text-align: right;">
                        <div class="form-group col-md-12 col-xs-12"></div>
                    </td>
                </tr>
                <tr>
                    <td id="label_abonadoPagado" class="text-bold">ABONADO</td>
                    <td id="Presu_Abonado" style="font-weight: bold; text-align: right;">
                        <div class="form-group col-md-12 col-xs-12"></div>
                    </td>
                </tr>
                <tr>
                    <td class="text-blue text-bold">REALIZADO <i class="fa fa-info-circle"></i></td>
                    <td id="Presu_Realizado" style="font-weight: bold; text-align: right;">
                        <div class="form-group col-md-12 col-xs-12 text-blue"></div>
                    </td>
                </tr>
                <tr>
                    <td class="text-bold">SALDO</td>
                    <td id="Presu_Saldo" style="font-weight: bold; text-align: right;">
                        <div class="form-group col-md-12 col-xs-12"></div>
                    </td>
                </tr>
            </table>
    </div>


    <div class="form-group col-xs-12 col-sm-12">
        <label for="">COMENTARIO</label>
        <textarea name="" id="addcomment" rows="5" class="form-control margin-bottom"></textarea>
        <button id="addCommentario" class="btn btnhover btn-block " style="font-weight: bolder; color: green">Guardar</button>
    </div>



<!--    MODALES DE PLAN DE TRATAMIENTO ADD-->
    <div class="form-group col-md-12 col-xs-12">
        <?php include_once DOL_DOCUMENT.'/application/system/pacientes/pacientes_admin/plan_tratamiento/view/modal_add_prestacion_planform.php'; ?>
    </div>

    <div class="form-group col-md-12 col-xs-12">
        <?php include_once DOL_DOCUMENT.'/application/system/pacientes/pacientes_admin/plan_tratamiento/view/modal_realizar_prestacion.php'; ?>
    </div>

</div>


<!--MODAL ELIMINAR ESTA PRESTACION-->
<div id="modDeletePrestacion" class="modal fade" role="dialog" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header modal-diseng">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><span>Eliminar Prestación</span></h4>
            </div>
            <div class="modal-body">
                <p><b>Desea Eliminar esta prestacion ? </b></p>
                <small> <b>Tener en cuenta que la prestación no podra ser eliminada si tiene saldo Asociado</b> </small>
            </div>
            <div class="modal-footer">
                <a href="#" class="btn btnhover" style="font-weight: bolder; color: green" id="AceptarDeletePrestacion" onclick="">Aceptar</a>
                <a href="#" class="btn btnhover" style="font-weight: bolder; " id="" onclick="" data-dismiss="modal" >Cancelar</a>
            </div>
        </div>

    </div>
</div>


<!--MODAL LISTA DE PAGOS DEL PACIENTE POR PRESTACIONES PAGADAS-->
<div id="modPagosxPacientes" class="modal fade" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header modal-diseng">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><span>Lista de pagos por prestaciones</span></h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="form-group col-xs-12 col-md-12">
                        <div class="table-responsive">
                            <table class="table table-condensed" id="pagosxpacientes_prestaciones" width="100%">
                                <thead style="background-color: #f4f4f4;">
                                    <tr>
                                        <th>Emitido</th>
                                        <th>Forma</th>
                                        <th># Pago</th>
                                        <th># Documento</th>
                                        <th>Saldo</th>
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


