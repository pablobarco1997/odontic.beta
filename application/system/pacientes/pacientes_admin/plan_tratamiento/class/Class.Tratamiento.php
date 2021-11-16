

<?php

class PlanTratamiento{

    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }


    function fetchTratamiento($detalle){

    }

    function AnularListTratamientoValid($id_pago, $paciente_id = "", $tratamiento_id, $count=false, $estado_caja='C'){

        $select = " cdd.fk_plan_tratam_cab,cdd.fk_plan_tratam_det,pdd.rowid AS idpagodet,prs.descripcion AS servicio,pdd.amount,cdd.estado 
            , pdd.feche_create as emitido , pcc.n_fact_boleta
            , cdd.fk_tipo_pago as forma_pago_id";

        $query = "
            SELECT 
              ".$select."
            FROM
                tab_ope_cajas_clinicas_det cdd
                    inner join 
                tab_plan_tratamiento_det tdd on tdd.rowid = cdd.fk_plan_tratam_det
                    inner join
                tab_plan_tratamiento_cab tcc on tcc.rowid = cdd.fk_plan_tratam_cab
                    inner join 
                tab_pagos_independ_pacientes_det pdd on pdd.fk_plantram_det = tdd.rowid and pdd.fk_plantram_cab = tdd.fk_plantratam_cab and pdd.estado = 'A'
                    inner join
                tab_pagos_independ_pacientes_cab pcc on pcc.rowid = pdd.fk_pago_cab
                    inner join
                tab_conf_prestaciones prs on prs.rowid = pdd.fk_prestacion
            WHERE
                cdd.estado = '$estado_caja' AND cdd.fk_plan_tratam_cab = ".$tratamiento_id." AND pdd.fk_pago_cab = $id_pago ";

        if(!empty($paciente_id)){
            $query .= " and pdd.fk_paciente = $paciente_id";
        }

        $query .= " group by pdd.rowid ";

//        print_r($query); die();
        $result = $this->db->query($query);
        if($result){
            $fetchData = $result->fetchAll(PDO::FETCH_ASSOC);

            //se valida las prestaciones activa
            //Del plan de tratamiento
            if($count==true){
                $cerrada=array();
                if($fetchData){
                    foreach ($fetchData as $fetch){
                        if($fetch['estado'] == 'A'){ // se valida prestaciones activa
                            $cerrada[] = $fetch;
                        }
                    }
                }
                return $cerrada;
            }else{ //desppligo el listo de las pagos que se puedes anular
                $Datos=array();
                foreach ($fetchData as $fetch){
                    if($fetch['estado'] == 'C'){ // se valida prestaciones activa
                        $Datos[] = $fetch;
                    }
                }

                return $Datos;
            }

        }else{
            return "Ocurrió un error con la Operación";
        }



    }


}


?>