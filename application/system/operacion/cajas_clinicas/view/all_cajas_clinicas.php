<?php

#breadcrumbs  -----------------------------------------------
$url_breadcrumbs = $_SERVER['REQUEST_URI'];
$titulo = "Cajas Abiertas";
$modulo = true;

?>




<div class="form-group col-xs-12 col-md-12 col-lg-12 no-padding">
    <div  class="form-group col-md-12 col-xs-12">
        <?= Breadcrumbs_Mod($titulo, $url_breadcrumbs, $modulo); ?>

        <label for="">LISTA DE COMPORTAMIENTOS</label>
        <ul class="list-inline" style="background-color: #f4f4f4; border-bottom: 0.6px solid #333333; padding: 3px; margin-left: 0px">
            <li><a href="#contentFilter" data-toggle="collapse" style="color: #333333" class="btnhover btn btn-sm " id="fitrar_document"> <b>  ▼ &nbsp;Filtrar <i></i> </b> </a></li>
        </ul>
    </div>

    <div class="form-group col-xs-12 col-md-12 col-lg-12 collapse contentFilterCajasClinicas" id="contentFilter" aria-expanded="true" style="margin-bottom: 0px">
        <div class="col-md-12 col-xs-12 col-lg-12" style="background-color: #f4f4f4; padding-top: 15px">
            <div class="form-group col-md-12 col-xs-12 col-lg-12"> <h3 class="no-margin"><span>Filtrar Cajas Clinca</span></h3> </div>

            <div class="form-group col-md-4 col-xs-12 col-lg-3">
                <label for="">Fecha de apertura</label>
                <div class="input-group form-group rango" style="margin: 0">
                    <input type="text" class="form-control   date_" readonly="" id="date_apertura_caja" value="" style="font-size: small">
                    <span class="input-group-addon" style="border-radius: 0"><i class="fa fa-calendar"></i></span>
                </div>
            </div>

            <div class="form-group col-md-4 col-xs-12 col-lg-3">
                <label for="">Fecha de Cierre</label>
                <div class="input-group form-group rango" style="margin: 0">
                    <input type="text" class="form-control   date_" readonly="" id="date_cierre_caja" value="" style="font-size: small">
                    <span class="input-group-addon" style="border-radius: 0"><i class="fa fa-calendar"></i></span>
                </div>
            </div>

            <div class="form-group col-md-4 col-xs-12 col-lg-3">
                <label for="">Usuario</label>
                <select name="usuario_caja" id="usuario_caja" style="width: 100%" class="form-control">
                    <option value=""></option>
                    <?php
                        $query = "select usuario, rowid, fk_doc from tab_login_users where estado  = 'A' ";
                        $result_usu_ape_caja = $db->query($query);
                        if($result_usu_ape_caja){
                            if($result_usu_ape_caja->rowCount()>0){
                                $all = $result_usu_ape_caja->fetchAll(PDO::FETCH_ASSOC);
                                foreach ($all as $value){
                                    if($value['fk_doc'] != 0){
                                        $odotc = getnombreDentiste($value['fk_doc']);
                                        $doct = 'Doctor(a): '.$odotc->nombre_doc.' '.$odotc->apellido_doc;
                                    }else{
                                        $doct ="";
                                    }

                                    $label = 'usuario: '.$value['usuario'] .'&nbsp;'.$doct;
                                    print '<option value="'.$value['rowid'].'">'.$label.'</option>';
                                }
                            }
                        }

                    ?>
                </select>
            </div>

            <div class="form-group col-md-3 col-xs-12 col-lg-3">
                <label for="">Acumulado</label>
                <input type="text" class="form-control" id="acumulado_caja">
            </div>

            <div class="form-group col-md-4 col-xs-12 col-lg-3">
                <label for="">Estado</label>
                <select name="estadoCajaClinica" id="estadoCajaClinica" style="width: 100%">
                    <option value=""></option>
                    <option value="C">Cerrada</option>
                    <option value="A">Abierta</option>
                </select>
            </div>

            <div class="form-group col-md-12 col-xs-12">
                <ul class="list-inline pull-right">
                    <li>  <button class="limpiar btn   btn-block  btn-default" id="limpiarCajasFiltro" style="float: right; padding: 10px"> &nbsp; &nbsp; Limpiar &nbsp; &nbsp;</button> </li>
                    <li>  <button class="aplicar btn   btn-block  btn-success" id="aplicarCajasFiltro" style="float: right; padding: 10px"> &nbsp;  &nbsp;Aplicar busqueda &nbsp;</button> </li>
                </ul>
            </div>

        </div>
    </div>

    <div class="form-group col-xs-12 col-md-12" style="margin-top: 10px">
        <span style=" color: #eb9627">
                        <i class="fa fa-info-circle"></i>
                                    Si desea eliminar un pago de una caja cerrada este es eliminada de dicha caja
                        </span>
    </div>

    <div class="form-group col-xs-12 col-md-12">
        <button class="aplicar btn  btn-sm "  title="Abrir caja" data-target="#modal_abrir_cajas_clinicas_fn" data-toggle="modal" style="float: right; padding: 5px" > &nbsp;  &nbsp;<b>Abrir Caja</b> &nbsp;</button>
    </div>

    <div class="form-group col-xs-12 col-md-12">
        <div class="table-responsive">
            <table class="table table-condensed " width="100%"  id="all_Cuenta_de_aperturas" >
                <thead style="background-color: #f4f4f4; ">
                    <tr>
                        <th>usuario</th>
                        <th>caja</th>
                        <th>Fecha de apertura</th>
                        <th>Fecha de cierre</th>
<!--                        <th>Saldo anterior</th>-->
                        <th>Saldo inicial</th>
                        <th>Acumulado</th>
                        <th></th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="modal_abrir_cajas_clinicas_fn" role="dialog" data-backdrop="static" >
        <div class="modal-dialog" >

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header modal-diseng">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title"><span>Apertura de Caja <?= date("Y/m/d") ?></span></h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="form-group col-xs-12 col-md-12">
                            <label for="">Usuario Responsable</label>
                            <select name="apertura_abrir_caja_usuario" id="apertura_abrir_caja_usuario" class="form-control" style="width: 100%">
                                <option></option>
                                <?php
                                    $query = "select usuario, rowid, fk_doc from tab_login_users where estado  = 'A' ";
                                    $result_usu_ape_caja = $db->query($query);
                                    if($result_usu_ape_caja){
                                        if($result_usu_ape_caja->rowCount()>0){
                                            $all = $result_usu_ape_caja->fetchAll(PDO::FETCH_ASSOC);
                                            foreach ($all as $value){


                                                if($value['fk_doc'] != 0){
                                                    $odotc = getnombreDentiste($value['fk_doc']);
                                                    $doct = 'Doctor(a): '.$odotc->nombre_doc.' '.$odotc->apellido_doc;
                                                }else{
                                                    $doct ="";
                                                }

                                                $label = 'usuario: '.$value['usuario'] .'&nbsp; '.$doct;
                                                print '<option value="'.$value['rowid'].'">'.$label.'</option>';
                                            }
                                        }
                                    }

                                ?>
                            </select>
                        </div>

                        <div class="form-group col-xs-12 col-md-12">
                            <label for="">Cuenta Caja Clinica</label>
                            <select name="apertura_abrir_cuentas_caja" id="apertura_abrir_cuentas_caja" class="form-control" style="width: 100%">
                                <option></option>
                            </select>
                        </div>

                        <div class="form-group col-xs-12 col-md-12">
                            <label for="">Saldo Inicial <span class="text-sm">(opcional)</span></label>
                            <input type="text" name="apertura_abrir_saldoInicial_caja" id="apertura_abrir_saldoInicial_caja" class="form-control" value="0.00" >
                        </div>

                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn "  style="color: green; font-weight: bold" id="guardarEditNameCuen" onclick="" > Guardar
                        <span class = "fa fa-refresh btnSpinner hide"> </span>
                    </button>
<!--                    <button type="button" class="btn btn-default" data-dismiss="modal"> cancelar </button>-->
                </div>
            </div>

        </div>
    </div>

</div>






<script>


    var CerrarCajaAsociada = function (Element) {

        var id = Element.prop('dataset').id;
        //crea el ojecto y guardas la funcion en el
        var object = {
            id: id,
            callback: function () {
                boxloading($boxContentCajasClinicas, true);
                $.ajax({
                    url: $DOCUMENTO_URL_HTTP + '/application/system/operacion/cajas_clinicas/controller/caja_controller.php',
                    delay:1000,
                    type:'POST',
                    data:{   'ajaxSend':'ajaxSend', 'accion':'cerrar_caja', 'id_ope_caja': id },
                    async:true,
                    cache:false,
                    dataType:'json',
                    complete: function(xhr, status){
                        boxloading($boxContentCajasClinicas, false, 1000);
                    },
                    success:function (response) {
                        // console.log(response);
                        boxloading($boxContentCajasClinicas, false, 1000);
                        if(response.error==""){
                            var table = $("#all_Cuenta_de_aperturas").DataTable();
                            table.ajax.reload(null, false);
                            setTimeout(()=>{ notificacion('información Actualizada', 'success'); }, 700);
                        }else{
                            setTimeout(()=>{ notificacion(response.error, 'error'); }, 700);
                        }
                    }
                });
            }
        };

        notificacionSIoNO("Cerrar Caja",null, object);
    };

    var form_validar_apertura_caja = function(){

        var Errores = [];
        var parent = $('#modal_abrir_cajas_clinicas_fn');
        var user_caja    = parent.find("#apertura_abrir_caja_usuario");
        var cuenta_caja  = parent.find("#apertura_abrir_cuentas_caja");
        var saldoInicial = parent.find("#apertura_abrir_saldoInicial_caja");

        if(user_caja.find("option:selected").val() == ""){
            Errores.push({
                "documento" :   user_caja,
                "mesg" :  "obligatorio",
            });
        }
        if(cuenta_caja.find("option:selected").val() == ""){
            Errores.push({
                "documento" :   cuenta_caja,
                "mesg" :  "obligatorio",
            });
        }

        if(((/^\s*$/).test(saldoInicial.val())) == true){
            Errores.push({
                "documento" :   saldoInicial,
                "mesg" :  "El campo no puede estar vacio puede ser 0 o mayor a 0",
            });
        }

        var valid = true;

        $(".error_caja_fn").remove();

        if(Errores.length>0){

            for (var i=0; i<=Errores.length-1;i++ ){

                var menssage =  document.createElement("small");
                menssage.setAttribute("style","display: block; color:blue;");
                menssage.setAttribute("class","error_caja_fn");
                menssage.appendChild(document.createTextNode(Errores[i]['mesg']));
                var documentoDol        = Errores[i]['documento'];

                console.log(documentoDol);
                if(documentoDol[0].localName=='select')
                    $(menssage).insertAfter(documentoDol.parent('.form-group').find('span:eq(0)'));
                if(documentoDol[0].localName=='input')
                    $(menssage).insertAfter(documentoDol);




            }
            valid = false;
        }else{
            valid = true;
        }

        return valid;

    };

    $('#modal_abrir_cajas_clinicas_fn').on('show.bs.modal', function () {

        var parent = $(this);
        var user_caja   = parent.find("#apertura_abrir_caja_usuario");
        var cuenta_caja = parent.find("#apertura_abrir_cuentas_caja");
        var saldoInicial = parent.find("#apertura_abrir_saldoInicial_caja");

        user_caja.val(null).trigger("change");
        cuenta_caja.val(null).trigger("change");
        saldoInicial.val("0.00");

        form_validar_apertura_caja();

    });

    $(document).ready(function () {

        $("#apertura_abrir_caja_usuario").select2({
            placeholder: 'seleccione un Usuario',
            language: languageEs
        });

        $("#apertura_abrir_cuentas_caja").select2({
            placeholder: 'buscar una cuenta caja',
            language: languageEs,
            delay: 500,
            ajax:{
                url: $DOCUMENTO_URL_HTTP + '/application/system/operacion/cajas_clinicas/controller/caja_controller.php',
                type:'POST',
                async:true,
                cache:false,
                dataType:'json',
                data: function (params) {
                    var query = {
                        'ajaxSend' : 'ajaxSend',
                        'accion'   : 'buscar_caja_clinica_ape',
                        'buscar' : params.term
                    };
                    return query;
                },
                delay: 500,
                processResults: function (results) {
                    // console.log(results);
                    return results;
                }
            }
        });

        $("#apertura_abrir_saldoInicial_caja").maskMoney({precision:2, thousands:'', decimal:'.',allowZero:true,allowNegative:true, defaultZero:true,allowEmpty: true});

    });

    function apertura_Caja_Date(){

        if(!ModulePermission('Cajas Clinicas','agregar')){
            notificacion('Ud. No tiene permiso para esta Operación', 'error');
            return false;
        }

        if(form_validar_apertura_caja() == false){
            return false;
        }


        button_loadding($("#guardarEditNameCuen"), true );

        var parent       = $('#modal_abrir_cajas_clinicas_fn');
        var user_caja    = parent.find("#apertura_abrir_caja_usuario");
        var cuenta_caja  = parent.find("#apertura_abrir_cuentas_caja");
        var saldoInicial = parent.find("#apertura_abrir_saldoInicial_caja");

        // parent.modal('hide'); //oculto el modal una vez fetch datos

        var params = {
            accion          :'apertura_caja',
            ajaxSend        :'ajaxSend',
            id_cuenta_caja  : cuenta_caja.find(':selected').val(),
            id_user_caja    : user_caja.find(':selected').val(),
            saldoInicial    : saldoInicial.val(),
        };

        $.ajax({
            url: $DOCUMENTO_URL_HTTP + '/application/system/operacion/cajas_clinicas/controller/caja_controller.php',
            type:'POST',
            delay: 500,
            async:true,
            cache:false,
            data: params,
            dataType:'json',
            complete: function(xhr, status){
                button_loadding($("#guardarEditNameCuen"), false );
            },
            success:function (response) {
                if(response.results.error != ""){
                    notificacion(response.results.error, 'error');
                }else{

                    var table = $("#all_Cuenta_de_aperturas").DataTable();
                    table.ajax.reload(null, false);
                    button_loadding($("#guardarEditNameCuen"), false );

                    parent.modal('hide');
                    setTimeout(()=>{
                        notificacion('Nueva Caja abierta', 'success');
                    },100);
                }
            }
        });
    }

    function list_cajas_abiertas(){

        $("#all_Cuenta_de_aperturas").DataTable({
            searching: false,
            "ordering":false,
            "serverSide": true,
            // responsive: true,
            destroy:true,
            scrollX: false,
            // scrollY: 500,
            lengthChange: false,
            fixedHeader: true,
            paging:true,
            processing: true,
            lengthMenu:[ 10 ],
            "ajax":{
                "url":$DOCUMENTO_URL_HTTP + '/application/system/operacion/cajas_clinicas/controller/caja_controller.php',
                "type":'POST',
                "data": {
                    'ajaxSend'   : 'ajaxSend',
                    'accion'     : 'list_cajas_abiertas',
                    'apertura'   : $('#date_apertura_caja').val(),
                    'cierre'     : $('#date_cierre_caja').val(),
                    'users'      : $('#usuario_caja').val(),
                    'acumulado'  : $('#acumulado_caja').val(),
                    'estado'     : $('#estadoCajaClinica').find(":selected").val(),
                },
                cache:false,
                "dataType":'json',
                "complete": function(xhr, status) {

                }
            },
            columnDefs:[
                {
                    targets:6,
                    render: function (data, type, row) {

                        var fetch = $.parseJSON(atob(row['datos']));
                        // console.log(fetch);

                        var url_detalles_caja = $DOCUMENTO_URL_HTTP+"/application/system/operacion/cajas_clinicas/index.php?view=detalles_cajas&key="+$keyGlobal+"&idcaj="+fetch['rowid'];

                        var menu = "<div class='dropdown pull-right'> ";
                        menu += "<div class='btn btnhover  btn-xs dropdown-toggle ' type='button' data-toggle='dropdown' aria-expanded='false'> <i class='fa fa-ellipsis-v'></i> </div>";
                        menu += "<ul class='dropdown-menu'>";
                        menu += "<li> <a href='"+url_detalles_caja+"' style='cursor: pointer; '>detalles de caja</a> </li>";
                        menu += "<li> <a href='#'  style='cursor: pointer; ' data-id='"+fetch['rowid']+"' onclick='CerrarCajaAsociada($(this))'>Cerrar Caja</a> </li>";
                        menu += "</ul>";
                        menu += "</div>";

                        return menu;
                    }
                }
            ],
            'createdRow': function (row, data, index) {

                $(row).children().eq(0).css('width','89px');
                $(row).children().eq(1).css('width','354px');
                $(row).children().eq(2).css('width','192px');
                $(row).children().eq(3).css('width','164px');
                $(row).children().eq(4).css('width','151px');
                $(row).children().eq(5).css('width','133px');
                $(row).children().eq(6).css('width','50px');
                // $(row).children().eq(7).css('width','17px');

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
            "infoCallback": function (settings, start, end, max, total, pre){

                return "Mostrando registros del "+ start +" al "+ end +" de un total de "+total+ " registros.";
            }

        });
    }

    //se detecta los cambios al selecionar usuaruioo caja
    $("#apertura_abrir_caja_usuario, #apertura_abrir_cuentas_caja").on('change', form_validar_apertura_caja);
    $("#apertura_abrir_saldoInicial_caja").on('keyup', form_validar_apertura_caja);

    //se agrega la funcion apertura_Caja_Date un evento click para crear la caja
    var btnAperturaCaja_crear = $("#guardarEditNameCuen");
    btnAperturaCaja_crear.click(function () {
        apertura_Caja_Date();
    });


    $("#limpiarCajasFiltro").click(function () {

        $(".contentFilterCajasClinicas")
            .find('input').val(null);

        $(".contentFilterCajasClinicas")
            .find('select').val(null).trigger('change');

        list_cajas_abiertas();
        
    });


    $("#aplicarCajasFiltro").click(function () {
        list_cajas_abiertas();
    });


    $("#date_apertura_caja").change(function () {
        if($(this).val()!=""){
            $("#date_cierre_caja").val(null)
        }

    });
    $("#date_cierre_caja").change(function () {
        if($(this).val()!=""){
            $("#date_apertura_caja").val(null)
        }
    });


    window.onload =  boxloading($boxContentCajasClinicas, true);


    $(window).on('load', function () {


        if(!ModulePermission('Cajas Clinicas','consultar')){
            notificacion('Ud. No tiene permiso para Consultar', 'error');
        }
        boxloading($boxContentCajasClinicas, true, 1000);

        $('#usuario_caja').select2({
            placeholder: 'buscar usuario',
            allowClear: true,
            language: languageEs
        });

        $('#estadoCajaClinica').select2({
            placeholder: 'Seleccione una opción',
            allowClear: true,
            language: languageEs
        });

        $('.date_').daterangepicker({

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

        // $('.rango span').click(function() {
        //     $(this).parent().find('input').click().val(null);
        // });

        list_cajas_abiertas();

        $("#acumulado_caja").maskMoney({precision:2,thousands:'', decimal:'.',allowZero:true,allowNegative:true, defaultZero:true,allowEmpty: true});

    });



</script>


