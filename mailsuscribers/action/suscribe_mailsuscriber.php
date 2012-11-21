<?php
/**
 * Plugin mailsuscribers
 * (c) 2012 Cédric Morin
 * Licence GNU/GPL v3
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Inscrire un email deja en base
 * (mise a jour du statut en prop ou valide selon l'option double-optin)
 *
 * @param string $email
 * @param null|bool $double_optin
 */
function action_suscribe_mailsuscriber_dist($email=null, $double_optin=null){
	include_spip('mailsuscribers_fonctions');
	include_spip('inc/config');
	if (is_null($email)){
		$email = _request('email');
		$arg = _request('arg');
		$row = sql_fetsel('id_mailsuscriber,email,jeton,lang,statut','spip_mailsuscribers','email='.sql_quote($email));
		if (!$row
			OR $arg!==mailsuscriber_cle_action("suscribe",$row['email'],$row['jeton'])){
			$row = false;
		}
	}
	else {
		$row = sql_fetsel('id_mailsuscriber,email,jeton,statut','spip_mailsuscribers','email='.sql_quote($email));
	}
	if (!$row){
		include_spip('inc/minipres');
		echo minipres();
		exit;
	}

	include_spip("inc/lang");
	changer_langue($row['lang']);

	if ($row['statut']!='valide'){
		// OK l'email est connu et valide
		include_spip("action/editer_objet");
		// si doubleoptin, envoyer un mail de confirmation
		if (is_null($double_optin))
			$double_optin = lire_config('mailsuscribers/double_optin',0);
		if ($double_optin){
			// on passe en prop qui declenche l'envoi d'un mail
			objet_modifier("mailsuscriber",$row['id_mailsuscriber'],array('statut'=>'prop'));
			$titre = _T('mailsuscriber:confirmsuscribe_texte_email_1',array('email'=>$row['email'],'nom_site_spip'=>$GLOBALS['meta']['nom_site'],'url_site_spip'=>$GLOBALS['meta']['adresse_site']));
			$titre .= "<br />"._T('mailsuscriber:confirmsuscribe_texte_email_envoye');
		}
		// sinon inscrire directement
		else {
			objet_modifier("mailsuscriber",$row['id_mailsuscriber'],array('statut'=>'valide'));
			$titre = _T('mailsuscriber:suscribe_texte_email_1',array('email'=>$row['email']));
		}
	}
	else {
		$titre = _T('mailsuscriber:suscribe_deja_texte',array('email'=>$row['email']));
	}


	// Dans tous les cas on finit sur un minipres qui dit si ok ou echec
	include_spip('inc/minipres');
	echo minipres($titre,"","",true);

}