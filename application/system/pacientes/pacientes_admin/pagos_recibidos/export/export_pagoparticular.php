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
          cast(pay.fecha as  date) as fecha , 
          n_fact_boleta ,
          observacion ,
          b.nom
          FROM 
          tab_pagos_independ_pacientes_cab pay 
             left join
          tab_bank_operacion b on b.rowid = pay.fk_tipopago
      where  pay.rowid = $idpago; ";
$dataPagosCab   = $db->query($queryCabPag)->fetchObject();



$queryDetPag = "SELECT 
	d.fk_prestacion,
	d.fk_plantratam_cab as id_tratamiento , 
	p.descripcion as nom, 
    d.fk_diente as pieza , 
    d.cantidad , 
    if(d.iva = 12, ((d.precio_u * d.cantidad ) * 12) / 100 , 0) calculado_iva,
    d.precio_u as precioU , 
    (d.total*d.desc_adicional/100) as descuento , 
    (d.total - (d.total*d.desc_adicional/100)) as Subtotal , 
    sum(pd.amount) as monto_abonado
FROM
     tab_plan_tratamiento_det as d
		inner join 
	tab_pagos_independ_pacientes_det pd  on  pd.fk_plantram_det =  d.rowid  and pd.estado = 'A' 
		inner join 
	tab_conf_prestaciones p on p.rowid = d.fk_prestacion
WHERE
      pd.fk_pago_cab = $idpago
group by pd.fk_plantram_det";

$result = $db->query($queryDetPag);
if($result && $result->rowCount()>0){
    $dataPagosDet = $result->fetchAll(PDO::FETCH_ASSOC);
}

//echo '<pre>'; print_r($queryDetPag); die();

$objectInfoPaciente = getnombrePaciente($idpaciente);

$pdf .= '<style>
                
            .tables {  }
            .theader{ padding: 4px}
            .detalle{ border-bottom: 1px solid #f0f0f0;  padding: 1px !important;}
            
        </style>';


$pdf .= '<br>
        <table width="100%" class="tables" style="margin-top: 20px">
            
            <tr>
                <td><b><h2>COMPROBANTE DE RECAUDACIÓN</h2></b></td>
                <td align="right" > <span> </td>
            </tr>
        </table>';



$pdf .= '<br>';
$pdf .= '<table width="100%" class="tables">
            <tr>
                <td width="60%">
                    <table width="100%" class="tables">
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
                        <!--
                        <tr style="display: none">
                            <td class="" style="width: 20%"><b>Descripción:</b></td>
                            <td class="">'.$dataPagosCab->observacion.'</td>
                        </tr>
                        -->
                    </table> 
                </td>
                
                <td width="40%">
                    <table width="100%" class="tables">
                        <tr>
                            <td align="center" style="width: 30%">
                                <span><b>Fecha </b></span>
                                <hr>
                                <span>'.date('Y/m/d', strtotime($dataPagosCab->fecha)).'</span>
                            </td>
                        </tr>
                        <tr>
                            <td align="center" style="width: 30%">
                                <span><b>Nº de Factura</b></span>
                                <hr>
                                <span>'.$dataPagosCab->n_fact_boleta.'</span>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            
            <tr>
                <td style="width: 60%">
                    <table class="tables" width="100%">
                        <tr>
                            <td colspan="2" >
                                <b><p style="color: #1e3762">Cliente</p></b>
                            </td>
                        </tr>
                        <tr>
                        <td class="" style="width: 20%"><b>Paciente:</b></td> 
                            <td class="" style="text-align: left"> '.($objectInfoPaciente->nombre.' '.$objectInfoPaciente->apellido).' </td> 
                        </tr>
                        <tr>
                            <td class="" style="width: 20%"><b>C.I.:</b></td> 
                            <td class="" style="text-align: left"> '.($objectInfoPaciente->ruc_ced).' </td> 
                        </tr>
                        <tr>
                            <td class="" style="width: 20%"><b>Dirección:</b></td> 
                            <td class="" style="text-align: left"> '.($objectInfoPaciente->direccion).' </td> 
                        </tr>
                    </table>
                </td>
                
                <td style="width: 40%"></td>
            </tr>
           
        </table>';

$pdf .= ' <br><br>
    <table width="100%" class="tables listdetalle" style="border-collapse: initial" >
            <thead>
                <tr class="theader" style="background-color: #f0f0f0">
                    <th class="theader">Descripción</th>
                    <th class="theader" align="right">Cantidad</th>
                    <th class="theader" align="right">P.Unitario</th>
                    <th class="theader" align="right">Descuento</th>
                    <th class="theader" align="right">Sub. Total</th>
                    <th class="theader" align="right">Abonado</th>
                </tr>
            </thead>
        <tbody>';

//echo '<pre>';print_r($dataPagosDet); die();

        $subTotal = 0;
        $descuento = 0;
        $abonado = 0;
        $iva = 0;
        foreach ($dataPagosDet as $key => $item) {

            $pieza = ($item['pieza']!=0)?"<small style='color: #1e3762; font-weight: bold'>Pieza: ".$item['pieza']."</small>":"";
            $servcio = $item['nom']."<br>".$pieza;

            $pdf.= '<tr>
                          <td  class="detalle">'.$servcio.'</td>
                          <td  class="detalle" align="right">'.number_format($item['cantidad'], 2, '.', '').'</td>
                          <td  class="detalle" align="right">'.number_format($item['precioU'], 2, '.', '').'</td>
                          <td  class="detalle" align="right">'.number_format($item['descuento'], 2, '.', '').'</td>
                          <td  class="detalle" align="right">'.number_format($item['Subtotal'], 2, '.', '').'</td>
                          <td  class="detalle" align="right" style="color:#1e3762; font-weight: bold ">'.number_format($item['monto_abonado'],2,'.','').'</td>  
                    </tr>';


            $subTotal  += (double)$item['Subtotal'];
            $descuento += (double)$item['descuento'];
            $abonado   += (double)$item['monto_abonado'];
            $iva       += (double)$item['calculado_iva'];
        }

$pdf .='</tbody>
    </table>';

        $pdf .= "<table width='100%'>";
            $pdf .= "<tr>";
            $pdf .= "<td width='50%'>&nbsp;</td>";
            $pdf .= "<td width='50%'>
                                    <table width='100%' class='tables'>
                                        <tr>
                                            <td>Sub. Total</td> 
                                            <td style='text-align: right'>$subTotal</td>
                                        </tr>
                                         <!--<tr style='display: none'>
                                            <td>Descuento</td> 
                                            <td style='text-align: right'>$descuento</td>
                                        </tr>-->
                                        <tr>
                                            <td>Iva</td> 
                                            <td style='text-align: right'>".$iva."</td>
                                        </tr>
                                        <tr>
                                            <td style='color: #1e3762; '><h4>Abonado</h4></td> 
                                            <td style='color: #1e3762;text-align: right;'>
                                                <h4>".($abonado)."</h4>
                                            </td>
                                        </tr>
                                       <tr >
                                            <td><h4>Total <small>(sub.total - abonado)</small></h4></td> 
                                            <td style='text-align: right'>
                                                 <h4>".($subTotal-$abonado)."</h4>
                                            </td>
                                        </tr>
                                    </table>
                                </td>";
            $pdf .= "</tr>";
        $pdf .= "</table>";

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
$mpdf=new mPDF('c','LETTER','14px','',
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