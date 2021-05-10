
var interval_notification;

var timeOut  = 1000;
var timeReal = 8000;

var url    = $DOCUMENTO_URL_HTTP + "/application/controllers/controller_peticiones_globales.php";
var paramt = { 'ajaxSend':'ajaxSend', 'accion':'notification_'};


function to_accept_noti_confirmpacient( id )
{
    $.ajax({
        url: $DOCUMENTO_URL_HTTP + '/application/controllers/controller_peticiones_globales.php',
        data:{'ajaxSend':'ajaSend', 'accion' : 'accept_noti_confirm_pacient', 'id': id},
        dataType:'json',
        async:false,
        success:function(resp){
            if(resp.error == "")
            {
                // location.reload(true);
                $.get(url, paramt , function(data) {

                    var HTML = $.parseJSON(data);
                    if(HTML['error'] == ""){
                        Htmlnotificacion( HTML.data, HTML.N_noti );
                    }
                });
            }
        }

    });
}


function Actulizar_notificacion_citas(idcita)
{
    $.ajax({
        url: $DOCUMENTO_URL_HTTP + "/application/system/agenda/controller/agenda_controller.php",
        type:'POST',
        data:{'ajaxSend': 'ajaxSend', 'accion': 'EstadoslistCitas', 'idestado':4, 'idcita':idcita },
        dataType:'json',
        async: false,
        success:function (resp) {

            if(resp['errmsg'] == "")
            {
                var encuentra = 0;
                //si en caso me encuentro en el modulo de agenda
                if($('#tableAgenda').length>0){
                    var  table      = $("#tableAgenda").DataTable();
                    table.ajax.reload();
                    encuentra++;
                }

                if(encuentra==0){
                    // location.reload(true);
                    $.get(url, paramt , function(data) {
                        var HTML = $.parseJSON(data);
                        if(HTML['error'] == ""){
                            Htmlnotificacion( HTML.data, HTML.N_noti );
                        }
                    });
                }
            }

        }

    });
}


// Consultar Tiempo Real


// interval_notification = setInterval(function () {
//
//     $.get(url, paramt , function(data){
//
//         var HTML = $.parseJSON(data);
//         if(HTML['error'] == ""){
//             Htmlnotificacion( HTML.data, HTML.N_noti );
//         }
//
//     });
//
// },timeReal);

$( window ).on("load", function() {

    // $('.notiflist , .media').mouseleave(function() {
    //
    //     interval_notification = setInterval(function(){
    //
    //         $.get(url, paramt , function(data){
    //
    //             var HTML = $.parseJSON(data);
    //             if(HTML['error'] == ""){
    //                 Htmlnotificacion( HTML.data, HTML.N_noti );
    //             }
    //         });
    //
    //     },timeReal);
    //
    // });

    // $('.notiflist , .media').mouseenter(function() {
    //     clearInterval( interval_notification );
    // });

    var CloseVal = 0;
    var Interval = setInterval(()=>{
        Notify_odontic();
        CloseVal++;
        // if(CloseVal==10){
        //     clearInterval(Interval);
        //     vuelvoEmpezarNotifyOdontic();
        // }
    },25000);

    function vuelvoEmpezarNotifyOdontic() {
        setTimeout(function() {
            Interval
        },5000);
    }

    Notify_odontic(true);

});


function Htmlnotificacion( $data , $N ) {
    $('.notiflist').html( $data );
    $('#N_Notificaciones').text( ($N==0)?0:$N );
    $('#N_noti').text( ($N==0)?0:$N );
}



//drop Static
$('.messages-menu').on({
    "shown.bs.dropdown": function() { this.closable = false; },
    "click":             function() { this.closable = true; },
    "hide.bs.dropdown":  function() { return this.closable; }
});


//click Agendar Citas
if( $('#nuevoGuardarCitas').length > 0){
    $('#nuevoGuardarCitas').click(function(){
        Notify_odontic();
    }); 
}


function Notify_odontic(tiempoReal=false) {

    var parametros = {
        'ajaxSend'  : 'ajaxSend',
        'accion'    : 'notification_',
        'validTime' : (tiempoReal==true)?1:0,
    };

    $.ajax({
        url: url,
        type:'POST',
        data: parametros,
        dataType:'json',
        cache:false,
        complete: function (xhr, status) {
            console.log(xhr);
        },
        success: function (response) {

            var push_html   = response['data'];
            var numero_push = response['N_noti'];

            Htmlnotificacion( push_html, numero_push );
        }
    });

    // $.get(url, paramt , function(data) {
    //     var HTML = $.parseJSON(data);
    //     if(HTML['error'] == ""){
    //         Htmlnotificacion( HTML.data, HTML.N_noti );
    //     }
    // });

}

//Filtrar x Fecha

/***$('#startDate').daterangepicker({

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
**/

