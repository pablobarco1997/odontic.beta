<?php

    $subaccion  = "";
    $countValid = 0;
    $idModi     = 0;
    if(isset($_GET['nuew'])){
        $subaccion = "nuevo";
        $countValid++;
    }
    if(isset($_GET['mod'])){
        $idModi    = (isset($_GET['idr']) && $_GET['idr']!=0)?$_GET['idr']:0;
        $subaccion = "modificar";
        $countValid++;
        if($idModi==0){
            $countValid=0;
        }
    }

    if($countValid == 0){
        print_r("<h3 style='color: red'>Ocurrio un Error</h3>"); die();
    }

    $nameDirectDocumento = "FormDocumentosEntity_".base64_encode($conf->EMPRESA->ID_ENTIDAD."".$conf->EMPRESA->ENTIDAD);

    $object    = array();
    $objectMod = array();

    if(isset($_GET['iddclin']))
    {

        $idDoc  = $_GET['iddclin'];
        $resultMod  = $db->query("SELECT * FROM tab_documentos_clinicos_data where  id_registro_form = $idModi and id_documn_clinico = $idDoc limit 1");
        if($resultMod){
            if($resultMod->rowCount()>0){
                $objectMod = $resultMod->fetchObject();
            }
        }

        $result = $db->query("SELECT * FROM tab_documentos_clinicos where rowid = $idDoc ");
        if($result){
            if($result->rowCount()==1){
                $object = $result->fetchObject();
            }
        }
    }else{

        print_r("<h3 style='color: red'>Ocurrio un error con los parametros Asignados</h3>");
        die();
    }

    $table      = base64_encode($object->id_table_form_document);
    $camposName = explode(",", $object->campos_asignados);

    #breadcrumbs
    $url_breadcrumbs = $_SERVER['REQUEST_URI'];
    $titulo = $object->nombre_documento;
    $modulo = false;

    #echo '<pre>';print_r($objectMod); die();
/*
    date_default_timezone_set('America/Guayaquil');
    print_r(date("Y-m-d  H:m:s"));  die();
*/

?>

<div class="row">

    <div class="form-group col-xs-12 col-md-12 no-padding">
        <div  class="form-group col-md-12 col-xs-12">
            <?= Breadcrumbs_Mod($titulo, $url_breadcrumbs, $modulo); ?>
        </div>
    </div>

    <div class="form- col-md-12 col-xs-12">
        <?php
            include_once DOL_DOCUMENT.'/application/system/documentos_clinicos/form_documentos/'.$nameDirectDocumento.'/'.$object->element_text.'.html';
        ?>
    </div>

    <div class="form-group col-md-12 col-xs-12">
        <div style="width: 800px" class="col-centered">
            <button class="btn btn-block btn-success" id="btnImprimir">Guardar</button>
        </div>
    </div>

</div>

<script>

    $idForm_Documento = "";
    $Mod              = ("<?= $subaccion ?>"=="modificar")?1:0;

    function FetchName() {

        var ObjectData = [];
        var Data = <?= json_encode($camposName); ?>;


        $(".countName").each(function(i, item) {

            var ElementName  = $(item);
            var name         = $(item).attr("name");

            // console.log(ElementName[0].nodeName);

            if(ElementName[0].nodeName=="INPUT"){
                var value   = "";
                var type    = $("[name='"+name+"']").attr("type");
                var input   = $("[name='"+name+"']");

                if(type == "text")
                    value = input.val();
                if(type == "date")
                    value = input.val();
                if(type == "number")
                    value = input.val();
                if(type == "checkbox")
                    value = (input.is(":checked"))?1:0;

                var parametros = [name, value];
                ObjectData.push(parametros);
            }
            if(ElementName[0].nodeName=="SELECT"){
                var select   = $("[name='"+name+"']");
                console.log(select);
                var value    = "";
                value = (select.find("options:selected").val()=="")?"":select.find("options:selected").val();
                var parametros = [name, select.val()];
                ObjectData.push(parametros);
            }
        });

        console.log(ObjectData);

        return ObjectData;

    }

    function fetchMod(){

        var DataMod = <?= json_encode(((count($objectMod)>0)?$objectMod->data_documn:array())) ?>;
        DataMod = $.parseJSON(DataMod);

        $.each(DataMod, function (i, item) {
            var name         = item[0]; //name
            var value        = item[1];
            var ElementName  = $("[name='"+name+"']");
            console.log(ElementName);

            if(ElementName[0].nodeName=="INPUT"){
                var type    = $("[name='"+name+"']").attr("type");
                var input   = $("[name='"+name+"']");

                if(type == "text")
                    input.val(value);
                if(type == "date")
                    input.val(value);
                if(type == "number")
                    input.val(value);
                if(type == "checkbox")
                    input.prop("checked", (value==1)?true:false);
            }
            if(ElementName[0].nodeName=="SELECT"){
                var select   = $("[name='"+name+"']");
                select.val(value).trigger('change');
            }

        });

        console.log(DataMod);
    }

    function NuevoModificarDocumento(Elementos){

        var FormInfo = new FormData();

        FormInfo.append("accion",    "NuevoModificarDocumento");
        FormInfo.append("ajaxSend",  "ajaxSend");
        FormInfo.append("table",     "<?= $table ?>"); //base 64
        FormInfo.append("campos",    "<?= $object->campos_asignados ?>"); //base 64
        FormInfo.append("Element",   Elementos);
        FormInfo.append("sub",       "<?= $subaccion ?>");
        FormInfo.append("iddclin",   "<?= $idDoc ?>");
        FormInfo.append("idmod",     "<?= $idModi ?>");

        $.ajax({
            url:$DOCUMENTO_URL_HTTP + '/application/system/documentos_clinicos/controller_documentos/controller_document.php',
            type: "POST",
            data: FormInfo,
            dataType:"json",
            processData:false,
            contentType:false,
            cache:false,
            error: function (xhr, status) {
                
            }, 
            success:function (resp) {
                if(resp['error']!=''){
                    notificacion(resp['error'], 'error');
                }else{
                    notificacion('Información Actualizada', 'success');

                    setTimeout(function() {
                        window.location = $DOCUMENTO_URL_HTTP + "/application/system/documentos_clinicos/index.php?view=form_doc&iddoc="+"<?= $object->id_table_form_document ?>"+"&iddclin="+<?= $idDoc; ?>;
                    },1000);
                }
            }
        });

    }


    $("#btnImprimir").on("click", function() {

        var Elementos = FetchName();

        if(Elementos.length>0){
            NuevoModificarDocumento(JSON.stringify(Elementos));
        }
        console.log(JSON.stringify(Elementos))
        // NuevoModificarDocumento();
        // window.location = $DOCUMENTO_URL_HTTP + "/application/system/documentos_clinicos/form_documentos/viewprint.php";

    });

    $(window).on("load", function() {

        $idForm_Documento = $("#Form_"+"<?= $object->element_text ?>");
        // var Elment = document.getElementsByClassName("name");
        // console.log(Elment);

        // alert($Mod);
        if($Mod){
            fetchMod();
        }

    });


</script>
