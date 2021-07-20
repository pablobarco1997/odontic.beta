<?php

class operacion{

    var $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    //guarda los ingresos de caja
    public function new_trasaccion_caja($datos, $name_paciente, $plantamiento, $Documento)
    {
        global $user, $log;

        //estado de caja detalle
        // A activo
        // C cerrada
        // E eliminada

        $fk_pago_cab        = $datos['id_pago'];
        $fk_plan_tratam_cab = $datos['plan_tram_cab'];
        $fk_plan_tratam_det = $datos['plan_tram_det'];
        $fk_paciente        = $datos['id_paciente'];
        $fk_prestacion_servicio = $datos['prestacion_servicio'];
        $fk_tipo_pago           = $datos['fk_tipo_pago'];
        $amount              = $datos['amount'];
        $id_ope_caja_cab     = $datos['id_ope_caja_cab'];
        $date_apertura       = $datos['date_apertura'];
        $date_cierre         = $datos['date_cierre'];
        $estado              = $datos['estado'];
        $to_plantram         = 1;
        $to_transaccion_caja = 0;

        $name_servicio = $this->db->query("select descripcion as name from tab_conf_prestaciones where rowid = $fk_prestacion_servicio")->fetchObject()->name;

        $label  = "Pago de Paciente: $name_paciente - Plan de tratamiento N.".$datos['n_tratamiento']." - Prestacion de Servicio: ".$name_servicio." - #Documento: ".$Documento;

        $query  = "INSERT INTO tab_ope_cajas_clinicas_det(";
        $query .= " `datecc`, ";
        $query .= " `fk_pago_cab`, ";
        $query .= " `fk_plan_tratam_cab`, ";
        $query .= " `fk_plan_tratam_det`, ";
        $query .= " `fk_paciente`, ";
        $query .= " `fk_prestacion_servicio`, ";
        $query .= " `fk_tipo_pago`, ";
        $query .= " `amount`, ";
        $query .= " `id_ope_caja_cab`, ";
        $query .= " `date_apertura`, ";
//        $query .= " `date_cierre`, ";
        $query .= " `estado`, ";
        $query .= " `user_author`, ";
        $query .= " `to_plantram`, ";
        $query .= " `to_transaccion_caja`, ";
        $query .= " `label` ";
        $query .= ")";
        $query .= " VALUES(";
        $query .= " now() , ";
        $query .= " $fk_pago_cab , ";
        $query .= " $fk_plan_tratam_cab , ";
        $query .= " $fk_plan_tratam_det , ";
        $query .= " $fk_paciente , ";
        $query .= " $fk_prestacion_servicio , ";
        $query .= " $fk_tipo_pago , ";
        $query .= " $amount , ";
        $query .= " $id_ope_caja_cab , ";
        $query .= " '$date_apertura' , ";
//        $query .= " '$date_cierre' , ";
        $query .= " 'A' , ";
        $query .= " $user->id , ";
        $query .= " $to_plantram , ";
        $query .= " 0 , ";
        $query .= " '$label'  ";
        $query .= " )";

        $result = $this->db->query($query);
        if(!$result){
            $log->log(0, $log->error, 'Ocurrio un error con el registro. Pago de paciente '.$name_paciente. ' Plan de tratamiento N. '.$plantamiento, 'tab_ope_cajas_clinicas_det', $query);
            return -1;
        }else{
            $id = $this->db->lastInsertId("tab_ope_cajas_clinicas_det");
            $log->log($id, $log->crear, 'Se registro nuevo pago del paciente '.$name_paciente.' del Plan de tratamiento N. '.$plantamiento.' .Prestacion de Servicio '.$name_servicio.'   N.pago '.$fk_pago_cab, 'tab_ope_cajas_clinicas_det');
        }


    }


    //guarda todo tipo de operacion ingreso o egreso de direntes cuentas parecido a un libro diario
    function diarioClinico($datos){

        global $user, $log;

        $label          = $datos['label'];
        $date_c         = $datos['date_c'];
        $user_author    = $user->id;

        $detalle        = $datos['detalle'];

        $sql_a  = "INSERT INTO `tab_ope_diario_admin_clinico_cab` ";
        $sql_a .= "(`date_c`,";
        $sql_a .= " `label`,";
        $sql_a .= " `id_user_author` )";
        $sql_a .= " VALUES ( ";
        $sql_a .= " $date_c ,";
        $sql_a .= " '$label' ,";
        $sql_a .= " $user_author ";
        $sql_a .= " ) ";

        $result = $this->db->query($sql_a);
        if($result){
            $id_last = $this->db->lastInsertId('tab_ope_diario_admin_clinico_cab');
            $log->log($id_last, $log->crear, "Se ha creado un registro ".$label, "tab_ope_diario_administratico_clinico_cab");

            $sql = "INSERT INTO `tab_ope_diario_admin_clinico_det`(";
            $sql .= " `id_diario_admin_cab`, ";
            $sql .= " `datec`, ";
            $sql .= " `id_cuenta`,";
            $sql .= " `id_user_athor`,";
            $sql .= " `tipo_mov`,";
            $sql .= " `amount_ingreso`,";
            $sql .= " `amount_egreso`, ";
            $sql .= " `id_documento`,";
            $sql .= " `tipo_documento`,";
            $sql .= " `fk_type_payment`,";
            $sql .= " `table`, ";
            $sql .= " `label`) ";
            $sql .= " VALUES ";

            $inserts = array();
            foreach ($detalle as $k => $value){

                $datec              = $value['datec'];
                $id_cuenta          = $value['id_cuenta'];
                $id_user_author     = $value['id_user_author'];
                $tipo_mov           = $value['tipo_mov'];
                $amount_ingreso     = $value['amount_ingreso'];
                $amount_egreso      = $value['amount_egreso'];
                $id_documento       = $value['id_documento'];
                $tipo_documento     = $value['tipo_documento'];
                $fk_type_payment    = $value['fk_type_payment'];
                $table              = $value['table'];
                $label              = $value['label'];

                $insert = "($id_last,$datec,$id_cuenta, $id_user_author,$tipo_mov,$amount_ingreso,$amount_egreso,$id_documento,'$tipo_documento',$fk_type_payment,'$table','$label')";
                $inserts[] = $insert;
            }

            $sql .= implode(',', $inserts);

//            print_r($sql); die();

            if(count($inserts)>0){
                $result_a = $this->db->query($sql);
                if($result_a){
                    $log->log($id_last, $log->crear, "Se ha actualizo los detalle #transaccion id_cab: ".$id_last, "tab_ope_diario_admin_clinico_det");
                }else{
                    $log->log($id_last, $log->error, "Ocurrio un error con la Operación agregar  detalles #transaccion id_cab: ".$id_last, "tab_ope_diario_admin_clinico_det", $sql);
                    return -1;
                }
            }

        }else{
            $log->log(0, $log->error, "Ocurrio un error con al creaccion de un registro ".$label, "tab_ope_diario_administratico_clinico_cab", $sql_a);
            return -1;
        }

    }




}


?>