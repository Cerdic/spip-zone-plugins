<?php
if (!defined('_DIR_PLUGIN_THEME')){
	if (!defined('_DIR_THEMES'))
		define('_DIR_THEMES',_DIR_RACINE."themes/");
	
	// si on est en mode apercu, il suffit de repasser dans l'espace prive pour desactiver l'apercu
	if (test_espace_prive() AND isset($_COOKIE['spip_zengarden_theme'])){
		include_spip('inc/cookie');
		spip_setcookie('spip_zengarden_theme',$_COOKIE['spip_zengarden_theme']=='',-1);
	}
	
	// ajouter le theme au path
	if (
	(
		// on est en mode apercu
		(isset($_COOKIE['spip_zengarden_theme']) AND $t = $_COOKIE['spip_zengarden_theme'])
		OR
		// ou un theme est vraiment selectionne
		(isset($GLOBALS['meta']['zengarden_theme']) AND $t = $GLOBALS['meta']['zengarden_theme'])
	)
	AND is_dir(_DIR_THEMES . $t)){
		_chemin(_DIR_THEMES.$t);
		$GLOBALS['marqueur'] = (isset($GLOBALS['marqueur'])?$GLOBALS['marqueur']:"").":$t";
	}
}

?>