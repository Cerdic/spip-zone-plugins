<?php

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *                                                                         *
 *  Copyright (c) 2001-2006                                                *
 *  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James  *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

if (!defined("_ECRIRE_INC_VERSION")) return;

// http://doc.spip.org/@nettoyer_caracteres_mail
function nettoyer_caracteres_mail($t) {

	$t = filtrer_entites($t);

	if ($GLOBALS['meta']['charset'] <> 'utf-8') {
		$t = str_replace(
			array("&#8217;","&#8220;","&#8221;"),
			array("'",      '"',      '"'),
		$t);
	}

	$t = str_replace(
		array("&mdash;", "&endash;"),
		array("--","-" ),
	$t);

	return $t;
}

if (!function_exists('nettoyer_titre_email')){
	// http://doc.spip.org/@nettoyer_titre_email
	function nettoyer_titre_email($titre) {
		return str_replace("\n", ' ', supprimer_tags(extraire_multi($titre)));
	}
}

// http://doc.spip.org/@envoyer_mail
function inc_envoyer_mail_dist($email, $sujet, $texte, $from = "", $headers = "") {
	include_spip('inc/charsets');
	include_spip('inc/class.phpmailer');
	$mailer = new PHPMailer();
	$mailer->IsSMTP();
	$mailer->Host=$GLOBALS['meta']['smtp_host'];
	$mailer->Port=$GLOBALS['meta']['smtp_port'];
	$mailer->SMTPAuth=($GLOBALS['meta']['smtp_auth']!='oui')?false:true;
	$mailer->Username=$GLOBALS['meta']['smtp_username'];
	$mailer->Password=$GLOBALS['meta']['smtp_password'];
	$mailer->WordWrap=70;

	
	if (!email_valide($email)) return false;
	if ($email == _T('info_mail_fournisseur')) return false; // tres fort

	// Ajouter au besoin le \n final dans les $headers passes en argument
	if (strlen($headers)){
		if ($headers = trim($headers)) $headers .= "\n";
		$mailer->AddCustomHeader($headers);
	}

	if (!$from) {
		$email_envoi = $GLOBALS['meta']["email_envoi"];
		$mailer->From = email_valide($email_envoi) ? $email_envoi : $email;
	} else {
		$mailer->From = $from;
		$mailer->AddReplyTo($from);
	}
	$mailer->FromName = $mailer->From;
	
	spip_log("mail ($email): $sujet". ($from ?", from <$from>":''));

	$mailer->AddAddress($email); // To
	
	$charset = $GLOBALS['meta']['charset'];
	$content_type = "text/plain";
	if (preg_match(",Content-Type: ([^;]*); charset=(.*),i",$headers,$regs)){
		$charset = $regs[2];
		$content_type = $regs[1];
	}
	$mailer->CharSet = $charset;
	$mailer->ContentType = $content_type;

	// nettoyer les &eacute; &#8217, &emdash; etc...
	$texte = nettoyer_caracteres_mail($texte);
	$sujet = nettoyer_caracteres_mail($sujet);
	
	// encoder le sujet si possible selon la RFC
	if (init_mb_string()) {
		# un bug de mb_string casse mb_encode_mimeheader si l'encoding interne
		# est UTF-8 et le charset iso-8859-1 (constate php5-mac ; php4.3-debian)
		mb_internal_encoding($charset);
		$sujet = mb_encode_mimeheader($sujet, $charset, 'Q', "\n");
		mb_internal_encoding('utf-8');
	}

	$mailer->Body = $texte;
	$mailer->Subject = $sujet;
	return $mailer->Send();
	
}

?>
