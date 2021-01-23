
<?php

#breadcrumbs  -----------------------------------------------
$url_breadcrumbs = $_SERVER['REQUEST_URI'];
$titulo = "Cajas Clinicas";
$modulo = true;

?>

<div class="form-group col-xs-12 col-md-12 col-lg-12 no-padding">
    <div  class="form-group col-md-12 col-xs-12">
        <?= Breadcrumbs_Mod($titulo, $url_breadcrumbs, $modulo); ?>
        <label for="">LISTA DE COMPORTAMIENTOS</label>
        <ul class="list-inline" style="border-bottom: 0.6px solid #333333; padding: 3px">
            <li> <a href="#ContenFiltroDocumentos" data-toggle="collapse" style="color: #333333" class="btnhover btn btn-sm " id="fitrar_document"> <b>  ▼ &nbsp;Filtrar <i ></i> </b> </a> </li>
            <li> <a href="#"  style="color: #333333" class="btnhover btn btn-sm " id="crear_nueva_caja"> <b>  Crear Caja </b> </a> </li>
        </ul>
    </div>

    <div id="ContenFiltroDocumentos"  class="form-group col-md-12 col-xs-12 collapse " aria-expanded="false"  >
        <div class="form-group col-md-12 col-xs-12" style="background-color: #f4f4f4; padding: 25px">
            <h3 class="no-margin"><span>Filtrar Cajas Clinicas</span></h3>

            <div class="row">
                <div class="form-group col-md-3 col-sm-12 col-xs-12">
                    <label for="">Cajas</label>
                    <select name="" id="" class="form-control"></select>
                </div>
            </div>

        </div>
    </div>

    <div class="form-group col-xs-12 col-md-12 col-lg-12 ">
        <div class="table-responsive">
            <table class="table table-striped" id="listCajas" width="100%">
                <thead>
                    <tr>
                        <th></th>
                        <th>usuario create</th>
                        <th>Caja #N</th>
                        <th>Dirección</th>
                        <th>Saldo Acumulado</th>
                        <th>Estado</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>


<!-- Modal -->
<div class="modal fade" id="transaccionCaja_modal" role="dialog">
    <div class="modal-dialog " style="width: 70%; margin: 3% auto">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header modal-diseng">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title" id="form_name_caja"></h4>
            </div>
            <div class="modal-body">
               <div class="row">
                   <div class="form-group">
                       <div class="col-md-12 col-xs-12">
                           <table width="100%" class="table-striped" >
                               <tr>
                                   <td ><b>Usuario:</b></td>
                                   <td width="30%">&nbsp;<?= $user->name ?></td>
                                   <td><b>Dirección:</b></td>
                                   <td width="50%" align="right" id="direccion_mod_trans"></td>
                               </tr>
                               <tr>
                                   <td colspan="2">Saldo Anterior:</td>
                                   <td colspan="2" align="right" id="saldoAnterior_mod_trans"></td>
                               </tr>
                               <tr>
                                   <td colspan="2">Saldo Actual:</td>
                                   <td colspan="2" align="right" id="saldoActual_mod_trans"></td>
                               </tr>
                               <tr>
                                   <td colspan="2"><b>Recaudado:</b></td>
                                   <td colspan="2" align="right" id="recaudad_mod_trans"></td>
                               </tr>
                               <tr>
                                   <td colspan="4"> <span>Transacciones de Caja</span> </td>
                               </tr>
                           </table>
                       </div>
                       <style>
                           .transacionesCaja {
                               font-size: 1.4rem;
                           }
                           .transacionesCaja tbody tr{
                               border-bottom: 1px solid #dddddd;
                           }
                       </style>
                       <div class="col-md-12 col-xs-12">
                           <div class="table-responsive">
                               <table class="table compact transacionesCaja table-condensed" id="transacionesCaja" width="100%">
                                   <thead>
                                        <tr>
                                            <th width="10%">&nbsp;</th>
                                            <th width="60%">Movimiento de Caja</th>
                                            <th width="10%">&nbsp;</th>
                                            <th width="10%">&nbsp;</th>
                                        </tr>
                                   </thead>
                               </table>
                           </div>
                       </div>
                   </div>
               </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>

    </div>
</div>


<script>

    $("#crear_nueva_caja").on("click", function() {

        if(!ModulePermission(25,2)){
            notificacion('Ud. no tiene permiso para crear', 'question');
            return false;
        }

        window.location = $DOCUMENTO_URL_HTTP + '/application/system/cajas/index.php?view=f_cajas_add';

    });

    function ActivarDesactivarCaja(id_caja,estado, Element){

        if( (id_caja=="" && estado=="") && (Element.prop('dataset').id=="" && Element.prop('dataset').status=="") ){
            notificacion('Ocurrió un error con la Operación caja', 'error');
            return false;
        }

        var url = $DOCUMENTO_URL_HTTP + '/application/system/cajas/controller/controller_caja.php';
        var data = {
            "accion":"update_stado_caja",
            "ajaxSend":"ajaxSend",
            "estado":estado,
            "idcaja":id_caja
        };

        $.get(url,data, function (data) {
            var datos = $.parseJSON(data);
            if(datos['error']==''){
                var table = $("#listCajas").DataTable();
                table.ajax.reload(null, false);
                notificacion('Información Actualizado', 'success');
            }else{
                notificacion('Ocurrió un error con la Operación Actualizar Caja', 'error');
            }
        });

    }


    function transaccionCaja_modal(id){

        if(id!=""){

            var parametros = {
                'accion':'fetch_caja_information',
                'ajaxSend':'ajaxSend',
                'id':id
            };

            $.ajax({
                url: $DOCUMENTO_URL_HTTP + '/application/system/cajas/controller/controller_caja.php',
                type:'POST',
                data: parametros,
                dataType:'json',
                async:false,
                cache:false,
                complete:function(xhr, status) {

                    if(xhr['status']=='200'){
                        boxloading($boxContentCajasModule,true,1000);
                    }else{
                        if(xhr['status']=='404'){
                            notificacion("Ocurrió un error con la <b>Obtener parametros</b> <br> <b>xhr: "+xhr['status']+" <br> Consulte con Soporte </b>");
                        }
                        boxloading($boxContentCajasModule,true,1000);
                    }
                },
                success:function(respuesta) {

                    boxloading($boxContentCajasModule,false,1000);

                    if(respuesta['error']!=''){
                        notificacion(respuesta['error'], 'error');
                    }else{

                        var datatransaccionCaja = {
                            'datacab' : respuesta['fetchColumnData'],
                            'datadetMov' : respuesta['fetchColumnDataMov']
                        };

                        fetchInformacionModal(datatransaccionCaja, id);
                    }
                }
            });
        }else{
            notificacion('Ocurrió un error con la Operación error de parametros', 'error');
        }

    }

    var fetchInformacionModal = function(data, idcaja) {

        var cabCaja   = data['datacab'];
        var nameCaja  = "<b>Caja #"+cabCaja.rowid+"</b>  &nbsp;&nbsp;  <b>Total:</b> &nbsp; $ "+ cabCaja.total_caja;
        var direccion = cabCaja.direccion;
        var saldoAnterior = cabCaja.saldo_anterior;
        var saldo_actual = cabCaja.saldo_actual;

        $("#form_name_caja").html("<span>"+nameCaja+"</span>").attr('data-idcaja', idcaja);
        $("#direccion_mod_trans").text(direccion);
        $("#saldoAnterior_mod_trans").text(saldoAnterior);
        $("#saldoActual_mod_trans").text(saldo_actual);
        $("#recaudad_mod_trans").html("<b>"+cabCaja.total_caja+"</b>");

        Dtable_transaccion(idcaja);

        console.log(data);
    };

    var Dtable_transaccion = function(idCaja){

        $("#transacionesCaja").DataTable({
            searching: false,
            destroy:true,
            ordering:false,
            ajax:{
                url: $DOCUMENTO_URL_HTTP + '/application/system/cajas/controller/controller_caja.php',
                type:"POST",
                data:{
                    "accion":"transaciones_caja",
                    "ajaxSend":"ajaxSend",
                    "idCaja" : idCaja
                },
                dataType:"json"
            },
            columnDefs:[
                {
                    targets:1,
                    render: function(data, type, full, meta){
                        console.log(data);
                        return "<p style='white-space: pre-wrap'>"+data+"</p>";
                    }
                }
            ],
            createdRow: function ( row, data, dataIndex ) {
                $(row).find('td').eq(1).css('width','60%');
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

    };



    $(window).on("load", function() {

        $("#listCajas").DataTable({
            // processing: true,
            searching: true,
            destroy:true,
            ordering:false,

            ajax:{
                url: $DOCUMENTO_URL_HTTP + '/application/system/cajas/controller/controller_caja.php',
                type:"POST",
                data:{
                    "accion":"listCajas",
                    "ajaxSend":"ajaxSend",
                },
                dataType:"json"
            },
            "columnDefs":[
                {
                    targets:0,
                    render: function(data, type, full, meta){
                        var domMenu = "<div class='dropdown pull-left'>";
                        domMenu += "<button class='btn btnhover  dropdown-toggle btn-xs ' type='button' data-toggle='dropdown' style='100%' aria-expanded='true'>" +
                            "<i class=\"fa fa-ellipsis-v\"></i>" +
                            "</button>";
                        domMenu += "<ul class='dropdown-menu pull-left'>";

                            if(full['estado_id']=='A')
                                domMenu += "<li> <a href='#' onclick='ActivarDesactivarCaja("+full['id_caja']+",\"E\", $(this))' data-id='"+full['id_caja']+"' data-status='E' >inhabilitar caja</a> </li>";
                            if(full['estado_id']=='E')
                                domMenu += "<li> <a href='#' onclick='ActivarDesactivarCaja("+full['id_caja']+",\"A\", $(this))' data-id='"+full['id_caja']+"' data-status='A' >habilitar caja</a> </li>";

                            domMenu += "<li> <a href='#transaccionCaja_modal' data-toggle='modal' onclick='transaccionCaja_modal("+full['id_caja']+")' >ver transaciones de caja</a> </li>";
                            domMenu += "</ul>";
                        domMenu += "</div>";

                        return domMenu;
                    }
                }
            ],
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

    });

</script>
