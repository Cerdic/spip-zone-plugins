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
 * Insertion dans le pipeline formulaire_traiter (SPIP)
 * 
 * Invalider le cache pour tout changement de configuration
 *
 * @param array $flux
 * 		Le contexte du pipeline
 * @return array
 * 		Le même contexte de pipeline après invalidation s'il y a lieu
 */
function manuelsite_formulaire_traiter($flux){
	if($flux['args']['form'] == 'configurer_manuelsite'){
		include_spip('inc/invalideur');
		suivre_invalideur('1');
	}
	return $flux;
}
?>