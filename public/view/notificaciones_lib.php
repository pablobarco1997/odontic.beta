<!--NOTIFICACIONES -->

<?php //echo '<pre>';  print_r($conf->NOTIFICACIONES->Glob_Notificaciones); die();  ?>


<li class="dropdown messages-menu" >
    <!-- Menu toggle button -->
    <a href="#"  class="dropdown-toggle" data-toggle="dropdown" onclick="Notify_odontic(false, true)">
        <i class="fa fa-bell-o"></i>
        <span class="label label-warning" id="N_noti"></span>
    </a>

    <ul class="dropdown-menu " id="menuNotificacion" style="width: 400px ;">
        <li class="header">  <b id="N_Notificaciones"></b> NOTIFICACIONES  </li>


<!--        clone templante notificacion de cita-->
        <li class="star_notificaciones hide" id="star_notificaciones_cita_agendada_clone">
            <a href="#">
                <div class="pull-left" style="position: relative">
                    <img src="<?= DOL_HTTP.'/logos_icon/logo_default/avatar_none.ico' ?>" class="img-circle notify_cita_img_paciente" title="Paciente" style="width: 70px; height: 70px; " alt="Paciente">
                    <i style="
                                position: absolute;
                                border-radius: 100%;
                                top: 40px;
                                right: 15px;
                                background-image: url('https://static.xx.fbcdn.net/rsrc.php/v3/yj/r/YIUMZFrXRCb.png?_nc_eui2=AeHVYdxh1DhAMVT3yIQmiSdsTUg3UAarw9lNSDdQBqvD2XIDaRNRfwJosU1s70CS1JgiCUWqb-gIhUQdE0DPY1zb');
                                background-position: 0px -319px;
                                background-size: auto;
                                width: 28px;
                                height: 28px;
                                background-repeat: no-repeat;
                                display: inline-block;
                                box-shadow: 1px 1px 2px 0.01em #ecf0f5;
                                    "></i>
                </div>
                <table style="width: 260px ">
                    <tr>
                        <td ><b>Cita <span class="notify_cita_numero">0025</span> &nbsp;&nbsp;<span class="notify_cita_dateff">2021/05/01</span> </b>
                            <small style="color: #888888; float: right"> <i class="fa fa-clock-o"></i> <span class="notify_cita_minutos">5 min</span> </small>
                        </td>
                    </tr>
                </table>
                <p class="notify_cita_encargado">Encargado: Juan Jose Molorem Lomss</p>
                <p class="notify_cita_paciente">Paciente: Pablo Barco Marquez</p>
                <table style="width: 280px ">
                    <tr><td><p class="notify_cita_estado no-margin" style="color: #888888">Estado actual de la cita</p></td></tr>
                </table>
                <p> <i class="fa fa-clock-o"></i> <span class="notify_cita_horaIniFin">Hora: 13:15:00 h 14:00:00</span></p>
            </a>
        </li>

<!--        clone templante notificacion de confirmacion via email-->
        <li class="star_notificaciones hide" id="star_noti_email_confirmacion_paciente">
            <a href="#">
                <div class="pull-left" style="position: relative">
                    <img src="<?= DOL_HTTP.'/logos_icon/logo_default/avatar_none.ico' ?>" class="img-circle notify_cita_img_paciente"  style="width: 70px; height: 70px; " >
                    <i style="
                                position: absolute;
                                border-radius: 100%;
                                top: 40px;
                                right: 15px;
                                background-image: url('https://static.xx.fbcdn.net/rsrc.php/v3/yj/r/YIUMZFrXRCb.png?_nc_eui2=AeHVYdxh1DhAMVT3yIQmiSdsTUg3UAarw9lNSDdQBqvD2XIDaRNRfwJosU1s70CS1JgiCUWqb-gIhUQdE0DPY1zb');
                                background-position: 0px -319px;
                                background-size: auto;
                                width: 28px;
                                height: 28px;
                                background-repeat: no-repeat;
                                display: inline-block;
                                box-shadow: 1px 1px 2px 0.01em #ecf0f5;
                                    "></i>
                </div>
                <table style="width: 250px ">
                    <tr>
                        <td>
                            <b>Confirmación vía Email</b>
                        </td>
                    </tr>
                </table>
                <table style="width: 280px ">
                    <tr>
                        <td>
                            <p class="notify_confirm_label no-margin" style="color: #888888">Paciente Pablo barco confirma que asistira a la cita #230</p>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <a href="#" data-id="" class="btn btn-xs btnhover" style="color: #2e5ac7; color: blue" onclick="to_accept_noti_confirmpacient($(this))" title="visto"><i class="fa fa-2x fa-eye-slash"></i></a>
                        </td>
                    </tr>
                </table>
            </a>
        </li>




<!--    lista de notificaciones-->
        <li>
            <ul class="menu " id="noti_list">
            </ul>
        </li>

        <li class="footer"><a href="#" class="btnhover">Mostrar Más</a></li>
    </ul>

</li>