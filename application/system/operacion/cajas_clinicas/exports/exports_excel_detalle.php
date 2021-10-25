<?php




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



if(isset($_GET['export'])){

    session_start();

    error_reporting(E_ALL);

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
    require_once DOL_DOCUMENT . '/application/system/operacion/cajas_clinicas/controller/caja_controller.php';

    $cn = new ObtenerConexiondb();                    //Conexion global Empresa Fija
    $db = $cn::conectarEmpresa($_SESSION['db_name']); //coneccion de la empresa variable global
    $objPHPExcel = new PHPExcel();

    $id_ope_caja = GETPOST('id_ope_caja');

    $array_a = [];
    $sql_a = "SELECT 
    dc.n_cuenta , 
    dc.to_caja_direccion ,
    dc.name_acount,
    c.estado, 
    c.date_apertura, 
    c.date_cierre, 
    c.saldo_inicial , 
    
    (select s.usuario from tab_login_users s where s.rowid = c.id_user_caja) as nom_usu,
     CASE
			WHEN c.estado = 'A' THEN 'Caja abierta'
			WHEN c.estado = 'C' THEN 'Caja Cerrada'
			WHEN c.estado = 'E' THEN 'Caja Eliminada'
		ELSE 'Caja no asignada' 
	END as estado_caja 
FROM
    tab_ope_cajas_clinicas c
      inner join
    tab_ope_declare_cuentas dc on dc.rowid = c.id_caja_cuenta
    where c.rowid = $id_ope_caja
    limit 1";

    $result_a = $db->query($sql_a);
    if($result_a){
        if($result_a->rowCount()>0){
            $result = $result_a->fetchObject();
        }
    }


    $direccion_caja  = $result->to_caja_direccion;
    $numero_caja     = $result->n_cuenta;
    $nom_usu         = $result->nom_usu;
    $cajaEstado      = $result->estado_caja;
    $st              = $result->estado;
    $date_apertura   = date("Y/m/d H:m:s", strtotime($result->date_apertura));
    $saldo_inicial   = round((double)$result->saldo_inicial, 2);

    if($result->date_cierre != ""){
        $date_cierre   = date("Y/m/d H:m:s", strtotime($result->date_cierre));
    }
    else{
        $date_cierre = "";
    }

    $Efectivo   = Saldo_caja_efectivo(19, 'Efectivo', $id_ope_caja, date('Y-m-d', strtotime($date_apertura)));
    $Recaudado  = Saldo_recaudado($id_ope_caja, date('Y-m-d', strtotime($date_apertura)));
    $Gasto      = Saldo_Gastos($id_ope_caja);


    $Total = (double)($Recaudado - $Gasto);

    $detall_caja =  array(
        'Dirección:'           =>$direccion_caja,
        'Usuario asociado:'    =>$nom_usu,
        'Fecha de apertura:'   =>$date_apertura,
        'Fecha de cierre:'     =>$date_cierre,
        'Saldo Inicial:'       =>$saldo_inicial,
        'Efectivo:'            =>$Efectivo,
        'Recaudado:'           =>$Recaudado,
        'Gasto(-)'             =>$Gasto,
        'Total'                =>$Total,
    );

    $objPHPExcel->setActiveSheetIndex(0)->setCellValueExplicit('A1', 'DETALLES DE CAJA #'.$numero_caja.' | FECHA DE IMPRESION ' . date('Y/m/d'));

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


    $i = 2;
    foreach ($detall_caja as $k => $valueTI){

        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A'.$i.':B'.$i);
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('C'.$i.':F'.$i);

        $col = 0;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($col, $i, $k);
        $col+=2;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($col, $i, $valueTI);


        $i++;
    }

    $style_a = array( 'font' => array(
        'bold' => false,
        'size' => 12,),'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,),);

    $objPHPExcel->getActiveSheet()->getStyle('A2:F'.$i)->applyFromArray($style_a);
    $objPHPExcel->getActiveSheet()->getStyle('A2:A'.$i)->getFont()->setBold(true);

    $objPHPExcel->getActiveSheet()->getStyle('A7:F7')->getFill()->applyFromArray(array('type'=>    PHPExcel_Style_Fill::FILL_SOLID,'startcolor' => array('rgb' => 'd7e4bc') ));
    $objPHPExcel->getActiveSheet()->getStyle('A9:F9')->getFill()->applyFromArray(array('type'=>    PHPExcel_Style_Fill::FILL_SOLID,'startcolor' => array('rgb' => 'f2dddc') ));

    //Caja cerrada o Abierta
    $objPHPExcel->setActiveSheetIndex(0)->setCellValueExplicit('A'.$i, $result->estado_caja);
    $objPHPExcel->getActiveSheet()->getStyle('A'.$i)->applyFromArray(array('alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,)));
    $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A'.$i.':F'.$i);

    //Transacciones de caja
    $i++;
    $objPHPExcel->setActiveSheetIndex(0)->setCellValueExplicit('A'.$i, 'TRASACCIONES DE CAJA #'.$numero_caja);
    $objPHPExcel->getActiveSheet()->getStyle('A'.$i)->getFill()->applyFromArray(array('type'=>    PHPExcel_Style_Fill::FILL_SOLID,'startcolor' => array('rgb' => 'd7e4bc') ));
    $objPHPExcel->getActiveSheet()->getStyle('A'.$i)->applyFromArray(array('alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,)));
    $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A'.$i.':F'.$i);

//    RECAUDACIONES DE PLANES DE TRATAMIENTO DE LOS PACIENTES
    $i++;
    $objPHPExcel->setActiveSheetIndex(0)->setCellValueExplicit('A'.$i, 'RECAUDACIONES DE PLANES DE TRATAMIENTO DE LOS PACIENTES');
    $objPHPExcel->getActiveSheet()->getStyle('A'.$i.':F'.$i)->applyFromArray(
        array( 'font' => array(
            'bold' => true,
            'size' => 12,
        ),  'alignment' => array( 'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,'wrap' => TRUE),)
    );
    $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A'.$i.':F'.$i);

//    LIST PLANES DE TRATAMIENTO
    $array_c = [];
    $sql_b = " SELECT 
        pg.fecha AS date_emitido_cobro,
        c.rowid AS id_ope_caja,
        c.id_caja_cuenta,
        c.date_apertura,
        c.date_cierre,
        CONCAT('Plan de Tratamiento', ' N.', td.numero) AS n_tratamiento,
        td.edit_name as edit_name_tratamiento, 
        CONCAT(p.nombre, ' ', p.apellido) AS paciente,
        ps.descripcion AS prestacion_servicio,
        ROUND(d.amount, 2) AS amount,
        b.nom as medio_pago,
        pg.n_fact_boleta
    FROM
        tab_ope_cajas_clinicas c
            INNER JOIN
        tab_ope_cajas_clinicas_det d ON c.rowid = d.id_ope_caja_cab
            INNER JOIN
        tab_bank_operacion b ON b.rowid = d.fk_tipo_pago
            INNER JOIN
        tab_plan_tratamiento_cab td ON td.rowid = d.fk_plan_tratam_cab
            INNER JOIN
        tab_admin_pacientes p ON p.rowid = d.fk_paciente
            INNER JOIN
        tab_conf_prestaciones ps ON ps.rowid = d.fk_prestacion_servicio
            INNER JOIN
        tab_pagos_independ_pacientes_cab pg ON pg.rowid = d.fk_pago_cab
    WHERE
        d.estado <> 'E'
        and c.estado <> 'E'
        and d.id_ope_caja_cab = ".$id_ope_caja.
        " and cast(d.date_apertura as date)  = '".(date('Y-m-d', strtotime($date_apertura)))."'  ";
//    print_r("<pre>".$sql_b); die();
    $result_b = $db->query($sql_b);
    if($result_b){
        if($result_b->rowCount()>0){
            $result_b = $result_b->fetchAll(PDO::FETCH_ASSOC);
            foreach ($result_b as $value){
                $rowss = [];
                    $rowss[] = $value['date_emitido_cobro'];
                    $rowss[] = $value['paciente'];
                    $rowss[] = $value['n_tratamiento'];
                    $rowss[] = $value['n_fact_boleta'];
                    $rowss[] = $value['prestacion_servicio'];
                    $rowss[] = $value['medio_pago'];
                    $rowss[] = $value['amount'];
                $array_c[] = $rowss;
            }
        }
    }

    $titulos_tratamientos = array(
        'Emitido pago',
        'Paciente',
        'Plan de tratamiento',
        'Documentos',
        'Prestación Servicios',
        'Medio de Pago',
        'monto'
    );

    $i++;
    $i = aplicarColunmDetalle($titulos_tratamientos, $array_c, $i, $objPHPExcel);

    //gastos
    $i++;//titulo principal de gastos clinicos
    $objPHPExcel->setActiveSheetIndex(0)->setCellValueExplicit('A'.$i, 'GASTOS CLINICOS');
    $objPHPExcel->getActiveSheet()->getStyle('A'.$i.':F'.$i)->applyFromArray(
        array( 'font' => array(
            'bold' => true,
            'size' => 12,
        ),  'alignment' => array( 'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,'wrap' => TRUE),)
    );
    $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A'.$i.':F'.$i);

    //detalles gastos
    $array_d = array();
    $sql_d = "select 
                    g.rowid as id_gasto_caja,
                    g.date_cc , 
                    gn.nom, 
                    g.detalle, 
                    gc.date_facture, 
                    b.nom as mediop, 
                    round(g.monto, 2) as monto
                from tab_ope_cajas_det_gastos g
                   inner join 
                   (select gc.rowid, gc.id_nom_gastos, gc.desc, gc.amount, gc.date_facture, gc.estado from tab_ope_gastos_clinicos gc where gc.on_caja_clinica = 1) as gc on gc.rowid = g.id_gasto
                   inner join
                   tab_ope_gastos_nom gn on gn.rowid = gc.id_nom_gastos
                   inner join 
                   tab_bank_operacion b on b.rowid = g.fk_medio_pago
                where gc.estado <> 'E' and g.id_ope_caja = $id_ope_caja";
    $result_d = $db->query($sql_d);
    if($result_d){
        if($result_d->rowCount()>0){
            $all = $result_d->fetchAll(PDO::FETCH_ASSOC);
            foreach ($all as $item) {
                $rowsg = [];
                    $rowsg[] = date('Y/m/d', strtotime($item['date_cc']));
                    $rowsg[] = $item['nom'];
                    $rowsg[] = $item['detalle'];
                    $rowsg[] = date('Y/m/d', strtotime($item['date_facture']));
                    $rowsg[] = $item['mediop'];
                    $rowsg[] = $item['monto'];
                $array_d[] = $rowsg;
            }
        }
    }

    $titulos_gastos = array('Emitido','Categoria','Detalle','Fecha Factura','Medio de Pago','monto');

    //asignacion de titulo y detalle
    $i++;
    $i = aplicarColunmDetalle($titulos_gastos,$array_d, $i, $objPHPExcel);


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