<?php


class log{

    var $id_users_author;

    private $db;

    public $consultar      = "CONSULTAR";
    public $crear          = "CREAR";
    public $modificar      = "MODIFICAR";
    public $eliminar       = "ELIMINAR";
    public $error          = "ERROR"; //cuando ocurre un error en la consulta
    public $CronLinux      = "TAREAS_CRON_SERVER";


    public function __construct($db, $id_users_author){
        $this->db = $db;
        $this->id_users_author = $id_users_author;
    }

    public function log($id, $tipo, $descripcion, $table, $errordb=""){

        $sql = "INSERT INTO tab_log_clinica(id, tipo, descripcion, id_users_author, `table`, error) ";
        $sql .= " VALUES(";
        $sql .= "  $id, ";
        $sql .= " '$tipo', ";
        $sql .= " '$descripcion', ";
        $sql .= "  $this->id_users_author , ";
        $sql .= " '$table' , ";
        $sql .= " '$errordb'   ";
        $sql .= " )";

        $result = $this->db->query($sql);
        if(!$result){
            return -1;
        }

    }

}



?>