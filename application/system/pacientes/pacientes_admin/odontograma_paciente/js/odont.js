
// LISTA DE ODONTOGRAMA
function  odontolist(){

    var ElemmentoContentload = $("#odontPLant");
    boxTableLoad(ElemmentoContentload, true);

    var busqueda = {
        'ajaxSend'      :'ajaxSend',
        'accion'        :'list_odontograma',
        'idpaciente'    : $id_paciente,
        'date_c'        : $("#startDate_odont").val() ,
        'plantramiento' : $("#tratamientoSeled").find('option:selected').val(),
        'numero'        : $("#numero_odont").val(),
        'estado'        : $("#estado_odont").find('option:selected').val()
    };

    var table = $('#odontPLant').DataTable({

        serverSide:true,
        searching: false,
        ordering:false,
        destroy:true,
        lengthChange: false,
        fixedHeader: true,
        paging:true,
        processing: true,
        lengthMenu:[ 10 ],

        ajax:{
            url: $DOCUMENTO_URL_HTTP + '/application/system/pacientes/pacientes_admin/controller/controller_adm_paciente.php',
            type:'POST',
            data: busqueda ,
            dataType:'json',
            cache:false,
            async:true,
            complete:function (xhr, status) {
                boxTableLoad(ElemmentoContentload, false);
            }
        },

        language: {
            "sProcessing": "Procesando...",
            "sLengthMenu": "Mostrar _MENU_ registros",
            "sZeroRecords": "No se encontraron resultados",
            "sEmptyTable": "Ningún dato disponible en esta tabla",
            "sInfo": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
            "sInfoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
            "sInfoFiltered": "(filtrado de un total de _MAX_ registros)",
            "sInfoPostFix": "",
            "sSearch": "Buscar:",
            "sUrl": "",
            "sInfoThousands": ",",
            "sLoadingRecords": "Cargando...",
            "oPaginate": {
                "sFirst": "Primero",
                "sLast": "Último",
                "sNext": "Siguiente",
                "sPrevious": "Anterior"
            },
            "oAria": {
                "sSortAscending": ": Activar para ordenar la columna de manera ascendente",
                "sSortDescending": ": Activar para ordenar la columna de manera descendente"
            }
        },

    }).on( 'length.dt', function ( e, settings, len ) { // cambiar
        boxTableLoad(ElemmentoContentload, true);
    }).on( 'page.dt', function ( e, settings, len ) { // cambiar
        boxTableLoad(ElemmentoContentload, true);
    });
}


//CONSULTAR SECUENCIAL ODONTOGRAMA
function  concultarSecuencialOdontograma() {

    var sucuencial = 0;

    $.ajax({
        url: $DOCUMENTO_URL_HTTP +'/application/system/pacientes/pacientes_admin/controller/controller_adm_paciente.php',
        type:'POST',
        data: {'accion':'consultar_numero_odontograma','ajaxSend':'ajaxSend'},
        dataType:'json',
        async:false,
        success:function(resp){

            $('#suencialOdontograma').text( resp );
            sucuencial = resp;
        }
    });

    return sucuencial;
}

// EXEC ODONTOGRAMA
// alert($accionOdontograma);
if( $accionOdontograma == 'principal'){

    //delete odontograma estado E
    var Eliminar_odontograma = function (Element) {

        if(!ModulePermission("Odontograma", "eliminar")){
            notificacion("Ud. No tiene permiso para realizar esta Operación", "error");
            return false;
        }


        //fetch id
        var id              = Element.find('.odont_id').prop("dataset").id;
        var plantratamiento = Element.find('.odont_id').prop("dataset").tratamiento;

        if(id==0 || id==""){
            notificacion("Ocurrio un error de parámetros de entrada, Consulte con soporte", "error");
            return false;
        }

        //crea el ojecto y guardas la funcion en el
        var object = {
            id: id,
            callback: function () {
                $.ajax({
                    url:  $DOCUMENTO_URL_HTTP +'/application/system/pacientes/pacientes_admin/controller/controller_adm_paciente.php',
                    delay:500,
                    type:'POST',
                    data:{ 'ajaxSend':'ajaxSend', 'accion':'deleteOdontograma', 'id': id },
                    async:true,
                    cache:false,
                    dataType:'json',
                    complete: function(xhr, status){
                    },
                    success:function (response) {
                        if(response.error==""){
                            var table = $("#odontPLant").DataTable();
                            table.ajax.reload(null, false);
                            setTimeout(()=>{ notificacion('información Actualizada', 'success'); }, 700);
                        }else{
                            setTimeout(()=>{ notificacion(response.error, 'error'); }, 700);
                        }
                    }
                });
            }
        };

        notificacionSIoNO("Eliminar el odontograma?","Este odontograma se encuentro asociado al "+ plantratamiento, object);
    };


    var FormValidationCrearOdontograma = function(){

        var Errores         = [];
        var PlanTratamiento = $("#tratamientoSeled_modal");
        if(PlanTratamiento.val() == "" ){
            Errores.push({
                "documento" :   PlanTratamiento,
                "mesg" :  "Campo Obligatorio",
            });
        }

        var valid = true;

        $(".error_odontograma_msg").remove();

        if(Errores.length>0){
            for (var i=0; i<=Errores.length-1;i++ ){

                var menssage =  document.createElement("small");
                menssage.setAttribute("style","display: block; color:blue;");
                menssage.setAttribute("class","error_odontograma_msg");
                menssage.appendChild(document.createTextNode(Errores[i]['mesg']));
                var documentoDol        = Errores[i]['documento'];
                if( $(documentoDol)[0].nodeName == 'SELECT'){
                    $(menssage).insertAfter($(documentoDol).parent().find('span:eq(0)'));
                }

            }
            valid = false;
        }else{
            valid = true;
        }

        return valid;
    };


    $("#tratamientoSeled_modal").change(function () {
        FormValidationCrearOdontograma();
    });

    /*crear Odontograma*/
    $('#crear_odontograma').on('click', function() {

        if(!ModulePermission('Odontograma','agregar')){
            notificacion('Ud. No tiene permiso para esta Operación', 'error');
            return false;
        }

        if(FormValidationCrearOdontograma()==false){
            return false;
        }

        button_loadding($("#crear_odontograma"), true);

        var parametros = {
            'accion':'nuevoUpdateOdontograma',
            'ajaxSend':'ajaxSend',
            // 'numero': concultarSecuencialOdontograma(),  //ultimo secuencial del odontograma
            'fk_tratamiento' : $('#tratamientoSeled_modal').find(':selected').val(),
            'descrip'        : $('#odontograDescrip').val(),
            'fk_paciente'    : $id_paciente,
            'nom_paciente'   : $("#nav_paciente_admin_nomb").text(),
        };

        $.ajax({
            url: $DOCUMENTO_URL_HTTP +'/application/system/pacientes/pacientes_admin/controller/controller_adm_paciente.php',
            type:'POST',
            data: parametros ,
            dataType:'json',
            async:true,
            cache:false,
            complete: function(xhr, status){
                button_loadding($("#crear_odontograma"), false);
            },
            success:function(resp){
                if(resp.error == ''){
                    /*se actualiza el odontograma update detalle*/
                    var idplantram = $('#tratamientoSeled').find(':selected').val();
                    crearDetalleOdontograma(resp.lasidOdont, idplantram);
                    notificacion('Información Actualizada', 'success');
                    window.location = $DOCUMENTO_URL_HTTP + "/application/system/pacientes/pacientes_admin/index.php?view=odot&key="+$keyGlobal+"&id="+(resp.idpa)+"&v=fordont&idplantram="+parametros.fk_tratamiento;
                }else{
                    $('#msg_errores_odontogram').html(resp.error);
                    setTimeout(function() {
                        $('#msg_errores_odontogram').html(null);
                    },3000);
                }
            }
        });

    });


    function crearDetalleOdontograma(idOdontogramaCab, idplantramiento)
    {
        var url = $DOCUMENTO_URL_HTTP +'/application/system/pacientes/pacientes_admin/controller/controller_adm_paciente.php';
        var paramters = {'ajaxSend':'ajaxSend', 'accion':'OdontogramaUpdate_detalle', 'idplantm':idplantramiento, 'idOdonto':idOdontogramaCab, 'idpaciente': $id_paciente };

        $.get(url, paramters , function (data){
            var info = $.parseJSON(data);
            if(info.error != ''){
                notificacion('Ocurrio un error con el odontograma '+idOdontogramaCab + 'consulte con soporte', 'error'  );
            }
        });
    }

    $("#add_odontograma").on('show.bs.modal', function() {
        $("#tratamientoSeled_modal").val(null).trigger("change");
        $("#odontograDescrip").val(null);
    });

    $(".aplicar_busq_odont").click(function () {
        odontolist();
    });

    $(".limpiar_busq_odont").click(function () {

        var parent = $("#contentFilter");
        parent.find('input').val(null);
        parent.find('select').val(null).trigger('change');
        odontolist();

    });

    $(window).on('load', function () {

        //lista de odontogramas creados
        odontolist();

        $('#startDate_odont').daterangepicker({

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

            startDate: moment().startOf('month'),
            endDate: moment().endOf('month'),
            ranges: {
                'Hoy': [moment(), moment()],
                'Ayer': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                'Últimos 7 Dias': [moment().subtract(6, 'days'), moment()],
                'Últimos 30 Dias': [moment().subtract(29, 'days'), moment()],
                'Mes Actual': [moment().startOf('month'), moment().endOf('month')],
                'Mes Pasado': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
                'Año Actual': [moment().startOf('year'), moment().endOf('year')],
                'Año Pasado': [moment().subtract(1, 'year').startOf('year'), moment().subtract(1, 'year').endOf('year')],
            }
        }).val(null);

        $('.rango span').click(function() {
            $(this).parent().find('input').click();
        });


        $("#estado_odont").select2({
            placeholder:'Elija una opción',
            allowClear: true,
            language: languageEs,
        });

        $('.tratamientoSeled').select2({
            placeholder: 'buscar Plan de tratamiento asignado',
            allowClear: true,
            language:languageEs,
            minimumInputLength:1,
            ajax:{
                url: $DOCUMENTO_URL_HTTP + '/application/system/pacientes/pacientes_admin/controller/controller_adm_paciente.php',
                type: "POST",
                dataType: 'json',
                async:false,
                data:function (params) {
                    var query = {
                        accion: 'filtrarPlantratamientoSearchSelect2',
                        ajaxSend:'ajaxSend',
                        paciente_id: $id_paciente,
                        search: params.term,
                    };
                    return query;
                },
                delay: 500,
                processResults:function (data) {
                    return data;
                }
            }
        });

    });


}

