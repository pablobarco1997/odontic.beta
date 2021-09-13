
//Objecto Prestacion Servicio
var objRealizarServicio = {
    idcab: 0, 
    iddet: 0,
    idpieza: 0,
};

/** JAVASCRIP DE PRESTACION REALIZADA **/

$("#modal_prestacion_realizada").on("show.bs.modal", function (event) {
    // console.log($(event.relatedTarget));
    var ElementoAfter = $(event.relatedTarget); //elemento de donde se levanto el modal
    realizarPrestacionShowModal(ElementoAfter);

});

// Prestaciones realizadas
function  realizarPrestacionShowModal($Dom)
{
    var idcabplantram = 0;
    var iddetplantram = 0;
    var iddiente      = 0;

    var padre = $Dom.parents('.detalleListaInsert');

    // alert( padre.find('.dientePieza').data('iddiente') );
    //si no hat diente asociado a la prestacion no se puede selecionar un estado con la evolucion
    if(parseInt(padre.find('.dientePieza').data('iddiente')) ==  0){
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

    objRealizarServicio.idcab   = idcabplantram;
    objRealizarServicio.iddet   = iddetplantram;
    objRealizarServicio.idpieza = iddiente;

    /*se ejecuta un attr onclick para crear la evolucion*/
    // $('#RealizarPrestacion').attr('onclick', 'RealizarPrestacionDetallePLantram('+idcabplantram+','+iddetplantram+','+iddiente+')');



}

$("#RealizarPrestacion").click(function () {

    if(objRealizarServicio.idcab==0 && objRealizarServicio.iddet==0){
        notificacion('Ocurrio un error de parámetros de entrada, Consulte con Soporte', 'error');
        return false;
    }else{
        RealizarPrestacionDetallePLantram(objRealizarServicio.idcab, objRealizarServicio.iddet, objRealizarServicio.idpieza);
    }
});

/*REALIZA LA PRESTACION*/
function RealizarPrestacionDetallePLantram(idcabplantram, iddetplantram, iddiente){

    if(!ModulePermission('Planes de Tratamientos','agregar')){
        notificacion('Ud. no puede tiene permiso para realizar esta Operación', 'error');
        return false;
    }

    var $msg_err = 0;
    var msgDoct  = $('#msgDoctorerr');
    if($('#evolucionDoct').find(':selected').val()==""){
        $msg_err = "Seleccione un Doctor(a)";
        msgDoct.text($msg_err);
    }

    if($msg_err==""){

        button_loadding($("#RealizarPrestacion"), true);

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
            async: true,
            cache: false,
            complete: function(xhr, status){
                button_loadding($("#RealizarPrestacion"), false);
            },
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
    }else{

        return false;
    }

}

$('#evolucionDoct').change(function() {
    if($(this).find(':selected').val()!=""){
        $('#msgDoctorerr').text(null);
    }else{
        $('#msgDoctorerr').text("Seleccione un Doctor(a) a cargo");
    }
});

//ELIMINAR PRESTACION
//ELIMINAR ESTADO DE LA PRESTACION
//TAMBIEN CAMBIA EL ESTADO DE LA PRESTACION
function UpdateDeletePrestacionAsignada(Element, AuxSatus = '')
{

    //Eliminar Validar Permiso
    if(Element.hasClass('eliminar_tratamiento')){
        if(!ModulePermission('Planes de Tratamientos', 'eliminar')){
            notificacion('Ud. No tiene permiso para realizar esta Operación', 'error');
            return false;
        }
    }

    var padre      = Element.parents('.detalleListaInsert');
    var status     = padre.find('.statusdet');
    var iddetplant = status.data('iddet');

    // alert(status.data('estadodet'));
    if(AuxSatus==''){
        //Prestacion realizada
        if( status.data('estadodet')  == 'R' ) {
            $('#modDeletePrestacion').modal('show');
            $('#AceptarDeletePrestacion').attr('onclick', 'DeletePrestacion('+iddetplant+')');
            // notificacion('Esta prestación se encuentra en estado realizado no se puede Eliminar', 'error');
        }

        //pendiente o activo
        // si se puede eliminar
        if( status.data('estadodet') == 'A') {
            if(!ModulePermission('Planes de Tratamientos', 'modificar')){
                notificacion('Ud. No tiene permiso para realizar esta Operación', 'error');
                return false;
            }
            $('#modDeletePrestacion').modal('show');
            $('#AceptarDeletePrestacion').attr('onclick', 'DeletePrestacion('+iddetplant+')');
        }

        // si se puede eliminar
        if( status.data('estadodet') == 'P') {
            // if(!ModulePermission('Planes de Tratamientos', 'modificar')){
            //     notificacion('Ud. No tiene permiso para realizar esta Operación', 'error');
            //     return false;
            // }
            $('#modDeletePrestacion').modal('show');
            $('#AceptarDeletePrestacion').attr('onclick', 'DeletePrestacion('+iddetplant+')');
        }
        // console.log(status);
    }

    if(AuxSatus!=''){ //cambiar de estados
        if(AuxSatus=='P'){ //cambiar el estado a EN PROCESO

            var paramtrs = {
                'ajaxSend'   :'ajaxSend',
                'accion'     :'UpdateStatusPrestacion',
                'iddetTratm' :iddetplant
            };

            $.ajax({
                url: $DOCUMENTO_URL_HTTP +'/application/system/pacientes/pacientes_admin/controller/controller_adm_paciente.php',
                type:'POST',
                data: paramtrs,
                dataType: "json",
                beforeSend: function () {
                    boxloading($boxContentViewAdminPaciente ,true);
                },complete: function (xhr, status) {
                    boxloading($boxContentViewAdminPaciente ,false, 1000);
                },success: function (response) {
                    if(response['error'] == ''){
                        fetch_plantratamiento('consultar');//refres
                        notificacion('Información Actualizada', 'success');
                    }else{
                        notificacion(response['error'], 'error');
                    }
                }
            });

        }
    }

}

/**Eliminar prestacion detalle */
function DeletePrestacion(iddetplant){

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
        cache: false,
        beforeSend:function () {
            boxloading($boxContentViewAdminPaciente ,true);
        }, complete: function (xhr, status) {
            boxloading($boxContentViewAdminPaciente ,false, 1000);
        },success: function(resp){
            if( resp.error == ''){
                fetch_plantratamiento('consultar');
                $('#modDeletePrestacion').modal('hide');
                setTimeout(function () {
                    notificacion('Información Actualizada', 'success');
                }, 700);
            }else{
                fetch_plantratamiento('consultar');
                $('#modDeletePrestacion').modal('hide');
                setTimeout(function () {
                    notificacion(resp.error , 'error');
                }, 700);
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


function ExportDetalleTTO(){

    if(!ModulePermission('Planes de Tratamientos', 'consultar')){
        notificacion('Ud. No tiene permiso para realizar esta Operación');
        return false;
    }

    var output = "?id_paciente="+$id_paciente+"&id_tratamiento="+$ID_PLAN_TRATAMIENTO;
    var url = $DOCUMENTO_URL_HTTP + "/application/system/pacientes/pacientes_admin/plan_tratamiento/export/excel_detallado_tratamientos";
    url += output;
    window.open(url, '_blank');

}

$('#addCommentario').click(function() {
    UpdateObservacionPlantramCab();
});