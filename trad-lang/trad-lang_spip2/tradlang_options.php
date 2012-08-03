<?php

/**
 * 
 * Trad-lang v2
 * Plugin SPIP de traduction de fichiers de langue
 * © Florent Jugla, Fil, kent1
 * 
 */
if (!defined("_ECRIRE_INC_VERSION")) return;

$GLOBALS['url_arbo_parents']['tradlang'] = array('id_tradlang_module','tradlang_module');

if(!defined('_TRAD_PRIORITES'))
	define('_TRAD_PRIORITES','10. core;20. extensions;25. community sites;30. contribs');
if(!defined('_TRAD_PRIORITE_DEFAUT'))
	define('_TRAD_PRIORITE_DEFAUT','30. contribs');

function str_statut_revision($id_tradlang,$c=false){
	include_spip('action/editer_tradlang');
	return tradlang_set($id_tradlang,$c);
}
?>