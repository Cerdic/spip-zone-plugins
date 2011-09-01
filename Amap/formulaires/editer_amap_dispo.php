<?php
function formulaires_editer_amap_dispo_traiter_dist() {
	// Le numéro du panier dispo
	$id_amap_panier = _request('id_amap_panier');
	// Le nom de l'amapiens qui prend pas le panier
	$nom = _request('nom');
	// La date ou le panier sera disponible.
	$date_distribution = _request('date_distribution');
	// Creation du lien vers le formulaire de recuperation
	$lien .= generer_url_public("panier","id_amap_panier=$id_amap_panier");
	// Creation de la liste des email d'amapiens
	// On recupere dans la table des auteurs un tableau de tous les emails il sera de la forme emails[0..X]['email'] 
	$emails = sql_allfetsel('email','spip_auteurs','email IS NOT NULL');  

	$envoyer_mail = charger_fonction('envoyer_mail','inc');
	$headers .= "MIME-Version: 1.0 \n";
	$headers .= "Content-type: text/html; charset=iso-8859-1 \n";
	$email_from = $GLOBALS['meta']['email_envoi'];	// Ici l'adresse EMAIL de ton choix pour l'envoi par exemple $GLOBALS['meta']['email_webmaster']
	$reply = $email_from;
	$sujet = _T('amap:panier_dispo', array('nb'=>date('d/m/Y',strtotime($date_distribution))));
	$message_mail = _T('amap:panier_dispo_auteur_mail', array('nom'=>$nom, 'date_distribution'=>$date_distribution, 'lien'=>$lien));
	// On boucle sur le tableau des emails recupérés dans la base, les elements du tableau $emails sont des tableaux avec la clé a 'email' (nom de la colonne dans la base)
	foreach($emails as $destinataire) {
		$envoyer_mail($destinataire['email'],$sujet,$message_mail,$email_from);
	}
	
	// Valeurs de retours
	$message['message_ok'] = _T('amap:confirmation_envoi', array('date_distribution'=>$date_distribution));
	return $message;
}
?>