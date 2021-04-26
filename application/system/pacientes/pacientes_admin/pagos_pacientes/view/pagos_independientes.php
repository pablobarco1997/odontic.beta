

<style>
    /*!* Custom checkbox *!*/
    /*.custom-checkbox-myStyle {*/
        /*position: relative;*/
    /*}*/
    /*.custom-checkbox-myStyle input[type="checkbox"] {*/
        /*opacity: 0;*/
        /*position: absolute;*/
        /*margin: 5px 0 0 3px;*/
        /*z-index: 9;*/
    /*}*/
    /*.custom-checkbox-myStyle label:before{*/
        /*width: 18px;*/
        /*height: 18px;*/
    /*}*/
    /*.custom-checkbox-myStyle label:before {*/
        /*content: '';*/
        /*margin-right: 10px;*/
        /*display: inline-block;*/
        /*vertical-align: text-top;*/
        /*background: white;*/
        /*border: 1px solid #bbb;*/
        /*border-radius: 2px;*/
        /*box-sizing: border-box;*/
        /*z-index: 2;*/
    /*}*/
    /*.custom-checkbox-myStyle input[type="checkbox"]:checked + label:after {*/
        /*content: '';*/
        /*position: absolute;*/
        /*left: 6px;*/
        /*top: 3px;*/
        /*width: 6px;*/
        /*height: 11px;*/
        /*border: solid #000;*/
        /*border-width: 0 3px 3px 0;*/
        /*transform: inherit;*/
        /*z-index: 3;*/
        /*transform: rotateZ(45deg);*/
    /*}*/
    /*.custom-checkbox-myStyle input[type="checkbox"]:checked + label:before {*/
        /*border-color: #212f3d;*/
        /*background: #15528A;*/
    /*}*/
    /*.custom-checkbox-myStyle input[type="checkbox"]:checked + label:after {*/
        /*border-color: #fff;*/
    /*}*/
    /*.custom-checkbox-myStyle input[type="checkbox"]:disabled + label:before {*/
        /*color: #b8b8b8;*/
        /*cursor: auto;*/
        /*box-shadow: none;*/
        /*background: #ddd;*/
    /*}*/

    /*.custom-checkbox-myStyle input[type="checkbox"]{*/
        /*cursor: pointer;*/
    /*}*/

</style>


<?php


global $db, $user;



$showCaja = ConsultarCajaUsers($user->id, false);

if($showCaja!=1){
    $showCaja = "Este usuario no tiene asociada una caja <br> <b>No puede realizar esta Operación</b>";
}else{
    $showCaja="1";
}


if(isset($_GET['v']) && $_GET['v'] == 'paym')
{


?>

<script>
    $(window).on("load", function () {
        if("<?= $showCaja ?>" != "1"){
            notificacion("<?= $showCaja ?>", "question");
        }
    });
</script>

<div class="form-group col-xs-12 col-md-12">
    <div class="row">
        <div class="col-md-12 col-xs-12">
            <div class="table-responsive">
                <table class="table table-striped" id="pagos_planestratamiento_list" width="100%">
                    <thead>
                    <tr>
                        <th colspan="3">PAGOS POR PLANES DE TRATAMIENTO DE PACIENTES</th>
                    </tr>
                    <tr>
                        <th width="5%">Cobrar</th>
                        <th width="8%">Fecha</th>
                        <th width="25%">Plan de Tratamiento</th>
                        <th width="15%">Cita Asociada</th>
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

     $query = "Select if(edit_name!=''|| edit_name!='NULL', edit_name , concat('Plan de Tratamiento No ', numero) ) as numplantram 
               From tab_plan_tratamiento_cab where rowid =". ((isset($_GET['idplantram']))?$_GET['idplantram']:0) ."   ";
     $n_plantram = $db->query($query)->fetchObject()->numplantram;


 ?>


     <div class="form-group col-xs-12 col-md-12 col-sm-12">
         <div class="table-responsive">
             <table class="table-striped table" id="ApagarlistPlantratmm" width="100%">

                 <thead>
                     <tr>
                         <th colspan="3">LISTA DE PRESTACIONES N. <?= $n_plantram ?> </th>
                     </tr>
                     <tr>
                         <th width="5%">
                             <span class="custom-checkbox-myStyle">
								<input type="checkbox" id="checkeAllCitas">
								<label for="checkeAllCitas"></label>
							</span>
                         </th>
                         <th width="35%">Prestación</th>
                         <th width="10%">Total</th>
                         <th width="10%">Abonado</th>
                         <th width="10%">Pendiente</th>
                         <th width="10%">Estado</th>
                         <th width="10%">Abonar</th>
                     </tr>
                 </thead>

                 <tfoot>
                    <tr>
                        <td colspan="5" class="text-right">&nbsp;</td>
                        <td colspan="1" class="" style="font-weight: bolder">TOTAL:</td>
                        <td colspan="1" class="text-center" style="font-weight: bolder">
                            <span id="totalPrestacion" style="padding: 5px; border-radius: 5px; padding: 5px; font-weight: bolder; background-color: #f0f0f0">0.00</span>
                        </td>
                    </tr>
                 </tfoot>
             </table>
             <br>
         </div>
     </div>

     <div class="form-group col-xs-12 col-md-12">
         <div class="col-sm-12 col-xs-12 col-md-9 col-centered" >
             <h3><span>Recaudación</span></h3>
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
                         <small style="color: red" id="err_t_pago"></small>
                     </div>
                 </div>

                 <div class="form-group">
                     <label for=""  class="control-label col-sm-4 col-md-4 col-xs-12"> № Factura / Boleta:</label>
                     <div class="col-sm-6 col-md-6 col-xs-12">
                         <input type="text" id="n_factboleta" class="form-control" maxlength="11">
                         <small style="color: red" id="err_t_nboleta"></small>
                     </div>
                 </div>

                 <div class="form-group">
                     <label for=""  class="control-label col-sm-4 col-md-4 col-xs-12"> Descripción ( <small>opcional</small> ): </label>
                     <div class="col-sm-6 col-md-6 col-xs-12">
                         <textarea id="descripObserv" class="form-control"></textarea>
                     </div>
                 </div>

                 <div class="form-group">
                     <label for=""  class="control-label col-sm-4 col-md-4 col-xs-12"> Monto: </label>
                     <div class="col-sm-5 col-md-5 col-xs-12">
                         <label for=""  class="control-label col-sm-8 col-md-8 col-xs-12">
                             <i class="fa fa-dollar"></i> <span id="monto_pag">0.00</span>
                             <small style="color: red; display: block" id="err_monto" ></small>
                         </label>
                     </div>
                 </div>


                 <div class="form-group col-sm-12 col-md-12 col-xs-12 pull-right">
                     <input type="button" class="btn btnhover btn-block" style="font-weight: bolder; color: green" id="btnApagar" value="Aceptar">
                 </div>

             </div>
         </div>
     </div>


     <div class="modal fade" id="modal_edit_tipoPago" role="dialog">
         <div class="modal-dialog">

             <!-- Modal content-->
             <div class="modal-content">
                 <div class="modal-header modal-diseng">
                     <button type="button" class="close" data-dismiss="modal">&times;</button>
                     <h4 class="modal-title" id="tipo_pago_id" data-idpagotype="">Editar</h4>
                 </div>
                 <div class="modal-body">
                   <div class="row">
                       <div class="col-md-12 col-xs-12">
                           <label for="">Tipo de pago</label>
                           <input type="text" class="form-control" id="formp_descrip_formp" name="formp_descrip_formp" placeholder="descripción del pago" onkeyup="FomValidFormaPagos()">
                       </div>
                       <div class="col-md-12 col-xs-12">
                           <label for="">Explicación</label>
                           <textarea name="formp_observacion" id="formp_observacion" cols="30" rows="5" class="form-control" maxlength="650" placeholder="opcional"></textarea>
                       </div>
                   </div>
                 </div>
                 <div class="modal-footer">
                     <a href="#" class="btn btnhover " style="font-weight: bolder; color: green; float: right" id="addFormaPago">Guardar</a>
                     <a type="button" class="btn btnhover " style="font-weight: bolder;  float: right" data-dismiss="modal">cerrar</a>
                 </div>
             </div>

         </div>
     </div>

     <script>
         $(window).on("load", function () {
             if("<?= $showCaja ?>" != "1"){
                 notificacion("<?= $showCaja ?>", "question");
                $("#btnApagar").attr("disabled",true).addClass("disabled_link3");
                $('.Abonar').attr("disabled",true);
             }
         });
     </script>

<?php

 }

?>

