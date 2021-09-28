<?php

if(isset($_GET['accion_exportar'])) {

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

    require_once '../../../config/lib.global.php';
    require_once DOL_DOCUMENT . '/application/system/conneccion/conneccion.php';    //Coneccion de Empresa
    require_once DOL_DOCUMENT . '/public/lib/PHPExcel2014/PHPExcel.php';
    require_once DOL_DOCUMENT . '/application/controllers/controller.php';

    $cn = new ObtenerConexiondb();                    //Conexion global Empresa Fija
    $db = $cn::conectarEmpresa($_SESSION['db_name']); //coneccion de la empresa variable global

    $objectCitasAgendadas = array();

    $doctor = "";
    $estado = "";
    $fechaInicio = "";
    $fechaFin = "";
    $fechaFin = "";
    $paciente = "";
    $n_citas = "";

    if (GETPOST('accion_exportar') == 'pdf_filter') {
        $doctor = GETPOST('odontologo');
        $estado = GETPOST('estados');
        $fechaInicio = str_replace('/', '-', (explode('-', GETPOST('fecha'))[0]));
        $fechaFin = str_replace('/', '-', (explode('-', GETPOST('fecha'))[1]));
        $paciente = GETPOST('pacientes');
        $n_citas = GETPOST('n_cita');
    }


    $sql = "SELECT 
            date_format(d.fecha_cita, '%Y/%m/%d')  as fecha_cita,         
            d.hora_inicio , 
            d.hora_fin ,
            d.rowid  as id_cita_det,
            concat(p.nombre ,' ',p.apellido) as paciente, 
            p.rowid as idpaciente,                   
            p.telefono_movil, 
            concat(o.nombre_doc,' ', o.apellido_doc) as doct, 
            s.text as estado,
            s.color as color,
            d.fk_estado_paciente_cita , 
            c.comentario ,
            ifnull(es.nombre_especialidad,'General') as especialidad,
            p.telefono_movil as telefono_movil,
            d.fk_doc as iddoctor , 
            p.email as email, 
            d.comentario_adicional as comentario_adicional,
            c.fk_paciente as idpaciente  ,
             -- validaciones
             -- citas atrazados con estado no confirmado
             IF( now() > CAST(d.fecha_cita AS DATETIME)  
                        && d.fk_estado_paciente_cita in(2,1,3,4,7,8,9,10,5)  , 
                            concat('Atrasada ', (select concat(s.text) from tab_pacientes_estado_citas s where s.rowid = d.fk_estado_paciente_cita) , 
                                    ' | Fecha : ' , date_format(d.fecha_cita, '%Y/%m/%d') , ' | Hora: ' , d.hora_inicio ,' h ' , d.hora_fin) , ''
                                    ) as cita_atrazada
									            
         FROM 
            tab_pacientes_citas_cab c 
				inner join
            tab_pacientes_citas_det d on d.fk_pacient_cita_cab = c.rowid
				inner join 
			tab_admin_pacientes p on c.fk_paciente = p.rowid
				inner join
			tab_pacientes_estado_citas s on s.rowid = d.fk_estado_paciente_cita
				inner join
			tab_odontologos o on o.rowid = d.fk_doc
				left join
			tab_especialidades_doc es on es.rowid = d.fk_especialidad
            where 1=1
         ";
    if (!empty($doctor)) {
        $sql .= " and d.fk_doc in(" . $doctor . ")";
    }
    if (!empty($estado)) {
        $sql .= " and d.fk_estado_paciente_cita in(" . $estado . ") ";
    }
    if (!empty($fechaInicio) && !empty($fechaFin)) {
        $sql .= " and cast(d.fecha_cita as date) between cast('$fechaInicio' as date) and cast('$fechaFin' as date) ";
    }
    if (!empty($MostrarCitasCanceladasEliminadas)) {
        $sql .= " and d.estado = 'E' or  d.fk_estado_paciente_cita = 9 ";
    }
    if (!empty($paciente)) {
        $sql .= " and c.fk_paciente in($paciente) ";
    }
    if (!empty($n_citas)) {
        $sql .= " and d.rowid like '%$n_citas%' ";
    }
    if (!empty($idagend)) {
        $sql .= " and d.rowid in(" . $idagend . ")";
    }

    $sql .= " order by d.fecha_cita desc ";


    $result = $db->query($sql);
    if ($result) {
        if ($result->rowCount() > 0) {
            $array = $result->fetchAll(PDO::FETCH_ASSOC);
        }
    }

//    echo '<pre>'; print_r($sql); die();

    $objPHPExcel = new PHPExcel();

    $objPHPExcel->setActiveSheetIndex(0)->setCellValueExplicit('A1', 'REPORTE DE CITAS AGENDADES ' . date('Y/m/d'));
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

    $array_titulos = array();
    $array_titulos[] = "N.citas";
    $array_titulos[] = "Fecha";
    $array_titulos[] = "Hora entrada";
    $array_titulos[] = "Hora salida";
    $array_titulos[] = "Pacientes";
    $array_titulos[] = "Doctor(a)";
    $array_titulos[] = "Estado";
    $array_titulos[] = "¿Atrasada?";

    //asignacion de titulos
    $cols = 0;
    foreach ($array_titulos as $titulo) {
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValueByColumnAndRow($cols, 2, $titulo);
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


    //asignacion de detalles
    $itemsAgenda = [];
    foreach ($array as $items) {
        $valueEx = [];
        $valueEx[] = 'C_' . str_pad($items['id_cita_det'], 5, "0", STR_PAD_LEFT);//NUMERO
        $valueEx[] = $items['fecha_cita'];
        $valueEx[] = $items['hora_inicio'];
        $valueEx[] = $items['hora_fin'];
        $valueEx[] = $items['paciente'];
        $valueEx[] = $items['doct'];
        $valueEx[] = $items['estado'];
        $valueEx[] = $items['cita_atrazada'];
        $itemsAgenda[] = $valueEx;
    }

    $rowsd = 3;//Fila
    foreach ($itemsAgenda as $value){
        $cols = 0;//columna
        foreach ($value as $itc){
            $objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($cols, $rowsd, $itc);
            $cols++;
        }
        $rowsd++;
    }



    //Se aplica el ancho automatico
    for ($i = 'A'; $i <= 'H'; $i++) {

        switch ($i){
            case 'E':
                $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension($i)->setWidth(30);
                break;
            case 'F':
                $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension($i)->setWidth(40);
                break;
            case 'G':
                $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension($i)->setWidth(40);
                break;
            case 'H':
                $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension($i)->setWidth(70);
                break;
            default:
                $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension($i)->setWidth(20);
                break;
        }
    }


    $objPHPExcel->getActiveSheet()->getStyle('A3:H'.$rowsd)->applyFromArray(array(
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
    header('Content-Disposition: attachment;filename="Reporte CITA.xlsx"');
    header('Cache-Control: max-age=0');


    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
    $objWriter->save('php://output');


}else{

    echo 'error de salida de datos';
    die();
}

?>