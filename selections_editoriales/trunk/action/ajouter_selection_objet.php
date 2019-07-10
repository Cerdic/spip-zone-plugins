<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function action_ajouter_selection_objet_dist($arg=null) {

	// Récupérer les paramètres dans le POST ou le GET si besoin
	if (is_null($arg)) {
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$arg = $securiser_action();
	} else {
		$has_param = true; // Indiquer qu'il y a un param
	}
	list($objet, $id_objet) = explode('-', $arg);

	// Si on a bien le droit de créer ici
	if (
		$objet
		and $id_objet = intval($id_objet)
		and $id_objet > 0
		and autoriser('associerselections', $objet, $id_objet)
		and autoriser('creer', 'selection')
	) {
		include_spip('action/editer_objet');
		include_spip('inc/config');

		// Si on a bien créé une nouvelle sélection
		if ($id_selection = objet_inserer('selection')) {
			// Si on a bien modifié son titre sans erreur
			if (!$erreur = objet_modifier(
				'selection',
				$id_selection,
				array(
					'titre'=>lire_config('selections_editoriales/titre_defaut', _T('selection:titre_selection_nouvelle'))
				)
			)) {
				include_spip('action/editer_liens');

				// On associe la sélection à l'objet voulu
				objet_associer(array('selection'=>$id_selection), array($objet=>$id_objet));
			}

			// Retourner l'id de la sélection créée
			if ($has_param) {
				return $id_selection;
			}
		}
	}
}
