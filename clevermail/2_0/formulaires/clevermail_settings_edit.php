<?php
function formulaires_clevermail_settings_edit_charger_dist() {
	$keys = array('CM_MAIL_ADMIN', 'CM_MAIL_FROM', 'CM_MAIL_RETURN', 'CM_SEND_NUMBER');
	$valeurs = array();
	foreach($keys as $key) {
		$valeurs[$key] = sql_getfetsel("set_value", "spip_cm_settings", "set_name=".sql_quote($key);
	}
	return $valeurs;
}

function formulaires_clevermail_settings_edit_verifier_dist() {
  include_spip('inc/filtres');
	$mails = array('CM_MAIL_ADMIN', 'CM_MAIL_FROM', 'CM_MAIL_RETURN');
	$keys = array('CM_MAIL_ADMIN', 'CM_MAIL_FROM', 'CM_MAIL_RETURN', 'CM_SEND_NUMBER');
	$erreurs = array();
	foreach($keys as $obligatoire) {
		if (!_request($obligatoire)) {
			$erreurs[$obligatoire] = 'Ce champ est obligatoire.';
		}
	}
	foreach($mails as $mail) {
		if (!email_valide(_request($mail))) {
      $erreurs[$mail] = 'Cette adresse e-mail n\'est pas valide.';
    }
	}
	if (count($erreurs)) {
		$erreurs['message_erreur'] = 'Veuillez corriger votre saisie.';
	}
	return $erreurs;
}

function formulaires_clevermail_settings_edit_traiter_dist() {
  $keys = array('CM_MAIL_ADMIN', 'CM_MAIL_FROM', 'CM_MAIL_RETURN', 'CM_SEND_NUMBER');
  foreach($keys as $key) {
    sql_updateq('spip_cm_settings', array('set_value' => sql_quote(_request($key))), "set_name=".sql_quote($key);
  }
  spip_log('Modification de la configuration.', 'clevermail');

 	return array('message_ok' => 'ok', 'editable' => 1);
}
?>