<?php
/**
 * Plugin Acces Restreint 3.0 pour Spip 2.0
 * Licence GPL (c) 2006-2008 Cedric Morin
 *
 */
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

include_spip('inc/actions');
include_spip('inc/editer');

/**
 * Identifier le formulaire en faisant abstraction des paramètres qui ne représentent pas l'objet edité
 *
 * @param int|string $id_chapitre
 *     Identifiant de la zone. 'new' pour une nouvelle zone.
 * @param string $retour
 *     URL de redirection après le traitement
 * @param string $associer_objet
 *     Objet à associer, de la forme 'objet|id_objet'
 * @param string $config_fonc
 *     Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array $row
 *     Valeurs de la ligne SQL de la zone, si connu
 * @param string $hidden
 *     Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 * @return string
 *     Hash du formulaire
 */
function formulaires_editer_zone_identifier_dist($id_zone = 'new', $retour = '', $associer_objet = '', $config_fonc = 'zones_edit_config', $row = array(), $hidden = '') {
	return serialize(array(intval($id_zone)));
}

/**
 * Chargement du formulaire d'édition d'une zone
 *
 * Déclarer les champs postés et y intégrer les valeurs par défaut
 *
 * @param int|string $id_chapitre
 *     Identifiant de la zone. 'new' pour une nouvelle zone.
 * @param string $retour
 *     URL de redirection après le traitement
 * @param string $associer_objet
 *     Objet à associer, de la forme 'objet|id_objet'
 * @param string $config_fonc
 *     Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array $row
 *     Valeurs de la ligne SQL de la zone, si connu
 * @param string $hidden
 *     Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 * @return string
 *     Hash du formulaire
 */
function formulaires_editer_zone_charger_dist($id_zone = 'new', $retour = '', $associer_objet = '', $config_fonc = 'zones_edit_config', $row = array(), $hidden = '') {
	$valeurs = formulaires_editer_objet_charger('zone', $id_zone, 0, 0, $retour, $config_fonc, $row, $hidden);
	include_spip('inc/accesrestreint');
	// charger les rubriques associees a la zone
	if ($id_zone = intval($id_zone)) {
		$valeurs['rubriques'] = accesrestreint_liste_contenu_zone_rub_direct($id_zone);
	} {
		// cas d'une creation, regardons si l'url propose deja des rubriques
		if ($r = _request('rubriques')) {
			if (is_numeric($r)) {
				$r = array($r);
			}
			if (is_array($r)) {
				$valeurs['rubriques'] = $r;
			}
		}
	}

	return $valeurs;
}

/**
 * Fonction ajoutant des configurations particulières au formulaire d'édition d'une zone
 *
 * @return array
 */
function zones_edit_config() {
	return array();
}

/**
 * Vérification de champs postés du formulaire d'édition d'une zone
 *
 * @param int|string $id_chapitre
 *     Identifiant de la zone. 'new' pour une nouvelle zone.
 * @param string $retour
 *     URL de redirection après le traitement
 * @param string $associer_objet
 *     Objet à associer, de la forme 'objet|id_objet'
 * @param string $config_fonc
 *     Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array $row
 *     Valeurs de la ligne SQL de la zone, si connu
 * @param string $hidden
 *     Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 * @return string
 *     Hash du formulaire
 */
function formulaires_editer_zone_verifier_dist($id_zone = 'new', $retour = '', $associer_objet = '', $config_fonc = 'zones_edit_config', $row = array(), $hidden = '') {
	$erreurs = formulaires_editer_objet_verifier('zone', $id_zone, array('titre'));
	return $erreurs;
}

/**
 * Traitement du formulaire d'édition d'une zone
 *
 * @param int|string $id_chapitre
 *     Identifiant de la zone. 'new' pour une nouvelle zone.
 * @param string $retour
 *     URL de redirection après le traitement
 * @param string $associer_objet
 *     Objet à associer, de la forme 'objet|id_objet'
 * @param string $config_fonc
 *     Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array $row
 *     Valeurs de la ligne SQL de la zone, si connu
 * @param string $hidden
 *     Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 * @return string
 *     Hash du formulaire
 */
function formulaires_editer_zone_traiter_dist($id_zone = 'new', $retour = '', $associer_objet = '', $config_fonc = 'zones_edit_config', $row = array(), $hidden = '') {
	if (_request('publique') !== 'oui') {
		set_request('publique', 'non');
	}
	if (_request('privee') !== 'oui') {
		set_request('privee', 'non');
	}
	if (_request('autoriser_si_connexion') !== 'oui') {
		set_request('autoriser_si_connexion', 'non');
	}

	$res = formulaires_editer_objet_traiter('zone', $id_zone, 0, 0, $retour, $config_fonc, $row, $hidden);

	if ($retour and $res['id_zone']) {
		$res['redirect'] = parametre_url($retour, 'id_zone', $res['id_zone']);
	}
	// Un lien auteur a prendre en compte ?
	if ($associer_objet and $id_zone = $res['id_zone']) {
		$objet = '';
		if (preg_match(',^\w+\|[0-9]+$,', $associer_objet)) {
			list($objet,$id_objet) = explode('|', $associer_objet);
		}
		if ($objet and $id_objet and autoriser('modifier', $objet, $id_objet)) {
			zone_lier($id_zone, $objet, $id_objet);
			if (isset($res['redirect'])) {
				$res['redirect'] = parametre_url($res['redirect'], 'id_lien_ajoute', $id_zone, '&');
			}
		}
	}

	return $res;
}
