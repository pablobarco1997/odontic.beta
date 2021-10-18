
<?php

accessoModule('Pagos de Paciente');

$accion = "";
if(isset($_GET['v']) && $_GET['v'] == 'pagos'){
    $accion = "pagos";
}

?>

<script>
    $accionPagospacientes = "<?= $accion ?>";
</script>

<div class="form-group col-md-12 col-xs-12">
    <label for="">LISTA DE COMPORTAMIENTOS</label>
    <ul class="list-inline" style="border-bottom: 0.6px solid #333333; padding: 3px; background-color: #f4f4f4; margin-left: 0px">
        <li><a href="#FiltrarPagoPacientes" data-toggle="collapse" style="color: #333333" class="btnhover btn btn-sm " > <b>   ▼  Filtrar  </b> </a> </li>
        <li>
            <a href="<?= DOL_HTTP .'/application/system/pacientes/pacientes_admin/?view=pagos_pacientes&key='.KEY_GLOB.'&id='. tokenSecurityId($idPaciente) .'&v=pagos' ?>" style="color: #333333" class="btnhover btn btn-sm " id="">
                <b>  Pagos  </b> </a>
        </li>
        <li>
            <a href="#" style="color: #333333" class="btnhover btn btn-sm PagosDetallados" readonly="" id="" onclick="Export($(this))">
                <b> <i class="fa fa-print"></i>  Pagos Detallados </b> </a>
        </li>
        <li>
            <a href="<?= DOL_HTTP .'/application/system/pacientes/pacientes_admin/?view=pagos_pacientes&key='.KEY_GLOB.'&id='. tokenSecurityId($idPaciente) .'&v=' ?>" style="color: #333333" class="btnhover btn btn-sm disabled_link3" disabled="disabled" readonly="" id="">
                <b>   Pagos por Financiamientos </b> </a>
        </li>
    </ul>
</div>


<!-- PAGOS PARTICULARES DEL PACIENTE -->
<?php

switch ($accion){
    case 'pagos':
        require_once 'pagos_pacientes.php';
        break;
    default:
        print "<div class='form-group col-md-3 col-xs-12'> <h4>No se encontro la vista</h4> </div>  ";
        die();
        break;
}

?>
