<?php

//header('Content-type: application/pdf');

require_once '../../../../config/lib.global.php';
session_start();

if(!isset($_SESSION['is_open']))
{
    header("location:".DOL_HTTP."/application/system/login");
}

require_once  DOL_DOCUMENT .'/application/system/conneccion/conneccion.php';    //Coneccion de Empresa
require_once  DOL_DOCUMENT .'/public/lib/mpdf60/mpdf.php';
require_once  DOL_DOCUMENT .'/application/controllers/controller.php';


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

/**SE CREA LAS VARIABLES DE INICIO**/
$cn = new ObtenerConexiondb();                    //Conexion global Empresa Fija
$db = $cn::conectarEmpresa($_SESSION['db_name']); //coneccion de la empresa variable global


$InformacionEntity = (object)array(
    'nombre'            => $_SESSION['nombreClinica'],
    'email'             => $_SESSION['emailClinica'],
    'direccion'         => $_SESSION['direccionClinica'],
);


$loginUsuario = $_SESSION['usuario']; #Login Inicio de Sesion
$pdf = null;
$id = GETPOST('id');

#echo '<pre>';print_r($ImagenLogoClinica);die();

$datos = [];
$sql = "SELECT * FROM tab_admin_pacientes  WHERE rowid in($id)";
$rs  = $db->query($sql);
if($rs->rowCount()>0){
    while ($obj = $rs->fetchObject())
    {
        $datos[] = (object)array(
            'nombre'          => $obj->nombre . ' '.  $obj->apellido,
            'ruc_ced'         => $obj->ruc_ced,
            'email'           => $obj->email,
            'numeroCelular'   => $obj->telefono_movil,
            'genero'          => $obj->sexo,
        );
    }
}


$pdf .= '<style>
            .tables {
                 border-collapse: collapse;
              
            }
           
            .tables {
                border: 1px solid black;
                font-size: 1.1rem;
                padding: 3px; 
            }
            
            
            </style>';

$pdf .= "
    <br>
    <table  width='100%' class='tables' style='margin-top: 25px'>
        <tr  style='width: 100%; '>
            <td style='text-align: center; background-color: #f0f0f0' > <h2> Directorio de Pacientes</h2> </td>
        </tr>
    </table>    
        ";


//echo '<pre>';
//print_r($datos);
//die();
//LISTA DE PACIENTES

$pdf .= "<table width='100%' class=\"tables\">";
    $pdf .= "<thead>
                <tr class='tables' style=\"background-color: #f0f0f0\">
                    <th class='tables' style='font-size: 1.2rem'>Paciente</th>
                    <th class='tables' style='font-size: 1.2rem'>Cedula</th>
                    <th class='tables' style='font-size: 1.2rem'>E-mail</th>
                    <th class='tables' style='font-size: 1.2rem'>Telefono</th>
                    <th class='tables' style='font-size: 1.2rem'>Género</th>
                </tr>
            </thead>
            <tbody>";

    foreach ($datos as $key => $val){

        $pdf .= "<tr>";
            $pdf .= "<td width='40%' class=\"tables\">".$val->nombre."</td>";
            $pdf .= "<td width='15%' class=\"tables\">".$val->ruc_ced."</td>";
            $pdf .= "<td width='35%' class=\"tables\">".$val->email."</td>";
            $pdf .= "<td width='15%' class=\"tables\">".$val->numeroCelular."</td>";
            $pdf .= "<td width='15%' class=\"tables\"><b>".strtoupper($val->genero)."</b></td>";
        $pdf .= "</tr>";

    }

    $pdf .= "</tbody>";
$pdf .= "</table>";


$footer = '<!--<hr style="margin-bottom: 2px"><table width="100%" style="font-size: 10pt;">-->
<br>
          <table>
                <tr>
                    <td width="50%">
                        <div align="left" style="display: none">'. $InformacionEntity->email .'</div>
                    </td>
                    <td width="50%" align="right">
                        <!--<div  style="float: right">Pagina:{PAGENO}</div>-->
                    </td>
                </tr>
            </table>';




$header = ' 
    <table width="100%" style="vertical-align: bottom; font-family: Arial; font-size: 9pt; color: black;">
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


$mpdf=new mPDF('c','LETTER','11px','Calibri',
    12, //left
    12, // right
    23, //top
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
$mpdf->SetTitle('Directorio de Paciente' );

$mpdf->WriteHTML($body.$pdf);


$mpdf->Output('Directorio de Paciente.pdf', 'I');
//print_r($mpdf); die();
//exit;

?>