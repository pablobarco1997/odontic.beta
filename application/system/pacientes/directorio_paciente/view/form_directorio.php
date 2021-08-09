<style>
    .list_option li{
        padding: 10px;
    }
</style>

<div class="row">
    <div class="col-md-12">

        <div class="box box-solid">

            <div class="box-header with-border">
                <h4 class="no-margin"><span><b>Directorio de Pacientes</b></span></h4>
            </div>

            <div class="box-body">
                <div class="row">
                    <div class="form-group col-xs-12 col-md-12">
                        <?php accessoModule("Directorio de pacientes"); ?>
                        <div class="col-lg-8 col-xs-12 col-md-6 col-sm-6 margenTopDiv pull-right">
                            <ul class="list-inline pull-right list_option">
                                <li>
                                    <div class="checkbox btnhover <?= (!PermitsModule("Directorio de pacientes", "consultar")?"disabled_link3":"") ?>" style="margin: 0px; padding: 5px">
                                        <label>
                                            <input type="checkbox" id="checkPacienteDesact">
                                            <i class="fa fa-user-times"></i> Ver lista de pacientes desabilitados
                                        </label>
                                    </div>
                                </li>
                                <li>
                                    <label>
                                        <a id="imprimir_listPacientes" class="btnhover <?= (!PermitsModule("Directorio de pacientes", "consultar")?"disabled_link3":"") ?>" style="padding: 5px; color: #333333" target="_blank" href="<?= DOL_HTTP .'/application/system/pacientes/directorio_paciente/export/export_pdf_directorio.php' ?>"><i class="fa fa-print"></i>   &nbsp;Imprimir Lista</a>
                                    </label>
                                </li>
                                <li>
                                    <label for="">
                                        <a class="btnhover" style="padding: 5px; color: #333333" href="<?= DOL_HTTP .'/application/system/pacientes/nuevo_paciente/index.php?view=nuev_paciente'?>"> <i class="fa fa-users"></i> Nuevo Paciente</a>
                                    </label>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>



                <div class="row">
                    <div class="form-group col-md-12 col-xs-12">
                        <div class="col-md-12  col-sm-12 margenTopDiv">
                            <div style=" border-radius: 3px; padding: 3px; width: 100%">
                                <div class="table-responsive">
                                    <table class="table  compact" id="table_direc" width="100%">
                                        <thead>
                                            <tr style="background-color: #f4f4f4">
                                                <th width="2%">&nbsp;</th>
                                                <th width="20%">Nombre</th>
                                                <th width="10%">Dirección</th>
                                                <th width="10%">C. I.	</th>
                                                <th width="15%">Contactos</th>
                                                <th width="10%">Género</th>
                                                <th width="5%">&nbsp;</th>
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

    </div>
</div>
<br>

<script src="<?php echo DOL_HTTP .'/application/system/pacientes/directorio_paciente/js/directorioPacientes.js';?>"></script>