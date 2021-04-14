
<!--MENU DE USUARIO - USUARIO LOGEADO - SESSION INICIADA X USUARIO-->

<!-- Navbar Right Menu -->
<div class="navbar-custom-menu">

    <ul class="nav navbar-nav">

        <!-- Notifications Menu -->


        <?php include_once  DOL_DOCUMENT .'/public/view/notificaciones_lib.php'?>

<!--        end notificaciones-->

        <li class="dropdown user user-menu">
            <!-- Menu Toggle Button -->
            <a href="#" class="dropdown-toggle" data-toggle="dropdown" >
                <!-- The user image in the navbar-->
                <!--<img src="< ?= $conf->PERFIL->icon ?> " class="user-image" alt="User Image">-->
                <i class="fa fa-user"></i>
                <!-- hidden-xs hides the username on small devices so only the image appears. -->
                <span class="hidden-xs hide"><?php echo $conf->PERFIL->nombre ?></span>
                <span class="hidden-xs"><?php echo $user->name ?></span>
            </a>

            <ul class="dropdown-menu">
                <!-- The user image in the menu -->
                <li class="user-header" style="background-color: #212f3d">
                <!--<img src="< ?php //echo $conf->PERFIL->icon ; ?> " class="img-circle" alt="User Image">-->
                    <i class="fa fa-4x fa-user" style="color: #f6f6f6"></i>
                    <p>
                        <?php echo $user->name ?>
                        <small>odont. <?= $conf->PERFIL->nombre .' '.$conf->PERFIL->apellido ?></small>
                    </p>
                </li>
                <!-- Menu Body -->
                <li class="user-body">
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