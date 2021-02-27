<?php


if(md5($_SERVER['SERVER_NAME'])=='068234a2d85a5233fd17f6d0507d3454'){//servidor remoto
    $doll_document = $_SERVER['DOCUMENT_ROOT'].'/odontic.beta';
    $doll_http_ip = "http://adminnube.com/odontic.beta";
}else{//servidor local
    $doll_document = $_SERVER['DOCUMENT_ROOT'].'/betaodontic';
    $doll_http_ip  = "http://localhost/betaodontic";
}

$DOL_DOCUMENT = $doll_document; //DIRECTORIO LOCAL
$DOL_HTTP_IP = $doll_http_ip; //DIRECCION HTTP

$KEY = 'PASSWORD_DEL_SERVIDO'; #Password del servidor para crear el Acceso a los modulos
$kEY_GLOB = 'PASSWORD_2020_123';

define('KEY', $KEY );
define('KEY_GLOB', md5($kEY_GLOB));
define('DOL_DOCUMENT', $DOL_DOCUMENT );
define('DOL_HTTP', $DOL_HTTP_IP);

?>