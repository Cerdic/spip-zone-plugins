<?php

set_include_path(get_include_path() . PATH_SEPARATOR . getcwd().'/'._DIR_PLUGIN_GFC.'auth');

/**
 * Definition du repertoire de l'api opensocial
 * (telechargee automatiquement ou manuellement a l'installation
 */
define('_DIR_OSAPI','lib/opensocial-php-client/osapi/');

// USER CONFIG

//consumer id
define('_GFC_CONSUMER_ID','');
//consumer key
define('_GFC_CONSUMER_KEY','');
//consumer secret
define('_GFC_CONSUMER_SECRET','');
//email of members automatically created with this plugin
define('_GFC_DEFAULT_EMAIL','nobody@noone.com');

//END USER CONFIG
define('_GFC_COOKIE_NAME','fcauth'._GFC_CONSUMER_ID);
$GLOBALS['gfc']['cookie_value'] = $_COOKIE[$GLOBALS['gfc']['cookie_name']];
?>
