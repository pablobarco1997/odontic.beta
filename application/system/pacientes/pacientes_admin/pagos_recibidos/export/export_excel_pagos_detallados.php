<?php


require_once '../../../../../config/lib.global.php';
session_start();

if(!isset($_SESSION['is_open'])){
    header("location:".DOL_HTTP."/application/system/login");
}

error_reporting(E_ALL);

define('PC_MEMORY_LIMIT', '1024M');
ini_set('memory_limit',PC_MEMORY_LIMIT);
set_time_limit(20000);


require_once  DOL_DOCUMENT .'/application/system/conneccion/conneccion.php';    //Coneccion de Empresa
require_once  DOL_DOCUMENT .'/application/controllers/controller.php';
require_once  DOL_DOCUMENT .'/public/lib/PHPExcel2014/PHPExcel.php';


$objPHPExcel = new PHPExcel();

function aplicarColunmDetalle($titulos = array(), $detallesRows = array(), $rows, $objPHPExcel, $marcelTitle=false){

    $acuRowis = $rows;
    foreach ($titulos as $col => $item){
        $objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($col, $acuRowis, $item);
    }

//    $last = $objPHPExcel->getActiveSheet()->getHighestDataColumn(); //ultimo Columna en uso
    $last = $objPHPExcel->setActiveSheetIndex(0)->getColumnDimensionByColumn($col)->getColumnIndex(PHPExcel_Worksheet::BREAK_COLUMN);

    if($marcelTitle==true){
//        print_r($lastColumn); die();
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A'.$acuRowis.':'.$objPHPExcel->getActiveSheet()->getHighestDataColumn().$acuRowis);
        $fill = array(
            'type' => PHPExcel_Style_Fill::FILL_SOLID,
            'color' => array(
                'argb' => 'dbeef3'));
        $alignment = array();
        $borders = array();
    }else{
        $fill = array();
        $alignment = array( 'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,'wrap' => TRUE);
        $borders = array('bottom'=>array('style'=>PHPExcel_Style_Border::BORDER_DOUBLE));
    }


    $objPHPExcel->getActiveSheet()->getStyle('A'.$acuRowis.':'.$last.$acuRowis)->applyFromArray(
        array(
            'font' => array(
                'bold' => true,
                'size' => 12,
            ),
            'borders' => $borders,
            'alignment' => $alignment,
            'fill' => $fill
        )
    );

    $RowsColorRed = [];
    $acuRowis++; //agrego el detalle
    foreach ($detallesRows as $k => $itemValue){
        $col=0;
        foreach ($itemValue as $value){
            $objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($col, $acuRowis, $value);
            $col++;
            if($value=="Anulado"){ //campo anulado
                $RowsColorRed[] = $acuRowis;
            }
        }
        $acuRowis++;
    }

    if(count($RowsColorRed)>0){
        foreach ($RowsColorRed as $cell){
            $fill = array('type' => PHPExcel_Style_Fill::FILL_SOLID,'color' => array('argb' => 'd99795'));
            $objPHPExcel->getActiveSheet()->getStyle('L'.$cell)->applyFromArray(array('font' => array('bold' => false,'size' => 12,),'fill' => $fill));
//            print_r('L'.$cell.':'.$last.$cell);
        }
//        die();
    }

    return $acuRowis;
}


/**SE CREA LAS VARIABLES DE INICIO**/
$cn = new ObtenerConexiondb();                    //Conexion global Empresa Fija
$db = $cn::conectarEmpresa($_SESSION['db_name']); //coneccion de la empresa variable global

//add log export excel o pdf
require_once  DOL_DOCUMENT .'/application/config/class.log.php';
$log = new log($db, $_SESSION['id_users_2']);
$log->log(0, $log->consultar, 'Se exporto el reporte:  PAGOS DETALLADOS.xlsx', '', '');

$fechaData =  array();
$sql_data = "select 
    pddc.rowid as n_pago,
    concat(ttp.numero_ttp,' Paciente: ',concat(adp.nombre,' ',adp.apellido)) as tratamiento,
    pdd.feche_create as emitido,
    prs.descripcion as servicio,
    if(ttp.pieza=0,'',ttp.pieza) as pieza, 
    ttp.totalttp as total,
    sum(round(pdd.amount, 2)) as abonado,
    round( (ttp.totalttp - sum(round(pdd.amount, 2)) ), 2) as pendiente, 
    case 	
		when sum(round(pdd.amount, 2))=ttp.totalttp then 'Recaudación Completa'
        when sum(round(pdd.amount, 2))>0 && sum(round(pdd.amount, 2))<>ttp.totalttp then 'Abonado'
        else 'Saldo Pendiente'
        end
        as recaudacion_estado
        
	, case ttp.estadodet
		when 'A' then 'PENDIENTE'
		when 'P' then 'EN PROCESO'
		when 'R' then 'REALIZADO'
		end  as estado_ttp , 
        
	pddc.n_fact_boleta as boleta,
	b.nom as typebank,
	ttp.idttp,
	pdd.feche_create,
	
	case 
	  when pdd.estado = 'A' then 'Activo'
	  when pdd.estado = 'E' then 'Anulado'
    end as estado 
    
from 
	tab_pagos_independ_pacientes_det pdd 
		left join
	tab_pagos_independ_pacientes_cab pddc on pdd.fk_pago_cab = pddc.rowid
	    inner join
	tab_bank_operacion b on b.rowid = pddc.fk_tipopago
		left join 
	(select concat('Plan de Tratamiento N.',tc.numero) as numero_ttp , td.fk_diente as pieza, tc.rowid idttp , td.rowid as idttpd, sum(round(td.total, 2)) as totalttp , td.estadodet from tab_plan_tratamiento_cab tc inner join tab_plan_tratamiento_det td on td.fk_plantratam_cab = tc.rowid group by tc.rowid, td.rowid) as ttp on ttp.idttp = pdd.fk_plantram_cab and ttp.idttpd = pdd.fk_plantram_det
		inner join 
	tab_conf_prestaciones prs on prs.rowid = pdd.fk_prestacion
		inner join 
	tab_admin_pacientes adp on adp.rowid  = pdd.fk_paciente
  WHERE pdd.fk_paciente = ".GETPOST('idpaciente');

if(GETPOST('n_pago')!=''){
    $sql_data .= " and pddc.rowid like '%".GETPOST('n_pago')."%' ";
}
if(GETPOST('n_x_documento')!=''){
    $sql_data .= " and pddc.n_fact_boleta like '%".GETPOST('n_x_documento')."%' ";
}
if(GETPOST('formapago')!=''){
    $sql_data .= " and b.rowid = '".GETPOST('formapago')."' ";
}
if(GETPOST('plan_tratam')!=''){
    $sql_data  .= " and pddc.fk_plantram = ".GETPOST('plan_tratam');
}
if(!empty(GETPOST('emitido'))){
    $dateff0 = explode('-', GETPOST('emitido'))[0];
    $dateff1 = explode('-', GETPOST('emitido'))[1];

    $dateff0 = date('Y-m-d', strtotime($dateff0));
    $dateff1 = date('Y-m-d', strtotime($dateff1));
    $sql_data  .= " and CAST(pdd.feche_create AS DATE) between '$dateff0' and '$dateff1' ";
}

$sql_data .= " group by pdd.fk_plantram_cab, pdd.fk_plantram_det, pdd.rowid ";

//print_r('<pre>'.$sql_data); die();

$data = array();
$result = $db->query($sql_data);
if($result){
    if($result->rowCount()>0){
        $data = $result->fetchAll(PDO::FETCH_ASSOC);
        foreach ($data as $k => $value){

            $fechaData[$value['idttp']]['text'] = $value['tratamiento'];
            $fechaData[$value['idttp']]['detalle'][] = array(
                'emitido'            => $value['feche_create'],
                'n_pago'             => 'P_'.str_pad($value['n_pago'],6,'0',STR_PAD_LEFT),
                'servicio'           => $value['servicio'],
                'pieza'              => $value['pieza'],
                'total'              => $value['total'],
                'abonado'            => $value['abonado'],
                'pendiente'          => $value['pendiente'],
                'recaudacion_estado' => $value['recaudacion_estado'],
                'estado_ttp'         => $value['estado_ttp'],
                'typebank'           => $value['typebank'],
                'boleta'             => $value['boleta'],
                'estado'             => $value['estado'],
            );
        }
    }else{
        print  '<h3>No hay datos</h3>';
        die();
    }
}else{
    print  '<h3>No hay datos Ocurrio un error</h3>';
    die();
}


//echo '<pre>'; print_r($fechaData); die();


$Titulos = array('Emitido','N.Pagos','Prestación/Servicios', 'Pieza','Total','Abonado','Pendiente','Recaudación Estado','Tratamiento Estado','Forma de pago', 'Boleta', 'Estado Anulado');

$objPHPExcel->setActiveSheetIndex(0)->setCellValueExplicit('A1', "PAGOS DETALLADOS ".date("Y/m/d"));
$objPHPExcel->setActiveSheetIndex(0)->mergeCells('A1:K1');
$objPHPExcel->getActiveSheet()->getStyle('A1:K1')->applyFromArray(
    array('font' => array(  'bold' => true, 'size' => 14,
        'color' => array(
            'argb' => '1f497d'
        ),
    ),
        'fill' => array(
            'type' => PHPExcel_Style_Fill::FILL_SOLID,
            'color' => array(
                'argb' => 'dae4f0')),
        'alignment' => array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,),)
);



$rowsExcel = 2;
$rowsExcel = aplicarColunmDetalle($Titulos, array(), $rowsExcel, $objPHPExcel, false);

foreach ($fechaData as $k => $item_a){
    //titulo
    $rowsExcel = aplicarColunmDetalle(array($item_a['text']), $item_a['detalle'], $rowsExcel, $objPHPExcel, true);
}

foreach(range('A','J') as $columnID) {
    $objPHPExcel->getActiveSheet()->getColumnDimension($columnID)
        ->setAutoSize(true);
}

$objPHPExcel->getActiveSheet()->calculateColumnWidths();

// Se asigna el nombre a la hoja
$objPHPExcel->getActiveSheet()->setTitle('PAGOS DETALLADOS');
// Se activa la hoja para que sea la que se muestre cuando el archivo se abre
$objPHPExcel->setActiveSheetIndex(0);



// Se manda el archivo al navegador web, con el nombre que se indica, en formato 2007
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="PAGOS DETALLADOS.xlsx"');
header('Cache-Control: max-age=0');

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');





?>