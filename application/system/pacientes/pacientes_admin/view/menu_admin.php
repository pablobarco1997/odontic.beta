<?php


            $array_datos_personales = (object)[
               'url'     =>  DOL_HTTP .'/application/system/pacientes/pacientes_admin/index.php?view=dop&key='.KEY_GLOB.'&id='.tokenSecurityId($idPaciente) ,
               'active'  =>  ($VISTAS == "dop") ? "ActivaLista" : "",
               'permiso' => ''
            ];

            $array_imagenes_archivos = (object)[
                'url'     =>  DOL_HTTP .'/application/system/pacientes/pacientes_admin/index.php?view=arch&key='.KEY_GLOB.'&id='.tokenSecurityId($idPaciente).'' ,
                'active'  =>  ($VISTAS == "arch") ? "ActivaLista" : "",
                'url_disabled'    => '#',
                'permiso'         => ''
            ];

            $array_plan_tratamiento = (object)[
                'url'     =>  DOL_HTTP .'/application/system/pacientes/pacientes_admin/index.php?view=plantram&key='.KEY_GLOB.'&id='.tokenSecurityId($idPaciente).'' ,
                'active'  =>  ($VISTAS == "plantram") ? "ActivaLista" : "",
                'permiso' => ''
            ];

            $array_odontograma = (object)[
                'url'     =>  DOL_HTTP .'/application/system/pacientes/pacientes_admin/index.php?view=odot&key='.KEY_GLOB.'&id='.tokenSecurityId($idPaciente).'&v=listp' ,
                'active'  =>  ($VISTAS == "odot") ? "ActivaLista" : "",
                'permiso' => ''
            ];

            $array_citas_asociadas = (object)[
                'url'     =>   DOL_HTTP .'/application/system/pacientes/pacientes_admin/index.php?view=citasoci&key='.KEY_GLOB .'&id='.tokenSecurityId($idPaciente),
                'active'  =>  ($VISTAS == "citasoci") ? "ActivaLista" : "",
                'permiso' => ''
            ];

            $array_documentAsociado = (object)[
                'url'     =>  DOL_HTTP .'/application/system/pacientes/pacientes_admin/index.php?view=docummclin&key='.KEY_GLOB .'&id='.tokenSecurityId($idPaciente).'&v=listdocumment',
                'active'  =>  ($VISTAS == "docummclin") ? "ActivaLista" : "",
                'permiso' => ''
            ];

            $array_Pagos_pacientes = (object)[
                'url'     => DOL_HTTP.'/application/system/pacientes/pacientes_admin/index.php?view=pagospaci&key='.KEY_GLOB.'&id='.tokenSecurityId($idPaciente).'&v=paym',
                'active'  =>  ($VISTAS == "pagospaci") ? "ActivaLista" : "",
                'permiso' => ''
            ];

            $array_evoluciones = (object)[
                'url'     => DOL_HTTP.'/application/system/pacientes/pacientes_admin/index.php?view=evoluc&key='.KEY_GLOB.'&id='.tokenSecurityId($idPaciente).'&v=list_evul',
                'active'  =>  ($VISTAS == "evoluc") ? "ActivaLista" : "",
                'permiso' => ''
            ];

            $array_PagosRealizados = (object)[
                'url'     => DOL_HTTP.'/application/system/pacientes/pacientes_admin/index.php?view=pagrealipricp&key='.KEY_GLOB.'&id='.tokenSecurityId($idPaciente).'&v=pagospartic',
                'active'  =>  ($VISTAS == "pagrealipricp") ? "ActivaLista" : "",
                'permiso' => ''
            ];

            $array_email_list = (object)[
                'url'     => DOL_HTTP.'/application/system/pacientes/pacientes_admin/index.php?view=mail&key='.KEY_GLOB.'&id='.tokenSecurityId($idPaciente).'&v=listpmail',
                'active'  =>  ($VISTAS == "mail") ? "ActivaLista" : "",
                'permiso' => ''
            ];

            $array_coment_pacient = (object)[
                'url'     => DOL_HTTP.'/application/system/pacientes/pacientes_admin/index.php?view=commp&key='.KEY_GLOB.'&id='.tokenSecurityId($idPaciente),
                'active'  =>  ($VISTAS == "commp") ? "ActivaLista" : "",
                'permiso' => ''
            ];

            $array_email_contrap = (object)[
                'url'     => DOL_HTTP.'/application/system/pacientes/pacientes_admin/index.php?view=programa_email&key='.KEY_GLOB.'&id='.tokenSecurityId($idPaciente).'&v=emails_program',
                'active'  =>  ($VISTAS == "programa_email") ? "ActivaLista" : "",
                'permiso' => ''
            ];


?>


<style>
    .listItem li{
        margin-bottom: 5px;
    }

    .lista{
        display: block;
        padding: 3px;
        color: black;
    }
    .lipaddi:hover{
        background-color: #202d3b;
        color: #ffffff;
        display: block;
    }
    .lipaddi:hover a{
        color: #ffffff;
    }
    .ActivaLista a{
        background-color: #202d3b;
        color: #ffffff;
        display: block;
    }

</style>

<!-- Modal -->
<div class="modal fade" id="menu_admin" role="dialog">
    <div class="modal-dialog modal-sm">

        <!-- Modal content-->
    <div class="modal-content">

            <div class="modal-header modal-diseng">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><span>Navegación</span></h4>
            </div>

            <div class="modal-body" style="padding-bottom: 0px; padding-top: 0px; background-color: #ffffff;">
                <div class="row">
                    <div class="col-md-12 col-xs-12" style="padding: 0px">
                        <div style="background-color: #ffffff; border-radius: 0px;" id="menus_admin">
                            <ul style="width: 100%;list-style: none;padding-left: 0px;padding: 15px;margin-bottom: 0px;" class="listItem">
                                <li>
                                    <div style="width: 100%">
                                        <p class="text-center"><i class="fa fa-4x fa-user"></i></p>

                                        <p class="text-center" id="nav_paciente_admin_nomb" style="font-weight: bold"><?= getnombrePaciente($idPaciente)->nombre.' '. getnombrePaciente($idPaciente)->apellido ?></p>
                                        <hr style="margin: 2px" >
                                    </div>
                                </li>

                                <li class="lipaddi <?= $array_datos_personales->active ?>">
                                    <a class="lista" href="<?= $array_datos_personales->url ?>">&nbsp;&nbsp;
                                        <i class="fa fa-user"></i>&nbsp;&nbsp; <b>Datos Personales</b> </a>
                                </li>

                                <li class="lipaddi hide">
                                    <a class="lista" href="">&nbsp;&nbsp;
                                        <i class="fa fa-link"></i>&nbsp;&nbsp; <b>Carga Familiares</b> </a>
                                </li>

                                <li class="lipaddi disabled_link3 <?= $array_imagenes_archivos->active ?>">
                                    <a class="lista disabled_link3" disabled="disabled" href="<?= $array_imagenes_archivos->url_disabled ?>">&nbsp;&nbsp;
                                        <i class="fa fa-folder"></i>&nbsp;&nbsp; <b>administrador de Ficheros</b> </a>
                                </li>

                                <li class="lipaddi <?= $array_citas_asociadas->active ?>">
                                    <a class="lista" href="<?= $array_citas_asociadas->url ?>">&nbsp;&nbsp;
                                        <i class="fa fa-calendar"></i>&nbsp;&nbsp; <b>Citas Asociadas</b> </a>
                                </li>

                                <li class="lipaddi <?= $array_email_list->active ?>">
                                    <a class="lista" href="<?= $array_email_list->url ?>">&nbsp;&nbsp;
                                        <i class="fa fa-envelope"></i>&nbsp;&nbsp; <b>E-mail Asociados</b> </a>
                                </li>

                                <li class="lipaddi <?=  $array_coment_pacient->active ?>">
                                    <a class="lista" href="<?= $array_coment_pacient->url ?>">&nbsp;&nbsp;
                                        <i class="fa fa-comment-o"></i>&nbsp;&nbsp; <b>Comentarios administrativos</b> </a>
                                </li>

                                <li class="lipaddi disabled_link3 <?=  $array_email_contrap->active ?>">
                                    <a class="lista disabled_link3" href="<?= $array_email_contrap->url ?>">&nbsp;&nbsp;
                                        <i class="fa fa-calendar-check-o"></i>&nbsp;&nbsp; <b>Programar Email</b> </a>
                                </li>

                                <!--CLINICO-->
                                <li>
                                    <hr style="margin: 0px; margin-bottom: 4px">
                                    <p class="text-center" style="font-weight: bold"><b>CLINICO</b></p>
                                </li>

                                <li class="lipaddi <?= $array_plan_tratamiento->active ?>"> <!--Plande tratamiento-->
                                    <a class="lista" href="<?= $array_plan_tratamiento->url; ?>">&nbsp;&nbsp;
                                        <i class="fa fa-list-ul"></i>&nbsp;&nbsp; <b>Planes de Tratamiento</b> </a>
                                </li>

                                <li class="lipaddi <?= $array_evoluciones->active ?>"><a class="lista" href="<?= $array_evoluciones->url;  ?>">&nbsp;&nbsp;
                                        <i class="fa fa-link"></i>&nbsp;&nbsp; <b>Evoluciones</b> </a>
                                </li>

                                <!--ocultar Documentos clinicos-->
                                <li class="lipaddi hide <?= $array_documentAsociado->active ?>">
                                    <a class="lista" href="<?= $array_documentAsociado->url ; ?>">&nbsp;&nbsp;
                                        <i class="fa fa-file"></i>&nbsp;&nbsp; <b>Documentos Clinicos Asociados</b> </a>
                                </li>

                                <li class="lipaddi <?= $array_odontograma->active ?>"><a class="lista" href="<?= $array_odontograma->url; ?>">&nbsp;&nbsp;
                                        <img  src="<?= DOL_HTTP ?>/logos_icon/logo_default/tooth-solid.svg" width="14px" height="14px" alt="">&nbsp;
                                        <b>Odontograma</b> </a>
                                </li>

                                <!--PAGOS-->
                                <li>
                                    <hr style="margin: 0px; margin-bottom: 4px">
                                    <p class="text-center" style="font-weight: bold"><b>FACTURACIÓN</b></p>
                                </li>

                                <li class="lipaddi <?= $array_Pagos_pacientes->active ?>">
                                    <a class="lista" href="<?= $array_Pagos_pacientes->url ; ?>">&nbsp;&nbsp;
                                        <i class="fa fa-shopping-cart"></i>&nbsp;&nbsp; <b>Recaudaciones</b>  </a>
                                </li>

                                <li class="lipaddi <?= $array_PagosRealizados->active ?>">
                                    <a class="lista" href="<?= $array_PagosRealizados->url ; ?>">&nbsp;&nbsp;
                                        <i class="fa fa-briefcase"></i>&nbsp;&nbsp; <b>Pagos Realizados</b>  </a>
                                </li>

                            </ul>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
