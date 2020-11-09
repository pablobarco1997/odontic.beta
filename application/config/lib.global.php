<?php



//$DOL_DOCUMENT = $_SERVER['DOCUMENT_ROOT'] .'/odontic2'; //DIRECTORIO REMOTO

$DOL_DOCUMENT = $_SERVER['DOCUMENT_ROOT'] .'/betaodontic'; //DIRECTORIO LOCAL


$DOL_HTTP_IP = 'http://localhost/betaodontic'; //DIRECCION HTTP
//$DOL_HTTP_IP = 'http://192.168.0.109/dental'; //DIRECCION HTTP


//$DOL_HTTP_IP = 'http://adminnube.com/odontic2'; //DIRECCION HTTP
$KEY = 'PASSWORD_DEL_SERVIDO'; #Password del servidor para crear el Acceso a los modulos
$kEY_GLOB = 'PASSWORD_2020_123';

define('KEY', $KEY );
define('KEY_GLOB', md5($kEY_GLOB));
define('DOL_DOCUMENT', $DOL_DOCUMENT );
define('DOL_HTTP', $DOL_HTTP_IP);

?>