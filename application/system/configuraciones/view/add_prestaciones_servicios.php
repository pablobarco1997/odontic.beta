<?php

if(isset($_GET['v'])){
    if($_GET['v']=='list'){
        $v = 'list';
    }else if($_GET['v']=='add'){
        $v = 'add';
    }else{
        $v = '';
    }
}else{
    $v = '';
    echo 'Ocurrio un error. Parámetros de entrada, Consulte con soporte';
}

?>



<div class="box box-solid">

    <div class="box-header with-border">
        <div class="form-group col-xs-12 col-sm-12 col-md-12 no-margin no-padding">
            <h4 class="no-margin"><span><b>
                        Prestación/Servicios   </b></span></h4>
        </div>
    </div>

    <div class="box-body">

        <?php

            accessoModule('Prestaciones');

        ?>

        <div class="form-group form-group col-xs-12 col-md-12">
            <div class="form-group col-centered col-xs-12 col-md-11 col-lg-10 col-sm-12" style="margin-top: 30px">
                <div class="form-group col-md-12 col-xs-12">
                    <ul class="list-inline" style="border-bottom: 1px solid #333333; border-top: 1px solid #333333; margin-left: 0px">
                        <li>
                            <a href="<?= DOL_HTTP.'/application/system/configuraciones/index.php?view=servicios&v=list' ?>" class="btnhover btn btn-sm" style="font-weight: bolder; color: #333333; ">
                                <b>Lista Prestaciones Servicios</b>
                            </a>
                        </li>
                        <li>
                            <a href="<?= DOL_HTTP.'/application/system/configuraciones/index.php?view=servicios&v=add' ?>" class="btnhover btn btn-sm" style="font-weight: bolder; color: #333333; ">
                                <b>Crear Prestaciones Servicios</b>
                            </a>
                        </li>
                    </ul>
                </div>


                <?php
                if($v=='list') {
                    ?>

                    <div class="form-group col-md-12 col-xs-12 col-lg-12">
                        <div class="table-responsive">
                            <table class="table" id="servicios_list" style="width: 100%">
                                <thead>
                                    <tr style="background-color: #f4f4f4">
                                        <th width="10%">Emtido</th>
                                        <th width="30%">Descripción</th>
<!--                                        <th width="15%">Categoría</th>-->
                                        <th width="5%">Costo</th>
                                        <th width="5%">Precio</th>
                                        <th width="3%">Estado</th>
                                        <th width="3%"></th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>

                    <script>

                        
                        function serviciosList() {
                            var ElemmentoContentload = $("#servicios_list");
                            boxTableLoad(ElemmentoContentload, true);
                            var table = $("#servicios_list").DataTable({
                                searching: true,
                                "ordering":false,
                                destroy:true,
                                "serverSide": true,
                                scrollX: false,
                                lengthChange: false,
                                fixedHeader: true,
                                paging:true,
                                processing: true,
                                lengthMenu:[ 10 ],
                                ajax:{
                                    url: $DOCUMENTO_URL_HTTP + '/application/system/configuraciones/controller/controller_config_clinico.php',
                                    type:'POST',
                                    delay: 500,
                                    data:{'ajaxSend':'ajaxSend','accion':'servicioList'},
                                    dataType:'json',
                                    cache:false,
                                    async:true,
                                    complete:function (xhr, status) {
                                        boxTableLoad(ElemmentoContentload, false);
                                    },
                                },
                                columnDefs: [
                                    {
                                        targets:4,
                                        render:function (data, type, row, meta) {
                                            var idrow = row["idserv"];
                                            var urlidmod = $DOCUMENTO_URL_HTTP+"/application/system/configuraciones/index.php?view=servicios&v=add&id="+idrow;
                                            var menu = "";
                                            menu = "<div class=\"col-xs-2 col-md-2 no-padding pull-right \" style=\"position: relative\">\n" +
                                                " <div class=\"dropdown pull-right \">\n" +
                                                " <button class=\"btn btnhover  btn-xs dropdown-toggle\" type=\"button\" data-toggle=\"dropdown\" style=\"height: 100%\" aria-expanded=\"true\"> <i class=\"fa fa-ellipsis-v\"></i> </button>";
                                            menu += "<ul class='dropdown-menu' style='z-index: +5000'>";
                                                menu += "<li><a href='#' data-urlmod='"+urlidmod+"'  onclick='mod_prestacion($(this))' style='cursor: pointer;' > Modificar </a></li>";

                                                if(row['estado']=='A'){
                                                    menu += "<li><a style='cursor: pointer;' data-id='"+row["id"]+"' data-status='E' onclick='ActivarDesactivarServicios("+idrow+", $(this))' > Desactivar </a></li>";
                                                }else{
                                                    menu += "<li><a style='cursor: pointer;' data-id='"+row["id"]+"' data-status='A' onclick='ActivarDesactivarServicios("+idrow+", $(this))' > Activar </a></li>";
                                                }

                                            menu += "</ul>";
                                            menu +=  "</div>";
                                            " </div>"
                                            return menu;
                                        }
                                    }
                                ],
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

                        function ActivarDesactivarServicios(id, Element){

                            if(!ModulePermission('Prestaciones','eliminar')){
                                notificacion('Ud. No tiene permiso para realizar esta Operación', 'error');
                                return false;
                            }

                            if(id==""){
                                notificacion('Error de parametros de entrada. Consulte con soporte','error');
                                return false;
                            }

                            var statusService = Element.prop('dataset').status;
                            boxloading($boxContentConfiguracion,true);
                            $.ajax({
                                url: $DOCUMENTO_URL_HTTP + '/application/system/configuraciones/controller/conf_controller.php',
                                type:'POST',
                                data: {'accion':'eleminar_prestacion', 'ajaxSend':'ajaxSend', 'id': id, 'statusPrestacion': statusService} ,
                                dataType:'json',
                                async:true,
                                cache:false,
                                complete: function(xhr, status){
                                    boxloading($boxContentConfiguracion,false,1000);
                                },
                                success: function(resp){
                                    if(resp.error != ''){
                                        notificacion( resp.error , 'error');
                                    }else{
                                        var table = $("#servicios_list").DataTable();
                                        table.page.info();
                                        table.ajax.reload(null, false);
                                    }
                                    boxloading($boxContentConfiguracion,false,1000);
                                }
                            });
                        }

                        function mod_prestacion(Element){
                            boxloading($boxContentConfiguracion,true);
                            if(!ModulePermission('Prestaciones', 'modificar')){
                                boxloading($boxContentConfiguracion,false,1000);
                                notificacion('Ud. No tiene permiso para realizar esta Operación', 'error');
                                return false;
                            }

                            var url = Element.prop('dataset').urlmod;
                            if(url != ""){
                                window.location = url;
                            }else {
                                notificacion('error de parametros de entrada consulte con Soporte', 'error');
                            }
                            boxloading($boxContentConfiguracion,false,1000);
                        }

                        $(window).on('load', function () {
                            serviciosList();
                        });

                    </script>

                <?php
                    }
                ?>

                <?php
                    if($v == 'add'){
                ?>

                        <div id="clasificacion_modal" class="modal fade" role="dialog" data-backdrop="static" data-keyboard="false">
                            <div class="modal-dialog">

                                <!-- Modal content-->
                                <div class="modal-content">
                                    <div class="modal-header modal-diseng">
                                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                                        <h4 class="modal-title" ><span>Clasificación de Servicio</span> <span></span> </h4>
                                    </div>
                                    <div class="modal-body">

                                        <div style="padding: 10px">
                                            <div class="form-group">
                                                <small style="color:#0866a5; font-weight: bolder "> <i class="fa fa-info-circle"></i> Crear Clasificación de Prestaciones/Servicios</small>
                                            </div>
                                            <div class="form-group">
                                                <label for="">Nombre</label>
                                                <input type="text" id="clasificacion_nomb_modal" class="form-control input-sm" >
                                            </div>
                                            <div class="form-group">
                                                <label for="">Descripción (opcional)</label>
                                                <textarea name="" class="form-control input-sm" id="clasificacion_desc_modal" rows="3" style="resize: vertical"></textarea>
                                            </div>

                                        </div>

                                    </div>
                                    <div class="modal-footer">
                                        <button href="#" class="btn btnhover" style="font-weight: bolder; color: green" id="NewModClasificacion" onclick="GuardarClasification()">Aceptar
                                            <span class="fa fa-refresh btnSpinner hide"></span>
                                        </button>
                                    </div>
                                </div>

                            </div>
                        </div>


                        <div class="form-group  col-md-12 col-xs-12 col-lg-12">
                            <div class="form-horizontal">

                                <div class="conf_form_servicios">
                                    <div class="form-group">
                                        <label for="" class="control-label col-sm-3">Codigo:</label>
                                        <div class="col-sm-7">
                                            <input type="text" name="codigo_serv" id="codigo_serv" class="form-control" onkeyup="FormValidcrearServicio()">
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="" class="control-label col-sm-3">Clasificación:</label>
                                        <div class="col-sm-7">
                                            <div class="input-group">
                                                <select name="clasificacion_serv" id="clasificacion_serv" class="form-control  " style="width: 100%" onchange="FormValidcrearServicio()">
                                                    <option value=""></option>
                                                </select>
                                                <span class="input-group-addon" style="cursor: pointer" data-toggle="modal" data-target="#clasificacion_modal" ><i class="fa fa-plus"></i></span>
                                                <span class="input-group-addon" style="cursor: pointer" data-toggle="modal" onclick="deleteCategoria()"><i class="fa fa-minus"></i></span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="" class="control-label col-sm-3">Asociar Laboratorio:</label>
                                        <div class="col-sm-7">
                                            <small class="text-blue">Puede asociar un laboratorio (opcional)</small>
                                            <select name="asociarLaboratorio" id="asociarLaboratorio" class="form-control  " style="width: 100%" >
                                                <option value=""></option>
                                                <?php
                                                $result = $db->query("select rowid, concat(name ,' Dir. ', direccion) as nom from tab_conf_laboratorios_clinicos where estado = 'A' ")->fetchAll(PDO::FETCH_ASSOC);
                                                foreach ($result as $value){
                                                    print "<option value='".$value['rowid']."'>".$value['nom']."</option>";
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="" class="control-label col-sm-3">Nombre:</label>
                                        <div class="col-sm-7">
                                            <input type="text" name="nomb_serv" id="nomb_serv" class="form-control  " onkeyup="FormValidcrearServicio()">
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="" class="control-label col-sm-3">Costo:</label>
                                        <div class="col-sm-7">
                                            <input type="text" name="costo_serv" id="costo_serv" class="form-control  " onkeyup="FormValidcrearServicio()">
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="" class="control-label col-sm-3">Precio:</label>
                                        <div class="col-sm-7">
                                            <input type="text" name="valor_serv" id="valor_serv" class="form-control  " onkeyup="FormValidcrearServicio()">
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="" class="control-label col-sm-3">IVA:</label>
                                        <div class="col-sm-7">
                                            <select name="iva_serv" id="iva_serv" class="form-control" style="width: 100%">
                                                <option value="0" >(opcional)</option>
                                                <option value="12" >12%</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="" class="control-label col-sm-3">Información Adicional:</label>
                                        <div class="col-sm-7">
                                            <textarea name="infoad_serv" id="infoad_serv" cols="30" rows="3" class="form-control" style="resize: vertical" ></textarea>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="" class="control-label col-sm-3">&nbsp;</label>
                                        <div class="col-sm-7">
                                            <button class="btn" style="color: green; float: right" onclick="Guardar($(this))" id="btnGuardarServicio">
                                                <b> Guardar </b>
                                                <span class="fa fa-refresh btnSpinner hide"></span>
                                            </button>
                                        </div>
                                    </div>

                                </div>

                            </div>
                        </div>

                        <script>

                            var id = "<?= (isset($_GET['id'])?$_GET['id']:"")?>";

                            var FormValidcrearServicio = function(){
                                var errores = [];
                                var form    = $(".conf_form_servicios");
                                if(form.find('[name="codigo_serv"]').val()==""){
                                    errores.push({
                                        document: form.find('[name="codigo_serv"]'), msg:'Campo obligatorio'
                                    });
                                }if(form.find('[name="nomb_serv"]').val()==""){
                                    errores.push({
                                        document: form.find('[name="nomb_serv"]'), msg:'Campo obligatorio'
                                    });
                                }if(form.find('[name="costo_serv"]').val()==""){
                                    errores.push({
                                        document: form.find('[name="costo_serv"]'), msg:'Campo obligatorio'
                                    });
                                }if(form.find('[name="valor_serv"]').val()==""){
                                    errores.push({
                                        document: form.find('[name="valor_serv"]'), msg:'Campo obligatorio'
                                    });
                                }if(form.find('[name="clasificacion_serv"]').find(":selected").val()==""){
                                    errores.push({
                                        document: form.find('[name="clasificacion_serv"]'), msg:'Campo obligatorio'
                                    });
                                }
                                $(".servicio_msg_errores").remove();
                                $.each(errores, function (i, item) {
                                    var msg_error = document.createElement('small');
                                    $(msg_error).addClass("servicio_msg_errores");
                                    item.document.parents('.col-sm-7')
                                        .append($(msg_error).text(item.msg).css('color', 'red'));
                                });
                            };

                            function  fetchCategorias(id="") {
                                boxloading($boxContentConfiguracion, true);
                                var url = $DOCUMENTO_URL_HTTP + '/application/system/configuraciones/controller/controller_config_clinico.php';
                                $.get(url,{accion:"fetchCategoria", ajaxSend:"ajaxSend"})
                                    .done(function (data) {
                                        var response = $.parseJSON(data);
                                        var form  = $(".conf_form_servicios");
                                        var select = form.find('[name="clasificacion_serv"]');
                                        select.empty()
                                            .append('<option></option>');
                                        select.select2({
                                            placeholder:'Seleccione un opción',
                                            allowClear: true,
                                            data: response['fetch'],
                                            language: languageEs
                                        });

                                        if(id!=""){
                                            select.val(id).trigger('change');
                                        }else{
                                            select.val(null).trigger('change');
                                        }
                                    })
                                    .always(function () {
                                        boxloading($boxContentConfiguracion, false, 1000);
                                    });
                            }

                            $("#clasificacion_modal").on("show.bs.modal", function (ev) {
                                $('#clasificacion_nomb_modal').val(null);
                                $('#clasificacion_desc_modal').val(null);
                                console.log($('[name="clasificacion_serv"]').find(":selected").val());
                                if( $('[name="clasificacion_serv"]').find(":selected").val() != ""){
                                    $(this)
                                        .find(".modal-title")
                                        .find('span:eq(1)')
                                        .text(' modificar');

                                    var array = $('[name="clasificacion_serv"]').select2('data');

                                    $("#clasificacion_nomb_modal").val(array[0].text);
                                    $("#clasificacion_desc_modal").val(array[0].descp);

                                }else{
                                    $(this)
                                        .find(".modal-title")
                                        .find('span:eq(1)')
                                        .text(' nuevo');
                                }
                            });

                            function GuardarClasification(){

                                if( $('[name="clasificacion_serv"]').find(':selected').val() != "")
                                    idclasi = $('[name="clasificacion_serv"]').find(':selected').val();
                                else
                                    idclasi = 0;

                                var paramtrs = {
                                    'accion': 'guardarClasification',
                                    'ajaxSend': 'ajaxSend',
                                    'id': idclasi,
                                    'label': $("#clasificacion_nomb_modal").val(),
                                    'descrip': $("#clasificacion_desc_modal").val(),
                                    'subaccion': ((idclasi==0)?'nuevo':'modificar')
                                };
                                var btn = $("#NewModClasificacion");
                                button_loadding(btn, true);
                                $.ajax({
                                    url: $DOCUMENTO_URL_HTTP + '/application/system/configuraciones/controller/conf_controller.php',
                                    type:'POST',
                                    data: paramtrs,
                                    dataType:'json',
                                    async: true,
                                    cache: false,
                                    complete: function(xhr, status){
                                        // button_loadding(btn, false);
                                    },
                                    success: function (response) {
                                        // console.log(response);
                                        if(response.error != ""){
                                            notificacion(response.error , 'error');
                                        }else{
                                            $("#clasificacion_modal").modal('hide');
                                            fetchCategorias(response.return_id);
                                        }
                                        button_loadding(btn, false);
                                    }
                                });
                            }

                            var fetch_datos = function (mod=false) {

                                var Element =   $('div.conf_form_servicios');
                                var codigo  = Element.find('#codigo_serv');
                                var clasi   = Element.find('#clasificacion_serv');
                                var nomb    = Element.find('#nomb_serv');
                                var valor   = Element.find('#valor_serv');
                                var infoadi = Element.find('#infoad_serv');
                                var iva     = Element.find('#iva_serv');
                                var costo   = Element.find('#costo_serv');
                                var laboratorio = Element.find('#asociarLaboratorio');

                                var paramtrs = [];
                                paramtrs.push(codigo.val(), clasi.find(':selected').val(), nomb.val(), valor.val(), infoadi.val(), iva.find(':selected').val(), costo.val(), laboratorio.find(':selected').val() );

                                if(mod==true){
                                    boxloading($boxContentConfiguracion, true);
                                    var url = $DOCUMENTO_URL_HTTP + '/application/system/configuraciones/controller/controller_config_clinico.php';
                                    $.get(url,{accion:"fetchServiciosProd", ajaxSend:"ajaxSend", id:id})
                                        .done(function (data) {
                                            var response = $.parseJSON(data);
                                            if(response.error != ''){
                                                notificacion(response.error, 'error');
                                                return false;
                                            }
                                            response = response['fetch'];
                                            codigo.val(response['codigo']);
                                            clasi.val(response['fk_categoria']).trigger('change');
                                            iva.val(response['iva']).trigger('change');
                                            nomb.val(response['descripcion']);
                                            valor.val(response['valor']);
                                            costo.val(response['costo_x_clinica']);
                                            infoadi.val(response['explicacion']);
                                            laboratorio.val(response['fk_laboratorio']).trigger('change');

                                            //select clasificacion/categoria
                                            fetchCategorias(response['fk_categoria']);
                                        })
                                        .always(function () {
                                            boxloading($boxContentConfiguracion, false, 1000);
                                        });
                                }
                                return paramtrs;

                            };

                            function Guardar(){
                                var fetch = fetch_datos();
                                var paramtrs = {
                                    'accion'    : 'newUpdateServicioProducto',
                                    'ajaxSend'  : 'ajaxSend',
                                    'id'        : id,
                                    'codigo'    : fetch[0],
                                    'clasi'     : fetch[1],
                                    'nomb'      : fetch[2],
                                    'valor'     : fetch[3],
                                    'infoadi'   : fetch[4],
                                    'iva'       : fetch[5],
                                    'costo'     : fetch[6],
                                    'laboratorio' : fetch[7],
                                };
                                button_loadding($("#btnGuardarServicio"), true);
                                $.ajax({
                                    url: $DOCUMENTO_URL_HTTP + '/application/system/configuraciones/controller/controller_config_clinico.php',
                                    type:'POST',
                                    data: paramtrs,
                                    dataType:'json',
                                    async:true,
                                    cache: false,
                                    complete: function(xhr, status){
                                        button_loadding($("#btnGuardarServicio"), false);
                                    },
                                    success: function (response) {
                                        if(response.error != ''){
                                            notificacion(response.error , 'error');
                                        }else{
                                            notificacion('Información Actualizada', 'success');
                                            setTimeout(()=>{
                                                window.location = $DOCUMENTO_URL_HTTP+"/application/system/configuraciones/index.php?view=servicios&v=list";
                                            },600);
                                        }
                                        button_loadding($("#btnGuardarServicio"), false);
                                    }
                                });

                            }

                            var deleteCategoria = function () {
                                var id = $('[name="clasificacion_serv"]').find(':selected').val();
                                var object = {
                                    id: id,
                                    callback: function () {
                                        boxloading($boxContentConfiguracion, true);
                                        var paramtrs = {
                                            accion   : 'delete_clasificacion_servicio',
                                            ajaxSend : 'ajaxSend',
                                            id_servicio : $('[name="clasificacion_serv"]').find(':selected').val()};
                                        $.ajax({
                                            url: $DOCUMENTO_URL_HTTP + '/application/system/configuraciones/controller/controller_config_clinico.php',
                                            delay:1000,
                                            type: 'POST',
                                            data: paramtrs ,
                                            async:true,
                                            cache:false,
                                            dataType:'json',
                                            complete: function(xhr, status){
                                                boxloading($boxContentConfiguracion, false, 1000);
                                            },
                                            success:function (response) {
                                                    boxloading($boxContentConfiguracion, false, 1000);
                                                if(response.error!=""){
                                                    notificacion(response.error, 'error');
                                                }else{
                                                    notificacion("Información Actualizado", 'success');
                                                    fetchCategorias();
                                                }
                                            }
                                        });
                                    }
                                };
                                notificacionSIoNO("Eliminar Prestación/Servicio",null, object);
                            };


                            $(window).on('load', function () {

                                if(id!=""){ //modificar
                                    fetch_datos(true);
                                }else {
                                    //nueva prestacion/servicio
                                    fetchCategorias();
                                }

                                $('[name="costo_serv"]').maskMoney({precision:2,thousands:'', decimal:'.',allowZero:true,allowNegative:true, defaultZero:true,allowEmpty: true});
                                $('[name="valor_serv"]').maskMoney({precision:2,thousands:'', decimal:'.',allowZero:true,allowNegative:true, defaultZero:true,allowEmpty: true});

                                $("#asociarLaboratorio").select2({
                                    placeholder:'opcional',
                                    language:languageEs,
                                    allowClear:true,
                                })
                            });
                        </script>

                <?php }?>

            </div>
        </div>

    </div>
</div>

