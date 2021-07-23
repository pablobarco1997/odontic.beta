

function evoluciones_principal()
{

    var ElemmentoContentload = $("#list_evoluprinpl");

    boxTableLoad(ElemmentoContentload, true);

    var table = $('#list_evoluprinpl').DataTable({
        searching: false,
        processing:true,
        ordering:false,
        destroy:false,
        lengthChange:false,
        lengthMenu:[10],
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
            complete: function(xhr, status) {
                boxTableLoad(ElemmentoContentload, false);
            }
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


function FiltrarEvolucion(){

    var  table      = $("#list_evoluprinpl").DataTable();
    var  accion     = "evolucion_listprincpl";
    var  ajaxSend   = "ajaxSend";
    var url = $DOCUMENTO_URL_HTTP + '/application/system/pacientes/pacientes_admin/controller/controller_adm_paciente.php';
    var newUrl = url + '?' +
        'accion='+accion+
        '&ajaxSend='+ajaxSend+
        '&idpaciente='+$id_paciente+
        '&idplant='+$('#filt_plantram').find(':selected').val()+
        '&date='+$('#startDateEvoluciones').val();

    table.ajax.url(newUrl).load();

}

function AppExporPrint(){

    var exporturl = $DOCUMENTO_URL_HTTP + '/application/system/pacientes/pacientes_admin/evoluciones/export/export_pdf_evoluciones.php?idpaciente='+$id_paciente;
    exporturl += '&idplant='+$('#filt_plantram').find(':selected').val()+'&date='+$("#startDateEvoluciones").val();

    window.open(exporturl, '_blank');
}


$(document).ready(function() {

    if($accion_evol == 'evol_listprincipal') {
        $('#filt_plantram').select2({
            placeholder: 'Seleccione una opción',
            allowClear: true,
            language: languageEs,
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
                delay: 500,
                processResults:function (data) {
                    return data;
                }
            }

        });

        $('#filtrar_evoluc').click(function() {
            FiltrarEvolucion();
        });

        $('#limpiar').click(function() {
            $('#filt_plantram').val(null).trigger('change');
            FiltrarEvolucion();
        });

        $('#startDateEvoluciones').daterangepicker({

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

        evoluciones_principal();

    }

});


//window onload
window.onload = boxloading($boxContentViewAdminPaciente ,true);


//window load
$(window).on('load', function() {
    boxloading($boxContentViewAdminPaciente ,false, 1000);

});
