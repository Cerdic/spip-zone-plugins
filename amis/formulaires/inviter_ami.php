<?php
/*
 * Plugin amis / gestion des amis
 * Licence GPL
 * (c) 2008 C.Morin Yterium
 *
 */

include_spip('inc/filtres');
include_spip('base/abstract_sql');

/**
 * Chargement par defaut des valeurs de saisie du #FORMULAIRE_INVITER_AMI
 * la fonction recoit en entree les arguments de la balise dans le squelette
 * renvoyer la liste des champs en cle, et les valeurs par defaut a la saisie
 * les valeurs seront automatiquement surchargees par _request() en cas de second tour de saisie
 * renvoyer false pour ne pas autoriser la saisie
 * dans id renvoyer la cle primaire de l'objet traite si necessaire (sera mise a new sinon)
 *
 * @return array
 */
function formulaires_inviter_ami_charger_dist(){
	
	$valeurs = array('email'=>null,'message'=>null);
	return $valeurs;
}

/**
 * Verification de la validite des donnees saisies et postees par #FORMULAIRE_INVITER_AMI
 *
 * @return array $erreurs
 */
function formulaires_inviter_ami_verifier_dist(){

	$erreurs = array();
	foreach(array('email') as $obli)
		if (!_request($obli))
			$erreurs[$obli] = (isset($erreurs[$obli])?$erreurs[$obli]:'') . _T('formulaires:info_obligatoire_rappel');

	if ($e=_request('email')){
		if (!email_valide($e))
			$erreurs['email'] = (isset($erreurs['email'])?$erreurs['email']:'') . _T('formulaires:email_invalide');
	}

	return $erreurs;
}

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