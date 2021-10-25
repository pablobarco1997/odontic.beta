
function list_mail_sent(){

    var fecha        = $("#startDate").val();
    var status       = $("#estadoEmailConfPaci").find(':selected').val();
    var n_citas      = $("#busqN_Cita").val();

    var ElemmentoContentload = $("#mailSentTable");
    boxTableLoad(ElemmentoContentload, true);

    var table = $('#mailSentTable').DataTable({
        searching: false,
        // destroy:true,
        ordering:false,
        processing:true,
        serverSide:true,
        lengthChange:false,
        lengthMenu:[ 10 ],
        ajax:{
            url: $DOCUMENTO_URL_HTTP + '/application/system/pacientes/pacientes_admin/email_sent/controller/controller_emailsent.php',
            type:'POST',
            data: {
                'ajaxSend'   :  'ajaxSend',
                'accion'     :  'list_mail_sent',
                'idpaciente' :  $id_paciente,
                'fecha'      :  fecha,
                'status'     :  status,
                'n_citas'    :  n_citas,
            },
            complete: function(xhr, status){
                boxTableLoad(ElemmentoContentload, false);
            },
            dataType:'json',
        },
        columnDefs:[
            {
                'targets'   : 5,
                'searchable':false,
                'orderable' :false,
                'className' : 'dt-body-center',
                'render'    : function (data, type, full, meta){

                    menu= "";

                    if(full['program']==1 && full['estado']=='P'){
                        var menu = "<div class='col-xs-2 col-md-2 no-padding pull-right ' style='position: relative'>";
                            menu += "<div class='dropdown pull-right '>";
                                menu += "<button class='btn btnhover  btn-xs dropdown-toggle' type='button' data-toggle='dropdown' style='height: 100%' >";
                                menu += " <i class=\"fa fa-ellipsis-v\"></i>";
                                menu += "</button>";

                                menu += "<ul class='dropdown-menu' style='z-index: +2000'>";
                                   menu += "<li> <a href='#' data-id='"+btoa(full['id_noti'])+"' onclick='anular_program($(this))'> Anular </a> </li>";
                                menu += "</ul>";

                            menu += "</div>";
                        menu += "</div>";
                    }

                    return menu;
                },

            }
        ],
        'createdRow':function(row, data, index){

            /** Aplicar el ancho */
            $(row).children().eq(0).css('width','20%');
            $(row).children().eq(1).css('width','15%');
            $(row).children().eq(2).css('width','15%');
            $(row).children().eq(3).css('width','15%');
            $(row).children().eq(4).css('width','5%');
            $(row).children().eq(5).css('width','2%');
            $(row).children().eq(6).css('width','2%');

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

function AplicarBusqueda(){

    var ElemmentoContentload = $("#mailSentTable");
    boxTableLoad(ElemmentoContentload, true);

    var table        = $("#mailSentTable").DataTable();
    var fecha        = $("#startDate").val();
    var status       = $("#estadoEmailConfPaci").find(':selected').val();
    var n_citas      = $("#busqN_Cita").val();
    var accion       = 'list_mail_sent';
    var ajaxSend     = 'ajaxSend';

    var url = $DOCUMENTO_URL_HTTP + '/application/system/pacientes/pacientes_admin/email_sent/controller/controller_emailsent.php';

    var newUrl = url+'?'+
        'accion='+accion+
        '&ajaxSend='+ajaxSend+
        '&fecha='+fecha+
        '&status='+status+
        '&n_citas='+n_citas;

    table.ajax.url(newUrl).load();

}

$(".aplicar").click(function() {
    AplicarBusqueda();
});
$(".limpiar").click(function() {
    $("#startDate").val(null);
    $("#busqN_Cita").val(null);
    $("#estadoEmailConfPaci").val(null).trigger('change');
    AplicarBusqueda();
});

function anular_program(element) {

    var id = element.prop('dataset').id;

    if(id!=""){

        $.ajax({
            url: $DOCUMENTO_URL_HTTP + '/application/system/pacientes/pacientes_admin/email_sent/controller/controller_emailsent.php',
            type:'POST',
            data:{
                ajaxSend:'ajaxSend',
                accion:'anular_program_email',
                id: atob(id),
            },
            cache:false, 
            async:false, 
            success:function (response) {
                if(response['error']!=""){
                    var table = $("#mailSentTable").DataTable();
                    table.ajax.reload(false, null);
                }
            }
        });
    }
}

$(document).ready(function() {

    /**FECHA X RANGO*/
    $('#startDate').daterangepicker({
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

    $('#estadoEmailConfPaci').select2({
        placeholder:'Selecione una opción',
        allowClear:true,
        language:'es'
    });

});

//window onload
window.onload = boxloading($boxContentViewAdminPaciente ,true);
//window load
$(window).on("load", function() {

    list_mail_sent();
    boxloading($boxContentViewAdminPaciente ,false, 1000);

    if(!ModulePermission('E-mail Asociados', 'consultar')){
        notificacion('Ud. no tiene permiso para Consultar', 'error');
        return false;
    }

});