
$(window).on('load', function () {

    // setTimeout(function () {
    //     $(document)
    //         .ajaxStart(function () {
    //             var ElemmentoContentload = $("#tableAgenda");
    //             boxTableLoad(ElemmentoContentload, true);
    //         })
    //         .ajaxStop(function () {
    //             var ElemmentoContentload = $("#tableAgenda");
    //             boxTableLoad(ElemmentoContentload, false);
    //         });
    // },1500);

});


function loadtableAgenda() {

     var ElemmentoContentload = $("#tableAgenda");

     var table = $('#tableAgenda').DataTable({
        searching: false,
        "ordering":false,
        "serverSide": true,
        // responsive: true,
        destroy:true,
        scrollX: false,
        // scrollY: 500,
        lengthChange: false,
        fixedHeader: true,
        paging:true,
        processing: true,
        lengthMenu:[ 5 ],
        "ajax":{
            "url": $DOCUMENTO_URL_HTTP + "/application/system/agenda/controller/agenda_controller.php",
            "type":'POST',
            "data": {
                'ajaxSend'             : 'ajaxSend',
                'accion'               : 'listCitas',
                'doctor'               : $("#filtro_doctor").find(':selected').val(),
                'estados'              : $("#filtroEstados").find(':selected').val(),
                'fecha'                : $('.filtroFecha').val(),
                'eliminada_canceladas' : ( ( $('#listcitasCanceladasEliminadas').is(':checked')==true) ? "checked" : "") ,
                'buscar_xpaciente'     : $('#buscarxPaciente').find(':selected').val(),
                'search_ncita'         : $('#n_citasPacientes').val(),
            },
            "dataType":'json',
            "cache": false,
            "asycn": false,
            "beforeSend": function () {
                boxTableLoad(ElemmentoContentload, true);
                $("#refresh_agenda_list").find('i').addClass('btnSpinner');
            },
            "complete": function(xhr, status) {

                if(xhr.responseJSON.permiso!=""){
                    notificacion('Ud. No tiene permiso para consultar', 'error');
                }
                boxTableLoad(ElemmentoContentload, false);
                $("#refresh_agenda_list").find('i').removeClass('btnSpinner');
            }
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
     }).on( 'length.dt', function ( e, settings, len ) { // cambiar
         boxTableLoad(ElemmentoContentload, true);
     }).on( 'page.dt', function ( e, settings, len ) { // cambiar
         boxTableLoad(ElemmentoContentload, true);
     });
    // new $.fn.dataTable.FixedHeader( table );
    new $.fn.dataTable.FixedHeader( table,
        {
            // headerOffset: 50
        }
    );


}

function filtrarAgenda(validStatus="") {

    loadtableAgenda();
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
    $.get(url , parameters , function(data) {
        var datos = $.parseJSON(data);
        $("#numCitas").text( datos.result );
    });
}

// fa-refresh
function fa_refresh_agenda() {

    // if(!ModulePermission('Agenda', 'consultar')){
    //     notificacion('Ud. notiene permiso para consultar', 'error');
    //     return false;
    // }

    filtrarAgenda();
    Notify_odontic(1, false);
}


//Funciones Cambio de estados 
function  AplicarStatusAgendada(idestado, idcita, JQUERYdom, idpaciente) {

    var status = JQUERYdom.data('text');

    if(idestado==1){ //email

        var modalpadre = $('#notificar_email-modal');
        modalpadre.modal('show');

        setTimeout(()=>{
            notificacion('El sistema no se responsabiliza por correo electrónico mal ingresado', 'question'); }
        ,800);

        var parametrosEmail = [];
            parametrosEmail = {
                'idpaciente' : idpaciente,
                'idcita'     : idcita,
                'idestado'   : idestado,
            };
                 parametrosEmail = JSON.stringify(parametrosEmail); //se pasa a string
                    var encry64 = btoa(parametrosEmail); //se incryta en 64
                                // console.log(encry64);

        if(encry64 != ""){
            modalpadre.find('input[name=SendEmailData64]').val(encry64);
        }else{
            encry64 = "";
            modalpadre.find('input[name=SendEmailData64]').val(encry64);
        }

        if(encry64==""){
            notificacion('Ocurrió un error con los parámetros. Consulte con soporte Técnico', 'error');
        }

        $("#para_email").keyup();

        return false;
    }

    //otro estado
    if(idestado==12){//Nuevo Agendamiento

        FechaCitaCambio(idcita);
        return false;
    }

    UpdateEstadoCita(idestado,idcita,status);

}
//Envio de email de confirmación
$("button#enviarEmail").on("click", function () {

    var ElementoParent = $(this).parents("#notificar_email-modal");
          var response = ElementoParent.find('input[name=SendEmailData64]').val();

    if(ElementoParent.find('#para_email').val()==""){
            notificacion('Campos Obligatorio Email', 'warning');
        return false;
    }

    if(response != ""){

        response = atob(response);
            response = JSON.parse(response);
                notificaionEmail(response.idpaciente, response.idcita, response.idestado);

        // console.log(response);

    }else{
        notificacion('Ocurrió un error con los parámetros. Consulte con soporte Técnico', 'error');
        $("button#enviarEmail").addClass('disabled_link3');
    }


});


//actualiza el estado 
function UpdateEstadoCita(idestado, idcita, textEstado) //Actualizar Estados de las citas
{

    var parametros = {
        'ajaxSend': 'ajaxSend',
        'accion': 'EstadoslistCitas',
        'idestado':idestado,
        'idcita':idcita,
        'estadoText':textEstado
    };

    $.ajax({
        url: $DOCUMENTO_URL_HTTP + "/application/system/agenda/controller/agenda_controller.php",
        type:'POST',
        data: parametros ,
        dataType:'json',
        async: false,
        cache: false,
        success: function(resp){

            if(resp.error == "" && resp.errmsg == "") {
                var table =  $('#tableAgenda').DataTable();
                notificacion( 'Información Actualizada', 'success');
                table.ajax.reload( null, false );
                Notify_odontic(1,false); //actualizo la notificacion o numero de notificacion
            }else{
                if(resp.errmsg != ""){
                    notificacion( resp.errmsg , 'question');
                }
                if(resp.error  != ""){
                    notificacion( resp.error , 'question');
                }
            }
        }
    });
}


function notificaionEmail(idPaciente, idcita, idestado ){

    var error = '';
    var error_registrar_email_ = '';

    var boxMail = $("#notificar_email-modal").find(".modal-dialog");
    boxloading(boxMail, true);

    var programarEmail = {
        'confirmar'    : ($("#emailConfirmacion_programar").is(':checked')?1:0),
        'date_program' : $("#date_programa_email_confirm").val(),
    };

    setTimeout(function() {
        $.ajax({
            url: $DOCUMENTO_URL_HTTP + "/application/system/agenda/controller/agenda_controller.php",
            type:'POST',
            data:{
                'ajaxSend'          : 'ajaxSend',
                'accion'            : 'envio_email_notificacion',
                'idpaciente'        : idPaciente,
                'idcita'            : idcita,
                'asunto'            : $('#asunto_email').val(),
                'from'              : $('#de_email').val(),
                'to'                : $('#para_email').val(),
                'subject'           : ($('#titulo_email').val()).replace(/(["'])(.*?)\1/g,' '),
                'message'           : ($('#messge_email').val()).replace(/(["'])(.*?)\1/g,' '),
                'programar_email'   : JSON.stringify(programarEmail),
            },
            dataType:'json',
            async: false,
            cache: false,
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
                $('#notificar_email-modal').modal('hide');

                if(error == "" && error_registrar_email_ == "") {

                    $('#asunto_email').val();
                    $('#de_email').val();
                    $('#para_email').val();
                    $('#titulo_email').val();
                    $('#messge_email').val();

                    //si es diferente a email programado
                    if(!$("#emailConfirmacion_programar").is(':checked')){
                        setTimeout(()=>{
                            UpdateEstadoCita(idestado, idcita, '', '' );
                        },800);
                    }else{
                        setTimeout(()=>{
                            notificacion('Información Actualizada', 'success');
                        },800);
                        var table =  $('#tableAgenda').DataTable();
                        table.ajax.reload(null, false);
                    }
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
                notificacion('Plan de Tratamiento creado con completo. espere un momento para el redireccionamiento...', 'success');
            }else {
                if(resp.error != ''){
                    notificacion(resp.error , 'error');
                }else{
                    notificacion('Ocurrio un error con la Operación' , 'error');
                }
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

function  FechaCitaCambio(idcita) {

    $("#iddetCitas").val(null);
    $("#iddetCitas").attr('data-cita', '');
    $.ajax({
        url: $DOCUMENTO_URL_HTTP + "/application/system/agenda/controller/agenda_controller.php" ,
        type:"POST",
        data:{
            "ajaxSend"  :"ajaxSend",
            "accion"    :"fetch_cita_now",
            "idCita"    : idcita
        },
        dataType:"json",
        async:true,
        cache:false,
        beforeSend: function(){
            boxloading($boxContent, true);
            $("#iddetCitas").val(idcita);
        },
        complete: function (xhr, status) {
            boxloading($boxContent, false, 1000);
        },
        success:function (response) {

            $("#modalCambioFechaCitas").modal("show");
            $("#modalCambioFechaCitas").find("input, select").val(null).trigger("change");

            var fetch = response['fetch'];
            $("#iddetCitas").attr('data-cita', JSON.stringify(fetch));
        }
    });

}

function  FormValidReagendar() {

    var errores   = [];
    var fecha     = $("#reagendar_fecha_cita");
    var duracion  = $("#reagendar_duracion");
    var hora      = $("#reagendar_hora_cita");

    if(fecha==""){
        errores.push({text:'campo obligatorio', 'document':fecha});
    }if(duracion.find(':selected').val()==""){
        errores.push({text:'campo obligatorio', 'document':duracion});
    }if(hora.find(':selected').val()==""){
        errores.push({text:'campo obligatorio', 'document':hora});
    }

    if(errores.length>0){
        notificacion('Campos Obligatorios', 'question');
        return false;
    }
    else{
        return true;
    }
}

function reagendarCitas(Elemento) {


    if(!FormValidReagendar()){
        return false;
    }

    var Element   =  $(Elemento);
    var padre     =  Element.parents('#modalCambioFechaCitas');

    var parametrs =  [];

    if( padre.find('[name="iddetCitas"]').prop('dataset').cita != "" ){
        parametrs = JSON.parse(padre.find('[name="iddetCitas"]').prop('dataset').cita);
    }

    var idCita    =  parametrs.rowid; //id cita
    var fecha     =  padre.find("#reagendar_fecha_cita").val();
    var duracion  =  padre.find("#reagendar_duracion").find(":selected").val();
    var hora      =  padre.find("#reagendar_hora_cita").find(":selected").val();
    var fk_doc    =  parametrs.id_doc;

    // console.log(fk_doc);

    var result = false;
    $.ajax({
        url: $DOCUMENTO_URL_HTTP + "/application/system/agenda/controller/agenda_controller.php" ,
        type:"POST",
        data:{
            "ajaxSend"  :"ajaxSend",
            "accion"    :"reagendar_cita_paciente",
            "idCita"    : idCita,
            "fecha"     : fecha,
            "hora"      : hora,
            "duracion"  : duracion,
            "fk_doc"    : fk_doc
        },
        dataType:"json",
        async:true,
        cache:false,
        beforeSend: function(){
            boxloading($boxContent, true);
            button_loadding($(Elemento), true);
        }, complete: function (xhr, status) {
            boxloading($boxContent, false, 1000);
            button_loadding($(Elemento), false);
        },success:function (response) {
            // console.log(response);
            if(response.error!=""){
                setTimeout(function () {
                    notificacion(response.error, 'error');
                },500)
            }else{

                $("#modalCambioFechaCitas")
                    .modal("hide");

                if(response.error==""){
                    setTimeout(function () {
                        notificacion('Información Actualizada', 'success');
                    },500)
                }

                loadtableAgenda();
                result = true;
            }
        }
    });

    return result;

}

var ImprimirCitasAgendadas = function(filter=false, Element = false){

    var objectLoad = {
        onload:function () {
            boxloading($boxContent, true);
        },
        offload: function () {
            boxloading($boxContent, false, 1000);
        }
    };

    if(!ModulePermission("Agenda","consultar", objectLoad)){
        notificacion("Ud. no tiene permiso para Consultar", "error");
        return false;
    }

    //busqueda de
    if(filter==true){

        var odonto     = $("#filtro_doctor").select2('data').map(function (arr) {
            return arr.id;
        });
        var estados    = $("#filtroEstados").select2('data').map(function (arr) {
            return arr.id;
        });
        var bxpaciente = $("#buscarxPaciente").select2('data').map(function (arr) {
            return arr.id;
        });

        var fecha = $("#startDate").val();
        var n_citasPacientes = $("#n_citasPacientes").val();

        var parametros = "&fecha="+fecha;
        parametros += "&odontologo="+odonto.toString();
        parametros += "&estados="+estados.toString();
        parametros += "&pacientes="+bxpaciente.toString();
        parametros += "&n_cita="+n_citasPacientes;

        if($(Element).hasClass('Excel')){
            urlprint = $DOCUMENTO_URL_HTTP + '/application/system/agenda/export/export_excel_agenda.php?accion_exportar=pdf_filter'+parametros;
        }
        if($(Element).hasClass('PDF')){
            urlprint = $DOCUMENTO_URL_HTTP + '/application/system/agenda/export/export_pdf_citas_agendadas.php?accion_exportar=pdf_filter'+parametros;
        }

        // alert(urlprint);
        window.open(urlprint, '_blank');

        return true;
    }

    //busqueda por checked citas selecionadas
    if( $("[name='checkedCitas']:checked").length == 0 )
        notificacion('Debe selecionar al menos una o varias Citas Agendadas', 'question');
    else{
        var checked = [];
        $("[name='checkedCitas']:checked").each(function(i, item) {
            checked.push($(item).prop('dataset').idcitadet);
        });

        if(!ModulePermission("Agenda","consultar")){
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
        endDate: moment().endOf('month'),
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

    NOTIFICACION_CITAS_NUMEROS();

});

$('#modal_coment_adicional').on('show.bs.modal', function() {
    $("#comment_adicional").val(null);
});

//modal email confirmacion
$('#notificar_email-modal').on('show.bs.modal', function() {
    $("#messge_email").val(null);
    $("#emailConfirmacion_programar").prop('checked', false).trigger('change');
    $("#date_programa_email_confirm").val(null);
});



window.onload =  boxloading($boxContent, true);

$(window).on("load", function() {

    var Dateadd = new Date();

    boxloading($boxContent, true, 1000);

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

    // if(!ModulePermission('Agenda', 'consultar')){
    //     notificacion('Ud. notiene permiso para consultar', 'error');
    // }

    //buscar pacientes habilitados o desabilitados
    $('#buscarxPaciente').select2({
        placeholder: 'buscar pacientes' ,
        language: languageEs,
        minimumInputLength: 2,
        ajax:{
            url: $DOCUMENTO_URL_HTTP + "/application/system/agenda/controller/agenda_controller.php",
            dataType: "json",
            data: function (params) {
                var query = {
                    search: params.term,
                    ajaxSend:'ajaxSend',
                    accion:'pacientes_activodesact'
                };
                return query;
            },
            processResults: function (data) {
                return {
                    results: data.items
                };
            }
        }
    });


    $('[name="reagendar_fecha_cita"]').daterangepicker({
        minDate : new Date(Dateadd.getFullYear(), Dateadd.getMonth(), Dateadd.getDate()),
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
        singleDatePicker: true,
        showDropdowns: true,
        autoclose: true,
        // "drops": "button",
        pickerPosition: "bottom-left"
    });

    $(".reagendar_select").select2({
        placeholder: 'Seleccione una opción',
        allowClear:true,
        language: languageEs
    });

    loadtableAgenda();

});
