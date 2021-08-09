

<li class="dropdown messages-menu" >
    <!-- Menu toggle button -->
    <a href="#"  class="dropdown-toggle" data-toggle="dropdown" onclick="Notify_odontic(false, true)">
        <i class="fa fa-bell-o"></i>
        <span class="label label-warning" id="N_noti"></span>
    </a>

    <ul class="dropdown-menu " id="menuNotificacion" style="width: 400px ;">
        <li class="header">  <b id="N_Notificaciones"></b> NOTIFICACIONES  </li>

        <li class="star_notificaciones_cargando" id="star_notificaciones_cargando">
            <a href="#">cargando...</a>
        </li>

        <li class="start_noti_vacio hide" id="start_noti_vacio">
            <a href="#">Ningún dato disponible</a>
        </li>

        <!--        clone templante notificacion de cita-->
        <li class="star_notificaciones hide" id="star_notificaciones_cita_agendada_clone">
            <a href="#">
                <div class="pull-left" style="position: relative">
                    <img src="<?= DOL_HTTP.'/logos_icon/logo_default/icon_avatar.svg' ?>" class="img-circle notify_cita_img_paciente" title="Paciente" style="width: 70px; height: 70px; " alt="Paciente">
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
                    <tr><td><p class="text-right no-margin" style="color: #888888"> <i class="fa fa-clock-o"></i> <span class="notify_cita_horaIniFin " >Hora: 13:15:00 h 14:00:00</span></p></td></tr>
                    <tr><td><a href="#" data-id="" data-type="cita_agendada" id="notify_cita_visto" class="notify_cita_visto btn btn-xs text-sm btnhover" style="color: #2e5ac7; color: #2e5ac7" title="visto" onclick="to_accept_noti_confirmpacient($(this))"><i class="fa fa-2x fa-eye-slash"></i></a></td></tr>
                </table>
            </a>
        </li>

        <!--        clone templante notificacion de confirmacion via email-->
        <li class="star_notificaciones hide" id="star_noti_email_confirmacion_paciente">
            <a href="#">
                <div class="pull-left" style="position: relative">
                    <img src="<?= DOL_HTTP.'/logos_icon/logo_default/icon_avatar.svg' ?>" class="img-circle notify_cita_img_paciente"  style="width: 70px; height: 70px; " >
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
                            <a href="#" data-id="" data-type="confirmacion_email_cita" id="notify_confirm_email_visto" class="btn btn-xs btnhover notify_confirm_email_visto" style="color: #2e5ac7; color: #2e5ac7" onclick="to_accept_noti_confirmpacient($(this))" title="visto"><i class="fa fa-2x fa-eye-slash"></i></a>
                        </td>
                    </tr>
                </table>
            </a>
        </li>



        <!--    lista de notificaciones-->
        <li>
            <ul class="menu " id="noti_list" style="max-height: 300px !important;">
            </ul>
        </li>

<!--        <li class="footer"><a href="#" class="btnhover">Mostrar Más</a></li>-->
    </ul>

</li>