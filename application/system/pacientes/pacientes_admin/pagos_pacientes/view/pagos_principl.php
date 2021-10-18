
<?php

$accionPag = "";

if(isset($_GET['v'])){

    if($_GET['v'] == 'paym'){
        $accionPag = "pagos_independientes";
    }

    if($_GET['v'] == 'paym_pay'){
        $accionPag = "cobros_independientes";
    }

}else{
    echo "Error de parámetros de entrada Consulte con Soporte";
    die();
}


if(!PermitsModule('Recaudaciones', 'consultar')){
    $consultar = "disabled_link3";
}else{
    $consultar = "";
}

?>

<script>
    $accionPagos = "<?= $accionPag ?>";
</script>



<div class="form-group col-xs-12 col-md-12">

    <?php  accessoModule('Recaudaciones'); ?>

    <div class="form-group col-md-12 col-xs-12">
        <label for="">LISTA DE COMPORTAMIENTOS</label>
        <ul class="list-inline" style="border-bottom: 0.6px solid #333333; padding: 3px; background-color: #f4f4f4; margin-left: 0px">
            <?php
                if($_GET['v']=="paym"){
                    print "            <li> <a data-toggle=\"collapse\" data-target=\"#contentFilter\" class=\"btnhover btn  $consultar btn-sm \" style=\"color: #333333\" aria-expanded=\"true\"> <b>   ▼  Filtrar  </b>  </a> </li>";
                }
            ?>
            <li> <a href="<?= DOL_HTTP .'/application/system/pacientes/pacientes_admin/?view=pagospaci&key='.KEY_GLOB.'&id='. tokenSecurityId($idPaciente) .'&v=paym' ?>" style="color: #333333" class="btnhover btn btn-sm <?= $consultar ?> " id="" title="Recaudar Planes de Tratamientos"> <b>Recaudar Planes de Tratamientos</b> </a></li>
            <li> <a href="<?= DOL_HTTP .'/application/system/pacientes/pacientes_admin/?view=pagospaci&key='.KEY_GLOB.'&id='. tokenSecurityId($idPaciente) .'&v=paym_financier' ?>" style="color: #333333" class="btnhover btn btn-sm disabled_link3" disabled="disabled" readonly="" id=""> <b>  Cargo por Financiamiento</b> </a></li>
        </ul>
    </div>


    <?php

        #Maneja dos vista lista de pagos donde muestra los planes de tratamientos => paym
        if(isset($_GET['v'])){
            #Maneja dos vista lista de las prestaciones realizadas de ese planes de tratamientos => paym_pay
            if($_GET['v'] == 'paym' || $_GET['v'] == 'paym_pay'){
                include_once 'pagos_independientes.php';
            }

            if($_GET['v'] == 'paym_financier'  ){
                include_once 'pagos_financieros.php';
            }

            if( $_GET['v'] != 'paym' && $_GET['v'] != 'paym_financier' && $_GET['v'] != 'paym_pay'){
                echo '<h1 style="color: red">Ocurrio un error no se encontro la vista a consultar - <b>NO TIENE ACCESO A ESTA VISTA</b></h1>';
                die();
            }
        }

    ?>

</div>

<script>
    function Get_jquery_URL(Getparam) {
        let paramsGet = new URLSearchParams(location.search);
        var idGetUrl = paramsGet.get(Getparam);
        return idGetUrl;
    }

    $(window).on('load', function () {

        if(!ModulePermission('Recaudaciones', 'consultar')){
            notificacion('Ud. No tiene permiso para Consultar Información', 'error');
            return false;
        }

    });

</script>