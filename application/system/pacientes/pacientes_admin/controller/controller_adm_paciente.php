<?php


//$db->set_charset("utf8");
//mysqli_set_charset($db, );

if(isset($_GET['ajaxSend']) || isset($_POST['ajaxSend']))
{
    session_start();

    require_once '../../../../config/lib.global.php';
    require_once DOL_DOCUMENT .'/application/config/main.php';
    require_once DOL_DOCUMENT .'/application/system/pacientes/class/class_paciente.php';


    global $db, $conf, $user, $messErr, $log;

    $paciente = new Pacientes($db); //se declara la clase de pacientes

    $accion = GETPOST('accion');

    switch($accion)
    {

        case 'updatePaciente':

            $path       = "";
            $error      = "";
            $logo       = "";
            $nameIcon   = "";
            $name       = "";
            $id         = GETPOST('id');
            $TieneImage = (GETPOST('TieneImage')=="true")?true:false;


            $name = "icon_paciente-datos_personales-$id".".png";
            if(isset($_FILES['file_icon']) && $_FILES['file_icon']['name'] != "")
            {
                $logo = $_FILES['file_icon'];
                $type = "";
                switch ($logo['type']) {
                    case 'image/png':
                        $type = '.png';
                        break;
                }

                if(!empty($type)){

                    $path = UploadFicherosLogosEntidadGlob($name, $type, $_FILES['file_icon']['tmp_name']);
                    //se comprueba si el fichero se pudo subir
                    if(!file_exists($conf->DIRECTORIO.'/'.$name) && empty($error)){
                        $error = "Ocurrio un error: subida de icono intentelo de nuevo o consulte con soporte ";
                    }
                }
            }

//            print_r($TieneImage); die();

            if(empty($error)){
                $paciente->nombre       = GETPOST('nombre');
                $paciente->apellido     = GETPOST('apellido');
                $paciente->rud_dni      = GETPOST('rud_dni');
                $paciente->email        = GETPOST('email');
                $paciente->convenio     = GETPOST('convenio');
                $paciente->n_interno    = GETPOST('n_interno');
                $paciente->sexo         = GETPOST('sexo');
                $paciente->fech_nacimit = GETPOST('fech_nacimit');
                $paciente->ciudad       = GETPOST('ciudad');
                $paciente->comuna       = GETPOST('comuna');
                $paciente->direcc       = GETPOST('direcc');
                $paciente->t_fijo       = GETPOST('t_fijo');
                $paciente->t_movil      = GETPOST('t_movil');
                $paciente->act_profec   = GETPOST('act_profec');
                $paciente->empleado     = GETPOST('empleado');
                $paciente->obsrv        = GETPOST('obsrv');
                $paciente->apoderado    = GETPOST('apoderado');
                $paciente->refer        = GETPOST('refer');
                $paciente->icon         = ($TieneImage==false)?'':$name; #icon de la imagen del paciente

                $error                  = $paciente->UpdatePaciente($id);

                if($TieneImage==false){
                    if(file_exists($conf->DIRECTORIO.'/'."icon_paciente-datos_personales-$id".".png")){
                        unlink($conf->DIRECTORIO.'/'."icon_paciente-datos_personales-$id".".png");
                    }
                }
            }

            $output = [ 'error' => $error ];

            echo json_encode($output);

            break;

        case 'fetchPaciente':

            $resp  = array();
            $data  = array();
            $error = '';
            $id = GETPOST('id');
            if(!empty($id)){
                $resp = $paciente->fectch_pacientes($id);
                if(count($resp) == 0){
                    $error = 'Ocurrio un error No se Encontraron datos de este paciente, Consulte con soporte';
                }else{
                    $data = $resp[0];
                    $iconstring = (string)$data->icon;
                    if(!empty($iconstring)){
                        if(file_exists(DOL_DOCUMENT.'/logos_icon/icon_logos_'.$conf->EMPRESA->ENTIDAD.'/'.$data->icon)){
                            $img64 = base64_encode(file_get_contents(DOL_DOCUMENT.'/logos_icon/icon_logos_'.$conf->EMPRESA->ENTIDAD.'/'.$data->icon));
                            $data->img_logo = 'data:image/png; base64, '.$img64;
                        }
                    }
                }
            }else{
                $error = 'Ocurrio un error no se pudo obtener los datos - no se encuentra el id';
            }


            $output = [
              'error' =>  $error,
              'data'  =>  $data ,
            ];

            echo json_encode($output);

            break;

        case 'cargaFamiliares_list':

            $data = array();

            $rs = "";

            $output = [
                'data' => $data
            ];

            echo json_encode($output);
            break;

        case 'Ficheros_pacientes':

            $data = array();

            $idpaciente = GETPOST('idpaciente');
            $sql = "select
                    c.rowid ,
                    fecha_creat,
                    titulo ,
                    (select nombre_doc from tab_odontologos where rowid = c.fk_doc) as nom,
                    comment 
                    from tab_fichero_pacientes_cab c where c.rowid > 0";

            if(!empty($idpaciente)){
                $sql .= " and c.fk_paciente = ".$idpaciente;
            }

            $rs  = $db->query($sql);

            if($rs->rowCount() > 0)
            {
                while ($obj = $rs->fetchObject())
                {

                    $row = array();

                        $sql2 = "select * from tab_fichero_pacientes_det where fk_fichero_paciente_cab = $obj->rowid ;";
                        $rs12 = $db->query($sql2);

                        if($rs12->rowCount() > 0)
                        {
                            $html2 = "";
                            $html2 .= "";

                            while($obj2 =  $rs12->fetchObject())
                            {
                                $nombreCarpeta = 'carpeta_entidad_'.(base64_encode($conf->EMPRESA->ENTIDAD));
                                $DirectorioFile = DOL_DOCUMENT .'/application/system/pacientes/pacientes_admin/archivos_ficheros/filesave/'.$nombreCarpeta.'/'.$obj2->ruta_fichero;
                                $DirectorioFileHTTP = DOL_HTTP.'/application/system/pacientes/pacientes_admin/archivos_ficheros/filesave/'.$nombreCarpeta.'/'.$obj2->ruta_fichero;

                                if($obj2->type=='.jpeg'||$obj2->type=='.png'||$obj2->type=='.pdf'){
                                    $icon_tipo = 'data:image/*; base64, '.base64_encode(file_get_contents(DOL_HTTP .'/application/system/pacientes/pacientes_admin/archivos_ficheros/filesave/'.$nombreCarpeta.'/'.$obj2->ruta_fichero));

                                    if($obj2->type=='.pdf'){
                                        $icon_tipo = 'data:image/*; base64, '.base64_encode(file_get_contents(DOL_HTTP .'/logos_icon/logo_default/pdf.png'));
                                    }
                                }

                                if(!file_exists($DirectorioFile)){
                                    $html2 .= "
                                                <a href='#' title='No se encuentra el fichero en la dataFile consulte con soporte Tecnico'>
                                                 No File
                                                &nbsp;   
                                                &nbsp;
                                                $obj->titulo
                                                </a>  ";
                                }else{
                                    #compruebo el type del fichero
                                    switch($obj2->type)
                                    {

                                        case '.jpeg':
                                            $html2 .= "
                                                <a href='".$DirectorioFileHTTP."' target='_blank' >
                                                <img width='50px' height='50px' src='".$icon_tipo."'  >
                                                &nbsp;   
                                                &nbsp;
                                                $obj->titulo
                                                </a>  ";
                                            break;
                                        case '.png':
                                            $html2 .= "
                                               <a  href='".$DirectorioFileHTTP."' target='_blank' >
                                               <img width='50px' height='50px' src='".$icon_tipo."'  >         
                                               &nbsp;   
                                               &nbsp;
                                               $obj->titulo
                                               </a>";
                                            break;

                                        case '.pdf':
                                            $html2 .= "
                                               <a href='".$DirectorioFileHTTP."' target='_blank' >
                                               <img width='50px' height='50px' src='".$icon_tipo."'  >       
                                               &nbsp;   
                                               &nbsp;
                                               $obj->titulo
                                               </a>";
                                            break;
                                    }
                                }

                            }


                            $html2 .= "";

                            $formgroupImg =
                                "<div class='form-group col-md-10 col-xs-10 '>
                                        $html2
                                </div>";

//                            $row[] = $formgroupImg;
                        }

                    $row[] = $formgroupImg;
                    $row[] = $obj->comment;
                    $row[] = date("Y/m/d", strtotime($obj->fecha_creat));
                    $row[] = "<a class='btn btn-sm'  onclick='del_ficheropaciente($obj->rowid)' style='background-color: #cc4b4c; color: #ffffff; padding: 10px;font-weight: bold' title='Eliminar Registro'>Eliminar File</a>";

                    $data[] = $row;

                }
            }


            $output = [
                'data' => $data
            ];

//            print_r($output);
//            die();

            echo json_encode($output);
            break;

        case 'FicheroPacienteInsert':


            $error = '';
            $errores_ficheros = '';

            $datostextcab = array();

            $datostextcab = array( GETPOST('doctor') , GETPOST('fechaFichero'), GETPOST('observacion'), GETPOST('tituloFichero'), GETPOST('idpaciente') );

            //declaro el nombre de la carpeta
            $nombreCarpeta = 'carpeta_entidad_'.(base64_encode($conf->EMPRESA->ENTIDAD));
            $direct = DOL_DOCUMENT .'/application/system/pacientes/pacientes_admin/archivos_ficheros/filesave/'.$nombreCarpeta; //Accedo a a raiz de la carpeta de esta empresa

            #compruebo si la carpeta exite , si no existe la creo
            $carpeta = "";
            if(!is_dir($direct)) {
                if(!mkdir($direct,0777, true)){
                    $error = 'Ocurrió un error con el servidor al crear la dataFile para esta clínica, contacte con soporte técnico';
                }
            }


            if(isset($_FILES['files'])){
                #No se encontro ningun fichero
                if( $_FILES['files']['error'][0] == 4){
                    $errores_ficheros = "No se econtro ningun fichero";
                }
            }

            #print_r($_FILES['files']); die();
            if(file_exists($direct))//compruebo si existe la carpeta
            {

                $acuF = 0;
                $sqlnF = "SELECT ifnull((MAX(rowid)+1),1) as nfichero FROM tab_fichero_pacientes_det";
                $r=$db->query($sqlnF);
                $numeroFichero = $r->fetchObject()->nfichero;

                $datostextdet = array();
                $acuF = $numeroFichero;

                foreach($_FILES['files']['name'] as $key => $val)
                {
                    $tmp_name = $_FILES["files"]["tmp_name"][$key];
                    // basename() puede evitar ataques de denegación de sistema de fichero_entidad;

                    $type = "";
                    switch ($_FILES['files']['type'][$key]) {
                        case 'image/png':
                            $type = '.png';
                            break;

                        case 'image/jpeg':
                            $type = '.jpeg';
                            break;

                        case 'application/pdf':
                            $type = '.pdf';
                            break;

                        default;
                            $error = 'Ocurrió un problema con la Operación, Contacte a con soporte Técnico';
                            break;

                    }

                    #print_r($_FILES); die();
                    $puedopasar = false;
                    if(!empty($type))
                    {
                        $name_fichero = 'filesave_'.$acuF.''.(base64_encode($conf->EMPRESA->ENTIDAD)).$type; //name de fichero

                        if($type == ".png" || $type == ".jpeg"){
                            $datostextdet[] = array("name" => $name_fichero , "type" => $type);
                            $URL_ = $direct; #Obtengo el directorio del archivo
                            UploadFicherosLogosEntidadGlob($name_fichero, $_FILES['files']['type'][$key], $_FILES['files']['tmp_name'][$key], $URL_);
                            $puedopasar = true;
                        }else{
                            if( move_uploaded_file($tmp_name, $direct.'/'.$name_fichero) ) {
                                $datostextdet[] = array("name" => $name_fichero , "type" => $type);
                                $puedopasar = true;
                            }else{
                                $puedopasar = false;
                                $error = 'Ocurrió, un problema con la Operación, Contacte a con soporte Técnico';
                            }
                        }

                    }else{
                        $acuF++;
                    }

                }

                #print_r($puedopasar);  die();

                $rs = 0;
                if( $puedopasar == true){
                    $rs = CrearInsertDirecFicheroPaciente($datostextcab, $datostextdet, $nombreCarpeta);
                }else{
                    $error = 'Ocurrió, un problema con la Operación, Contacte a con soporte Técnico';
                }
            }

            if($rs > 0) {
                $error = '';
            }
            if($errores_ficheros != ''){
                $error = $errores_ficheros;
            }

            $output = [
                'error' => $error
            ];

            echo json_encode($output);

            break;

        case 'delete_fichero_paciente':

            $error = '';

            $id = GETPOST("id");
            $sql1 = "select * from tab_fichero_pacientes_cab where rowid = $id";
            $rsF1 = $db->query($sql1);

            $sql2 = "select * from tab_fichero_pacientes_det where fk_fichero_paciente_cab = $id limit 1";
            $rsF2 = $db->query($sql2);

//            print_r($rsF2->rowCount()); die();
            if($rsF1->rowCount() == $rsF2->rowCount()){

                $objFichero        = $rsF2->fetchObject();
                $bakFichero        = $objFichero->ruta_fichero;
                $directorioEntidad = $objFichero->name_direct;

                $sqldelcab = "DELETE FROM tab_fichero_pacientes_cab WHERE rowid  = $id";
                $rscab = $db->query($sqldelcab);

                $sqldel = "DELETE FROM tab_fichero_pacientes_det WHERE rowid > 0 and fk_fichero_paciente_cab = $id";
                $rsdel = $db->query($sqldel);
                if(!$rsdel){
                    $error = 'Ocurrio un error con la Operación Eliminar Ficheros';
                }

                if($rsdel) {
                    if( $bakFichero != "") {
                        unlink(DOL_DOCUMENT .'/application/system/pacientes/pacientes_admin/archivos_ficheros/filesave/'.$directorioEntidad.'/'.$bakFichero);
                    }
                }

            }else{
                $error = 'Ocurrio un error con la Operación Eliminar Ficheros la dimención de los ficheros no coinciden consulte con soporte tecnico';
            }

            $output = [
                'error' => $error
            ];

            echo json_encode($output);
            break;

        case "comecent_doct_paciente_crearte":

            $text        = GETPOST("text");
            $idPaciente  = GETPOST("idPaciente");
            $idUser      = $user->id;
            $subaccion   = GETPOST('subaccion');

            //cuando se ingresa un comentario
            if($subaccion == "agregar") {
                    $sql_a = "INSERT INTO tab_comentarios_odontologos (`fk_users_athor`, `comentario`, `fk_paciente`) VALUES ($idUser, '$text', $idPaciente);";
                    $result_a = $db->query($sql_a);
                    if($result_a){
                        $error = "";
                        $idLast = $db->lastInsertId('tab_comentarios_odontologos');
                        $log->log($idLast, $log->crear, 'Se ha registrado un comentario codigo64: '.base64_encode($idLast).' user: '.$user->name, 'tab_comentarios_odontologos');
                    }else{
                        $log->log(0, $log->error, 'Ha Ocurrido un error con la Operación crear Comentario. User: '.$user->name, 'tab_comentarios_odontologos', $sql_a);
                        $error = $messErr;
                    }
            }else{
                $error = "Error de parámetros de entrada consulte con Soporte";
            }


            $output = [
                'error'  => $error,
            ];
            echo json_encode($output);
            break;

        case 'listComment':


            if(!PermitsModule('Comentarios Administrativos', 'consultar')){
                $permits = " and 1<>1";
            }else{
                $permits = " and 1=1";
            }

            $id = GETPOST('paciente_id');
            $start = GETPOST('start');
            $length = GETPOST('length');

            $datos = [];

            $sql = "SELECT 
                        c.tms AS date,
                        c.rowid,
                        c.comentario, 
                        s.usuario , 
                        '' as icon
                    FROM
                        tab_comentarios_odontologos c
                        inner join 
                        tab_login_users s on s.rowid = c.fk_users_athor
                    WHERE
                        c.fk_paciente = $id and c.comentario <> '' ";
            $sql .= $permits;
            $sql .= " ORDER BY c.rowid desc ";
            $total = $db->query($sql)->rowCount();

            if($start || $length)
                $sql.=" LIMIT $start,$length ";

            $result = $db->query($sql);
            if($result){
                if($result->rowCount()>0){
                    $fetch = $result->fetchAll(PDO::FETCH_ASSOC);
                    if(count($fetch)>0){
                        foreach ($fetch as $item){

                            $icon = str_replace(' ', '', $item['icon']);
                            if(!empty($icon)){
                                if(file_exists(DOL_HTTP.'/logos_icon/'.$conf->NAME_DIRECTORIO.'/'.$item['icon'])){
                                    $url = DOL_HTTP.'/logos_icon/'.$conf->NAME_DIRECTORIO.'/'.$item['icon'];
                                    $imgbase64 = base64_encode(file_get_contents($url));
                                    $imgbase64 = 'data:image/png; base64, '.$imgbase64;
                                }else{
                                    $url = DOL_HTTP.'/logos_icon/logo_default/icon_avatar.svg';
                                    $imgbase64 = $url;
                                }
                            }else{
                                $url = DOL_HTTP.'/logos_icon/logo_default/icon_avatar.svg';
                                $imgbase64 = $url;
                            }

                            $row = [];
                            $row[] = "<img class='direct-chat-img img-sm' src='$imgbase64'>";
                            $row[] = "";

                            $row['icon']    = $icon;
                            $row['usuario'] = $item['usuario'];
                            $row['msg']     = $item['comentario'];
                            $row['date']    = GET_DATE_SPANISH($item['date']).' '.date('H:m:s', strtotime($item['date']));
                            $datos[]        = $row;

                        }
                    }
                }
            }


            $output = [
                "data"            => $datos,
                "recordsTotal"    => $total,
                "recordsFiltered" => $total

            ];
            echo json_encode($output);
            break;


        case "list_informacion_doc":

            $data = array();

            $idpaciente = GETPOST('idpaciente');

            $respobj = info_type_document_pacient( $idpaciente );


            $output = [
                "data" => $respobj,
            ];

            echo json_encode($output);
            break;

        case "obj_document_clinico":

            $error=false;
            $dataPrincipal = array();
            $id = GETPOST("idClinico");

            $sql = "SELECT * FROM tab_documentos_ficha_clinica where rowid = " . $id;
            $rs  = $db->query($sql);

            if($rs->rowCount() > 0){
                $error=true;
                while ($Obj = $rs->fetchObject()){
                    $dataPrincipal = $Obj;
                }
            }

            $output = [
                "error" => $error,
                "data"  => $dataPrincipal
            ];

            echo json_encode($output);
            break;

        case "list_citas_admin":

            $idPaciente  = GETPOST('idpaciente');
            $Fecha       = !empty(GETPOST('fecha'))?explode('-',GETPOST('fecha')):"";
            $n_cita      = GETPOST('n_cita');
            $EstadosLis  = GETPOST('estadoslist');

            $fechaInicio  = "";
            $fechafin     = "";

            if($Fecha!=""){
                $fechaInicio = str_replace("/","-",$Fecha[0]);
                $fechafin    = str_replace("/","-",$Fecha[1]);
            }

            $resultado = listcitas_admin($idPaciente, $fechaInicio, $fechafin, $n_cita, $EstadosLis);

            $output = array(
                "data"            => $resultado['datos'],
                "recordsTotal"    => $resultado['total'],
                "recordsFiltered" => $resultado['total']
            );

            echo json_encode($output);

            break;

        case 'consultar_numero_odontograma':

            $numero = "";

            $sql = "SELECT 
                IFNULL(MAX(rowid), 1) AS numero
            FROM
                tab_odontograma_paciente_cab";
            $resul = $db->query($sql)->fetchObject();
            $numero = "Odontograma - " . str_pad($resul->numero, 4, "0", STR_PAD_LEFT);

            echo json_encode($numero);
            break;

        case 'nuevoUpdateOdontograma':

            $error = '';
            $puedoPasar = 0;
            $lastidOdontogramacab = 0;


            $fk_tratamiento  = GETPOST('fk_tratamiento');
            $descript        = GETPOST('descrip');
            $numero          = 0;
            $idpaciente      = GETPOST("fk_paciente");
            $nombre_paciente = GETPOST("nom_paciente");


            $sql_a    = "SELECT * FROM tab_odontograma_paciente_cab WHERE fk_tratamiento = $fk_tratamiento and fk_paciente = $idpaciente and estado_odont='A' ";
            $result_a = $db->query($sql_a);
            $valid    = $result_a->rowCount();
            if($valid>0){
                $object_a = $result_a->fetchObject();
                // se detecta si esta asociado ya a un odontograma
                if($object_a->fk_tratamiento == $fk_tratamiento){
                    $error = 'Se detecto asociado a un odontograma '.$object_a->numero;
                    $puedoPasar++;
                }
            }

            if( $puedoPasar == 0 && $error == ''){

                $paciente->fk_plantratamiento = $fk_tratamiento;
                $paciente->numero             = $numero;
                $paciente->odontodescripcion  = $descript;
                $paciente->fk_usuario         = $user->id;
                $paciente->fk_paciente        = $idpaciente;

                $error = $paciente->createOdontogramaCab($nombre_paciente);
                $lastidOdontogramacab = $db->lastInsertId('tab_odontograma_paciente_cab'); /*ultimo id del odontograma insertado cabezera*/

            }

            $output = [
                'error'      => $error ,
                'lasidOdont' => $lastidOdontogramacab ,
                'idpa'       => tokenSecurityId($idpaciente),
            ];

            echo json_encode($output);
            break;

            /*se inserta odontogramaUpdate para el detalle
            esta funcionalidad solo aplica cuando se crea un odontograma x primira vez y su plan de  tratamiento asignado*/
        case 'OdontogramaUpdate_detalle':

            $error = '';
            $fk_paciente         = GETPOST("idpaciente");
            $fk_plantratamiento  = GETPOST("idplantm");

            $detInsert = array();
            $dataNumeroDientes = array(11,12,13,14,15,16,17,18,21,22,23,24,25,26,27,28,41,42,43,44,45,46,47,48,31,32,33,34,35,36,37,38);

            foreach ($dataNumeroDientes as $value)
            {
                $iddiente = $value;
                $jsoncaras = "{\"vestibular\":\"false\",\"distal\":\"false\",\"palatino\":\"false\",\"oclusal\":\"false\",\"mesial\":\"false\",\"lingual\":\"false\"}";

                $detInsert[] = "($iddiente,'$jsoncaras','no hay momentaneo',0,$fk_plantratamiento,$fk_paciente)";
            }

            $sqldetupdate = "INSERT INTO `tab_odontograma_update` (`fk_diente`, `json_caras`, `type_hermiarcada`, `fk_estado_pieza`, `fk_tratamiento`, `fk_paciente`) VALUES ".(implode(',', $detInsert));
            $db->query($sqldetupdate);
//            print_r($sqldetupdate); die();

            $output = [
                'error' => $error
            ];

            echo json_encode($output);
            break;

        case 'list_odontograma':

            $Total          = 0;
            $start          = $_POST["start"];
            $length         = $_POST["length"];

            $data       = array();
            $idpaciente = GETPOST('idpaciente');
            $date_cc    = GETPOST('date_c');
            $fk_plantratamiento    = GETPOST('plantramiento');
            $estado     = GETPOST('estado');

            if(!empty($date_cc)){
                $date_cc = explode('-', $date_cc);
                $date_cc_ini =  str_replace('/','-', $date_cc[0] );
                $date_cc_fin =  str_replace('/','-', $date_cc[1] );
            }

            if(!PermitsModule("Odontograma", "consultar")){
                $permits = " and 1<>1 ";
            }else{
                $permits = " and 1=1 ";
            }

            $sql = "SELECT
                        dc.fecha,
                        dc.rowid, 
                        dc.rowid as odontograma_id, 
                        dc.numero,
                        dc.descripcion,
                        dc.fk_tratamiento, 
                        dc.estado_odont , 
                        concat('Plan de tratamiento N.', pc.numero) as label
                    FROM 
                    tab_odontograma_paciente_cab dc 
                      inner  join 
                    tab_plan_tratamiento_cab pc on pc.rowid = dc.fk_tratamiento
                    where 1=1";

            if(!empty($idpaciente)){
                $sql .= " and dc.fk_paciente = ".$idpaciente;
            }
            if(!empty($fk_plantratamiento)){
                $sql .= " and dc.fk_tratamiento = ".$fk_plantratamiento;
            }
            if(!empty($estado)){
                $sql .= " and dc.estado_odont = '$estado' ";
            }else{
                $sql .= " and dc.estado_odont = 'A' ";
            }
            if(!empty($date_cc_ini) && !empty($date_cc_fin)){
                $sql .= " and cast(dc.fecha as date) between '$date_cc_ini' and '$date_cc_fin' ";
            }

            $sql .= $permits;

            $Total = $db->query($sql)->rowCount();

            $sql .= ' order by dc.numero desc';
            if($start || $length){
                $sql.=" LIMIT $start,$length;";
            }

//            print_r($sql); die();
            $resul = $db->query($sql);

            if($resul->rowCount() > 0){
                while ( $ob = $resul->fetchObject() ){

                    $row = array();
                    $itemAsociarOdontograma = "";
                    if($ob->fk_tratamiento == 0){
                        $itemAsociarOdontograma = "<p>
                            <b>Asociar Odontograma</b>
                        </p>";

                    }

                    #FK_PLAN DE TRATAMIENTO QUE ESTA ASOCIADO A ESTE ODONTOGRAMA
                    $URL_idplantramiento = '&idplantram='.$ob->fk_tratamiento;

                    $url_updateOdont = DOL_HTTP.'/application/system/pacientes/pacientes_admin/index.php?view=odot&key='.KEY_GLOB.'&id='.tokenSecurityId($idpaciente).'&v=fordont'.$URL_idplantramiento;


                    $delete = "<td><a class='btnhover btn btn-xs delete_odont_click ".(!PermitsModule("Odontograma", "eliminar")?"disabled_link3":"")." ' onclick='Eliminar_odontograma($(this))' style='font-weight: bolder; color: red'>
                                        <input type='text' class='hidden odont_id' id='odont_id' value='".$ob->odontograma_id."' data-id='".$ob->odontograma_id."' data-tratamiento='".$ob->label."'>
                                        <i class='fa fa-trash'></i> Anular </a>
                                </td>";
                    if($ob->estado_odont == 'E'){
                        $delete = "";
                    }

                    $opciones = "<table>
                                   <tr>
                                       <td class='".(($ob->estado_odont=='E')?"disabled_link3":"")." '><a href='$url_updateOdont' class='btnhover btn btn-xs ".(!PermitsModule("Odontograma", "modificar")?"disabled_link3":"")." ' style='font-weight: bolder'> <i class='fa fa-edit'></i> Actualizar </a>     </td>
                                       $delete
                                   </tr> 
                                </table>";

                    $numero = (int)$ob->numero;

                    if($ob->estado_odont=='E')
                        $stado = "<small style='display: block; color: #9f191f'>odontograma Eliminado</small>";
                    if($ob->estado_odont=='A')
                        $stado = "<small style='display: block; color: green'>odontograma Activo</small>";

                    $row[] = date('Y/m/d', strtotime($ob->fecha));
                    $row[] = 'Odontograma N.'.str_pad($numero, 6, "0", STR_PAD_LEFT)."".$stado;
                    $row[] = $ob->descripcion;
                    $row[] = $ob->label; #PLAN DE TRATAMIENTO NOMBRE
                    $row[] = $opciones;

                    #ID
                    $row[] = $ob->fk_tratamiento;

                    $data[] = $row;
                }
            }


            $output = array(
                "draw"            => $_POST['draw'],
                "data"            => $data,
                "recordsTotal"    => $Total,
                "recordsFiltered" => $Total
            );


            echo json_encode($output);
            break;


        case 'deleteOdontograma':

            $error = "";

            $id = GETPOST('id');
            $sql_b = "Select * from tab_odontograma_paciente_cab where rowid = ".$id;
            $result_b = $db->query($sql_b);
            if($result_b){
                $object_b = $result_b->fetchObject();
                if($object_b->estado_odont == 'E'){
                    $error = 'Se encuentra Eliminado';
                }else{
                    $sql_a = "UPDATE `tab_odontograma_paciente_cab` SET `estado_odont`='E' WHERE `rowid`= $id;";
                    $result = $db->query($sql_a);
                    if($result){
                        $error = "";
                        $log->log($id, $log->modificar ,'Se ha actualizado un registro Odontograma'.$object_b->numero .' ha estado Eliminar', 'tab_odontograma_paciente_cab');
                    }else{
                        $error = $messErr;
                        $log->log($id, $log->error ,'Ha ocurrido un error con la operación Eliminar Odontograma.'.$object_b->numero, 'tab_odontograma_paciente_cab', $sql_b);
                    }
                }
            }else{
                $error = $messErr;
            }

            $output = [
                'error' => $error
            ];
            echo json_encode($output);
            break;

        case 'fetchnewtratamiento':

            $objetoCab = array();
            $objetoDet = array();

            $error='';
            $subaccion  = GETPOST('subaccion');

            $idpaciente        = GETPOST('idpaciente');
            $idtratamiento     = GETPOST('idtratamiento');

            //                  $numero_tratamiento = ''.str_pad($rse->numero, 7, "0", STR_PAD_LEFT);

            #informacion plan de tratamiento cabezera
            $sqltrancab = "SELECT 
                    tc.numero numero,
                    tc.abonos abonos,
                    tc.fk_doc fkdoc,
                    ifnull((SELECT CONCAT(od.nombre_doc, ' ', od.apellido_doc)  FROM tab_odontologos od WHERE tc.fk_doc = od.rowid), 'No asignado') nombre_doc,
                    tc.fk_paciente,
                    CONCAT(ap.nombre, ' ', ap.apellido) nombre,
                    tc.fk_convenio,
                    IFNULL((SELECT cf.nombre_conv FROM tab_conf_convenio_desc cf WHERE cf.rowid = tc.fk_convenio), 'convenio no asignado') convenio,
                    IFNULL((SELECT cf.valor FROM tab_conf_convenio_desc cf WHERE cf.rowid = tc.fk_convenio),0) valorConvenio ,
                    tc.edit_name as edit_name , 
                    -- ABONADO
                    (SELECT round(sum(pd.amount),2) saldo FROM tab_pagos_independ_pacientes_det pd where pd.fk_plantram_cab = tc.rowid and pd.estado = 'A' and pd.fk_paciente = $idpaciente) abonado_cab , 
                    tc.observacion, 
                    tc.estados_tratamiento
                    
                FROM
                    tab_plan_tratamiento_cab tc,
                    tab_admin_pacientes ap,
                    tab_odontologos od
                WHERE
                    tc.fk_paciente = ap.rowid
                        AND tc.rowid = ".$idtratamiento."
                        AND tc.fk_paciente = ". $idpaciente ." limit 1";

//            print_r($sqltrancab);
            $rscab = $db->query($sqltrancab);
            if($rscab && $rscab->rowCount() > 0 ){

                while ($obcab = $rscab->fetchObject()){
                    $objetoCab[] = $obcab;

                }
            }else{
                $error = 'Ocurrio un error , consulte con soporte Tecnico';
            }

            #informacion plan de tratamiento detalle
            $sqltransdet = "SELECT 
                        pd.rowid, 
                        pd.fk_plantratam_cab ,
                        pd.fk_diente AS diente,
                        cp.descripcion AS prestacion,
                        pd.fk_prestacion AS fk_prestacion,
                        round(pd.precio_u, 2) AS precio,
                        pd.cantidad , 
                        round((pd.total * (case pd.iva
								when 12 then 0.12 
								else 0
								end)), 2) as iva , 
                        pd.desc_convenio AS descconvenio,
                        pd.desc_adicional AS descadicional,
                        pd.json_caras,
                        pd.total,
                        pd.estadodet , 
                        pd.fk_usuario , 
                        ifnull((SELECT usuario FROM tab_login_users s where s.rowid = pd.fk_usuario limit 1),'') as usuario_creator , 
                        ifnull((SELECT concat(s.nombre_doc ,' ', s.apellido_doc) as doc FROM tab_odontologos s where s.rowid = pd.realizada_fk_dentista limit 1),'') as usuario_realizado , 
                        pd.estado_pay as estado_pago, 
                        ifnull((select l.name from tab_conf_laboratorios_clinicos l where l.rowid = cp.fk_laboratorio),'') as laboratorio
                    FROM
                        tab_plan_tratamiento_det pd,
                        tab_conf_prestaciones cp
                    WHERE
                        pd.fk_prestacion = cp.rowid
                        AND pd.fk_plantratam_cab = ".$idtratamiento." order by pd.rowid desc";
//            print_r($sqltransdet); die();
            $rsd = $db->query($sqltransdet);
            if($rsd->rowCount() > 0 ){
                while ($obdet = $rsd->fetchObject()){
                    $objetoDet[] = $obdet;
                }

            }else{
//                $error = 'Ocurrio un error , consulte con soporte Tecnico';
            }

            $output = [
              'error'         => $error,
              'objetoCab'     => $objetoCab,
              'objetoDet'     => $objetoDet,
              'ico_diente'    => "data: image/*; base64, ".base64_encode(file_get_contents(DOL_DOCUMENT.'/logos_icon/logo_default/diente.png')),
            ];

            echo json_encode($output);
            break;


        case 'fetch_prestaciones':

            $productos = array();
            $idprest   = GETPOST('idprest');

            $sql = "SELECT 
                    c.rowid,
                    c.descripcion,
                    c.valor,
                    c.precio_paciente, 
                    c.costo_x_clinica, 
                    c.iva, 
                    IFNULL((SELECT  d.nombre_conv FROM tab_conf_convenio_desc d WHERE d.rowid = c.fk_convenio), '') AS convenio,
                    IFNULL((SELECT  d.valor FROM tab_conf_convenio_desc d WHERE d.rowid = c.fk_convenio), 0) convenio_valor
                FROM
                    tab_conf_prestaciones c where 1=1";

            if(!empty($idprest)){
                $sql .= " and c.rowid = $idprest";
            }

            $rs = $db->query($sql);
            if ($rs->rowCount()>0){
                while ($obj = $rs->fetchObject()){
                    $productos = $obj;
                }
            }

            echo json_encode($productos);
            break;

        case 'list_tratamiento':

            if(!PermitsModule("Planes de Tratamientos", "consultar")){
                $permits = "  1<>1";
            }else{
                $permits = "  1=1";
            }

            $Total          = 0;
            $start          = $_POST["start"];
            $length         = $_POST["length"];

            $idplantmiento            = GETPOST('idplantmiento');
            $idpaciente               = GETPOST('idpaciente');
            $fecha_range              = (GETPOST('fecha_range')!="")?explode('-', GETPOST('fecha_range')):"";

            $estado_TTO               = GETPOST('estado');

            if(!empty($estado_TTO)){
                if($estado_TTO=='Finalizados')
                    $estadoWhere = " and tc.estados_tratamiento in('F') ";//TT0 FINALIZADOS
                if($estado_TTO=='Anulados')
                    $estadoWhere = " and tc.estados_tratamiento in('E') "; //TT0 ANULADOS
                if($estado_TTO=='Abonados')
                    $estadoWhere = " and tc.estados_tratamiento in('S') ";//TT0 ABONADO
                if($estado_TTO=='Diagnostico')
                    $estadoWhere = " and tc.estados_tratamiento in('A') ";//TT0 DIAGNÓSTICO
            }else
                $estadoWhere = "";



            $dataprincipal = array();
            $sql = "SELECT 
                        tc.rowid,
                        tc.numero,
                        tc.fk_paciente,
                        CONCAT(ap.nombre, ' ', ap.apellido) nombre,
                        tc.fk_paciente,
                        tc.fk_doc fkdoc,
                        
                        IFNULL((SELECT CONCAT(od.nombre_doc, ' ', od.apellido_doc) FROM tab_odontologos od WHERE od.rowid = tc.fk_doc), 'No asignado') AS nombre_doc,
                                
                        tc.estados_tratamiento,
                        tc.ultima_cita,
                        tc.situacion,
                        tc.edit_name AS edit_name,
                        tc.fk_paciente AS idpaciente,
                        tc.fk_cita AS idCitas,
                        tc.fecha_create , 
						sum(pd.abonado) as abonado
                   FROM 
                    tab_plan_tratamiento_cab tc 
						inner join
                    tab_admin_pacientes ap on ap.rowid = tc.fk_paciente
						left join
                    (select pd.fk_plantram_cab as id_tratamiento, round(pd.amount, 2) as abonado from tab_pagos_independ_pacientes_det pd where pd.estado = 'A') as pd on pd.id_tratamiento = tc.rowid
                    where  $permits
                   ";
            $sql .= " and tc.fk_paciente = ".$idpaciente." ";
            if(!empty($estadoWhere)) { //por estados
                $sql .= $estadoWhere;
            }else{ //Mostrando default estado Activos o saldo asociado
                $sql .= " and tc.estados_tratamiento in('A', 'S')"; #tratamientos con saldos y Activos
            }
            if(!empty($idplantmiento)){
                $sql .= " and tc.rowid = $idplantmiento";
            }

            if($fecha_range!=""){
                $fechaIni = trim( str_replace('/', '-', $fecha_range[0] ) );
                $fechaFin = trim( str_replace('/', '-', $fecha_range[1] ) );
                $sql .= " and cast(tc.fecha_create as date) between cast('$fechaIni' as date) and cast('$fechaFin' as date) ";
            }

            $sql .= " group by tc.rowid  order by tc.rowid desc";

            $sqlTotal = $sql;

            if($start || $length){
                $sql.=" LIMIT $start,$length;";
            }

//            print_r($sql); die();

            $Total = $db->query($sqlTotal)->rowCount();
            $rul = $db->query($sql);

            if($rul->rowCount()>0){
                while ($ob = $rul->fetchObject()){

                    $row = array();
                    $estado = "";
                    if($ob->estados_tratamiento == 'A'){
                        $estado = 'Activo';
                    }else{
                        $estado = 'Inactivo';
                    }

                    $nombre_tratamiento = null;
                    if($ob->edit_name != ""){
//                        $nombre_tratamiento = $ob->edit_name;
                        $edit = "<small style='display: block; font-weight: normal;' class='text-sm' >Editado: ".$ob->edit_name."</small>";
                    }else{
                        $nombre_tratamiento = "Plan de Tratamiento: N. $ob->numero ";
                        $edit = "";
                    }

                    //numero del plan de tratamiento del Paciente
                    $nombre_tratamiento = "Plan de Tratamiento: N. $ob->numero ".$edit;

                    $url_planform = DOL_HTTP .'/application/system/pacientes/pacientes_admin/?view=plantram&key='.KEY_GLOB.'&id='.tokenSecurityId($ob->idpaciente).'&v=planform&idplan='.tokenSecurityId($ob->rowid);

                    $row[] = "<a  href='$url_planform' style='font-weight: bold; font-size: 1.6rem; ' class='text-center btn btnhover'>  $nombre_tratamiento  </a>"; #descripcion o numero de tratamiento
//                    $row[] = "<a  href='".DOL_HTTP."/application/system/pacientes/admin_paciente/?view=form_plan_tratamiento&id=".$ob->fk_paciente."&ope=mod&idtratam=".$ob->rowid."' style='font-weight: bold; font-size: 1.6rem' class='text-center btn btnhover'>  $nombre_tratamiento  </a>"; #descripcion o numero de tratamiento
                    $row[] = $ob->nombre_doc;  #nombre Doctor
                    $row[] = $estado;
                    $row[] = date('Y/m/d', strtotime($ob->ultima_cita));
                    $row[] = date('H:i:s', strtotime($ob->ultima_cita));
                    $row[] = $ob->situacion;
                    $row[] = $ob->rowid; #id plan de tratamiento
                    $row[] = $ob->idCitas; #id plan de cita asociada

                    $row[] = $ob->estados_tratamiento; #estado plan de tratamiento
                    $row[] = $ob->fecha_create; #fecha de creacion del plan de tratamiento
                    $row[] = $ob->estados_tratamiento; #estado plan de tratamiento
                    $row[] = str_pad($ob->idCitas, 6, "0", STR_PAD_LEFT); #cita asociada numero
                    $row['saldoAbonado'] = $ob->abonado;
//                    $row['img_ico_cita'] = "data: image/*; base64, ".base64_encode(file_get_contents(DOL_DOCUMENT.'/logos_icon/logo_default/cita-medica.ico'));

                    $dataprincipal[] = $row;

                }
            }

            $output = [
                "draw" => $_POST['draw'],
                "data" => $dataprincipal,
                "recordsTotal"    => $Total,
                "recordsFiltered" => $Total
            ];

            echo json_encode($output);
            break;

        case 'invalic_prestacion_diente':

            $error = '';

            $subaccion     = GETPOST('subaccion'); #Subaccion Diente o Prestacion
            $idplantram    = GETPOST('idplantram');
            $idprestacion  = GETPOST('prestacion');

            if($subaccion == 'diente'){

                $diente = GETPOST('diente');

                $sql1 = "SELECT * FROM tab_plan_tratamiento_det d WHERE d.fk_plantratam_cab = ".$idplantram." and d.fk_prestacion = $idprestacion and fk_diente = ". $diente." limit 1";
                $rs1  = $db->query($sql1);
                if($rs1->rowCount() > 0){
                    $error = 'Esta Prestación ya esta asiganada junto con la pieza: ' . $diente;
                }
            }

            if( $subaccion == 'prestacion'){

                $sql1 = "SELECT * FROM tab_plan_tratamiento_det d WHERE d.fk_plantratam_cab = ".$idplantram." and d.fk_prestacion = $idprestacion and fk_diente = 0 limit 1";
                $rs1  = $db->query($sql1);
                if($rs1->rowCount() > 0){
                    $error = 'Esta Prestación ya esta asiganada';
                }

            }


//            print_r($error);
//            die();
            $output = [
              'error' => $error
            ];
            echo  json_encode($output);
            break;


        case 'realizarPrestacion':

            $error = '';

            $idCabPlant      = GETPOST('idcabplantram');
            $idDetPlant      = GETPOST('iddetplantram');
            $idpaciente      = GETPOST('idpaciente');
            $iddiente        = GETPOST('iddiente');
            $iddoct          = GETPOST('fk_doct');
            $observacion     = GETPOST('observacion');
            $estadoDiente    = GETPOST('fk_estadodiente');

            $queEstado       = 0;//comprubeo que sea diferente de estado realizado

            $tieneOdontograma = ''; #esta variable comprueba si el plan de tratamiento tiene odontogrma
            $datosRealizarPrestacion = [];

            $result_d = $db->query("SELECT p.descripcion FROM tab_plan_tratamiento_det d INNER JOIN tab_conf_prestaciones p ON p.rowid = d.fk_prestacion WHERE d.rowid = $idDetPlant");
            $detalle_tratamiento = $result_d->fetchObject();

            $sql = "SELECT * FROM tab_plan_tratamiento_det where rowid = $idDetPlant and fk_plantratam_cab = $idCabPlant limit 1";
            $resul = $db->query($sql);
            if($resul && $resul->rowCount() > 0)
            {
                $objtratm = $resul->fetchObject();

                $labellist = []; #aux caras
                $listcaras = []; #cadena de caras


                if($objtratm->json_caras != "")
                {
                    $labellist = json_decode( $objtratm->json_caras );

                    if($labellist->vestibular == "true")
                    { $listcaras[] = "vestibular"; }
                    if($labellist->distal == "true")
                    { $listcaras[] = "distal";  }
                    if($labellist->palatino == "true")
                    { $listcaras[] = "palatino"; }
                    if($labellist->oclusal == "true")
                    { $listcaras[] = "oclusal"; }
                    if($labellist->mesial == "true")
                    { $listcaras[] = "mesial"; }
                    if($labellist->lingual == "true")
                    { $listcaras[] = "lingual"; }

                }

                $datosRealizarPrestacion = (object)[
                    'fk_paciente'        =>  $idpaciente ,
                    'fk_plantram_cab'    =>  $idCabPlant ,
                    'fk_plantram_det'    =>  $idDetPlant ,
                    'observacion'        =>  $observacion ,
                    'json_caras'         =>  $objtratm->json_caras ,
                    'estadodiente'       =>  $estadoDiente,
                    'fk_doctor'          =>  $iddoct,
                    'idlogin'            =>  $conf->login_id,
                    'iddiente'           =>  $objtratm->fk_diente,

                    'AxulisttaCaras'     =>  implode(',', $listcaras),
                ];


            }else{
                $error = 'Ocurrio un error no se encontraron datos para ejecutar el proceso Realizar prestación';
            }

            if( $iddiente != 0 && $error == '') //Se actualiza el odontograma en caso de tener asociado
            {
                $sqlodont = "SELECT * FROM tab_odontograma_update od where od.fk_tratamiento = $idCabPlant and  od.fk_diente = $iddiente";
                $rsodont  = $db->query($sqlodont);
                if($rsodont && $rsodont->rowCount() > 0)
                {
                    #solo se actualiza si el estado es diferente de 0
                    if($estadoDiente!=0)
                    {

                        $updateOdont1 = "UPDATE `tab_odontograma_update` SET `fk_estado_pieza`= $estadoDiente , json_caras = '". $datosRealizarPrestacion->json_caras ."' WHERE `rowid`>0 and fk_tratamiento = $idCabPlant and fk_diente = $iddiente;";
                        $rsultUpdate = $db->query($updateOdont1);

                        if($rsultUpdate){

                        }

                        #Se actualiza el odontograma detalle
                        $InsertOdont2  = "INSERT INTO `tab_odontograma_paciente_det` (`fk_diente`, `json_caras`, `fk_estado_diente`, `fk_tratamiento`, `obsrvacion`, `list_caras`, `fecha`, `estado_anulado`)";
                        $InsertOdont2 .= "VALUES (";
                        $InsertOdont2 .= "  $datosRealizarPrestacion->iddiente , ";
                        $InsertOdont2 .= " '$datosRealizarPrestacion->json_caras'  , ";
                        $InsertOdont2 .= " '$datosRealizarPrestacion->estadodiente' , ";
                        $InsertOdont2 .= "  $datosRealizarPrestacion->fk_plantram_cab ,";
                        $InsertOdont2 .= " '$datosRealizarPrestacion->observacion' ,";
                        $InsertOdont2 .= " '$datosRealizarPrestacion->AxulisttaCaras' ,";
                        $InsertOdont2 .= " ' " . date("Y-m-d") . " ' ,  ";
                        $InsertOdont2 .= " 'A' ";
                        $InsertOdont2 .= " ) ";

                        /*
                        $updateOdont2  = " UPDATE `tab_odontograma_paciente_det` SET  `list_caras`= '". $datosRealizarPrestacion->AxulisttaCaras ."' , ";
                        $updateOdont2 .= " `json_caras`= '". $datosRealizarPrestacion->json_caras ."' ,";
                        $updateOdont2 .= " `fk_estado_diente`= '". $datosRealizarPrestacion->estadodiente ."' ,";
                        $updateOdont2 .= " `obsrvacion`= '". $datosRealizarPrestacion->observacion ."' ";
                        $updateOdont2 .= " WHERE `rowid` > 0  and fk_tratamiento = ".$datosRealizarPrestacion->fk_plantram_cab." and fk_diente =  ".$datosRealizarPrestacion->iddiente." "; */

                        $result_b = $db->query($InsertOdont2);
                        if($result_b){
                            $log->log($idCabPlant, $log->modificar,  "Se actualizado el Odontograma de la Prestación $detalle_tratamiento->descripcion, Pieza: $datosRealizarPrestacion->iddiente. por el Usuario: ".$user->name, 'tab_odontograma_update');
                        }
                    }

                }else{

                    $tieneOdontograma = " <br> <img src='". DOL_HTTP."/logos_icon/logo_default/tooth-solid.svg' width='14px' height='14px' alt=''> <b> Este Plan de tratamiento no tiene Asociado un Odontograma </b>";
                }
            }


            if($error == "") {
                $rlcr = realizarPrestacionupdate( $datosRealizarPrestacion, $detalle_tratamiento );
                if( $rlcr != "" ){
                    $error = $rlcr;
                }
            }

            $output = [
                'error' => $error,
                'tieneOdontograma' => $tieneOdontograma
            ];

//            print_r($output);
//            die();

            echo  json_encode($output);

            break;


        case 'eliminar_prestacion_plantram':



            $error = '';
            $iddetplant = GETPOST('iddetplantram');
            $idCabplant = GETPOST('idplanCab');
            $idpaciente = GETPOST('idpaciente');

            if(!PermitsModule('Planes de Tratamientos', 'eliminar')){
                $permits=false;
            }else{
                $permits=true;
            }

            if($permits){

                $sql = "SELECT 
                        dt.fk_prestacion , 
                        dt.estado_pay , 
                        dt.estadodet , 
                        (select cfp.descripcion from tab_conf_prestaciones cfp WHERE cfp.rowid = dt.fk_prestacion) as prestacion ,
                        round(dt.total, 2) as total, 
                        (select ifnull(round(sum(P.amount), 2),0) from tab_pagos_independ_pacientes_det P where P.fk_plantram_cab = cb.rowid and P.fk_plantram_det = dt.rowid) as cancelado_cobro
                    FROM
                        tab_plan_tratamiento_det dt,
                        tab_plan_tratamiento_cab cb
                    WHERE
                        dt.fk_plantratam_cab = $idCabplant and 
                        cb.rowid = $idCabplant  
                        and dt.rowid = $iddetplant
                        and cb.fk_paciente = $idpaciente
                        limit 1 ";

//            print_r($sql); die();
                $result = $db->query($sql);
                if($result && $result->rowCount() > 0){

                    $obj = $result->fetchObject();

                    #COMPRUEBO EL ESTADO PAGADO DE LA PRESTACION
                    if( $obj->estado_pay == 'PA'){
                        $error = 'No puede Eliminar esta prestación <br><b>' .$obj->prestacion .'</b><br> se encuentra <i class="fa fa-dollar"></i> pagada';
                    }
                    #COMPRUEBO EL ESTADO EN ESTA PRESTACION TIENE SALDO   ABONADO
                    if( $obj->estado_pay == 'PS'){
                        $error = 'No puede Eliminar esta prestación <br><b>' .$obj->prestacion .'</b><br> tiene <i class="fa fa-dollar"></i> saldo asociado comprueba en el modulo de pagos de este plan de tratamiento ';
                    }
                    #SALDO ASOCIADO
                    if( (double)$obj->cancelado_cobro > 0){
                        $error = 'No puede Eliminar <br> <b> se encuentra saldo asociado '. '<span style="color: green">$ '.$obj->cancelado_cobro.'</span> </b>';
                    }

                    #REALIZADA
//                if( $obj->estadodet == 'R'){
//                    $error = 'No puede Eliminar esta prestación <b>' .$obj->prestacion .'</b> se encuentra Realizada';
//                }

                    if( $error == '' ){
                        #Estado de pagado estado_pay
                        #PA ==> PAGADO
                        #PE ==> PENDIENTE
                        #PS ==> SALDO ASOCIADO

                        #Estado de estadodet
                        # A PRESTACION ACTIVA
                        # R PRESTACION REALIZADA
                        if( ($obj->estadodet == 'A' || $obj->estadodet == 'R' || $obj->estado_pay  == 'PE')  ){
                            $delete   = "DELETE FROM tab_plan_tratamiento_det WHERE rowid ='$iddetplant' and fk_plantratam_cab = $idCabplant;";
                            $result_d  = $db->query($delete);
                            if($result_d){
                                $prestacionDesc = getnombrePrestacionServicio($obj->fk_prestacion)->descripcion;
                                $log->log($iddetplant, $log->eliminar, 'Ud. ha eliminado un registros del Plan de Tratamiento N.'.str_pad($idCabplant,6, "0", STR_PAD_LEFT)." | Prestación/Servio: ".$prestacionDesc, 'tab_plan_tratamiento_det');
                            }
                        }else{
                            $error = '<p style="color:  red; font-weight: bolder"> Ocurrió un error no se puede eliminar esta prestación compruebe en que estado se encuentra la prestación. Consulte con Soporte Técnico</p> ';
                        }
                    }

                }else{
                    $error = 'Ocurrio un error no se puede Eliminar esta prestacion, Consulte con soporte Tecnico';
                }
            }else{
                $error = "Ud. No tiene permiso para realizar esta Operación";
            }


//            die();
            $output = [
                'error' => $error,
            ];
            echo  json_encode($output);
            break;

        //FINALIZAR EL PLAN DE TRATAMIENTO
        case 'finalizar_plantramento':

            if(!PermitsModule("Planes de Tratamientos", "modificar")){
                $output = [
                    'error'     =>  "Ud. no tiene permiso para realizar esta operación",
                    'consultar' =>  "<span style='color: red; display: block'><i class='fa fa-warning'></i> Ud. no tiene permiso para realizar esta operación</span>"
                ];
                echo json_encode($output);
                die();
            }

            $consultar = "";
            $error     = "";
            #un de plan de tratamiento solo se puede finalizar si esta tiene todas las  prestaciones pagadas
            $subaccion   = GETPOST('subaccion');
            $idpaciente  = GETPOST('idpaciente');
            $idplantram  = GETPOST('idplant');
//            die();
            if($subaccion == 'consultarfinalizado')
            {

            }

            //Finalizar Plan de tratamiento
            if($subaccion == 'finalizar_plantram'){

                $odontologo = $db->query("Select fk_doc From tab_plan_tratamiento_cab where rowid = ".$idplantram)->fetchObject()->fk_doc;

                $sql2 = "SELECT 
                        c.numero , 
                        c.rowid,
                        (SELECT cp.descripcion FROM tab_conf_prestaciones cp WHERE cp.rowid = d.fk_prestacion) AS labelprestacion,
                        d.estado_pay,
                        (SELECT SUM(pd.amount) FROM tab_pagos_independ_pacientes_det pd WHERE pd.fk_plantram_cab = c.rowid AND pd.fk_plantram_det = d.rowid AND pd.fk_paciente = c.fk_paciente AND pd.fk_prestacion = d.fk_prestacion) AS cancelado_saldo , 
                        d.estadodet 
                        FROM
                            tab_plan_tratamiento_cab c,
                            tab_plan_tratamiento_det d
                        WHERE
                            c.rowid = d.fk_plantratam_cab
                                AND c.rowid = $idplantram
                                AND c.fk_paciente = $idpaciente; ";
                $rs2 = $db->query($sql2);

                $puedeFinalizar = "";
                $invalic = 0;
                $nomb_tratamiento = "";
                $prestaciones_pendientes = []; #PRESTACIONES QUE AUN NO ESTAN PAGADAS
                $prestaciones_saldo      = []; #PRESTACIONES QUE TIENE SALDO ( OSEA UNA PARTE PAGADA )
                $prestacion_Norealizada  = []; #PRESTACIONES QUE AUN NO ESTAN REALIZADA


                if($rs2 && $rs2->rowCount()>0)
                {
                    while ( $obprestFinal = $rs2->fetchObject() )
                    {
                        if($obprestFinal->estado_pay == 'PS'){
                            $prestaciones_saldo[] = $obprestFinal->labelprestacion. " <span style='color: green'> $ ".$obprestFinal->cancelado_saldo."</span>";
                            $invalic++;
                        }
                        if($obprestFinal->estado_pay == 'PE'){
                            $prestaciones_pendientes[] = $obprestFinal->labelprestacion;
                            $invalic++;
                        }

                        #prestaciones que aun no estan realizadas
                        /*
                         * A estado Activo Pendiente
                         * P estado En Proceso
                         * R Realizada
                         * */
                        if($obprestFinal->estadodet == 'A' || $obprestFinal->estadodet == 'P'){
                            if($obprestFinal->estadodet == 'A') //pendiente
                                $prestacion_Norealizada[] = $obprestFinal->labelprestacion ."&nbsp;&nbsp; <span style='color: #008000'>(Pendiente)</span>";
                            if($obprestFinal->estadodet == 'P')
                                $prestacion_Norealizada[] = $obprestFinal->labelprestacion ."&nbsp;&nbsp; <span style='color: #488cd5'>(En Proceso)</span>";

                            $invalic++;
                        }

                        $nomb_tratamiento = "Plan de tratamiento N. ".$obprestFinal->numero;
                    }
                }else{
                    $error = "No hay prestaciones";

                }

                if($invalic > 0){

                    $error = 'no puede finalizar este tratamiento';

                    $puedeFinalizar .= '<h3 style="font-size: 2rem">Ud. No puede Finalizar '.$nomb_tratamiento.'</h3>';
                    $consultar .= '<br>'.$puedeFinalizar;

                    if($odontologo==0){
                        $consultar .= "<p><b>No tiene asociado un Doctor(a) a cargo</b></p>";
                    }

                    if(count($prestaciones_pendientes) > 0)
                    {
                        $prestacionesPend = implode(',', $prestaciones_pendientes);
                        $consultar .= '<p>
                                            <b>Prestaciones Pendientes y en Procesos</b>
                                            <br>'. (str_replace(',', '<br>', $prestacionesPend)) .'
                                        </p>';
                    }

                    if(count($prestaciones_saldo) > 0)
                    {
                        $prestacionesSaldo = implode(',', $prestaciones_saldo);
                        $consultar .= '<p>
                                            <b>Prestaciones Abonadas</b>
                                            <br>'. (str_replace(',', '<br>', $prestacionesSaldo)) .'
                                        </p>';
                    }

                    if(count($prestacion_Norealizada) > 0)
                    {
                        $prestacionesNorealizada = implode(',', $prestacion_Norealizada);
                        $consultar .= '<p>
                                            <b>Prestaciones No realizadas</b>
                                            <br>'. (str_replace(',', '<br>', $prestacionesNorealizada)) .'
                                        </p>';
                    }

                }else{

                    if( $error == '' )
                    {
                        $error     = '';
                        $consultar = '';

                        #Si todo esta ok se actualiza el plan de tratamiento a estado finalizado
                        $sqlUpdPlantCab = "UPDATE `tab_plan_tratamiento_cab` SET `estados_tratamiento`='F' WHERE `rowid`='$idplantram';";
                        $rsCanFinl = $db->query($sqlUpdPlantCab);

                        if(!$rsCanFinl){ $error = 'Ocurrio un error con el servidor con crear el proceso intentelo de nuevo';  }

                    }else{

                        $consultar = "<p style='font-weight: bolder; color: red'> Este plan de traramiento no contiene prestaciones asociadas</p>";

                    }
                }
            }

            $output = [
                'error'     => $error,
                'consultar' => $consultar
            ];
            echo  json_encode($output);
            break;


        case 'fecht_odontograma':

            $error = '';
            $dataPrincipal = array();
            $idpaciente    = GETPOST('idpaciente');
            $idtratamiento = GETPOST('idtratamiento');

            if($idpaciente != "" && $idtratamiento != "")
            {

                $sql = "SELECT *
                  , (select image_status from tab_odontograma_estados_piezas e where e.rowid=u.fk_estado_pieza) as img_status 
                  , (select descripcion from tab_odontograma_estados_piezas e where e.rowid=u.fk_estado_pieza) as nom_status 
                FROM tab_odontograma_update u WHERE u.fk_tratamiento = $idtratamiento and u.fk_paciente = $idpaciente ";
                $resul = $db->query($sql);
                if($resul->rowCount()>0){

                    while ($ob = $resul->fetchObject()){

                        $dataPrincipal[$ob->fk_diente] = $ob;

                        if(!empty($ob->img_status)){
                            $url=DOL_HTTP.'/application/system/pacientes/pacientes_admin/odontograma_paciente/img/'.$ob->img_status;
                            $imgbase64=base64_encode(file_get_contents($url));
                            $dataPrincipal[$ob->fk_diente]->img_status = ((!empty($ob->img_status))?'data:image/png; base64, '.$imgbase64 : '');
                        }

                    }
//                    die();
                }
            }else{
                $error = 'Ocurró un error inesperado, consulte con soporte Técnico';
            }


            $output = [
                'dataprincipal' => $dataPrincipal,
                'error' => $error,
            ];

            echo  json_encode($output);
            break;

            /*obntengo los estados del odontograma esto puedo variar ya que
            el doctor puede add nuevos estados
            pero no se puede eliminar los que vienen en el sistema*/
        case "estadodienteOdontograma":

            $error = "";
            $data = array();

            $rs = $db->query("SELECT * FROM tab_odontograma_estados_piezas");
            if($rs->rowCount()>0){
                while ($obj = $rs->fetchObject()){
                    $data[] = $obj;
                }
            }else{
                $error = 'Ocurrio un erro fetch_estados_odontograma consulte con soporte';
            }
            $output = [
               'error' => $error,
               'data'  => $data
            ];
            echo  json_encode($output);
            break;

        case "list_detalles_odont_estados":

            if(!PermitsModule('Odontograma', 'consultar')){
                $permits = " and 1<>1";
            }else{
                $permits = "";
            }

            $data = array();

            $start  = $_POST['start'];
            $length = $_POST['length'];

            $idtratamiento = GETPOST("idtratamiento");

            # lista de estado pertenesientes a ese tratamiento
            $sql = "SELECT 
                    d.rowid,
                    d.fk_diente , 
                    (select s.descripcion from tab_odontograma_estados_piezas s where s.rowid = d.fk_estado_diente) as estado,
                    d.list_caras,
                    d.fecha,
                    d.obsrvacion,
                    d.estado_anulado
                    FROM tab_odontograma_paciente_det d where rowid > 0 $permits";

            if(!empty($idtratamiento)){
                $sql .= " and  d.fk_tratamiento = $idtratamiento ";
            }

            $sql .= " order by d.rowid desc";

            $Total = $db->query($sql)->rowCount();

            if($start || $length){
                $sql.=" LIMIT $start,$length;";
            }


            $rs = $db->query($sql);
            if($rs->rowCount() > 0){

                while ($obj = $rs->fetchObject()){

                    $row = array();
                    if(!empty($obj->obsrvacion)) {
                        $observacion = "<b>observación:</b> ".$obj->obsrvacion;
                    }else{
                        $observacion = "";
                    }

                    if($obj->estado_anulado == 'A'){
                        $row[] = date("Y/m/d", strtotime($obj->fecha));
                        $row[] = $obj->fk_diente;
                        $row[] = str_replace(',',' , ', $obj->list_caras)  ;
                        $row[] = $obj->estado .'<span style="color: #0866a5; display: block" class="text-sm">'.$observacion.'</span>';
                        $row[] = "<a class='btn btn-xs btnhover ".((!PermitsModule('Odontograma', 'eliminar')?"disabled_link3":""))." ' style='font-weight: bold ; padding: 4px 8px; color:red; ' onclick='anular_estado_update($obj->rowid)'  >Anular</a>";
                    }

                    if($obj->estado_anulado == 'E'){
                        $row[] = "<strike title='".date("Y/m/d", strtotime($obj->fecha))."'>".date("Y/m/d", strtotime($obj->fecha))."</strike>";
                        $row[] = "<strike> ".$obj->fk_diente." </strike>";
                        $row[] = "<strike>".str_replace(',',' , ', $obj->list_caras)."</strike>"  ;
                        $row[] = "<strike>".$obj->estado ." ".$observacion."</strike>";
                        $row[] = "<a class='btn btn-xs btnhover disabled_link3' style='padding: 4px 8px; color:red '  >Anular</a>";
                    }


                    $data[] = $row;
                }
            }

            $resultado['datos'] = $data;
            $resultado['total'] = $Total;

            $output = [
                "data"            => $resultado['datos'],
                "recordsTotal"    => $resultado['total'],
                "recordsFiltered" => $resultado['total']
            ];

            echo json_encode($output);
            break;

        case 'nuevo_odontograma_detalle':

            $error= '';
            $informacion_detalle = GETPOST('info');

            $datos['fk_diente']         = $informacion_detalle['fk_diente'];
            $datos['caras_json']        = $informacion_detalle['datosPiezas'];
            $datos['fk_estadoDiente']   = $informacion_detalle['fk_estadoDiente'];
            $datos['fk_trataminto']     = $informacion_detalle['fk_trataminto'];
            $datos['observacion']       = $informacion_detalle['observacion'];
            $datos['labelCaras']        = explode(' ', $informacion_detalle['labelCaras']);

            $paciente->fk_diente                = $datos['fk_diente'];
            $paciente->json_caras               = $datos['caras_json'];
            $paciente->fk_estadosdientes        = $datos['fk_estadoDiente'];
            $paciente->observacionOdont         = $datos['observacion'];
            $paciente->listCaras                = implode(',', array_filter($datos['labelCaras'], 'strlen'));
            $paciente->fk_plantratamiento       = $datos['fk_trataminto'];
            $paciente->fechaDet                 = "now()";

            $error = $paciente->createOdontogramaDet();

            $output = [
                'error' => $error
            ];

            echo json_encode($output);
            break;

        case "odontograma_update":

            $error = '';

            $datos = array();
            $datos  = GETPOST("piezas");

            $fk_plantratamiento    =   GETPOST('fk_tratamiento');
            $fk_paciente           =   GETPOST('idpaciente');

            $sql_a = "SELECT * FROM tab_odontograma_update WHERE fk_tratamiento = $fk_plantratamiento and fk_paciente = $fk_paciente";
            $result_a = $db->query($sql_a);

            #si es mayor a 0 ose hay datos , eliminos los datos anteriores y ingreso los nuevo datos
            if($result_a->rowCount()>0)
            {
                $odong = $result_a->fetchObject()->rowid;
                $log->log($fk_plantratamiento, $log->crear, 'Se ha creado nuevo registro de Odontograma update del Plan de Tratamiento:#'.$fk_plantratamiento, 'tab_odontograma_update');

                $sql_b = "DELETE FROM `tab_odontograma_update` WHERE rowid > 0 and fk_tratamiento = $fk_plantratamiento and fk_paciente = $fk_paciente";
                $result_b  = $db->query($sql_b);

                if($result_b){
                    for ($i =0; $i <= count($datos) -1; $i++){

                        $val = $datos[$i];

                        $fkdiente       = $val['diente'];
                        $json_caras     = $val['caras'];
                        $fk_estadopieza = $val['estado_diente'];

                        $sql_c = "INSERT INTO `tab_odontograma_update` (`fk_diente`, `json_caras`, `type_hermiarcada`, `fk_estado_pieza`, `fk_tratamiento`, `fk_paciente`)";
                        $sql_c .= "VALUES(";
                        $sql_c .= "'$fkdiente' , ";
                        $sql_c .= "'".json_encode($json_caras)."' , ";
                        $sql_c .= "'no hay momentaneo' , ";
                        $sql_c .= "'$fk_estadopieza' , ";
                        $sql_c .= "'$fk_plantratamiento' , ";
                        $sql_c .= "'$fk_paciente' ";
                        $sql_c .= ")";
                        $result_c = $db->query($sql_c);

                        if(!$result_c){
                            $error += "Ocurrió un problema con la Operación, contacte con soporte tecnico";
                        }
                    }

                }else{
                    $error += "Ocurrió un problema con la Operación, contacte con soporte tecnico";
                }

            }else{ #caso contrario ingreso por primera vez

                for ($i =0; $i <= count($datos)-1; $i++){

                    $val = $datos[$i];

                    $fkdiente       = $val['diente'];
                    $json_caras     = $val['caras'];
                    $fk_estadopieza = $val['estado_diente'];

                    $sql_d = "INSERT INTO `tab_odontograma_update` (`fk_diente`, `json_caras`, `type_hermiarcada`, `fk_estado_pieza`, `fk_tratamiento`, `fk_paciente`)";
                    $sql_d .= "VALUES(";
                    $sql_d .= "'$fkdiente' , ";
                    $sql_d .= "'".json_encode($json_caras)."' , ";
                    $sql_d .= "'no hay momentaneo' , ";
                    $sql_d .= "'$fk_estadopieza' , ";
                    $sql_d .= "'$fk_plantratamiento' , ";
                    $sql_d .= "'$fk_paciente' ";
                    $sql_d .= ")";
                    $result_d = $db->query($sql_d);

                    if(!$result_d){
                        $error += "Ocurrió un problema con la Operación, contacte con soporte tecnico";
                    }
                }
            }

            $output = [
                'error' => $error
            ];

            echo json_encode($output);
            break;

        case 'anular_estado_odontogramadet':

            $error = '';
            $id = GETPOST('id');
            $sql = "UPDATE tab_odontograma_paciente_det set estado_anulado = 'E'  WHERE rowid = $id ";
            $rrs = $db->query($sql);
            if(!$rrs){
                $error = 'Ocurrió un error con la Operación Anular, Consulte con soporte Técnico';
            }

            $output = [
                'error' => $error
            ];

            echo json_encode($output);
            break;

        case 'editnametratamiento':

            $error = '';
            $id = GETPOST('id');
            $nametratamiento = GETPOST('name');

            if($id != 0){
                $sql = "UPDATE tab_plan_tratamiento_cab set edit_name = '$nametratamiento'  WHERE rowid = $id ";
                $rrs = $db->query($sql);
                if(!$rrs){
                    $error = 'Ocurrió un error con la Operación cambiar nombre del tratamiento, Consulte con soporte Técnico';
                }
            }else{
                $error = 'Ocurrió un error con la Operación cambiar nombre del tratamiento, Consulte con soporte Técnico';
            }

            $output = [
                'error' => $error
            ];

            echo json_encode($output);

            break;

        case 'confirm_eliminar_plantratamiento':

            $subaccion  = GETPOST('subaccion');
            $idplantcab = GETPOST('idplan');
            $idpaciente = GETPOST('idpaciente');

            $acierto = 0;

            $msgConfirm = "";
            $error = 0;
            $errores = '';

            if($subaccion == 'eliminar_plantcab_preguntar')
            {
                $sql_a = "SELECT  rowid,
                            fk_cita,
                            estados_tratamiento,
                            CONCAT('Plan de Tratamiento: N.', '', numero) numero,
                            edit_name, 
                            (SELECT SUM(pg.amount) AS amount FROM tab_pagos_independ_pacientes_det pg WHERE pg.fk_plantram_cab = p.rowid AND pg.fk_paciente = p.fk_paciente) AS saldo
                         FROM tab_plan_tratamiento_cab p where p.rowid = $idplantcab and p.fk_paciente = $idpaciente limit 1;";
                $result_ab = $db->query($sql_a);
                if( $result_ab->rowCount() > 0 )
                {
                    $objectplantram = $result_ab->fetchObject();

                    if( $objectplantram->estados_tratamiento == 'E' ) #ANULADO O ELIMINADO
                    {
                        $error++;
                        $errores = 'Este plan de tratamiento ya se encuentra ANULADO';
                    }

                    if( $objectplantram->estados_tratamiento == 'A' || $objectplantram->estados_tratamiento == 'S')
                    {
                        $nametram = ($objectplantram->edit_name == null) ? $objectplantram->numero : $objectplantram->edit_name;

                        if( $objectplantram->fk_cita  > 0 ) #asociado a una cita
                        {
                            $titulo = "<h3 style=\"font-size: 2rem\">Este ". $nametram ." esta asociado a una o varias citas</h3>";
                            $msgConfirm = " $titulo
                                                <b>Continuar con la Anulación</b><br>
                                                <small><b>Si plan de tratamiento esta asociado a un Odontograma Este Tambien se Eliminara</b></small>";
                        }

                        if( $objectplantram->fk_cita  == 0 )
                        {
                            $titulo = "<h3 style=\"font-size: 2rem\">Anulación de este " . $nametram . "</h3>";
                            $msgConfirm = "$titulo
                                                <b>Continuar con la Anulación</b><br>
                                                <span style='display: block'> <i class='fa fa-info-circle'></i> Si el Plan de Tratamiento está asociado a un Odontograma. Este también se Eliminara</small>";
                        }
                    }

                }else{

                    $error++;
                    $errores = 'Ese plan de tratamiento no existe';
                }
            }

            if($subaccion == 'confirm_eliminar')
            {

                #COMPRUEBO SI EN EL PLAN D ETRATAMIENTO  TIENE PRESTACIONES ASOCIADAS O SALDO
                $sqlplantrCab = "SELECT p.rowid,
                                p.fk_cita,
                                p.estados_tratamiento,
                                CONCAT('Plan de Tratamiento N.', p.numero, ' | Paciente: ',(select concat(ad.nombre,' ',ad.apellido) from tab_admin_pacientes ad where ad.rowid = $idpaciente limit 1)) numero,
                                p.edit_name,
                                (SELECT 
                                        SUM(pg.amount) AS amount
                                    FROM
                                        tab_pagos_independ_pacientes_det pg
                                    WHERE
                                        pg.fk_plantram_cab = p.rowid
                                            AND pg.fk_paciente = p.fk_paciente) AS saldo , 
                                            
                                (select count(*) from tab_plan_tratamiento_det d where d.fk_plantratam_cab = p.rowid and d.estadodet = 'R') as prestaciones_realizadas
                                
                                FROM 
                                tab_plan_tratamiento_cab p where p.rowid = $idplantcab and p.fk_paciente = $idpaciente limit 1";
                $rsplanCab = $db->query($sqlplantrCab);
                if( $rsplanCab && $rsplanCab->rowCount() > 0 )
                {

                    $objPlanCab = $rsplanCab->fetchObject();

                    $puedeAnular = 0;
                    $prestacionesRealizadas = "";
                    $tieneSaldo = "";
                    $EstadoTratamiento = "";

                    if( $objPlanCab->prestaciones_realizadas > 0 ){
                        $puedeAnular++;
                        $prestacionesRealizadas .= " Tiene ". $objPlanCab->prestaciones_realizadas ." prestaciones realizadas";
                    }
                    if( $objPlanCab->saldo > 0 ){
                        $puedeAnular++;
                        $tieneSaldo .= " Tiene Saldo Asociado <i class='fa fa-dollar'> </i> ".number_format($objPlanCab->saldo, 2,'.', '');
                    }

                    if($objPlanCab->estados_tratamiento == 'F'){
                        $puedeAnular++;
                        $EstadoTratamiento .= "<i class='fa fa-flag'></i>  Este plan de tratamiento se encuentra en estado Finalizado ";
                    }

                    if($puedeAnular > 0){

                        $acierto = 0; #negativo no se puede eliminar
                        $msgConfirm = "<div class='form-group col-lg-12 col-xs-12'>
                                                <b><i class='fa fa-warning'></i></b> 
                                                    <p>No se puede <b>anular</b> este plan de tratamiento  ". $prestacionesRealizadas ." ". $tieneSaldo ."</p>    
                                            </div>";
                    }

                    if( $puedeAnular == 0)
                    {

                        $acierto++;  #Anular

                        #UPDATE ESTADO ANULADO
                        $sqlupdatPlant = "UPDATE `tab_plan_tratamiento_cab` SET `estados_tratamiento`='E' , situacion = 'ANULADO' WHERE `rowid`='$idplantcab';";
                        $delUpd = $db->query($sqlupdatPlant);
                        if(!$delUpd) {
                            $error++;
                            $errores = "Ocurrio un error con la eliminación Consulte con soporte";

                        }else{
                            $log->log($idplantcab, $log->eliminar, 'Se ha Anulado el registro '.$objPlanCab->numero, 'tab_plan_tratamiento_cab');
                        }

                        #ELIMINACION DE PLAN DE TRATAMIENTO
                        /*
                        $sqldelcab = "DELETE FROM `tab_plan_tratamiento_cab` WHERE `rowid`='$idplantcab' and fk_paciente = $idpaciente;";
                        $delrcab = $db->query($sqldelcab);

                        if($delrcab){

                            $sqldeldet = "DELETE FROM `tab_plan_tratamiento_det` WHERE `rowid` > 0 and fk_plantratam_cab = $idplantcab ;";
                            $db->query($sqldeldet);

                        }else{
                            $error++;
                            $errores = "Ocurrio un error con la eliminación Consulte con soporte";
                        }*/


                    }
                }

            }


            $output = [
                'error' => $error , 'errores' => $errores , 'msgConfirm' => $msgConfirm, 'acierto'=> $acierto
            ];

            echo json_encode($output);
            break;


        case 'update_observacion':

            $error = '';
            $idplantcab = GETPOST('idplantram');
            $comment = GETPOST('observacion');

            if(!empty($idplantcab)){

                $sqlupdateObserv = "UPDATE `tab_plan_tratamiento_cab` SET `observacion`='$comment' WHERE `rowid`='$idplantcab'";
                $db->query($sqlupdateObserv);

            }else{
                $error = 'Ocurrio un error no se puede guardar el comentario';
            }

            $output = [
                'error' => $error ,
            ];

            echo json_encode($output);
            break;


        case 'evolucion_listprincpl':

            $error = '';

            $idpaciente = GETPOST('idpaciente');
            $idplantram = GETPOST('idplant');
            $date       = GETPOST('date');

            $datos['idpaciente'] = $idpaciente;
            $datos['idplan']     = $idplantram;
            $datos['date']       = $date;

            $respuesta = evoluc_listprincpl($datos);

            $output = array(
                "data"            => $respuesta['datos'],
                "recordsTotal"    => $respuesta['total'],
                "recordsFiltered" => $respuesta['total']
            );

            echo json_encode($output);
            break;


        #Actualiza la prestaciones  del plan de tratamiento ----------------------
        case 'UpdateStatusPrestacion':

            if(!PermitsModule('Planes de Tratamientos', 'modificar')){
                $permits=false;
            }else{
                $permits=true;
            }
            $error = '';
            $iddetTratamiento =  GETPOST("iddetTratm");

            if($permits){
                $resul = $db->query("SELECT  rowid , fk_plantratam_cab , estadodet FROM tab_plan_tratamiento_det WHERE rowid = ".$iddetTratamiento);
                if($resul && $resul->rowCount()>0){

                    $objtratmdet = $resul->fetchObject();

                    if($objtratmdet->estadodet=='R'){
                        $error = 'Ya se encuentra en estado <b>REALIZADO</b>';
                    }
                    if($objtratmdet->estadodet=='P'){
                        $error = 'Ya se encuentra en estado <b>EN PROCESO</b>';
                    }

                    if($objtratmdet->estadodet=='A'){

                        $comment_status = 'Cambio de estado (EN PROCESO) de la Prestación por el Usuario: '.$user->name ." | Plan de Tratamiento N.".str_pad($objtratmdet->fk_plantratam_cab, 6, "0", STR_PAD_LEFT);
                        $result = $db->query("UPDATE `tab_plan_tratamiento_det` SET `estadodet`='P' , `comment_laboratorio_auto` = '".$comment_status."', `date_recepcion_status_tramient`= now()   WHERE `rowid`=".$iddetTratamiento."; ");
                        if(!$result){
                            $error = 'Ocurrio un error con la Operación Actualizar Estado';
                        }else{
                            $log->log($iddetTratamiento, $log->modificar, 'Se ha actualizado el registro | '.$comment_status, 'tab_plan_tratamiento_det');
                        }
                    }

                }else{
                    $error = 'No se encontro esta Prestacion <small>compruebe la Información</small>  ';
                }
            }else{
                $error = 'Ud. No tiene permiso para realizar esta Operación';
            }

            $output = [
               'error' => $error
            ];
            echo json_encode($output);
            break;


        case 'UpdateOdontolTratamiento':

            $error = '';
            $idTratamiento = GETPOST('idTratamiento');
            $idOdontol = GETPOST('idOdontol');

            if($idTratamiento=="" || $idTratamiento==0)
                $error = "Ocurrio un error  obteniendo los parámetros <br> <b>Vuelva a intentarlo o consulte con soporte Tecnico</b>";
            if($idOdontol=="" || $idOdontol == 0)
                $error = "Ocurrio un error con la Operación <br> No se identifica el profecional a cargo selecionado <br> compruebe la información o consulte con soporte ";


//            print_r($idTratamiento); die();
            if(empty($error)){

                $valid = "select count(*) as count from tab_plan_tratamiento_cab where rowid = $idTratamiento and fk_doc = 0";
                $countValid = $db->query($valid)->fetchObject()->count;

//                print_r($valid); die();
                if($countValid==1){
                    $update = "UPDATE `tab_plan_tratamiento_cab` SET `fk_doc`= $idOdontol WHERE `rowid`= $idTratamiento;";
                    $result = $db->query($update);
                    if(!$result){
                        $error = $messErr;
                    }
                }else{
                    $error = "No puede asociar un <b>Odontolog@</b> a este plan de tratamiento <br> Registro ya se encuentra Asociado";
                }
            }

            $output = [
                'error' => $error
            ];
            echo json_encode($output);
            break;

        case 'filtrarPlantratamientoSearchSelect2':

            $data=[];
            $paciente_id=GETPOST('paciente_id');
            $search = GETPOST('search');

            $sql = "SELECT 
                        c.rowid , 
                        cast(c.fecha_create as date) as date_c , 
                        concat('Plan de Tratamiento: N.', c.numero, ' - Doc(a): ', ifnull( (select concat( od.nombre_doc , ' ', od.apellido_doc ) as nomb from tab_odontologos od where od.rowid = c.fk_doc), 'No asignado'), ' ') as plantram
                    FROM tab_plan_tratamiento_cab c where 1=1 
                    ";
            if($paciente_id != 0){
                $sql .= " and  c.fk_paciente = $paciente_id  ";
            }
            if(!empty($search)){
                $sql .= " and replace((concat('Plan de Tratamiento ', 'N. ', c.numero , ' ', concat('Doc(a) ', ' ', ifnull( (select concat( od.nombre_doc , ' ', od.apellido_doc ) as nomb from tab_odontologos od where od.rowid = c.fk_doc), 'No asignado')))) , ' ','') like '%".(str_replace(' ','', $search))."%' ";
            }

            $sql .= " limit 5";
//            print_r($sql); die();

            $results = $db->query($sql);
            if($results && $results->rowCount()>0){
                $results = $results->fetchAll(PDO::FETCH_ASSOC);
                foreach ($results as $value){
                    $data[] = array('id' => $value['rowid'], 'text' => $value['plantram'].' '.(!empty($value['date_c'])?' - Emitido: '.(date("Y/m/d", strtotime($value['date_c']))):''));
                }
            }

            $output=[
                'results' => $data
            ];
            echo json_encode($output);
            break;


        case 'CitasAgendadasSearchSelect2':

            $data=[];
            $paciente_id = GETPOST('paciente_id');

            if($paciente_id!=0){
                $sql = "SELECT 
                    d.fk_especialidad,
                    d.fk_doc,
                    d.rowid AS id_cita_det,
                    cast(c.fecha_create as date) date_c, 
                    (concat('C_', lpad('0',(5-length(d.rowid)),'0' ),d.rowid, '  Doc(a): ' , 
			                    concat(o.nombre_doc, ' ', o.apellido_doc), ' - Especialidad: ', IFNULL((SELECT s.nombre_especialidad FROM tab_especialidades_doc s WHERE s.rowid = d.fk_especialidad), 'General'), ' - Emitido: ', replace(cast(c.fecha_create as date),'-','/') )) as label_cita
                FROM
                    tab_pacientes_citas_det d,
                    tab_pacientes_citas_cab c,
                    tab_odontologos o 
                WHERE
                    d.fk_pacient_cita_cab = c.rowid
                    and o.rowid = d.fk_doc
                    and c.fk_paciente = $paciente_id
                    ";
                if(GETPOST('search')!=""){
                    $search = GETPOST('search');
                    $sql   .= " and (concat('C_', lpad('0',(5-length(d.rowid)),'0' ),d.rowid, '  Doc(a): ' , 
			                    concat(o.nombre_doc, ' ', o.apellido_doc),' - Especialidad: ', IFNULL((SELECT s.nombre_especialidad FROM tab_especialidades_doc s WHERE s.rowid = d.fk_especialidad), 'General') )) like '%$search%' ";
                }
                $sql .= " limit 4 ";
//                print_r($sql); die();
                $result = $db->query($sql);
                while ($object = $result->fetchObject()){
                    $data[] = array('id' => $object->id_cita_det, 'text' => $object->label_cita);
                }

            }else{
                $data=[];
            }

            $output=[
                'results' => $data
            ];
            echo json_encode($output);
            break;


        case 'listTramnCitasAsoc':

            $data=[];
            $tratamiento_id = GETPOST("tratamiento_id");
            $start  = $_POST['start'];
            $length = $_POST['length'];

//            die();
            $total = $db->query("SELECT  count(*) as num_rows FROM 
                                tab_pacientes_citas_cab c , 
                                tab_pacientes_citas_det d , 
                                tab_plan_asoc_tramt_citas asoc  
                                where c.rowid = d.fk_pacient_cita_cab
                                and asoc.fk_cita = d.rowid
                                and asoc.fk_tratamiento = $tratamiento_id")->fetchObject()->num_rows;


            $sql = "SELECT 
                        concat('', lpad('0',(5-length(d.rowid)),'0'),d.rowid) as numberCitas ,
                        d.fecha_cita  as fecha_cita,         
                        d.hora_inicio , 
                        d.hora_fin ,
                        d.rowid  as id_cita_det,
                        (select concat(o.nombre_doc,' ', o.apellido_doc) from tab_odontologos o where o.rowid = d.fk_doc) as doct ,
                        (select concat(s.text) from tab_pacientes_estado_citas s where s.rowid = d.fk_estado_paciente_cita) as estado,
                        (select s.color from tab_pacientes_estado_citas s where s.rowid = d.fk_estado_paciente_cita) as color,
                        d.fk_estado_paciente_cita , 
                        c.comentario ,
                        ifnull((select es.nombre_especialidad FROM tab_especialidades_doc es where es.rowid = d.fk_especialidad),'General') as especialidad,
                        d.fk_doc as iddoctor , 
                        d.comentario_adicional as comentario_adicional,
                        c.fk_paciente as idpaciente  ,
                         -- validaciones
                         -- citas atrazados con estado no confirmado
                         IF( now() > CAST(d.fecha_cita AS DATETIME)  
                                    && d.fk_estado_paciente_cita in(2,1,3,4,7,8,9,10,11,5,  (select statusc.rowid from tab_pacientes_estado_citas statusc where statusc.system=0) )  , 
                                        concat('Atrasada ', (select concat(s.text) from tab_pacientes_estado_citas s where s.rowid = d.fk_estado_paciente_cita) , 
                                                '<br> Fecha : ' , date_format(d.fecha_cita, '%Y/%m/%d') , '<br>Hora: ' , d.hora_inicio ,' a ' , d.hora_fin) , ''
                                                ) as cita_atrazada   
                     FROM 
                        tab_pacientes_citas_cab c , 
                        tab_pacientes_citas_det d , 
                        tab_plan_asoc_tramt_citas asoc  
                        where c.rowid = d.fk_pacient_cita_cab
                        and asoc.fk_cita = d.rowid
                        and asoc.fk_tratamiento = ".$tratamiento_id;

            if($start || $length){
                $sql .= " limit $start, $length";
            }

//            $ico_cita = "<img src='"."data: image/png; base64, ".base64_encode(file_get_contents(DOL_HTTP.'/logos_icon/logo_default/cita-medica.ico'))."' style='width: 25px; height: 25px'>";

            $result = $db->query($sql);
            if($result){
                if($result->rowCount()>0){
                    while ($object = $result->fetchObject()){

                        $row = [];
                        $row[] =  "<span style='font-weight: bold'>C_".$object->numberCitas."</span>";
                        $row[] = $object->especialidad;
                        $row[] = date("Y/m/d", strtotime($object->fecha_cita))."   ".$object->hora_inicio;
                        $row[] = "<span style='font-weight: bold; background-color: $object->color '>".$object->estado."</span>";

                        $data[] = $row;
                    }
                }
            }

            $output=[
                "draw" => $_POST["draw"],
                "data" => $data,
                "recordsTotal"    => $total,
                "recordsFiltered" => $total

            ];
            echo json_encode($output);
            break;


        case 'prestacionesSearchSelect2':


            $search = $_POST['search'];

            $result = fetchPrestacionGroupLab(null, $search);

            $output=[
                "results" => $result
            ];
            echo json_encode($output);
            break;


        case 'pagosxpacientes_prestaciones':

            $data = [];


            $start          = $_POST["start"];
            $length         = $_POST["length"];

            $idpaciente      = GETPOST("idpaciente");
            $iddetalle       = GETPOST("iddetalle");
            $idcabtranmiento = GETPOST("idtratamiento");

            $fk_prestacion = "";
            $fk_pieza      = "";

            $query = "SELECT            

                pc.rowid,
                pc.n_fact_boleta, 
                cast(pd.feche_create as date) as emitido,
                dt.rowid iddetplantram,
                ct.rowid idcabplantram,   
                dt.fk_prestacion , 
                ct.fk_paciente as paciente,  
                dt.fk_diente as diente,           
                dt.estado_pay , 
                cp.descripcion prestacion ,  
                dt.estadodet ,
                ROUND(dt.total, 2) AS totalprestacion , 
                ifnull(round(sum(pd.amount),2),0) as abonado, 
                (select t.nom from tab_bank_operacion t where t.rowid = pd.fk_tipopago) as forma_pago ,
                (select s.usuario from tab_login_users s where s.rowid = pc.id_login) user_athor
            FROM
                tab_conf_prestaciones            as cp ,
                tab_plan_tratamiento_cab         as ct ,
                tab_plan_tratamiento_det         as dt ,
                tab_pagos_independ_pacientes_det as pd ,
                tab_pagos_independ_pacientes_cab as pc
            WHERE
                ct.rowid                = dt.fk_plantratam_cab
                AND pd.fk_plantram_cab  = ct.rowid
                AND pd.fk_plantram_det  = dt.rowid
                AND pc.rowid            = pd.fk_pago_cab
                AND cp.rowid            = dt.fk_prestacion
                AND pd.fk_plantram_det  = $iddetalle 
                AND ct.fk_paciente      = $idpaciente
                AND ct.rowid            = $idcabtranmiento
                AND pd.estado           = 'A' ";
            $query .= " group by dt.rowid, pd.rowid  ";

            if($start || $length){
                $query .= " LIMIT $start,$length;";
            }

//            print_r($query); die();
            $Total = $db->query($query)->rowCount();
            $result = $db->query($query);
            if($result){
                if($result->rowCount()>0){
                    $resultData = $result->fetchAll(PDO::FETCH_ASSOC);
                        foreach ($resultData as $value){

                            if($value['user_athor']!="")
                                $user_author = "<small class='text-blue' style='display: block'> autor user: ".$value['user_athor']."</small>";
                            else
                                $user_author = "";


                            $row = [];
                            $row[] = date("Y/m/d", strtotime($value['emitido']));
                            $row[] = $value['forma_pago'];
                            $row[] = 'P_'.str_pad($value['rowid'], 6, "0", STR_PAD_LEFT);
                            $row[] = $value['n_fact_boleta'].$user_author;
                            $row[] = number_format($value['abonado'],2,'.','');

                            $data[] = $row;
                        }
                }
            }

            $output=[
                "draw" => $_POST['draw'],
                "data" => $data,
                "recordsTotal"    => $Total,
                "recordsFiltered" => $Total

            ];
            echo json_encode($output);
            break;


    }
}

function CrearInsertDirecFicheroPaciente($cabezera = array(), $detalle = array(), $nombreCarpeta)
{

    global $db;

//    print_r($cabezera); die();

    $n = 0;
    if(count($cabezera) > 0)
    {

        $fk_doc      = $cabezera[0];
        $comment     = $cabezera[2];
        $titulo      = $cabezera[3];
        $fk_paciente = $cabezera[4];

        $sql = "INSERT INTO `tab_fichero_pacientes_cab` 
            (`fecha_creat`, `fk_doc`, `fk_paciente`, `titulo`, `comment`) 
            VALUES (now(), '$fk_doc',$fk_paciente,'$titulo','$comment' );";

        $db->query($sql);

        $n++;
    }

    $id = $db->lastInsertId('tab_fichero_pacientes_cab');

    if( !empty($id) )
    {
        if(count($detalle) > 0)
        {
            for ($i = 0; $i<= count($detalle)-1; $i++)
            {
                $fichero = $detalle[$i]['name']; //nombre del fichero
                $tipo    = $detalle[$i]['type']; // typo fichero

                $sql = "INSERT INTO `tab_fichero_pacientes_det` (`fk_fichero_paciente_cab`, `ruta_fichero`, `name_direct`, `type` ) VALUES ($id, '$fichero', '$nombreCarpeta', '$tipo');";
                $db->query($sql);
            }
        }


        $n++;
    }

    return $n;

}


function listcitas_admin($idPaciente, $fechaInicio, $fechafin, $n_citas, $EstadosLis)
{


    global $db, $conf, $user;

    if(!PermitsModule('Citas Asociadas', 'consultar')){
        $permits = " 1<>1 ";
    }else{
        $permits = " 1=1 ";
    }

    $Total          = 0;
    $start          = $_POST["start"];
    $length         = $_POST["length"];

    $fecha_hoy = date('Y-m-d');

    $data = array();

    $sql = "select 
                date_format(d.fecha_cita, '%Y-%m-%d')  as fecha_cita,
                c.rowid as id_cita_cab ,
                d.hora_inicio , 
                d.hora_fin ,
                d.rowid  as id_cita_det,
                concat(p.nombre ,' ',p.apellido) as paciente,
                p.rowid as idpaciente,
                concat(o.nombre_doc,' ', o.apellido_doc) as doct,
                s.text as estado,
                s.color,
                d.fk_estado_paciente_cita , 
                c.comentario ,
                IFNULL(es.nombre_especialidad , 'General') as especialidad,
                p.telefono_movil,
                -- validaciones
                -- citas atrazada con estado no confirmado
                IF( now() > CAST(d.fecha_cita AS DATETIME)  
                                        && d.fk_estado_paciente_cita in(2,1,3,4,7,8,9,10,11,5,  (select statusc.rowid from tab_pacientes_estado_citas statusc where statusc.system=0) )  , 
                                            concat('Atrasada ', (select concat(s.text) from tab_pacientes_estado_citas s where s.rowid = d.fk_estado_paciente_cita) , 
                                                    ' | Fecha : ' , date_format(d.fecha_cita, '%Y/%m/%d') , ' | Hora: ' , d.hora_inicio ,' h ' , d.hora_fin) , ''
                                                    ) as cita_atrazada
         
             FROM
         
                tab_pacientes_citas_cab c 
                    inner join
                tab_pacientes_citas_det d on d.fk_pacient_cita_cab = c.rowid
                    inner join 
                tab_admin_pacientes p on c.fk_paciente = p.rowid
                    inner join
                tab_pacientes_estado_citas s on s.rowid = d.fk_estado_paciente_cita
                    inner join
                tab_odontologos o on o.rowid = d.fk_doc
                    left join
                tab_especialidades_doc es on es.rowid = d.fk_especialidad
             where $permits";

    if(!empty($EstadosLis))
        $sql .= " and d.fk_estado_paciente_cita in($EstadosLis) ";

    if(!empty($idPaciente))
        $sql .= "  and c.fk_paciente = $idPaciente";

    if(!empty($fechaInicio) && !empty($fechafin))
        $sql .= "  and cast(d.fecha_cita as date) between cast('$fechaInicio' as date) and cast('$fechafin' as date)";

    if(!empty($n_citas))
        $sql .= " and d.rowid like '%$n_citas%' ";

    $sql .= " order by d.fecha_cita desc ";
    $sqlTotal = $sql;

    if($start || $length)
        $sql.=" LIMIT $start,$length;";

//    print_r($sql); die();
    $resultTotal = $db->query($sqlTotal);
    $res = $db->query($sql);
    if($res->rowCount()>0){

        $Total = $resultTotal->rowCount();

        while ($obj = $res->fetchObject()){

            $row = array();

            #Citas Atrazadas
            $citas_atrazadas = "";
            if( $obj->cita_atrazada != "") {
                $citas_atrazadas = '<small style="white-space: pre-wrap;  color: red; display: block; font-weight: none"  class="" title="'. $obj->cita_atrazada .'">'.$obj->cita_atrazada.'</small>';
            }

//            $iconCita = 'data:image/*; base64, '.base64_encode(file_get_contents(DOL_DOCUMENT.'/logos_icon/logo_default/cita-medica.ico'));
//            <img  src='". $iconCita. "' class='img-rounded' style='width: 20px; height: 20px' >

            $numero_cita_asociada = "<table>
                                        <tr> 
                                            <td title='número de cita' > </td>
                                            <td style='font-weight: bold'> C_".(str_pad($obj->id_cita_det, 5, "0", STR_PAD_LEFT))." </td> 
                                        </tr>
                                     </table>";


            $list_ptranm = []; //lista de plan de tratamientos asociados
            $sql_a = "SELECT 
                    concat('Plan de Tratamiento: N.',tc.numero) as plantratamiento , 
                    aso.fk_cita
                FROM
                    tab_plan_asoc_tramt_citas aso
                    inner join 
                    tab_plan_tratamiento_cab tc on tc.rowid = aso.fk_tratamiento
                    where aso.fk_cita = ".$obj->id_cita_det." limit 20";
            $result_a = $db->query($sql_a)->fetchAll(PDO::FETCH_ASSOC);
            if(count($result_a)>0){
                foreach ($result_a as $item){
                    $list_ptranm[] = $item['plantratamiento'];
                }
            }

            if(count($list_ptranm)>0)
                $lista_p = implode(", ", $list_ptranm);
            else
                $lista_p = "";

            //Fecha Hora de cita
            $row[]  = "<span style='white-space: pre-wrap;'> <b>".date('Y/m/d', strtotime($obj->fecha_cita))."</b>\n<b><small style='padding: 3px; background-color: #eaecee; font-weight: bold '> <i class='fa fa-clock-o'></i> &nbsp; ".$obj->hora_inicio." - ".$obj->hora_fin."</small></b> </span>";


            $row[]  = "<span style='white-space: pre-wrap;'>".$obj->especialidad."\n<b>Doctor(a):</b>$obj->doct<span>";
            $row[]  = $numero_cita_asociada;
            $row[]  = (($obj->comentario == "")?"":$obj->comentario).$citas_atrazadas."<small style='display: block;' class='text-blue' title='$lista_p'>$lista_p</small>";
            $row[]  = "<label class='control-label' style='background-color: $obj->color !important;  color: #333333; margin-top: 3%; padding: 5px;'> $obj->estado </label>";
            $row[]  = "";
            $data[] = $row;
        }
    }

    $resultFinal = [
        'datos' => $data,
        'total' => $Total
    ];

    return $resultFinal;

}

//lista de documentos filtrados
function info_type_document_pacient($idpaciente="")
{

    global  $db;

    $permisoConsultar = (!PermitsModule(4,1))?" and 1<>1 ":"";


    $data = array();

    $sql = "
            SELECT 
                cl.rowid AS rowid,
                cl.tms AS fecha,
                cl.label AS label,
               
                -- loging del creador del documento
                (SELECT 
                        (SELECT 
                                    CONCAT(o.nombre_doc, ' ', apellido_doc)
                                FROM
                                    tab_odontologos o
                                WHERE
                                    o.rowid = s.fk_doc) AS labl
                    FROM
                        tab_login_users s
                    WHERE
                        s.rowid = cl.fk_usuario_logeado) AS login_creador_document,
                        
                        
                -- id tipo de documento
                cl.fk_document_clinico AS fk_documento,
                -- id del documento
                cl.fk_document_det
                
        FROM
            tab_documentos_clinicos_admin cl
        WHERE
            cl.rowid > 0 ";

    $sql .= " and cl.fk_paciente =".$idpaciente;
    $sql .= $permisoConsultar;

    $resul = $db->query($sql);

    if($resul->rowCount()>0)
    {
        while ($Fila = $resul->fetchObject())
        {
            $row = array();


            $row[] = date("Y/m/d", strtotime($Fila->fecha));
            $row[] = $Fila->label;
            $row[] = "Creado x Doc(a) " . (($Fila->login_creador_document=="") ? "No asignado": $Fila->login_creador_document);
            $row[] = "<div class='form-group col-md-12 col-xs-12'>
                        <ul class='list-inline pull-right'>
                           
                            <li class='btn lipdf'    data-idtipo='$Fila->fk_documento'  data-iddocument='$Fila->fk_document_det' id='impr_pdf'>
                                 <a href='#' class='impripdf' >  <img src='". DOL_HTTP ."/logos_icon/logo_default/pdf.png' alt='Export - pdf' class='img-rounded img-md'> </a> 
                            </li>
                            
                            <li class='btn liexcel '  data-idtipo='$Fila->fk_documento'  data-iddocument='$Fila->fk_document_det'> 
                                <a href='#' class='disabled_link3'> <img src='". DOL_HTTP ."/logos_icon/logo_default/xls.png' alt='Export - pdf' class='img-rounded img-md'> </a> 
                            </li>
                             
                            <li class='btn limodificar'  data-idtipo='$Fila->fk_documento'  data-iddocument='$Fila->fk_document_det'> 
                                <a href='" . DOL_HTTP . "/application/system/pacientes/pacientes_admin/?view=docummclin&key=". KEY_GLOB ."&id=". tokenSecurityId( $idpaciente ) ."&v=docum_clin&dt=". $Fila->fk_documento ."&iddocmnt=". $Fila->fk_document_det ."'> 
                                    <img src='". DOL_HTTP ."/logos_icon/logo_default/modificar-icon.png' alt='Export - pdf' class='img-rounded img-md'> 
                                </a> 
                            </li>
                            
                        </ul>
                      </div>";


            $row[] = $Fila->fk_documento; //id del documento
            $row[] = $Fila->fk_document_det; #id del documento detallado "tab_documentos_clinicos_admin"
            $row[] = $Fila->rowid; #Id de la cabezera principal tabla del documento tab_documentos_ficha_clinica o puede ser diferentes tablas

            $data[] = $row;
        }

    }

    return $data;

}


function realizarPrestacionupdate($datos = array(), $detalle_tratamiento)
{
    global  $db , $conf, $user, $log;

    if( count($datos) != 0)
    {
        $sqlrealizar =  "INSERT INTO `tab_evolucion_plantramiento` (`fecha_create`, `fk_paciente`, `fk_plantram_cab`, `fk_plantram_det`, `observacion`, `fk_diente`, `json_caras`, `estado_diente`, `fk_doctor`, `id_login`)";
        $sqlrealizar .= "VALUES (";
        $sqlrealizar .= " now() ,";
        $sqlrealizar .= " $datos->fk_paciente , ";
        $sqlrealizar .= " $datos->fk_plantram_cab , ";
        $sqlrealizar .= " $datos->fk_plantram_det , ";
        $sqlrealizar .= " '$datos->observacion' , ";
        $sqlrealizar .= " $datos->iddiente , ";
        $sqlrealizar .= " '$datos->json_caras ', ";
        $sqlrealizar .= " ".(empty($datos->estadodiente)?0:$datos->estadodiente)." , ";
        $sqlrealizar .= " $datos->fk_doctor , ";
        $sqlrealizar .= " $datos->idlogin  ";
        $sqlrealizar .= " ) ";

        $rs = $db->query($sqlrealizar);

        if(!$rs){
            return 'Ocurrio un error no se realizar la evolución';

        }else{

            $comment_status_auto = "Se ha actualizado el registro | Cambio de estado (EN REALIZADO) de la Prestación $detalle_tratamiento->descripcion  por el Usuario: ".$user->name ." | Plan de Tratamiento N.".str_pad($datos->fk_plantram_cab, 6, "0", STR_PAD_LEFT);

            $sqlUpdattramm =  "UPDATE `tab_plan_tratamiento_det` SET";
            $sqlUpdattramm .= "  `estadodet`       = 'R'  ," ;
            $sqlUpdattramm .= "  `realizada_fk_dentista`    = $datos->fk_doctor  ," ;
            $sqlUpdattramm .= "  `evolucion_escrita`        ='$datos->observacion' , " ;
            $sqlUpdattramm .= "  `fk_estado_odontograma`    = ".(empty($datos->estadodiente)?0:$datos->estadodiente)."  ," ;
            $sqlUpdattramm .= "  `comment_laboratorio_auto` = '$comment_status_auto' , " ;
            $sqlUpdattramm .= "  `date_recepcion_status_tramient` = now()  " ;
            $sqlUpdattramm .= "   WHERE `rowid`= $datos->fk_plantram_det ";

            $rsUp = $db->query($sqlUpdattramm);
            if(!$rsUp){
                return 'Ocurrion un error con la Operación Evolución';
            }else{
                $log->log($datos->fk_plantram_det, $log->modificar, $comment_status_auto, 'tab_plan_tratamiento_det');
            }
        }

    }

    return '';

}

#LIST EVOLUCIONES PRINCIPAL
function evoluc_listprincpl($datos)
{
    global  $db;
    
    if(!PermitsModule('Evoluciones', 'consultar')){
        $permits = " and 1<>1";
    }else{
        $permits = " and 1=1";
    }
    $data = array();
    $Total          = 0;
    $start          = $_POST["start"];
    $length         = $_POST["length"];


    $sqlevolucprip = "SELECT 
                        concat('Plan de Tratamiento ', 'N. ', c.numero) plantram ,
                        concat('<b>edit:</b> ',c.edit_name) as edit_name , 
                        ev.fecha_create fechaevul ,
                        cp.descripcion as presstacion, 
                        ev.fk_diente as diente , 
                        (select concat( o.nombre_doc , ' ', o.apellido_doc ) from tab_odontologos o where o.rowid = ev.fk_doctor) as doct , 
                        ev.observacion , 
                        ifnull((select odes.descripcion from tab_odontograma_estados_piezas odes where odes.rowid = ev.estado_diente), 'Estado no asignado' )as estadodiente , 
                        ev.json_caras
                    FROM
                        tab_evolucion_plantramiento ev , 
                        tab_plan_tratamiento_cab c , 
                        tab_plan_tratamiento_det d , 
                        tab_conf_prestaciones cp
                    WHERE
                        ev.fk_plantram_cab  = c.rowid and 
                        ev.fk_plantram_det  = d.rowid and 
                        d.fk_prestacion     = cp.rowid and ";
    $sqlevolucprip .= " ev.fk_paciente =  " . $datos['idpaciente'] . "  ";

    if( !empty( $datos['idplan'] ) ){
        $sqlevolucprip .= " and ev.fk_plantram_cab =  " . $datos['idplan'] . "  ";
    }
    if( !empty( $datos['date']  ) ){
        $datex1 = str_replace('/','-', explode('-',$datos['date'])[0]);
        $datex2 = str_replace('/','-', explode('-',$datos['date'])[1]);
        $sqlevolucprip .= " and cast(ev.fecha_create as date) between '".$datex1."' and '".$datex2."' ";
    }

    $sqlevolucprip .=  $permits ;
    $sqlevolucprip .= " order by ev.rowid desc";

    $sqlTotal = $sqlevolucprip;

    if($start || $length){
        $sqlevolucprip.=" LIMIT $start,$length;";
    }


    $resultTotal = $db->query($sqlTotal);
    $Total = $resultTotal->rowCount();
    $rsevol = $db->query($sqlevolucprip);

    if( $rsevol && $rsevol->rowCount() > 0){
        while ( $objevol =   $rsevol->fetchObject() ) {

//            $edit = "<small class='text-blue' style='display: block'>".$objevol->edit_name."</small>";

            $cadena_caras = array();
            $caras = json_decode($objevol->json_caras);

            $cadena_caras[] = ($caras->vestibular=="true") ? "vestibular" : "";
            $cadena_caras[] = ($caras->distal=="true") ? "distal" : "";
            $cadena_caras[] = ($caras->palatino=="true") ? "palatino" : "";
            $cadena_caras[] = ($caras->oclusal=="true") ? "oclusal" : "";
            $cadena_caras[] = ($caras->lingual=="true") ? "lingual" : "";

            $Pieza = ($objevol->diente!=0)?$objevol->diente:"No asignado";

            $Servicio = "<div class='col-md-12 col-xs-12 no-padding'>
                            <span style='display: block' class='text-sm' >".$objevol->presstacion."</span>
                            <span style='display: block' class='text-sm text-blue' >Pieza: ".$Pieza."</span>
                            <span style='display: block' class='text-sm text-blue' >".$objevol->plantram."</span>
                        </div>";

            $doct = "<span style='display: block' class='text-sm text-blue' title='Doctor(a) encargado ".$objevol->doct." '>Doctor(a): ".$objevol->doct."</span>";

            $row   = array();
            $row[] = date('Y/m/d', strtotime($objevol->fechaevul) );
            $row[] = $Servicio;
            $row[] = $objevol->estadodiente." ".$doct;
            $row[] = "<p class='text-blue text-sm' title='".$objevol->observacion."' >".((strlen($objevol->observacion)>50)?substr($objevol->observacion,0,50)." ...":$objevol->observacion)."</p>";
            $row[] = "<small class='text-blue text-sm'>". (implode(', ', array_filter( $cadena_caras )))." </small> ";  ;

            $data[] = $row;

        }
    }

    $resultFinal = [
        'datos' => $data,
        'total' => $Total
    ];


    return $resultFinal;

}


function fetchPrestacionGroupLab($id = null, $name = ""){

    global $db;


    $data = [];

    if($id==true)
        return '';


//    if($name==true)
//        return '';



    $arr_prestacion  = array();
    $Arr_laboratorio = array();

//    $Arr_laboratorio['0'] = array('id' => 0, 'name' => 'General');
//    $quelab   = $db->query("select rowid , name  from tab_conf_laboratorios_clinicos where estado = 'A' ");
//    $fetchlab = $quelab->fetchAll();
//    foreach ($fetchlab as $key => $value){
//        $Arr_laboratorio[$value['rowid']] = array('id' => $value['rowid'], 'name' => $value['name']);
//    }

    #echo '<pre>';  print_r($Arr_laboratorio); die();
    $sqlprestacion = "SELECT 
                        p.rowid as id, 
                        p.descripcion as prestacion,
                        IFNULL((SELECT l.name FROM tab_conf_laboratorios_clinicos l WHERE l.rowid = p.fk_laboratorio),'') AS lab,
                        p.fk_laboratorio,
                        p.estado
                    FROM
                        tab_conf_prestaciones p
                    WHERE
                        p.estado = 'A' ";

    if($name!=""){
        $sqlprestacion .= " and p.descripcion like '%$name%'";
    }

    $sqlprestacion .= " limit 5";
    $result  =  $db->query($sqlprestacion);

    if($result->rowCount() > 0) {
        while ($obj = $result->fetchObject()) {
//            $lab = "LAB(". (($obj->lab!='')?$obj->lab:'') .")";
            $lab = " -  Asociado a Laboratorio ";
            if($obj->lab==''){
                $lab='';
            }

            $label = $obj->prestacion." "."$lab";
            $arr_prestacion[] = array('id' => $obj->id , 'text' => $label);
        }
    }

//    foreach ($Arr_laboratorio as $key => $value){
//        $data[$value['name']] =  $Arr_prestacion[$key];
//    }

//    echo '<pre>'; print_r($data); die();

    return $arr_prestacion;

}




?>