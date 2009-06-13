<?php
/*
 * Plugin amis / gestion des amis
 * Licence GPL
 * (c) 2008 C.Morin Yterium
 *
 */


include_spip('base/abstract_sql');

/**
 * traitment post saisie des valeurs postees par #FORMULAIRE_INVITER_AMI
 *
 * @return array(bool,string)
 */
function formulaires_inviter_ami_traiter_dist(){

	$message='erreur';
	
	if ($e=_request('email')){
		$envoyer_mail = charger_fonction('envoyer_mail','inc');
		$qui = $GLOBALS['visiteur_session']['id_auteur'];
		$profil_decrire = charger_fonction('profil_decrire','inc');
		$row = $profil_decrire($qui,true);
		$row['url_site']=$GLOBALS['meta']['adresse_site'];
		$texte = _T('inviter_ami:mail_rejoignez_message',$row);
		if ($m = _request('message'))
			$texte = trim($m) . "\n----\n\n$texte";
		$subject = _T('inviter_ami:mail_rejoignez_sujet',$row);
		
		$envoyer_mail($e,$subject,$texte,''/*$GLOBALS['meta']['email_webmestre']*/,"Reply-To: ".$row['email']."\n");
		$message = _T('inviter_ami:votre_ami_a_ete_invite',array('email'=>$e));
		
		// on vide la saisie pour le tour suivant
		set_request('email',NULL);
		set_request('message',NULL);
	}

	return array(true,$message);
}

?>