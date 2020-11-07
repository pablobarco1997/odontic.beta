
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

            $total_anual = $db->query("SELECT  IFNULL(ROUND(SUM(pd.amount), 2),0) AS totalMes FROM tab_pagos_independ_pacientes_det pd where YEAR(pd.feche_create) = ".$year." limit 1")->fetchObject()->totalMes;

            $Meses = [1,2,3,4,5,6,7,8,9,10,11,12];

            $arr = ObtenerPagoRecibidosMensuales($Meses, $year,$mes);

            $output = [
                'error'      => '',
                'err'        => $arr,
                'totalAnual' => $total_anual
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

?>