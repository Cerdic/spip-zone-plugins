<?php
/**
 * Gestion du formulaire d'édition rapide d'un contact
 *
 * @plugin  InfoSites
 * @license GPL (c) 2016-2019
 * @author  Teddy Payet
 *
 * @package SPIP\InfoSites\Formulaires
 **/

if (!defined("_ECRIRE_INC_VERSION")) {
	return;
}

include_spip('inc/editer');


/**
 * Chargement du formulaire d'édition d'un contact
 *
 * @param int|string $id_contact
 *     Identifiant du contact. 'new' pour un nouveau contact.
 * @param int $id_organisation
 *     Identifiant de l'organisation parente, ou 0.
 * @param string $redirect
 *     URL de redirection après le traitement
 * @param string $associer_objet
 *     Éventuel 'objet|x' indiquant de lier le contact à cet objet,
 *     tel que 'article|3'
 *
 * @return array
 *     Environnement du formulaire
 **/
function formulaires_editer_contact_rapide_charger_dist(
	$id_contact = 'new',
	$id_organisation = 0,
	$redirect = '',
	$associer_objet = ''
) {
	$contexte = array();

	$champs = array(
		'civilite',
		'prenom',
		'nom',
		'fonction',
		'date_naissance',
		'descriptif',
		'titre_email',
		'email',
		'type_email',
		'titre_numero',
		'numero',
		'type_numero',
		'titre_adresse',
		'voie',
		'type_adresse',
		'complement',
		'boite_postale',
		'code_postal',
		'region',
		'ville',
		'pays'
	);

	foreach ($champs as $champ) {
		$contexte[$champ] = (_request($champ)) ? _request($champ) : '';
	}

	return $contexte;
}


/**
 * Vérification du formulaire d'édition rapide d'un contact
 *
 * @param int|string $id_contact
 *     Identifiant du contact. 'new' pour un nouveau contact.
 * @param int $id_organisation
 *     Identifiant de l'organisation parente, ou 0.
 * @param string $redirect
 *     URL de redirection après le traitement
 * @param string $associer_objet
 *     Éventuel 'objet|x' indiquant de lier le contact à cet objet,
 *     tel que 'article|3'
 *
 * @return array
 *     Tableau des éventuelles erreurs
 **/
function formulaires_editer_contact_rapide_verifier_dist(
	$id_contact = 'new',
	$id_organisation = 0,
	$redirect = '',
	$associer_objet = ''
) {
	include_spip('inc/utils');
	$erreurs = array();
	$champs = array(
		'civilite',
		'prenom',
		'nom',
		'fonction',
		'date_naissance',
		'descriptif',
		'titre_email',
		'email',
		'type_email',
		'titre_numero',
		'numero',
		'type_numero',
		'titre_adresse',
		'voie',
		'type_adresse',
		'complement',
		'boite_postale',
		'code_postal',
		'region',
		'ville',
		'pays'
	);

	$obligatoires = array('prenom', 'nom');
	foreach ($obligatoires as $obligatoire) {
		if (!_request($obligatoire)) {
			$erreurs[$obligatoire] = _T('info_obligatoire');
		}
	}
	if (_request('prenom') and _request('nom')) {
		include_spip('base/abstract_sql');
		$compteur = sql_countsel('spip_contacts', array("prenom=" . sql_quote('prenom'), "nom=" . sql_quote('nom')));
		spip_log(print_r($compteur, true), 'info_sites');
		if ($compteur > 0) {
			$erreurs['prenom'] = _T('info_sites:contact_existant');
		}
		$coordonnees = array('email', 'numero', 'adresse');
		foreach ($coordonnees as $coordonnee) {
			if (_request('titre_' . $coordonnee) and !_request($coordonnee)) {
				$erreurs[$coordonnee] = _T('info_obligatoire');
			}
		}
	}

	return $erreurs;
}

/**
 * Traitements du formulaire d'édition rapide d'un contact
 *
 * Crée l'enregistrement et l'association éventuelle avec un objet
 * indiqué
 *
 * @param int|string $id_contact
 *     Identifiant du contact. 'new' pour un nouveau contact.
 * @param int $id_organisation
 *     Identifiant de l'organisation parente, ou 0.
 * @param string $redirect
 *     URL de redirection après le traitement
 * @param string $associer_objet
 *     Éventuel 'objet|x' indiquant de lier le contact à cet objet,
 *     tel que 'article|3'
 *
 * @return array
 *     Retour des traitements
 **/
function formulaires_editer_contact_rapide_traiter_dist(
	$id_contact = 'new',
	$id_organisation = 0,
	$redirect = '',
	$associer_objet = ''
) {
	include_spip('base/abstract_sql');
	include_spip('inc/utils');
	$res = array();
	$champs_contact = array('civilite', 'prenom', 'nom', 'fonction', 'date_naissance');
	$champs_insert = array();
	foreach ($champs_contact as $champ_contact) {
		$champs_insert[$champ_contact] = _request($champ_contact);
	}
	$id_contact = sql_insertq('spip_contacts', $champs_insert);

	// Si on a bien un id pour le contact, on peut travailler.
	if (intval($id_contact)) {
		include_spip('action/editer_liens');
		// Association du contact à un objet.
		$associer_objet = _request('associer_objet');
		if ($associer_objet and preg_match('/|/', $associer_objet)) {
			$associer_objet = explode('|', $associer_objet);
			list($objet, $id_objet) = $associer_objet;
			objet_associer(array($objet => $id_objet), array('contact' => $id_contact));
		}
		// Adresse email
		if (_request('email')) {
			$id_email = sql_insertq('spip_emails',
				array('email' => _request('email'), 'titre' => _request('titre_email')));
			if (intval($id_email)) {
				objet_associer(array('email' => $id_email), array('contact' => $id_contact));
				// Indication du type email
				sql_updateq('spip_emails_liens', array('type' => _request('type_email')),
					"id_email=$id_email AND objet='contact' AND id_objet=$id_contact");
			}
		}
		// Le numero de téléphone
		if (_request('numero')) {
			$id_numero = sql_insertq('spip_numeros',
				array('numero' => _request('numero'), 'titre' => _request('titre_numero')));
			if (intval($id_numero)) {
				objet_associer(array('numero' => $id_numero), array('contact' => $id_contact));
				// Indication du type numero
				sql_updateq('spip_numeros_liens', array('type' => _request('type_numero')),
					"id_numero=$id_numero AND objet='contact' AND id_objet=$id_contact");
			}
		}
		// L'adresse postale.
		$check_adresse = _request('titre_adresse') . _request('voie') . _request('complement') . _request('boite_postale') . _request('code_postal') . _request('region') . _request('ville');
		$check_adresse = trim($check_adresse);
		if (strlen($check_adresse) > 0) {
			$id_adresse = sql_insertq('spip_adresses',
				array(
					'titre' => _request('titre_adresse'),
					'voie' => _request('voie'),
					'complement' => _request('complement'),
					'boite_postale' => _request('boite_postale'),
					'code_postal' => _request('code_postal'),
					'region' => _request('region'),
					'ville' => _request('ville'),
					'pays' => _request('pays'),
				));
			if (intval($id_adresse)) {
				objet_associer(array('adresse' => $id_adresse), array('contact' => $id_contact));
				// Indication du type d'adresse
				sql_updateq('spip_adresses_liens', array('type' => _request('type_adresse')),
					"id_adresse=$id_adresse AND objet='contact' AND id_objet=$id_contact");
			}
		}
		$res['message_ok'] = _T('enregistrement_ok');
		if (isset($objet) and isset($id_objet)) {
			$res['redirect'] = generer_url_entite($id_objet, $objet);
		} else {
			$res['redirect'] = generer_url_entite($id_contact, 'contact');
		}
	} else {
		$res['message_erreur'] = _T('enregistrement_ko');
	}

	return $res;
}
