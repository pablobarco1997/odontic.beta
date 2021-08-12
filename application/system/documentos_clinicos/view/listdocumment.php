
<?php


$url_breadcrumbs = $_SERVER['REQUEST_URI'];
$titulo = "Documentos Asociados";
$modulo = true;



if(!PermitsModule('Documentos clinicos', 'consultar')){
    $disabled_link3 = "disabled_link3";
}else{
    $disabled_link3 = " and 1<>1";
}

?>

<div class="form-group col-xs-12 col-md-12 no-padding">
    <div  class="form-group col-md-12 col-xs-12">
        <?= Breadcrumbs_Mod($titulo, $url_breadcrumbs, $modulo); ?>
        <label for="">LISTA DE COMPORTAMIENTOS</label>
        <ul class="list-inline" style="border-bottom: 0.6px solid #333333; padding: 3px; background-color: #f4f4f4; margin-left: 0px">
            <li> <a href="#ContenFiltroDocumentos" data-toggle="collapse" style="color: #333333" class="btnhover btn btn-sm <?= $disabled_link3 ?>" id="fitrar_document"> <b>  ▼ &nbsp;Filtrar <i ></i> </b> </a> </li>
            <li> <a href="#addnewdocument" data-toggle="modal" style="color: #333333" class="btnhover hidden btn btn-sm " id="create_document" > <b> <i class="fa fa-file-text"></i> &nbsp; nuevo documento clinico <i ></i> </b> </a> </li>
            <li> <a href="#" style="color: #333333" class="btnhover btn btn-sm <?= (!PermitsModule('Documentos clinicos', 'agregar')?"disabled_link3":"")?>" onclick="crearOdontograma()" > <b> <i class="fa fa-file"></i> &nbsp; crear nuevo formulario clinico <i ></i> </b> </a> </li>
        </ul>
    </div>

    <div id="ContenFiltroDocumentos"  class="form-group col-md-12 col-xs-12 collapse " aria-expanded="false"  style="margin-bottom: 0px;">
        <div class="form-group col-md-12 col-xs-12" style="background-color: #f4f4f4; padding: 25px; margin-top: 0px">
            <h3 class="margin-bottom"><span class="margin-bottom">Filtrar Documentos Clinicos </span></h3>

            <div class="row">

                <div class="form-group col-md-4 col-sm-12 col-xs-12">
                    <label for="">Emitido</label>
                    <div class="input-group form-group rango" style="margin: 0">
                        <input type="text" class="form-control   "  readonly="" id="fecha_creacion_doc" name="fecha_creacion_doc" value="">
                        <span class="input-group-addon" style="border-radius: 0"><i class="fa fa-calendar"></i></span>
                    </div>
                </div>

                <div class="form-group col-md-4 col-sm-12 col-xs-12">
                    <label for="">busqueda x Documento</label>
                    <select name="bsq_documento" id="bsq_documento" style="width: 100%" class="form-control">
                        <option value=""></option>
                        <?php
                            $result = $db->query("SELECT rowid , nombre_documento , Descripcion, id_table_form_document, datecreate FROM tab_documentos_clinicos where rowid != 0 and id_table_form_document != '' ");
                            if($result && $result->rowCount() > 0){
                                while ($object = $result->fetchObject()){
                                    print "<option value='$object->rowid'> ".$object->nombre_documento ."</option>";
                                }
                            }
                        ?>
                    </select>
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
</div>


<div class="form-group col-xs-12 col-md-12">
    <div class="table-responsive">
        <table class="table" WIDTH="100%" id="list_docum_clini">
            <thead>
                <tr style="background-color: #f4f4f4">
                    <th width="20%">Emitido</th>
                    <th width="40%">Documento Clinico</th>
                    <th width="50%">Descripción (opcional)</th>
                    <th>&nbsp;</th>
                </tr>
            </thead>
        </table>

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