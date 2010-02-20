<?php
/**
 * Plugin Zen-Garden pour Spip 2.0
 * Licence GPL (c) 2006-2009 Cedric Morin
 *
 */

if (!defined('_DIR_PLUGIN_THEME')){
	if (!defined('_DIR_THEMES'))
		define('_DIR_THEMES',_DIR_RACINE."themes/");
	
	// si on est en mode apercu, il suffit de repasser dans l'espace prive pour desactiver l'apercu
	if (test_espace_prive()){
		if (isset($_COOKIE['spip_zengarden_theme'])){
			include_spip('inc/cookie');
			spip_setcookie('spip_zengarden_theme',$_COOKIE['spip_zengarden_theme']=='',-1);
		}
	}
	elseif(defined('_ZEN_VAR_THEME')){
		if (!is_null($arg = _request('var_theme'))){
			include_spip('inc/cookie');
			spip_setcookie('spip_zengarden_theme',$_COOKIE['spip_zengarden_theme'] = $arg);
		}
	}
	
	// ajouter le theme au path
	if (
	(
		// on est en mode apercu
		(isset($_COOKIE['spip_zengarden_theme']) AND $t = $_COOKIE['spip_zengarden_theme'])
        // ou avec le cookie du switcher
        OR
		(isset($_COOKIE['spip_zengarden_switch_theme']) AND $t = $_COOKIE['spip_zengarden_switch_theme'] and lire_config('zengarden/switcher'))
		OR
		// ou un theme est vraiment selectionne
		(isset($GLOBALS['meta']['zengarden_theme']) AND $t = $GLOBALS['meta']['zengarden_theme'])
	)
	AND is_dir(_DIR_THEMES . $t)){
		_chemin(_DIR_THEMES.$t);
		$GLOBALS['marqueur'] = (isset($GLOBALS['marqueur'])?$GLOBALS['marqueur']:"").":$t";
	}
}

// Déclaration des pipelines permettant d'ajouter traitements lors de la preview et de l'activiation
$GLOBALS['spip_pipeline']['zengarden_apercevoir_theme'] = '';
$GLOBALS['spip_pipeline']['zengarden_activer_theme'] = '';
$GLOBALS['spip_pipeline']['zengarden_effacer_theme'] = '';

?>