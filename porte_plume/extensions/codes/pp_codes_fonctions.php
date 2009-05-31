<?php

@define('_URL_BROWSER_TRAC', 'http://trac.rezo.net/trac/spip/browser/spip/');

/*
 * Un raccourci pour des chemins vers de trac
 * [?ecrire/inc_version.php#trac]
 * [?ecrire/inc_version.php#tracNNN] // NNN = numero de ligne
 * 
 */
if (!function_exists('glossaire_trac')) {
	function glossaire_trac($texte, $id=0) {
		return _URL_BROWSER_TRAC . $texte . ($id ? '#L'.$id : '');
	} 
}
?>
