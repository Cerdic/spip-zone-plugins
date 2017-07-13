<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

include_spip('inc/session');

/**
 * Chercher dans les groupes de saisies une saisie upload
 *
 * @param array $saisies
 * @access public
 * @return array
 */
function chercher_saisie_upload($saisies) {
	include_spip('inc/saisies');

	$saisie_upload = array();
	foreach ($saisies as $key => $saisie) {
		if ($saisie['saisie'] == 'upload') {
			$saisie_upload = $saisies[$key];
		}

		// récursivité au besoin
		if (isset($saisie['saisies'])) {
			$saisie_upload = chercher_saisie_upload($saisie['saisies']);
		}
	}

	return $saisie_upload;
}

/**
 * Fonction qui renvoie les documents uploader dans un tableau
 * utilisable par objet_associer
 *
 * @access public
 * @return mixed
 */
function saisie_upload_get() {
	// récupérer les documents en session
	$documents = session_get('upload');

	if (empty($documents)) {
		return false;
	}

	// On va renvoyer un tableau formaté pour passer dans objet_associer
	return array('document' => $documents);
}

/**
 * Détruire la session d'upload quand on à terminé
 *
 * @access public
 */
function saisie_upload_terminer() {
	session_set('upload', null);
}

/**
 * Supprimer un document de la session
 *
 * @param mixed $id_document
 * @access public
 */
function saisie_supprimer_document_session($id_document) {
	$upload = session_get('upload');
	unset($upload[array_search($id_document, $upload)]);
	session_set('upload', $upload);
}

/**
 * Traiter une saisie upload.
 * Basiquement, on associe les documents à un objet spécifique
 * Ensuite on nettoye la session
 *
 * @param string $objet
 * @param int $id_objet
 * @param bool $lien_direct Gerer un champ id_document dans l'objet ?
 * @param string $statut Gerer le statut d'insertion des documents en base
 * @access public
 */
function saisie_upload_traiter($objet, $id_objet, $lien_direct = false, $statut = 'publie') {

	include_spip('action/editer_objet');
	include_spip('action/editer_liens');

	// Récupérer les documents et associer à l'objet
	$documents = saisie_upload_get();

	if (!$documents) {
		return false;
	}

	if (!$lien_direct) {
		objet_associer($documents, array($objet => $id_objet));
	} else {
		// Traitement des liens directs entre les objets
		// Lorsqu'il y a un champ id_document sur un objet
		$table = table_objet_sql($objet);
		$cle_primaire = id_table_objet($objet);
		sql_updateq(
			$table,
			array('id_document' => $documents['document'][0]),
			$cle_primaire.'='.$id_objet
		);
	}

	// Le lien est fait, les documents ne doivent plus être en mode temporaire
	foreach ($documents['document'] as $id_document) {
		objet_instituer('document', $id_document, array('statut' => $statut));
	}

	// Terminer l'upload en nettoyant la session
	saisie_upload_terminer();
}

/**
 * Une fonction pour traiter les logos via la saisie upload
 *
 * @param mixed $objet
 * @param mixed $id_objet
 * @param bool $supprimer
 *		  Supprime le fichier logo de la session et de la médiathèque si true
 * @access public
 */
function saisie_upload_traiter_logo($objet, $id_objet, $supprimer = true) {

	// On prend le premier fichier image de la saisie et on le transforme en logo
	$documents = saisie_upload_get();

	if (!$documents) {
		return false;
	}

	$fichier = sql_fetsel(
		'id_document,fichier',
		'spip_documents',
		array(
			'media='.sql_quote('image'),
			sql_in('id_document', $documents['document'])
		),
		'',
		'',
		'0,1'
	);

	// On utilise ce fichier de la médiathèque comme logo
	include_spip('uploadhtml5_fonctions');
	uploadhtml5_uploader_logo($objet, $id_objet, _DIR_IMG.$fichier['fichier']);

	// Supprime le fichier logo de la session et de la médiathèque
	if ($supprimer) {
		saisie_supprimer_document_session($fichier['id_document']);
		$supprimer_document = charger_fonction('supprimer_document_tmp', 'action');
		$supprimer_document($fichier['id_document']);
	}
}

/**
 * fonction pour charger des documents dans la session
 *
 * @param mixed $id_document
 * @access public
 */
function saisie_upload_charger($id_document) {
	$uploads = session_get('upload') ?: array();
	$uploads[] = $id_document;
	session_set('upload', $uploads);
}
