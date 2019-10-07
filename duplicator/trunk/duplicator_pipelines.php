<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function duplicator_boite_infos($flux){
	include_spip('inc/config');
	include_spip('base/objets');
	include_spip('inc/autoriser');
	$objet = $flux['args']['type'];
	$id_objet = intval($flux['args']['id']);
	$table = table_objet_sql($objet);
	
	// Si on a accepté de dupliquer cet objet et que la personne en cours a bien le droit
	if (
		$objets = lire_config('duplicator/objets')
		and in_array($table, $objets)
		and autoriser('dupliquer', $objet, $id_objet)
	) {
		include_spip('inc/filtres');
		include_spip('inc/actions');
		include_spip('base/objets_parents');
		
		// Un bouton pour dupliquer le contenu
		$flux["data"] .= bouton_action(
			_T('duplicator:action_dupliquer_contenu'),
			generer_action_auteur('dupliquer_objet', "$objet/$id_objet")
		);
		
		// Un bouton pour dupliquer aussi les enfants, seulement si on trouve des enfants possibles
		if (type_objet_info_enfants($objet) and objet_trouver_enfants($objet, $id_objet)) {
			$flux["data"] .= bouton_action(
				_T('duplicator:action_dupliquer_contenu_enfants'),
				generer_action_auteur('dupliquer_objet', "$objet/$id_objet/enfants"),
				'',
				_T('duplicator:action_dupliquer_contenu_enfants_confirmer')
			);
		}
	}
	
	return $flux;
}
