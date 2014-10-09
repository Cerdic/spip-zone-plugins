<?php

if (!defined('_ECRIRE_INC_VERSION')) return;

defined('_LOG_FILTRE_GRAVITE') || define('_LOG_FILTRE_GRAVITE', 8);
defined('_DEBUG_AUTORISER')    || define('_DEBUG_AUTORISER', true);

$GLOBALS['test_i18n'] = true; // signaler les trads manquantes

if(!defined('_DEBUG_MINIPRES'))
	define('_DEBUG_MINIPRES',true);

function affiche_usage_memoire(){
	if (!defined('_AJAX') OR !_AJAX){
		chdir(_ROOT_CWD); // precaution
		// dans l'espace prive uniquement, et si la fonction taille_en_octets est deja chargee
		if (test_espace_prive()
		    AND function_exists('taille_en_octets')
				AND !_request('action'))
			echo "<div style='position:fixed;top:0;right:0;color:#fff;background:#666;padding:5px;z-index:1000;'>"
			 . taille_en_octets(memory_get_usage())
			 . '</div>';
		if (isset($GLOBALS['_debug']))
			echo var_export($GLOBALS['_debug'],true);
	}
}
register_shutdown_function('affiche_usage_memoire');

?>
