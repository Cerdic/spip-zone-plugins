<?php
/*
 * Plugin Slogan
 * (c) 2009 C.Morin
 * Distribue sous licence GPL
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

// http://doc.spip.org/@balise_NOM_SITE_SPIP_dist
function balise_SLOGAN_SITE_SPIP_dist($p) {
	$p->code = "\$GLOBALS['meta']['slogan_site']";
	#$p->interdire_scripts = true;
	return $p;
}

?>