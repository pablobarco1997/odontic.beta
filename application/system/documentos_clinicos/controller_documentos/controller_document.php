

<?php

if( (isset($_POST['ajaxSend']) && isset($_POST['accion'])) || (isset($_GET['ajaxSend']) && isset($_GET['accion'])) )
{
    session_start();

    include_once '../../../config/lib.global.php';
    require_once DOL_DOCUMENT .'/application/config/main.php';
    require_once DOL_DOCUMENT .'/application/system/pacientes/class/class_paciente.php';

    global $db, $conf, $messErr, $log;


    if(isset($_POST['accion']) || isset($_GET['accion']))
    {

        $accion = GETPOST("accion");

        switch ($accion)
        {

            case "list_informacion_doc":

                if(!PermitsModule('Documentos clinicos', 'consultar')){
                    $permits = " and 1<>1";
                }else{
                    $permits = " and 1=1";
                }

                $data           = array();
                $idpaciente     = GETPOST('idpaciente');
                $bsq_documento  = GETPOST('bsq_documento');
                $fecha_create   = GETPOST('fecha_create');

                if(!empty($fecha_create)){
                    $dateff_ini = str_replace('/','-', explode('-',$fecha_create)[0]);
                    $dateff_fin = str_replace('/','-', explode('-',$fecha_create)[1]);
                }

                $sql  = "SELECT rowid , nombre_documento , Descripcion, id_table_form_document, datecreate FROM tab_documentos_clinicos where id_table_form_document != '' ";
                $sql .= $permits;

                if(!empty($fecha_create)){
                    $sql .= " and cast(datecreate as date) between '$dateff_ini' and '$dateff_fin' ";
                }
                if(!empty($bsq_documento)){
                    $sql .= " and rowid = ".$bsq_documento;
                }

                $result = $db->query($sql);
                if($result){
                    if($result->rowCount()>0){
                        while ($object = $result->fetchObject()){

                            if($object->id_table_form_document != null || $object->id_table_form_document != ""){
                                $rows = array();
                                $rows[] = date("Y/m/d", strtotime($object->datecreate));
                                $rows[] = $object->nombre_documento;
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

                if(!PermitsModule('Documentos clinicos', 'consultar')){
                    $primetrs = " and 1<>1 ";
                }else{
                    $primetrs = " and 1=1 ";
                }

                $error =  "";
                $data  = array();
                $total = 0;
                $idtableDocument = GETPOST("idtableDocument");
                $iddoc           = GETPOST("iddoc");
                $paciente_doccum = GETPOST("paciente_doccum");
                $emitido         = GETPOST("emitido");
                $numero          = GETPOST("numero");

                if(!empty($idtableDocument)){

                    $start  = $_POST['start'];
                    $length = $_POST['length'];

                    $sql = "SELECT 
                                td.id_registro_form,
                                td.name_documn,
                                td.id_documn_clinico,
                                td.date_create, 
                                concat(p.nombre,' ', p.apellido) as paciente
                            FROM
                                tab_documentos_clinicos_data td
                                    LEFT JOIN
                                tab_admin_pacientes p ON p.rowid = td.fk_paciente";
                    $sql .= " where  id_documn_clinico = ".$iddoc;
                    $sql .= $primetrs;

                    if($paciente_doccum!="")
                        $sql .= " and p.rowid = ".$paciente_doccum;

                    if($emitido!=""){
                        $emitido = explode('-', $emitido);
                        $emitido1 = date('Y-m-d', strtotime(str_replace('/','-', $emitido[0])));
                        $emitido2 = date('Y-m-d', strtotime(str_replace('/','-', $emitido[1])));
                        $sql .= " and cast(td.date_create as date) between '$emitido1' and '$emitido2' ";
                    }

                    if($numero!="")
                        $sql .= " and td.id_registro_form like '%$numero%' ";


                    $sql .= " order by id_registro_form desc ";

//                    print_r($sql); die();
                    $total = $db->query($sql)->rowCount();
                    if($start || $length){
                        $sql.=" LIMIT $start,$length ";
                    }
                    $result = $db->query($sql);
                    if($result){
                        if($result->rowCount()>0){
                            while ($object = $result->fetchObject()){

                                if($object->paciente != ""){
                                    $nomp = "<span class='text-sm' style='color: #0866a5; display: block'><b>Paciente:</b>".($object->paciente)."</span>";
                                }else{
                                    $nomp = "";
                                }

                                $rows = array();
                                $rows[] = "";
                                $rows[] = "DOCUMENTO_INFO_".str_pad($object->id_registro_form, 6, "0", STR_PAD_LEFT). " ".$nomp;
                                $rows[] = date("Y/m/d", strtotime($object->date_create));
                                $rows[] = "";
                                $rows['idInfoDoc'] =  $object->id_registro_form; //id de registro documento
                                $rows['number'] =  "DOCUMENTO_INFO_".str_pad($object->id_registro_form, 6, "0", STR_PAD_LEFT);
                                $data[] = $rows;
                            }
                        }
                    }
                }



                $output = array(
                    "data"            => $data,
                    "recordsTotal"    => $total,
                    "recordsFiltered" => $total
                );

                echo json_encode($output);
                break;


            case 'asociar_pacientes_document':

                $error = "";

                $id_list     = GETPOST('id');
                $id_paciente = GETPOST('idpaciente');

                if($id_paciente != ""){
                    $error = "Seleccione un paciente";
                }

                $nompaciente = getnombrePaciente($id_paciente);
                $nompaciente = $nompaciente->nombre." ".$nompaciente->apellido;

                $sql = "UPDATE `tab_documentos_clinicos_data` SET `fk_paciente`='$id_paciente' WHERE `id_registro_form` in($id_list)";
                $result = $db->query($sql);
                if($result){
                    $error = "";
                    $id_list = explode(',', $id_list );
                    foreach ($id_list as $valueid){
                        $num  = "DOCUMENTO_INFO_".str_pad($valueid, 6, "0", STR_PAD_LEFT);
                        $desc = "Se modifico el registro ".$num.". Documento Asociado al paciente: ".$nompaciente;
                        $log->log($valueid, $log->modificar, $desc, "tab_documentos_clinicos_data");
                    }
                }else{
                    $error = $messErr;
                }

                $output = array(
                    "error" => $error,
                );

                echo json_encode($output);
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
            case "eleminar_Registro_Doc_Data":
                //tab_documentos_clinicos_data
                $error = "";
                $iddocData = GETPOST("id");
                $result = $db->query("DELETE FROM `tab_documentos_clinicos_data` WHERE `id_registro_form`=$iddocData ");
                if(!$result){
                    $error = $messErr;
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






?>