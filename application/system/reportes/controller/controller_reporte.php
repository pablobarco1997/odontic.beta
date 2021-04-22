
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

            $year = GETPOST("year");
            $mes  = GETPOST("mes");

            $quAnual = "SELECT  IFNULL(ROUND(SUM(pd.amount), 2),0) AS total_anual FROM tab_pagos_independ_pacientes_det pd where YEAR(pd.feche_create) = ".$year." limit 1";
            $total_anual = $db->query($quAnual)->fetchObject()->total_anual;

            $Meses = [1,2,3,4,5,6,7,8,9,10,11,12];

            $arr = ObtenerPagoRecibidosMensuales($Meses, $year,$mes);

            $output = [
                'error'      => '',
                'err'        => $arr,
                'totalAnual' => $total_anual
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

    }

}

function ObtenerPagoRecibidosMensuales($arr_mens = array(), $year = "", $mes=""){

    global $db;

    $dataMensuales = array();

    foreach ($arr_mens as $value){

        if(!empty($mes)){
            if($mes==$value){
                $sql = "SELECT 
                    MONTH(pd.feche_create) AS mes, ROUND(SUM(pd.amount), 2) AS totalMes
                FROM
                    tab_pagos_independ_pacientes_det pd
                WHERE
                    MONTH(pd.feche_create) = ".$value."
                ";
                if($year!="")
                    $sql .= " and year(pd.feche_create) = ".$year;

                $sql .= " GROUP BY MONTH(pd.feche_create)";
                $result = $db->query($sql);
                if($result&&$result->rowCount()>0){
                    while ($object = $result->fetchObject()){
                        $dataMensuales[] = (double)$object->totalMes;
                    }
                }else{
                    $dataMensuales[] = 0.00;
                }
            }
        }
        else{
            $sql = "SELECT 
                    MONTH(pd.feche_create) AS mes, ROUND(SUM(pd.amount), 2) AS totalMes
                FROM
                    tab_pagos_independ_pacientes_det pd
                WHERE
                    MONTH(pd.feche_create) = ".$value."
                ";
            if($year!="")
                $sql .= " and year(pd.feche_create) = ".$year;

            $sql .= " GROUP BY MONTH(pd.feche_create)";
            $result = $db->query($sql);
            if($result&&$result->rowCount()>0){
                while ($object = $result->fetchObject()){
                    $dataMensuales[] = (double)$object->totalMes;
                }
            }else{
                $dataMensuales[] = 0.00;
            }
        }


    }

    return $dataMensuales;

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
    $planesTratamientoActivos = $db->query("select count(*) as count from tab_plan_tratamiento_cab where estados_tratamiento in('A','S')  $fecha_tratamiento ")->fetchObject()->count;
    $object->n_tratamientos = $planesTratamientoActivos;

    //Citas canceladas o anuladas
    $citas = $db->query("select count(*) as count from tab_pacientes_citas_det where fk_estado_paciente_cita in(9) and cast(fecha_cita as date) between '$dateInicio' and '$dateFin' ")->fetchObject()->count;
    $object->citas_canceladas = $citas;

    //atendido
    $atendidos = $db->query("select count(*) as count from tab_pacientes_citas_det where fk_estado_paciente_cita in(6) and cast(fecha_cita as date) between '$dateInicio' and '$dateFin' ")->fetchObject()->count;
    $object->atendidos = $atendidos;

    return $object;

}

?>