<?php
    accessoModule('Planes de Tratamientos');
?>

<?php

    /*
    $optionPlamtramFiltro  = "<option></option>";
    $sqlOptionPlantCab = "SELECT 
                            c.rowid , 
                            ifnull(c.edit_name, concat('Plan de Tratamiento ', 'N. ', c.numero)) plantram ,
                            concat('Doc(a) ', ' ', ifnull( (select concat( od.nombre_doc , ' ', od.apellido_doc ) as nomb from tab_odontologos od where od.rowid = c.fk_doc), 'No asignado')) as encargado
                          FROM tab_plan_tratamiento_cab c where c.fk_paciente = $idPaciente";
    $rsOption = $db->query($sqlOptionPlantCab);
    if($rsOption && $rsOption->rowCount()>0){
        while ($obOption = $rsOption->fetchObject()){
            $optionPlamtramFiltro .= "<option value='$obOption->rowid'> $obOption->plantram  &nbsp;&nbsp; $obOption->encargado </option>";
        }
    }*/

?>


<?php

    #ID PLAN DE TRATAMIENTO DECLARADO
    $idplantram = 0;
    if(isset($_GET['idplan']) && $_GET['idplan'] != 0 )
    {

        $idplantram = decomposeSecurityTokenId($_GET['idplan']);

    }

    $accion = "principal";
    if(isset($_GET['v']) && $_GET['v'] == 'planform')
    {
        $accion = 'addplan'; #cuando se add plan de tratamiento

    }


    #breadcrumbs  -----------------------------------------------
    $url_breadcrumbs = "";
    $titulo = "";
    $modulo = "";
    if(isset($_GET['v'])){

        if($_GET['v']=='planform'){
            $url_breadcrumbs = $_SERVER['REQUEST_URI'];
            $titulo = "Agregar prestaciones";
            $modulo = false;
        }

    }else{
        $url_breadcrumbs = $_SERVER['REQUEST_URI'];
        $titulo = "Planes de Tratamiento";
        $modulo = true;
    }

?>

<style>


    #listtratamientotable  tbody tr td > div{
        background-color: #ffffff;
        transition-duration: 0.2s;
    }
    #listtratamientotable  tbody tr td > div:hover{
        box-shadow:0 2px 5px 0 rgba(0, 0, 0, 0.225);
        border:0;
    }

</style>


<script>
    //ID DEL PLAN DE TRATAMIENTO
    $ID_PLAN_TRATAMIENTO =  <?= $idplantram ?>;
    $accion              = "<?= $accion ?>";
</script>


<div class="form-group col-md-12 col-xs-12" >


    <?php if(isset($_GET['v']) && $_GET['v'] == 'planform') { ?>

        <?php include_once DOL_DOCUMENT.'/application/system/pacientes/pacientes_admin/plan_tratamiento/view/add_plan_tratam.php'; ?>

    <?php }else{ ?>

        <script>

            $(window).on('load', function () {
                //valida si tiene permiso para consultar
                if(!ModulePermission('Planes de Tratamientos', 'consultar')){
                    notificacion('Ud. No tiene permiso para consultar', 'error');
                    return false;
                }
            });

        </script>


        <!-- breadcrumbs -->
        <div class="form-group col-md-6 col-xs-12 col-lg-6 pull-left">
            <?= Breadcrumbs_Mod($titulo, $url_breadcrumbs, $modulo); ?>
        </div>

        <div class="form-group col-md-12 col-xs-12 no-margin">
            <label for="">LISTA DE COMPORTAMIENTOS</label>
            <ul class="list-inline" style="border-bottom: 0.6px solid #333333; padding: 3px; background-color: #f4f4f4; width: 100%; margin-left: 0px;">
                <li><a data-toggle="collapse"  data-target="#contentFilter" class="btnhover btn btn-sm <?= (PermitsModule('Planes de Tratamientos', 'consultar')==0)?'disabled_link3':'' ?> " style="color: #333333" > <b>   ▼  Filtrar  </b>  </a> </li>
                <li>
                    <a href="#" style="color: #333333" class="btnhover btn btn-sm <?= ((PermitsModule('Planes de Tratamientos', 'agregar')==0)?'disabled_link3':'') ?> " id="createPlanTratamientoCab"> <b>  <i class="fa fa-file-o"></i>  Tratamiento en blanco </b> </a>
                </li>

                <li>
                    <a href="#modal_plantrem_citas" style="color: #333333" data-toggle="modal" class="btnhover btn btn-sm hidden" onclick="attrChangAsociarCitas(null)"> <b>  <i class="fa fa-clone"></i>  Crear Plan de tratamiento desde cita de paciente  </b> </a>
                </li>
            </ul>

        </div>

<!--        OTRAS OPCIONES DE FILTRO  -->

        <div class="form-group col-xs-12 col-md-12 col-lg-12 collapse no-margin" id="contentFilter" >
            <div class="col-md-12 col-xs-12 col-lg-12" style="background-color: #f4f4f4; padding-top: 15px; margin-bottom: 15px">
                <div class="form-group col-md-12 col-xs-12 col-lg-12"> <h3 class="no-margin"><span>Filtrar Planes de Tratamiento</span></h3> </div>

                <div class="form-group col-xs-12 col-md-4 col-sm-12">
                    <label for="">Fecha</label>
                    <div class="input-group form-group rango" style="margin: 0">
                        <input type="text" class="form-control filtroFecha  " readonly id="startDate" value="">
                        <span class="input-group-addon" style="border-radius: 0"><i class="fa fa-calendar"></i></span>
                    </div>
                </div>

                <div class="form-group col-xs-12 col-md-8 col-sm-12">
                    <label for="filtrPlantram">Plan de Tramamiento</label>
                    <select id="filtrPlantram" class="form-control" style="width:100% ;">
                        <option value=""></option>
                    </select>
                </div>

                <div class="form-group col-xs-12 col-md-3 col-sm-12">
                    <label for="">Estados</label>
                    <div class="radio">
                        <label for="mostaraFinalizados">
                            <input type="radio" name="estado_tratamiento" id="mostaraFinalizados" class="Finalizados" style="margin-top: 2px !important;"> <b>Finalizados</b>
                        </label>
                    </div>
                    <div class="radio">
                        <label for="mostrarAnuladosPlantram">
                            <input type="radio" name="estado_tratamiento" id="mostrarAnuladosPlantram" class="Anulados" style="margin-top: 2px !important;"> <b>Anulados</b>
                        </label>
                    </div>
                    <div class="radio">
                        <label for="mostrarAbonadosPlantram">
                            <input type="radio" name="estado_tratamiento" id="mostrarAbonadosPlantram" class="Abonados" style="margin-top: 2px !important;"> <b>Abonados</b>
                        </label>
                    </div>
                    <div class="radio">
                        <label for="mostrarDiagnosticoPlantram">
                            <input type="radio" name="estado_tratamiento" id="mostrarDiagnosticoPlantram" class="Diagnostico" style="margin-top: 2px !important;"> <b>Diagnóstico</b>
                        </label>
                    </div>
                </div>


                <div class="form-group col-md-12 col-xs-12">
                    <ul class="list-inline pull-right">
                        <li>  <button class="exportExcelTranm btn   btn-block  btn-default text-bold" id="exportExcelTranm" onclick="ExportPlanTratamiento()" style="float: right; padding: 10px">&nbsp; &nbsp; Export Excel &nbsp; <span class="fa fa-print"></span> &nbsp;</button> </li>
                        <li>  <button class="limpiar btn   btn-block  btn-default" id="limpiarFiltro" style="float: right; padding: 10px"> &nbsp; &nbsp; Limpiar &nbsp; &nbsp;</button> </li>
                        <li>  <button class="aplicar btn   btn-block  btn-success" id="filtrar_evoluc" style="float: right; padding: 10px"> &nbsp;  &nbsp;Aplicar busqueda &nbsp;</button> </li>
                    </ul>
                </div>

            </div>
        </div>


        <!-- END OPCIONES CREACION DE PLANDES DE TRATAMIENTO -->
        <div class="form-group col-xs-12 col-md-12">
            <div class="table-responsive">
                <table class="table" id="listtratamientotable" width="100%">
                    <thead>
                        <tr>
                            <th style="background-color: #f4f4f4">PLANES DE TRATAMIENTO</th>
                        </tr>
                    </thead>
                </table>
                <br>
            </div>
        </div>

        <!--    MODAL CREAR PLAN DE TRATAMIENTO ASOCIADO A UNA CITA  --------------------------------------------------->
        <div id="modal_plantrem_citas" class="modal fade" role="dialog" data-backdrop="static">
            <div class="modal-dialog " style="margin: 2% auto; width: 50%" >

                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header modal-diseng">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title"><span>Asociar citas</span></h4>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="form-group col-xs-12">

                                <div class="form-group col-xs-6 col-md-6 col-sm-6 no-margin">
                                    <p class="text-right" id="nuPlanTratamiento" data-id="0"></p>
                                </div>

                                <div class="form-group col-xs-12 col-md-12 col-sm-12 no-margin">
                                   <span style=" color: #eb9627">
                                        <i class="fa fa-info-circle"></i>
                                            Ud. puede asociar varias citas agendadas a un plan de tratamiento
                                    </span>
                                </div>

                                <br>

                                <div class="form-group col-xs-12 col-md-12 col-sm-12 no-margin">
                                    <select name="" id="citasPaciente" class="form-control" style="width: 100%">
                                        <option value=""></option>
                                        <?php

                                        ?>
                                    </select>
                                    <small id="error_asociarCitas" style="color: red;"></small>
                                </div>

                                <div class="form-group col-xs-12 col-md-12 col-sm-12 ">
                                    <br>
                                    <div class="table-responsive">
                                        <table id="listTramnCitasAsoc" class="table table-condensed" width="100%" style="border-collapse: collapse; ">
                                            <thead style="background-color: #f4f4f4;">
                                                <tr>
                                                    <th colspan="4">Lista de Citas Agendadas para este Plan de Tratamiento</th>
                                                </tr>
                                                <tr>
                                                    <th width="20%">Citas Agendadas</th>
                                                    <th width="15%">Especialidad</th>
                                                    <th width="25%">Emisión de cita</th>
                                                    <th width="20%">Estado</th>
                                                </tr>
                                            </thead>
                                        </table>
                                    </div>
                                </div>

                            </div>
                            <div class="form-group col-md-5 " style="float: right" >
                                <button class="btn btnhover " style="font-weight: bolder; color: green; float: right" id="CrearPlanTratamientoPlantram" >
                                    Guardar
                                    <span class="fa fa-refresh btnSpinner hide"> </span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>



        <!--MENSAJE DE CONFIRMACION DE ELIMINACION DE PLAN DE TRATAMIENTO-->
        <div id="confirm_eliminar_plantram" class="modal fade" role="dialog" data-backdrop="static">
            <div class="modal-dialog">

                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header modal-diseng">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title"><span>Anular</span></h4>
                    </div>
                    <div class="modal-body">
                        <p id="msg_eliminar_plantram"></p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" style="font-weight: bolder; color: red" class="btn btnhover" id="delete_plantram_confirm" onclick="delete_confirmar_true_plantram($(this))">
                            Anular
                            <span class="fa fa-refresh btnSpinner hide"></span>
                        </button>
                    </div>
                </div>

            </div>
        </div>

        <!--MODAL ASOCIAR PROFECIONAL-->
        <div id="modal_asociar_profecional" class="modal fade" role="dialog">
            <div class="modal-dialog">

                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header modal-diseng">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title"><span>Asociar Profecional</span></h4>
                        <input type="text" class="hidden" id="PlantTratamientoAsociOdont">
                    </div>
                    <div class="modal-body">
                        <label for="">Asociar profecional acargo:</label>
                        <select name="odontolog_id" id="odontolog_id" class="form-control" style="width: 100%">
                            <option value=""></option>
                            <?php
                                $result = $db->query("select rowid, concat(nombre_doc,' ', apellido_doc) as nom from tab_odontologos where estado = 'A'")->fetchAll(PDO::FETCH_ASSOC);
                                foreach ($result as $c => $value){
                                    print '<option value="'.$value['rowid'].'">'.$value['nom'].'</option>';
                                }


                            ?>
                        </select>
                    </div>
                    <div class="modal-footer">
                        <button type="button" style="font-weight: bolder; color: green" class="btn btnhover" id="asociar_profecional_" >Guardar</button>
                        <button type="button" style="font-weight: bolder;" class="btn btnhover" data-dismiss="modal">Cerrar</button>
                    </div>
                </div>

            </div>
        </div>

        <!--MENSAJE DE CONFIRMACION DE ELIMINACION DE PLAN DE TRATAMIENTO-->
        <div id="confirm_finalizar_plantramiento" class="modal fade" data-backdrop="static" role="dialog">
            <div class="modal-dialog">

                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header modal-diseng">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title"><span>Confirmar</span></h4>
                    </div>
                    <div class="modal-body">
                        <h3 style="font-size: 2rem">Finalizar Plan de tratamiento</h3>
                        <p>
                            <i class="fa fa-eye"></i> Un plan de tratamiento sé finalizar siempre y cuando contenga todas las prestaciones Pagadas y realizadas
                        </p>
                        <span style="display: block;  color: #eb9627"  >
                            <i class="fa fa-info-circle"> </i> Una vez realizada esta operación el plan de tratamiento no podrá ser modificado
                        </span>
                        <p id="mg_finalizar_plantramiento"></p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" style="font-weight: bolder; color: green" class="btn btnhover" id="finalizar_plantramiento" >Aceptar

                            <span class="fa fa-refresh btnSpinner hide"></span>
                        </button>
                    </div>
                </div>

            </div>
        </div>

    <?php } ?>

</div>


<!--modal cambiar nombre plan de tratamiento-->

<div class="modal fade" id="modnombPlantratamiento" role="dialog">
    <div class="modal-dialog modal-sm">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header modal-diseng">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title" data-id="0" id="idplanTratamientotitulo"><span>EDITAR NOMBRE PLAN DE TRATAMIENTO</span></h4>
            </div>
            <div class="modal-body">

                <div class="row">

                    <div class="col-md-12 col-sm-12">
                        <div class="form-group col-xs-12">
                            <label for="">Editar Nombre - Plan de tratamiento</label>
                            <input type="text" class="form-control" id="nametratamiento">
                        </div>
                    </div>

                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn" style="font-weight: bold"  data-dismiss="modal">cancelar</button>
                <button type="button" class="btn text-bold" style="color: green" id="acetareditNomPlanT">
                    Aceptar
                    <span class="fa fa-refresh btnSpinner hide"></span>
                </button>
            </div>
        </div>

    </div>
</div>

