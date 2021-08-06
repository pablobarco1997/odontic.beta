
<?php


    accessoModule('E-mail Asociados');

    $accion  = "";
    $modv    = "";

    # listpmail ==> LISTA DE MAIL VISTA PRINCIPAL
    if(isset($_GET['v']) && $_GET['v'] == "listpmail"){
        $accion = 'list_email';
    }


?>

<script>
    $subaccion = "<?= $accion ?>";
</script>


<?php
    if(isset($_GET['v']) && $_GET['v'] == "listpmail") {
        include_once 'list_emailsent.php';

    }
?>
