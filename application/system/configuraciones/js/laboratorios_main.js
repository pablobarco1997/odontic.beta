

var table = $("#laboratorio_list").DataTable({

    searching: true,
    ordering:false,
    destroy:false,
    serverSide:true,
    processing:true,

    ajax:{
        url: $DOCUMENTO_URL_HTTP + '/application/system/configuraciones/controller/conf_controller.php',
        type:'POST',
        data:{
            'ajaxSend':'ajaxSend',
            'accion': 'list_laboratorios',
        } ,
        dataType:'json',
    },


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

var FormaValidLaboratorio = function(revalid=false) {

    var RgxVacio                =  new RegExp((/^\s*$/));
    var Error                   = [];
    var nombre_laboratorio      = $("#nombre_laboratorio");
    var direccion_laboratorio   = $("#direccion_laboratorio");
    var telefono_laboratorio    = $("#telefono_laboratorio");

    if( RgxVacio.test(nombre_laboratorio.val()) ){
        Error.push({
            'Dom' : nombre_laboratorio ,
            'msgerr' : 'No puede Ingresar campo vacio'
        });
    }
    if( RgxVacio.test(direccion_laboratorio.val()) ){
        Error.push({
            'Dom' : direccion_laboratorio ,
            'msgerr' : 'No puede Ingresar campo vacio'
        });
    }
    if( RgxVacio.test(telefono_laboratorio.val()) ){
        Error.push({
            'Dom' : telefono_laboratorio ,
            'msgerr' : 'No puede Ingresar campo vacio'
        });
    }

    $(".msg_err_name_document").remove();

    for (var i = 0; i <= Error.length -1; i++)
    {
        var msg   = document.createElement('small');
        var Dom   = Error[i]['Dom'];
        $(msg)
            .html(Error[i]['msgerr']+"<br>")
            .addClass("msg_err_name_document")
            .css('color', 'red')
            .insertAfter(Dom);
    }

    if(Error.length>0)
        return false;
    else
        return true;



};


//nuevo Laboratorio
$("#crearLaboratorio").on("click", function() {

    $("#nombre_laboratorio").val(null);
    $("#direccion_laboratorio").val(null);
    $("#telefono_laboratorio").val(null);
    $("#infoAdicional_laboratorio").val(null);

    $("#InputLaboratorio").attr("data-idlaboratorio", "");
    $("#InputLaboratorio").attr("data-subaccion", "nuevo");

});




//nuevo modificar  Laboratorio
$("#nuevoUpdateLaboratorio").on("click", function() {

    if( FormaValidLaboratorio() == false)
        return false;


    var idLaboratorio = $("#InputLaboratorio").prop("dataset").idlaboratorio;
    var subaccion     = $("#InputLaboratorio").prop("dataset").subaccion;

    NuevoModificarLaboratorio(idLaboratorio, subaccion);

});


function NuevoModificarLaboratorio(idLaboratorio = 0, sub){

    if(sub=="")
        return false;



    var parametros  = [];

    var datos = {
        'nombre_laboratorio'        :$("#nombre_laboratorio").val() ,
        'direccion_laboratorio'     :$("#direccion_laboratorio").val(),
        'telefono_laboratorio'      :$("#telefono_laboratorio").val(),
        'infoAdicional_laboratorio' :$("#infoAdicional_laboratorio").val()
    };

    parametros  = {
        'accion'        : 'nuevoUpdateLaboratorio',
        'ajaxSend'      : 'ajaxSend',
        'subaccion'     : sub,
        'idLaboratorio' : idLaboratorio,
        'datos' : JSON.stringify(datos)
    };


    $.ajax({
        url:$DOCUMENTO_URL_HTTP + '/application/system/configuraciones/controller/conf_controller.php',
        type:'POST',
        data: parametros  ,
        dataType:'json' ,
        async:false,
        success: function (resp){
            if(resp['error'] == ''){
                notificacion('Información Actualizada', 'success');
                table.ajax().reload();
            }else {
                notificacion(resp['error'], 'error');
            }
        }
    });

}




$(document).ready(function() {

});

/*onload window*/

window.onload = boxloading($boxContentConfiguracion, true);

/*load window*/
$(window).on("load", function() {

    boxloading($boxContentConfiguracion, true, 500);

});