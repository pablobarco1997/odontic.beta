

<div class="form-group col-xs-12 col-sm-12 col-md-8 col-lg-4">
    <div class="info-box">
        <div class="info-box-icon bg-aqua" style="background-color: #212f3d!important;">
            <i class="fa fa-calendar" style="margin-top: 20px"></i>
        </div>
        <div class="info-box-content">
            <span class=""><span class="trunc"> <b>Citas Agendadas para Hoy </b></span></span>
            <span class="info-box-number" style="font-size: 2em" id="numCitas">0</span>
            <span> <b><?= date("Y/m/d")?></b> </span>
        </div>
    </div>
</div>

<div class="form-group col-md-12 col-lg-12">
    <ul class="list-inline">
        <li>
            <div class="callbox">
                <div class="form-group">
                    <div class="checkbox  ">
                        <a style="color: #333333" href="<?= DOL_HTTP .'/application/system/agenda/index.php?view=principal&list=diaria' ?>" class="btn btnhover ">
                            <b>Diaria</b>
                        </a>
                    </div>
                </div>
            </div>
        </li>

        <li>
            <div class="callbox">
                <div class="form-group disabled_link3">
                    <div class="checkbox  ">
                        <a style="color: #333333" href="<?= DOL_HTTP.'/application/system/agenda/index.php?view=principal&list=diariaglob'?>" class="btn btnhover ">
                            <b>Diaria Global</b>
                        </a>
                    </div>
                </div>
            </div>
        </li>

        <li>
            <div class="callbox">
                <div class="form-group">
                    <div class="checkbox <?= (!PermitsModule(2,2))?"disabled_link3":"" ?>">
                        <a href="<?= DOL_HTTP .'/application/system/agenda/index.php?view=agendadd'?>"  style="color: #333333" class="btn btnhover addCitas  ">
                            <b>Agendar una Cita </b>&nbsp;&nbsp;<i class="fa fa-calendar-check-o"></i>
                        </a>
                    </div>
                </div>
            </div>
        </li>

        <li>
            <div class="callbox">
                <div class="form-group">
                    <div class="checkbox ">
                        <label for="listcitasCanceladasEliminadas" class="btnhover hide">
                            <a style="color: #333333"  class="btn  ">
                                <input type="checkbox" id="listcitasCanceladasEliminadas">  MOSTRAR CITAS CANCELADAS O ELIMINADAS
                            </a>
                        </label>
                    </div>
                </div>
            </div>
        </li>

        <li>
            <div class="callbox">
                <div class="form-group">
                    <div class="checkbox  ">
                        <a href="#" class="btn btnhover" style="color: #333333" onclick="ImprimirCitasAgendadas()">
                            <b>Imprimir Citas Agendadas &nbsp;<i class="fa fa-print"></i></b>
                        </a>
                    </div>
                </div>
            </div>
        </li>

        <li>
            <div class="callbox">
                <div class="form-group">
                    <div class="checkbox  ">
                        <a href="#FiltrarAgenda" class="btn btnhover " style="color: #333333" data-toggle="collapse" >
                            <b>Filtrar Citas</b>
                        </a>
                    </div>
                </div>
            </div>
        </li>

    </ul>
</div>

<div id="FiltrarAgenda" class="collapse form-group col-xs-12 col-md-12">

    <div class="form-group col-md-12 col-xs-12"  style="background-color: #f4f4f4; padding: 25px">
        <h3 class="no-margin"><span>Filtrar Citas</span></h3>
        <div class="row">

            <div class="form-group col-md-3 col-sm-12 col-xs-12">
                <label for="">Fecha</label>
                <div class="input-group form-group rango" style="margin: 0">
                    <input type="text" class="form-control filtroFecha  " readonly id="startDate" value="" style="font-size: small">
                    <span class="input-group-addon" style="border-radius: 0"><i class="fa fa-calendar"></i></span>
                </div>
            </div>

            <div class="form-group col-md-3 col-sm-12 col-xs-12">
                <label for="">Doctor(a)</label>
                <select name="filtro_doctor" id="filtro_doctor" style="width: 100%" multiple class="filtrar_doctor form-control   " >
                    <?php
                        $sql = "SELECT rowid , nombre_doc , apellido_doc , if(estado = 'A' , 'Activo' , 'Inactivo') as iestado FROM tab_odontologos where estado in('A' , 'E') ;";
                        $rs = $db->query($sql);
                        if($rs->rowCount() > 0){
                            while ($obj = $rs->fetchObject()){
                                $nombOdontologos = $obj->nombre_doc ."  ". $obj->apellido_doc;
                                print "<option value='$obj->rowid'> ".(strtoupper($nombOdontologos))."  &nbsp;&nbsp;&nbsp;&nbsp;  ( <small> ".(strtolower($obj->iestado))."</small>  ) </option>";
                            }
                        }
                    ?>
                </select>
            </div>

            <div class="form-group col-md-4 col-sm-12 col-xs-12">
                <label for="">Estado de citas</label>
                <select name="" id="filtroEstados"  style="width: 100%" class="form-control  filtrar_estados " multiple>
                    <?php
                        $sql = "SELECT * FROM tab_pacientes_estado_citas;";
                        $rs = $db->query($sql);
                        if($rs->rowCount() > 0){
                            while ($obj = $rs->fetchObject()){
                                echo "<option value='$obj->rowid' >$obj->text</option>";
                            }
                        }
                    ?>
                </select>
            </div>

            <div class=" form-group col-md-2 col-sm-12 col-xs-12">
                <label for="">buscar N. Cita</label>
                <input type="text" class="form-control" id="n_citasPacientes" style="width: 100%" placeholder="Ingrese numero de cita ">
            </div>
        </div>

        <div class="row">
            <div class=" form-group col-md-6 col-xs-12 ">
                <label for="">buscar Pacientes</label>
                <select name="buscarxPaciente" id="buscarxPaciente" style="width: 100%" multiple class="form-control  buscarxPaciente "></select>
            </div>
        </div>

        <div class="row">
            <div class="form-group col-md-12 ">
                <ul class="list-inline pull-right">
                    <li>  <button class="btn   btn-block  btn-default" onclick="ImprimirCitasAgendadas(true)" style="float: right; padding: 10px" > &nbsp; &nbsp; <small style="font-weight: bold"> Imprimir por rango de Filtro <i class="fa fa-print"></i> </small> &nbsp; &nbsp;</button> </li>
                    <li>  <button class="limpiar btn   btn-block  btn-default" style="float: right; padding: 10px" > &nbsp; &nbsp; Limpiar &nbsp; &nbsp;</button> </li>
                    <li>  <button class="aplicar btn   btn-block  btn-success" style="float: right; padding: 10px" > &nbsp;  &nbsp;Aplicar busqueda &nbsp;</button> </li>
                </ul>
            </div>
        </div>
    </div>

</div>

<style>

    .fixedHeader-floating{
        top: -4px!important;
    }
</style>

<div class="form-group col-md-12 col-xs-12 col-sm-12 col-lg-12" >
    <div class="table-responsive" style="width: 100%; ">
        <table class="table tableAgenda compact" id="tableAgenda" width="100%" >
            <thead  style="background-color: #f4f4f4; border-bottom: 1px solid #333333" id="headAgendaDiaria">
                <tr id="cabezeraListAgenda" class="cabezeraListAgenda " >
                    <th class="text-left " width="3%">
                        <input type="checkbox" id="checkeAllCitas" >
                        <label for="checkeAllCitas"></label>
                    </th>
                    <th class="text-center " width="8%">N.- Citas</th>
                    <th class="text-center " width="10%">Hora</th>
                    <th class="text-center " width="23%">Paciente</th>
                    <th class="text-center " width="23%">Doctor(a)</th>
                    <th class="text-center " width="15%">Estado de Citas</th>
<!--                    <th class="text-center " width="10%">Situación</th>-->
                </tr>
            </thead>
        </table>
        <br>
        <br>
    </div>
</div>

<style>
    /*#status_add{*/
        /*font-size: 1.3rem;*/
    /*}*/
</style>

<!-- Modal Agregar Nuevo Estado -->
<div class="modal fade" id="addStatusCitas" role="dialog" data-backdrop="static" >
    <div class="modal-dialog">

        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><span>Crear Nuevo Estado</span></h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="form-group col-xs-12 col-md-12">
                        <span style=" color: #eb9627; font-weight: bold">
                             <i class="fa fa-info-circle"></i>
                            Si requiere la creación de Estado con comportamientos adicionales Consulte con soporte para más información
                        </span>
                    </div>
                    <div class="form-group col-xs-12 col-md-12">
                        <label for="">Ingrese un nombre para el Estado de la Cita</label>
                        <input type="text" class="form-control" id="new_estados_citas">
                    </div>
                    <div class="form-group col-xs-12 col-md-12">
                        <label for="">Selecione un color para el nuevo Estado de la cita</label>
                        <div class="input-group new_estados_citas_color">
                            <input type="text" class="form-control" id="new_estados_citas_color" readonly>
                            <div class="input-group-addon"><i></i></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default"  data-dismiss="modal" id="showModalEstados" >Mostrar Estados Agregados</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-success" id="nuevoStatus">Guardar</button>
            </div>
        </div>

    </div>
</div>

<!--lista estados asignados-->
<div class="modal fade" id="listStatusModal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><span>Lista de Estados Agregados</span></h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="form-group col-xs-12 col-md-12">
                        <span style=" color: #eb9627">
                        <i class="fa fa-info-circle"></i>
                            Tener en cuenta que solo puede <b>Eliminar el Estado</b> si este no está Asociado a una cita Agendada
                        </span>
                    </div>
                    <div class="form-group col-xs-12 col-md-12">
                        <div class="table-responsive" style="width: 100%">
                            <table id="status_add_list2" class="table" style="border-collapse: collapse;" width="100%">
                                <thead>
                                    <tr>
                                        <th>name</th>
<!--                                        <th>comment</th>-->
                                        <th>color</th>
                                        <th>&nbsp;</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<!--modales en la vista principal lista de agenda ( LISTA DIARIA )-->
<?php
    include_once DOL_DOCUMENT .'/application/system/agenda/view/status_modal.php';
?>

<script>
    //color picker with addon
    $(".new_estados_citas_color").colorpicker();

    var formValidCrearEstado = function(){
        var Errores = [];
        var nomstatus = $('#new_estados_citas');
        var color = $('#new_estados_citas_color');
        if(nomstatus.val() == "" || (!/^\s/.test(nomstatus.val())) == false){
            Errores.push({
                "documento" :   nomstatus,
                "mesg" :  "Campo Obligatorio",
            });
        }if(color.val() == "" || (!/^\s/.test(color.val())) == false){
            Errores.push({
                "documento" :   nomstatus,
                "mesg" :  "Debe selecionar un color para el estado",
            });
        }

        var valid = true;
        $(".error_perfil").remove();
        if(Errores.length>0){
            for (var i=0; i<=Errores.length-1;i++ ){
                var menssage =  document.createElement("small");
                menssage.setAttribute("style","display: block; color:blue;");
                menssage.setAttribute("class","error_perfil");
                menssage.appendChild(document.createTextNode(Errores[i]['mesg']));
                var documentoDol        = Errores[i]['documento'];
                if(documentoDol.attr("id")=="perf_passd"){
                    $(menssage).insertAfter(documentoDol.parent('.input-group'))
                }else{
                    $(menssage).insertAfter(documentoDol);
                }
            }
            valid = false;
        }else{
            valid = true;
        }
        return valid;
    };

    /*Crear nuevo Estado*/
    $("#nuevoStatus").on("click", function() {

        if(!ModulePermission(2,2)){
            notificacion('Ud. No tiene permiso para Crear','question');
            return false;
        }

        if(!formValidCrearEstado())
            return false;

        $.ajax({
            url: $DOCUMENTO_URL_HTTP + "/application/system/agenda/controller/agenda_controller.php",
            type:'POST',
            dataType:'json',
            data:{
                "ajaxSend":"ajaxSend",
                "accion":"addnewSatusCitas",
                "statusCitas":$('#new_estados_citas').val(),
                "colorSatus":$('#new_estados_citas_color').val(),
            },
            async:false,
            success:function(respuesta){
                if(respuesta['error']==""){
                    notificacion('Información Actualizada', 'success');
                    var table = $('#tableAgenda').DataTable();
                    table.ajax.reload(null, false);
                    $("#addStatusCitas").modal("hide");
                }else{
                    notificacion(respuesta['error'], 'error');
                }
            }
        });

    });


    var fetchSatatus = function() {

        $('#status_add_list2').DataTable({
            "searching": false,
            "destroy":true,
            "ordering":false,
            "paging":true,
            "lengthMenu":[ 5, 10, 25 ],
            "ajax":{
                "url": $DOCUMENTO_URL_HTTP + "/application/system/agenda/controller/agenda_controller.php",
                "type":'POST',
                "data": {
                    "ajaxSend"             : 'ajaxSend',
                    "accion"               : 'statusList2',
                },
                "dataType":'json',
            },
            "createdRow": function ( row, data, dataIndex ) {

                $(row).find('td:eq(1)').attr('align','center');
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

    var EliminarStatus = function(id){
        var url = $DOCUMENTO_URL_HTTP + "/application/system/agenda/controller/agenda_controller.php";
        $.get(url, {
            "ajaxSend" : "ajaxSend",
            "accion"   : "EliminaStatus",
            "id": id
        }, function(data) {
            var e = $.parseJSON(data);
            if(e.error != ''){
                notificacion(e.error, 'error');
            }else{
                var table =  $('#tableAgenda').DataTable();
                table.ajax.reload( null, false );
                var table2 = $('#status_add_list2').DataTable();
                table2.ajax.reload( null, false );
                notificacion('Información Actualizado', 'success');
            }
        });
    };


    $("#listStatusModal").on("shown.bs.modal", function() {
        fetchSatatus();
        $("#new_estados_citas").val(null).trigger("keyup");
        $("#new_estados_citas_color").val(null).trigger("keyup");
    });


    //funcionalidad  Programar Email (opcional)
    $("#emailConfirmacion_programar").change(function () {

        if($('#emailConfirmacion_programar').is(':checked')) {
            notificacion('<b>Programar email de confirmación del Paciente</b><br> Este sera enviado dependiendo de la fecha seleccionada <br> Puede ver el estado del e-mail programado en el módulo <b>E-mail Asociados</b> a dicho paciente ', 'question');
            $("#date_programa_email_confirm").removeClass('disabled_link3').text(null).parent('div').removeClass('disabled_link3').attr('disabled', false);

            var Dateadd = new Date();
            var DateElement =  $("#date_programa_email_confirm").daterangepicker({
                drops: 'top',
                minDate : new Date(Dateadd.getFullYear(), Dateadd.getMonth(), Dateadd.getDate() ),
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
                singleDatePicker: true,
                showDropdowns: true,
                autoclose: true,
                pickerPosition: "bottom-left"
            });

        }else{
            $("#date_programa_email_confirm").val(null).parent('div').addClass('disabled_link3').attr('disabled', true);
        }
    });

    //Mostrar list Data de estados de citas agendadas
    $("#showModalEstados").click('click', function () {
        $("#addStatusCitas").on('hide.bs.modal', function () {
            setTimeout(function () {
                $("#listStatusModal").modal("show");
            },700);
        });
    });

    window.addEventListener('load', function() {

    });


</script>

<script src="<?= DOL_HTTP ?>/application/system/agenda/js/agent.js"></script>
