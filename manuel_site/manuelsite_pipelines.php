<?php
/**
 * Plugin Manuel du site
 * 
 * Utilisation des pipelines dans l'espace public
 * 
 * @package SPIP\Manuelsite\Pipelines
 */
 
if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Insertion dans le pipeline pre_boucle (SPIP)
 * 
 * On enlève le manuel du site des boucles de l'espace public s'il est configuré ainsi
 * 
 * @param object $boucle
 * 		La boucle
 * @return object $boucle
 * 		La boucle modifiée
 */
function manuelsite_pre_boucle($boucle) {
	if(!test_espace_prive() && ($boucle->type_requete == 'articles')){
		$article = $boucle->id_table . '.id_article';
		$boucle->where[] = array("'!='", "'$article'", "manuelsite_article_si_cacher()");
	}
	return $boucle;
}

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