<?php

function formulaires_unsplash_ajout_charger_dist() {
	// Contexte du formulaire.
	$contexte = array();

	return $contexte;
}

/*
*   Fonction de vérification, cela fonction avec un tableau d'erreur.
*   Le tableau est formater de la sorte:
*   if (!_request('NomErreur')) {
*       $erreurs['message_erreur'] = '';
*       $erreurs['NomErreur'] = '';
*   }
*   Pensez à utiliser _T('info_obligatoire'); pour les éléments obligatoire.
*/
function formulaires_unsplash_ajout_verifier_dist() {
	$erreurs = array();
	include_spip('base/abstract_sql');
	$id_new = _request('id_new');
	$mode = _request('mode');
	$id_objet = _request('id_objet');
	$objet = _request('objet');
	$where = array('id=' . $id_new, 'mode=' . sql_quote($mode));
	if (isset($id_objet) and isset($objet)) {
		$where[] = 'id_objet=' . $id_objet;
		$where[] = 'objet=' . sql_quote($objet);
	}
	/* On vérifie que la photo n'a pas déjà été importé. */
	$deja_upload = sql_countsel('spip_unsplash', $where);
	if ($deja_upload) {
		$erreurs['message_erreur'] = _T('unsplash:photo_deja_importe');
	}
	$id_new = trim($id_new);
	if (empty($id_new) or !isset($id_new)) {
		$erreurs['message_erreur'] = _T('unsplash:photo_non_selectionnee');
	}
	$obligatoires = array('width', 'height');
	foreach ($obligatoires as $obligatoire) {
		$valeur = trim(_request($obligatoire));
		// La valeur renseignée ne doit pas être nulle ni vide
		if (empty($valeur) or !isset($valeur)) {
			$erreurs[$obligatoire] = _T('info_obligatoire');
		}
		// La valeur doit être un nombre positif et supérieur à 0.
		if ($valeur = intval($valeur) and !is_int($valeur) and ($valeur == 0 or $valeur < 0)) {
			$erreurs[$obligatoire] = _T('unsplash:valeur_entiere_attendue');
		}
	}

	return $erreurs;
}

function formulaires_unsplash_ajout_traiter_dist() {
	include_spip('base/abstract_sql');
	include_spip('base/objets');
	include_spip('inc/distant');
	include_spip('inc/documents');
	include_spip('inc/utils');
	$extension = 'jpg';
	// Traitement du formulaire.
	$_width = _request('width');
	$_height = _request('height');
	$_greyscale = _request('greyscale');
	$mode = _request('mode');
	$_associer_objet = _request('associer_objet');
	if ($_associer_objet and $_associer_objet = explode('|', $_associer_objet) and is_array($_associer_objet)) {
		$objet = $_associer_objet[0];
		$id_objet = $_associer_objet[1];
	}
	$id_new = _request('id_new');

	// Par défaut, on retourne une erreur
	$resultats = array('editable' => true, 'message_erreur' => _T('unsplash:erreur_formulaire'), 'redirect' => '');

	// On reprend le fichier json pour récupérer les infos de l'image à importer
	$unsplash_list = json_decode(file_get_contents(_UNSPLASH_JSON), true);
	// On cherche l'index de l'image sélectionnée pour la stocker
	$_index_photo = array_search($id_new, array_column($unsplash_list, 'id'));
	$photo_infos = $unsplash_list[$_index_photo];

	if (is_array($photo_infos)) {
		$photo_infos['date_ajout'] = date('Y-m-d H:i:s'); /* On indique la date d'ajout */
		$photo_infos['mode'] = $mode; /* On indique le mode de document pour lequel est utilisé cette photo : document, normal, survol */
		if ($mode == 'document') {
			$photo_infos['objet'] = objet_type('documents');
		}
	}
	/*
	 * On stocke l'extension du fichier mais si on a l'extension 'jpeg', on la reformate en 'jpg'
	 */
	$extension = $photo_infos['format'];
	if ($photo_infos['format'] === 'jpeg') {
		$extension = 'jpg';
	}
	// Récupérer le nom du fichier image.
	$import_filename = explode('.', $photo_infos['filename']);
	$import_filename = $import_filename[0];
	/*
	 * On construit l'url vers le fichier
	 * Si la case greyscale a été coché, alors on récupère la photo en niveaux de gris.
	 */
	$import_distant = _UNSPLASH_URL . (isset($_greyscale) ? 'g/' : '') . $_width . '/' . $_height . '/?image=' . $id_new;
	$import_photo = _DIR_RACINE . copie_locale($import_distant);
	/*
	 * On est ici dans le cadre d'un import d'une photo Unsplash en tant que document
	 */
	if ($mode === 'document' and $import_photo) {
		$import_dir = _DIR_IMG . $extension . '/';
		$import_destination = $import_dir . $import_filename . '.' . $extension;
		$import_result = deplacer_fichier_upload($import_photo, $import_destination, true);
		/*
		 * Si la copie du fichier physique a réussi, on peut créer
		 * le tableau d'informations à enregistrer dans la base de données.
		 * cf. spip_documents
		 */
		if ($import_result) {
			$document_info = array(
				'extension' => $extension,
				'date' => $photo_infos['date_ajout'],
				'fichier' => set_spip_doc($import_destination),
				'taille' => filesize($import_destination),
				'largeur' => $_width,
				'hauteur' => $_height,
				'mode' => 'image',
				'statut' => 'prepa',
				'distant' => 'non',
				'date_publication' => $photo_infos['date_ajout'],
				'credits' => '[' . $photo_infos['author'] . '->' . $photo_infos['author_url'] . ']',
				'media' => 'image',
			);
			$_id_document = sql_insertq('spip_documents', $document_info);
			$photo_infos['id_photo'] = $photo_infos['id']; // On stocke l'id du json en tant que id_photo
			unset($photo_infos['id']); // On supprime l'index 'id' car on n'en a plus besoin
			$photo_infos['id_objet'] = $_id_document; // On indique l'identifiant du document fraichement inséré dans la BDD
			$photo_infos['objet'] = objet_type('documents'); // On indique l'objet
			sql_insertq('spip_unsplash', $photo_infos); // On insère le tout dans spip_unsplash
			$resultats = array(
				'editable' => true,
				'message_ok' => _T('unsplash:importation_reussie_document'),
				'redirect' => generer_url_ecrire('unsplash'),
			);
		}
	} elseif ($import_photo) {
		/*
		 * Ici on importe une photo Unsplash en tant que logo
		 */
		include_spip('inc/chercher_logo');
		$import_dir = _DIR_LOGOS;
		$id_table_objet = id_table_objet($objet); // On cherche la clé primaire de l'objet
		$_mode_logo = ($mode === 'normal') ? 'on' : 'off'; // Le mode du logo désiré
		$import_destination = $import_dir . type_du_logo($id_table_objet) . $_mode_logo . $id_objet . '.' . $extension; // On construit le futur logo de l'objet
		$import_result = deplacer_fichier_upload($import_photo, $import_destination,
			true); // On déplace la photo Unsplash vers le logo de l'objet
		$photo_infos['id_photo'] = $photo_infos['id']; // On stocke l'id du json en tant que id_photo
		unset($photo_infos['id']); // On supprime l'index 'id' car on n'en a plus besoin
		$photo_infos['id_objet'] = $id_objet;
		$photo_infos['objet'] = $objet;
		if ($import_result) {
			sql_insertq('spip_unsplash', $photo_infos); // On insère la trace de la photo dans la BDD.
			$resultats = array(
				'editable' => true,
				'message_ok' => _T('unsplash:importation_reussie_logo'),
				'redirect' => generer_url_ecrire($objet, $id_table_objet . '=' . $id_objet),
			);
		}
	}

	// Donnée de retour.
	return $resultats;
}
