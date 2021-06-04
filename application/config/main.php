<?php


    global   $db, $conf, $user , $permisos, $dateZoneCurrent,  $messErr, $dbConectar;

    /** Coneccion a la entidad de la empresa o clinica x login*/
    require_once  DOL_DOCUMENT .'/application/config/conneccion_entidad.php';

    /**Coneccion a la empresa o clinica dental asociada x el usuario*/
    require_once  DOL_DOCUMENT .'/application/system/conneccion/conneccion.php';


    /** -------------------   MAIN  ----------------------------*/

    /** Informacion del servidor - priviligios o credenciales del servidor - REMOTO or LOCAL */
    #INFORMACION JSON PRIVILEGIOS SERVER
    $Server = json_decode( file_get_contents( DOL_DOCUMENT.'/application/config/privileges.json') , true );


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
    $permisos = (object)[];#Obtiene los permisos asignados

    /** Usuario
     *  id => guarda el id del doctor que esta asociado al usuario
     *  id_entidad_login => guarda el id del login de la tabla principal global - db adminnub_schema_dental_entity_login - table : schema_dental_entity_login.tab_login_entity para poder actualizar la tabla
     *  name => nombre de usuario
     */
    $user     = (object)array
    (   "id"                => $_SESSION['id_users_2'],
        "name"              => $_SESSION['usuario'],
        "id_entidad_login"  => $_SESSION["login_entidad"],
        "idPerfil"          => $_SESSION["fk_perfil"],
        "admin"             => $_SESSION["admin"],
    );

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

//    $conf->ObtnerNoficaciones($db, false);

    /**ruta de la carpeta donde se guardan ficheros de la clinicla - global del directorio x defaul del sistema*/
    $conf->DIRECTORIO = DOL_DOCUMENT.'/logos_icon/icon_logos_'.$_SESSION['entidad']; #url de los ficheros que upload clinica dental especifica


    /** Nombre de la carpeta donde se guarda los fiucheros x clinica*/
    $conf->NAME_DIRECTORIO = 'icon_logos_'.$_SESSION['entidad']; //NAME DE LA CARPETA DEL DIRECTORIO


    $conf->perfil($db, $user->id, DOL_HTTP, $conf->NAME_DIRECTORIO );

    /** Se asocia el controller del sistema*/
    require_once  DOL_DOCUMENT .'/application/controllers/controller.php';

    /** VARIABLE GLOBAL DE DATETIME MYSQL*/
    $sqlCurrentDatezone = "SELECT NOW() datezpnecurrent;";
    $dateZoneCurrent    = $db->query($sqlCurrentDatezone)->fetchObject()->datezpnecurrent;

    $NavSearchPacientes = NavSearchPacientes();

    /** PERMISOS DE USUARIO
     * obtengo los permisos de usuario asociados con el usuario
     */

    /*
    $sqllogin = "SELECT permisos FROM tab_login_users WHERE usuario = '$user->name' and fk_doc = '$user->id' limit 1";
    $login  = $db->query($sqllogin)->fetchObject();

    $objPermisos = json_decode($login->permisos);

    #Tipo de Usuario
    # 1 = Super Administrador
    # 2 = Usuario normal

    $permisos         = (object)array(
        "consultar"   => ($objPermisos->consultar  == "true") ? ""   : "disabled_link3" ,
        "agregar"     => ($objPermisos->agregar    == "true") ? ""   : "disabled_link3" ,
        "modificar"   => ($objPermisos->modificar  == "true") ? ""   : "disabled_link3" ,
        "eliminar"    => ($objPermisos->eliminar   == "true") ? ""   : "disabled_link3" ,
    ); */


    #echo '<pre>'; print_r($conf); die();

    #echo phpversion();

?>