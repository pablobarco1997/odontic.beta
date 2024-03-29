
<!-- MODAL DE REALIZAR PRESTACION -->

<div id="modal_prestacion_realizada" class="modal fade" data-backdrop="static" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header modal-diseng">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><span>Evolucionar Prestación</span></h4>
            </div>
            <div class="modal-body">

                <div class="row">
                    <div class="form-group col-sm-12 col-md-12">
                        <label for="">Seleccione el profecional que realizó esta prestación</label>
                        <select name="" id="evolucionDoct" class="form-control " style="width: 100%" tabindex="5">
                            <option value=""></option>
                            <?php
                                $sqldoc = " SELECT concat(nombre_doc, ' ', apellido_doc) as nomb , rowid, estado FROM tab_odontologos WHERE estado = 'A' ";
                                $rsdoc  = $db->query($sqldoc);
                                if($rsdoc && $rsdoc->rowCount() > 0)
                                {while ($doc = $rsdoc->fetchObject())
                                { echo "<option value='$doc->rowid' > ".($doc->nomb)." </option>";}}
                            ?>
                        </select>
                        <small id="msgDoctorerr" style="color: red"></small>
                    </div>

                    <div class="form-group col-sm-12 col-md-12">
                        <label for="">Evolución escrita (opcional)</label>
                        <textarea id="descripEvolucion" rows="3" class="form-control" style=""></textarea>
                    </div>

                    <div class="form-group col-sm-12 col-md-12">
                        <label for="">Actualizar Odontograma (opcional)</label>
                        <select id="actualizarOdontogramaPlantform" class="form-control "  style="width: 100%" tabindex="5">
                            <option value=""></option>
                            <?php
                                $sqlEstado = "SELECT * FROM tab_odontograma_estados_piezas";
                                $rsEstado  = $db->query($sqlEstado);
                                if($rsEstado && $rsEstado->rowCount() > 0)
                                {
                                    while($estadoOdontograma = $rsEstado->fetchObject())
                                    {      echo "<option value='$estadoOdontograma->rowid'>$estadoOdontograma->descripcion</option>"; }
                                }
                            ?>
                        </select>
                    </div>

                </div>

            </div>
            <div class="modal-footer">
                <button class="btn btnhover" style="font-weight: bolder; color: green"  id="RealizarPrestacion">
                    Aceptar <span class="fa fa-refresh btnSpinner hide"> </span>
                </button>
                <a class="btn btnhover" style="font-weight: bolder; "  data-dismiss="modal" id="">Cerrar</a>
            </div>
        </div>

    </div>
</div>