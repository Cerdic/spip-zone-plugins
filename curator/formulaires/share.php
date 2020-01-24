<?php

function formulaires_share_charger_dist() {
	$valeurs = array(
		'titre'     => _request('titre'),
		'nom_site'  => '',
		'url_site'  => _request('url_site'),
		'texte'     => _request('extrait'),
		'logo'      => _request('logo'),
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
			$erreurs[$obligatoire] = _T('info_obligatoire');
		}
	}

	return $erreurs;
}

function formulaires_share_traiter_dist() {
	$id_auteur = (int)$GLOBALS['visiteur_session']['id_auteur'];

	$titre        = _request('titre');
	$nom_site     = _request('nom_site');
	$id_rubrique  = intval(_request('id_parent'));
	$url_site     = _request('url_site');
	$texte        = _request('texte');
	$ps           = _request('ps');
	$ajouter_logo = _request('ajouter_logo') == 'on' ? true : false;
	$logo         = _request('logo');
	$logo 		  = explode('?', $logo)[0];
	
	// créer un article dans la bonne rubrique
	include_spip('action/editer_objet');
	$id_article = objet_inserer('article', $id_rubrique);

	// statut de publication souhaité
	$statut = lire_config('curator/statut_souhaite','publie');

	if ($id_article) {
		// insérer les valeurs saisies
		$valeurs = array(
			'titre'    => $titre,
			'texte'    => $texte,
			'nom_site' => $nom_site,
			'url_site' => $url_site,
			'date'     => date('Y-m-d H:i:s'),
			'ps'       => $ps,
			'statut'   => $statut,
		);
		objet_modifier('article', $id_article, $valeurs);

		// associer l'auteur
		objet_associer(
			array('auteur' => $id_auteur),
			array('article' => $id_article)
		);

		// ajout les mots clés
		if ($etiquettes = _request('tags')) {
			include_spip('inc/tag-machine');
			$groupe_mots = sql_getfetsel('titre','spip_groupes_mots','id_groupe='.lire_config('curator/groupe_mots'));
			if( !$groupe_mots ) {
				$groupe_mots = 'Tags';
			}
			ajouter_mots($etiquettes, $id_article, $groupe_mots, 'articles', 'id_article');
		}

		// ajouter le logo
		if ($ajouter_logo && $logo) {
			include_spip('inc/distant');

			$info = pathinfo($logo);
			$ext = $info['extension'];
			$logo_file = _DIR_IMG.'arton'.$id_article.'.'.$ext;
			copie_locale($logo, 'force', $logo_file);
		}

		// fermer la popup
		echo '<script language="JavaScript">self.close();</script>';
		exit;

	}
	else {
		return array('message_erreur' => _L("Erreur lors de la création de l'article"));
	}
}
