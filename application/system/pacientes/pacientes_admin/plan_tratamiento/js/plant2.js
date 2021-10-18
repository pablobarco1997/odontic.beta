

//CUANDO LA PLANFORM - formulario asociar plan de tratamiento una prestaciones o varias

if($accion == 'addplan')
{

    $arr_caras = [
        'vestibular',
        'distal',
        'palatino',
        'mesial',
        'lingual',
        'oclusal'
    ];

    //FORM DETALLE MUESTRA TODOS LOS DETALLES CON SUS ESTADO DE LAS PRESTACIONES GURADAS
    function fetch_plantratamiento(subaccion){

        button_loadding($("#refresh_detalle_table"), true);

        // $formatIndex = 0;
        var datos = {
            'ajaxSend'     : 'ajaxSend',
            'accion'       : 'fetchnewtratamiento',
            'subaccion'    : 'consultar',
            'idpaciente'    : $id_paciente,
            'idtratamiento' : $ID_PLAN_TRATAMIENTO
        };

        $.ajax({
            url: $DOCUMENTO_URL_HTTP +'/application/system/pacientes/pacientes_admin/controller/controller_adm_paciente.php',
            type:'POST',
            data: datos,
            dataType:'json',
            async: true,
            cache: false,
            complete:function(xhr, status){
                button_loadding($("#refresh_detalle_table"), false);
            },
            success:function(respuesta) {

                if(respuesta.error == "") {

                    var cabezera             = respuesta.objetoCab[0];
                    //cab plan de tratamiento
                    $('#addcomment').val( cabezera.observacion );

                    var abonado              =  cabezera.abonado_cab;
                    var profecional          =  cabezera.nombre_doc;
                    var convenio             =  cabezera.convenio;
                    var Nomb_tratam          =  (cabezera.edit_nam == null) ? "Plan de Tratamiento N." + cabezera.numero : cabezera.edit_nam;

                    $convenioValor           = (cabezera.valorConvenio == "") ? "No asignado" : cabezera.valorConvenio; //% Porcentage del convenio asociado
                    $AbonadoGlob             = redondear(abonado, 2, false);

                    //SI EL PLAN DE TRATAMIENTO ESTA FINALIZADO   'F'
                    if(cabezera.estados_tratamiento == 'F'){
                        notificacion('Este plan de tratamiento se encuentra Finalizado, no puede modificarlo', 'question');
                        $('#asociarPrestacion').addClass('disabled_link3');
                        $('#addCommentario').addClass('disabled_link3');
                        $('#detalle-body').addClass('disabled_link3');
                    }
                    //SI EL PLAN DE TRATAMIENTO ESTA ANULADO   == 'E'
                    if(cabezera.estados_tratamiento == 'E'){
                        notificacion('Ud. No puede actualizar este plan de tratamiento se encuentra Anulado', 'error');
                        $('#asociarPrestacion').addClass('disabled_link3');
                        $('#addCommentario').addClass('disabled_link3');
                        $('#detalle-body').addClass('disabled_link3');
                    }

                    /*view header plan de tratamiento*/
                    print_html_cabezera_viewPrincipal(profecional,convenio, Nomb_tratam);

                    // console.log(respuesta['objetoDet']);
                    /*Se comprueba si hay prestaciones agregadas*/
                    if(respuesta.objetoDet.length > 0){

                        var icoCheckedTrue  = respuesta['ico_checked_1'];
                        var icoCheckedFalse = respuesta['ico_checked_2'];

                        var icoDiente = respuesta['ico_diente'];
                        /*So hay prestacion se pinta el detalle */
                        var detalle = respuesta['objetoDet'];
                        print_html_detalle_viewPrincipal(detalle, icoDiente, icoCheckedTrue, icoCheckedFalse);

                        //recalcula los detalles agregados
                        recalculoViewForm();

                    }else{
                        $('#detalle-body').empty();
                    }

                }else{

                    notificacion('Ha ocurrido un error obteniendo la información del plan de tratamiento. Consulte con soporte Técnico', 'error');
                    $("#detdienteplantram").find('.modal-content').addClass('disabled_link3');
                    $("#asociarPrestacion").addClass('disabled_link3');
                    $("#refresh_detalle_table").addClass('disabled_link3');
                    $("#addCommentario").addClass('disabled_link3');

                }
            }
        });

    }


    $("#refresh_detalle_table").click(function () {
        fetch_plantratamiento();
    });

    //PINTA LAS PRESTACIONES GUARDAS EN EL FORMULARIO
    function print_html_detalle_viewPrincipal(tratramientodet, icoDiente, icoCheckedTrue, icoCheckedFalse)
    {

        var html               = "";
        var i = 0;
        while(i <= tratramientodet.length -1){

            //id de la prestacion detalle
            var detalleTratamiento        = tratramientodet[i];
            var rowiddetalle              = tratramientodet[i]['rowid'];
            var usuarioCreatePrestacion   = tratramientodet[i]['usuario_creator']; //usuario quien creo la prestacion
            var usuarioRealizarPrestacion = tratramientodet[i]['usuario_realizado']; //usuario quien realizo la prestacion

            var LaboratorioPrestacion   =  tratramientodet[i]['laboratorio']; //prestacion creada desde un laboratorio
            var iddiente                = tratramientodet[i]['diente'];
            var prestacion              = tratramientodet[i]['prestacion'];
            var fk_prestacion           = tratramientodet[i]['fk_prestacion'];
            var subtotal                = tratramientodet[i]['subtotal'];
            var descAdicional           = tratramientodet[i]['descadicional'];
            var statusdet               = tratramientodet[i]['estadodet']; //estado de la prestacion
            var total1                  = tratramientodet[i]['total'];

            var realizacion             = "";  //estado realizado , procesos o pendiente
            var estado_pago             = tratramientodet[i]['estado_pago'];
            var labelestadoPago         = "";

            if(estado_pago == 'PA'){
                labelestadoPago = '<i class="fa fa-dollar"></i> &nbsp;Pagado';
            }
            if(estado_pago == 'PS'){
                labelestadoPago = '<i class="fa fa-dollar"></i> &nbsp;Saldo';
            }

            //Estado detalle
            if(statusdet=='A') //Pendiente
            { realizacion = '<label class="label" style="background-color: #F6E944; color: #B88B1C; font-weight: bolder;font-size: 0.8em">PENDIENTE</label>' }
            if(statusdet=='P') //En Proceso
            { realizacion = '<label class="label" style="background-color: #7BA5E1; color: #114DA4; font-weight: bolder;font-size: 0.8em">EN PROCESO</label>' }
            if(statusdet=='R') //En Proceso
            { realizacion = '<label class="label" style="background-color: #D5F5E3; color: green; font-weight: bolder;font-size: 0.8em">REALIZADO</label>' }

            //comportamiento realizar prestacion
            var ImgRealizadoChecked   = " <i class='fa fa-square'></i> Realizar ? "; //No realizado
            var onclickRealizadoModal = " href='#modal_prestacion_realizada' data-toggle='modal' title='Realizar esta prestación' "; //Click para realizar la prestacion

            if(tratramientodet[i]['estadodet'] == 'R'){
                var ImgRealizadoChecked = "<i class='fa fa-check'></i> Realizado  ";
                onclickRealizadoModal = " title='Prestación Realizada' ";
            }
            //end comportamiento realizar

            var valor1 = subtotal;
            var total  =  0;
            var valor2 =  0;

            //Descuento de convenio
            if($convenioValor != 0){
                valor1 =  valor1 - ((subtotal * $convenioValor) / 100); //calculo de descuento o convenio
            }

            // alert(prestacion + subtotal);
            valor2 =  parseFloat(valor1) - ((parseFloat(valor1) * parseFloat(descAdicional)) /100);
            total = redondear(valor2, 2, false); //rendondear a los decimales del segundo parametro a 6 o a 2

            var smallUsuarioxCreate     =  ""; //informativo muestra el usuario que creo o agrego esta prestacion al plan de trtam
            if(usuarioCreatePrestacion!="") {
                smallUsuarioxCreate     = "<small  style='display: block;font-weight: bolder'>Creado x Usuario: " + usuarioCreatePrestacion + " </small>";
            }
            var smallUsuarioxRealizado  = "";
            if(usuarioRealizarPrestacion!=""){
                smallUsuarioxRealizado  = "<small  style='display: block;font-weight: bolder'>Realizado x Doctor(a): " + usuarioRealizarPrestacion + " </small>";
            }


            //ESTADO DE LAS PRESTACION
            // A = PENDIENTE O ACTIVA
            // E = ELIMINADO
            // R = PRESTACION REALIZADA
            // P = EN PROCESO
            html += "<tr class='detalleListaInsert'>";

            html += "<td class='dientePieza' data-iddiente='"+iddiente+"' style='padding-left: 15px'>  " +
                        "<div class='form-group col-md-12 col-xs-12 no-padding no-margin' style='padding: 0px !important;'>" +
                        "<div class='form-group col-sm-12 col-xs-12 no-padding no-margin' >" +
                        "<p class='' style='margin: 0px; font-size: 1.5rem' data-id='"+ fk_prestacion +"'>  "+ prestacion +"  &nbsp; <i class='fa fa-flag statusdet' data-estadodet='"+statusdet+"' data-iddet='"+rowiddetalle+"' ></i> </p>  ";

            //Si en caso la prestacion esta relacionada an laboratorio
            if(LaboratorioPrestacion != ''){
                html += "<small class='text-sm' style='margin: 0px' data-diente='"+iddiente+"' title='Laboratorio: "+LaboratorioPrestacion+"'> <i class='fa fa-flask'></i>  &nbsp;&nbsp; "+LaboratorioPrestacion+"  </small>";
            }

            //Muestra el diente asociado a esta prestacion
            if(iddiente != 0){
                html += "<p class='' data-diente='"+iddiente+"' > Pieza: "+iddiente+" &nbsp;&nbsp; " +
                        "     <img src='"+icoDiente+"' width='14px' height='14px' alt=''> " +
                        "</p>";
            }


                    html +=  "<div class='col-sm-12 col-md-12 col-lg-12' style='padding: 3px;  background-color: rgba(221,221,221, 0.3); '>" +
                                " <a class='btn btn-xs text-bold btnhover eliminar_tratamiento'  style='cursor:pointer;color: #9f191f' onclick='UpdateDeletePrestacionAsignada($(this))' > <i class='fa fa-trash'></i> Eliminar  </a>" +
                                " <a class='btn btn-xs text-bold btnhover'  style='cursor:pointer;' "+onclickRealizadoModal+" > "+ImgRealizadoChecked+"  </a>" +
                                " <a style='cursor: pointer' data-toggle='collapse' data-target='#masInformacion-"+i+"' class='btn btn-xs text-bold btnhover'> <i class='fa fa-info-circle'></i> Mas información</a>" +
                                " <a href='#detdienteplantram' data-toggle='modal' class='btn btn-xs text-bold btnhover hide'  style='cursor: pointer'  > <i class='fa fa-edit'></i> Modificar</a>" +
                                " <a href='#modPagosxPacientes' data-toggle='modal' class='btn btn-xs text-bold btnhover "+((labelestadoPago=='')?'hidden':'')+"'  style='cursor: pointer' data-iddet='"+rowiddetalle+"' > "+ labelestadoPago +" </a>" +
                                " <a class='btn btn-xs text-bold btnhover'  style='cursor: pointer' onclick='UpdateDeletePrestacionAsignada($(this), \"P\")' > "+ ((statusdet=='A')?"En Proceso":"") +" </a>" +
                             "</div>" +

                                "       <div class='masInformacion col-xs-12 col-md-12 collapse' id='masInformacion-"+i+"'>" +
                                "           <p class='text-justify no-margin'> " +
                                "              <p class='no-margin' > " +
                                "                 "+smallUsuarioxCreate+"       " +
                                "                 "+smallUsuarioxRealizado+"    " +
                                "               </p>  " +
                                "           </p>" +
                                "       </div>"+

                        "</div> " +
                "   </div>  " +
                " </td>";

            html += "<td>  " +
                    "   <div class='form-group col-md-12 col-xs-12' style='margin-top: 15%'>" +
                    "     " + realizacion + "  " +
                    "   </div>  " +
                    " </td>";

            html += "<td>  " +
                    "   <div class='form-group col-md-12 col-xs-12' style='margin-top: 10%'>" +
                    "     <p class='qty' style='margin: 0px; font-size: 1.5rem; text-align: right'>  <b class='qty'>" + (parseFloat(detalleTratamiento['precio']).toFixed(2)) + " </b> </p> " +
                    "   </div>  " +
                    "</td>";

            html += "<td>  " +
                    "   <div class='form-group col-md-12 col-xs-12' style='margin-top: 10%'>" +
                    "     <p class='qty' style='margin: 0px; font-size: 1.5rem; text-align: right'>  <b class='qty'>" + (parseFloat(detalleTratamiento['cantidad']).toFixed(2)) + " </b> </p> " +
                    "   </div>  " +
                    "</td>";


            //descuento adicional
            html += "<td>  " +
                "   <div class='form-group col-md-12 col-xs-12' style='margin-top: 10%'>" +
                "     <p class='descConvenio' style='margin: 0px; font-size: 1.5rem; text-align: right'>  <b class='descAdicional'>" + descAdicional + " </b> %</p> " +
                "   </div>  " +
                " </td>";

            //iva calculado
            html += "<td>  " +
                "   <div class='form-group col-md-12 col-xs-12' style='margin-top: 10%'>" +
                "     <p class='ivaServicio' style='margin: 0px; font-size: 1.5rem; text-align: right'>  <b class='ivaServicio'>" + detalleTratamiento['iva'] + " </b></p> " +
                "   </div>  " +
                " </td>";

            html += "<td>  " +
                        "   <div class='form-group col-md-12 col-xs-12' style='margin-top: 10%'>" +
                        "     <p class='' style='margin: 0px; font-size: 1.5rem; text-align: right'> $ <b class='total' >" + (parseFloat(total1).toFixed(2)) + " </b> </p> " +
                        "   </div>  " +
                   " </td>";

        //estado pagado
        /*
        html += "<td>  " +
                    "   <div class='form-group col-md-12 col-xs-12'>" +
                    "       <div class='text-center'>" +
                    "           <p style='display: inline-block; color: #00a157; font-size: 2rem'> <i class='fa fa-shopping-cart'> </i> </p> &nbsp; &nbsp;  &nbsp; &nbsp;" +
                    "           <p style='display: inline-block; color: #d10d10'> <i class='fa fa-circle'> </i> </p>" +
                    "       </div>" +
                    "   </div>  " +
             " </td>"; */

            html += "</tr>";

            i++;

        }

        document.getElementById('detalle-body').innerHTML = html;

    }

    //ADD PRESTACION
    function fetch_prestaciones(idprest, async){

        if($("#btn_refresh_addService").hasClass("fa-plus-square")){
            $("#btn_refresh_addService").removeClass("fa-plus-square");
            $("#btn_refresh_addService").addClass("fa-refresh btnSpinner");
        }

        var dataPrest = [];
        $.ajax({
            url: $DOCUMENTO_URL_HTTP +'/application/system/pacientes/pacientes_admin/controller/controller_adm_paciente.php',
            type:'POST',
            data: {'ajaxSend': 'ajaxSend', 'accion':'fetch_prestaciones', 'idprest': idprest},
            dataType:'json',
            cache: false,
            async: async,
            complete:function(xhr, status){
                if($("#btn_refresh_addService").hasClass("fa-refresh btnSpinner")){
                    $("#btn_refresh_addService").removeClass("fa-refresh btnSpinner");
                    $("#btn_refresh_addService").addClass("fa-plus-square");
                }
            },
            success: function (response) {
                dataPrest = response;
                if(async){
                    //Se pinta las prestaciones en el modal
                    print_html_detallePrestacion(response, idprest);

                    //se remueve las clases activas
                    $(".CaraDiv").each(function (index, Element){
                        var Elementdiv = $(Element);
                        $(Element).parents('td').removeClass('PiezaActiva');
                        for (var i = 0;i<=$arr_caras.length-1; i++){
                            Elementdiv.find('.selectCell').removeClass($arr_caras[i]+'Activar');
                        }
                    });
                }
            }
        });

        return dataPrest;
    }

    function print_html_cabezera_viewPrincipal(profecional, convenio, nombTratam) {

        $('#profecional').html("<span>&nbsp;&nbsp;"+profecional+"</span>");
        $('#nomb_plantram').text(nombTratam);
        $('#convenio').text(convenio);
    }

    //PINTA LAS PRESTACIONES AGREGADAS  O GUARDADAS
    function print_html_detallePrestacion(Servicio, idprestacion)
    {

        var puedoAdddet = 0;
        if($('#detalle-prestacionesPlantram').find('tr td').text() == $.trim('NO HAY DETALLE')){
            //limpio las filas en caso no aya datos
            $('#detalle-prestacionesPlantram tr').remove();
        }

        //Obtengo todas las piezas seleciondas Activas
        var objPiezas =  ArrayPeizas();

        //ciclo por diente seleccionados
        var detalle = "";
        if( (objPiezas.length) > 0){
            var io = 0;
            while(io < objPiezas.length) {

                var detalleDiente = objPiezas[io];

                $formatoIndexPrestacion++;
                //obtengo solo las caras de las piezas seleccionadas
                var pieza="";
                if(detalleDiente.pieza > 0){
                    pieza="<small title='Diente: " + detalleDiente.pieza + "'>Pieza: &nbsp; " + detalleDiente.pieza + " <i class='fa fa-tooth'></i> <img src='"+$iconDienteGlob+"' width='15px' height='15px' > </small>";
                }else{
                    pieza="";
                }

                detalle += "" +
                    "<tr  data-idprestacion='" + idprestacion + "'  data-iddiente='" + detalleDiente.pieza + "' name='detalleRow["+$formatoIndexPrestacion+"].detalle' class='detallePrincipalPrestacion' data-caras='"+JSON.stringify(detalleDiente.carasActivas)+"' >" +
                        //eliminar
                        "<td style='width: 500px'> <a class='btn btn-xs text-bold btnhover' style='cursor:pointer;color: #9f191f' id='del-row' onclick='delete_row($(this))'> <i class='fa fa-trash'></i> Eliminar </a> </td>" +
                        //prestacion servicio
                        "<td name='Prestacion[" + $formatoIndexPrestacion + "].detalle' class='prestacion' data-idprestacion='" + idprestacion + "'  data-iddiente='" + detalleDiente.pieza + "'>" +
                            "<p style='margin: 0px'>" + Servicio.descripcion + "</p>" + pieza +
                        "</td>" +
                        //Precio de paciente del servicio
                        "<td style='width: 160px' name='precioServicio[" + $formatoIndexPrestacion + "].detalle' class='precioServicio' >" + Servicio.precio_paciente + "</td>" +
                        //cantidad
                        "<td style='width: 160px'> " +
                            " <input name='cantPresst["+$formatoIndexPrestacion+"].detalle' type='text' class='cantidadPrest input-sm' style='width: 100%;' value='1'> " +
                        "</td> "+
                        //desc adicional
                        "<td style='width: 160px'> " +
                            "<input name='descAdicional["+$formatoIndexPrestacion+"].detalle' type='text' class='adicional input-sm' style='width: 100%' > " +
                        "</td>" +
                        //Iva
                        "<td style='width: 160px' name='iva["+$formatoIndexPrestacion+"].detalle' class='detalle_iva' data-id_iva='"+Servicio.iva+"'></td>" +
                        //Sub Total
                        "<td style='width: 160px' name='totalPrestacion["+$formatoIndexPrestacion+"].detalle' class='totalprestacion'></td>" +
                    "</tr>";
                io++;
            }
        }

        //Se limpia el las piezas activas
        clearModalDetalle('soloActivas');

        //se agrega el HTML a la vista
        $('#detalle-prestacionesPlantram').append($(detalle));

        $(".adicional").maskMoney({precision:2, thousands:'', decimal:'.',allowZero:true,allowNegative:true, defaultZero:true,allowEmpty: true})
            .keyup(function () {
                if($(this).val()<=100){
                    $(this).parent().find(".invalic_descuento").remove();
                }else{
                    //remove elemento
                    $(this).parent().find(".invalic_descuento").remove();
                    $(this).parent().append('<small style="display: block; color: red" class="invalic_descuento">descuento Invalido</small>');
                }
        });

        // CANTIDAD ONKEYUP
        $(".cantidadPrest").keyup(function() {
            recalcularPrestacion( $(this) )
        });
        //DESCUENTO ADICIONAL
        $(".adicional").keyup(function() {
            recalcularPrestacion( $(this) )
        });

        $(".cantidadPrest").maskMoney({precision:0, thousands:'', decimal:'.',allowZero:true,allowNegative:true, defaultZero:true,allowEmpty: true}).keyup();

    }



    //Se valida las prestacion ingresada con el mismo diente == no puede repetirse
    function invalicErrorPrestacionDiente(prestacion, diente, subaccion)
    {

        var PuedoPasar = 0;

        if( $('#detalle-prestacionesPlantram tr').length > 0 )
        {

            $('#detalle-prestacionesPlantram tr').each(function(i, item)
            {

                if(subaccion == 'diente')
                {
                    if($(this).data('idprestacion') == prestacion && $(this).data('iddiente') == diente )
                    {
                        PuedoPasar++;
                        $('#errores_msg_addplantram').text( 'Esta prestación ya esta asignada');
                    }
                }

                if(subaccion == 'prestacion')
                {
                    if( $(this).data('idprestacion') == prestacion && $(this).data('iddiente') == 0)
                    {
                        PuedoPasar++;
                        $('#errores_msg_addplantram').text( 'Esta prestación ya esta asignada');
                    }
                }

            });

            if( PuedoPasar > 0){
                setTimeout(function() {

                    $('#errores_msg_addplantram').text(null);

                },3000)
            }

        }


        //Tiempo real verificar prestaciones guardadas
        $.ajax({
            url: $DOCUMENTO_URL_HTTP +'/application/system/pacientes/pacientes_admin/controller/controller_adm_paciente.php',
            type:'POST',
            data: {
                'ajaxSend': 'ajaxSend',
                'accion':'invalic_prestacion_diente',
                'idplantram': $ID_PLAN_TRATAMIENTO,
                'prestacion': prestacion,
                'diente': diente,
                'subaccion' : subaccion
            },
            dataType:'json',
            async: false,
            success: function(resp) {
                if(resp.error != ''){

                    $('#errores_msg_addplantram').text(resp.error);

                    setTimeout(function() {

                        $('#errores_msg_addplantram').text(null);

                    },3000)

                    PuedoPasar++;

                }
            }

        });

        return PuedoPasar;

    }

    //OBTENER UN ARRAY DE LAS PRESTACION A AGREGAR
    function fetch_objectPrestacionDetalle()
    {

        var informacionPrestacion = []; //GUARDO LAS PRESTACIONES TEMPORAL
        var detencion = '';
        if($('#detencionPermanente').is('checked')){
            detencion = 'permanente';
        }
        if($('#detencionTemporal').is('checked')){
            detencion = 'temporal';
        }
        if( $('.detallePrincipalPrestacion').length > 0 ){

            for( var i = 0; i <= $formatoIndexPrestacion ; i++ ){

                var rowdet = $('[name="detalleRow[' + i + '].detalle"]');
                if(rowdet.length > 0){
                    var iddiente = 0;
                    var idPrestacion     = $('[name="Prestacion[' + i + '].detalle"]').data('idprestacion');
                    iddiente             =  $('[name="Prestacion[' + i + '].detalle"]').data('iddiente');
                    var descConv         = $('[name="convenioPresst[' + i + '].detalle"]').text();
                    var subtotal         = $('[name="precioServicio[' + i + '].detalle"]').text(); //es precio
                    var descAdicional    = $('[name="descAdicional[' + i + '].detalle"]').val();
                    var cantidad         = $('[name="cantPresst[' + i + '].detalle"]').val();
                    var iva              = $('[name="iva[' + i + '].detalle"]').text();

                    //JASON STRING CARAS
                    var Pieza =  rowdet.data('caras');
                    var total = $('[name="totalPrestacion[' + i + '].detalle"]').text();

                    informacionPrestacion.push({
                        'prestacion' : idPrestacion,
                        'iddiente' : iddiente ,
                        'pieza' : Pieza,
                        'subtotal' : subtotal,
                        'descConvenio': descConv ,
                        'descAdicional': descAdicional ,
                        'cantidad': cantidad ,
                        'total' : total ,
                        'detencion'  : detencion,
                        'iva'  : iva,
                    });
                }
            }
        }
        // console.log(informacionPrestacion.filter(Boolean));
        return informacionPrestacion.filter(Boolean);

    }

    //Recalcular prestacion/Servicio
    function recalcularPrestacion(Element){

        var padre               = Element.parents('.detallePrincipalPrestacion');

        if(padre.find('.cantidadPrest')=="")
            padre.find('.cantidadPrest').val(1).keyup();
        if(padre.find('.adicional')=="")
            padre.find('.adicional').val(0).keyup();

        var iva = 0, CalIva=0;
        var precio = 0;
        var cantidad = 0;
        var descuentoAdic = 0;
        var subTotal  = 0;

        precio          = padre.find(".precioServicio").text();
        cantidad        = padre.find(".cantidadPrest").val();
        descuentoAdic   = padre.find(".adicional").val();
        iva             = padre.find(".detalle_iva").prop('dataset').id_iva||0;

        var CalprecioCant   = parseFloat(precio).toFixed(2) * parseFloat(cantidad||0).toFixed(2);
        var CalDescuento    = parseFloat(((parseFloat(CalprecioCant).toFixed(2) * parseFloat(descuentoAdic||0).toFixed(2)) / 100)).toFixed(2);
        var CalSubtotal     = parseFloat(CalprecioCant - CalDescuento).toFixed(2); //precio * cantidad - descuento


        if(iva==12){
            CalIva = parseFloat(CalSubtotal).toFixed(2) * 0.12;
        }
        else{
            CalIva = 0;
        }

        var Total = parseFloat((CalSubtotal + CalIva)).toFixed(2);

        console.log(Total);

        padre.find(".detalle_iva").text(parseFloat(CalIva).toFixed(2));
        padre.find(".totalprestacion").text(Total);

    }


    //RECALCULAR EN LA VISTA DEL FORMULARI DONDE MUESTRO LAS  PRESTACIONES GUARDADAS ----------- DETALLADO
    //SE SUMA LOS TOTALES DE LAS PRESTACIONES
    function recalculoViewForm()
    {
        var Total = 0;
        var TotalRealizado = 0;
        var Total_saldo = 0;
        var Total_Abonado = 0;

        //SE SUMA TODOS LOS TOTALES
        $('.detalleListaInsert').each(function() {

            var padre = $(this);
            var getEstado = padre.find('.statusdet').data('estadodet');
            var totales   = padre.find('.total').text();

            //Si el Plan de Tratamiento se encuentra en estado a A activo
            if(getEstado == 'A' || getEstado == 'P' || getEstado == 'R'){
                Total += parseFloat(totales);
            }
            if(getEstado == 'R'){
                TotalRealizado += parseFloat(totales);
            }
        });


        var totalPresupuesto = Total;
        //TOTAL REDONDEAR RESULTADO DE LAS PRESTACION

        Total_saldo   = parseFloat(Total) - $AbonadoGlob;
        Total_Abonado = redondear($AbonadoGlob);

        var TOTAL_TO = redondear(Total, 2, false);
        var TOTAL_TO_REALIZADO = redondear(TotalRealizado, 2, false);
        var TOTAL_SALDO = redondear(  Total_saldo , 2 , false);
        var TOTAL_ABONADO = Total_Abonado;

        $('#Presu_totalPresu')
            .find('div')
            .text( parseFloat(TOTAL_TO).toFixed(2) );

        $('#Presu_Abonado')
            .find('div')
            .text( parseFloat(TOTAL_ABONADO).toFixed(2) );

        $('#Presu_Realizado')
            .find('div')
            .text( parseFloat(TOTAL_TO_REALIZADO).toFixed(2) );

        $('#Presu_Saldo')
            .find('div')
            .text( parseFloat(TOTAL_SALDO).toFixed(2) );

        $('#saldoPagado')
            .find('div')
            .text( (parseFloat(TOTAL_SALDO)).toFixed(2) );


        //se valida si esta pagada o esta abonado
        if( totalPresupuesto == TOTAL_ABONADO) {
            $('#label_abonadoPagado').text('PAGADO');
        }else{
            $('#label_abonadoPagado').text('ABONADO');
        }

    }

    //GUARDAR LA INFORMACIO DE LAS PRESTACIONES
    //GUARDAR LA PRESTACION AGREGADA AL DETALLE NUEVO PLAN DE TRATAMIENTO DETALLE
    $('#guardarPrestacionPLantram').click(function(){

        if(!ModulePermission('Planes de Tratamientos', 'agregar')){
            notificacion('Ud. No tiene permiso para realizar esta Operación', 'error');
            return false;
        }

        if($(".invalic_descuento").length>0){
            notificacion("Descuento Invalido", "error");
            return false;
        }

        var objInformacion = fetch_objectPrestacionDetalle();

        var $puedoPasar = 0;
        var DetencionPermanente = 0;
        var DetencionTemporal = 0;
        var DetencionLabel = "";

        if($('#detencionPermanente').is(':checked')){
            DetencionPermanente++;
            DetencionLabel = "permanente";
        }

        if($('#detencionTemporal').is(':checked')){
            DetencionTemporal++;
            DetencionLabel = "temporal";
        }

        if($('#detencionTemporal').is(':checked') == false && $('#detencionPermanente').is(':checked') == false){
            $puedoPasar = 2
        }

        if($puedoPasar == 2){
            notificacion('Debe Seleccionar una Detención', 'question');
        }

        if(DetencionTemporal == 1 && DetencionPermanente == 1){
            $('#errores_msg_addplantram').text('Solo puede seleccionar una Detención');
            $puedoPasar++;
            setTimeout(function() {
                $('#errores_msg_addplantram').text(null);
            },3000);
        }

        if(objInformacion.length == 0 ){
            notificacion('No hay ningun detalle asignado', 'question');
        }

        if(objInformacion.length > 0 &&  $puedoPasar == 0){
            button_loadding($("#guardarPrestacionPLantram"), true);
            $.ajax({
                delay:500,
                url: $DOCUMENTO_URL_HTTP + "/application/system/agenda/controller/agenda_controller.php",
                type:'POST',
                data: {
                    'ajaxSend':'ajaxSend',
                    'accion': 'nuevoUpdatePlanTratamientoDetalle',
                    'datos': objInformacion ,
                    'idtratamiento': $ID_PLAN_TRATAMIENTO,
                    'idpaciente': $id_paciente, /*Id del paciente Global*/
                    'subaccion': 'create',
                    'detencion': DetencionLabel
                },
                dataType:'json',
                async: true,
                cache: false,
                complete:function(xhr, status){
                    button_loadding($("#guardarPrestacionPLantram"), false);
                },
                success: function(resp){
                    if( resp.error != ''){
                        notificacion(resp.error , 'error');
                    }else{
                        clearModalDetalle('todo');
                        $('#detdienteplantram').modal('hide');
                        fetch_plantratamiento(); //reload lista de plantram form
                        setTimeout(function () {
                            notificacion('Información Actualizada' , 'success');
                        },500);
                    }
                }
            });

        }

        recalculoViewForm(); //RECALCULAR EL TOTAL DE PRESTACION

    });

    //AGREGAR DETALLE PRESTACIONES ASOCIADAS A ESTE PLAN DE TRATAMIENTO
    $("#addprestacionPlantram").click(function() {

        if( $("#prestacion_planform").find(':selected').val() != "" ){

            var idprestacion = $("#prestacion_planform").find(':selected').val();
            if(idprestacion > 0){
                //se pinta el detalle a agregar de manera async
                fetch_prestaciones(idprestacion, true);
            }
        }else{
            notificacion('Debe seleccionar una prestación', 'error');
        }

        $("#prestacion_planform").val(null).trigger('change');

    });

    //eliminar row del modal detalles de prestaciones
    function delete_row(este) {
        var row = este.parents('.detallePrincipalPrestacion');
        row.remove();
    }

    //refresco las caras activadas de las piezas
    $("#detdienteplantram").on("show.bs.modal", function () {
        $(".CaraDiv").each(function (index, Element){
            var Elementdiv = $(Element);
            $(Element).parents('td').removeClass('PiezaActiva');
            for (var i = 0;i<=$arr_caras.length-1; i++){
                Elementdiv.find('.selectCell').removeClass($arr_caras[i]+'Activar');
            }
        });
    });

    // $(".cantidadPrest").maskMoney({precision:1,thousands:'', decimal:'.',allowZero:true,allowNegative:false, defaultZero:true,allowEmpty: true});

}


// ---------------------------------------------------------------------------------------------------------------------



if($accion == "principal")
{


    /** COMPORTAMIENTOS LISTA PRINCIPAL **/
    function  optionTratamiento(idplantratamiento, subaccion)
    {

        //edit name -- nombre del plan de tratamiento
        //edit name -- nombre del plan de tratamiento
        if(idplantratamiento!="" && subaccion == 'editname' )
        {

            if(!ModulePermission('Planes de Tratamientos','modificar')){
                notificacion('Ud. No tiene permiso para modificar', 'error');
                return false;
            }

            $('#modnombPlantratamiento').modal('show');
            $('#idplanTratamientotitulo').attr('data-id', idplantratamiento);
            $('#nametratamiento').val(null);
        }

        // finalizar  plan de tratamiento
        if(idplantratamiento !="" && subaccion == 'finalizar_plantram' )
        {
            // finalizarPlanTratamiento(idplantratamiento, 'consultarfinalizado');
            $('#mg_finalizar_plantramiento').html( null );
            $('#confirm_finalizar_plantramiento').modal('show');
            $("#finalizar_plantramiento").attr('onclick', 'finalizarPlanTratamiento('+idplantratamiento+', \'finalizar_plantram\' )');
        }

    }

    function finalizarPlanTratamiento( idplantratamiento, subaccion )
    {
        button_loadding($("#finalizar_plantramiento"), true);
        $.ajax({
            url: $DOCUMENTO_URL_HTTP + "/application/system/pacientes/pacientes_admin/controller/controller_adm_paciente.php",
            type:'POST',
            data: {
                'ajaxSend'   : 'ajaxSend',
                'accion'     : 'finalizar_plantramento',
                'subaccion'  :  subaccion,
                'idplant'    :  idplantratamiento,
                'idpaciente' :  $id_paciente
            },
            dataType:'json',
            async: true,
            cache: false,
            complete: function(xhr, ststus){
                button_loadding($("#finalizar_plantramiento"), false);
            },
            success: function(resp){
                if(subaccion == 'finalizar_plantram'){
                    if(resp.error.toString() == ''){
                        $('#confirm_finalizar_plantramiento').modal('hide');
                        notificacion('Información Actualizada', 'success');
                    }else{
                        $('#mg_finalizar_plantramiento').html( resp.consultar );
                    }
                }
                button_loadding($("#finalizar_plantramiento"), false);
            }
        });
    }

        //comportamiento cambiar nombre del tratamiento
        $('#acetareditNomPlanT').click(function(){

            if(!ModulePermission('Planes de Tratamientos','modificar')){
                notificacion('Ud. No tiene permiso para modificar', 'error');
                return false;
            }

            button_loadding($("#acetareditNomPlanT"), true);
            $.ajax({
                url: $DOCUMENTO_URL_HTTP + "/application/system/pacientes/pacientes_admin/controller/controller_adm_paciente.php",
                type:'POST',
                data: {
                    'ajaxSend':'ajaxSend',
                    'accion': 'editnametratamiento',
                    'id': $('#idplanTratamientotitulo').data('id'),
                    'name' : $('#nametratamiento').val()
                },
                dataType:'json',
                async: true,
                cache: false,
                complete:function(xhr, status){
                    button_loadding($("#acetareditNomPlanT"), false);
                },
                success: function(resp){
                    if(resp.error == ''){
                        $('#modnombPlantratamiento').modal('hide');
                        var table = $('#listtratamientotable').DataTable();
                        table.ajax.reload(null, false);
                    }
                    button_loadding($("#acetareditNomPlanT"), false);
                }

            });
        });

    /*ESTA FUNCION ME PERMITE ELIMINAR EL PLAN DE TRATAMIENTO*/
    function eliminarPlan_tratamiento(padreDom, $idplantratamientoDom)
    {
        if(!ModulePermission('Planes de Tratamientos','eliminar')){
                notificacion('Ud. No tiene permisos para Anular ','error');
            return false;
        }

        var Dom = padreDom.parents('.row_list_plantram');

        if($idplantratamientoDom > 0){
            preguntar_confirm_eliminacion($idplantratamientoDom, 'eliminar_plantcab_preguntar');
        }
    }
    
    function preguntar_confirm_eliminacion(idplantDom, subaccion ){


        if(subaccion=='eliminar_plantcab_preguntar'){
            boxloading($boxContentViewAdminPaciente ,true);
        }
        if(subaccion=='confirm_eliminar'){
            button_loadding($('#delete_plantram_confirm'), true);
        }

        var preguntar = false;
        $.ajax({
            url: $DOCUMENTO_URL_HTTP +'/application/system/pacientes/pacientes_admin/controller/controller_adm_paciente.php',
            type:'POST',
            data: {'ajaxSend': 'ajaxSend', 'accion':'confirm_eliminar_plantratamiento', 'idplan':idplantDom, 'idpaciente': $id_paciente ,  'subaccion': subaccion},
            dataType:'json',
            async: true,
            cache:false,
            complete: function(xhr, status){
                if(subaccion=='eliminar_plantcab_preguntar'){
                    boxloading($boxContentViewAdminPaciente ,false, 1000);
                }
                if(subaccion=='confirm_eliminar'){
                    button_loadding($('#delete_plantram_confirm'), false);
                }
            },
            success:function(respuesta){
                if(respuesta.error > 0){
                    notificacion(respuesta.errores, 'error');
                }else{
                    //confirmar - para la eliminacion
                    $('#confirm_eliminar_plantram').modal('show');
                    $('#msg_eliminar_plantram').html( respuesta.msgConfirm );

                        //subaccion preguntar si puedo eliminar elemento
                        if(subaccion=='eliminar_plantcab_preguntar'){
                            //Se cambia el atributo para confirmaar la eliminacion
                            //se cambia el atributo con la funcion  delete_confirmar_true_plantram
                            // alert($idplantratamientoDom);
                            $('#delete_plantram_confirm').attr('data-id', idplantDom);
                        }
                    preguntar = true;
                    if(respuesta.acierto > 0){
                        //datos actualizados
                        $('#confirm_eliminar_plantram').modal('hide');
                        setTimeout(()=>{
                            notificacion('Información Actualizada', 'success');
                        },500);
                        listplaneTratamiento();
                    }else{
                        $('#msg_eliminar_plantram').html( respuesta.msgConfirm );
                    }
                }
            }
        });
        return preguntar;
    }

    // eliminar plan de tratamiento confirmado
    function delete_confirmar_true_plantram(Elemento){

        if(!ModulePermission('Planes de Tratamientos','eliminar')){
            notificacion('Ud. No tiene permiso para Anular', 'error');
            return false;
        }

        if( Elemento.prop('dataset').id != "" ){
            preguntar_confirm_eliminacion(Elemento.prop('dataset').id, 'confirm_eliminar');
        }else {
            notificacion('Ocurrio un error de parametros de entrada', 'error');
        }
    }

}


//OBTENER EL ID DE UNA URL CON JQUERY
function Get_jquery_URL(Getparam)
{
    let paramsGet = new URLSearchParams(location.search);
    var idGetUrl = paramsGet.get(Getparam);

    return idGetUrl;
}

function ArrayPeizas(){
    var objPieza  = [];
    $(".PiezaActiva").each(function (i,item) {

        objPieza.push({
            'pieza': $(item).attr("id"),
            'carasActivas': {
                'vestibular': ($(item).find(".vestibularActivar").length>0)?true:false,
                'distal':($(item).find('.distalActivar').length>0)?true:false,
                'palatino':($(item).find('.palatinoActivar').length>0)?true:false,
                'mesial':($(item).find('.mesialActivar').length>0)?true:false,
                'lingual':($(item).find('.lingualActivar').length>0)?true:false,
                'oclusal':($(item).find('.oclusalActivar').length>0)?true:false
            }
        });
    });

    if(objPieza.length==0){
        objPieza.push({
            'pieza':0,
            'carasActivas': {
                'vestibular': false,
                'distal':false,
                'palatino':false,
                'mesial':false,
                'lingual':false,
                'oclusal':false
            }
        });
    }

    return objPieza;
}

$("#prestacion_planform").select2({
    placeholder: 'Buscar prestaciones',
    allowClear:true,
    language: languageEs,
    minimumInputLength:1,
    ajax:{
        delay: 500,
        url: $DOCUMENTO_URL_HTTP + '/application/system/pacientes/pacientes_admin/controller/controller_adm_paciente.php',
        type: "POST",
        dataType: 'json',
        async:true,
        cache: false,
        data:function (params) {
            var query = {
                accion: 'prestacionesSearchSelect2',
                ajaxSend:'ajaxSend',
                paciente_id: $id_paciente,
                search: params.term,
            };
            return query;
        },
        processResults:function (data) {
            return data;
        }
    }
});


$("#modPagosxPacientes").on('show.bs.modal', function (e) {


    var iddetalle = $(e.relatedTarget).prop('dataset').iddet;

    if(iddetalle != ""){
        if(iddetalle != 0){

            var table3 = $("#pagosxpacientes_prestaciones").DataTable({
                searching: false,
                destroy: true,
                "ordering":false,
                "serverSide": true,
                lengthChange: false,
                fixedHeader: true,
                paging:true,
                processing: true,
                lengthMenu:[ 10 ],
                "ajax":{
                    "url": $DOCUMENTO_URL_HTTP + '/application/system/pacientes/pacientes_admin/controller/controller_adm_paciente.php',
                    "type":'POST',
                    "data": {
                        'ajaxSend'             : 'ajaxSend',
                        'accion'               : 'pagosxpacientes_prestaciones',
                        'iddetalle'            :  iddetalle ,
                        'idpaciente'           :  $id_paciente,
                        'idtratamiento'        :  $ID_PLAN_TRATAMIENTO,
                    },
                    "dataType":'json',
                    "complete": function(xhr, status) {
                        boxloading($boxContentViewAdminPaciente ,false, 1000);
                    }
                },
                language: {
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

});



