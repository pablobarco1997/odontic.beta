
var interval_notification;

var timeOut  = 1000;
var timeReal = 8000;

var url    = $DOCUMENTO_URL_HTTP + "/application/controllers/controller_peticiones_globales.php";
var paramt = { 'ajaxSend':'ajaxSend', 'accion':'notification_'};


function to_accept_noti_confirmpacient( Elemento )
{
    var table = '';

    if(Elemento.prop('dataset').id == ""){
        return false;
    }
    table = Elemento.prop('dataset').type;

    if(table=='')
        return false;

    var id  =  Elemento.prop('dataset').id;

    $.ajax({
        url: $DOCUMENTO_URL_HTTP + '/application/controllers/controller_peticiones_globales.php',
        data:{'ajaxSend':'ajaSend', 'accion' : 'accept_noti_confirm_pacient', 'id': id, 'table': table },
        dataType:'json',
        async:false,
        success:function(resp){
            if(resp.error == ""){
                // location.reload(true);
                Notify_odontic(1 , false);
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
        Notify_odontic(1, true);
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

    Notify_odontic(1, true);

});


//drop Static
$('.messages-menu').on({
    "shown.bs.dropdown": function() { this.closable = true; },
    "click":             function() { this.closable = false; },
    "hide.bs.dropdown":  function() { return this.closable; }
});

//click Agendar Citas
if( $('#nuevoGuardarCitas').length > 0){
    $('#nuevoGuardarCitas').click(function(){
        Notify_odontic(1 , false );
    }); 
}

function Notify_odontic(solo_numero_noti = false, tiempoReal=false) {

    if(solo_numero_noti==1){

        const Object = {
            accion:'n_notify',
            ajaxSend:'ajaxSend'
        };

        var historial = fetch(url+'?ajaxSend=ajaxSend&accion=notification_&subaccion=noti_numero'+'&numero_notify='+(parseFloat(($('#N_noti').text()=="")?0:$('#N_noti').text())), {
            method: 'POST',
            body:JSON.stringify(Object),
            headers:{
                "Content-type" : "application/json; charset-UTF-8"
            }
        })
        .then(reponse  => reponse.json())
        .then(response => {

            $('#N_noti').text(response.n_notify.result);
            if(response.notificacion_push > 0){
                //alert notificacion
                if($("#swal2-container").length == 0){
                    notificacion('Tiene notificaciones Pendientes', 'question');
                }
            }
        });


        return true;
    }

    var parametros = {
        'ajaxSend'  : 'ajaxSend',
        'accion'    : 'notification_',
        'validTime' : (tiempoReal==true)?1:0,
    };

    $.ajax({
        url: url,
        type:'POST',
        delay:300,
        data: parametros,
        dataType:'json',
        cache:false,
        async:true,
        complete: function (xhr, status) {

        },
        success: function (response) {

            var push_data   = response['data'];
            var numero_push = response['N_noti'];

            Htmlnotificacion( push_data, numero_push );
        }
    });

    // $.get(url, paramt , function(data) {
    //     var HTML = $.parseJSON(data);
    //     if(HTML['error'] == ""){
    //         Htmlnotificacion( HTML.data, HTML.N_noti );
    //     }
    // });

}

function Htmlnotificacion( push_data , n ) {

    if($('.remove_element_cita_agend').length>0){
        // $('.remove_element_cita_agend').remove();
    }

    var array_notify = [];
    var ul_noti_list = $("#noti_list");
    var acumulador = 1000;

    var n = push_data['data'].length;
    var i = 0;
    for (i = 0; i <= push_data['data'].length -1; i++ ){

        var response = push_data['data'][i];

        /*NOTIFICAIONES_CITAS_PACIENTES*/
        if(response['tipo_notificacion']=='NOTIFICAIONES_CITAS_PACIENTES'){

            var clone_notificaion_citas = $("#star_notificaciones_cita_agendada_clone")
                .clone()
                .removeClass('hide')
                .attr('id','notificacion_cita_agendada_'+acumulador)
                .addClass('notificacion_cita_agendada_'+acumulador)
                .addClass('remove_element_cita_agend')
                .attr('title', 'notificación de cita para Hoy');

            var elemento = clone_notificaion_citas;
            elemento.find('.notify_cita_img_paciente').attr('src',response['icon']);
            elemento.find('.notify_cita_dateff').text((response['fecha'].replaceAll('-','/')));
            elemento.find('.notify_cita_encargado').text('Encargado: '+response['doctor_cargo']);
            elemento.find('.notify_cita_paciente').text('Paciente: '+response['nombe_paciente']);
            elemento.find('.notify_cita_horaIniFin').text(response['horaIni']+' h '+response['horafin']);
            elemento.find('.notify_cita_numero').text("#"+response['id_detalle_cita']);
            elemento.find('.notify_cita_estado').text('Estado actual de la cita '+response['estado_cita']);
            elemento.find('.notify_cita_visto').attr('data-id',response['id_detalle_cita']);

            var minutos_entrada = response['minutos_entrada']+' min';
            if(response['minutos_entrada']=='cita atrazada'){
                minutos_entrada = response['minutos_entrada'];  //sita atrazada
            }
            elemento.find('.notify_cita_minutos').text(minutos_entrada);
            array_notify.push(elemento);

        }

        /*NOTIFICACION_CONFIRMAR_PACIENTE*/
        if(response['tipo_notificacion']=='NOTIFICACION_CONFIRMAR_PACIENTE'){

            var clone_noti_email_confirmacion_paciente = $("#star_noti_email_confirmacion_paciente")
                .clone()
                .removeClass('hide')
                .attr('id','notificacion_confirm_via_mail_'+acumulador)
                .addClass('notificacion_confirm_via_mail_'+acumulador)
                .addClass('remove_element_cita_agend')
                .attr('title', 'confirmación de paciente por email');

            var msg = '';
            if(response['ope']=='ASISTIR'){ //asistir
                msg = "a notificado que <b>si asistirá</b> a la cita #"+response['numero_cita']+ '';
            }
            if(response['ope']=='NO_ASISTIR'){ //no asistir
                msg = "a notificado que <b>no asistirá</b> a la cita #"+response['numero_cita']+ '';
            }
            var elemento = clone_noti_email_confirmacion_paciente;
            elemento.find('.notify_confirm_label').html('El paciente <b>'+response['paciente']+'</b> '+msg);
            elemento.find('.notify_confirm_email_visto').attr('data-id', response['id']);
            array_notify.push(elemento);
        }

        acumulador++;
    }

    console.log(array_notify);
    ul_noti_list.empty();
    ul_noti_list.html(array_notify);

    // $('.notiflist').html( $data );
    // $('#N_Notificaciones').text( ($N==0)?0:$N );
    if(n!=0){
        $('#N_noti').text(n);
    }
}

