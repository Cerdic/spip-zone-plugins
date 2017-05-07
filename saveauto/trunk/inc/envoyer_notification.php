<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Envoyer un email de notification
 * Le sujet peut etre vide, dans ce cas il reprendra la premiere ligne non vide du texte
 *
 * @param array/string $emails
 * @param string $texte
 * @param string $sujet
 * @param string $pieces
 * @param string $headers
 */
function inc_envoyer_notification_dist($emails, $texte, $sujet="", $pieces=array(), $headers=""){
	include_spip('inc/notifications');

	// rien a faire si pas de texte !
	if (!strlen($texte))
		return;

	// si on ne specifie qu'un email, le mettre dans un tableau
	if (!is_array($emails))
		$emails = explode(',',$emails);

	notifications_nettoyer_emails($emails);

	// tester si le mail est deja en html
	if (!strlen($sujet)
	  AND strpos($texte,"<")!==false // eviter les tests suivants si possible
		AND $ttrim = trim($texte)
		AND substr($ttrim,0,1)=="<"
	  AND substr($ttrim,-1,1)==">"
	  AND stripos($ttrim,"</html>")!==false){

		// dans ce cas on ruse un peu : extraire le sujet du title
		if (preg_match(",<title>(.*)</title>,Uims",$texte,$m))
			$sujet = $m[1];
		else {
			// fallback, on prend le body si on le trouve
			if (preg_match(",<body[^>]*>(.*)</body>,Uims",$texte,$m))
				$ttrim = $m[1];

			// et on extrait la premiere ligne de vrai texte...
			// nettoyer le html et les retours chariots
			$ttrim = textebrut($ttrim);
			$ttrim = str_replace("\r\n", "\r", $ttrim);
			$ttrim = str_replace("\r", "\n", $ttrim);
			// decouper
			$ttrim = explode("\n",trim($ttrim));
			// extraire la premiere ligne de texte brut
			$sujet = array_shift($ttrim);
		}

		// si besoin on ajoute le content-type dans les headers
		if (stripos($headers,"Content-Type")===false)
			$headers .= "Content-Type: text/html\n";
	}

	// si le sujet est vide, extraire la premiere ligne du corps
	// du mail qui est donc du texte
	if (!strlen($sujet)){
		// nettoyer un peu les retours chariots
		$texte = str_replace("\r\n", "\r", $texte);
		$texte = str_replace("\r", "\n", $texte);
		// decouper
		$texte = explode("\n",trim($texte));
		// extraire la premiere ligne
		$sujet = array_shift($texte);
		$texte = trim(implode("\n",$texte));
	}

	$envoyer_mail = charger_fonction('envoyer_mail','inc');
	$corps = $pieces ? array('texte' => $texte, 'pieces_jointes' => $pieces) : $texte;
	foreach($emails as $_email){
		$envoyer_mail($_email, $sujet, $corps, '', $headers);
	}

}

