

<div class="form-group col-md-12 col-xs-12">
    <label for="">LISTA DE COMPORTAMIENTOS</label>
    <ul class="list-inline" style="background-color: #f4f4f4; border-bottom: 0.6px solid #333333; padding: 3px; margin-left: 0px">
        <li><a href="#contentFilter" data-toggle="collapse" style="color: #333333" class="btnhover btn btn-sm " id="fitrar_document"> <b>  ▼ &nbsp;Filtrar <i></i> </b> </a></li>
<!--        <li><a href="#" style="color: #333333" class="btnhover btn btn-sm " id="crearTransaccion" onclick="to_crear_trans()"> <b> Crear Transaccion <i></i> </b> </a></li>-->
        <li><a id="refresh_list_transsa" class="btn btn-sm" style="color: black" title="refresh" onclick="transsacionesClinicas($(this))">
                <span class="fa fa-refresh"></span>
            </a>
        </li>
    </ul>
</div>

<!-- contentFilter -->
<div class="form-group col-xs-12 col-md-12 col-lg-12 collapse " id="contentFilter" aria-expanded="true" style="margin-bottom: 0px;">
    <div class="col-md-12 col-xs-12 col-lg-12" style="background-color: #f4f4f4; padding-top: 15px">
        <div class="form-group col-md-12 col-xs-12 col-lg-12"> <h3 class="no-margin"><span>Filtrar Transacciones Clinicas
    </span></h3> </div>

        <div class="form-group col-xs-12 col-md-4 ">
            <label for="">Emitido</label>
            <div class="input-group form-group rango" style="margin: 0">
                <input type="text" class="form-control " readonly="" id="emitido_tc" name="emitido_tc" value="">
                <span class="input-group-addon" style="border-radius: 0"><i class="fa fa-calendar"></i></span>
            </div>
        </div>

        <div class="form-group col-xs-12 col-md-3 ">
            <label for="">Forma de Pago/Cobro</label>
            <select name="formapgs_tc" id="formapgs_tc" class="form-control" style="width: 100%">
                <option value=""></option>
                <?php
                    $resultp = $db->query("select * from tab_bank_operacion");
                    if($resultp){
                        if($resultp->rowCount()>0){
                            $formas = $resultp->fetchAll(PDO::FETCH_ASSOC);
                            foreach ($formas as $value){
                                print "<option value='".$value['rowid']."'>".$value['nom']."</option>";
                            }
                        }
                    }
                ?>
            </select>
        </div>

        <div class="form-group col-xs-12 col-md-2 ">
            <label for="">Valor</label>
            <input type="text" id="valor_tc" name="valor_tc" class="form-control">
        </div>

        <div class="form-group col-xs-12 col-md-3">
            <label for="">Cuenta</label>
            <select name="cuenta_tc" id="cuenta_tc" style="width: 100%" class="form-control">
                <option value=""></option>
                <?php
                    $resultp = $db->query("select rowid ,  concat(n_cuenta,' ', name_acount) nom from tab_ope_declare_cuentas where estado = 'A';");
                    if($resultp){
                        if($resultp->rowCount()>0){
                            $formas = $resultp->fetchAll(PDO::FETCH_ASSOC);
                            foreach ($formas as $value){
                                print "<option value='".$value['rowid']."'>".$value['nom']."</option>";
                            }
                        }
                    }
                ?>
            </select>
        </div>

        <div class="form-group col-xs-12 col-md-6 ">
            <label for="">Descripción</label>
            <input type="text" id="desc" name="desc" class="form-control input-sm">
        </div>



        <div class="form-group col-md-12 col-xs-12">
            <ul class="list-inline pull-right">
                <li>  <button class="limpiar btn   btn-block  btn-default" id="limpiar_tc" onclick="applicarBusq($(this))" style="float: right; padding: 10px"> &nbsp; &nbsp; Limpiar &nbsp; &nbsp;</button> </li>
                <li>  <button class="aplicar btn   btn-block  btn-success" id="" onclick="applicarBusq($(this))" style="float: right; padding: 10px"> &nbsp;  &nbsp;Aplicar busqueda &nbsp;</button> </li>
            </ul>
        </div>

    </div>
</div>

<div class="form-group col-md-12 col-xs-12 " style="margin-top: 15px" >
    <div class="table-responsive">
        <table class="table table-condensed table-hover" id="transaccion_clinicas" width="100%">
            <thead>
                <tr style="background-color: #f4f4f4">
                    <th width="5%">Emitido</th>
                    <th width="20%">Cuenta</th>
<!--                    <th>Operación</th>-->
                    <th width="60%">Descripción</th>
                    <th style="width: 10%">Valor</th>
                </tr>
            </thead>
        </table>
    </div>
</div>


<script>


    function transsacionesClinicas(Element = false){
        var ElemmentoContentload = $("#transaccion_clinicas");

        var parametrs = {
            ajaxSend   : 'ajaxSend',
            accion     : 'list_transacciones_clinicas',
            datecc     : $('#emitido_tc').val(),
            formp      : $('#formapgs_tc').find(':selected').val(),
            valor_tc   : $('#valor_tc').val(),
            desc       : $('#desc').val(),
            cuenta_tc  : $('#cuenta_tc').find(':selected').val(),
        };

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
                "data": parametrs,
                "dataType":'json',
                "cache": false,
                async: true,
                "beforeSend": function () {
                    boxTableLoad(ElemmentoContentload, true);
                    if(Element!=false) {
                        console.log(Element);
                        Element.find('span').addClass('btnSpinner');
                    }
                },
                "complete": function(xhr, status) {
                    boxTableLoad(ElemmentoContentload, false);
                    if(Element!=false){
                        Element.find('span').removeClass('btnSpinner');
                    }
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
                $(row).children().eq(2).css('width','40%');
                $(row).children().eq(3).css('width','5%');

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


    function applicarBusq(Element){
        var form = $("#contentFilter");
        if(Element.attr('id')=='limpiar_tc'){
            form.find('input, select').val(null).trigger('change');
        }else{
           //aplicar busqueda
        }

        transsacionesClinicas();
    }


    function to_crear_trans(){

        var objectLoad = {
            onload:function () {
                boxloading($boxContenTransacciones, true);
            },
            offload: function () {
                boxloading($boxContenTransacciones, false, 1000);
            }
        };

        if(!ModulePermission('Transacciones Clinicas','agregar', objectLoad)){
            notificacion('Ud. No tiene permiso para realizar esta Operación','error');
            return false;
        }

        window.location = $DOCUMENTO_URL_HTTP+"/application/system/operacion/transacciones_clinicas?view=crear_transaccion&key="+$keyGlobal+"&crear=on";

    }

    window.onload =  boxloading($boxContenTransacciones, true);

    $(window).on("load", function () {
        transsacionesClinicas();

        boxloading($boxContenTransacciones, false, 1000);
    });

    $(document).ready(function () {


        $("select").select2({
            placeholder: 'Seleccione una opción',
            allowClear: true,
            language: languageEs
        });

        $('#emitido_tc').daterangepicker({

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

            startDate: moment().startOf('month'),
            endDate: moment().endOf('month'),
            ranges: {
                'Hoy': [moment(), moment()],
                'Ayer': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                'Últimos 7 Dias': [moment().subtract(6, 'days'), moment()],
                'Últimos 30 Dias': [moment().subtract(29, 'days'), moment()],
                'Mes Actual': [moment().startOf('month'), moment().endOf('month')],
                'Mes Pasado': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
                'Año Actual': [moment().startOf('year'), moment().endOf('year')],
                'Año Pasado': [moment().subtract(1, 'year').startOf('year'), moment().subtract(1, 'year').endOf('year')],
            }
        }).val(null);

        $('.rango span').click(function() {
            $(this).parent().find('input').click();
        });

    });

</script>