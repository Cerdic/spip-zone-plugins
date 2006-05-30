<?php

	define('_DIR_PLUGIN_LETTRE_INFORMATION', (_DIR_PLUGINS.end(explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__)))))));
	define('_NOM_PLUGIN_LETTRE_INFORMATION', (end(explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__)))))));

	include_spip('inc/lettres_fonctions');

	global $page;
	$fond_message_html		= $GLOBALS['meta']['fond_message_html'];
	$fond_message_texte		= $GLOBALS['meta']['fond_message_texte'];

	if ($page == $fond_message_html OR $page == $fond_message_texte)
		$flag_preserver = true;
	
?>