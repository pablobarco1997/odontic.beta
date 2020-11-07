<?php

require_once '../../../../application/config/lib.global.php';

session_start();

//print_r(DOL_HTTP); die();
if(!isset($_SESSION['is_open']))
{
    header("location:".DOL_HTTP."/application/system/login");
}

require_once  DOL_DOCUMENT .'/application/system/conneccion/conneccion.php';    //Coneccion de Empresa
require_once  DOL_DOCUMENT .'/public/lib/mpdf60/mpdf.php';
require_once  DOL_DOCUMENT .'/application/controllers/controller.php';

/**SE CREA LAS VARIABLES DE INICIO**/
$cn             = new ObtenerConexiondb();                    //Conexion global Empresa Fija
$db             = $cn::conectarEmpresa($_SESSION['db_name']); //coneccion de la empresa variable global
$iddoc          = GETPOST("idform");
$document_blank = GETPOST("docblank");

$pdf .= '<style>
            .tables {
                border-collapse: collapse;
                 width="100%"; 
               
            }
            
            .tables {
                border: 1px solid black;
                font-size: 1.1rem;
                padding: 3px;
            }
            .countName {
                display: block;
                width: 100%;
                font-size: 1.1rem;
            }
            
            </style>';


#echo '<pre>'; print_r($_SESSION); die();
$InformacionEntity = (object)array(
    'nombre'            => $_SESSION['nombreClinica'],
    'email'             => $_SESSION['emailClinica'],
    'direccion'         => $_SESSION['direccionClinica'],
    'entidad'           => $_SESSION['entidad'],
    'id_Entidad'        => $_SESSION['id_Entidad']
);

$loginUsuario = $_SESSION['usuario']; #Login Inicio de Sesion
$pdf = null;

$result = $db->query("SELECT * FROM tab_documentos_clinicos where rowid = $iddoc ");
if($result){
    if($result->rowCount()==1){
        $object = $result->fetchObject();
    }
}


if($document_blank=="blank"){//imprime el documento el blanco
    $FormDocument = "FormDocumentosEntity_".(base64_encode($InformacionEntity->id_Entidad."".$InformacionEntity->entidad));
    $linkDirect = DOL_DOCUMENT."/application/system/documentos_clinicos/form_documentos/".$FormDocument."/".$object->element_text.".html";
    $GetContentFile = file_get_contents($linkDirect);
    print_r($GetContentFile); die();
}
if($document_blank=="data"){
    $datadoct   = GETPOST("dataPrint");
    $text = base64_decode($datadoct);
    $pdf .= utf8_encode($text);
//    print_r($pdf); die();
}


$footer = '<!--<hr style="margin-bottom: 2px"><table width="100%" style="font-size: 10pt;">-->
<br>
          <table>
                <tr>
                    <td width="50%">
                        <div align="left">'. $InformacionEntity->email .'</div>
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
            <td width="33%">'.$InformacionEntity->direccion.' <span style="font-size:10pt;"></span></td>
            <td width="33%" style="text-align: right;">Usuario:<span style="font-weight: bold;"> '.$loginUsuario.'</span></td>
        </tr>
        <tr>
            <td width="33%">'.$InformacionEntity->email.'<span style="font-size:10pt;"></span></td>
            <td width="33%" style="text-align: right;">Fecha: <span style="font-weight: bold;">{DATE j/m/Y}</span></td>
        </tr>
    </table>
    ';


$mpdf=new mPDF('c','LETTER','11px','Calibri',
    12, //left
    12, // right
    23, //top
    18, //bottom
    3, //header top
    3 //footer botoom
);

$mpdf->allow_charset_conversion=true;
$mpdf->charset_in='UTF-8';

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
$mpdf->SetTitle($object->element_text);

$mpdf->WriteHTML($body.$pdf);

ob_end_clean();

//Descarga el fichero en un ruta
//$mpdf->Output(($object->element_text.'.pdf'),'F');
$mpdf->Output(($object->element_text.'.pdf'), 'I');

#https://es.stackoverflow.com/questions/216777/como-descargar-archivos-pdf-mediante-jquery-generados-en-php
//print_r($FilePdf); die();


?>