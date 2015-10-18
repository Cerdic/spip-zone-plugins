<?php

function formulaires_share_charger_dist() {
	$valeurs = array(
		'titre'     => '',
		'nom_site'  => '',
		'url_site'  => '',
		'texte'     => '',
		'id_parent' => '1',
		'ps'        => ''
	);

	return $valeurs;
}

function formulaires_share_verifier_dist() {
	$erreurs      = array();
	$obligatoires = array(
		'titre',
		'id_parent',
	);
	foreach ($obligatoires as $obligatoire) {
		if (!_request($obligatoire)) {
			$erreurs[$obligatoire] = _T('champ_obligatoire');
		}
	}

	return $erreurs;
}

function formulaires_share_traiter_dist() {
	$id_auteur_session = (int)$GLOBALS['visiteur_session']['id_auteur'];

	$titre       = _request('titre');
	$nom_site    = _request('nom_site');
	$id_rubrique = intval(_request('id_parent'));
	$url_site    = _request('url_site');
	$texte       = _request('texte');
	$ps          = _request('ps');

	// créer un article dans la bonne rubrique
	include_spip('action/editer_objet');
	$id_article_cree = objet_inserer('article', $id_rubrique);

	if ($id_article_cree) {
		// insérer les valeurs saisies
		$valeurs = array(
			'titre'    => $titre,
			'texte'    => $texte,
			'nom_site' => $nom_site,
			'url_site' => $url_site,
			'date'     => date('Y-m-d H:i:s'),
			'ps'       => $ps,
			'statut'   => 'publie',

		);
		objet_modifier('article', $id_article_cree, $valeurs);

		// associer l'auteur
		objet_associer(
			array('auteur' => $id_auteur_session),
			array('article' => $id_article_cree)
		);

		// ajout les mots clés
		if ($etiquettes = _request('tags')) {
			include_spip('inc/tag-machine');
			ajouter_mots($etiquettes, $id_article_cree, 'tags', 'articles', 'id_article');
		}

		// fermer la popup
		echo '<script language="JavaScript">self.close();</script>';
		exit;

	}
	else {
		return array('message_erreur' => _L("Erreur lors de la création de l'article"));
	}
}
