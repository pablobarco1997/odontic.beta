
function  cargarlistdocummclini()
{

    $('#list_docum_clini').DataTable({

        searching: false,
        ordering:false,
        destroy:true,

        ajax:{
            url: $DOCUMENTO_URL_HTTP + '/application/system/documentos_clinicos/controller_documentos/controller_document.php',
            type:'POST',
            data:{
                'ajaxSend':'ajaxSend',
                'accion':'list_informacion_doc',
                'idpaciente': $('#pacientes').find(':selected').val(),
                'fecha_create': $('#fecha_creacion_doc').val(),
                'bsq_documento': $('#bsq_documento').val(),
            },
            dataType:'json',
        },
        columnDefs:[
            {
                targets:3,
                render:function(data, type, full, meta) {

                    var urlDom = $DOCUMENTO_URL_HTTP+"/application/system/documentos_clinicos/index.php?view=form_doc&iddoc="+full['iddocument']+"&iddclin="+full['rowid'];

                    var domMenu = "<div class='dropdown pull-right'>";
                        domMenu += "<button class='btn btnhover  dropdown-toggle btn-xs ' type='button' data-toggle='dropdown' style='100%' aria-expanded='true'>" +
                                        "<i class=\"fa fa-ellipsis-v\"></i>" +
                                   "</button>";
                            domMenu += "<ul class='dropdown-menu pull-right'>";
                                 domMenu += "<li> <a href='"+urlDom+"'>Ver Documento</a> </li>";
                                 domMenu += "<li> <a href='#eliminar_documento_clinico_Modal'  data-toggle='modal' onclick='ElemenAttr("+full['rowid']+")'>Eliminar</a> </li>";
                            domMenu += "</ul>";
                    domMenu += "</div>";

                    return domMenu;

                }
            }
        ],

        // createdRow:function(row, data, dataIndex){
        //     // console.log(row);
        //     // $(row).on('click', '.impripdf', function() {
        //     //
        //     //     var DOMpdf = $(this).parents('.lipdf');
        //     //     var idtypeDocument = DOMpdf.data('idtipo'); /*tipo del documento ya sea ficha clinica o otros*/
        //     //     var iddocument     = DOMpdf.data('iddocument'); /*id del documento */
        //     //
        //     // });
        // },
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


/**Eliminar Documento Clinico Drop*/
var ElemenAttr = function(id){ $("#idElemnDocument").attr("data-id", id) };

$("#EliminarDocumentoClinico").on("click", function() {

    if( $("#idElemnDocument").prop("dataset").id != "" && $("#idElemnDocument").prop("dataset").id != 0 ){

        var parametros = {
            "accion"            : "eliminar_documento_clinico",
            "ajaxSend"          : "ajaxSend",
            "id"                : $("#idElemnDocument").prop("dataset").id ,
        };

        var urldoc = $DOCUMENTO_URL_HTTP + '/application/system/documentos_clinicos/controller_documentos/controller_document.php';

        $.get(urldoc , parametros , function(data) {
            var result = $.parseJSON(data);
            if(result['error'] != ''){
                notificacion(result['error'], 'error');
            }else{
                notificacion('Información Actualizada', 'success');
                cargarlistdocummclini();
                $("#eliminar_documento_clinico_Modal").modal("hide");
            }
        });

    }

});

/**END Drop*/


$("#crearDocumentClinico").click(function() {

    if($('#documento_').find(':selected').val() == "" || $('#doctpacientes').find(':selected').val() == ""){
        notificacion('Debe selecionar una opcion', 'error');
    }else{

        var fichaClinica = '&iddocumentype='+ $('#documento_').find(':selected').val();
        var idp    = '&idp='+ $('#doctpacientes').find(':selected').val();
        var view   = null;

        //ficha clinica general
        if( $.trim($('#documento_').find('option:selected').prop('dataset').iddocument) == $.trim('fichaclinicageneral')){
            view = 'formdocument_asoc_fichaclinica'; // ficha clinica
        }

        var url = $DOCUMENTO_URL_HTTP +'/application/system/documentos_clinicos/index.php?view=' + view + fichaClinica + idp;
        window.open(url);

    }
});

$(".aplicar").click(function() {
    cargarlistdocummclini();
});
$(".limpiar ").click(function() {
    $("#fecha_creacion_doc").val(null);
    $("#bsq_documento").val(null).trigger('change');
    cargarlistdocummclini();
});

$(document).ready(function() {

    $('#documento_ , #doctpacientes').select2({
        placeholder:'Seleccione una opción',
        allowClear:true,
        language:'es'
    });


    $('#fecha_creacion_doc').daterangepicker({

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

    $('#fecha_creacion_doc').val(null);

    //cargas los documentos clinicos
    cargarlistdocummclini();  //esta funcion es ta la lista de los documentos - debe ejecutarse primero

    $("#bsq_documento").select2({
        placeholder:'Seleccione una opción'
    });

});


/*
if( $acciondocummAsociado == "listdocumment")
{

    //creas un documento para el paciente - admin - en este caso este es un script para Fichas clinicas
    //En esta opcion uno puede seleccionar cualquier documento que este creado listo para guardar ya sea una ficha clinica o ficha odontogrma etc
    $('#crearDocumentClinico').click(function() {

        if($('#documento_').find('option:selected').val() != 0 || $('#documento_').find('option:selected').val() != "")
        {

            var idtypedoc = $('#documento_').find('option:selected').val(); //#id tipo del documento
            var urlDocument = $DOCUMENTO_URL_HTTP + '/application/system/pacientes/pacientes_admin/?view=docummclin&key=' + $keyGlobal + '&id='+ Get_jquery_URL('id') +'&v=docum_clin&dt='+idtypedoc;
            // alert(urlDocument);
            window.location = urlDocument;

        }else{

            notificacion('Debe selecionar un documento', 'error');
        }

    });



} */


/*

if($acciondocummAsociado == "docum_clin") //Docoumentos clinicos
{

    var iddocumentfk     = 0; //El id del documento  creado
    var idtipodocumentfk = 0; //El id del tipo de documento  creado

    idtipodocumentfk = Get_jquery_URL('dt');  //tipo
    iddocumentfk     = Get_jquery_URL('iddocmnt'); // id documento


    if(idtipodocumentfk == 1) //FICHA  CLINICA
    {
        if(iddocumentfk > 0)
        {
            setDocumentMod_fichaClinica( idtipodocumentfk , iddocumentfk );
        }
    }

}  */


//OBTENER EL ID DE UNA URL CON JQUERY         ----------------------------------------
function Get_jquery_URL(Getparam)
{
    let paramsGet = new URLSearchParams(location.search);
    var idGetUrl = paramsGet.get(Getparam);

    return idGetUrl;
}


window.onload =  boxloading($boxContentDocumento,true);

$(window).on("load", function() {

    boxloading($boxContentDocumento,false,1500);

});