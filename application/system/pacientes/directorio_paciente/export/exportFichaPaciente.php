<?php


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

$idpaciente = GETPOST("idpac");
$pdf = null;


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
    <table  width='100%' style='margin-top: 25px'>
        <tr  style='width: 100%; '>
            <td style='text-align: left'> <h2> Datos Personales del Paciente</h2> </td>
        </tr>
    </table>    
        ";


$object = array();
$sql = "select * from tab_admin_pacientes where rowid = ".$idpaciente;
$result = $db->query($sql);
if($result && $result->rowCount()>0){
    $object = $result->fetchObject();
}

$iconPaciente = $object->icon;

if(!empty($iconPaciente))
    $iconimg = $DirectorioImgClinicaHttp.'/'.$iconPaciente;
else{
    $iconPaciente = 'file_No_found.png';
    $iconimg = $DirectorioImgClinicaHttp.'/'.$iconPaciente;
}

//print_r($iconPaciente); die();

if( file_exists(DOL_DOCUMENT.'/logos_icon/icon_logos_'.$_SESSION['entidad'].'/'.$iconPaciente) != 1 ){
    $iconimg = DOL_HTTP.'/logos_icon/logo_default/avatar_none.PNG';
}


$pdf .= "
    <table width='100%' style='margin-top: 25px; font-size: 1.2rem; border: 1px solid #e2e2e2; border-collapse: collapse;'>
            
            <tr>
                <td colspan='2' align='center' style='padding: 5px'>
                     <img src='".$iconimg."' alt='' style='width: 30%; height: 30%'>   
                </td>
            </tr>
            
            <tr>
                <td width='50%' style='text-align: center; background-color: #e2e2e2; padding: 8px; border-top: 1px solid #ffffff'><b>Nombre</b></td> 
                <td width='50%' style='text-align: center;border-top: 1px solid #e2e2e2; padding: 8px'> ".$object->nombre." </td>
            </tr>
            <tr>
                <td width='50%' style='text-align: center; background-color: #e2e2e2;padding: 8px; border-top: 1px solid #ffffff'><b>Apellido</b></td> 
                <td width='50%' style='text-align: center; border-top: 1px solid #e2e2e2;padding: 8px'>".$object->apellido."</td>
            </tr>
            <tr>
                <td width='50%' style='text-align: center; background-color: #e2e2e2;padding: 8px; border-top: 1px solid #ffffff'><b>Cedula</b></td> 
                <td width='50%' style='text-align: center; border-top: 1px solid #e2e2e2;padding: 8px'>".$object->ruc_ced."</td>
            </tr>
            <tr>
                <td width='50%' style='text-align: center; background-color: #e2e2e2;padding: 8px; border-top: 1px solid #ffffff'><b>E-mail</b></td> 
                <td width='50%' style='text-align: center; border-top: 1px solid #e2e2e2;padding: 8px'>".$object->email."</td>
            </tr>
            <tr>
                <td width='50%' style='text-align: center; background-color: #e2e2e2;padding: 8px; border-top: 1px solid #ffffff'><b>Género</b></td> 
                <td width='50%' style='text-align: center; border-top: 1px solid #e2e2e2;padding: 8px'><b>".(strtoupper($object->sexo))."</b></td>
            </tr>
            <tr>
                <td width='50%' style='text-align: center; background-color: #e2e2e2;padding: 8px; border-top: 1px solid #ffffff'><b>Fecha de Nacimiento</b></td> 
                <td width='50%' style='text-align: center; border-top: 1px solid #e2e2e2;padding: 8px'>".$object->fecha_nacimiento."</td>
            </tr>
            <tr>
                <td width='50%' style='text-align: center; background-color: #e2e2e2;padding: 8px; border-top: 1px solid #ffffff'><b>Ciudad</b></td> 
                <td width='50%' style='text-align: center; border-top: 1px solid #e2e2e2;padding: 8px'>".$object->fk_ciudad."</td>
            </tr>
            <tr>
                <td width='50%' style='text-align: center; background-color: #e2e2e2;padding: 8px; border-top: 1px solid #ffffff'><b>Dirección</b></td> 
                <td width='50%' style='text-align: center; border-top: 1px solid #e2e2e2;padding: 8px'>".$object->direccion."</td>
            </tr>
            <tr>
                <td width='50%' style='text-align: center; background-color: #e2e2e2;padding: 8px; border-top: 1px solid #ffffff'><b>Teléfono Fijos</b></td> 
                <td width='50%' style='text-align: center; border-top: 1px solid #e2e2e2;padding: 8px'>".$object->telefono_fijo."</td>
            </tr>
            <tr>
                <td width='50%' style='text-align: center; background-color: #e2e2e2;padding: 8px'><b>Teléfono celular</b></td> 
                <td width='50%' style='text-align: center; border-top: 1px solid #e2e2e2;padding: 8px'>".$object->telefono_movil."</td>
            </tr>
            <tr>
                <td width='50%' style='text-align: center; background-color: #e2e2e2;padding: 8px; border-top: 1px solid #ffffff'><b>Actividad Profecional</b></td> 
                <td width='50%' style='text-align: center; border-top: 1px solid #e2e2e2;padding: 8px'>".$object->actividad_profecion."</td>
            </tr>
            <tr>
                <td width='50%' style='text-align: center; background-color: #e2e2e2;padding: 8px; border-top: 1px solid #ffffff'><b>Referencia</b></td> 
                <td width='50%' style='text-align: center; border-top: 1px solid #e2e2e2;padding: 8px'>".$object->referencia."</td>
            </tr>
            <tr>
                <td width='50%' style='text-align: center; background-color: #e2e2e2;padding: 8px; border-top: 1px solid #ffffff'><b>Observacion</b></td> 
                <td width='50%' style='text-align: center; border-top: 1px solid #e2e2e2;padding: 8px'>".$object->observacion."</td>
            </tr>
    </table>";



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
$mpdf->SetTitle('Ficha datos Personales (Paciente)' );

$mpdf->WriteHTML($body.$pdf);


$mpdf->Output('Ficha Datos Personales (Paciente).pdf', 'I');
//print_r($mpdf); die();
//exit;



?>