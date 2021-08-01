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
                        Usuarios Clinicos                    </b></span></h4>
        </div>
    </div>

    <div class="box-body">
        <div class="form-group form-group col-xs-12 col-md-12">

            <div class="form-group col-centered col-xs-12 col-md-11 col-lg-10 col-sm-12" style="margin-top: 30px">
                <div class="form-group col-md-12 col-xs-12">
                    <ul class="list-inline" style="border-bottom: 1px solid #333333; border-top: 1px solid #333333; margin-left: 0px">
                        <li>
                            <a href="<?= DOL_HTTP.'/application/system/configuraciones/?view=admin_users&v=list' ?>" class="btnhover btn btn-sm" style="font-weight: bolder; color: #333333; ">
                                <b>Lista de Usuarios</b>
                            </a>
                        </li>

                        <li>
                            <a href="<?= DOL_HTTP.'/application/system/configuraciones/?view=admin_users&v=add' ?>" class="btnhover btn btn-sm" style="font-weight: bolder; color: #333333; ">
                                <span class="fa fa-user"></span> <b>Crear Usuario Clinico </b>
                            </a>
                        </li>
                    </ul>
                </div>


                <?php
                if($v=='list') {

                    ?>

                    <div class="form-group col-md-12 col-xs-12 col-lg-12">
                        <div class="table-responsive">
                            <table class="table table-hover" style="width: 100%" id="usuarios_list_conf">
                                <thead style="background-color: #f4f4f4">
                                    <tr>
                                        <th width="3%"></th>
                                        <th width="50%">Usuario</th>
                                        <th width="30%">Perfil</th>
                                        <th width="10%">Estado</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>


                    <script>

                        function statusUserslogin( idusers = 0, status ) {
                            boxloading($boxContentConfiguracion,true);
                            if(idusers != 0){
                                var paramtros = {
                                    "accion"   : "status_update_users",
                                    "ajaxSend" : "ajaxSend" ,
                                    "idlogin"  : idusers ,
                                    "status"   : status
                                };
                                var url = $DOCUMENTO_URL_HTTP + '/application/system/configuraciones/controller/conf_controller.php';
                                $.get(url , paramtros, function(data) {
                                    var respuesta = $.parseJSON(data);
                                    if(respuesta['error'] == ''){
                                        notificacion("información Actualizada", "success");
                                        var table = $('#usuarios_list_conf').DataTable();
                                        table.ajax.reload(null, false);
                                    }else{
                                        notificacion(respuesta['error'], 'error');
                                    }
                                    boxloading($boxContentConfiguracion,true,1000);
                                });

                            }else{
                                notificacion('Ocurrio un error', 'error');
                            }

                        }

                        function usuarios_list(){
                            var table = $('#usuarios_list_conf').DataTable({
                                searching: false,
                                ordering:false,
                                destroy:true,
                                serverSide: true,
                                lengthChange: false,
                                processing: true,
                                lengthMenu:[ 10 ],
                                ajax:{
                                    url: $DOCUMENTO_URL_HTTP + '/application/system/configuraciones/controller/conf_controller.php',
                                    type:'POST',
                                    data:{'ajaxSend':'ajaxSend', 'accion':'infoUsuarioOdontic', 'cual': 'list'},
                                    dataType:'json',
                                    cache:false,
                                    async: true
                                },
                                columnDefs:[{
                                    targets:0 ,
                                    render: function (data , type , full , meta) {
                                        var menu        = "";
                                        var idusers     = full[6];
                                        var url_mod     = $DOCUMENTO_URL_HTTP+"/application/system/configuraciones/?view=admin_users&v=add&id="+full[6];
                                        menu += "<div class='dropdown'>";
                                        menu += "<button class='btn btnhover btn-xs dropdown-toggle' type='button' data-toggle='dropdown' > <i style='padding-top: 3px; padding-bottom: 3px' class=\"fa fa-ellipsis-v\"></i> </button> ";
                                            menu += "<ul class=\"dropdown-menu\"> ";
                                                menu += "<li> <a style='cursor: pointer;' href='#' data-url='"+url_mod+"' onclick='ModUsers($(this))'> Modificar  </a> </li>";
                                                // INACTIVAR
                                                if(full[4] == 'A'){
                                                    menu += "<li> <a style='cursor: pointer; ' onclick='statusUserslogin("+idusers+",\"E\")'> Inactivar  </a> </li>";
                                                }
                                                // ACTIVAR
                                                if(full[4] == 'E'){
                                                    menu += "<li> <a style='cursor: pointer; ' onclick='statusUserslogin("+idusers+", \"A\")' > Activar  </a> </li>";
                                                }
                                            menu += "</ul>";
                                        menu += "</div>";
                                        return menu;
                                    }
                                }],
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

                        function ModUsers(Element) {
                            if(!ModulePermission(14,3)){
                                    notificacion('Ud. No tiene permiso para Modificar','question');
                                return false;
                            }
                            var url = Element.prop('dataset').url;
                            if(url != ""){
                                window.location = url;
                            }
                        }


                        $(document).ready(function () {

                        });

                        $(window).on('load', function () {
                            usuarios_list();
                        });

                    </script>



                    <?php
                }
                ?>

                <!--agregar nuevo usuario o actualizarlo -->
                <?php
                    if($v=='add') {
                ?>

                        <div class="form-group  col-md-12 col-xs-12 col-lg-12">
                            <div class="form-horizontal">
                                <div class="conf_form_create_users">

                                    <div class="form-group">
                                        <label for="" class="control-label col-sm-4">Administrador</label>
                                        <div class="col-sm-6">
                                            <input type="checkbox" id="ckeckedAllAdminUsers" style="margin-top: 10px" title="Asignar todos los privilegios">
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="" class="control-label col-sm-4">Habilitar Doctor(a)</label>
                                        <div class="col-sm-6">
                                            <input type="checkbox" id="ckeckedDoctorHabilitar" style="margin-top: 10px">
                                        </div>
                                    </div>

                                    <div class="form-group hide">
                                        <label for="" class="control-label col-sm-4">Doctor(a)</label>
                                        <div class="col-sm-6">
                                            <select type="text" class="form-control" style="width: 100%" id="odontol_conf">
                                                <option value=""></option>
                                                <?php
                                                $result_ab = $db->query("select rowid,  concat(nombre_doc, ' ', apellido_doc) as label from tab_odontologos where estado = 'A' ");
                                                if($result_ab){
                                                        if($result_ab->rowCount()>0){
                                                            $result_ab = $result_ab->fetchAll(PDO::FETCH_ASSOC);
                                                            foreach ($result_ab  as $item){
                                                                print "<option value='".$item['rowid']."'>".$item['label']."</option>";
                                                            }
                                                        }
                                                    }
                                                ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="" class="control-label col-sm-4">Usuario</label>
                                        <div class="col-sm-6">
                                            <input type="text" class="form-control" id="usuario_conf">
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="" class="control-label col-sm-4">Password</label>
                                        <div class="col-sm-6">
                                            <input type="password" class="form-control" id="pass_conf">
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="" class="control-label col-sm-4">Perfil Permisos</label>
                                        <div class="col-sm-6">
                                            <select type="text" class="form-control" style="width: 100%" id="perfilpermm_conf">
                                                <option value=""></option>
                                                <?php
//                                                    $result_ab = $db->query("select rowid,  concat(nombre_doc, ' ', apellido_doc) as label from tab_odontologos");
//                                                    if($result_ab){
//                                                        if($result_ab->rowCount()>0){
//                                                            $result_ab = $result_ab->fetchAll(PDO::FETCH_ASSOC);
//                                                            foreach ($result_ab  as $item){
//                                                                print "<option value='".$item['rowid']."'>".$item['label']."</option>";
//                                                            }
//                                                        }
//                                                    }
                                                ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="" class="control-label col-sm-3">&nbsp;</label>
                                        <div class="col-sm-7">
                                            <button class="btn" style="color: green; float: right" onclick="GuardarUsers($(this))">
                                                <b> Guardar </b>
                                                <span class="fa fa-refresh btnSpinner hide"></span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <script>

                            //id
                            var id = "<?= (isset($_GET['id']))?$_GET['id']:0 ?>";

                            var FormValidUsuariosConf = function() {
                                var Errores          = [];
                                var usuario          = $('#usuario_conf');
                                var pass             = $('#pass_conf');
                                var odontol          = $('#odontol_conf');
                                var perfilperm       = $('#perfilpermm_conf');
                                if((!/^\s/.test(pass.val()))==false){
                                    Errores.push({
                                        "documento" :   pass,
                                        "mesg" :  "Campo Obligatorio",
                                    });
                                }
                                if((!/^\s/.test(usuario.val()))==false){
                                    Errores.push({
                                        "documento" :   usuario,
                                        "mesg" :  "Campo Obligatorio",
                                    });
                                }
                                // valid usuario administrador false
                                if( $("#ckeckedAllAdminUsers").is(":checked") == false ){
                                    if(perfilperm.find(":selected").val()==""){
                                        Errores.push({
                                            "documento" :   perfilperm,
                                            "mesg" :  "Campo Obligatorio",
                                        });
                                    }
                                }
                                //valid doctor true
                                if( $("#ckeckedDoctorHabilitar").is(":checked") == true ){
                                    if(odontol.find(":selected").val()==""){
                                        Errores.push({
                                            "documento" :   odontol,
                                            "mesg" :  "Campo Obligatorio",
                                        });
                                    }
                                }
                                $(".error_usuario_msg").remove();
                                if(Errores.length>0){
                                    for (var i=0; i<=Errores.length-1;i++ ){

                                        var menssage =  document.createElement("small");
                                        menssage.setAttribute("style","display: block; color:red;");
                                        menssage.setAttribute("class","error_usuario_msg");
                                        menssage.appendChild(document.createTextNode(Errores[i]['mesg']));
                                        var documentoDol        = Errores[i]['documento'];
                                        if( $(documentoDol)[0].nodeName == 'SELECT' ){
                                            $(menssage).insertAfter($(documentoDol).parent().find('span:eq(0)'));
                                        }
                                        if($(documentoDol)[0].nodeName == 'INPUT'){
                                            $(menssage).insertAfter($(documentoDol));
                                        }

                                    }
                                }
                                if(Errores.length>0){
                                    return false;
                                }else{
                                    return true;
                                }
                            };
                            //end form valid

                            $("#ckeckedDoctorHabilitar").change(function () {
                                if(this.checked){
                                    if($("#odontol_conf").parent().parent().hasClass('hide')){
                                        $("#odontol_conf").parent().parent().removeClass('hide')
                                    }
                                }else{
                                    $("#odontol_conf").parent().parent().addClass('hide')
                                }
                                $("#odontol_conf").val(null).trigger('change');

                            });

                            $("#ckeckedAllAdminUsers").change(function () {
                                if(this.checked){
                                    $("#perfilpermm_conf").attr('disabled', true)
                                        .parent()
                                        .parent().addClass('hide');
                                }else{
                                    $("#perfilpermm_conf").attr('disabled', false)
                                        .parent()
                                        .parent().removeClass('hide');
                                }
                            });

                            $("input").keyup(function () {
                                FormValidUsuariosConf();
                            });
                            $("select").change(function () {
                                FormValidUsuariosConf();
                            });

                            function  fetchusers(fetchMod=false) {
                                var usuario     = $("#usuario_conf"); //0
                                var pass        = btoa($("#pass_conf").val()); //pass en base64 //1
                                var perfilperm  = $("#perfilpermm_conf").find(":selected").val() || 0; //2
                                var Doctor      = $("#odontol_conf").find(":selected").val() || 0; //3
                                var admin_users = document.getElementById("ckeckedAllAdminUsers").checked || 0; //4

                                var fetch = [];
                                fetch.push( usuario.val(), pass, perfilperm, Doctor, admin_users );
                                if(fetchMod==true){
                                    boxloading($boxContentConfiguracion,true);
                                    $.ajax({
                                        url: $DOCUMENTO_URL_HTTP + '/application/system/configuraciones/controller/controller_config_clinico.php',
                                        type:'POST',
                                        data: {'id':id, 'ajaxSend':'ajaxSend', 'accion':'fetchDatosUsurs'},
                                        dataType:'json',
                                        cache:false,
                                        async:true,
                                        complete:function (xhr, status) {
                                            boxloading($boxContentConfiguracion,false, 1000);
                                        }, 
                                        success:function (response) {

                                            if(response['error']!=""){
                                                notificacion(response['error'], "error");
                                                return false;
                                            }

                                            usuario.val(response['users']['usuario']);
                                            $("#pass_conf").val(atob(response['users']['passwor_abc']));

                                            if(response['users']['fk_doc']!="0"){
                                                $("#ckeckedDoctorHabilitar")
                                                    .prop("checked", true).trigger("change");
                                                $("#odontol_conf").val(response['users']['fk_doc']).trigger("change");
                                            }
                                            if(response['users']['admin']==1){
                                                $("#ckeckedAllAdminUsers")
                                                    .prop("checked", true).trigger("change");
                                            }

                                        }
                                    });

                                }

                                return fetch;
                            }

                            //guarda el usuario
                            function GuardarUsers(Estebtn){

                                if(FormValidUsuariosConf() ==false){
                                    return false;
                                }

                                var fetch = fetchusers(false);

                                var datos = {
                                    'accion'  : 'createUsers',
                                    'ajaxSend': 'ajaxSend',
                                    'id'      : id,
                                    'datos'   : JSON.stringify({
                                        'usuario' : fetch[0],
                                        'pass'    : fetch[1],
                                        'perfil'  : fetch[2],
                                        'doctor'  : fetch[3],
                                        'admin'   : fetch[4],
                                    }),
                                };
                                button_loadding(Estebtn, true);
                                $.ajax({
                                    url: $DOCUMENTO_URL_HTTP + '/application/system/configuraciones/controller/controller_config_clinico.php',
                                    type:'POST',
                                    data: datos,
                                    dataType:'json',
                                    async:true,
                                    cache:false,
                                    complete:function(xhr, status){
                                        button_loadding(Estebtn, false);
                                    },
                                    success: function (response) {
                                        if(response['error']!=""){
                                            notificacion(response['error'] ,'error');
                                        }else{
                                            notificacion('Información Actualizada', 'success');
                                        }
                                    }
                                });
                            }
                            $(document).ready(function () {

                            });
                            $(window).on('load', function () {
                                $("#odontol_conf, #perfilpermm_conf").select2({
                                    placeholder:'Seleccione una opción',
                                    allowClear:true,
                                    language:languageEs
                                });
                                //modificar id > 0
                                if(id>0){
                                    fetchusers(true);
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
