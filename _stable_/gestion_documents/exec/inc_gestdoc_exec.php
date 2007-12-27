<?php

include_spip ("inc/presentation");
include_spip ("inc/documents");
include_spip('inc/indexation');
include_spip ("inc/logos");
include_spip ("inc/session");

if (!defined('_DIR_PLUGIN_GESTIONDOCUMENTS')){
	$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(dirname(__FILE__)))));
	define('_DIR_PLUGIN_GESTIONDOCUMENTS',(_DIR_PLUGINS.end($p)));
}

// Compatibilites
if (version_compare($GLOBALS['spip_version_code'],'1.9300','>=')) @define('_SPIP19300', 1);
if (version_compare($GLOBALS['spip_version_code'],'1.9200','>=')) @define('_SPIP19200', 1);
else @define('_SPIP19100', 1);

if(defined('_SPIP19100') && !function_exists('fin_gauche')) { function fin_gauche(){return '';} }
function gestdoc_compat_boite($b) {if(defined('_SPIP19200')) echo $b('', true); else $b(); }

?>