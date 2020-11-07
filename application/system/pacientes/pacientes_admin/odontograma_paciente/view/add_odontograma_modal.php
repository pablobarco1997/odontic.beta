<div id="add_odontograma" class="modal fade" role="dialog">
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
                        <select name="" class="form-control select2_max_ancho" id="tratamientoSeled" style="width: 100%">
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
                        <small style="color: red" id="msg_errores_odontogram"></small>
                    </div>
                    <div class="form-group col-lg-12 col-md-12">
                        <label for="">Descripción (opcional)</label>
                        <textarea class="form-control" id="odontograDescrip"></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <a href="#" class="btn btnhover" style="font-weight: bolder; color: green" id="crear_odontograma">Guardar</a>
                <a href="#" class="btn btnhover" data-dismiss="modal" style="font-weight: bolder">Close</a>
            </div>
        </div>

    </div>
</div>