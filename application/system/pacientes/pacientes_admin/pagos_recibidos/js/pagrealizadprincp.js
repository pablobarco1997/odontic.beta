
/** PAGOS REALIZADOS PARTICULARES*/
if($accionPagospacientes = "pagos_particular")
{

    function list_pagos_particulares()
    {
        
        $('#pag_particular').DataTable({
            searching: false,
            processing: true,
            ordering:false,
            destroy:true,
            paging: true,
            serverSide:true,
            ajax:{
                url: $DOCUMENTO_URL_HTTP + '/application/system/pacientes/pacientes_admin/pagos_pacientes/controller_pagos/controller_pag.php',
                type:'POST',
                data:{
                    'ajaxSend'   :'ajaxSend',
                    'accion'     : 'list_pagos_particular',
                    'idpaciente' : $id_paciente
                },
                dataType:'json',
            },
            columnDefs:[
                {
                    targets:0,
                    render:function(data, type, row) {
                        var idpago = row[8];
                        return "<input type='checkbox' class='custom-checkbox-myStyle' value='"+idpago+"' >";
                    }
                },
                {
                    targets:8,
                    render: function (data, type, row) {

                        var dropdown_menu = "<div class='dropdown col-centered col-xs-1 '>";
                        dropdown_menu += "<button class='btn btnhover  btn-xs dropdown-toggle' data-toggle=\"dropdown\" type='button' aria-expanded='false'><i class=\"fa fa-ellipsis-v\"></i></button>";
                        dropdown_menu += "<ul class='dropdown-menu pull-right' >";
                                dropdown_menu += "<li>"+row['url_imprimir']+"</li>";
                                dropdown_menu += "<li><a href='#'>Enviar Email</a></li>";
                                dropdown_menu += "<li><a href='#detalleprestacionPagos' data-toggle='modal'  onclick='detalle_prestaciones_pagosParticulares("+row[8]+", \""+row['name_tratamiento']+"\" )'  >Mostrar detalle</a></li>";
                                dropdown_menu += "<li><a href='#' onclick='deletePagoPrestacion("+row['id_pagocab']+","+row['idPlantratamCab']+")' >Eliminar Pago</a></li>";
                            dropdown_menu += "</ul>";
                        dropdown_menu += "</div>";


                        console.log(row);
                        return dropdown_menu;
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
        });
        
    }

    /**delete pago prestacion plande tratamiento*/
    function deletePagoPrestacion(idpagosCab, idPlantratamCab)
    {
        if(idpagosCab!=0){

            var url = $DOCUMENTO_URL_HTTP + '/application/system/pacientes/pacientes_admin/pagos_pacientes/controller_pagos/controller_pag.php';
            $.get(url , {
                'accion'        : 'deletePagoPlantram',
                'ajaxSend'      : 'ajaxSend',
                'idpagocab'     : idpagosCab,
                'idpaciente'    : $id_paciente,
                'idPlantratam'  : idPlantratamCab,
            }, function (data) {
                var respuesta = $.parseJSON(data);
                if(respuesta['error'] == '') {
                    notificacion("Información Actualizada", "success");
                }else{

                }
            });

        }else{
            notificacion('Ocurrio un error', 'error');
        }
    }
    
    
    function detalle_prestaciones_pagosParticulares($idpago, n_plantrat)
    {

        $("#n_plantrtam_detalle").html("<b>"+n_plantrat+"</b>");

        $('#detalle_prestaciones_pagos_part').DataTable({
            searching: true,
            ordering:false,
            destroy:true,
            paging: false,
            searching:false,
            ajax:{
                url: $DOCUMENTO_URL_HTTP + '/application/system/pacientes/pacientes_admin/pagos_pacientes/controller_pagos/controller_pag.php',
                type:'POST',
                data:{
                    'ajaxSend'   : 'ajaxSend',
                    'accion'     : 'detalle_pagos_particular',
                    'idpaciente' : $id_paciente ,
                    'idpago'     : $idpago
                },
                dataType:'json',
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
        });
    }


    var FiltrosPagosxPacientes = function() {

        var  table = $("#pag_particular").DataTable();
        var  accion     = "list_pagos_particular";
        var  ajaxSend   = "ajaxSend";

        var n_pago         = $("#pagPrestacion").val();
        var n_x_documento  = $("#n_x_documento").val();
        var formaPago      = $("#formaPago").find(':selected').val();
        var id_tratamiento = $("#busquedaxTratamiento").find(':selected').val();

        var url = $DOCUMENTO_URL_HTTP + '/application/system/pacientes/pacientes_admin/pagos_pacientes/controller_pagos/controller_pag.php';
        var newUrl = url + '?' +
            'accion='+accion+
            '&ajaxSend='+ajaxSend+
            '&npago='+n_pago+
            '&formapago='+formaPago+
            '&plan_tratam='+id_tratamiento+
            '&n_x_documento='+n_x_documento;

        table.ajax.url(newUrl).load();

    };

    $(".aplicar").click(function() {
        FiltrosPagosxPacientes();
    });
    $(".limpiar").click(function() {

        $("#pagPrestacion").val(null);
        $("#n_x_documento").val(null);
        $("#formaPago").val(null).trigger('change');
        $("#busquedaxTratamiento").val(null).trigger('change');

        FiltrosPagosxPacientes();

    });

    $('#busquedaxTratamiento').select2({
        placeholder:'buscar x Tratamiento',
        allowClear:true ,
        language: 'es'
    });
    $('#formaPago').select2({
        placeholder:'buscar x Forma de Pago',
        allowClear:true ,
        language: 'es'
    });

}

$(document).ready(function() {

    list_pagos_particulares();
});


//window onload
window.onload = boxloading($boxContentViewAdminPaciente ,true);
//window load
$(window).on("load", function() {
    boxloading($boxContentViewAdminPaciente ,false, 1000);
});

