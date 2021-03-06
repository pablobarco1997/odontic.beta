
// LISTA DE ODONTOGRAMA
function  odontolist()
{
    var table = $('#odontPLant').DataTable({

        searching: true,
        ordering:false,
        destroy:true,

        ajax:{
            url: $DOCUMENTO_URL_HTTP + '/application/system/pacientes/pacientes_admin/controller/controller_adm_paciente.php',
            type:'POST',
            data:{'ajaxSend':'ajaxSend', 'accion':'list_odontograma', 'idpaciente': $id_paciente } ,
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

//CONSULTAR SECUENCIAL ODONTOGRAMA
function  concultarSecuencialOdontograma() {

    var sucuencial = 0;

    $.ajax({
        url: $DOCUMENTO_URL_HTTP +'/application/system/pacientes/pacientes_admin/controller/controller_adm_paciente.php',
        type:'POST',
        data: {'accion':'consultar_numero_odontograma','ajaxSend':'ajaxSend'},
        dataType:'json',
        async:false,
        success:function(resp){

            $('#suencialOdontograma').text( resp );
            sucuencial = resp;
        }
    });

    return sucuencial;
}

//corre todos los campos de entrada modificados con librias o framewrok
function inputs_runn() {


    $('#tratamientoSeled').select2({
        placeholder: 'seleccione una opcion',
        allowClear: true,
        language:languageEs,
        minimumInputLength:1,
        ajax:{
            url: $DOCUMENTO_URL_HTTP + '/application/system/pacientes/pacientes_admin/controller/controller_adm_paciente.php',
            type: "POST",
            dataType: 'json',
            async:false,
            data:function (params) {
                var query = {
                    accion: 'filtrarPlantratamientoSearchSelect2',
                    ajaxSend:'ajaxSend',
                    paciente_id: $id_paciente,
                    search: params.term,
                };
                return query;
            },
            delay: 250,
            processResults:function (data) {
                return data;
            }
        }
    });


}





// EXEC ODONTOGRAMA
// alert($accionOdontograma);
if( $accionOdontograma == 'principal')
{
    odontolist();
    inputs_runn();

    /*CREAR EL ODONTOGRAMA*/
    $('#crear_odontograma').on('click', function() {

        // alert( $id_paciente );

        var parametros = {
            'accion':'nuevoUpdateOdontograma',
            'ajaxSend':'ajaxSend',
            'numero': concultarSecuencialOdontograma(),  //ultimo secuencial del odontograma
            'fk_tratamiento' : $('#tratamientoSeled').find(':selected').val(),
            'descrip'        : $('#odontograDescrip').val(),
            'fk_paciente'    : $id_paciente
        };

        $.ajax({
            url: $DOCUMENTO_URL_HTTP +'/application/system/pacientes/pacientes_admin/controller/controller_adm_paciente.php',
            type:'POST',
            data: parametros ,
            dataType:'json',
            async:false,
            success:function(resp){

                if(resp.error == ''){

                    /*se actualiza el odontograma update detalle*/
                    var idplantram = $('#tratamientoSeled').find(':selected').val();
                    crearDetalleOdontograma(resp.lasidOdont, idplantram);

                    notificacion('Información Actualizada', 'success');

                    setTimeout(function() {
                        location.reload();
                    }, 1500);

                }else{

                    $('#msg_errores_odontogram').html(resp.error);
                    setTimeout(function() {
                        $('#msg_errores_odontogram').html(null);
                    },3000);
                }
            }
        });

    });


    function crearDetalleOdontograma(idOdontogramaCab, idplantramiento)
    {
        var url = $DOCUMENTO_URL_HTTP +'/application/system/pacientes/pacientes_admin/controller/controller_adm_paciente.php';
        var paramters = {'ajaxSend':'ajaxSend', 'accion':'OdontogramaUpdate_detalle', 'idplantm':idplantramiento, 'idOdonto':idOdontogramaCab, 'idpaciente': $id_paciente };

        $.get(url, paramters , function (data){
            var info = $.parseJSON(data);
            if(info.error != ''){
                notificacion('Ocurrio un error con el odontograma '+idOdontogramaCab + 'consulte con soporte', 'error'  );
            }
        });
    }

    $("#add_odontograma").on('show.bs.modal', function() {
        $("#tratamientoSeled").val(null).trigger("change");
        $("#odontograDescrip").val(null);
    });


}

