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


function UploadFicherosLogosEntidadGlob($name, $type, $tmp_name, $new_url = '', $sinOptimizar=false) //Mueve los archivos a una carpeta creada
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

    if($sinOptimizar==true){
        $link_sinOptimizar = $conf->DIRECTORIO.'/'.(basename($name)); //creo la ruta donde se guarda el ico o png
        if(!move_uploaded_file($tmp_name, $link_sinOptimizar)){
            return false;
        }else{
            return true;
        }

    }

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

    $idEntity = "USUID_".$conf->EMPRESA->ENTIDAD."_".$id; //id usuario de la entidad unico
    $cnEntity = new CONECCION_ENTIDAD();

    #Se comprueba si es super usuario
    $sqladmin = "SELECT admin FROM tab_login_entity where login_idusers_entity = '$idEntity'";
    $rs = $cnEntity::CONNECT_ENTITY()->query($sqladmin);
    if($rs && $rs->rowCount()==1){
        $admin = $rs->fetchObject()->admin;
        $objet->admin = $admin;
    }else{
        $objet->admin = -1;
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

    $sql = "SELECT * , concat(nombre, ' ', apellido) as nom FROM tab_admin_pacientes WHERE rowid = $id";
    $rs = $db->query($sql);

    if($rs->rowCount()>0)
    {
        while ($ob = $rs->fetchObject()){
            $objeto = $ob;
        }
    }

    return $objeto;
}

function getnombrePrestacionServicio($id=''){

    global $db, $conf;

    $objeto = array();

    $sql = "SELECT * FROM tab_conf_prestaciones WHERE rowid = $id";
    $rs = $db->query($sql);

    if($rs->rowCount()>0)
    {
        while ($ob = $rs->fetchObject()){
            $objeto = $ob;
        }
    }

    return $objeto;
}

function getnombreCuenta($id=''){
    global $db, $conf;

    $objeto = array();

    $sql = "SELECT *, concat(n_cuenta,' ',name_acount) as nom FROM tab_ope_declare_cuentas WHERE rowid = $id";
    $rs = $db->query($sql);

    if($rs->rowCount()>0)
    {
        while ($ob = $rs->fetchObject()){
            $objeto = $ob;
        }
    }

    return $objeto;
}

function getnombreEstadoCita($id=''){
    global $db, $conf;

    $objeto = array();

    $sql = "SELECT *, text as nom FROM tab_pacientes_estado_citas WHERE rowid = $id";
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


function ConfirmacionEmailHTML($token, $cron=false)
{
    if($cron==true){
        $Url_ConfirmCita = 'https://adminnube.com/odontic.beta/public/information/index.php?v=confirm_cita&token='.$token;
    }else{
        $Url_ConfirmCita = DOL_HTTP .'/public/information/index.php?v=confirm_cita&token='.$token;
    }


    $buttonToken = '';
    $buttonToken .= '
            <a href='. $Url_ConfirmCita .' style="
                 background-color: #28a745; 
                 padding: 15px; 
                 color: #f0f0f0;
                 border-radius: 5px;
                 padding:10px 40px;margin:0px 10px 10px 0px;border-radius:3px;font-size:1.4rem;color:#fff;text-decoration:none;border-bottom:5px solid #28a745"> 
             <b>C O N F I R M A R</b> </a>';

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
        if(isset($_SESSION['breadcrumbs']) && is_array($_SESSION['breadcrumbs']) && count($_SESSION['breadcrumbs']) > 0){

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

            if( isset($_SESSION['breadcrumbs']) && isset($_SESSION['breadcrumbs']) ){
                foreach ($_SESSION['breadcrumbs'] as $key => $value)
                {
                    if($value['titulo'] == $titulo){
                        unset($_SESSION['breadcrumbs'][$key]);
                    }
                }
            }else{
                $_SESSION['breadcrumbsAcu'] = 0;
            }

            $_SESSION['breadcrumbs'][] = array( 'url' => $url , 'titulo' => $titulo );
            $Breadcrumbs_Mod = $_SESSION['breadcrumbs'];
            $_SESSION['breadcrumbsAcu']++;
            $CountBread = $_SESSION['breadcrumbsAcu'];

        }

    }


    if(!empty($titulo) )
    {


        $Breadcrumbs .= '<ul style="list-style: none; " class="list-inline" >';
                    $Breadcrumbs .= '<li> <small> <a href="'.DOL_HTTP.'/index.php?view=inicio" class="text-blue Breadcrumbs_link" style="font-weight: lighter;  color: #212f3d;" title="inicio"  ><i class="fa fa-dashcube"></i></a></small></li>';
        for( $i = 0; $i <= $CountBread; $i++ ){
            if(isset($Breadcrumbs_Mod[$i])){
                if($i==0){
                    $Breadcrumbs .= '<li><small><a href=" '. $Breadcrumbs_Mod[$i]['url'] .'" class="text-blue Breadcrumbs_link" style="font-weight: lighter;  color: #212f3d" title="'. $Breadcrumbs_Mod[$i]['titulo'] .'">'.$Breadcrumbs_Mod[$i]['titulo'].'</a></small></li>';
                }else{
                    $Breadcrumbs .= '<li><small><a href=" '. $Breadcrumbs_Mod[$i]['url'] .'" class="text-blue Breadcrumbs_link" style="font-weight: lighter;  color: #212f3d" title="'. $Breadcrumbs_Mod[$i]['titulo'] .'"  >| &nbsp; '.$Breadcrumbs_Mod[$i]['titulo'] .' </a> </small>  </li>';
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

function validSuperAdmin($idEntityLogin = ""){

    global $conf;
    $cn = new CONECCION_ENTIDAD();
    $conectar = $cn::CONNECT_ENTITY();
    $valid = false;
    if($idEntityLogin!=""){
        $que = "select admin from tab_login_entity where entity = '".$conf->EMPRESA->ENTIDAD."' and login_idusers_entity = '".$idEntityLogin."' limit 1";
        $result = $conectar->query($que);
        if($result&&$result->rowCount()>0){
            $object = $result->fetchObject();
            if($object->admin == 1){
                $valid = true; // si es uno no puedo modificar el
            }
        }
    }

    return $valid;

}

function PermitsModule($nom_module="", $permiso="", $die=0){

    global $user, $conf, $db;

    /*
     * action Permisos
     *
     * 1 => consultar
     * 2 => agregar
     * 3 => modificar
     * 4 => eliminar
     * */

    if(validSuperAdmin($user->users_unique_id))
        $admin = 1;
    else
        $admin = 0;


    if(!$admin){

        if(!is_string($nom_module) && !is_string($permiso)){
            return false;
        }

        $column = " m.name AS modulo,
                    s.usuario,
                    pn.name AS name_permiso,
                    p.id_module,
                    p.id_permissions ";

        if($permiso != "" && $nom_module != ""){
            $column = " count(*) ";
        }

        $sql_a = "SELECT 
                    ".$column."
                FROM
                    tab_login_users s
                        INNER JOIN
                    tab_login_users_permissions p ON p.fk_perfiles = s.fk_perfil_entity
                        INNER JOIN
                    tab_login_permissions pn ON pn.rowid = p.id_permissions
                        INNER JOIN
                    tab_modulos_clinicos m ON m.rowid = p.id_module
                        INNER JOIN
                    tab_login_users_permissions_modulos prm ON prm.id_modulo = m.rowid
                        AND prm.fk_perfil = p.fk_perfiles
                WHERE
                    s.rowid = $user->id 
                    AND pn.name = '$permiso'
                    AND replace(m.name,' ','') = replace('$nom_module',' ','')
                GROUP BY m.rowid , pn.rowid";

        if($die==1){
//             print_r($sql_a); die();
        }
        $result = $db->query($sql_a);
        if($result){
            if($result->rowCount()>0){
                $valid =  true;
            }else{
                $valid =  false;
            }
        }else{
            $valid =  false;
        }


    }else{
        $valid = true;
    }

    return $valid;
}

function accessoModule($nomb_module){

    global $user, $db;

    if(validSuperAdmin($user->users_unique_id))
        $admin = 1;
    else
        $admin = 0;

    //si no es administrador
    if(!$admin){
        //se comprueba que tenga permiso para acceder al modulo
        $sql_m = "SELECT 
                        mc.name, m.fk_perfil
                    FROM
                        tab_login_users_permissions_modulos m
                            inner join 
                        tab_login_users s on s.fk_perfil_entity = m.fk_perfil
                            INNER JOIN
                        tab_modulos_clinicos mc ON mc.rowid = m.id_modulo
                    WHERE
                        mc.name = '$nomb_module' AND s.rowid = $user->id
                    group by mc.rowid";
//        print_r($sql_m); die();
        $result = $db->query($sql_m);
        if($result->rowCount()==1){
            $result_a = $result->fetchObject()->name;
            if($result_a==$nomb_module){
                return true;
            }else{
                errorAccessoDenegado();
                return false;
            }
        }else{
            errorAccessoDenegado();
            return false;
        }
    }


}

function errorAccessoDenegado($otroTexto = ""){

    print "<div class=\"error-page\">
        <h2 class=\"headline text-red\">403</h2>

        <div class=\"error-content\">
          <h3><i class=\"fa fa-warning text-red\"></i> ACCESO DENEGADO</h3>

          <p>
            Ud. No tiene permiso asignado para este módulo
          </p>
                    
           <p>
            $otroTexto
          </p>

          <form class=\" hide search-form\">
            <div class=\"input-group\">
              <input type=\"text\" name=\"search\" class=\"form-control\" placeholder=\"Search\">

              <div class=\"input-group-btn\">
                <button type=\"submit\" name=\"submit\" class=\"btn btn-danger btn-flat\"><i class=\"fa fa-search\"></i>
                </button>
              </div>
            </div>
            <!-- /.input-group -->
          </form>
        </div>
      </div>";

    die();
}

//se consulta la caja para que usuario puede realizar cobros de un plan de tratamiento
function ConsultarCajaUsers($id_users=0){

    global $db, $messErr;

    if($id_users==0){
        return 'Este usuario no está asociado a ninguna caja';
    }

    $err_caja = [];

    $sql = "SELECT 
        s.rowid as id_user, 
        cp.date_apertura , 
        cp.date_cierre , 
        cp.estado, 
        cp.rowid as id_caja_ope, 
        cp.estado , 
        CASE
                WHEN cp.estado = 'A' THEN 'Caja abierta'
                WHEN cp.estado = 'C' THEN 'Caja Cerrada'
                WHEN cp.estado = 'E' THEN 'Caja Eliminada'
            ELSE 'Caja no asignada' 
        END as estado_caja , 
        concat(dc.n_cuenta,' ',dc.name_acount,' Dir.',dc.to_caja_direccion) as name_caja,
        s.usuario , 
        dc.rowid as id_cuenta, 
        dc.n_cuenta
    FROM
        tab_login_users s 
        left join
        tab_ope_cajas_clinicas cp on cp.id_user_caja = s.rowid
        inner join 
        tab_ope_declare_cuentas dc on dc.rowid = cp.id_caja_cuenta
    where 
        cp.estado = 'A'
        and cp.date_cierre is null
        and s.rowid = ".$id_users. " limit 1";

    //error caja no asignada
    $err_caja['error'] = "";

    $result = $db->query($sql);
    if($result && $result->rowCount() > 0){
        $all = $result->fetchAll(PDO::FETCH_ASSOC);

        foreach ($all as $k => $value){
            if($value['estado'] == 'A'){
                $err_caja['caja'] = $value;
            }
            if($value['estado'] == 'C'){
                $err_caja['error'] = 'Caja Clinica Cerrada';
            }
        }
    }else{
        $err_caja['error'] = 'Este usuario no está asociado a ninguna Caja Clinica';
    }

    return $err_caja;

}

function acceso_system($key, $module, $pathView, $operacion){

    $AcumuladorDie = 0;

    switch ($operacion){

        case 'pathView':


            break;

    }

    if($AcumuladorDie > 0){

        print_r("Acceso denegado se detecto manipulación sospechosa");
        die();


    }

}


function string_comillas_delet($string_c = ""){
    if($string_c !="" ){
        $string_c =  preg_replace('/["]/', '', $string_c);
        $string_c =  preg_replace("/[']/", '', $string_c);
        return $string_c;
    }else{
        return false;
    }
}


function getnombFormaPago($id){
    global  $db;
    $query   = "select nom, rowid as id from tab_bank_operacion where rowid = $id";
    $result  = $db->query($query);
    if($result && $result->rowCount()>0) {
        $object = $result->fetchObject();
        return $object->nom;
    }else{
        return 0;
    }
}


?>