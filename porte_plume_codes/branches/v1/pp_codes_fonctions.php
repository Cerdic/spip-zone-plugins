<?php


// trac SVN
# @define('_URL_BROWSER_TRAC', 'http://trac.rezo.net/trac/spip/browser/spip/');

// trac GIT
#@define('_URL_BROWSER_TRAC', 'https://core.spip.net/trac/spip/browser/@file@?rev=spip-2.1');

// redmine SVN (trunk)
@define('_URL_BROWSER_TRAC', 'https://core.spip.net/projects/spip/repository/entry/spip/@file@');

// redmine SVN (branche 2.1)
#@define('_URL_BROWSER_TRAC', 'https://core.spip.net/projects/spip/repository/entry/branches/spip-2.1/@file@');

/*
 * Un raccourci pour des chemins vers de trac
 * [?ecrire/inc_version.php#trac]
 * [?ecrire/inc_version.php#tracNNN] // NNN = numero de ligne
 * 
 */
if (!function_exists('glossaire_trac')) {
	function glossaire_trac($texte, $id=0) {
		// si @file@ present dans le define, on remplace par le texte
		if (false !== strpos(_URL_BROWSER_TRAC, '@file@')) {
			return str_replace('@file@', $texte, _URL_BROWSER_TRAC) . ($id ? '#L'.$id : '');
		}
		
		// sinon, on met bout a bout comme avant...
		return _URL_BROWSER_TRAC . $texte . ($id ? '#L'.$id : '');
	} 
}
?>
