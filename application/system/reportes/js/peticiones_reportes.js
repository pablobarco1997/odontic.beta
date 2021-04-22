



$boxHomeInicio = $("#ContentboxHomeInicio");


$tableLanguaje = {
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
};



function consultarAcciones(){

    var date = $('#startDate').val();

    var parametros = {
        'ajaxSend': 'ajaxSend',
        'accion':'consultar_accion_date',
        'date': date
    };

    $.ajax({
        url: $DOCUMENTO_URL_HTTP + '/application/system/reportes/controller/controller_reporte.php',
        type: 'POST',
        data:parametros,
        dataType:'json',
        async: false,
        cache:false,
        success:function (respuesta) {
            console.log(respuesta);

            var object = respuesta['result'];
            $("#nu_plantActivoAbonad").text(object['n_tratamientos']);
            $("#nu_citasAnuladaCancel").text(object['citas_canceladas']);
            $("#nu_citasAtendidas").text(object['atendidos']);
            $("#nu_paciente").text(object['n_pacientes']);
        }
    });

}



$('#buscarPaciente').on('click', function() {

    var $id =  $('#idpacienteAutocp').text();
    if($id !="" && $('.seachPacienteHome').val() !="")
    {
        var $url = $DOCUMENTO_URL_HTTP + '/application/system/pacientes/pacientes_admin?view=dop&key=' + $keyGlobal + '&id=' + $id;
        location.href = $url;
    }

});


$("#startDate").on("change", function() {

    cargarRecursosInfo();

    var textDate = $("#labelSpanishSatrDtae");
    var arrDate  = ($("#startDate").val()).split("-");

    var startDateOne = ToLocalDateSpanish(arrDate[0]);
    var startDateTwo = ToLocalDateSpanish(arrDate[1]);

    textDate.find('span').html(
        startDateOne+' <b>hasta</b> '+startDateTwo
    );

});


$("#reportes_pacientes_anulados").click(function() {

    var textDate = $("#labelRegistroxDate");
    var arrDate  = ($("#startDate").val()).split("-");
    var startDateOne = ToLocalDateSpanish(arrDate[0]);
    var startDateTwo = ToLocalDateSpanish(arrDate[1]);
    textDate.find('span').html(
        startDateOne+' <b>hasta</b> '+startDateTwo
    );

    $('#pacientes_registrados_').modal('show');

    $('#reporte_pacientes_registrados').DataTable({
        "processing": true,
        "serverSide": true,
        destroy:true,
        searching:false,
        ordering:false,
        lengthMenu: [10],
        ajax:{
            url:$DOCUMENTO_URL_HTTP + '/application/controllers/controller_peticiones_globales.php',
            type:'POST',
            data:{
                'accion':'pacientesxDate','ajaxSend':'ajaxSend',
                'date': $('#startDate').val(), 'object': 1
            },
            dataType:'json',
            complete:function () {
                // boxloading(idmodal ,false , 1500);
            },
        },
        createdRow:function (row, data, dataIndex) {
            $('td:eq(0)', row).css('width','20%');
            $('td:eq(1)', row).css('width','30%');
            $('td:eq(2)', row).css('width','8%');
            $('td:eq(3)', row).css('width','10%');
        },
        "language": $tableLanguaje
    });

});

var cargarRecursosInfo = function(){
    consultarAcciones();
};

$(document)
    .ajaxStart(function () {
        //ajax request went so show the loading image
        boxloading($boxHomeInicio ,true);
    })
    .ajaxStop(function () {
        //got response so hide the loading image
        boxloading($boxHomeInicio ,true,1000);
    });

$(document).ready(function () {

});

window.onload = boxloading($boxHomeInicio ,true);

$(window).on("load", function() {

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

    boxloading($boxHomeInicio ,true ,1500);

});
