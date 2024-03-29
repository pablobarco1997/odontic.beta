
<link rel="stylesheet" href="css/login.css">


<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<!--<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@500&display=swap" rel="stylesheet">-->


<style>

    @font-face {
        font-family: 'AmazonEmber_Rg';
        src: local('AmazonEmber_Rg'), url('<?= DOL_HTTP ?>/public/font/AmazonEmber_Rg.ttf');
    }
    *{
        /*font-family: 'Roboto', sans-serif;*/
        /*font-size: small;*/
        font-family: 'AmazonEmber_Rg';
    }
</style>

<div class="container">

    <div class="login-content">
        <div class="form">
            <img src="img/logo.odontic.svg" style="margin-right: 30px">
            <h2 class="title" >
                <span class="title" style="color: #0866a5; font-size: 5rem; font-family: Cambria; display: inline-block; font-weight: bold">ODO</span>
                <span class="title" style="color: #3fa9f5; font-size: 5rem; font-family: Cambria; display: inline-block; font-weight: bold">NTIC</span>
                <span style="display: block; font-weight: bold" >SISTEMA CRM ODONTOLÓGICO ONLINE</span>
            </h2>
            <div class="input-div one">
                <div class="i">
                    <i class="fa fa-2x fa-user"></i>
                </div>
                <div class="div">
<!--                    <h5>usuario</h5>-->
                    <input type="text" class="input" placeholder="Usuario" style="font-size: 1.4rem" id="usu" required>
                </div>
            </div>
            <div class="input-div pass">
                <div class="i">
                    <i class="fa fa-2x fa-lock"></i>
                </div>
                <div class="div">
<!--                    <h5 >Password</h5 >-->
                    <input type="password" class="input" placeholder="Password" style="font-size: 1.4rem" id="pass" required>
                </div>
            </div>
            <a href="#" class="" style="margin: 15px 5px 0px 0px; font-size: 1.4rem">¿Olvidó su contraseña?</a>
            <input type="button" class="btn_login_style" value="Login" style="font-weight: bold; font-family: 'AmazonEmber_Rg'; " id="btn_logearse">
        </div>
    </div>
</div>


<script>
    const inputs = document.querySelectorAll(".input");


    function addcl(){
        let parent = this.parentNode.parentNode;
        parent.classList.add("focus");
    }

    function remcl(){
        let parent = this.parentNode.parentNode;
        if(this.value == ""){
            parent.classList.remove("focus");
        }
    }


    inputs.forEach(input => {
        input.addEventListener("focus", addcl);
        input.addEventListener("blur", remcl);
    });


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

        $.ajax({
            url: "<?php echo DOL_HTTP .'/application/system/login/controller/controller_login.php'?>",
            type:'POST',
            data: param,
            dataType:'json',
            async:true,
            cache:false,
            complete:function(xhr, status){
                $("#btn_logearse").attr("value","LOGIN")
            },
            success:function(resp) {
                if(resp.error == "SesionIniciada"){
                    location.href = "<?php echo DOL_HTTP.'/index.php?view=inicio' ?>";
                }else{
                    notificacion("usuario o contraseña invalida", "error");
                }
            }

        });
    }
    $('#btn_logearse').on('click', function() {
        if($('#password').val() == "" || $('#usu').val() == "" ){
            notificacion('Campos Obligatorios','question');
            return false;
        }
        logearse();
    });

    $(document).ready(function() {
        window.onload = boxloading($('body') ,true);
    });

    //window onload
    window.onload = boxloading($('body') ,true);
    //window load
    $(window).on("load", function() {
        boxloading($('body') ,true, 1000);
    });


</script>