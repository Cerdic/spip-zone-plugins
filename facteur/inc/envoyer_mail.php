<?php
/*
 * Plugin Facteur
 * (c) 2009-2010 Collectif SPIP
 * Distribue sous licence GPL
 *
 */


include_spip('classes/facteur');
// inclure le fichier natif de SPIP, pour les fonctions annexes
include_once _DIR_RESTREINT."inc/envoyer_mail.php";

function inc_envoyer_mail($destinataire, $sujet, $corps, $from = "", $headers = "") {
	$message_html	= '';
	$message_texte	= '';

	// si $corps est un tableau -> fonctionnalites etendues
	// avec entrees possible : html, texte, pieces_jointes, nom_envoyeur
	if (is_array($corps)) {
		$message_html	= $corps['html'];
		$message_texte	= nettoyer_caracteres_mail($corps['texte']);
		$pieces_jointes	= $corps['pieces_jointes'];
		$nom_envoyeur = $corps['nom_envoyeur'];
		$cc = $corps['cc'];
		$bcc = $corps['bcc'];
	} 
	// si $corps est une chaine -> compat avec la fonction native SPIP
	// gerer le cas ou le corps est du html avec un Content-Type: text/html dans les headers
	else {
		if (preg_match(',Content-Type:\s*text/html,ims',$headers)){
			$message_html	= $corps;
		}
		else {
			$message_texte	= nettoyer_caracteres_mail($corps);
		}
	}
	$sujet = nettoyer_titre_email($sujet);
	// mode TEST : forcer l'email
	if (defined('_TEST_EMAIL_DEST')) {
		if (!_TEST_EMAIL_DEST)
			return false;
		else
			$destinataire = _TEST_EMAIL_DEST;
	}
	
	// On crée l'objet Facteur (PHPMailer) pour le manipuler ensuite
	$facteur = new Facteur($destinataire, $sujet, $message_html, $message_texte);
	
	// On ajoute le courriel de l'envoyeur s'il est fournit par la fonction
	if (!empty($from)) {
		$facteur->From = $from;
		// la valeur par défaut de la config n'est probablement pas valable pour ce mail,
		// on l'écrase pour cet envoi
		$facteur->FromName = $from;
	}

	// On ajoute le nom de l'envoyeur s'il fait partie des options
	if ($nom_envoyeur)
		$facteur->FromName = $nom_envoyeur;
	
	// S'il y a des copies à envoyer
	if ($cc){
		if (is_array($cc))
			foreach ($cc as $courriel)
				$facteur->AddCC($courriel);
		else
			$facteur->AddCC($cc);
	}
	
	// S'il y a des copies cachées à envoyer
	if ($bcc){
		if (is_array($bcc))
			foreach ($bcc as $courriel)
				$facteur->AddBCC($courriel);
		else
			$facteur->AddBCC($bcc);
	}
	
	// S'il y a des pièces jointes on les ajoute proprement
	if (count($pieces_jointes)) {
		foreach ($pieces_jointes as $piece) {
			$facteur->AddAttachment($piece['chemin'], $piece['nom'], $piece['encodage'], $piece['mime']);
		}
	}
	
	// On passe dans un pipeline pour modifier tout le facteur avant l'envoi
	$facteur = pipeline('facteur_pre_envoi', $facteur);
	
	// On génère les headers
	$head = $facteur->CreateHeader();
		
	// Et c'est parti on envoie enfin
	spip_log("mail via facteur\n$head"."Destinataire: 
$destinataire\n",'mail');
	spip_log("mail\n$head"."Destinataire: 
$destinataire\n",'facteur');
	return $facteur->Send();
}

// Juste pour déclarer le pipeline
function facteur_facteur_pre_envoi($facteur){
	return $facteur;
}

?>
