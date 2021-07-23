
<?php

    $idOdontograma = "";
    $view          = "";
    $v             = "";

    #LISTA DE PRINCIPAL
    if(isset($_GET['v']) && $_GET['v'] == 'listp')
    {
        $view  = "principal";
        $v     = $_GET['v'];
    }

    #ACTUALIZAR ODONTOGRAMA
    if(isset($_GET['v']) && $_GET['v'] == 'fordont')
    {
        $v        = $_GET['v'];
        $view     = "form_odont";
    }

?>


<script>

    $accionOdontograma = "<?= $view ?>";

</script>

<div class="form-group col-md-12 col-xs-12">

<!--    LISTA DE ODONTOGRAMA PRINCIPAL -->
    <?php

        if($v == "listp")
        {
            $module = true;
            $url_breadcrumb = $_SERVER['REQUEST_URI'];
            $titulo         = 'odontogramas creados';

    ?>

        <div class="form-group col-md-6 col-xs-12 col-lg-6 pull-left">
            <div class="col-md-12 col-xs-12 pull-left no-padding">
                <?php echo Breadcrumbs_Mod($titulo, $url_breadcrumb, $module) ?>
            </div>
        </div>
<!--        OPCIONES ODONTOGRAMA-->
        <div class="form-group col-md-12 col-xs-12">
            <label for="">LISTA DE COMPORTAMIENTOS</label>
            <ul class="list-inline" style="border-bottom: 0.6px solid #333333; padding: 3px; margin-left: 0px; background-color: #f4f4f4">
                <li><a href="#contentFilter" data-toggle="collapse" style="color: #333333" class="btnhover btn btn-sm" id="fitrar_document" aria-expanded="true"> <b>  ▼ &nbsp;Filtrar <i></i> </b> </a></li>
                <li>
                    <a href="#add_odontograma" data-toggle="modal" class="btnhover btn btn-sm " style="color: #333333" id="createOdontograma"> <b> <i class="fa fa-plus-square"></i> Crear Odontograma  </b> </a>
                </li>
            </ul>
        </div>

        <div class="form-group col-xs-12 col-md-12 col-lg-12 no-margin collapse " id="contentFilter" aria-expanded="true" style="">
            <div class="col-md-12 col-xs-12 col-lg-12" style="background-color: #f4f4f4; padding-top: 15px; margin-bottom: 15px">
                <div class="form-group col-md-12 col-xs-12 col-lg-12"> <h3 class="no-margin"><span>Filtrar Odontograma</span></h3> </div>

                <div class="form-group col-xs-12 col-md-4 col-sm-6">
                    <label for="">Fecha</label>
                    <div class="input-group form-group rango" style="margin: 0">
                        <input type="text" class="form-control " readonly="" id="startDate_odont" value="">
                        <span class="input-group-addon" style="border-radius: 0"><i class="fa fa-calendar"></i></span>
                    </div>
                </div>

                <div class="form-group col-xs-12 col-md-8 col-sm-8">
                    <label for="">Plan de Tramamiento</label>
                    <select class="form-control tratamientoSeled" name="tratamientoSeled" id="tratamientoSeled" style="width: 100%" ></select>
                </div>

                <div class="form-group col-xs-12 col-md-2 col-sm-2">
                    <label for="">Numero</label>
                    <input type="text" class="form-control" placeholder="busq. por numero" id="numero_odont" name="numero_odont">
                </div>

                <div class="form-group col-xs-12 col-md-2 col-sm-2">
                    <label for="">Estado</label>
                    <select name="estado_odont" id="estado_odont" class="form-control" style="width: 100%">
                        <option value=""></option>
                        <option value="E">Eliminado</option>
                        <option value="A">Activo</option>
                    </select>
                </div>


                <div class="form-group col-md-12 col-xs-12">
                    <ul class="list-inline pull-right">
                        <li>  <button class="limpiar_busq_odont btn   btn-block  btn-default" id="limpiarFiltro" style="float: right; padding: 10px"> &nbsp; &nbsp; Limpiar &nbsp; &nbsp;</button> </li>
                        <li>  <button class="aplicar_busq_odont btn   btn-block  btn-success" id="filtrar_evoluc" style="float: right; padding: 10px"> &nbsp;  &nbsp;Aplicar busqueda &nbsp;</button> </li>
                    </ul>
                </div>

            </div>
        </div>


<!--        LISTA DE ODONTOGRAMA-->
        <div class="form-group col-md-12 col-xs-12">
            <div class="table-responsive">
                <table class="table dataTable" id="odontPLant" width="100%">
                    <thead>
                        <tr style="background-color: #f4f4f4">
                            <th WIDTH="10%">Emitido</th>
                            <th WIDTH="20%">Numero</th>
                            <th WIDTH="30%">Observación</th>
                            <th WIDTH="20%">Plan de Tratamiento</th>
                            <th WIDTH="20%"></th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>

        <?php include_once 'add_odontograma_modal.php'; ?>

    <?php } ?>


    <!--MODIFICAR EL ODONTOGRAMA-->
    <?php if($v == 'fordont')
        {

            $module         = false;
            $url_breadcrumb = $_SERVER['REQUEST_URI'];
            $titulo         = 'Actualizar odontograma';
    ?>

        <div class="form-group col-md-6 col-xs-12 col-lg-6 pull-left">
            <div class="col-md-12 col-xs-12 pull-left no-padding">
                <?php echo Breadcrumbs_Mod($titulo, $url_breadcrumb, $module) ?>
            </div>
        </div>

        <div class="form-group col-md-12 col-xs-12 no-margin">
            <label for="">LISTA DE COMPORTAMIENTOS</label>
            <ul class="list-inline" style="border-bottom: 0.6px solid #333333; padding: 3px; margin-left: 0px; background-color: #f4f4f4">
                <li><a href="#" id="imprimirOdontogramapdf" target="_blank" class="btnhover btn-sm btn" style="font-weight: bolder; color: #333333 "> <i class="fa fa-print"></i> &nbsp;PDF  </a> </li>
            </ul>
        </div>

        <div class="form-group col-md-12 col-xs-12">
            <style>
                .showblock{
                    display: block;
                }
                .hiddenblock{
                    display: none;
                }
            </style>
            <div class="picture-odontograma"  style="padding-left: 10px">
                <div class="text-center showblock" id="odontload"><b>Cargando Odontograma ...</b></div>
                <div id="CargarOdontogramalodd" class="hiddenblock">
                    <!--Odontograma dibujo-->
                    <?php include_once DOL_DOCUMENT .'/application/system/pacientes/pacientes_admin/odontograma_paciente/view/view_new_odontograma2021.php'; ?>
                </div>
            </div>
            <br>
            <?php include_once DOL_DOCUMENT .'/application/system/pacientes/pacientes_admin/odontograma_paciente/view/odontogram_tram_form.php'; ?>
        </div>

        <script>

            //funcion para cargar odontograma
            function viewloaddingOdontrograma($consult, $load){
                $('#CargarOdontogramalodd').removeClass($consult);
                $('#odontload').removeClass($load).addClass($consult);
            }

            window.addEventListener('load', function(event){
                viewloaddingOdontrograma('hiddenblock','showblock')
            });
        </script>

        <script src="<?= DOL_HTTP ?>/application/system/pacientes/pacientes_admin/odontograma_paciente/js/html2canvas.min.js" ></script>
        <script src="<?= DOL_HTTP ?>/application/system/pacientes/pacientes_admin/odontograma_paciente/js/canvas2image.js" ></script>

    <?php } ?>

</div>
