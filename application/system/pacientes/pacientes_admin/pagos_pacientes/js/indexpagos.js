
function listPagosIndependientes()
{

    var ElemmentoContentload = $("#pagos_planestratamiento_list");

    boxTableLoad(ElemmentoContentload, true);

    var abonado   = document.getElementById('abonadoPagosxPacien').checked;
    var realizado = document.getElementById('realizadoPagosxPacien').checked;

    var paramtros = {
        'ajaxSend'   : 'ajaxSend',
        'accion'     : 'listpagos_indepent',
        'idpaciente' : $id_paciente,
        'abonado'    : abonado  ,
        'realizado'  : realizado  ,
        'emitido'    : $("#startDatePagosxPacien").val(),
        'id_tratamiento'    : $("#tratamientoPagosxPacien").find(':selected').val(),
    };

    var table = $('#pagos_planestratamiento_list').DataTable({
        destroy:true,
        searching: false,
        ordering:false,
        "serverSide": true,
        fixedHeader: true,
        paging:true,
        processing: true,
        lengthChange: false,
        lengthMenu:[ 10 ],
        ajax:{
            url: $DOCUMENTO_URL_HTTP + '/application/system/pacientes/pacientes_admin/pagos_pacientes/controller_pagos/controller_pag.php',
            type:'POST',
            data: paramtros,
            dataType:'json',
            async: true,
            cache:false,
            complete: function (xhr, status) {
                boxTableLoad(ElemmentoContentload, false);
            }
        },
        language:{
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
            "sLoadingRecords": "Cargando...",
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


    }).on( 'length.dt', function ( e, settings, len ) { // cambiar
        boxTableLoad(ElemmentoContentload, true);
    }).on( 'page.dt', function ( e, settings, len ) { // cambiar
        boxTableLoad(ElemmentoContentload, true);
    });

    new $.fn.dataTable.FixedHeader( table,
        {
            // headerOffset: 50
        }
    );

}

//window onload
window.onload = boxloading($boxContentViewAdminPaciente ,true);

$(window).on('load', function () {

    //window load
    boxloading($boxContentViewAdminPaciente ,false, 1000);

    if($accionPagos == "pagos_independientes"){


        $('#startDatePagosxPacien').daterangepicker({

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
        }).val(null);


        $('.rango span').click(function() {
            $(this).parent().find('input').click();
        });

        $('#startDate').val(null);

        $('#tratamientoPagosxPacien').select2({
            placeholder: 'buscar plan de tratamiento',
            allowClear:true,
            language: languageEs,
            minimumInputLength:1,
            ajax:{
                url: $DOCUMENTO_URL_HTTP + '/application/system/pacientes/pacientes_admin/controller/controller_adm_paciente.php',
                type: "POST",
                dataType: 'json',
                async:false,
                cache: false,
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

        listPagosIndependientes();

    }

});