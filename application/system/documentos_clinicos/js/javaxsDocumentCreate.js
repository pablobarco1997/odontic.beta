
// import {Documento} from './class_document';


$idFormDocument           = "";
$FormatoIndexName         = 0;
$FormatoIndexElment       = 0;
$nameDocumentClinico      = "";
$DescripcionDocumnClinico = "";
$UltimaHoja               = "Hoja";


var FormValidNameDocumenClinico = function(El = false, Click = false){

    var Error = [];
    var Camptext      = $("#nameFormClinico");
    var CampDescript  = $("#DescripFormClinico");

    var RgxVacio                =  new RegExp((/^\s*$/));
    var RgxLetrasNumeros        =  new RegExp(/^[A-Za-z0-9\s]+$/g);
    var RgxCarateresEspeciales  =  new RegExp(/[^a-z0-9\s]/gi); //Remplaza caracteres especiales
    var RgxDelVacio             =  new RegExp(/\s+/g); //Remplazar Vacio

    if( RgxVacio.test( Camptext.val() ) ){
        Error.push({
            'Dom':Camptext ,
            'msgerr' : 'No puede Ingresar campo vacio'
        });
    }
    if( !RgxVacio.test( Camptext.val() ) ){
        if( RgxLetrasNumeros.test(Camptext.val()) == false ){
            Error.push({
                'Dom':Camptext ,
                'msgerr' : 'ingresar solo letras y numeros (No acepta caracteres Especiales)'
            });
        }
        if(Camptext.val().length > 200){
            Error.push({
                'Dom':Camptext ,
                'msgerr' : 'Max de caracteres 200'
            });
        }
        // if(El!=false){
        //     if(ValidarTitulo(Camptext) == true){
        //         Error.push({
        //             'Dom':Camptext ,
        //             'msgerr' : 'Nombre de Documento ya existe'
        //         });
        //     }
        // }
        if(Click!=false){
            if(ValidarTitulo(Camptext) == true){
                Error.push({
                    'Dom':Camptext ,
                    'msgerr' : 'Nombre de Documento ya existe'
                });
            }
        }
    }
    if( RgxVacio.test( CampDescript.val() ) ){
        Error.push({
            'Dom':CampDescript ,
            'msgerr' : 'No puede Ingresar campo vacio'
        });
    }
    if( !RgxVacio.test( CampDescript.val() ) ){
        if( CampDescript.val().length >  200){
            Error.push({
                'Dom':CampDescript ,
                'msgerr' : 'Max de caracteres 200'
            });
        }
    }

    $(".msg_err_name_document").remove();

    for (var i = 0; i <= Error.length -1; i++){
        var msg   = document.createElement('small');
        var Dom   = Error[i]['Dom'];

        $(msg)
            .html(Error[i]['msgerr']+"<br>")
            .addClass("msg_err_name_document")
            .css('color', 'red')
            .insertAfter(Dom);
    }

    if(Error.length>0)
        return false;
    else
        return true;

};

//Funciones
var ValidarTitulo = function(Camptext){
    var url = $DOCUMENTO_URL_HTTP + '/application/system/documentos_clinicos/controller_documentos/controller_document.php';
    var valid = false;
    $.ajax({
        url: url,
        type:"GET",
        data:{"ajaxSend":"ajaxSend", "accion":"valid_title_doc","title": Camptext.val()},
        dataType:"json",
        async:false,
        cache:false,
        success:function(resp){
            if(resp['error']!=''){
                valid = true;
            }else{
                valid = false;
            }
        }
    });
    return valid;
};


/**Agregar Titulo o Identificardo al Documento*/
$("#namebtnFormClinico").on("click", function() {

    var nameCliniDom        = $("#nameFormClinico").val();
    var DescriptCliniDom    = $("#DescripFormClinico").val();

    if( FormValidNameDocumenClinico(false, true) == false)
        return false;
    else{
        $nameDocumentClinico      = (nameCliniDom.replace((/\s+/g),'_'));
        $DescripcionDocumnClinico = DescriptCliniDom;
    }


    if($nameDocumentClinico!=""){

        var nameTitulo = "<h2 id='namClinicoPrincipalTitulo'  class='text_label dobleClickOption'  data-name='"+$nameDocumentClinico+"'>"+nameCliniDom+"</h2>";
        var FormClin   = "<form id='Form_"+$nameDocumentClinico+"' action='' style=\"width:100%; position: relative;  \">" +
                                "<table style=\"border-collapse: collapse; width: 100%\" width=\"100%\">" +
                                    "<tr> <td style='text-align: center'> "+nameTitulo+" </td> </tr>" +
                                "</table>" +
                          "</form>" ;

        $("#ContentForm").html(FormClin);
        $("#addNameDocumento").modal("hide");

    }
});



/**Si en caso el modal se llega a esconder por sircuntancia de la vida
 * lo show de nuevo*/
$("#addNameDocumento").on("hidden.bs.modal", function() {
    if($nameDocumentClinico==""){
        notificacion("Debe Ingresar un nombre al Formulario Clinico", "question");
        setTimeout(()=>{ $("#addNameDocumento").modal("show"); }, 1000)
    }
});


//Caja de texto identificador
$("#idCajaTexto").keyup(function() {
    ValidarCaracteresEspecialesInputModal($(this));
});
//Caja checkedbox
$("#idCajaChecked").keyup(function() {
    ValidarCaracteresEspecialesInputModal($(this));
});

function ValidarCaracteresEspecialesInputModal(Elem) {

    $(".msg_err_cajatexto").remove();

    var Msgerr = "<small class='msg_err_cajatexto' style='color: blue;'>solo se permite letras y numeros </small>";

    var RgxVacio =  new RegExp((/^\s*$/));
    var RgxLetrasNumeros = new RegExp(/^[A-Za-z0-9\s]+$/g);
    var valid = 0;

    if( RgxLetrasNumeros.test(Elem.val()) != true ){
        Elem.val(null);
        valid++;
    }

    if(valid>0){
        $(Msgerr).insertBefore(Elem);
    }
}



/**Agregar Columna*/
$(".Columna").on("click", function() {

    var Documento = new DocumentoFormulario( $("#ContentForm").find("form"), $idFormDocument);
    var colm = $(this).prop("dataset").columna;

    if(colm==1){
        Documento.Columna("Column1",1,100);
    }
    if(colm==2){
        Documento.Columna("Column2",2,50);
    }
    if(colm==3){
        Documento.Columna("Column2",3,33.33);
    }
    if(colm==4){
        Documento.Columna("Column2",4,25);
    }

});

function addElmentosConf(Element){

    $("#ConfiguracionElmentTable").modal("show");

    var $DataElement = $("#ElementDom");

    //column 1
    if(Element.hasClass("Column1")){
        var Elementtable = Element.find("table");
        var idTable      = Elementtable.attr("id");
        $DataElement
            .val(idTable)
            .attr("data-idElment",idTable);

        console.log($("#"+idTable));
    }

    //column 2
    if(Element.hasClass("Column2")){
        var Elementtable = Element.find("table");
        var idTable      = Elementtable.attr("id");
        $DataElement
            .val(idTable)
            .attr("data-idElment",idTable);

        console.log($("#"+idTable));
    }

}

//Eliminar Elmento Desplegable
function ElemenAddOptionDesplegable(Element, type){

    var err = $(".listDespl_error");

    if(type){
        var padre      = $("#addListaDesplegable");
        var li         = $("#liprincipalDesplagble");
        var cloneli    = li.clone();
        cloneli.removeAttr("id").find("a").removeClass("disabled_link3");
        cloneli.find("label").find("input").val(null);
        padre.append($(cloneli));
    }

    if(!type){
        if($(".optionListaDesp").length == 1){
            err.text("Ya no puede Eliminar mas Elementos");
            setTimeout(()=>{ err.text(null); }, 1500);
            return false;
        }else{
            Element.parent().remove();
        }
    }
}


//Aliniear Texto
$(".btntextalign").click(function() {
    var Documento = new DocumentoFormulario( $("#ContentForm").find("form"), $idFormDocument);
    Documento.btntextalign($(this));
});

//Agregar Elmentos Table
$("#addElmentosTable").on("click", function() {

    var RgxVacio =  new RegExp((/^\s*$/));

    var Documento = new DocumentoFormulario( $("#ContentForm").find("form"), $idFormDocument);
    var idTableParent = $("#ElementDom").val();

    /**Eliminar Table*/
    if($("#EliminarElementoTable").is(":checked")){
        $("#"+idTableParent).parent().parents("table").remove();
    }
    /**Add TEXTO p h o label*/
    if(!RgxVacio.test($("#value_texto").val()) && $('input[name="texto_conf"]:checked').is(":checked")){
        Documento.addElementosTexto(idTableParent);
    }
    /**Add CAJA DE TEXTO*/
    if(!RgxVacio.test($("#caja_texto_conf").val()) && $('input[name="cajaTexto"]:checked').is(":checked")){
        var name = ($("#caja_texto_conf").val()).replace((/[^a-z0-9\s]/),'').replace((/ /g),'_').normalize("NFD").replace(/[\u0300-\u036f]/g, "");
        Documento.addElementosCamposTexto(idTableParent,name);
    }
    /**Add CHECKEBOX*/
    if(!RgxVacio.test($("#idcheckedbox").val()) && $('input[name="addchecked"]:checked').is(":checked")){
        var name  = ($("#idcheckedbox").val()).replace((/[^a-z0-9\s]/),'').replace((/ /g),'_').normalize("NFD").replace(/[\u0300-\u036f]/g, "");
        var label =  $("#idcheckedbox").val();
        Documento.addElementosCamposCheckebox(idTableParent, name, label);
    }
    /**Add Lista desplegable*/
    if(!RgxVacio.test($("#idnameListDesplegable").val())){
        var name  = ($("#idnameListDesplegable").val()).replace((/[^a-z0-9\s]/),'').replace((/ /g),'_').normalize("NFD").replace(/[\u0300-\u036f]/g, "");
        var label =  $("#idnameListDesplegable").val();
        Documento.addElementosListDesplegable(idTableParent, name, label);
    }

    $("#ConfiguracionElmentTable").modal("hide");

});


/** Cargar Elemento DOM html*/
$("#crearte_form_dom").click(function() {

    boxloading($boxContentDocumento,true);

    var ElementDom      = document.getElementById("ContentForm");
    var ElementoString  = ElementDom.outerHTML; //Convierto el elemento en un string
    var FormInformation = new FormData();
    var DataForm        = [];

    var PrimerForm = $("#Form_"+$nameDocumentClinico);
    // console.log(PrimerForm);

    //Recorro todos los name que se encuentran solo en el formulario asignado
    $("input, select, textarea", PrimerForm).each(function() {
        console.log($(this));
        DataForm.push($(this).attr("name"));
    });

    console.log(DataForm);

    FormInformation.append("accion","crear_form_documento");
    FormInformation.append("ajaxSend","ajaxSend");
    FormInformation.append("nameDocument",$nameDocumentClinico );
    FormInformation.append("DescripFormClinico", $DescripcionDocumnClinico );
    FormInformation.append("ElementoString",ElementoString.toString() );
    FormInformation.append("NameDocumentTable",$("#namClinicoPrincipalTitulo").text() );
    FormInformation.append("DataFormCamposTable", ((DataForm.length>0)?DataForm.toString():"") );


    $.ajax({
        url:$DOCUMENTO_URL_HTTP + '/application/system/documentos_clinicos/controller_documentos/controller_document.php',
        type: "POST",
        data:FormInformation,
        dataType:"json",
        processData:false,
        contentType:false,
        cache:false,
        error:function(xhr, status){
            if(xhr['status']=='200'){
                boxloading($boxContentDocumento,true,1000);
            }else{
                if(xhr['status']=='404'){
                    notificacion("Ocurrió un error con la <b>Creacion del documento</b> <br> <b>xhr: "+xhr['status']+" <br> Consulte con Soporte </b>");
                }
                boxloading($boxContentDocumento,true,1000);
            }
        },
        success:function(resp) {

            boxloading($boxContentDocumento,true,1000);
            if(resp['error']==''){

                notificacion("Informacion Actualizada", "success");

                setTimeout(function () {
                    window.location = $DOCUMENTO_URL_HTTP + "/application/system/documentos_clinicos/index.php?view=listdocumment";
                },1500);

                boxloading($boxContentDocumento,true);

            }else{
                notificacion(resp['error'], "error");
            }
        }
    });

    // console.log( ElementDom.outerHTML );

});

//Show Modal Configuracion
$("#ConfiguracionElmentTable").on("show.bs.modal", function() {

    $('input[name="texto_conf"]').prop("checked", false);
    $('input[name="cajaTexto"]').prop("checked", false);
    $('input[name="addchecked"]').prop("checked", false);
    $("#value_texto").val(null);
    $("#idcheckedbox").val(null);
    $("#caja_texto_conf").val(null);
    $("#idnameListDesplegable").val(null);
    EmpityListDesplegable();
    $("#EliminarElementoTable").prop("checked", false).trigger("change");

});

//Eliminar Elementos
$("#EliminarElementoTable").on("change", function() {
    if($(this).is(":checked")){
        $('input[name="texto_conf"]').prop("checked", false).attr("disabled", true);
        $('input[name="cajaTexto"]').prop("checked", false).attr("disabled", true);
        $('input[name="addchecked"]').prop("checked", false).attr("disabled", true);
        $("#value_texto").val(null).attr("disabled", true);
        $("#idcheckedbox").val(null).attr("disabled", true);
        $("#caja_texto_conf").val(null).attr("disabled", true);
        $("#idnameListDesplegable").val(null).attr("disabled", true);
        EmpityListDesplegable();
        $(".optionListaDesp").attr("disabled",true);
        $("#addPanelTexto").attr("disabled",true).val(null);

    }else{
        $('input[name="texto_conf"]').prop("checked", false).attr("disabled", false);
        $('input[name="cajaTexto"]').prop("checked", false).attr("disabled", false);
        $('input[name="addchecked"]').prop("checked", false).attr("disabled", false);
        $("#value_texto").val(null).attr("disabled", false);
        $("#idcheckedbox").val(null).attr("disabled", false);
        $("#caja_texto_conf").val(null).attr("disabled", false);
        $("#idnameListDesplegable").val(null).attr("disabled", false);
        EmpityListDesplegable();
        $(".optionListaDesp").attr("disabled",false);
        $("#addPanelTexto").attr("disabled",false).val(null);
    }
});

function EmpityListDesplegable(){

    var li = "";
    li = "    <li> <small class=\"btnhover btn-xs\" style=\"cursor: pointer\" onclick=\"ElemenAddOptionDesplegable(null, true)\"> <i class=\"fa fa-plus\"></i> Add Nueva opción </small>  </li>\n" +
        "<li id=\"liprincipalDesplagble\"><a href=\"#\" class=\"disabled_link3\" onclick=\"ElemenAddOptionDesplegable($(this),false)\"><i class=\"fa fa-trash-o\"></i></a> <label class=\"control-label\"> opciones   <input  type=\"text\" class=\"form-control input-sm optionListaDesp\" style=\"font-weight: normal\" ></label> </li>\n" +
        "<li><a href=\"#\" onclick=\"ElemenAddOptionDesplegable($(this), false)\"><i class=\"fa fa-trash-o\"></i></a> <label class=\"control-label\"> opciones  <input  type=\"text\" class=\"form-control input-sm optionListaDesp\" style=\"font-weight: normal\"></label> </li>\n" +
        "<li><a href=\"#\" onclick=\"ElemenAddOptionDesplegable($(this), false)\"><i class=\"fa fa-trash-o\"></i></a> <label class=\"control-label\"> opciones  <input  type=\"text\" class=\"form-control input-sm optionListaDesp\" style=\"font-weight: normal\"></label> </li>\n" +
        "";

    $("#addListaDesplegable").html($(li));

}


$(document).ready(function() {

    if($nameDocumentClinico == ""){
        notificacion("Debe Ingresar un nombre al Formulario Clinico", "question");
        $("#addNameDocumento").modal("show");
    }

});

window.onload =  boxloading($boxContentDocumento,true);

$(window).on("load", function() {
    boxloading($boxContentDocumento,false, 1000);
    $idFormDocument           = $("#ContentForm").find("form").attr("id");
});