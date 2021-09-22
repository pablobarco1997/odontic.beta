
<?php

    $Permissions_inicio = array(
        'url'     => DOL_HTTP .'/index.php?view=inicio',
        'Active'  => ( isset($Active) && $Active == 'inicio') ? 'Active_link' : '',
        'permiso' => '',
    );

    $Permissions_agenda = array(
        'url'     => DOL_HTTP.'/application/system/agenda/index.php?view=principal&list=diaria',
        'Active'  => ( isset($Active) && $Active == 'agenda') ? 'Active_link' : '',
        'permiso' => '',
    );

    //pacientes con submodulos
    $Permissions_pacientes = array(
        'url'     => array(
                'directorioPaciente' => array('url'           => DOL_HTTP.'/application/system/pacientes/directorio_paciente/index.php?view=directorio', 'Active_subMod' => ( isset($_GET['view']) && $_GET['view'] == 'directorio') ? 'Active_link_subM' : '' ,) ,
                'nuevoPaciente'      => array('url' => DOL_HTTP.'/application/system/pacientes/nuevo_paciente/index.php?view=nuev_paciente' , 'Active_subMod' => ( isset($_GET['view']) && $_GET['view'] == 'nuev_paciente') ? 'Active_link_subM' : '' ,) ,
        ),

        'Active'  => ( isset($Active) && $Active == 'pacientes') ? 'Active_link' : '',
        'permiso' => '',
    );

    //configuraciones
    $Permissions_configuration = array(
        'url'     => array(
                'principal'       => array('url' => DOL_HTTP .'/application/system/configuraciones/index.php?view=principal' , 'Active_subMod' => (isset($_GET['view']) && $_GET['view'] == 'principal') ? 'Active_link_subM' : ''),
                'prestaciones'    => array('url' => DOL_HTTP .'/application/system/configuraciones/index.php?view=servicios&v=list' , 'Active_subMod' => (isset($_GET['view']) && $_GET['view'] == 'servicios') ? 'Active_link_subM' : ''),
                'descuento'       => array('url' => DOL_HTTP .'/application/system/configuraciones/index.php?view=form_convenios_desc' , 'Active_subMod' => (isset($_GET['view']) && $_GET['view'] == 'form_convenios_desc') ? 'Active_link_subM' : ''),
                'odontolog'       => array('url' => DOL_HTTP .'/application/system/configuraciones/?view=odontologos&v=list' , 'Active_subMod' => (isset($_GET['view']) && $_GET['view'] == 'odontologos') ? 'Active_link_subM' : ''),
                'especialidades'  => array('url' => DOL_HTTP .'/application/system/configuraciones/index.php?view=especialidades&v=list' , 'Active_subMod' => (isset($_GET['view']) && $_GET['view'] == 'especialidades') ? 'Active_link_subM' : ''),
                'users'           => array('url' => DOL_HTTP .'/application/system/configuraciones/?view=admin_users&v=list' , 'Active_subMod' => (isset($_GET['view']) && $_GET['view'] == 'admin_users') ? 'Active_link_subM' : ''),
                'laboratorios'    => array('url' => DOL_HTTP .'/application/system/configuraciones/index.php?view=form_laboratorios_conf&v=laboratorios' , 'Active_subMod' => (isset($_GET['view']) && $_GET['view'] == 'form_laboratorios_conf') ? 'Active_link_subM' : ''),
        ),
        'Active'  => (isset($Active)  && $Active == 'configuraciones') ? 'Active_link' : '' ,
        'permiso' => '',
    );

    //documentos clinicos
    $Permissions_documentosClinicos = array(
        'url'     =>  DOL_HTTP .'/application/system/documentos_clinicos/index.php?view=listdocumment' ,
        'Active'  => (isset($Active)  && $Active == 'documento_clinicos') ? 'Active_link' : '' ,
        'permiso' => '',
    );


    //Modulo de Cajas
    $Permissions_Cajas = array(
        'url'     =>  DOL_HTTP .'/application/system/cajas/index.php?view=principal_cajas' ,
        'Active'  => (isset($Active)  && $Active == 'module_cajas') ? 'Active_link' : '' ,
        'permiso' => '',
    );

    //Modulo de Tareas Clinicas
    $Permissions_TareasClinicas = array(
        'url'     =>  DOL_HTTP .'/application/system/tareas_clinicas/index.php?view=tareas' ,
        'Active'  => (isset($Active)  && $Active == 'module_tareas') ? 'Active_link' : '' ,
        'permiso' => '',
    );


    //Modulo Operaciones
    $Permissions_Operaciones = array(
        'url'     => array(
            'declaracion_cuentas'       => array('url' => DOL_HTTP .'/application/system/operacion/declaracion_cuentas/index.php?view=all_cuentas&key='.KEY_GLOB , 'Active_subMod' => (isset($Active) && $Active == 'Declaracion_Cuentas') ? 'Active_link_subM' : ''),
            'cajas_clinicas'            => array('url' => DOL_HTTP .'/application/system/operacion/cajas_clinicas/index.php?view=all_cajas_clinicas&key='.KEY_GLOB , 'Active_subMod' => (isset($Active) && $Active == 'cajas_clinicas') ? 'Active_link_subM' : ''),
            'gastos'                    => array('url' => DOL_HTTP .'/application/system/operacion/gastos/index.php?view=listgatos&key='.KEY_GLOB , 'Active_subMod' => (isset($Active) && $Active == 'gastos') ? 'Active_link_subM' : ''),
            'Transacciones_Clinicas'    => array('url' => DOL_HTTP .'/application/system/operacion/transacciones_clinicas/index.php?view=transacciones_clinicas&key='.KEY_GLOB , 'Active_subMod' => (isset($_GET['view']) && $_GET['view'] == 'transacciones_clinicas') ? 'Active_link_subM' : ''),
            'crear_transaccion_clinica' => array('url' => DOL_HTTP .'/application/system/operacion/transacciones_clinicas/index.php?view=list_transacc_creadas&key='.KEY_GLOB , 'Active_subMod' => (isset($_GET['view']) && ($_GET['view'] == 'list_transacc_creadas' || $_GET['view'] == 'crear_transaccion')) ? 'Active_link_subM' : ''),
        ),
        'Active'  => (isset($Active)  && $Active == 'Declaracion_Cuentas') ? 'Active_link' : '' ,
        'permiso' => '',
    );

?>


<?php
    $stylefontSmall = 'style="font-size: small; padding-top: 16px" ';
?>

<li class="header">NAVEGACIÓN</li>


<li class=" <?php if(isset($Active) && $Active =='inicio'){ echo 'disabled_link3'; } ?> hide">
    <a href="#buscarPacienteModal" data-toggle="modal"><i class="fa fa-search"></i> <span >Buscar</span></a>
</li>

<li class="<?= $Permissions_inicio['Active'] ?>">
    <a href="<?= $Permissions_inicio['url'] ?>"><i class="fa fa-dashcube"></i>
        <span <?= $stylefontSmall ?>  <?= $stylefontSmall ?>>Inicio</span>
    </a>
</li>

<!--<li  class="" ><a href="#"><i class="fa fa-link"></i> <span>Another Link</span></a></li>-->
<!--<li class="treeview">-->
<!--    <a href="#"><i class="fa fa-calendar"></i> <span>Multilevel</span>-->
<!--        <span class="pull-right-container">-->
<!--                <i class="fa fa-angle-left pull-right"></i>-->
<!--              </span>-->
<!--    </a>-->
<!--    <ul class="treeview-menu">-->
<!--        <li><a href="#">Link in level 2</a></li>-->
<!--        <li><a href="#">Link in level 2</a></li>-->
<!--    </ul>-->
<!---->
<!--</li>-->


<!--MODULO AGENDA-->
<li class="<?= $Permissions_agenda['Active'] ?>">
    <a href="<?= $Permissions_agenda['url'] ?>">
        <i class="fa fa-list-alt"></i><span   <?= $stylefontSmall ?>>Agenda</span>
    </a>
</li>

<!--MODULO PACIENTES-->
<li class="treeview <?= $Permissions_pacientes['Active'] ?> " style="cursor: pointer">
    <a><i class="fa fa-users"></i> <span <?= $stylefontSmall ?>>Paciente</span>
        <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
        </span>
    </a>
    <ul class="treeview-menu">
        <li><a href="<?= $Permissions_pacientes['url']['directorioPaciente']['url'] ?>"  class="<?= $Permissions_pacientes['url']['directorioPaciente']['Active_subMod'] ?>" <?= $stylefontSmall ?>>Directorio de pacientes</a></li>
        <li><a href="<?= $Permissions_pacientes['url']['nuevoPaciente']['url'] ?>"  class="<?= $Permissions_pacientes['url']['nuevoPaciente']['Active_subMod'] ?>"    <?= $stylefontSmall ?>>Nuevo Paciente</a></li>
    </ul>
</li>

<!--MODULO DE DOCUMENTOS CLINICOS DE UN PACIENTE-->
<li class="<?= $Permissions_documentosClinicos['Active'] ?>"><a href="<?= $Permissions_documentosClinicos['url'] ?>"><i class="fa fa fa-briefcase"></i><span <?= $stylefontSmall ?>>Documentos clinicos</span></a></li>

<!--MODULO DE TAREAS CLINICAS-->
<li class="<?= $Permissions_TareasClinicas['Active'] ?> hidden"><a href="<?= $Permissions_TareasClinicas['url'] ?>"><i class="fa fa-calendar"></i><span <?= $stylefontSmall ?> >Tareas Clinicas</span></a></li>

<!--MODULO CONFIGURACIONES-->
<li class="treeview <?= $Permissions_configuration['Active'] ?> <?= (!empty($Permissions_configuration['Active'])?"menu-open":"")?> " style="cursor: pointer">
    <a><i class="fa fa-wrench"></i> <span <?= $stylefontSmall ?> >Configuraciones</span>
        <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
        </span>
    </a>

    <!--SUB MENUS-->
    <ul class="treeview-menu" <?= (!empty($Permissions_configuration['Active'])?"style=\"display: block\"":"") ?> >
        <li class="hidden"><a href="<?= $Permissions_configuration['url']['principal']['url'] ?>"  class="<?= $Permissions_configuration['url']['principal']['Active_subMod'] ?>"  <?= $stylefontSmall ?>> Configuraciones Clinicas</a></li>
        <li><a href="<?= $Permissions_configuration['url']['prestaciones']['url'] ?>"  class="<?= $Permissions_configuration['url']['prestaciones']['Active_subMod'] ?>"  <?= $stylefontSmall ?>>Prestaciones/Servicios</a></li>
        <li class="hidden"><a href="<?= $Permissions_configuration['url']['descuento']['url'] ?>"  class=" <?= $Permissions_configuration['url']['descuento']['Active_subMod'] ?> disabled_link3"  <?= $stylefontSmall ?>>Descuento</a></li>
        <li><a href="<?= $Permissions_configuration['url']['odontolog']['url'] ?>"  class="<?= $Permissions_configuration['url']['odontolog']['Active_subMod'] ?>"  <?= $stylefontSmall ?>>Odontologos</a></li>
        <li><a href="<?= $Permissions_configuration['url']['especialidades']['url'] ?>"  class="<?= $Permissions_configuration['url']['especialidades']['Active_subMod'] ?>"  <?= $stylefontSmall ?>>Especialidades</a></li>
        <li><a href="<?= $Permissions_configuration['url']['users']['url'] ?>"  class="<?= $Permissions_configuration['url']['users']['Active_subMod'] ?>"  <?= $stylefontSmall ?>>  Usuarios</a></li>
        <li><a href="<?= $Permissions_configuration['url']['laboratorios']['url'] ?>"  class="<?= $Permissions_configuration['url']['laboratorios']['Active_subMod'] ?>"  <?= $stylefontSmall ?>>Laboratorios Clinicos</a></li>
    </ul>

</li>

<!--MODULO DE CAJA-->
<li class="<?= $Permissions_Cajas['Active'] ?> hide"><a href="<?= $Permissions_Cajas['url'] ?>"><i class="fa fa-bar-chart"></i><span <?= $stylefontSmall ?> >Cajas Clinicas</span></a></li>



<!--MODULO OPERACIONES-->
<li class="treeview <?= $Permissions_Operaciones['Active'] ?> <?= (!empty($Permissions_Operaciones['Active'])?"menu-open":"")?> " style="cursor: pointer">
    <a><i class="fa fa-bar-chart"></i> <span <?= $stylefontSmall ?>>Operaciones</span>
        <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
        </span>
    </a>

    <!--SUB MENUS-->
    <ul class="treeview-menu" <?= (!empty($Permissions_Operaciones['Active'])?"style=\"display: block\"":"") ?> >
        <li><a href="<?= $Permissions_Operaciones['url']['declaracion_cuentas']['url'] ?>"  class="<?= $Permissions_Operaciones['url']['declaracion_cuentas']['Active_subMod'] ?>" <?= $stylefontSmall ?> >Declarar Cuentas</a></li>
        <li><a href="<?= $Permissions_Operaciones['url']['cajas_clinicas']['url'] ?>"  class="<?= $Permissions_Operaciones['url']['cajas_clinicas']['Active_subMod'] ?>" <?= $stylefontSmall ?> >Cajas Clinicas</a></li>
        <li><a href="<?= $Permissions_Operaciones['url']['gastos']['url'] ?>"  class="<?= $Permissions_Operaciones['url']['gastos']['Active_subMod'] ?>" <?= $stylefontSmall ?> >Gastos</a></li>
        <li><a href="<?= $Permissions_Operaciones['url']['Transacciones_Clinicas']['url'] ?>"  class="<?= $Permissions_Operaciones['url']['Transacciones_Clinicas']['Active_subMod'] ?>" <?= $stylefontSmall ?> >Transacciones Clinicas</a></li>
        <li><a href="<?= $Permissions_Operaciones['url']['crear_transaccion_clinica']['url'] ?>"  class="<?= $Permissions_Operaciones['url']['crear_transaccion_clinica']['Active_subMod'] ?>" <?= $stylefontSmall ?> >Crear Transaccion Clinica</a></li>
    </ul>

</li>