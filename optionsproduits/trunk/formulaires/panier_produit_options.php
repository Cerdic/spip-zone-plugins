<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function formulaires_panier_produit_options_charger_dist($id_produit) {
	$valeurs = array('id_produit' => $id_produit);

	// On transmet les options reçues au formaulaire sous forme concaténee
	// pour qu'il les réaffiche comme cochées, mais on ne lui transmet pas les
	// id_options reçues pour qu'il ne les remette pas dans #ACTION_FORMULAIRE
	$options = array();
	foreach ($_REQUEST as $key => $value) {
		if($value && strpos($key,'id_option')!==false){
			set_request($key,'');
			$options[] = intval($value);
		}
	}
	$valeurs['options'] = join('|',$options);
	
	return $valeurs;
}

function formulaires_panier_produit_options_traiter_dist($id_produit) {
	// On récupère les infos	
	$quantite= intval(_request('quantite'));
	$negatif= intval(_request('negatif'));

	// On reçoit des options en POST sous la forme id_option
	// ou id_optionX où X est l'id du groupe d'options.
	$options = array(_request('id_option'));
	$groupes = sql_allfetsel('id_optionsgroupe', 'spip_optionsgroupes');
	foreach ($groupes as $groupe) {
		if ($id_option = _request('id_option' . $groupe['id_optionsgroupe'])) {
			$options[] = $id_option;
		}
	}
	// On concatène pour passer les options à l'action remplir_panier.
	$options = join('|', array_filter($options));

	if($id_objet = _request('id_produit')) {
		$id_objet= intval(_request('id_produit'));
		$objet = 'produit';
	} else {
		$objet = _request('objet');
		$id_objet= intval(_request('id_objet'));
	}

	// On appelle l'action remplir_panier
	$remplir_panier=charger_fonction('remplir_panier','action');
	$remplir_panier($objet.'-'.$id_objet.'-'.$quantite.'-'.$negatif.'-'.$options);

}
