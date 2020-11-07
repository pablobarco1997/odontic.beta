
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
            $titulo         = 'odontogramas';

    ?>

        <div class="form-group col-md-6 col-xs-12 col-lg-6 pull-left">
            <div class="col-md-12 col-xs-12 pull-left no-padding">
                <?php echo Breadcrumbs_Mod($titulo, $url_breadcrumb, $module) ?>
            </div>
        </div>
<!--        OPCIONES ODONTOGRAMA-->
        <div class="form-group col-md-12 col-xs-12">
            <label for="">LISTA DE COMPORTAMIENTOS</label>
            <ul class="list-inline" style="border-bottom: 0.6px solid #333333; padding: 3px">
                <li>
                    <a href="#add_odontograma" data-toggle="modal" class="btnhover btn btn-sm " style="color: #333333" id="createOdontograma"> <b> &nbsp;&nbsp; <img  src=" <?= DOL_HTTP .'/logos_icon/logo_default/diente.png';?>" width="12px" height="14px" alt="">Crear Odontograma  </b> </a>
                </li>
            </ul>

            <br>
        </div>


<!--        LISTA DE ODONTOGRAMA-->
        <div class="form-group col-md-12 col-xs-12">
            <div class="table-responsive">
                <table class="table dataTable" id="odontPLant" width="100%">
                    <thead>
                        <tr>
                            <th WIDTH="10%">FECHA</th>
                            <th WIDTH="20%">NÚMERO</th>
                            <th WIDTH="30%">DESCRIPCIÓN</th>
                            <th WIDTH="20%">PLAN DE TRATAMIENTO</th>
                            <th WIDTH="20%"></th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>

        <?php include_once 'add_odontograma_modal.php'; ?>

    <?php } ?>


<!--    FORMULARIO ODONTOGRAMA-->

    <?php if($v == 'fordont')
        {

            $module = false;
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
            <ul class="list-inline" style="border-bottom: 0.6px solid #333333; padding: 3px">
                <li><a href="#" id="imprimirOdontogramapdf" target="_blank" class="btnhover btn-sm btn" style="font-weight: bolder; color: #333333 "> <i class="fa fa-print"></i> &nbsp; Imprimir PDF  </a> </li>
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
                    <?php include_once DOL_DOCUMENT .'/application/system/pacientes/pacientes_admin/odontograma_paciente/view/picture_piezas_odontograma.php'; ?>
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
            window.addEventListener('load', function(event){ viewloaddingOdontrograma('hiddenblock','showblock') });
        </script>

        <script src="<?= DOL_HTTP ?>/application/system/pacientes/pacientes_admin/odontograma_paciente/js/html2canvas.min.js" ></script>
        <script src="<?= DOL_HTTP ?>/application/system/pacientes/pacientes_admin/odontograma_paciente/js/canvas2image.js" ></script>

    <?php } ?>
</div>
