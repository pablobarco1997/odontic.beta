
<?php


    $accion = "";

    //lista de documentos
    if(isset($_GET['v']) && $_GET['v'] == "listdocumment") {
        $accion = $_GET['v'];
    }

    //documento nuevo o modificar
    if(isset($_GET['v']) && $_GET['v'] == "docum_clin") {
        $accion = $_GET['v'];
    }

?>
<script>
    $acciondocummAsociado = "<?= $accion; ?>"; //accion de documentos asociados
</script>

    <?php


        //tipo de vista
        if(isset($_GET['v'])){
            if($_GET['v']=="listdocumment"){
                //lista de documento creados
                include_once 'document_list.php';
            }

            //Se muestra los documentos si se va a crear un nuevo documento
            if($_GET['v']=="docum_clin"){
                include_once 'docum_clin.php';
            }

            //si en caso no se encuentra ninguna vista
            if($_GET['v'] != "docum_clin" && $_GET['v'] != "listdocumment") {
                echo '<h2 style="color: red; font-weight: bolder">OCURRIO UN ERROR NO SE ENCONTRO LA VISTA</h2>';
                die();
            }
        }else{

            echo '<h2 style="color: red; font-weight: bolder">OCURRIO UN ERROR NO SE ENCONTRO LA VISTA</h2>';
            die();
        }
    ?>
