<?php

if (!defined("_ECRIRE_INC_VERSION")) {
	return;
}

function formulaires_changer_statut_articles_charger_dist() {
	$trouver_table = charger_fonction('trouver_table', 'base');
	$desc          = $trouver_table('spip_articles');
	$statuts       = array();
	foreach ($desc['statut_titres'] as $key => $value) {
		$statuts[$key] = _T($value);
	}
	$valeurs = array(
		'statuts'           => $statuts,
		'articles'          => '',
		'filtre_rubrique'   => '',
		'filtre_date_debut' => '',
		'filtre_date_fin'   => '',
		'filtre_statut'     => '',
		'recherche'         => '',
		'confirmer'         => '',
		'statut_final'      => '',
	);

	return $valeurs;
}

function formulaires_changer_statut_articles_verifier_dist() {
	$erreurs = array();

	if (_request('confirmer') && !_request('statut_final')) {
		$erreurs['statut_final'] = _T('champ_obligatoire');
	}

	return $erreurs;
}

function formulaires_changer_statut_articles_traiter_dist() {
	include_spip('action/editer_article');
	include_spip('inc/invalideur');
	$retour = array();

	if (_request('confirmer')) {
		$id_articles = _request('articles');
		// changer le statut des articles
		foreach ($id_articles as $id_article) {
			$data = array('statut' => _request('statut_final'));
			article_instituer($id_article, $data);
			// invalider le cache
			suivre_invalideur("id='article/$id_article'");
		}

		// recalculer les secteurs et les statuts des rubriques et des articles
		include_spip('inc/rubriques');
		calculer_rubriques();
		propager_les_secteurs();
		
		$retour['message_ok'] = _T('statut_articles:statut_articles_modifies');
	}
	else {
		$retour['editable'] = 1;
	}

	return $retour;
}
