<?php

// libs
if (!defined('_DIR_LIB')) define('_DIR_LIB', _DIR_RACINE . 'lib/');

define('_DIR_OPENID_LIB', _DIR_LIB . 'php-openid-2.1.3/');

define('Auth_OpenID_RAND_SOURCE', null); // a priori...

$GLOBALS['liste_des_authentifications']['openid'] = 'openid';

$GLOBALS['openid_statut_nouvel_auteur'] = '1comite';
?>
