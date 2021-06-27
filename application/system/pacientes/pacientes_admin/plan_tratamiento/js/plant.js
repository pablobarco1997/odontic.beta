
//VARIABLES GLOBALES DETALLES ------------------------------------------------------------------------------------------
$formatoIndexPrestacion = 0;
$convenioValor = 0;   //Porcentage del conveio asociado
$AbonadoGlob = 0;
$detalle_plantratm = [];
$ID_PLAN_TRATAMIENTO


//LISTA DE PLAN DE TRATAMIENTO
function  listplaneTratamiento(){

    var ElemmentoContentload = $("#listtratamientotable");

    boxTableLoad(ElemmentoContentload, true);

    var table = $('#listtratamientotable').DataTable({
        searching: false,
        ordering:false,
        destroy:true,
        paging: true,
        serverSide: true,
        lengthChange:false,
        lengthMenu:[ 10 ],
        fixedHeader: true,
        processing:true,
        // scrollX: true,

        ajax:{
            url: $DOCUMENTO_URL_HTTP + '/application/system/pacientes/pacientes_admin/controller/controller_adm_paciente.php',
            type:'POST',
            data:{

                'ajaxSend'               : 'ajaxSend',
                'accion'                 : 'list_tratamiento',
                'idpaciente'             : $id_paciente,
                'mostrar_anulados'       : ($('#mostrarAnuladosPlantram').prop('checked') == true) ? 'si': 'no',
                'mostrar_finalizados'    : ($('#mostaraFinalizados').prop('checked') == true) ? 'si': 'no',
                'idplantmiento'          : $('#filtrPlantram').find(':selected').val(),
                'fecha_range'            : $('.filtroFecha').val(),

            } ,
            dataType:'json',
            complete: function (xhr, status) {
                boxTableLoad(ElemmentoContentload, false);
            }
        },

        columnDefs:[
            {
                'targets': 0,
                'searchable':false,
                'orderable':false,
                'className': 'dt-body-center',
                'render': function (data, type, full, meta){

                    // console.log(full);

                    var idplantratamiento     = null;
                    var numeroPlantratamiento = null;
                    var profecionalCargo      = null;
                    var ultimaCitaFecha       = null;
                    var ultimaCitaHora        = null;
                    var estadoPlanTram        = null;
                    var situacionPlatram      = null;
                    var estadoanulado         = null;


                    var modalProfecional      = "";

                    idplantratamiento     = full[6];
                    profecionalCargo      = full[1];
                    numeroPlantratamiento = full[0];
                    ultimaCitaFecha       = full[3];
                    ultimaCitaHora        = full[4];
                    estadoPlanTram        = full[8];
                    situacionPlatram      = full[5];
                    estadoanulado         = (full[10] == 'E') ? 'disabled_link3': '';

                    var situacion = "";
                    if(full[10] == 'S') //SALDO ABONADO
                    {
                        situacion = full[5] + '&nbsp; ABONADO  &nbsp;' + '<span style="font-weight: bold; color: green" ><i class=\'fa fa-dollar\'></i>&nbsp;'+full['saldoAbonado']+'</span>';
                    }
                    if(full[10] == 'A' ) //ACTIVO O PENDIENTE
                    {
                        situacion = "<i class='fa fa-user-md'></i> &nbsp; "+ full[5];
                    }
                    if(full[10] == 'F' ) // FINALIZADO
                    {
                        situacion = "<i class='fa fa-user-md'></i> &nbsp; "+ full[5];
                    }
                    if(full[10] == 'E' ) // ANULADO
                    {
                        situacion = "<i class='fa fa-user-md'></i> &nbsp; "+ 'ANULADO';
                    }
                    if(profecionalCargo=='No asignado'){
                        modalProfecional = " data-toggle='modal' data-target='#modal_asociar_profecional' ";
                    }

                    //Se bloqueara si ya tiene asociada una cita
                    var disablelinkCitasAsocid = "";
                    var disabledFinalizado     = "";
                    var linkCitaAsociada       = "";

                    if(full[7] != 0){ //cuando ya esta asociada a una cita se disabled
                        disablelinkCitasAsocid = 'disabled_link3';
                        linkCitaAsociada = "<table> <tr><td><img src='"+full['img_ico_cita']+"' WIDTH='20px' HEIGHT='20px'></td> <td>"+full[11]+"</td> </tr> </table> ";
                    }

                    if(estadoPlanTram == 'F'){
                        disabledFinalizado = 'disabled_link3';
                    }

                    var urlRecaudacion = $DOCUMENTO_URL_HTTP + '/application/system/pacientes/pacientes_admin/?view=pagospaci&key='+$keyGlobal+'&id='+Get_jquery_URL('id')+'&v=paym_pay&idplantram='+idplantratamiento;

                    //DROPDOWN MENU
                    var listaOpciones = "" +
                        "<ul class='dropdown-menu pull-left ' >"+
                                "   <li><a href=\"#\" onclick='optionTratamiento("+idplantratamiento+", \"editname\")'>Cambiar Nombre de Plan Tratamiento</a></li>" +
                                "   <li><a href=\"#\" disabled=''  class='disabled_link3'>Financiamiento</a></li>" +
                                "   <li><a href='"+urlRecaudacion+"' >Recaudar este Tratamiento</a></li>" +
                                "   <li><a href=\"#\" class='' onclick='eliminarPlan_tratamiento($(this), "+ idplantratamiento +")' >Anular</a></li>" +
                                "   <li><a href=\"#\"   onclick='optionTratamiento("+idplantratamiento+", \"finalizar_plantram\")'  >Finalizar</a></li>" +
                                "   <li><a href=\"#\" disabled=''  class='disabled_link3'>Duplicar este plan de tratamiento</a></li>" +
                        "</ul>";

                    //------------------------html ------------------------------------
                 var html = "";

                 html += "<div id='boxtratamiento' class='box-ptratamiento row_list_plantram form-group col-lg-12 col-xs-12 col-md-12  ' style=' padding: 7px' >";
                        html += "<div class='form-group col-md-12 col-xs-12 col-lg-12'> " +
                                    " <div class='col-md-11 col-sm-9 col-xs-12'>" +
                                    "       <ul class='list-inline'>" +
                                    "           <li style='font-weight: bold '  >" +numeroPlantratamiento+ "</li>" +
                                    "           <li>" +
                                    // DROPDOWN MENU ACCIONES PLAN TRATAMIENTO
                                    "             &nbsp;&nbsp;&nbsp;&nbsp;" +
                                    "             <div class='dropdown'>" +
                                    "               <button class=\"btn btn-xs dropdown-toggle "+disabledFinalizado+" "+ estadoanulado +"   \" type=\"button\" data-toggle=\"dropdown\"> <i class='fa fa-ellipsis-v'></i> </button>" +
                                    "               "+ listaOpciones +"    " +
                                    "             </div> " +

                                    "           </li>" +
                                    "       </ul>   " +
                                    " </div>" +
                                "</div>";

                  html +=     " <div class='form-group col-md-12 col-xs-12 col-lg-12'>" +
                                        "<div class='col-sm-12 col-md-3 col-xs-12'>" +
                                                "<small style='color: #85929E; font-weight: bold'>PROFESIONAL</small>" +
                                                "<br>" +
                                                  "<small style='font-weight: bold; width: 100%; display: block; cursor: pointer' class='trunc' "+modalProfecional+" data-idtratamiento='"+idplantratamiento+"' onclick='cambiar_attr_id_tratamiento($(this), \"#PlantTratamientoAsociOdont\")' > <i class='fa fa-user-md'></i> &nbsp; " + (profecionalCargo.toUpperCase()) + " </small>" +
                                        "</div>" +

                                        "<div class='col-sm-12 col-md-3 col-xs-12'>" +
                                            "<small style='color: #85929E; font-weight: bold'>ÚLTIMA CITA</small>" +
                                            "<br>" +
                                                "<small style='font-weight: bold; width: 100%; display: block' class='trunc' title='Ultima cita fecha: "+ultimaCitaFecha+" - Hora: "+ultimaCitaHora+" '> " +
                                                    "<i class='fa fa-calendar'></i> &nbsp; " + ultimaCitaFecha + "  " +
                                                    "<i class='fa fa-clock-o'></i>  &nbsp; " + ultimaCitaHora + " " +
                                                "</small>" +
                                        "</div>" +

                                        "<div class='col-sm-12 col-md-3 col-xs-12'>" +
                                            "<small style='color: #85929E; font-weight: bold; '>ESTADO FINANCIERO</small>" +
                                            "<br>" +
                                                "<small style='font-weight: bold; width: 100%; display: block' class='trunc fontsize'> "+ situacion +"  </small>" +
                                        "</div>" +

                                        "<div class='col-sm-12 col-md-2 col-xs-12'>" +
                                            "<small style='color: #85929E; font-weight: bold; '>CITA ASOCIADA</small>" +
                                            "<br>" +
                                                "<a href='#modal_plantrem_citas' onclick='attrChangAsociarCitas("+idplantratamiento+")' data-toggle='modal' style='font-weight: bold; font-size: 1.1rem; color: #000;' class=' fontsize btn btn-xs btnhover "+estadoanulado+"  "+disabledFinalizado+" '> " +
                                                "<i class='fa fa-list-ul'></i> &nbsp; CITAS AGENDADAS ASOCIADAS </a>" + " " +
                                                // " " + linkCitaAsociada +
                                        "</div>" +
                                "   </div>";
                    html += "</div>";


                    // return full[4];
                    return html;
                },

            }
        ],

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
    // new $.fn.dataTable.FixedHeader( table );
    new $.fn.dataTable.FixedHeader( table,
        {
            // headerOffset: 50
        }
    );

}


//PANTALLA PRINCIPAL TRATAMIENTO  --------------------------------------------------------------------------------------
if($accion == "principal")
{


    //CREA EL PLEN DE TRATAMIENTO DESDE EL MODULO PLAN DE TRATAMIENTO
    $('#createPlanTratamientoCab').click(function() {

        attrChangAsociarCitas(null);
        CrearPlanTratamientoIndependienteDependiente(null, true);
    });

    //Se puede asociar varias citas a un plan de tratamiento
    $('#CrearPlanTratamientoPlantram').click(function() {

        if($('#citasPaciente').find('option:selected').val() > 0){
            CrearPlanTratamientoIndependienteDependiente(null);
        }else{

            $('#error_asociarCitas').text('Debe seleccionar una cita');
            setTimeout(function() {
                $('#error_asociarCitas').text(null);
            }, 3000);
        }
    });
    
    //CAMBIAR ATRIBUTO ASOCIAR CITAS A PLANES DE TRATAMIENTO
    function attrChangAsociarCitas($idPlanTratamiento)
    {
        var id = ($idPlanTratamiento==null) ? 0 : $idPlanTratamiento;
        $('#nuPlanTratamiento').attr('data-id', id);

        if(id!=0){

            var table = $("#listTramnCitasAsoc").DataTable({
                    destroy:true,
                    searching:false,
                    serverSide: true,
                    ordering:false,
                    lengthChange: false,
                    fixedHeader: true,
                    paging:true,
                    processing: true,
                    lengthMenu:[ 10 ],
                    ajax:{
                        url : $DOCUMENTO_URL_HTTP + '/application/system/pacientes/pacientes_admin/controller/controller_adm_paciente.php',
                        type:'POST',
                        data:{
                            ajaxSend       :'ajaxSend',
                            accion         :'listTramnCitasAsoc',
                            tratamiento_id : id
                        }
                    },
                    createdRow: function (row, data, index) {
                        // console.log($(row).find('td'));
                        // $(row).find('td').css('vertical-align','center');
                        $(row).find('td').eq(0).css('width','15%');
                        $(row).find('td').eq(1).css('width','20%');
                        $(row).find('td').eq(2).css('width','25%');
                        $(row).find('td').eq(3).css('width','20%');
                    },
                    "language": {
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
    }

    //CREAR PLAN DE TRATAMIENTO CABEZERA INDEPENDIENTE ---------------------
    function CrearPlanTratamientoIndependienteDependiente(subaccion, redirecionar=false)
    {
        var CitasPacientes = $('#citasPaciente').find(':selected').val();

        if($('#nuPlanTratamiento').prop('dataset').id == "" && $('#nuPlanTratamiento').prop('dataset').id != 0){
                 notificacion('Ocurrio un error consulte con soporte <br> <b>No se detecto el plan de tratamiento seleccionado</b> ', 'question');
            return false;
        }

        var subaccion = ($('#nuPlanTratamiento').data('id') == 0) ? "CREATE" : "ASOCIAR_CITAS";

        //EL CREAR PLAN DE TRATAMIENTO
        //AGENDA CONTROLLER
        $.ajax({
            url: $DOCUMENTO_URL_HTTP + "/application/system/agenda/controller/agenda_controller.php",
            type:'POST',
            data: {
                'ajaxSend':'ajaxSend',
                'accion': 'nuevoUpdatePlantratamiento',
                'idpaciente': $id_paciente,
                'idcitadet' : (CitasPacientes == "") ? 0 : CitasPacientes,
                // 'iddoct': ( CitasPacientes.data('iddoct') == "" ) ? 0 : CitasPacientes.data('iddoct') ,
                'idplantramAsociar' : $('#nuPlanTratamiento').prop('dataset').id ,
                'subaccion' : ($('#nuPlanTratamiento').data('id') == 0) ? "CREATE" : "ASOCIAR_CITAS"
            },
            dataType:'json',
            async: false,
            success: function(resp){

                var idpacienteToken = resp.idpacientetoken;

                if(subaccion=='ASOCIAR_CITAS'){
                    var table_aso_citas = $("#listTramnCitasAsoc").DataTable();
                    table_aso_citas.ajax.reload(null, false);
                }

                $("#citasPaciente").val(null).trigger('change');

                if(resp.error == ''){
                    if(subaccion=="CREATE"){
                        notificacion('Plan de Tratamiento Creado - cargando...', 'success');
                    }if(subaccion=="ASOCIAR_CITAS"){
                        notificacion('Información Actualizada', 'success');
                    }
                }else {
                    //Error esta cita ya esta asociada a un plan de tratamiento
                    $('#error_asociarCitas').html(resp.error) ;
                        setTimeout(function() {
                            $('#error_asociarCitas').text(null);
                        },7000);
                }


                if(resp.error == '' && subaccion=="ASOCIAR_CITAS" && redirecionar == true){
                    var $tener = 0;
                    var $idtratamiento = 0;
                    if( resp.idtratamiento > 0){
                        $idtratamiento = resp.idtratamiento;
                        $tener++;
                    }
                    if( subaccion == "ASOCIAR_CITAS"){
                        if($tener > 0){
                            if($idtratamiento > 0){
                                setTimeout(function() {
                                    window.location = $DOCUMENTO_URL_HTTP + '/application/system/pacientes/pacientes_admin/?view=plantram&key=' + $keyGlobal + '&id=' + idpacienteToken + '&v=planform&idplan=' + $idtratamiento;
                                }, 1500);
                            }
                        }
                    }
                }

                if(redirecionar==true){
                    console.log(resp);
                    $idtratamiento = resp.idtratamiento;
                    setTimeout(function() {
                        window.location = $DOCUMENTO_URL_HTTP + '/application/system/pacientes/pacientes_admin/?view=plantram&key=' + $keyGlobal + '&id=' + idpacienteToken + '&v=planform&idplan=' + $idtratamiento;
                    }, 1500);
                }



            }

        });

    }


    $('#citasPaciente').select2({
        placeholder: 'Mostrar citas asociadas a este paciente',
        allowClear:true,
        language: languageEs,
        minimumInputLength:1,
        ajax:{
            url: $DOCUMENTO_URL_HTTP + '/application/system/pacientes/pacientes_admin/controller/controller_adm_paciente.php',
            type: "POST",
            dataType: 'json',
            async:false,
            data:function (params) {
                var query = {
                    accion: 'CitasAgendadasSearchSelect2',
                    ajaxSend:'ajaxSend',
                    paciente_id: $id_paciente,
                    search: params.term,
                };
                return query;
            },
            delay: 250,
            processResults:function (data) {
                return data;
            }
        }

    });

    $('#filtrPlantram').select2({
        placeholder: 'Seleccione una opcion',
        allowClear:true,
        language: languageEs,
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
            delay: 250,
            processResults:function (data) {
                return data;
            }
        }
    });

    /*buscar x plan tramiento*/
    $('#filtrar_evoluc').click(function() {
        listplaneTratamiento();
    });
    /*limpiar filtro plan tratamiento*/
    $('#limpiarFiltro').click(function() {
        $('#startDate').val(null);
        $('#filtrPlantram').val(null).trigger('change');
        listplaneTratamiento();
    });

    /**FECHA X RANGO*/
    $('#startDate').daterangepicker({

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
        endDate: moment(),
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
    });


    $('.rango span').click(function() {
        $(this).parent().find('input').click();
    });

    $('#startDate').val(null);

    listplaneTratamiento();  /**lista principal plan de tratamiento*/

    /*Detecto si mi modal para asociar  cita show*/
    $("#modal_plantrem_citas").on('show.bs.modal', function() {
        $("#citasPaciente").val(null).trigger("change");
        $("#error_asociarCitas").text(null);
    });

}


//FORMULARIO TRTAMIENTO
if($accion == 'addplan')
{

    fetch_plantratamiento('consultar'); //Obtengo lso datos plan de tratamiento

    //LIMPIAR CARAS MODAL
    function clearModalDetalle(por)
    {

        if(por == 'soloActivas')
        {
            $(".cara").each(function() {
                $(this).removeClass('activeCara');
            });
        }

        if(por == 'todo')
        {
            $(".cara").each(function() {
                $(this).removeClass('activeCara');
            });
            $('#prestacionestratamiento').removeClass('disabled_link3');
            $('#addplantratamientodetalle').removeClass('disabled_link3');
            $('#detallemod').attr('data-iddet', 0);
            $('#detalle-prestacionesPlantram tr').remove();
            $('#detencionPermanente , #detencionTemporal').prop('checked', false);
            $('#prestacion_planform').val(null).trigger('change');
        }

    }


    //ACTIVAR CARAS COLOREAR CARAS PIEZA -------------------------------------------------------------------------------

    //CARAS
    $('.CaraClickDenticionPermanente').click(function() {

        var $htmlCara= $(this);

        var ActiveCara = $htmlCara.find('.activeCara'); //verifico si esta activo la cara

        if(ActiveCara.length > 0) //activo
        {
            $htmlCara.removeClass('activeCara');
        }else{ //Desactivo
            $htmlCara.addClass('activeCara');
        }

        console.log(ActiveCara.length);
    });

    //PIEZAS
    $('.CheckPiezasDenticionPermanente').click(function() {

        var $htmlpieza= $(this).parents('table');

        var cara = $htmlpieza.find('.cara');

        if(cara.length == 5)
        {
            if($(this).is(':checked')) //si esta activo
            {
                cara.addClass('activeCara');
            }else{
                cara.removeClass('activeCara');
            }
        }
        console.log($htmlpieza);

    });

    //END CARAS PIEZAS -------------------------------------------------------------------------------------------------


    // $('#prestacion_planform').select2({
    //      placeholder:'Seleccione una prestación',
    //      allowClear: true,
    //      language: languageEs,
    //      // dropdownParent: $(".modal-backdrop"),
    //
    // });

    recalculoViewForm(); //RECALCULAR TOTAL PRESTACION


    //REALIZAR PRESTACION MODAL COMPORTAMIENTOS ------------------------------------------------------------------------
    $('#evolucionDoct').select2({
        placeholder:'Seleccione un doctor',
        allowClear: true,
        language: languageEs,
    });

    $('#actualizarOdontogramaPlantform').select2({
        placeholder:'Seleccione un estado del odontograma',
        allowClear: true,
        language: languageEs,
    })



}




//CARAS PIEZAS ACTIVAR FETCH  ------------------------------------------------------------------------------------------
function fetchPiezasCaras( seach_diente ){

    var dataPrincipal = [];
    var piezas = [];
    var numeroCaras = 0;
    var i = 0;
    //recorro las piezas
    $('.dientePermanente').each(function() {

        var activo = 0;
        var diente = $(this);
        var cara = diente.find('.cara');

        var CaraActivada = diente.find('.activeCara');


        var vestibular = 0;
        var distal     = 0;
        var palatino   = 0;
        var oclusal    = 0;
        var mesial     = 0;
        var lingual    = 0;

        var $puedopasar     = 0;

        //recorro las caras
        CaraActivada.each(function() {

            // alert( $(this).data('id') );

            if($(this).data('id') == 'vestibular'){
                vestibular++;
            }
            if($(this).data('id') == 'distal'){
                distal++;
            }
            if($(this).data('id') == 'palatino'){
                palatino++;
            }
            if($(this).data('id') == 'oclusal'){
                oclusal++;
            }
            if($(this).data('id') == 'mesial'){
                mesial++;
            }
            if($(this).data('id') == 'lingual'){
                lingual++;
            }

            $puedopasar++; //para capturar el diente
            numeroCaras++;
        });

        // PARA BUSCAR EL DIENTE
        if(seach_diente != null){

            if(seach_diente == diente.data('diente')){

                if( $puedopasar > 0){

                    piezas.push({
                        'diente': diente.data('diente'), //numero del diente
                        'caras' : {
                            'vestibular' : (vestibular > 0) ? true : false,
                            'distal'     : (distal > 0) ? true : false,
                            'palatino'   : (palatino > 0) ? true : false,
                            'oclusal'    : (oclusal > 0) ? true : false,
                            'mesial'     : (mesial > 0) ? true : false,
                            'lingual'    : (lingual > 0) ? true : false,
                        }
                    });

                    return piezas;
                }
            }
        }else{

            if( $puedopasar > 0){

                piezas.push({
                    'diente': diente.data('diente'), //numero del diente
                    'caras' : {
                        'vestibular' : (vestibular > 0) ? true : false,
                        'distal'     : (distal > 0) ? true : false,
                        'palatino'   : (palatino > 0) ? true : false,
                        'oclusal'    : (oclusal > 0) ? true : false,
                        'mesial'     : (mesial > 0) ? true : false,
                        'lingual'    : (lingual > 0) ? true : false,
                    }
                });

            }
        }

        i++;

    });



    dataPrincipal = {
        'piezas' : piezas,
        'numeroCaras' : numeroCaras,
    };

    return dataPrincipal;
}




//asociar Odontologo procesional
var cambiar_attr_id_tratamiento = function(elemento, attrElement){

    var id = elemento.prop('dataset').idtratamiento;
    $(attrElement).attr('data-idtratamiento', id).attr('value',id);
};

$('#asociar_profecional_').on('click', function() {

    var id = $("#PlantTratamientoAsociOdont").val();

    if(id == "" ){
        notificacion('Ocurrio un error de parametros , consulte con soporte','question');
        return false;
    }

    if($('#odontolog_id').find(':selected').val() != ""){

        $.ajax({
            url: $DOCUMENTO_URL_HTTP + '/application/system/pacientes/pacientes_admin/controller/controller_adm_paciente.php',
            type:'POST',
            data:{
                'ajaxSend':'ajaxSend',
                'accion':'UpdateOdontolTratamiento',
                'idTratamiento':id ,
                'idOdontol':$('#odontolog_id').find(':selected').val()
            },
            dataType:'json',
            async:false,
            cache:false,
            success:function(resp) {
                if(resp['error'] == ''){
                    notificacion('Información Actualizada', 'success');
                    $("#odontolog_id").val(null).trigger('change');
                    listplaneTratamiento();
                    $("#modal_asociar_profecional").modal("hide");
                }else{
                   notificacion(resp['error'], 'error');
                   listplaneTratamiento();
                }
            }
        });

    }else{
        notificacion('Debe selecionar un profecional a cargo ','question');
    }
});


//window onload
window.onload = boxloading($boxContentViewAdminPaciente ,true);
//window load
$(window).on("load", function() {
    boxloading($boxContentViewAdminPaciente ,false, 1000);

    //Principal
    $('#odontolog_id').select2({
        placeholder:'Seleccione una opción',
        allowClear:false,
    });
    $('#modal_asociar_profecional').on('show.bs.modal', function () {
        $('#odontolog_id').val(null).trigger('change');
    }) ;
});