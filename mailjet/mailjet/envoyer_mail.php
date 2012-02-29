<?php
/*
 * Plugin Mailjet
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

// inclure le fichier natif de SPIP, pour les fonctions annexes
include_once _DIR_RESTREINT."inc/envoyer_mail.php";


/**
 * @param string $destinataire
 * @param string $sujet
 * @param string|array $corps
 *   au format string, c'est un corps d'email au format texte, comme supporte nativement par le core
 *   au format array, c'est un corps etendu qui peut contenir
 *     string texte : le corps d'email au format texte
 *     string html : le corps d'email au format html
 *     string from : email de l'envoyeur (prioritaire sur argument $from de premier niveau, deprecie)
 *     string nom_envoyeur : un nom d'envoyeur pour completer l'email from
 *     string cc : destinataires en copie conforme
 *     string bcc : destinataires en copie conforme cachee
 *     string|array repondre_a : une ou plusieurs adresses à qui répondre
 *     string adresse_erreur : addresse de retour en cas d'erreur d'envoi
 *     array pieces_jointes : listes de pieces a embarquer dans l'email, chacune au format array :
 *       string chemin : chemin file system pour trouver le fichier a embarquer
 *       string nom : nom du document tel qu'apparaissant dans l'email
 *       string encodage : encodage a utiliser, parmi 'base64', '7bit', '8bit', 'binary', 'quoted-printable'
 *       string mime : mime type du document
 *     array headers : tableau d'en-tetes personalises, une entree par ligne d'en-tete
 * @param string $from (deprecie, utiliser l'entree from de $corps)
 * @param string $headers (deprecie, utiliser l'entree headers de $corps)
 * @return bool
 */
function mailjet_envoyer_mail($destinataire, $sujet, $corps, $from = "", $headers = "") {
	$message_html	= '';
	$message_texte	= '';

	if (!email_valide($destinataire)) return false;
	if ($destinataire == _T('info_mail_fournisseur')) return false;

	// si $corps est un tableau -> fonctionnalites etendues
	// avec entrees possible : html, texte, pieces_jointes, nom_envoyeur, ...
	if (is_array($corps)) {
		$message_html	= $corps['html'];
		$message_texte	= nettoyer_caracteres_mail($corps['texte']);
		$pieces_jointes	= $corps['pieces_jointes'];
		$nom_envoyeur = $corps['nom_envoyeur'];
		$from = (isset($corps['from'])?$corps['from']:$from);
		$cc = $corps['cc'];
		$bcc = $corps['bcc'];
		$repondre_a = $corps['repondre_a'];
		$adresse_erreur = $corps['adresse_erreur'];
		$headers = (isset($corps['headers'])?$corps['headers']:$headers);
		if (is_string($headers))
			$headers = array_map('trim',explode("\n",trim($headers)));
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
		$headers = array_map('trim',explode("\n",trim($headers)));
	}
	$sujet = nettoyer_titre_email($sujet);

	include_spip('inc/charsets');
	include_spip('mailjet/class.phpmailer');
	$mailer = new PHPMailer();
	$mailer->IsSMTP();
	$mailer->Host = $GLOBALS['meta']['mailjet_host'];
	$mailer->Port = $GLOBALS['meta']['mailjet_port'];
	$mailer->SMTPAuth = TRUE;
	$mailer->Username = $GLOBALS['meta']['mailjet_username'];
	$mailer->Password = $GLOBALS['meta']['mailjet_password'];
	$mailer->WordWrap=70;

	$charset = $GLOBALS['meta']['charset'];

	$mailer->AddCustomHeader('X-Mailer: Mailjet-for-Spip/2.0');
	if (count($headers)){
		foreach ($headers as $h){
			if ($h){
				$mailer->AddCustomHeader($h);
				if (preg_match(",Content-Type: ([^;]*); charset=(.*),i",$h,$regs)){
					$charset = $regs[2];
					$content_type = $regs[1];
				}
			}
		}
	}

	$mailer->CharSet = $charset;

	// On ajoute le courriel de l'envoyeur s'il est fournit par la fonction
	if (empty($from)) {
		$from = $GLOBALS['meta']["email_envoi"];
		if (empty($from) OR !email_valide($from)) {
			spip_log("Meta email_envoi invalide. Le mail sera probablement vu comme spam.");
			$from = $destinataire;
		}
	}
	if (!empty($from)){
		$mailer->From = $from;
		// la valeur par défaut de la config n'est probablement pas valable pour ce mail,
		// on l'écrase pour cet envoi
		$mailer->FromName = $from;
	}

	// On ajoute le nom de l'envoyeur s'il fait partie des options
	if ($nom_envoyeur)
		$mailer->FromName = $nom_envoyeur;

	// S'il y a des copies à envoyer
	if ($cc){
		if (is_array($cc))
			foreach ($cc as $courriel)
				$mailer->AddCC($courriel);
		else
			$mailer->AddCC($cc);
	}

	// S'il y a des copies cachées à envoyer
	if ($bcc){
		if (is_array($bcc))
			foreach ($bcc as $courriel)
				$mailer->AddBCC($courriel);
		else
			$mailer->AddBCC($bcc);
	}

	// S'il y a des copies cachées à envoyer
	if ($repondre_a){
		if (is_array($repondre_a))
			foreach ($repondre_a as $courriel)
				$mailer->AddReplyTo($courriel);
		else
			$mailer->AddReplyTo($repondre_a);
	}


	// plusieurs destinataires peuvent etre fournis separes par des virgules
	// c'est un format standard dans l'envoi de mail
	// les passer au format array pour phpMailer
	// mais ne pas casser si on a deja un array en entree
	if (is_array($destinataire))
		$destinataire = implode(", ",$destinataire);

	$destinataire = array_map('trim',explode(",",$destinataire));
	foreach($destinataire as $d)
		$mailer->AddAddress($d);

	if (init_mb_string()){
		mb_internal_encoding($charset);
		$sujet = mb_encode_mimeheader($sujet, $charset, 'Q', "\n");
		mb_internal_encoding('utf-8');
	}
	$mailer->Subject = $sujet;

	if (!empty($message_html)) {
		$message_html = unicode_to_utf_8(charset2unicode($message_html,$GLOBALS['meta']['charset']));
		$mailer->Body = $message_html;
		$mailer->IsHTML(true);
	}
	if (!empty($message_texte)) {
		$message_texte = unicode_to_utf_8(charset2unicode($message_texte,$GLOBALS['meta']['charset']));
		if (!$mailer->Body) {
			$mailer->IsHTML(false);
			$mailer->Body = $message_texte;
		}
		else {
			$mailer->AltBody = $message_texte;
		}
	}

	// On génère les headers
	$head = $mailer->CreateHeader();
	// Et c'est parti on envoie enfin
	spip_log("mail via mailjet\n$head"."Destinataire:".print_r($destinataire,true),'mail');
	spip_log("mail\n$head"."Destinataire:".print_r($destinataire,true),'mailjet');

	return $mailer->Send();
}

?>