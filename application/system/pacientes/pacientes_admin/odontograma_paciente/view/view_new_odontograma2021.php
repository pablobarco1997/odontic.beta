
<?php

    function fetchPiezaCaras($pieza){

        $h_usp_derecha   =[18,17,16,15,14,13,12,11];
        $h_usp_izquierda =[21,22,23,24,25,26,27,28];

        $hb_usp_derecha =[48,47,46,45,44,43,42,41];
        $hb_usp_izquierda =[31,32,33,34,35,36,37,38];

        $hermiarcada_top       = "";
        $hermiarcada_bottom    = "";
        $hermiarcada_left      = "";
        $hermiarcada_right     = "";
        $hermiarcada_center    = "";

        if(in_array($pieza, $h_usp_derecha)){
            $hermiarcada_top      = "vestibular";
            $hermiarcada_bottom   = "palatino";
            $hermiarcada_left     = "mesial";
            $hermiarcada_right    = "distal";
            $hermiarcada_center   = "oclusal";
        }if(in_array($pieza, $h_usp_izquierda)){
            $hermiarcada_top      = "vestibular";
            $hermiarcada_bottom   = "palatino";
            $hermiarcada_left     = "distal";
            $hermiarcada_right    = "mesial";
            $hermiarcada_center   = "oclusal";
        }if(in_array($pieza, $hb_usp_derecha)){
            $hermiarcada_top      = "lingual";
            $hermiarcada_bottom   = "vestibular";
            $hermiarcada_left     = "mesial";
            $hermiarcada_right    = "distal";
            $hermiarcada_center   = "oclusal";
        }if(in_array($pieza, $hb_usp_izquierda)){
            $hermiarcada_top      = "lingual";
            $hermiarcada_bottom   = "vestibular";
            $hermiarcada_left     = "distal";
            $hermiarcada_right    = "mesial";
            $hermiarcada_center   = "oclusal";
        }


        $caras = '<div class="TableDiv CaraDiv" style="height: 30px;">
                        <div class="RowDiv">
                            <div class="CellDiv"></div>
                            <div class="CellDiv borderCell selectCell '.$hermiarcada_top.'  " title="'.$hermiarcada_top.'"></div>
                            <div class="CellDiv"></div>
                        </div>

                        <div class="RowDiv">
                            <div class="CellDiv borderCell selectCell '.$hermiarcada_right.' " title="'.$hermiarcada_right.'"></div>
                            <div class="CellDiv borderCell selectCell '.$hermiarcada_center.'" title="'.$hermiarcada_center.'"></div>
                            <div class="CellDiv borderCell selectCell '.$hermiarcada_left.'  " title="'.$hermiarcada_left.'"></div>
                        </div>

                        <div class="RowDiv">
                            <div class="CellDiv"></div>
                            <div class="CellDiv borderCell selectCell '.$hermiarcada_bottom.' " title="'.$hermiarcada_bottom.'"></div>
                            <div class="CellDiv"></div>
                        </div>
                    </div>';

        return $caras;

    }


?>


<style>

    .vestibular:hover{
        background-color: #5474b5;
    }.distal:hover{
        background-color: #17855b;
    }.palatino:hover{
        background-color: #fddcf3;
    }.mesial:hover{
        background-color: #a82f4a;
    }.lingual:hover{
        background-color: yellow;
    }.oclusal:hover{
        background-color: #fddcf3;
    }

    .vestibularActivar{
        background-color: #5474b5;
    }.distalActivar{
        background-color: #17855b;
    }.palatinoActivar{
        background-color: #fddcf3;
    }.mesialActivar{
        background-color: #a82f4a;
    }.lingualActivar{
        background-color: yellow;
    }.oclusalActivar{
        background-color: #fddcf3;
    }

    .TableDiv {
        display: table;
    }
    .RowDiv {
        display: table-row;
    }
    .CellDiv {
        display: table-cell;
        padding-left: 6px;
        padding-right: 6px;
        padding-bottom: 15px;
    }
    .borderCell {
        border: solid;
        border-width: thin;
    }
    .selectCell:hover {
        cursor: pointer;
    }

    .SelectedTableEstado{
        background-color: #f4f4f4;
    }

</style>


<?php

//numero de dientes asignados
$dataNumeroDientes   = array();
$dataNumeroDientes['derecha_izquierda'] = [18,17,16,15,14,13,12,11,21,22,23,24,25,26,27,28];
$dataNumeroDientes['inferior_derecha_inferior_izquierda'] = [48,47,46,45,44,43,42,41,31,32,33,34,35,36,37,38];


?>

<div class="table-responsive">
    <table style="margin: auto;">

        <tr class="columnRow">
            <?php
                $c = 0;
                $Empieza=8;
                foreach ($dataNumeroDientes['derecha_izquierda'] as $value_id){
                    ?>
                    <td style="padding: 3px;" align="center" class="odontoPieza_<?= $value_id?> odontoPieza" id="<?= $value_id ?>"  >
                        <div class="TableDiv" style="margin-bottom: 4px;">
                            <div class="RowDiv" >
                                <div class="CellDiv"><button class="btn btn-xs" onclick="changeStadosPieza($(this), true)" data-id="odontoPieza_<?= $value_id?>" >...</button></div>
                            </div>
                            <div class="RowDiv" id="<?= $value_id?>">
                                <div class="CellDiv">
                                    <div style="position: relative; ;width: 40px; height: 45px;">
                                        <img src="" class="hide" alt="" title="" id="imgStados" width="40px" height="45px" style="position: absolute; z-index: +1;">
                                        <img src="<?= "data:image/png; base64, ". base64_encode(file_get_contents(DOL_HTTP .'/application/system/pacientes/pacientes_admin/odontograma_paciente/img/pieza_web_odontic.png')) ?> " alt="" width="40px" height="45px">
                                    </div>
                                </div>
                            </div>
                            <div class="RowDiv">
                                <div class="CellDiv"><?= $value_id ?></div>
                            </div>
                        </div>

                        <?= fetchPiezaCaras($value_id) ?>
                    </td>

                    <?php $c++;
                }
            ?>
        </tr>

        <tr>
            <td colspan="16"><br>
                <hr>
            </td>
        </tr>

        <tr class="columnRow">
            <?php
            $c = 0;
            $Empieza=8;
            foreach ($dataNumeroDientes['inferior_derecha_inferior_izquierda'] as $value_id){
                ?>
                <td style="padding: 3px;" align="center" class="odontoPieza_<?= $value_id ?> odontoPieza" id="<?= $value_id ?>">
                    <div class="TableDiv" style="margin-bottom: 4px;">
                        <div class="RowDiv" >
                            <div class="CellDiv"><button class="btn btn-xs" onclick="changeStadosPieza($(this), true)" data-id="odontoPieza_<?= $value_id?>" >...</button></div>
                        </div>
                        <div class="RowDiv">
                            <div class="CellDiv">
                                <div style="position: relative; ;width: 40px; height: 45px;">
                                    <img src="" class="hide" alt="" id="imgStados" title="" width="40px" height="45px" style="position: absolute; z-index: +1; transform:rotate(540deg);">
                                    <img src="<?= "data:image/png; base64, ". base64_encode(file_get_contents(DOL_HTTP .'/application/system/pacientes/pacientes_admin/odontograma_paciente/img/pieza_web_odontic.png')) ?> " alt="" width="40px" height="45px" style="transform: rotate(540deg);">
                                </div>
                            </div>
                        </div>
                        <div class="RowDiv">
                            <div class="CellDiv"><?= $value_id ?></div>
                        </div>
                    </div>

                    <?= fetchPiezaCaras($value_id) ?>
                </td>

                <?php $c++;
            }
            ?>
        </tr>


    </table>
</div>


<div id="CambiarStadoPieza" class="modal fade" role="dialog">
    <div class="modal-dialog ">
        <div class="modal-content">
            <div class="modal-header modal-diseng">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"></h4>
                <input type="text" class="hidden" id="ElementoPieza" value="">
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-12 col-xs-12 col-md-8 col-centered">
                        <label for="">Observación</label>
                        <textarea name="" id="observacionUpdateStado"  class="form-control" placeholder="opcional" style="resize: vertical"></textarea>
                        <table width="100%" class="table table-hover" id="selectedEstadosPieza">

                            <?php

                            $directElement = opendir( DOL_DOCUMENT.'/application/system/pacientes/pacientes_admin/odontograma_paciente/img/');
                            $result = $db->query("select  rowid , descripcion, image_status from tab_odontograma_estados_piezas")->fetchAll(PDO::FETCH_ASSOC);
                            foreach ($result as $k => $value){

                                $url=DOL_HTTP.'/application/system/pacientes/pacientes_admin/odontograma_paciente/img/'.$value['image_status'];
                                $url2=DOL_HTTP.'/application/system/pacientes/pacientes_admin/odontograma_paciente/img/pieza_web_odontic.png';

                                $imgbase64=base64_encode(file_get_contents($url));
                                $imgbase64_2=base64_encode(file_get_contents($url2));

                                $dataImg='data:image/png; base64, '.$imgbase64;
                                $dataImg_2='data:image/png; base64, '.$imgbase64_2;

                                print '
                                <tr style="cursor: pointer" data-id="'.($value['rowid']).'" data-img="'.$dataImg.'" data-title="'.$value['descripcion'].'">
                                    <td>
                                        <div style="position: relative; ;width: 30px; height: 35px;">
                                            <img src="'.$dataImg.'" class="" alt="" id="imgStados" title="" width="30px" height="35px" style="position: absolute; z-index: +1;">
                                            <img src="'.$dataImg_2.'" alt="" width="30px" height="35px">
                                        </div>
                                    </td>
                                    <td> '.strtoupper($value['descripcion']).' </td>
                                </tr>';
                            }

                            ?>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn " data-dismiss="modal" id="aplicar_estado_odont">Aplicar</button>
            </div>
        </div>
    </div>
</div>




<script>

    //get parametros url
    var get = function (string="") {
        let paramsGet = new URLSearchParams(location.search);
        var paramget = paramsGet.get(string);
        return paramget;
    };


    var changeStadosPieza = function (indexElement, valid=false) {

        if(valid==true){

            $('#CambiarStadoPieza').modal("show")
                .find('#ElementoPieza').attr('value', indexElement.prop('dataset').id);

        }
        console.log(indexElement.prop('dataset').id);
    };

    function fetchOdontograma() {

        $.ajax({
            url: $DOCUMENTO_URL_HTTP +'/application/system/pacientes/pacientes_admin/controller/controller_adm_paciente.php',
            type:'POST',
            data: {'ajaxSend':'ajaxSend', 'accion':'fecht_odontograma', 'idtratamiento': get('idplantram'), 'idpaciente': $id_paciente} ,
            dataType:'json',
            async:false,
            cache:false,
            success:function (result) {
                if(result['error'] == ""){

                    var dataOdont  = result['dataprincipal'];
                    console.log(dataOdont);

                    //recorrer piezas
                    $.each(dataOdont,  function (i, item) {

                        var index_odont       = '.odontoPieza_'+item['fk_diente'];
                        var estado_odont      = item['fk_estado_pieza'];
                        var caras             = $.parseJSON(item['json_caras']);
                        var ElementoPieza     = $(index_odont);
                        var img_status        = item['img_status'];
                        var nom_status        = item['nom_status'];

                        //add estado
                        $(ElementoPieza).attr('data-id_estado_pieza',item['fk_estado_pieza']);

                        if(img_status!="" && img_status!=null){
                            ElementoPieza.find('#imgStados').attr('src', img_status).removeClass('hide').attr('title', nom_status);
                            console.log(ElementoPieza.find('#imgStados'));
                        }

                        $(ElementoPieza).find('.CaraDiv').children().each(function (i, div) {
                            $.each(caras, function (i, value) {
                                if(value=='true'){
                                    if($(div).find('.'+i).hasClass(i)==true){
                                        $(div).find('.'+i).addClass(i+'Activar');
                                    }
                                }
                            });
                        });
                        // console.log($(ElementoCaras).find('.CaraDiv').children());
                    });

                }
            }
        });
    }

    function resetOdont(){
        $('div.CellDiv > img').attr('class','');
        $('div.CellDiv > img').attr('class','hide');

        var resetCaras = {"vestibular":"true","distal":"true","palatino":"false","oclusal":"true","mesial":"false","lingual":"false"};

        $.each(resetCaras, function (i, item) {
            $('div.selectCell').removeClass(i+'Activar');
        });
    }

    var pintarCuadro = function(count=0, Element){
        if(count!=0){
            if(count==1){
                Element.addClass(Element.attr('title')+'Activar');
            }
            if(count==2){
                Element.removeClass(Element.attr('title')+'Activar');
            }
        }
    };

    $(".selectCell").click(function () {
        pintarCuadro(1, $(this));
    });
    $(".selectCell").dblclick(function () {
        pintarCuadro(2, $(this));
    });

    //selected tr estado
    $("#selectedEstadosPieza tbody tr").click(function () {
        $("#selectedEstadosPieza tbody").children().removeClass('SelectedTableEstado');

        if($(".SelectedTableEstado").length==0){
            $(this).addClass('SelectedTableEstado');
        }
    });

    $("#CambiarStadoPieza").on('hide.bs.modal', function(){
        $("#selectedEstadosPieza tbody").children().removeClass('SelectedTableEstado');
    });


    var Aplicar_Estados = function(){

        if($(".SelectedTableEstado").length==1){

            //id estado de (pieza o diente) selecionado
            var id      = $(".SelectedTableEstado").prop('dataset').id;
            var Element = '.'+$("#ElementoPieza").val();
            var estado_id = id;

            var fk_diente   = $(Element).attr('id');
            var datosPieza  = fetchPiezas(fk_diente);


            $(Element).find('#imgStados')
                .removeClass('hide')
                .attr('src', $(".SelectedTableEstado").prop('dataset').img)
                .attr('title',$(".SelectedTableEstado").prop('dataset').title);

            //cambio el estado o diente selecionado
            $(Element).attr('data-id_estado_pieza', estado_id);

            adddetalle(fk_diente, datosPieza[0], estado_id, datosPieza[0]['label_selected']);
            updateOdontograma(fetchPiezas());

        }else{
            notificacion('Ocurrio un error', 'error');
        }

    };

    function fetchPiezas(idpieza=0, Element){

        var pieza = [];

        var recorrer = ".odontoPieza";

        if(idpieza!=0){
            recorrer = ".odontoPieza_"+idpieza;
        }

        //recorro las piezas
        $(recorrer).each(function (i, itemPadre) {

            //recorro las caras selecionadas o  no selecionadas
            var label_selected = "";
            var objectCaras = {
                vestibular : 0,
                distal     : 0,
                palatino   : 0,
                oclusal    : 0,
                mesial     : 0,
                lingual    : 0,
            };

            var CaraDiv    = $(this).children('.CaraDiv');
            var carasSelected = CaraDiv.children();
            $.each(carasSelected, function (i, itemCaras) {
                //recorro las key activadas
                $.each(objectCaras, function (key,value) {

                    var caraKey = '.'+key+'Activar'; //busco la clase activa por cada pieza y cara
                    if($(itemCaras).find(caraKey).length>0){
                        objectCaras[key]++;
                        label_selected += " " + key;
                    }
                });
            });

            pieza.push({
                'diente' : $(itemPadre).attr('id'),
                'estado_diente': $(itemPadre).prop('dataset').id_estado_pieza||0,
                'caras' : {
                    'vestibular' : (objectCaras['vestibular'] > 0) ? true : false,
                    'distal'     : (objectCaras['distal'] > 0) ? true : false,
                    'palatino'   : (objectCaras['palatino'] > 0) ? true : false,
                    'oclusal'    : (objectCaras['oclusal'] > 0) ? true : false,
                    'mesial'     : (objectCaras['mesial'] > 0) ? true : false,
                    'lingual'    : (objectCaras['lingual'] > 0) ? true : false,
                },
                'label_selected' : label_selected
            });

        });

        return pieza;
    }

    $('#aplicar_estado_odont').click(Aplicar_Estados);

    //funciones para agregar y actualizar el odontograma
    function adddetalle(fk_diente, datosPiezas, fk_estadoDiente, labelCaras){

        var error = false;

        var inform = {
            'fk_diente'          :  fk_diente,
            'datosPiezas'        :  datosPiezas,
            'fk_estadoDiente'    :  fk_estadoDiente,
            'fk_trataminto'      :  get("idplantram"),
            'observacion'        :  $("#observacionUpdateStado").val(),
            'labelCaras'         :  labelCaras,
        };

        $.ajax({
            url: $DOCUMENTO_URL_HTTP +'/application/system/pacientes/pacientes_admin/controller/controller_adm_paciente.php',
            type:'POST',
            data: { 'ajaxSend':'ajaxSend', 'accion':'nuevo_odontograma_detalle', 'info': inform } ,
            dataType:'json',
            async:false,
            success:function(resp) {

                if(resp.error == ''){
                    error = true;
                    $('#observacionOpcional').val(null);
                }else {
                    error = false;
                }
            }
        });

        return error;
    }
    function updateOdontograma(datosPiezas){
        var $parametros = {
            'accion': 'odontograma_update',
            'ajaxSend': 'ajaxSend',
            'piezas': datosPiezas,
            'fk_tratamiento': get("idplantram"),
            'idpaciente': $id_paciente,

        };
        $.ajax({
            url: $DOCUMENTO_URL_HTTP +'/application/system/pacientes/pacientes_admin/controller/controller_adm_paciente.php',
            type:'POST',
            data: $parametros ,
            dataType:'json',
            async:false,
            success:function(resp){
                if(resp.error != ''){
                    notificacion('Ocurrió un error con la Operación , Odontograma Update, Consulte con soperte Técnico');
                }else{
                    fetchOdontograma();
                    detallesOdontogramasEstados();
                }
            }
        });
    }


    $(window).on('load', function () {

        fetchOdontograma();
        // resetOdont();
    });

</script>