
$selecioneLaboratorio = null;

var table = null;
var table2 = null;

var listLaboratorios = function () {

    table =  $("#laboratorio_list").DataTable({

        searching: false,
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
        columnDefs:[
            {
                targets:0,
                render: function(data, type, full, meta) {

                    var domMenu = "<div class='dropdown pull-left'>";
                    domMenu += "<button class='btn btnhover  dropdown-toggle btn-xs ' type='button' data-toggle='dropdown' style='100%' aria-expanded='true'>" +
                        "<i class=\"fa fa-ellipsis-v\"></i>" +
                        "</button>";
                    domMenu += "<ul class='dropdown-menu pull-left'>";
                        domMenu += "<li> <a href='#' onclick='FormModificarLaboratorio("+full['idlab']+")' > Modificar Laboratorio</a> </li>";

                        if(full['estado']=='A')
                            domMenu += "<li> <a href='#' onclick=''> inhabilitar </a> </li>";

                        if(full['estado']=='E')
                            domMenu += "<li> <a href='#' onclick=''> habilitar </a> </li>";


                        domMenu += "<li> <a href='"+$DOCUMENTO_URL_HTTP+"/application/system/configuraciones/index.php?view=form_laboratorios_conf&v=prestacionlab&idlabora="+full['idlab']+"'> Prestación Laboratorio </a> </li>";
                    domMenu += "</ul>";
                    domMenu += "</div>";

                    return domMenu;

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



};

var FormModificarLaboratorio = function(idLaboratorio) {

    var idLaborat = idLaboratorio;

    $("#InputLaboratorio").attr("data-idlaboratorio",idLaborat);
    $("#InputLaboratorio").attr("data-subaccion","modificar");

    $("#addModificarLaboratorio").modal("show");

    var parametros = {
       'accion': 'fetchModificarLaboratorio',
       'ajaxSend': 'ajaxSend',
       'idLab': idLaborat
    };

    var url = $DOCUMENTO_URL_HTTP + '/application/system/configuraciones/controller/conf_controller.php';

    $.get(url , parametros, function(data) {

        var respuesta = $.parseJSON(data);

        if(respuesta['error'] == ''){

            var object = respuesta['information'];

            $("#nombre_laboratorio").val(object['name']).trigger('keyup');
            $("#direccion_laboratorio").val(object['direccion']).trigger('keyup');
            $("#telefono_laboratorio").val(object['telefono']).trigger('keyup');
            $("#infoAdicional_laboratorio").val(object['info_adicional']).trigger('keyup');

        }else{

        }

    });

};

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

var FormValidarPrestacion = function(){

    var Error = [];
    var RgxVacio                =  new RegExp((/^\s*$/));
    var catprestacion = $("#catprestacion");
    var nameprestacion = $("#name_prestacion");
    var costo = $("#costo_prestacion");
    var precio = $("#precio_prestacion");

    if( catprestacion.find(":selected").val() == "" ){
        Error.push({
            'Dom' : catprestacion ,
            'msgerr' : 'Debe selecionar una opción'
        });
    }
    if( RgxVacio.test(nameprestacion.val()) ){
        Error.push({
            'Dom' : nameprestacion ,
            'msgerr' : 'No puede Ingresar campo vacio'
        });
    }
    if( RgxVacio.test(costo.val()) ){
        Error.push({
            'Dom' : costo ,
            'msgerr' : 'No puede Ingresar campo vacio'
        });
    }
    if( RgxVacio.test(precio.val()) ){
        Error.push({
            'Dom' : precio ,
            'msgerr' : 'No puede Ingresar campo vacio'
        });
    }

    $(".msg_err_name_document_prestacion").remove();

    for (var i = 0; i <= Error.length -1; i++)
    {
        var msg   = document.createElement('small');
        var Dom   = Error[i]['Dom'];
        $(msg)
            .html(Error[i]['msgerr']+"<br>")
            .addClass("msg_err_name_document_prestacion")
            .css('color', 'red')
            .insertAfter(Dom);
    }

    if(Error.length>0)
        return false;
    else
        return true;

};

//

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


//Nuevo Modificar Prestacion de Laboratorio
$("#crearPresatacionAsoLabo").on("click", function() {

    if( FormValidarPrestacion() == false)
        return false;



    if($("#Labprestacion").prop('dataset').subaccion=="nuevo")
        var subaccion = "nuevo";
    else
        var subaccion = "modificar";

    var catprestacion = $("#catprestacion").find(":selected").val();
    var nameprestacion = $("#name_prestacion").val();
    var costo = $("#costo_prestacion").val();
    var precio = $("#precio_prestacion").val();

    var datos = {
        catprestacion, nameprestacion, costo,  precio
    };

    var parametros = {
        'accion': 'nuevoModificarPrestacionLab',
        'ajaxSend': 'ajaxSend',
        'idlab': $selecioneLaboratorio,
        'subaccion': subaccion,
        'datos': datos
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
                $("#ModalPrestacion_LaboratorioClinico").modal("hide");
                var table = $("#prestacionLaboratorio").DataTable();
                table.ajax.reload();
            }else {
                notificacion(resp['error'], 'error');
            }
        }
    });

});

function NuevoModificarLaboratorio(idLaboratorio = 0, sub){

    var table = $("#laboratorio_list").DataTable();

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
                filtroLaboratorio();
                $("#addModificarLaboratorio").modal('hide');
            }else {
                notificacion(resp['error'], 'error');
            }
        }
    });

}

function filtroLaboratorio(){

    var accion = "list_laboratorios";
    var ajaxSend = "ajaxSend";

    var url = $DOCUMENTO_URL_HTTP + '/application/system/configuraciones/controller/conf_controller.php';
    var newUrl = url + '?' +
        'accion='+accion+
        '&ajaxSend='+ajaxSend;

    table.ajax.url(newUrl).load();

}


function fetchLaboratorioPrestaciones(idLab, Element){

    if(idLab!=""&&idLab!=0&&idLab!=null){

        var objectLab  = Element.find("option:selected").prop("dataset").arraylab;
        console.log($.parseJSON(objectLab));

        MostrarInformacionPrestaLabo($.parseJSON(objectLab));

        tableDinamicPrestacion('PrestacionesXlaboratorio') //lista de prestaciones

    }else{
        $("#selecioneLaboratorio").modal("show");
    }
}


//mostrar informacion Prestacion
function MostrarInformacionPrestaLabo(object){

    if( $("#informacionPrestacion").has("none") ){

        if(object['name']!=""){

            $("#informacionPrestacion").css("display", "block");

            $("#nameLab").text(object['name']);
            $("#DirecLab").text(object['direccion']);
            $("#telefLab").text(object['telefono']);
            $("#infoLab").text(object['info_adicional']);

        }

    }

}

$FormTable = "";

var tableDinamicPrestacion = function(table=""){

    if(table2!=null){
        table2.destroy();
        $("#prestacionLaboratorio").empty();
    }


    var thead = "";

    if(table=="PrestacionesXlaboratorio") {
        thead += "<thead>";
            thead += "<tr>";
                thead += "<th>&nbsp;</th>";
                thead += "<th>Prestación</th>";
                thead += "<th>Costo de Clinica</th>";
                thead += "<th>Precio de Cliente</th>";
            thead += "</tr>";
        thead += "</thead>";
    }

    if(table=="PagosRealizado"){
        thead += "<thead>";
            thead += "<tr>";
                thead += "<th>&nbsp;</th>";
                thead += "<th>Fecha de Pago</th>";
                thead += "<th># Pago</th>";
                thead += "<th># P.Tratamiento</th>";
                thead += "<th>Paciente</th>";
                thead += "<th>Prestación</th>";
                thead += "<th>users autor</th>";
                thead += "<th>Costo de Clinica</th>";
                thead += "<th>Precio de Cliente</th>";
                thead += "<th>Total pagado</th>";
            thead += "</tr>";
        thead += "</thead>";
    }

    if(table=="tratamientosPrestaciones"){
        thead += "<thead>";
            thead += "<tr>";
                thead += "<th>&nbsp;</th>";
                // thead += "<th>Laboratorio</th>";
                thead += "<th>Tratamiento</th>";
                thead += "<th>Prestación</th>";
                thead += "<th>Paciente</th>";
                thead += "<th>Pieza</th>";
                thead += "<th>Estado</th>";
                thead += "<th>Monto</th>";
            thead += "</tr>";
        thead += "</thead>";
    }

    $FormTable = table;

    $("#prestacionLaboratorio").html(thead);

    var parametros = {
        'accion': 'tableDinamicPrestacion',
        'ajaxSend': 'ajaxSend',
        'table': table,
        'idlab': $selecioneLaboratorio
    };

    table2 = $("#prestacionLaboratorio").DataTable({
        destroy: true,
        searching: false,
        ordering:false,
        serverSide:true,
        processing:true,

        ajax:{
            url:  $DOCUMENTO_URL_HTTP + '/application/system/configuraciones/controller/conf_controller.php',
            type: 'POST',
            data: parametros,
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
};

//Activar y Desactivar Laboratorio



//Filtros Aplicar limpiar
$(".aplicar").click(function() {
    FiltroLabPrestacion();
});

$(".limpiar").click(function() {
    $("#nam_prestacion").val(null);
    FiltroLabPrestacion();
});

function FiltroLabPrestacion(){

    var  table      = $("#prestacionLaboratorio").DataTable();
    var  accion     = "tableDinamicPrestacion";
    var  ajaxSend   = "ajaxSend";
    var  idlab      = $selecioneLaboratorio;

    var url = $DOCUMENTO_URL_HTTP + '/application/system/configuraciones/controller/conf_controller.php';
    var newUrl = url + '?' +
        'accion='+accion+
        '&ajaxSend='+ajaxSend+
        '&table='+$FormTable+
        '&idlab='+idlab+
        '&searchLab='+$("#nam_prestacion").val();

    table.ajax.url(newUrl).load();

}

$(document).ready(function() {

});

/*onload window*/

window.onload = boxloading($boxContentConfiguracion, true);

/*load window*/
$(window).on("load", function() {

    boxloading($boxContentConfiguracion, true, 500);

});