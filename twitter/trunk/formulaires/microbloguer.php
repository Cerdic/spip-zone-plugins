<?php
/*
 * Plugin spip|twitter
 * (c) 2009-2013
 *
 * envoyer et lire des messages de Twitter
 * distribue sous licence GNU/LGPL
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Fonction de chargement des valeurs par defaut des champs du formulaire
 */
function formulaires_microbloguer_charger_dist(){
	return 
		array(
			'status' => '',
		);
}

/**
 * Fonction de vérification du formulaire avant traitement
 * 
 * Vérifie la présence d'un statut depuis le champs adéquat
 * Vérifie que la longueur du statut n'excède pas la longueur maximale
 */
function formulaires_microbloguer_verifier_dist(){
	include_spip('inc/charsets');
	$erreurs = array();
	if (!$status = _request('status')){
		$erreurs['status'] = _T('info_obligatoire');
	}
	elseif (spip_strlen($status)>140){
		$erreurs['status'] = _T('twitter:longueur_maxi_status');
	}

	return
		$erreurs;
}

/**
 * Fonction de traitement du formulaire
 * Envoie la contribution au service configuré
 * 
 * S'il y a une erreur en retour (false), 
 * on affiche un message explicitant qu'il y a une erreur dans la configuration
 */
function formulaires_microbloguer_traiter_dist(){
	$res = array();
	if ($status = _request('status')){
		include_spip('inc/microblog');
		$retour = microblog($status);
		spip_log($retour,'twitter');
		
		if($retour){
			set_request('status','');
			$res = array('message_ok'=>_T('twitter:message_envoye')." ".$status,'editable'=>true);
		}else{
			$erreur = _T('twitter:erreur_verifier_configuration');
			if (defined('_TEST_MICROBLOG_SERVICE') AND !_TEST_MICROBLOG_SERVICE)
				$erreur = _T('twitter:erreur_envoi_desactive');
			$res = array('message_erreur'=>$erreur,'editable'=>true);
		}
	}
	else
		$res = array('message_erreur'=>'???','editable'=>true);

	return
		$res;
}

?>
