
<?php



class transsacion{

    /**
     * Type Movimiento
     * caja = 1 (CAJA)
     *
    */

    var $userAuthor = 0;
    var $db;
    var $type_mov = ""; //tipo de movimiento
    var $type_operacion = 0;

    public function __construct($db){
        $this->db = $db;
    }

    function crear_caja($nom, $total){ #se crea la cuenta en tab_cuentas_add para realizar transacciones

        $sql  = " INSERT INTO `tab_cuentas_add`(`Descripcion`, `total`, `estado`, `sistema`, `datecc`) ";
        $sql .= " VALUES(";
        $sql .= "'".$nom."',";
        $sql .= " round(".$total.",2), ";
        $sql .= " 'A' ,";
        $sql .= " 0 ,";
        $sql .= " now() ";
        $sql .= ")";

        $result = $this->db->query($sql);
        if($result){

            $id_account = $this->db->lastInsertId("tab_cuentas_add");

            return ((int)$id_account);

        }else{

            return 'Ocurrió un error con la Operación';
        }

    }

    function error_caja_status($idcaja){//si en caso hay un error se cambia de estado a E (eliminado )

        $this->db->query("UPDATE tab_cajas_clinicas SET estado='E' WHERE rowid=$idcaja");
    }

    function type_movimiento(){// sirve para identificar que tipo de movimiento es

        if($this->type_mov==1){ //si es caso es un ingreso de caja o egreso
            return "CAJA";
        }
        if($this->type_mov==2){ // si en caso es un ingreso
            return "COBOR_PLAN_DE_TRATAMIENTO";
        }
        if($this->type_mov==3){ // si en caso es un egreso pago eliminado de plan de tratamiento
            return "COBRO_ELIMINADO_PLAN_DE_TRATAMIENTO";
        }
    }

    function _operacion_(){
        /**
        1    pagos
        2    cobros
        3    egreso
        4    ingreso
        5    transferencia
        6    depositos
        7    anticipos
         **/
    }

    function total_mov_bank($id_account, $dateIni="", $dateFin=""){//fetch el total por medio de la id cuenta registrada (Cuentas regsitradas)

        $qu = "select  round(sum(b.value),2) as total from tab_bank_transacciones b where b.id_account = $id_account";

        $result = $this->db->query($qu);
        if($result && $result->rowCount()>0){
            return $result->fetchObject()->total;
        }else{
            return 0;
        }

    }

    function create_movimiento_bank($id_account,$id_document,$value,$comment,$table="",$log_tmp=""){

        if($this->type_mov!=""){

            $query  = " INSERT INTO `tab_bank_transacciones` ";
            $query .= " (`date_time`, `id_account`, `id_document`, `type_movement` , `value`, `comment`, `users_author`, `log_tmp`, `table`, `operacion`) ";
            $query .= " VALUES( ";
            $query .= " now() ,";
            $query .= " $id_account ,";
            $query .= " $id_document ,";
            $query .= " '".$this->type_movimiento()."' ,";
            $query .= " round($value,2) ,";
            $query .= " '".$comment."' ,";
            $query .= " $this->userAuthor ,";
            $query .= " '".$log_tmp."',";
            $query .= " '".$table."' ,  ";
            $query .= " ".$this->type_operacion."  ";
            $query .= ")";

//            echo '<pre>'; print_r($query); die();

            $result = $this->db->query($query);
            if(!$result){
                return 'Ocurrió un error con la Operación bank Movimiento';
            }else{

                $this->db->query("UPDATE tab_cuentas_add SET total=round(".(($this->total_mov_bank($id_account)))." ,2) WHERE rowid=$id_account;");
                return "";
            }
        }

    }


}


?>