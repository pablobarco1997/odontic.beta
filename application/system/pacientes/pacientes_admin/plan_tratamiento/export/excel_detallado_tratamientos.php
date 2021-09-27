<?php

//me declara si hay algun error
error_reporting(E_ALL);

require_once '../../../../../config/lib.global.php';

if(isset($_GET['id_tratamiento']) && isset($_GET['id_paciente'])){

    session_start();

    $idTTO = $_GET['id_tratamiento'];
    $idpac = $_GET['id_paciente'];

    if(!isset($_SESSION['is_open']))
    {
        header("location:".DOL_HTTP."/application/system/login");
    }else{


        require_once  DOL_DOCUMENT .'/application/system/conneccion/conneccion.php';    //Coneccion de Empresa
        require_once DOL_DOCUMENT .'/public/lib/PHPExcel2014/PHPExcel.php';
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


        $object_a = array(); //infomacion del plan de tratamiento cabezera
        $object_b = array();//infomacion del plan de tratamiento detalle


        $query_a = "SELECT 
                c.numero, 
                concat(p.nombre ,' ', p.apellido) as nom , 
                p.direccion, 
                p.email , 
                p.telefono_movil 
            FROM
                tab_plan_tratamiento_cab c
                inner join 
                tab_admin_pacientes p on p.rowid = c.fk_paciente 
            where c.rowid = ".$idTTO." and p.rowid = ".$idpac;
        $result_a = $db->query($query_a);
        if($result_a){
            if($result_a->rowCount()>0){
                $object_a = $result_a->fetchAll(PDO::FETCH_ASSOC);
                $object_a = $object_a[0];
            }
        }

        $query_b = "SELECT 
                        pd.rowid, 
                        pd.fk_diente AS pieza,
                        cp.descripcion AS prestacion_servicio,
                        round(pd.precio_u, 2) AS precio,
                        pd.cantidad , 
                        round((pd.precio_u*pd.cantidad), 2) as total_serv, 
                        round((pd.total * (case pd.iva
								when 12 then 0.12 
								else 0
								end)), 2) as iva_calculado, 
                        pd.desc_adicional AS descuento_porc,
                        round(((pd.precio_u * pd.cantidad) * (pd.desc_adicional) / 100),2) as desc_dolares,  
                        pd.total,
                        pd.json_caras,
                        case pd.estadodet
							when 'P' then 'EN PROCESO'
							when 'A' then 'PENDIENTE'
							when 'R' then 'REALIZADO'
                            else 'ERROR CONSULTE CON SOPORTE'
                            end
                            as estado_detalle , 
                        ifnull((SELECT usuario FROM tab_login_users s where s.rowid = pd.fk_usuario limit 1),'') as user_create , 
                        ifnull((SELECT concat(s.nombre_doc ,' ', s.apellido_doc) as doc FROM tab_odontologos s where s.rowid = pd.realizada_fk_dentista limit 1),'') as user_realizado , 
                        ifnull((select l.name from tab_conf_laboratorios_clinicos l where l.rowid = cp.fk_laboratorio),'') as laboratorio
                    FROM
                        tab_plan_tratamiento_det pd,
                        tab_conf_prestaciones cp
                    WHERE
                        pd.fk_prestacion = cp.rowid
                        AND pd.fk_plantratam_cab = ".$idTTO."
                        order by pd.rowid desc";
        $result_b = $db->query($query_b);
        if($result_b){
            if($result_b->rowCount()>0){
                $object_b = $result_b->fetchAll(PDO::FETCH_ASSOC);
            }
        }

        $objPHPExcel = new PHPExcel();

        //estilos excel
        $titulo = array (
            'font' => array(
                'bold' => true,
                'name' => 'Calibri',
                'size' => 16
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                ),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array(
                    'argb' => 'dae4f0')
            ),);

        $subtitulos = array(
            'font'=>array(
                'bold' => true,
                'name' => 'Calibri',
                'size' => 12
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
        );

        $text_one =array(
            'font'=>array(
                'bold' => false,
                'name' => 'Calibri',
                'size' => 12
            ),
        );
        $text_one_two =array(
            'font'=>array(
                'bold' => true,
                'name' => 'Calibri',
                'size' => 12,
                'color' => array(
                    'argb' => '1f497d')
            ),
        );

        //information
        $information = array(
            'font'=>array(
                'bold' => false,
                'name' => 'Cambria',
                'size' => 12,
            ),
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array(
                        'rgb' => '3a2a47'
                    )
                )
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                'wrap' => TRUE
            )
        );

        //titulo
        $objPHPExcel->setActiveSheetIndex(0)->setCellValueExplicit('A1', 'REPORTE DETALLADO PLAN DE TRATAMIENTO  '.date('Y/m/d'));
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A1:H1');

        //sub titulo 1
        $objPHPExcel->setActiveSheetIndex(0)->setCellValueExplicit('A2', 'Información Clinica');
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A2:H2');

        $objPHPExcel->setActiveSheetIndex(0)->setCellValueExplicit('A3', 'Clinica');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValueExplicit('A4', 'Dirección');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValueExplicit('A5', 'Email');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValueExplicit('A6', 'usuario autor');

        $objPHPExcel->setActiveSheetIndex(0)->setCellValueExplicit('B3', $InformacionEntity->nombre);
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('B3:H3');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValueExplicit('B4', $InformacionEntity->direccion);
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('B4:H4');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValueExplicit('B5', $InformacionEntity->email);
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('B5:H5');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValueExplicit('B6', $_SESSION['usuario']);
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('B6:H6');

        //sub titulo 2
        $objPHPExcel->setActiveSheetIndex(0)->setCellValueExplicit('A7', 'Información del Paciente');
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A7:H7');


        //sub titulo 3
        $objPHPExcel->setActiveSheetIndex(0)->setCellValueExplicit('A8',  'Paciente' );
        $objPHPExcel->setActiveSheetIndex(0)->setCellValueExplicit('A9',  'Telf. Celular');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValueExplicit('A10', 'Email');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValueExplicit('A11', 'Direccion');

        $objPHPExcel->setActiveSheetIndex(0)->setCellValueExplicit('B8',  $object_a['nom'] );
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('B8:K8');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValueExplicit('B9',  $object_a['email'] );
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('B9:K9');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValueExplicit('B10', $object_a['direccion'] );
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('B10:K10');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValueExplicit('B11', $object_a['telefono_movil'] );
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('B11:K11');

        $objPHPExcel->setActiveSheetIndex(0)->setCellValueExplicit('A12',  'PLAN DE TRATAMIENTO N.'.$object_a['numero'] );
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A12:K12');

        $objPHPExcel->getActiveSheet()->getStyle('A12')->applyFromArray(array(
            'font'=> array(
                'bold'=>true,
                'size'=>12,
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
        ));

        //se aplica los estilos
        $objPHPExcel->getActiveSheet()->getStyle('A1:K1')->applyFromArray($titulo);
        $objPHPExcel->getActiveSheet()->getStyle('A2:K2')->applyFromArray($subtitulos);
        $objPHPExcel->getActiveSheet()->getStyle('A7:K7')->applyFromArray($subtitulos);

        //otros textos informativos
        $objPHPExcel->getActiveSheet()->getStyle('B3:K7')->applyFromArray($text_one);
        $objPHPExcel->getActiveSheet()->getStyle('A3:A6')->applyFromArray($text_one_two);

        $objPHPExcel->getActiveSheet()->getStyle('B8:K11')->applyFromArray($text_one);
        $objPHPExcel->getActiveSheet()->getStyle('A8:A11')->applyFromArray($text_one_two);


        //Detalles del plan de tratamiento
        $titulos_two = array(
            'PRESTACIÓN/SERVICIOS',
            'PIEZA',
            'CARAS',
            'LABORATORIO',
            'ESTADO',
            'PRECIO',
            'CANTIDAD',
            'DESC. %',
            'DESC. $ Dolares',
            'Iva calculado',
            'Sub.Total'
        );

        $colunm=0;
        foreach ($titulos_two as $value){
            $objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($colunm, 13, strtoupper(($value)));
            $colunm++;
        }
        $objPHPExcel->getActiveSheet()->getStyle('A13:K13')->applyFromArray(array(
            'font'=> array(
                'bold'=>true,
                'size'=>12,
            ),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array(
                    'argb' => 'd8d8d8')
            ),
        ));


        function caras($value){
            return $value != "false";
        }


//        echo '<pre>';print_r($object_b); die();
        $i = 14;
        foreach ($object_b as $valores){

            $caras = array();
            $caras = ((array)json_decode($valores['json_caras']));
            $caras = array_filter($caras, 'caras');

            if(count($caras)>0){
                $caras = implode(', ', array_keys($caras));
            }else{
                $caras = implode(',', $caras);
            }


            $prestacionServicio = $valores['prestacion_servicio'];
            if($valores['pieza']!=0){
//                $prestacionServicio .= "\n"."Pieza: ".$valores['pieza'];
            }
            if($caras!=""){
//                $prestacionServicio .= "\n"."Caras: ".$caras;
            }



            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue("A$i",  $prestacionServicio)
                ->setCellValue("B$i",  (($valores['pieza']==0)?"":$valores['pieza']) )
                ->setCellValue("C$i",  $caras)
                ->setCellValue("D$i",  $valores['laboratorio']) //caras selecionadas
                ->setCellValue("E$i",  $valores['estado_detalle'])
                ->setCellValue("F$i", $valores['precio'])
                ->setCellValue("G$i", $valores['cantidad'])
                ->setCellValue("H$i", $valores['descuento_porc'])
                ->setCellValue("I$i", $valores['desc_dolares'])
                ->setCellValue("J$i", $valores['iva_calculado'])
                ->setCellValue("K$i", $valores['total']);


//            echo '<pre>';
//            print_r($prestacionServicio);

            $i++;

        }

        /*
        $rows = $i;
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValueExplicit('G'.$rows, "TOTAL PRESUPUESTO")
            ->setCellValueExplicit('H'.$rows, "=SUMA(H14:H".($rows-1).")");
        */

        $objPHPExcel->getActiveSheet()->getStyle('A14:k'.($i-1))->applyFromArray($information);



        for ($i = 'A'; $i <= 'K'; $i++) {

            switch ($i){
                case 'A':
                    $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension($i)->setWidth(50);
                    break;
                case 'C':
                    $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension($i)->setWidth(50);
                    break;
                case 'B':
                    $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension($i)->setWidth(8);
                    break;
                default:
                    $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension($i)->setWidth(20);
                    break;
            }
        }

        // Se asigna el nombre a la hoja
        $objPHPExcel->getActiveSheet()->setTitle('Reporte detallado TTO.');
        // Se activa la hoja para que sea la que se muestre cuando el archivo se abre
        $objPHPExcel->setActiveSheetIndex(0);

        //paneles congelados
        $objPHPExcel->getActiveSheet(0)->freezePaneByColumnAndRow(0, 14);

        // Se manda el archivo al navegador web, con el nombre que se indica, en formato 2007
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Reporte detallado TTO.xlsx"');
        header('Cache-Control: max-age=0');


        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');


        die();

    }


}else{
    echo "<pre>"; print_r('Ha ocurrido un error obteniendo los datos');  die();
}






?>