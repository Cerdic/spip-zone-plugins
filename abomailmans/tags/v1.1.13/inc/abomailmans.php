<?php

/*
 * Abomailmans
 * MaZiaR - NetAktiv
 * tech@netaktiv.com
 * Printemps 2007 - 2012
 * Inspire de Spip-Listes
 * $Id$
*/

if (!defined("_ECRIRE_INC_VERSION")) return;

// a utiliser dans le form de son choix ...
function abomailman_traiter_abonnement($id_liste,$abonnement){
	$liste_data = sql_fetsel("*","spip_abomailmans","id_abomailman = $id_liste");
	$sujet=null;
	$dowhat=null;
	$titre=$liste_data['titre'];
	//Si on a les 2 emails
	if($liste_data['email_subscribe'] AND $liste_data['email_unsubscribe']){
		spip_log("Liste defini par mails","abomailmans");
		$liste_email = !empty($abonnement)?$liste_data['email_subscribe']:$liste_data['email_unsubscribe'];
	}else{
		//sinon comme avant
		// 1er cas : c'est une liste MAILMAN ? join et leave etrange  !!!
		//ne serait-ce pas plutot subscribe et unsubscribe ?
		if($liste_data['email_sympa'] == '') {
			spip_log("Liste -join ou -leave","abomailmans");
			$liste_email = explode ("@", $liste_data['email']);
			// abonnement ou desabonement : on rajoute -join ou -leave dans l'email de la liste
			$dowhat = !empty($abonnement)?"-join@":"-leave@";
			$liste_email = $liste_email[0]."$dowhat".$liste_email[1];
		}
		// 2eme cas : c'est une liste SYMPA (presence de deux @ à suivre)
		else {
			spip_log("Liste sympa","abomailmans");
			$proprio_email = $liste_data['email_sympa'];
			$sujet = empty($abonnement)? 'UNSUBSCRIBE ' : 'SUBSCRIBE ';
			$sujet .= $liste_data['email'].' ';
			$sujet .= empty($desabonnement) ? $nom : '';
			$liste_email = $liste_data['titre'];
		}
	}
	$sujet=isset($sujet)?$sujet:$liste_email;
	$quoifait=!empty($abonnement)?_T("abomailmans:veut_s_abonner"):_T("abomailmans:veut_se_desabonner");
	$body="$quoifait"."\n ".$titre."(".$liste_data['email'].") \n "._T("abomailmans:envoi_vers")." $liste_email";


	return array($titre,$proprio_email,$liste_email, $sujet, $body,$headers);
}


//* Envoi de mail via facteur
function abomailman_mail($nom, $email, $to_email,$liste_email, $sujet="", $body="", $html="", $headers="") {
	// si $to_mail est plein, c'est Sympa, s'il est vide c'est Mailman et il faut alors utiliser $liste_email
	if (!$to_email)
		$to_email = $liste_email;
	
	// Pas beau mais faudrait reprendre le code plus en profondeur
	// et rajouter une liste de choix du robot en page de config
	// Modifier le destinataire d’envoi dans le cas ezmlm pour que 
	// les inscriptions fonctionnent si facteur utilise l’envoi via
	// la fonction mail() de php. En effet dans ce cas, le header return-path
	// n’est pas renseigné. Or c’est ce header qui est utilisé par le robot
	// pour répondre et non le champ from... Il faut modifier le destinataire
	// comme ceci maliste-subscribe-lemail=ledomaine.tld@monsite.tld
	if (defined('_ABOMAILMAN_ROBOT_EZMLM') && preg_match("/subscribe/",$to_email)) {
		$souscripteur = str_replace("@" , "=" , $email ) ;
		$to_email = str_replace("@" , "-".$souscripteur."@" , $to_email ) ;
	}

	$envoyer_mail = charger_fonction('envoyer_mail','inc/');
	if($envoyer_mail($to_email, $sujet, $body, $email, $headers))
		$retour=true;
	else
		$retour=false;

	spip_log("abomailman_mail nom $nom, email $email, to_email $to_email, liste_email $liste_email, sujet $sujet, body $body, html $html, headers $headers, retour envoyer_mail : $retour","abomailmans");
	return $retour ;
}


?>
