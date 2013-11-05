<?php
/**
 * Plugin mailsubscribers
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
function action_subscribe_mailsubscriber_dist($email=null, $double_optin=null){
	include_spip('mailsubscribers_fonctions');
	include_spip('inc/config');
	if (is_null($email)){
		$email = _request('email');
		$arg = _request('arg');
		if (is_null($arg) AND strpos($_SERVER["QUERY_STRING"],"arg%")!==false){
			$query = str_replace("arg%","arg=",$_SERVER["QUERY_STRING"]);
			parse_str($query,$args);
			$arg = strtolower($args['arg']);
			if (strlen($arg)>40)
				$arg = substr($arg,-40);
		}
		$row = sql_fetsel('id_mailsubscriber,email,jeton,lang,statut','spip_mailsubscribers','email='.sql_quote($email));
		if (!$row
			OR $arg!==mailsubscriber_cle_action("subscribe",$row['email'],$row['jeton'])){
			$row = false;
		}
	}
	else {
		$row = sql_fetsel('id_mailsubscriber,email,jeton,statut','spip_mailsubscribers','email='.sql_quote($email));
	}
	if (!$row){
		include_spip('inc/minipres');
		echo minipres();
		exit;
	}

	include_spip("inc/lang");
	changer_langue($row['lang']);
	include_spip("inc/autoriser");
	autoriser_exception("modifier","mailsubscriber",$row['id_mailsubscriber']);
	autoriser_exception("instituer","mailsubscriber",$row['id_mailsubscriber']);

	if ($row['statut']!='valide'){
		// OK l'email est connu et valide
		include_spip("action/editer_objet");
		// si doubleoptin, envoyer un mail de confirmation
		if (is_null($double_optin))
			$double_optin = lire_config('mailsubscribers/double_optin',0);
		if ($double_optin){
			// on passe en prop qui declenche l'envoi d'un mail
			objet_modifier("mailsubscriber",$row['id_mailsubscriber'],array('statut'=>'prop'));
			$titre = _T('mailsubscriber:confirmsubscribe_texte_email_1',array('email'=>$row['email'],'nom_site_spip'=>$GLOBALS['meta']['nom_site'],'url_site_spip'=>$GLOBALS['meta']['adresse_site']));
			$titre .= "<br />"._T('mailsubscriber:confirmsubscribe_texte_email_envoye');
		}
		// sinon inscrire directement
		else {
			objet_modifier("mailsubscriber",$row['id_mailsubscriber'],array('statut'=>'valide'));
			$titre = _T('mailsubscriber:subscribe_texte_email_1',array('email'=>$row['email']));
		}
	}
	else {
		$titre = _T('mailsubscriber:subscribe_deja_texte',array('email'=>$row['email']));
	}

	autoriser_exception("modifier","mailsubscriber",$row['id_mailsubscriber'],false);
	autoriser_exception("instituer","mailsubscriber",$row['id_mailsubscriber'],false);

	// Dans tous les cas on finit sur un minipres qui dit si ok ou echec
	include_spip('inc/minipres');
	echo minipres($titre,"","",true);

}
