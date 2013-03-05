<?php
/**
 * Plugin mailsubscribers
 * (c) 2012 Cédric Morin
 * Licence GNU/GPL v3
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Des-inscrire un email deja en base
 * (mise a jour du statut en refuse)
 *
 * @param string $email
 */
function action_unsubscribe_mailsubscriber_dist($email=null, $double_optin=true){
	include_spip('mailsubscribers_fonctions');
	if (is_null($email)){
		$email = _request('email');
		$arg = _request('arg');
		$row = sql_fetsel('id_mailsubscriber,email,jeton,lang,statut','spip_mailsubscribers','email='.sql_quote($email));
		if (!$row
			OR $arg!==mailsubscriber_cle_action("unsubscribe",$row['email'],$row['jeton'])){
			$row = false;
		}
	}
	else {
		$double_optin = false;
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

	if (!$row['statut']=='valide'){
		$titre = _T('mailsubscriber:unsubscribe_deja_texte',array('email'=>$row['email']));
	}
	else {
		if ($double_optin){
			include_spip("inc/filtres");
			$titre = _T('mailsubscriber:unsubscribe_texte_confirmer_email_1',array('email'=>$row['email']));
			$titre .= "<br /><br />".bouton_action(_T('newsletter:bouton_unsubscribe'),generer_action_auteur('confirm_unsubscribe_mailsubscriber',"$email-$arg"));
		}
		else {
			autoriser_exception("modifier","mailsubscriber",$row['id_mailsubscriber']);
			autoriser_exception("instituer","mailsubscriber",$row['id_mailsubscriber']);
			// OK l'email est connu et valide
			include_spip("action/editer_objet");
			objet_modifier("mailsubscriber",$row['id_mailsubscriber'],array('statut'=>'refuse'));
			$titre = _T('mailsubscriber:unsubscribe_texte_email_1',array('email'=>$row['email']));
			autoriser_exception("modifier","mailsubscriber",$row['id_mailsubscriber'],false);
			autoriser_exception("instituer","mailsubscriber",$row['id_mailsubscriber'],false);
		}
	}

	// Dans tous les cas on finit sur un minipres qui dit si ok ou echec
	include_spip('inc/minipres');
	echo minipres($titre,"","",true);

}
