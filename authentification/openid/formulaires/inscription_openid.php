<?php

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *                                                                         *
 *  Copyright (c) 2001-2008                                                *
 *  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James  *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

if (!defined("_ECRIRE_INC_VERSION")) return;

if (!defined('_LOGIN_TROP_COURT')) define('_LOGIN_TROP_COURT', 4);

function formulaires_inscription_openid_charger_dist($cible="") {
	
	$valeurs = array('openid_inscription'=>'');

	/*
	if ($mode=='1comite')
		$valeurs['_commentaire'] = _T('pass_espace_prive_bla');
	else 
		$valeurs['_commentaire'] = _T('pass_forum_bla');
	*/
	
	if ($GLOBALS['meta']['accepter_inscriptions']!='oui')
		return array(false,$valeurs);

	// recuperer les messages retournes depuis l'action controler_openid
	if ($message_retour = _request('message_erreur')){
		$valeurs['erreur_retour_openid'] = urldecode($message_retour);
	} elseif ($message_retour = _request('message_ok')) {
		$valeurs['message_retour_openid'] = urldecode($message_retour);
	}
	return $valeurs;
}

// Si inscriptions pas autorisees, retourner une chaine d'avertissement
function formulaires_inscription_openid_verifier_dist($cible="") {
	$erreurs = array();
		
	if (!$openid = _request('openid_inscription'))
		$erreurs['openid_inscription'] = _T("info_obligatoire");
	else {
		include_spip('inc/openid');
		$erreurs['openid_inscription'] = demander_authentification_openid($openid, $cible);
	}

	return $erreurs;
}

function formulaires_inscription_openid_traiter_dist($cible="") {
	return "";
}


?>
