<?php
/**
 * Plugin Manuel du site
 * 
 * Fonctions spécifiques du plugin
 * 
 * @package SPIP\Manuelsite\Fonctions
 */
if (!defined("_ECRIRE_INC_VERSION")) return;


/**
 * Fonction retournant l'article du manuel enregistré dans la config
 * si on doit le cacher
 *
 * @return int|0 $id
 * 		L'identifiant numérique de l'article à cacher ou 0 si pas d'article à cacher
 */
function manuelsite_article_si_cacher() {
	include_spip('inc/config');
	$conf_manuelsite = lire_config('manuelsite',array());
	if (!test_espace_prive() && isset($conf_manuelsite["cacher_public"]) && isset($conf_manuelsite["id_article"]) && $conf_manuelsite["cacher_public"] && $id=intval($conf_manuelsite["id_article"]))
		return($id);
	return 0;
}

?>