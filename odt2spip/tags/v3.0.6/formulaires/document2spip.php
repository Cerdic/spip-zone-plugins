<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


/**
 * Charger le contenu d’un document dans un objet SPIP.
 *
 * Le document est transformé en document ODT (si possible),
 * puis la transformation odt2spip est appliquée.
 *
 * @param string $objet
 * @param int $id_objet
 * @param null|string $creer_objet
 *     Si définie, créera un nouvel objet enfant de $objet/$id_objet.
 *     Note que seul le cas 'article' dans une rubrique est actuellement géré.
 * @param string $retour
 * @return array|false
 */
function formulaires_document2spip_charger_dist($objet, $id_objet, $creer_objet = null, $retour = '') {
	include_spip('inc/config');

	if ($creer_objet and !autoriser('creer' . objet_type($creer_objet) . 'dans', $objet, $id_objet)) {
		return false;
	} elseif (!autoriser('modifier', $objet, $id_objet)) {
		return false;
	}

	include_spip('inc/odt2spip');
	$valeurs = array(
		'objet' => $objet,
		'id_objet' => $id_objet,
		'creer_objet' => $creer_objet,
		'attacher_fichier' => lire_config('odt2spip/defaut_attacher'),
		'attacher_fichier_odt' => lire_config('odt2spip/defaut_attacher_odt_genere'),
		'mode_image' => 'image',
		// interne.
		'_conversion_disponible' => odt2spip_convertisseur_disponible(),
		'_bigup_rechercher_fichiers' => true,
		'_accept' => odt2spip_liste_extensions_acceptees(true),
		'_selecteur_langue' => (bool)lire_config('langues_multilingue'),
	);

	return $valeurs;
}

/**
 * Vérifier
 *
 * @param string $objet
 * @param int $id_objet
 * @param null|string $creer_objet
 *     Si définie, créera un nouvel objet enfant de $objet/$id_objet.
 *     Note que seul le cas 'article' dans une rubrique est actuellement géré.
 * @param string $retour
 * @return array
 */
function formulaires_document2spip_verifier_dist($objet, $id_objet, $creer_objet = null, $retour = '') {
	$erreurs = array();

	if (!in_array(_request('mode_image'), array('image', 'document'))) {
		$erreurs['mode_image'] = _T('info_obligatoire');
	}

	include_spip('inc/odt2spip');
	$extensions_acceptees = odt2spip_liste_extensions_acceptees();

	if (empty($_FILES['fichier']['name'])) {
		$erreurs['fichier'] = _T('info_obligatoire');
	} elseif ($_FILES['fichier']['error'] != 0) {
		$erreurs['fichier'] = _T('odtspip:err_recuperer_fichier');
	} elseif (!in_array(strtolower(pathinfo($_FILES['fichier']['name'], PATHINFO_EXTENSION)), $extensions_acceptees)) {
		$erreurs['fichier'] = _T('odtspip:err_extension_fichier', array('extension' => implode(', ', $extensions_acceptees)));
	}

	return $erreurs;
}

/**
 * Traiter
 *
 * @param string $objet
 * @param int $id_objet
 * @param null|string $creer_objet
 *     Si définie, créera un nouvel objet enfant de $objet/$id_objet.
 *     Note que seul le cas 'article' dans une rubrique est actuellement géré.
 * @param string $retour
 * @return array
 */
function formulaires_document2spip_traiter_dist($objet, $id_objet, $creer_objet = null, $retour = '') {
	$res = array(
		'editable' => true,
	);

	include_spip('inc/odt2spip');
	try {
		$fichier_source = odt2spip_deplacer_fichier_upload('fichier');
	} catch (\Exception $e) {
		$res['message_erreur'] = $e->getMessage();
		return $res;
	}

	// Si le fichier n’est pas un document odt, le traduire.
	$extension = strtolower(pathinfo($fichier_source, PATHINFO_EXTENSION));
	if ($extension !== 'odt') {
		try {
			$fichier = odt2spip_convertir_fichier($fichier_source);
			if (!$fichier) {
				$res['message_erreur'] = _T('odtspip:err_convertir_fichier');
				return $res;
			}
		} catch (\Exception $e) {
			spip_log($e->getMessage(), 'odtspip.' . _LOG_ERREUR);
			$res['message_erreur'] = _T('odtspip:err_convertir_fichier');
			return $res;
		}
	} else {
		$fichier = $fichier_source;
	}

	list($id, $erreurs) = odt2spip_integrer_fichier(
		$fichier,
		$objet,
		$id_objet,
		$creer_objet,
		array(
			'attacher_fichier' => _request('attacher_fichier'),
			'fichier_source' => $fichier_source,
			'attacher_fichier_odt' => _request('attacher_fichier_odt'),
		)
	);

	if (!$id) {
		$res['message_erreur'] = $erreurs;
		return $res;
	}

	$res['redirect'] = generer_url_entite($id, $creer_objet ? $creer_objet : $objet);
	$res['message_ok'] = _T('odtspip:fichier_traiter_ok');
	return $res;
}
