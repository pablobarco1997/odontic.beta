
//FUNCIONES

$boxContentNewPaciente = $("#boxprincipalNewPaciente");

function GuardarDatosPacientes()
{
    boxloading($boxContentNewPaciente,true);

    var puedoPasar = invalic_paciente();

    var nombre       = $('#nombre').val();
    var apellido     = $('#apellido').val();
    var rud_dni      = $('#rud_dni').val();
    var email        = $('#email').val();
    var convenio        = $('#convenio').find('option:selected').val();
    var n_interno       = $('#n_interno').val();
    var sexo            = $('#sexo').find('option:selected').val();
    var fech_nacimit    = $('#fech_nacimit').val();
    var ciudad       = $('#ciudad').val();
    var comuna       = $('#comuna').val();
    var direcc       = $('#direcc').val();
    var t_fijo       = $('#t_fijo').val();
    var t_movil       = $('#t_movil').val();
    var act_profec   = $('#act_profec').val();
    var empleado     = $('#empleado').val();
    var obsrv        = $('#obsrv').val();
    var apoderado        = $('#apoderado').val();
    var refer        = $('#refer').val();

    var datos_paciente = {
        'nombre'    : nombre,
        'apellido'    : apellido,
        'rud_dni'   : rud_dni,
        'email'     : email,
        'convenio'  : convenio,
        'n_interno' : n_interno,
        'sexo'      : sexo,
        'fech_nacimit': fech_nacimit,
        'ciudad'      : ciudad,
        'comuna'      : comuna,
        'direcc'      : direcc,
        't_fijo'      : t_fijo,
        't_movil'      : t_movil,
        'act_profec'  : act_profec,
        'empleado'    : empleado,
        'obsrv'       : obsrv,
        'apoderado'   : apoderado,
        'refer'       : refer,
    };

    var parametros = {
        'accion': 'nuew_paciente',
        'ajaxSend':'ajaxSend',
        'datos': datos_paciente,
    };

    // console.log(parametros);

    if(puedoPasar == true){
        $.ajax({
            url: $DOCUMENTO_URL_HTTP +'/application/system/pacientes/nuevo_paciente/controller/nuevo_pacit_controller.php',
            type:"POST",
            data: parametros,
            dataType: 'json',
            async: false,
            error:function(xhr, status){
                if(xhr['status']=='200'){
                    boxloading($boxContentNewPaciente,false,1000);
                }else{
                    if(xhr['status']=='404'){
                        notificacion("Ocurrió un error con la <b>solicitud Agendar citas</b> <br> <b>xhr: "+xhr['status']+" <br> Consulte con Soporte </b>");
                    }
                    boxloading($boxContentNewPaciente,false,1000);
                }
            },
            complete:function(xhr, status){
                if(xhr['status']=='200'){
                    boxloading($boxContentNewPaciente,false,1000);
                }else{
                    if(xhr['status']=='404'){
                        notificacion("Ocurrió un error con la <b>solicitud Agendar citas</b> <br> <b>xhr: "+xhr['status']+" <br> Consulte con Soporte </b>");
                    }
                    boxloading($boxContentNewPaciente,false,1000);
                }
            },
            success: function(resp)
            {
                if(resp.error == "exito")
                {
                    boxloading($boxContentNewPaciente,false,1000);
                    notificacion("Información Actualizada", "success");
                    // location.reload(true);

                }else{
                    boxloading($boxContentNewPaciente,false,1000);
                    notificacion("Error, Ocurrió un error con la Operción", "error");
                }

                boxloading($boxContentNewPaciente,false,1000);
            }

        });
    }else{
        boxloading($boxContentNewPaciente,false,1000);
    }


    $(document).ajaxComplete(boxloading($boxContentNewPaciente,false));

}

function invalic_paciente(input = false)
{

    var cont = 0;

    if($('#nombre').val() == ''){
        cont++;
        $('#noti_nombre').text("ingrese el nombre del paciente");
    }else{
        $('#noti_nombre').text(null);
    }


    if($('#apellido').val() == ''){
        cont++;
        $('#noti_apellido').text("ingrese el apellido del paciente");
    }else{
        $('#noti_apellido').text(null);
    }

    if($('#sexo').find(':selected').val() == ''){
        cont++;
        $('#noti_sexo').text("ingrese el genero del paciente");
    }else{
        $('#noti_sexo').text(null);
    }

    if($('#fech_nacimit').find(':selected').val() == ''){
        cont++;
        $('#noti_date_nacimiento').text("ingrese fecha de nacimiento del paciente");
    }else{
        $('#noti_date_nacimiento').text(null);
    }

    if($('#direcc').val() == ''){
        cont++;
        $('#noti_direccion').text("ingrese la dirección del paciente");
    }else{
        $('#noti_direccion').text(null);
    }

    //se comprueba el numero de cedula se esta repetido
    if($('#rud_dni').val()==""){
        $('#noti_ruddni').text('Campo obligatorio');
        cont++;
    }else{
        if(input==true){
            if( invalicrucCedula($('#rud_dni'), false) > 0 ){
                $('#noti_ruddni').text('Este numero se encuentra repetido');
                cont++;
            }
        }
        $('#noti_ruddni').text(null);
    }

    if( cont > 0){

        return false
    }else {

        return true
    }

}

function carga_subida_masiva_pacientes()
{
    $('#subida_masiva_pasiente').trigger('click');
}
$('#carga_masv_pasiente').click(function() {
    carga_subida_masiva_pacientes();
});

$('#subida_masiva_pasiente').change(function() {

    var inputFile = document.getElementById('subida_masiva_pasiente');
    console.log(inputFile.files[0]);

    var form = new FormData();

    form.append('ajaxSend', 'ajaxSend');
    form.append('accion', 'carga_masiva_pacientes');
    form.append('file', inputFile.files[0]);

    $.ajax({
        url: $DOCUMENTO_URL_HTTP +'/application/system/pacientes/nuevo_paciente/controller/nuevo_pacit_controller.php',
        type:"POST",
        data: form,
        dataType: 'json',
        async: false,
        contentType:false,
        processData: false,
        success: function(resp)
        {
            if(resp.errores.error == '' && resp.req == ''){
                notificacion('información Actualizada', 'success');
                // location.reload();
            }else{

                notificacion( resp.errores.error + '<br>' + resp.req , 'error');
            }
        }
    });

});

//eventos
$('#guardar').on('click', function(){
    GuardarDatosPacientes();
});

function invalicrucCedula(el, val)
{
    //solo numeros
    if(val==true){
        el.value = el.value.replace(/\D/g, '');
    }

    var puedepasar   = 0;
    var input_rucced =  $(el).val();
    var msg_error    =  $('#noti_ruddni');

    var url = $DOCUMENTO_URL_HTTP +'/application/system/pacientes/nuevo_paciente/controller/nuevo_pacit_controller.php';
    var parametros = { 'ajaxSend':'ajaxSend', 'accion':'validarCedulaRuc', 'ruc_ced': input_rucced };

    $.get( url , parametros , function(data) {
            var rs = $.parseJSON(data);
            if(rs.error != '')
            {
                msg_error.text('Este numero se encuentra repetido');
                $('#guardar').addClass('disabled_link3');
                puedepasar++;
            }else{
                // msg_error.text(null);
                $('#guardar').removeClass('disabled_link3');
                puedepasar=0;
            }
    });

    return puedepasar;
}

function invalicEmailText(el , val)
{
    if(val==true){

        var email = $(el).val();
        const re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;

        if( !re.test(email) )
        {
            $('#noti_email').text('Email incorrecto');
        } else {
            $('#noti_email').text(null);
        }

    }
}

$(document).ready(function() {

    $('#fech_nacimit').daterangepicker({
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

});

window.onload =  boxloading($boxContentNewPaciente, true);

$(window).on("load", function() {
    boxloading($boxContentNewPaciente, false, 1000);
});