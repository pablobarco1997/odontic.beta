


<!--estylos caras dientes-->
<style>

    .boderTd{
        border: 1px solid black;
        padding: 7px;
        cursor: pointer;
    }

    .activeCara{
        background-color: #9f191f;
    }

    .pieza .boderTd:hover{
        background-color: #9f191f;
    }

</style>


<?php


$dataNumeroPiezas = array();
$dataNumeroPiezas1 = [18,17,16,15,14,13,12,11,21,22,23,24,25,26,27,28];
$dataNumeroPiezas2 = [48,47,46,45,44,43,42,41,31,32,33,34,35,36,37,38];

#HEMIARCADA SUPERIOR DERECHA
$listHemiarSupDerech  = "";
for ($i = 0; $i <= count($dataNumeroPiezas1)-1; $i++){

    if($i > 7)
       continue;

    $piezaN = $dataNumeroPiezas1[$i];
    $listHemiarSupDerech .= '<li class="diente-'.$piezaN.' dientePermanente" data-diente="'.$piezaN.'" style="margin-right: 6.5px ; margin-left: 6.5px">';
    $listHemiarSupDerech .= '<table class="pieza piezaClick " id="" style="padding: 5px; margin-left: 2px" >';
    $listHemiarSupDerech .= '<tr>
                                        <td></td>
                                        <td  style="font-size: 1.5rem" class="text-center" title="">'.$piezaN.'</td>
                                        <td></td>
                                    </tr>';
    $listHemiarSupDerech .= '<tr>
                                        <td></td>
                                        <td  class="boderTd CaraClickDenticionPermanente cara" data-id="vestibular" title="vestibular"></td>
                                        <td></td>
                                    </tr>';
    $listHemiarSupDerech .= '<tr>
                                        <td class="boderTd CaraClickDenticionPermanente cara" data-id="distal"   title="distal"></td>
                                        <td class="boderTd CaraClickDenticionPermanente cara" data-id="oclusal"  title="oclusal"></td>
                                        <td class="boderTd CaraClickDenticionPermanente cara" data-id="mesial"   title="mesial"></td>
                                    </tr>';
    $listHemiarSupDerech .= '<tr>
                                        <td></td>
                                        <td class="boderTd CaraClickDenticionPermanente cara" data-id="palatino" title="palatino" ></td>
                                        <td></td>
                                    </tr>';

    $listHemiarSupDerech .= '<tr>
                                <td></td>
                                <td class="text-center"><input type="checkbox" id="CheckPiezas" class="CheckPiezasDenticionPermanente"></td>
                                <td></td>
                             </tr>';

    $listHemiarSupDerech .= '</table>';
    $listHemiarSupDerech .= '</li>';

}

#HEMIARCADA  SUPERIOR IZQUIERDA
$listHemiarSupIzquied = "";
for ($iu = 8; $iu <= count($dataNumeroPiezas1) -1; $iu++){

    if($iu == 16)
        continue;

    $piezaN2  = $dataNumeroPiezas1[$iu];
    $listHemiarSupIzquied .= '<li class="diente-'.$piezaN2.' dientePermanente" data-diente="'.$piezaN2.'" style="margin-right: 6.5px ; margin-left: 6.5px">';
    $listHemiarSupIzquied .= '<table class="pieza piezaClick " id="" style="padding: 5px; margin-left: 2px" >';
    $listHemiarSupIzquied .= '<tr>
                                    <td></td>
                                    <td  style="font-size: 1.5rem" class="text-center" title="">'.$piezaN2.'</td>
                                    <td></td>
                                 </tr>';

    $listHemiarSupIzquied .= '<tr>
                                    <td></td>
                                    <td  class="boderTd CaraClickDenticionPermanente cara" data-id="vestibular" title="vestibular"></td>
                                    <td></td>
                                 </tr>';

    $listHemiarSupIzquied .= '<tr>
                                    <td class="boderTd CaraClickDenticionPermanente cara" data-id="mesial" title="mesial"></td>
                                    <td class="boderTd CaraClickDenticionPermanente cara" data-id="oclusal" title="oclusal"></td>
                                    <td class="boderTd CaraClickDenticionPermanente cara" data-id="distal" title="distal"></td>
                                 </tr>';

    $listHemiarSupIzquied .= '<tr>
                                    <td></td>
                                    <td class="boderTd CaraClickDenticionPermanente cara"data-id="palatino" title="palatino" ></td>
                                    <td></td>
                                  </tr>';

    $listHemiarSupIzquied .= '<tr>
                                    <td></td>
                                    <td class="text-center"><input type="checkbox" id="CheckPiezas" class="CheckPiezasDenticionPermanente"></td>
                                    <td></td>
                                 </tr>';

    $listHemiarSupIzquied .= '</table>';
    $listHemiarSupIzquied .= '</li>';

}

#HEMIARCADA  INFERIOR DERECHA
$listHemiarInfDerech = "";
for ($c = 0; $c <= count($dataNumeroPiezas2)-1; $c++){

    if($c > 7)
        continue;

    $piezaN3 = $dataNumeroPiezas2[$c];
    $listHemiarInfDerech  .= '<li class="diente-'.$piezaN3.' dientePermanente" data-diente="'.$piezaN3.'" style="margin-right: 6.5px ; margin-left: 6.5px">';
    $listHemiarInfDerech  .= '<table class="pieza piezaClick " id="" style="padding: 5px; margin-left: 2px" >';

    $listHemiarInfDerech  .= '<tr>
                                <td></td>
                                <td  style="font-size: 1.5rem" class="text-center" title="">'.$piezaN3.'</td>
                                <td></td>
                              </tr>';

    $listHemiarInfDerech  .= '<tr>
                                   <td></td>
                                   <td  class="boderTd CaraClickDenticionPermanente cara" data-id="lingual" title="lingual"></td>
                                   <td></td>
                              </tr>';

    $listHemiarInfDerech  .= '<tr>
                                <td class="boderTd CaraClickDenticionPermanente cara"   data-id="distal"  title="distal" ></td>
                                <td class="boderTd CaraClickDenticionPermanente cara"   data-id="oclusal" title="oclusal"></td>
                                <td class="boderTd CaraClickDenticionPermanente cara"   data-id="mesial"  title="mesial"></td>
                              </tr>';

    $listHemiarInfDerech  .= '<tr>
                                <td></td>
                                <td class="boderTd CaraClickDenticionPermanente cara" data-id="vestibular" title="vestibular" ></td>
                                <td></td>
                              </tr>';

    $listHemiarInfDerech  .= '<tr>
                                 <td></td>
                                 <td class="text-center"><input type="checkbox" id="CheckPiezas" class="CheckPiezasDenticionPermanente"> </td>
                                 <td></td>
                               </tr>';

    $listHemiarInfDerech  .= '</table>';
    $listHemiarInfDerech  .= '</li>';

}

#HEMIARCADA INFERIOR IZQUIERDA
$listHemiarInfIzqui = "";
for ($co = 8; $co <= count($dataNumeroPiezas2)-1; $co++){

    if($co == 16)
        continue;

    $piezaN4 = $dataNumeroPiezas2[$co];
    $listHemiarInfIzqui .= '<li class="diente-'.$piezaN4.' dientePermanente" data-diente="'.$piezaN4.'" style="margin-right: 6.5px ; margin-left: 6.5px">';
    $listHemiarInfIzqui .= '<table class="pieza piezaClick " id="" style="padding: 5px; margin-left: 2px" >';

    $listHemiarInfIzqui .= '<tr>
                               <td></td>
                               <td  style="font-size: 1.5rem" class="text-center" title="">'.$piezaN4.'</td>
                               <td></td>
                            </tr>';

    $listHemiarInfIzqui .= '<tr>
                                <td></td>
                                <td  class="boderTd CaraClickDenticionPermanente cara" data-id="lingual" title="lingual"></td>
                                <td></td>
                            </tr>';

    $listHemiarInfIzqui .= '<tr>
                                <td class="boderTd CaraClickDenticionPermanente cara" data-id="mesial" title="mesial"></td>
                                <td class="boderTd CaraClickDenticionPermanente cara" data-id="oclusal" title="oclusal"></td>
                                <td class="boderTd CaraClickDenticionPermanente cara" data-id="distal" title="distal"></td>
                            </tr>';

    $listHemiarInfIzqui .= '<tr>
                                <td></td>
                                <td class="boderTd CaraClickDenticionPermanente cara" data-id="vestibular" title="vestibular"></td>
                                <td></td>
                            </tr>';

    $listHemiarInfIzqui .= '<tr>
                                <td></td>
                                <td class="text-center"><input type="checkbox" id="CheckPiezas" class="CheckPiezasDenticionPermanente"> </td>
                                <td></td>
                            </tr>';

    $listHemiarInfIzqui .= '</table>';
    $listHemiarInfIzqui .= '</li>';

}


?>




<div class="center-block" style="width: 1140px !important;">

    <ul class="list-inline">
        <?php echo $listHemiarSupDerech; ?>
        <?php echo $listHemiarSupIzquied; ?>
    </ul>

</div>


<div class="center-block" style="width: 1140px !important;">
    <ul class="list-inline">
        <?php echo $listHemiarInfDerech; ?>
        <?php echo $listHemiarInfIzqui; ?>
    </ul>
</div>