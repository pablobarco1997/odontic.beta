<?php

class Users{

    var $id                     = "";
    var $name                   = "";
    var $id_entidad_login       = "";
    var $idPerfil               = "";
    var $admin                  = "";
    var $users_unique_id        = "";
    var $perfil_users           = "";

    var $permissions            = [];


    public function __construct($db){
        $this->users();
    }

    private function users(){

        $this->id                = $_SESSION['id_users_2'];
        $this->name              = $_SESSION['usuario'];
        $this->id_entidad_login  = $_SESSION["login_entidad"];
        $this->idPerfil          = $_SESSION["fk_perfil"];
        $this->admin             = $_SESSION["admin"];
        $this->users_unique_id   = $_SESSION["users_unique_id"];
        $this->perfil_users      = $_SESSION["perfil_users"];
    }


}

?>