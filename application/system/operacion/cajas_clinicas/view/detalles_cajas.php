<?php

#breadcrumbs  -----------------------------------------------
$url_breadcrumbs = $_SERVER['REQUEST_URI'];
$titulo = "Detalles de caja";
$modulo = false;


$id_ope_caja = GETPOST('idcaj');

if($id_ope_caja == ""){
    echo 'ocurrio un error con los parametros de entrada consulte soporte ';
    die();
}
if($id_ope_caja == 0){
    echo 'ocurrio un error con los parametros de entrada consulte soporte ';
    die();
}

if((int)$id_ope_caja>0){

    $q = "SELECT 
    dc.n_cuenta , 
    dc.to_caja_direccion ,
    dc.name_acount,
    c.estado, 
    c.date_apertura, 
    c.date_cierre, 
    c.saldo_inicial , 
    
    (select s.usuario from tab_login_users s where s.rowid = c.id_user_caja) as nom_usu,
     CASE
			WHEN c.estado = 'A' THEN 'Caja abierta'
			WHEN c.estado = 'C' THEN 'Caja Cerrada'
			WHEN c.estado = 'E' THEN 'Caja Eliminada'
		ELSE 'Caja no asignada' 
	END as estado_caja 
FROM
    tab_ope_cajas_clinicas c
      inner join
    tab_ope_declare_cuentas dc on dc.rowid = c.id_caja_cuenta
    where c.rowid = $id_ope_caja
    limit 1";
    $result = $db->query($q);
    if($result && $result->rowCount()>0){
        $result = $result->fetchObject();
    }

    $direccion_caja  = $result->to_caja_direccion;
    $numero_caja     = $result->n_cuenta;
    $nom_usu         = $result->nom_usu;
    $cajaEstado      = $result->estado_caja;
    $st              = $result->estado;
    $date_apertura   = date("Y/m/d H:m:s", strtotime($result->date_apertura));
    $saldo_inicial   = round((double)$result->saldo_inicial, 2);

    if($result->date_cierre != "")
        $date_cierre   = date("Y/m/d H:m:s", strtotime($result->date_cierre));
    else
        $date_cierre = "";

}

?>


<div class="form-group col-xs-12 col-md-12 col-lg-12 no-padding">
    <div  class="form-group col-md-12 col-xs-12">
        <?= Breadcrumbs_Mod($titulo, $url_breadcrumbs, $modulo); ?>
    </div>

    <div class="form-group col-md-12 col-xs-12">
        <span style="font-weight: bolder; font-size: 2rem; display: block">Detalles de Caja # <?= $numero_caja?></span>
        <table class="table">
            <tr>
                <td>
                    <b>Dirección:</b> <?= $direccion_caja ?>
                </td>
            </tr>
            <tr>
                <td>
                    <b>Usuario asociado:</b> <?= $nom_usu ?>
                </td>
            </tr>
            <tr>
                <td style=""></td>
            </tr>
        </table>

        <?php
            $cajaEstado  = (strtoupper($cajaEstado));
            if($st=='A'){
                print ' <label for="#" style="display: block;padding: 5px; background-color: #D5F5E3; color: green; font-weight: bolder">'.$cajaEstado.'</label>';
            }
            if($st=='C'){
                print ' <label for="#" style="display: block;padding: 5px; background-color: #fadbd8; color: red; font-weight: bolder; ">'.$cajaEstado.'</label>';
            }
            if($st=='E'){
                print ' <label for="#" style="display: block;padding: 5px; background-color: #fadbd8; color: red; font-weight: bolder; ">'.$cajaEstado.'</label>';
            }
        ?>
    </div>

    <style>

        .table_info_caja tr td{
            font-size: 1.5rem;
        }

    </style>

    <div class="form-group col-xs-12 col-md-12">
        <div class="table-responsive">
            <table class="table table_info_caja table-hover" id="" style="width: 100%; margin: 0 auto;">
                <tr>
                    <td style="font-weight: bolder">Fecha de apertura</td>
                    <td style="text-align: right"><?= $date_apertura;  ?></td>
                </tr>
                <tr>
                    <td style="font-weight: bolder">Fecha de Cierre</td>
                    <td style="text-align: right"><?= $date_cierre;  ?></td>
                </tr>
                <tr>
                    <td style="font-weight: bolder">Saldo Inicial</td>
                    <td style="text-align: right"><?= number_format($saldo_inicial, 2, '.',''); ?></td>
                </tr>
                <tr>
                    <td style="font-weight: bolder">Efectivo</td>
                    <td style="text-align: right" id="valor_caja_efectivo">0.00</td>
                </tr>
                <tr>
                    <td style="font-weight: bolder">Recaudado</td>
                    <td style="text-align: right" id="valor_caja_recaudado">0.00</td>
                </tr>
                <tr style="background-color: #fadbd8">
                    <td style="font-weight: bolder">Gastos(-)</td>
                    <td style="text-align: right; color: red; font-weight: bolder" id="valor_caja_gastos">0.00</td>
                </tr>
                <tr>
                    <td style="font-weight: bolder">Total</td>
                    <td style="text-align: right;">
                        <span style="text-align: right; color: #008000; font-weight: bolder"  id="valor_caja_total" >0.00</span>
                    </td>
                </tr>
            </table>
        </div>
    </div>

    <div class="form-group col-xs-12 col-md-12">
        <label for="#" style="display: block;padding: 5px; background-color: #D5F5E3; color: green; font-weight: bolder">TRASACCIONES DE CAJA <?= $numero_caja ?></label>
        <div class="table-responsive">
            <table class="table table-hover" id="recaudacion_caja_plantratamiento" style="width: 100%">
                <thead >
                    <tr>
                        <th colspan="6">RECAUDACIONES DE PLANES DE TRATAMIENTO DE LOS PACIENTES</th>
                    </tr>
                    <tr style="background-color: #f4f4f4">
                        <th>Emitido pago</th>
                        <th>Paciente</th>
                        <th>Plan de tratamiento</th>
                        <th>Prestación Servicios</th>
                        <th>Medio de Pago</th>
                        <th>monto</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>


    <div class="form-group col-xs-12 col-md-12">
        <label for="#" style="display: block;padding: 5px; background-color: #fadbd8; color: red; font-weight: bolder">GASTOS CLINICOS DE CAJA <?= $numero_caja ?></label>
        <div class="table-responsive">
            <table class="table table-hover" id="gastos_caja_list" style="width: 100%">
                <thead >
                <tr>
                    <th colspan="6">GASTOS CLINICOS DE CAJA</th>
                </tr>
                <tr style="background-color: #f4f4f4">
                    <th>Emitido</th>
                    <th>Categoria</th>
                    <th>Detalle</th>
                    <th>Fecha Factura</th>
                    <th>Medio de Pago</th>
                    <th>monto</th>
                </tr>
                </thead>
            </table>
        </div>
    </div>

</div>


<script>

    function gastos_list() {
        var ElemmentoContentload = $("#gastos_caja_list");
        boxTableLoad(ElemmentoContentload, true);
        var table = $("#gastos_caja_list").DataTable({
            searching: false,
            "ordering":false,
            "serverSide": true,
            destroy:true,
            scrollX: false,
            // scrollY: 500,
            lengthChange: false,
            fixedHeader: false,
            paging:true,
            processing: true,
            lengthMenu:[ 10 ],
            ajax:{
                url: $DOCUMENTO_URL_HTTP + '/application/system/operacion/cajas_clinicas/controller/caja_controller.php',
                type:'POST',
                cache:false,
                async:true,
                data:{
                    'ajaxSend':'ajaxSend',
                    'accion':'gastos_list_caja',
                    'id_ope_caja'   : "<?= $id_ope_caja ?>",
                },
                dataType:'json',
                "complete": function(xhr, status) {
                    boxTableLoad(ElemmentoContentload, false);
                }
            },
            createdRow:function(row, data, index){

                $(row).children().eq(0).css('width','10%');
                $(row).children().eq(1).css('width','15%');
                $(row).children().eq(2).css('width','15%');
                $(row).children().eq(3).css('width','10%');
                $(row).children().eq(4).css('width','10%');
                $(row).children().eq(5).css('width','5%');

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
            "infoCallback": function (settings, start, end, max, total, pre){

                return "Mostrando registros del "+ start +" al "+ end +"<br>de un total de "+total+ " registros.";
            }
        });

    }

    function recaudacion_planes_tratamiento() {
        var ElemmentoContentload = $("#recaudacion_caja_plantratamiento");
        boxTableLoad(ElemmentoContentload, true);
        var table = $("#recaudacion_caja_plantratamiento").DataTable({
            searching: false,
            "ordering":false,
            "serverSide": true,
            destroy:true,
            scrollX: false,
            // scrollY: 500,
            lengthChange: false,
            fixedHeader: false,
            paging:true,
            processing: true,
            lengthMenu:[ 10 ],
            ajax:{
                url: $DOCUMENTO_URL_HTTP + '/application/system/operacion/cajas_clinicas/controller/caja_controller.php',
                type:'POST',
                cache:false,
                async:true,
                data:{
                    'ajaxSend':'ajaxSend',
                    'accion':'recaudacion_planestratamiento',
                    'id_ope_caja'   : "<?= $id_ope_caja ?>",
                    'date_apertura' : "<?= $date_apertura ?>"
                },
                dataType:'json',
                "complete": function(xhr, status) {
                    boxTableLoad(ElemmentoContentload, false);
                }
            },
            createdRow:function(row, data, index){

                $(row).children().eq(0).css('width','10%');
                $(row).children().eq(1).css('width','15%');
                $(row).children().eq(2).css('width','15%');
                $(row).children().eq(3).css('width','10%');
                $(row).children().eq(4).css('width','10%');
                $(row).children().eq(5).css('width','5%');

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
            "infoCallback": function (settings, start, end, max, total, pre){

                return "Mostrando registros del "+ start +" al "+ end +"<br>de un total de "+total+ " registros.";
            }
        });

    }

    function recursos_caja(){

        var params = {
            'accion'        : 'fetch_recursos_caja',
            'ajaxSend'      : 'ajaxSend',
            'id_ope_caja'   : "<?= $id_ope_caja ?>",
            'date_apertura' : "<?= $date_apertura ?>"
        };

        $.ajax({
            url: $DOCUMENTO_URL_HTTP + '/application/system/operacion/cajas_clinicas/controller/caja_controller.php',
            type:'POST',
            delay: 1000,
            async:true,
            cache:false,
            data: params,
            dataType:'json',
            complete: function(xhr, status){

            },
            success: function (response) {

                var Gastos_caja         = response['Gastos_caja'];

                var Saldo_caja_efectivo = response['Saldo_caja_efectivo'];
                var Recaudado_caja      = response['Recaudado_caja'];
                var saldoInicial        = "<?= $saldo_inicial ?>";

                var totalCaja = parseFloat(Recaudado_caja) + parseFloat(saldoInicial);
                totalCaja -= Gastos_caja;

                $("#valor_caja_gastos").text(Gastos_caja);
                $("#valor_caja_efectivo").text(Saldo_caja_efectivo);
                $("#valor_caja_recaudado").text(Recaudado_caja);

                if(totalCaja>0){ //positivo
                    $("#valor_caja_total")
                        .css('color', '#228000')
                        .text(parseFloat(totalCaja).toFixed(2));
                }else{ //negativo
                    $("#valor_caja_total")
                        .css('color', '#ff0014')
                        .text(parseFloat(totalCaja).toFixed(2));
                }
            }
        });
    }


    window.onload =  boxloading($boxContentCajasClinicas, true);

    $(window).on("load", function () {

        boxloading($boxContentCajasClinicas, true, 1000);
        recursos_caja();

        setTimeout(function () {
            recaudacion_planes_tratamiento();
            gastos_list();
        }, 1000);
    });

</script>