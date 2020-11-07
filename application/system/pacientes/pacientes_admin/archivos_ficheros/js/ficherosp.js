
var FormValidationFicheroUpload = function() {

    var MsgError = [];

    var Titulo         = $("#ficheroTitulo");
    var odontolo       = $("#doctor");
    var Observacion    = $("#ficheroobservacion");

    if(Titulo.val() == ''){
        MsgError.push({
            'document': Titulo ,
            'msg_err': 'campo obligatorio',
        });
    }
    if(odontolo.find(':selected').val() == ''){
        MsgError.push({
            'document': odontolo,
            'msg_err': 'campo obligatorio',
        });
    }

    $('.msg_err_fichero').remove();

    for (var i = 0; i <= MsgError.length -1; i++)
    {
        var DomMsg  = document.createElement("small");
        var Element = MsgError[i];

        $(DomMsg)
            .text(Element['msg_err'])
            .css('color','red')
            .attr('class','msg_err_fichero');

        if(Element['document'].attr('name') == 'doctor'){
            $(DomMsg).insertAfter(Element['document'].next("span"));
        }else{
            $(DomMsg).insertAfter(Element['document']);
        }
    }


    if(MsgError.length>0)
        return false;
    else
        return true;

};


//Table
function LoadPacientesFicheros()
{
    $('#table_ficheros_paciente').DataTable({

        searching: true,
        ordering:false,
        destroy:true,
        // scrollX: true,

        ajax:{
            url: $DOCUMENTO_URL_HTTP + '/application/system/pacientes/pacientes_admin/controller/controller_adm_paciente.php',
            type:'POST',
            data:{'ajaxSend':'ajaxSend', 'accion':'Ficheros_pacientes', 'idpaciente': $id_paciente} ,
            dataType:'json',
        },

        language: {
            "sProcessing": "Procesando...",
            "sLengthMenu": "Mostrar _MENU_ registros",
            "sZeroRecords": "No se encontraron resultados",
            "sEmptyTable": "Ningún dato disponible en esta tabla",
            "sInfo": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
            "sInfoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
            "sInfoFiltered": "(filtrado de un total de _MAX_ registros)",
            "sInfoPostFix": "",
            "sSearch": "Buscar:",
            "sUrl": "",
            "sInfoThousands": ",",
            "sLoadingRecords": "Cargando...",
            "oPaginate": {
                "sFirst": "Primero",
                "sLast": "Último",
                "sNext": "Siguiente",
                "sPrevious": "Anterior"
            },
            "oAria": {
                "sSortAscending": ": Activar para ordenar la columna de manera ascendente",
                "sSortDescending": ": Activar para ordenar la columna de manera descendente"
            }
        },

    });
}


//VALIDACION
function InvalicFicheros(){

    var puedo = 0;

    if( $('#ficheroTitulo').val() == "" ){
        puedo++;
        $('#ficheroTitulo').addClass('INVALIC_ERROR');
    }else{
        $('#ficheroTitulo').removeClass('INVALIC_ERROR');
    }

    if( $('#doctor').find('option:selected').val() == ""){
        puedo++;
        $('#doctor').addClass('INVALIC_ERROR');
    }else{
        $('#doctor').removeClass('INVALIC_ERROR');
    }

    if( $('#ficheroobservacion').val() == ""){
        puedo++;
        $('#ficheroobservacion').addClass('INVALIC_ERROR');
    }else {
        $('#ficheroobservacion').removeClass('INVALIC_ERROR');
    }

    if( $('#file-5').val() == ''){
        notificacion('No está seleccionado ningún archivo', 'error');
    }

    return puedo;
}

function ficherosInputsClear()
{
    $('#ficheroTitulo').val(null);
    $('#doctor').val(null).trigger('change');
    $('#ficheroobservacion').val(null);
    $('#file-5').val(null);
    $('#iconviewblock').attr('src', $DOCUMENTO_URL_HTTP + '/logos_icon/logo_default/file.png');
}

$('#formFicheros').on('submit', function(e) {

    boxloading($boxContentViewAdminPaciente ,true);

    var $puedo = true;

    // if(InvalicFicheros() == 0){
    //     $puedo = true;
    // }
    if($('#file-5').val() == ''){
        $puedo = false;
        notificacion("Debe selecionar un Archivo", "error");
    }

    if(FormValidationFicheroUpload() == false){
        boxloading($boxContentViewAdminPaciente ,false,1000);
        $puedo = false;
        return false;
    }

    e.preventDefault();

    var formdata = new FormData($(this)[0]);

    formdata.append('idpaciente', $id_paciente );
    formdata.append('accion', 'FicheroPacienteInsert');
    formdata.append('ajaxSend', 'ajaxSend');

    // console.log(formdata);

    if($puedo == true){

        $.ajax({
            url:  $DOCUMENTO_URL_HTTP +'/application/system/pacientes/pacientes_admin/controller/controller_adm_paciente.php',
            type: 'POST',
            data: formdata,
            dataType:'json',
            contentType: false,
            processData: false,
            error:function(xhr, status) {
                if(xhr['status']=='200'){
                    boxloading($boxContentViewAdminPaciente,false,1000);
                }else{
                    if(xhr['status']=='404'){
                        notificacion("Ocurrió un error con la <b>UPLOAD FICHEROS</b> <br> <b>xhr: "+xhr['status']+" <br> Consulte con Soporte </b>");
                    }
                    boxloading($boxContentViewAdminPaciente,false,1000);
                }
            },
            complete:function(xhr, status) {

                if(xhr['status']=='200'){
                    boxloading($boxContentViewAdminPaciente,false,1000);
                }else{
                    if(xhr['status']=='404'){
                        notificacion("Ocurrió un error con la <b>UPLOAD FICHEROS</b> <br> <b>xhr: "+xhr['status']+" <br> Consulte con Soporte </b>");
                    }
                    boxloading($boxContentViewAdminPaciente,false,1000);
                }
            },
            success: function(resp){
                if(resp.error == '') {
                    notificacion('Información Actualizada', 'success');
                    var table = $('#table_ficheros_paciente').DataTable();
                    table.ajax.reload();
                    ficherosInputsClear();
                    setTimeout(function() { location.reload(true);}, 1500);

                }else{
                    notificacion( resp.error , 'error');
                }

            }

        });
    }else{
        boxloading($boxContentViewAdminPaciente ,false,1000);
    }


});

function del_ficheropaciente(id)
{
    $.ajax({
        url:  $DOCUMENTO_URL_HTTP + '/application/system/pacientes/pacientes_admin/controller/controller_adm_paciente.php',
        type: 'POST',
        data: { 'accion':'delete_fichero_paciente', 'ajaxSend':'ajaxSend', 'id': id },
        dataType:'json',
        success:function(resp) {
            if( resp.error == ''){

                notificacion('Información Actualizada', 'success');
                var table = $('#table_ficheros_paciente').DataTable();
                table.ajax.reload();
            }else{
                notificacion(resp.error , 'error');

            }
        }
    });
}


$(document).ready(function() {
    LoadPacientesFicheros();
});

//window onload
window.onload = boxloading($boxContentViewAdminPaciente ,true);
//window load
window.addEventListener("load", function() {

    $('#doctor').select2({
        allowClear: true,
        placeholder: 'Seleccionar doctor',
    });

    boxloading($boxContentViewAdminPaciente ,false, 1000);
});