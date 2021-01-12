

<?php

if( (isset($_POST['ajaxSend']) && isset($_POST['accion'])) || (isset($_GET['ajaxSend']) && isset($_GET['accion'])) )
{
    session_start();

    include_once '../../../config/lib.global.php';
    require_once DOL_DOCUMENT .'/application/config/main.php';
    require_once DOL_DOCUMENT .'/application/system/pacientes/class/class_paciente.php';

    global $db, $conf;


    if(isset($_POST['accion']) || isset($_GET['accion']))
    {

        $accion = GETPOST("accion");

        switch ($accion)
        {

            case "list_informacion_doc":

                $data           = array();
                $idpaciente     = GETPOST('idpaciente');
                $bsq_documento  = GETPOST('bsq_documento');
                $fecha_create   = GETPOST('fecha_create');

                if(!empty($fecha_create)){
                    $dateff_ini = str_replace('/','-', explode('-',$fecha_create)[0]);
                    $dateff_fin = str_replace('/','-', explode('-',$fecha_create)[1]);
                }

                $permisoConsultar = (!PermitsModule(4,1))?" and 1<>1 ":"";

                $sql  = "SELECT rowid , nombre_documento , Descripcion, id_table_form_document, datecreate FROM tab_documentos_clinicos where id_table_form_document != '' ";
                $sql .= $permisoConsultar;

                if(!empty($fecha_create)){
                    $sql .= " and cast(datecreate as date) between '$dateff_ini' and '$dateff_fin' ";
                }
                if(!empty($bsq_documento)){
                    $sql .= " and rowid = ".$bsq_documento;
                }

                $sql .= " ";
                $result = $db->query($sql);
                if($result){
                    if($result->rowCount()>0){
                        while ($object = $result->fetchObject()){

                            if($object->id_table_form_document != null || $object->id_table_form_document != ""){
                                $rows = array();
                                $rows[] = date("Y/m/d H:m:s", strtotime($object->datecreate));
                                $rows[] = "<i class='fa fa-file-text-o'></i> &nbsp;" . $object->nombre_documento;
                                $rows[] = $object->Descripcion;
                                $rows[] = "";
                                $rows[] = "";
                                $rows['iddocument'] = $object->id_table_form_document;
                                $rows['rowid']      = $object->rowid;
                                $data[] = $rows;

                            }
                        }
                    }
                }

//                print_r($data); die();
                $output = [
                    "data" => $data,
                ];

                echo json_encode($output);
                break;


            case 'nuevo_documentos_ficha_clinica':

                $error = "";

                $idpaciente    = GETPOST('idpaciente');
                $dataPrincipal = $_POST['principal'];

                $id_docuemnt_det = 0;
                $iddocumento_det = GETPOST('iddocumentdet'); # puede ser el id de cualquier documento por medio de un tipo

                if($iddocumento_det != 0 && !empty($idpaciente))
                {
                    $eliminar = 0;
                    #Si en caso existe lo elimino y lo vuelvo a crear - eliminar
                    $sql1 = "SELECT  * FROM tab_documentos_clinicos_admin where fk_document_det = '$iddocumento_det' and fk_document_clinico = 1";
                    $rsul1  = $db->query($sql1);
                    if($rsul1->rowCount() > 0)
                    {
                        $eliminar++;
                    }

                    #busco en la tabla ficha clinico para eliminarlo
                    $sql2 = "SELECT  * FROM tab_documentos_ficha_clinica where rowid = '$iddocumento_det' ";
                    $rsul2  = $db->query($sql2);
                    if($rsul2->rowCount() > 0)
                    {
                        $eliminar++;
                    }

                    if($eliminar > 0) #Se elimina el fichero y se vuelve a crear
                    {

                        $sqldel1 = "DELETE FROM `tab_documentos_clinicos_admin` WHERE `rowid` > 0 and fk_document_det = '$iddocumento_det' and fk_paciente = '$idpaciente'";
                        $r1 = $db->query($sqldel1);

                        if($r1){
                            $sqldel2 = "DELETE FROM `tab_documentos_ficha_clinica` WHERE `rowid` = '$iddocumento_det';";
                            $db->query($sqldel2);
                        }else{
                            $error = "Ocurrio un error con clinicos_admin , consulte con soporte tecnico";
                        }


                    }
                }

//                print_r($dataPrincipal);
//                die();
                $error = create_fichaClinica( (object)$dataPrincipal );
                $id_docuemnt_det = $db->lastInsertId("tab_documentos_ficha_clinica");

                if($error == '')
                {
                    $sql = "INSERT INTO `tab_documentos_clinicos_admin` (`label`, `fk_document_clinico`, `fk_usuario_logeado`, `fk_document_det`, `fk_paciente`) ";
                    $sql .= "   VALUES (";
                    $sql .= "   ' Ficha Clinica ', ";
                    $sql .= "   1 ,";
                    $sql .= "   '" . $conf->login_id . "' ,";
                    $sql .= "   "  . $id_docuemnt_det . "  , ";
                    $sql .= "   "  . $idpaciente. "   ";
                    $sql .= "   )";
                    $rs = $db->query($sql);

                    if (!$rs){
                        $error = 'Ocurrio un error no se pudo crear el documento';
                    }
                }


//                print_r($sql); die();

                $outuput = [
                    'error' => $error
                ];

                echo json_encode($outuput);

                break;


            case 'fetch_document': #Obtengo toda la informacion guardada del documento por tipo de documento

                $error= '';
                $typedocumennt  = GETPOST('idtypodocument');
                $iddocumen      = GETPOST('iddocument');
                $data = array();

                if( !empty($typedocumennt) )
                {

                    $data = fetch_set_document($typedocumennt,  $iddocumen);

                    if(count($data)==0)
                    {
                        $error = 'Ocurrio un error no se pudo obtener la informacion de este documento';
                    }


                }else{

                    $error = "Ocurrio un error no se identifica el tipo de documento";
                }

//                print_r($data);
//                print_r($error);
//                die();

                $outuput = [

                    'error' => $error,
                    'data'  => $data,
                ];
                echo json_encode($outuput);

                break;


            case "crear_form_documento":

//                die();
                $error = "";
                $NameDocumentTable      = GETPOST("NameDocumentTable");
                $nameDocument           = GETPOST("nameDocument");
                $ElementoString         = GETPOST("ElementoString");
                $DataFormCamposTable    = GETPOST("DataFormCamposTable");
                $DescriptionForm        = GETPOST("DescripFormClinico");

//                echo '<pre>'; print_r($DescriptionForm); die();

                $nameDirectDocumento = "FormDocumentosEntity_".base64_encode($conf->EMPRESA->ID_ENTIDAD."".$conf->EMPRESA->ENTIDAD);
                if(!file_exists(DOL_DOCUMENT."/application/system/documentos_clinicos/form_documentos/".$nameDirectDocumento)){
                    if(!mkdir(DOL_DOCUMENT."/application/system/documentos_clinicos/form_documentos/".$nameDirectDocumento)){
                        $error = "Ocurrio un error con la Operación";
                    }
                }

                #die();

                if(empty($error)){

                    $Ruta2Elment    = "/application/system/documentos_clinicos/form_documentos/".$nameDirectDocumento."/".$nameDocument.".html";
                    $urlFile        = DOL_DOCUMENT."/application/system/documentos_clinicos/form_documentos/".$nameDirectDocumento."/".$nameDocument.".html";
                    $ArchivoHtml    = fopen($urlFile,'w');
                    fwrite($ArchivoHtml, $ElementoString);
                    fclose($ArchivoHtml);

                    if(file_exists($urlFile))
                    {
                        $queryInsert = "INSERT INTO `tab_documentos_clinicos` (`nombre_documento`, `Descripcion`, `element_text`, `campos_asignados`, `id_table_form_document`, `datecreate`)";
                        $queryInsert .= " VALUES ('".$NameDocumentTable."', '".$DescriptionForm."', '".$nameDocument."', '".$DataFormCamposTable."', 'form_".(strtolower($nameDocument))."' , now() )";

                        #print_r($queryInsert); die();
                        $result = $db->query($queryInsert);
                        if($result)
                        {
                            $idDocumentClinicos = $db->lastInsertId('tab_documentos_clinicos');

                        }else{
                            $error = 'Ocurrio un error con la Operación Crear Documento <b>Varifique el nombre del documento a crear</b>';
                        }

                    }else{
                        $error = 'Ocurrio un error con la Operación';
                    }

                }else{
                    $error = 'Ocurrio un error con la Operación';
                }

//                die();

                $outuput = [
                    'error' => $error,
                ];
                echo json_encode($outuput);
                break;


            case "DocumentosForm":

                $error =  "";
                $data  = array();
                $idtableDocument = GETPOST("idtableDocument");
                $iddoc           = GETPOST("iddoc");

                if(!empty($idtableDocument))
                {
                    $sql = "SELECT 
                            id_registro_form , name_documn , id_documn_clinico, date_create
                        FROM
                            tab_documentos_clinicos_data";
                    $sql .= " where  id_documn_clinico = ".$iddoc;
                    $result = $db->query($sql);
                    if($result)
                    {
                        if($result->rowCount()>0)
                        {
                            while ($object = $result->fetchObject())
                            {
                                $rows = array();
                                $rows[] = "";
                                $rows[] = "DOCUMENTO_INFO_".str_pad($object->id_registro_form, 7, "0", STR_PAD_LEFT). " ";
                                $rows[] = date("Y/m/d H:m:s", strtotime($object->date_create));
                                $rows[] = "";
                                $rows['idInfoDoc'] =  $object->id_registro_form; //id de registro documento
                                $data[] = $rows;
                            }
                        }
                    }
                }


                $outuput = [
                    'data' => $data,
                ];
                echo json_encode($outuput);
                break;

            /////////////////**************************/////////////////////////////////////////
            case "eliminar_documento_clinico":

                $error = "";
//                $idtableDocument = GETPOST("idtableDocument");
                $iddoc               = GETPOST("id");
                $table               = "";
                $FormHTMLFile        = "";

                #print_r("0011"); die();

                if(!empty($iddoc)){

                    $objectDomclinico = array();

                    $q = "select rowid, id_table_form_document, element_text from tab_documentos_clinicos where rowid = $iddoc ;";
                    $rs = $db->query($q);

                    if($rs->rowCount()==0){
                        $error = "El registro ya esta Eliminado";
                    }else{
                        $objectDomclinico = $rs->fetchObject();
                    }

                    #print_r($objectDomclinico); die();

                    if(empty($error)){

                        if($rs && $rs->rowCount() > 0){

                            $table          = $objectDomclinico->id_table_form_document;
                            $FormHTMLFile   = $objectDomclinico->element_text;

                            #print_r($objectDomclinico); die();

                            $resultR = $db->query("DELETE FROM `tab_documentos_clinicos` WHERE `rowid`='$iddoc';");
                            if($resultR){
                                if(!empty($table)){

                                    $DIRECTORIO_FORM_CLINICO    = 'FormDocumentosEntity_'.base64_encode($conf->EMPRESA->ID_ENTIDAD."".$conf->EMPRESA->ENTIDAD);
                                    $unlink                     = DOL_DOCUMENT.'/application/system/documentos_clinicos/form_documentos/'.$DIRECTORIO_FORM_CLINICO.'/'.$objectDomclinico->element_text.'.html';

                                    if(file_exists($unlink)){
                                        unlink($unlink);
                                    }

                                }else{
                                    $error = "Ocurrio un error con el parametro asignado <b> ( Operación Eliminar Documento Clinico ) </b>, consulte con soporte Tecnico";
                                }
                            }else{
                                $error = "Ocurrio un error con la Operación Eliminar, Consulte con Soporte";
                            }
                        }
                    }


                }else{
                    $error = "Ocurrio un error con el parametro asignado Operación Eliminar, Consulte con Soporte";
                }


                $outuput = [
                    "error" => $error,
                ];
                echo json_encode($outuput);
                break;

            /////////////////**************************/////////////////////////////////////////
            case "EliminarDomClinicoRegistro":

                $error = "";
                $table = base64_decode(GETPOST("idtableDocument"));

//                print_r($table); die();
                if(!empty(GETPOST("idtableDocument"))){
                    $id = GETPOST("id");
                    $result = $db->query("DELETE FROM `$table` WHERE `rowid`='$id';");

                    if(!$result)
                        $error = "Ocurrio un error con la Operación Eliminar";
                }else{
                    $error = "Ocurrio un error con la Operación Eliminar";
                }

                $outuput = [
                    "error" => $error,
                ];
                echo json_encode($outuput);
                break;


            /////////////////**************************/////////////////////////////////////////
            case "NuevoModificarDocumento":

                date_default_timezone_set('America/Guayaquil');

                $error = "";
                $table        = GETPOST("table");
                $campos       = GETPOST("campos");
                $Element      = GETPOST("Element");
                $tableData    = json_decode($Element);
                $sub          = GETPOST("sub");
                $iddocClin    = GETPOST("iddclin");

                $CamposAsoc  = array();
                $ValueAsoc   = array();

//                echo '<pre>'; print_r($Element); die();
                if(!empty($table))
                {
                    if($sub=="nuevo"){
                        $table2 = base64_decode($table);
                        $Q      = "SELECT rowid , nombre_documento FROM tab_documentos_clinicos where rowid = $iddocClin";
                        $result = $db->query($Q);
                        if($result){
                            if($result->rowCount()>0)
                            {
                                $objectdoc = $result->fetchObject();
                                if($objectdoc->rowid){

                                    $SQL  = "INSERT INTO `tab_documentos_clinicos_data` (`name_documn`, `id_documn_clinico`, `data_documn`, `fk_paciente`, `date_create`) ";
                                    $SQL .= " VALUES(";
                                    $SQL .= "'".$objectdoc->nombre_documento."'";
                                    $SQL .= ", $objectdoc->rowid";
                                    $SQL .= ",'".$Element."'";
                                    $SQL .= ",0";
                                    $SQL .= ",now()";
                                    $SQL .= ")";

                                    $resul = $db->query($SQL);
                                    if(!$resul){
                                        $error = "Ocurrio un error consulte con soporte Tecnico";
                                    }
                                }
                            }else{
                                $error = "No se encontró la Data compruebe la Información";
                            }
                        }
                        if(!$result){
                            $error = "Ocurrio un error con la Operación Nuevo Modificar Consulte con Soporte Tecnico";
                        }
                    }
                    if($sub=="modificar"){

                        $idmod  = GETPOST("idmod");
                        $result = $db->query("UPDATE `tab_documentos_clinicos_data` SET `data_documn`='".$Element."' WHERE `id_registro_form`='$idmod';");
                        if(!$result){
                            $error = "Ocurrio un error con la Operación Modificar";
                        }
                    }

                }else{
                    $error = "Ocurrio un error con la Operación Nuevo Modificar Consulte con Soporte Tecnico";
                }

//                die();

                $outuput = [
                    "error" => $error,
                ];
                echo json_encode($outuput);
                break;

            /////////////////**************************/////////////////////////////////////////
            case "fetch_viewprint":

                $error = "";
                $object = array();
                $table = json_decode(GETPOST("table"));
                $camposAsignados = json_decode(GETPOST("campos_asignados"));

                if(!empty($table)){
                    $sql = "SELECT ".$camposAsignados." FROM ".$table;
                    print_r($sql); die();
                }else{
                    $error = "Ocurrio un error con la Operación, consulte con soporte Tecnico";
                }

                $sql = "";


                $outuput = [
                    "error" => $error,
                ];
                echo json_encode($outuput);
                break;

                /////////////////**************************/////////////////////////////////////////
            case "valid_title_doc":

                $valid = 0;
                $error = "";
                $search = GETPOST("title");
                $query  = "SELECT replace(nombre_documento,' ','')  FROM tab_documentos_clinicos where replace(nombre_documento,' ','') = replace('$search',' ','')";
                $rs = $db->query($query);
                if($rs&&$rs->rowCount()>0){
                    $valid++;
                }

//                print_r($query); die();
                if($valid>0)
                    $error = "Este Documento Ya se encuentra en Uso";
                else
                    $error = "";

                $outuput = [
                    "error" => $error,
                ];
                echo json_encode($outuput);
                break;

        }
    }
}


//ID Ficha clinica document - 1
function create_fichaClinica( $dataPrincipal = array() )
{

    global $db, $conf;

    $sqlNuevoUpdate = "INSERT INTO tab_documentos_ficha_clinica(";
    $sqlNuevoUpdate .= "   nombre_apellido";
    $sqlNuevoUpdate .= " , cedula_pasaporte";
    $sqlNuevoUpdate .= " , fecha_nacimiento";
    $sqlNuevoUpdate .= " , lugar_nacimiento";
    $sqlNuevoUpdate .= " , estado_civil";
    $sqlNuevoUpdate .= " , n_hijos";
    $sqlNuevoUpdate .= " , sexo";
    $sqlNuevoUpdate .= " , edad";
    $sqlNuevoUpdate .= " , ocupacion";
    $sqlNuevoUpdate .= " , direccion_domicilio";
    $sqlNuevoUpdate .= " , emergencia_call_a";
    $sqlNuevoUpdate .= " , emergencia_telefono";
    $sqlNuevoUpdate .= " , telefono_convencional";
    $sqlNuevoUpdate .= " , operadora";
    $sqlNuevoUpdate .= " , celular";
    $sqlNuevoUpdate .= " , email";
    $sqlNuevoUpdate .= " , twiter";
    $sqlNuevoUpdate .= " , lugar_trabajo";
    $sqlNuevoUpdate .= " , telefono_trabajo";
    $sqlNuevoUpdate .= " , posee_seguro";
    $sqlNuevoUpdate .= " , motivo_consulta";
    $sqlNuevoUpdate .= " , tiene_enfermedades";
    $sqlNuevoUpdate .= " , otras_enfermedades";

    $sqlNuevoUpdate .= " , esta_algun_tratamiento_medico";
    $sqlNuevoUpdate .= " , cual_tratamiento_medico";

    $sqlNuevoUpdate .= " , tiene_problema_hemorragico";
    $sqlNuevoUpdate .= " , cual_problema_hemorragico";

    $sqlNuevoUpdate .= " , alergico_medicamento";
    $sqlNuevoUpdate .= " , cual_alergico_medicamento";

    $sqlNuevoUpdate .= " , toma_medicamento";
    $sqlNuevoUpdate .= " , cual_toma_medicamento";

    $sqlNuevoUpdate .= " , esta_embarazada";
    $sqlNuevoUpdate .= " , cual_esta_embarazada";

    $sqlNuevoUpdate .= " , enfermedades_hereditarias";
    $sqlNuevoUpdate .= " , cual_enfermedades_hereditarias";

    $sqlNuevoUpdate .= " , que_toma_ult_24horass";
    $sqlNuevoUpdate .= " , resistente_medicamento";
    $sqlNuevoUpdate .= " , hemorragia_bucales";
    $sqlNuevoUpdate .= " , complicacion_masticar";
    $sqlNuevoUpdate .= " , habitos_consume";

    $sqlNuevoUpdate .= ")";
    $sqlNuevoUpdate .= "VALUES(";

    $sqlNuevoUpdate .= "  '$dataPrincipal->doc_nombre_apellido'";
    $sqlNuevoUpdate .= ", '$dataPrincipal->doc_cedula'";
    $sqlNuevoUpdate .= ", '$dataPrincipal->doc_fecha_nc'";
    $sqlNuevoUpdate .= ", '$dataPrincipal->doc_lugar_n'";
    $sqlNuevoUpdate .= ", '$dataPrincipal->doc_estado_civil'";
    $sqlNuevoUpdate .= ", '$dataPrincipal->doc_hijos_n'";
    $sqlNuevoUpdate .= ", '".json_encode($dataPrincipal->sexo)."'";
    $sqlNuevoUpdate .= ", '$dataPrincipal->doc_edad'";
    $sqlNuevoUpdate .= ", '$dataPrincipal->doc_ocupacion'";
    $sqlNuevoUpdate .= ", '$dataPrincipal->doc_domicilio'";
    $sqlNuevoUpdate .= ", '$dataPrincipal->doc_emergencia_call_a'";
    $sqlNuevoUpdate .= ", '$dataPrincipal->doc_emergencia_telef'";
    $sqlNuevoUpdate .= ", '$dataPrincipal->doc_telef_convencional'";
    $sqlNuevoUpdate .= ", '$dataPrincipal->doc_operadora'";
    $sqlNuevoUpdate .= ", '$dataPrincipal->doc_celular'";
    $sqlNuevoUpdate .= ", '$dataPrincipal->doc_email'";
    $sqlNuevoUpdate .= ", '$dataPrincipal->doc_twiter'";
    $sqlNuevoUpdate .= ", '$dataPrincipal->doc_lugar_trabajo'";
    $sqlNuevoUpdate .= ", '$dataPrincipal->doc_telef_trabajo'";
    $sqlNuevoUpdate .= ", '$dataPrincipal->doc_q_seguro_posee'";
    $sqlNuevoUpdate .= ", '$dataPrincipal->doc_motivo_consulta'";

    $sqlNuevoUpdate .= ", '".json_encode($dataPrincipal->enfermedades)."'";
    $sqlNuevoUpdate .= ", '$dataPrincipal->doc_otras_enferm'";

    $sqlNuevoUpdate .= ", '". json_encode($dataPrincipal->segui_tratamiento) ."'";
    $sqlNuevoUpdate .= ", '$dataPrincipal->doc_tratmient_descrip'";

    $sqlNuevoUpdate .= ", '". json_encode($dataPrincipal->problemas_hemorragicos) ."'";
    $sqlNuevoUpdate .= ", '$dataPrincipal->doc_descrip_hemorragicos'";

    $sqlNuevoUpdate .= ", '". json_encode($dataPrincipal->alergico_medicamento) ."'";
    $sqlNuevoUpdate .= ", '$dataPrincipal->doc_descrip_alergia'";

    $sqlNuevoUpdate .= ", '". json_encode($dataPrincipal->toma_medicamento_frecuente) ."'";
    $sqlNuevoUpdate .= ", '$dataPrincipal->doc_descrip_medicamento'";

    $sqlNuevoUpdate .= ", '". json_encode($dataPrincipal->embarazada) ."'";
    $sqlNuevoUpdate .= ", '$dataPrincipal->doc_descrip_embarazada'";

    $sqlNuevoUpdate .= ", '". json_encode($dataPrincipal->enferm_hederitarias) ."'";
    $sqlNuevoUpdate .= ", '$dataPrincipal->doc_descript_hederitaria'";

    $sqlNuevoUpdate .= ", '$dataPrincipal->q_medicina_tomo_24h_ultima'";
    $sqlNuevoUpdate .= ", '$dataPrincipal->doc_resistente_medicamento'";

    $sqlNuevoUpdate .= ", '". json_encode($dataPrincipal->hemorragias_bocales) ."'";

    $sqlNuevoUpdate .= ", '". json_encode($dataPrincipal->complicaciones_masticar) ."'";

    $sqlNuevoUpdate .= ", '". json_encode($dataPrincipal->abitos_consume) ."'";


    $sqlNuevoUpdate .= ")";

    $rs = $db->query($sqlNuevoUpdate);

    if(!$rs){
        return 'Error no se pudo guardar el documento Ficha clinica';
    }else{
        return '';
    }

}

function fetch_set_document($tyoedocmm = "", $iddocumdet = "")
{
    global  $db , $conf;

    $dataPrincipal= array();

    //FICHA CLINICA
    if($tyoedocmm == 1)
    {
        $sql = "SELECT * FROM tab_documentos_ficha_clinica where rowid = " . $iddocumdet;
        $rs  = $db->query($sql);

        if($rs->rowCount() > 0)
        {
            while ($Obj = $rs->fetchObject())
            {
                $dataPrincipal = $Obj;
            }
        }
    }

    return $dataPrincipal;

}




?>