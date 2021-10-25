

//window onload
$(window).on('onload', function () {

    boxloading($boxContentViewAdminPaciente ,true);
});


function commentlist(){

    var paramts = {
       'accion': 'listComment',
       'ajaxSend': 'ajaxSend',
       'paciente_id': $id_paciente
    };

    $("#refresh_comment").find("span").addClass('btnSpinner');

    var table =  $('#list_Comentarios_asociados').DataTable({
        searching: false,
        "ordering":false,
        "serverSide": true,
        // responsive: true,
        destroy:true,
        scrollX: false,
        // scrollY: 500,
        lengthChange: false,
        fixedHeader: true,
        paging:true,
        processing: true,
        lengthMenu:[ 5 ],
        ajax: {
            url: $DOCUMENTO_URL_HTTP +'/application/system/pacientes/pacientes_admin/controller/controller_adm_paciente.php',
            type:'POST',
            data: paramts,
            async:true,
            cache:false,
            dataType:'json',
            complete: function(xhr, status){
                $("#refresh_comment").find("span").removeClass('btnSpinner');
            }
        },
        columnDefs:[
            {
                targets:1,
                render:function (data, type, row, meta) {
                    var date = '<span style="display: block; color: grey; margin-left: 10px" class="">'+row["date"]+'</span>';
                    var user = '<span style="display: block; margin-left: 10px" class=""><b>usuario:</b> '+row["usuario"]+'</span>';
                    var msg  = '<div class="direct-chat-text" style="margin-left: 5px !important;">' +
                        '           <a style="color: black; white-space: pre-wrap; display: block; ">'+(row['msg'])+'</a>' +
                        '       </div>';

                    var dom = date + user + msg;
                    return dom;
                }
            }
        ],
        "language": {
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
        "infoCallback": function (settings, start, end, max, total, pre){
            return "Mostrando registros del "+ start +" al "+ end +"<br>de un total de "+total+ " registros.";
        }
    }).on( 'length.dt', function ( e, settings, len ) { // cambiar
        // $("#refresh_comment").find("span").addClass('btnSpinner');
    }).on( 'page.dt', function ( e, settings, len ) { // cambiar
        // $("#refresh_comment").find("span").addClass('btnSpinner');
    });

}


function  ajax_load_comment_time(text) {

    $.ajax({
        url: $DOCUMENTO_URL_HTTP +'/application/system/pacientes/pacientes_admin/controller/controller_adm_paciente.php',
        type:'GET',
        data: {
            'ajaxSend':'ajaxSend',
            'accion': 'comecent_doct_paciente_crearte',
            'idPaciente': $id_paciente,
            'text':text,
            'subaccion': "agregar"
        },
        async:true,
        cache:false,
        dataType:'json',
        complete: function(xhr, status){
            $("#refresh_comment") .find("span").removeClass('btnSpinner');
        },
        success: function(response) {
            if(response['error']!=''){
                notificacion(response['error'], 'error');
            }else{
                commentlist();
            }
        }

    });
}

$("#comment").click(function() {

    if(!ModulePermission('Comentarios Administrativos', 'agregar')){
        notificacion('Ud. No tiene permiso para esta Operación', 'error');
        return false;
    }

    if($("#texto_comment").val() == ""){
        notificacion('Ingrese un mensaje', 'question');
        return false;
    }

    var text = $("#texto_comment").val();
    ajax_load_comment_time(text);
    $("#texto_comment").val(null);
});

$("#refresh_comment").click(function () {

    if(!$("#refresh_comment").find("span").hasClass('btnSpinner')){
        $("#refresh_comment").find("span").addClass('btnSpinner');
    }
    commentlist();
});

$(window).on("load", function() {

    commentlist();
    boxloading($boxContentViewAdminPaciente ,false, 1000);

    if(!ModulePermission('Comentarios Administrativos', 'consultar')){
        notificacion('Ud. No tiene permiso para consultar', 'error');
        return false;
    }
});