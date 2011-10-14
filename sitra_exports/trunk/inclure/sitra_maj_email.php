<?php

if (!defined('_ECRIRE_INC_VERSION')) return;

/**
// Envoi des emails
**/

// pour les messages mail
$objet = 'Import sitra du : '.date('d/m/Y H:i')."\n";

if ($GLOBALS['sitra_config']['erreur'])
	$objet = $GLOBALS['sitra_config']['mail_objet'];

$mail_dest = lire_config('sitra_config/mail_dest');

$mail_from = lire_config('sitra_config/mail_from');

if ($mail_dest and $mail_from) {
	$envoyer_mail = charger_fonction('envoyer_mail','inc');
	$envoyer_mail($mail_dest, $objet, $GLOBALS['sitra_config']['mail_message'], $mail_from, 'X-Originating-IP: '.$GLOBALS['ip']."\n".'Return-Path: -f'.$mail_from);
}

message($nl.'/// Envoi des emails');

?>

