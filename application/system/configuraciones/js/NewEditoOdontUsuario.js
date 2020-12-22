/** ODONTOLOGOS Y CREACION DE USUARIOS**/

if($accion == 'dentist')
{

    var FormValidationOdontolotoMod = function(fiel=false, input=false) {

        var documentInput   = [];
        var accionOdontolo  = $('#accion').prop('dataset').subaccodontol; //subaccion Nuevo o Modificar

        var nombre          = $('.nombre_doct');
        var apellido        = $('.apellido_doct');
        var telefono        = $('.TelefonoConvencional_doct');
        var direccion       = $('.direccion_doct');
        var celular         = $('.celular_doct');
        var email           = $('.email_doct');
        var ciudad          = $('.ciudad_doct');
        var ruc_cedula      = $('.rucedula_doct');
        var cedula2         = $('.rucedula_doct');

        if(nombre.val()==''||nombre.val()==null){
            documentInput.push({
                'document' : nombre,
                'msg' : 'Este campo es requerido',
            });
        }
        if(apellido.val()==''||apellido.val()==null){
            documentInput.push({
                'document' : apellido,
                'msg' : 'Este campo es requerido',
            });
        }
        if(direccion.val()==''||direccion.val()==null){
            documentInput.push({
                'document' : direccion,
                'msg' : 'Este campo es requerido',
            });
        }
        if(celular.val()==''||celular.val()==null){
            documentInput.push({
                'document' : celular,
                'msg' : 'Este campo es requerido',
            });
        }
        if(email.val()==''||email.val()==null){
            documentInput.push({
                'document' : email,
                'msg' : 'Este campo es requerido',
            });
        }
        if(ruc_cedula.val()==''||ruc_cedula.val()==null){
            documentInput.push({
                'document' : ruc_cedula,
                'msg' : 'Este campo es requerido',
            });
        }
        if(ciudad.val()==''||ciudad.val()==null){
            documentInput.push({
                'document' : ciudad,
                'msg' : 'Este campo es requerido',
            });
        }

        //change - keyUp  input
        if(cedula2!=false){
            if(cedula2.attr('id') == 'rucedula_doct'){
                if(cedula2.val()!=""){
                    if(accionOdontolo == "modificar"){
                        if(cedula2.val() != cedula2.prop('dataset').idcedula){ // si la cedula es diferente a la que ya esta add al modificarla
                            if(validarCedulaOdontol(cedula2.val())==false){
                                documentInput.push({
                                    'document' : cedula2,
                                    'msg' : 'No puede ingresar n.cedula repetido.  <b>Ya se encuentra registrado esta CI aun odontolog@</b>  ',
                                });
                            }
                        }
                    }
                    if(accionOdontolo == "nuevo"){
                        if(cedula2.val() != ""){ // si la cedula es diferente a la que ya esta add al modificarla
                            if(validarCedulaOdontol(cedula2.val())==false){
                                documentInput.push({
                                    'document' : cedula2,
                                    'msg' : 'No puede ingresar n.cedula repetido.  <b>Ya se encuentra registrado esta CI aun odontolog@</b>  ',
                                });
                            }
                        }
                    }
                }
            }
        }

        //con funcion asignada

        $('.err_msg').remove();

        for (var i = 0; i <= documentInput.length -1; i++)
        {
            var MsgError  = document.createElement('small');
            MsgError.setAttribute('class','err_msg');

            var documento = null;
            documento       = documentInput[i]['document'];
            var msg         = $(MsgError)
                                    .html(documentInput[i]['msg'])
                                    .css('color','red').append("<br>");

            msg.insertAfter(documento);
            console.log(documento);
        }

        if(documentInput.length>0)
            return false;
        else
            return true;


    };

    //LIST ODONTOLOGOS
    //FUNCION
    function list_odontologos(estado)
    {
        $('#gention_odontologos_list').DataTable({

            searching: true,
            ordering:false,
            destroy:true,
            ajax:{
                url: $DOCUMENTO_URL_HTTP + '/application/system/configuraciones/controller/conf_controller.php',
                type:'GET',
                data:{
                    'ajaxSend': 'ajaxSend' ,
                    'accion'  : 'list_odontologos' ,
                    'estado'  :  estado
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

    //GUARDAR NUEVO ODONTOLOGO
    //GUARDAR REGISTRO DE ODONTOLOGOS
    $('#guardar_informacion_odontologos').click(function(){

        var archivo = document.getElementById('icon_doct');
        var fichero = archivo.files[0];

        console.log(fichero);

        var $puedo = 0;

        var nombre       = $('#nombre_doct');
        var apellido     = $('#apellido_doct');
        var telefono     = $('#TelefonoConvencional_doct');
        var direccion    = $('#direccion_doct');
        var celular      = $('#celular_doct');
        var email        = $('#email_doct');
        var ciudad       = $('#ciudad_doct');
        var ruc_cedula   = $('#rucedula_doct');
        var especialidad = $('#especialidad_doct').find(':selected').val();

        if(FormValidationOdontolotoMod()==false)
            $puedo++;

        //subaaccion modal

        var subaccion = '';
        var id = '';
        if($('#accion').prop('dataset').id == '0'){
            subaccion = 'nuevo';
        }
        if(parseInt( $('#accion').prop('dataset').id ) > 0){
            subaccion = 'modificar';
            id = $('#accion').prop('dataset').id;
        }

        var datos = {
            'nombre'    :nombre.val(),
            'apellido'  :apellido.val(),
            'telefono'  :telefono.val(),
            'direccion' :direccion.val(),
            'celular'   :celular.val(),
            'email'     :email.val(),
            'ciudad'    :ciudad.val(),
            'especialidad': especialidad,
            // 'icon'        : fichero,
            'cedula_ruc': ruc_cedula.val(),
            'TieneImagen': ($('#valid_ico').val()=="TieneImagen")?1:0,
        };

        if($puedo == 0){
            nuevoGuardarOdontologo(datos, subaccion, id, ((fichero==undefined)?"":fichero));
            $('#icon_doct').val(null);
        }else{
            // notificacion('No puede guardar la informacion, Faltan campos obligatorios', 'error');

        }

    });


    function nuevoGuardarOdontologo(datos, subaccion, id, fichero)
    {
        var boxModalFormOdonto = $("#modal_conf_doctor");

        boxloading(boxModalFormOdonto, true);

        var form = new FormData();

        form.append('ajaxSend', 'ajaxSend');
        form.append('accion', 'crear_odontologo');
        form.append('aux_idcedula', $("#rucedula_doct").prop('dataset').idcedula);
        form.append('subaccion', subaccion);
        form.append('id', id);
        form.append('datos', JSON.stringify(datos));

        if(fichero!="")
            form.append('icon', fichero);



        $.ajax({
            url: $DOCUMENTO_URL_HTTP + '/application/system/configuraciones/controller/conf_controller.php',
            type:'POST',
            data: form,
            dataType:'json',
            contentType:false,
            processData:false,
            async:false,
            error:function(xhr, status){
                if(xhr['status']=='200'){
                    boxloading(boxModalFormOdonto,false,1000);
                }else{
                    if(xhr['status']=='404'){
                        notificacion("Ocurrió un error con la <b>Nuevo Update Odontólog@</b> <br> <b>xhr: "+xhr['status']+" <br> Consulte con Soporte </b>");
                    }
                    boxloading(boxModalFormOdonto,false,1000);
                }
            },
            complete:function(xhr, status) {

                if(xhr['status']=='200'){
                    boxloading(boxModalFormOdonto,false,1000);
                }else{
                    if(xhr['status']=='404'){
                        notificacion("Ocurrió un error con la <b>Nuevo Update Odontólog@</b> <br> <b>xhr: "+xhr['status']+" <br> Consulte con Soporte </b>");
                    }
                    boxloading(boxModalFormOdonto,false,1000);
                }
            },
            success: function(resp){

                if(resp.error == ''){
                    notificacion('Información Actualizada', 'success');
                    setTimeout(function () { location.reload(true); } ,1000);
                }else{
                    notificacion(resp.error, 'error');
                }

                boxloading(boxModalFormOdonto,false,1000);
            }

        });
    }



    //MODIFICAR ODONTOLOGO
    function modificarOdontologo(id)
    {
        if(!ModulePermission(12,3)){
            notificacion('Ud. No tiene permiso para Modificar odontolog@', 'question');
            $("#modal_conf_doctor").modal("hide");
            return false;
        }else{
            $("#modal_conf_doctor").modal("show");
        }

        //Modificar
        $('#accion').attr('data-id', id).attr('data-subaccodontol','modificar');
        $('#accion').text( 'Modificar' );
        $.ajax({
            url: $DOCUMENTO_URL_HTTP + '/application/system/configuraciones/controller/conf_controller.php',
            type:'POST',
            data:{ 'ajaxSend':'ajaxSend', 'accion':'fetch_odontologos', 'id': id },
            dataType:'json',
            async:false,
            success: function(resp){

                console.log(resp);

                var datos         = resp.error;
                var nombre        = $('#nombre_doct');
                var apellido      = $('#apellido_doct');
                var telefono      = $('#TelefonoConvencional_doct');
                var direccion     = $('#direccion_doct');
                var celular       = $('#celular_doct');
                var email         = $('#email_doct');
                var ciudad        = $('#ciudad_doct');
                var especialidad  = $('#especialidad_doct');
                var rucedula_doct = $('#rucedula_doct');

                var img           = $('#icon_usuario_doct');
                var validimg      = $('#valid_ico');

                nombre.val( datos.nombre_doc );
                apellido.val( datos.apellido_doc );
                celular.val( datos.celular );
                telefono.val( datos.telefono_convencional );
                direccion.val( datos.direccion );
                email.val( datos.email );
                ciudad.val( datos.ciudad );
                rucedula_doct.val(datos.cedula).attr('data-idcedula',datos.cedula);
                especialidad.val( datos.fk_especialidad ).trigger('change');

                if(datos.icon != '' && datos.icon != null){
                    img.attr('src',  $DOCUMENTO_URL_HTTP + '/logos_icon/' + $DIRECTORIO + '/' + datos.icon );
                    validimg.val("TieneImagen");
                }

                if( datos.icon == null || datos.icon == ""){
                    img.attr('src',  $DOCUMENTO_URL_HTTP + '/logos_icon/logo_default/doct-icon.ico' );
                    validimg.val("NoTieneImagen");
                }

            }

        });

        FormValidationOdontolotoMod();
    }

    //ACTUALIZAR ESTADO DEL ODONTOLOGO
    function UpdateEstadoOdontologos(id, estado)
    {
        if(estado=='E'){
            if(!ModulePermission(12,4)){
                notificacion('Ud. No tiene permiso para Desactivar Odontolog@','question');
                return false;
            }
        }
        $.ajax({
            url: $DOCUMENTO_URL_HTTP + '/application/system/configuraciones/controller/conf_controller.php',
            type:'POST',
            data:{ 'ajaxSend':'ajaxSend', 'accion':'actualizar_estados', 'id': id, 'estado': estado },
            dataType:'json',
            async:false,
            success: function(resp) {

                if(resp.error != ''){
                    notificacion(resp.error, 'error');
                }else{
                    notificacion('Información Actualizada', 'success');
                    if(estado == 'A'){
                        list_odontologos('E');
                    }
                    if(estado == 'E'){
                        list_odontologos('A');
                    }

                }
            }
        });
    }

    //cambiar attr accion nuevo && modificar
    function cambiarattr(){

        //Odontologos
        $('#accion').attr('data-id', 0).attr('data-subaccodontol', 'nuevo');
        $('#accion').text( 'Nuevo' );

        $('#nombre_doct').val(null);
        $('#apellido_doct').val(null);
        $('#TelefonoConvencional_doct').val(null);
        $('#direccion_doct').val(null);
        $('#celular_doct').val(null);
        $('#email_doct').val(null);
        $('#ciudad_doct').val(null);
        $('#rucedula_doct').val(null).attr("data-idcedula","");
        $('#especialidad_doct').val(0).trigger('change');
        $('#icon_usuario_doct').attr('src',  $DOCUMENTO_URL_HTTP + '/logos_icon/logo_default/doct-icon.ico' );
        $('#valid_ico').val("NoTieneImagen");
        FormValidationOdontolotoMod();

        if(!ModulePermission(12,2)){
            $("#modal_conf_doctor").modal("hide");
            notificacion('Ud. No tiene permiso para crear Odontolog@', 'question');
            return false;
        }else{
            $("#modal_conf_doctor").modal("show");
        }

    }


    /**EVENTOS Cambiar Icono paciente**/
    $('#icon_doct').change(function(e){

        if($(this).val()!="")
            $("#valid_ico").val("TieneImagen");
        else
            $("#valid_ico").val("NoTieneImagen");


        SubirImagenes( this , $('#icon_usuario_doct') , $DOCUMENTO_URL_HTTP + '/logos_icon/logo_default/doct-icon.ico');
    });


    $('#desabilitado_doctores').change(function() {
        if( $(this).prop('checked')){
            list_odontologos('E');
        }else{
            list_odontologos('A');
        }
    });


    /**CREACION MODIFICAR USUARIO DEL PACIENTE**/

    function encrytar_base64(dato) {
        return btoa(dato);
    }
    function descrytar_base64(dato) {
        return atob(dato);
    }


    $('#tipoUsuario').change(function() {


        var modificarcheck  = $('#chek_modificar');
        var eliminarcheck   = $('#chek_eliminar');
        var consultar       = $('#chek_consultar');
        var agregarcheck    = $('#chek_agregar');

        if( $(this).find(':selected').val() == '' || $(this).find(':selected').val() == 1 ){

            modificarcheck.addClass('disabled_link3').attr('disabled', true);
            eliminarcheck.addClass('disabled_link3').attr('disabled', true);
            consultar.addClass('disabled_link3').attr('disabled', true);
            agregarcheck.addClass('disabled_link3').attr('disabled', true);

        }else{

            modificarcheck.removeClass('disabled_link3').attr('disabled', false);
            eliminarcheck.removeClass('disabled_link3').attr('disabled', false);
            consultar.removeClass('disabled_link3').attr('disabled', false);
            agregarcheck.removeClass('disabled_link3').attr('disabled', false);
        }

        //si es igual a 0 y no se a seleccionado nada
        if( $(this).find(':selected').val() == '')
        {
            $(this).addClass('INVALIC_ERROR');
            $('#msg_permisos').text('Debe seleccionar un tipo de usuario');
            $('#nuevoUpdateUsuario').addClass('disabled_link3');
        }else{
            $(this).removeClass('INVALIC_ERROR');
            $('#msg_permisos').text(null);
            $('#nuevoUpdateUsuario').removeClass('disabled_link3');
        }

        //administrador
        if(  $(this).find(':selected').val() == 1 ){

            modificarcheck.prop('checked', true);
            eliminarcheck.prop('checked', true);
            consultar.prop('checked', true);
            agregarcheck.prop('checked', true);

        }else{

            modificarcheck.prop('checked', false);
            eliminarcheck.prop('checked', false);
            consultar.prop('checked', false);
            agregarcheck.prop('checked', false);
        }
    });

    //compruebo si el odontologo ya tiene creado un usuario
    function UsuarioOdctor(docto)
    {

        var puede = 0;

        var iddoctor = docto.find(':selected').val();
        $.ajax({
            url: $DOCUMENTO_URL_HTTP + '/application/system/configuraciones/controller/conf_controller.php',
            type:'POST',
            data: {'ajaxSend':'ajaxSend','accion':'consultar_usuario', 'iddoctor': iddoctor , 'subaccion':'doct_usuario'},
            dataType:'json',
            async:false,
            success: function(resp){
                if(resp.error != ''){
                    $('#msg_doctorUsuario').text(resp.error);
                    $('#nuevoUpdateUsuario').addClass('disabled_link3');
                    puede++;
                }else {
                    $('#msg_doctorUsuario').text(null);
                    $('#nuevoUpdateUsuario').removeClass('disabled_link3');
                    $('#usu_doctor').removeClass('INVALIC_ERROR');
                }
            }
        });

        if( iddoctor == ""){
            $('#msg_doctorUsuario').text('Debe Selecionar un doctor');
            $('#nuevoUpdateUsuario').addClass('disabled_link3');
        }

        return puede;

    }

    /** Validar cedula de odontologo no puede registrar la misma cedula ya que es un identificado unico*/
    function validarCedulaOdontol(cedula)
    {
        var valid = false;
        var url = $DOCUMENTO_URL_HTTP + '/application/system/configuraciones/controller/conf_controller.php';

        $.ajax({
            url: url,
            type:'GET',
            data: {
                'accion'      :'valid_ci_odontolog',
                'ajaxSend'    :'ajaxSend',
                'id_cedula'      : cedula.replace('-','') ,
                'idodonti'    : $('#accion').prop('dataset').id,
                'subaccionMod': $('#accion').prop('dataset').subaccodontol,
            },
            async:false,
            dataType:'json',
            cache:false,
            success:function (respuesta){
                if(respuesta['error'] == ""){
                    valid = true;
                }
            }
        });

        return valid;

    }

    $('#nuevoUpdateUsuario').click(function(){

        var $puedoPasar = 0;

        var doctor  = $("#usu_doctor");
        var usuario  = $("#usu_usuario");
        var passeord = $("#usu_password");
        var Confir_passeord = $("#usu_confir_password");
        var tipoUsuario = $("#tipoUsuario");

        if(doctor.find(':selected').val() == "" ){

            doctor.addClass('INVALIC_ERROR');
            $('#msg_doctorUsuario').text('Tiene que asociar un doctor');
            $puedoPasar++;
        }else{
            doctor.removeClass('INVALIC_ERROR');
            $('#msg_doctorUsuario').text(null);
        }

        if(usuario.val() == ""){
            usuario.addClass('INVALIC_ERROR');
            $('#msg_usuario_repit').text('Debe Ingresar un Usuario');
            $puedoPasar++;
        }else{
            usuario.removeClass('INVALIC_ERROR');
            $('#msg_usuario_repit').text(null);
        }

        if(passeord.val() == ""){
            passeord.addClass('INVALIC_ERROR');
            $('#msg_password_d').text('Debe Ingresar un password');
            $puedoPasar++;
        }else{
            passeord.removeClass('INVALIC_ERROR');
            $('#msg_password_d').text(null);
        }

        if(Confir_passeord.val() == ""){
            Confir_passeord.addClass('INVALIC_ERROR');
            $('#msg_password').text('Debe confirmar el password');
            $puedoPasar++;
        }else{
            Confir_passeord.removeClass('INVALIC_ERROR');
            $('#msg_password').text(null);
        }

        if(tipoUsuario.find(':selected').val() == ""){
            tipoUsuario.addClass('INVALIC_ERROR');
            $('#msg_permisos').text('Debe seleccionar un tipo de usuario');
            $puedoPasar++;
        }else{
            tipoUsuario.removeClass('INVALIC_ERROR');
            $('#msg_permisos').text(null);
        }

        if( $puedoPasar == 0){

            var subaccion = ( $('#accionUsuario').prop('dataset').id == '0') ? 'nuevo' : 'modificar';

            var parametros = {
                'ajaxSend'  : 'ajaxSend',
                'accion'    : 'nuevoUpdateUsuario',
                'subaccion' : subaccion ,
                'idUsuario' : ( $('#accionUsuario').prop('dataset').id == '0') ? 0 : $('#accionUsuario').prop('dataset').id,

                'doctor'      : $('#usu_doctor').find(':selected').val(),
                'usuario'     : $('#usu_usuario').val(),
                'passwords'   : encrytar_base64( $('#usu_password').val() ) +'-'+ $('#usu_password').val(),
                'tipoUsuario' : $('#tipoUsuario').find(':selected').val(),

                'permisos': {
                    'consultar' : $('#chek_consultar').prop('checked'),
                    'agregar'   : $('#chek_agregar').prop('checked'),
                    'modificar' : $('#chek_modificar').prop('checked'),
                    'eliminar'  : $('#chek_eliminar').prop('checked'),
                }

            };

            if( subaccion == 'nuevo' ){

                //de comprueba de NUEVO  el usuario ANTES de crearlo
                if( UsuarioOdctor( $('#usu_doctor') ) == 0 ){

                    $.ajax({
                        url: $DOCUMENTO_URL_HTTP + '/application/system/configuraciones/controller/conf_controller.php',
                        type:'POST',
                        data: parametros,
                        dataType:'json',
                        async:false,
                        success: function(resp){

                            if(resp.error == ''){
                                notificacion('Información Actualizada', 'success');
                                reloadPagina();
                            }else{
                                notificacion( resp.error , 'error');
                            }
                        }
                    });

                }else{
                    notificacion('Ya tiene Usuario Asignado', 'error');
                }
            }

            if( subaccion == 'modificar'){

                $.ajax({
                    url: $DOCUMENTO_URL_HTTP + '/application/system/configuraciones/controller/conf_controller.php',
                    type:'POST',
                    data: parametros,
                    dataType:'json',
                    async:false,
                    success: function(resp){

                        if(resp.error == ''){
                            notificacion('Información Actualizada', 'success');
                            reloadPagina();
                        }else{
                            notificacion( resp.error , 'error');
                        }
                    }
                });

            }

        }

    });




    $('#usu_doctor').select2({
        placeholder: 'seleccione un doctor',
        allowClear:true,
        language:'es'
    });
    $('#tipoUsuario').select2({
        placeholder: 'seleccione un tipo de Usuario',
        allowClear:true,
        language:'es'
    });
    $('#especialidad_doct').select2({
        dropdownParent: $('#modal_conf_doctor')
    });

    $('#rucedula_doct').mask("000000000-0",{placeholder:"_________-_"});
    $('#celular_doct').mask("000 000 0000",{placeholder:"___ ___ ____"});
    $('#TelefonoConvencional_doct').mask("0#");

}



$(document).ready(function() {
    list_odontologos('A');
});

window.onload =     boxloading($boxContentConfiguracion, true);

window.addEventListener("load", function() {
    boxloading($boxContentConfiguracion, true,1000);
});