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


$n_tratamiento = $db->query("select ifnull(c.edit_name, concat('Plan de Tratamiento #', c.numero)) as nom from tab_plan_tratamiento_cab c where c.rowid = $idplantratamiento")->fetchObject()->nom;

$sql = "SELECT 
    cast(pc.fecha as date) as date, 
	concat('Plan de Tratamiento #',' ',td.fk_plantratam_cab) as n_tratamiento,
	concat(p.descripcion , if(td.fk_diente!=0,concat('  Pieza: ',td.fk_diente),'') ) as prestacion, 
	round(td.total,2) as total_prestacion, 
    round(sum(pd.amount),2)  abonado , 
    if(round(td.total,2)=round(sum(pd.amount),2), 'Prestación Cancelada', 'Pendiente') as estado, 
    if(round(td.total,2)>round(sum(pd.amount),2), (round(td.total,2)-round(sum(pd.amount),2)),0) as  pendiente,
    (select t.nom from tab_bank_operacion t where t.rowid = pc.fk_tipopago) as tipo_p
FROM
tab_plan_tratamiento_det td , 
tab_pagos_independ_pacientes_det pd, 
tab_pagos_independ_pacientes_cab pc, 
tab_conf_prestaciones p
where 
td.rowid = pd.fk_plantram_det
and p.rowid = pd.fk_prestacion
and pc.rowid = pd.fk_pago_cab ";

if(!empty($idplantratamiento)){
    $sql .= " and td.fk_plantratam_cab = ".$idplantratamiento;
}

$sql .= " group by pd.fk_prestacion, pd.fk_plantram_cab, td.fk_diente ";
//echo '<pre>'; print_r($sql); die();
$result = $db->query($sql);
$data = $result->fetchAll(PDO::FETCH_ASSOC);



$pdf .= '<style>
                
            .tables {  }
            .theader{ border: 1px solid black;}
            .detalle{ border: 1px solid black;  padding: 1.3px !important;}
            /*.listdetalle tr:nth-child(even){background-color: #f2f2f2;}*/
            
        </style>';

$pdf .= '<br>
        <table width="100%" class="tables" style="margin-top: 20px">
            
            <tr>
                <td><b><h3>PAGOS REALIZADOS DEL '.(strtoupper($n_tratamiento)).' </h3></b></td>
                <td align="right" > <span> </td>
            </tr>
        </table>';

$pdf .= "<br><table  width=\"100%\" class=\"tables\" style='border-collapse: collapse'>";
    $pdf .= "<thead>";
        $pdf .= "<tr style='background-color: #f0f0f0'>";
            $pdf .= "<th class='theader'>Fecha</th>";
            $pdf .= "<th class='theader'>Prestación</th>";
            $pdf .= "<th class='theader'>Total</th>";
            $pdf .= "<th class='theader'>Abonado</th>";
            $pdf .= "<th class='theader'>Pendiente</th>";
            $pdf .= "<th class='theader'>Forma</th>";
            $pdf .= "<th class='theader'>Estado</th>";
        $pdf .= "</tr>";
    $pdf .= "</thead>";

    $pdf .= '<tbody>';

        foreach ($data as $k => $value){

            $pdf .= '<tr>';
                    $pdf .= '<td class="detalle">'.(str_replace('-','/', $value['date'])).'</td>';
                    $pdf .= '<td class="detalle">'.$value['prestacion'].'</td>';
                    $pdf .= '<td class="detalle">'.$value['total_prestacion'].'</td>';
                    $pdf .= '<td class="detalle">'.$value['abonado'].'</td>';
                    $pdf .= '<td class="detalle">'.$value['pendiente'].'</td>';
                    $pdf .= '<td class="detalle">'.$value['tipo_p'].'</td>';

                    if($value['estado']=='Prestación Cancelada'){
                        $pdf .= '<td class="detalle"> <span style="background-color: #ccff99">'.$value['estado'].'</span></td>';
                    }else{

                        $pdf .= '<td class="detalle">'.$value['estado'].'</td>';
                    }

            $pdf .= '</tr>';


        }

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
$mpdf->SetTitle('Recaudaciones Realizadas' );

$mpdf->WriteHTML($body.$pdf);


$mpdf->Output('Recaudaciones Realizadas.pdf', 'I');


?>