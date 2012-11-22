<?php
/**
 * Plugin mailsubscribers
 * (c) 2012 Cédric Morin
 * Licence GNU/GPL v3
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

include_spip('inc/actions');
include_spip('inc/editer');

/**
 * Identifier le formulaire en faisant abstraction des parametres qui ne representent pas l'objet edite
 */
function formulaires_editer_mailsubscriber_identifier_dist($id_mailsubscriber='new', $retour='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	return serialize(array(intval($id_mailsubscriber)));
}

/**
 * Declarer les champs postes et y integrer les valeurs par defaut
 */
function formulaires_editer_mailsubscriber_charger_dist($id_mailsubscriber='new', $retour='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	$valeurs = formulaires_editer_objet_charger('mailsubscriber',$id_mailsubscriber,'',$lier_trad,$retour,$config_fonc,$row,$hidden);
	return $valeurs;
}

/**
 * Verifier les champs postes et signaler d'eventuelles erreurs
 */
function formulaires_editer_mailsubscriber_verifier_dist($id_mailsubscriber='new', $retour='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	$erreurs = formulaires_editer_objet_verifier('mailsubscriber',$id_mailsubscriber, array('email'));
	if (!isset($erreurs['email'])){
		$email = _request('email');
		// verifier que l'email est valide
		if (!email_valide($email))
			$erreurs['email'] = _T('info_email_invalide');
		else {
			if (sql_countsel('spip_mailsubscribers','email='.sql_quote($email))>0)
				$erreurs['email'] = _T('mailsubscriber:erreur_adresse_existante');
		}
	}
	return $erreurs;
}

/**
 * Traiter les champs postes
 */
function formulaires_editer_mailsubscriber_traiter_dist($id_mailsubscriber='new', $retour='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	set_request('lang',_request('langue'));
	return formulaires_editer_objet_traiter('mailsubscriber',$id_mailsubscriber,'',$lier_trad,$retour,$config_fonc,$row,$hidden);
}


?>