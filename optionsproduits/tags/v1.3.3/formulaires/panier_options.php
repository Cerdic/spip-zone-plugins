<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function formulaires_panier_options_charger_dist($objet, $id_objet) {
	$valeurs = array(
		'objet'    => $objet,
		'id_objet' => $id_objet,
	);
	
	// Calculer le prix de l'objet pour le passer au formulaire
	$fonction_prix = charger_fonction('prix', 'inc/');
	$fonction_prix_ht = charger_fonction('ht', 'inc/prix');
	$valeurs['prix_ht'] = $fonction_prix_ht($objet, $id_objet, 6);
	$valeurs['prix'] = $fonction_prix($objet, $id_objet, 6);
	
	// On transmet les options reçues au formulaire sous forme concaténee
	// pour qu'il les réaffiche comme cochées, mais on ne lui transmet pas les
	// id_options reçues pour qu'il ne les remette pas dans #ACTION_FORMULAIRE
	$options = array();
	foreach ($_REQUEST as $key => $value) {
		if ($value && strpos($key, 'id_option') !== false) {
			set_request($key, '');
			$options[] = intval($value);
		}
	}
	$valeurs['options'] = join('|', array_filter($options));

	return $valeurs;
}

function formulaires_panier_options_traiter_dist($id_objet) {
	// On récupère les infos	
	$objet    = _request('objet');
	$id_objet = intval(_request('id_objet'));
	$quantite = intval(_request('quantite'));
	$negatif  = intval(_request('negatif'));
	$options  = array(_request('id_option'));

	// On reçoit des options en POST sous la forme id_option
	// ou id_optionX où X est l'id du groupe d'options.
	$groupes = sql_allfetsel('id_optionsgroupe', 'spip_optionsgroupes');
	foreach ($groupes as $groupe) {
		if ($id_option = _request('id_option' . $groupe['id_optionsgroupe'])) {
			$options[] = $id_option;
		}
	}
	// On concatène pour passer les options à l'action remplir_panier.
	$options = join('|', array_filter($options));

	// On appelle l'action remplir_panier
	$remplir_panier = charger_fonction('remplir_panier', 'action');
	$remplir_panier($objet . '-' . $id_objet . '-' . $quantite . '-' . $negatif . '-' . $options);
}
