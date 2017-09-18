<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Retourne le répertoire de stockage des documents à traiter
 * @return string
 * @throws \Exception
 */
function odt2spip_get_repertoire_temporaire() {
	// ss-rep temporaire specifique de l'auteur en cours: tmp/odt2spip/id_auteur/
	// => le créer s'il n'existe pas
	$base_dezip = _DIR_TMP . 'odt2spip/';  // avec / final
	if (!is_dir($base_dezip) and !sous_repertoire(_DIR_TMP, 'odt2spip')) {
		throw new \Exception(_T('odtspip:err_repertoire_tmp'));
	}

	include_spip('inc/session');
	$id_auteur = session_get('id_auteur');
	$rep_dezip = $base_dezip . $id_auteur . '/';

	if (!is_dir($rep_dezip) and !sous_repertoire($base_dezip, $id_auteur)) {
		throw new \Exception(_T('odtspip:err_repertoire_tmp'));
	}

	// $rep_pictures = $rep_dezip.'Pictures/';
	return $rep_dezip;
}

/**
 * Déplace un fichier posté dans un répertoire temporaire de travail
 * @return string
 * @throws \Exception
 */
function odt2spip_deplacer_fichier_upload($key) {
	$rep_dezip = odt2spip_get_repertoire_temporaire();

	// traitement d'un fichier envoyé par $_POST
	if (
		empty($_FILES[$key]['name'])
		or $_FILES[$key]['error'] != 0
		or !($fichier = $rep_dezip . addslashes($_FILES[$key]['name']))
	) {
		throw new \Exception(_T('odtspip:err_telechargement_fichier'));
	}

	include_spip('inc/documents');
	if (!deplacer_fichier_upload($_FILES[$key]['tmp_name'], $fichier, true)) {
		throw new \Exception(_T('odtspip:err_telechargement_fichier'));
	}

	return $fichier;
}

/**
 * Dézippe un fichier dans le répertoire temporaire d’odt2spip
 * @param string $fichier Chemin du fichier ODT
 * @return bool
 * @throws \Exception
 */
function odt2spip_deziper_fichier($fichier) {
	$rep_dezip = odt2spip_get_repertoire_temporaire();

	// dezipper le fichier odt a la mode SPIP
	include_spip('inc/pclzip');
	$zip = new \PclZip($fichier);
	$ok = $zip->extract(
		PCLZIP_OPT_PATH,
		$rep_dezip,
		PCLZIP_OPT_SET_CHMOD,
		_SPIP_CHMOD,
		PCLZIP_OPT_REPLACE_NEWER
	);

	if ($zip->error_code < 0) {
		spip_log('charger_decompresser erreur zip ' . $zip->error_code . ' pour fichier ' . $fichier, 'odt2spip.' . _LOG_ERREUR);
		throw new \Exception($zip->errorName(true));
	}

	return ($ok > 0);
}

/**
 * Intègre le contenu du fichier dans l’objet indiqué (ou un nouvel enfant)
 *
 * @param string $fichier
 * @param string $objet
 * @param int $id_objet
 * @param string $objet_dest Nouvel objet enfant, si indiqué
 * @param array $options {
 *     @var bool attacher_fichier
 * }
 * @return array {
 *     @var bool|int $id_objet ou fales,
 *     @var string|null $errors,
 * }
 */
function odt2spip_integrer_fichier($fichier, $objet, $id_objet, $objet_dest = '', $options = array()) {
	list($champs, $erreurs) = odt2spip_analyser_fichier($fichier);
	if ($erreurs) {
		return array(false, $erreurs);
	}
	// si necessaire créer l'objet
	if ($objet_dest) {
		include_spip('action/editer_objet');
		$id_objet = objet_inserer($objet_dest, $id_objet);
		$objet = $objet_dest;
		if (!$id_objet) {
			return array(false, _L('Impossible de créer le nouvel objet'));
		}
	}

	odt2spip_objet_modifier($fichier, $objet, $id_objet, $champs, $options);

	// vider le contenu du rep de dezippage
	include_spip('inc/getdocument');
	effacer_repertoire_temporaire(odt2spip_get_repertoire_temporaire());

	// identifiant d’objet créé éventuellement.
	return array($id_objet, null);
}

/**
 * Analyse le fichier ODT transmis
 * @param string $fichier Chemin vers le fichier ODT
 * @return array
 */
function odt2spip_analyser_fichier($fichier) {
	try {
		if (!odt2spip_deziper_fichier($fichier)) {
			return array(false, _L('Impossible de décompresser le fichier'));
		}
	} catch (\Exception $e) {
		return array(false, _L('Impossible de décompresser le fichier'));
	}

	try {
		$rep_dezip = odt2spip_get_repertoire_temporaire();
	} catch (\Exception $e) {
		return array(false, _L('Impossible d’attribuer un répertoire temporaire'));
	}

	// Création de l'array avec les parametres de l'article:
	// c'est ici que le gros de l'affaire se passe!
	$odt2spip_generer_sortie = charger_fonction('odt2spip_generer_sortie', 'inc');
	try {
		$champs = $odt2spip_generer_sortie($rep_dezip, $fichier);
	} catch (\Exception $e) {
		spip_log($e->getMessage(), 'odt2spip.' . _LOG_ERREUR);
		return array(false, _L('Erreur lors de l’analyse du fichier ODT.'));
	}

	return array($champs, null);
}

/**
 * Modifie le contenu d’un objet avec les champs indiqués
 *
 * Note qu’une clé contient la liste des images.
 *
 * @param string $fichier
 * @param string $objet
 * @param int $id_objet
 * @param array $set
 * @param array $options
 * @return bool
 */
function odt2spip_objet_modifier($fichier, $objet, $id_objet, $set, $options = array()) {

	// le remplir
	include_spip('action/editer_objet');
	objet_modifier($objet, $id_objet, $set);

	// si necessaire recup les id_doc des images associées et les lier à l'article
	if (!empty($set['Timages']) > 0) {
		foreach ($set['Timages'] as $id_img) {
			$champs = array(
				'parents' => array($objet . '|' . $id_objet),
				'statut' => 'publie'
			);
			document_modifier($id_img, $champs);
		}
	}

	// si nécessaire attacher le fichier odt à l'article
	// et lui mettre un titre signifiant
	if (!empty($options['attacher_fichier'])) {
		$titre = $set['titre'];
		$ajouter_documents = charger_fonction('ajouter_documents', 'action');
		$id_document = $ajouter_documents(
			'new',
			array(
				array(
					'tmp_name' =>  $fichier,
					'name' => basename($fichier),
					'titrer' => 0,
					'distant' => 0,
					'type' => 'document'
				),
			),
			$objet,
			$id_objet,
			'document'
		);
		if (
			$id_document
			and $id_doc_odt = intval($id_document[0])
			and $id_doc_odt == $id_document[0]
		) {
			$c = array(
				'titre' => $titre,
				'descriptif' => _T('odtspip:cet_article_version_odt'),
				'statut' => 'publie'
			);
			document_modifier($id_doc_odt, $c);
		}
	}

	return true;
}