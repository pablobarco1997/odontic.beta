

<div class="form-group col-md-12 col-lg-12 col-xs-12">

    <div id="FiltrarPagoPacientes" class="form-group col-xs-12 col-md-12 collapse" aria-expanded="true" style="">
        <div class="form-group col-md-12 col-xs-12" style="background-color: #f4f4f4; padding: 25px">
            <h3 class="no-margin"><span>Filtrar Pagos de Pacientes</span></h3>
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
                            $quy = "SELECT * FROM tab_tipos_pagos where estado = 'A' ";
                            $result = $db->query($quy);
                            if($result&&$result->rowCount()>0){
                                while ($object = $result->fetchObject()){
                                    print "<option value='".$object->rowid."'>".$object->descripcion."</option>";
                                }
                            }
                        ?>
                    </select>
                </div>
                <div class="form-group col-md-7 col-sm-12 col-xs-12">
                    <label for="">busqueda x Plan de Tratamiento</label>
                    <select name="" class="form-control " id="busquedaxTratamiento" style="width: 100%">
                        <option value=""></option>
                        <?php

                        $sql1 = "SELECT 
                                        t.rowid,
                                        IFNULL(t.edit_name,
                                                CONCAT('Plan de tratamiento # ', t.numero)) AS editnum,
                                        (SELECT 
                                                CONCAT('Paciente: ', p.nombre, ' ', p.apellido)
                                            FROM
                                                tab_admin_pacientes p
                                            WHERE
                                                p.rowid = t.fk_paciente) AS nompaciente, 
                                        ifnull((select o.nombre_doc from tab_odontologos o where o.rowid = t.fk_doc), '') as odontolg
                                    FROM
                                        tab_plan_tratamiento_cab t
                                    WHERE fk_paciente = ".$idPaciente ." and estados_tratamiento in('A', 'S') ";
                        $rs1 = $db->query($sql1);
                        if($rs1->rowCount() > 0)
                        {
                            while ($ob1 =  $rs1->fetchObject()) {
                                $doctor_asignado = "Dr(a) no asignado";
                                if(trim($ob1->odontolg) != '')
                                    $doctor_asignado = "Dr(a) ".$ob1->odontolg;

                                print '<option value="'.$ob1->rowid.'">'.$ob1->editnum.' &nbsp;&nbsp; '. $doctor_asignado .' &nbsp;&nbsp; '.$ob1->nompaciente.'  </option>';
                            }
                        }

                        ?>
                    </select>
                </div>
            </div>

            <div class="row">
                <div class="form-group col-md-3 col-sm-12 col-xs-12">
                    <label for="">busqueda x N. Documento</label>
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

    <div class="form-group col-xs-12 col-md-12 col-lg-12">
        <div class="table-responsive">
            <table id="pag_particular" class="table table-striped" width="100%">
                <thead>
                    <tr>
                        <th>&nbsp;</th>
                        <th>Fecha</th>
                        <th># Pago</th>
                        <th># Plan de Tratamiento</th>
                        <th>Forma de Pago</th>
                        <th>Observación</th>
                        <th># Documento</th>
                        <th>$ Monto</th>
                        <th>&nbsp;</th>
                    </tr>
                </thead>
            </table>
            <br><br>
            <br><br>
        </div>
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

                    <div class="form-group col-md-12 col-xs-12">
                        <h5><span id="n_plantrtam_detalle"></span></h5>
                    </div>

                    <div class="form-group col-md-12 col-xs-12">
                        <div class="table-responsive">
                            <table class="table-striped table" id="detalle_prestaciones_pagos_part" width="100%">
                                <thead>
                                <tr>
                                    <th WIDTH="75%">Prestaciones</th>
                                    <th WIDTH="25%">Monto <i class="fa fa-dollar"></i></th>
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