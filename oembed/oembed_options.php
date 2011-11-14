<?php
/**
 * Plugin oEmbed
 * Licence GPL3
 *
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

define('_DEFINIR_CONTEXTE_TYPE',true);

// intercepter les appels à l'url /services/oembed/ (merci fil)
if (preg_match(',/services/(oembed)/,', $_SERVER['REQUEST_URI'], $r) 
	AND $GLOBALS['profondeur_url']==2) {
		set_request('page', $r[1]);
}

?>