<?php


if(isset($_GET['ajaxSend']) || isset($_POST['ajaxSend']))
{

    session_start();

    require_once '../../../config/lib.global.php';
    require_once DOL_DOCUMENT. '/application/config/main.php';

    global  $db , $conf, $user;

    $accion = GETPOST('accion');

    switch ($accion)
    {
        case 'nueva_caja':

            $error = '';
            $nom_caja  = GETPOST('nam_caja');
            $desc_caja = GETPOST('direccion_caja');
            $sald_ini  = GETPOST('saldo_ini_caja');

            $error = insertarCajas(('CAJA_'.$nom_caja),$desc_caja,$sald_ini);

            $output = [
                'error' => $error
            ];

            echo json_encode($output);
            break;

        case "listCajas":


            $data = array();

            $saldo = "round((SELECT sum(t.value) FROM tab_bank_transacciones t where t.id_account = b.id_account),2) as saldo_caja,";

            $sql = "SELECT 
                    b.rowid, 
                    (SELECT u.usuario FROM tab_login_users u WHERE u.rowid = b.userAuthor) user,
                    b.name,
                    b.direccion, 
                    $saldo
                    case 
                      when b.estado = 'A' then 'Activo'
                      when b.estado = 'E' then 'Eliminado'
                      when b.estado = 'C' then 'Cerrado'
                      else ''
                    end as estado,
                    b.estado as estado_id
                       
                FROM
                    tab_cajas_clinicas b";
            $sql .= " order by b.rowid desc";
            $result = $db->query($sql);
            if($result){

                while ($obj = $result->fetchObject()){

                    $row    = array();

                    $row[]  = "";
                    $row[]  = $obj->user;
                    $row[]  = "Caja #".$obj->rowid."<br><a href='#'><small>".str_replace('CAJA_','',$obj->name)."</small></a>";
                    $row[]  = $obj->direccion;
                    $row[]  = $obj->saldo_caja;

                    if($obj->estado_id=='A')
                        $row[]  = "<label class=\"label\" style=\"background-color: #D5F5E3; color: green; font-weight: bolder\">ACTIVO</label>";
                    if($obj->estado_id=='E')
                        $row[]  = "<label class=\"label \" style=\"background-color: #FADBD8; color: red; font-weight: bolder\">INACTIVO</label>";

                    $row['estado_id'] = $obj->estado_id;
                    $row['id_caja'] = $obj->rowid;
                    $data[] = $row;
                }
            }

            $output = [
                'data' => $data
            ];

            echo json_encode($output);
            break;

        case 'update_stado_caja':

            $error = '';

            $stado = GETPOST("estado");
            $caja_id = GETPOST("idcaja");

            $sql = "UPDATE `tab_cajas_clinicas` SET `estado`='".$stado."' WHERE `rowid`= $caja_id ;";
            $result = $db->query($sql);
            if(!$result){
                $error = "Ocurrio un error";
            }

            $output = [
                'error' => $error
            ];

            echo json_encode($output);
            break;


        case 'fetch_caja_information':

            $error = '';
            $id = GETPOST("id");

            $fetchColumnData = array();
            $fetchColumnDataMov = array();

            $sql = "select *, 
                        (select round(sum(b.value),2) from tab_bank_transacciones b where b.id_account = c.id_account) as total_caja , 
                        ifnull((select round(sum(b.value),2) from tab_bank_transacciones b where b.id_account = c.id_account and cast(b.date_time as date) = cast(now() as date)),0) as saldo_actual, 
                        ifnull((select round(sum(b.value),2) from tab_bank_transacciones b where b.id_account = c.id_account and cast(b.date_time as date) < cast(now() as date)),0) as saldo_anterior
	                from tab_cajas_clinicas c where rowid = ".$id;
            $fetchColumnData = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC)[0];


            $sql = "select * from tab_bank_transacciones where id_account = ".$fetchColumnData['id_account'];
            $fetchColumnDataMov = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);

            if(count($fetchColumnData)==0 && count($fetchColumnDataMov)==0){
                $error = 'No hay data';
            }

            $output = [
                'error' => $error,
                'fetchColumnData' => $fetchColumnData,
                'fetchColumnDataMov' => $fetchColumnDataMov
            ];

            echo json_encode($output);
            break;

        case 'transaciones_caja';

            $data = array();
            $idCaja = GETPOST("idCaja");

            $id_account = $db->query("select id_account from tab_cajas_clinicas where rowid = $idCaja limit 1")->fetchObject()->id_account;

            $sql = "select 
                         cast(t.date_time as date) dateff,
                         t.comment , 
                         (select b.nom from tab_bank_operacion b where b.rowid = t.operacion) as operacion,
                         t.value
                    from tab_bank_transacciones t where t.id_account = $id_account";

            $result = $db->query($sql);
            if($result && $result->rowCount()>0){
                while ($obj =$result->fetchObject()){
                    $row = array();
                    $row[] = $obj->dateff;
                    $row[] = $obj->comment;
                    $row[] = $obj->operacion;
                    $row[] = "<span data-value='$obj->value' style='color: green' > $ $obj->value</span>";
                    $data[] = $row;
                }
            }

            $output = [
                'data' => $data
            ];

            echo json_encode($output);
            break;

    }


}

function insertarCajas($nom_caja,$desc_caja,$sald_ini=0){

    global  $db , $conf, $user;

    require_once DOL_DOCUMENT. '/application/system/cajas/class_transacciones/class_transsacion.php';

    if($nom_caja!=""){

        $q = "INSERT INTO `tab_cajas_clinicas` (`name`, `direccion`, `estado` ,`datecc`, `id_account`, `userAuthor` )
            VALUES ('".((!empty($nom_caja)?$nom_caja:""))."', '".((!empty($desc_caja)?$desc_caja:""))."',  'A', now(), 0, $user->id);";
        $result = $db->query($q);

//        print_r($q); die();
        if($result){

            $idcaja = $db->lastInsertId("tab_cajas_clinicas");

            $transacciones = new transsacion($db);
            $id_account  = $transacciones->crear_caja($nom_caja, $sald_ini);

            if(is_int($id_account)){//retorna id_account id de la cuenta

                $resultCaja = $db->query("UPDATE tab_cajas_clinicas SET id_account=$id_account WHERE rowid=$idcaja");

                if(!$resultCaja){

                    $transacciones->error_caja_status($idcaja);
                    return 'Ocurrió un error con la Operación';
                }else{

                    if((double)$sald_ini>0){//si es primer transaccion tiene que ser positivo

                        $comment = "Registro de la Primera transacion de Caja";
                        $transacciones->type_mov = 1; // 1 es CAJA
                        $transacciones->userAuthor = $user->id;

                        $error = $transacciones->create_movimiento_bank($id_account,$idcaja,$sald_ini,$comment,"tab_cajas_clinicas", "Registro de Caja");
                        return $error;

                    }else{

                        return '';
                    }
                }

            }else{

                $transacciones->error_caja_status($idcaja);
                return 'Ocurrió un error con la Operación ';
            }

        }else{
            return 'Ocurrió un error con la Operación';
        }

    }else{

        return 'No se detecto parametros agregados';
    }

}

?>