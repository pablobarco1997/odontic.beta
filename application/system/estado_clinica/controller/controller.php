

<?php

if(isset($_GET['ajaxSend']) || isset($_POST['ajaxSend'])){

    session_start();
    require_once '../../../config/lib.global.php';
    require_once DOL_DOCUMENT. '/application/config/main.php'; //el main contiene la sesion iniciada
    require_once DOL_DOCUMENT. '/application/config/conneccion_entidad.php'; //Coneccion entidad

    $accion = GETPOST('accion');

    switch ($accion){


        case 'listresultadoClinica':

            $datos['emitido'] = GETPOST('emitido');

            $result = resultClinicaEstadoList($datos);

            echo json_encode(array('data' => $result));
            break;

    }



}

function resultClinicaEstadoList($datos = array(), $export = false){

    global $db;

    $consultar = "";
    $data = array();
    $where = "";

    if(!PermitsModule('Reporte estado de clinica','consultar')){
        $where = " 1<>1 ";
        $consultar = "invalic";
    }else{
        $where = " 1=1 ";
    }

    $emitido = $datos['emitido'];
    if($emitido!=""){
        $emitido = explode('-', $emitido);
        $date_one = date('Y-m-d', strtotime($emitido[0]));
        $date_two = date('Y-m-d', strtotime($emitido[1]));
        $where .= " and cast(d.datec as date) between '$date_one' and '$date_two'  ";
    }

    $array_estado = array('INGRESOS', 'COSTO','GASTOS');
    $array_resultClinica = array();

    $sql = "select 
            concat(cd.n_cuenta, ' ', cd.name_acount) as name_acount, 
            sum(round(d.value, 2)) as valor, 
            cd.tipo_operacion
        from 
            tab_ope_diario_admin_clinico_det d 
                inner join 
            (select * from tab_ope_declare_cuentas cd where cd.to_caja <> 1 ) cd on cd.rowid = d.id_cuenta
            
            where ";

    $sql .= $where;
    $sql .= " group by cd.rowid ";
    $result = $db->query($sql);
    if($result){
        if($result->rowCount()>0){

            $list = $result->fetchAll(PDO::FETCH_ASSOC);
            foreach ($list as $value){
                $array_resultClinica[$value['tipo_operacion']][] = $value;
            }
//           print_r($sql); die();
            $Totales = array('INGRESOS' => 0, 'COSTO' => 0, 'GASTOS' => 0);
            $data = array();

            if(count($array_estado)>0){

                foreach ($array_estado as $value){

                    $rows = [];
                    $rows[] = $value;
                    $rows[] = "";

                    if(!$export){
                        if($value=='INGRESOS')
                            $rows['color'] = '#d5f5e3'; //green
                        if($value=='COSTO' || $value=='GASTOS')
                            $rows['color'] = '#fadbd8'; //red
                    }

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
                    $rows['color'] = "#f4f4f4";
                }
                $data[] = $rows;

                //Totales
                foreach ($Totales as $k => $item){
                    $rows = [];
                    $rows[] = $k;
                    $rows[] = $item;
                    $data[] = $rows;
                }
                return $data;

            }else{

                return $data;
            }
        }else{
            return $data;
        }
    }else{
        return $data;
    }

}


?>