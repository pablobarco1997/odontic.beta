

var $TieneImagenAsociada = false;

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

                if($.trim( data['icon'] ) != "")
                {

                    $('#fileIcon').css('display',  'none');
                    var img = document.createElement('img'); //creo el elmento img
                    img.setAttribute('width', '140px'); //agrego attr y css
                    img.setAttribute('height', '140px'); //agrego setAttribute y css
                    img.setAttribute('class', 'iconpaciente'); //agrego setAttribute y css
                    img.classList.add('img-circle'); //agrego setAttribute y css

                    //$HTTP_DIRECTORIO_ENTITY esta variable global de js contiene el directorio si alguna vez fue creado
                    img.setAttribute('src', $DOCUMENTO_URL_HTTP + '/logos_icon/' + $HTTP_DIRECTORIO_ENTITY + '/' + data['icon']);
                    $('#imgpaciente').append(img);
                    $('#imgClasica').remove(); //remuevo la img anterior

                    $TieneImagenAsociada = true;
                }else{
                    $TieneImagenAsociada = false;
                }
                document.getElementById('tituloInfo').scrollIntoView(); //me recorre hacia ese id
            }
            if(resp['error'] != '')
            {
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

    boxloading($boxContentViewAdminPaciente, true);

    e.preventDefault();

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
        async: false,
        contentType: false,
        processData: false,
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
        },
        success:function(resp) {
            if (resp.error == '') {
                notificacion("Informcaión Actualizada", "success");
                setTimeout(function() {location.reload(true);},1000)
            }else{
                notificacion(resp.error, 'error');
            }
            boxloading($boxContentViewAdminPaciente,false,1000);
        }

    });

    // alert($id_paciente);

});

function  invaliUpdateDatosPaciente()
{
    var cont = 0;

    if($('#nombre').val() == ''){
        cont++;
        $('#nombre').focus();
        $('#noti_nombre').text("ingrese el nombre del paciente");
    }else{
        $('#noti_nombre').text(null);
    }


    if($('#apellido').val() == ''){
        cont++;
        $('#apellido').focus();
        $('#noti_apellido').text("ingrese el apellido del paciente");
    }else{
        $('#noti_apellido').text(null);
    }

    if($('#rud_dni').val() == ''){
        cont++;
        $('#rud_dni').focus();
        $('#noti_ruddni').text("ingrese un ruc o cedula del paciente");
    }else{
        $('#noti_ruddni').text(null);
    }



    if(cont>0){
        return false;
    }else{
        return true;
    }
}

function invalicEmailText(el , val)
{
    if(val==true){

        var email = $(el).val();
        const re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;

        if( !re.test(email) )
        {
            $('#noti_email').text('Email incorrecto');
        } else {
            $('#noti_email').text(null);
        }

    }
}

function invalicrucCedula(el, val)
{
    //solo numeros
    if(val==true){
        el.value = el.value.replace(/\D/g, '');
    }

    var puedepasar   = 0;
    var input_rucced =  $(el).val();
    var msg_error    =  $('#noti_ruddni');

    var url = $DOCUMENTO_URL_HTTP +'/application/system/pacientes/nuevo_paciente/controller/nuevo_pacit_controller.php';
    var parametros = { 'ajaxSend':'ajaxSend', 'accion':'validarCedulaRuc', 'ruc_ced': input_rucced };

    $.get( url , parametros , function(data) {
        var rs = $.parseJSON(data);
        if(rs.error != ''){
            msg_error.text('Este numero se encuentra repetido');
            $('#submit').addClass('disabled_link3');
            puedepasar++;
        }else{
            msg_error.text(null);
            $('#submit').removeClass('disabled_link3');
            puedepasar=0;
        }
    });

    return puedepasar;
}

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
