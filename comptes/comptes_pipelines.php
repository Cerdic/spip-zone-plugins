<?php
/**
 * Plugin Comptes & Contacts pour Spip 2.0
 * Licence GPL (c) 2009 - 2010 - Ateliers CYM
 */

/**
 *
 * Insertion dans le pipeline affiche_milieu
 * Dans la page auteur_infos, insertion des champs spécifiques d'Inscription2
 *
 * @return array Le $flux modifié
 * @param array $flux
 */
function comptes_affiche_milieu($flux){
	if($flux['args']['exec'] == 'auteur_infos') {
#		$exceptions_des_champs_auteurs_elargis = pipeline('i2_exceptions_des_champs_auteurs_elargis',array());
#		$legender_auteur_supp = recuperer_fond('prive/inscription2_fiche',array('id_auteur'=>$flux['args']['id_auteur'],'exceptions'=>$exceptions_des_champs_auteurs_elargis));
		$flux['data'] .= recuperer_fond('prive/listes/adresses',array('id_auteur'=>$flux['args']['id_auteur']));
		$flux['data'] .= recuperer_fond('prive/listes/numeros',array('id_auteur'=>$flux['args']['id_auteur']));
	}
	return $flux;
}





?>
