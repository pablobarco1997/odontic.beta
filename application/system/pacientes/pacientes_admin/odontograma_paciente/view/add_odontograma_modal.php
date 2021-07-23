<div id="add_odontograma" class="modal fade" role="dialog" data-backdrop="static">
    <div class="modal-dialog modal-lg">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header modal-diseng">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><span>Crear Odontograma  <?= date('Y/m/d')?></span></h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="form-group col-lg-12 col-md-12">
                        <small style="color: #e79627; font-weight: bolder"> <i class="fa fa-info-circle"></i> Debe seleccionar un plan de tratamiento, el cual va a estar vinculado a este Odontograma</small>
                    </div>
                    <div class="form-group col-md-12 col-lg-12">
                        <select name="tratamientoSeled_modal" class="form-control select2_max_ancho tratamientoSeled" id="tratamientoSeled_modal" style="width: 100%">
                            <option value=""></option>
                            <?php
                            ?>
                        </select>
                        <small style="color: red" id="msg_errores_odontogram"></small>
                    </div>
                    <div class="form-group col-lg-12 col-md-12">
                        <label for="">Descripción (opcional)</label>
                        <textarea class="form-control" id="odontograDescrip"></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btnhover" style="font-weight: bolder; color: green" id="crear_odontograma">
                    Guardar <span class="fa fa-refresh btnSpinner hide"> </span>
                </button>
                <button class="btn btnhover" data-dismiss="modal" style="font-weight: bolder">
                    Cerrar
                </button>
            </div>
        </div>

    </div>
</div>