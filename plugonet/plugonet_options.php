<?php

if (!defined('_ECRIRE_INC_VERSION')) return;

// Le serveur d'aide en ligne pour ce plugin
$GLOBALS['help_server'][] ='http://plugins.spip.net/aide/';
$GLOBALS['index_aide_plugonet'] = array(
	'paqxmlgen',
	'paqxmlpaquet',
	'paqxmlnom',
	'paqxmldesc',
	'paqxmlaut',
	'paqxmlcred',
	'paqxmlcopy',
	'paqxmllic',
	'paqxmltrad',
	'paqxmlbout',
	'paqxmlpipe',
	'paqxmlnec',
	'paqxmllib',
	'paqxmlproc',
	'paqxmlpath',
	'paqxmlspip',
	'paqxmlfoi',
	'paqxmlexe'
);

// Version SPIP minimale quand un plugin ne le precise pas
// -- Version SPIP correspondant a l'apparition des plugins
define('_PLUGONET_VERSION_SPIP_MIN', '1.9.0');
// -- Pour l'instant on ne connait pas la borne sup exacte
define('_PLUGONET_VERSION_SPIP_MAX', '3.0.99');

?>
