<?php
/**
 * Editer l'identifiant page d'un article
 *
 * @plugin     Pages Uniques
 * @copyright  2013
 * @author     RastaPopoulos
 * @licence    GNU/GPL
 * @package    SPIP\Pages\Formulaires
 * @link       https://contrib.spip.net/Pages-uniques
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function formulaires_editer_identifiant_page_charger($id_article, $retour = '') {
	$valeurs = array();
	$valeurs['champ_page'] = generer_info_entite($id_article, 'article', 'page');
	$valeurs['_saisie_en_cours'] = (_request('champ_page') !== null);
	return $valeurs;
}

/**
 * Identifier le formulaire en faisant abstraction des parametres qui
 * ne representent pas l'objet edite
 */
function formulaires_editer_identifiant_page_identifier_dist($id_article, $retour = '') {
	return serialize(array('article', $id_article));
}

/**
 * Verification avant traitement
 *
 * @param integer $id_article
 * @param string $retour
 * @return Array Tableau des erreurs
 */
function formulaires_editer_identifiant_page_verifier_dist($id_article, $retour = '') {
	$erreurs = array();
	return $erreurs;
}

/**
 * Traitement
 *
 * @param integer $id_article
 * @param string $retour
 * @return Array
 */
function formulaires_editer_identifiant_page_traiter_dist($id_article, $retour = '') {
	$res = array();
	if (
		_request('changer')
		and $page = _request('champ_page')
	) {
		include_spip('action/editer_objet');
		objet_modifier('article', $id_article, array('page' => $page));
	}

	set_request('champ_page');
	$res['editable'] = true;
	if ($retour) {
		$res['redirect'] = $retour;
	}

	return $res;
}
