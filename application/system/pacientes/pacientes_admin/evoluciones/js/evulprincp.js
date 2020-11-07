

function evoluciones_principal()
{
    $('#list_evoluprinpl').DataTable({

        searching: true,
        ordering:false,
        destroy:true,

        ajax:{
            url: $DOCUMENTO_URL_HTTP + '/application/system/pacientes/pacientes_admin/controller/controller_adm_paciente.php',
            type:'POST',
            data:{
                'ajaxSend'  :'ajaxSend',
                'accion'    :'evolucion_listprincpl',
                'idpaciente': $id_paciente,
                'idplant'   : $('#filt_plantram').find(':selected').val()
            },
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




//PRINCIPAL
if($accion_evol == 'evol_listprincipal')
{
    $('#filt_plantram').select2({
        placeholder: 'Seleccione una opción',
        allowClear: true,
        language:'es'

    });

    evoluciones_principal();

    $('#filtrar_evoluc').click(function() {

        evoluciones_principal();
    });
}