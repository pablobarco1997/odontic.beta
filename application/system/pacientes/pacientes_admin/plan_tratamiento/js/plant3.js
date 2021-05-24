
/** JAVASCRIP DE PRESTACION REALIZADA **/

//REALIZAR PRESTACION -----------------------
function  realizarPrestacionModal($Dom)
{
    var idcabplantram = 0;
    var iddetplantram = 0;
    var iddiente      = 0;

    var padre = $Dom.parents('.detalleListaInsert');

    // alert( padre.find('.dientePieza').data('iddiente') );
    //si no hat diente asociado a la prestacion no se puede selecionar un estado con la evolucion
    if(parseInt(padre.find('.dientePieza').data('iddiente')) ==  0)
    {
        $('#actualizarOdontogramaPlantform').addClass('disabled_link3');

    }else{
        $('#actualizarOdontogramaPlantform').removeClass('disabled_link3');
    }

    //limpias el modal de realizar prestacion
    $('#evolucionDoct').val(null).trigger('change');
    $('#actualizarOdontogramaPlantform').val(null).trigger('change');
    $('#descripEvolucion').val(null);


    idcabplantram = $ID_PLAN_TRATAMIENTO; //el id del plan de tratamiento global
    iddetplantram = padre.find('.statusdet').data('iddet');
    iddiente      = padre.find('.dientePieza').data('iddiente');

    /*se ejecuta un attr onclick para crear la evolucion*/
    $('#RealizarPrestacion').attr('onclick', 'RealizarPrestacionDetallePLantram('+idcabplantram+','+iddetplantram+','+iddiente+')');

}

/*REALIZA LA PRESTACION*/
function RealizarPrestacionDetallePLantram(idcabplantram, iddetplantram, iddiente)
{
    var $msg_err = 0;
    var msgDoct  = $('#msgDoctorerr');

    if($('#evolucionDoct').find(':selected').val()==""){
        $msg_err = "Seleccione un Doct@r";
        msgDoct.text($msg_err);
    }

    if($msg_err==""){

        $.ajax({
            url: $DOCUMENTO_URL_HTTP +'/application/system/pacientes/pacientes_admin/controller/controller_adm_paciente.php',
            type:'POST',
            data: {
                'ajaxSend': 'ajaxSend',
                'accion':'realizarPrestacion',
                'idcabplantram' : idcabplantram,
                'iddetplantram' : iddetplantram,
                'iddiente' : iddiente , //esta opcion puede ser 0
                'idpaciente' : $id_paciente,

                'fk_doct'        : $('#evolucionDoct').find(':selected').val() ,
                'observacion'    : $('#descripEvolucion').val() ,
                'fk_estadodiente': ($('#actualizarOdontogramaPlantform').find(':selected').val() == "") ? 0 : $('#actualizarOdontogramaPlantform').find(':selected').val()
            },
            dataType: 'json',
            async: false,
            success: function(resp) {

                if(resp.error == ""){
                    $('#modal_prestacion_realizada').modal('hide');
                    setTimeout(function () {
                        notificacion('Información Actualizada' + resp.tieneOdontograma  , 'success');
                        fetch_plantratamiento('consultar'); //Obtengo lso datos plan de tratamiento
                    },800);
                }else{

                }

            }
        });
    }

}

$('#evolucionDoct').change(function() {
    if($(this).find(':selected').val()!=""){
        $('#msgDoctorerr').text(null);
    }else{
        $('#msgDoctorerr').text("Seleccione un Doct@r");
    }
});

//ELIMINAR PRESTACION
//ELIMINAR ESTADO DE LA PRESTACION
//TAMBIEN CAMBIA EL ESTADO DE LA PRESTACION
function UpdateDeletePrestacionAsignada(html, AuxSatus = '')
{
    var padre      = html.parents('.detalleListaInsert');
    var status     = padre.find('.statusdet');
    var iddetplant = status.data('iddet');

    if(AuxSatus==''){
        //Prestacion realizada
        if( status.data('estadodet')  == 'R' ) {
            $('#modDeletePrestacion').modal('show');
            $('#AceptarDeletePrestacion').attr('onclick', 'DeletePrestacion('+iddetplant+')');
            // notificacion('Esta prestación se encuentra en estado realizado no se puede Eliminar', 'error');
        }

        //pendiente o activo
        if( status.data('estadodet') == 'A') {
            $('#modDeletePrestacion').modal('show');
            $('#AceptarDeletePrestacion').attr('onclick', 'DeletePrestacion('+iddetplant+')');
        }

        if( status.data('estadodet') == 'P') {
            $('#modDeletePrestacion').modal('show');
            $('#AceptarDeletePrestacion').attr('onclick', 'DeletePrestacion('+iddetplant+')');
        }
        // console.log(status);
    }

    if(AuxSatus!=''){ //cambiar de estados
        if(AuxSatus=='P'){ //cambiar el estado a EN PROCESO
            var url = $DOCUMENTO_URL_HTTP +'/application/system/pacientes/pacientes_admin/controller/controller_adm_paciente.php';
            $.get(url, {
                'ajaxSend':'ajaxSend',
                'accion':'UpdateStatusPrestacion',
                'iddetTratm':iddetplant
            }, function(data) {
                var respuesta = $.parseJSON(data);
                if(respuesta['error'] == ''){
                    fetch_plantratamiento('consultar');//REFRES LIST DETALLE
                    notificacion('Información Actualizada', 'success');
                }else{
                    notificacion(respuesta['error'], 'error');
                }
            });
        }
    }

}

/**Eliminar prestacion detalle */
function DeletePrestacion(iddetplant)
{

    $.ajax({
        url: $DOCUMENTO_URL_HTTP +'/application/system/pacientes/pacientes_admin/controller/controller_adm_paciente.php',
        type:'POST',
        data: {
            'ajaxSend': 'ajaxSend',
            'accion' : 'eliminar_prestacion_plantram' ,
            'iddetplantram' : iddetplant,
            'idplanCab'  : $ID_PLAN_TRATAMIENTO,
            'idpaciente' : $id_paciente,
        },
        dataType: 'json',
        async: false,
        success: function(resp)
        {
            if( resp.error == ''){

                notificacion('Información Actualizada', 'success');
                fetch_plantratamiento('consultar');
                $('#modDeletePrestacion').modal('hide');

            }else{

                notificacion(resp.error , 'error');
            }
        }
    });

}



function UpdateObservacionPlantramCab()
{
    $.ajax({
        url: $DOCUMENTO_URL_HTTP +'/application/system/pacientes/pacientes_admin/controller/controller_adm_paciente.php',
        type:'POST',
        data: {
            'ajaxSend'   : 'ajaxSend',
            'accion'     : 'update_observacion' ,
            'idplantram' : $ID_PLAN_TRATAMIENTO,
            'observacion': $('#addcomment').val(),
        },
        dataType: 'json',
        async: false,
        success: function(resp)
        {
        }
    });
}

$('#addCommentario').click(function() {
    UpdateObservacionPlantramCab();
});