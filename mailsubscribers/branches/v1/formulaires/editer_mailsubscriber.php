<?php
/**
 * Plugin mailsubscribers
 * (c) 2012 Cédric Morin
 * Licence GNU/GPL v3
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

include_spip('inc/actions');
include_spip('inc/mailsubscribers');
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
	$valeurs['listes'] = explode(',',$valeurs['listes']);
	$valeurs['_listes_dispo'] = mailsubscribers_listes();
	return $valeurs;
}

/**
 * Verifier les champs postes et signaler d'eventuelles erreurs
 */
function formulaires_editer_mailsubscriber_verifier_dist($id_mailsubscriber='new', $retour='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	$listes = _request('listes');
	if ($listes AND is_array($listes))
		set_request('listes',implode(',',$listes));
	if (!$listes)
		$listes = array();

	$erreurs = formulaires_editer_objet_verifier('mailsubscriber',$id_mailsubscriber, array('email'));
	if (!isset($erreurs['email'])){
		$email = _request('email');
		// verifier que l'email est valide
		if (!email_valide($email))
			$erreurs['email'] = _T('info_email_invalide');
		else {
			// verifier que l'email n'est pas deja dans la base si c'est une tentative de creation
			if (!intval($id_mailsubscriber) AND sql_countsel('spip_mailsubscribers','email='.sql_quote($email))>0)
				$erreurs['email'] = _T('mailsubscriber:erreur_adresse_existante');
		}
	}
	if (count($erreurs))
		set_request('listes',$listes);
	return $erreurs;
}

/**
 * Traiter les champs postes
 */
function formulaires_editer_mailsubscriber_traiter_dist($id_mailsubscriber='new', $retour='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	set_request('lang',_request('langue'));

	$GLOBALS['notification_instituermailsubscriber_status'] = false; // pas de notification depuis l'edition du form

	// creation : verifier qu'on retombe pas sur un email obfusque, et dans ce cas se retablir dessus
	if (!intval($id_mailsubscriber)
		AND $id = sql_getfetsel('id_mailsubscriber','spip_mailsubscribers',"email=".sql_quote(mailsubscribers_obfusquer_email(_request('email')))))
		$id_mailsubscriber = $id;
	effacer_meta("newsletter_subscribers_count");
	return formulaires_editer_objet_traiter('mailsubscriber',$id_mailsubscriber,'',$lier_trad,$retour,$config_fonc,$row,$hidden);
}


?>
