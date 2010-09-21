<?php
/**
 * Plugin mots-auteurs pour Spip 2.0
 * Licence GPL 
 * Adaptation Cyril MARION - (c) 2010 Ateliers CYM http://www.cym.fr
 *
 */



/**
 * Ajout du bloc d'attribution de mot-clé
 * sur la page de visualisation d'un auteur
**/
function mots_auteurs_affiche_milieu($flux) {
	if ($flux['args']['exec'] == 'auteur_infos') {
		if ($id_auteur = $flux['args']['id_auteur']) {
			$contexte = array(
				'objet' => 'auteur',
				'id_objet' => $id_auteur
			);

			$flux = $editer_mots('article', $id_article, $cherche_mot, $select_groupe, $flag_editable, false, 'auteurs'));
		}
	}
	return $flux;
}


?>