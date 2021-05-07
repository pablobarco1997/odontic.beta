

//window onload
window.onload = boxloading($boxContentViewAdminPaciente ,true);


$Ultimo_iddocument = 0;
function  ajax_load_comment_time(text, subaccion, scroll = false) {

    $.ajax({
        url: $DOCUMENTO_URL_HTTP +'/application/system/pacientes/pacientes_admin/controller/controller_adm_paciente.php',
        type:'GET',
        data: {
            'ajaxSend':'ajaxSend',
            'accion': 'comecent_doct_paciente',
            'idPaciente': $id_paciente,
            'text':text,
            'subaccion': subaccion,
            'id_ultimo' :$Ultimo_iddocument
        },
        dataType:'json',
        success: function(resp) {
            var comment_html = "";
            if(resp.error == '') {
                if(resp.numero > 0) {
                    var $comentario = resp.data;
                    var a = 0;
                    $Ultimo_iddocument = resp.ultimoid;
                    while (a <= $comentario.length -1)
                    {
                        var text          = ($comentario[a]['text'] == "") ? "&nbsp;" : $comentario[a]['text'];
                        var doctor        = $comentario[a]['doctor'];
                        var url_icon      = $comentario[a]['icon'];
                        var fechaComment  = $comentario[a]['fecha'];

                        if(a ==  $comentario.length -1)
                        {
                            comment_html += '' +
                                '<div class="direct-chat-msg" id="loadMensage">\n' +
                                '<div class="direct-chat-info clearfix">\n' +
                                '<span class="direct-chat-name pull-left">'+ doctor +'</span>\n' +
                                ' <span class="direct-chat-timestamp pull-right">'+ fechaComment +'</span>\n' +
                                ' </div>\n' +
                                '\n' +
                                ' <img class="direct-chat-img" src="' + url_icon + '" alt="message user image">\n' +
                                '\n' +
                                ' <div class="direct-chat-text">\n' +
                                '   <a style="color: black" >'+ text +'</a>\n' +
                                ' </div>\n' +
                                '\n' +
                                ' </div>';

                        }else{
                            comment_html += '' +
                                '<div class="direct-chat-msg" >\n' +
                                '<div class="direct-chat-info clearfix">\n' +
                                '<span class="direct-chat-name pull-left">'+ doctor +'</span>\n' +
                                '<span class="direct-chat-timestamp pull-right">' +fechaComment+ '</span>\n' +
                                '</div>\n' +
                                '\n' +
                                '  <img class="direct-chat-img" src="' + url_icon + '" alt="message user image">\n' +
                                '\n' +
                                '  <div class="direct-chat-text">\n' +
                                '       <a style="color: black">'+ text +'</a>\n' +
                                '</div>\n' +
                                '\n' +
                                '</div>';
                        }
                        a++;
                    }
                    $('#chatUpdate').html(comment_html);
                    if(resp['ultimo'] == true) {
                        document.getElementById('loadMensage').scrollIntoView();
                    }
                    if(scroll==true){
                        document.getElementById('loadMensage').scrollIntoView();
                    }
                }

                if($('.direct-chat-msg').length == 0){
                    $('#chatUpdate').html(  '<h3 class="text-center">No hay ningún comentario para este paciente</h3>' )
                }
                if(resp.numero == 0) {
                    $('#chatUpdate').html(  '<h3 class="text-center">No hay ningún comentario para este paciente</h3>' )
                }
            }
        }

    });
}

$("#comment").click(function() {
    var text = $("#texto_comment").val();
    ajax_load_comment_time(text, "agregar", true);
    $("#texto_comment").val(null);
});


$(window).on("load", function() {

    ajax_load_comment_time(null, "consultar");

    // setInterval(function() {
    //     ajax_load_comment_time(null, "consultar");
    // },3500);

    boxloading($boxContentViewAdminPaciente ,false, 1000);
});