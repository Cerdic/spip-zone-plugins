<?php
/*
 * SpipMailer
 * Envoyer des mails par SMTP sur SPIP
 *
 * Auteurs :
 * Quentin Drouet (kent1@arscenic.info)
 *
 * © 2008 - Distribue sous licence GNU/GPL
 * Pour plus de details voir le fichier COPYING.txt
 *
 */
 
function inc_envoyer_phpmail_dist($email,$sujet,$texte,$from = "",$headers = "",$fromname = "",$reply_to = "",$errors_to = "",$return_path = "",$lang = "",$isHtml="false"){
	if($lang){
		lang_select($lang);
	}
	include_spip('inc/distant');
	include_spip("lib/phpMailer_v2.2.1_/class.phpmailer");
	
	include_spip('inc/charsets');
	// Pour email_valide()
	include_spip('inc/filtres');

	if ($email == _T('info_mail_fournisseur')) return false; // tres fort
	
	// Traiter les headers existants
	if (strlen($headers)) $headers = trim($headers)."\n";
	
	// Fournir si possible un Message-Id: conforme au RFC1036,
	// sinon SpamAssassin denoncera un MSGID_FROM_MTA_HEADER
	if (!$from) $from = $GLOBALS['meta']["email_envoi"] ? $GLOBALS['meta']["email_envoi"] : $GLOBALS['meta']["email_webmaster"];
	
	// ceci est la RegExp NO_REAL_NAME faisant hurler SpamAssassin
	if (preg_match('/^["\s]*\<?\S+\@\S+\>?\s*$/', $from))
	$from .= ' (' . str_replace(')','', translitteration(str_replace('@', ' at ', $from))) . ')';

	$fromname = $fromname ? $fromname : lire_config('phpmailer/fromname');
	$fromname = $fromname ? $fromname : $GLOBALS['meta']["nom_site"];
	
	$methode = lire_config('phpmailer/methode') ? lire_config('phpmailer/methode') : "mail";
	
	$reply_to = $reply_to ? $reply_to : lire_config('phpmailer/reply_to');
	$reply_to = $reply_to ? $reply_to : $from;
	
	$mail = new PHPMailer();
	$mail->AddCustomHeader("Errors-To: ".$from); 
	$mail->AddCustomHeader("Return-Path: ".$from); 
	
	if ($methode = 'smtp'){
		$mail->IsSMTP();
		$mail->Mailer = "smtp"; 
		//Nom du serveur SMTP
		$mail->Host = lire_config('phpmailer/smtp_adresse');
		//Le serveur necessite t il une authentification?
		$mail->SMTPAuth = lire_config('phpmailer/smtp_auth');
		if (lire_config('phpmailer/smtp_auth')== 'true'){
			//si il necessite authentification : User / MDP
			$mail->Username = lire_config('phpmailer/smtp_username'); 
			$mail->Password = lire_config('phpmailer/smtp_password');
		}
	}
	
	if ($methode = 'mail'){
		$mail->Mailer = "mail"; 
	}
	
	$charset = $GLOBALS['meta']['charset'];
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
	
	$language = $lang ? $lang : $GLOBALS['meta']['langue_site'];
	
	$mail->SetLanguage($language, "phpmailer/language/");
	$mail->CharSet	= $charset;  // Jeu de caracteres
	$mail->Timeout	= "20";
	$mail->From     = $from;
	$mail->FromName = $fromname;
	
	if(!is_array($email)){
		$separateur = explode(',',$email);		
	}
	else{
		$separateur = $email;
	}
	foreach($separateur as $email_dest) {
		if (email_valide($email_dest)){
			$mail->AddAddress($email_dest);// on ajoute chaque destinataire en vérifiant leur email (ce qui est sensé être déjà fait)
			spip_log("On ajoute l'adresse $email_dest dans les destinataires","phpmailer");
		}
		else{
			spip_log("L'adresse $email_dest n'est pas bonne et n'est donc pas ajoutée","phpmailer");
		}
	};
	
	if (strpos($headers,"Reply-To:")===FALSE)
		$mail->AddReplyTo($reply_to);
	
	$mail->Subject = $sujet;
	$mail->Body    = $texte;
	if($isHtml){
		$texte_body = "-----------------------\n";
		$texte_body .= _T('phpmailer:avertissement_mail_texte')."\n";
		$texte_body .= "-----------------------\n\n";
		$texte_body .= trim(supprimer_tags($texte));
		$texte_body = nettoyer_caracteres_mail($texte_body);
		if (function_exists('wordwrap'))
			$texte_body = wordwrap($texte_body);
		$mail->AltBody = $texte_body; // optional, comment out and test
		$mail->IsHTML(true);
	}
	// ENVOI (5 essais)
	$envoi = $mail->Send();
	$essai=1; 
   	while((!$envoi)&&($essai<5)&&($mail->ErrorInfo!="SMTP Error: Data not accepted")){
	   sleep(5);
     	   echo $mail->ErrorInfo;
     	   $envoi = $mail->Send();
     	   $essai=$essai+1;				
   	}
	spip_log("Email envoye - From : ".$from." to : ".$email." Sujet ".$sujet, "phpmailer");
	if($lang){
		lang_select();
	}
	return true;
}
?>