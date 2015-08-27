<?php

/**
 * Gestion du formulaire de réassociation d'un auteur sur un autre
 *
 * @package SPIP\rao\Formulaires
**/

if (!defined("_ECRIRE_INC_VERSION")) return;


function formulaires_reassocier_auteur_objets_charger() {
	$valeurs = array(
		'auteur_source' => '',
		'auteur_destination' => '',
		'objets' => array('spip_articles'),
	);
	return $valeurs;
}


function formulaires_reassocier_auteur_objets_verifier() {
	$source = _request('auteur_source');
	$destination = _request('auteur_destination');
	$objets = _request('objets');

	$erreurs = array();

	if (!$source) {
		$erreurs['auteur_source'] = _T('info_obligatoire');
	}
	if (!$destination) {
		$erreurs['auteur_destination'] = _T('info_obligatoire');
	}
	if (!$objets) {
		$erreurs['objets'] = _T('info_obligatoire');
	}

	if ($erreurs) {
		return $erreurs;
	}

	// demande confirmée ?
	if (!_request('confirmer')) {

		include_spip('action/editer_liens');
		include_spip('inc/filtres');

		$liaisons = array_fill_keys(array_filter($objets), '*');
		$liens = objet_trouver_liens(array('auteur' => $source), $liaisons);
		$desc = array();

		// trier par objet
		foreach ($liens as $l) {
			if (!isset($desc[$l['objet']])) {
				$desc[$l['objet']] = array();
			}
			$desc[$l['objet']][] = $l;
		}

		ksort($desc);

		// présenter les résultats
		if (!$liens) {
			$message = _T("rao:aucune_liaison_trouvee") . "\n";
		} else {
			$message = singulier_ou_pluriel(count($liens), "rao:une_liaison_trouvee", "rao:nb_liaisons_trouvees");
			foreach ($desc as $objet => $d) {
				$message .= "\n-* " . objet_afficher_nb(count($d), $objet);
			}
		}
		$erreurs['_confirmer'] = $message;
		$erreurs['message_erreur'] = '';
	}

	return $erreurs;
}


function formulaires_reassocier_auteur_objets_traiter() {

	$source = _request('auteur_source');
	$destination = _request('auteur_destination');
	$objets = _request('objets');

	include_spip('action/editer_liens');

	$liaisons = array_fill_keys(array_filter($objets), '*');
	$liens = objet_trouver_liens(array('auteur' => $source), $liaisons);

	// associer au nouvel auteur
	$associations = array();
	foreach ($liens as $l) {
		if (!isset($associations[$l['objet']])) {
			$associations[$l['objet']] = array();
		}
		$associations[$l['objet']][] = $l['id_objet'];
	}
	objet_associer(array('auteur' => $destination), $associations);

	// Note: objet_dupliquer_liens() serait plus simple, mais
	// prendrait aussi et de préférence les liaisons spip_xx_liens
	// de l'objet (et pas uniquement spip_auteurs_liens)
	// ce qui n'est pas forcément ce qui est souhaité ici.
	#$types = array_map('objet_type', array_filter($objets));
	#objet_dupliquer_lienrs('auteur', $source, $destination, $types);

	// suppression
	objet_dissocier(array('auteur' => $source), $liaisons);

	$res = array(
		'editable' => true,
		'message_ok' => _T('rao:reassociation_effectuee'),
	);

	return $res;
}
