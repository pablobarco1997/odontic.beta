
$formatoIndexCitas = 0;
$pushFechas = 0;

$('#addCloneCitas').on('click', function() {

    var puedoClonar = 0;

    $('.detalle_citas').each(function(i, item){

        var padre           = $(this);
        var especialidad    = padre.find('.opcionEspecialidad');
        var doctor          = padre.find('.opcionOdont');
        var duracion        = padre.find('.duracion');
        var fecheIni        = padre.find('.fechaIni');
        var hora            = padre.find('.horaCita');

        if(especialidad.find(':selected').val() == "")
        { puedoClonar++; }
        if(doctor.find(':selected').val() == "")
        { puedoClonar++; }
        if(duracion.find(':selected').val() == "")
        { puedoClonar++; }
        if(hora.find(':selected').val() == "")
        { puedoClonar++; }

    });

    if( puedoClonar > 0)
    {
        notificacion('Debe seleccionar antes de agregar', 'error');

    }else{

        $formatoIndexCitas++;

        var template = $("#template-index");
            var clone = template
                .clone()
                .attr('id', 'detalle-citas-index-'+$formatoIndexCitas)
                .addClass('detalle-citas-index-'+$formatoIndexCitas+'  detalle_citas')
                .attr('data-id', $formatoIndexCitas)
                .removeClass('hide template-index')
                .insertAfter(template);

            clone
                .find('#clone-especialidad').addClass('opcionEspecialidad').end()
                .find('#clone-odont').addClass('opcionOdont').end()
                .find('#clone-duraccion').addClass('duracion').end()
                .find('#clone-fecha').addClass('fechaIni').end()
                .find('#clone-hora').addClass('horaCita').end();

            clone
                .find('#clone-especialidad').attr('name', 'especialida['+$formatoIndexCitas+'].det').end()
                .find('#clone-odont').attr('name', 'odont['+$formatoIndexCitas+'].det').end()
                .find('#clone-duraccion').attr('name', 'duraccion['+$formatoIndexCitas+'].det').end()
                .find('#clone-fecha').attr('name', 'fecha['+$formatoIndexCitas+'].det').end()
                .find('#clone-hora').attr('name', 'hora['+$formatoIndexCitas+'].det').end()
                .find('#clone-eliminarow').attr('name', 'eliminrow['+$formatoIndexCitas+'].det').end();


                $('[name="especialida['+$formatoIndexCitas+'].det"]').select2({
                    placeholder:"Seleccione una opción",
                    allowClear: true,
                    language:"es"
                });
                $('[name="odont['+$formatoIndexCitas+'].det"]').select2({
                    placeholder:"Seleccione una opción",
                    allowClear: true,
                    language:"es"
                });
                $('[name="duraccion['+$formatoIndexCitas+'].det"]').select2({
                    placeholder:"Seleccione una opción",
                    allowClear: true,
                    language:"es"
                });
                $('[name="hora['+$formatoIndexCitas+'].det"]').select2({
                    placeholder:"Seleccione una opción",
                    allowClear: true,
                    language:"es"
                });

                datepickerCloneOrigin( $('[name="fecha['+$formatoIndexCitas+'].det"]') );

                $('[name="eliminrow['+$formatoIndexCitas+'].det"]').click(function() {

                    var row = $(this).parents('.detalle_citas');
                    row.remove();

                });

                $('[name="fecha['+$formatoIndexCitas+'].det"]').change(function() {

                    var numero    =  $(this).parents('.detalle_citas').data('id');
                    invalic_date_dateif($(this).val(), $(this), numero );

                });

                $('[name="fecha['+$formatoIndexCitas+'].det"]').trigger('change');

    }


});




$('#masCitasPacient').on('click', function() {

    if( $('#addCloneCitas').data('active') == 'activado'){
        notificacion('La funcionalidad "Agregar más citas esta Activado" ', 'question');
    }else{

        notificacion('Solo puede ingresar más citas a este paciente. Si las citas tienen fechas con diferentes días', 'warning');

        $('.swal2-confirm').click(function() {

            $('#addCloneCitas').removeClass('disabled').attr('disabled', false).attr('data-active', 'activado');

        });
    }

});

//'valida los dias que no concuerden - esta funcion solo esta disponible para la la funcionalidad => más citas
$('.fechaIni').change(function() {

    var estaFecha =  $(this).val();
    var numero    =  $(this).parents('.detalle_citas').data('id');
    invalic_date_dateif(estaFecha, $(this), numero);
    // alert(estaFecha);

});

function invalic_date_dateif(fecha_insertada, inputfecha, unoMismo)
{

    var fecha_repetida = 0;
    var fecha_invalic  = 0;


    // alert(unoMismo);
    for (var i=0; i <= ($formatoIndexCitas + 1) ; i++)
    {
        if(i != unoMismo)
        {
            if($('.detalle-citas-index-'+i).length > 0)
            {

                var padre = $('.detalle-citas-index-'+i);

                var especialidad    = padre.find('.opcionEspecialidad');
                var doctor          = padre.find('.opcionOdont');
                var duracion        = padre.find('.duracion');
                var fecheIni2       = padre.find('.fechaIni');
                var hora            = padre.find('.horaCita');
                var msgerror        = padre.find('.msg-error');

                // alert($.trim(fecha_insertada)+ '=='+ $.trim(fecheIni2.val()));
                if( $.trim(fecha_insertada) == $.trim(fecheIni2.val()) ) //Si la fecha son las misma entonces hay un error
                {
                    fecha_repetida++;
                }

            }
        }

    }

    var Meses = [ '01', '02', '03', '04' , '05', '06', '07', '08', '09', '10', '11', '12' ];

    if(fecha_insertada != ''){

        var fechaValidar = fecha_insertada.replace('/', '-').replace('/','-');
        var fechaActual  = '';

        if(  fechaValidar < fechaActual  ){

        }
        // console.log( date + '<'  + fechaValidar);

    }

    // alert(fecha_repetida);
    var subpadre  = inputfecha.parents('.date2');
    var msg_error = subpadre.find('.msg-error');

    console.log(subpadre);
    if( fecha_repetida > 0) //errores
    {
        var Dom_error = "No puede ingresar una fecha ya ingresada";
        msg_error.text(Dom_error);
    }

    if(msg_error.text() != "")
    {
        setTimeout(function(){
            msg_error.text( null )
        }, 4000)
    }

}

// obtener los datos de la citas agregadas   ---------------------------------------------------------------------------
function getdatoscitas()
{

    var  ArrayObtenerCab = {
        'fk_paciente' : $('#agndar_paciente').find('option:selected').val(),
        'comment'     : $('#info-adicional').val(),
        'detalle'     : [],
    };

    var ArrayObtenerDet = [];

    ArrayObtenerDet.push({
        'especialidad'  : $('[name="especialida[0].det"]').find(':selected').val(),
        'doctor'        : $('[name="odont[0].det"]').find(':selected').val(),
        'recursos'      : 0,
        'duraccion'     : $('[name="duraccion[0].det"]').find(':selected').val(),
        'fechacita'     : $('[name="fecha[0].det"]').val(),
        'hora'          : $('[name="hora[0].det"]').find(':selected').val()
    });

    ArrayObtenerCab['detalle'] = ArrayObtenerDet;

    return ArrayObtenerCab;
}

function invalic_puedoGuardar()
{

        var validFechaRepetidas = [];
        var numFechas           = 0;

        var puedoGuardar        = 0;
        var citas_MismaFechas   = 0;
        var FechaNow            = 0;

        /**Valid campos Obligatorios*/
        var CountValid          = 0;

        var especialidad    = $('.opcionEspecialidad');
        var doctor          = $('.opcionOdont');
        var duracion        = $('.duracion');
        var fecheIni        = $('.fechaIni');
        var hora            = $('.horaCita');


            if(especialidad.find(':selected').val() == "")
            { puedoGuardar++; CountValid++; }
            if(doctor.find(':selected').val() == "")
            { puedoGuardar++; CountValid++;}
            if(duracion.find(':selected').val() == "")
            { puedoGuardar++; CountValid++; }
            if(hora.find(':selected').val() == "")
            { puedoGuardar++; CountValid++;}

            var sepuede       = Consultar_CitasHorus_( fecheIni.val() ,hora.find(':selected').val(),duracion.find(':selected').val(),doctor.find(':selected').val());
            var FechaMenorNow = ConsultarDateNow( fecheIni.val(), hora.find(':selected').val() );

            if(sepuede == false){
                citas_MismaFechas++;
            }

            if(FechaMenorNow == false){
                FechaNow++;
            }


            // validFechaRepetidas.push(fecheIni.val());
            // numFechas++;


        //Fecha menor a la actual
        if(FechaNow>0){
            puedoGuardar++;
        }

        //Campos Obligatorios
        if(CountValid > 0){
            notificacion('Campos Obligatorios', 'error');
            puedoGuardar++;
        }

        // Elimino las fechas repetidas de la matriz
        // var lengthFechas = $.unique(validFechaRepetidas);

        // if(lengthFechas.length != numFechas){
        //     notificacion('Se encontraron fechas repetidas, compruebe la información agregada', 'error');
        //     puedoGuardar++;
        // }

        if(citas_MismaFechas > 0){
            puedoGuardar++;
            notificacion('Ya se encuentra agendada una cita con esta fecha y hora, asignados a un Odontólog@ específico compruebe la información antes de agregar', 'error');
        }

        if($('#agndar_paciente').find(':selected').val()==''){
            puedoGuardar++;
            $('#agndar_paciente').focus();
            notificacion('Selecione un paciente','error');
        }

        // alert(puedoGuardar);
        return puedoGuardar;
}

function ConsultarDateNow( dateadd, hour ) {

    var valid = 0;

    if(dateadd=="" || hour=="")
        return false;


    var form = new FormData();
    form.append("accion", "validDateNow");
    form.append("ajaxSend", "ajaxSend");
    form.append("dateadd", dateadd);
    form.append("hour", hour);

    $.ajax({
        url: $DOCUMENTO_URL_HTTP + "/application/system/agenda/controller/agenda_controller.php",
        type:"POST",
        data:form,
        dataType:"json",
        contentType:false,
        processData:false,
        async:false,
        success:function(resp) {
            if(resp['error'] != ''){
                notificacion(resp['error'], 'error');
                valid++;
            }else{
                valid = 0;
            }
        }
    });

    if(valid==0)
        return true;
    else
        return false;

}

function datepickerCloneOrigin(html){

    var Dateadd = new Date();

    html.daterangepicker({
        drops: 'up',
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
        pickerPosition: "bottom-left"
    });

}

function Consultar_CitasHorus_(fecha, hora, duracion, fk_doc)
{

    var respuesta = true;

    var parametros = {
        "ajaxSend":"ajaxSend",
        "accion":"validacionFechasCitas",
        "fecha" : fecha,
        "hora"  : hora,
        "duracion" : duracion,
        "fk_doc" : fk_doc
    };

    $.ajax({
        url: $DOCUMENTO_URL_HTTP + "/application/system/agenda/controller/agenda_controller.php",
        type:'POST',
        data:parametros,
        dataType:'json',
        async:false,
        success: function(res) {
            respuesta = res['respuesta'];
        }

    });

    return respuesta;

}


//GUARDAR CITAS
$('#nuevoGuardarCitas').click(function(){

    if(!ModulePermission(2,2)){
        notificacion('ud. no tiene permiso para Agendar', 'error');
        return false;
    }

    $("#nuevoGuardarCitas").addClass("disabled_link3");

    boxloading($boxContent,true);

    var datos_citas = getdatoscitas();

    // if( $('.detalle_citas').length == 0) {
    //     notificacion("No se detecto detalles asignados", "error");
    //     boxloading($boxContent,false,1000);
    //     $("#nuevoGuardarCitas").removeClass("disabled_link3");
    // }

    if(invalic_puedoGuardar() > 0){
        boxloading($boxContent,false,1000);
        $("#nuevoGuardarCitas").removeClass("disabled_link3");
    }else{

        var paramters = {
            'ajaxSend': 'ajaxSend',
            'accion':'create_cita_paciente',
            'datos': datos_citas
        };

        $.ajax({
            url: $DOCUMENTO_URL_HTTP + "/application/system/agenda/controller/agenda_controller.php",
            type:'POST',
            data:paramters,
            dataType:'json',
            error:function(xhr, status) {
                if(xhr['status']=='200'){
                    boxloading($boxContent,false,1000);
                }else{
                    if(xhr['status']=='404'){
                        notificacion("Ocurrió un error con la <b>solicitud Agendar citas</b> <br> <b>xhr: "+xhr['status']+" <br> Consulte con Soporte </b>");
                    }
                    boxloading($boxContent,false,1000);
                }
            },
            complete:function(xhr, status) {

                if(xhr['status']=='200'){
                    boxloading($boxContent,false,1000);
                }else{
                    if(xhr['status']=='404'){
                        notificacion("Ocurrió un error con la <b>solicitud Agendar citas</b> <br> <b>xhr: "+xhr['status']+" <br> Consulte con Soporte </b>");
                    }
                    boxloading($boxContent,false,1000);
                }
            },
            success: function(respuesta) {

                if(respuesta.error == "")
                {
                    notificacion('Información Actualizada', 'success');
                    boxloading($boxContent,false,1000);
                    setTimeout(()=>{                    window.location = $DOCUMENTO_URL_HTTP + "/application/system/agenda/index.php?view=principal&list=diaria";}, 1500);
                }else{

                    notificacion(respuesta.error , 'error');
                    $(this).removeClass('disabled_link3');
                }

            }
        });

    }


});

$(document).ready(function() {

    datepickerCloneOrigin( $('[name="fecha[0].det"]') );

    $('#agndar_paciente').select2({
        placeholder:'Pacientes',
        allowClear: true ,
        language: 'es'
    });
    $('[name="especialida[0].det"]').select2({
        placeholder:"Seleccione una opción",
        allowClear: true,
        language:"es"
    });
    $('[name="odont[0].det"]').select2({
        placeholder:"Seleccione una opción",
        allowClear: true,
        language:"es"
    });
    $('[name="duraccion[0].det"]').select2({
        placeholder:"Seleccione una opción",
        allowClear: true,
        language:"es"
    });

    $('[name="hora[0].det"]').select2({
        placeholder:"Seleccione una opción",
        allowClear: true,
        language:"es"
    }).on('change', function() {
        ConsultarDateNow( $('[name="fecha[0].det"]').val() , $('[name="hora[0].det"]').find(':selected').val() );
    });

    $('[name="fecha[0].det"]').on('change', function() {
        ConsultarDateNow( $('[name="fecha[0].det"]').val() , $('[name="hora[0].det"]').find(':selected').val() );
    });

    $('[name="eliminrow[0].det"]').click(function() {
        var row = $(this).parents('.detalle_citas');
        row.remove();
    });

});

window.onload =  boxloading($boxContent, true);

//documento cargado
$(window).on('load', function() {
    boxloading($boxContent,true,1500);
});