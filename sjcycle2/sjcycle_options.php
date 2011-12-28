<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

// Le serveur d'aide en ligne pour ce plugin
$GLOBALS['help_server'][] = url_de_base(1) . str_replace("../", "", _DIR_PLUGIN_SJCYCLE) . "aide/";
// Tests en cours
//$GLOBALS['help_server'][] ='http://plugins.spip.net/aide/';
//$GLOBALS['index_aide_sjcycle'] = array(	'sjcycle' );

?>
