<?php

/**
 * Plugin Coordonnees
 * Licence GPL (c) 2010 Matthieu Marcillaud 
**/


/**
 * Ajout des informations de coordonnées (adresses, mails, numéros)
 * sur la page de visualisation d'un auteur
**/
function coordonnees_affiche_milieu($flux) {
	if ($flux['args']['exec'] == 'auteur_infos') {
		if ($id_auteur = $flux['args']['id_auteur']) {
			$contexte = array(
				'objet' => 'auteur',
				'id_objet' => $id_auteur
			);
			$flux['data'] .= recuperer_fond('prive/boite/coordonnees', $contexte, array('ajax'=>true));
		}
	}
	return $flux;
}


/**
 * Ajout de l'objet 'adresse'
 * à la liste des objets pouvant recevoir des champs extras 
**/
function coordonnees_objets_extensibles($objets){
		return array_merge($objets, array(
			'adresse' => _T('coordonnees:adresses'),
		));
}

?>
