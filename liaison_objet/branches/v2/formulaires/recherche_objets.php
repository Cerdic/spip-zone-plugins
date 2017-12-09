<?php
if (!defined("_ECRIRE_INC_VERSION"))
	return;
function formulaires_recherche_objets_charger_dist($objet_dest = 'rubrique', $id_objet_dest, $lang = '') {
	include_spip('inc/config');
	// Les objets destinataires choisies
	$special = array(
		'article',
		'rubrique'
	);
	if (in_array($objet_dest, $special))
		$choisies = picker_selected(lire_config('liaison_objet/liaison_' . $objet_dest . '_dest', array()), $objet_dest);
	else
		$choisies = lire_config('liaison_objet/liaison_' . $objet_dest . '_dest', array());

	// Quelques objets ne sont pas conforme, on adapte
	$exceptions = charger_fonction('exceptions', 'inc');

	// On garde l'objet original pour la détection des données de l'objet
	$objet_dest_original = $objet_dest;
	$e = trouver_objet_exec($objet_dest);
	$objet_dest = $e['type'] ? $e['type'] : $objet_dest;

	// Déterminer le bon objet
	$e = trouver_objet_exec($objet);
	$objet = $e['type'] ? $e['type'] : $objet;

	// Les types liens pour l'objet concerné
	if (!$types = lire_config('liaison_objet/type_liens_' . $objet_dest_original, array()))
		$types = lire_config('liaison_objet/type_liens', array());

	$types_lien = array();
	foreach ($types as $cle => $valeur) {
		if ($valeur)
			$types_lien[$cle] = _T($valeur);
	}

	$url_recherche = generer_url_public('recherche_objet', 'langue=' . $lang . '&objet_dest=' . $objet_dest . '&id_objet_dest=' . $id_objet_dest, true);

	$valeurs = array(
		"id_objet" => $id_objet,
		"objet" => $objet,
		"lang" => $lang,
		"objet_dest" => $objet_dest,
		"id_objet_dest" => $id_objet_dest,
		'types_lien' => $types_lien,
		'type_lien' => '',
		'objet_sel' => '',
		'label_objet' => _T('liaison_objet:ajouter_objet'),
		'label_lien' => _T('liaison_objet:liaison_type_lien'),
		'url_recherche' => $url_recherche
	);

	return $valeurs;
}
function formulaires_recherche_objets_verifier_dist($objet_dest = 'rubrique', $id_objet_dest, $lang = '') {
	include_spip('inc/config');
	$config = lire_config('liaison_objet');

	$erreurs = array();

	if (!_request('objet_sel'))
		$erreurs['objet_sel'] = _T("info_obligatoire");
	else {
		list($id_objet, $objet) = explode('-', _request('objet_sel'));
		$where = array(
			'id_objet_dest=' . $id_objet_dest,
			'objet_dest=' . sql_quote($objet_dest),
			'objet=' . sql_quote($objet),
			'id_objet=' . $id_objet,
			'lang=' . sql_quote($lang)
		);
		if (!isset($config['choix_illimite']) and $id = sql_getfetsel('id_liaison_objet', 'spip_liaison_objets', $where))
			$erreurs['objet_sel'] = _T("liaison_objet:erreur_deja_lie");
	}

	return $erreurs;
}

/* @annotation: Actualisation de la base de donnée */
function formulaires_recherche_objets_traiter_dist($objet_dest = 'rubrique', $id_objet_dest, $lang = '') {
	$type_lien = _request('type_lien');
	$valeurs = array(
		'type_lien' => $type_lien
	);
	unset($valeurs['objet_sel']);
	$instituer_objet = charger_fonction('instituer_objet_lie', 'action/');

	list($id_objet, $objet) = explode('-', _request('objet_sel'));

	$id_liaison_objet = $instituer_objet($id_objet . '-' . $objet . '-' . $lang . '-' . $lang . '-' . $objet_dest . '-' . $id_objet_dest . '-' . $type_lien);

	if ($id_liaison_objet)
		$valeurs['message_ok'] = 'ok';

	return $valeurs;
}
