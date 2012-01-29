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

function mail_normaliser_headers($headers, $from, $to, $texte, $parts="")
{
    $charset = $GLOBALS['meta']['charset'];

    // Ajouter le Content-Type et consort s'il n'y est pas deja
    if (strpos($headers, "Content-Type: ") === false)
        $type =
        "Content-Type: text/plain;charset=\"$charset\";\n".
        "Content-Transfer-Encoding: 8bit\n";
    else $type = '';

    // calculer un identifiant unique
    preg_match('/@\S+/', $from, $domain);
    $uniq = rand() . '_' . md5($to . $texte) . $domain[0];

    // Si multi-part, s'en servir comme borne ...
    if ($parts) {
        $texte = "--$uniq\n$type\n" . $texte ."\n";
        foreach ($parts as $part) {
            $n = strlen($part[1]) . ($part[0] ? "\n" : '');
            $e = join("\n", $part[0]);
            $texte .= "\n--$uniq\nContent-Length: $n$e\n\n" . $part[1];
        }
        $texte .= "\n\n--$uniq--\n";
        // Si boundary n'est pas entre guillemets,
        // elle est comprise mais le charset est ignoree !
        $type = "Content-Type: multipart/mixed; boundary=\"$uniq\"\n";
    }

    // .. et s'en servir pour plaire a SpamAssassin

    $mid = 'Message-Id: <' . $uniq . ">";

    // indispensable pour les sites qui collent d'office From: serveur-http
    // sauf si deja mis par l'envoyeur
    $rep = (strpos($headers,"Reply-To:")!==FALSE) ? '' : "Reply-To: $from\n";

    // Nettoyer les en-tetes envoyees
    if (strlen($headers)) $headers = trim($headers)."\n";

    // Et mentionner l'indeboulonable nomenclature ratee

    $headers .= "From: $from\n$type$rep$mid\nMIME-Version: 1.0\n";

    return array($headers, $texte);
}

if (!function_exists('nettoyer_titre_email')){
	// http://doc.spip.org/@nettoyer_titre_email
	function nettoyer_titre_email($titre) {
		return str_replace("\n", ' ', supprimer_tags(extraire_multi($titre)));
	}
}

// http://doc.spip.org/@envoyer_mail
function inc_envoyer_mail_dist($email, $sujet, $texte, $from = "", $headers = "") {

    if (! $GLOBALS['meta'] ['mj_customsmtp_enabled'])
    {
        return mj_customsmtp_envoyer_mail_original ($email, $sujet, $text, $from, $headers);
    }

	include_spip('inc/charsets');
	include_spip('inc/class.phpmailer');
	$mailer = new PHPMailer();
	$mailer->IsSMTP();
	$mailer->Host = $GLOBALS['meta']['mj_customsmtp_host'];
	$mailer->Port = $GLOBALS['meta']['mj_customsmtp_port'];
	$mailer->SMTPAuth = TRUE;
	$mailer->Username = $GLOBALS['meta']['mj_customsmtp_username'];
	$mailer->Password = $GLOBALS['meta']['mj_customsmtp_password'];
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

function mj_customsmtp_envoyer_mail_original ($destinataire, $sujet, $corps, $from = "", $headers = "") {

    if (!email_valide($destinataire)) return false;
    if ($destinataire == _T('info_mail_fournisseur')) return false; // tres fort
    global $hebergeur, $queue_mails;

    // Fournir si possible un Message-Id: conforme au RFC1036,
    // sinon SpamAssassin denoncera un MSGID_FROM_MTA_HEADER

    $email_envoi = $GLOBALS['meta']["email_webmaster"];
    if (!email_valide($email_envoi)) {
        spip_log("Meta email_envoi invalide. Le mail sera probablement vu comme spam.");
        $email_envoi = $destinataire;
    }

    if (is_array($corps)){
        $texte = $corps['texte'];
        $from = (isset($corps['from'])?$corps['from']:$from);
        $headers = (isset($corps['headers'])?$corps['headers']:$headers);
        if (is_array($headers))
            $headers = implode("\n",$headers);
        $parts = "";
        if ($corps['pieces_jointes'] AND function_exists('mail_embarquer_pieces_jointes'))
            $parts = mail_embarquer_pieces_jointes($corps['pieces_jointes']);
    } else
        $texte = $corps;

    if (!$from) $from = $email_envoi;

    // ceci est la RegExp NO_REAL_NAME faisant hurler SpamAssassin
    if (preg_match('/^["\s]*\<?\S+\@\S+\>?\s*$/', $from))
        $from .= ' (' . str_replace(')','', translitteration(str_replace('@', ' at ', $from))) . ')';

    // nettoyer les &eacute; &#8217, &emdash; etc...
    // les 'cliquer ici' etc sont a eviter;  voir:
    // http://mta.org.ua/spamassassin-2.55/stuff/wiki.CustomRulesets/20050914/rules/french_rules.cf
    $texte = nettoyer_caracteres_mail($texte);
    $sujet = nettoyer_caracteres_mail($sujet);

    // encoder le sujet si possible selon la RFC
    if (init_mb_string()) {
        # un bug de mb_string casse mb_encode_mimeheader si l'encoding interne
        # est UTF-8 et le charset iso-8859-1 (constate php5-mac ; php4.3-debian)
        $charset = $GLOBALS['meta']['charset'];
        mb_internal_encoding($charset);
        $sujet = mb_encode_mimeheader($sujet, $charset, 'Q', "\n");
        mb_internal_encoding('utf-8');
    }

    if (function_exists('wordwrap') && (preg_match(',multipart/mixed,',$headers) == 0))
        $texte = wordwrap($texte);

    list($headers, $texte) = mail_normaliser_headers($headers, $from, $destinataire, $texte, $parts);

    if (_OS_SERVEUR == 'windows') {
        $texte = preg_replace ("@\r*\n@","\r\n", $texte);
        $headers = preg_replace ("@\r*\n@","\r\n", $headers);
        $sujet = preg_replace ("@\r*\n@","\r\n", $sujet);
    }

    spip_log("mail $destinataire\n$sujet\n$headers",'mails');
    // mode TEST : forcer l'email
    if (defined('_TEST_EMAIL_DEST')) {
        if (!_TEST_EMAIL_DEST)
            return false;
        else
            $destinataire = _TEST_EMAIL_DEST;
    }

    switch($hebergeur) {
        case 'lycos':
            $queue_mails[] = array(
            	'email' => $destinataire,
            	'sujet' => $sujet,
            	'texte' => $texte,
            	'headers' => $headers);
            return true;
        case 'free':
            return false;
        default:
            return @mail($destinataire, $sujet, $texte, $headers);
    }
}

?>
