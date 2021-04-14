
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
                'prestaciones'    => array('url' => DOL_HTTP .'/application/system/configuraciones/index.php?view=form_prestaciones' , 'Active_subMod' => (isset($_GET['view']) && $_GET['view'] == 'form_prestaciones') ? 'Active_link_subM' : ''),
                'descuento'       => array('url' => DOL_HTTP .'/application/system/configuraciones/index.php?view=form_convenios_desc' , 'Active_subMod' => (isset($_GET['view']) && $_GET['view'] == 'form_convenios_desc') ? 'Active_link_subM' : ''),
                'odontolog'       => array('url' => DOL_HTTP .'/application/system/configuraciones/index.php?view=form_gestion_odontologos_especialidades&v=dentist' , 'Active_subMod' => (isset($_GET['v']) && $_GET['v'] == 'dentist') ? 'Active_link_subM' : ''),
                'especialidades'  => array('url' => DOL_HTTP .'/application/system/configuraciones/index.php?view=form_gestion_odontologos_especialidades&v=specialties' , 'Active_subMod' => (isset($_GET['v']) && $_GET['v'] == 'specialties') ? 'Active_link_subM' : ''),
                'users'           => array('url' => DOL_HTTP .'/application/system/configuraciones/index.php?view=form_gestion_odontologos_especialidades&v=users&list=true' , 'Active_subMod' => (isset($_GET['v']) && $_GET['v'] == 'users') ? 'Active_link_subM' : ''),
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


?>


<style>
    ul.sidebar-menu  li.treeview a span{
        font-size: 1.5rem;
    }
    ul.sidebar-menu a span{
        font-size: 1.5rem;
    }
</style>


<li class="header">NAVEGACIÓN</li>


<li class=" <?php if(isset($Active) && $Active =='inicio'){ echo 'disabled_link3'; } ?> hide">
    <a href="#buscarPacienteModal" data-toggle="modal"><i class="fa fa-search"></i> <span>Buscar</span></a>
</li>

<li class="<?= $Permissions_inicio['Active'] ?>">
    <a href="<?= $Permissions_inicio['url'] ?>"><i class="fa fa-dashcube"></i>
        <span>inicio</span>
    </a>
</li>

<!--<li  class="" ><a href="#"><i class="fa fa-link"></i> <span>Another Link</span></a></li>-->
<!--<li class="treeview">-->
<!--    <a href="#"><i class="fa fa-link"></i> <span>Multilevel</span>-->
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
        <i class="fa fa-list-alt"></i> <span>Agenda</span>
    </a>
</li>

<!--MODULO PACIENTES-->
<li class="treeview <?= $Permissions_pacientes['Active'] ?> " style="cursor: pointer">
    <a><i class="fa fa-users"></i> <span>Paciente</span>
        <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
        </span>
    </a>
    <ul class="treeview-menu">
        <li><a href="<?= $Permissions_pacientes['url']['directorioPaciente']['url'] ?>"  class="<?= $Permissions_pacientes['url']['directorioPaciente']['Active_subMod'] ?>" >Directorio de pacientes</a></li>
        <li><a href="<?= $Permissions_pacientes['url']['nuevoPaciente']['url'] ?>"  class="<?= $Permissions_pacientes['url']['nuevoPaciente']['Active_subMod'] ?>"    >Nuevo Paciente</a></li>
    </ul>
</li>

<!--MODULO DE DOCUMENTOS CLINICOS DE UN PACIENTE-->
<li class="<?= $Permissions_documentosClinicos['Active'] ?>"><a href="<?= $Permissions_documentosClinicos['url'] ?>"><i class="fa fa-file"></i><span>Documentos clinicos</span></a></li>

<!--MODULO CONFIGURACIONES-->
<li class="treeview <?= $Permissions_configuration['Active'] ?> <?= (!empty($Permissions_configuration['Active'])?"menu-open":"")?> " style="cursor: pointer">
    <a><i class="fa fa-wrench"></i> <span>Configuraciones</span>
        <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
        </span>
    </a>

    <!--SUB MENUS-->
    <ul class="treeview-menu" <?= (!empty($Permissions_configuration['Active'])?"style=\"display: block\"":"") ?> >
        <li><a href="<?= $Permissions_configuration['url']['principal']['url'] ?>"  class="<?= $Permissions_configuration['url']['principal']['Active_subMod'] ?>" >Principal</a></li>
        <li><a href="<?= $Permissions_configuration['url']['prestaciones']['url'] ?>"  class="<?= $Permissions_configuration['url']['prestaciones']['Active_subMod'] ?>" >Prestaciones</a></li>
        <li><a href="<?= $Permissions_configuration['url']['descuento']['url'] ?>"  class="<?= $Permissions_configuration['url']['descuento']['Active_subMod'] ?> disabled_link3" >Descuento</a></li>
        <li><a href="<?= $Permissions_configuration['url']['odontolog']['url'] ?>"  class="<?= $Permissions_configuration['url']['odontolog']['Active_subMod'] ?>" >Odontolog@</a></li>
        <li><a href="<?= $Permissions_configuration['url']['especialidades']['url'] ?>"  class="<?= $Permissions_configuration['url']['especialidades']['Active_subMod'] ?>" >Especialidades</a></li>
        <li><a href="<?= $Permissions_configuration['url']['users']['url'] ?>"  class="<?= $Permissions_configuration['url']['users']['Active_subMod'] ?>" >Users</a></li>
        <li><a href="<?= $Permissions_configuration['url']['laboratorios']['url'] ?>"  class="<?= $Permissions_configuration['url']['laboratorios']['Active_subMod'] ?>" >Laboratorios Clinicos</a></li>
    </ul>

</li>

<!--MODULO DE CAJA-->
<li class="<?= $Permissions_Cajas['Active'] ?>"><a href="<?= $Permissions_Cajas['url'] ?>"><i class="fa fa-bar-chart"></i><span>Cajas Clinicas</span></a></li>

<!--MODULO DE TAREAS CLINICAS-->
<li class="<?= $Permissions_TareasClinicas['Active'] ?>"><a href="<?= $Permissions_TareasClinicas['url'] ?>"><i class="fa fa-calendar"></i><span>Tareas Clinicas</span></a></li>