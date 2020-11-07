<div class="box box-solid">
    <div class="box-header with-border">
        <div class="form-group col-xs-12 col-sm-12 col-md-12 no-margin">
            <h4 class="no-margin"><span><b>Descuentos</b></span></h4>
        </div>
    </div>

    <div class="box-body">
        <br>

        <div class="form-group col-xs-12 col-md-10 col-lg-10 col-sm-12 col-centered">

            <div class="form-group col-sm-12 col-md-12 col-xs-12">
                <ul class="list-inline pull-right">
                    <li> <b> <a  href="#modal_conf_convenio" data-toggle="modal" class="btn btnhover "  onclick="InputsClean()" style=" background-color: #f0f0f0"> <i class="fa fa-plus-square-o"></i> Agregar Descuento </a> </b> </li>
                </ul>
            </div>

            <div class="form-group col-xs-12 col-md-12 col-lg-12">
                <div class="table-responsive">
                    <table class="table table-striped" id="conf_table_convenio" width="100%">
                        <thead >
                            <tr>
                                <th WIDTH="25%">Nombre</th>
                                <th WIDTH="25%">Descripción</th>
                                <th WIDTH="6%">Descuento %</th>
                                <th WIDTH="10%"></th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>


        </div>

    </div>

</div>

<!--//Modal de agregar convenio ------------------------------------------- -->
<div id="modal_conf_convenio" class="modal fade" role="dialog" data-backdrop="static" data-keyboard="false"  >
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header modal-diseng">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title" id="comportamiento"> <span>AGREGAR CONVENIO</span> </h4>
            </div>
            <div class="modal-body">

                <div style="padding: 10px">
                    <div class="col-centered form-group col-md-12 col-sm-10 col-xs-12">

                        <div class="form-horizontal">
                            <div class="form-group">
                                <label class="control-label col-md-3">Nombre</label>
                                <div class="col-md-8">
                                    <input type="text" id="nomb_conv" class="form-control input-sm" onkeyup="FormValidationDescuentos($(this), true)">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-3">Descripción</label>
                                <div class="col-md-8">
                                    <textarea name="" class="form-control input-sm" id="descrip_conv" rows="3"></textarea>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-3">Descuento %</label>
                                <div class="col-md-8">
                                    <input type="text" id="valor_conv" class="form-control input-sm " onkeyup="FormValidationDescuentos($(this), true)">
                                    <small style="color: red; " id="msg_descuento"></small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <div class="modal-footer">

                <a href="#" class="btn btnhover " style="font-weight: bolder; color: green" id="guardar_convenio_conf">Aceptar</a>
                <a href="#" class="btn btnhover" style="font-weight: bolder;" data-dismiss="modal">cerrar</a>

            </div>
        </div>

    </div>
</div>

