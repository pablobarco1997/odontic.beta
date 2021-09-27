<?php

require_once '../../../config/lib.global.php';
session_start();

if(!isset($_SESSION['is_open']))
{
    header("location:".DOL_HTTP."/application/system/login");
}


//parametros de entrada para procesa la informacion
//configuracion de php
define('PC_MEMORY_LIMIT', '1024M');
ini_set('memory_limit',PC_MEMORY_LIMIT);
set_time_limit(20000);
//echo '<pre>'; print_r(ini_get_all()); die();

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
);
$loginUsuario = $_SESSION['usuario']; #Login Inicio de Sesion

$idagend = GETPOST('idagend');

$objectCitasAgendadas = array();

$doctor="";
$estado="";
$fechaInicio="";
$fechaFin="";
$fechaFin="";
$paciente="";
$n_citas="";

if(GETPOST('accion_exportar')=='pdf_filter'){
    $doctor     = GETPOST('odontologo');
    $estado     = GETPOST('estados');
    $fechaInicio= str_replace('/', '-', (explode('-', GETPOST('fecha'))[0]));
    $fechaFin   = str_replace('/', '-', (explode('-', GETPOST('fecha'))[1]));
    $paciente   = GETPOST('pacientes');
    $n_citas    = GETPOST('n_cita');

}


$sql = "SELECT 
            date_format(d.fecha_cita, '%Y/%m/%d')  as fecha_cita,         
            d.hora_inicio , 
            d.hora_fin ,
            d.rowid  as id_cita_det,
            (select concat(p.nombre ,' ',p.apellido) from tab_admin_pacientes p where p.rowid = c.fk_paciente) as paciente,
            (select rowid from tab_admin_pacientes p where p.rowid = c.fk_paciente) as idpaciente,                   
            (select telefono_movil from tab_admin_pacientes p where p.rowid = c.fk_paciente) as telefono_movil,
            (select concat(o.nombre_doc,' ', o.apellido_doc) from tab_odontologos o where o.rowid = d.fk_doc) as doct ,
            (select concat(s.text) from tab_pacientes_estado_citas s where s.rowid = d.fk_estado_paciente_cita) as estado,
            (select s.color from tab_pacientes_estado_citas s where s.rowid = d.fk_estado_paciente_cita) as color,
            d.fk_estado_paciente_cita , 
            c.comentario ,
            ifnull((select es.nombre_especialidad FROM tab_especialidades_doc es where es.rowid = d.fk_especialidad),'General') as especialidad,
            (select p.telefono_movil from tab_admin_pacientes p where p.rowid = c.fk_paciente) as telefono_movil,
            d.fk_doc as iddoctor , 
            (select p.email from tab_admin_pacientes p where p.rowid = c.fk_paciente) as email, 
            d.comentario_adicional as comentario_adicional,
            c.fk_paciente as idpaciente  ,
             -- validaciones
             -- citas atrazados con estado no confirmado
             IF( now() > CAST(d.fecha_cita AS DATETIME)  
                        && d.fk_estado_paciente_cita in(2,1,3,4,7,8,9,10,5)  , 
                            concat('Atrasada ', (select concat(s.text) from tab_pacientes_estado_citas s where s.rowid = d.fk_estado_paciente_cita) , 
                                    '<br> Fecha : ' , date_format(d.fecha_cita, '%Y/%m/%d') , '<br>Hora: ' , d.hora_inicio ,' a ' , d.hora_fin) , ''
                                    ) as cita_atrazada
									            
         FROM 
            tab_pacientes_citas_cab c , tab_pacientes_citas_det d
            where c.rowid = d.fk_pacient_cita_cab
         ";

if(!empty($doctor)) {
    $sql .= " and d.fk_doc in(".$doctor.")";
}

if(!empty($estado)) {
    $sql .= " and d.fk_estado_paciente_cita in(".$estado.") ";
}

if(!empty($fechaInicio) && !empty($fechaFin)) {
    $sql .= " and cast(d.fecha_cita as date) between cast('$fechaInicio' as date) and cast('$fechaFin' as date) ";
}

if(!empty($MostrarCitasCanceladasEliminadas)) {
    $sql .= " and d.estado = 'E' or  d.fk_estado_paciente_cita = 9 ";
}

if(!empty($paciente)) {
    $sql .= " and c.fk_paciente in($paciente) ";
}

if(!empty($n_citas)) {
    $sql .= " and d.rowid like '%$n_citas%' ";
}

/*
if ($colum_ord == 3) {
    $sql .= " order by 2 $direcc_ord";
}else if ($colum_ord == 4) {
    $sql .= " order by 3 $direcc_ord";
}else if ($colum_ord == 5) {
    $sql .= " order by 2  $direcc_ord";
}else if ($colum_ord == 6) {
    $sql .= " order by 5 $direcc_ord";
}else if ($colum_ord == 7) {
    $sql .= " order by 6 $direcc_ord";
}else{
    $sql .= " order by d.fecha_cita desc ";
}*/

//busqueda por id otro tipo de filtro
if(!empty($idagend)){
    $sql .= " and d.rowid in(".$idagend.")";
}

$sql .= " order by d.fecha_cita desc ";

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
    </table> ';

//$pdf .= $header;

$pdf .= '<br>';
$pdf .= "<table class='tables' style='width: 100%'>";
    $pdf .= "<thead>";
        $pdf .= "<tr> <th colspan='5' class='tables' style='background-color: #f0f0f0'>DETALLES DE CITAS AGENDADAS</th> </tr>";
        $pdf .= "<tr class='tables' style='background-color: #f0f0f0'>";
            $pdf .= "<th class='tables'>N#. Citas</th>";
            $pdf .= "<th class='tables'>Hora</th>";
            $pdf .= "<th class='tables'>Paciente</th>";
            $pdf .= "<th class='tables'>Doctor(a)</th>";
            $pdf .= "<th class='tables'>Estado de Citas</th>";
        $pdf .= "</tr>";
    $pdf .= "</thead>";

    $pdf .= "<tbody>";

    $result = $db->query($sql);
    if($result && $result->rowCount()>0){
        while ($object = $result->fetchObject()){

            $pdf .= "<tr>";

                $pdf .= "<td class='tables'  style='vertical-align: top' >";
                    $pdf .= "<table>
                                <tr>
                                    <td>  </td>
                                    <td> C_ ". str_pad($object->id_cita_det, 6, "0", STR_PAD_LEFT) ." </td>
                                </tr>
                              </table>";
                $pdf .= "</td>";

                $pdf .= "<td class='tables'  style='vertical-align: top' >
                             <table>
                                <tr><td colspan='2'>$object->fecha_cita</td></tr>
                                <tr><td>Hora Inicio: </td> <td> $object->hora_inicio </td></tr>
                                <tr><td>Hora Fin: </td> <td> $object->hora_fin </td></tr>
                             </table> 
                         </td>";

                $pdf .= "<td class='tables'  style='vertical-align: top' >
                            <table>
                                <tr><td>$object->paciente</td></tr>
                                <tr><td><b>Telf:&nbsp;</b>$object->telefono_movil</td></tr>
                                <tr><td><span style='color: red;'>$object->cita_atrazada</span></td></tr>
                             </table> 
                         </td>";

                $pdf .= "<td class='tables'  style='vertical-align: top' >
                             <table>
                                <tr><td><b>Doc(a).</b>&nbsp; $object->doct</td></tr>
                                <tr><td><b>Especialidad:</b>&nbsp; $object->especialidad</td></tr>
                              ";
                              if(!empty($object->comentario_adicional))
                                  $pdf .= "<tr><td><b>Comment Adicional:</b>&nbsp; $object->comentario_adicional</td></tr>";

                $pdf.=       "</table> 
                         </td>";

                $pdf .= "<td class='tables'  style='vertical-align: top' >
                             <table>
                                <tr><td style='background-color: $object->color'>$object->estado</td></tr>
                             </table>
                         </td>";


            $pdf .= "</tr>";
        }
    }
    $pdf .= "</tbody>";
$pdf .= "</table>";


$footer = '<!--<hr style="margin-bottom: 2px"><table width="100%" style="font-size: 10pt;">-->
              <table width="100%" style="border-collapse: collapse">
                    <tr>
                        <td width="50%">
                            <div align="left">&nbsp;</div>
                        </td>
                        <td width="50%" align="right">
                            <div  style="float: right">Hoja:{PAGENO}</div>
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

$mpdf->setFooter("{PAGENO}");
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
$mpdf->SetTitle('Citas Agendadas' );

$mpdf->WriteHTML($body.$pdf);


$mpdf->Output('Citas Agendadas.pdf', 'I');


?>