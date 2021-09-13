

<div class="form-group col-md-12 col-xs-12">
    <label for="">LISTA DE COMPORTAMIENTOS</label>
    <ul class="list-inline" style="background-color: #f4f4f4; border-bottom: 0.6px solid #333333; padding: 3px; margin-left: 0px">
        <li><a href="#contentFilter" data-toggle="collapse" style="color: #333333" class="btnhover btn btn-sm " id="fitrar_document"> <b>  ▼ &nbsp;Filtrar <i></i> </b> </a></li>
    </ul>
</div>

<!-- contentFilter -->
<div class="form-group col-xs-12 col-md-12 col-lg-12 collapse " id="contentFilter" aria-expanded="true" style="margin-bottom: 0px;">
    <div class="col-md-12 col-xs-12 col-lg-12" style="background-color: #f4f4f4; padding-top: 15px">
        <div class="form-group col-md-12 col-xs-12 col-lg-12"> <h3 class="no-margin"><span>Filtrar Transacciones Clinicas
    </span></h3> </div>

        <div class="form-group col-xs-12 col-md-3 ">
            <label for="">Emitido</label>
            <div class="input-group form-group rango" style="margin: 0">
                <input type="text" class="form-control filtroFecha  " readonly="" id="Fn_emitido" value="">
                <span class="input-group-addon" style="border-radius: 0"><i class="fa fa-calendar"></i></span>
            </div>
        </div>

        <div class="form-group col-xs-12 col-md-3 ">
            <label for="">Tipo</label>
        </div>

        <div class="form-group col-xs-12 col-md-2 ">
            <label for="">Saldo</label>
            <input type="text" id="Fn_saldo" class="form-control">
        </div>

        <div class="form-group col-md-12 col-xs-12">
            <ul class="list-inline pull-right">
                <li>  <button class="limpiar btn   btn-block  btn-default" id="LimpiarFn" style="float: right; padding: 10px"> &nbsp; &nbsp; Limpiar &nbsp; &nbsp;</button> </li>
                <li>  <button class="aplicar btn   btn-block  btn-success" id="FiltrarFn" style="float: right; padding: 10px"> &nbsp;  &nbsp;Aplicar busqueda &nbsp;</button> </li>
            </ul>
        </div>

    </div>
</div>

<div class="form-group col-md-12 col-xs-12 " style="margin-top: 15px" >
    <div class="table-responsive">
        <table class="table table-condensed table-hover" id="transaccion_clinicas" width="100%">
            <thead>
                <tr style="background-color: #f4f4f4">
                    <th>Emitido</th>
                    <th>Cuenta</th>
                    <th>Operación</th>
                    <th>Descripción</th>
                    <th>Valor</th>
                </tr>
            </thead>
        </table>
    </div>
</div>


<script>

    function transsacionesClinicas(){
        var ElemmentoContentload = $("#transaccion_clinicas");
        boxTableLoad(ElemmentoContentload, true);
        var table = $('#transaccion_clinicas').DataTable({
            searching: false,
            "ordering":false,
            "serverSide": true,
            destroy:true,
            scrollX: false,
            lengthChange: false,
            fixedHeader: true,
            paging:true,
            processing: true,
            lengthMenu:[ 10 ],
            "ajax":{
                "url": $DOCUMENTO_URL_HTTP + '/application/system/operacion/transacciones_clinicas/controller/controller.php',
                "type":'POST',
                "data": {
                    'ajaxSend'   : 'ajaxSend',
                    'accion'     : 'list_transacciones_clinicas',
                    // 'doctor'               : $("#filtro_doctor").find(':selected').val(),
                },
                "dataType":'json',
                "cache": false,
                async: true,
                "complete": function(xhr, status) {
                    boxTableLoad(ElemmentoContentload, false);
                }
            },
            'createdRow':function(row, data, index){

                if( data[7] == 6){
                    $(row).css('backgroundColor','#EAFAF1');
                }

                //aplico style ancho a los hijos del primer nivel
                // console.log(            $(row).children().eq(1));
                $(row).children().eq(0).css('width','3%');
                $(row).children().eq(1).css('width','10%');
                $(row).children().eq(2).css('width','5%');
                $(row).children().eq(3).css('width','30%');
                $(row).children().eq(4).css('width','3%');

            },
            // columnDefs:[
            //     {
            //         targets:3,
            //         render:function (data, type, row, meta) {
            //             console.log(meta);
            //             return data;
            //         }
            //     }
            // ],
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


    window.onload =  boxloading($boxContenTransacciones, true);

    $(window).on("load", function () {
        transsacionesClinicas();

        boxloading($boxContenTransacciones, false, 1000);
    });

    $(document).ready(function () {

    });

</script>