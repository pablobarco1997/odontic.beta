

//Lista principal
function list_citas_Asociadas()
{
    var ElemmentoContentload = $("#list_citasAsociadas");

    boxTableLoad(ElemmentoContentload, true);

    var table = $('#list_citasAsociadas').DataTable({
        destroy:true,
        searching: false,
        ordering:false,
        processing:true,
        serverSide: true,
        ajax:{
            url: $DOCUMENTO_URL_HTTP + '/application/system/pacientes/pacientes_admin/controller/controller_adm_paciente.php',
            type:'POST',
            data:{
                'ajaxSend'  :'ajaxSend',
                'accion'    :'list_citas_admin',
                'idpaciente': $id_paciente ,
                'fecha'     : $("#startDate").val(),
                'n_cita'    : $("#filtra_citas").val()
            },
            complete:function(xhr, status){
                boxTableLoad(ElemmentoContentload, false);
            },
            dataType:'json',
        },

        'createdRow':function(row, data, index){

            /** Aplicar el ancho */
            $(row).children().eq(0).css('width','15%');
            $(row).children().eq(1).css('width','17%');
            $(row).children().eq(2).css('width','9%');
            $(row).children().eq(3).css('width','27%');
            $(row).children().eq(4).css('width','20%');
            $(row).children().eq(5).css('width','17%');

        },
        language:{
            "sProcessing":     "Procesando...",
            "sLengthMenu":     "Mostrar _MENU_ registros",
            "sZeroRecords":    "No se encontraron resultados",
            "sEmptyTable":     "Ningún dato disponible en esta tabla",
            "sInfo":           "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
            "sInfoEmpty":      "Mostrando registros del 0 al 0 de un total de 0 registros",
            "sInfoFiltered":   "(filtrado de un total de _MAX_ registros)",
            "sInfoPostFix":    "",
            "sSearch":         "Buscar:",
            "sUrl":            "",
            "sInfoThousands":  ",",
            "sLoadingRecords": "Cargando...",
            "oPaginate": {
                "sFirst":    "Primero",
                "sLast":     "Último",
                "sNext":     "Siguiente",
                "sPrevious": "Anterior"
            },

            "oAria": {
                "sSortAscending":  ": Activar para ordenar la columna de manera ascendente",
                "sSortDescending": ": Activar para ordenar la columna de manera descendente"
            }

        },
    }).on( 'length.dt', function ( e, settings, len ) { // cambiar
        boxTableLoad(ElemmentoContentload, true);
    }).on( 'page.dt', function ( e, settings, len ) { // cambiar
        boxTableLoad(ElemmentoContentload, true);
    });

    new $.fn.dataTable.FixedHeader( table,
        {
            // headerOffset: 50
        }
    );

}

function AplicarFiltro() {

    var table        = $("#list_citasAsociadas").DataTable();
    var listEstados  = ($('#filtrar_estados').val().length>0)?($('#filtrar_estados').val()).toString():'';
    var accion   = 'list_citas_admin';
    var ajaxSend = 'ajaxSend';
    var url = $DOCUMENTO_URL_HTTP + '/application/system/pacientes/pacientes_admin/controller/controller_adm_paciente.php';
    var newUrl = url+'?'+
        'accion='+accion+
        '&ajaxSend='+ajaxSend+
        '&idpaciente='+$id_paciente+
        '&fecha='+$("#startDate").val()+
        '&n_cita='+$("#filtra_citas").val()+
        '&estadoslist='+listEstados;

    console.log(listEstados);
    table.ajax.url(newUrl).load();

}

function aplicarExportPdf(){

    var ElemmentoContentload = $("#list_citasAsociadas");

    boxTableLoad(ElemmentoContentload, true);

    var listEstados  = ($('#filtrar_estados').val().length>0)?($('#filtrar_estados').val()).toString():'';

    var urlpdf = $DOCUMENTO_URL_HTTP + "/application/system/pacientes/pacientes_admin/citas_asociadas/export/exportpdf_historialcitas.php?idpaciente=" + $id_paciente
        + "&date=" + $("#startDate").val()
        + "&ncita=" + $("#filtra_citas").val()
        + '&estadoslist=' + listEstados;

    $("#exportCitasPaciente").attr("href", urlpdf);

}


$("#limpiarFiltro").click(function() {

    $("#startDate").val(null);
    $("#filtra_citas").val(null);
    $("#filtrar_estados").val(null).trigger('change');
    aplicarExportPdf();
    AplicarFiltro();

});

$("#filtrar").click(function() {
    aplicarExportPdf();
    AplicarFiltro();
});




$(document).ready(function() {

    $('#startDate').daterangepicker({

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

    $("#startDate").val(null);

    $("#filtrar_estados").select2({
        placeholder:'seleccione uno o varios Estados',
        language:'es'
    });

});

window.onload = boxloading($boxContentViewAdminPaciente ,true);

$(window).on("load", function(e) {
    list_citas_Asociadas();
    boxloading($boxContentViewAdminPaciente ,false, 1000);
});