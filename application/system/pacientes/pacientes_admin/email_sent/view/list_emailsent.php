


<div class="form-group col-lg-12 col-md-12 col-xs-12">
    <div class="form-group col-md-12 col-xs-12">

        <label for="">LISTA DE COMPORTAMIENTOS</label>
        <ul class="list-inline" style="border-bottom: 0.6px solid #333333; padding: 3px">
            <li>
                <a data-toggle="collapse" data-target="#contentFilter" class="btnhover btn btn-sm collapsed" style="color: #333333" aria-expanded="false"> <b>   ▼  Filtrar  </b>  </a>
            </li>
        </ul>
    </div>

    <div class="form-group col-xs-12 col-md-12 col-lg-12 collapse" id="contentFilter" aria-expanded="false" style="height: 0px;" >
        <div class="form-group col-xs-12 col-md-12 col-sm-12 col-lg-12" style="background-color: #f4f4f4; padding-top: 15px">
            <div class="form-group col-md-12 col-xs-12 col-lg-12">
                <h3 class="no-margin"><span>Filtrar E-mail Enviados</span></h3>
            </div>
            <div class="form-group col-md-7 col-xs-12">
                <label for="">Fecha</label>
                <div class="input-group form-group rango" style="margin: 0">
                    <input type="text" class="form-control filtroFecha  " readonly="" id="startDate" value="">
                    <span class="input-group-addon" style="border-radius: 0"><i class="fa fa-calendar"></i></span>
                </div>
            </div>
            <div class="form-group col-md-5 col-xs-12">
                <label for="">Estado</label>
                <select name="estadoEmailConfPaci" id="estadoEmailConfPaci" class="form-control" style="width: 100%;">
                    <option value=""></option>
                    <option value="ConfirmadoAsistir">Confirmado (Asistir)</option>
                    <option value="ConfirmadoNoAsistir">Confirmado (No Asiste)</option>
                    <option value="NoConfirmado">No Confirmado</option>
                </select>
            </div>
            <div class="form-group col-md-3 col-xs-12">
                <label for="">buscar N. Cita</label>
                <input type="text" class="form-control" id="busqN_Cita" name="busqN_Cita">
            </div>
            <div class="form-group col-md-12 col-xs-12">
                <ul class="list-inline pull-right">
                    <li>  <button class="limpiar btn   btn-block  btn-default" id="limpiarFiltro" name="limpiarFiltro" style="float: right; padding: 10px"> &nbsp; &nbsp; Limpiar &nbsp; &nbsp;</button> </li>
                    <li>  <button class="aplicar btn   btn-block  btn-success" id="aplicarFiltro" name="aplicarFiltro" style="float: right; padding: 10px"> &nbsp;  &nbsp;Aplicar busqueda &nbsp;</button> </li>
                </ul>
            </div>
        </div>
    </div>

    <div class="form-group col-lg-12 col-md-12 col-xs-12">
        <label for="">LISTA DE E-MAIL DE ENVIADOS &nbsp; <i class="fa fa-envelope-open"></i></label>
    </div>

    <div class="form-group col-md-12 col-xs-12">
        <div class="table-responsive">
            <table class="table table-striped" id="mailSentTable" width="100%">
                <thead>
                    <th> Fecha de Envio </th>
                    <th> Confirmación x Paciente </th>
                    <th> Desde </th>
                    <th> Para </th>
                    <th> Mensaje </th>
                    <th> N. Cita </th>
                </thead>
            </table>
        </div>
    </div>

</div>
