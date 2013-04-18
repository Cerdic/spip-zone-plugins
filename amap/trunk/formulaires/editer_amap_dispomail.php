<?php
/**
* Plugin Amap
*
* @author: Stephane Moulinet
* @author: E-cosystems
* @author: Pierre KUHN 
*
* Copyright (c) 2010-2013
* Logiciel distribue sous licence GPL.
*
**/

function formulaires_editer_amap_dispomail_traiter_dist() {
	// Le numéro du panier dispo
	$id_amap_panier = _request('id_amap_panier');
	// Le nom de l'amapiens qui prend pas le panier
	$nom_adherent = _request('nom_adherent');
	// La date ou le panier sera disponible.
	$date_distribution = _request('date_distribution');
	// Creation du lien vers le formulaire de recuperation
	$lien = url_absolue(generer_url_public('panier','id_amap_panier='.$id_amap_panier));
	// Le texte supplémentaire
	$panier_dispo_plus = $panier_dispo_plus;
	// Creation de la liste des email d'amapiens
	// On recupere dans la table des auteurs un tableau de tous les emails il sera de la forme emails[0..X]['email'] 
	$emails = sql_allfetsel('email','spip_auteurs','email IS NOT NULL');

	$envoyer_mail = charger_fonction('envoyer_mail','inc');
	$headers .= "MIME-Version: 1.0 \n";
	$headers .= "Content-type: text/html; charset=iso-8859-1 \n";
	$email_from = $GLOBALS['meta']['email_envoi'];	// Ici l'adresse EMAIL de ton choix pour l'envoi par exemple $GLOBALS['meta']['email_webmaster']
	$reply = $email_from;
	$sujet = _T('amap:panier_dispo', array('date_distribution'=>date('d/m/Y',strtotime($date_distribution))));
	$message_mail = _T('amap:panier_dispo_auteur_mail', array('nom_adherent'=>$nom_adherent, 'date_distribution'=>$date_distribution, 'lien'=>$lien, 'panier_dispo_plus'=>$panier_dispo_plus));
	// On boucle sur le tableau des emails recupérés dans la base, les elements du tableau $emails sont des tableaux avec la clé a 'email' (nom de la colonne dans la base)
	foreach($emails as $destinataire) {
		$envoyer_mail($destinataire['email'],$sujet,$message_mail,$email_from);
	}

	// Le numéro de l'amapiens qui a le panier
	$id_auteur = _request('id_auteur');
	// Le numéro du producteur du panier
	$id_producteur = _request('id_producteur');
	// La date de distribution 
	$date_distribution2 = _request('date_distribution2');
	sql_replace("spip_amap_paniers", array("id_amap_panier" => $id_amap_panier, "id_auteur" => $id_auteur, "id_producteur" => $id_producteur, "date_distribution" => $date_distribution2, "dispo" => oui));

	// Valeurs de retours
	$message['message_ok'] = _T('amap:confirmation_envoi', array('date_distribution'=>$date_distribution));
	return $message;
}
?>
