<?php

#breadcrumbs  -----------------------------------------------
$url_breadcrumbs = $_SERVER['REQUEST_URI'];
$titulo = "Cajas Abiertas";
$modulo = true;

?>


<div class="form-group col-xs-12 col-md-12 col-lg-12 no-padding">
    <div  class="form-group col-md-12 col-xs-12">
        <?= Breadcrumbs_Mod($titulo, $url_breadcrumbs, $modulo); ?>

        <label for="">LISTA DE COMPORTAMIENTOS</label>
        <ul class="list-inline" style="background-color: #f4f4f4; border-bottom: 0.6px solid #333333; padding: 3px">
            <li><a href="#contentFilter" data-toggle="collapse" style="color: #333333" class="btnhover btn btn-sm " id="fitrar_document"> <b>  ▼ &nbsp;Filtrar <i></i> </b> </a></li>
            <li><a href="<?= DOL_HTTP.'/application/system/operacion/cajas_clinicas/index.php?view=all_cajas_clinicas&key='.$_GET['key'] ?>" style="color: #333333" class="btnhover btn btn-sm  "> <b> Cajas Abiertas </b> </a></li>
        </ul>
    </div>

    <div class="form-group col-xs-12 col-md-12 col-lg-12 collapse" id="contentFilter" aria-expanded="true" style="">
        <div class="col-md-12 col-xs-12 col-lg-12" style="background-color: #f4f4f4; padding-top: 15px">
            <div class="form-group col-md-12 col-xs-12 col-lg-12"> <h3 class="no-margin"><span>Filtrar Cuentas Cajas</span></h3> </div>

        </div>
    </div>

    <div class="form-group col-xs-12 col-md-12">
        <button class="aplicar btn  btn-sm btn-success" data-target="#modal_abrir_cajas_clinicas_fn" data-toggle="modal" style="float: right; font-weight: bolder;padding: 5px"> &nbsp;  &nbsp;Abrir Caja &nbsp;</button>
    </div>

    <div class="form-group col-xs-12 col-md-12">
        <div class="table-responsive">
            <table class="table table-condensed " width="100%"  id="all_Cuenta" >
                <thead style="background-color: #f4f4f4; ">
                    <tr>
                        <th>usuario</th>
                        <th>fecha de apertura</th>
                        <th>fecha de cierre</th>
                        <th>Saldo anterior</th>
                        <th>Saldo inicial</th>
                        <th>Acumulado</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>


    <!-- Modal -->
    <div class="modal fade" id="modal_abrir_cajas_clinicas_fn" role="dialog">
        <div class="modal-dialog modal-sm" style="margin: 2% auto; width: 30%">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header modal-diseng">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title"><span>Editar Nombre cuenta</span></h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-xs-12 col-md-12">
                            <label for="">Agrege un nombre</label>
                            <textarea class="form-control" id="fn_editar_name_cuenta" maxlength="500"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" id="guardarEditNameCuen" onclick=""> Guardar </button>
                </div>
            </div>

        </div>
    </div>

</div>


