<div id="FiltrarPagoPacientes" class="form-group col-xs-12 col-md-12 collapse" aria-expanded="true" style="margin-bottom: 0px;">
    <div class="form-group col-md-12 col-xs-12" style="background-color: #f4f4f4; padding: 25px; ">
        <h3 class=""><span>Filtrar Pagos de Pacientes</span></h3>
        <div class="row">
            <div class="form-group col-md-2 col-sm-12 col-xs-12">
                <label for="">Número</label>
                <input type="text" class="form-control" name="pagPrestacion" id="pagPrestacion">
            </div>
            <div class="form-group col-md-3 col-sm-12 col-xs-12">
                <label for="">Forma de Pago</label>
                <select name="formaPago" id="formaPago" class="form-control" style="width: 100%">
                    <option value=""></option>
                    <?php
                    $quy = "select rowid, nom from tab_bank_operacion where rowid not in(1,2,3,4,7)";
                    $result = $db->query($quy);
                    if($result&&$result->rowCount()>0){
                        while ($object = $result->fetchObject()){
                            print "<option value='".$object->rowid."'>".$object->nom."</option>";
                        }
                    }
                    ?>
                </select>
            </div>
            <div class="form-group col-md-7 col-sm-12 col-xs-12">
                <label for="">busqueda por Plan de Tratamiento</label>
                <select name="" class="form-control " id="busquedaxTratamiento" style="width: 100%">
                    <option value=""></option>
                </select>
            </div>
        </div>

        <div class="row">
            <div class="form-group col-md-3 col-sm-12 col-xs-12">
                <label for="">busqueda por N. Documento</label>
                <input type="text" class="form-control" name="n_x_documento" id="n_x_documento">
            </div>
        </div>

        <div class="row">
            <div class="form-group col-md-12 no-margin">
                <ul class="list-inline pull-right no-margin">
                    <li>  <button class="limpiar btn   btn-block  btn-default" style="float: right; padding: 10px"> &nbsp; &nbsp; Limpiar &nbsp; &nbsp;</button> </li>
                    <li>  <button class="aplicar btn   btn-block  btn-success" style="float: right; padding: 10px"> &nbsp;  &nbsp;Aplicar busqueda &nbsp;</button> </li>
                </ul>
            </div>
        </div>
    </div>
</div>


<div class="form-group col-md-12 col-xs-12 col-lg-12">
    <div class="table-responsive">
        <table id="pag_particular" class="table" width="100%" style="border-collapse: collapse">
            <thead style="background-color: #f4f4f4">
                <tr>
                    <th>&nbsp;</th>
                    <th>Fecha</th>
                    <th># Pago</th>
<!--                    <th># Plan de Tratamiento</th>-->
                    <th>Forma de Pago</th>
                    <th>Observación</th>
                    <th># Documento</th>
                    <th>Valor</th>
                    <th>&nbsp;</th>
                </tr>
            </thead>
        </table>
    </div>
</div>


<script>


    function PagosPacientes()
    {
        var n_pago         = $("#pagPrestacion").val();
        var n_x_documento  = $("#n_x_documento").val();
        var formaPago      = $("#formaPago").find(':selected').val();
        var id_tratamiento = $("#busquedaxTratamiento").find(':selected').val();

        var table = $('#pag_particular').DataTable({
            searching: false,
            processing: true,
            ordering:false,
            destroy:true,
            paging: true,
            serverSide:true,
            ajax:{
                url: $DOCUMENTO_URL_HTTP + '/application/system/pacientes/pacientes_admin/pagos_pacientes/controller_pagos/controller_pag.php',
                type:'POST',
                data:{
                    'ajaxSend'   :'ajaxSend',
                    'accion'     : 'list_pagos_particular',
                    'idpaciente' : $id_paciente,
                    'npago'      : n_pago,
                    'formapago'  : formaPago,
                    'plan_tratam'   : id_tratamiento,
                    'n_x_documento' : n_x_documento,
                },
                dataType:'json',
            },
            columnDefs:[
                {
                    targets:7,
                    render: function (data, type, row) {

                        // console.log(row);
                        var dropdown_menu = "<div class='dropdown col-centered col-xs-1 '>";
                        dropdown_menu += "<button class='btn btnhover  btn-xs dropdown-toggle' data-toggle=\"dropdown\" type='button' aria-expanded='false'><i class=\"fa fa-ellipsis-v\"></i></button>";
                            dropdown_menu += "<ul class='dropdown-menu pull-right' >";
                                dropdown_menu += "<li>" + row['url_imprimir'] + "</li>";
                                dropdown_menu += "<li><a href='#'> Enviar Email </a></li>";
                                dropdown_menu += "<li><a href='#detalleprestacionPagos' data-toggle='modal' data-nametratam='"+row['name_tratamiento']+"' data-idp='"+row['id_pagocab']+"' data-nboleta='"+row['n_boleta']+"' onclick='detalle_prestaciones_pagosParticulares("+row['id_pagocab']+", $(this))'  >Mostrar detalle</a></li>";
                                dropdown_menu += "<li><a href='#' data-nametratam='"+row['name_tratamiento']+"' data-valor='"+row['valor']+"' onclick='deletePagoPrestacion("+row['id_pagocab']+","+row['idPlantratamCab']+", $(this))' >Eliminar Pago</a></li>";
                            dropdown_menu += "</ul>";
                        dropdown_menu += "</div>";

                        if(row['boldPlanCab']!=1){
                            return dropdown_menu;
                        }else{
                            return "&nbsp;";
                        }
                    }
                }
            ],
            createdRow: function (row, data, dataIndex) {
                if(data['boldPlanCab'] == 1){
                    var objectTd =  $(row).children();
                    console.log($(row).children());
                    $.each(objectTd, function(i , item) {
                        $(item).attr("colspan", "7").css("background-color", "#f9f9f9");
                        console.log($(item));
                        if($(item).text()==""){
                            $(item).css("display", "none");
                        }else{
                            if(i>2){
                                $(item).html("<a class='btnhover btn-sm btn' onclick='PrintPagosParticulares("+(data['idPlantratamCab'])+")' style='font-weight: bolder'> <i class='fa fa-print'></i> PDF</a>");
                            }
                        }
                    });
                }else{
                    $("td:eq(0)", row).css("width", "1%").css("padding","6px 6px");
                    $("td:eq(1)", row).css("width", "3%").css("padding","6px 6px");
                    $("td:eq(2)", row).css("width", "20%").css("padding","6px 6px");
                    $("td:eq(3)", row).css("width", "10%").css("padding","6px 6px");
                    $("td:eq(4)", row).css("width", "35%").css("padding","6px 6px");
                    $("td:eq(5)", row).css("width", "15%").css("padding","6px 6px");
                    $("td:eq(6)", row).css("width", "5%").css("padding","6px 6px");
                    $("td:eq(7)", row).css("width", "5%").css("padding","6px 6px");
                }
            },
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

    function deletePagoPrestacion(idpagosCab, idPlantratamCab, Element)
    {
        if(idpagosCab!=0){

            var url = $DOCUMENTO_URL_HTTP + '/application/system/pacientes/pacientes_admin/pagos_pacientes/controller_pagos/controller_pag.php';
            $.get(url , {
                'accion'        : 'deletePagoPlantram',
                'ajaxSend'      : 'ajaxSend',
                'idpagocab'     : idpagosCab,
                'idpaciente'    : $id_paciente,
                'idPlantratam'  : idPlantratamCab,
                'numTratamiento': Element.prop('dataset').nametratam,
                'valor': Element.prop('dataset').valor,
            }, function (data) {
                var respuesta = $.parseJSON(data);
                if(respuesta['error'] == '') {
                    notificacion("Información Actualizada", "success");
                }else{

                }
            });

        }else{
            notificacion('Ocurrio un error', 'error');
        }
    }

    var PrintPagosParticulares = function(idTratamiento){

        if(!ModulePermission(24, 1)){
            notificacion('Ud. no tiene permiso para esta Operación', 'error');
            return false;
        }

        if(idTratamiento==0||idTratamiento==''){
            notificacion('Ocurrio un error de paramtros consulte con soporte', 'error');
            return false;
        }

        var parametros = '?idplantratamiento='+idTratamiento;
        var url = $DOCUMENTO_URL_HTTP+'/application/system/pacientes/pacientes_admin/pagos_recibidos/export/export_recaudaciones_realizadas.php'+parametros;
        window.open(url, '_blank');
    };


    $(".aplicar").click(function() {
        PagosPacientes();
    });
    $(".limpiar").click(function() {

        $("#pagPrestacion").val(null);
        $("#n_x_documento").val(null);
        $("#formaPago").val(null).trigger('change');
        $("#busquedaxTratamiento").val(null).trigger('change');

        PagosPacientes();

    });

    $('#busquedaxTratamiento').select2({
        placeholder:'buscar x Tratamiento',
        allowClear:true ,
        language: 'es'
    });
    $('#formaPago').select2({
        placeholder:'buscar x Forma de Pago',
        allowClear:true ,
        language: 'es'
    });

    $(document).ready(function () {

        $('#busquedaxTratamiento').select2({
            placeholder: 'buscar plan de tratamiento',
            allowClear:true,
            language: languageEs,
            minimumInputLength:1,
            ajax:{
                url: $DOCUMENTO_URL_HTTP + '/application/system/pacientes/pacientes_admin/controller/controller_adm_paciente.php',
                type: "POST",
                dataType: 'json',
                async:false,
                data:function (params) {
                    var query = {
                        accion: 'filtrarPlantratamientoSearchSelect2',
                        ajaxSend:'ajaxSend',
                        paciente_id: $id_paciente,
                        search: params.term,
                    };
                    return query;
                },
                delay: 500,
                processResults:function (data) {
                    return data;
                }
            }
        });

    });

    //window onload
    window.onload = boxloading($boxContentViewAdminPaciente ,true);

    $(window).on('load', function () {


        PagosPacientes();

        boxloading($boxContentViewAdminPaciente ,false, 1000);
    });

</script>