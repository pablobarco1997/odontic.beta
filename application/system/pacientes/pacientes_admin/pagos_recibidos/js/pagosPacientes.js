
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
                    targets:8,
                    render: function (data, type, row) {

                        // console.log(row);
                        var dropdown_menu = "<div class='dropdown col-centered col-xs-1 '>";
                        dropdown_menu += "<button class='btn btnhover  btn-xs dropdown-toggle' data-toggle=\"dropdown\" type='button' aria-expanded='false'><i class=\"fa fa-ellipsis-v\"></i></button>";
                        dropdown_menu += "<ul class='dropdown-menu pull-right' >";
                                dropdown_menu += "<li>" + row['url_imprimir'] + "</li>";
                                dropdown_menu += "<li><a href='#'> Enviar Email </a></li>";
                                dropdown_menu += "<li><a href='#detalleprestacionPagos' data-toggle='modal' data-nametratam='"+row['name_tratamiento']+"' data-idp='"+row['id_pagocab']+"' data-nboleta='"+row['n_boleta']+"' onclick='detalle_prestaciones_pagosParticulares("+row['id_pagocab']+", $(this))'  >Mostrar detalle</a></li>";
                                dropdown_menu += "<li><a href='#' data-nametratam='"+row['name_tratamiento']+"' data-valor='"+row['valor']+"' onclick='deletePagoPrestacion("+row['id_pagocab']+","+row['idPlantratamCab']+", $(this))' >Eliminar Pago</a></li>";
                            dropdown_menu += "</ul>";
                        dropdown_menu += "</div>";

                        if(row['boldPlanCab']!=1){
                            return dropdown_menu;
                        }else{
                            return "&nbsp;";
                        }
                    }
                }
            ],
            createdRow: function (row, data, dataIndex) {
                if(data['boldPlanCab'] == 1){
                    var objectTd =  $(row).children();
                    console.log($(row).children());
                    $.each(objectTd, function(i , item) {
                        $(item).attr("colspan", "8").css("background-color", "#f9f9f9");
                        console.log($(item));
                        if($(item).text()==""){
                            $(item).css("display", "none");
                        }else{
                            if(i>2){
                                $(item).html("<a class='btnhover btn-sm btn' onclick='PrintPagosParticulares("+(data['idPlantratamCab'])+")' style='font-weight: bolder'> <i class='fa fa-print'></i> Imprimir PDF</a>");
                            }
                        }
                    });
                }else{
                    $("td:eq(1)", row).css("width", "10%").css("padding","6px 6px");
                    $("td:eq(2)", row).css("width", "5%").css("padding","6px 6px");
                    $("td:eq(3)", row).css("width", "15%").css("padding","6px 6px");
                    $("td:eq(4)", row).css("width", "15%").css("padding","6px 6px");
                    $("td:eq(5)", row).css("width", "20%").css("padding","6px 6px");
                    $("td:eq(6)", row).css("width", "20%").css("padding","6px 6px");
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
        });
        
    }

    /**delete pago prestacion plande tratamiento*/
    function deletePagoPrestacion(idpagosCab, idPlantratamCab, Element)
    {
        if(idpagosCab!=0){

            var url = $DOCUMENTO_URL_HTTP + '/application/system/pacientes/pacientes_admin/pagos_pacientes/controller_pagos/controller_pag.php';
            $.get(url , {
                'accion'        : 'deletePagoPlantram',
                'ajaxSend'      : 'ajaxSend',
                'idpagocab'     : idpagosCab,
                'idpaciente'    : $id_paciente,
                'idPlantratam'  : idPlantratamCab,
                'numTratamiento': Element.prop('dataset').nametratam,
                'valor': Element.prop('dataset').valor,
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
    
    
    function detalle_prestaciones_pagosParticulares($idpago, elemento)
    {

        $("#n_plantrtam_detalle").html("<b>"+(elemento.prop('dataset').nametratam)+"</b>").css('font-size','2rem');
        $("#n_documento_pago_detalle").html('Numero de Documento: '+elemento.prop('dataset').nboleta);

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

        var  table      = $("#pag_particular").DataTable();
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


var PrintPagosParticulares = function(idTratamiento){

    if(!ModulePermission(24, 1)){
        notificacion('Ud. no tiene permiso para esta Operación', 'error');
        return false;
    }

    if(idTratamiento==0||idTratamiento==''){
        notificacion('Ocurrio un error de paramtros consulte con soporte', 'error');
        return false;
    }

    var parametros = '?idplantratamiento='+idTratamiento;
    var url = $DOCUMENTO_URL_HTTP+'/application/system/pacientes/pacientes_admin/pagos_recibidos/export/export_recaudaciones_realizadas.php'+parametros;
    window.open(url, '_blank');
};

$(document).ready(function() {

    list_pagos_particulares();
    $('#busquedaxTratamiento').select2({
        placeholder: 'buscar plan de tratamiento',
        allowClear:true,
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

});


//window onload
window.onload = boxloading($boxContentViewAdminPaciente ,true);
//window load
$(window).on("load", function() {
    boxloading($boxContentViewAdminPaciente ,false, 1000);
});

