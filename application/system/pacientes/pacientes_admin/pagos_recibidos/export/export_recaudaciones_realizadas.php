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


//ARMAR EL PDF
$idplantratamiento = GETPOST('idplantratamiento');

//print_r($idplantratamiento); die();

if(empty($idplantratamiento)){

    echo '<h2>Error </h2>';
    die();
}

$paciente_nomb = $db->query("select  (select concat(a.nombre, ' ', a.apellido) as nom from tab_admin_pacientes a where a.rowid = ct.fk_paciente) as paciente
 from tab_plan_tratamiento_cab ct where rowid = $idplantratamiento")->fetchObject()->paciente;

$n_tratamiento = $db->query("select concat('Plan de Tratamiento N. ', c.numero) as nom from tab_plan_tratamiento_cab c where c.rowid = $idplantratamiento")->fetchObject()->nom;

$sql = "SELECT 
    cast(pc.fecha as date) as date, 
	concat('Plan de Tratamiento: ',' ',td.fk_plantratam_cab) as n_tratamiento,
	p.descripcion as prestacion, 
	if(td.fk_diente!=0,concat('Pieza: ',td.fk_diente),'') as pieza, 
	round(td.total,2) as total_prestacion, 
    round(sum(pd.amount),2)  abonado , 
    if(td.estadodet = 'A', 'Pendiente', if(td.estadodet = 'P' , 'En Proceso', if(td.estadodet = 'R' , 'Realizada' , ''))) as estado, 
    if(round(td.total,2)>round(sum(pd.amount),2), (round(td.total,2)-round(sum(pd.amount),2)),0) as  pendiente ,
    t.nom as tipo_p
FROM
     tab_plan_tratamiento_det td
        inner join 
    tab_pagos_independ_pacientes_det pd on td.rowid = pd.fk_plantram_det
        inner join
    tab_pagos_independ_pacientes_cab pc on pc.rowid = pd.fk_pago_cab
        inner join 
    tab_conf_prestaciones p on p.rowid = pd.fk_prestacion
        inner join 
    tab_bank_operacion t on t.rowid = pc.fk_tipopago
where pd.estado = 'A' ";


if(!empty($idplantratamiento)){
    $sql .= " and td.fk_plantratam_cab = ".$idplantratamiento;
}else{
    $sql .= " and td.fk_plantratam_cab = 0";
}

$sql .= " group by pd.fk_plantram_det,pd.fk_prestacion, pd.fk_plantram_cab, td.fk_diente";
$result = $db->query($sql);
$data = $result->fetchAll(PDO::FETCH_ASSOC);

//echo '<pre>'; print_r($sql); die();


$pdf .= '<style>
                
            .tables {  }
            .theader{ padding: 5px}
            .detalle{ border-bottom: 1px solid #f0f0f0;  padding: 3px 5px !important;}
            /*.listdetalle tr:nth-child(even){background-color: #f2f2f2;}*/
            
        </style>';

$pdf .= '<br>
        <table width="100%" class="tables" style="margin-top: 20px">
            
            <tr>
                <td COLSPAN="1"><b><h3>DETALLES DE PAGOS REALIZADOS DEL <span style="color: #1e3762">'.(strtoupper($n_tratamiento)).'</span> </h3></b></td>
                <td align="right" > <span> </td>
            </tr>
            <tr>
             <td COLSPAN="1"><b><h3>PACIENTE: <span style="color: #1e3762">'.(strtoupper($paciente_nomb)).'</span> </h3></b></td>   
            </tr>
        </table>';

$pdf .= "<br><table  width=\"100%\" class=\"tables\" style='border-collapse: collapse'>";
    $pdf .= "<thead>";
        $pdf .= "<tr style='background-color: #f0f0f0'>";
            $pdf .= "<th class='theader' width='6%' >Emitido</th>";
            $pdf .= "<th class='theader' width='40%'>Prestación/Servicios</th>";
            $pdf .= "<th class='theader' width='8%' style='text-align: right'>Total</th>";
            $pdf .= "<th class='theader' width='8%' style='text-align: right'>Abonado</th>";
            $pdf .= "<th class='theader' width='8%' style='text-align: right'>Pendiente</th>";
            $pdf .= "<th class='theader' width='10%' style='text-align: center'>Forma de Pago</th>";
            $pdf .= "<th class='theader' width='10%' style='text-align: right'>Estado</th>";
        $pdf .= "</tr>";
    $pdf .= "</thead>";

    $pdf .= '<tbody>';

    $Abonado    = 0;
    $Pendiente  = 0;
    $Total      = 0;

        foreach ($data as $k => $value){

            $pieza = (!empty($value['pieza']))?"<br><small style='display: block; color: #1e3762; font-weight: bold'>".$value['pieza']."</small>":"";

            $pdf .= '<tr>';
                    $pdf .= '<td class="detalle" width="6%">'.(str_replace('-','/', $value['date'])).'</td>';
                    $pdf .= '<td class="detalle" width="40%">'.$value['prestacion'].$pieza.'</td>';
                    $pdf .= '<td class="detalle" style=\'text-align: right\' width="8%">'.$value['total_prestacion'].'</td>';
                    $pdf .= '<td class="detalle" style=\'text-align: right\' width="8%">'.$value['abonado'].'</td>';
                    $pdf .= '<td class="detalle" style=\'text-align: right\' width="10%">'.$value['pendiente'].'</td>';
                    $pdf .= '<td class="detalle" style=\'text-align: right\' width="10%">'.strtoupper($value['tipo_p']).'</td>';
                    $pdf .= '<td class="detalle" style=\'text-align: right\'>'.$value['estado'].'</td>';

            $pdf .= '</tr>';

            $Abonado    += (double)$value['abonado'];
            $Pendiente  += (double)$value['pendiente'];
            $Total      += (double)$value['total_prestacion'];


        }

        $pdf .= "<tr>";
            $pdf .= "<td class='detalle' style='text-align: right' colspan='2'><b>Total:</b></td>";
            $pdf .= "<td class='detalle' style='text-align: right'  ><b>".number_format($Total, 2, '.', '')."</b></td>";
            $pdf .= "<td class='detalle' style='text-align: right'  ><b>".number_format($Abonado, 2, '.', '')."</b></td>";
            $pdf .= "<td class='detalle' style='text-align: right'  ><b>".number_format($Pendiente, 2, '.', '')."</b></td>";
        $pdf .= "</tr>";

    $pdf .= '</tbody>';
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
$mpdf=new mPDF('c','LETTER','13px','',
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
$mpdf->SetTitle('Recaudaciones Realizadas' );

$mpdf->WriteHTML($body.$pdf);


$mpdf->Output('Pagos Paciente.pdf', 'I');


?>