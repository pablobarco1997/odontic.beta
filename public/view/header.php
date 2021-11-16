<!-- Main Header -->
<header class="main-header">

    <!-- Logo -->
    <a href="#" class="logo" style="background-color: #212F3D!important;">
        <?php
            $urllogoEntity=DOL_DOCUMENT.'/logos_icon/'.$conf->NAME_DIRECTORIO.'/'.$conf->EMPRESA->INFORMACION->logo;
            if(file_exists($urllogoEntity) && !empty($conf->EMPRESA->INFORMACION->logo)){ //si existe el logo de la clinica
                $logo_entity = 'data:image/png; base64, '.base64_encode(file_get_contents(DOL_HTTP.'/logos_icon/'.$conf->NAME_DIRECTORIO.'/'.$conf->EMPRESA->INFORMACION->logo));
            }else{
                $logo_entity = 'data:image/png; base64, '.base64_encode(file_get_contents(DOL_HTTP .'/logos_icon/logo_default/icon_software_dental.png'));
            }
        ?>
        <span class="logo-lg">
            <h3 style="display: inline-block"><?= $conf->EMPRESA->INFORMACION->nombre ?></h3>&nbsp;<img width="48px" src="<?= DOL_HTTP."/logos_icon/logo_default/app_ia_odontic.svg" ?>" alt="">
        </span>
        <span class="logo-mini">
            <img width="50px" height="50px" src="<?= DOL_HTTP."/logos_icon/logo_default/app_ia_odontic.svg" ?>" alt="">
        </span>
    </a>

    <!-- Header Navbar -->
    <nav class="navbar navbar-static-top" role="navigation" style="background-color: #212F3D!important;">
        <!-- Sidebar toggle button-->
        <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
            <span class="sr-only">Toggle navigation</span>
        </a>

        <?php include_once $DOL_DOCUMENT.'/public/view/dropdown.php'; ?>

    </nav>

</header>