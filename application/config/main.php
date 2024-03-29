<?php


    global   $db, $conf, $user , $permisos, $dateZoneCurrent,  $messErr, $dbConectar, $log;

    /** Coneccion a la entidad de la empresa o clinica x login*/
    require_once  DOL_DOCUMENT .'/application/config/conneccion_entidad.php';

    /** class log **/
    require_once  DOL_DOCUMENT .'/application/config/class.log.php';

    /**Coneccion a la empresa o clinica dental asociada x el usuario*/
    require_once  DOL_DOCUMENT .'/application/system/conneccion/conneccion.php';

    /**User class */
    require_once  DOL_DOCUMENT .'/application/config/class.users.php';

    /** -------------------   MAIN  ----------------------------*/
    $coneccion  = new CONECCION_ENTIDAD();
    $dbConectar = $coneccion::CONNECT_ENTITY();

    /**
     * Conccion a la empresa asociada al usuario logeado
    */
    $cn = new ObtenerConexiondb();                    #Conexion global Empresa o Clinica dental
    $db = $cn::conectarEmpresa($_SESSION['db_name']); #Declara Variable Global de Coneccion


    require_once DOL_DOCUMENT  .'/application/config/configuration.php';              #inicializas la clas configuracion

    $entity   = new CONECCION_ENTIDAD();
    $conf     = new configuration();

    /** Usuario
     *  id => guarda el id del doctor que esta asociado al usuario
     *  id_entidad_login => guarda el id del login de la tabla principal global - db adminnub_schema_dental_entity_login - table : schema_dental_entity_login.tab_login_entity para poder actualizar la tabla
     *  name => nombre de usuario
     */

    /**$user     = (object)array(
        "id"                => $_SESSION['id_users_2'],
        "name"              => $_SESSION['usuario'],
        "id_entidad_login"  => $_SESSION["login_entidad"],
        "idPerfil"          => $_SESSION["fk_perfil"],
        "admin"             => $_SESSION["admin"],
        "users_unique_id"   => $_SESSION["users_unique_id"],
        "perfil_users"      => $_SESSION["perfil_users"],
    );**/
    $user = new Users($db);

    /**Mensage de error global*/
    $messErr = "Ocurrió un error con la Operación consulte con Soporte";

    /** conf **/
    $conf->Entidad    = $_SESSION['entidad'];
    $conf->db_schema  = $_SESSION['db_name'];
    $conf->login_user = $_SESSION['usuario'];
    $conf->login_id   = $_SESSION['id_user'];

    #obtengo todo un (obj) de los pacientes que tiene esta clinica
    //    $conf->ObtenerPaciente($db, null, false);

    $conf->EMPRESA =    (object)array(
        "ID_ENTIDAD"    => $_SESSION["id_Entidad"],
        "ENTIDAD"       => $conf->Entidad,
        "SCHEMA"        => $conf->db_schema,
        "INFORMACION"   => $entity::INFORMACION_EMPRESA_GLOB($_SESSION["id_Entidad"], $dbConectar) #INFORMACION DE LA ENTIDAD GLOB
    );


    /** log de la clinica**/
    $log = new log($db, $user->id);

    /**ruta de la carpeta donde se guardan ficheros de la clinicla - global del directorio x defaul del sistema*/
    $conf->DIRECTORIO = DOL_DOCUMENT.'/logos_icon/icon_logos_'.$_SESSION['entidad']; #url de los ficheros que upload clinica dental especifica


    /** Nombre de la carpeta donde se guarda los fiucheros x clinica*/
    $conf->NAME_DIRECTORIO = 'icon_logos_'.$_SESSION['entidad']; //NAME DE LA CARPETA DEL DIRECTORIO


    $conf->perfil($db, $user->id, DOL_HTTP, $conf->NAME_DIRECTORIO );

    /** Se asocia el controller del sistema*/
    require_once  DOL_DOCUMENT .'/application/controllers/controller.php';

    $NavSearchPacientes = NavSearchPacientes();
    date_default_timezone_set('America/Guayaquil');

    //echo '<pre>'; print_r($ServerWeb); die();
    //echo phpversion();

?>