<?php

if(isset($_GET['export'])){

    session_start();

    error_reporting(E_ALL);

    //parametros de entrada para procesa la informacion
    //configuracion de php
    define('PC_MEMORY_LIMIT', '1024M');
    ini_set('memory_limit',PC_MEMORY_LIMIT);
    set_time_limit(20000);
    //print_r(ini_get()); die();

    if (!isset($_SESSION['is_open'])) {
        header("location:" . DOL_HTTP . "/application/system/login");
    }

    require_once '../../../../config/lib.global.php';
    require_once DOL_DOCUMENT . '/application/system/conneccion/conneccion.php';
    require_once DOL_DOCUMENT . '/public/lib/PHPExcel2014/PHPExcel.php';
    require_once DOL_DOCUMENT . '/application/controllers/controller.php';

    $cn = new ObtenerConexiondb();                    //Conexion global Empresa Fija
    $db = $cn::conectarEmpresa($_SESSION['db_name']); //coneccion de la empresa variable global


    $objPHPExcel = new PHPExcel();

    $objPHPExcel->setActiveSheetIndex(0)->setCellValueExplicit('A1', 'REPORTE TRANSACCIONES CLINICAS	' . date('Y/m/d'));
    $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A1:E1');
    $objPHPExcel->getActiveSheet()->getStyle('A1:E1')->applyFromArray(array(
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

    $array_titulos = array();
    $array_titulos[] = "Emitido";
    $array_titulos[] = "Cuenta";
    $array_titulos[] = "Operación";
    $array_titulos[] = "Descripción";
    $array_titulos[] = "Valor";

    //asignacion de titulos
    $cols = 0;
    foreach ($array_titulos as $titulo) {
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValueByColumnAndRow($cols, 2, $titulo);
        $cols++;
    }

    //titulo stylos
    $objPHPExcel->getActiveSheet()->getStyle('A2:E2')->applyFromArray(
        array(
            'font' => array(
                'bold' => true,
                'size' => 12,
            ),
            'borders' => array('bottom'=>array('style'=>PHPExcel_Style_Border::BORDER_DOUBLE)),
            'alignment' => array( 'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,'wrap' => TRUE),
        )
    );

    for ($i = 'A'; $i <= 'D'; $i++) {
        switch ($i){
            case 'B':
                $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension($i)->setWidth(50);
                break;
            case 'D':
                $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension($i)->setWidth(100);
                break;
            default:
                $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension($i)->setWidth(20);
                break;
        }
    }

    $rowsd = 3;

    $date_cc    = GETPOST('datecc');
    $formp      = GETPOST('formp');
    $valor      = GETPOST('valor_tc');
    $desc       = GETPOST('desc');
    $cuenta_tc  = GETPOST('cuenta_tc');

    $where = "";
    if(!empty($date_cc)){
        $fecha = array();
        $fecha = explode('-', $date_cc);
        $fech1 = $fecha[0];
        $fech2 = $fecha[1];
        $where = " and cast(d.datec as date) between cast('$fech1' as date) and cast('$fech2' as date) ";
    }if(!empty($formp)){
        $where .= " and b.rowid = ".$formp;
    }if(!empty($valor)){
        $where .= " and d.value like '%$valor%' ";
    }if(!empty($desc)){
        $where .= " and d.label like '%$desc%' ";
    }if(!empty($cuenta_tc)){
        $where .= " and c.rowid = $cuenta_tc ";
    }

    $sql = "select 
            d.id_diario_admin_cab , 
            cast( d.datec as date) as date_cc, 
            
            concat(c.n_cuenta ,' ', c.name_acount, ' ',
                if(c.to_caja=1,concat('| Dir. ',c.to_caja_direccion ),'')) as nomb_cuenta,
                 
            d.value, 
            b.nom as operacion,
            d.label 
        from 
        tab_ope_diario_admin_clinico_det d 
            inner join 
        tab_ope_declare_cuentas c on c.rowid = d.id_cuenta
            inner join 
        tab_bank_operacion b on b.rowid = d.fk_type_payment
        where 1=1 ";
    $sql .= $where;
    $sql .= " ORDER BY d.rowid DESC ";

//    echo '<pre>'; print_r($sql); die();

    $array_dc = array();
    $result   = $db->query($sql);
    if($result){
        if($result->rowCount()>0){
            $array_dc = $result->fetchAll(PDO::FETCH_ASSOC);
        }
    }

    $tmp = [];
    foreach ($array_dc as $abc){
        $rowsValue = [];
            $rowsValue[] = date('Y/m/d', strtotime($abc['date_cc']));
            $rowsValue[] = $abc['nomb_cuenta'];
            $rowsValue[] = $abc['operacion'];
            $rowsValue[] = $abc['label'];
            $rowsValue[] = $abc['value'];
        $tmp[] = $rowsValue;
    }

    $rowsd = 3;
    foreach ($tmp as $value){
        $cols = 0;
        foreach ($value as $it){
                $objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($cols,$rowsd, $it);
            $cols++;
        }
        $rowsd++;
    }


    $objPHPExcel->getActiveSheet()->getStyle('A3:E'.$rowsd)->applyFromArray(array(
        'alignment' => array( 'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,'wrap' => TRUE),
    ));

    // Se asigna el nombre a la hoja
    $objPHPExcel->getActiveSheet()->setTitle('Reporte CITA.');
    // Se activa la hoja para que sea la que se muestre cuando el archivo se abre
    $objPHPExcel->setActiveSheetIndex(0);

    //paneles congelados
    $objPHPExcel->getActiveSheet(0)->freezePaneByColumnAndRow(0, 3);

    // Se manda el archivo al navegador web, con el nombre que se indica, en formato 2007
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="Reporte Transacciones Clinicas.xlsx"');
    header('Cache-Control: max-age=0');


    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
    $objWriter->save('php://output');


}

?>