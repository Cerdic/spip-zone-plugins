<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function formulaires_charger_taxonomie_charger() {
	$valeurs = array();
	include_spip('inc/taxonomer');

	// Lister les actions sur les règnes
	$valeurs['_actions_regne'] = array(
		'charger' => _T('taxonomie:label_action_charger_regne'),
		'vider' => _T('taxonomie:label_action_vider_regne')
	);

	// Acquérir la liste des règnes gérer par le plugin et leur statut de chargement
	// Désactiver l'action vider si aucun règne n'est chargé
	$aucun_regne_charge = true;
	$regnes = lister_regnes();
	foreach ($regnes as $_regne) {
		$valeurs['_regnes'][$_regne] = '<span class="nom_scientifique">' . $_regne . '</span>, ' . _T("taxonomie:regne_$_regne");
		if (taxonomie_regne_existe($_regne, $meta_regne)) {
			$valeurs['_regnes'][$_regne] .= ' [' . _T("taxonomie:info_regne_charge") . ']';
			$aucun_regne_charge = false;
		}
	}
	if ($aucun_regne_charge) {
		$valeurs['_actions_disable'] = array('vider' => 'oui');
		$valeurs['_action_defaut'] = 'charger';
	}

	// Acquérir la liste des rangs taxonomiques exception faite du règne et de l'espèce
	$rangs = taxonomie_lister_rangs(
		_TAXONOMIE_REGNE_ANIMAL,
		_TAXONOMIE_RANGS_PARENTS_ESPECE,
		array(_TAXONOMIE_RANG_REGNE));
	foreach ($rangs as $_rang) {
		$valeurs['_rangs'][$_rang] = ucfirst(_T("taxonomie:rang_${_rang}"));
	}
	$valeurs['_rang_defaut'] = _TAXONOMIE_RANG_GENRE;

	// Acquérir la liste des langues utilisables par le plugin
	// -> pour l'instant on se limite au français et à l'anglais quelque soit la langue du site
	$valeurs['_langues_regne'] = array('fr' =>  traduire_nom_langue('fr'), 'en' =>  traduire_nom_langue('en'));
	$valeurs['_langues_defaut'] = array('fr');

	return $valeurs;
}

function formulaires_charger_taxonomie_verifier() {
	$erreurs = array();

	$obligatoires = array('action_regne', 'regne');
	foreach ($obligatoires as $_obligatoire) {
		if (!_request($_obligatoire))
			$erreurs[$_obligatoire] = _T('info_obligatoire');
	}

	return $erreurs;
}

function formulaires_charger_taxonomie_traiter() {
	$retour = array();

	$action = _request('action_regne');
	$regne = _request('regne');
	$regne_existe = taxonomie_regne_existe($regne, $meta_regne);

	$ok = true;
	$item = '';
	if ($action == 'vider') {
		if ($regne_existe) {
			$ok = taxonomie_vider_regne($regne);
			$item = $ok ? 'taxonomie:succes_vider_regne' : 'taxonomie:erreur_vider_regne';
		}
		else {
			// Inutile de vider un règne non encore chargé
			$ok = false;
			$item = 'taxonomie:notice_vider_regne_inexistant';
		}
	}
	else {
		// La fonction de chargement du règne lance un vidage préalable si le règne
		// demandé est déjà chargé. Un mécanisme de sauvegarde interne permet aussi de
		// restituer les modifications manuelles des taxons
		$langues = _request('langues_regne');
		$rang_feuille = _request('rang_feuille');
		$ok = taxonomie_charger_regne($regne, $rang_feuille, $langues);
		$item = $ok ? 'taxonomie:succes_charger_regne' : 'taxonomie:erreur_charger_regne';
	}

	$message = $ok ? 'message_ok' : 'message_erreur';
	$retour[$message] = _T($item, array('regne' => '<i>' . ucfirst($regne) . '</i>'));
	$retour['editable'] = true;

	return $retour;
}

?>