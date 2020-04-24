<?php
/**
 * Plugin Facteur 4
 * (c) 2009-2019 Collectif SPIP
 * Distribue sous licence GPL
 *
 * @package SPIP\Facteur\Formulaires\Tester_facteur
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function formulaires_tester_facteur_charger_dist() {
	include_spip('inc/config');

	$valeurs = array(
		'email_test' => $GLOBALS['meta']['email_webmaster'],
		'email_test_from' => '',
		'email_test_important' => 0,
	);
	if (!empty($GLOBALS['visiteur_session']['email'])) {
		$valeurs['email_test'] = $GLOBALS['visiteur_session']['email'];
	}

	if (defined('_TEST_EMAIL_DEST')) {
		if (_TEST_EMAIL_DEST) {
			$valeurs['_message_warning'] = _T('facteur:info_envois_forces_vers_email', array('email' => _TEST_EMAIL_DEST));
		}
		else {
			$valeurs['_message_warning'] = _T('facteur:info_envois_bloques_constante');
		}
	}

	if (isset($GLOBALS['_message_html_test'])) {
		$valeurs['_message_html_test'] = $GLOBALS['_message_html_test'];
	}

	return $valeurs;
}

function formulaires_tester_facteur_verifier_dist() {
	$erreurs = array();

	if (!$email = _request('email_test')) {
		$erreurs['email_test'] = _T('info_obligatoire');
	} elseif (!email_valide($email)) {
		$erreurs['email_test'] = _T('form_email_non_valide');
	}
	if ($from = _request('email_test_from')
	  and !email_valide($from)) {
		$erreurs['email_test_from'] = _T('form_email_non_valide');
	}

	return $erreurs;
}

function formulaires_tester_facteur_traiter_dist() {

	// envoyer un message de test ?
	$res = array();
	$destinataire = _request('email_test');
	$message_html = '';
	$options = array();
	if ($from = _request('email_test_from')) {
		$options['from'] = $from;
	}
	if (_request('email_test_important')) {
		$options['important'] = true;
	}

	$err = facteur_envoyer_mail_test($destinataire, _T('facteur:corps_email_de_test'), $message_html, $options);
	if ($err) {
		$res['message_erreur'] = nl2br($err);
	} else {
		$res['message_ok'] = _T('facteur:email_test_envoye');
		$GLOBALS['_message_html_test'] = $message_html;
	}

	return $res;
}

/**
 * Inliner du contenu base64 pour presenter le html du mail de test envoye
 * @param string $texte
 * @param string $type
 * @return string
 */
function facteur_inline_base64src($texte, $type="text/html"){
	return "data:$type;charset=".$GLOBALS['meta']['charset'].";base64,".base64_encode($texte);
}

/**
 * Fonction pour tester un envoi de mail ver sun destinataire
 * renvoie une erreur eventuelle ou rien si tout est OK
 * @param string $destinataire
 * @param string $titre
 * @param string $message_html
 * @param array $options
 * @return string
 *   message erreur ou vide si tout est OK
 */
function facteur_envoyer_mail_test($destinataire, $titre, &$message_html, $options = array()) {

	include_spip('classes/facteur');

	$piece_jointe = array();

	if (test_plugin_actif('medias')) {
		include_spip('inc/documents');
		// trouver une piece jointe dans les documents si possible, la plus legere possible, c'est juste pour le principe
		$docs = sql_allfetsel('*', 'spip_documents', 'media='.sql_quote('file').' AND distant='.sql_quote('non').' AND brise=0','', 'taille', '0,10');
		foreach ($docs as $doc) {
			$file = get_spip_doc($doc['fichier']);
			if (file_exists($file)) {
				$mime = sql_getfetsel('mime_type', 'spip_types_documents', 'extension='.sql_quote($doc['extension']));
				$piece_jointe = array(
					'chemin' => $file,
					'nom' => $doc['titre'] ? $doc['titre'] : basename($doc['fichier']),
					'mime' => $mime,
				);
				break;
			}
		}
	}

	$message_html	= recuperer_fond('emails/test_email_html', array('piece_jointe' => $piece_jointe));
	$message_texte	= recuperer_fond('emails/test_email_texte', array('piece_jointe' => $piece_jointe));
	$corps = array(
		'html' => $message_html,
		'texte' => $message_texte,
		'exceptions' => true,
	);

	if ($piece_jointe) {
		$corps['pieces_jointes'] = array($piece_jointe);
	}

	if ($options) {
		$corps = array_merge($options, $corps);
	}

	// passer par envoyer_mail pour bien passer par les pipeline et avoir tous les logs
	$envoyer_mail = charger_fonction('envoyer_mail', 'inc');
	try {
		$retour = $envoyer_mail($destinataire, $titre, $corps);
	} catch (Exception $e) {
		return $e->getMessage();
	}

	// si echec mais pas d'exception, on signale de regarder dans les logs
	if (!$retour) {
		return _T('facteur:erreur').' '._T('facteur:erreur_dans_log');
	}

	// tout est OK, pas d'erreur
	return '';
}
