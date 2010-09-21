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

	// si on est sur une page d'info d'un auteur
	if ($flux['args']['exec'] == 'auteur_infos') {
	
		// on récupère l'auteur en cours, et si on le récupère correctement...
		if ($id_auteur = $flux['args']['id_auteur']) {
			$contexte = array(
				'objet' => 'auteur',
				'id_objet' => $id_auteur
			);
			
			// ...on ajoute la boite de séletion des mots-clé dans le flux html
			$editer_mots = charger_fonction('editer_mots', 'inc');
			$flux['data'] .= '<h3>test (debut)</h3>'; 
			$flux['data'] .= $editer_mots('auteur', $id_auteur, $cherche_mot, $select_groupe, $flag_editable, false, 'auteurs');
			$flux['data'] .= '<h3>test (fin)</h3>';

		}
	}
	return $flux;
}


?>