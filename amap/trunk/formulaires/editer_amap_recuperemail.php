<?php
/**
* @plugin	Amap
* @author	Stephane Moulinet
* @author	E-cosystems
* @author	Pierre KUHN 
* @copyright 2010-2013
* @licence	GNU/GPL
*
**/

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/actions');
include_spip('inc/editer');

function formulaires_editer_amap_recuperemail_charger_dist($id_amap_panier,$id_auteur,$id_producteur,$date_distribution) {
	$valeurs = array('id_amap_panier'=>$id_amap_panier, 'id_auteur'=>$id_auteur, 'id_producteur'=>$id_producteur, 'date_distribution'=>$date_distribution);
	return $valeurs;
}

function formulaires_editer_amap_recuperemail_verifier_dist($id_amap_panier,$id_auteur,$id_producteur,$date_distribution){
	$erreurs = array();
	// verifier que les champs obligatoires sont bien la :
	foreach(array('id_auteur','id_producteur','date_distribution') as $obligatoire)
		if (!_request($obligatoire)) $erreurs[$obligatoire] = 'Ce champ est obligatoire';
	
	if (count($erreurs))
		$erreurs['message_erreur'] = 'Votre saisie contient des erreurs !';
	return $erreurs;
}

function formulaires_editer_amap_recuperemail_traiter_dist($id_amap_panier,$id_auteur,$id_producteur,$date_distribution) {
	// Le n° de panier
	$id_amap_panier = _request('id_amap_panier');
	// L'ahérent du panier
	$id_auteur = _request('id_auteur');
	// Le producteur du panier
	$id_producteur = _request('id_producteur');
	// La date de distribution
	$date_distribution = _request('date_distribution');

	sql_replace("spip_amap_paniers", array("id_amap_panier" => $id_amap_panier, "id_auteur" => $id_auteur, "id_producteur" => $id_producteur, "date_distribution" => $date_distribution, "dispo" => "non"));
	spip_log("Le $id_amap_panier a bien été récupéré par l'adhérent $id_auteur, panier produit par $id_producteur pour la livraison du $date_distribution", "amap_installation");

	// L'envoie des mails.
	// Le nom de l'amapiens qui prend pas le panier
	$nom_adherent = sql_getfetsel('nom', 'spip_auteurs', 'id_auteur=' . intval($id_auteur));
	// Le nom du producteur du panier
	$nom_producteur = sql_getfetsel('nom', 'spip_auteurs', 'id_auteur=' . intval($id_producteur));
	// Creation de la liste des email d'amapiens
	// On recupere dans la table des auteurs un tableau de tous les emails il sera de la forme emails[0..X]['email'] 
	$emails = sql_allfetsel('email','spip_auteurs','email IS NOT NULL');  

	$envoyer_mail = charger_fonction('envoyer_mail','inc');
	$headers .= "MIME-Version: 1.0 \n";
	$headers .= "Content-type: text/html; charset=iso-8859-1 \n";
	$email_from = $GLOBALS['meta']['email_envoi'];	// Ici l'adresse EMAIL de ton choix pour l'envoi par exemple $GLOBALS['meta']['email_webmaster']
	$reply = $email_from;
	$sujet = _T('amap:panier_recupere', array('date_distribution'=>date('d/m/Y',strtotime($date_distribution))));
	$message_mail = _T('amap:panier_recupere_auteur_mail', array('nom_adherent'=>$nom_adherent, 'nom_producteur'=>$nom_producteur, 'date_distribution'=>$date_distribution));
	// On boucle sur le tableau des emails recupérés dans la base, les elements du tableau $emails sont des tableaux avec la clé a 'email' (nom de la colonne dans la base)
	foreach($emails as $destinataire) {
		$envoyer_mail($destinataire['email'],$sujet,$message_mail,$email_from);
	}

	// Valeurs de retours
	$message['message_ok'] = _T('amap:panier_vous_bien_attribuer');
	return $message;
}
?>
