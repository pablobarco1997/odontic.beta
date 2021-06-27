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

$object      = [];
$fecha_hoy   = date('Y-m-d');

$idPaciente  = GETPOST("idpaciente");

$Fecha       = !empty(GETPOST('date'))?explode('-',GETPOST('date')):"";
$n_cita      = GETPOST('ncita');

$fechaInicio  = "";
$fechafin     = "";

if($Fecha!=""){
    $fechaInicio = str_replace("/","-",$Fecha[0]);
    $fechafin    = str_replace("/","-",$Fecha[1]);
}


$sql = "select 
                date_format(d.fecha_cita, '%Y-%m-%d')  as fecha_cita,
                c.rowid as id_cita_cab ,
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
                IFNULL((select es.nombre_especialidad FROM tab_especialidades_doc es where es.rowid = d.fk_especialidad), 'General') as especialidad,
                (select IFNULL(tc.edit_name, concat('Plan de tratamiento #',tc.numero)) from tab_plan_tratamiento_cab tc where tc.fk_cita = c.rowid limit 1) as plantratamiento ,
                (select p.telefono_movil from tab_admin_pacientes p where p.rowid = c.fk_paciente) as telefono_movil ,
                
                -- validaciones
                -- citas atrazada con estado no confirmado
                IF( now() > CAST(d.fecha_cita AS DATETIME)  
                                        && d.fk_estado_paciente_cita in(2,1,3,4,7,8,9,10,11,5,  (select statusc.rowid from tab_pacientes_estado_citas statusc where statusc.system=0) )  , 
                                            concat('Atrasada ', (select concat(s.text) from tab_pacientes_estado_citas s where s.rowid = d.fk_estado_paciente_cita) , 
                                                    '\n Fecha : ' , date_format(d.fecha_cita, '%Y/%m/%d') , '<br> Hora: ' , d.hora_inicio ,' h ' , d.hora_fin) , ''
                                                    ) as cita_atrazada
         
             from 
         
             tab_pacientes_citas_cab c , tab_pacientes_citas_det d
             where c.rowid = d.fk_pacient_cita_cab ";

if(!empty($idPaciente))
    $sql .= "  and c.fk_paciente = $idPaciente";

if(!empty($fechaInicio) && !empty($fechafin))
    $sql .= "  and cast(d.fecha_cita as date) between cast('$fechaInicio' as date) and cast('$fechafin' as date)";

if($n_cita!="")
    $sql .= " and d.rowid like '%$n_cita%' ";

$sql .= " order by d.fecha_cita desc ";

$rscitas = $db->query($sql);

if($rscitas&&$rscitas->rowCount()>0){
    while ($objc = $rscitas->fetchObject()){
        $object[] = $objc;
    }
}

#echo '<pre>';print_r($sql); die();

$InformacionEntity = (object)array(
    'nombre'            => $_SESSION['nombreClinica'],
    'email'             => $_SESSION['emailClinica'],
    'direccion'         => $_SESSION['direccionClinica'],
);

$loginUsuario = $_SESSION['usuario']; #Login Inicio de Sesion
$pdf          = null;
$idpaciente   = GETPOST('idpaciente');



$pdf .= '
            
            <style>
             p{
                font-size: 1.1rem;
             }
            .tables {
                border-collapse: collapse;
                 width="100%"; 
               
            }
            
            .tables {
                border: 1px solid black;
                font-size: 1.2rem;
                padding: 3px;
            }
            
            td{
                vertical-align: top
            }
            
            </style>';

$pdf .= "
    <table  width='100%' class='tables'>
        <tr  style='width: 100%; background-color: #f0f0f0'>
            <td style='text-align: center; ' > <h4> HISTORIAL DE CITAS </h4> </td>
        </tr>
    </table>    ";


$objPaciente = getnombrePaciente($idpaciente);

$pdf .= "<br>";

$pdf .= "
    <p style='margin: 0.5px; font-size: 1.1rem;'><b>Nombre de Paciente:</b>&nbsp; ".($objPaciente->nombre). " ". ($objPaciente->apellido) ."</p>
    <p style='margin: 0.5px; font-size: 1.1rem;'><b>C.I.:</b>&nbsp; ".($objPaciente->ruc_ced). "</p>
    <p style='margin: 0.5px; font-size: 1.1rem;'><b>E-mail:</b>&nbsp; ".($objPaciente->email). "</p>
    <br>";

$pdf .= "<table class='tables' width='100%'>";
    $pdf .= "    <thead>
                    <tr style='background-color: #f0f0f0'>
                        <th class='tables' width='10%' style='font-size: 1rem'>Fecha</th>    
                        <th class='tables' width='30%' style='font-size: 1rem'>Hora</th>    
                        <th class='tables' width='20%' style='font-size: 1rem'>Espacialidad</th>    
                        <th class='tables' width='12%' style='font-size: 1rem'>N.cita</th>    
                        <th class='tables' width='15%' style='font-size: 1rem'>Información Adicional</th>    
                        <th class='tables' title='plan de tratamiento asociado' style='font-size: 1rem'>P. Tratamiento asoc.</th>    
                        <th class='tables' style='font-size: 1rem'>Estado de la Cita</th>    
                    </tr>
                </thead>
            <tbody>";

    foreach ($object as $item)
    {
        $pdf .= "<tr>";
            $pdf .= "<td class='tables' width='10%' align='center' style='vertical-align: top; font-weight: bold; font-size: 1rem'> ". date('Y/m/d', strtotime( $item->fecha_cita))." </td>";

            $pdf .= "<td class='tables' width='10%' style='vertical-align: top'>
                            <table>
                                <tr>
                                    <td style='font-weight: bold'>Hora Inicio: </td> 
                                    <td style='font-weight: bold'> $item->hora_inicio </td>
                                </tr>
                                <tr>
                                    <td style='font-weight: bold'>Hora Fin: </td> 
                                    <td style='font-weight: bold'> $item->hora_fin </td>
                                </tr>
                            </table>     
                     </td>";

            $pdf .= "<td class='tables' width='30%' style='vertical-align: top'> 
                            <p>$item->especialidad</p> 
                                <hr style='margin: 1px; background-color: black; color: black'> 
                            <small style='color: black; display: block; font-size: 1rem; font-weight: bold' > <b>Doctor(a):</b> ".$item->doct."</small>
                     </td>";

            $pdf .= "<td class='tables' width='8%' style='vertical-align: top'> 
                        <table>
                            <tr>
                                <td>  </td>
                                <td> C_". str_pad($item->id_cita_det, 6, "0", STR_PAD_LEFT) ." </td>
                            </tr>
                        </table>
                     </td>";

            $pdf .= "<td class='tables' width='30%' style='vertical-align: top'> <p>$item->comentario  </p> <p><small style='color: red; font-weight: bold'>".($item->cita_atrazada)."</small></p> </td>";
            $pdf .= "<td class='tables' width='10%' style='vertical-align: top'> <small style='font-weight: bold'> ". $item->plantratamiento ." </small> </td>";
            $pdf .= "<td class='tables' width='15%' style='vertical-align: top'> <p style='background-color: $item->color; margin-top: 3px; font-weight: bold'> ".$item->estado." </p> </td>";
        $pdf .= "</tr>";


    }

    $pdf .= "</tbody>";


$pdf .= "</table>";


$footer = '<!--<hr style="margin-bottom: 2px"><table width="100%" style="font-size: 10pt;">-->
<br>
          <table>
                <tr>
                    <td width="50%">
<!--                        <div align="left"> </div> -->
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


$mpdf=new mPDF('c','LETTER','','',
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
$mpdf->SetTitle('Historial de Citas' );

$mpdf->AddPage('L');

$mpdf->WriteHTML($body.$pdf);


$mpdf->Output('Historial de Citas.pdf', 'I');
//print_r($mpdf); die();


?>