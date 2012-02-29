<?php
/*
 * Plugin Mailjet
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

// http://doc.spip.org/@envoyer_mail
function inc_envoyer_mail($email, $sujet, $texte, $from = "", $headers = "") {
	include_spip('mailjet/envoyer_mail');
	return mailjet_envoyer_mail($email, $sujet, $texte, $from, $headers);
}