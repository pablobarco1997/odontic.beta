<?php

if(isset($_GET['accion_exportar'])){

    session_start();

    error_reporting(E_ALL);

    //parametros de entrada para procesa la informacion
    //configuracion de php
    define('PC_MEMORY_LIMIT', '1024M');
    ini_set('memory_limit',PC_MEMORY_LIMIT);
    set_time_limit(20000);

    if (!isset($_SESSION['is_open'])) {
        header("location:" . DOL_HTTP . "/application/system/login");
    }

    require_once '../../../../config/lib.global.php';
    require_once DOL_DOCUMENT . '/application/system/conneccion/conneccion.php';    //Coneccion de Empresa
    require_once DOL_DOCUMENT . '/public/lib/PHPExcel2014/PHPExcel.php';
    require_once DOL_DOCUMENT . '/application/controllers/controller.php';
    require_once DOL_DOCUMENT . '/application/config/class.log.php';

    $cn = new ObtenerConexiondb();                    //Conexion global Empresa Fija
    $db = $cn::conectarEmpresa($_SESSION['db_name']); //coneccion de la empresa variable global

    $log = new log($db);

    $log->log(0, $log->consultar, 'Se ha realizado un export Reporte Gastos.xlsx', '', '');


    $emitido    = GETPOST("emitido");
    $facture    = GETPOST("facture");
    $pago       = GETPOST("pago");
    $estado     = GETPOST("estado");
    $cuenta     = GETPOST("cuenta");  //cuenta caja

    $filtro = "";
    if($emitido!=""){
        $emitido = explode('-', $emitido);
        $date_one = date('Y-m-d', strtotime($emitido[0]));
        $date_two = date('Y-m-d', strtotime($emitido[1]));
        $filtro .= " and cast(gc.tms as date) between '$date_one' and '$date_two'  ";
    }if($facture!=""){
        $facture = explode('-', $facture);
        $date_one = date('Y-m-d', strtotime($facture[0]));
        $date_two = date('Y-m-d', strtotime($facture[1]));
        $filtro .= " and cast(gc.date_facture as date) between '$date_one' and '$date_two'  ";
    }if($pago!=""){
        $pago = explode('-', $pago);
        $date_one = date('Y-m-d', strtotime($pago[0]));
        $date_two = date('Y-m-d', strtotime($pago[1]));
        $filtro .= " and  cast(gc.date_facture as date) between '$date_one' and '$date_two'  ";
    }if($estado!=""){
        $filtro .= " and  gc.estado = '$estado' ";
    }if($cuenta!=""){
        $filtro .= " and cgc.id_caja_cuenta = '$cuenta' ";
    }


    $sql = "SELECT 
                *, gc.rowid AS id
                 , CASE
                        when gc.estado = 'P' then 'PENDIENTE'
                        when gc.estado = 'E' then 'ANULADO'
                        when gc.estado = 'A' then 'GENERADO'
                   END as estado_gasto 
                   ,concat('CJA_', lpad('0',(5-length(cgc.id_ope_caja)),'0'),cgc.id_ope_caja) as n_abierta_caja
            FROM
                (SELECT * FROM tab_ope_gastos_clinicos n) AS gc
                    INNER JOIN
                (SELECT m.rowid, m.nom FROM tab_ope_gastos_nom m) AS m ON m.rowid = gc.id_nom_gastos
                LEFT JOIN 
                ( select cg.id_ope_caja, cg.id_gasto , dc.n_cuenta , dc.name_acount , dc.to_caja_direccion ,  c.id_caja_cuenta , 
                     (select u.usuario from tab_login_users u where u.rowid = c.id_user_caja) as usuario  FROM
                     tab_ope_cajas_det_gastos cg
                      inner join 
                     tab_ope_cajas_clinicas c on c.rowid = cg.id_ope_caja 
                      inner join 
                     tab_ope_declare_cuentas dc on dc.rowid = c.id_caja_cuenta ) cgc on cgc.id_gasto = gc.rowid";
    $sql .= " where 1=1 ".$filtro ;
    $sql .= " order by gc.rowid desc ";

//    echo '<pre>';print_r($sql); die();

    $result = $db->query($sql);
    if($result){
        if($result->rowCount()>0) {

            $data = array();
            while ($obj = $result->fetchObject()){

                if($obj->name_acount){
                    $caja = $obj->n_cuenta." ".$obj->name_acount." ".$obj->to_caja_direccion." | ".$obj->n_abierta_caja ." | users: ".$obj->usuario;
                }else{
                    $caja = "";
                }
                $rows = array();

                $rows[] = date("Y/m/d", strtotime($obj->tms));
                $rows[] = $obj->nom;
                $rows[] = $obj->desc;
                $rows[] = $caja;
                $rows[] = date("Y/m/d", strtotime($obj->date_facture));
                $rows[] = (($obj->date_payent=='0000-00-00')?'':date("Y/m/d", strtotime($obj->date_payent)));
                $rows[] = number_format($obj->amount, '2','.', '');
                $rows[] = $obj->estado_gasto;

                $data[] = $rows;

            }
        }
    }


    $objPHPExcel = new PHPExcel();

    $objPHPExcel->setActiveSheetIndex(0)->setCellValueExplicit('A1', 'REPORTE DE GASTOS ' . date('Y/m/d'));
    $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A1:H1');
    $objPHPExcel->getActiveSheet()->getStyle('A1:H1')->applyFromArray(
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

    //asignacion de titulos
    $array = array('Emitido','Categoria','Detalle','Caja','Fecha de Factura','Fecha de Pago','Monto','Estado');
    $cols = 0;
    foreach ($array as $titulo) {
            $objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($cols, 2, $titulo);
        $cols++;
    }

    //titulo stylos
    $objPHPExcel->getActiveSheet()->getStyle('A2:H2')->applyFromArray(
        array(
            'font' => array(
                'bold' => true,
                'size' => 12,
            ),
            'borders' => array('bottom'=>array('style'=>PHPExcel_Style_Border::BORDER_DOUBLE)),
            'alignment' => array( 'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,'wrap' => TRUE),
        )
    );


    //Se aplica el ancho automatico
    for ($i = 'A'; $i <= 'H'; $i++) {

        switch ($i){
            case 'C':
                $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension($i)->setWidth(50);
                break;
            case 'D':
                $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension($i)->setWidth(60);
                break;
            default:
                $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension($i)->setWidth(20);
                break;
        }
    }

    $rowsd = 3;//Fila
    foreach ($data as $value){
        $cols = 0;//columna
        foreach ($value as $itc){
            $objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($cols, $rowsd, $itc);
            $cols++;
        }
        $rowsd++;
    }

//    echo '<pre>'; print_r($sql); die();

    $objPHPExcel->getActiveSheet()->getStyle('A3:H'.$rowsd)->applyFromArray(array(
        'alignment' => array( 'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,'wrap' => TRUE),
    ));


    // Se asigna el nombre a la hoja
    $objPHPExcel->getActiveSheet()->setTitle('Reporte Gastos');
    // Se activa la hoja para que sea la que se muestre cuando el archivo se abre
    $objPHPExcel->setActiveSheetIndex(0);

    //paneles congelados
    $objPHPExcel->getActiveSheet(0)->freezePaneByColumnAndRow(0, 3);

    // Se manda el archivo al navegador web, con el nombre que se indica, en formato 2007
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="Reporte Gastos.xlsx"');
    header('Cache-Control: max-age=0');


    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
    $objWriter->save('php://output');

}

?>