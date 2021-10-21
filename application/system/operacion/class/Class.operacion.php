<?php

class operacion{

    var $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    //guarda los ingresos de caja
    public function new_trasaccion_caja_tratamiento($datos, $name_paciente, $plantamiento, $Documento)
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
        $id_cobro_recaudado   = $datos['id_cobro_recaudado'];

        $name_servicio = $this->db->query("select descripcion as name from tab_conf_prestaciones where rowid = $fk_prestacion_servicio")->fetchObject()->name;

        $label  = "Paciente: ".$name_paciente." | Plan de tratamiento N. ".$datos['n_tratamiento']." | Prestacion/Servicio: ".$name_servicio." | Doc.".$Documento;

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
        $query .= " `label` , ";
        $query .= " `id_cobro_recaudado` ";
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
        $query .= " '$label' , ";
        $query .= " '$id_cobro_recaudado'  ";
        $query .= " )";

        $result = $this->db->query($query);

        if(!$result){
            $log->log(0, $log->error, 'Ocurrio un error con el registro. Pago de paciente '.$name_paciente. ' Plan de tratamiento N. '.$plantamiento, 'tab_ope_cajas_clinicas_det', $query);
            return -1;
        }else{
            $id = $this->db->lastInsertId("tab_ope_cajas_clinicas_det");
            $log->log($id, $log->crear, 'Se registro nuevo pago del paciente '.$name_paciente.' del Plan de tratamiento N. '.$plantamiento.' .Prestacion de Servicio '.$name_servicio.'   N.pago '.$fk_pago_cab, 'tab_ope_cajas_clinicas_det');
            return $id;
        }


    }

    public  function new_trasaccion_caja_gastos($datos){

        global $user, $log;

        $id_ope_caja        = $datos['id_ope_caja'];
        $id_categoria   = $datos['id_categoria'];
        $detalle        = $datos['detalle'];
        $monto          = $datos['monto'];
        $medio_pago_gastos    = $datos['medio_pago_gastos'];
        $id_gasto       = $datos['id_gasto'];

        $sql     = "INSERT INTO `tab_ope_cajas_det_gastos`(`id_ope_caja`,`id_categoria`,`detalle`,`monto`,`id_gasto`,`fk_medio_pago`)";
        $sql    .= " VALUES ($id_ope_caja, $id_categoria, '$detalle', $monto, $id_gasto, $medio_pago_gastos) ";
        $result  = $this->db->query($sql);
        if($result){
            return "";
        }else{
            return -1;
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
            $sql .= " `label` , ";
            $sql .= " `value` ) ";
            $sql .= " VALUES ";

            $inserts = array();
            foreach ($detalle as $k => $item){

                $datec              = $item['datec'];
                $id_cuenta          = $item['id_cuenta'];
                $id_user_author     = $item['id_user_author'];
                $tipo_mov           = $item['tipo_mov'];
                $amount_ingreso     = $item['amount_ingreso'];
                $amount_egreso      = $item['amount_egreso'];
                $id_documento       = $item['id_documento'];
                $tipo_documento     = $item['tipo_documento'];
                $fk_type_payment    = $item['fk_type_payment'];
                $table              = $item['table'];
                $label              = $item['label'];
                $value              = (!empty($item['value'])?$item['value']:0);

                $insert = "($id_last,$datec,$id_cuenta, $id_user_author,$tipo_mov,$amount_ingreso,$amount_egreso,$id_documento,'$tipo_documento',$fk_type_payment,'$table','$label',$value)";
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



    //anulacion de diario clinico detalle
    function AnulacionDiDeClinico($iddocument, $tipo_document){

        global $log;

        $query = "UPDATE `tab_ope_diario_admin_clinico_det` SET `estado`='N' WHERE `tipo_documento`='$tipo_document' and  id_documento=$iddocument  and rowid > 0;";
        $result =  $this->db->query($query);
        if($result){
            $log->log($iddocument, $log->eliminar , 'Anulación de registro Clinico', 'tab_ope_diario_admin_clinico_det', '');
        }
    }



}


?>