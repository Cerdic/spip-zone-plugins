<?php
/**
 * Gestion du formulaire de d'édition d'un contact
 *
 * @plugin Contacts & Organisations pour Spip 3.0
 * @license GPL (c) 2009 - 2013
 * @author Cyril Marion, Matthieu Marcillaud, Rastapopoulos
 *
 * @package SPIP\Contacts\Formulaires
**/

if (!defined('_ECRIRE_INC_VERSION')) {
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
 * @return array
 *     Environnement du formulaire
**/
function formulaires_editer_contact_charger_dist($id_contact = 'new', $id_organisation = 0, $redirect = '', $associer_objet = '') {
	$contexte = formulaires_editer_objet_charger('contact', $id_contact, $id_organisation, 0, $redirect, '');
	
	if (!intval($id_contact) and $id_annuaire = _request('id_annuaire')) {
		$contexte['id_annuaire'] = $id_annuaire;
	}
	
	return $contexte;
}


/**
 * Vérification du formulaire d'édition d'un contact
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
 * @return array
 *     Tableau des éventuelles erreurs
**/
function formulaires_editer_contact_verifier_dist($id_contact = 'new', $id_organisation = 0, $redirect = '', $associer_objet = '') {
	$erreurs = formulaires_editer_objet_verifier('contact', $id_contact);
	
	if ($editer_contact_verifier = charger_fonction('editer_contact_verifier', 'inc', true)){
		$erreurs = array_merge($erreurs, $editer_contact_verifier($id_contact, $id_organisation));
	}
	
	return $erreurs;
}

/**
 * Traitements du formulaire d'édition d'un contact
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
 * @return array
 *     Retour des traitements
**/
function formulaires_editer_contact_traiter_dist($id_contact = 'new', $id_organisation = 0, $redirect = '', $associer_objet = '') {
	$res = formulaires_editer_objet_traiter('contact', $id_contact, $id_organisation, 0, $redirect);

	include_spip('inc/config');
	if (
		!intval($id_contact)
		and lire_config('contacts_et_organisations/associer_aux_auteurs','') == 'obli'
		and $id_contact = $res['id_contact']
	) {
		$creer_auteur_lie = charger_fonction('creer_auteur_lie', 'action');
		$id_auteur = $creer_auteur_lie("contact/$id_contact");
	}

	// Un lien organisation ou autre a prendre en compte ?
	if ($associer_objet and $id_contact = $res['id_contact']) {
		$objet = '';
		if (intval($associer_objet)) {
			$objet = 'organisation';
			$id_objet = intval($associer_objet);
		}
		elseif(preg_match(',^\w+\|[0-9]+$,', $associer_objet)) {
			list($objet, $id_objet) = explode('|', $associer_objet);
		}
		
		if ($objet and $id_objet and autoriser('modifier', $objet, $id_objet)) {
			include_spip('action/editer_liens');
			// organisation sur spip_organisations_liens
			if ($objet == 'organisation') {
				objet_associer(array($objet => $id_objet), array('contact' => $id_contact));
			} else {
				objet_associer(array('contact' => $id_contact), array($objet => $id_objet));
			}
			if (isset($res['redirect']))
				$res['redirect'] = parametre_url($res['redirect'], "id_lien_ajoute", $id_contact, '&');
		}
	}

	return $res;
}
