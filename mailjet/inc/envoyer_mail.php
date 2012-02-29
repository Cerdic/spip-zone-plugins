<?php
/*
 * Plugin Mailjet
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

// inclure le fichier natif de SPIP, pour les fonctions annexes
include_once _DIR_RESTREINT."inc/envoyer_mail.php";


// http://doc.spip.org/@envoyer_mail
function inc_envoyer_mail($email, $sujet, $texte, $from = "", $headers = "") {

  if (! $GLOBALS['meta'] ['mailjet_enabled']){
		return inc_envoyer_mail_dist($email, $sujet, $texte, $from, $headers);
	}

	include_spip('inc/charsets');
	include_spip('inc/class.phpmailer');
	$mailer = new PHPMailer();
	$mailer->IsSMTP();
	$mailer->Host = $GLOBALS['meta']['mailjet_host'];
	$mailer->Port = $GLOBALS['meta']['mailjet_port'];
	$mailer->SMTPAuth = TRUE;
	$mailer->Username = $GLOBALS['meta']['mailjet_username'];
	$mailer->Password = $GLOBALS['meta']['mailjet_password'];
	$mailer->WordWrap=70;


	if (!email_valide($email)) return false;
	if ($email == _T('info_mail_fournisseur')) return false;

	if (strlen($headers)){
		if ($headers = trim($headers)) $headers .= "\n";
		$mailer->AddCustomHeader($headers);
	}

    $mailer->AddCustomHeader('X-Mailer: Mailjet-for-Spip/1.0');

	if (!$from) {
		$email_envoi = $GLOBALS['meta']["email_webmaster"];
		$mailer->From = email_valide($email_envoi) ? $email_envoi : $email;
	} else {
		$mailer->From = $from;
		$mailer->AddReplyTo($from);
	}
	$mailer->FromName = $mailer->From;

	spip_log("mail ($email): $sujet". ($from ?", from <$from>":''));

	$mailer->AddAddress($email);

	$charset = $GLOBALS['meta']['charset'];
	$content_type = "text/plain";

    if (preg_match(",Content-Type: ([^;]*); charset=(.*),i",$headers,$regs))
	{
		$charset = $regs[2];
		$content_type = $regs[1];
	}

    $mailer->CharSet = $charset;
	$mailer->ContentType = $content_type;

	$texte = nettoyer_caracteres_mail($texte);
	$sujet = nettoyer_caracteres_mail($sujet);

	if (init_mb_string())
	{
		mb_internal_encoding($charset);
		$sujet = mb_encode_mimeheader($sujet, $charset, 'Q', "\n");
		mb_internal_encoding('utf-8');
	}

	$mailer->Body = $texte;
	$mailer->Subject = $sujet;

	return $mailer->Send();
}

?>