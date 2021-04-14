
<div class="form-group col-md-12 col-lg-12">
    <div class="table-responsive">
        <h3 style="font-weight: bolder">Información de Estados</h3>
        <table class="table" id="detalles_estados_odontograma" width="100%">
            <thead>
                <th>Emitido</th>
                <th>Pieza</th>
                <th>Caras</th>
                <th>Estado</th>
                <th>&nbsp;</th>
            </thead>
        </table>
    </div>
</div>



<!--MODAL DEL ODONTOGRAMA AL MOMENTO DE GUARDAR O ACTUALIZAR LA INFORMACION -->
<div id="UpdateInformacionCommentOdontograma" class="modal fade" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header modal-diseng">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Información Adicional</h4>
            </div>
            <div class="modal-body">

                <div class="row">
                    <div class="form-group col-md-12">
                        <div class="center-block" style="width: 500px">
                            <label for="">Observación  <small>(opcional)</small></label>
                            <textarea class="form-control" id="observacionOpcional"></textarea>
                        </div>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <a type="button" class="btn btnhover" data-dismiss="modal" style="font-weight: bolder;">Cerrar</a>
                <a type="button" class="btn btnhover AceptarUpdateOdontogram" id="AceptarUpdateOdontogram" style="font-weight: bolder; color: #00a157" >Guardar</a>
            </div>
        </div>

    </div>
</div>