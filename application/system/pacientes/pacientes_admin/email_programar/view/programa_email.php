

<div class="form-group col-xs-12 col-md-12">

    <?php

        if(isset($_GET['v'])){

            if($_GET['v'] == 'emails_program'){
                include_once DOL_DOCUMENT.'/application/system/pacientes/pacientes_admin/email_programar/view/emails_program.php';

            }else if($_GET['v'] == 'crear_programacion_email'){
                include_once DOL_DOCUMENT.'/application/system/pacientes/pacientes_admin/email_programar/view/crear_programacion_email.php';

            }else if($_GET['v']==''){
                echo 'Ocurrio un error de Vistas'; die();
            }

        }else{

            echo 'Ocurrio un error de Vistas'; die();
        }

    ?>
</div>





<script>


    $(document).ready(function() {



    });

    $(window).on('load', function () {

    });

</script>