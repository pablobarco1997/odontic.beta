<?php

if(isset($_GET['v'])){
    if($_GET['v']=='list'){
        $v = 'list';
    }else if($_GET['v']=='add'){
        $v = 'add';
    }else if($_GET['v']=='perfiles'){
        $v = 'perfiles';
    }else if($_GET['v']=='listperfiles'){
        $v = 'listperfiles';
    }else{
        $v = '';
        echo 'Ocurrio un error. Parametros de entrada, Consulte con soporte';
        die();
    }
}else{
    $v = '';
    echo 'Ocurrio un error. Parametros de entrada, Consulte con soporte';
    die();
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

            <?php  accessoModule('Usuarios') ?>

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
                        <li>
                            <a href="<?= DOL_HTTP.'/application/system/configuraciones/?view=admin_users&v=listperfiles' ?>" class="btnhover btn btn-sm" style="font-weight: bolder; color: #333333; ">
                                <span class="fa fa-unlock"></span> <b>Perfiles Permisos</b>
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
                                        <th width="30%">Perfiles</th>
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
                                                    $result_ab = $db->query("select rowid, nom from tab_login_perfil_name where estado = 'A' ");
                                                    if($result_ab){
                                                        if($result_ab->rowCount()>0){
                                                            $result_ab = $result_ab->fetchAll(PDO::FETCH_ASSOC);
                                                            foreach ($result_ab  as $item){
                                                                print "<option value='".$item['rowid']."'>".$item['nom']."</option>";
                                                            }
                                                        }
                                                    }
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
                                if(pass.val()==""){
                                    Errores.push({
                                        "documento" :   pass,
                                        "mesg" :  "Campo Obligatorio",
                                    });
                                }
                                if(usuario.val()==""){
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

                                    $("#perfilpermm_conf")
                                        .val(null)
                                        .trigger('change');

                                }else{
                                    $("#perfilpermm_conf").attr('disabled', false)
                                        .parent()
                                        .parent().removeClass('hide');

                                    $("#perfilpermm_conf")
                                        .val(null)
                                        .trigger('change');
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

                                            if(response['users']['fk_perfil']!="0"){
                                                $("#perfilpermm_conf").val(response['users']['fk_perfil']).trigger("change");
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

                                            FormValidUsuariosConf();
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
                                            window.location = $DOCUMENTO_URL_HTTP + "/application/system/configuraciones/?view=admin_users&v=list";
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


                <!-- PERFILES DEL USUARIO Y PERMISOS ASOCIADOS -->

                <!--agregar modificar perfiles-->
                <?php
                    if($v=='perfiles'){

                        $id = ((isset($_GET['id'])) ? $_GET['id'] : "");
                ?>
                        <div class="form-group  col-md-12 col-xs-12 col-lg-12" >
                            <div class="form-horizontal">
                                <div class="conf_form_perfiles">
                                    <div class="form-group">
                                        <label for="" class="control-label col-sm-4">PERFIL</label>
                                        <div class="col-sm-6">

                                            <?php
                                                if($id!=""){
                                                    $sql_bx     = "select p.nom, p.rowid , p.desc from tab_login_perfil_name p where p.rowid = $id";
                                                    $result_bx  = $db->query($sql_bx)->fetchObject();
                                                    $nom        = $result_bx->nom;
                                                    $desc       = $result_bx->desc;
                                                }else{
                                                    $nom  = "";
                                                    $desc = "";
                                                }
                                            ?>

                                            <input type="text" id="name_perfil" class="form-control " value="<?= $nom ?>" style="width: 100%"  >
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="" class="control-label col-sm-4">DESCRIPCIÓN</label>
                                        <div class="col-sm-6">
                                            <textarea class="form-control text-left " style="resize: vertical" id="desc_Perfil_modificar"  ><?= $desc ?></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>


                            <div class="form-group col-md-12 col-xs-12 col-sm-12 col-lg-10 col-centered" >
                                <div class="table-responsive">
                                    <table class="table table-hover perfiles_Permissions" id="perfiles_Permissions" width="100%" >
                                        <thead style="background-color: #f4f4f4;">
                                            <tr>
                                                <th width="40%" rowspan="2">Modulo</th>
                                                <th width="20%" rowspan="2" class="text-center">Permiso</th>
                                                <th width="10%" rowspan="2"> Permiso Modulo </th>
                                                <th width="80%" rowspan="1" colspan="4" style="text-align: center">acción</th>
                                            </tr>
                                            <tr>
                                                <?php
                                                    $consultarPermisos = array();
                                                    $result_abc = $db->query("select rowid as id_permiso, name  from tab_login_permissions where estado = 'A' ")->fetchAll(PDO::FETCH_ASSOC);
                                                    if(count($result_abc)>0){
                                                        $consultarPermisos = $result_abc;
                                                        foreach ($result_abc as $item){
                                                            print '<th width="10%" class="text-center" >'.$item['name'].'</th>';
                                                        }
                                                    }
                                                ?>
                                            </tr>
                                        </thead>

                                        <tfoot>
                                            <tr>
                                                <td colspan="<?= (double)(count($consultarPermisos)+3) ?>">
                                                    <button class="btn" style="color: green; float: right" onclick="GuardarPermisos($(this))">
                                                        <b> Guardar </b>
                                                        <span class="fa fa-refresh btnSpinner hide"></span>
                                                    </button>
                                                </td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>


                        <script>

                            var id = "<?= (isset($_GET['id'])) ? $_GET['id'] : ""?>";


                            var FormValidarPermits = function() {
                                var Errores = [];
                                var name = $("#name_perfil");
                                if(name.val() == ""){
                                    Errores.push({
                                        "documento" :   name,
                                        "mesg" :  "Campo Obligatorio",
                                    });
                                }

                                $(".error_usuario_perfiles_msg").remove();

                                if(Errores.length>0){
                                    for (var i=0; i<=Errores.length-1;i++ ){
                                        var menssage =  document.createElement("small");
                                            menssage.setAttribute("style","display: block; color:red;");
                                            menssage.setAttribute("class","error_usuario_perfiles_msg");
                                            menssage.appendChild(document.createTextNode(Errores[i]['mesg']));
                                        var dom        = Errores[i]['documento'];
                                        if( $(dom)[0].nodeName == 'SELECT' ){
                                            $(menssage).insertAfter($(dom).parent().find('span:eq(0)'));
                                        }
                                        if( $(dom)[0].nodeName == 'INPUT'){
                                            $(menssage).insertAfter($(dom));
                                        }
                                    }
                                }

                                if(Errores.length>0){
                                    return false;
                                }else{
                                    return true;
                                }
                            };

                            $('input[type="text"]').keyup(function () {
                                FormValidarPermits();
                            });

                            function Permissions_table(datos){

                                var valuepermisos = [];
                                    valuepermisos = <?= (count($consultarPermisos)==0) ? json_encode([]): json_encode($consultarPermisos) ?>;

                                var tbody = "<tbody >";
                                        $.each(datos['datos'], function (i, item) {
                                            var idpadremod = item[0];  //id modulo padre
                                            if(item[2] == "son"){//hijo
                                                tbody += "<tr>";
                                                tbody += "<td >"+item[1]+" </td>";
                                                tbody += "<td class='text-center'> <input type='checkbox'  class='checked_all' onclick='checked_all($(this))' > </td>";
                                                tbody += "<td class='text-center'> <input type='checkbox'  class=' module active_module_"+idpadremod+"'  value='"+idpadremod+"' > </td>";
                                                    if( valuepermisos.length > 0 ){
                                                        for (var i=0; i<= valuepermisos.length-1; i++){
                                                                 var fetch = valuepermisos[i];
                                                                 var addClassid = idpadremod+"_"+fetch['id_permiso']; //id modulo y id permiso
                                                                 var nom = item[1]+'_'+fetch['name'];
                                                            tbody += "<td class='text-center'> <input type='checkbox' class='"+addClassid+" "+nom+" checkedChildren' onclick='checked_all($(this), true)' value='"+addClassid+"' ></td>";
                                                        }
                                                    }else{
                                                        boxloading($boxContentConfiguracion,true);
                                                        notificacion("Ocurrio un error con la Operación Consulte con Soporte", "error");
                                                        return false;
                                                    }

                                                tbody += "</tr>";
                                            }else{
                                                //padre
                                                tbody += "<tr >";
                                                    tbody += "<td colspan='6' class='' style='font-weight: bolder'>"+item[1].toUpperCase()+" </td>";
                                                tbody += "</tr>";

                                                //hijos del padre
                                                $.each(item[3], function (i_a, item_b) {
                                                    // var addClassid = item_b['rowid']+"_"+
                                                    tbody += "<tr>";
                                                        tbody += "<td >"+item_b['name']+" </td>";
                                                    tbody += "<td class='text-center'> <input type='checkbox'  class='checked_all' onclick='checked_all($(this))' > </td>";
                                                    tbody += "<td class='text-center'> <input type='checkbox'  class=' module active_module_"+item_b['rowid']+"'  value='"+item_b['rowid']+"' > </td>";

                                                        if( valuepermisos.length > 0 ){
                                                            var i=0;
                                                            for (i=0; i<= valuepermisos.length-1; i++){
                                                                var fetch = valuepermisos[i];
                                                                var addClassid = item_b['rowid']+"_"+fetch['id_permiso']; //id modulo y id permiso
                                                                var nom = item_b['name']+'_'+fetch['name'];
                                                                tbody += "<td class='text-center'> <input type='checkbox' class='"+addClassid+" "+nom+" checkedChildren' onclick='checked_all($(this), true)' value='"+addClassid+"' ></td>";
                                                            }
                                                        }else{
                                                            boxloading($boxContentConfiguracion,true);
                                                            notificacion("Ocurrio un error con la Operación Consulte con Soporte", "error");
                                                            return false;
                                                        }

                                                    tbody += "</tr>";
                                                });
                                            }
                                        });
                                tbody += "</tbody>";

                                $("#perfiles_Permissions").append($(tbody));

                            }

                            function fetchModulePermiso(fetchMod=false, id=0){

                                boxloading($boxContentConfiguracion,true);

                                $.ajax({
                                    url: $DOCUMENTO_URL_HTTP + '/application/system/configuraciones/controller/controller_config_clinico.php',
                                    type:'POST',
                                    data: {'id':id, 'ajaxSend':'ajaxSend', 'accion':'fetchModulosPermisos'},
                                    dataType:'json',
                                    cache:false,
                                    async:false,
                                    complete:function (xhr, status) {
                                        boxloading($boxContentConfiguracion,false, 1000);
                                    },
                                    success:function (response) {
                                        Permissions_table(response);
                                    }
                                });

                            }

                            function checked_all(Elemento, child = false){

                                var padre = Elemento.parents('tr');
                                var checkboxChild = padre.find('input[type="checkbox"]:not(.checked_all)');
                                var checkboxChild = checkboxChild.not(".module");
                                //checked padre
                                if(child == false){
                                    if(Elemento.is(":checked")){
                                        checkboxChild.prop('checked', true);
                                        Elemento.find('.module').prop('checked', true); 
                                    }else{
                                        checkboxChild.prop('checked', false);
                                    }
                                }

                                //checked hijo
                                if(child ==  true){
                                    if(checkboxChild.not(':checked').length > 0  ){ //si los que NOT esta checked padre false
                                        padre.find(".checked_all").prop('checked', false);
                                    }else{
                                        padre.find(".checked_all").prop('checked', true); //caso contrario true si estan todos 4 checked
                                    }
                                }
                            }

                            function fetchpermits(Mod = false){
                                var value   =  $("#perfiles_Permissions").children();
                                    value   =  value.find('input[type="checkbox"]:checked:not(.checked_all)');
                                    value   =  value.filter(":checked");
                                var checked = [];
                                    checked.module = [];
                                    checked.checkedChildren = [];

                                $.each(value, function (i, item) {
                                    if($(item).hasClass('module')){
                                        checked.module.push($(item).val());
                                    }
                                    if($(item).hasClass('checkedChildren')){
                                        checked.checkedChildren.push($(item).val());
                                    }
                                });

                                if(Mod){
                                    boxloading($boxContentConfiguracion,true);
                                    $.ajax({
                                        url: $DOCUMENTO_URL_HTTP + '/application/system/configuraciones/controller/controller_config_clinico.php',
                                        type: "POST",
                                        data:{"ajaxSend":"ajaxSend", "accion":"fetchModperfiles", "id":id},
                                        dataType:"json",
                                        cache:false,
                                        async:true,
                                        delay:500,
                                        complete:function(xhr, status){
                                            boxloading($boxContentConfiguracion,false, 1000);
                                        },
                                        success: function (responce) {
                                            $("#name_perfil").val( responce['success']['name'] );
                                            //permisos Hijos
                                            if(responce['success']['permits'].length != 0){
                                                var last_element;
                                                for (var i = 0; i <= responce['success']['permits'].length -1; i++){
                                                        var ElementChecked = $("."+responce['success']['permits'][i]['key_id']);
                                                        ElementChecked.prop('checked', true);
                                                }
                                            }
                                            //permisos Modulo
                                            if(responce['success']['permitsModule'].length  != 0){
                                                for (var c = 0; c <= responce['success']['permitsModule'].length -1; c++){
                                                    var ElementChecked = $(".active_module_"+responce['success']['permitsModule'][c]['id_modulo']);
                                                    ElementChecked.prop('checked', true);
                                                }
                                            }

                                            //se comprubue y valida los checked padres
                                            $(".checked_all").each(function (i, item) {
                                                checked_all($(item), true);
                                            });
                                        }
                                    });
                                }
                                return checked;
                            }


                            function GuardarPermisos(Elemento){

                                if(FormValidarPermits()==false){
                                    return false;
                                }

                                button_loadding(Elemento, true);
                                var permits = fetchpermits();
                                var module = permits['module'];
                                var child  = permits['checkedChildren'];
                                $.ajax({
                                    url: $DOCUMENTO_URL_HTTP + '/application/system/configuraciones/controller/controller_config_clinico.php',
                                    type: "POST",
                                    data:{
                                        "ajaxSend":"ajaxSend",
                                        "accion":"nuevoPerfilpermits",
                                        "fetchpermitsModule": JSON.stringify(module),
                                        "fetchpermitsChild":  JSON.stringify(child),
                                        "id":id,
                                        "name": $("#name_perfil").val(),
                                        "desc" : $("#desc_Perfil_modificar").val()
                                    },
                                    dataType:"json",
                                    cache:false,
                                    async:true,
                                    complete:function(xhr, status){
                                        button_loadding(Elemento, false);
                                    },
                                    success: function (responce) {
                                        if(responce.error == ""){
                                            notificacion("Información Actualizado", "success");
                                            window.location.reload(true);
                                            window.location = $DOCUMENTO_URL_HTTP + "/application/system/configuraciones/?view=admin_users&v=listperfiles";
                                        }else{
                                            notificacion( responce.error  , "success");
                                        }
                                    }
                                });
                            }



                            $(document).ready(function () {

                                //si es nuevo perfil
                                if("<?= (isset($_GET['id'])) ? $_GET['id'] : ""?>" == ""){
                                    $("#modificar_checked_perfil")
                                        .parent()
                                        .parent()
                                        .addClass('hide');
                                }
                            });


                            $(window).on("load", function () {

                                fetchModulePermiso();

                                if("<?= (isset($_GET['id'])) ? $_GET['id'] : ""?>" != ""){
                                    boxloading($boxContentConfiguracion,true);
                                    fetchpermits(true);
                                }

                                $("#mod_name_perfil").select2({
                                    placeholder: "Seleccione un Perfil",
                                    language: languageEs,
                                    allowClear: true,
                                });

                            });

                        </script>

                <?php
                    }
                ?>


                <!--lista de perfiles-->
                <?php
                    if($v=='listperfiles')
                    {

                ?>
                        <div class="form-group col-md-12 col-xs-12 col-lg-12">
                            <ul class="list-inline" style="display: block; background-color: #f4f4f4; float: right">
                                <li>
                                    <a class="btn  btn-sm " href="<?= DOL_HTTP.'/application/system/configuraciones/?view=admin_users&v=perfiles' ?>"> <span class="fa fa-plus-square"></span> Crear </a>
                                </li>
                            </ul>
                        </div>

                        <div class="form-group col-md-12 col-xs-12 col-lg-12">
                            <div class="table-responsive">
                                <table class="table table-hover"  style="width: 100%" id="list_perfiles_permisos">
                                    <thead style="background-color: #f4f4f4">
                                        <tr>
                                            <th width="2%"></th>
                                            <th width="50%">Perfil</th>
                                            <th width="15%">Estado</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>


                        <script>


                            function list_permisos_perfil(){

                                var table = $("#list_perfiles_permisos").DataTable({
                                    searching: false,
                                    ordering:false,
                                    destroy:true,
                                    serverSide: true,
                                    lengthChange: false,
                                    processing: true,
                                    lengthMenu:[ 10 ],
                                    ajax:{
                                        url: $DOCUMENTO_URL_HTTP + '/application/system/configuraciones/controller/controller_config_clinico.php',
                                        type:'POST',
                                        data:{'ajaxSend':'ajaxSend', 'accion':'list_perfiles_permisos'},
                                        dataType:'json',
                                        cache:false,
                                        async: true
                                    },
                                    columnDefs:[
                                        {
                                            targets:0 ,
                                            render: function (data , type , full , meta) {
                                                var menu = "";
                                                var url_mod     = $DOCUMENTO_URL_HTTP+"/application/system/configuraciones/?view=admin_users&v=perfiles&id="+full['id'];
                                                menu += "<div class='dropdown'>";
                                                menu += "<button class='btn btnhover btn-xs dropdown-toggle' type='button' data-toggle='dropdown' > <i style='padding-top: 3px; padding-bottom: 3px' class=\"fa fa-ellipsis-v\"></i> </button> ";
                                                menu += "<ul class=\"dropdown-menu\"> ";
                                                    menu += "<li> <a style='cursor: pointer;' href='#' data-url='"+url_mod+"' onclick='Modperfiles($(this))'> Editar  </a> </li>";
                                                menu += "</ul>";
                                                menu += "</div>";
                                                return menu;
                                            }
                                        }
                                    ],
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

                            function Modperfiles(Elemento){
                                window.location = Elemento.prop('dataset').url;
                            }

                            $(window).on("load", function () {

                                list_permisos_perfil();
                            });

                        </script>

                <?php }?>

            </div>
        </div>
    </div>
</div>
