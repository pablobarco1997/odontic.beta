

var $TieneImagenAsociada = false;


var FormvalidPacienteMod = function(input = false){

    var valid = false;
    var ErroresData = [];

    const errmail = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/

    var nom      = $("#nombre");
    var ape      = $("#apellido");
    var ci       = $("#rud_dni");
    var ciud     = $("#ciudad");
    var direc    = $("#direcc");
    var t_movil  = $("#t_movil");
    var email    = $("#email");

    if(email.val()!=""){
        if(!errmail.test(email.val())){
            ErroresData.push({
                'document' : email,
                'text' : 'Email Icorrecto'
            });
        }
    }if(nom.val()==""){
        ErroresData.push({
            'document' : nom,
            'text' : 'Campo Obligatorio'
        });
    }if(ape.val()==""){
        ErroresData.push({
            'document' : ape,
            'text' : 'Campo Obligatorio'
        });
    }if(ci.val()==""){
        ErroresData.push({
            'document' : ci,
            'text' : 'Campo Obligatorio'
        });
    }else{
        ci.val(ci.val().replace(/[^0-9]/g, ''));
    }
    if(ciud.val()==""){
        ErroresData.push({
            'document' : ciud,
            'text' : 'Campo Obligatorio'
        });
    }if(direc.val()==""){
        ErroresData.push({
            'document' : direc,
            'text' : 'Campo Obligatorio'
        });
    }
    if(t_movil.val()==""){
        ErroresData.push({
            'document' : t_movil,
            'text' : 'Campo Obligatorio'
        });
    }else{
        t_movil.val(t_movil.val().replace(/[^1-9]/g, ''));
        if(t_movil.val().length!=9){
            ErroresData.push({
                'document' : t_movil,
                'text' : 'numero invalido'
            });
        }
    }

    $(".msg_error_add_paciente").remove();
    if(ErroresData.length>0){
        for (var i=0; i<=ErroresData.length-1;i++ ){
            var documento = ErroresData[i]['document'];
            var text      = ErroresData[i]['text'];
            var Msg       = document.createElement('small');
            $(Msg).addClass('msg_error_add_paciente').css('color', 'red');
            if(documento[0].localName=='select'){
                $(Msg).insertAfter(documento.parent().find('span:eq(0)')).text(text);
            }else{
                $(Msg).insertAfter(documento).text(text);
            }
        }
        valid = false;
    }else{
        valid = true;
    }
    return valid;


};

//OBTENER DATOS DE INFORMACION DEL PACIENTE
function obtenerDatosP($id)
{

    $.ajax({
        url: $DOCUMENTO_URL_HTTP +'/application/system/pacientes/pacientes_admin/controller/controller_adm_paciente.php',
        type:'POST',
        data: {'accion':'fetchPaciente','ajaxSend':'ajaxSend', 'id':$id},
        dataType:'json',
        async:false,
        // contentType: false,
        // processData: false,
        success:function(resp) {
            if(resp['error'] == '')
            {
                var data = resp['data'];
                $('#nombre').val(data['nombre']);
                $('#apellido').val(data['apellido']);
                $('#rud_dni').val(data['ruc_ced']);
                $('#email').val(data['email']);
                $('#convenio').val(data['fk_convenio']).trigger('change');
                $('#n_interno').val(data['numero_interno']);
                $('#sexo').val(data['sexo']).trigger('change');
                $('#fech_nacimit').val(data['fecha_nacimiento']);
                $('#ciudad').val(data['fk_ciudad']);
                $('#comuna').val(data['comuna']);
                $('#direcc').val(data['direccion']);
                $('#t_fijo').val(data['telefono_fijo']);
                $('#t_movil').val(data['telefono_movil']);
                $('#act_profec').val(data['actividad_profecion']);
                $('#empleado').val(data['empleador']);
                $('#obsrv').val(data['observacion']);
                $('#apoderado').val(data['apoderado']);
                $('#refer').val(data['referencia']);

                if($.trim( data['icon'] ) != ""){

                    $('#fileIcon').css('display',  'none');
                    var img = document.createElement('img'); //creo el elmento img
                    img.setAttribute('width', '140px'); //agrego attr y css
                    img.setAttribute('height', '140px'); //agrego setAttribute y css
                    img.setAttribute('class', 'iconpaciente'); //agrego setAttribute y css
                    img.classList.add('img-circle'); //agrego setAttribute y css

                    //$HTTP_DIRECTORIO_ENTITY esta variable global de js contiene el directorio si alguna vez fue creado
                    img.setAttribute('src', data['img_logo']);
                    $('#imgpaciente').append(img);
                    $('#imgClasica').remove(); //remuevo la img anterior

                    $TieneImagenAsociada = true;
                }else{
                    $TieneImagenAsociada = false;
                }
                // document.getElementById('tituloInfo').scrollIntoView(); //me recorre hacia ese id
            }
            if(resp['error'] != ''){
                notificacion(resp['error'], 'question');
            }
        }

    });
}

//comprobar si existe la url o fichero destino
function FileExisteUrlImg(url)
{
    var img = new Image();
    img.src = url;
    return img.height != 0;
}

//SUBIDA DE ICONO DEL PACIENTE
$('#file_icon').change(function(e){

    // var img = '<img src="" class="img-circle img-md img-sm" class="img-circle" width="107.16px" height="140px">';
    var $padre = $(this).parents('#imgpaciente');

    var fontIcon = $padre.find('#fileIcon');
    var Icon = $padre.find('#file_icon');

    if($(this).val()!= "") //cuando tenga valores
    {
        $TieneImagenAsociada = true;

        var iconpaciente = $padre.find(".iconpaciente");

        if(iconpaciente.length >0){
            iconpaciente.remove();
        }

        fontIcon.css('display', 'none');
        var img = document.createElement('img'); //creo el elmento img
        img.setAttribute('width', '140px'); //agrego attr y css
        img.setAttribute('height', '140px'); //agrego setAttribute y css
        img.setAttribute('class', 'iconpaciente'); //agrego setAttribute y css
        img.classList.add('img-circle'); //agrego setAttribute y css
        $padre.append(img); //lo agrego dentro del padre

        var iconpaciente = $padre.find(".iconpaciente");

        //compruebo si existe ese elemento
        if(iconpaciente.length > 0){
            SubirImagenes( this , iconpaciente , '');

            //si aparecece un mensaje de error entonces se ejecuta la funcion regresar
            if($('.swal2-show').length > 0 ){
                invalic_Icon_default();
            }

        }else{

        }

    }else{

        $TieneImagenAsociada = false;
        invalic_Icon_default();
    }

    if($(this).val()=="")
        $TieneImagenAsociada=false;



    // alert($TieneImagenAsociada);
    function  invalic_Icon_default(){

        var iconpaciente =  $padre.find('.iconpaciente');

        if(iconpaciente.length > 0)
            iconpaciente.remove();

        var im = '<img src="'+$DOCUMENTO_URL_HTTP+'/logos_icon/logo_default/avatar_none.ico" alt="" width="140px"  height="140px" class="iconpaciente img-circle" id="imgClasica">';
        $("#imgClasica").remove();
        $("#imgpaciente").append($(im));
        Icon.val(null);

    }

});

//UPDATE PACIENTES FORM_DATOPS_PACIENTE
$('#form_update_paciente').submit(function(e) {

    e.preventDefault();

    if(!ModulePermission("Datos Personales","modificar")){
        notificacion('Ud. No tiene permiso para Actualizar la Información del Paciente', 'error');
        return false;
    }

    button_loadding($(".idsubmitbtnUpdate"), true);
    boxloading($boxContentViewAdminPaciente, true);

    var formulario = $('#form_update_paciente');
    var form = new FormData(formulario[0]);
    form.append('ajaxSend','ajaxSend');
    form.append('accion','updatePaciente');
    form.append('TieneImage',$TieneImagenAsociada );
    form.append('id', $id_paciente);
    $.ajax({

        url: $DOCUMENTO_URL_HTTP +'/application/system/pacientes/pacientes_admin/controller/controller_adm_paciente.php',
        type:'POST',
        data: form,
        dataType:'json',
        contentType: false,
        processData: false,
        cache:false,
        async: true,
        error:function(xhr, status) {
            if(xhr['status']=='200'){
                boxloading($boxContentViewAdminPaciente,false,1000);
            }else{
                if(xhr['status']=='404'){
                    notificacion("Ocurrió un error con la <b>Datos Personales</b> <br> <b>xhr: "+xhr['status']+" <br> Consulte con Soporte </b>");
                }
                boxloading($boxContentViewAdminPaciente,false,1000);
            }
        },
        complete:function(xhr, status) {

            if(xhr['status']=='200'){
                boxloading($boxContentViewAdminPaciente,false,1000);
            }else{
                if(xhr['status']=='404'){
                    notificacion("Ocurrió un error con la <b>Datos Personales</b> <br> <b>xhr: "+xhr['status']+" <br> Consulte con Soporte </b>");
                }
                boxloading($boxContentViewAdminPaciente,false,1000);
            }
            button_loadding($(".idsubmitbtnUpdate"), false);
        },
        success:function(resp) {
            if (resp.error == '') {
                notificacion("Informcaión Actualizada", "success");
                setTimeout(function() {location.reload(true);},1000)
            }else{
                notificacion(resp.error, 'error');
            }
            boxloading($boxContentViewAdminPaciente,false,1000);
            button_loadding($(".idsubmitbtnUpdate"), false);
        }

    });

    // alert($id_paciente);

});


$(document).ready(function() {

    obtenerDatosP($id_paciente);

    $("#fech_nacimit").daterangepicker({
        drops: 'up',
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
        singleDatePicker: true,
        showDropdowns: true,
        autoclose: true,
        pickerPosition: "bottom-left"
    });


});

window.onload = boxloading($boxContentViewAdminPaciente ,true);

window.addEventListener("load", function() {
    boxloading($boxContentViewAdminPaciente ,false, 1000);
});
