

/** ESPECIALIDADES DE ODONTOLOGOS **/

if($accion == 'specialties')
{

    //LISTA DE ESPECIALIDADES
    function list_especialidades()
    {
        $('#gention_especialidades').DataTable({

            searching: true,
            ordering:false,
            destroy:true,
            ajax:{
                url: $DOCUMENTO_URL_HTTP + '/application/system/configuraciones/controller/conf_controller.php',
                type:'POST',
                data:{'ajaxSend':'ajaxSend', 'accion':'list_especialidades'},
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

    //ESPECIALIDADES LIST
    $('#guardar_conf_especialidad').click(function() {

        if(!ModulePermission(13,2)){
            notificacion('Ud. No tiene permiso para crear Especialidades', 'question');
            return false;
        }

        var boxConfEspecialidad = $("#ModalConfEspecialidades").find(".modal-dialog");

        boxloading(boxConfEspecialidad,true);

        var puedo = 0;

        var especialidad = $('#especialidad_nombre');

        if( especialidad.val() == ''){
            puedo++;
            especialidad.addClass('INVALIC_ERROR');
            $('#msg_especialidad').text('Campo obligatorio, (escriba una especialidad)');
        }else{
            especialidad.removeClass('INVALIC_ERROR');
            $('#msg_especialidad').text(null);
        }

        if( puedo == 0){

            $.ajax({
                url: $DOCUMENTO_URL_HTTP + '/application/system/configuraciones/controller/conf_controller.php',
                type:'POST',
                data: { 'ajaxSend': 'ajaxSend', 'accion': 'nuevo_update_especialidad', 'especialidad': especialidad.val(), 'descrip': $('#especialidad_descripcion').val() },
                dataType:'json',
                async:false,
                error:function(xhr, status){
                    if(xhr['status']=='200'){
                        boxloading(boxConfEspecialidad,false,1000);
                    }else{
                        if(xhr['status']=='404'){
                            notificacion("Ocurrió un error con la <b>Especialidades</b> <br> <b>xhr: "+xhr['status']+" <br> Consulte con Soporte </b>");
                        }
                        boxloading(boxConfEspecialidad,false,1000);
                    }
                },
                complete:function(xhr, status) {

                    if(xhr['status']=='200'){
                        boxloading(boxConfEspecialidad,false,1000);
                    }else{
                        if(xhr['status']=='404'){
                            notificacion("Ocurrió un error con la <b>Especialidades</b> <br> <b>xhr: "+xhr['status']+" <br> Consulte con Soporte </b>");
                        }
                        boxloading(boxConfEspecialidad,false,1000);
                    }
                },
                success: function(resp){

                    if( resp.error == ''){
                        notificacion('Información Actualizada', 'success');
                        list_especialidades();
                        $("#ModalConfEspecialidades").modal("hide");
                    }else {
                        notificacion(resp.error , 'error');
                    }

                    boxloading(boxConfEspecialidad,false,1000);
                }
            });
        }else{
            boxloading(boxConfEspecialidad,false,1000);
        }

    });

    $("#especialidad_nombre").keyup(function() {
        if( $(this).val() == ''){
            $(this).addClass('INVALIC_ERROR');
            $('#msg_especialidad').text('Campo obligatorio, (escriba una especialidad)');
        }else{
            $(this).removeClass('INVALIC_ERROR');
            $('#msg_especialidad').text(null);
        }
    });


    $('#ModalConfEspecialidades').on('show.bs.modal', function (e) {
        $("#especialidad_nombre").val(null);
        $("#especialidad_descripcion").val(null);
    });



    //eliminar especialidad
    function eliminar_especialidad(id){

        var boxConfEspecialidad = $("#ModalConfEspecialidades").find(".modal-dialog");

        if(!ModulePermission(13,4)){
            notificacion('Ud. No tiene permiso para Eliminar Especialidades', 'question');
            return false;
        }

        boxloading(boxConfEspecialidad,true);

        if(id != ""){

            $.ajax({
                url: $DOCUMENTO_URL_HTTP + '/application/system/configuraciones/controller/conf_controller.php',
                type:'POST',
                data: { 'ajaxSend': 'ajaxSend', 'accion': 'delete_especialidad', 'id': id},
                dataType:'json',
                async:false,
                complete:function(xhr, status) {

                    if(xhr['status']=='200'){
                        boxloading(boxConfEspecialidad,false,1000);
                    }else{
                        if(xhr['status']=='404'){
                            notificacion("Ocurrió un error con la <b>Eliminar Especialidad</b> <br> <b>xhr: "+xhr['status']+" <br> Consulte con Soporte </b>");
                        }
                        boxloading(boxConfEspecialidad,false,1000);
                    }
                },
                success: function(resp) {

                    if(resp.error == ''){
                        boxloading(boxConfEspecialidad,false,1000);
                        notificacion('Información Actualizada', 'success');
                        list_especialidades();
                    }else{
                        boxloading(boxConfEspecialidad,false,1000);
                        notificacion(resp.error, 'error');
                    }

                }
            });

        }
    }



}

window.onload =  boxloading($boxContentConfiguracion,true);

$(window).on("load", function() {

    list_especialidades();
    boxloading($boxContentConfiguracion,false,1000);

});