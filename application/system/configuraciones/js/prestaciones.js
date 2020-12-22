
var validCategoria = function() {

    var Errores = [];
    var nomCat = $('#nomb_cat');
    if(nomCat.val() == "" || (!/^\s/.test(nomCat.val())) == false){
        Errores.push({
            "documento" :   nomCat,
            "mesg" :  "Campo Obligatorio",
        });
    }

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

};

var validNuevUpdate = function() {

    var Errores     = [];
    var nomCat      = $('#conf_cat_prestaciones');
    var prest_name  = $('#prestacion_descr');
    var val_prest   = $('#valorPrestacion');

    if(nomCat.find('option:selected').val() == "" || (!/^\s/.test(nomCat.val())) == false){
        Errores.push({
            "documento" :   nomCat,
            "mesg" :  "Campo Obligatorio",
        });
    }if(prest_name.val() == ""){
        Errores.push({
            "documento" :   prest_name,
            "mesg" :  "Campo Obligatorio",
        });
    }if(val_prest.val() == ""){
        Errores.push({
            "documento" :   val_prest,
            "mesg" :  "Campo Obligatorio",
        });
    }

    var valid = true;
    $(".error_perfil").remove();
    if(Errores.length>0){
        for (var i=0; i<=Errores.length-1;i++ ){

            var menssage =  document.createElement("small");
            menssage.setAttribute("style","display: block; color:blue;");
            menssage.setAttribute("class","error_perfil");
            menssage.appendChild(document.createTextNode(Errores[i]['mesg']));
            var documentoDol        = Errores[i]['documento'];

            if(documentoDol.attr("id")=="conf_cat_prestaciones"){
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


};

//funciones =========================
function load_table_prestaciones() {

    $('#listprestacionestable').DataTable({

        searching: true,
        ordering:false,
        destroy:true,
        serverSide: true,
        processing:true,
        ajax:{
            url: $DOCUMENTO_URL_HTTP + '/application/system/configuraciones/controller/conf_controller.php',
            type:'POST',
            data:{'ajaxSend':'ajaxSend', 'accion':'list_prestaciones'},
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
            },
            "search": "_INPUT_",
            "searchPlaceholder": "busqueda por Prestación"
        },

    });
}


function fecth_updatePrestacion( $idprestacion ){

    $.ajax({
        url: $DOCUMENTO_URL_HTTP + '/application/system/configuraciones/controller/conf_controller.php',
        type:'POST',
        data: {'accion':'fecth_update_prestacion', 'ajaxSend':'ajaxSend', 'id': $idprestacion} ,
        dataType:'json',
        async:false,
        success: function(resp){

            //para modificar
            if( resp.obj.length > 0 ){

                var vl = resp.obj[0];
                $('#conf_cat_prestaciones').val( vl.fk_categoria ).trigger('change');
                $('#prestacion_descr').val( vl.descripcion );
                $('#valorPrestacion').val( vl.valor );
                $('#convenioConf').val((vl.fk_convenio == 0)?null : vl.fk_convenio).trigger('change');
                $('#explicacionInfo').val(vl.explicacion);

            }else{
                notificacion(resp.error, 'error');
            }

        }
    });
}

function ActivarDesactivarServicios($id, Element){

    if(!ModulePermission(8,4)){
        notificacion('Ud. No tiene permiso para Desactivar');
        return false;
    }

    var statusService = Element.prop('dataset').status;

    $.ajax({
        url: $DOCUMENTO_URL_HTTP + '/application/system/configuraciones/controller/conf_controller.php',
        type:'POST',
        data: {'accion':'eleminar_prestacion', 'ajaxSend':'ajaxSend', 'id': $id, 'statusPrestacion': statusService} ,
        dataType:'json',
        async:false,
        success: function(resp){

            if(resp.error != ''){
                notificacion( resp.error , 'error');

            }else{
                notificacion('Información Actualizada', 'success');
                // location.href = $DOCUMENTO_URL_HTTP + '/application/system/configuraciones/index.php?view=form_prestaciones';
                //   load_table_prestaciones(); //resfresco la table list servicios

                var table = $("#listprestacionestable").DataTable();
                table.page.info();
                var url = $DOCUMENTO_URL_HTTP + '/application/system/configuraciones/controller/conf_controller.php'+'?accion=list_prestaciones'+'&ajaxSend=ajaxSend';
                table.ajax.url(url).load();
            }
        }
    });

}

function eliminar_categoria_desc_prestacion(subaccion){

    var id = "";
    if(subaccion == 'categoria'){
        id = $('#conf_cat_prestaciones').find(':selected').val();
    }
    if( id != "" &&  subaccion != "")
    {
        var puedo = 0;

        $('#eliminarConfCategoriaDescuento').removeClass('disabled_link3');

    }else{

        if(id == ""){
            notificacion('No a seleccionado Ninguna categoria','error');
            $('#eliminarConfCategoriaDescuento').addClass('disabled_link3');
        }
    }

}

$('#eliminarConfCategoriaDescuento').click(function() {

    if($('#conf_cat_prestaciones').find(':selected').val()!=""){
        $.ajax({
            url: $DOCUMENTO_URL_HTTP + '/application/system/configuraciones/controller/conf_controller.php',
            type:'POST',
            data: {'accion':'eliminar_conf_categoria_desc', 'ajaxSend':'ajaxSend', 'id': $('#conf_cat_prestaciones').find(':selected').val(), 'subaccion': 'categoria'} ,
            dataType:'json',
            async:false,
            success: function(resp){

                if(resp.error != ''){
                    notificacion( resp.error , 'error');
                }else{
                    notificacion('Información Actualizada', 'success');
                    reloadPagina();
                }
            }
        });
    }
});

//convenio
function nuevoUpdateConvenio(subacion){

    var puedo = 0;

    var nombre = $('#nomb_conv');
    var valor = $('#valor_conv');

    if(nombre.val() == ''){
        nombre.addClass('INVALIC_ERROR');
        puedo++;
    }else{
        nombre.removeClass('INVALIC_ERROR');
    }

    if(valor.val() == ''){
        valor.addClass('INVALIC_ERROR');
        puedo++;
    }else{
        valor.removeClass('INVALIC_ERROR');
    }

    if( puedo == 0){

        var parametros = {
            'accion'  :'nuevoConvenio',
            'subaccion' :subacion,
            'ajaxSend': 'ajaxSend' ,
            'nombre'  : nombre.val() ,
            'valor'   : valor.val() ,
            'descrip' : $('#descrip_conv').val(),
        };

        $.ajax({
            url: $DOCUMENTO_URL_HTTP + '/application/system/configuraciones/controller/conf_controller.php',
            type:'POST',
            data: parametros ,
            dataType:'json',
            async:false,
            success:function(resp){

                if(resp.error != ''){
                    notificacion( resp.error , 'error');
                }else{
                    notificacion( 'Informacion Actualizada' , 'success');
                    reloadPagina();
                }
            }

        });

    }
}

function nuevoUpdateConvenio(subaccion)
{

    var puedo = 0;

    var idConveinoDesc = ($('#convenioConf').find(':selected').val() == "") ? 0 :  $('#convenioConf').find(':selected').val();

    var nombre  = $('#nomb_conv');
    var valor   = $('#valor_conv');

    if(nombre.val() == ''){
        nombre.addClass('INVALIC_ERROR');
        puedo++;
    }else{
        nombre.removeClass('INVALIC_ERROR');
    }

    if(valor.val() > 100)
    {
        $('#msg_descuento').text('El descuento no puede ser mayor al 100%');
        valor.addClass('INVALIC_ERROR');
        puedo++;
    }
    if(valor.val() == '')
    {
        valor.addClass('INVALIC_ERROR');
        puedo++;
    }else{
        valor.removeClass('INVALIC_ERROR');
    }

    if( puedo == 0 || subaccion == 'eliminar'){

        var parametros = {
            'accion'  :'nuevoConvenio',
            'ajaxSend': 'ajaxSend' ,
            'subaccion' : subaccion,
            'id': idConveinoDesc,
            'nombre'  : nombre.val() ,
            'valor'   : valor.val() ,
            'descrip' : $('#descrip_conv').val(),
        };
        $.ajax({
            url: $DOCUMENTO_URL_HTTP + '/application/system/configuraciones/controller/conf_controller.php',
            type:'POST',
            data: parametros ,
            dataType:'json',
            async:false,
            success:function(resp){

                if(resp.error != ''){

                    notificacion( resp.error , 'error');

                }else{

                    notificacion( 'Informacion Actualizada' , 'success');
                    location.reload();

                }
            }

        });
    }


}

/*Modifca la categoria de la prestacion*/
function nuevoUpdateCategoria(){

    var subaccion ='';
    var id = $('#conf_cat_prestaciones').find(':selected').val();

    if(id  == ''){
        subaccion = 'nuevo';
    }else{
        subaccion = 'modificar';
    }

    if(subaccion == 'nuevo'){
        $('#nomb_cat').val(null);
        $('#descrip_cat').val(null);
    }

    if(subaccion == 'modificar'){

        $.ajax({
            url: $DOCUMENTO_URL_HTTP + '/application/system/configuraciones/controller/conf_controller.php',
            type:'POST',
            data: { 'accion':'nuevoCategoriaPrestacion', 'ajaxSend':'ajaxSend', 'subaccion': 'consultar' ,'label': $('#nomb_cat').val(), 'descrip': $('#descrip_cat').val(), 'idCat': id } ,
            dataType:'json',
            async:false,
            success:function(resp) {

                if(resp.error == ''){
                    // alert( resp.datos.nombre_cat );
                    $('#nomb_cat').val(resp.datos.nombre_cat);
                    $('#descrip_cat').val(resp.datos.descrip);

                }else {

                    // notificacion( 'Informacion Actualizada' , 'success');
                    // reloadPagina();

                }

            }

        });

    }
}

$('#guardar_categoria_conf').click(function() {

    var puedo = 0;
    var subaccion ='';

    //subaccion nuevo Update
    var id = $('#conf_cat_prestaciones').find(':selected').val();

    if(id  == ''){
        subaccion = 'nuevo';
    }else{
        subaccion = 'modificar';
    }

    if(!validCategoria()){
        return false;
    }

    if(puedo == 0){

        $.ajax({
            url: $DOCUMENTO_URL_HTTP + '/application/system/configuraciones/controller/conf_controller.php',
            type:'POST',
            data: { 'accion':'nuevoCategoriaPrestacion', 'ajaxSend':'ajaxSend', 'subaccion': subaccion ,'label': $('#nomb_cat').val(), 'descrip': $('#descrip_cat').val(), 'idCat': id } ,
            dataType:'json',
            async:false,
            success:function(resp) {
                if(resp.error!=''){
                    notificacion( resp.error , 'error');
                }else {
                    notificacion( 'Informacion Actualizada' , 'success');
                    location.reload(true);
                }
            }

        });
    }
});



//eventos
$('#guardar_prestacion').click(function() {

    if($idprestacion_prestacion==0){//crear
        if(!ModulePermission(8,2)){
            notificacion('Ud. No tiene permiso para crear','question');
            return false;
        }
    }else{ //modificar
        if(!ModulePermission(8,3)){
            notificacion('Ud. No tiene permiso para Modificar','question');
            return false;
        }
    }

    if(!validNuevUpdate()){
        return false;
    }else{
        boxloading($boxContentConfiguracion, true);
    }

    var parametros = {
        'ajaxSend'         : 'ajaxSend',
        'accion'           : 'nuevoUpdatePrestacion',
        'id'               : $idprestacion_prestacion,
        'subaccion'        : $accion_prestacion,
        'label_prestacion' : $('#prestacion_descr').val(),
        'cat_prestacion'   : $('#conf_cat_prestaciones').find(':selected').val(),
        'costo_prestacion' : $("#valorPrestacion").val(),
        'convenio'         : $('#convenioConf').find('option:selected').val(),
        'explicacion'      : $('#explicacionInfo').val()
    };

    $.ajax({
        url: $DOCUMENTO_URL_HTTP + '/application/system/configuraciones/controller/conf_controller.php',
        type:'POST',
        data: parametros ,
        dataType:'json',
        async:false,
        error:function(xhr, status){
            if(xhr['status']=='200'){
                boxloading($boxContentConfiguracion,false,1000);
            }else{
                if(xhr['status']=='404'){
                    notificacion("Ocurrió un error con la <b>Configuracion Prestaciones</b> <br> <b>xhr: "+xhr['status']+" <br> Consulte con Soporte </b>");
                }
                boxloading($boxContentConfiguracion,false,1000);
            }
        },
        complete:function(xhr, status) {

            if(xhr['status']=='200'){
                boxloading($boxContentConfiguracion,false,1000);
            }else{
                if(xhr['status']=='404'){
                    notificacion("Ocurrió un error con la <b>Configuracion Prestaciones</b> <br> <b>xhr: "+xhr['status']+" <br> Consulte con Soporte </b>");
                }
                boxloading($boxContentConfiguracion,false,1000);
            }
        },
        success: function(resp){

            if( resp.error == ''){

                boxloading($boxContentConfiguracion,true,1100);
                notificacion('información Actualizada', 'success');
                setTimeout(function () {location.reload(true);}, 1500);

            }else{
                boxloading($boxContentConfiguracion,false,1000);
                notificacion(resp.error, 'error');
            }

            boxloading($boxContentConfiguracion,false,1000);

        }

    });

});


/*onload window*/

window.onload = boxloading($boxContentConfiguracion, true);

/*Load window*/
$(window).on("load", function() {


    //modificar prestacion
    if( $accion_prestacion == 'modificar'){
        fecth_updatePrestacion( $idprestacion_prestacion );
    }

    $('#conf_cat_prestaciones').select2({
        placeholder: 'Selecione una categoria',
        allowClear: true,
        language:'es'
    });
    $('#convenioConf').select2({
        placeholder: 'Selecione una categoria',
        allowClear: true,
        language:'es'
    });

    $('#valorPrestacion').maskMoney({precision:2,thousands:'', decimal:'.',allowZero:true,allowNegative:true, defaultZero:true,allowEmpty: true});

    boxloading($boxContentConfiguracion, true, 1500);

    validNuevUpdate();

});


