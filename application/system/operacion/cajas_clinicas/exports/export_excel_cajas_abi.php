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
    require_once DOL_DOCUMENT . '/application/system/conneccion/conneccion.php';    //Coneccion de Empresa
    require_once DOL_DOCUMENT . '/public/lib/PHPExcel2014/PHPExcel.php';
    require_once DOL_DOCUMENT . '/application/controllers/controller.php';

    $cn = new ObtenerConexiondb();                    //Conexion global Empresa Fija
    $db = $cn::conectarEmpresa($_SESSION['db_name']); //coneccion de la empresa variable global
    $objPHPExcel = new PHPExcel();


    $users        = GETPOST('users');
    $apertura     = GETPOST('apertura');
    $cierre       = GETPOST('cierre');
    $estado       = GETPOST('estado');
    $acumulado    = GETPOST('acumulado');

    $where = "";

    if($apertura!=""){
        $date = explode('-', $apertura);
        $where .= " and cast(c.date_apertura as date) between '".(date('Y-m-d', strtotime($date[0])))."' and '".(date('Y-m-d', strtotime($date[1])))."' ";
    }
    if($cierre!=""){
        $date = explode('-', $cierre);
        $where .= " and cast(c.date_cierre as date) between '".(date('Y-m-d', strtotime($date[0])))."' and '".(date('Y-m-d', strtotime($date[1])))."' ";
    }
    if($acumulado!=""){
        $where .= " and ROUND((ifnull(d.saldo_acumulado, 0) - ifnull(g.monto, 0)), 2) = ROUND($acumulado, 2) ";
    }
    if($users!=""){
        $where .= " and c.id_user_caja = $users";
    }
    if($estado!=""){
        $where .= " and c.estado = '$estado' ";
    }


    $arrayDetalle = [];
    $sql = "SELECT 
                    c.rowid as id_ope_caja, 
                    c.rowid, 
                    concat(dc.n_cuenta, ' ', dc.name_acount) as cuenta, 
                    dc.to_caja_direccion, 
                    c.id_caja_cuenta,
                    c.date_registro,
                    c.date_apertura,
                    c.date_cierre,
                    c.id_user_caja,
                    us.usuario,
                    c.saldo_inicial, 
                    ifnull(g.monto, 0) as montoGasto, 
                    ifnull(d.saldo_acumulado, 0) as saldo_acumulado, 
                    ifnull(g.monto, 0) as montoGasto, 
                    (ifnull(d.saldo_acumulado, 0) - ifnull(g.monto, 0)) as total, 
                    (case c.estado
						when 'A' then 'Abierto'
                        when 'C' then 'Cerrada'
                        end ) as estado
                FROM
                      tab_ope_cajas_clinicas c
                    left join
                      (select ifnull(round(sum(g.monto),2), 0) as monto , g.id_ope_caja as id_ope_caja_gst from tab_ope_cajas_det_gastos g where 1=1 and g.estado <> 'E' group by g.id_ope_caja) as g on g.id_ope_caja_gst = c.rowid
                    left join
                      (select ifnull(round(sum(d.amount), 2), 0) as saldo_acumulado, id_ope_caja_cab from tab_ope_cajas_clinicas_det d where d.estado <> 'E' group by d.id_ope_caja_cab) as d on d.id_ope_caja_cab = c.rowid
                    inner join
                       tab_ope_declare_cuentas dc on dc.rowid = c.id_caja_cuenta
                    inner join 
                       tab_login_users us on us.rowid = c.id_user_caja 
                            where   1=1  ";
    $sql .= $where;
    $sql .= " order by c.rowid desc ";
    $result = $db->query($sql);
    if($result) {
        if($result->rowCount()>0){
            while ($obbject = $result->fetchObject()){
                $secu = "CJA_".str_pad($obbject->id_ope_caja, 5, "0", STR_PAD_LEFT);
                $rows = [];
                $rows[] = $secu;
                $rows[] = $obbject->usuario;
                $rows[] = $obbject->cuenta;
                $rows[] = $obbject->to_caja_direccion;
                $rows[] = $obbject->date_apertura;
                $rows[] = $obbject->date_cierre;
                $rows[] = $obbject->saldo_inicial;
                $rows[] = $obbject->total;
                $rows[] = $obbject->estado;
                $arrayDetalle[] = $rows;
            }
        }
    }

    //titulo stylos
    $objPHPExcel->getActiveSheet()->getStyle('A2:I2')->applyFromArray(
        array(
            'font' => array(
                'bold' => true,
                'size' => 12,
            ),
            'borders' => array('bottom'=>array('style'=>PHPExcel_Style_Border::BORDER_DOUBLE)),
            'alignment' => array( 'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,'wrap' => TRUE),
        )
    );


    $array_titulos = array('Secuencial','Usuario','Caja','Direccion','Fecha de apertura','Fecha de Cierre','Saldo Inicial','Acumulado','Estado');

    //asignacion de titulos
    $cols = 0;
    foreach ($array_titulos as $titulo) {
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValueByColumnAndRow($cols, 2, $titulo);
        $cols++;
    }

    $objPHPExcel->setActiveSheetIndex(0)->setCellValueExplicit('A1', 'CAJAS CLINICAS | FECHA DE IMPRESION' . date('Y/m/d'));
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

    $rowsd = 3;
    foreach ($arrayDetalle as $value){
        $cols=0;
        foreach ($value as $it){
                $objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($cols, $rowsd, $it);
            $cols++;
        }
        $rowsd++;
    }

    $objPHPExcel->getActiveSheet()->getStyle('A1:I'.$rowsd)
        ->applyFromArray(array(
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
        ));

    //Se aplica el ancho automatico
    for ($i = 'A'; $i <= 'I'; $i++) {
        switch ($i){
            case 'A':
                $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension($i)->setWidth(15);
                break;
            case 'B':
                $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension($i)->setWidth(20);
                break;
            case 'D':
                $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension($i)->setWidth(50);
                break;
            case 'E':
                $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension($i)->setWidth(20);
                break;
            case 'F':
                $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension($i)->setWidth(20);
                break;
            case 'C':
                $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension($i)->setWidth(30);
                break;
            case 'I':
                $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension($i)->setWidth(30);
                break;
            default:
                $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension($i)->setWidth(20);
                break;
        }
    }

//    echo '<pre>'; print_r($arrayDetalle);  die();


    // Se asigna el nombre a la hoja
    $objPHPExcel->getActiveSheet()->setTitle('Reporte Cajas Clinica.');
    // Se activa la hoja para que sea la que se muestre cuando el archivo se abre
    $objPHPExcel->setActiveSheetIndex(0);

    //paneles congelados
    $objPHPExcel->getActiveSheet(0)->freezePaneByColumnAndRow(0, 3);

    // Se manda el archivo al navegador web, con el nombre que se indica, en formato 2007
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="Reporte Cajas Clinica.xlsx"');
    header('Cache-Control: max-age=0');

    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
    $objWriter->save('php://output');


}


?>