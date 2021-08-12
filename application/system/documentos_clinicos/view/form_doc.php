

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
        <ul class="list-inline" style="border-bottom: 0.6px solid #333333; padding: 3px; margin-left: 0px; background-color: #f4f4f4">
            <li> <a href="#ContenFiltroDocumentos" data-toggle="collapse" style="color: #333333" class="btnhover btn btn-sm  and 1<>1" id="fitrar_document"> <b>  ▼ &nbsp;Filtrar <i></i> </b> </a> </li>
            <li> <a href="#" style="color: #333333" class="btnhover btn btn-sm " id="imprimirDocumentForm"> <b> <i class="fa fa-print"></i>  Imprimir <i ></i> </b> </a> </li>
            <li> <a href="<?= DOL_HTTP ?>/application/system/documentos_clinicos/index.php?view=doc_clinicos&iddclin=<?= $idDocRegistro ?>&nuew=true" style="color: #333333" class="btnhover btn btn-sm " > <b> <i class="fa fa-edit"></i> Nuevo </b> </a> </li>
            <li> <a href="#" style="color: #333333" class="btnhover btn btn-sm " id="asociarDocumento" onclick="AsociarDocumento(this)"> <b>   Asociar Documento <i ></i> </b> </a>  </li>
        </ul>
    </div>


    <div id="ContenFiltroDocumentos" class="form-group col-md-12 col-xs-12 collapse " aria-expanded="true" style="margin-bottom: 0px;">
        <div class="form-group col-md-12 col-xs-12" style="background-color: #f4f4f4; padding: 25px; margin-top: 0px">
            <h3 class="margin-bottom"><span class="margin-bottom">Filtrar Documentos</span></h3>

            <div class="row">
                <div class="form-group col-md-4 col-sm-12 col-xs-12">
                    <label for="">Emitido</label>
                    <div class="input-group form-group rango" style="margin: 0" >
                        <input type="text" class="form-control   " readonly="" id="emitido_doccum" name="fecha_creacion_doc" value="">
                        <span class="input-group-addon" style="border-radius: 0"><i class="fa fa-calendar"></i></span>
                    </div>
                </div>

                <div class="form-group col-md-3 col-sm-12 col-xs-12">
                    <label for="">Numero</label>
                    <input type="number" class="form-control" placeholder="buscar por numero documento" id="numero_doccum">
                </div>

                <div class="form-group col-md-3 col-sm-12 col-xs-12">
                    <label for="">Paciente</label>
                    <select name="paciente_documento" id="paciente_documento" style="width: 100%" class="form-control">
                        <option value=""></option>
                    </select>

                    <script>
                        $(window).on('load', function () {

                            $("#paciente_documento").select2({
                                placeholder: 'buscar pacientes' ,
                                language: languageEs,
                                minimumInputLength: 2,
                                ajax:{
                                    url: $DOCUMENTO_URL_HTTP + "/application/system/agenda/controller/agenda_controller.php",
                                    dataType: "json",
                                    data: function (params) {
                                        var query = {
                                            search: params.term,
                                            ajaxSend:'ajaxSend',
                                            accion:'pacientes_activodesact'
                                        };
                                        return query;
                                    },
                                    processResults: function (data) {
                                        return {
                                            results: data.items
                                        };
                                    }
                                }
                            });

                        });

                    </script>
                </div>

            </div>
            <div class="row">
                <div class="form-group col-md-12 no-margin">
                    <ul class="list-inline pull-right no-margin">
                        <li>  <button class="limpiar btn   btn-block  btn-default" style="float: right; padding: 10px" id="limp_doc"> &nbsp; &nbsp; Limpiar &nbsp; &nbsp;</button> </li>
                        <li>  <button class="aplicar btn   btn-block  btn-success" style="float: right; padding: 10px" id="aplicar_doc"> &nbsp;  &nbsp;Aplicar busqueda &nbsp;</button> </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

</div>

<div class="form-group col-xs-12 col-md-12 no-padding">
    <div class="form-group col-md-12 col-xs-12">
        <div class="table-responsive">
            <table class="table " id="listaDocumentosForm" width="100%">
                <thead>
                    <tr style="background-color: #f4f4f4">
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


<!--Asociar Documento-->
<div id="asociar_documento_pacientes" class="modal fade" role="dialog" data-backdrop="static">
    <div class="modal-dialog " >
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header modal-diseng">
                <button type="button" class="close" data-dismiss="modal">×</button>
                <h4 class="modal-title" ><span>Asociar Documento Clinico</span>&nbsp;<input type="text" class="hidden" id="idElemnDocument" data-id="" data-idregistro=""> </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <!--                    <div class="col-xs-12 col-md-12">-->
                    <!--                        <p><b style="color: red; font-weight: bolder">Eliminar Documento Clinico</b></p>-->
                    <!--                    </div>-->
                    <div class="col-xs-12 col-md-12">
                        <span style=" color: #eb9627"> <i class="fa fa-info-circle"></i>
                               Puede asociar Documentos clinicos a más de un Paciente
                    </div>

                    <div class="col-xs-12 col-md-12 " style="margin-top: 15px">
                        <label for="">Paciente</label>
                        <select name="document_paciente" id="document_paciente" class="form-control" style="width: 100%">
                            <option value=""></option>
                        </select>

                        <script>
                           $('#document_paciente').select2({
                                placeholder: 'buscar pacientes' ,
                                language: languageEs,
                                minimumInputLength: 2,
                                ajax:{
                                    url: $DOCUMENTO_URL_HTTP + "/application/system/agenda/controller/agenda_controller.php",
                                    dataType: "json",
                                    data: function (params) {
                                        var query = {
                                            search: params.term,
                                            ajaxSend:'ajaxSend',
                                            accion:'pacientes_activodesact'
                                        };
                                        return query;
                                    },
                                    processResults: function (data) {
                                        return {
                                            results: data.items
                                        };
                                    }
                                }
                            });
                        </script>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn text-bold" style="color: green" id="Guardar_documento_clinico" onclick="asociarPaciente()">
                    Guardar
                    <span class="fa fa-refresh btnSpinner hide"></span>
                </button>
            </div>
        </div>

    </div>
</div>


<script>
    
    function asociarPaciente() {

        // var checkeid = $('[name=checkedHijos]:checked').map(function (el, value) {
        //     return $(value).prop('dataset').iddoc;
        // });

        var checkeid = $( '[name=checkedHijos]:checked' )
            .map(function() {
                return this.value;
            }).get().join();

        // console.log(checkeid);

        var parametros ={
            'accion': 'asociar_pacientes_document',
            'ajaxSend': 'ajaxSend',
            'id': checkeid,
            'idpaciente': $('#document_paciente').find(':selected').val()
        };

        button_loadding($("#Guardar_documento_clinico"), true);
        $.ajax({
            url: $DOCUMENTO_URL_HTTP + '/application/system/documentos_clinicos/controller_documentos/controller_document.php',
            type:'POST',
            data: parametros,
            dataType:'json',
            cache:false,
            async:true,
            complete: function (xhr, status) {
                button_loadding($("#Guardar_documento_clinico"), false);
            }, 
            success:function (response) {
                if(response['error']==''){
                    var table = $("#listaDocumentosForm").DataTable();
                    table.ajax.reload(null, false);
                    $("#asociar_documento_pacientes").modal("hide");
                    setTimeout(()=>{notificacion('Información Actualizado', 'success');}, 500);
                }else{

                }
                button_loadding($("#Guardar_documento_clinico"), false);
            }
        });
    }

    function DocumentosForm(){


        var ElemmentoContentload = $("#listaDocumentosForm");
        boxTableLoad(ElemmentoContentload, true);
        var parametros = {
            'ajaxSend'  :'ajaxSend',
            'accion'    :'DocumentosForm',
            'idtableDocument' : "<?= base64_encode($idDoc) ?>",
            "iddoc"           : "<?= $idDocRegistro?>" ,
            "numero"       : $("#numero_doccum").val() ,
            "emitido"      : $("#emitido_doccum").val() ,
            "paciente_doccum"      : $("#paciente_documento").val() ,
        };
        var table = $("#listaDocumentosForm").DataTable({
            searching: false,
            ordering:false,
            destroy:true,
            serverSide:true,
            fixedHeader: true,
            paging:true,
            processing: true,
            lengthMenu:[ 10 ],
            lengthChange: false,
            ajax:{
                url: $DOCUMENTO_URL_HTTP + '/application/system/documentos_clinicos/controller_documentos/controller_document.php',
                type:'POST',
                data: parametros,
                dataType:'json',
                cache:false,
                async:true,
                complete: function (xhr, status) {
                    boxTableLoad(ElemmentoContentload, false);
                }
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
                            domMenu += "<li><a href='#' data-number='"+full['number']+"'  onclick='EliminarDocumento(this, "+full['idInfoDoc']+")' >Eliminar</a></li>";
                        domMenu += "</ul>";
                        domMenu += "</div>";
                        return domMenu;
                    }
                },
                {
                    targets:0,
                    render: function (data, type, full, meta) {
                        var name_form = "<?= $idDoc ?>";
                        var inputChecked = "<input type='checkbox' name='checkedHijos' data-name_form='"+name_form+"' data-iddoc='"+full['idInfoDoc']+"' value='"+full['idInfoDoc']+"'>";
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
        }).on( 'length.dt', function ( e, settings, len ) { // cambiar
            boxTableLoad(ElemmentoContentload, true);
        }).on( 'page.dt', function ( e, settings, len ) { // cambiar
            boxTableLoad(ElemmentoContentload, true);
        });
        // new $.fn.dataTable.FixedHeader( table );
        new $.fn.dataTable.FixedHeader( table,
            {
                // headerOffset: 50
            }
        );

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

        if(!ModulePermission('Documentos clinicos', 'consultar')){
            notificacion('Ud. No tiene permiso para esta Operación', 'error');
            return false;
        }

        if($("input[name='checkedHijos']:checked").length==1){
            var inputCheacked = $("input[name='checkedHijos']:checked");
            if(inputCheacked.is(":checked")){
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
        // console.log($("input[name='checkedHijos']:checked").length);

    });

    var PermisoModificar = function(Element){
        if(!ModulePermission('Documentos clinicos','modificar')){
            notificacion('Ud. no tiene permiso para realizar esta Operación', 'error');
            return false;
        }
        window.location = Element.prop('dataset').url;
    };

    function AsociarDocumento(Elemento){

        if(!ModulePermission('Documentos clinicos', 'modificar')){
            $(Elemento).attr('disabled', true);
            notificacion('Ud. No tiene permiso para realizar esta Operación', 'error');
            return false;
        }

        $(Elemento).attr('disabled', false);

        if($('[name=checkedHijos]:checked').length>0){
            $("#asociar_documento_pacientes").modal('show');
        }else if($('[name=checkedHijos]:checked').length==0){
            notificacion('Debe seleccion una opción', 'error');
        }else{
            // notificacion('Solo puede seleccionar una opción', 'error');
        }

    }


    var EliminarDocumento = function (Element, id_docc) {

        if(!ModulePermission('Documentos clinicos', 'eliminar')){
                notificacion('Ud. No tiene permiso para realizar esta Operación', 'error');
            return false;
        }

        Element = $(Element);
        var id = id_docc;
        var object = {
            id: id,
            callback: function () {
                boxloading($boxContentDocumento, true);
                $.ajax({
                    url: $DOCUMENTO_URL_HTTP + '/application/system/documentos_clinicos/controller_documentos/controller_document.php',
                    delay:1000,
                    type:'POST',
                    data:{'ajaxSend':'ajaxSend',
                        'accion':'eleminar_Registro_Doc_Data',
                        'id': id
                    },
                    async:true,
                    cache:false,
                    dataType:'json',
                    complete: function(xhr, status){
                        boxloading($boxContentDocumento, false, 1000);
                    },
                    success:function (response) {
                        // console.log(response);
                        boxloading($boxContentDocumento, false, 1000);
                        if(response.error==""){
                            var table = $("#listaDocumentosForm").DataTable();
                            table.ajax.reload(null, false);
                            setTimeout(()=>{ notificacion('información Actualizada', 'success'); }, 700);
                        }else{
                            setTimeout(()=>{ notificacion(response.error, 'error'); }, 700);
                        }
                    }
                });
            }
        };

        notificacionSIoNO("Eliminar Registro ","Una vez Eliminado el registro no podra ser recuperado #"+Element.prop('dataset').number, object);
    };


    $("#aplicar_doc").click(function () {
        DocumentosForm();
    });
    $("#limp_doc").click(function () {

        $("#emitido_doccum").val(null);
        $("#paciente_documento").val(null);
        $("#paciente_documento").val(null).trigger('change');
        DocumentosForm();
    });

    $(document).ready(function() {
        DocumentosForm();
    });

    window.addEventListener("load", function() {

        $('#emitido_doccum').daterangepicker({

            locale: {
                format: 'YYYY/MM/DD' ,
                daysOfWeek: [
                    "Dom",
                    "Lun",
                    "Mar",
                    "Mie",
                    "Jue",
                    "Vie",
                    "Sáb"
                ],
                monthNames: [
                    "Enero",
                    "Febrero",
                    "Marzo",
                    "Abril",
                    "Mayo",
                    "Junio",
                    "Julio",
                    "Agosto",
                    "Septiembre",
                    "Octubre",
                    "Noviembre",
                    "Diciembre"
                ],
            },

            startDate: moment().startOf('month'),
            endDate: moment(),
            ranges: {
                'Hoy': [moment(), moment()],
                'Ayer': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                'Últimos 7 Dias': [moment().subtract(6, 'days'), moment()],
                'Últimos 30 Dias': [moment().subtract(29, 'days'), moment()],
                'Mes Actual': [moment().startOf('month'), moment().endOf('month')],
                'Mes Pasado': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
                'Año Actual': [moment().startOf('year'), moment().endOf('year')],
                'Año Pasado': [moment().subtract(1, 'year').startOf('year'), moment().subtract(1, 'year').endOf('year')],
            }
        });

        $('.rango span').click(function() {
            $(this).parent().find('input').click();
        });

        $('#emitido_doccum').val(null);

        if(!ModulePermission('Documentos clinicos', 'consultar')){
            notificacion('Ud. No tiene permiso para consultar', 'error');
            return false;
        }

    });


</script>



