<?php


#breadcrumbs  ----------------------------------------------
$url_breadcrumbs = $_SERVER['REQUEST_URI'];
$titulo = "Nuevo Formulario Clinico";
$modulo = false;


?>

<style>
    .tdbox{
        padding: 30px;
        border: 1px solid #bfbfbf;
        cursor: pointer;
    }
</style>

<!--jquery ui min js-->
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js"></script>

<div class="form-group col-xs-12 col-md-12 no-padding">
    <div  class="form-group col-md-12 col-xs-12">
        <?= Breadcrumbs_Mod($titulo, $url_breadcrumbs, $modulo); ?>
        <label for="">ELEMENTOS DE FORMULARIO</label>
        <ul class="list-inline" style="border-bottom: 0.6px solid #333333; padding: 3px">
            <li> <a href="#addParrafoModal"  data-toggle="modal" style="color: #333333" class="btnhover btn btn-sm hide ">  <b> <i class="fa fa-text-height"></i> Texto o Parrafo </b> </a> </li>
            <li>
                <div class="dropdown">
                    <a href="#" style="color: #333333" class="btnhover btn btn-sm dropdown-toggle" data-toggle="dropdown" aria-expanded="false" >
                        <b> <i class="fa fa-cube"></i> Paneles </b>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a href="#" class="Columna" data-columna="1">1 Columna</a></li>
                        <li><a href="#" class="Columna" data-columna="2">2 Columna</a></li>
                        <li><a href="#" class="Columna" data-columna="3">3 Columna</a></li>
                        <li><a href="#" class="Columna" data-columna="4">4 Columna</a></li>
                    </ul>
                </div>
            </li>
            <li> <a href="#addinputsModal" data-toggle="modal"  style="color: #333333" class="btnhover btn btn-sm  hide" > <b> <i class="fa fa-cogs"></i> controles </b> </a> </li>
        </ul>
    </div>

</div>

<div class="form-group col-md-12 col-xs-12 col-xs-12">
    <div class="form-group col-md-12 col-xs-12 ">

        <div class="col-centered" style="width: 780px; ">
            <button class="btn btn-block " id="crearte_form_dom" style="background-color: ; font-weight: bolder"> CREAR FORMULARIO </button>
        </div>

        <div class="col-centered"  id="ContentForm" style="width: 780px; min-height: 1000px; border: 1px solid #e9e9e9; padding-bottom: 5px; padding-right: 5px; padding-left: 5px" >
            <!--form donde se ingresa y crean los formulario-->
            <form id="" action="" style="100%; height: 100%; padding-bottom: 5px; padding-right: 5px; padding-left: 5px"></form>
        </div>

    </div>
</div>



<!-- ===========================================================  MODAL  ===========================================================-->

<!--add nombre del documento-->
<div id="addNameDocumento" class="modal fade" role="dialog" data-backdrop="static"  >
    <div class="modal-dialog modal-sm" style="margin: 2% auto; width: 30%">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header modal-diseng">
<!--                <button type="button" class="close" data-dismiss="modal">×</button>-->
                <h4 class="modal-title" id="" ><span>DOCUMENTO NAME</span></h4>

            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="form-group col-xs-12 col-md-12">
                        <label for="">
                            <small style="border-bottom: 1px solid #e9edf2; display: block; margin-bottom: 10px">Ingrese un nombre para indentificar este Formulario Clinico</small>
                        </label>
                        <input type="text" class="form-control" id="nameFormClinico" onkeyup="FormValidNameDocumenClinico($(this))" autocomplete="off">
                    </div>
                    <div class="form-group col-xs-12 col-md-12">
                        <label for="">
                            <small style="border-bottom: 1px solid #e9edf2; display: block; margin-bottom: 10px">Descripción del Documento Clinico</small>
                        </label>
                        <textarea id="DescripFormClinico" cols="30"  class="form-control" onkeyup="FormValidNameDocumenClinico()"></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" id="namebtnFormClinico"> Agregar </button>
            </div>
        </div>

    </div>
</div>

<!--modal Configuracion de Elementos-->
<div id="ConfiguracionElmentTable" class="modal fade" role="dialog" data-backdrop="static"  >
    <div class="modal-dialog modal-lg" >

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header modal-diseng">
            <button type="button" class="close" data-dismiss="modal">×</button>
                <h4 class="modal-title" id="" ><span>Add Elementos</span></h4>
                <input type="text" class="hidden" id="ElementDom" data-idElment="" >

            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="form-group col-xs-12 col-md-6">
                        <div class="form-group">
                            <span style=" color: #eb9627">
                            <i class="fa fa-info-circle"></i>
                                El sistema no tomara en cuenta el <b>control</b> si la caja de texto del Identificador se encuentra vacia
                            </span>
                        </div>
                        <!--content-->
                        <div class="form-group">
                            <small style="border-bottom: 1px solid #e9edf2; display: block; margin-bottom: 10px; font-size: 1.3rem"><b>Texto</b></small>
                            <div class="btn-group">
                                <button type="button" class="btn active btntextalign btn-checked-align-left" id=""><i class="glyphicon glyphicon-align-left"></i></button>
                                <button type="button" class="btn btntextalign btn-checked-align-center" id=""><i class="glyphicon glyphicon-align-center"></i></button>
                                <button type="button" class="btn btntextalign btn-checked-align-right" id=""><i class="glyphicon glyphicon-align-right"></i></button>
                            </div>
                            <div class="radio">
                                <label><input type="radio" name="texto_conf" id="text_titulo" value="" >Titulo</label>
                            </div>
                            <div class="radio">
                                <label><input type="radio" name="texto_conf" id="text_parrafo" value="" >Parrafo</label>
                            </div>
                            <div class="radio">
                                <label><input type="radio" name="texto_conf" id="text_label" value="" >label</label>
                            </div>
                            <textarea  id="value_texto" cols="30" class="form-control" placeholder="Ingrese un texto (Identificador)"></textarea>
                        </div>

                        <div class="form-group">
                            <small style="border-bottom: 1px solid #e9edf2; display: block; margin-bottom: 10px; font-size: 1.3rem"><b>Add Caja de texto</b></small>
                            <div class="radio">
                                <label><input type="radio" name="cajaTexto" id="cajaTextoTexo" value="" >texto</label>
                            </div>
                            <div class="radio">
                                <label><input type="radio" name="cajaTexto" id="cajaTextoNumero" value="" >Numero</label>
                            </div>
                            <div class="radio">
                                <label><input type="radio" name="cajaTexto" id="cajaTextoFecha" value="" >Fecha</label>
                            </div>
                            <div class="radio">
                                <label><input type="radio" name="cajaTexto" id="cajaTextoPanelTexT" value="" >Panel de Texto</label>
                            </div>
                            <input type="text" class="form-control" id="caja_texto_conf" placeholder="Ingresar un texto para identificar el campo (nombre del Campo)">
                        </div>

                    </div>
                    <div class="form-group col-xs-12 col-md-6">
                        <!--content-->
                        <div class="form-group">
                            <small style="border-bottom: 1px solid #e9edf2; display: block; margin-bottom: 10px; font-size: 1.3rem"><b>Agregar checkbox (Selecion Multiple)<i class="fa fa-check-square"></i> </b></small>
                            <div class="checkbox">
                                <label><input type="checkbox" name="addchecked" id="addchecked"  >CheckBox</label>
                            </div>
                            <input type="text" class="form-control" id="idcheckedbox" placeholder="Ingresar un texto para identificar el campo seleccion Multiple">
                        </div>

                        <div class="form-group">
                            <small  style="border-bottom: 1px solid #e9edf2; display: block; margin-bottom: 10px; font-size: 1.3rem"><b> Lista desplegable <i class="fa fa-list-ul"></i> </b></small>
                            <input type="text" class="form-control" id="idnameListDesplegable" placeholder="Ingresar un texto para identificar el campo Lista deplegable">
                            <div class="form-group">
                                <small class="listDespl_error" style="color: red"></small>
                                <ul id="addListaDesplegable" style="list-style: none; margin-top: 3px">
                                    <li> <small class="btnhover btn-xs" style="cursor: pointer" onclick="ElemenAddOptionDesplegable(null, true)"> <i class="fa fa-plus"></i> Add Nueva opción </small>  </li>
                                    <li id="liprincipalDesplagble"><a href="#" class="disabled_link3" onclick="ElemenAddOptionDesplegable($(this),false)"><i class="fa fa-trash-o"></i></a> <label class="control-label"> opciones   <input  type="text" class="form-control input-sm optionListaDesp" style="font-weight: normal" ></label> </li>
                                    <li><a href="#" onclick="ElemenAddOptionDesplegable($(this), false)"><i class="fa fa-trash-o"></i></a> <label class="control-label"> opciones  <input  type="text" class="form-control input-sm optionListaDesp" style="font-weight: normal"></label> </li>
                                    <li><a href="#" onclick="ElemenAddOptionDesplegable($(this), false)"><i class="fa fa-trash-o"></i></a> <label class="control-label"> opciones  <input  type="text" class="form-control input-sm optionListaDesp" style="font-weight: normal"></label> </li>
                                </ul>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <small style="color: red;border-bottom: 1px solid #e9edf2; display: block; margin-bottom: 10px; font-size: 1.3rem"><b>Eliminar Columna <i class="fa fa-trash"></i> </b></small>
                            <div class="checkbox">
                                <label style="font-weight: bolder; color: red"><input type="checkbox" name="EliminarElementoTable" id="EliminarElementoTable"  >Eliminar Elemento  </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" id="addElmentosTable"> Agregar </button>
            </div>
        </div>

    </div>
</div>

<script src="<?= DOL_HTTP ?>/application/system/documentos_clinicos/js/class_document.js" ></script>
<script src="<?= DOL_HTTP ?>/application/system/documentos_clinicos/js/javaxsDocumentCreate.js"></script>
