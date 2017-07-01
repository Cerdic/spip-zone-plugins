<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function formulaires_ajouter_ingredient_saisies_dist($objet, $id_objet) {
	$saisies = array(
		array(
			'saisie' => 'input',
			'options' => array(
				'nom' => 'quantite',
				'label' => _T('ingredient:quantite'),
				'li_class' => 'haut'
			)
		)
	);
	return $saisies;
}

function formulaires_ajouter_ingredient_charger_dist($objet, $id_objet) {

	$quantite = sql_getfetsel(
		'quantite',
		'spip_ingredients_liens',
		array(
			'id_objet='.intval($id_objet),
			'objet='.sql_quote($objet)
		)
	);

	return array('quantite' => $quantite);
}

function formulaires_ajouter_ingredient_traiter_dist($objet, $id_objet) {
	//Traitement du formulaire.
	sql_updateq(
		'spip_ingredients_liens',
		array('quantite' => _request('quantite')),
		array(
			'id_objet='.intval($id_objet),
			'objet='.sql_quote($objet)
		)
	);

	// Donnée de retour.
	return array(
		'editable' => false,
		'message_ok' => _T('ingredient:quantite_confirme'),
		'redirect' => self()
	);
}
