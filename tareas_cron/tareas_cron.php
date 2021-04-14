
<?php

include_once '../application/config/lib.global.php';
require_once 'class_connecion_and_process.php';

//obtengos la clinicas registradas
function fetchClinicas(){

    $fetchClinicas=[];
    $dbconectar = new db_and_procesos();

    $sql = "SELECT 
            nombre_db_entity AS db_name, numero_entity AS entity
        FROM
            tab_entidades_dental;";
    $result = $dbconectar->Connection->query($sql);
    if($result && $result->rowCount()>0){
        $fetchClinicas = $result->fetchAll(PDO::FETCH_ASSOC);
    }

    return $fetchClinicas;
}

function ProccessCronSendEmail(){

    $dbconectar       = new db_and_procesos();
    $fetchClinicas    = fetchClinicas();
    $ArraySendProgram = array();

    //recorro las database existentes
    foreach ($fetchClinicas as $k => $value){
        //comprueba las bases existentes para realizar el proceso cron de los envios email
        if( $dbconectar->Connection->query("SHOW DATABASES LIKE '".$value['db_name']."' ")->rowCount() ){
            $db = $dbconectar->dbConectar($value['db_name']);//conexion

            $ArrayToFileSend=[];
            $sqlfile = "select name, name64, path_to_file, type_to_file, fk_send_email_program from tab_send_email_programa_to_file;";
            $resulltF = $db->query($sqlfile);
            if($resulltF && $resulltF->rowCount()>0){
                while ($objf = $resulltF->fetchObject()){
                    $ArrayToFileSend[$objf->fk_send_email_program] = $objf;
                }
            }

            $sql = "select 
                    rowid as idsendp,
                    date_cc, 
                    fk_paciente, 
                    destinario, 
                    asunto, 
                    message,
                    cast(date_program as date) as date_program,
                    'Programado' as status
                from tab_send_email_programa where estado='C' 
                and cast(now() as date) <= cast(date_program as date)
                order by rowid desc";
            $result = $db->query($sql);
            if($result && $result->rowCount()>0){
                while ($object =$result->fetchObject()){
                    $ArraySendProgram['clinica_'.$value['db_name']]['name_db'] = $value['db_name'];
                    $ArraySendProgram['clinica_'.$value['db_name']][$object->idsendp]['send']   = $object;
                    $ArraySendProgram['clinica_'.$value['db_name']][$object->idsendp]['tofile'] = (!empty($ArrayToFileSend[$object->idsendp]))?$ArrayToFileSend[$object->idsendp]:"";
                }
            }

        }
    }


    echo '<pre>'; print_r($ArraySendProgram);  die();
}

ProccessCronSendEmail();

?>
