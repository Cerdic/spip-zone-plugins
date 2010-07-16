<?php

/**
 * Plugin Coordonnees
 * Licence GPL (c) 2010 Matthieu Marcillaud 
**/

function cop_affiche_milieu($flux) {
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


function cop_objets_extensibles($objets){
		return array_merge($objets, array(
			'adresse' => _T('cop:adresses'),
		));
}

?>
