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

	// $corps peut etre une chaine -> compat avec la fonction native SPIP :
	// ou un tableau -> fonctionnalites etendues
	if (is_array($corps)) {
		$message_html	= $corps['html'];
		$message_texte	= nettoyer_caracteres_mail($corps['texte']);
		$pieces_jointes	= $corps['pieces_jointes'];
		$nom_envoyeur = $corps['nom_envoyeur'];
	} else {
		$message_texte	= nettoyer_caracteres_mail($corps);
	}
	$sujet = nettoyer_titre_email($sujet);

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
	return $facteur->Send();
}


?>
