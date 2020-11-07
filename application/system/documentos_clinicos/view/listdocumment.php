
<?php

$opcionDocmmClinicos = "";
$sql = "select nombre_documento , rowid , replace(nombre_documento,' ','') as nomid from tab_documentos_clinicos";
$rsDoctClini = $db->query($sql);
if($rsDoctClini->rowCount()>0)
{
    while ($Doc = $rsDoctClini->fetchObject())
    {
        $opcionDocmmClinicos .= "<option value='$Doc->rowid'  data-iddocument='".(strtolower($Doc->nomid))."' > $Doc->nombre_documento </option>";
    }
}

$optionPacientes = "";
$sql = "select rowid as id, concat(nombre,' ', apellido) as paciente   from tab_admin_pacientes where estado = 'A';";
$rsDoctClini = $db->query($sql);
if($rsDoctClini->rowCount()>0)
{
    while ($Doc = $rsDoctClini->fetchObject())
    {
        $optionPacientes .= "<option value='$Doc->id' > ".(strtoupper($Doc->paciente))." </option>";
    }
}

#breadcrumbs  -----------------------------------------------
$url_breadcrumbs = $_SERVER['REQUEST_URI'];
$titulo = "Documentos Asociados";
$modulo = true;

?>

<div class="form-group col-xs-12 col-md-12 no-padding">
    <div  class="form-group col-md-12 col-xs-12">
        <?= Breadcrumbs_Mod($titulo, $url_breadcrumbs, $modulo); ?>
        <label for="">LISTA DE COMPORTAMIENTOS</label>
        <ul class="list-inline" style="border-bottom: 0.6px solid #333333; padding: 3px">
            <li> <a href="#" style="color: #333333" class="btnhover btn btn-sm " id="fitrar_document"> <b>  ▼ &nbsp;Filtrar <i ></i> </b> </a> </li>
            <li> <a href="#addnewdocument" data-toggle="modal" style="color: #333333" class="btnhover btn btn-sm " id="create_document" > <b> <i class="fa fa-file-text"></i> &nbsp; nuevo documento clinico <i ></i> </b> </a> </li>
            <li> <a href="<?= DOL_HTTP ?>/application/system/documentos_clinicos/index.php?view=createform_regist"  style="color: #333333" class="btnhover btn btn-sm " > <b> <i class="fa fa-file"></i> &nbsp; crear nuevo formulario clinico <i ></i> </b> </a> </li>
        </ul>
    </div>
</div>


<div class="form-group col-xs-12 col-md-12">
    <div class="table-responsive">
        <table class="table" WIDTH="100%" id="list_docum_clini">
            <thead>
                <tr>
                    <th width="15%">Fecha</th>
                    <th width="30%">Documento Clinico</th>
                    <th width="60%">Descripción (opcional)</th>
                    <th>&nbsp;</th>
                </tr>
            </thead>
        </table>
        <br>
    </div>
</div>


<!--CREAR DOCUMENTO CLINICO-->
<div class="modal fade" id="addnewdocument" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header modal-diseng">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h5 class="modal-title">CREAR NUEVO DOCUMENTO CLINICO</h5>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="form-group col-md-12 col-xs-12">

                        <small style="border-bottom: 1px solid #e9edf2; display: block; margin-bottom: 10px">Seleccione un documento clinico</small>
                        <select class="form-control select2_max_ancho" id="documento_">
                            <option value=""></option>
                            <?= $opcionDocmmClinicos; ?>
                        </select>
                        <br>

                        <br>
                        <small style="border-bottom: 1px solid #e9edf2; display: block; margin-bottom: 10px">Seleccione un paciente</small>
                        <select class="form-control select2_max_ancho" id="doctpacientes">
                            <option value=""></option>
                            <?= $optionPacientes; ?>
                        </select>

                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12 col-xs-12">
                        <a href="#" class="btn btnhover pull-right" style="font-weight: bolder; color: green" id="crearDocumentClinico">crear</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!--ELIMINAR DOCUMENTO CLINICO-->
<div id="eliminar_documento_clinico_Modal" class="modal fade" role="dialog" data-backdrop="static">
    <div class="modal-dialog modal-sm" style="margin: 2% auto; width: 30%">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header modal-diseng">
                <button type="button" class="close" data-dismiss="modal">×</button>
                <h4 class="modal-title" ><span>Eliminar Documento Clinico</span>&nbsp;<input type="text" class="hidden" id="idElemnDocument" data-id=""> </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <!--                    <div class="col-xs-12 col-md-12">-->
                    <!--                        <p><b style="color: red; font-weight: bolder">Eliminar Documento Clinico</b></p>-->
                    <!--                    </div>-->
                    <div class="col-xs-12 col-md-12">
                        <span style=" color: #eb9627"> <i class="fa fa-info-circle"></i>
                                Tener en cuenta que si Elimina este Documento clínico, Eliminara toda la data(registros) relacionada a este Documento <i class="fa fa-file-text-o"></i> </span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" id="EliminarDocumentoClinico"> Eliminar </button>
            </div>
        </div>

    </div>
</div>



<!--LISTA PRINCIPAL VIEW-->
<script src="<?= DOL_HTTP ?>/application/system/documentos_clinicos/js/indexjavax.js"></script>