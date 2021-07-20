
<?php

require_once '../../../../../config/lib.global.php';
session_start();

if(!isset($_SESSION['is_open']))
{
    header("location:".DOL_HTTP."/application/system/login");
}


require_once  DOL_DOCUMENT .'/application/system/conneccion/conneccion.php';
require_once  DOL_DOCUMENT .'/public/lib/mpdf60/mpdf.php';
require_once  DOL_DOCUMENT .'/application/controllers/controller.php';

/**SE CREA LAS VARIABLES DE INICIO**/
$cn = new ObtenerConexiondb();                    //Conexion global Empresa Fija
$db = $cn::conectarEmpresa($_SESSION['db_name']); //coneccion de la empresa variable global


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


$object      = [];
$fecha_hoy   = date('Y-m-d');

$loginUsuario = $_SESSION['usuario']; #Login Inicio de Sesion

$InformacionEntity = (object)array(
    'nombre'            => $_SESSION['nombreClinica'],
    'email'             => $_SESSION['emailClinica'],
    'direccion'         => $_SESSION['direccionClinica'],
);


$datos['idplan']     = GETPOST('idplant');
$datos['idpaciente'] = GETPOST('idpaciente');
$datos['date']       = GETPOST('date');

$NombrePaciente = null;

$data = [];
$sqlEvul = "SELECT 
    ifnull(c.edit_name, concat('Plan de Tratamiento ', 'N. ', c.numero)) plantram ,
    ev.fecha_create fechaevul ,
    cp.descripcion as presstacion, 
    ev.fk_diente as diente , 
    (select concat( o.nombre_doc , ' ', o.apellido_doc ) from tab_odontologos o where o.rowid = ev.fk_doctor) as doct , 
    ev.observacion , 
    ifnull((select odes.descripcion from tab_odontograma_estados_piezas odes where odes.rowid = ev.estado_diente), 'Estado no asignado' )as estadodiente , 
    ev.json_caras, 
    (select concat(a.nombre,' ',a.apellido) as nom from tab_admin_pacientes a where a.rowid = ev.fk_paciente) as NomPaciente
FROM
    tab_evolucion_plantramiento ev , 
    tab_plan_tratamiento_cab c , 
    tab_plan_tratamiento_det d , 
    tab_conf_prestaciones cp
WHERE
    ev.fk_plantram_cab = c.rowid and 
    ev.fk_plantram_det = d.rowid and 
    d.fk_prestacion = cp.rowid and 
    ev.fk_paciente = ".$datos['idpaciente'];
if( !empty( $datos['idplan'] ) ){
    $sqlEvul .= " and ev.fk_plantram_cab =  " . $datos['idplan'] . "  ";
}
if( !empty( $datos['date']  ) ){
    $datex1 = str_replace('/','-', explode('-',$datos['date'])[0]);
    $datex2 = str_replace('/','-', explode('-',$datos['date'])[1]);
    $sqlEvul .= " and cast(ev.fecha_create as date) between '".$datex1."' and '".$datex2."' ";
}
//print_r($sqlEvul); die();
$rsevol = $db->query($sqlEvul);
if( $rsevol && $rsevol->rowCount() > 0){
    while ( $objevol =   $rsevol->fetchObject() ) {

        $NombrePaciente = $objevol->NomPaciente;

        $cadena_caras = array();
        $caras = json_decode($objevol->json_caras);

        $cadena_caras[] = ($caras->vestibular=="true") ? "vestibular" : "";
        $cadena_caras[] = ($caras->distal=="true") ? "distal" : "";
        $cadena_caras[] = ($caras->palatino=="true") ? "palatino" : "";
        $cadena_caras[] = ($caras->oclusal=="true") ? "oclusal" : "";
        $cadena_caras[] = ($caras->lingual=="true") ? "lingual" : "";

        $row   = array();
        $row[] = date('Y/d/m', strtotime( $objevol->fechaevul ) );
        $row[] = $objevol->plantram;
        $row[] = $objevol->presstacion;
        $row[] = ($objevol->diente!=0)?$objevol->diente:'No asignado';
        $row[] = $objevol->estadodiente;
        $row[] = $objevol->doct;
        $row[] = $objevol->observacion;
        $row[] = "". (implode(',', array_filter( $cadena_caras ))) ; #lista de caras

        $data[] = $row;

    }
}

#echo '<pre>';print_r($data); die();


$pdf .= '<style>
            .tables {
                 border-collapse: collapse;
              
            }
           
            .tables {
                border: 1px solid black;
                padding: 3px; 
            }
            
            .tablesTd{
                border: 1px solid black;
                padding: 2px;
            }
            
            </style>';


$pdf .= '
    <br>
    <table class="tables" width="100%" style="margin-top: 25px" >';
    $pdf .= '<thead>
                <tr style="background-color: #f0f0f0">
                    <th class="tables" colspan="8">Evoluciones Realizadas</th>
                </tr>
                <tr style="background-color: #f0f0f0">
                    <th class="tables" colspan="8" style="text-align: left">Paciente:  '.$NombrePaciente.'</th>
                       
                </tr>
                <tr style="background-color: #f0f0f0">
                    <th class="tables">Emitido</th>
                    <th class="tables">Plan de Tratamiento</th>
                    <th class="tables">Prestación</th>
                    <th class="tables">Pieza</th>
                    <th class="tables">Estado de Pieza</th>
                    <th class="tables">Doctor(a) Encargado</th>
                    <th class="tables">Observación</th>
                    <th class="tables">Caras</th>
                </tr>   
             </thead>';

$pdf .= '<tbody>';

    foreach ($data as $key => $value){

        $pdf .= '<tr>';
            foreach ($value as $key2 => $val){
                $pdf .= '<td class="tablesTd">'.$val.'</td>';
            }
        $pdf .= '</tr>';

    }

$pdf .= '</tbody>';

$pdf .= '</table>';

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
    10, //bottom
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
$mpdf->SetTitle('Evoluciones' );

$mpdf->AddPage('L');

$mpdf->WriteHTML($body.$pdf);


$mpdf->Output('Evoluciones.pdf', 'I');



?>