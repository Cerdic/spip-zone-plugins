<?php
/**
 * Plugin mailsubscribers
 * (c) 2012 Cédric Morin
 * Licence GNU/GPL v3
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

include_spip('inc/actions');
include_spip('inc/mailsubscribers');
include_spip('inc/editer');

/**
 * Identifier le formulaire en faisant abstraction des parametres qui ne representent pas l'objet edite
 */
function formulaires_editer_mailsubscribinglist_identifier_dist(
	$id_mailsubscribinglist = 'new',
	$retour = '',
	$lier_trad = 0,
	$config_fonc = '',
	$row = array(),
	$hidden = ''
) {
	return serialize(array(intval($id_mailsubscribinglist)));
}

/**
 * Declarer les champs postes et y integrer les valeurs par defaut
 */
function formulaires_editer_mailsubscribinglist_charger_dist(
	$id_mailsubscribinglist = 'new',
	$retour = '',
	$lier_trad = 0,
	$config_fonc = '',
	$row = array(),
	$hidden = ''
) {
	$valeurs = formulaires_editer_objet_charger('mailsubscribinglist', $id_mailsubscribinglist, '', $lier_trad, $retour,
		$config_fonc, $row, $hidden);

	return $valeurs;
}

/**
 * Verifier les champs postes et signaler d'eventuelles erreurs
 */
function formulaires_editer_mailsubscribinglist_verifier_dist(
	$id_mailsubscribinglist = 'new',
	$retour = '',
	$lier_trad = 0,
	$config_fonc = '',
	$row = array(),
	$hidden = ''
) {

	$oblis = array('titre');
	if (_request('adresse_envoi_nom')) {
		$oblis[] = 'adresse_envoi_email';
	}

	$erreurs = formulaires_editer_objet_verifier('mailsubscribinglist', $id_mailsubscribinglist, $oblis);

	if (!isset($erreurs['titre'])) {
		$id = _request('identifiant');
		if (!$id) {
			include_spip("inc/charsets");
			$id = translitteration(trim(_request('titre')));
			$id = preg_replace(',\W+,Uims', '_', $id);
			$id = trim(strtolower($id), '_');
			$suff = 0;
			$id_suff = $id;
			while (sql_countsel('spip_mailsubscribinglists',
				'identifiant=' . sql_quote($id_suff) . ' AND id_mailsubscribinglist!=' . intval($id_mailsubscribinglist))) {
				$suff++;
				$id_suff = $id . '_' . $suff;
			}
			$id = $id_suff;
			set_request('identifiant', $id);
		}
		if ($id !== strtolower($id) or preg_match(',\W,', $id)) {
			include_spip("inc/charsets");
			$id = translitteration(trim($id));
			$id = preg_replace(',\W+,Uims', '_', $id);
			$id = trim(strtolower($id), '_');
			set_request('identifiant', $id);
			$erreurs['identifiant'] = _T('mailsubscribinglist:erreur_identifiant_corrige');
		} elseif (sql_countsel('spip_mailsubscribinglists',
			'identifiant=' . sql_quote($id) . ' AND id_mailsubscribinglist!=' . intval($id_mailsubscribinglist))) {
			// TODO : gerer la confirmation de fusion ici
			$erreurs['identifiant'] = _T('mailsubscribinglist:erreur_identifiant_existe_deja');
		}
	}

	return $erreurs;
}

/**
 * Traiter les champs postes
 */
function formulaires_editer_mailsubscribinglist_traiter_dist(
	$id_mailsubscribinglist = 'new',
	$retour = '',
	$lier_trad = 0,
	$config_fonc = '',
	$row = array(),
	$hidden = ''
) {
	$res = formulaires_editer_objet_traiter('mailsubscribinglist', $id_mailsubscribinglist, '', $lier_trad, $retour,
		$config_fonc, $row, $hidden);

	return $res;
}

