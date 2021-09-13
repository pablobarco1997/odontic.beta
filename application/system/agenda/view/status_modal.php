
<!--CONFIRMACION DE CITA EMAIL-->
<div id="notificar_email-modal" class="modal fade" role="dialog" data-backdrop="static" data-keyboard="false" >
    <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header modal-diseng">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title"> <span>Notificar  e-mail</h4>
                </div>
                <div class="modal-body">
                    <div class="form-horizontal">

                        <div class="form-group">
                            <label for="" class="control-label col-sm-3">Programa e-mail <small>(opcional)</small></label>
                            <div class="col-sm-7">
                                <table class="table" width="100%" style="border-collapse: collapse">
                                    <tr class="checked_programar">
                                        <td > <span><input type="checkbox"  id="emailConfirmacion_programar" title="Programar E-mail de Confirmación" style="margin-top: 10px"></span> </td>

                                        <td id="validar_td_fecha_program_email_confirm">
                                            <div class="input-group date disabled_link3" disabled="disabled" data-provide="datepicker">
                                                <input type="text" class="form-control disabled_link3" name="" id="date_programa_email_confirm" readonly="">
                                                <div class="input-group-addon">
                                                    <span class="fa fa-calendar"></span>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="" class="control-label col-sm-3">Asunto</label>
                            <div class="col-sm-7">
                                <input type="text" class="form-control" id="asunto_email" placeholder="asunto" value="Notificación de Cita">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="" class="control-label col-sm-3">From</label>
                            <div class="col-sm-7">
                                <input type="text" class="form-control" disabled id="de_email" placeholder="" value="<?= $conf->EMPRESA->INFORMACION->email ?>">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="" class="control-label col-sm-3">To</label>
                            <div class="col-sm-7">
                                <input type="text" class="form-control" id="para_email" placeholder="destinario" value="" onkeyup="keyemail_invalic()">
                                <small style="color: red;" id="invali_emil_mssg"></small>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="" class="control-label col-sm-3">Titulo</label>
                            <div class="col-sm-7">
                                <input type="text" class="form-control" id="titulo_email" placeholder="titulo" value="Notificación de Citas - Clinica <?= $conf->EMPRESA->INFORMACION->nombre;  ?> ">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="" class="control-label col-sm-3">Message</label>
                            <div class="col-sm-7">
                                <textarea id="messge_email" class="form-control" cols="30" rows="4"></textarea>
                            </div>
                        </div>


                        <div class="form-group" >
                            <label for="" class="control-label col-sm-3">&nbsp;&nbsp;</label>
                            <div class="col-sm-7">
                                <small id="emailEspere" style="font-weight: bolder">  </small>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
<!--                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>-->
                    <button type="button" class="btn btn-success" id="enviarEmail" onclick="">Enviar</button>
                </div>
            </div>

    </div>
</div>




<!--COMENTARIO ADICIONAL-->

<div id="modal_coment_adicional"  class="modal fade" role="dialog">
    <div class="modal-dialog modal-sm" style="margin: 2% auto; width: 30%">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header modal-diseng">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title" id="iddet-comment"><span>Observación</span></h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-xs-12 col-md-12">
                        <label for="#">Agrege su Observación</label>
                        <textarea cols="" class="form-control" id="comment_adicional" maxlength="700"></textarea>
                        <small style="color: blue;" id="invali_commentadciol_mssg"></small>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"> Close </button>
                <button type="button" class="btn btn-success" id="guardarCommentAdicional" > Guardar </button>
            </div>
        </div>

    </div>
</div>


<!--MODAL DE whatsapp-->

<div id="modalWhapsapp" class="modal fade" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header modal-diseng" >
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h3 class="no-padding no-margin">Send Whatsapp</h3>
                <span> <i class="fa fa-phone-square"></i> <b> Telefono Movil: </b> &nbsp;</span> <span id="number_whasap"></span>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="form-group col-md-12">
                        <div class="center-block" style="width: 100px">
                            <img src="https://img.icons8.com/plasticine/2x/whatsapp.png" alt="" style="width: 100%">
                        </div>
                    </div>
                    <div class="form-group col-md-12">
                        <label for="">Mensaje</label>
                        <textarea name="" id="mensajetext" class="form-control" cols="20" rows="5"></textarea>
                    </div>

                    <div class="form-group col-md-12">
                        <a href="#" onclick="" target="_blank" id="sendwhap"  class="btn btnhover pull-right" style="color: green; font-weight: bolder"><i class="fa fa-whatsapp"></i> Send </a>
                    </div>

                </div>
            </div>

        </div>

    </div>
</div>


<?php

$duracion = "";
$Minh = array();
$minhours = 0;
for ($min = 0; $min <= 10; $min++){
    $minhours += 15;
    $Minh[] = "<option value='".$minhours."'>$minhours .min</option>";
}
$duracion = implode(" ", $Minh);


$horaCita = "";
$hoursAxu = array();
for ($h = 8; $h <= 23; $h++){

    $min15 = 0;
    for ($m = 0; $m <= 3; $m++){

        $hourString = date('H:i',strtotime($h.':'.$min15));
        $hoursAxu[] = "<option value='".$hourString."'>$hourString</option>";
        $min15 += 15;
    }
}
$horaCita = implode(" ", $hoursAxu);

?>

<!--MODAL CAMBIO DE FECHA-->
<div id="modalCambioFechaCitas"  class="modal fade" role="dialog" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-sm" style="margin: 2% auto; width: 30%">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header modal-diseng">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title" id=""><span>Cambio de fecha</span></h4>
                <input type="hidden" id="iddetCitas" name="iddetCitas" value="">
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-xs-12 col-md-12">
                        <label for="#">Nueva Fecha de Cita</label>
                        <div class="input-group">
                            <input type="text" class="form-control" value="" name="reagendar_fecha_cita" id="reagendar_fecha_cita" readonly>
                            <div class="input-group-addon">
                                <span class="fa fa-calendar"></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-12 col-md-12">
                        <label for="#">Duración</label>
                        <select name="reagendar_duracion" class="form-control reagendar_select" id="reagendar_duracion" style="width: 100%">
                            <option value=""></option>
                            <?= $duracion ?>
                        </select>
                    </div>
                    <div class="col-xs-12 col-md-12">
                        <label for="#">Hora de Cita</label>
                        <select name="reagendar_hora_cita" class="form-control reagendar_select" id="reagendar_hora_cita" style="width: 100%">
                            <option value=""></option>
                            <?= $horaCita ?>
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn text-blue " style="font-weight: bold" data-dismiss="modal"> Cancelar </button>
                <button type="button" class="btn " style="color: green; font-weight: bold" onclick="reagendarCitas(this)" >
                    Guardar
                    <span class="fa fa-refresh btnSpinner"></span>
                </button>
            </div>
        </div>

    </div>
</div>