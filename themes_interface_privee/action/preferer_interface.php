<?php
if (!defined("_ECRIRE_INC_VERSION")) return;


// http://doc.spip.org/@action_preferer_dist
function action_preferer_interface() {
	$arg = $_GET['arg'];

	$GLOBALS['visiteur_session']['prefs']['interface_privee'] = $arg;
	
	if ($arg == "standard" || $arg == "blanche" || $arg == "degrades" || $arg == "wpip" || $arg == "bonux" || $arg == "chocolat" || $arg == "ispip" || $arg == "geek") $prefs_mod = true;
	
	if ($prefs_mod AND intval($GLOBALS['visiteur_session']['id_auteur']))
		sql_updateq('spip_auteurs', array('prefs' => serialize($GLOBALS['visiteur_session']['prefs'])), "id_auteur=" .intval($GLOBALS['visiteur_session']['id_auteur']));

	// Si modif des couleurs en ajax, redirect inutile on a change de CSS
	if ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') exit;


}

?>