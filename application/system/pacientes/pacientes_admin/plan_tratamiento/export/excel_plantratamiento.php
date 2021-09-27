<?php

//me declara si hay algun error
error_reporting(E_ALL);

require_once '../../../../../config/lib.global.php';

if(isset($_GET['id_paciente'])){

    session_start();

//    $idTTO = $_GET['id_tratamiento'];
    $idpac = $_GET['id_paciente'];

    if(!isset($_SESSION['is_open']))
    {
        header("location:".DOL_HTTP."/application/system/login");
    }else{

        require_once  DOL_DOCUMENT .'/application/system/conneccion/conneccion.php';    //Coneccion de Empresa
        require_once  DOL_DOCUMENT .'/public/lib/PHPExcel2014/PHPExcel.php';
        require_once  DOL_DOCUMENT .'/application/controllers/controller.php';

        /**SE CREA LAS VARIABLES DE INICIO**/
        $cn = new ObtenerConexiondb();
        $db = $cn::conectarEmpresa($_SESSION['db_name']);
        $InformacionEntity = (object)array(
            'nombre'            => $_SESSION['nombreClinica'],
            'email'             => $_SESSION['emailClinica'],
            'direccion'         => $_SESSION['direccionClinica'],
            'telefonoClinica'   => $_SESSION['telefonoClinica'],
            'entidad'           => $_SESSION['entidad'],
        );


        $fecha           = GETPOST('fecha');
        $id_ptratamiento = GETPOST('tratmid');
        $estado          = GETPOST('estado');

        $where = "";
        if(!empty($fecha)){
            $fecha = explode('-', $fecha);
            $fechaIni = trim( str_replace('/', '-', $fecha[0] ) );
            $fechaFin = trim( str_replace('/', '-', $fecha[1] ) );
            $where .= " and cast(tc.fecha_create as date) between cast('$fechaIni' as date) and cast('$fechaFin' as date) ";
        }if(!empty($id_ptratamiento)){
            $where .= " and tc.rowid in($id_ptratamiento) ";
        }
        if(!empty($estado)){

            if($estado=='Finalizados')
                $where .= " and tc.estados_tratamiento in('F') ";
            if($estado=='Anulados')
                $where .= " and tc.estados_tratamiento in('E') ";
            if($estado=='Abonados')
                $where .= " and tc.estados_tratamiento in('S') ";
            if($estado=='Diagnostico')
                $where .= " and tc.estados_tratamiento in('A') ";
        }else{
            $where .= " and tc.estados_tratamiento in('A', 'S') ";
        }


        $ArrayTratamiento = [];
        $sql_a = "SELECT 
                        tc.rowid as idTratamiento,
                        CONCAT('PLAN DE TRATAMIENTO: N. ', tc.numero) as numero,
                        tc.fk_paciente,
                        CONCAT(ap.nombre, ' ', ap.apellido) nombre,
                        tc.fk_paciente,
                        tc.fk_doc fkdoc,
                        IFNULL(CONCAT(od.nombre_doc, ' ', od.apellido_doc), 'NO ASIGNADO') AS nombre_doc,
                        tc.estados_tratamiento,
                        tc.ultima_cita,
                        CASE tc.estados_tratamiento
							when 'E' then 'ANULADO'
                            when 'F' then 'FINALIZADO'
							when 'A' then 'DIAGNÓSTICO'
							when 'S' then 'SALDO ASOCIADO'
                            ELSE ''
                        END  AS situacion,
                        tc.edit_name AS edit_name,
                        tc.fk_paciente AS idpaciente,
                        tc.fk_cita AS idCitas,
                        tc.fecha_create , 
						sum(pd.abonado) as abonado, 
                        ifnull(n_serv.num,0) as cant_servicios,
                        dr.realizado_mont ,
                        dp.pendiente_mont ,
                        tc.ultima_cita
                   FROM 
						tab_plan_tratamiento_cab tc 
							inner join
						tab_admin_pacientes ap on ap.rowid = tc.fk_paciente
							left join 
						tab_odontologos od on od.rowid = tc.fk_doc
							left join
						(select pd.fk_plantram_cab as id_tratamiento, round(pd.amount, 2) as abonado from tab_pagos_independ_pacientes_det pd) as pd on pd.id_tratamiento = tc.rowid
							left join
						(select count(*) as num, d.fk_plantratam_cab as idt_cab  from tab_plan_tratamiento_det d group by d.fk_plantratam_cab) as n_serv on n_serv.idt_cab = tc.rowid
							left join
						(select sum(round(d.total,2)) as realizado_mont, d.fk_plantratam_cab as idt_cab_r from tab_plan_tratamiento_det d where d.estadodet = 'R' group by d.fk_plantratam_cab) as dr on dr.idt_cab_r = tc.rowid
                        	left join
						(select sum(round(d.total,2)) as pendiente_mont, d.fk_plantratam_cab as idt_cab_p from tab_plan_tratamiento_det d where d.estadodet in('A','P') group by d.fk_plantratam_cab) as dp on dp.idt_cab_p = tc.rowid
                    where    1=1
                    and tc.fk_paciente = ".($idpac)."   
					".$where."
                    group by tc.rowid  order by tc.rowid desc ";
        $result_a = $db->query($sql_a);
        if($result_a){
            $arr = $result_a->fetchAll(PDO::FETCH_ASSOC);
            foreach ($arr as $item){
                $ArrayTratamiento[$item['idTratamiento']] = $item;
            }
        }

//        echo '<pre>';print_r($sql_a); die();

        //Citas asociado
        $sql_b = "SELECT 
                        concat('C_', lpad('0',(5-length(d.rowid)),'0'),d.rowid) as numero_c ,
                        asoc.fk_tratamiento as id_ptratamiento, 
                        d.fecha_cita  as fecha_cita, 
                        cast(concat(cast(d.fecha_cita as date),' ',d.hora_inicio) as datetime) as fechaIniCita,        
                        d.hora_inicio , 
                        d.hora_fin ,
                        concat(od.nombre_doc,' ', od.apellido_doc)  as doct ,
                        sc.text as estado_c,
                        sc.color as color_estado_c, 
                        c.comentario ,
                        ifnull((select es.nombre_especialidad FROM tab_especialidades_doc es where es.rowid = d.fk_especialidad),'General') as especialidad,
                        d.fk_doc as iddoctor , 
                        d.comentario_adicional as comentario_adicional,
                        c.fk_paciente as idpaciente  ,
                         -- validaciones
                         -- citas atrazados con estado no confirmado
                         IF( now() > CAST(d.fecha_cita AS DATETIME)  
                                    && d.fk_estado_paciente_cita in(2,1,3,4,7,8,9,10,11,5,  (select statusc.rowid from tab_pacientes_estado_citas statusc where statusc.system=0) )  , 
                                        concat('Atrasada ', (select concat(s.text) from tab_pacientes_estado_citas s where s.rowid = d.fk_estado_paciente_cita) , 
                                                ' | Fecha : ' , date_format(d.fecha_cita, '%Y/%m/%d') , ' | Hora: ' , d.hora_inicio ,' a ' , d.hora_fin) , ''
                                                ) as cita_atrazada   
                     FROM 
                        tab_pacientes_citas_cab c 
							inner join 
                        tab_pacientes_citas_det d on d.fk_pacient_cita_cab = c.rowid
							inner join 
						tab_pacientes_estado_citas sc on sc.rowid = d.fk_estado_paciente_cita
							left join 
						tab_especialidades_doc esp on esp.rowid = d.fk_especialidad
							inner join
						tab_odontologos od on od.rowid = d.fk_doc
							inner join
                        tab_plan_asoc_tramt_citas asoc  on asoc.fk_cita = d.rowid
                        where asoc.fk_paciente =  ".($idpac);

        $result_b = $db->query($sql_b);
        if($result_b){
            $arr_citas_ascia = array();
            $arr_citas_ascia = $result_b->fetchAll(PDO::FETCH_ASSOC);
            if(count($arr_citas_ascia)>0){
                foreach ($arr_citas_ascia as $value){
                    if(array_key_exists( $value['id_ptratamiento'], $ArrayTratamiento)){
                        $ArrayTratamiento[ $value['id_ptratamiento'] ]['citasAsoc'][] = $value;
                    }
                }
            }
        }

       //echo '<pre>'; print_r($ArrayTratamiento); die();

        $getPaciente = getnombrePaciente($idpac);
        $objPHPExcel = new PHPExcel();

        //titulo 1
        $objPHPExcel->setActiveSheetIndex(0)->setCellValueExplicit('A1', 'REPORTE PLANES DE TRATAMIENTO'.date('Y/m/d'));
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A1:G1');
        $objPHPExcel->getActiveSheet()->getStyle('A1:G1')->applyFromArray(
          array(
             'font'=> array(
                  'bold'=>true,
                  'size'=>14,
                  'color'=>array(
                      'argb'=> '1f497d'
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


        //titulos 2
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValueExplicit('A2', 'Fecha de impresión')
            ->setCellValueExplicit('C2', date('Y/m/d H:m:s'))
            ->setCellValueExplicit('A3', 'Paciente:')
            ->setCellValueExplicit('C3', $getPaciente->nom)
            ->setCellValueExplicit('A4', 'C.I.')
            ->setCellValueExplicit('C4', $getPaciente->ruc_ced)
            ;

        $objPHPExcel->getActiveSheet()->getStyle('A2:C4')->applyFromArray(
            array(
                'font'=> array(
                    'bold'=>true,
                    'size'=>12,
                ),
            )
        );

        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A2:B2');
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A3:B3');
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A4:B4');

        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('C2:G2');
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('C3:G3');
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('C4:G4');



        $AddTitulosCitasAso = array('N.CITA','ESPECIALIDAD','EMITIDO','ESTADO','ATRAZADA');
        $Addtitulos = array('PROFESIONAL','ÚLTIMA CITA','ESTADO FINANCIERO','CANT. PRESTACIONES/SERVICIOS','TOTAL REALIZADO','TOTAL ABONADO','TOTAL PENDIENTE');

        $i = 6;
        foreach ($ArrayTratamiento as $value){

            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValueExplicit('A'.$i, $value['numero'].' '.date('Y/m/d', strtotime($value['fecha_create'])) );

            $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A'.$i.':G'.$i);

            $objPHPExcel->getActiveSheet()->getStyle('A'.$i.':G'.$i)->applyFromArray(
                array(
                    'font'=> array('bold'=>true,'size'=>12,'color'=>array('argb'=>'1f497d')),
                    'fill' => array(  'type' => PHPExcel_Style_Fill::FILL_SOLID,'color' => array('argb' => 'eeece1')),
                    'borders' => array( 'top' => array( 'style'=> PHPExcel_Style_Border::BORDER_DOUBLE) ),
                    'alignment' => array(  'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,),
                ));

            //se agrega los titulos
            $i++;
            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValueExplicit('A'.$i,$Addtitulos[0]) //PROFESIONAL
                ->setCellValueExplicit('B'.$i,$Addtitulos[1]) //ULTIMA CITA
                ->setCellValueExplicit('C'.$i,$Addtitulos[2]) //ESTADO FINANCIERO
                ->setCellValueExplicit('D'.$i,$Addtitulos[3]) //CANT. PRESTACIONES/SERVICIOS
                ->setCellValueExplicit('E'.$i,$Addtitulos[4]) //TOTAL REALIZADO
                ->setCellValueExplicit('F'.$i,$Addtitulos[5]) //TOTAL ABONADO
                ->setCellValueExplicit('G'.$i,$Addtitulos[6]) //TOTAL PENDIENTE
            ;

            $objPHPExcel->getActiveSheet()->getStyle('A'.$i.':G'.$i)->applyFromArray(array('font'=> array('bold'=>true,'size'=>12),));

            //Se agrega el detalle Asignado
            $i++;
            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValueExplicit('A'.$i, $value['nombre_doc'])
                ->setCellValueExplicit('B'.$i, date('Y/m/d  H:m:s', strtotime($value['ultima_cita'])))
                ->setCellValueExplicit('C'.$i, $value['situacion'])
                ->setCellValueExplicit('D'.$i, $value['cant_servicios']) //numero de  servicios asignado al tratamiento
                ->setCellValue('E'.$i, round($value['realizado_mont'], 2)) //saldo realizado
                ->setCellValue('F'.$i, round($value['abonado'], 2)) //saldo abonado
                ->setCellValue('G'.$i, round($value['pendiente_mont'], 2))    //saldo pendiente
             ;

            //Se agrega las citas asociadas
            //solo si tiene asociado
            if(array_key_exists('citasAsoc', $value)){
                $i++;
                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValueExplicit('A'.$i, 'CITAS ASOCIADAS');
                $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A'.$i.':G'.$i);
                $objPHPExcel->getActiveSheet()->getStyle('A'.$i.':G'.$i)->applyFromArray(array('font'=> array('bold'=>true,'size'=>12),));

                $i++;
                //se agregan los titulos
                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValueExplicit('A'.$i,$AddTitulosCitasAso[0]) //N. CITAS
                    ->setCellValueExplicit('B'.$i,$AddTitulosCitasAso[1]) //ESPECIALIDAD
                    ->setCellValueExplicit('C'.$i,$AddTitulosCitasAso[2]) //EMITIDO
                    ->setCellValueExplicit('D'.$i,$AddTitulosCitasAso[3]) //CANT. PRESTACIONES/SERVICIOS
                    ->setCellValueExplicit('E'.$i,$AddTitulosCitasAso[4]) //ATRAZADAS
                ;
                $objPHPExcel->setActiveSheetIndex(0)->mergeCells('E'.$i.':G'.$i);
                $objPHPExcel->getActiveSheet()->getStyle('A'.$i.':G'.$i)->applyFromArray(array('font'=> array('bold'=>true,'size'=>12),));


                $ArrCitas = $value['citasAsoc'];

                foreach ($ArrCitas as $itemTC){
                    $i++;
                    $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValueExplicit('A'.$i,$itemTC['numero_c'])
                        ->setCellValueExplicit('B'.$i,$itemTC['especialidad'])
                        ->setCellValueExplicit('C'.$i,date('Y/m/d H:m:s', strtotime($itemTC['fechaIniCita'])))
                        ->setCellValueExplicit('D'.$i,$itemTC['estado_c'])
                        ->setCellValueExplicit('E'.$i,$itemTC['cita_atrazada'])
                    ;
                    $objPHPExcel->setActiveSheetIndex(0)->mergeCells('E'.$i.':G'.$i);
                    $objPHPExcel->getActiveSheet()->getStyle('A'.$i.':G'.$i)->applyFromArray(array( 'font'=> array('bold'=>false,'size'=>12,'color'=>array('argb'=>'1f497d')),));

                }

            }


            $i+=4;
        }

        //Se aplica el ancho automatico
        for ($i = 'A'; $i <= 'G'; $i++) {
            $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension($i)->setWidth(30);
        }

        // Se asigna el nombre a la hoja
        $objPHPExcel->getActiveSheet()->setTitle('Reporte TTO.');
        // Se activa la hoja para que sea la que se muestre cuando el archivo se abre
        $objPHPExcel->setActiveSheetIndex(0);

        //paneles congelados
        //$objPHPExcel->getActiveSheet(0)->freezePaneByColumnAndRow(0, 14);

        // Se manda el archivo al navegador web, con el nombre que se indica, en formato 2007
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Reporte TTO.xlsx"');
        header('Cache-Control: max-age=0');


        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');


        die();

    }

}

?>