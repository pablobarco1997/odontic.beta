

<!--Mostrar pacientes Registrado x Fecha -->
<div id="pacientes_registrados_" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg" >
        <div class="modal-content">
            <div class="modal-header modal-diseng">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"> <span>Pacientes Registrados</span> </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12 col-xs-12" style="margin-bottom: 15px">
                       <h4 id="labelRegistroxDate" style="background-color: #f0f0f0; padding: 7px;border-bottom: 1px solid #e9edf2; display: block; margin-bottom: 10px"><span>1 de enero de 2021 hasta 31 de diciembre de 2021</span></h4>
                    </div>
                    <div class="col-md-12 col-xs-12">
                        <div class="table-responsive">
                            <table class="table  table-condensed" id="reporte_pacientes_registrados" width="100%">
                                <thead>
                                    <tr>
                                        <th width="10%" rowspan="2" colspan="1">Nombre</th>
                                        <th width="30%" rowspan="2" colspan="1">Dirección</th>
                                        <th width="8%"  rowspan="2" colspan="1">C. I.	</th>
                                        <th width="20%" colspan="2" class="text-center"  >Contactos</th>
                                    </tr>
                                    <tr>
                                        <th colspan="1" rowspan="1">E-mail</th>
                                        <th colspan="1" rowspan="1">Phone</th>
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



<!--Mostrar Citas canceladas x Fecha -->
<div id="citas_canceladas_xdate" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg" >
        <div class="modal-content">
            <div class="modal-header modal-diseng">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"> <span>Citas Canceladas</span> </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12 col-xs-12" style="margin-bottom: 15px">
                        <h4 id="labelRegistroxDate" class="labelRegistroxDate" style="background-color: #f0f0f0; padding: 7px;border-bottom: 1px solid #e9edf2; display: block; margin-bottom: 10px"><span>1 de enero de 2021 hasta 31 de diciembre de 2021</span></h4>
                    </div>
                    <div class="col-md-12 col-xs-12">
                        <div class="table-responsive">
                            <table class="table  table-condensed" id="reporte_citas_canceladas" width="100%">
                                <thead>
                                    <tr>
                                        <th>N.- Cita</th>
                                        <th>Fecha</th>
                                        <th>Hora</th>
                                        <th>Paciente</th>
                                        <th>Doctor(a)</th>
                                        <th>Estado</th>
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


<!--Mostrar Citas canceladas x Fecha -->
<div id="tratamientos_activos_finalizados" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg" style="width: 85%" >
        <div class="modal-content" >
            <div class="modal-header modal-diseng">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"> <span>Plan de Tratamientos Activos y Finalizados</span> </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12 col-xs-12" style="margin-bottom: 15px">
                        <h4 id="labelRegistroxDate" class="labelRegistroxDate" style="background-color: #f0f0f0; padding: 7px;border-bottom: 1px solid #e9edf2; display: block; margin-bottom: 10px"><span>1 de enero de 2021 hasta 31 de diciembre de 2021</span></h4>
                    </div>
                    <div class="col-md-12 col-xs-12">
                        <div class="table-responsive">
                            <table class="table  table-condensed" id="reporte_tratamientosActivFinalizado" width="100%">
                                <thead>
                                    <tr>
                                        <th>Emitido</th>
                                        <th>Plan de Tratamiento</th>
                                        <th>Ultima Cita</th>
                                        <th>Paciente</th>
                                        <th>Encargado</th>
<!--                                        <th>Situación</th>-->
                                        <th>Estado</th>
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