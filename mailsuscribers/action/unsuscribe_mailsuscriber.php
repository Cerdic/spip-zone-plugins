<?php
/**
 * Plugin mailsuscribers
 * (c) 2012 Cédric Morin
 * Licence GNU/GPL v3
 */

if (!defined('_ECRIRE_INC_VERSION')) return;


function action_unsuscribe_mailsuscriber_dist($email=null){
	include_spip('mailsuscribers_fonctions');
	if (is_null($email)){
		$email = _request('email');
		$arg = _request('arg');
		$row = sql_fetsel('id_mailsuscriber,email,jeton,lang,statut','spip_mailsuscribers','email='.sql_quote($email));
		if (!$row
			OR $arg!==mailsuscriber_cle_action("unsuscribe",$row['email'],$row['jeton'])){
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

	if ($row['statut']=='valide'){
		// OK l'email est connu et valide
		include_spip("action/editer_objet");
		objet_modifier("mailsuscriber",$row['id_mailsuscriber'],array('statut'=>'refuse'));
		$titre = _T('mailsuscriber:unsuscribe_texte_email_1',array('email'=>$row['email']));
	}
	else {
		$titre = _T('mailsuscriber:unsuscribe_deja_texte',array('email'=>$row['email']));
	}


	// Dans tous les cas on finit sur un minipres qui dit si ok ou echec
	include_spip('inc/minipres');
	echo minipres($titre,"","",true);

}