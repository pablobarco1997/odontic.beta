
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

//    print_r($v); die();
?>

<div class="box box-solid">
    <div class="box-header with-border">
        <div class="form-group col-xs-12 col-sm-12 col-md-12 no-margin no-padding">
            <h4 class="no-margin"><span><b>
                        Doctor(a) Odontologos                    </b></span></h4>
        </div>
    </div>

    <div class="box-body">
        <div class="form-group form-group col-xs-12 col-md-12">

            <div class="form-group col-centered col-xs-12 col-md-11 col-lg-10 col-sm-12" style="margin-top: 30px">


                <div class="form-group col-md-12 col-xs-12">
                    <ul class="list-inline" style="border-bottom: 1px solid #333333; border-top: 1px solid #333333; margin-left: 0px">
                        <li>
                            <a href="<?= DOL_HTTP.'/application/system/configuraciones/?view=odontologos&v=list' ?>" class="btnhover btn btn-sm" style="font-weight: bolder; color: #333333; ">
                                <b>Lista Doctor(a)</b>
                            </a>
                        </li>

                        <li>
                            <a href="<?= DOL_HTTP.'/application/system/configuraciones/?view=odontologos&v=add' ?>" class="btnhover btn btn-sm" style="font-weight: bolder; color: #333333; ">
                                <b>Crear Doctor(a)</b>
                            </a>
                        </li>
                    </ul>
                </div>

                <?php
                    if($v=='list') {
                        ?>

                        <div class="form-group col-md-12 col-xs-12 col-lg-12">
                            <!--formulario list de doctores-->
                            <div class="table-responsive">
                                <table class="table" id="doctor_list" style="width: 100%">
                                    <thead>
                                    <tr style="background-color: #f4f4f4">
                                        <th width="30%">Doctor(a)</th>
                                        <th width="10%">C.I</th>
                                        <th width="15%">Dirección</th>
                                        <th width="15%">E-mail</th>
                                        <th width="15%">Especialidad</th>
                                        <th width="10%">Cambiar</th>
                                    </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>


                        <script>

                            function  doctores_list() {

                                var ElemmentoContentload = $("#doctor_list");
                                boxTableLoad(ElemmentoContentload, true);

                                var table = $("#doctor_list").DataTable({
                                    searching: false,
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
                                        data:{'ajaxSend':'ajaxSend','accion':'list_doctores'},
                                        dataType:'json',
                                        cache:false,
                                        async:true,
                                        complete:function (xhr, status) {
                                            boxTableLoad(ElemmentoContentload, false);
                                        },

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


                            function DoctorEstados(id, estado)
                            {
                                if(estado=='E'){
                                    if(!ModulePermission(12,4)){
                                        notificacion('Ud. No tiene permiso para Desactivar Doctor(a)','question');
                                        return false;
                                    }
                                }
                                $.ajax({
                                    url: $DOCUMENTO_URL_HTTP + '/application/system/configuraciones/controller/conf_controller.php',
                                    type:'POST',
                                    data:{ 'ajaxSend':'ajaxSend', 'accion':'actualizar_estados', 'id': id, 'estado': estado },
                                    dataType:'json',
                                    async:true,
                                    cache:false,
                                    success: function(resp) {

                                        if(resp.error != ''){
                                            notificacion(resp.error, 'error');
                                        }else{
                                            notificacion('Información Actualizada', 'success');
                                            var table = $("#doctor_list").DataTable();
                                            table.ajax.reload(null, false);
                                        }
                                    }
                                });
                            }

                            $(document).ready(function () {

                            });

                            $(window).on('load', function () {
                                doctores_list();
                            });

                        </script>


                        <?php
                    }
                ?>





                <?php
                if($v=='add') {
                    ?>

                    <!--            formulario add odontologo-->
                    <div class="form-group  col-md-12 col-xs-12 col-lg-12">
                        <div class="form-horizontal">
                            <div class="conf_form_odontologo">

                                <div class="form-group">
                                    <label for="" class="control-label col-sm-3">Epecialidad</label>
                                    <div class="col-sm-7">
                                        <select name="especialidad_doct" id="especialidad_doct" class="form-control" style="width: 100%">
                                            <option></option>
                                            <?php
                                                $result_bc = $db->query("select rowid , nombre_especialidad  from tab_especialidades_doc ")->fetchAll(PDO::FETCH_ASSOC);
                                                foreach ($result_bc as $item){
                                                    echo "<option value='".$item['rowid']."' >".$item['nombre_especialidad']."</option>";
                                                }
                                            ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="" class="control-label col-sm-3">Nombre</label>
                                    <div class="col-sm-7">
                                        <input type="text" class="form-control" name="nombre_doct" id="nombre_doct">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="" class="control-label col-sm-3" >Apellido</label>
                                    <div class="col-sm-7">
                                        <input type="text" class="form-control" id="apellido_doct" name="apellido_doct">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="" class="control-label col-sm-3">C.I.</label>
                                    <div class="col-sm-7">
                                        <input type="text" class="form-control" name="rucedula_doct" id="rucedula_doct">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="" class="control-label col-sm-3">Telef. Cel</label>
                                    <div class="col-sm-7">
                                        <input type="text" class="form-control" id="celular_doct" name="celular_doct">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="" class="control-label col-sm-3">Fax</label>
                                    <div class="col-sm-7">
                                        <input type="text" class="form-control" name="TelefonoConvencional_doct" id="TelefonoConvencional_doct">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="" class="control-label col-sm-3">E-mail</label>
                                    <div class="col-sm-7">
                                        <input type="text" class="form-control" id="email_doct" name="email_doct">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="" class="control-label col-sm-3">Ciudad</label>
                                    <div class="col-sm-7">
                                        <input type="text" class="form-control" id="ciudad_doct" name="ciudad_doct">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="" class="control-label col-sm-3">Dirección</label>
                                    <div class="col-sm-7">
                                        <input type="text" class="form-control" id="direccion_doct" name="direccion_doct">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="" class="control-label col-sm-3">&nbsp;</label>
                                    <div class="col-sm-7">
                                        <button class="btn" style="color: green; float: right" onclick="Guardar($(this))">
                                            <b> <?= isset($_GET['id']) ? 'Modificar Doctor(a)' : 'Guardar Doctor(a)' ?></b>
                                            <span class="fa fa-refresh btnSpinner hide"></span>
                                        </button>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                    <script>

                        var FormValidationOdontolotoMod = function(fiel=false, input=false) {
                            var documentInput   = [];
                            var nombre          = $('#nombre_doct');
                            var apellido        = $('#apellido_doct');
                            var telefono        = $('#TelefonoConvencional_doct');
                            var direccion       = $('#direccion_doct');
                            var celular         = $('#celular_doct');
                            var email           = $('#email_doct');
                            var ciudad          = $('#ciudad_doct');
                            var ruc_cedula      = $('#rucedula_doct');

                            if(nombre.val()==''||nombre.val()==null){
                                documentInput.push({
                                    'document' : nombre,
                                    'msg' : 'Este campo es requerido',
                                });
                            }
                            if(apellido.val()==''||apellido.val()==null){
                                documentInput.push({
                                    'document' : apellido,
                                    'msg' : 'Este campo es requerido',
                                });
                            }
                            if(direccion.val()==''||direccion.val()==null){
                                documentInput.push({
                                    'document' : direccion,
                                    'msg' : 'Este campo es requerido',
                                });
                            }
                            if(celular.val()==''||celular.val()==null){
                                documentInput.push({
                                    'document' : celular,
                                    'msg' : 'Este campo es requerido',
                                });
                            }
                            if(email.val()==''||email.val()==null){
                                documentInput.push({
                                    'document' : email,
                                    'msg' : 'Este campo es requerido',
                                });
                            }
                            if(ruc_cedula.val()==''||ruc_cedula.val()==null){
                                documentInput.push({
                                    'document' : ruc_cedula,
                                    'msg' : 'Este campo es requerido',
                                });
                            }
                            if(ciudad.val()==''||ciudad.val()==null){
                                documentInput.push({
                                    'document' : ciudad,
                                    'msg' : 'Este campo es requerido',
                                });
                            }

                            //con funcion asignada
                            $('.err_msg').remove();

                            for (var i = 0; i <= documentInput.length -1; i++)
                            {
                                var MsgError  = document.createElement('small');
                                MsgError.setAttribute('class','err_msg');

                                var documento = null;
                                documento       = documentInput[i]['document'];
                                var msg         = $(MsgError)
                                    .html(documentInput[i]['msg'])
                                    .css('color','red').append("<br>");

                                msg.insertAfter(documento);
                                console.log(documento);
                            }

                            if(documentInput.length>0)
                                return false;
                            else
                                return true;


                        };

                        function fetch_odontologos(fetchMod=false){

                            var nombre       = $('#nombre_doct');
                            var apellido     = $('#apellido_doct');
                            var telefono     = $('#TelefonoConvencional_doct');
                            var direccion    = $('#direccion_doct');
                            var celular      = $('#celular_doct');
                            var email        = $('#email_doct');
                            var ciudad       = $('#ciudad_doct');
                            var ci           = $('#rucedula_doct');
                            var especialidad = $('#especialidad_doct').find(':selected').val();
                            var fetch        = [];

                            fetch.push(nombre.val(), apellido.val(), telefono.val(), direccion.val(), celular.val(), email.val(), ciudad.val(), ci.val(), especialidad);

                            if(fetchMod==true){
                                $.ajax({
                                    url: $DOCUMENTO_URL_HTTP + '/application/system/configuraciones/controller/controller_config_clinico.php',
                                    type:'POST',
                                    data: {'id':id, 'ajaxSend':'ajaxSend', 'accion':'fetchDatosDoctor'},
                                    dataType:'json',
                                    cache:false, 
                                    async:true, 
                                    complete: function (xhr, status) {

                                    },success: function (response) {

                                        if(response['error']!=""){
                                            notificacion(response['error'], 'error');
                                            return false;
                                        }
                                        nombre
                                            .val(response['doctor'][0]);
                                        apellido
                                            .val(response['doctor'][1]);
                                        telefono
                                            .val(response['doctor'][2]);
                                        direccion
                                            .val(response['doctor'][3]);
                                        celular
                                            .val(response['doctor'][4]);
                                        email
                                            .val(response['doctor'][5]);
                                        ciudad
                                            .val(response['doctor'][6]);
                                        ci
                                            .val(response['doctor'][7]);

                                        $("#especialidad_doct").val(response['doctor'][8]).trigger('change');
                                        FormValidationOdontolotoMod();
                                    }
                                });
                            }
                            return fetch;
                        }

                        //id
                        var id = "<?= (isset($_GET['id']))?$_GET['id']:0 ?>";

                        function Guardar(Estebtn){

                            if(FormValidationOdontolotoMod()==false){
                                return false;
                            }
                            var fetch = fetch_odontologos();
                            var datos = {
                                'nombre'       : fetch[0],
                                'apellido'     : fetch[1],
                                'telefono'     : fetch[2],
                                'direccion'    : fetch[3],
                                'celular'      : fetch[4],
                                'email'        : fetch[5],
                                'ciudad'       : fetch[6],
                                'cedula_ruc'   : fetch[7],
                                'especialidad' : fetch[8],
                            };
                            var form = new FormData();
                            form.append('ajaxSend', 'ajaxSend');
                            form.append('accion', 'crear_odontologo');
                            form.append('id', id);
                            form.append('datos', JSON.stringify(datos));

                            button_loadding(Estebtn, true);
                            $.ajax({
                                url: $DOCUMENTO_URL_HTTP + '/application/system/configuraciones/controller/controller_config_clinico.php',
                                type:'POST',
                                data: form,
                                dataType:'json',
                                contentType:false,
                                processData:false,
                                async:true,
                                cache:false,
                                complete:function(xhr, status){
                                    button_loadding(Estebtn, false);
                                },
                                success:function (response) {
                                    if(response.error != ""){
                                        notificacion(response.error, 'error');
                                    }else{
                                        window.location = $DOCUMENTO_URL_HTTP + '/application/system/configuraciones/?view=odontologos&v=list';
                                    }
                                }
                            });

                        }

                        $("input").keyup(function () {
                            FormValidationOdontolotoMod();
                        });

                        $(document).ready(function () {
                            FormValidationOdontolotoMod();
                        });

                        $(window).on('load', function () {

                            $("#especialidad_doct").select2({
                                placeholder:'Seleccione una opción',
                                language:languageEs
                            });
                            $('#rucedula_doct').mask("000000000-0",{placeholder:"_________-_"});
                            $('#celular_doct').mask("000 000 0000",{placeholder:"___ ___ ____"});
                            $('#TelefonoConvencional_doct').mask("0#");

                            if( "<?= (isset($_GET['id']))?"modify":"" ?>" == "modify" ){
                                fetch_odontologos(true);
                            }
                        });

                    </script>

                    <?php
                }
                ?>

            </div>
        </div>
    </div>
</div>