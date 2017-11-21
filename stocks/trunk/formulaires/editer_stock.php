<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function formulaires_editer_stock_saisies_dist($objet, $id_objet) {
	include_spip('inc/config');
	$stock_default = lire_config('stocks/quantite_default');
	include_spip('inc/stocks');
	$quantite = get_quantite($objet, $id_objet);

	$saisies = array(
		array(
			'saisie' => 'input',
			'options' => array(
				'nom' => 'quantite',
				'label' => _T('stocks:quantite_produit'),
				'defaut' => isset($quantite) ? $quantite : $stock_default
			),
			'verifier' => array(
				'type' => 'entier',
				'options' => array(
					'min' => 0
				)
			)
		)
	);

	return $saisies;
}

function formulaires_editer_stock_charger_dist($objet, $id_objet) {

	// Récupérer la quantité pour le contexte du formulaire
	include_spip('inc/stocks');
	$contexte['quantite'] = get_quantite($objet, $id_objet);

	return $contexte;
}

function formulaires_editer_stock_traiter_dist($objet, $id_objet) {
	include_spip('inc/stocks');
	set_quantite($objet, $id_objet, _request('quantite'));

	// Donnée de retour.
	return array(
		'editable' => true,
		'message_ok' => _T('info_modification_enregistree'),
		'redirect' => ''
	);
}
