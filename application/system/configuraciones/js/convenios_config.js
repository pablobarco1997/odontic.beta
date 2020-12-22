
var FormValidationDescuentos = function (input = null, revalidate) {

    var msg_error   = [];
    var dataErrores = [];

    var nombDescuento        = $("#nomb_conv");
    var ValorDescuento       = $("#valor_conv");

    if(revalidate==true){

        if( input!= null){
            if(input.attr("id")=="nomb_conv"){
                input.removeClass("INVALIC_ERROR");
                if(input.val()==""){
                    dataErrores.push({
                        'document': input,
                        'class': 'INVALIC_ERROR'
                    });
                }
            }
            if(input.attr("id")=="valor_conv"){
                input.removeClass("INVALIC_ERROR");
                if(input.val()==""){
                    dataErrores.push({
                        'document': input,
                        'class': 'INVALIC_ERROR'
                    });
                }
            }
        }
    }

    if(revalidate==false){

        nombDescuento.removeClass("INVALIC_ERROR");
        ValorDescuento.removeClass("INVALIC_ERROR");

        if(nombDescuento.val() == ""){
            dataErrores.push({
                'document': nombDescuento,
                'class': 'INVALIC_ERROR'
            });
        }
        if(ValorDescuento.val() == ""){
            dataErrores.push({
                'document': ValorDescuento,
                'class': 'INVALIC_ERROR'
            });
        }
    }

    console.log(dataErrores);
    for (var i = 0; i<= dataErrores.length -1; i++)
    {
        var object  = dataErrores[i];
        var dom     = object['document'];
        // alert(object['class']);
        dom.addClass(object['class']);

    }
};


function list_convenios_configurados()
{
    $('#conf_table_convenio').DataTable({

        searching: true,
        ordering:false,
        destroy:true,
        ajax:{
            url: $DOCUMENTO_URL_HTTP + '/application/system/configuraciones/controller/conf_controller.php',
            type:'POST',
            data:{'ajaxSend':'ajaxSend', 'accion':'list_convenios' },
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


function nuevoUpdateConvenio($subaccion, id)
{

    var boxModalDescuento = $("#modal_conf_convenio").find(".modal-dialog");


    var puedo = 0;

    var nombre  = $('#nomb_conv');
    var valor   = $('#valor_conv');

    if(nombre.val() == ''){
        nombre.addClass('INVALIC_ERROR');
        puedo++;
    }else{
        nombre.removeClass('INVALIC_ERROR');
    }

    if(valor.val() > 100){
        $('#msg_descuento').text('El descuento no puede ser mayor al 100%');
        valor.addClass('INVALIC_ERROR');
        puedo++;
    }
    if(valor.val() == ''){
        valor.addClass('INVALIC_ERROR');
        puedo++;
    }else{
        valor.removeClass('INVALIC_ERROR');
    }

    if(puedo>0){
        boxloading(boxModalDescuento,true,1100);
    }

    var parametros = {
        'accion'  :'nuevoConvenio',
        'ajaxSend': 'ajaxSend' ,
        'subaccion' : $subaccion,
        'id' : id  ,
        'nombre'  : nombre.val() ,
        'valor'   : valor.val() ,
        'descrip' : $('#descrip_conv').val(),
    };

    if( puedo == 0)
    {

        $.ajax({
            url: $DOCUMENTO_URL_HTTP + '/application/system/configuraciones/controller/conf_controller.php',
            type:'POST',
            data: parametros ,
            dataType:'json',
            async:false,
            error:function(xhr, status){
                if(xhr['status']=='200'){
                    boxloading(boxModalDescuento,false,1000);
                }else{
                    if(xhr['status']=='404'){
                        notificacion("Ocurrió un error con la <b>Descuentos</b> <br> <b>xhr: "+xhr['status']+" <br> Consulte con Soporte </b>");
                    }
                    boxloading(boxModalDescuento,false,1000);
                }
            },
            complete:function(xhr, status) {

                if(xhr['status']=='200'){
                    // boxloading($boxContentConfiguracion,false,1000);
                }else{
                    if(xhr['status']=='404'){
                        notificacion("Ocurrió un error con la <b>Descuentos</b> <br> <b>xhr: "+xhr['status']+" <br> Consulte con Soporte </b>");
                    }
                    // boxloading($boxContentConfiguracion,false,1000);
                }
            },
            success:function(resp){

                if(resp.error != ''){
                    boxloading(boxModalDescuento,false,1000);
                    notificacion( resp.error , 'error');
                }else{
                    notificacion( 'Informacion Actualizada' , 'success');
                    list_convenios_configurados();
                    $('#modal_conf_convenio').modal('hide');
                    boxloading(boxModalDescuento,false,1000);
                }

                boxloading(boxModalDescuento,false,1000);
            }

        });

    }


    if($subaccion == 'eliminar')
    {
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
                    list_convenios_configurados();
                    // $('#modal_conf_convenio').modal('hide');
                }
            }

        });
    }
    setTimeout(function() {

        $('#msg_descuento').text(null)
    },3000);
}

function  fetch_modificar_convenio(id) {

    if( id != ""){

        //cambio el comportamient a modificar
        $('#comportamiento').attr('data-subaccion','modificar').attr('data-id', id).find("span").text("Modificar Descuento");

        $.ajax({
            url: $DOCUMENTO_URL_HTTP + '/application/system/configuraciones/controller/conf_controller.php',
            type:'POST',
            data: {'accion':'fetch_modificar_convenio', 'ajaxSend':'ajaxSend', 'id': id} ,
            dataType:'json',
            async:false,
            success:function(resp) {

                console.log(resp['error']);

                if(resp.error == ""){

                    var datos = resp.respuesta;

                    var nombre  = datos[0];
                    var descrip = datos[1];
                    var valor   = datos[3];

                    $('#nomb_conv').val( nombre ).trigger("keyup");
                    $('#descrip_conv').val( descrip );
                    $('#valor_conv').val( valor ).trigger("keyup");

                }else{

                    notificacion(resp.error, 'error');
                }
            }
        });
    }
}

function InputsClean()
{
    $('#nomb_conv').val(null).removeClass('INVALIC_ERROR');
    $('#descrip_conv').val(null).removeClass('INVALIC_ERROR');
    $('#valor_conv').val(null).removeClass('INVALIC_ERROR');

    $('#comportamiento').attr('data-subaccion', 'nuevo').attr('data-id','').find('span').text("Nuevo Descuento");
}

function modalCleanInputs(){

    //nuevo
    $('#comportamiento').attr('data-subaccion','nuevo').text('AGREGAR CONVENIO').attr('data-id',0);
    $('#nomb_conv').val(null);
    $('#valor_conv').val(null);
    $('#descrip_conv').val(null);
}

/*nuevo Update convenios*/
$('#guardar_convenio_conf').click(function() {

    var accion = $('#comportamiento').prop('dataset').subaccion;
    var id = $('#comportamiento').prop('dataset').id;

    if(id!=0){//modificar
        if(!ModulePermission(11,3)){
            notificacion('Ud. No tiene permiso para Modificar','question');
            return false;
        }
    }else{ //crear
        if(!ModulePermission(11,2)){
            notificacion('Ud. No tiene permiso para Crear','question');
            return false;
        }
    }

    var boxModalDescuento = $("#modal_conf_convenio").find(".modal-dialog");
    boxloading(boxModalDescuento, true);
    nuevoUpdateConvenio(accion, id);

});

/*onload window*/
window.onload = boxloading($boxContentConfiguracion, true);

/*Documento cargado*/
$(window).on("load", function() {

    $("#valor_conv").maskMoney({precision:2,thousands:'', decimal:'.',allowZero:true,allowNegative:true, defaultZero:true,allowEmpty: true});

    list_convenios_configurados();

    boxloading($boxContentConfiguracion, true, 1500);
});
