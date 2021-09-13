

<?php


global $db, $user;

$showCaja = ConsultarCajaUsers($user->id);

if($showCaja['error']!=""){
    $showCaja = "Este usuario no tiene asociada una caja <br> <b>No puede realizar esta Operación</b>";
}else{
    $showCaja="1";
}


//LISTA PRINCIPAL DE PAGOS DE PACIENTES
if(isset($_GET['v']) && $_GET['v'] == 'paym')
{

?>

    <div class="form-group col-xs-12 col-md-12 col-lg-12 no-margin collapse " id="contentFilter" aria-expanded="true" style="">
        <div class="form-group col-md-12 col-xs-12 col-lg-12" style="background-color: #f4f4f4; padding-top: 15px">

            <div class="form-group col-md-12 col-xs-12 col-lg-12"> <h3 class="no-margin"><span>Filtrar Pagos de Pacientes</span></h3> </div>

            <div class="form-group col-md-3 col-xs-12">
                <label>Emitido</label>
                <div class="input-group form-group rango" style="margin: 0">
                    <input type="text" class="form-control filtroFecha  " readonly="" id="startDatePagosxPacien" value="">
                    <span class="input-group-addon" style="border-radius: 0"><i class="fa fa-calendar"></i></span>
                </div>
            </div>

            <div class="form-group col-md-7 col-xs-12">
                <label>Plan de Tratamiento</label>
                <select name="tratamientoPagosxPacien" id="tratamientoPagosxPacien" class="form-control" style="width: 100%"></select>
            </div>

            <div class="form-group col-md-2 col-xs-12" style="margin-bottom: 0px;margin-top: 15px;">
                <span style="display: block"><input type="radio" name="abonadoRPagosxPacien"  id="abonadoPagosxPacien"> Abonado</span>
                <span style="display: block"><input type="radio" name="abonadoRPagosxPacien"  id="realizadoPagosxPacien"> Realizado</span>
            </div>

            <div class="form-group col-md-12 col-xs-12">
                <ul class="list-inline pull-right">
                    <li>  <button class=" btn   btn-block  btn-default" id="limpiarPagosxPacien" style="float: right; padding: 10px"> &nbsp; &nbsp; Limpiar &nbsp; &nbsp;</button> </li>
                    <li>  <button class=" btn   btn-block  btn-success" id="aplicarPagosxPacien" style="float: right; padding: 10px"> &nbsp;  &nbsp;Aplicar busqueda &nbsp;</button> </li>
                </ul>
            </div>
        </div>
    </div>

        <script>

            $(window).on("load", function () {

                if("<?= $showCaja ?>" != "1"){
                    notificacion("<?= $showCaja ?>", "question");
                }


                //busqueda pagos x pacientes
                $("#limpiarPagosxPacien").click(function () {

                    $("[name='abonadoRPagosxPacien']").prop('checked', false);
                    $("[name='tratamientoPagosxPacien']").val(null).trigger('change');
                    $("#startDatePagosxPacien").val(null);

                    listPagosIndependientes();
                });
                $("#aplicarPagosxPacien").click(function () {

                    listPagosIndependientes();
                });

            });
        </script>

        <div class="form-group col-xs-12 col-md-12" style="margin-bottom: 25px">
            <div class="row">
                <div class="col-md-12 col-xs-12">
                    <div class="table-responsive">
                        <table class="table table-striped" id="pagos_planestratamiento_list" width="100%">
                            <thead>
                            <tr>
                                <th colspan="6" style="background-color: #f4f4f4 ">PAGOS POR PLANES DE TRATAMIENTO DE PACIENTES</th>
                            </tr>
                            <tr>
                                <th width="5%">Cobrar</th>
                                <th width="8%">Fecha</th>
                                <th width="25%">Plan de Tratamiento</th>
        <!--                        <th width="15%">Cita Asociada</th>-->
                                <th width="15%">$&nbsp;Total</th>
                                <th width="15%">$&nbsp;Realizado</th>
                                <th width="15%">$&nbsp;Abono de Paciente</th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>

<?php

}

?>


<?php

# lista de prestaciones de este plan de tratamiento cobros a realizar
/**
 * muestra todas las prestaciones del plan de tratamiento a Pagar
*/

 if(isset($_GET['v']) && $_GET['v'] == 'paym_pay')
 {

     $query = "Select concat('Plan de Tratamiento N. ', numero) as numplantram 
               From tab_plan_tratamiento_cab where rowid =". ((isset($_GET['idplantram']))?$_GET['idplantram']:0) ."   ";
     $n_plantram = $db->query($query)->fetchObject()->numplantram;


 ?>


     <div class="form-group col-xs-12 col-md-12 col-sm-12">
         <div class="table-responsive">
             <table class="table-condensed table" id="ApagarlistPlantratmm" width="100%" >
                 <thead >
                     <tr>
                         <th colspan="6">LISTA DE PRESTACIONES  <span style="color: #0866a5"> <?= strtoupper($n_plantram) ?> </span> </th>
                         <th style="text-align: right; padding: 5px !important;">
                             <button class="btn btn-sm" title="Refresh detalle" id="refresh_detalles_pagos_pacientes" onclick="listaprestacionesApagar()"> Actualizar
                                 <span class="fa fa-refresh btnSpinner hide"> </span>
                             </button>
                         </th>
                        </tr>
                     <tr style="background-color: #f0f0f0">
                         <th width="1%">
                             <span class="custom-checkbox-myStyle hide">
								<input type="checkbox" id="checkeAllCitas">
								<label for="checkeAllCitas"></label>
							</span>
                         </th>
                         <th width="40%">Prestaciones</th>
                         <th width="10%">Total</th>
                         <th width="10%">Abonado</th>
                         <th width="10%">Pendiente</th>
                         <th width="10%">Estado</th>
                         <th width="10%">Abonar</th>
                     </tr>
                 </thead>

                 <tfoot style="background-color: #f0f0f0">
                    <tr>
                        <td colspan="4" class="text-right">&nbsp;</td>
                        <td colspan="2" class="" align="right" style="font-weight: bolder">TOTAL RECAUDADO:</td>
                        <td colspan="1" class="text-center" style="font-weight: bolder; text-align: left" >
                            <span id="totalPrestacion" style="padding: 5px; border-radius: 5px; font-weight: bolder; background-color: #f0f0f0; text-align: left">0.00</span>
                        </td>
                    </tr>
                 </tfoot>
             </table>
             <br>
         </div>
     </div>

     <div class="form-group col-xs-12 col-md-12" >
         <div class="col-sm-12 col-xs-12 col-md-9 col-centered" style="background-color:#f9f9f9;">
             <br>
             <div class="form-horizontal">

                 <div class="form-group">
                     <label for=""  class="control-label col-sm-4 col-md-4 col-xs-12">Medio de Pago:</label>
                     <div class="col-sm-6 col-md-6 col-xs-12">
                         <select id="t_pagos" class="form-control" style="width: 100%">
                             <option value=""></option>
                             <?php

                             ?>
                         </select>
                     </div>
                 </div>

                 <div class="form-group">
                     <label for=""  class="control-label col-sm-4 col-md-4 col-xs-12"> № Factura / Boleta:</label>
                     <div class="col-sm-6 col-md-6 col-xs-12">
                         <input type="text" id="n_factboleta" class="form-control" maxlength="11">
                     </div>
                 </div>

                 <div class="form-group">
                     <label for=""  class="control-label col-sm-4 col-md-4 col-xs-12"> Descripción: </label>
                     <div class="col-sm-6 col-md-6 col-xs-12">
                         <textarea id="descripObserv" class="form-control" placeholder="opcional" rows="2" style="resize: vertical"></textarea>
                     </div>
                 </div>

                 <div class="form-group">
                     <label for=""  class="control-label col-sm-4 col-md-4 col-xs-12"> Monto: </label>
                     <div class="col-sm-5 col-md-5 col-xs-12">
                         <label for=""  class="control-label col-sm-8 col-md-8 col-xs-12" style="padding-left: 0px; ">
                             <span id="monto_pag" class="pull-left">0.00</span>
                         </label>
                     </div>
                 </div>


                 <div class="form-group pull-right">
                     <button class="btn btnhover btn-block" style="font-weight: bolder; color: green" id="btnApagar" >
                         Aceptar
                         <span class="fa fa-refresh btnSpinner hide"></span>
                     </button>
                 </div>

             </div>
         </div>
     </div>


     <script>
         $(window).on("load", function () {
             if("<?= $showCaja ?>" != "1"){
                 notificacion("<?= $showCaja ?>", "question");
                $("#btnApagar").addClass("disabled_link3");
                $('.Abonar').attr("disabled",true);
             }
         });
     </script>

<?php

 }

?>

