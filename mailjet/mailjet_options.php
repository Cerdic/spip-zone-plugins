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
		// http://code.spip.net/@envoyer_mail
		function inc_envoyer_mail($email, $sujet, $texte, $from = "", $headers = "") {
			include_spip('mailjet/envoyer_mail');
			return mailjet_envoyer_mail($email, $sujet, $texte, $from, $headers);
		}
	}
}

/**
 * Forcer les reglages SMTP de facteur si utilise
 * pour utiliser mailjet
 * @param $facteur
 */
function mailjet_facteur_pre_envoi($facteur){
	if ($GLOBALS['meta']['mailjet_enabled']){
		$facteur->Mailer	= 'smtp';
		$host = explode('://',$GLOBALS['meta']['mailjet_host']);
		$facteur->Host 	= end($host);
		$facteur->Port 	= $GLOBALS['meta']['mailjet_port'];
		$facteur->SMTPAuth = true;
		$facteur->Username = $GLOBALS['meta']['mailjet_username'];
		$facteur->Password = $GLOBALS['meta']['mailjet_password'];
		if (intval(phpversion()) >= 5) {
			if (reset($host)=="ssl")
				$facteur->SMTPSecure = 'ssl';
			elseif (reset($host)=="tls")
				$facteur->SMTPSecure = 'tls';
		}
		$facteur->AddCustomHeader('X-Mailer: Mailjet-for-Spip/2.0');
	}
	return $facteur;
}

function mailjet_formulaire_charger($flux){
	if ($GLOBALS['meta']['mailjet_enabled']){
		if ($flux['args']['form']=='configurer_facteur'){
			$flux['data']['_hidden'].='<script type="text/javascript">
			jQuery(function(){jQuery(".editer_facteur_smtp_param").remove();jQuery(".editer_facteur_smtp ").html("'.texte_script(_T('mailjet:info_mailjet_enabled')).'");});
			</script>';
		}
	}
	return $flux;
}