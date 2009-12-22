<?php
/**
 * Plugin Zen-Garden pour Spip 2.0
 * Licence GPL (c) 2006-2009 Cedric Morin
 *
 */

/**
 * Lister les thèmes
 * 
 * @param bool $tous
 * @return array
 */
function 	zengarden_liste_themes($tous){
	include_spip('inc/zengarden');
	return zengarden_charge_themes(_DIR_THEMES,$tous);
}

?>