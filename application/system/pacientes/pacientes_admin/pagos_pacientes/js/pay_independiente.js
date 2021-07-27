

var FomValidFormaPagos = function() {

    var valid = 0;
    var ErroresData              = [];
    var FormaPago                = $("#formp_descrip_formp");
    var FormaPagoObservacion     = $("#formp_observacion");

    if( FormaPago.val() == "" ){

        ErroresData.push({
            'document' : FormaPago ,
            'text' : 'Campo requerido',
        });

        valid++;
    }

    if($('.ElementErrorFormaPago').length>0)
        $('.ElementErrorFormaPago').remove();

    if(ErroresData.length>0){

        for (var i=0; i<=ErroresData.length-1;i++){

            var documento = ErroresData[i]['document'];
            var text = ErroresData[i]['text'];
            var Msg = document.createElement('small');

            $(Msg)
                .insertAfter(documento)
                .addClass('ElementErrorFormaPago')
                .css('color','red')
                .text(text);

        }
    }

    if(ErroresData.length>0)
        return false;
    else
        return true;


};


//Formulario validar de recaudacion
var FormValidaRecaudar = function () {

    var valid = 0;
    var ErroresData  = [];
    var forma_pagos  = $("#t_pagos"); //forma de pagos
    var nfacture     = $("#n_factboleta"); //numero de facture
    var monto        = $("#monto_pag"); //monto


    if(forma_pagos.find(":selected").val() == ""){
        ErroresData.push({
            'document' : forma_pagos ,
            'text' : 'Campo requerido',
        });
    }
    if(parseFloat(monto.text())==0){
        ErroresData.push({
            'document' : monto,
            'text' : 'El monto no puede ser 0',
        });
    }
    if(nfacture.val().length!=17 && nfacture.val()==""){
        ErroresData.push({
            'document' : nfacture,
            'text' : 'Campo requerido',
        });
    }

    if(ErroresData.length>0){
        valid++;
    }

    $(".ElementErrorFormaPago").remove();

    if(ErroresData.length>0){
        for (var i=0; i<=ErroresData.length-1;i++){

            var documento = ErroresData[i]['document'];
            var text      = ErroresData[i]['text'];
            var Msg       = document.createElement('small');

            console.log(documento[0].localName);
            if(documento[0].localName=='select'){
                $(Msg).insertAfter(documento.parent().find('span:eq(0)')).addClass('ElementErrorFormaPago').css('color','red').css('display','block').text(text);
            }else{
                $(Msg).insertAfter(documento).addClass('ElementErrorFormaPago').css('color','red').css('display','block').text(text);
            }

        }
    }

    if(valid>0){
        return false;
    }else{
        return true;
    }
};

function listaprestacionesApagar()
{
    $('#ApagarlistPlantratmm').DataTable({
        searching: false,
        ordering:false,
        destroy:true,
        paging:false,
        ajax:{
            url: $DOCUMENTO_URL_HTTP + '/application/system/pacientes/pacientes_admin/pagos_pacientes/controller_pagos/controller_pag.php',
            type:'POST',
            data: {
                    'ajaxSend'   : 'ajaxSend',
                    'accion'     : 'listaprestaciones_apagar',
                    'idpaciente' : $id_paciente,
                    'idplantram' : Get_jquery_URL('idplantram'),
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


//crear forma de pago update delete Nuevo
$("#addFormaPago").on("click", function() {

    if(FomValidFormaPagos()==false)
        return false;

    var idpagotipo = $("#tipo_pago_id").prop("dataset").idpagotype;

    if(idpagotipo!="" && idpagotipo !=0){
        fetchUpdateFormaPago("update", idpagotipo);
    }else{
        fetchUpdateFormaPago("nuevo", null);
        $('#formp_observacion').val(null);
        $('#formp_descrip_formp').val(null);
    }



});

//mask money
function moneyPagosInput(Input)
{
    FormValidaRecaudar();

    if(Input.val()=="")
        Input.val("0.00");

    Input.maskMoney({precision:2,thousands:'', decimal:'.',allowZero:true,allowNegative:true, defaultZero:true,allowEmpty: true});
    acumuladorTotal();
    IngresarValorApagar(Input, 'Input');

}


//CHECKEAR TODODS LOS CHECKBOX -- EJECUTAR UMA TOTAL
$('#checkeAllCitas').change(function(){

    if($(this).is(':checked') == true){
        $('.check_prestacion').prop('checked', true);
        IngresarValorApagar($(this), 'checkeboxAll');
    }else{
        IngresarValorApagar($(this), 'checkeboxAll');
        $('.check_prestacion').prop('checked', false);
    }
    
});


function IngresarValorApagar(html , xComportamiento)
{
    var erro_invalic = 0;

    // console.log(html.parents('tr'));

    if( xComportamiento == 'checkebox')
    {
        var padre          =  html.parents('tr');
        var TotalPrest     =  padre.find('.total_apagar').text();
        var Abonado        =  padre.find('.Abonado').text();
        var Abonar         =  padre.find('.Abonar');
        var error_apagar   = padre.find('.error_pag');
        var Pendiente      =  padre.find('.Pendiente').text();
        var AbonarAux      = 0;


        //Si pendiente es 0
        if(parseFloat(Pendiente) == parseFloat(0)){
            AbonarAux = parseFloat(TotalPrest);
            Abonar.val(AbonarAux.toFixed(2));
        }

        // Si pendiente es mayor a 0
        if(parseFloat(Pendiente) > parseFloat(0)){
            AbonarAux = parseFloat(Pendiente);
            Abonar.val(AbonarAux.toFixed(2));
        }

        if(html.is(":checked") == false){
            Abonar.val(parseFloat(0));
        }

        setTimeout(function() {
            error_apagar.text(null);
        }, 1500);

    }

    if( xComportamiento == 'checkeboxAll')
    {
        if( html.is(':checked') == true){

            $('#ApagarlistPlantratmm tbody tr').each(function(){

                var padre      =  $(this);
                var TotalPrest =  padre.find('.total_apagar').text();
                var Abonado    =  padre.find('.Abonado').text();
                var Abonar     =  padre.find('.Abonar');
                var Pendiente  =  padre.find('.Pendiente').text();
                var AbonarAux  = 0;

                var errorpago = padre.find('.error_pag');

                //Si pendiente es 0
                if(parseFloat(Pendiente) == parseFloat(0)){
                    AbonarAux = parseFloat(TotalPrest);
                    Abonar.val(redondear(AbonarAux,2,false));
                }

                // Si pendiente es mayor a 0
                if(parseFloat(Pendiente) > parseFloat(0)){
                    AbonarAux = parseFloat(Pendiente);
                    Abonar.val(redondear(AbonarAux,2,false));
                }

                setTimeout(function() {
                    errorpago.text(null);
                }, 1500);

            });


        }

        if( html.is(':checked') == false){
            $('.Abonar').val(0.00).trigger('onkeyup');
        }

    }

    if(xComportamiento == 'Input')
    {

        var padre      =  html.parents('tr');
        var TotalPrest =  padre.find('.total_apagar').text();
        var Abonado    =  padre.find('.Abonado').text();
        var Abonar     =  padre.find('.Abonar');
        var error_apagar = padre.find('.error_pag');
        var Pendiente  =  padre.find('.Pendiente').text();
        var AbonarAux  = 0;


        //Si pendiente es 0
        if(parseFloat(Pendiente) == parseFloat(0))
        {
            AbonarAux = parseFloat(TotalPrest);

            // console.log(parseFloat(Abonar.val()) + '>' + AbonarAux);
            if(parseFloat(Abonar.val()) > AbonarAux)
            {
                Abonar.addClass('INVALIC_ERROR');
                error_apagar.text('El pago no puede ser mayor al Total');
                erro_invalic++;

            }else{
                Abonar.removeClass('INVALIC_ERROR');
                error_apagar.text(null);
            }
        }

        // alert(Pendiente);
        // Si pendiente es mayor a 0
        if( parseFloat(Pendiente) > parseFloat(0) ){
            AbonarAux = parseFloat(Pendiente);

            if(parseFloat(Abonar.val()) > AbonarAux)
            {
                Abonar.addClass('INVALIC_ERROR');
                error_apagar.text('El pago no puede ser mayor al Pendiente');
                erro_invalic++;

            }else{
                Abonar.removeClass('INVALIC_ERROR');
                error_apagar.text(null);
            }
        }

        setTimeout(function() {
            $('#error_pag').text(null);
        }, 1500);
    }

    acumuladorTotal();

    if(erro_invalic > 0){
        $('#btnApagar').attr('disabled', true).addClass('disabled_link3');
    }else{
        $('#btnApagar').attr('disabled', false).removeClass('disabled_link3');
    }
}

//ACUMULA EL TOTAL
function acumuladorTotal()
{
    var totalPrestacion = 0;
    $('#ApagarlistPlantratmm tbody tr').each(function(){

        var padre      =  $(this);
        var TotalPrest =  padre.find('.total_apagar').text();
        var Abonado    =  padre.find('.Abonado').text();
        var Abonar     =  padre.find('.Abonar');

        if(Abonar.val()!=""){
            totalPrestacion += parseFloat(Abonar.val());
        }

    });

    $('#totalPrestacion').text( totalPrestacion.toFixed(2) );
    $('#monto_pag').text( totalPrestacion.toFixed(2) );

}

//Obtengo los valores a pagar
function fetch_apagar()
{
    var data_pagos = [];

    $('#ApagarlistPlantratmm tbody tr').each(function(){

        var padre      =  $(this);
        var TotalPrest =  padre.find('.total_apagar').text();
        var Abonado    =  padre.find('.Abonado').text();
        var Abonar     =  padre.find('.Abonar'); //input abonar

        var fk_prestacion = padre.find('.prestaciones_det').prop('dataset').idprest;
        var iddetplantram = padre.find('.prestaciones_det').prop('dataset').iddetplantram;
        var idcabplantram = padre.find('.prestaciones_det').prop('dataset').idcabplantram;

        var statusPrestacion = padre.find('.prestaciones_det').prop('dataset').status;

        var valorAbonar = Abonar.val();

        if(Abonar.val() != 0 && statusPrestacion != 'PA') {
            data_pagos.push({
                fk_prestacion, iddetplantram,  idcabplantram, valorAbonar ,
                'totalprestacion':TotalPrest
            });
        }

    });

    console.log(data_pagos);
    return data_pagos;
}

/**fetch pagos list*/
function fetchPagosListSelect(){

    $("#t_pagos").empty().html('<option value=""></option>');

    var url = $DOCUMENTO_URL_HTTP + '/application/system/pacientes/pacientes_admin/pagos_pacientes/controller_pagos/controller_pag.php';
    $.get(url , {'accion':'fetchTiposPagos','ajaxSend':'ajaxSend'}, function(data) {
        var respuesta = $.parseJSON(data);
        if(respuesta['object'].length > 0){
            $('#t_pagos').select2({
                placeholder:'Seleccione un tipo de pago',
                allowClear: true,
                language:'es',
                data: respuesta['object'],
            });
        }
    });

}

/**PAGAR PLAN DE TRATAMIENTO ------------------*/
$('#btnApagar').click(function() {

    if(FormValidaRecaudar()==false){
        return false;
    }

    if(cajaUsuario()==false){
        notificacion("Este usuario no tiene asociada una caja <br> <b>No puede realizar esta Operación</b>", "question");
    }

    button_loadding($('#btnApagar'), true);
    var datos = fetch_apagar();

    $.ajax({
        url: $DOCUMENTO_URL_HTTP + '/application/system/pacientes/pacientes_admin/pagos_pacientes/controller_pagos/controller_pag.php',
        delay:250,
        type:'POST',
        data: {
            'ajaxSend': 'ajaxSend',
            'accion':'realizar_pago_independiente',
            'datos': datos,
            'tipo_pago' : $('#t_pagos').find(':selected').val(),
            'n_fact_bolet' : $('#n_factboleta').val(),
            'amount_total' : $('#monto_pag').text(),
            'observ' : $('#descripObserv').val(),
            'idpaciente': $id_paciente , 'idplancab': Get_jquery_URL('idplantram'),
        },
        dataType: 'json',
        async: true,
        cache:false,
        complete: function(xhr, status){
            button_loadding($('#btnApagar'), false);
        },
        success: function( respuesta ){
            if(respuesta.error == 1){
                notificacion('Información Actualizada', 'success');
                // location.reload();
                listaprestacionesApagar();
                $("#t_pagos").val(null).trigger('change');
                $("#n_factboleta").val(null);
                $("#descripObserv").val(null);
                $('.Abonar').click();
            }else{
                notificacion(respuesta.error, 'error');
            }

            button_loadding($('#btnApagar'), false);
        }

    });

});


var cajaUsuario = function consulCajaUsuario(){

    var valid = false;
    $.ajax({
        url: $DOCUMENTO_URL_HTTP + '/application/system/pacientes/pacientes_admin/pagos_pacientes/controller_pagos/controller_pag.php',
        type:'POST',
        data:{
          "accion":"consulCajaUsuario",
          "ajaxSend":"ajaxSend",
        },
        complete:function(xhr, status) {
            if(xhr['status']=='200'){
                boxloading($boxContentViewAdminPaciente,false,1000);
            }else{
                if(xhr['status']=='404'){
                    notificacion("Ocurrió un error con la <b>FETCH</b> <br> <b>xhr: "+xhr['status']+" <br> Consulte con Soporte </b>");
                }
                boxloading($boxContentViewAdminPaciente,false,1000);
            }
        },
        dataType: 'json',
        async: false,
        cache:false,
        success:function(resp) {
            if(resp['error']==''){
                valid=true; 
            }else{
                valid=false;
            }
        }
    });
    return valid;
};

$("#t_pagos").change(function () {
    FormValidaRecaudar();
});
$("#n_factboleta").keyup(function () {
    FormValidaRecaudar();
});

$(document).ready(function() {

    listaprestacionesApagar();
    fetchPagosListSelect();
    $('#n_factboleta').mask('000-000-000000000',{placeholder:'___-___-_________'});

});