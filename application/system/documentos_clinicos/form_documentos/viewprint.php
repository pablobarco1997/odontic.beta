
<?php


    $url_http = $_SERVER['REQUEST_URI'];

    session_start();

    include_once '../../../config/lib.global.php';
    include_once '../../../config/main.php';

    $nameDirectDocumento = "FormDocumentosEntity_".base64_encode($conf->EMPRESA->ID_ENTIDAD."".$conf->EMPRESA->ENTIDAD);


    if(!isset($_GET['htmldom'])){
        print_r("<h3 style='color: red'>Ocurrio un Error</h3>"); die();
    }

    $DomHTML = $_GET['htmldom'];

    $idDoc = $_GET['idform'];
    $iddocResgistro = $_GET['iddoct'];


    $result = $db->query("SELECT * FROM tab_documentos_clinicos where rowid = $idDoc ");
    if($result){
        if($result->rowCount()==1){
            $object = $result->fetchObject();
        }
    }


    $ArrayOn = array();

    $sql = " SELECT id_registro_form , name_documn , id_documn_clinico, date_create, data_documn FROM tab_documentos_clinicos_data";
    $sql .= " WHERE id_registro_form = ".$iddocResgistro ." limit 1";
    $result2 = $db->query($sql);
    if($result2){
        if($result2->rowCount()>0){
            $ArrayOn = $result2->fetchObject()->data_documn;
        }
    }

//    echo '<pre>';print_r($ArrayOn); die();

    #print_r($sql); die();
    #echo '<pre>'; print_r($ArrayOn); die();
    
?>


<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0" >
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="<?php echo  DOL_HTTP.'/public/bower_components/bootstrap/dist/css/bootstrap.min.css' ?>">
    <link rel="stylesheet" href="<?php echo DOL_HTTP .'/public/css/css_global/lib_glob_style.css'?>">
    <script src="<?php echo DOL_HTTP.'/public/bower_components/jquery/dist/jquery.js'?>"></script>
    <link href="https://fonts.googleapis.com/css2?family=Baloo+Da+2&display=swap" rel="stylesheet">
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.3.2/jspdf.min.js"></script>
    <title>Document</title>
</head>
<style>
    *{
        font-family: 'Baloo Da 2', cursive;
    }
    .form-control{
        color: currentColor;
        cursor: not-allowed;
        /*opacity: 2;*/
        text-decoration: none;
        pointer-events: none;
    }
    .form-control{
        box-shadow: none;
    }
    .form-control:focus{
        box-shadow: none;
    }
    .bloqueo{
        color: currentColor;
        cursor: not-allowed;
        /*opacity: 0.9;*/
        text-decoration: none;
        pointer-events: none;
    }


</style>
<body>

<form action="../export/export_pdf.php" method="post">
    <br>

    <div class="col-centered" style="width: 780px">
        <input name="idform" class="hidden" id="idform" value="<?= $idDoc ?>">
        <input name="docblank" class="hidden" id="docblank" value="data">
        <input name="dataPrint" class="hidden" id="dataPrint" value="">
            <br>
        <button type="submit" class="btn btn-block btnhover" id="imprimir_document"> <i class="fa fa-print"></i> <b>IMPRIMIR</b> </button>
    </div>

    <div class="elementH"></div>

    <?php include_once DOL_DOCUMENT.'/application/system/documentos_clinicos/form_documentos/'.$nameDirectDocumento.'/'.$DomHTML;  ?>

</form>


<br>
</body>
</html>

<script>

    var ObjectDatos = <?= $ArrayOn ?>;

    window.addEventListener("load", function() {

        console.log(ObjectDatos);
        $.each(ObjectDatos, function(i, item){

            var name  = item[0];
            var value = item[1];

            var ElementName = $("[name='"+name+"']");

            // console.log(ElementName);
            if(ElementName[0].nodeName=="INPUT"){

                var type      = $("input[name='"+name+"']").attr("type");
                var input     = $("input[name='"+name+"']");
                var soloTexto = "";

                if(type!="checkbox"){
                    soloTexto = " <span style='color: dodgerblue; font-weight: bold'> "+value+"</span>";
                    ElementName
                        .parent("td")
                        .css("padding-top", "10px")
                        .html($(soloTexto));
                }

                if(type == "text"){
                    input.val(value).css("display","none");
                    $("[name='"+name+"']").remove();
                }
                if(type == "date"){
                    input.val(value).css("display","none");
                    $("[name='"+name+"']").remove();
                }
                if(type == "number"){
                    input.val(value).css("display","none");
                    $("[name='"+name+"']").remove();
                }
                if(type == "checkbox"){
                    input.prop('checked', (value=="1")?true:false).parent('label').addClass('bloqueo');
                    if(input.is(':checked')){
                        input.attr('checked', true);
                    }
                }

            }

            if(ElementName[0].nodeName=="SELECT"){
                var select   = $("[name='"+name+"']");
                select.val(value).trigger("change");
                var soloTexto = " <span style='color: dodgerblue; font-weight: bold'> "+select.find(":selected").text()+"</span>";

                ElementName
                    .parent("td")
                    .css("padding-top", "10px")
                    .html($(soloTexto));

                $("[name='"+name+"']").remove();
            }

            if(ElementName[0].nodeName=="TEXTAREA"){
                var soloTexto = "";
                soloTexto = " <span style='color: dodgerblue; font-weight: bold;'> "+value+"</span>";
                ElementName
                    .parent("div")
                    .css("padding-top", "10px")
                    .css("padding-bottom", "10px")
                    .html($(soloTexto));
                $("[name='"+name+"']").remove();
            }

        });

        setTimeout(()=>{ sendHTML();  },500);

    });

    function sendHTML(){
        var ElementDom      = document.getElementById("ContentForm");
        var ElementoString  = ElementDom.outerHTML; //Convierto el elemento en un string
        $("[name='dataPrint']").attr("value", btoa(ElementoString));
        return ElementoString;
    }


</script>