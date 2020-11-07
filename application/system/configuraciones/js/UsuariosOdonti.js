

/**Obtiene el parametro x la url con javascript*/
let UrlGet = new URLSearchParams(location.search);
$dataPerfiles               = [];
$idUsersEntity              = "";
$idPerfilAsociadoEntity     = "";


/**Form validacion*/
var FormValidacion = function(input, revalidate = false) {

    var valid = 0;

    var ErrorMsgDoctor              = $("#msg_doctorUsuario"); //doctor ya esta asociado a un usuario
    var ErrMsgUsuarioEnUsu          = $("#msg_usuario_repit"); //cuando el usuario esta repetido

    var vacio = /^\s*$/;
    var espacios_blanco

    //reautoValidar Todos los Inputs   ///*****************////
    if(revalidate==false){

        //valido el usuario si en caso esta repetido
        if($("#usu_usuario").attr("name")=="usu_usuario"){
            if( vacio.test($("#usu_usuario").val()) ){
                $('#msg_usuario_repit').text('campo requerido');
                valid++;
            }else{
                $('#msg_usuario_repit').text(null);
            }

            // if($("#usu_usuario").val().indexOf(" ")){
            //     $('#msg_usuario_repit').text('No puede agregar espacios en blanco');
            //     valid++;
            // }

            if(!vacio.test($("#usu_usuario").val()) && $("#usu_usuario").val() != ""){
                if(usuarioEnUsu()==false){
                    valid++;
                }
            }
        }


        //valido el odontologo seleccionado si en caso tiene usuario asociado
        if($("#usu_doctor").attr("name")=="usu_doctor"){
            if($("#usu_doctor").find(':selected').val()==""){
                $("#msg_doctorUsuario").text("campo requerido");
                valid++;
            }else{
                if(Odontolusuario()==false){
                    ErrorMsgDoctor.text("usuario asociado");
                    valid++;
                }
            }
        }

        if( vacio.test($("#usu_password").val())  &&  vacio.test($("#usu_confir_password").val())){
            $("#msg_password_d2").text("campo requerido");
            valid++;
        }else{
            if( $("#usu_password").val() != $("#usu_confir_password").val() ){ //valida el password
                $("#msg_password_d2").text("password Incorrecto");
                valid++;
            }else{
                if($("#usu_password").val() == $("#usu_confir_password").val())
                    $("#msg_password_d2").text(null);
            }
        }


        if($("#tipoUsuarioPerfil").find(":selected").val()==""){
            valid++;
            $("#msg_permisos").text("Debe selecionar un Perfil"); //Mensaje de perfiles error
        }else{
            $("#msg_permisos").text(null);
        }

    }


    //reautoValidar Inputs independiente  ///*****************////
    if(revalidate==true){


        if(input.attr("name")=="usu_usuario"){

            if(vacio.test(input.val())){
                $('#msg_usuario_repit').text("campo requerido");
                valid++;
            }

            // if(input.val().indexOf(" ")){
            //     $('#msg_usuario_repit').text('No puede agregar espacios en blanco');
            //     valid++;
            // }

            if(!vacio.test(input.val()) && input.val()!=''){
                if(usuarioEnUsu()==false){
                    $('#msg_usuario_repit').text("usuario en uso");
                    valid++;
                }
            }
        }


        if(input.attr("name")=="usu_doctor"){
            var validMsg = 0;
            if(input.find('option:selected').val() == ""){
                ErrorMsgDoctor.text("Campo requerido");
                validMsg++;
                valid++;
            }
            if(Odontolusuario()==false){
                ErrorMsgDoctor.text("usuario asociado");
                valid++;
                validMsg++;
            }

            if(validMsg==0)
                ErrorMsgDoctor.text(null);
        }

        if(input.attr("name") == "usu_password"){ //password
            if($("#usu_password").val().replace(' ','')==""){
                $("#msg_password_d2").text("campo requerido");
                valid++;
            }else{
                $("#msg_password_d2").text(null);
            }
        }

        if(input.attr("name") == "usu_password" || input.attr("name") == "usu_confir_password"){ //confirmar password
            if( $("#usu_password").val() != $("#usu_confir_password").val() ){
                $("#msg_password_d2").text("password Incorrecto");
                valid++;
            }else{
                if($("#usu_password").val() == $("#usu_confir_password").val())
                $("#msg_password_d2").text(null);
            }
        }

    }



    if(valid>0)
        return false;
    if(valid==0)
        return true;

};


var FormValidationPerfil = function(revalidate) {

    var RgxVacio                =  new RegExp((/^\s*$/));
    var subaccion       = $("input[name='idperfilUsersEntity']").prop("dataset").subaccion_perfil;
    var valid           = 0;
    var perfil          = $("#nomPerfil");
    var PermisosModules = $(".elemenErrorPermisosModule");
    var document_error  = [];

    if( RgxVacio.test(perfil.val()) ){
        document_error.push({
            "document": perfil ,
            "msg_error" : "Campo requerido"
        });

        valid++;
    }

    if( !RgxVacio.test(perfil.val()) ){

        if(ValidarTituloPerfil()==false){

            document_error.push({
                "document": perfil ,
                "msg_error" : "Nombre del perfil ya asignado"
            });
            valid++;
        }
    }


    if(revalidate==true){

        if( $(".checked_module").is(":checked") == false ){

            document_error.push({
                "document": PermisosModules ,
                "msg_error" : "No se detecto Ningún permiso Asociado"
            });

            valid++;

        }
    }

    $(".msg_err_perfil").remove();

    for (var i=0;i<=document_error.length-1;i++){

        var elementMsg      = document.createElement("small");
        var object          = document_error[i];

        $(elementMsg)
            .addClass("msg_err_perfil")
            .text(object['msg_error'])
            .css('color', 'red')
            .insertAfter(object['document']);
    }

    if(valid>0)
        return false;
    if(valid==0)
        return true;

};

ValidarTituloPerfil = function() {

    var valid           = true;
    var perfil          = $("#nomPerfil");

    var namePerfil      = perfil.val();
    var sub             = $("input[name='idperfilUsersEntity']").prop("dataset").subaccion_perfil;
    var nameperfilMod   = $("input[name='idperfilUsersEntity']").prop("dataset").nameperfil;

    if(sub=="Nuevo"){
        namePerfil = perfil.val();
    }
    if(sub=="Modificar"){
        if( (perfil.val()).replace((/\s+/g),'') == (nameperfilMod).replace((/\s+/g),'') ) {
            return true;
        }
    }

    $.ajax({
        url: $DOCUMENTO_URL_HTTP + '/application/system/configuraciones/controller/conf_controller.php',
        type:'POST',
        data:{
            'ajaxSend':'ajaxSend',
            'accion':'validtitulo_perfil',
            'namePerfil': (perfil.val()).replace((/\s+/g),'')
        },
        dataType:'json',
        async:false,
        success: function (respuesta) {
            if(respuesta['err']!=''){
                valid = false;
            }
        }
    });

    return valid;
};


function CargarUsuarioInfo()
{

    if( $('#usuariolistinfo').length > 0 )
    {

        $('#usuariolistinfo').DataTable({
        searching: true,
        ordering:false,
        destroy:true,
        ajax:{
            url: $DOCUMENTO_URL_HTTP + '/application/system/configuraciones/controller/conf_controller.php',
            type:'GET',
            data:{'ajaxSend':'ajaxSend', 'accion':'infoUsuarioOdontic', 'cual': 'list'},
            dataType:'json',
        },
        columnDefs:[
            {
                targets:0 ,
                render: function (data , type , full , meta) {

                    var menu        = "";
                    var idusers     = full[6];

                    menu += "<div class='dropdown'>";
                        menu += "<button class='btn btnhover btn-xs dropdown-toggle' type='button' data-toggle='dropdown' > <i style='padding-top: 3px; padding-bottom: 3px' class=\"fa fa-ellipsis-v\"></i> </button> ";

                        menu += "<ul class=\"dropdown-menu\"> ";
                            menu += "<li> <a style='cursor: pointer;' href='"+$DOCUMENTO_URL_HTTP+"/application/system/configuraciones/index.php?view=form_gestion_odontologos_especialidades&v=users&mod=true&id="+full[6] +"'> Modificar  </a> </li>";

                            // INACTIVAR
                            if(full[4] == 'A'){
                                menu += "<li> <a style='cursor: pointer; ' onclick='statusUserslogin("+idusers+",\"E\")'> Inactivar  </a> </li>";
                            }
                            // ACTIVAR
                            if(full[4] == 'E'){
                                menu += "<li> <a style='cursor: pointer; ' onclick='statusUserslogin("+idusers+", \"A\")' > Activar  </a> </li>";
                            }
                            
                            // menu += "<li> <a style='cursor: pointer; font-size: 1.1rem;'> Eliminar </a> </li>";

                        menu += "</ul>";
                    menu += "</div>";

                    console.log(full);
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
    });
    }
}


function statusUserslogin( idusers = 0, status ) {

    boxloading($boxContentConfiguracion,true);

    if(idusers != 0){

        var paramtros = {
            "accion"   : "status_update_users",
            "ajaxSend" : "ajaxSend" ,
            "idlogin"  : idusers ,
            "status"   : status
        };

        var url = $DOCUMENTO_URL_HTTP + '/application/system/configuraciones/controller/conf_controller.php';

        $.get(url , paramtros, function(data) {

            var respuesta = $.parseJSON(data);
            if(respuesta['error'] == ''){
                notificacion("información Actualizada", "success");
                CargarUsuarioInfo();
            }

            boxloading($boxContentConfiguracion,true,1000);

        });

    }else{
        notificacion('Ocurrio un error', 'error');
    }

}


function keyConfirmarPassword()
{
    if( $('#usu_password').val() != $('#usu_confir_password').val())
    {
        $('#msg_password').text('Password Incorrecto');
        $('#usu_confir_password').addClass('INVALIC_ERROR');
        $('#nuevoUpdateUsuario').addClass('disabled_link3').attr('disabled', true);
    }else{
        $('#msg_password').text(null);
        $('#usu_confir_password').removeClass('INVALIC_ERROR');
        $('#nuevoUpdateUsuario').removeClass('disabled_link3').attr('disabled', false);
    }

    if( $('#usu_password').val() != '' ){
        $('#usu_password').removeClass('INVALIC_ERROR');
        $('#msg_password_d').text(null);
    }
}
function passwordMostrarOcultar( por )
{
    if(por == 'mostrar'){
        $('#usu_password').attr('type','text');
        $('#usu_confir_password').attr('type','text');
    }
    if(por == 'ocultar'){
        $('#usu_password').attr('type','password');
        $('#usu_confir_password').attr('type','password');
    }
}


$('#tipoUsuarioPerfil').change(function() {

    if(FormValidacion(null, false)==false)
        $('#nuevoUpdateUsuario').addClass('disabled_link3');
    else
        $('#nuevoUpdateUsuario').removeClass('disabled_link3');


});


/**Nuevo Update Usuario */

$('#nuevoUpdateUsuario').on('click', function(){


    var $puedoPasar = 0;

    var doctor     = $("#usu_doctor");
    var usuario    = $("#usu_usuario");
    var passeord   = $("#usu_password");
    var Confir_passeord = $("#usu_confir_password");
    var tipoUsuario = $("#tipoUsuarioPerfil");

    // alert(FormValidacion(null, false));

    if(FormValidacion(null, false)==false)
        return false;

    var subaccion = "";
    if (UrlGet.get("creat") == "true")
        subaccion = "nuevo";
    if(UrlGet.get("mod")=="true") {
        subaccion = "modificar";
    }


    if( $puedoPasar == 0){

        boxloading($boxContentConfiguracion,true);


        var parametros = {
            'ajaxSend'         : 'ajaxSend',
            'accion'           : 'nuevoUpdateUsuarioData',
            'subaccion'        :  subaccion,
            'doctor'           : $('#usu_doctor').find(':selected').val(),
            'usuario'          : $('#usu_usuario').val(),
            'passwords'        : encrytar_base64( $('#usu_password').val() ) ,
            'tipoUsuario'      : $('#tipoUsuarioPerfil').find(':selected').val(),
            'fk_perfil_entity' : $('#tipoUsuarioPerfil').find(':selected').val(),
            'idUsuario'        : (UrlGet.get("id")=="")?0:UrlGet.get("id"),
            'idEntityusers'    : $idUsersEntity

        };

        $.ajax({
            url: $DOCUMENTO_URL_HTTP + '/application/system/configuraciones/controller/conf_controller.php',
            type:'POST',
            data: parametros,
            dataType:'json',
            async:false,
            cache: false ,
            error:function(xhr, status){
                if(xhr['status']=='200'){
                    boxloading($boxContentConfiguracion,false,1000);
                }else{
                    if(xhr['status']=='404'){
                        notificacion("Ocurrió un error con la <b>nuevo Update Usuario</b> <br> <b>xhr: "+xhr['status']+" <br> Consulte con Soporte </b>");
                    }
                    boxloading($boxContentConfiguracion,false,1000);
                }
            },
            complete:function(xhr, status) {

                if(xhr['status']=='200'){
                    boxloading($boxContentConfiguracion,false,1000);
                }else{
                    if(xhr['status']=='404'){
                        notificacion("Ocurrió un error con la <b>nuevo Update Usuario</b> <br> <b>xhr: "+xhr['status']+" <br> Consulte con Soporte </b>");
                    }
                    boxloading($boxContentConfiguracion,false,1000);
                }
            },
            success:function(r) {
                if(r.error == ''){
                    notificacion('Información Actualizada', 'success');
                    setTimeout(function() {
                        window.location = $DOCUMENTO_URL_HTTP +  "/application/system/configuraciones/index.php?view=form_gestion_odontologos_especialidades&v=users&list=true";
                    },1000);
                }else{
                    notificacion(r.error, 'error');
                }

                boxloading($boxContentConfiguracion,false,1000);
            }

        });
    }
});



//Guardar Perfil  entity  entidades
$('#guardarPerfil').click(function() {

    var url          = $DOCUMENTO_URL_HTTP + '/application/system/configuraciones/controller/conf_controller.php';
    var error        = 0;
    var Perfil       = $("#nomPerfil");
    var ModulosPerms = fetchpermissCheck(false);

    console.log(ModulosPerms);

    if(Perfil.val()==""){
        error++;
    }

    if(FormValidationPerfil(null)==false){
        error++;
    }
    if(FormValidationPerfil(true)==false){
        error++;
    }

    if(error>0)
        return false;

    var FormDataArray = new FormData();

    /*var parametro = {
        "permisosM"       : ModulosPerms,
        "perfil_nom"      : Perfil,
        "accion"          : "nuevoPerfil",
        "ajaxSend"        :"ajaxSend",
        "idEntity"        : $idUsersEntity,
        "subaccion"       : $("input[name='idperfilUsersEntity']").prop("dataset").subaccion_perfil,
        "idPerfilSelect"  : $("#tipoUsuarioPerfil").find(":selected").val() ,
    };*/

    FormDataArray.append("accion", "nuevoPerfil");
    FormDataArray.append("ajaxSend", "ajaxSend");
    FormDataArray.append("idEntity", $idUsersEntity);
    FormDataArray.append("perfil_nom", Perfil.val());
    FormDataArray.append("permisosM", JSON.stringify(ModulosPerms));
    FormDataArray.append("idPerfilSelect", $("#tipoUsuarioPerfil").find(":selected").val() );
    FormDataArray.append("subaccion", $("input[name='idperfilUsersEntity']").prop("dataset").subaccion_perfil);

    // NuevoPerfiladd(FormData);

    $.ajax({
        url: $DOCUMENTO_URL_HTTP + '/application/system/configuraciones/controller/conf_controller.php' ,
        type: 'POST',
        cache: false,
        data: FormDataArray,
        dataType: 'json',
        async:false,
        processData: false,
        contentType: false,
        success: function (resp)
        {
            if(resp['error']==''){
                notificacion("Perfil Actualizado", "success");
                $("#modalCreatePerfil").modal('hide');
                fetchPerfiles();
            }else{
                notificacion(resp['error'], 'error');
            }
        }
    });

    if(error==0){

        /*
        $.ajax({
            url: url ,
            type: "POST",
            dataType: "json",
            data: parametro,
            async: false,
            processData: false,
            contentType: false
            success:function (resp) {
                if(resp['error']==''){
                    notificacion("Perfil Actualizado", "error");
                    fetchPerfiles();
                }else{

                }
            }
        });*/

    }else{

    }

    
});


$("#usu_usuario").keyup(function() {
    if (!/^[ a-z0-9áéíóúüñ]*$/i.test(this.value)) {
        this.value = this.value.replace(/[^ a-z0-9áéíóúüñ]+/ig,"");
    }
    if((/^\s+|\s+$/g).test(this.value)){
        this.value = this.value.replace(/^\s+|\s+$/g,"");
    }
});


//Obtener Usuario  Para  Modificar
function fetchUsuarioUpdate( id_usu )
{

    if( id_usu != '')
    {

        $.ajax({
            url: $DOCUMENTO_URL_HTTP + '/application/system/configuraciones/controller/conf_controller.php',
            type:'POST',
            data: {'ajaxSend':'ajaxSemd', 'accion':'fech_usuariodoct', 'id':id_usu},
            dataType:'json',
            async:false,
            success: function(resp){


                if( resp['error'] == ""){


                    var obj = resp['object'];
                    var doctor               = obj.fk_doc;
                    var usuario              = obj.usuario;
                    var password             = obj.passwor_abc;
                    var confir_password      = obj.passwor_abc;
                    var tipousuario          = obj.tipo_usuario; /** Tipo de usuario o perfil de la entidad */
                    // var permisos             = JSON.parse(obj.permisos);
                    var idcedula             = obj.cedula;

                    $idUsersEntity                 = resp['identity'];  /** id login desde la entidad db*/
                    $idPerfilAsociadoEntity        = resp['idPerfilEntity']; /** id de perfil de entidad asociado al usuario a modificar*/


                    $('#tipoUsuarioPerfil').val(tipousuario).trigger('change');

                    // $("#chek_consultar").prop('checked', ( permisos.consultar == "true" ) ? true : false);
                    // $("#chek_agregar").prop('checked'  , ( permisos.agregar == "true" ) ? true : false);
                    // $("#chek_modificar").prop('checked', ( permisos.modificar == "true" ) ? true : false);
                    // $("#chek_eliminar").prop('checked' , ( permisos.eliminar == "true" ) ? true : false);


                    $("#usu_doctor").val( doctor ).trigger('change').prop('disabled', true);
                    $('#usu_usuario').val( usuario );
                    $('#usu_password').val( descrytar_base64(password) );
                    $('#usu_confir_password').val( descrytar_base64(confir_password) );
                    $('#idcedusu').attr('data-idcedula', idcedula); //id de la cedula de usuario
                    $('#idcedusu').attr('data-nomusers', usuario);

                }else{

                    notificacion( resp['error'] , 'error' );

                    $("#nuevoUpdateUsuario").addClass("disabled_link3").attr("disabled", true);
                    $("#tipoUsuarioPerfil").addClass("disabled_link3").attr("disabled", true);
                    $("#addPerfilUsers").addClass("disabled_link3").attr("disabled", true);
                    $("#deletePerfilUsers").addClass("disabled_link3").attr("disabled", true);
                }
            }

        });

    }else{

        $("#nuevoUpdateUsuario").addClass("disabled_link3").attr("disabled", true);
        $("#tipoUsuarioPerfil").addClass("disabled_link3").attr("disabled", true);
        $("#addPerfilUsers").addClass("disabled_link3").attr("disabled", true);
        $("#deletePerfilUsers").addClass("disabled_link3").attr("disabled", true);
    }
}


function usuarioEnUsu(){

    var sub   = null;

    if(UrlGet.get("creat"))
        sub = "crear";
    if(UrlGet.get("mod"))
        sub = "mod";


    var msg_error = $('#msg_usuario_repit');
    var url = $DOCUMENTO_URL_HTTP + '/application/system/configuraciones/controller/conf_controller.php';
    var paramtros = {
        'ajaxSend'    :'ajaxSend',
        'accion'      :'consultar_usuario',
        'idcedula'    : $("#idcedusu").prop('dataset').idcedula ,
        'nameUsuario' : $("#usu_usuario").val() ,
        'subaccion'   : sub,
    };

    var valid = true;

    if(sub=="crear")
        consultarusersdoct();
    else
        if( ($("#usu_usuario").val()).replace(/ /g,"") != ($("#idcedusu").prop("dataset").nomusers).replace(/ /g,"") )
            consultarusersdoct();


    function consultarusersdoct(){
        $.ajax({
            type: "GET" ,
            url: url ,
            data: paramtros ,
            dataType: "json",
            async:false,
            success: function (r) {
                if(r.error != ''){
                    msg_error.html(r.error);
                    valid = false;
                } else{
                    msg_error.html(r.error);
                    valid = true;
                }
            }
        });
    }

    return valid;

}

function Odontolusuario() {

    var valid = true;

    var url = $DOCUMENTO_URL_HTTP + '/application/system/configuraciones/controller/conf_controller.php';


    if(UrlGet.get("creat")=="true")
        consultarusuariorevlid();


    function consultarusuariorevlid() {
        $.ajax({
            type: "GET" ,
            url: url ,
            data: {
                "accion": "odontolusuario" ,
                "ajaxSend" : "ajaxSend"  ,
                "odontol": $("#usu_doctor").find(":selected").val()
            } ,
            dataType: "json",
            async:false,
            success: function (r) {
                if(r.error != ""){
                    valid = false;
                } else{
                    valid = true;
                }
            }
        });
    }

    return valid;
}


/**Esta funcion esta conectada directamente a las entidades de las clinicas
 * solo fetch informacion las entidad de la sesion*/

function fetchPerfiles( $id = 0){

    clearSelect($('#tipoUsuarioPerfil'));
    $dataPerfiles = [];
    
    var Perfiles = [];
    var url = $DOCUMENTO_URL_HTTP + '/application/system/configuraciones/controller/conf_controller.php';

    $.ajax({
        url: url,
        type:"POST",
        dataType:"json",
        data:{
            "accion":"fetchPerfilesUsuariosEntity",
            "ajaxSend":"ajaxSend",
            "id": $id
        },
        async:false,
        success:function(resp) {

            if(resp.error == ""){

                $.each(resp["permisos_perfil"], function(i, item) {
                    $dataPerfiles.push(item);
                    Perfiles.push({
                        "id":item["select_perfil"]["id"],
                        "text": item["select_perfil"]["text"]
                    });
                });

            }else{

            }
        }
    });

    // console.log(Perfiles);
    // alert($idPerfilAsociadoEntity);

    $('#tipoUsuarioPerfil').select2({
        placeholder: 'Seleccione un Perfil',
        allowClear: true,
        data: Perfiles,
        dropdownParent: $('.box-body')
    });

    $('#tipoUsuarioPerfil').val($idPerfilAsociadoEntity).trigger('change');

}

function viewactionpermsis(el, bool) {

    if(bool==true){
        if(el.prop("dataset").thijo==1){

            var mod = '.'+el.attr("id");

            /*
            alert(mod);
            console.log($(mod));*/

            if($(mod).parent("tr").hasClass("noneMod")==true)
                $(mod).parent("tr").removeClass("noneMod");
            else
                $(mod).parent("tr").addClass("noneMod");

        }
    }

    if(bool==false){

        var check       = el.parents("tr");
        var checkeds    = check.find(".checked_module");

        var consultar   = check.find(".consultar");
        var agregar     = check.find(".agregar");
        var modificar   = check.find(".modificar");
        var eliminar    = check.find(".eliminar");

        var puedcheck   = 0;

        if(consultar.is(":checked")==true)
            puedcheck++;
        if(agregar.is(":checked")==true)
            puedcheck++;
        if(modificar.is(":checked")==true)
            puedcheck++;
        if(eliminar.is(":checked")==true)
            puedcheck++;

        console.log(checkeds.is(":checked"));

        if( checkeds.is(":checked") == true ){
            checkeds.prop("checked",false);
        }else{
            checkeds.prop("checked",true);
        }
    }


}

//obtengo los permisos
function fetchpermissCheck(boolCheckFalse = false){

    var data = [];

    if(boolCheckFalse==false){

        $(".checked_module").each(function() {

            var checkModule = $(this);
            if( checkModule.is(":checked") ){

                var idPermisoAction = checkModule.prop("dataset").idpermiso;
                var idModule        = checkModule.prop("dataset").moduleid;

                data.push({
                    'idmodule' : idModule , 'idPermiso' : idPermisoAction
                });

            }
        });

        return data;

    }

    if(boolCheckFalse == true){

        $(".checked_module").each(function() {

            var checkModule = $(this);
            var idPermisoAction = checkModule.prop("dataset").idpermiso;
            var idModule        = checkModule.prop("dataset").moduleid;
            data.push({
                'idmodule' : idModule , 'idPermiso' : idPermisoAction
            });
        });

        return data;

    }


}

$(".checked_module").change(function() {
    FormValidationPerfil(true);
});

//Eliminar Perfil
// $().


//Add Perfil entity usuario
$("#addPerfilUsers").on("click", function() {

    $("#nomPerfil").val(null);
    $(".checked_module").prop("checked", false);
    $(".active").prop("checked", false).change();
    $(".active").attr("disabled", false);
    $("input[name='idperfilUsersEntity']").val(null).attr("data-subaccion_perfil","Nuevo");
    $('[name="titleperfil"]').text("Nuevo Perfil");
    $("#tipoUsuarioPerfil").val(null).trigger("change");
    $("input[name='idperfilUsersEntity']").attr("data-nameperfil", "" );
    // $('[name="cuenta[0].masiva"]')

});


//modificar perfil de ese clinica o entidad x usuario
$("#modificarPerfilUsers").on("click", function() {

    console.log($dataPerfiles);

    var idPerfilAsociad = $("#tipoUsuarioPerfil").find(":selected").val();

    if(idPerfilAsociad>0){

        $('[name="titleperfil"]').text("Modificar Perfil");
        $("input[name='idperfilUsersEntity']").val(idPerfilAsociad).attr("data-subaccion_perfil","Modificar");
        $("input[name='idperfilUsersEntity']").attr("data-nameperfil", $("#tipoUsuarioPerfil").find(":selected").text() );

        $(".active").attr("disabled", true);
        $(".checked_module").prop("checked", false);

        $.each($dataPerfiles , function (i, item) {

            if( item['idPerfil'] == idPerfilAsociad ){

                $("#nomPerfil").val( item['Perfil'] );

                $.each( item['Permiss'], function(i2, item2) {

                    var idModuleMod     = item2['idModulo'];
                    var idPermisoMod    = item2['idPermiso'];

                    $(".checked_module").each(function() {

                        var checkModule = $(this);
                        var idPermisoAction = checkModule.prop("dataset").idpermiso;
                        var idModule        = checkModule.prop("dataset").moduleid;

                        if(idModuleMod == idModule && idPermisoMod == idPermisoAction){
                            checkModule.prop("checked", true);
                        }


                    });

                });

            }

        });

        $("#modalCreatePerfil").modal("show");

        setTimeout(()=>{ $('#nomPerfil').trigger('keyup');  },500);

    }else{
        notificacion("Debe selecionar un perfil antes de modificar", "error");
    }



});



$(document).ready(function() {

    CargarUsuarioInfo();

    $('#usu_doctor').select2({
        placeholder: 'Odontolog@',
        allowClear: true,
    });

    //Modificar
    if(UrlGet.get('mod')){
        if((UrlGet.get('id'))){
            fetchUsuarioUpdate( UrlGet.get('id') );
        }
    }

    fetchPerfiles();

    //Create
    //si en caso esta con create y tiene el id
    if(UrlGet.get('creat')){
        if((UrlGet.get('id'))){
            $('.row').eq(1).attr('disabled', true).addClass('disabled_link3');
        }
    }

});



function encrytar_base64(dato) {
    return btoa(dato);
}
function descrytar_base64(dato) {
    return atob(dato);
}

function clearSelect(el){
    el.empty();
    el.html('<option value=""></option>');
}


window.onload = boxloading($boxContentConfiguracion,true);

window.addEventListener("load", function() {

    boxloading($boxContentConfiguracion,false, 1000);

    FormValidacion(null, false);

});