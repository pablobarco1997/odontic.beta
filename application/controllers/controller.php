<?php


function GETPOST($paramname, $check = '', $method = 0)
{
    if (empty($method)) $out = isset($_GET[$paramname]) ? $_GET[$paramname] : (isset($_POST[$paramname]) ? $_POST[$paramname] : '');
    elseif ($method == 1) $out = isset($_GET[$paramname]) ? $_GET[$paramname] : '';
    elseif ($method == 2) $out = isset($_POST[$paramname]) ? $_POST[$paramname] : '';
    elseif ($method == 3) $out = isset($_POST[$paramname]) ? $_POST[$paramname] : (isset($_GET[$paramname]) ? $_GET[$paramname] : '');
    else return 'BadParameter';

    if (!empty($check)) {
        // Check if numeric
        if ($check == 'int' && !preg_match('/^[-\.,0-9]+$/i', $out)) {
            $out = trim($out);
            $out = '';
        } // Check if alpha
        elseif ($check == 'alpha') {
            $out = trim($out);
            // '"' is dangerous because param in url can close the href= or src= and add javascript functions.
            // '../' is dangerous because it allows dir transversals
            if (preg_match('/"/', $out)) $out = '';
            else if (preg_match('/\.\.\//', $out)) $out = '';
        } elseif ($check == 'array') {
            if (!is_array($out) || empty($out)) $out = array();
        }
    }

    return $out;
}


function UploadFicherosLogosEntidadGlob($name, $type, $tmp_name, $new_url = '') //Mueve los archivos a una carpeta creada
{
    global $db, $conf;

    $error = false;

    //compruebo si la carpeta exite , si no existe la creo
    if(!is_dir($conf->DIRECTORIO)){
        mkdir($conf->DIRECTORIO,0777, true);
    }

    // Obtenemos la información de la imagen
    $imgInfo    = getimagesize($tmp_name);
    $mime       = $imgInfo['mime'];

    if($type){
        if(!empty($type))
        {
            // Creamos una imagen
            switch ($mime){
                case 'image/jpeg':
                    $image = imagecreatefromjpeg($tmp_name);
                    subirImageComprimido($mime, $image, $name, 0,300,300, $new_url);
                    break;
                case 'image/png':
                    $image = imagecreatefrompng($tmp_name);
                    subirImageComprimido($mime, $image, $name, 0,300,300, $new_url);
                    break;
                case 'image/gif':
                    $image = imagecreatefromgif($tmp_name);
                    subirImageComprimido($mime, $image, $name, 0,300,300, $new_url);
                    break;
                case '':
                    break;
                default:
                    break;
            }

        }
    }
    else{
        $error= false;
    }

    return $error;
}



function subirImageComprimido($mime, $image, $name, $quality, $x_pixeles, $y_pixeles, $new_url = "")
{
    global $db, $conf;

    $error = null;
    if(file_exists($conf->DIRECTORIO))//Si la carpeta Si exite de los logos Default
    {
        if($new_url!="")
            $link = $new_url.'/'.(basename($name));
        else
            $link = $conf->DIRECTORIO.'/'.(basename($name)); //la url a donde se va a dirigir el archivo carpeta padre  /logos_icon/

        //optimizar imagen
        $img2=null;
        $optimizar = true;
        $x = imagesx($image);
        $y = imagesy($image);

        if($x <= $x_pixeles && $y <= $y_pixeles){
            $optimizar = false;
        }

        if($x >= $y) {
            $nuevax = $x_pixeles;
            $nuevay = $nuevax * $y / $x;
        }
        else {
            $nuevay = $y_pixeles;
            $nuevax = $x / $y * $nuevay;
        }

        if($optimizar==true){
            $img2 = imagecreatetruecolor($nuevax, $nuevay);
            imagecopyresized($img2,$image,0, 0, 0, 0,floor($nuevax), floor($nuevay), $x, $y);
        }elseif ($optimizar==false){
            $img2 = $image;
        }

        if($img2!=null){
            if($mime=='image/jpeg')
                imagejpeg($img2, $link, $quality);
            if($mime=='image/png')
                imagepng($img2, $link);
            $error = $link;
        }else{
            $error = false;
        }

//        if( move_uploaded_file($tmp_name, $link) ) {
//            $error= $link;
//        }else{
//            $error= false;
//        }
    }else{
        $error = false;
    }
    return $error;
}


function getnombreUsuario($id=''){

    global $db, $conf;

    $objet = array();

    $sql  = 'select * from tab_login_users where rowid = ' .$id .' limit 1';
    $resl = $db->query($sql);
    if($resl&&$resl->rowCount()>0){

        while ($ko = $resl->fetchObject()){

            $objet = $ko;
        }
    }

    return $objet;
}

function getnombreDentiste($id=''){

    global $db, $conf;

    $objeto = array();

    $sql = "SELECT * FROM tab_odontologos WHERE rowid = $id";
    $rs = $db->query($sql);

    if($rs->rowCount()>0)
    {
        while ($ob = $rs->fetchObject()){
            $objeto = $ob;
        }
    }

    return $objeto;
}


function getnombrePaciente($id=''){

    global $db, $conf;

    $objeto = array();

    $sql = "SELECT * FROM tab_admin_pacientes WHERE rowid = $id";
    $rs = $db->query($sql);

    if($rs->rowCount()>0)
    {
        while ($ob = $rs->fetchObject()){
            $objeto = $ob;
        }
    }

    return $objeto;
}

#OBTENER LA FECHA EN ESPAÑOL
function GET_DATE_SPANISH($fecha)
{

    setlocale(LC_TIME, 'es_Es');

    $mes1 =  date('m', strtotime($fecha));
    $dia1 =  date('D', strtotime($fecha));
    $year1 = date('Y', strtotime($fecha));

//    print_r($dia1); die();
    $dialabel = '';

    $dateObjm = DateTime::createFromFormat('m', $mes1 );
    $nommes = strftime('%B', $dateObjm->getTimestamp());

    switch ($dia1)
    {
        case 'Mon': #lunes
            $dialabel = 'lunes';
            break;
        case 'Tue': #martes
            $dialabel = 'martes';
            break;
        case 'Wed':#miercoles
            $dialabel = 'miercoles';
            break;
        case 'Thu':#jueves
            $dialabel = 'jueves';
            break;
        case 'Fri':#viernes
            $dialabel = 'viernes';
            break;
        case 'Sat':#sabado
            $dialabel = 'sabado';
            break;
        case 'Sun':#domingo
            $dialabel = 'domingo';
            break;
    }

    return $nommes .' '.$dialabel.' ' .date('d',strtotime($fecha)).' , '.$year1;
}

#ESTE PERMISO SIRVE PARA PERMITIR ACCEDER A CMS DE PACIENTES
function PERMISO_ACCESO_ADMIN_PACIENTES($keyGlob)
{

    if($keyGlob == md5('PASSWORD_2020_123')){

    }else{

        echo '<h1> ACCESO DENEGADO </h1>';
        die();
    }

}

#token permisos acceso
function tokenSecurityId($token){
    return bin2hex($token);
}
function decomposeSecurityTokenId($token){
    return hex2bin($token);
}


function ConfirmacionEmailHTML($token)
{
    $Url_ConfirmCita = DOL_HTTP .'/public/information/index.php?v=confirm_cita&token='.$token;

    $buttonToken = '';
    $buttonToken .= '
            
    <div style=\'width: 100%\'>
            <a href='. $Url_ConfirmCita .' style="
                position: relative;
                padding: 10px 40px;
                margin: 0px 10px 10px 0px;
                border-radius: 3px;
                /*font-family: \'Lato\', sans-serif;*/
                font-size: 1.4rem;
                color: #FFF;
                text-decoration: none; 
                
                background-color: #3498db;
                border-bottom: 5px solid #2980B9;
                text-shadow: 0px -2px #2980B9;
                cursor: pointer;
            ">CONFIRMAR CITA &nbsp;&nbsp;  </a>
    </div>';

    return $buttonToken;
}

function Breadcrumbs_Mod( $titulo, $url, $module )
{


    $Breadcrumbs_Mod = array();
    $Breadcrumbs = "";
    $htmlBreadcrumbs = "";
    $CountBread = 0;

    #cuando sea el modulo principal
    if( $module == true){

        $_SESSION['breadcrumbsAcu'] = 0;
        $_SESSION['breadcrumbs'] = array();
        $_SESSION['breadcrumbs'][] = array( 'url' => $url , 'titulo' => $titulo );
        $Breadcrumbs_Mod = $_SESSION['breadcrumbs'];

    }else{

        #cuando sea varios modulos
        if(is_array($_SESSION['breadcrumbs']) && count($_SESSION['breadcrumbs']) > 0){

            foreach ($_SESSION['breadcrumbs'] as $key => $value)
            {
                if($value['titulo'] == $titulo){
                    unset($_SESSION['breadcrumbs'][$key]);
                }
            }

            $_SESSION['breadcrumbs'][] = array( 'url' => $url , 'titulo' => $titulo );
            $_SESSION['breadcrumbsAcu']++;

            $Breadcrumbs_Mod = $_SESSION['breadcrumbs'];
            $CountBread = $_SESSION['breadcrumbsAcu'];

        }else{

            foreach ($_SESSION['breadcrumbs'] as $key => $value)
            {
                if($value['titulo'] == $titulo){
                    unset($_SESSION['breadcrumbs'][$key]);
                }
            }

            $_SESSION['breadcrumbs'][] = array( 'url' => $url , 'titulo' => $titulo );
            $Breadcrumbs_Mod = $_SESSION['breadcrumbs'];
            $_SESSION['breadcrumbsAcu']++;
            $CountBread = $_SESSION['breadcrumbsAcu'];

        }
    }

//    echo '<pre>'; print_r($_SESSION['breadcrumbs']); die();

    if(!empty($titulo) )
    {


        $Breadcrumbs .= '<ul class="breadcrumb3 " >';
        for( $i = 0; $i <= $CountBread; $i++ )
        {
            if(isset($Breadcrumbs_Mod[$i])) //verifico si existe o hay valores
            {
                if($i==0){
                    $Breadcrumbs .= '<li><a href=" '. $Breadcrumbs_Mod[$i]['url'] .' " title="'. $Breadcrumbs_Mod[$i]['titulo'] .'"  > '. $Breadcrumbs_Mod[$i]['titulo'] .' &nbsp;</a></li>';
                }else{
                    $Breadcrumbs .= '<li><a href=" '. $Breadcrumbs_Mod[$i]['url'] .' " title="'. $Breadcrumbs_Mod[$i]['titulo'] .'"  >  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; '. $Breadcrumbs_Mod[$i]['titulo'] .' &nbsp;</a></li>';

                }
            }
        }
        $Breadcrumbs .= '</ul>' ;



//        $htmlBreadcrumbs .= '<div class="btn-group btn-breadcrumb pull-right">';
//        $htmlBreadcrumbs .= '            <a href="#" class="btn btn-default"><i class="fa fa-dashcube"></i></a>';
//        for( $i = 0; $i <= $CountBread; $i++ )
//        {
//            if(isset($Breadcrumbs_Mod[$i])){
//                $htmlBreadcrumbs .= '<a href="'. $Breadcrumbs_Mod[$i]['url'] .'" class="btn btn-default"> '. $Breadcrumbs_Mod[$i]['titulo'] .' </a>';
//            }
//        }
//        $htmlBreadcrumbs .= '</div>';

    }

    return $Breadcrumbs;
}


function checkPermissModule( $fk_modulo, $fk_actionpermiso, $idUser ){

    global $db, $conf;

    $coneccionEntidad = new CONECCION_ENTIDAD();

    $idUsuarioEntity = 0;
    $datauser = getnombreUsuario( $idUser ); #obtengo un objeto

    $sqlentity = "select rowid as idlogin_entidad, nombre_user  from tab_login_entity where  id_usuario = '".$datauser->cedula."' and fk_entidad = ".$conf->EMPRESA->ID_ENTIDAD." and entity = '".$conf->EMPRESA->ENTIDAD."' ";
    $rsentity = $coneccionEntidad::CONNECT_ENTITY()->query($sqlentity);

    if($rsentity){

        if( $rsentity->rowCount()>0 ){
            $idUsuarioEntity = $rsentity->fetchObject()->idlogin_entidad;
        }else{
            return false; 
        }

    }else{
        return false;
    }


    $sql  = " SELECT * FROM tab_permisos_user WHERE   fk_entity = ".$conf->EMPRESA->ID_ENTIDAD." and numero_entity = '".$conf->EMPRESA->ENTIDAD."' ";
    $sql .= " and fk_users_login = $idUsuarioEntity ";
    $sql .= " and fk_action_permisos = $fk_actionpermiso ";
    $sql .= " and fk_modulo = $fk_modulo ";
    $result = $coneccionEntidad::CONNECT_ENTITY()->query($sql);
    if($result){

        if($result->rowCount() > 0){

            return true;

        }else{

            return false;
        }

    }else{

        return false;
    }


}

function fetchEntityPerfilPermisos($Permss_Module = array() , $Perfil_Entity = 0)
{
    global $conf;

    $coneccionEntidad = new CONECCION_ENTIDAD();

    if($Perfil_Entity!=0)
    {
        $sqlPerfilEntity = "select rowid, text from tab_perfiles_add where fk_entity = ".$conf->EMPRESA->ID_ENTIDAD." and numero_entity = '".$conf->EMPRESA->ENTIDAD."' and rowid =".$Perfil_Entity." limit 1";
        $rs = $coneccionEntidad::CONNECT_ENTITY()->query($sqlPerfilEntity);
        if($rs){
            if($rs->rowCount()>0){
                $object = $rs->fetchObject();

                if($object->text!=null && $object->text!="" ){
                    return $object->text ;
                }else{
                    return "";
                }
            }
        }
    }

}

function NavSearchPacientes(){

    $navSearch = '
        <div  class="navbar-static-top" role="search" style="position: relative">
            <div class="form-group content-box-search col-centered" style="margin-top: 7px; margin-bottom: 0px; padding-top: 3px; width: 50%">
                <input type="text" class="input-sm search_paciente_id" id="navbar-search-input" onkeyup="buscarpacientes(this)"  placeholder="buscar paciente x c.i. o nombre" style="background-color: #ffffff; border-radius: 0px !important; width: 100%; ">
                <div style="position: absolute; background-color: #ffffff; width: 50%; display: none" class="box box-solid contlistsearch">
                    <br>
                    <ul style="list-style: none;" class="list_search"></ul>
                </div>
            </div>
        </div>';

    return $navSearch;
}

function DateSpanish($Date = "", $SoloMes = false){

    global  $db ;


    if($Date==""){
        $dateFechaNow = $db->query("SELECT CAST(NOW() AS DATE) AS date")->fetchObject()->date;
        $Date = $dateFechaNow;
    }


    setlocale(LC_TIME, 'es_Es');

    $mes1 =  date('m', strtotime($Date));
    $dia1 =  date('D', strtotime($Date));
    $year1 = date('Y', strtotime($Date));

//    print_r($dia1); die();
    $dialabel = '';

    $dateObjm = DateTime::createFromFormat('m', $mes1 );
    $NameMes   = strftime('%B', $dateObjm->getTimestamp());

    if($SoloMes!=""){ //me retorna solo el nombre del mes
        $dateObjm = DateTime::createFromFormat('m', $SoloMes );
        $NameMes   = strftime('%B', $dateObjm->getTimestamp());
        return $NameMes;
    }

    switch ($dia1)
    {
        case 'Mon': #lunes
            $dialabel = 'lunes';
            break;
        case 'Tue': #martes
            $dialabel = 'martes';
            break;
        case 'Wed':#miercoles
            $dialabel = 'miercoles';
            break;
        case 'Thu':#jueves
            $dialabel = 'jueves';
            break;
        case 'Fri':#viernes
            $dialabel = 'viernes';
            break;
        case 'Sat':#sabado
            $dialabel = 'sabado';
            break;
        case 'Sun':#domingo
            $dialabel = 'domingo';
            break;
    }

    $likeDate = $dialabel.' '.date('d', strtotime($Date)).' de '.$NameMes.' '.$year1;

    return $likeDate;


}

?>