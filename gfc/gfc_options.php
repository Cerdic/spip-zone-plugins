<?php

/**
 * Definition du repertoire de l'api opensocial
 * (telechargee automatiquement ou manuellement a l'installation
 */
define('_DIR_OSAPI',_DIR_RACINE.'lib/opensocial-php-client/osapi/');
// USER CONFIG

//consumer id
$GLOBALS['gfc']['consumer_id'] = '';
//consumer key
$GLOBALS['gfc']['consumer_key'] = '';
//consumer secret
$GLOBALS['gfc']['consumer_secret'] = '';
//email of members automatically created with this plugin
$GLOBALS['gfc']['default_email'] = 'nobody@noone.com';

//END USER CONFIG

$GLOBALS['gfc']['cookie_name'] = 'fcauth'.$GLOBALS['gfc']['consumer_id'];
$GLOBALS['gfc']['cookie_value'] = $_COOKIE[$GLOBALS['gfc']['cookie_name']];
?>
