<?php
/**
 * Petit Cochon
 *
 * @plugin     Petit Cochon
 * @copyright  2016
 * @author     cyp
 * @licence    GNU/GPL
 * @package    SPIP\formulaires\petitcochon
 */

/**
 * Gestion du formulaire de petitcochon des sites 
 *
 * @package SPIP\Formulaires
**/
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

include_spip('inc/editer');
include_spip('action/editer_objet');

/**
 * Chargement du formulaire de configuration du petitcochon des sites
 *
 * @return array
 *     Environnement du formulaire
**/
function formulaires_editer_petitcochon_charger_dist($id_petitcochon = 'new', $objet = '', $id_objet = '', $retour = '', $ajaxload = 'oui', $options = '') {
	$valeurs = formulaires_editer_objet_charger('petitcochon', $id_petitcochon, '', '', $retour, '');

	return $valeurs;
	
}

/**
 * Vérifications du formulaire de configuration du petitcochon des sites
 *
 * @return array
 *     Tableau des erreurs
**/
function formulaires_editer_petitcochon_verifier_dist() {

	$erreurs = formulaires_editer_objet_verifier('petitcochon', $id_petitcochon, array('nom', 'poids'));
	if (_request('nom')) {
		$nom = sql_getfetsel('nom', 'spip_petitcochon', 'nom=' . sql_quote(_request('nom')));
		if ($nom == _request('nom')) {
			$erreurs['nom'] = _T('petitcochon:nom_exist');
		}
	}
	if (preg_match('/,/i', _request('poids'))) {
		$erreurs['poids'] = _T('petitcochon:pas_de_virgule');
	}

	return $erreurs;
}

/**
 * Traitement du formulaire de configuration du petitcochon des sites
 *
 * @return array
 *     Retours du traitement
**/
function formulaires_editer_petitcochon_traiter_dist($id_petitcochon = 'new', $objet = '', $id_objet = '', $retour = '', $ajaxload = 'oui', $options = '') {
	$valeurs = formulaires_editer_objet_traiter('petitcochon', $id_petitcochon, '', '', $retour, '');

	return array('message_ok'=>_T('petitcochon:configuration_jeu'));
}
