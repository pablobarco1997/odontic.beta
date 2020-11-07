

<div class="form-group col-xs-12 col-sm-12 col-md-8 col-lg-4">
    <div class="info-box">
        <div class="info-box-icon bg-aqua" style="background-color: #212f3d!important;">
            <i class="fa fa-calendar" style="margin-top: 20px"></i>
        </div>
        <div class="info-box-content">
            <span class=""><span class="trunc"> <b>Citas para Hoy &nbsp; Estado no Confirmado  </b></span></span>
            <span class="info-box-number" style="font-size: 2em" id="numCitas">0</span>
            <span> <b><?= date("Y/m/d")?></b> </span>
        </div>
    </div>
</div>

<div class="form-group col-md-12 col-lg-12">
    <ul class="list-inline">
        <li>
            <div class="callbox">
                <div class="form-group">
                    <div class="checkbox  ">
                        <a style="color: #333333" href="<?= DOL_HTTP .'/application/system/agenda/index.php?view=principal&list=diaria' ?>" class="btn btnhover ">
                            <b>Diaria</b>
                        </a>
                    </div>
                </div>
            </div>
        </li>

        <li>
            <div class="callbox">
                <div class="form-group disabled_link3">
                    <div class="checkbox  ">
                        <a style="color: #333333" href="<?= DOL_HTTP.'/application/system/agenda/index.php?view=principal&list=diariaglob'?>" class="btn btnhover ">
                            <b>Diaria Global</b>
                        </a>
                    </div>
                </div>
            </div>
        </li>

        <li>
            <div class="callbox">
                <div class="form-group">
                    <div class="checkbox ">
                        <a href="<?= DOL_HTTP .'/application/system/agenda/index.php?view=agendadd'?>"  style="color: #333333" class="btn btnhover addCitas  ">
                            <b>Agendar una Cita </b>&nbsp;&nbsp;<i class="fa fa-calendar-check-o"></i>
                        </a>
                    </div>
                </div>
            </div>
        </li>

        <li>
            <div class="callbox">
                <div class="form-group">
                    <div class="checkbox ">
                        <label for="listcitasCanceladasEliminadas" class="btnhover hide">
                            <a style="color: #333333"  class="btn  ">
                                <input type="checkbox" id="listcitasCanceladasEliminadas">  MOSTRAR CITAS CANCELADAS O ELIMINADAS
                            </a>
                        </label>
                    </div>
                </div>
            </div>
        </li>

        <li>
            <div class="callbox">
                <div class="form-group">
                    <div class="checkbox  ">
                        <a href="#FiltrarAgenda" class="btn btnhover " style="color: #333333" data-toggle="collapse" >
                            <b>Filtrar Citas</b>
                        </a>
                    </div>
                </div>
            </div>
        </li>

    </ul>
</div>

<div id="FiltrarAgenda" class="collapse form-group col-xs-12 col-md-12">

    <div class="form-group col-md-12 col-xs-12"  style="background-color: #f4f4f4; padding: 25px">
        <h3 class="no-margin"><span>Filtrar Citas</span></h3>
        <div class="row">
            <div class="form-group col-md-3 col-sm-12 col-xs-12">
                <label for="">Fecha</label>
                <div class="input-group form-group rango" style="margin: 0">
                    <input type="text" class="form-control filtroFecha  " readonly id="startDate" value="">
                    <span class="input-group-addon" style="border-radius: 0"><i class="fa fa-calendar"></i></span>
                </div>
            </div>

            <div class="form-group col-md-4 col-sm-12 col-xs-12">
                <label for="">Doctor (Odontolog@)</label>
                <select name="filtro_doctor" id="filtro_doctor" style="width: 100%" multiple class="filtrar_doctor form-control   " >

                    <?php
                    $sql = "SELECT rowid , nombre_doc , apellido_doc , if(estado = 'A' , 'Activo' , 'Inactivo') as iestado FROM tab_odontologos where estado in('A' , 'E') ;";
                    $rs = $db->query($sql);
                    if($rs->rowCount() > 0)
                    {
                        while ($obj = $rs->fetchObject())
                        {
                            $nombOdontologos = $obj->nombre_doc ."  ". $obj->apellido_doc;
                            print "<option value='$obj->rowid'> ".(strtoupper($nombOdontologos))."  &nbsp;&nbsp;&nbsp;&nbsp;  ( <small> ".(strtolower($obj->iestado))."</small>  ) </option>";
                        }
                    }
                    ?>
                </select>
            </div>

            <div class="form-group col-md-3 col-sm-12 col-xs-12">
                <label for="">Estado de citas</label>
                <select name="" id="filtroEstados"  style="width: 100%" class="form-control  filtrar_estados " multiple>

                    <?php
                    $sql = "SELECT * FROM tab_pacientes_estado_citas;";
                    $rs = $db->query($sql);
                    if($rs->rowCount() > 0)
                    {
                        while ($obj = $rs->fetchObject())
                        {
                            echo "<option value='$obj->rowid' >$obj->text</option>";
                        }
                    }
                    ?>
                </select>
            </div>

            <div class=" form-group col-md-2 col-sm-12 col-xs-12">
                <label for="">buscar N. Cita</label>
                <input type="text" class="form-control" id="n_citasPacientes" style="width: 100%" placeholder="Ingrese numero de cita ">
            </div>
        </div>

        <div class="row">
            <div class=" form-group col-md-6 col-xs-12 ">
                <label for="">buscar Pacientes</label>
                <ul class="list-inline">
                    <li><input type="radio" name="pacientesOption" id="pacientes_habilitados" >  Pacientes habilitados</li>
                    <li><input type="radio" name="pacientesOption" id="pacientes_desabilitados">  Pacientes desabilitados</li>
                </ul>
                <select name="buscarxPaciente" id="buscarxPaciente" style="width: 100%" multiple class="form-control  buscarxPaciente "></select>
            </div>
            <div class="form-group col-md-12 ">
                <ul class="list-inline pull-right">
                    <li>  <button class="limpiar btn   btn-block  btn-default" style="float: right; padding: 10px" > &nbsp; &nbsp; Limpiar &nbsp; &nbsp;</button> </li>
                    <li>  <button class="aplicar btn   btn-block  btn-success" style="float: right; padding: 10px" > &nbsp;  &nbsp;Aplicar busqueda &nbsp;</button> </li>
                </ul>
            </div>
        </div>
    </div>

</div>

<style>
   table.tableAgenda{
       /*text-align: left;*/
       /*position: relative; !important;*/
       /*border-collapse: collapse;*/
   }
   tr.cabezeraListAgenda th{
       /*position: sticky; !important;*/
       /*top:0;*/
       /*box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);*/
   }

</style>

<div class="form-group col-md-12 col-xs-12 col-sm-12 col-lg-12" >
    <div class="table-responsive" >
        <table class="table table-striped  tableAgenda" id="tableAgenda" width="100%" >
            <thead  style="background-color: #f4f4f4; width: 100%; border-bottom: 1px solid #333333">
                <tr id="cabezeraListAgenda" class="cabezeraListAgenda" >
                    <th class="text-left" width="3%">
                        <input type="checkbox" id="checkeAllCitas" >
                        <label for="checkeAllCitas"></label>
                    </th>
                    <th class="text-center" width="8%">N.- Citas</th>
                    <th class="text-center" width="10%">Hora</th>
                    <th class="text-center" width="23%">Paciente</th>
                    <th class="text-center" width="23%">Doctor</th>
                    <th class="text-center" width="15%">Estado de Citas</th>
                    <th class="text-center" width="10%">Situación</th>
                </tr>
            </thead>
        </table>
    </div>
</div>

<script src="<?= DOL_HTTP ?>/application/system/agenda/js/agent.js"></script>
