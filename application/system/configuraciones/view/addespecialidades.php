
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
    echo 'Ocurrio un error. Parametros de entrada, Consulte con soporte';
}

?>


<div class="box box-solid">
    <div class="box-header with-border">
        <div class="form-group col-xs-12 col-sm-12 col-md-12 no-margin no-padding">
            <h4 class="no-margin"><span><b>
                        Especialidades   </b></span></h4>
        </div>
    </div>


    <div class="box-body">

        <?php accessoModule('Especialidades');  ?>

        <div class="form-group form-group col-xs-12 col-md-12">
            <div class="form-group col-centered col-xs-12 col-md-11 col-lg-10 col-sm-12" style="margin-top: 30px">

                <div class="form-group col-md-12 col-xs-12">
                    <ul class="list-inline" style="border-bottom: 1px solid #333333; border-top: 1px solid #333333; margin-left: 0px">
                        <li>
                            <a href="<?= DOL_HTTP.'/application/system/configuraciones/index.php?view=especialidades&v=list' ?>" class="btnhover btn btn-sm" style="font-weight: bolder; color: #333333; ">
                                <b>Lista Especialidades</b>
                            </a>
                        </li>
                        <li>
                            <a href="<?= DOL_HTTP.'/application/system/configuraciones/index.php?view=especialidades&v=add' ?>" class="btnhover btn btn-sm" style="font-weight: bolder; color: #333333; ">
                                <b>Crear Especialidad</b>
                            </a>
                        </li>
                    </ul>
                </div>


                <?php
                    if($v=='list') {
                    ?>

                        <div class="form-group col-md-12 col-xs-12 col-lg-12">
                             <span class="margin-bottom" style=" color: #eb9627">
                                <i class="fa fa-info-circle"></i>
                                Tener en cuenta que si elimina una especialidad, aquellos Odontólogos
                                relacionados con esta, se actualizaran a especialidad General incluyendo todas las citas asociadas con la especialidad
                                eliminada
                             </span>
                        </div>

                        <!--table de informacion especialidades-->
                        <div class="form-group col-md-12 col-xs-12 col-lg-12">
                            <!--formulario list de especialidades-->
                            <div class="table-responsive">
                                <table class="table" id="especialidades_list" style="width: 100%">
                                    <thead>
                                        <tr style="background-color: #f4f4f4">
                                            <th width="10%">Emtido</th>
                                            <th width="30%">Especialidad</th>
                                            <th width="5%"></th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>

                        <script>

                            function  especialidadeslist() {

                                var ElemmentoContentload = $("#especialidades_list");
                                boxTableLoad(ElemmentoContentload, true);

                                var table = $("#especialidades_list").DataTable({
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
                                        data:{'ajaxSend':'ajaxSend','accion':'especialidades_list'},
                                        dataType:'json',
                                        cache:false,
                                        async:true,
                                        complete:function (xhr, status) {
                                            boxTableLoad(ElemmentoContentload, false);
                                        },

                                    },
                                    columnDefs: [
                                        {
                                            targets:2,
                                            render:function (data, type, row, meta) {

                                                    var urlidmod = $DOCUMENTO_URL_HTTP+"/application/system/configuraciones/index.php?view=especialidades&v=add&id="+row["id"];

                                                    var menu = "";
                                                    menu = "<div class=\"col-xs-2 col-md-2 no-padding pull-right \" style=\"position: relative\">\n" +
                                                        " <div class=\"dropdown pull-right \">\n" +
                                                        " <button class=\"btn btnhover  btn-xs dropdown-toggle\" type=\"button\" data-toggle=\"dropdown\" style=\"height: 100%\" aria-expanded=\"true\"> <i class=\"fa fa-ellipsis-v\"></i> </button>";
                                                            menu += "<ul class='dropdown-menu' style='z-index: +5000'>";
                                                                menu += "<li><a href='#' data-urlmod='"+urlidmod+"' onclick='mod_esp($(this))' style='cursor: pointer;' > Modificar </a></li>";
                                                                menu += "<li><a style='cursor: pointer;' data-id='"+row["id"]+"' onclick='deleteespecia($(this), "+row['id']+")' > Eliminar </a></li>";
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

                            function mod_esp(Element){
                                if(!ModulePermission('Especialidades', 'modificar')){
                                    notificacion('Ud. No tiene permiso para consultar', 'error');
                                    return false;
                                }
                                var url = Element.prop('dataset').urlmod;
                                if(url==""){
                                    notificacion("Error de parametros de entrada. Consulte con soporte ", "error");
                                    return false;
                                }
                                window.location = url;
                            }

                            function deleteespecia(Element, id){

                                if(!ModulePermission('Especialidades', 'eliminar')){
                                    notificacion('Ud. No tiene permiso para Eliminar Especialidades', 'question');
                                    return false;
                                }

                                boxloading($boxContentConfiguracion,true);

                                if(id != ""){

                                    $.ajax({
                                        url: $DOCUMENTO_URL_HTTP + '/application/system/configuraciones/controller/conf_controller.php',
                                        type:'POST',
                                        data: { 'ajaxSend': 'ajaxSend', 'accion': 'delete_especialidad', 'id': id},
                                        dataType:'json',
                                        async:true,
                                        cache: false,
                                        complete:function(xhr, status) {
                                            boxloading($boxContentConfiguracion,false,1000);
                                        },
                                        success: function(resp) {
                                            if(resp.error == ''){
                                                boxloading($boxContentConfiguracion,false,1000);
                                                notificacion('Información Actualizada', 'success');
                                                var table = $("#especialidades_list").DataTable();
                                                table.ajax.reload(null,  false);
                                            }else{
                                                boxloading($boxContentConfiguracion,false,1000);
                                                notificacion(resp.error, 'error');
                                            }

                                        }
                                    });

                                }
                            }

                            $(window).on('load', function () {

                                especialidadeslist();
                            });

                        </script>

                <?php
                    }
                ?>


                <?php
                    if($v=='add'){
                ?>

                        <div class="form-group  col-md-12 col-xs-12 col-lg-12">
                            <div class="form-horizontal">
                                <div class="conf_form_especialidad">
                                    <div class="form-group">
                                        <label for="" class="control-label col-sm-3">Especialidad:</label>
                                        <div class="col-sm-7">
                                            <input type="text" name="conf_espc_espcd" id="conf_espc_espcd" class="form-control" onkeyup="FormValid()">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="" class="control-label col-sm-3">Desc.:</label>
                                        <div class="col-sm-7">
                                            <textarea id="conf_espc_desc" name="conf_espc_desc" class="form-control" style="resize: vertical" cols="30" rows="5"></textarea>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="" class="control-label col-sm-3">&nbsp;</label>
                                        <div class="col-sm-7">
                                            <button class="btn" style="color: green; float: right" onclick="Guardarespelid($(this))">
                                                <b> Guardar </b>
                                                <span class="fa fa-refresh btnSpinner hide"></span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <script>

                            var id = "<?= (isset($_GET['id']) ? $_GET['id'] : "") ?>";

                            var FormValid = function(){
                                var errores = [];
                                var div     = $(".conf_form_especialidad");
                                var especid = div.find('#conf_espc_espcd');

                                if(especid.val() == ""){
                                    errores.push({document: especid, text: 'Campo obligatorio, (escriba una especialidad)'});
                                }

                                $('.error_msg_especialida').remove();


                                if(errores.length>0){

                                    console.log($(errores));
                                    $(errores).each(function (i, item) {
                                        var msg     = document.createElement('small');
                                        var Element = item.document;
                                        var text    = item.text;
                                        $(msg)
                                            .addClass('error_msg_especialida')
                                            .text(text)
                                            .css('color','red');

                                        console.log(Element);
                                        $(msg).insertAfter(Element);

                                    });

                                }

                                if(errores.length>0){
                                    return false;
                                }else{
                                    return true;
                                }
                            };
                            function Guardarespelid(Element){

                                if(!FormValid()){
                                    return false;
                                }
                                var btn  = Element;

                                button_loadding(btn, true);

                                var paramts = {
                                    'accion' : 'newEspecialidad',
                                    'ajaxSend' : 'ajaxSend',
                                    'id' : id,
                                    'especialidad' : $("#conf_espc_espcd").val(),
                                    'desc' : $("#conf_espc_desc").val(),
                                };
                                $.ajax({
                                    url: $DOCUMENTO_URL_HTTP + '/application/system/configuraciones/controller/controller_config_clinico.php',
                                    type:'POST',
                                    data:paramts,
                                    dataType:'json',
                                    cache:false,
                                    async:true,
                                    complete: function (xhr, status) {
                                        button_loadding(btn, false);
                                    }, 
                                    success: function (response) {
                                        if(response.error == ""){
                                            notificacion('información Actualizada', 'success');
                                            window.location = $DOCUMENTO_URL_HTTP + "/application/system/configuraciones/index.php?view=especialidades&v=list";
                                        }else{
                                            notificacion(response.error, 'error');
                                        }

                                        button_loadding(btn, false);
                                    }
                                });
                            }

                            function fetchMod(){
                                boxloading($boxContentConfiguracion, true);
                                var url = $DOCUMENTO_URL_HTTP + '/application/system/configuraciones/controller/controller_config_clinico.php';
                                $.get(url,{accion:"fetchespecialidad", ajaxSend:"ajaxSend", id: id})
                                    .done(function (data) {
                                        var response = $.parseJSON(data);
                                        var form = $(".conf_form_especialidad");
                                        form.find("[name='conf_espc_espcd']").val(response['fetch']['nombre_especialidad']);
                                        form.find("[name='conf_espc_desc']").val(response['fetch']['descripcion']);
                                    })
                                    .always(function () {
                                        boxloading($boxContentConfiguracion, false, 1000);
                                });
                            }

                            $(window).on('load', function () {

                                if(id!=""){
                                    fetchMod();
                                }

                            });
                        </script>
                <?php } ?>

            </div>

        </div>
    </div>

</div>


