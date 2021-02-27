

<?php

    $NameDirectorio = "FormDocumentosEntity_".base64_encode($conf->EMPRESA->ID_ENTIDAD."".$conf->EMPRESA->ENTIDAD);

    $idDoc         = (GETPOST('iddoc')=='')?'':GETPOST('iddoc');
    $idDocRegistro = (GETPOST('iddclin')=='')?'':GETPOST('iddclin');

    if(empty($idDoc) || empty($idDocRegistro)){
        print_r("<h3 style='color: red; font-weight: bolder'>Ocurrió un error</h3>"); die();
    }

    $object = $db->query("SELECT * FROM tab_documentos_clinicos where  rowid = $idDocRegistro")->fetchObject();

    $Titulo = $db->query("SELECT nombre_documento , Descripcion, id_table_form_document, datecreate FROM tab_documentos_clinicos where rowid = $idDocRegistro")->fetchObject()->nombre_documento;

    #breadcrumbs  -----------------------------------------------
    $url_breadcrumbs = $_SERVER['REQUEST_URI'];
    $titulo = "Registro de Documentos";
    $modulo = false;

?>


<div class="form-group col-xs-12 col-md-12 no-padding">
    <div  class="form-group col-md-12 col-xs-12">
        <?= Breadcrumbs_Mod($titulo, $url_breadcrumbs, $modulo); ?>
        <label for="">LISTA DE COMPORTAMIENTOS</label>
        <ul class="list-inline" style="border-bottom: 0.6px solid #333333; padding: 3px">
            <li> <a href="#" style="color: #333333" class="btnhover btn btn-sm " id="imprimirDocumentForm"> <b> <i class="fa fa-print"></i>  Imprimir <i ></i> </b> </a> </li>
            <li> <a href="<?= DOL_HTTP ?>/application/system/documentos_clinicos/index.php?view=doc_clinicos&iddclin=<?= $idDocRegistro ?>&nuew=true" style="color: #333333" class="btnhover btn btn-sm " > <b> <i class="fa fa-edit"></i> Nuevo </b> </a> </li>
        </ul>
    </div>
</div>

<div class="form-group col-xs-12 col-md-12 no-padding">
    <div class="form-group col-md-12 col-xs-12">
        <div class="table-responsive">
            <table class="table table-striped" id="listaDocumentosForm" width="100%">
                <thead>
                    <tr>
                        <th> <input type="checkbox" id="checkboxPadre" class="hidden"> </th>
                        <th width="80%">Documento</th>
                        <th width="20%">&nbsp;</th>
                        <th width="5%">&nbsp;</th>
                    </tr>
                </thead>
            </table>
            <br>
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
                <h4 class="modal-title" ><span>Eliminar Documento Clinico</span>&nbsp;<input type="text" class="hidden" id="idElemnDocument" data-id="" data-idregistro=""> </h4>
            </div>
            <div class="modal-body">
                <div class="row">
<!--                    <div class="col-xs-12 col-md-12">-->
<!--                        <p><b style="color: red; font-weight: bolder">Eliminar Documento Clinico</b></p>-->
<!--                    </div>-->
                    <div class="col-xs-12 col-md-12">
                        <span style=" color: #eb9627"> <i class="fa fa-info-circle"></i>
                                Desea Eliminar el Registro ? </span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" id="EliminarDocumentoClinico"> Eliminar </button>
            </div>
        </div>

    </div>
</div>


<script>

    function DocumentosForm(){

        var parametros = {
            'ajaxSend'  :'ajaxSend',
            'accion'    :'DocumentosForm',
            'idtableDocument' : "<?= base64_encode($idDoc) ?>",
            "iddoc"           : "<?= $idDocRegistro?>" ,
        };

        var table = $("#listaDocumentosForm").DataTable({
            searching: true,
            ordering:false,
            destroy:true,
            ajax:{
                url: $DOCUMENTO_URL_HTTP + '/application/system/documentos_clinicos/controller_documentos/controller_document.php',
                type:'POST',
                data: parametros,
                dataType:'json',
            },
            columnDefs:[
                {
                    targets:3,
                    render: function(data, type, full, meta) {

                        var domMenu = "<div class='dropdown pull-right'>";
                        domMenu += "<button class='btn btnhover  dropdown-toggle btn-xs ' type='button' data-toggle='dropdown' style='100%' aria-expanded='true'>" +
                            "<i class=\"fa fa-ellipsis-v\"></i>" +
                            "</button>";

                        var url = "<?= DOL_HTTP ?>"+"/application/system/documentos_clinicos/index.php?view=doc_clinicos&iddclin=<?= $idDocRegistro ?>&mod=true&idr="+full['idInfoDoc'];

                        domMenu += "<ul class='dropdown-menu pull-right'>";
                            domMenu += "<li><a href='#' data-url='"+url+"' onclick='PermisoModificar($(this))'>Modificar</a></li>";
                            domMenu += "<li><a href='#eliminar_documento_clinico_Modal' onclick='ElemenAttr("+full['idInfoDoc']+")' data-toggle='modal'>Eliminar</a></li>";
                        domMenu += "</ul>";
                        domMenu += "</div>";

                        return domMenu;

                    }
                },
                {
                    targets:0,
                    render: function (data, type, full, meta) {
                        var name_form = "<?= $idDoc ?>";
                        var inputChecked = "<input type='checkbox' name='checkedHijos' data-name_form='"+name_form+"' data-iddoc='"+full['idInfoDoc']+"' >";
                        return inputChecked;
                    }
                }

            ],
            language: {
                "sProcessing": "Procesando...",
                "sLengthMenu": "Mostrar _MENU_ registros",
                "sZeroRecords": "No se encontraron resultados",
                "sEmptyTable": "Ningún dato disponible en esta tabla",
                "sInfo": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                "sInfoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
                "sInfoFiltered": "(filtrado de un total de _MAX_ registros)",
                "sInfoPostFix": "",
                "sSearch": "Buscar:",
                "sUrl": "",
                "sInfoThousands": ",",
                "sLoadingRecords": "Cargando...",
                "oPaginate": {
                    "sFirst": "Primero",
                    "sLast": "Último",
                    "sNext": "Siguiente",
                    "sPrevious": "Anterior"
                },
                "oAria": {
                    "sSortAscending": ": Activar para ordenar la columna de manera ascendente",
                    "sSortDescending": ": Activar para ordenar la columna de manera descendente"
                }
            },
        });
    }


    var ElemenAttr = function(id){ $("#idElemnDocument").attr("data-id", id).attr("data-idregistro", id) };


    //Eliminar Registro Documento Clinicos ( tab_documentos_clinicos_data )
    $("#EliminarDocumentoClinico").on("click", function() {

        if( $("#idElemnDocument").prop("dataset").id != "" && $("#idElemnDocument").prop("dataset").id != 0 ){

            var parametros = {
                "accion"            : "eleminar_Registro_Doc_Data",
                "ajaxSend"          : "ajaxSend",
                "idtableDocument"   : "<?= base64_encode($idDoc) ?>",
                "id"                : $("#idElemnDocument").prop("dataset").idregistro ,
                //"iddoc"             : "< ?//= $idDocRegistro ? >//"
            };

            var urldoc = $DOCUMENTO_URL_HTTP + '/application/system/documentos_clinicos/controller_documentos/controller_document.php';

            $.get(urldoc , parametros , function(data) {
                var result = $.parseJSON(data);
                if(result['error'] != ''){
                    notificacion(result['error'], 'error');
                }else{
                    $("#eliminar_documento_clinico_Modal").modal("hide");
                    notificacion('Información Actualizada', 'success');
                    DocumentosForm();
                }
            });

        }

    });

    $("#imprimirDocumentForm").click(function() {

        if($("input[name='checkedHijos']:checked").length==1){
            var inputCheacked = $("input[name='checkedHijos']:checked");
            if(inputCheacked.is(":checked"))
            {
                var DomHTML         = "<?= $object->element_text ?>"+".html";
                var Directorio      = "<?= $NameDirectorio ?>";
                var url = $DOCUMENTO_URL_HTTP + "/application/system/documentos_clinicos/form_documentos/viewprint.php?htmldom="+DomHTML+"&iddoct="+inputCheacked.prop("dataset").iddoc+"&idform="+"<?= $idDocRegistro ?>";
                // alert(inputCheacked.prop("dataset").iddoc);
                window.open(url, "_blank");
            }

        }
        if($("input[name='checkedHijos']:checked").length==0){
            notificacion("Debe Selecionar una opción", "error");
        }
        if($("input[name='checkedHijos']:checked").length>1){
            notificacion("No puede selecionar mas de una opción", "error");
        }
        console.log($("input[name='checkedHijos']:checked").length);

    });

    var PermisoModificar = function(Element){
        if(!ModulePermission(4,3)){

            notificacion('Ud. no tiene permiso para Modificar', 'error');
            return false;
        }

        window.open(Element.prop('dataset').url);
    };

    $(document).ready(function() {
        DocumentosForm();
    });

    window.addEventListener("load", function() {

    });


</script>



