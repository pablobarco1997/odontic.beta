
var FormValidationPerfilGlobal = function(el){

    var Errores = [];

    var perfil_usu                   = $("#perf_usu");
    var perfil_pass                  = $("#perf_passd");
    var perfil_nom                   = $("#perf_nom");
    var perfil_ape                   = $("#perf_apell");
    var perfil_cedul                 = $("#perf_cedula");
    var perfil_email                 = $("#perf_email");
    var perfil_especialida           = $("#especialidadPerfil");


    if(perfil_usu.val() == "" || (!/^\s/.test(perfil_usu.val())) == false){
        Errores.push({
            "documento" :   perfil_usu,
            "mesg" :  "Campo Obligatorio",
        });
    }
    if(perfil_pass.val() == "" || (!/^\s/.test(perfil_pass.val())) == false){
        Errores.push({
            "documento" :   perfil_pass,
            "mesg" :  "Campo Obligatorio",
        });
    }
    if(perfil_nom.val() == "" || (!/^\s/.test(perfil_nom.val())) == false){
        Errores.push({
            "documento" :   perfil_nom,
            "mesg" :  "Campo Obligatorio",
        });
    }
    if(perfil_ape.val() == "" || (!/^\s/.test(perfil_ape.val())) == false){
        Errores.push({
            "documento" :   perfil_ape,
            "mesg" :  "Campo Obligatorio",
        });
    }
    if(perfil_cedul.val() == "" || (!/^\s/.test(perfil_cedul.val())) == false){
        Errores.push({
            "documento" :   perfil_cedul,
            "mesg" :  "Campo Obligatorio",
        });
    }
    if(perfil_email.val() == "" || (!/^\s/.test(perfil_email.val())) == false){
        Errores.push({
            "documento" :   perfil_email,
            "mesg" :  "Campo Obligatorio",
        });
    }

    if(perfil_usu.val()!="" && !(/^\s*$/).test(perfil_usu.val())){
        if( perfil_usu.val() != perfil_usu.prop('dataset').usurcurrent ){
            if(consultarUsuarioPerfilGlod("usesr",perfil_usu.val(), "") == false){
                Errores.push({
                    "documento" :   perfil_usu,
                    "mesg" :  "usuario en uso",
                });
            }
        }
    }
    if(perfil_cedul.val()!="" && !(/^\s*$/).test(perfil_cedul.val())){
        if( perfil_usu.prop('dataset').idcedulalogin != perfil_cedul.val() )
        if(consultarUsuarioPerfilGlod("odontol","", perfil_cedul.val()) == false){
            Errores.push({
                "documento" :   perfil_cedul,
                "mesg" :  "numero C.I repetido",
            });
        }
    }

    console.log(Errores);

    var valid = true;

    $(".error_perfil").remove();

    if(Errores.length>0){
        for (var i=0; i<=Errores.length-1;i++ ){

            var menssage =  document.createElement("small");
            menssage.setAttribute("style","display: block; color:blue;");
            menssage.setAttribute("class","error_perfil");
            menssage.appendChild(document.createTextNode(Errores[i]['mesg']));
            var documentoDol        = Errores[i]['documento'];

            if(documentoDol.attr("id")=="perf_passd"){
                $(menssage).insertAfter(documentoDol.parent('.input-group'))
            }else{
                $(menssage).insertAfter(documentoDol);
            }

        }
        valid = false;
    }else{
        valid = true;
    }

    return valid;

     function consultarUsuarioPerfilGlod(subaccion, name_users, name_cedula)
     {
         var validar = null;
         $.ajax({
             type: "GET" ,
             url: $DOCUMENTO_URL_HTTP + '/application/controllers/controller_peticiones_globales' ,
             data: {
                 "ajaxSend" : "ajaxSend",
                 "accion" : "valid_usuario_perfil_glob",
                 "subaccion" : subaccion,
                 "nameUsers": name_users ,
                 "nu_cedula": name_cedula,
             } ,
             dataType: "json",
             async:false,
             success: function (resp){
                 if(resp['error'] == ""){
                     validar = true;
                 } else{
                     validar = false;
                 }
             }
         });
         return validar;
     }

};






function autocomplete(inp, arr)
{
    /*the autocomplete function takes two arguments,
    the text field element and an array of possible autocompleted values:*/
    var currentFocus;
    /*execute a function when someone writes in the text field:*/
    inp.addEventListener("input", function(e) {
        var a, b, i, val = this.value;
        /*close any already open lists of autocompleted values*/
        closeAllLists();
        if (!val) { return false;}
        currentFocus = -1;

        /*crear un elemento DIV que contendrá los elementos (valores):*/
        a = document.createElement("DIV"); //se crea un contenedor principal
        a.setAttribute("id", this.id + "autocomplete-list");
        a.setAttribute("class", "autocomplete-items");
        /*append the DIV element as a child of the autocomplete container:*/
        this.parentNode.appendChild(a);

        /*para cada elemento de la matriz ...*/
        console.log(arr);
        for (i = 0; i < arr.length; i++)
        {
            /*check if the item starts with the same letters as the text field value:*/
            if (arr[i]['nomb'].substr(0, val.length).toUpperCase() == val.toUpperCase())
            {

            }

            var idPaciente = arr[i]['id'];
            /*crear un elemento DIV para cada elemento coincidente:*/

            b = document.createElement("DIV");
            b.setAttribute('data-id', arr[i]['id']);
            b.setAttribute('onclick', "valoresPacientes("+idPaciente+")");
            b.setAttribute('onkeypress', "valoresPacientes("+idPaciente+")");

            /*make the matching letters bold:*/
            b.innerHTML = "";
            // b.innerHTML = "<strong>" + arr[i]['nomb'].substr(0, val.length) + "</strong>";

            // b.innerHTML += arr[i]['nomb'].substr(val.length);
            b.innerHTML += arr[i]['nomb'];
            /*insert a input field that will hold the current array item's value:*/
            b.innerHTML += "<input  data-id='"+arr[i]['id']+"' type='hidden' value='" + arr[i]['nomb'] + "'>";


            /*ejecutar una función cuando alguien hace clic en el valor del elemento (elemento DIV):*/
            b.addEventListener("click", function(e) {
                /*insert the value for the autocomplete text field:*/
                inp.value = this.getElementsByTagName("input")[0].value;
                /*close the list of autocompleted values,
                (or any other open lists of autocompleted values:*/
                closeAllLists();
            });

            a.appendChild(b);
        }

    });

    /*ejecutar una función presiona una tecla en el teclado:*/

    // inp.addEventListener("keydown", function(e) {
    //     var x = document.getElementById(this.id + "autocomplete-list");
    //     if (x) x = x.getElementsByTagName("div");
    //     if (e.keyCode == 40) {
    //         /*If the arrow DOWN key is pressed,
    //         increase the currentFocus variable:*/
    //         currentFocus++;
    //         /*and and make the current item more visible:*/
    //         addActive(x);
    //     } else if (e.keyCode == 38) { //up
    //         /*If the arrow UP key is pressed,
    //         decrease the currentFocus variable:*/
    //         currentFocus--;
    //         /*and and make the current item more visible:*/
    //         addActive(x);
    //     } else if (e.keyCode == 13) {
    //         /*If the ENTER key is pressed, prevent the form from being submitted,*/
    //         e.preventDefault();
    //         if (currentFocus > -1) {
    //             /*and simulate a click on the "active" item:*/
    //             if (x) x[currentFocus].click();
    //         }
    //     }
    // });

    function addActive(x) {

        /*a function to classify an item as "active":*/
        if (!x) return false;
        /*start by removing the "active" class on all items:*/
        removeActive(x);
        if (currentFocus >= x.length) currentFocus = 0;
        if (currentFocus < 0) currentFocus = (x.length - 1);
        /*add class "autocomplete-active":*/
        x[currentFocus].classList.add("autocomplete-active");
    }

    function removeActive(x) {
        /*a function to remove the "active" class from all autocomplete items:*/
        for (var i = 0; i < x.length; i++)
        {
            x[i].classList.remove("autocomplete-active");
        }
    }

    function closeAllLists(elmnt)
    {
        /*close all autocomplete lists in the document,
        except the one passed as an argument:*/
        var x = document.getElementsByClassName("autocomplete-items");
        for (var i = 0; i < x.length; i++)
        {
            if (elmnt != x[i] && elmnt != inp) {

                x[i].parentNode.removeChild(x[i]);
            }
        }
    }
    /*execute a function when someone clicks in the document:*/
    document.addEventListener("click", function (e) {
        closeAllLists(e.target);
    });
}

/*An array containing all the country names in the world:*/
// var countries = ["Afghanistan","Albania","Algeria","Andorra","Angola","Anguilla","Antigua & Barbuda","Argentina","Armenia","Aruba","Australia","Austria","Azerbaijan","Bahamas","Bahrain","Bangladesh","Barbados","Belarus","Belgium","Belize","Benin","Bermuda","Bhutan","Bolivia","Bosnia & Herzegovina","Botswana","Brazil","British Virgin Islands","Brunei","Bulgaria","Burkina Faso","Burundi","Cambodia","Cameroon","Canada","Cape Verde","Cayman Islands","Central Arfrican Republic","Chad","Chile","China","Colombia","Congo","Cook Islands","Costa Rica","Cote D Ivoire","Croatia","Cuba","Curacao","Cyprus","Czech Republic","Denmark","Djibouti","Dominica","Dominican Republic","Ecuador","Egypt","El Salvador","Equatorial Guinea","Eritrea","Estonia","Ethiopia","Falkland Islands","Faroe Islands","Fiji","Finland","France","French Polynesia","French West Indies","Gabon","Gambia","Georgia","Germany","Ghana","Gibraltar","Greece","Greenland","Grenada","Guam","Guatemala","Guernsey","Guinea","Guinea Bissau","Guyana","Haiti","Honduras","Hong Kong","Hungary","Iceland","India","Indonesia","Iran","Iraq","Ireland","Isle of Man","Israel","Italy","Jamaica","Japan","Jersey","Jordan","Kazakhstan","Kenya","Kiribati","Kosovo","Kuwait","Kyrgyzstan","Laos","Latvia","Lebanon","Lesotho","Liberia","Libya","Liechtenstein","Lithuania","Luxembourg","Macau","Macedonia","Madagascar","Malawi","Malaysia","Maldives","Mali","Malta","Marshall Islands","Mauritania","Mauritius","Mexico","Micronesia","Moldova","Monaco","Mongolia","Montenegro","Montserrat","Morocco","Mozambique","Myanmar","Namibia","Nauro","Nepal","Netherlands","Netherlands Antilles","New Caledonia","New Zealand","Nicaragua","Niger","Nigeria","North Korea","Norway","Oman","Pakistan","Palau","Palestine","Panama","Papua New Guinea","Paraguay","Peru","Philippines","Poland","Portugal","Puerto Rico","Qatar","Reunion","Romania","Russia","Rwanda","Saint Pierre & Miquelon","Samoa","San Marino","Sao Tome and Principe","Saudi Arabia","Senegal","Serbia","Seychelles","Sierra Leone","Singapore","Slovakia","Slovenia","Solomon Islands","Somalia","South Africa","South Korea","South Sudan","Spain","Sri Lanka","St Kitts & Nevis","St Lucia","St Vincent","Sudan","Suriname","Swaziland","Sweden","Switzerland","Syria","Taiwan","Tajikistan","Tanzania","Thailand","Timor L'Este","Togo","Tonga","Trinidad & Tobago","Tunisia","Turkey","Turkmenistan","Turks & Caicos","Tuvalu","Uganda","Ukraine","United Arab Emirates","United Kingdom","United States of America","Uruguay","Uzbekistan","Vanuatu","Vatican City","Venezuela","Vietnam","Virgin Islands (US)","Yemen","Zambia","Zimbabwe"];

/*initiate the autocomplete function on the "myInput" element, and pass along the countries array as possible autocomplete values:*/

function searchPacientesGlob( inputText )
{
    //busco el paciente - obtengo el arreglo de ese paciente
    var Obj_paciente = ObtenerPacienteslistaSearch( inputText.value );
    autocomplete( inputText , Obj_paciente );

}

/**BUSQUE DE PACIENTES A NIVEL GLOBAL **/
/***OBTENGO EL ARREGLO DE LAS LISTA DE PACIENTES - ADMIN **/

function ObtenerPacienteslistaSearch( label ) {

    var data = [];
    $.ajax({
        url:  $DOCUMENTO_URL_HTTP + '/application/system/pacientes/directorio_paciente/controller/directorio_paciente_controller.php',
        type:'POST',
        data:{'ajaxSend':'ajaxSend', 'accion':'ObtenerPacienteslistaSearch', 'label' : label , nombreP , apellidoP, cedulaP },
        dataType:'json',
        async:false,
        success:function(resp) {
            if( resp.length > 0)
            { data = resp }
        }
    });
    return data;
}

function getidPacienteAutocomplete(id)
{
    var idp = (id=="")?"":id;
    $('#idpacienteAutocp').text(idp);
}

function buscarpacientes(input = false) {

    if(input != false){

        var Element = $(input);
        var content = $(".content-box-search");


        var url =  $DOCUMENTO_URL_HTTP + '/application/controllers/controller_peticiones_globales';
        var dataparametros = {'ajaxSend':'ajaxSend', 'accion':'ObtenerPacienteslistaSearch', 'label' : Element.val() };

        $.get(url , dataparametros , function (data) {
            var object = $.parseJSON(data);
            if(object['data'] == true){
                $(".list_search").empty();
                    $.each(object['object'], function(i, item) {
                        var li = document.createElement('li');
                        $(li).html(item['name']);
                        $(".list_search").append($(li));
                    });
                $(".contlistsearch").css("display", "block");
            }else{
                $(".contlistsearch").css("display", "none");
                $(".list_search").empty();
            }
        });

        console.log(Element);
    }
}

//Notificaciones
function notificacion(mensage, accion)
{
    var label = "";

    if(accion == "error")
    {
        label="Error!";
    }
    if(accion == "success")
    {
        label="Exito!";
    }
    if(accion == "question")
    {
        label = "Información";
    }
    if(accion == "warning")
    {
        label = "Advertencia";
    }

    Swal.fire(label, mensage, accion);

}

//CARGAR LOGO E GUARDAR INFOMACION COMPLETA DE LA EMPRESA
$("#subirLogo").change(function(event){

    console.log(this.files);
    SubirImagenes( this, $("#imgLogo"), $DOCUMENTO_URL_HTTP + '/logos_icon/logo_default/icon_software_dental.png');

});

function SubirImagenes(Este,imageid,url)
{
    // var file     = event.target.files[0]; //Img
    var file = Este.files[0];
    if( Este.files.length > 0)
    {
        var preview = imageid;
        var reader   = new FileReader();
        // console.log(preview);
        var puede = false;
        // var image = file["type"].substr(0,5); //solo imagenes
        var image = file.type; //solo imagenes
        if(image == "image/png")
        {
            reader.onloadend = function(){
                preview.attr("src", reader.result);
            };
            if( Este.files.length > 0){     reader.readAsDataURL( Este.files[0] ); }
        }else{
            notificacion("Error de Fichero, Solo admite Ficheros tipo imagenes .png", "error");
        }
        // console.log(file);
    }else{
        imageid.attr("src", url );
    }
    return file;
}

//Update Informacion Entidad Actualiza la informacion de la clinica dental - informacion personal de la clinica dental
$("#Update_entidad").click(function() {

    var form = new FormData();
    var input = document.getElementById('subirLogo');

    // console.log(input.files[0]);
    form.append('accion',   'UpdateEntidad');
    form.append('ajaxSend', 'ajaxSend');

    form.append('nombre'    , $("#entidad_clinica").val());
    form.append('pais'      , $("#entidad_pais").val() );
    form.append('ciudad'    , $("#entidad_ciudad").val() );
    form.append('direccion' , $("#entidad_direccion").val() );
    form.append('telefono'  , $("#entidad_telefono").val());
    form.append('celular'   , $("#entidad_celular").val());
    form.append('email'     , $("#entidad_email").val());
    form.append('logo'      , input.files[0]);

    //configuracion de email para envios de correo
    form.append('conf_emil'      ,     $("#conf_email_entidad").val());
    form.append('conf_password'      , $("#conf_password_entidad").val());

    $.ajax({
        url:  $DOCUMENTO_URL_HTTP + '/application/controllers/controller_peticiones_globales.php',
        type:'POST',
        data: form,
        dataType:'json',
        async:false,
        contentType: false,
        processData:false,
        success:function(resp) {

            if(resp.error == 1)
            {
                location.reload();
            }
        }
    });
});

//recarga la pagina con f5
function reloadPagina(){
    setTimeout(function() {
        location.reload(true);
    },1000)
}

//redondear decimales glob
function redondear(numero, decimales = 2, usarComa = false) {
    var opciones = {
        maximumFractionDigits: decimales,
        useGrouping: false
    };
    usarComa = usarComa ? "es" : "en";
    return new Intl.NumberFormat(usarComa, opciones).format(numero);
}

//loadding load cargando ....
function loaddingload_XMLHttpRequest(screen){

    $(document)
        .ajaxStart( function() {
            /*
            screen.fadeIn();
            $('body').addClass('disabled_link3').css('overflow-y','hidden');*/
            screen.addClass('cargando');
        })
        .ajaxStop( function() {
            /*
            screen.fadeOut();
            $('body').removeClass('disabled_link3').css('overflow-y','scroll');*/
            screen.removeClass('cargando');
        })
}

/** usuario perfil modificar perfil de usuario */

$('#cerrarSesionlink').on('click', function() {
    closeSESSION();
});

function closeSESSION()
{
    var url = $DOCUMENTO_URL_HTTP + "/application/system/login/controller/controller_login.php";
    $.get(url, {'accion':'CerraSesion', 'ajaxSend':'ajaxSend'} , function(data) {
        console.log( $.parseJSON(data) );
        var rsp = $.parseJSON(data);
        if(rsp.error == ''){
            location.reload(true);
        }else{
            notificacion(rsp.error, 'error');
        }
    });
}

function fetch_perfil()
{
    var url = $DOCUMENTO_URL_HTTP + "/application/controllers/controller_peticiones_globales.php";
    var parametros = {
        'ajaxSend':  "ajaxSend",
        'accion'  :  "perfil_glb",
        'idperfil':  $('#obinfoperfil').prop('dataset').idperfil,
        'usuario' :  $('#obinfoperfil').prop('dataset').usuario,
    };

    $.get(url, parametros , function(data) {

        var prf = $.parseJSON(data).objPerfil;
        console.log(prf);

        $('#perf_usu').val(prf.usuario) //se aplica el id de la cedula y el nom usuario
            .attr('data-idcedulalogin', prf.cedulalogin)
            .attr('data-usurcurrent', prf.usuario);

        $('#perf_passd').val( atob(prf.passwor_abc) );
        $('#perf_cedula').val( prf.cedulalogin );
        $('#perf_nom').val(prf.nombre_doc);
        $('#perf_apell').val(prf.apellido_doc);
        $('#perf_email').val(prf.email);
        $('#perf_celular').val(prf.celular);
        $('#especialidadPerfil').val(prf.fk_especialidad).trigger('change');


    });
}

function passwordMostrarOcultarPERFIL( por , el )
{
    if(por == 'mostrar'){
        el.attr('type','text');
    }
    if(por == 'ocultar'){
        el.attr('type','password');
    }
}

/*obtener datos de perfil*/
$('#modificarPerfil').on('click', function() {
    fetch_perfil();
    notificacion('<br> Tener en cuenta que al cambiar el usuario el <b>sistema cerrara la sessión</b> y tendra que iniciar con el nuevo usuario registrado', 'question');
});

/*guarda el perifl de usuario*/
function GuardarPerfilGlob()
{

    var idcedula            = $('#perf_usu').prop('dataset').idcedulalogin;
    var usersActual         = $('#perf_usu').prop('dataset').usurcurrent; //Usuario actual registrado
    var usuario             = $('#perf_usu').val();
    var passwd              = btoa($('#perf_passd').val());
    var nombreOdont         = $('#perf_nom').val();
    var apellidoOdont       = $('#perf_apell').val();
    var cedula              = $('#perf_cedula').val();
    var email               = $('#perf_email').val();
    var perfilEspecialidad  = $('#especialidadPerfil').find(':selected').val();
    var celularPerfil       = $('#perf_celular').val();


    var puedeNO = 0;
    if(usuario == ''){
        puedeNO++;
    }
    if(passwd == ''){
        puedeNO++;
    }

    if(FormValidationPerfilGlobal()==false)
        puedeNO++;

    var $datosPerfil  = { idcedula , usuario, passwd, nombreOdont, apellidoOdont, cedula, email, perfilEspecialidad, celularPerfil };

    // alert( JSON.stringify($datosPerfil) );
    var parametros = {'ajaxSend':'ajaxSend', 'accion':'UpdatePerfilLogin', 'params': $datosPerfil, 'usuarioActual': usersActual };
    var url = $DOCUMENTO_URL_HTTP + '/application/controllers/controller_peticiones_globales';

    if(puedeNO==0) {
        $.get(url, parametros , function($data){

            var rs = $.parseJSON($data);
            if(rs.error == '')
            {
                if(rs.msg!=''){
                    notificacion(rs.msg, 'question');
                }else{

                    if(rs.refrescar > 0)
                    {
                        Swal.fire({
                            icon: 'success',
                            title: 'Reset...',
                            text: "Información actualizada ",
                            showConfirmButton: false,
                            timer: 1500
                        });

                        // location.reload(true);
                        setTimeout(function() {
                            closeSESSION();
                        },1000);
                    }
                }
            }else{
                notificacion(rs.error, 'error');
            }
        });
    }else{
        // notificacion('Campos Obligatorios', 'error');
    }


}

/**funcion loadding inegrar box*/

function boxloading(box = false, load = false , Timer = 0 ){

    var elementSidebar = $(".MenumainSidebar");

    $(".Elementboxloading").remove();

    if(box==false)
        return false;

    if(load==true){

        box.addClass("disabled_link3").css("position","relative");
        elementSidebar.addClass("disabled_link3");

        var Elementboxload = document.createElement("div");
        Elementboxload.setAttribute("class","Elementboxloading");

        $(Elementboxload)
            .addClass("form-group")
            .css("position","absolute");

        var imgload  = document.createElement("img");
        imgload.setAttribute("src",  $DOCUMENTO_URL_HTTP + "/logos_icon/logo_default/iconload.gif");
        imgload.setAttribute("class","img-md");

        var concentp = document.createElement("p");
        $(concentp)
            .html($(imgload))
            .append("<br>Procesando")
            .addClass("text-center")
            .css("display","inline-block");

        $(Elementboxload).html(concentp);
        box.append($(Elementboxload));

        if(Timer>0){
            /**Remuvo  el elemento loadding en un tiempo determinado*/
            setTimeout(()=>{
                $(".Elementboxloading").remove();
                box.removeClass("disabled_link3");
                elementSidebar.removeClass("disabled_link3");
            },Timer);
        }

    }else{
        box.removeClass("disabled_link3");
        elementSidebar.removeClass("disabled_link3");
        $(".Elementboxloading").remove();
    }

    if(load==false){
        box.removeClass("disabled_link3");
        elementSidebar.removeClass("disabled_link3");
        $(".Elementboxloading").remove();
    }

}

var ModulePermission = function(idModule = 0, actionPermiso = 0){

    var Permiso = false;

    var FormDataFetch = new FormData();

    FormDataFetch.append("accion", "ConsultarTypePermisos");
    FormDataFetch.append("ajaxSend", "ajaxSend");
    FormDataFetch.append("idModule", idModule);
    FormDataFetch.append("actionPermiso", actionPermiso);

    $.ajax({
        url: $DOCUMENTO_URL_HTTP + '/application/controllers/controller_peticiones_globales',
        type:'POST',
        data: FormDataFetch ,
        dataType:'json',
        processData:false,
        contentType:false ,
        async:false,
        success:function(resp) {
            Permiso = resp['valid'];
            if(resp['valid']=='')
                Permiso = false;
            if(resp['valid']==false)
                Permiso = false;
            if(resp['valid']==true)
                Permiso = true;
        }
    });
    alert(Permiso);
    return Permiso;
};


$('#perf_cedula').mask("000000000-0",{placeholder:"_________-_"});

$('#especialidadPerfil').select2({
    placeholder:'Selecione opcion',
    allowClear: false,
    language:'es'
});
