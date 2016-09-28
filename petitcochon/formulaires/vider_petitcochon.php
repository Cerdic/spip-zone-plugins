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

/**
 * Chargement du formulaire de suppression des utilisateurs
 *
 * @return array
 *     Environnement du formulaire
**/
function formulaires_vider_petitcochon_charger_dist() {
	
	$valeurs = array();
	return $valeurs;
	
}

/**
 * Vérifications du formulaire de suppression des utilisateurs
 *
 * @return array
 *     Tableau des erreurs
**/
function formulaires_vider_petitcochon_verifier_dist() {

	$erreurs = array();
	return $erreurs;

}

/**
 * Traitement du formulaire de suppression des utilisateurs
 *
 * @return array
 *     Retours du traitement
**/
function formulaires_vider_petitcochon_traiter_dist() {

	include_spip('inc/actions');
	$vider_vote = charger_fonction('vider_vote', 'action');
	$action = $vider_vote();

	$res = array(
			'editable' => true,
			'message_ok' => _T('petitcochon:message_confirmation_petitcochon')
	);

	$res['message_ok'] .= "<script type='text/javascript'>if (window.jQuery) $('.liste-objets.petitcochon').ajaxReload();</script>";
	return $res;

}
