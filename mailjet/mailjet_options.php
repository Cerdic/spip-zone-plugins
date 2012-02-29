<?php
/*
 * Plugin Mailjet
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

/*
 * Si pas de plugin facteur, surcharger inc_envoyer_mail de SPIP
 * et router sur la fonction propre a mailjet
 */
if (!test_plugin_actif('facteur')){
	if ($GLOBALS['meta']['mailjet_enabled']){
		// http://doc.spip.org/@envoyer_mail
		function inc_envoyer_mail($email, $sujet, $texte, $from = "", $headers = "") {
			include_spip('mailjet/envoyer_mail');
			return mailjet_envoyer_mail($email, $sujet, $texte, $from, $headers);
		}
	}
}