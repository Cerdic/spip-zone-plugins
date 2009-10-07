<?php

function action_envoyer_propaganda_dist(){

	include_spip('inc/mail');
	include_spip ("inc/texte");
	
	$destinataire = _request('email_destinataire');
	$nom_destinataire = _request('nom_destinataire');
	$titre = _request('sujet');
	$texte = _request('texte_message_auteur');
	$document_carte = _request('document_carte');
	$nom_expediteur = _request('nom_expediteur');
	$adresse = _request('mail_expediteur');
	
	$id_auteur = $GLOBALS['visiteur_session']['id_auteur'] ? $GLOBALS['visiteur_session']['id_auteur'] : 0;
	
	$id_propaganda = sql_insertq("spip_propaganda",
						array(
							'id_auteur'=>$id_auteur,
							'id_document'=>$document_carte,
							'titre'=>$titre,
							'texte'=>$texte,
							'email_destinataire'=>$destinataire,
							'nom_destinataire'=>$nom_destinataire,
							'hash'=>$hash,
							'confidentiel'=>$confidentiel));

	$url = parametre_url(generer_url_public('carte'),'id_propaganda',$id_propaganda);

	$texte_mail = ""._T('propaganda:bonjour')." $nom_destinataire".",\n\n$nom_expediteur ($adresse) "._T('propaganda:untel_envoi_carte')."\n\n";
	$texte_mail .= "\n"._T('propaganda:consulter_carte')." \n$url\n\n";
	$texte_mail .= _T('propaganda:son_message')."\n\n".$texte."\n\n";
	$texte_mail .= "----------------\n$url\n\n"._T('propaganda:merci_de_visite')."\n";
	$texte_mail .= "\n\n-- "._T('envoi_via_le_site')." ".supprimer_tags(extraire_multi(lire_meta('nom_site')))." (".lire_meta('adresse_site')."/) --\n";
	
	$titre_mail = "[".supprimer_tags(extraire_multi(lire_meta('nom_site')))."] - ";
	$titre_mail .= $titre;
	$titre_mail = utf8_decode($titre_mail);
	$envoyer_mail = envoyer_mail($destinataire, $titre_mail, $texte_mail, $nom_expediteur.' <'.$adresse.'>',
				"X-Originating-IP: ".$GLOBALS['REMOTE_ADDR']);
				
	if(!$envoyer_mail){
		$err = _T('propaganda:erreur_envoi_mail');
	}
	
	return array($id_propaganda,$err);
}

?>