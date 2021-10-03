<?php


function resultClinicaEstadoListExcel($datos = array(), $export = false, $db){

    $emitido = $datos['emitido'];

    $array_estado = array('INGRESOS', 'COSTO','GASTOS');
    $array_resultClinica = array();

    $sql = "select 
	concat(cd.n_cuenta, ' ', cd.name_acount) as name_acount, 
    sum(round(d.value, 2)) as valor, 
    cd.tipo_operacion
from 
	tab_ope_diario_admin_clinico_det d 
		inner join 
	(select * from tab_ope_declare_cuentas cd where cd.to_caja <> 1 ) cd on cd.rowid = d.id_cuenta";

    $sql .= " group by cd.rowid ";
    $result = $db->query($sql);
    if($result){
        if($result->rowCount()>0){

            $list = $result->fetchAll(PDO::FETCH_ASSOC);
            foreach ($list as $value){
                $array_resultClinica[$value['tipo_operacion']][] = $value;
            }
        }
    }

    $Totales = array('INGRESOS' => 0, 'COSTO' => 0, 'GASTOS' => 0);

    $data = array();
    foreach ($array_estado as $value){

        $rows = [];
        $rows[] = $value;
        $rows[] = "";

        if(!$export){
        }

        if($value=='INGRESOS')
            $rows['color'] = 'd5f5e3'; //green
        if($value=='COSTO' || $value=='GASTOS')
            $rows['color'] = 'fadbd8'; //red

        $data[] = $rows;

        //typo de operacion
        $fetch = $array_resultClinica[$value];
        foreach ($fetch as $item){

            $rows = [];
            $rows[] =  $item['name_acount'];
            $rows[] =  $item['valor'];
            $data[] = $rows;

            $Totales[$value] += (double)number_format($item['valor'], 2, '.','');
        }
    }

    //Se agrega los Totales
    $rows   = [];
    $rows[] = "TOTALES";
    $rows[] = "";
    if(!$export){
    }
    $rows['color'] = "f4f4f4";
    $data[] = $rows;

    //Totales
    foreach ($Totales as $k => $item){
        $rows = [];
        $rows[] = $k;
        $rows[] = $item;
        $data[] = $rows;
    }

//    print_r($data); die();

    return $data;

}


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

    $colors = [];
    $acuRowis++; //agrego el detalle
    foreach ($detallesRows as $k => $itemValue){

//        print_r($itemValue); die();
        if( array_key_exists('color', $itemValue) ){
            cellColor('A'.$acuRowis.':B'.$acuRowis, $itemValue['color'], $objPHPExcel);

            unset($itemValue['color']);
        }


        $col=0;
        foreach ($itemValue as $value){
            $objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($col, $acuRowis, $value);
            $col++;
        }
        $acuRowis++;
    }

    return $acuRowis;
}

function cellColor($cells,$color,$objPHPExcel){

    $objPHPExcel->getActiveSheet()->getStyle($cells)->getFill()->applyFromArray(array(
        'type' => PHPExcel_Style_Fill::FILL_SOLID,
        'startcolor' => array(
            'rgb' => $color
        )
    ));
}

if($_GET['export']){

    session_start();

    error_reporting(E_ALL);

    define('PC_MEMORY_LIMIT', '1024M');
    ini_set('memory_limit',PC_MEMORY_LIMIT);
    set_time_limit(20000);

    if (!isset($_SESSION['is_open'])) {
        header("location:" . DOL_HTTP . "/application/system/login");
    }


    require_once '../../../config/lib.global.php';
    require_once DOL_DOCUMENT . '/application/system/conneccion/conneccion.php';    //Coneccion de Empresa
    require_once DOL_DOCUMENT . '/public/lib/PHPExcel2014/PHPExcel.php';
    require_once DOL_DOCUMENT . '/application/controllers/controller.php';

    $cn = new ObtenerConexiondb();                    //Conexion global Empresa Fija
    $db = $cn::conectarEmpresa($_SESSION['db_name']); //coneccion de la empresa variable global
    $objPHPExcel = new PHPExcel();


    $datos = [];
    $datos['emitido'] = GETPOST('emitido');
    $result = resultClinicaEstadoListExcel($datos, true, $db);
    $titulos = array('Cuentas', 'Saldo');



    $objPHPExcel->setActiveSheetIndex(0)->setCellValueExplicit('A1', 'Estado Clinia | FECHA DE IMPRESION' . date('Y/m/d'));
    $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A1:I1');
    $objPHPExcel->getActiveSheet()->getStyle('A1:I1')->applyFromArray(
        array(
            'font' => array(
                'bold' => true,
                'size' => 14,
                'color' => array(
                    'argb' => '1f497d'
                ),
            ),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array(
                    'argb' => 'dae4f0')
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
        )
    );



    $rowsDet = aplicarColunmDetalle($titulos, $result, 2, $objPHPExcel);


//    echo '<pre>'; print_r($result); die();



    foreach(range('A','K') as $columnID) {
        $objPHPExcel->getActiveSheet()->getColumnDimension($columnID)
            ->setAutoSize(true);
    }

    $objPHPExcel->getActiveSheet()->calculateColumnWidths();


    // Se asigna el nombre a la hoja
    $objPHPExcel->getActiveSheet()->setTitle('Detalle Cajas Clinica');
    // Se activa la hoja para que sea la que se muestre cuando el archivo se abre
    $objPHPExcel->setActiveSheetIndex(0);

    //paneles congelados
//    $objPHPExcel->getActiveSheet(0)->freezePaneByColumnAndRow(0, 3);

    // Se manda el archivo al navegador web, con el nombre que se indica, en formato 2007
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="Detalle Cajas Clinica.xlsx"');
    header('Cache-Control: max-age=0');

    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
    $objWriter->save('php://output');



}


?>