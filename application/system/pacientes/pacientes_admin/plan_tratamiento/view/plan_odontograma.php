

<?php

function fetchPiezaCaras($pieza){

    $h_usp_derecha    =[18,17,16,15,14,13,12,11];
    $h_usp_izquierda  =[21,22,23,24,25,26,27,28];

    $hb_usp_derecha   =[48,47,46,45,44,43,42,41];
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


//numero de dientes asignados
$dataNumeroDientes   = array();
$dataNumeroDientes['derecha_izquierda'] = [18,17,16,15,14,13,12,11,21,22,23,24,25,26,27,28];
$dataNumeroDientes['inferior_derecha_inferior_izquierda'] = [48,47,46,45,44,43,42,41,31,32,33,34,35,36,37,38];

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
                        <div class="RowDiv" id="<?= $value_id?>">
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
                        <div class="RowDiv">
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


<script>

    $iconDienteGlob  = "<?= "data:image/png; base64, ". base64_encode(file_get_contents(DOL_HTTP."/logos_icon/logo_default/diente.png"))?>";

    $(".selectCell").click(function () {
        pintarCuadro(1, $(this));
    });
    $(".selectCell").dblclick(function () {
        pintarCuadro(2, $(this));
    });
    var pintarCuadro = function(count=0, Element){
        if(count!=0){
            if(count==1){
                Element.addClass(Element.attr('title')+'Activar ActivaCaraGlod');
                Element.parents('td').addClass('PiezaActiva');
            }
            if(count==2){
                Element.removeClass(Element.attr('title')+'Activar ActivaCaraGlod');
                console.log(Element.parents('.CaraDiv').find(".ActivaCaraGlod").length);
                if(Element.parents('.CaraDiv').find(".ActivaCaraGlod").length==0){
                    Element.parents('td').removeClass('PiezaActiva');
                }
            }
        }
    };

</script>