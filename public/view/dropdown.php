
<!--MENU DE USUARIO - USUARIO LOGEADO - SESSION INICIADA X USUARIO-->

<!-- Navbar Right Menu -->
<div class="navbar-custom-menu">

    <ul class="nav navbar-nav">

        <!-- Notifications Menu -->


        <?php include_once  DOL_DOCUMENT .'/public/view/notificaciones_lib.php'?>

        <!--end notificaciones-->

        <li class="dropdown user user-menu">
            <!-- Menu Toggle Button -->
            <a href="#" class="dropdown-toggle" data-toggle="dropdown" >
                <!-- The user image in the navbar-->
                <img src="<?= DOL_HTTP."/logos_icon/logo_default/icon_avatar.svg" ?>" class="user-image" alt="User Image">
                <!-- hidden-xs hides the username on small devices so only the image appears. -->
                <?php
                    if($conf->PERFIL->id != 0){
                        $odoctname = $conf->PERFIL->nombre.' '.$conf->PERFIL->apellido;
                    }else{
                        $odoctname = '';
                    }
                ?>
                <span class="hidden-xs hide"><?= $odoctname ?></span>
                <span class="hidden-xs"><?= $user->name ?></span>
            </a>

            <!--dropdown Menu-->
            <ul class="dropdown-menu">
                <!-- The user image in the menu -->
                <li class="user-header" style="background-color: #212f3d">
                <img src="<?= DOL_HTTP."/logos_icon/logo_default/icon_avatar.svg" ?>" class="img-circle" alt="User Image">
<!--                    <i class="fa fa-4x fa-user" style="color: #f6f6f6"></i>-->
                    <p>
                        <small style="font-weight: bolder"><?= ($conf->PERFIL->id!=0)?"Doctor(a): ".$conf->PERFIL->nombre ." ".$conf->PERFIL->apellido:"" ?></small>
                    </p>
                </li>
                <!-- Menu Body -->
                <li class="user-body hide">
                    <div class="row">

                    </div>
                    <!-- /.row -->
                </li>
                <!-- Menu Footer-->
                <li class="user-footer">
                    <div class="pull-left">
<!--                        <a href="#" class="btn btn-default btn-flat">Profile</a>-->
                    </div>
                    <div class="pull-right">
                        <a  href="#" id="modificarPerfil"  data-toggle="modal" data-target="#ModificarPerfilUsuario" class="btn btn-default btn-flat"><i class="fa fa-user"></i></a>
                        <a href="<?= DOL_HTTP.'/application/system/clinica/index.php?view=vista_clinica' ?>" class="btn btn-default btn-flat" > <i class="fa fa-hospital-o"></i> </a>
                        <a  id="cerrarSesionlink"  class="btn btn-default btn-flat"><i class="fa fa-power-off"></i></a>
                    </div>
                </li>
            </ul>
        </li>
        <!-- Control Sidebar Toggle Button -->
        <li>
            <a href="#" data-toggle="control-sidebar" class="hide"><i class="fa fa-gears"></i></a>
        </li>
    </ul>

</div>


<script src="<?php echo DOL_HTTP .'/public/js/lib_glob.js' ?>"></script>