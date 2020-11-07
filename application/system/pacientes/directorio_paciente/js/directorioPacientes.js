//FUCIONES

$boxContentDirectorio = $("#boxprincipalDirectPaciente");

function loadtabledirectPacien(estado)
{
    $('#table_direc').DataTable({
        searching: true,
        ordering:false,
        destroy:true,
        ajax:{
              url: $DOCUMENTO_URL_HTTP + '/application/system/pacientes/directorio_paciente/controller/directorio_paciente_controller.php',
              type:'POST',
              data:{'ajaxSend':'ajaxSend', 'accion':'direct_pacient_list', 'estado':estado},
              dataType:'json',
        },
        columnDefs:[

            {
                targets:0 ,
                render: function (data, type, row) {

                    console.log(row);

                    var menu = "<div class='dropdown pull-left'> ";

                            menu += "<input type='text' class='hidden link_pacientes_id' data-idpac='"+row['id_paciente']+"' >"
                            menu += "<div class='btn btnhover  btn-xs dropdown-toggle' type='button' data-toggle='dropdown' aria-expanded='false'> <i class='fa fa-ellipsis-v'></i> </div>";
                            menu += "<ul class='dropdown-menu'>";
                                menu += "<li> <a href='"+row['url_datosper']+"' style='cursor: pointer; '> Datos Persolanes </a> </li>";
                                menu += "<li> <a href='#' onclick='ImprimirFicha("+row['id_paciente']+")' style='cursor: pointer;'> Imprimir Ficha </a> </li>";
                            menu += "</ul>";

                    menu += "</div>";

                    menu += "";

                    return menu;
                }
            }
        ],
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
        // ajax:{
        //
        // },
    });
}


//Eliminar paciente
function ActivarEliminarPaciente(id, acc)
{
    var estado = "";

    switch (acc)
    {
        case 0: //ACTIVAR
            estado="E";
            break;
        case 1://ELIMINAR
            estado="A";
            break;

    }

    $.ajax({
       url: $DOCUMENTO_URL_HTTP + '/application/system/pacientes/directorio_paciente/controller/directorio_paciente_controller.php',
       type:'POST',
       data:{'ajaxSend':'ajaxSend', 'accion':'updateEstado', 'estado':estado, 'id': id},
       dataType:'json',
       async:false,
       success:function(resp) {

            if(resp == 'OK')
            {
                loadtabledirectPacien('A');
                $("#checkPacienteDesact").prop('checked', false);

                Swal.fire(
                    'Exito!',
                    'Informacion Actualizada!',
                    'success'
                )

            }else{

            }

       }

    });
}

//EVENTOS
$('#checkPacienteDesact').on('change', function() {

    if($(this).is(':checked'))
    {
        loadtabledirectPacien('E');

    }else{
        loadtabledirectPacien('A');
    }
});

// imprimir lista de pacientes mostrando en la tabla
$('#imprimir_listPacientes').click(function(){

    var ids = [];
    $('.link_pacientes_id').each(function(i, item) {

        ids.push( $(this).data('idpac') );

    });

    var url = $DOCUMENTO_URL_HTTP + '/application/system/pacientes/directorio_paciente/export/export_pdf_directorio.php?id=' + ids.toString();
    if(ids.length > 0){
        $(this).attr('href', url);
    }

    console.log(ids);
});


//Imprimir Ficha Del Paciente
var ImprimirFicha = function(idpaciente){

    var url = $DOCUMENTO_URL_HTTP + '/application/system/pacientes/directorio_paciente/export/exportFichaPaciente.php?idpac=' + idpaciente;
    window.open(url, '_blank');
};

//READY WINDOW
$(document).ready(function() {
    loadtabledirectPacien('A');
});

window.onload =     boxloading($boxContentDirectorio, true);

$(window).on("load", function() {
    boxloading($boxContentDirectorio, false, 1000);
});

