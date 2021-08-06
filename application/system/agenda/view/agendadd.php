
<?php


/**Obtengo las minutos*/
$duracion = "";
$Minh = array();
$minhours = 0;
for ($min = 0; $min <= 10; $min++){
    $minhours += 15;
    $Minh[] = "<option value='".$minhours."'>$minhours .min</option>";
}
$duracion = implode(" ", $Minh);

/**Obtengo las Horas*/
$horaCita = "";
$hoursAxu = array();
for ($h = 8; $h <= 23; $h++){

    $min15 = 0;
    for ($m = 0; $m <= 3; $m++){

        $hourString = date('H:i',strtotime($h.':'.$min15));
        $hoursAxu[] = "<option value='".$hourString."'>$hourString</option>";
        $min15 += 15;
    }
}
$horaCita = implode(" ", $hoursAxu);

#echo '<pre>';  print_r($horaCita); die();

$opcionPacientes = ""; //pacientes activados
$sql = "SELECT * FROM tab_admin_pacientes where estado = 'A' ;";
$rs = $db->query($sql);
if($rs->rowCount() > 0)
{
    while ($obj = $rs->fetchObject())
    {
        $opcionPacientes .= "<option value='$obj->rowid'>".($obj->nombre ." ". $obj->apellido)."</option>";
    }
}

$opcionEspecialidad = ""; // especialidades
$sql = "SELECT * FROM tab_especialidades_doc;";
$rs = $db->query($sql);
if($rs->rowCount() > 0)
{
    while ($obj = $rs->fetchObject())
    {
        $opcionEspecialidad .= "<option value='$obj->rowid'>$obj->nombre_especialidad</option>";
    }
}

$opcionOdont = ""; //odontologos
$sql = "SELECT rowid , nombre_doc , apellido_doc , if(estado = 'A' , 'Activo' , 'Inactivo') as iestado FROM tab_odontologos WHERE estado = 'A';";
$rs = $db->query($sql);
if($rs->rowCount() > 0)
{
    while ($objodont = $rs->fetchObject())
    {
        $opcionOdont .= "<option value='$objodont->rowid' > ". $objodont->nombre_doc ."  ". $objodont->apellido_doc  ."  </option>";
    }
}


$url_breadcrumb = ""; #Obtengo la url
$module         = "";

if(isset($_GET['view']) && $_GET['view'] == 'agendadd'){

    $module = false;
    $url_breadcrumb = $_SERVER['REQUEST_URI'];
    $titulo         = 'Agendar Citas';

}

?>


<style>

    table{
        margin-bottom: 50px !important;
    }

</style>

<div class="row">
    <div class="form-group col-lg-12 col-xs-12 col-md-12">
        <div class="col-md-6 col-xs-12 pull-left hide">
            <?php //echo Breadcrumbs_Mod($titulo, $url_breadcrumb, $module) ?>
        </div>
        <div class="col-md-6 col-xs-12 ">
            <h3 class="" style="font-size: 2rem">AGENDAR CITA</h3>
        </div>
    </div>
</div>


<div class="form-group col-lg-12 col-md-12 col-xs-12">
    <div class="form-group col-md-8 col-centered">
        <div class="form-horizontal">
            <div class="form-group">
                <label for="" class="control-label col-sm-3">Paciente:</label>
                <div class="col-sm-9">
                    <select  id="agndar_paciente" class="form-control" style="width: 100%">
                        <option value=""></option>
                        <?= $opcionPacientes ?>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label for="" class="control-label col-sm-3">Observación:</label>
                <div class="col-sm-9">
                    <textarea  class="form-control" id="info-adicional" cols="30" rows="3"></textarea>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="form-group col-md-12 col-lg-12 col-xs-12">
    <div class="form-group col-md-8 col-centered">
        <h4><span style="border-bottom: 1px solid #e9edf2; display: block; margin-bottom: 10px"><b>Detalle</b></span></h4>
        <div class="form-horizontal">
            <div class="form-group">
                <label class="control-label col-sm-3" >Especialidad:</label>
                <div class="col-sm-9">
                    <select id="" name="especialida[0].det" class="form-control optionSelect2 select2_max_ancho opcionEspecialidad " style="width: 100%">
                        <option value=""></option>
                        <?= $opcionEspecialidad; ?>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-sm-3" >Doctor(a):</label>
                <div class="col-sm-9">
                    <select id="" name="odont[0].det" class="form-control optionSelect2 select2_max_ancho opcionOdont" style="width: 100%">
                        <option value=""></option>
                        <?= $opcionOdont;?>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-sm-3" >Duración:</label>
                <div class="col-sm-9">
                    <select  id="" name="duraccion[0].det" class="form-control optionSelect2 select2_max_ancho duracion" style="width: 100%">
                        <option value=""></option>
                        <?= $duracion; ?>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-sm-3" >Fecha de la Cita:</label>
                <div class="col-sm-9">
                    <div class="date2">
                        <div class="input-group date" data-provide="datepicker">
                            <input type="text" class="form-control fechaIni" name="fecha[0].det" id="inputFecha" readonly="">
                            <div class="input-group-addon">
                                <span class="fa fa-calendar"></span>
                            </div>
                        </div>
                        <small class="msg-error" style="color: red"></small>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-sm-3" >Hora de la Cita:</label>
                <div class="col-sm-9">
                    <select  id="" name="hora[0].det" class="form-control optionSelect2 select2_max_ancho horaCita" style="width: 100%">
                        <option value=""></option>
                        <?= $horaCita;  ?>
                    </select>
                </div>
            </div>

            <button class="btn pull-right" style="color: green; font-weight: bolder" onclick="GuardarCitas($(this))">
                    Agendar
                <span class="fa fa-refresh btnSpinner hide"></span>
            </button>
        </div>
    </div>
</div>


<!--
<div class="row hidden">
    <div class="form-group col-md-12 col-lg-12 col-xs-12">
        <div class="table-responsive">

            <div style="width: 1490px !important;">
                <table class="table table-striped" width="100%">
                    <thead>
                        <tr>
                            <th colspan="6">
                                <ul class="list-inline pull-left">
                                    <li>  <a href="#" id="addCloneCitas" class="btn btnhover text-bold disabled" disabled style="color:#333333; background-color: #F8F9F9"> + agregar más de una cita </a></li>
                                </ul>

                                <ul class="list-inline pull-right">
                                    <li>  <a href="#" id="masCitasPacient" disabled class="btn btnhover text-bold disabled"  style="color:#333333; background-color: #F8F9F9" title="ingresar mas citas para el mismo paciente">  ingresar mas citas para el mismo paciente </a></li>
                                </ul>

                            </th>
                        </tr>
                        <tr>
                            <th></th>
                            <th width="20%" class="text-center">ESPECIALIDAD</th>
                            <th width="20%" class="text-center">DOCTOR</th>
                            <th width="20%" class="text-center">DURACIÓN</th>
                            <th width="20%" class="text-center">FECHA CITA</th>
                            <th width="20%" class="text-center">HORA CITA</th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr class="template-index hide " id="template-index">
                            <td > <span id="clone-eliminarow " class="disabled disabled_link3" style="padding: 5px"> <i class="fa fa-trash fa-2x"></i> </span> </td>
                            <td>
                                <select id="clone-especialidad" class="form-control optionSelect2 select2_max_ancho">
                                    <option value=""></option>
                                    <?= $opcionEspecialidad; ?>
                                </select>
                            </td>

                            <td>
                                <select id="clone-odont" class="form-control optionSelect2 select2_max_ancho">
                                    <option value=""></option>
                                    <?= $opcionOdont;?>
                                </select>
                            </td>
                            <td>
                                <select  id="clone-duraccion" class="form-control optionSelect2 select2_max_ancho">
                                    <option value=""></option>
                                    <?= $duracion; ?>
                                </select>
                            </td>
                            <td>
                                <div class="form-group date2">
                                    <div class="input-group date" data-provide="datepicker">
                                        <input type="text" class="form-control " id="clone-fecha" readonly="">
                                        <div class="input-group-addon">
                                            <span class="fa fa-calendar"></span>
                                        </div>
                                    </div>
                                    <small class="msg-error" style="color: red"></small>
                                </div>
                            </td>
                            <td>
                                <select  id="clone-hora" class="form-control optionSelect2 select2_max_ancho">
                                    <option value=""></option>
                                    <?= $horaCita;  ?>
                                </select>
                            </td>
                        </tr>

                        <tr id="detalle-citas-index-0" class=" detalle_citas detalle-citas-index-0" data-id="0">
                            <td > <span name="eliminrow[0].det" style="padding: 5px" class="disabled disabled_link3"> <i  class="fa fa-trash-o fa-2x"></i> </span> </td>
                            <td>
                                <select id="" name="especialida[0].det" class="form-control optionSelect2 select2_max_ancho opcionEspecialidad ">
                                    <option value=""></option>
                                    < ?= $opcionEspecialidad; ?>
                                </select>
                            </td>

                            <td>
                                <select id="" name="odont[0].det" class="form-control optionSelect2 select2_max_ancho opcionOdont">
                                    <option value=""></option>
                                    < ?= $opcionOdont;?>
                                </select>
                            </td>
                            <td>
                                <select  id="" name="duraccion[0].det" class="form-control optionSelect2 select2_max_ancho duracion">
                                    <option value=""></option>
                                    < ?= $duracion; ?>
                                </select>
                            </td>
                            <td>
                                <div class="form-group date2">
                                    <div class="input-group date" data-provide="datepicker">
                                        <input type="text" class="form-control fechaIni" name="fecha[0].det" id="inputFecha" readonly="">
                                        <div class="input-group-addon">
                                            <span class="fa fa-calendar"></span>
                                        </div>
                                    </div>
                                    <small class="msg-error" style="color: red"></small>
                                </div>
                            </td>
                            <td>
                                <select  id="" name="hora[0].det" class="form-control optionSelect2 select2_max_ancho horaCita">
                                    <option value=""></option>
                                    < ?= $horaCita;  ?>
                                </select>
                            </td>
                        </tr>

                    </tbody>
                </table>

            </div>
        </div>
    </div>
    <div class="form-group col-xs-12 col-md-12">
        <div class="col-md-12 col-xs-12">
            <input type="button" class="btn btnhover btn-block" style="font-weight: bolder; color: green" id="guardar-citas" value="Guardar">
        </div>
        <br><br><br><br><br>
    </div>
</div> -->

<script src="<?= DOL_HTTP ?>/application/system/agenda/js/agentcreate.js"></script>
