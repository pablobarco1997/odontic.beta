
//FUNCIONES

$boxContentNewPaciente = $("#boxprincipalNewPaciente");

function GuardarDatosPacientes()
{

    if(!ModulePermission("Nuevo Paciente","agregar")){
        notificacion('Ud. Notiene permisos para crear', 'error');
        return false;
    }

    if(!FormvalidPacienteAdd()){
        return false;
    }

    boxloading($boxContentNewPaciente,true);

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
        'nombre'       : nombre,
        'apellido'     : apellido,
        'rud_dni'      : rud_dni,
        'email'        : email,
        'convenio'     : convenio,
        'n_interno'    : n_interno,
        'sexo'         : sexo,
        'fech_nacimit' : fech_nacimit,
        'ciudad'       : ciudad,
        'comuna'       : comuna,
        'direcc'       : direcc,
        't_fijo'       : t_fijo,
        't_movil'      : t_movil,
        'act_profec'   : act_profec,
        'empleado'     : empleado,
        'obsrv'        : obsrv,
        'apoderado'    : apoderado,
        'refer'        : refer,
    };

    var parametros = {
        'accion': 'nuew_paciente',
        'ajaxSend':'ajaxSend',
        'datos': datos_paciente,
    };

    $.ajax({
        url: $DOCUMENTO_URL_HTTP +'/application/system/pacientes/nuevo_paciente/controller/nuevo_pacit_controller.php',
        type:"POST",
        data: parametros,
        dataType: 'json',
        cache:false,
        async: true,
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
        success: function(resp) {
            if(resp.error == "exito") {
                boxloading($boxContentNewPaciente,false,1000);
                notificacion("Información Actualizada", "success");
                window.location = $DOCUMENTO_URL_HTTP + '/application/system/pacientes/directorio_paciente/index.php?view=directorio';
                // location.reload(true);

            }else{
                boxloading($boxContentNewPaciente,false,1000);
                notificacion("Error, Ocurrió un error con la Operción", "error");
            }

            boxloading($boxContentNewPaciente,false,1000);
        }

    });

}

var FormvalidPacienteAdd = function(input = false){

    var valid = false;
    var ErroresData = [];

    const errmail = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/

    var nom      = $("#nombre");
    var ape      = $("#apellido");
    var ci       = $("#rud_dni");
    // var ciud     = $("#ciudad");
    var direc    = $("#direcc");
    var t_movil  = $("#t_movil");
    var email    = $("#email");

    if(email.val()!=""){
        if(!errmail.test(email.val())){
            ErroresData.push({
                'document' : email,
                'text' : 'Email Icorrecto'
            });
        }
    }if(nom.val()==""){
        ErroresData.push({
            'document' : nom,
            'text' : 'Campo Obligatorio'
        });
    }if(ape.val()==""){
        ErroresData.push({
            'document' : ape,
            'text' : 'Campo Obligatorio'
        });
    }if(ci.val()==""){
        ErroresData.push({
            'document' : ci,
            'text' : 'Campo Obligatorio'
        });
    }else{
        ci.val(ci.val().replace(/[^0-9]/g, ''));
    }
    // if(ciud.val()==""){
    //     ErroresData.push({
    //         'document' : ciud,
    //         'text' : 'Campo Obligatorio'
    //     });
    // }
    if(direc.val()==""){
        ErroresData.push({
            'document' : direc,
            'text' : 'Campo Obligatorio'
        });
    }
    if(t_movil.val()==""){
        ErroresData.push({
            'document' : t_movil,
            'text' : 'Campo Obligatorio'
        });
    }else{
        if( ((/^[1-9]\d*$/).test(t_movil.val())) == false){ //si el primer caracter es 0
            t_movil.val(t_movil.val().replace(/^[0-9]\d*$/, '')); //renplaso el primer 0 de la primera posicion
        }
        if(t_movil.val().length!=9){
            ErroresData.push({
                'document' : t_movil,
                'text' : 'numero invalido'
            });
        }
    }

    $(".msg_error_add_paciente").remove();
    if(ErroresData.length>0){
        for (var i=0; i<=ErroresData.length-1;i++ ){
            var documento = ErroresData[i]['document'];
            var text      = ErroresData[i]['text'];
            var Msg       = document.createElement('small');
                            $(Msg).addClass('msg_error_add_paciente').css('color', 'red');
            if(documento[0].localName=='select'){
                $(Msg).insertAfter(documento.parent().find('span:eq(0)')).text(text);
            }else{
                $(Msg).insertAfter(documento).text(text);
            }
        }
        valid = false;
    }else{
        valid = true;
    }
    return valid;


};

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