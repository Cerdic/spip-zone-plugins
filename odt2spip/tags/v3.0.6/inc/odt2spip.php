<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Indique si la commande 'libreoffice' est disponible sur ce serveur
 * @return bool
 */
function odt2spip_commande_libreoffice_disponible() {
	static $est_disponible = null;
	if (is_null($est_disponible)) {
		if (defined('_LIBREOFFICE_PATH') and _LIBREOFFICE_PATH) {
			$est_disponible = true;
		} else {
			$est_disponible = (bool)odt2spip_obtenir_commande_serveur('libreoffice');
		}
	}
	return $est_disponible;
}

/**
 * Obtient le chemin d'un executable sur le serveur.
 *
 * @param string $command
 *     Nom de la commande
 * @return string
 *     Chemin de la commande
 **/
function odt2spip_obtenir_commande_serveur($command) {
	static $commands = array();

	if (array_key_exists($command, $commands)) {
		return $commands[$command];
	}

	@exec("which $command", $output, $err);
	if (!$err and count($output) and $cmd = trim($output[0])) {
		spip_log("Commande '$command' trouvée dans $cmd", 'odtspip.' . _LOG_DEBUG);
		return $commands[$command] = $cmd;
	}

	spip_log("Commande '$command' introuvable sur ce serveur…", 'odtspip.' . _LOG_DEBUG);
	return $commands[$command] = '';
}


/**
 * Indique si une clé est autorisée à utiliser ce site comme
 * serveur de conversion
 */
function odt2spip_cle_autorisee($key) {
	include_spip('inc/config');
	// récupérer la liste des clés
	$keys = lire_config('odt2spip/authorized_keys');
	$keys = explode("\n", trim($keys));
	$keys = array_filter(array_map('trim', $keys));
	$liste = array();
	foreach ($keys as $line) {
		list($k, $nom) = explode(':', $line, 2);
		$liste[trim($k)] = trim($nom);
	}
	// tester si la clé est correcte
	$ok = in_array($key, array_keys($liste));
	if ($ok) {
		spip_log('Cle autorisée du site : ' . $liste[$key], 'odtspip.' . _LOG_INFO);
	} else {
		spip_log('Cle invalide utilisée : ' . $key, 'odtspip.' . _LOG_INFO);
	}
	// maintenir un temps fixe d’exécution, si possible
	if (function_exists('hash_equals')) {
		hash_equals($key, $key);
	}
	return $ok;
}

/**
 * Indique si un convertisseur de document est disponible
 *
 * C’est disponible si
 * - la commande libreoffice est disponible
 * - OU un serveur de conversion est indiqué
 *
 * @return bool
 */
function odt2spip_convertisseur_disponible() {
	static $est_disponible = null;
	if (is_null($est_disponible)) {
		include_spip('inc/config');
		if (odt2spip_commande_libreoffice_disponible()) {
			$est_disponible = true;
		} elseif (
			function_exists('curl_file_create') // php 5.5+
			and lire_config('odt2spip/serveur_api_url')
			and lire_config('odt2spip/serveur_api_cle')
		) {
			$est_disponible = true;
		} else {
			$est_disponible = false;
		}
	}
	return $est_disponible;
}

/**
 * Retourne la liste des extensions de documents acceptées
 * @param bool $accept True pour retourner au format 'accept' d’html5
 * @return string|string[]
 */
function odt2spip_liste_extensions_acceptees($accept = false) {
	if (odt2spip_convertisseur_disponible()) {
		// TODO: vérifier la liste des extensions possibles
		$liste = array('odt', 'doc', 'docx', 'html');
	} else {
		$liste = array('odt');
	}
	if ($accept) {
		return '.' . implode(',.', $liste);
	}
	return $liste;
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
	$id_auteur = (int)session_get('id_auteur');
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
		spip_log('charger_decompresser erreur zip ' . $zip->error_code . ' pour fichier ' . $fichier, 'odtspip.' . _LOG_ERREUR);
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
			return array(false, _T('odtspip:err_creer_nouvel_objet'));
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
			return array(false, _T('odtspip:err_decompresser_fichier'));
		}
	} catch (\Exception $e) {
		return array(false, _T('odtspip:err_decompresser_fichier'));
	}

	try {
		$rep_dezip = odt2spip_get_repertoire_temporaire();
	} catch (\Exception $e) {
		return array(false, _T('odtspip:err_repertoire_temporaire'));
	}

	// Création de l'array avec les parametres de l'article:
	// c'est ici que le gros de l'affaire se passe!
	$odt2spip_generer_sortie = charger_fonction('odt2spip_generer_sortie', 'inc');
	try {
		$champs = $odt2spip_generer_sortie($rep_dezip, $fichier);
	} catch (\Exception $e) {
		spip_log($e->getMessage(), 'odtspip.' . _LOG_ERREUR);
		return array(false, _T('odtspip:err_analyse_odt'));
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

	// si nécessaire attacher le fichier source à l'article
	if (!empty($options['attacher_fichier']) and !empty($options['fichier_source'])) {
		odt2spip_objet_lier_fichier($options['fichier_source'], $objet, $id_objet, $set['titre']);
	}

	// si nécessaire attacher le fichier odt généré à l'article
	if (
		!empty($options['attacher_fichier_odt']) and !empty($options['fichier_source'])
		and ($fichier != $options['fichier_source'] or !empty($options['attacher_fichier']))
	) {
		odt2spip_objet_lier_fichier($fichier, $objet, $id_objet, $set['titre']);
	}

	return true;
}

/**
 * Lie un fichier en tant que document d’un objet.
 *
 * @param string $fichier
 * @param string $objet
 * @param int $id_objet
 * @param string $titre
 */
function odt2spip_objet_lier_fichier($fichier, $objet, $id_objet, $titre) {
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

/**
 * Convertir un fichier vers le format odt en utilisant
 * un outil de conversion, local ou distant
 *
 * @param string $fichier_source
 * @return string|bool
 */
function odt2spip_convertir_fichier($fichier_source) {
	if (!odt2spip_convertisseur_disponible()) {
		return false;
	}
	if (odt2spip_commande_libreoffice_disponible()) {
		include_spip('inc/convertir_avec_libreoffice');
		$fichier = convertir_avec_libreoffice($fichier_source, 'odt');
		return $fichier;
	}
	if ($fichier = odt2spip_convertir_fichier_par_api($fichier_source)) {
		return $fichier;
	}
	return false;
}

/**
 * Convertir un fichier vers le format odt en utilisant
 * un serveur distant de conversion
 *
 * @param string $fichier_source
 * @return string|bool
 */
function odt2spip_convertir_fichier_par_api($fichier_source, $format = 'odt') {
	include_spip('inc/config');
	$api_url = lire_config('odt2spip/serveur_api_url');
	$api_key = lire_config('odt2spip/serveur_api_cle');
	if (!$api_url or !$api_key) {
		return false;
	}
	$api_url = rtrim($api_url, '/') . '/convert_to.api/' . $format;

	$post = array(
		'api_key' => $api_key,
		'file'=> curl_file_create(realpath($fichier_source))
	);

	// Poster la requête et récupérer le contenu du fichier
	// FIXME: idéalement il faudrait streamer le fichier retourné… mais comment ?
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $api_url);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
	$content = curl_exec($ch);
	curl_close($ch);

	// Écrire le nouveau fichier localement
	if ($content) {
		$fichier = dirname($fichier_source) . DIRECTORY_SEPARATOR . pathinfo($fichier_source, PATHINFO_FILENAME) . '.' . $format;
		if (file_put_contents($fichier, $content)) {
			spip_log('Fichier converti dans : ' . $fichier, 'odtspip.' . _LOG_DEBUG);
			return $fichier;
		}
	}

	return false;
}