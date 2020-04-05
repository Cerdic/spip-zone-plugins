<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

function formulaires_clevermail_tester_charger_dist($pst_id=0){
	include_spip('inc/config');
	$valeurs = array(
		'email_test'                  => lire_config('facteur_adresse_envoi')=='oui'?lire_config('facteur_adresse_envoi_email'):$GLOBALS['meta']['email_webmaster'],
		'tester'                      => '',
		'pst_id'                      => $pst_id,
	);

	return $valeurs;
}

function formulaires_clevermail_tester_verifier_dist($pst_id=0){
	$erreurs = array();
	if (_request('tester')){
		if (!$email = _request('email_test')){
			$erreurs['email_test'] = _T('info_obligatoire');
		}
		elseif (!email_valide($email)) {
			$erreurs['email_test'] = _T('form_email_non_valide');
		}
	}

	
	if(count($erreurs)>0){
		$erreurs['message_erreur'] = _T('facteur:erreur_generale');
	}
	return $erreurs;
}

function formulaires_clevermail_tester_traiter_dist($pst_id=0){

	// faut-il envoyer un message de test ?
	if (_request('tester')){
		$res = array();
		$destinataire = _request('email_test');
		$err = clevermail_envoyer_mail_test($destinataire,$pst_id);
		if ($err) {
			$res['message_erreur'] = $err;
		}
		else {
			$res['message_ok'] = _T('facteur:email_test_envoye');
		}
	}
	
	return $res;
}

/**
 * Fonction pour tester un envoi de mail ver sun destinataire
 * renvoie une erreur eventuelle ou rien si tout est OK
 * @param string $destinataire
 * @param string $titre
 * @return string
 *   message erreur ou vide si tout est OK
 */
function clevermail_envoyer_mail_test($destinataire,$pst_id){

	include_spip('classes/facteur');
	$post = sql_fetsel("*", "spip_cm_posts", "pst_id = ".intval($pst_id));
	// message content
	$titre = $post['pst_subject'];
	
	$message_texte = $post['pst_text'];
	$message_texte = str_replace("(\r\n|\n|\n)", CM_NEWLINE, $message_texte);

	$message_html = $post['pst_html'];
	$message_html = str_replace("(\r\n|\n|\n)", CM_NEWLINE, $message_html);
	$corps = array(
		'html' => $message_html,
		'texte' => $message_texte,
		'exceptions' => true,
	);

	// passer par envoyer_mail pour bien passer par les pipeline et avoir tous les logs
	$envoyer_mail = charger_fonction('envoyer_mail','inc');
	try {
		$retour = $envoyer_mail($destinataire, $titre, $corps);
	}
	catch (Exception $e) {
		return $e->getMessage();
	}

	// si echec mais pas d'exception, on signale de regarder dans les logs
	if (!$retour) {
		return _T('facteur:erreur').' '._T('facteur:erreur_dans_log');
	}

	// tout est OK, pas d'erreur
	return "";
}
?>
