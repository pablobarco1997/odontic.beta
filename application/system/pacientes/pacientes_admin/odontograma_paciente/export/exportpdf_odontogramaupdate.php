<?php

require_once '../../../../../config/lib.global.php';

session_start();

if(!isset($_SESSION['is_open']))
{
    header("location:".DOL_HTTP."/application/system/login");
}

require_once  DOL_DOCUMENT .'/application/system/conneccion/conneccion.php';    //Coneccion de Empresa
require_once  DOL_DOCUMENT .'/public/lib/mpdf60/mpdf.php';
require_once  DOL_DOCUMENT .'/application/controllers/controller.php';

/**SE CREA LAS VARIABLES DE INICIO**/
$cn = new ObtenerConexiondb();                    //Conexion global Empresa Fija
$db = $cn::conectarEmpresa($_SESSION['db_name']); //coneccion de la empresa variable global
$id = GETPOST("iddocument");

$InformacionEntity = (object)array(
    'nombre'            => $_SESSION['nombreClinica'],
    'email'             => $_SESSION['emailClinica'],
    'direccion'         => $_SESSION['direccionClinica'],
);

$loginUsuario = $_SESSION['usuario']; #Login Inicio de Sesion

//echo '<pre>';print_r($_SESSION['usuario']);die();

$DirectorioImgClinicaHttp = DOL_HTTP.'/logos_icon/icon_logos_'.$_SESSION['entidad'];

if(isset($_SESSION['logoClinica']))
{
    if($_SESSION['logoClinica']!="" && !file_exists($DirectorioImgClinicaHttp)){
        $iconClinica = $DirectorioImgClinicaHttp.'/'.$_SESSION['logoClinica'];
    }else{
        $iconClinica = DOL_HTTP.'/logos_icon/logo_default/none-icon-20.jpg';
    }
}else{
    $iconClinica = DOL_HTTP.'/logos_icon/logo_default/none-icon-20.jpg';
}

$ImagenLogoClinica = "<img src='".$iconClinica."' style='width:40px; height: 40px; border-radius: 100%;' >";

//numero de dientes asignados
$dataNumeroDientes   = array();
$dataNumeroDientes[] = [18,17,16,15,14,13,12,11,21,22,23,24,25,26,27,28];
$dataNumeroDientes[] = [48,47,46,45,44,43,42,41,31,32,33,34,35,36,37,38];

$dataEstadosDiente = [];

$idpaciente      = GETPOST('idp');
$idplantramiento = GETPOST('idplant');

$rs = $db->query("SELECT * FROM tab_odontograma_estados_piezas");
if($rs->rowCount()>0){
    while ($obj = $rs->fetchObject()){
        $dataEstadosDiente[] = $obj;
    }
}


$detalle_odontologicos = null;

$query = "SELECT 
            d.rowid,
            d.fk_diente , 
            (select s.descripcion from tab_odontograma_estados_piezas s where s.rowid = d.fk_estado_diente) as estado,
            d.list_caras,
            d.fecha,
            d.obsrvacion,
            d.estado_anulado
            FROM tab_odontograma_paciente_det d
            where  d.fk_tratamiento = $idplantramiento 
                    order by d.rowid desc";

$rslist = $db->query($query);
if($rslist&&$rslist->rowCount()>0)
{
    $detalle_odontologicos = array();

    while ($ob = $rslist->fetchObject())
    {
        $row= array();
        if($ob->estado_anulado=='A'){
            $row[] = date('Y/m/d', strtotime($ob->fecha));
            $row[] = $ob->fk_diente;
            $row[] = $ob->list_caras;
            $row[] = $ob->estado;
        }


        if($ob->estado_anulado=='E'){
            $row[] = "<strike>".date("Y/m/d", strtotime($ob->fecha))."</strike>";
            $row[] = "<strike> ".$ob->fk_diente." </strike>";
            $row[] = "<strike>".$ob->list_caras."</strike>"  ;
            $row[] = "<strike>".$ob->estado ." ".$observacion."</strike>";
        }

        $detalle_odontologicos[] = $row;
    }

}else{
    $detalle_odontologicos = "No hay datos";
}


$pdf = "";

$pdf .= '<style>
                .tables {
                    border-collapse: collapse;
                     width="100%"; 
                }
                .tablesCaras {
                    border-collapse: collapse;
                     width="100%"; 
                }
                
                .tables {
                    border: 1px solid black;
                }
                
                .borderButom{
                     border-bottom: 1px solid black;
                     text-align: left;
                }
                .fonttml{
                    /*font-size: 1.3rem; */
                }
                .boderTd{
                    border: 1px solid black;
                    padding: 4.5px;
                }
                .colorRed{
                    background-color: #9f191f; 
                    color: #9f191f;
                }
            </style>';


$pdf .= "
    <table  width='100%'>
        <tr  style='width: 100%'>
            <td style='text-align: center'> <h2> ODONTOGRAMA </h2> </td>
        </tr>
        <tr> <td>&nbsp;</td> </tr>
        <tr> <td width='50%'> <H3>ESTADOS ASIGNADOS</H3> </td> </tr>
    </table>    
        ";

$pdf .= "<table width='100%' style='border-collapse: collapse'>";
    $pdf .= "<tr><td></td></tr>";
    $pdf .= "<tr> <td width='50%' class='fonttml'> <img src='".DOL_HTTP."/logos_icon/logo_default/odontograma/estados_dientes/ausente.png' alt='' width='22px'>         Ausente</td> </tr>";
    $pdf .= "<tr> <td width='50%' class='fonttml'> <img src='".DOL_HTTP."/logos_icon/logo_default/odontograma/estados_dientes/caries.png' alt='' width='22px'>          Caries</td> </tr>";
    $pdf .= "<tr> <td width='50%' class='fonttml'> <img src='".DOL_HTTP."/logos_icon/logo_default/odontograma/estados_dientes/corona.png' alt='' width='22px'>          Corona</td> </tr>";
    $pdf .= "<tr> <td width='50%' class='fonttml'> <img src='".DOL_HTTP."/logos_icon/logo_default/odontograma/estados_dientes/endodoncia.png' alt='' width='22px'>      Endodoncia</td> </tr>";
    $pdf .= "<tr> <td width='50%' class='fonttml'> <img src='".DOL_HTTP."/logos_icon/logo_default/odontograma/estados_dientes/fractura.png' alt='' width='22px'>        Fractura</td> </tr>";
    $pdf .= "<tr> <td width='50%' class='fonttml'>  <img src='".DOL_HTTP."/logos_icon/logo_default/odontograma/estados_dientes/implante.png' alt='' width='22px'>        Implante</td> </tr>";
    $pdf .= "<tr> <td width='50%' class='fonttml'> <img src='".DOL_HTTP."/logos_icon/logo_default/odontograma/estados_dientes/indicacion_extraccion.png' alt='' width='22px'> Indicacion extraccion</td> </tr>";
    $pdf .= "<tr> <td width='50%' class='fonttml'> <img src='".DOL_HTTP."/logos_icon/logo_default/odontograma/estados_dientes/infeccion_pulpar.png' alt='' width='22px'>     Infeccion pulpar</td> </tr>";
    $pdf .= "<tr> <td width='50%' class='fonttml'> <img src='".DOL_HTTP."/logos_icon/logo_default/odontograma/estados_dientes/perno_punon.png' alt='' width='22px'>          Perno muñon</td> </tr>";
    $pdf .= "<tr> <td width='50%' class='fonttml'> <img src='".DOL_HTTP."/logos_icon/logo_default/odontograma/estados_dientes/restauracion.png' alt='' width='22px'>         Restauracion</td> </tr>";

    $pdf .= "
    </table> 
    
<br>";



$pdf .= "<table width='100%' >";

    $arrayStatus = array();
    for($u = 0; $u <= 1; $u++)
    {
        for($i = 0; $i <= count($dataNumeroDientes[$u]) -1; $i++)
        {
            $pieza_id = $dataNumeroDientes[$u][$i];

            $rs = $db->query("select * from tab_odontograma_update where fk_tratamiento = $idplantramiento and fk_paciente = $idpaciente and fk_diente = $pieza_id");

            while ($obpk = $rs->fetchObject())
            {

                if($pieza_id == $obpk->fk_diente)
                {
                    $obstatus = $db->query("select rowid , descripcion from tab_odontograma_estados_piezas where rowid = $obpk->fk_estado_pieza")->fetchObject();

                    $arrayStatus[] = array('img' => (obtenerImgDienteStatus($obpk->fk_diente, $obpk->fk_estado_pieza)) , 'status' => $obstatus->descripcion, 'jcaras' => json_decode($obpk->json_caras) , 'n_diente' => $obpk->fk_diente);
                }
            }
        }
    }

    //se pinta el img de los dientes  ----------------------------------------------------------------------------------
    // parte dearriba
    $pdf .= "<tr>";
        for ($c = 0; $c <= 15; $c++ )
        {
            $pdf .= "<td width='6.25'> ";
            $pdf .= "  <div style='display: block'>
                         ".$arrayStatus[$c]['img']."
                       </div>
                       <div style='display: block'>
                         ".anatomiaCarasView($arrayStatus[$c]['jcaras'], $arrayStatus[$c]['n_diente'])."
                        </div>";
            $pdf .= "</td>";
        }
    $pdf .= "</tr>";

    //parte de abajo
    $pdf .= "<tr>";
        for ($c = 16; $c <= 31; $c++ )
        {
            $pdf .= "<td width='6.25'> ".$arrayStatus[$c]['img']." ". anatomiaCarasView($arrayStatus[$c]['jcaras'], $arrayStatus[$c]['n_diente']) ." </td>";
        }
    $pdf .= "</tr>";


$pdf .= "</table>";

$pdf .= "<br>";
$pdf .= "<br>";

$pdf .= "<table width='100%' class='tables' style='padding: 3px;' >";
    $pdf .= "<thead >
                <tr style='background-color: #f2f2f2;'>
                    <th>Fecha</th>
                    <th>Pieza</th>
                    <th>Caras</th>
                    <th>Estados</th>
                </tr>
             </thead>";

    $pdf .= "<tbody >";

        foreach ($detalle_odontologicos as $key => $valor)
        {
            $pdf.= "<tr >";
                $pdf .= "<td class='tables' style='padding: 3px'>". $valor[0] ."</td>";
                $pdf .= "<td class='tables' style='padding: 3px'>". $valor[1] ."</td>";
                $pdf .= "<td class='tables' style='padding: 3px'>". $valor[2] ."</td>";
                $pdf .= "<td class='tables' style='padding: 3px'>". (strtoupper($valor[3])) ."</td>";
            $pdf.= "</tr>";

        }

    $pdf .= "</tbody>";
$pdf .= "</table>";


function obtenerImgDienteStatus($pieza, $idestado )
{
    $width = "width='28px;'";
    if($pieza==18||$pieza==17||$pieza==16){
        $width = "width='29.5px;'";
    }
    if($idestado!=0)
    {
        return "<img src='".DOL_HTTP."/logos_icon/logo_default/odontograma/numeros_dientes/dropwdon-menu-pieza".$pieza."/".$idestado.".png' $width >";
    }else{
        return "<img src='".DOL_HTTP."/logos_icon/logo_default/odontograma/numeros_dientes/dropwdon-menu-pieza".$pieza."/pieza".$pieza."-ai.png' $width >";
    }
}

function anatomiaCarasView( $arr_caras , $n_diente )
{
//    $color = " style='background-color: #9f191f;'  ";

    $caras      = "";
    $vestibular = (($arr_caras->vestibular=='true') ?'colorRed':'no');
    $distal     = (($arr_caras->distal=='true')     ?'colorRed':'');
    $palatino   = (($arr_caras->palatino=='true')   ?'colorRed':'');
    $oclusal    = (($arr_caras->oclusal=='true')    ?'colorRed':'');
    $mesial     = (($arr_caras->mesial=='true')     ?'colorRed':'');
    $lingual    = (($arr_caras->lingual=='true')    ?'colorRed':'');

//    echo '<pre>'; print_r($vestibular);
//    echo '<pre>'; print_r($distal);
//    echo '<pre>'; print_r($palatino);
//    echo '<pre>'; print_r($oclusal);
//    echo '<pre>'; print_r($mesial);

    if(count($arr_caras)>0)
    {
        #HEMIARCADA SUPERIOR DERECHA
        if(28 >= $n_diente )
        {
            if(18 >= $n_diente ){
                $caras = "
                        
                        <table style='margin-right: 10px; ' class='tablesCaras' >
                            <tr><td colspan='3' style='text-align: center'>".$n_diente."</td></tr>
                            <tr><td colspan='3'>&nbsp;</td></tr>
                            <tr>
                                <td></td>
                                <td class='boderTd $vestibular'  ></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td class='boderTd  $distal '  ></td> <td class='boderTd $oclusal ' ></td> <td class='boderTd $mesial '  ></td>
                            </tr>
                            <tr>
                                <td></td>
                                <td class='boderTd $palatino ' ></td>
                                <td></td>
                            </tr>
                        </table>";
            }else{
                if(28 >= $n_diente){
                    $caras = "
                        <table style='margin-right: 10px; '  class='tablesCaras' >
                           <tr><td colspan='3' style='text-align: center'>".$n_diente."</td></tr>
                           <tr><td>&nbsp;</td></tr>
                            <tr>
                                <td></td>
                                <td class='boderTd $vestibular'></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td class='boderTd $mesial '></td><td class='boderTd $oclusal '></td><td class='boderTd $distal'></td>
                            </tr>
                            <tr>
                                <td></td>
                                <td class='boderTd  $palatino'></td>
                                <td></td>
                            </tr>
                        </table>";
                }
            }
        }else{
            #HEMIARCADA SUPERIOR IZQUIERDA
            if(48 >= $n_diente)
            {
                if(38 >= $n_diente ){
                    $caras = "
                        <table style='margin-right: 10px; ' class='tablesCaras' >
                            <tr><td colspan='3' style='text-align: center'>".$n_diente."</td></tr>
                            <tr><td>&nbsp;</td></tr>
                            <tr>
                                <td></td>
                                <td class='boderTd $lingual'></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td class='boderTd $mesial'></td><td class='boderTd $oclusal'></td><td class='boderTd $distal'></td>
                            </tr>
                            <tr>
                                <td></td>
                                <td class='boderTd $vestibular'></td>
                                <td></td>
                            </tr>
                        </table>";
                }else{
                    if(48 >= $n_diente ){
                        $caras = "
                            <table style='margin-right: 10px; ' class='tablesCaras' >
                                <tr><td colspan='3' style='text-align: center'>".$n_diente."</td></tr>
                                <tr><td>&nbsp;</td></tr>
                                <tr>
                                    <td></td>
                                    <td class='boderTd $lingual'></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td class='boderTd $distal'></td><td class='boderTd $oclusal'></td><td class='boderTd $mesial'></td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td class='boderTd $vestibular'></td>
                                    <td></td>
                                </tr>
                            </table>";

                    }
                }
            }
        }

    }

    return $caras;
}


$header = ' 
    <table width="100%" style="vertical-align: bottom;  font-size: 10pt; color: black; margin-bottom: 10px">
        <tr>
             <td width="100%" align="left"><span style="font-size:28pt;">'.$InformacionEntity->nombre.'</span></td>
        </tr>
        <tr>
            <td WIDTH="33%">'.$ImagenLogoClinica.'</td>
        </tr>
        <tr>
            <td width="33%">'.$InformacionEntity->direccion.' <span style="font-size:10pt;"></span></td>
            <td width="33%" style="text-align: right;">Usuario:<span style="font-weight: bold;"> '.$loginUsuario.'</span></td>
        </tr>
        <tr>
            <td width="33%">'.$InformacionEntity->email.'<span style="font-size:10pt;"></span></td>
            <td width="33%" style="text-align: right;">Fecha de Impresión: <span style="font-weight: bold;">'.date("Y/m/d").'</span></td>
        </tr>
    </table> ';


$footer = '<!--<hr style="margin-bottom: 2px"><table width="100%" style="font-size: 10pt;">-->
              <table width="100%" style="border-collapse: collapse">
                    <tr>
                        <td width="50%" align="right">
                            <div  style="float: right">hoja:{PAGENO}</div>
                        </td>
                    </tr>
                </table>';

ob_end_clean();

$mpdf=new mPDF('c','LETTER','12px','',
    12, //left
    12, // right
    40, //top
    10, //bottom
    3, //header top
    3 //footer botoom
);

$mpdf->SetHTMLHeader($header,"E",true);
$mpdf->SetHTMLHeader($header,"O",true);
$mpdf->SetHTMLFooter($footer,"E",true);
$mpdf->SetHTMLFooter($footer,"O",true);

$mpdf->mirrorMargins = 1;	// Use different Odd/Even headers and footers and mirror margins

// Make it DOUBLE SIDED document with 4mm bleed
$mpdf->mirrorMargins = 1;
$mpdf->bleedMargin = 4;
// Set left to right text
$mpdf->SetDirectionality('ltr');
$mpdf->showImageErrors = 'true';
$mpdf->SetDisplayMode('fullpage');
$mpdf->SetTitle('Ficha Odontograma' );

$mpdf->WriteHTML($body.$pdf);

#Muestro la Informacion
$mpdf->Output('Ficha Odontograma.pdf', 'I');


?>