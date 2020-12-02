
<style>

    #detalles_plantram td, #detalles_plantram th {
        border: 1px solid #ddd;
        /*padding: px;*/
    }

    #headplantram th{
        box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);
    }

</style>


<!--        breadcrumbs-->
<div class="form-group col-md-6 col-xs-12 col-lg-6 pull-left">
    <?= Breadcrumbs_Mod($titulo, $url_breadcrumbs, $modulo); ?>
</div>

<!--FORMULARIO  PLAN DE TRATAMIENTO-->
<div class="form-group col-xs-12 col-md-12">

     <div class="form-group col-md-6 col-xs-12">
         <h4 id="nomb_plantram"></h4>
         <table >
             <tr>
                 <td><b><i class="fa fa-user"></i> Profecional a cargo: </b></td>
                 <td class="pull-right" id="profecional">&nbsp;</td>
             </tr>
             <tr>
                 <td><b><i class="fa fa-folder-open"></i> convenio: </b></td>
                 <td class="pull-right" id="convenio">&nbsp;</td>
             </tr>
         </table>
     </div>

     <div class="form-group col-md-6 col-xs-12">
         <ul class="list-inline pull-right">
             <li><p class="text-center labeltextBono " style="font-size: 1.5em; font-weight: bold; text-shadow: 1px 1px 0.2rem #00a157;"> Saldo Deudor </p></li>
             <li><p class="text-center labeltextBono " style="font-size: 1.5em; font-weight: bold; text-shadow: 1px 1px 0.2rem #00a157;"> <i class="fa fa-dollar"></i> <span id="saldoPagado" class="text-center"> 0.00 </span> </p></li>
         </ul>
     </div>

<!--    DETALLES -->
    <div class="form-group col-xs-12 col-md-12">

        <div class="table-responsive">
            <table class="table table-striped" width="100%" id="detalles_plantram">
                <thead id="headplantram">
                    <tr>
                        <th width="40%">
                            <label  style="float: left">Prestación</label>

                            <a href="#detdienteplantram" id="asociarPrestacion" data-toggle="modal" onclick="clearModalDetalle('todo')" class="btnhover" style="color: #00a157; font-size: 1.4rem; cursor: pointer; float: right; padding-top: 2.5px">
                                    <i class="fa fa-plus-circle"></i> Cargar Prestaciones</a>

                        </th>
                        <th width="10%">
                            <label for="">Realización</label>
                        </th>
                        <th width="15%">
                            <label for="">Dcto Adicional</label>
                        </th>
                        <th width="15%">
                            <label for="">Total</label>
                        </th>
                    </tr>
                <tr>
                    <th colspan="5" style="font-size: 1.4rem; cursor: pointer">Acciones Clinicas</th>
                </tr>
                </thead>


                <!--            detalle-->
                <tbody id="detalle-body"></tbody>

            </table>
        </div>

    </div>

    <div class="form-group col-xs-12 col-sm-9 col-md-8 col-lg-5 pull-right">
            <table class="table">
                <tr>
                    <td>TOTAL PRESUPUESTO</td>
                    <td id="Presu_totalPresu">0.00</td>
                </tr>
                <tr>
                    <td id="label_abonadoPagado">ABONADO</td>
                    <td id="Presu_Abonado">0.00</td>
                </tr>
                <tr>
                    <td>REALIZADO</td>
                    <td id="Presu_Realizado">0.00</td>
                </tr>
                <tr>
                    <td>SALDO</td>
                    <td id="Presu_Saldo">0.00</td>
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
<div id="modDeletePrestacion" class="modal fade" role="dialog">
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
                <a href="#" class="btn btnhover" data-dismiss="modal" style="font-weight: bolder">Close</a>
            </div>
        </div>

    </div>
</div>

