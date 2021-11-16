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
require_once DOL_DOCUMENT . '/public/lib/PHPExcel2014/PHPExcel.php';

/**SE CREA LAS VARIABLES DE INICIO**/
$cn = new ObtenerConexiondb();                    //Conexion global Empresa Fija
$db = $cn::conectarEmpresa($_SESSION['db_name']); //coneccion de la empresa variable global

$idplantratamiento = GETPOST('idplantratamiento');

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
    t.nom as tipo_p, 
    
    case 
	  when pd.estado = 'A' then 'Activo'
	  when pd.estado = 'E' then 'Anulado'
    end as estado 
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
where 1=1 ";

if(!empty($idplantratamiento)){
    $sql .= " and td.fk_plantratam_cab = ".$idplantratamiento;
}

$sql .= " group by pd.fk_plantram_det,pd.fk_prestacion, pd.fk_plantram_cab, td.fk_diente";
$result = $db->query($sql);
$data = $result->fetchAll(PDO::FETCH_ASSOC);

//echo '<pre>'; print_r($sql); die();


//esta funcion me arma la tabla
function aplicarColunmDetalle($titulos = array(), $detallesRows = array(), $rows, $objPHPExcel){

    $acuRowis = $rows;
    foreach ($titulos as $col => $item){
        $objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($col, $acuRowis, $item);
    }

//    $last = $objPHPExcel->getActiveSheet()->getHighestDataColumn(); //ultimo letra
    $last = $objPHPExcel->setActiveSheetIndex(0)->getColumnDimensionByColumn($col)->getColumnIndex(PHPExcel_Worksheet::BREAK_COLUMN);
//    print_r($last); die();
    $objPHPExcel->getActiveSheet()->getStyle('A'.$acuRowis.':'.$last.$acuRowis)->applyFromArray(
        array(
            'font' => array(
                'bold' => true,
                'size' => 12,
            ),
            'borders' => array('bottom'=>array('style'=>PHPExcel_Style_Border::BORDER_DOUBLE)),
            'alignment' => array( 'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,'wrap' => TRUE),
        )
    );

    $acuRowis++; //agrego el detalle
    foreach ($detallesRows as $k => $itemValue){
        $col=0;
        foreach ($itemValue as $value){
            $objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($col, $acuRowis, $value);
            $col++;
        }
        $acuRowis++;
    }

    return $acuRowis;
}



/** PHP EXCEL **/


$objPHPExcel = new PHPExcel();


//titulo principal
$objPHPExcel->setActiveSheetIndex(0)->setCellValueExplicit('A1', 'DETALLES DE PAGOS REALIZADOS DEL '.strtoupper($n_tratamiento)." ".date("Y/m/d"));
$objPHPExcel->setActiveSheetIndex(0)->mergeCells('A1:H1');
$objPHPExcel->getActiveSheet()->getStyle('A1:H1')->applyFromArray(
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

//pacientes
$objPHPExcel->setActiveSheetIndex(0)->setCellValueExplicit('A2', 'Paciente: ');
$objPHPExcel->setActiveSheetIndex(0)->setCellValueExplicit('B2', $paciente_nomb);

$objPHPExcel->getActiveSheet()->getStyle('A2:A2')->applyFromArray(
    array('font' => array(  'bold' => true, 'size' => 12,
        'color' => array(
            'argb' => '1f497d'
        ),
    ),)
);
$objPHPExcel->getActiveSheet()->getStyle('B2:B2')->applyFromArray(
    array('font' => array(  'bold' => false, 'size' => 12,
        'color' => array(
            'argb' => '1f497d'
        ),
    ),)
);


$titulos_array = array('Emitido', 'Prestación/Servicios', 'Pieza', 'Total', 'Abonado', 'Pendiente', 'Forma de Pago');
$detallesRow = [];
foreach ($data as $k => $value){

    $rows = [];
        $rows[] = date('Y/m/d', strtotime($value['date']));
        $rows[] = $value['prestacion'];
        $rows[] = $value['pieza'];
        $rows[] = $value['total_prestacion'];
        $rows[] = $value['abonado'];
        $rows[] = $value['pendiente'];
        $rows[] = $value['tipo_p'];
//        $rows[] = $value['estado'];
    $detallesRow[] = $rows;
}

//obtengo la ultima fila insertada
$rowsFila = aplicarColunmDetalle($titulos_array, $detallesRow , 3, $objPHPExcel);

//aplico estylos a la columna que quiero
$objPHPExcel->getActiveSheet()->getStyle('E4:E'.$rowsFila)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

//print_r('E3:E'.$rowsFila);die();

foreach(range('A','F') as $columnID) {
    $objPHPExcel->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
}

$objPHPExcel->getActiveSheet()->calculateColumnWidths();

// Se asigna el nombre a la hoja
$objPHPExcel->getActiveSheet()->setTitle('DETALLES DE PAGOS REALIZADOS');
// Se activa la hoja para que sea la que se muestre cuando el archivo se abre
$objPHPExcel->setActiveSheetIndex(0);


// Se manda el archivo al navegador web, con el nombre que se indica, en formato 2007
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="Detalles de Pagos Realizados.xlsx"');
header('Cache-Control: max-age=0');

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');





?>