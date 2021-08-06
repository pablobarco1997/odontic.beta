<?php


require_once '../../../controllers/controller.php';
require_once '../../../config/conneccion_entidad.php';
require_once '../../../config/lib.global.php';
require_once '../../conneccion/conneccion.php';

//controllers
if(isset($_POST['ajaxSend']) || isset($_GET['ajaxSend']))
{
    $accion = GETPOST('accion');

    switch ($accion)
    {
        case 'logearse':

            $msg_error = "";
            $error = "";
            if(isset($_SESSION['is_open'])){
                $error="SesionIniciada"; //se verifica si la sesion esta iniciada
            }
            else {

                $usuario  = GETPOST('usua'); //Usuario
                $password = GETPOST('pass'); //password

                //se valida los usuario en la base principal
                $objers    = concretar_validacion_usuario_coneccion_entidad($usuario, $password);
                $respuesta = $objers['respuesta'];  //obtengo la información de la clinica logeada
                if ( $objers['error_session'] == true )
                {
                    #0 que ningun cliente no esta usando el usuaior
                    #1 que este usuario esta en session o en uso
                    if( $objers['en_session'] == 0){

                        //se verifica el login en la base de la clinica
                        $conectar_clinica = new ObtenerConexiondb();
                        $sql = "SELECT 
                            s.fk_doc, 
                            s.rowid AS id_login_2, 
                            s.login_idusers_entity, 
                            s.fk_perfil_entity as Perfil_users
                        FROM
                            tab_login_users s
                                LEFT JOIN
                            tab_odontologos d ON d.rowid = s.fk_doc
                        WHERE
                              s.estado = 'A'  
                                AND s.usuario = '$usuario'
                                    AND s.passwords = MD5('$password')";
                        $result  = $conectar_clinica::conectarEmpresa($respuesta['nombreDataBase'])->query($sql);
                        if ($result){
                            if($result->rowCount() > 0){

                                $result_abc = $result->fetchObject();
                                session_start();

                                $_SESSION['is_open']                = true;
                                $_SESSION['id_user']                = $result_abc->id_login_2||0;
                                $_SESSION['db_name']                = $respuesta['nombreDataBase'];
                                $_SESSION['usuario']                = $usuario;
                                $_SESSION['entidad']                = $respuesta['entity'];
                                $_SESSION['id_users_2']             = $result_abc->id_login_2; #rowid de la data de la clinica id usuario 2
                                $_SESSION['fk_perfil']              = $respuesta['fk_perfiles']; #El id fk_perfil  para comprobar que tipos de permisos tienen
                                $_SESSION['admin']                  = $respuesta['admin'];


                                $_SESSION['id_Entidad']             = $respuesta['id_Entidad'];
                                $_SESSION['nombreClinica']          = $respuesta['nombreClinica'];
                                $_SESSION['direccionClinica']       = $respuesta['direccionClinica'];
                                $_SESSION['telefonoClinica']        = $respuesta['telefonoClinica'];
                                $_SESSION['celularClinica']         = $respuesta['celularClinica'];
                                $_SESSION['emailClinica']           = $respuesta['emailClinica'];
                                $_SESSION['logoClinica']            = $respuesta['logoClinica'];
                                $_SESSION['login_entidad']          = $respuesta['login_entidad'];

                                //perfil users
                                $_SESSION['perfil_users']           = $result_abc->Perfil_users;
                                //unique login users
                                $_SESSION['users_unique_id']        = $result_abc->login_idusers_entity;

                                if (isset($_SESSION['db_name']) && isset($_SESSION['usuario']) && isset($_SESSION['id_user'])) {
                                    $error = "SesionIniciada";
                                } else {
                                    $error = "ErrorSesion";
                                }
                            }
                        }
                        else {
                            $error = "ErrorSesion";
                        }
                    }
                    else{
                        $error = "ErrorSesion";
                        $msg_error = "Este usuario ya se encuentra en session";
                    }

                }else{
                    $error = "ErrorSesion";
                }
            }

            $output = [
                'error'     => $error ,
                'msg_err'   => $msg_error
            ];

            echo json_encode($output);
            break;

        case 'CerraSesion':

            session_start();
            $connecion = new CONECCION_ENTIDAD();
            $iduserEntity = $_SESSION['login_entidad'];

            session_unset(); //borra los valores de las sessiones
            session_destroy(); //destrulle la session

            $redirecionar = null;
            $error = '';
            if(!isset($_SESSION['is_open']))
            {
//                header('location:'.DOL_HTTP.'/application/system/login');
//                header('location:'.'http://192.168.0.108/dental'.'/application/system/login');
                if(isset($iduserEntity)){
                    if($iduserEntity!=0){
                         $quentity = "UPDATE `tab_login_entity` SET `session` = 0 WHERE `rowid`= ".$iduserEntity."   ;";
                         $rsultentit = $connecion::CONNECT_ENTITY()->query($quentity);
                         if(!$rsultentit){
                             $error = 'Ocurrio un error con la session consulte con soporte Tecnico';
                         }
                    }
                }

                $redirecionar =  DOL_HTTP.'/application/system/login';

            }else{
                $error = 'Ocurrio un error';
            }

            echo  json_encode(array('error' => $error, 'redireccionar' => $redirecionar));

            break;
    }
}
function concretar_validacion_usuario_coneccion_entidad($user, $pass)
{
    $msg_sussces  = false; /*mensaje*/
    $en_session   = 0;

    /*informacion global*/
    $id_Entidad         = "";
    $nombreDataBase     = "";
    $entity             = "";
    $nombreClinica      = "";
    $direccionClinica   = "";
    $telefonoClinica    = "";
    $celularClinica     = "";
    $emailClinica       = "";
    $logoClinica        = "";
    $admin              = 0;
    $login_entidad      = "";
    $idPerfil           = "";

    $con1 = new CONECCION_ENTIDAD();

    $sql = "SELECT rowid , nombre_user , password_user, email , nombre , apellido, id_usuario , estado, idcedula, fk_perfiles, entity, fk_entidad,  session as session_user_u, fk_perfiles, admin  FROM tab_login_entity WHERE  to_base64(nombre_user) = to_base64(replace('$user',' ','')) and password_user = md5('$pass') and estado = 'A' ";
    $result_b = $con1::CONNECT_ENTITY()->query($sql);
    if($result_b){
        if($result_b->rowCount() == 1){
            $msg_sussces = true;
            while ($object_b = $result_b->fetchObject()){
                $en_session =  0;

                $sql_a = "SELECT rowid,  nombre_db_entity , numero_entity , nombre, direccion , telefono , celular , email , logo, pais , ciudad FROM tab_entidades_dental where rowid = $object_b->fk_entidad and numero_entity = '".$object_b->entity."' ;";
                $result_a    = $con1::CONNECT_ENTITY()->query($sql_a);
                if($result_a){
                    if($result_a->rowCount() == 1){
                        $object_a                   = $result_a->fetchObject();
                        $id_Entidad                 = $object_a->rowid; #id de la entidad de la empresa

                        $nombreDataBase             = $object_a->nombre_db_entity;
                        $entity                     = $object_a->numero_entity;
                        $nombreClinica              = $object_a->nombre;
                        $direccionClinica           = $object_a->direccion;
                        $telefonoClinica            = $object_a->telefono;
                        $celularClinica             = $object_a->celular;
                        $emailClinica               = $object_a->email;
                        $logoClinica                = $object_a->logo;
                        $admin                      = $object_b->admin;


                        $login_entidad              = $object_b->rowid;  #ID LOGIN ENTIDAD
                        $idPerfil                   = $object_b->fk_perfiles;  #id Perfil del Usuario

                        #Session iniciada
//                        $squpdate = "UPDATE `tab_login_entity` SET `session`= 1 WHERE `rowid`=".$object_b->rowid." and  entity = '".$object_b->entity."' and fk_entidad = ".$object_b->fk_entidad." ;";
//                        $con1::CONNECT_ENTITY()->query($squpdate);

                    }else{

                        $msg_sussces = false;
//                        $squpdate = "UPDATE `tab_login_entity` SET `session`= 0 WHERE `rowid`=".$object_b->rowid." and  entity = '".$object_b->entity."' and fk_entidad = ".$object_b->fk_entidad." ;";
//                        $con1::CONNECT_ENTITY()->query($squpdate);
                    }
                }else{

//                    $squpdate = "UPDATE `tab_login_entity` SET `session`= 0 WHERE `rowid`=".$object_b->rowid." and  entity = '".$object_b->entity."' and fk_entidad = ".$object_b->fk_entidad." ;";
//                    $con1::CONNECT_ENTITY()->query($squpdate);
                }
            }

        }else{
            $msg_sussces = false;
        }
    }


    $respto = [
        'mess'                  => $msg_sussces,
        'id_Entidad'            => $id_Entidad,
        'nombreDataBase'        => $nombreDataBase,
        'entity'                => $entity,
        'nombreClinica'         => $nombreClinica,
        'direccionClinica'      => $direccionClinica,
        'telefonoClinica'       => $telefonoClinica,
        'celularClinica'        => $celularClinica,
        'emailClinica'          => $emailClinica,
        'logoClinica'           => $logoClinica,
        'login_entidad'         => $login_entidad,
        'fk_perfiles'           => $idPerfil,
        'admin'                 => $admin,
    ];

    return [
        'error_session'     => $msg_sussces,
        'respuesta'         => $respto , //Array
        'en_session'        => $en_session,
    ];

}
?>