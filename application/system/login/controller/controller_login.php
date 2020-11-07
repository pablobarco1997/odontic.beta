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
            $r = "";
            if(isset($_SESSION['is_open']))
            {
                $r="SesionIniciada"; //se verifica si la sesion esta iniciada
            }
            else {

                $usuario  = GETPOST('usua'); //Usuario
                $password = GETPOST('pass'); //password

                $objers    = concretar_validacion_usuario_coneccion_entidad($usuario, $password);

                $respuesta = $objers['respuesta'];  //OBTENGO LA INFORMACION DE LA ENTIDAD

                #print_r($objers); die();
                if ( $objers['error_session'] == true )
                {
                    #0 que ningun cliente no esta usando el usuaior
                    #1 que este usuario esta en session o en uso
                    if( $objers['en_session'] == 0)
                    {
                        $coneccion_entity = new ObtenerConexiondb();

                        $sql = "SELECT lg.fk_doc , lg.rowid as id_login_2 FROM tab_login_users lg , tab_odontologos o where lg.fk_doc = o.rowid and usuario = '$usuario' and passwords = md5('$password') and lg.estado = 'A' limit 1";
                        $rs  = $coneccion_entity::conectarEmpresa($respuesta['nombreDataBase'])->query($sql);

                        if ($rs->rowCount() > 0)
                        {

                            session_start();

                            $row = $rs->fetchObject();

                            $_SESSION['is_open']                = true;
                            $_SESSION['id_user']                = $row->fk_doc; #usuario de sesion es el doctor del usuario
                            $_SESSION['db_name']                = $respuesta['nombreDataBase'];
                            $_SESSION['usuario']                = $usuario;
                            $_SESSION['entidad']                = $respuesta['entity'];
                            $_SESSION['id_users_2']             = $row->id_login_2; #rowid de la data de la clinica id usuario 2


                            $_SESSION['id_Entidad']             = $respuesta['id_Entidad'];
                            $_SESSION['nombreClinica']          = $respuesta['nombreClinica'];
                            $_SESSION['direccionClinica']       = $respuesta['direccionClinica'];
                            $_SESSION['telefonoClinica']        = $respuesta['telefonoClinica'];
                            $_SESSION['celularClinica']         = $respuesta['celularClinica'];
                            $_SESSION['emailClinica']           = $respuesta['emailClinica'];
                            $_SESSION['logoClinica']            = $respuesta['logoClinica'];
                            $_SESSION['login_entidad']          = $respuesta['login_entidad'];


                            if (isset($_SESSION['db_name']) && isset($_SESSION['usuario']) && isset($_SESSION['id_user'])) {
                                $r = "SesionIniciada";
                            } else {
                                $r = "ErrorSesion";
                            }
                        }
                        else {
                            $r = "ErrorSesion";
                        }
                    }
                    else{
                        $r = "ErrorSesion";
                        $msg_error = "Este usuario ya se encuentra en session";
                    }

                }else{
                    $r = "ErrorSesion";
                }
            }

            $output = [
                'error'     => $r ,
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

    $login_entidad        = "";

    $con1 = new CONECCION_ENTIDAD(); //ME CONECTO CON LA ENTIDAD



    $sql = "SELECT rowid , nombre_user , password_user, email , nombre , apellido, id_usuario , estado, idcedula, fk_perfiles, entity, fk_entidad,  session as session_user_u  FROM tab_login_entity WHERE  to_base64(nombre_user) = to_base64(replace('$user',' ','')) and password_user = md5('$pass') and estado = 'A' ";
    $resp = $con1::CONNECT_ENTITY()->query($sql);

//    print_r($sql); die();
    if($resp)
    {
        if($resp->rowCount() == 1)
        {
            $msg_sussces = true;
            while ($row = $resp->fetchObject())
            {
                $en_session =  0;

                $sql2 = "SELECT rowid,  nombre_db_entity , numero_entity , nombre, direccion , telefono , celular , email , logo, pais , ciudad FROM tab_entidades_dental where rowid = $row->fk_entidad and numero_entity = '".$row->entity."' ;";
                $r    = $con1::CONNECT_ENTITY()->query($sql2);
                if($r)
                {
                    if($r->rowCount() == 1)
                    {
                        $fil                        = $r->fetchObject();
                        $id_Entidad                 = $fil->rowid; #id de la entidad de la empresa

                        $nombreDataBase             = $fil->nombre_db_entity;
                        $entity                     = $fil->numero_entity;
                        $nombreClinica              = $fil->nombre;
                        $direccionClinica           = $fil->direccion;
                        $telefonoClinica            = $fil->telefono;
                        $celularClinica             = $fil->celular;
                        $emailClinica               = $fil->email;
                        $logoClinica                = $fil->logo;

                        $login_entidad              = $row->rowid;  #ID LOGIN ENTIDAD

                        #Session iniciada
//                        $squpdate = "UPDATE `tab_login_entity` SET `session`= 1 WHERE `rowid`=".$row->rowid." and  entity = '".$row->entity."' and fk_entidad = ".$row->fk_entidad." ;";
//                        $con1::CONNECT_ENTITY()->query($squpdate);

                    }else{

                        $msg_sussces = false;
//                        $squpdate = "UPDATE `tab_login_entity` SET `session`= 0 WHERE `rowid`=".$row->rowid." and  entity = '".$row->entity."' and fk_entidad = ".$row->fk_entidad." ;";
//                        $con1::CONNECT_ENTITY()->query($squpdate);
                    }
                }else{

//                    $squpdate = "UPDATE `tab_login_entity` SET `session`= 0 WHERE `rowid`=".$row->rowid." and  entity = '".$row->entity."' and fk_entidad = ".$row->fk_entidad." ;";
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
    ];

    return [
        'error_session'     => $msg_sussces,
        'respuesta'         => $respto , //Array
        'en_session'        => $en_session,
    ];

}
?>