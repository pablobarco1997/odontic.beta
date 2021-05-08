

<div class="form-group col-md-12 col-xs-12">
    <label for="">LISTA DE COMPORTAMIENTOS</label>
    <ul class="list-inline" style="background-color: #f0f0f0;border-bottom: 0.6px solid #333333; padding: 3px">
        <li> <a data-toggle="collapse" data-target="#contentFilter" class="btnhover btn btn-sm collapsed" style="color: #333333" aria-expanded="false"> <b>   ▼  Filtrar  </b>  </a> </li>
        <li> <a class="btnhover btn btn-sm " style="color: #333333" onclick="Programar()" > <b> <i class="fa fa-clock-o"></i> Programar  </b>  </a> </li>
    </ul>
</div>

<div class="form-group col-xs-12 col-md-12 col-lg-12 collapse" id="contentFilter" aria-expanded="false" style="height: 0px;">
    <div class="form-group col-xs-12 col-md-12 col-sm-12 col-lg-12" style="background-color: #f4f4f4; padding-top: 15px">
        <div class="form-group col-md-12 col-xs-12 col-lg-12">
            <h3 class="no-margin"><span>Filtrar E-mail</span></h3>
        </div>
        <div class="form-group col-md-4 col-xs-12">
            <label for="">Fecha</label>
            <div class="input-group form-group rango" style="margin: 0">
                <input type="text" class="form-control filtroFecha  " readonly="" id="startDate" value="">
                <span class="input-group-addon" style="border-radius: 0"><i class="fa fa-calendar"></i></span>
            </div>
        </div>
        <div class="form-group col-md-12 col-xs-12">
            <ul class="list-inline pull-right">
                <li>  <button class="limpiar btn   btn-block  btn-default" id="limpiarFiltro" name="limpiarFiltro" style="float: right; padding: 10px"> &nbsp; &nbsp; Limpiar &nbsp; &nbsp;</button> </li>
                <li>  <button class="aplicar btn   btn-block  btn-success" id="aplicarFiltro" name="aplicarFiltro" style="float: right; padding: 10px"> &nbsp;  &nbsp;Aplicar busqueda &nbsp;</button> </li>
            </ul>
        </div>
    </div>
</div>

<style>
    .fixedHeader-floating{
        top: -5px!important;
    }
</style>

<div class="form-group col-md-12 col-xs-12">
    <div class="table-responsive">
        <table class="table" id="CorreoListProgram" width="100%">
            <thead>
                <tr>
                    <th>Emitido</th>
                    <th>destinario</th>
                    <th>Asunto</th>
                    <th>mensaje</th>
                    <th>Programado al</th>
                    <th>Estado</th>
                    <th></th>
                </tr>
            </thead>
        </table>
        <br>
        <br>
    </div>
</div>


<!--//modal add fecha programable -->
<div id="modalInfoCorreo" class="modal fade " role="dialog" data-backdrop="static">
    <div class="modal-dialog " >

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header modal-diseng">
                <button type="button" class="close" data-dismiss="modal">×</button>
                <h4 class="modal-title" id="iddet-comment" data-iddet="163"><span>Programar Correo</span></h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-xs-12 col-md-12">
                        <table style="width: 100%" class="table">
                            <tr>
                                <td width="40%"><b>Destinario:</b></td>
                                <td id="destinarioModal"></td>
                            </tr>
                            <tr>
                                <td width="40%"><b>Asunto:</b></td>
                                <td id="asuntoModal"></td>
                            </tr>
                            <tr>
                                <td colspan="2"><b>Mensage:</b></td>
                            </tr>
                            <tr>
                                <td colspan="2" id="msgModal"></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>


<script>


    var Programar= function(){

        if(!ModulePermission(27, 2)){
            notificacion('Ud. No tiene permiso para esta Operación','error');
            return false;
        }

        window.location = $DOCUMENTO_URL_HTTP + '/application/system/pacientes/pacientes_admin/index.php?view=programa_email&key='+$keyGlobal+'&id='+$Paciente_id+'&v=crear_programacion_email';

    };

    $(document).ready(function () {


        var parametros = {
            'accion':'listdateCorreosProgram',
            'ajaxSend':'ajaxSend',
            'idpaciente':$id_paciente,

        };

        table = $('#CorreoListProgram').DataTable({
            searching: false,
            "ordering":false,
            "serverSide": true,
            fixedHeader: true,
            paging:true,
            processing: true,
            lengthMenu:[ 10, 25, 50, 100 ],
            "ajax":{
                "url": $DOCUMENTO_URL_HTTP + '/application/system/pacientes/pacientes_admin/email_programar/controller.php',
                "type":'POST',
                "data": parametros,
                "dataType":'json',
            },
            'createdRow':function(row, data, index){

                $(row).children().eq(0).css('width','10%');
                $(row).children().eq(1).css('width','8%');
                $(row).children().eq(2).css('width','15%');
                $(row).children().eq(3).css('width','25%');
                $(row).children().eq(4).css('width','10%');
                $(row).children().eq(5).css('width','10%');
                $(row).children().eq(6).css('width','2%');
            },
            columnDefs:[
                {
                    targets:6,
                    render:function (data, type, row, meta) {

                        var info = ""+row['dataCorreo']+"";
                        var idcorreo = row['idcorreo'];

                        var dropdown = '<div class="dropdown pull-right">'+
                            '           <button class="btn btnhover  btn-xs dropdown-toggle" type="button" data-toggle="dropdown" style="height: 100%" aria-expanded="false"> <i class="fa fa-ellipsis-v"></i> </button>\n' +
                            '                <ul class="dropdown-menu">';
                                    dropdown += '<li>   <a style="cursor: pointer; " href="#" onclick="MostraInfoCorreo($(this))" data-info='+info+' >Mostrar Correo Completo</a> </li>';
                                    if(row['estado']=='P' || row['estado']=='N'){ //solo estado pendiente o  anulado
                                        dropdown += '<li>   <a style="cursor: pointer; color: red;  " href="#" onclick="DeleteCorreo('+idcorreo+')" data-info='+info+' data-idcor="'+idcorreo+'" >Eliminar Correo</a> </li>';
                                    }
                                    if(row['estado']=='C'){ //solo estado Programado
                                        dropdown += '<li>   <a style="cursor: pointer; color: red;  " href="#" onclick="To_EstadoAnulado('+idcorreo+')" data-info='+info+' data-idcor="'+idcorreo+'" >Anular Correo</a> </li>';
                                    }
                                dropdown += '</ul>' +
                                '</div>';
                        return dropdown;
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
            "infoCallback": function (settings, start, end, max, total, pre){

                return "Mostrando registros del "+ start +" al "+ end +"<br>de un total de "+total+ " registros.";
            }
            // ajax:{
            //
            // },
        });
        // new $.fn.dataTable.FixedHeader( table );
        new $.fn.dataTable.FixedHeader( table,
            {
                headerOffset: 50
            }
        );

    });

    function  MostraInfoCorreo(Elemen) {

        var arr = JSON.parse(atob(Elemen.prop('dataset').info));
        console.log(arr);
        $("#modalInfoCorreo").modal("show");

        $("#destinarioModal").text(arr['destinario']);
        $("#asuntoModal").text(arr['asunto']);
        $("#msgModal").html( "<p style='white-space: pre-wrap'>"+arr['message']+"</p>");
    }

    function  DeleteCorreo(correo_id="") {

        if(correo_id==""){
            notificacion('Ocurrio un error de parametros consulte con soporte Tecnico', 'error');
            return false;
        }

        var url = $DOCUMENTO_URL_HTTP + '/application/system/pacientes/pacientes_admin/email_programar/controller.php';
        var parametros = {
            'accion':'deleteCorreo',
            'ajaxSend':'ajaxSend',
            'idcorreo':correo_id
        };

        $.get(url, parametros, function (data) {
            var arr = $.parseJSON(data);
            if(arr['error'] == ""){
                notificacion('Información Actualizada', 'success');
                var table2 = $('#CorreoListProgram').DataTable();
                table2.ajax.reload(null, false);
            }else{
                notificacion(arr['error'], 'error');
            }
        });

    }


    function  To_EstadoAnulado(correo_id="") {

        if(correo_id==""){
            notificacion('Ocurrio un error de parametros consulte con soporte Tecnico', 'error');
            return false;
        }

        var url = $DOCUMENTO_URL_HTTP + '/application/system/pacientes/pacientes_admin/email_programar/controller.php';
        var parametros = {
            'accion':'estadoAnular',
            'ajaxSend':'ajaxSend',
            'idcorreo':correo_id
        };

        $.get(url, parametros, function (data) {
            var arr = $.parseJSON(data);
            if(arr['error'] == ""){
                notificacion('Información Actualizada', 'success');
                var table2 = $('#CorreoListProgram').DataTable();
                table2.ajax.reload(null, false);
            }else{
                notificacion(arr['error'], 'error');
            }
        });

    }
    
</script>