
<?php


if(isset($_POST['ajaxSend']) || isset($_GET['ajaxSend']))
{
    session_start();
    require_once '../../../config/lib.global.php';
    require_once DOL_DOCUMENT. '/application/config/main.php';

    $accion = GETPOST('accion');

    switch ($accion)
    {

        case "ModalPacientesRegistrados":



            $sql = "select nombre, apellido, email, sexo as genero, fk_ciudad as direccion, telefono_movil 
                        from tab_admin_pacientes where cast(tms as date) between '2020-10-01' and '2020-10-24' ";

            $output = [
                'error' => ""
            ];
            echo json_encode($output);
            break;

        case "fechPagosRecibidosMensuales":

            $data = ObtenerPagoRecibidosMensuales();

            $output = [
                'error'       => '',
                'data'        => $data,
            ];

            echo json_encode($output);
            break;

        case "consultar_accion_date":

            $date = GETPOST('date');
            $result = CargarConsultasReportes($date);

            $output = [
                'result' => $result
            ];
            echo json_encode($output);
            break;


        case "Charts_prestaciones_realizadas":

            $result = Obtener_prestaciones_realizadas();

            $output = [
                'result' => $result
            ];
            echo json_encode($output);
            break;

    }

}

function Obtener_prestaciones_realizadas(){

    global  $db;

    $prestaciones_realizadas = [];

    $query = "SELECT 
                year(b.fecha_create) as anual, 
                d.fk_prestacion, 
                c.descripcion  as label, 
                round(sum(d.total), 2) as saldo
            FROM
                
                tab_plan_tratamiento_cab  b
                    inner join 
                tab_plan_tratamiento_det  d on d.fk_plantratam_cab = b.rowid
                    inner join 
                tab_conf_prestaciones c on c.rowid = d.fk_prestacion
                
                WHERE d.estadodet = 'R'
                and year(b.fecha_create) = year(now()) 
                group by d.fk_prestacion
                order by round(sum(d.total), 2) desc
                limit 10;";

    $results = $db->query($query);
    if($results){
        if($results->rowCount()>0){
            $result_arr = $results->fetchAll(PDO::FETCH_ASSOC);
            foreach ($result_arr as $value){
                $prestaciones_realizadas[] = array('name'=>$value['label'], 'y'=>(double)$value['saldo'],
                    'saldo' => (double)$value['saldo']
                );
            }

        }
    }

    $output = [
        'data'  => $prestaciones_realizadas,
        'anual' => date('Y')
    ];

    return $output;

}

function ObtenerPagoRecibidosMensuales(){

    global $db;

    $fetch = [];

    $query = "
    SELECT 
    YEAR(pd.feche_create) AS anual,
    (MONTH(pd.feche_create) - 1) AS mes,
    ROUND(SUM(pd.amount), 2) AS totalMes
FROM
    tab_pagos_independ_pacientes_det pd
WHERE
    YEAR(pd.feche_create) IN ((YEAR(pd.feche_create) - 1) , YEAR(pd.feche_create))
GROUP BY YEAR(pd.feche_create) , MONTH(pd.feche_create)
ORDER BY YEAR(pd.feche_create) , MONTH(pd.feche_create);";
    $result = $db->query($query);

    if($result && $result->rowCount()>0){

        while ($object = $result->fetchObject()){
            $fetch[$object->anual][$object->mes] = $object->totalMes;
        }
    }


    $Mensuales = [];
    for ($i=0;$i<=11;$i++){
        $Mensuales[$i]=0;
    }

    $Array = [];
    foreach ($fetch as $k => $value){

        $dataMensual = $value;
        $data = [];
        foreach ($Mensuales as $km => $valuesM){

            if(isset($dataMensual[$km])){
                if(count($dataMensual)>0){
                    $data[] = (double)$dataMensual[$km];
                }else{
                    $data[] = 0.00;
                }
            }else{
                $data[] = 0.00;
            }
        }


        $Array[] = array('name' => 'Recaudación '.$k, 'data' => $data);

    }

    $result = $Array;

    return $result;

}

function CargarConsultasReportes($date){

    global  $db;
    $object = new stdClass();

    $arr_date   = explode('-', $date);
    $dateInicio = str_replace('/','-',$arr_date[0]);
    $dateFin    = str_replace('/','-',$arr_date[1]);

    //Pacientes Registrados depende de la fecha del filtro
    $fecha_pacientes = "  and tms between '$dateInicio' and '$dateFin'  ";
    $pacientes = $db->query("SELECT count(*) as count FROM tab_admin_pacientes WHERE estado = 'A' and rowid > 0 $fecha_pacientes")->fetchObject()->count;
    $object->n_pacientes = $pacientes;

    //planes de tratamiento activos y abonados
    $fecha_tratamiento = " and fecha_create between '$dateInicio' and '$dateFin' ";
    $planesTratamientoActivos = $db->query("select count(*) as count from tab_plan_tratamiento_cab where estados_tratamiento in('A','S','F')  $fecha_tratamiento ")->fetchObject()->count;
    $object->n_tratamientos = $planesTratamientoActivos;

    //Citas canceladas o anuladas
    $citas = $db->query("select count(*) as count from tab_pacientes_citas_det where fk_estado_paciente_cita in(9,7) and cast(fecha_cita as date) between '$dateInicio' and '$dateFin' ")->fetchObject()->count;
    $object->citas_canceladas = $citas;

    //atendido
    $atendidos = $db->query("select count(*) as count from tab_pacientes_citas_det where fk_estado_paciente_cita in(6) and cast(fecha_cita as date) between '$dateInicio' and '$dateFin' ")->fetchObject()->count;
    $object->atendidos = $atendidos;

    return $object;

}

?>