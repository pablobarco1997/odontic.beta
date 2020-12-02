
function list_mail_sent(){

    $('#mailSentTable').DataTable({
        searching: false,
        ordering:false,
        serverSide:true,
        ajax:{
            url: $DOCUMENTO_URL_HTTP + '/application/system/pacientes/pacientes_admin/email_sent/controller/controller_emailsent.php',
            type:'POST',
            data: {
                'ajaxSend'   : 'ajaxSend',
                'accion'     : 'list_mail_sent',
                'idpaciente' : $id_paciente,
            },

            dataType:'json',
        },
        'createdRow':function(row, data, index){

            /** Aplicar el ancho */
            $(row).children().eq(0).css('width','5%');
            $(row).children().eq(1).css('width','15%');
            $(row).children().eq(2).css('width','10%');
            $(row).children().eq(3).css('width','15%');
            $(row).children().eq(4).css('width','30%');
            $(row).children().eq(5).css('width','5%');

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

    });

}

function AplicarBusqueda(){

    var table        = $("#mailSentTable").DataTable();
    var fecha        = $("#startDate").val();
    var status       = $("#estadoEmailConfPaci").find(':selected').val();
    var n_citas      = $("#busqN_Cita").val();
    var accion       = 'list_mail_sent';
    var ajaxSend     = 'ajaxSend';

    var url = $DOCUMENTO_URL_HTTP + '/application/system/pacientes/pacientes_admin/email_sent/controller/controller_emailsent.php';

    var newUrl = url+'?'+
        'accion='+accion+
        '&ajaxSend='+ajaxSend+
        '&fecha='+fecha+
        '&status='+status+
        '&n_citas='+n_citas;

    table.ajax.url(newUrl).load();

}

$(".aplicar").click(function() {
    AplicarBusqueda();
});
$(".limpiar").click(function() {
    $("#startDate").val(null);
    $("#busqN_Cita").val(null);
    $("#estadoEmailConfPaci").val(null).trigger('change');
    AplicarBusqueda();
});


$(document).ready(function() {

    /**FECHA X RANGO*/
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

    $('#estadoEmailConfPaci').select2({
        placeholder:'Selecione una opción',
        allowClear:true,
        language:'es'
    });

});

//window onload
window.onload = boxloading($boxContentViewAdminPaciente ,true);
//window load
$(window).on("load", function() {

    list_mail_sent();
    boxloading($boxContentViewAdminPaciente ,false, 1000);

});