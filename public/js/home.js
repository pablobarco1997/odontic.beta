
function Npresupuestos() {
    var url = $DOCUMENTO_URL_HTTP + '/application/controllers/controller_peticiones_globales.php';
    var Data = {
        'ajaxSend' :'ajaxSend',
        'accion'   :'Npresupuestos',
        'date': $('#startDate').val()
    };
    $.get(url, Data, function (data) {
        var respuesta = $.parseJSON(data);
        $("#nu_plantActivoAbonad").text(respuesta['presupuestos']);
    })
}

function CitasAnuladaxDateAtendidos() {
    var url = $DOCUMENTO_URL_HTTP + '/application/controllers/controller_peticiones_globales.php';
    var Data = {
        'ajaxSend' :'ajaxSend',
        'accion'   :'CitasAnuladaxDate_Atendidos',
        'date': $('#startDate').val(),
    };
    $.get(url, Data, function (data) {
        var respuesta = $.parseJSON(data);
        $("#nu_citasAnuladaCancel").text(respuesta['citasAnulaxDate']);
        $("#nu_citasAtendidas").text(respuesta['citasAtendidas']);
    })
}

function obtenerPacientesxDate() {
    $.ajax({
        url: $DOCUMENTO_URL_HTTP + '/application/controllers/controller_peticiones_globales.php',
        type:'GET',
        data:{'ajaxSend':'ajaxSend', 'accion':'pacientesxDate', 'date': $('#startDate').val() },
        dataType:'json',
        success:function( resp ) {
            $('#nu_paciente').text( resp['pacientesxDate'] );
        }
    });
}


//BUSCAR PACIENTE
$('#buscarPaciente').on('click', function() {

    var $id =  $('#idpacienteAutocp').text();
    if($id !="" && $('.seachPacienteHome').val() !="")
    {
        // var $url = $DOCUMENTO_URL_HTTP + '/application/system/pacientes/admin_paciente/?view=form_datos_personales&id='+$id;
        var $url = $DOCUMENTO_URL_HTTP + '/application/system/pacientes/pacientes_admin?view=dop&key=' + $keyGlobal + '&id=' + $id;
        // alert($url);
        location.href = $url;
    }

});

$("#startDate").on("change", function() {
    obtenerPacientesxDate();
    CitasAnuladaxDateAtendidos();
    Npresupuestos();
});

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

    obtenerPacientesxDate();
    CitasAnuladaxDateAtendidos();
    Npresupuestos();

});