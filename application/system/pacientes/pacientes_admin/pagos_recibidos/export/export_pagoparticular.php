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

$DirectorioImgClinicaHttp = DOL_HTTP.'/logos_icon/icon_logos_'.$_SESSION['entidad'];
if(isset($_SESSION['logoClinica'])) {
    if($_SESSION['logoClinica']!="" && !file_exists($DirectorioImgClinicaHttp)){
        $iconClinica = $DirectorioImgClinicaHttp.'/'.$_SESSION['logoClinica'];
    }else{
        $iconClinica = DOL_HTTP.'/logos_icon/logo_default/none-icon-20.jpg';
    }
}else{
    $iconClinica = DOL_HTTP.'/logos_icon/logo_default/none-icon-20.jpg';
}
$ImagenLogoClinica = "<img src='".$iconClinica."' style='width:40px; height: 40px; border-radius: 100%;' >";

/**SE CREA LAS VARIABLES DE INICIO**/
$cn = new ObtenerConexiondb();                    //Conexion global Empresa Fija
$db = $cn::conectarEmpresa($_SESSION['db_name']); //coneccion de la empresa variable global


$InformacionEntity = (object)array(
    'nombre'            => $_SESSION['nombreClinica'],
    'email'             => $_SESSION['emailClinica'],
    'direccion'         => $_SESSION['direccionClinica'],
    'telefonoClinica'   => $_SESSION['telefonoClinica'],
    'logoClinica'       => DOL_HTTP.'/logos_icon/icon_logos_'.$_SESSION['entidad'].'/'.$_SESSION['logoClinica'],
    'entidad'           => $_SESSION['entidad'],
);


$loginUsuario = $_SESSION['usuario']; #Login Inicio de Sesion

$pdf = null;

/**------------------------------------------------------------------------------------------------------------------**/


$dataPagosCab = [];
$dataPagosDet = [];

$idpaciente   = GETPOST('idpac');
$idpago       = GETPOST('npag');

$queryCabPag = "SELECT 
          cast(fecha as  date) as fecha , 
          n_fact_boleta ,
          observacion ,
          (select c.descripcion from tab_tipos_pagos c where c.rowid = fk_tipopago) as tp
      FROM tab_pagos_independ_pacientes_cab where  rowid = $idpago; ";
$dataPagosCab   = $db->query($queryCabPag)->fetchObject();


$queryDetPag = "SELECT 
   concat('AGR_',d.rowid) AS codpag ,
    (SELECT 
            c.descripcion
        FROM
            tab_conf_prestaciones c
        WHERE
            c.rowid = d.fk_prestacion) AS prestacion,
    (SELECT 
            IFNULL(dt.fk_diente, ' ')
        FROM
            tab_plan_tratamiento_det dt
        WHERE
            dt.rowid = d.fk_plantram_det) AS diente,
    d.amount
FROM
    tab_pagos_independ_pacientes_det d
WHERE
    d.fk_paciente = $idpaciente
        AND d.fk_pago_cab = $idpago";

$rsDet = $db->query($queryDetPag);
if($rsDet && $rsDet->rowCount()>0){

    while ($dp = $rsDet->fetchObject()){

        $prestacion = null;

        if($dp->diente==0){
            $prestacion = "&nbsp;&nbsp;&nbsp;&nbsp;".$dp->prestacion;
        }else{
            $prestacion = "&nbsp;&nbsp;&nbsp;&nbsp;".$dp->prestacion ." ". "<img src='".DOL_HTTP."/logos_icon/logo_default/diente.png' width='10px' height='10px' > ".$dp->diente;
        }
        $dataPagosDet[] = (object)array(
              'codpag'   => $dp->codpag,
              'prestacion' => $prestacion,
              'amount'     => $dp->amount,
        );
    }
}

//echo '<pre>'; print_r($dataPagosCab); die();

$objectInfoPaciente = getnombrePaciente($idpaciente);

$pdf .= '<style>
                
            .tables {  }
            .theader{ border: 1px solid black;}
            .detalle{ border: 1px solid black;  padding: 1px !important;}
            /*.listdetalle tr:nth-child(even){background-color: #f2f2f2;}*/
            
        </style>';


$pdf .= '<br>
        <table width="100%" class="tables" style="margin-top: 20px">
            
            <tr>
                <td><b><h3>COMPROBANTE DE RECAUDACIÓN</h3></b></td>
                <td align="right" > <span> </td>
            </tr>
        </table>';



$pdf .= '<br>';
$pdf .= '<table width="100%" class="tables">
            <tr>
                <td>
                    <table width="100%" class="tables">
                        <tr>
                            <td class="" style="width: 20%"><b>Paciente:</b></td> 
                            <td class="" style="text-align: left"> '.($objectInfoPaciente->nombre.' '.$objectInfoPaciente->apellido).' </td> 
                        </tr>
                        <tr>
                            <td class="" style="width: 20%"><b>C.I.:</b></td> 
                            <td class="" style="text-align: left"> '.($objectInfoPaciente->ruc_ced).' </td> 
                        </tr>
                        <tr>
                            <td class="" style="width: 20%"><b>Clinica:</b></td> 
                            <td class="" style="text-align: left"> '.$InformacionEntity->nombre.' </td> 
                        </tr>
                        <tr>
                            <td class="" style="width: 20%"><b>Dirección:</b></td> 
                            <td class="">'.$InformacionEntity->direccion.'</td>
                        </tr>
                        <tr>
                            <td class="" style="width: 20%"><b>Telefono:</b></td>
                            <td class="">'.$InformacionEntity->telefonoClinica.'</td>
                        </tr>
                        <tr>
                            <td class="" style="width: 20%"><b>E-mail:</b></td>
                            <td class="">'.$InformacionEntity->email.'</td>
                        </tr>
                        <tr>
                            <td class="" style="width: 20%"><b>Descripción:</b></td>
                            <td class="">'.$dataPagosCab->observacion.'</td>
                        </tr>
                    </table> 
                </td>
                
                <td>
                    <table width="100%" class="tables">
                        <tr>
                            <td align="center">
                                <span><b>Fecha </b></span>
                                <hr>
                                <span>'.$dataPagosCab->fecha.'</span>
                            </td>
                        </tr>
                        <tr>
                            <td align="center">
                                <span><b>Nº de Comprobante</b></span>
                                <hr>
                                <span>'.$dataPagosCab->n_fact_boleta.'</span>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
           
        </table>';

$pdf .= ' <br><br>
    <table width="100%" class="tables listdetalle" style="border-collapse: initial" >
        <thead>
            <tr class="theader" style="background-color: #f0f0f0">
                <th class="theader">Nº AGR</th>
                <th class="theader">PRESTACIONES</th>
                <th class="theader">ABONO</th>
            </tr>
        </thead>
        <tbody>';

        $amountTotal = 0;
        foreach ($dataPagosDet as $key => $item) {


            $pdf.= '<tr>
                          <td  class="detalle">'.$item->codpag.'</td>
                          <td  class="detalle">'.$item->prestacion.'</td>
                          <td  class="detalle">'.(number_format($item->amount,2,'.',',')).'</td>  
                    </tr>';


            $amountTotal += (double)$item->amount;
        }

        $pdf .= '<tr> 
                    <td  class="detalle" colspan="2"> <b>TOTAL PAGOS</b> </td> 
                    <td  class="detalle"><b> '.(number_format($amountTotal,2,'.',',')).' </b></td> 
                </tr>';

$pdf .='</tbody>
    </table>';

$header = ' 
    <table width="100%" style="vertical-align: bottom;  font-size: 10pt; color: black;">
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
    </table> 
    ';

ob_end_clean();
$mpdf=new mPDF('c','LETTER','12px','',
    12, //left
    12, // right
    40, //top
    18, //bottom
    3, //header top
    3 //footer botoom
);


$mpdf->SetHTMLHeader($header,"E",true);
$mpdf->SetHTMLHeader($header,"O",true);
$mpdf->SetHTMLFooter($footer,"E",true);
$mpdf->SetHTMLFooter($footer,"O",true);

// Make it DOUBLE SIDED document with 4mm bleed
$mpdf->mirrorMargins = 1;
$mpdf->bleedMargin = 4;
// Set left to right text
$mpdf->SetDirectionality('ltr');
$mpdf->showImageErrors = 'true';
$mpdf->SetDisplayMode('fullpage');
$mpdf->SetTitle('Comprobante de Pago' );

$mpdf->WriteHTML($body.$pdf);


$mpdf->Output('Comprobante de Pago.pdf', 'I');

//print_r($pdf);
//die();

?>