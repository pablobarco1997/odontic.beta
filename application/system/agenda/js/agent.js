
function loadtableAgenda()
{
     var table = $('#tableAgenda').DataTable({
        searching: false,
        "ordering":false,
        "serverSide": true,
        // responsive: true,
        // destroy:true,
        // scrollX: true,
        // scrollY: 700,
        fixedHeader: true,
        paging:true,
        processing: true,
        lengthMenu:[ 5, 10, 25, 50, 100 ],
        "ajax":{
            "url": $DOCUMENTO_URL_HTTP + "/application/system/agenda/controller/agenda_controller.php",
            "type":'POST',
            "data": {
                'ajaxSend'             : 'ajaxSend',
                'accion'               : 'listCitas',
                'doctor'               : ($("#filtro_doctor").val()!="")?$("#filtro_doctor").val().toString():"",
                'estados'              : ($("#filtroEstados").val()!="")?$("#filtroEstados").val().toString():"",
                'fecha'                : $('.filtroFecha').val(),
                'eliminada_canceladas' : ( ( $('#listcitasCanceladasEliminadas').is(':checked')==true) ? "checked" : "") ,
                'buscar_xpaciente'     : ($('#buscarxPaciente').val()!="")?$('#buscarxPaciente').val().toString():"",
                'search_ncita'         : $('#n_citasPacientes').val(),
            },
            "dataType":'json',
        },
        'createdRow':function(row, data, index){

            if( data[7] == 6){
                $(row).css('backgroundColor','#EAFAF1');
            }

            //aplico style ancho a los hijos del primer nivel
            // console.log(            $(row).children().eq(1));
            $(row).children().eq(0).css('width','3%');
            $(row).children().eq(1).css('width','8%');
            $(row).children().eq(2).css('width','10%');
            $(row).children().eq(3).css('width','23%');
            $(row).children().eq(4).css('width','23%');
            $(row).children().eq(5).css('width','15%');
            $(row).children().eq(6).css('width','10%');

        },
         // columnDefs:[
         //     {
         //         targets:3,
         //         render:function (data, type, row, meta) {
         //             console.log(meta);
         //             return data;
         //         }
         //     }
         // ],
        // select: {
        //      style:    'os',
        //      selector: 'td:first-child'
        // },
         "language": {
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
         },
        "infoCallback": function (settings, start, end, max, total, pre){

            return "Mostrando registros del "+ start +" al "+ end +"<br>de un total de "+total+ " registros.";
        }
        // ajax:{
        //
        // },
    });
    // new $.fn.dataTable.FixedHeader( table );
    new $.fn.dataTable.FixedHeader( table,
        {
            // headerOffset: 50
        }
    );


}

function filtrarAgenda(validStatus="") {


    var  table      = $("#tableAgenda").DataTable();
    var  accion     = "listCitas";
    var  ajaxSend   = "ajaxSend";

    var info = table.page.info();

    console.log(info);

    var url = $DOCUMENTO_URL_HTTP + "/application/system/agenda/controller/agenda_controller.php";
    var newUrl = url + '?' +
        'accion='+accion+
        '&ajaxSend='+ajaxSend+
        '&doctor='+(($("#filtro_doctor").val()!="")?$("#filtro_doctor").val().toString():"")+
        '&estados='+( ($("#filtroEstados").val()!="")?$("#filtroEstados").val().toString():"")+
        '&fecha='+$('.filtroFecha').val()+
        '&eliminada_canceladas='+(( $('#listcitasCanceladasEliminadas').is(':checked')==true) ? "checked" : "") +
        '&buscar_xpaciente='+(($('#buscarxPaciente').val()!="")?$('#buscarxPaciente').val().toString():"") +
        '&search_ncita='+$('#n_citasPacientes').val();

    // if(validStatus!=""||validStatus=="reload"){
    //     newUrl += '&start2='+info['start']+'&validSatus=1';
    // }

    table.ajax.url(newUrl).load();

}

//Numero de citas
function NOTIFICACION_CITAS_NUMEROS()
{

    var parameters = {
        "ajaxSend": "ajaxSend",
        "accion"  : "numero_citas_pacientes_hoy",
    };

    var url = $DOCUMENTO_URL_HTTP + "/application/system/agenda/controller/agenda_controller.php";

    $.get(url , parameters , function(data) {

        var datos = $.parseJSON(data);

        $("#numCitas").text( datos.result );

    });


    setInterval(function(){
        $.get(url , parameters , function(data) {
            var datos = $.parseJSON(data);
            $("#numCitas").text( datos.result );
        });
    },3500)
}


//Funciones Cambio de estados 
function EstadosCitas(idestado, idcita, html, idpaciente) //Comprotamientos de los estados de las citas
{

    if(!ModulePermission(2,1)){
        notificacion('ud. No tiene permiso para consultar <br>Se le han deshabilitado las opciones','error');
        return false
    }

    var textEstado = html.data('text');

    switch (idestado)
    {
        case 1: //notificar por email

            $('#notificar_email-modal').modal('show');
            notificacion('El sistema no se responsabiliza por correo electrónico mal ingresado', 'question');
            $('#para_email').val( $.trim( html.data('email') ) ); //email destinario
            $("#enviarEmail").attr('onclick', 'notificaionEmail('+idpaciente+','+idcita+','+idestado+','+idcita+')');
            $("#para_email").keyup();

            break;

        case 2: // No confirmado

            UpdateEstadoCita(idestado, idcita, html, textEstado );
            break;

        case 3: // Confirmar por Telefono

            UpdateEstadoCita(idestado, idcita, html, textEstado );
            break;

        case 4: // En sala de espera

            UpdateEstadoCita(idestado, idcita, html, textEstado );
            break;

        case 5: // Atendiendose

            UpdateEstadoCita(idestado, idcita, html, textEstado );
            break;

        case 6: // Atendido

            $.get($DOCUMENTO_URL_HTTP + "/application/system/agenda/controller/agenda_controller.php" , {'ajaxSend':'ajaxSend', 'accion':'consultar_estado_cita_atrazada', 'idcita':idcita } , function(data) {

                var dato = $.parseJSON(data);

                if(dato.result == 'atrazada'){
                    notificacion('Esta cita se encuentra atrasada no puede cambiar a estado <b>Atendido</b>', 'question');
                }else{
                    UpdateEstadoCita(idestado, idcita, html, textEstado );
                }

            });

            break;

        case 7: // No asiste

            UpdateEstadoCita(idestado, idcita, html, textEstado );
            break;

        case 9: // Cancelada

            UpdateEstadoCita(idestado, idcita, html, textEstado );
            break;

        case 8:

            $("#number_whasap").text(html.data("telefono"));
            var number = html.data("telefono");
            $("#modalWhapsapp").modal("show");
            // $("#sendwhap").addClass('disabled_link3');

            /*se cambia el attr de un onclick y herf*/
            $("#sendwhap")
                .attr('onclick', 'UpdateEstadoCita('+idestado+','+idcita+','+'0'+','+"'textEstado'"+')')
                .attr('href','https://wa.me/'+number+'?text=hola');

            break;

        default:

            UpdateEstadoCita(idestado, idcita, html, textEstado );
            break;

    }


}

function UpdateEstadoCita(idestado, idcita, html = "", textEstado) //Actualizar Estados de las citas
{
    $.ajax({
        url: $DOCUMENTO_URL_HTTP + "/application/system/agenda/controller/agenda_controller.php",
        type:'POST',
        data:{'ajaxSend': 'ajaxSend', 'accion': 'EstadoslistCitas', 'idestado':idestado, 'idcita':idcita, 'estadoText':textEstado },
        dataType:'json',
        async: false,
        success: function(resp)
        {
            if(resp.error != "") {
                var table =  $('#tableAgenda').DataTable();
                notificacion( 'Información Actualizada', 'success');
                table.ajax.reload( null, false );
            }
        }
    });
}


function notificaionEmail($idPaciente, $idcita, idestado, idcita )
{


    $('#emailEspere').text('Enviando mensaje espere ...');

    // $(document).
    // bind("ajaxStart", function(){
    //     $('#emailEspere').text('Enviando mensaje espere ...');
    // }) .bind("ajaxSend", function(){
    //     $('#emailEspere').text('Enviando mensaje espere ...');
    // }).bind("ajaxComplete", function(){
    //     $('#emailEspere').text(null);
    // });

    var boxMail = $("#notificar_email-modal").find(".modal-dialog");
    boxloading(boxMail, true);

    var error = '';
    var error_registrar_email_ = '';

    setTimeout(function() {


        $.ajax({
            url: $DOCUMENTO_URL_HTTP + "/application/system/agenda/controller/agenda_controller.php",
            type:'POST',
            data:{
                'ajaxSend': 'ajaxSend',
                'accion': 'envio_email_notificacion',
                'idpaciente':$idPaciente,
                'idcita' : $idcita,

                'asunto': $('#asunto_email').val(),
                'from': $('#de_email').val(),
                'to': $('#para_email').val(),
                'subject': $('#titulo_email').val(),
                'message': $('#messge_email').val(),
            },
            dataType:'json',
            async: false,
            complete: function(xhr, status){
                $('#emailEspere').text(null);

                if(xhr['status']=='200'){
                    boxloading(boxMail,false,1000);
                }else{
                    if(xhr['status']=='404'){
                        notificacion("Ocurrió un error con la <b>solicitud Enviar Email</b> <br> <b>xhr: "+xhr['status']+" <br> Consulte con Soporte </b>");
                    }
                    boxloading(boxMail,false,1000);
                }
            },
            error:function(xhr, status){
                if(xhr['status']=='200'){
                    boxloading(boxMail,false,1000);
                }else{
                    if(xhr['status']=='404'){
                        notificacion("Ocurrió un error con la <b>solicitud Enviar Email</b> <br> <b>xhr: "+xhr['status']+" <br> Consulte con Soporte </b>");
                    }
                    boxloading(boxMail,false,1000);
                }

            },
            success: function(resp){

                error                   = resp.error_email;
                error_registrar_email_  = resp.registrar;

                boxloading(boxMail,false,1000);

                if(error == "" && error_registrar_email_ == "") {

                    $('#asunto_email').val();
                    $('#de_email').val();
                    $('#para_email').val();
                    $('#titulo_email').val();
                    $('#messge_email').val();

                    $('#notificar_email-modal').modal('hide');
                    UpdateEstadoCita(idestado, idcita, '', '' );
                    $('#emailEspere').text(null);

                }else{

                    if(error!="" ){
                        notificacion(error, 'error');
                    }
                    if(error_registrar_email_ != ""){
                        notificacion(error_registrar_email_, 'error');
                    }
                    $('#emailEspere').text(null);
                }

            }
        });

    },1500);

    return error;

}


//CREATE PLAN DE TRATAMIENTO DESDE CITAS
function create_plandetratamiento($idpaciente, $idcitadet, $iddoct, $html)
{

    var $puedo = false;

    var consultarPlanTratamiento = [];

    // alert($idcitadet+': idpaciente:' +$idpaciente);
    $.ajax({
        url: $DOCUMENTO_URL_HTTP + "/application/system/agenda/controller/agenda_controller.php",
        type:'POST',
        data:{'ajaxSend': 'ajaxSend', 'accion': 'nuevoUpdatePlantratamiento', 'idpaciente': $idpaciente, 'idcitadet': $idcitadet, 'iddoct': $iddoct, 'subaccion':'CREATE'},
        dataType:'json',
        async:false,
        success:function(resp) {
            var idpacienteToken = resp.idpacientetoken;
            if(resp.error == ''){
                notificacion('Plan de Tratamiento Creado - cargando...', 'success');
            }else {
                notificacion('Ocurrio un error con la Operación' , 'error');
            }
            if(resp.error == ''){
                var $tener = 0;
                var $idtratamiento = 0;
                if( resp.idtratamiento > 0){
                    $idtratamiento = resp.idtratamiento;
                    $tener++;
                }
                if($tener > 0){
                    if($idtratamiento > 0){

                        setTimeout(function() {
                            window.location = $DOCUMENTO_URL_HTTP + '/application/system/pacientes/pacientes_admin/?view=plantram&key=' + $keyGlobal + '&id=' + idpacienteToken + '&v=planform&idplan=' + $idtratamiento;
                        }, 1500);
                    }
                }
            }
        }

    });

    filtrarAgenda("reload"); //reload table agenda
}

function keyemail_invalic()
{
    var expresionRegularEmail = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;

    var email = $('#para_email');

    if(!expresionRegularEmail.test(email.val()))
    {
        $('#invali_emil_mssg').text('email incorrecto');
    }else{
        $('#invali_emil_mssg').text('');
    }

}


//Enviar Mensaje por whatsap
$("#mensajetext").keyup(function() {

    var telf  = $("#number_whasap").text();
    var texto = $(this).val();
    var url = "https://wa.me/"+telf+"?text=" + texto;

    if(texto != ""){
        $("#sendwhap").removeClass('disabled_link3');
        $("#sendwhap").attr("href", url);
    }

    if(texto == ""){
        $("#sendwhap").addClass('disabled_link3');
    }
    // alert(url);

});


// $(".select2_especialidad .select2_doctor .select2_duraccion .inputFecha .select2_hora").on("change",function(){
//     var $padre = $(this).parents(".row_detalleCitas");
//     INVALIC_CITAS_DETALLE($padre);
// });


//Commentari adicional muestra el modal y guarda el comentario
function clearModalCommentAdicional(iddetcita, html)
{
    $("#comment_adicional").val(null);

    if(iddetcita != "")
    {
        $.ajax({
            url: $DOCUMENTO_URL_HTTP + "/application/system/agenda/controller/agenda_controller.php",
            type:'GET',
            data:{'ajaxSend':'ajaxSend', 'accion':'addObservacion','iddetcita':iddetcita},
            dataType:'json',
            async:false,
            cache:false,
            success:function(resp) {
                if(resp['error']!=''){

                    $("#modal_coment_adicional")
                        .find('.modal-dialog')
                        .addClass('disabled_link3')
                        .modal('hide');

                    notificacion(resp['error'], 'error');

                }else{

                    $('#iddet-comment').attr('data-iddet', iddetcita);
                    $('#invali_commentadciol_mssg').text(null);
                    $('#comment_adicional').text(null);

                    $('#guardarCommentAdicional').attr('onclick', 'UpdateCitasCommentAdicional('+iddetcita+')')

                    $("#modal_coment_adicional")
                        .find('.modal-dialog')
                        .removeClass('disabled_link3');
                }
            }
        });

    }
}


function UpdateCitasCommentAdicional(iddetcita)
{
    var puedo = 0;

    if( $('#comment_adicional').val() == "" ){
        puedo++;
        $('#invali_commentadciol_mssg').text("Debe Ingresar un comentario");
    }else{
        puedo = 0;
        $('#invali_commentadciol_mssg').text(null);
    }

    if(puedo == 0){

        $.ajax({
            url: $DOCUMENTO_URL_HTTP + "/application/system/agenda/controller/agenda_controller.php",
            type:'POST',
            data:{'ajaxSend': 'ajaxSend', 'accion': 'UpdateComentarioAdicional', 'iddetcita': iddetcita ,'commentAdicional': $('#comment_adicional').val() },
            dataType:'json',
            async:false,
            success:function(resp) {

                if(resp.error == ''){
                    var table = $('#tableAgenda').DataTable();
                    table.ajax.reload(null, false);
                    $('#modal_coment_adicional').modal('hide');
                }else {
                    notificacion(resp.error , 'error');
                }
            }

        });

    }
}

$('#pacientes_habilitados , #pacientes_desabilitados').change(function() {

    var dataPacientes = [];

    var url = $DOCUMENTO_URL_HTTP + "/application/system/agenda/controller/agenda_controller.php";

    var pamarts = { 'accion':'pacientes_activodesact', 'ajaxSend':'ajaxSend', 'habilitado': $('#pacientes_habilitados').prop('checked') , 'desabilitado':$('#pacientes_desabilitados').prop('checked') };

    var puede = 0;

    if( $('#pacientes_habilitados').prop('checked') ){
        puede++;
    }
    if( $('#pacientes_desabilitados').prop('checked') ){
        puede++;
    }


    if( puede > 0){

        var option = "";
        $.get(url , pamarts ,  function (data) {

            dataPacientes = $.parseJSON(data);

            // $('#buscarxPaciente').empty();

            option += '<option value=""></option>';
            $.each(dataPacientes, function(i, item) {
                console.log(item);
                option += '<option value="'+item.id+'">'+item.text+'</option>';
            });

            $('.buscarxPaciente').html( option );

            $('#buscarxPaciente').select2({
                placeholder:'buscar pacientes' ,
                // language:'es'
            });

        });

    }else{

        $('#buscarxPaciente').empty();
    }

});


//APLICAR FILTRO DE BUSQUEDA O LIMPIAR
$(".aplicar").click(function() {
    filtrarAgenda();
});

//MOSTRAR CITAS ELIMINADAS O CANCELADAS
$('#listcitasCanceladasEliminadas').change(function(){
    filtrarAgenda();
});

$(".limpiar").click(function() {
    limpiarInpust();
    filtrarAgenda();
});


//SELECCIONAR TODOS LOS CHECKEDS DIARIA
$('#checkeAllCitas').change(function() {

    if($(this).is(':checked') == true)
    {
        $('.checked_detalleCitas').prop('checked', true);
    }else{
        $('.checked_detalleCitas').prop('checked', false);
    }

});

function limpiarInpust(){
    $("#buscarxPaciente").val(null).trigger('change');
    $("#filtro_doctor").val(null).trigger('change');
    $("#filtroEstados").val(null).trigger('change');
    $("#n_citasPacientes").val(null);
}

var ImprimirCitasAgendadas = function(){
    if( $("[name='checkedCitas']:checked").length == 0 )
        notificacion('Debe selecionar al menos una o varias Citas Agendadas', 'question');
    else{
        var checked = [];
        $("[name='checkedCitas']:checked").each(function(i, item) {
            checked.push($(item).prop('dataset').idcitadet);
        });

        if(!ModulePermission(2,1)){
            notificacion('Ud. No tiene permiso para Consultar', 'question');
            return false;
        }else{

            if(checked.length>0){
                var urlprint = $DOCUMENTO_URL_HTTP + '/application/system/agenda/export/export_pdf_citas_agendadas.php?idagend='+checked.toString();
                window.open(urlprint, '_blank');
            }
        }

    }
};

$(document).ready(function() {

    $('.filtroFecha').daterangepicker({

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

    loadtableAgenda();

    NOTIFICACION_CITAS_NUMEROS();

});

$('#modal_coment_adicional').on('show.bs.modal', function() {
    $("#comment_adicional").val(null);
});

window.onload =  boxloading($boxContent, true);

$(window).on("load", function() {
    boxloading($boxContent, true, 1000);

    $('.filtrar_doctor').select2({
        placeholder: 'Seleccionar un doctor',
        // allowClear:true,
        language:'es',
    });
    $('#pacienteCita').select2({
        placeholder: 'Pacientes',
        // allowClear: true,
        language:'es'
    });
    $('.filtrar_estados').select2({
        placeholder:'Seleccione estados cita',
        // allowClear:true,
        language:'es'
    });
    $('#buscarxPaciente').select2({
        placeholder:'buscar pacientes',
        // allowClear:true,
        language:'es',
    });


});
