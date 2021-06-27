<style>
    .btnlogin{
        /*background: rgb(2,0,36);*/
        /*background: linear-gradient(35deg, rgba(2,0,36,1) 0%, rgba(9,9,121,0.9528186274509804) 0%, rgba(0,212,255,1) 100%);*/
        /*background: rgb(2,0,36);*/
        background: #0866a5;
    }
    input[type="text"]{
        font-size: 1.5rem;
    }
    input[type="password"]{
        font-size: 1.5rem;
    }

    label{
        font-size: 1.5rem;
    }

</style>
<!--jquery ui min js-->
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js"></script>
<div class="col-xs-12 col-md-5 col-sm-7 col-lg-4 col-centered" style="margin-top: 10%">
    <div  class="form-uic" style="width: 100%;">

        <div class="form-group col-sm-12 col-xs-12  " style="padding: 10px">

            <?php

                $url = DOL_DOCUMENT.'/application/system/login/img/odontic.beta2_sistema_odontologico_online.png';
                $base64 = base64_encode(file_get_contents($url));
                $imgbase64 = "data:image/*; base64, ".$base64;

            ?>

            <img  width="100%" id="iconLoginPrincipal" class="img-rounded center-block" src="<?= $imgbase64; ?>" alt="">

            <div class="form-group">
                <div class="col-3">
                    <label for=""> <i class="fa fa-2x fa-fw fa-user"></i> <b>username</b> </label>
                    <input class="effect-2 outlogintext text-sm"  style=" font-weight: bold; font-size: small" type="text" autocomplete="off" placeholder="username" id="usu">
                    <span class="focus-border"></span>
                </div>
                <div class="col-3">
                    <small style="color: red;" id="msg_usuario">&nbsp;</small>
                </div>
            </div>

            <div class="form-group">
                <div class="col-3">
                    <label for=""> <i class="fa fa-2x fa-fw fa-unlock-alt"></i> <b>password</b> </label>
                    <input class="effect-2 outlogintext "  style=" font-weight: bold; font-size: small" type="password" autocomplete="off" placeholder="password" id="pass">
                    <span class="focus-border"></span>
                </div>
                <div class="col-3">
                    <small style="color: red;" id="msg_password">&nbsp;</small>
                </div>
            </div>

        </div>

        <div style="width: 100%;  " >
            <input type="button" id="btn_logearse" value="LOGIN" class="btn  btnlogin"  style="width: 100%;height: 50px;   !important; font-size: 1.5rem; font-weight: bolder;  color: #ffffff; outline: none" >
        </div>

    </div>

</div>


<script>

    function logearse()
    {
        var usu  =  $('#usu').val();
        var pass = $('#pass').val();

        $("#btn_logearse").attr("value", "Redirigiendo...");

        var param = {
            'accion': 'logearse',
            'ajaxSend':'ajaxSend',
            'usua': usu,
            'pass': pass,
        };

        setTimeout(function () {
            $.ajax({
                url: "<?php echo DOL_HTTP .'/application/system/login/controller/controller_login.php'?>",
                type:'POST',
                data: param,
                dataType:'json',
                async:false,
                complete:function(xhr, status){

                    $("#btn_logearse").attr("value","LOGIN")
                },
                success:function(resp)
                {
                    if(resp.error == "SesionIniciada")
                    {
                        location.href = "<?php echo DOL_HTTP.'/index.php?view=inicio' ?>";

                    }else{

                        if(resp['msg_err']!=''){
                            Swal.fire('Información' , resp['msg_err'], 'question');
                        }else{
                            var text = " <i class='fa fa-fw fa-user'></i> Usuario: " + $('#usu').val() + " <br> " +
                                "<b>" +
                                "   <small> usuario no encontrado <br> <span class=''> compruebe la información antes de iniciar <i class='fa fa-fw fa-times-circle'></i> </span> </small>" +
                                "</b>";
                            Swal.fire('Error' , text, 'error');
                        }
                    }
                }

            });
        },1000);
    }

    $('#btn_logearse').on('click', function() {

        var $puedo = 0;

        var usu  =  $('#usu').val();
        var pass = $('#pass').val();

        if( usu == '' ) {
            $puedo++;
            $('#msg_usuario').text('Ingrese el usuario');
        }
        if( pass == '' ) {
            $puedo++;
            $('#msg_password').text('Ingrese la contraseña');
        }

        if( $puedo == 0 ){
            logearse();
        }

        setTimeout(function() {
            $('#msg_usuario').html('&nbsp');
            $('#msg_password').html('&nbsp');
        }, 2500);

    });

    $(document).ready(function() {
        var iconLoginPrincipal = $("#iconLoginPrincipal");
        console.log(iconLoginPrincipal);
    });

    //window onload
    window.onload = boxloading($('body') ,true);
    //window load
    $(window).on("load", function() {
        boxloading($('body') ,false, 1000);
    });

</script>