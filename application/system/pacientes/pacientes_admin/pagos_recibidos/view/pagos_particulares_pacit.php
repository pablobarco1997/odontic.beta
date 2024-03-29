
    <div id="FiltrarPagoPacientes" class="form-group col-xs-12 col-md-12 collapse" aria-expanded="true" style="margin-bottom: 0px;">
        <div class="form-group col-md-12 col-xs-12" style="background-color: #f4f4f4; padding: 25px; ">
            <h3 class=""><span>Filtrar Pagos de Pacientes</span></h3>
            <div class="row">
                <div class="form-group col-md-2 col-sm-12 col-xs-12">
                    <label for="">Número</label>
                    <input type="text" class="form-control" name="pagPrestacion" id="pagPrestacion">
                </div>
                <div class="form-group col-md-3 col-sm-12 col-xs-12">
                    <label for="">Forma de Pago</label>
                    <select name="formaPago" id="formaPago" class="form-control" style="width: 100%">
                        <option value=""></option>
                        <?php
                            $quy = "select rowid, nom from tab_bank_operacion where rowid not in(1,2,3,4,7)";
                            $result = $db->query($quy);
                            if($result&&$result->rowCount()>0){
                                while ($object = $result->fetchObject()){
                                    print "<option value='".$object->rowid."'>".$object->nom."</option>";
                                }
                            }
                        ?>
                    </select>
                </div>
                <div class="form-group col-md-7 col-sm-12 col-xs-12">
                    <label for="">busqueda por Plan de Tratamiento</label>
                    <select name="" class="form-control " id="busquedaxTratamiento" style="width: 100%">
                        <option value=""></option>
                    </select>
                </div>
            </div>

            <div class="row">
                <div class="form-group col-md-3 col-sm-12 col-xs-12">
                    <label for="">busqueda por N. Documento</label>
                    <input type="text" class="form-control" name="n_x_documento" id="n_x_documento">
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

    <div class="form-group col-md-12">
        <div class="table-responsive">
            <table id="pag_particular" class="table" width="100%" style="border-collapse: collapse">
                <thead style="background-color: #f4f4f4">
                <tr>
                    <th>&nbsp;</th>
                    <th>Fecha</th>
                    <th># Pago</th>
                    <th># Plan de Tratamiento</th>
                    <th>Forma de Pago</th>
                    <th>Observación</th>
                    <th># Documento</th>
                    <th>Valor</th>
                    <th>&nbsp;</th>
                </tr>
                </thead>
            </table>
        </div>
    </div>


<!--Modal pagos detalles-->
<div id="detalleprestacionPagos" class="modal fade" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header modal-diseng">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><span>Prestaciones</span></h4>
            </div>
            <div class="modal-body">
                <div class="row">

                    <div class="col-md-12 col-xs-12">
                        <div class="row">
                            <div class="col-xs-12 col-md-12">
                                <h5><span id="n_plantrtam_detalle"></span></h5>
                            </div>
                            <div class="col-xs-12 col-md-9 ">
                                <h5><span id="n_documento_pago_detalle"></span></h5>
                            </div>
                        </div>
                    </div>

                    <div class="form-group col-md-12 col-xs-12">
                        <div class="row">
                            <div class="col-xs-12 col-md-12">
                                <div>
                                    <table class="table-striped table" id="detalle_prestaciones_pagos_part" width="100%" style="border-collapse: collapse">
                                        <thead>
                                            <tr>
                                                <th WIDTH="75%">Prestaciones</th>
                                                <th WIDTH="25%">Valor</th>
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
    </div>
</div>