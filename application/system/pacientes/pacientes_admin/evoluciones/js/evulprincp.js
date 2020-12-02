

function evoluciones_principal()
{
    $('#list_evoluprinpl').DataTable({
        searching: false,
        processing:true,
        ordering:false,
        destroy:false,
        serverSide:true,
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


function FiltrarEvolucion(){

    var  table      = $("#list_evoluprinpl").DataTable();
    var  accion     = "evolucion_listprincpl";
    var  ajaxSend   = "ajaxSend";
    var url = $DOCUMENTO_URL_HTTP + '/application/system/pacientes/pacientes_admin/controller/controller_adm_paciente.php';
    var newUrl = url + '?' +
        'accion='+accion+
        '&ajaxSend='+ajaxSend+
        '&idpaciente='+$id_paciente+
        '&idplant='+$('#filt_plantram').find(':selected').val();

    table.ajax.url(newUrl).load();

}

function AppExporPrint(){

    var exporturl = $DOCUMENTO_URL_HTTP + '/application/system/pacientes/pacientes_admin/evoluciones/export/export_pdf_evoluciones.php?idpaciente='+$id_paciente;
    exporturl += '&idplant='+$('#filt_plantram').find(':selected').val();

    window.open(exporturl, '_blank');
}


$(document).ready(function() {

    if($accion_evol == 'evol_listprincipal') {
        $('#filt_plantram').select2({
            placeholder: 'Seleccione una opción',
            allowClear: true,
            language:'es'
        });

        evoluciones_principal();

        $('#filtrar_evoluc').click(function() {
            FiltrarEvolucion();
        });

        $('#limpiar').click(function() {
            $('#filt_plantram').val(null).trigger('change');
            FiltrarEvolucion();
        });
    }

});


//window onload
window.onload = boxloading($boxContentViewAdminPaciente ,true);


//window load
$(window).on('load', function() {
    boxloading($boxContentViewAdminPaciente ,false, 1000);

});
