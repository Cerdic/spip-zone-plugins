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

	$facteur = new Facteur($destinataire, $sujet, $message_html, $message_texte);
	if (!empty($from))
		$facteur->From = $from;
	if ($nom_envoyeur)
		$facteur->FromName = $nom_envoyeur;
	if (count($pieces_jointes)) {
		foreach ($pieces_jointes as $piece) {
			$facteur->AddAttachment($piece['chemin'], $piece['nom'], $piece['encodage'], $piece['mime']);
		}
	}
	$head = $facteur->CreateHeader();
	spip_log("mail via facteur\n$head\n",'mail');
	spip_log("mail\n$head\n",'facteur');
	return $facteur->Send();
}


?>
