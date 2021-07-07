
<?php


    class OperacionCuentasDeclare{

        private $db;

        var $n_cuenta;
        var $name_cuenta;
        var $description;
        var $tipo_ope;
        var $to_banco=0;
        var $to_caja=0;
        var $to_bancoCaja=0;
        var $saldo_current=0;
        var $direccionCaja="";

        public function __construct($db)
        {
            $this->db =  $db;
        }

        public function addAcount(){

            $query  = "INSERT INTO `tab_ope_declare_cuentas` (`n_cuenta`,`name_acount`,`description`,`tipo_operacion`,`to_banco`,`to_caja`, `to_banco_caja`, `date_c`,`estado`,`saldo_current`, `to_caja_direccion`)";
            $query .= "VALUES(";
            $query .= "'".$this->n_cuenta."', ";
            $query .= "'".$this->name_cuenta."', ";
            $query .= "'".$this->description."', ";
            $query .= "'".$this->tipo_ope."', ";
            $query .= "'".$this->to_banco."', ";
            $query .= "'".$this->to_caja."', ";
            $query .= "'".$this->to_bancoCaja."', ";
            $query .= " now() , ";
            $query .= "  'A' , ";
            $query .= "  round(".$this->saldo_current.",2) ,";
            $query .= "'".$this->direccionCaja."' ";
            $query .= ")";

            $result = $this->db->query($query);
            if(!$result){
                return -1;
            }else{
                $lastId = $this->db->lastInsertId('tab_ope_declare_cuentas');
                return $lastId;
            }

        }



    }


?>