<?php
function formulaires_clevermail_list_edit_charger_dist($lst_id = -1) {
	if ($lst_id == -1 || !$valeurs = sql_fetsel('*', 'spip_cm_lists', 'lst_id='.intval($lst_id))) {
    $cm_mail_admin = sql_getfetsel('set_value', 'spip_cm_settings', 'set_name="CM_MAIL_ADMIN"');
		$valeurs = array(
			'lst_id' => -1,
			'lst_name' => '',
			'lst_comment' => '',
			'lst_moderation' => 'closed',
			'lst_moderator_email' => $cm_mail_admin,
			'lst_subscribe_subject' => _T('clevermail:confirmation_votre_inscription'),
			'lst_subscribe_text' => _T('clevermail:confirmation_votre_inscription_text'),
			'lst_subject' => '',
			'lst_unsubscribe_subject' => _T('clevermail:confirmation_votre_desinscription'),
			'lst_unsubscribe_text' => _T('clevermail:confirmation_votre_desinscription_text'),
			'lst_subject_tag' => 1,
			'lst_url_html' => "http://",
			'lst_url_text' => "http://"
		);
	}
	return $valeurs;
}

function formulaires_clevermail_list_edit_verifier_dist($lst_id = -1) {
	$erreurs = array();
	foreach(array('lst_name', 'lst_moderator_email', 'lst_url_html', 'lst_url_text') as $obligatoire) {
		if (!_request($obligatoire)) {
			$erreurs[$obligatoire] = 'Ce champ est obligatoire.';
		}
	}
	$nb = sql_countsel("spip_cm_lists", "lst_id != ".intval(_request('lst_id'))." AND lst_name = ".sql_quote(_request('lst_name')));
  if ($nb > 0) {
  	$erreurs['lst_name'] = _T('clevermail:lettre_meme_nom');
  }
	include_spip('inc/filtres');
	if (_request('lst_moderator_email') && !email_valide(_request('lst_moderator_email'))) {
		$erreurs['lst_moderator_email'] = 'Cette adresse e-mail n\'est pas valide.';
	}
	if (count($erreurs)) {
		$erreurs['message_erreur'] = 'Veuillez corriger votre saisie.';
	}
	return $erreurs;
}

function formulaires_clevermail_list_edit_traiter_dist($lst_id = -1) {
  $champs = array(
    'lst_name' => sql_quote(_request('lst_name')),
    'lst_comment' => sql_quote(_request('lst_comment')),
    'lst_moderation' => sql_quote(_request('lst_moderation')),
    'lst_moderator_email' => sql_quote(_request('lst_moderator_email')),
    'lst_subscribe_subject' => sql_quote(_request('lst_subscribe_subject')),
    'lst_subscribe_text' => sql_quote(_request('lst_subscribe_text')),
    'lst_subject' => sql_quote(_request('lst_subject')),
    'lst_unsubscribe_subject' => sql_quote(_request('lst_unsubscribe_subject')),
    'lst_unsubscribe_text' => sql_quote(_request('lst_unsubscribe_text')),
    'lst_subject_tag' => sql_quote(_request('lst_subject_tag')),
    'lst_url_html' => sql_quote(_request('lst_url_html')),
    'lst_url_text' => sql_quote(_request('lst_url_text'))
  );

  // Handle checkbox value
  if (isset($list['lst_subject_tag']) && ($list['lst_subject_tag'] == 'on' || $list['lst_subject_tag'] == 1)) {
    $champs['lst_subject_tag'] = 1;
  }
  
  if (_request('lst_id') == -1) {
    sql_insertq('spip_cm_lists', $champs);
    spip_log('Nouvelle liste « '._request('lst_name').' »', 'clevermail');
  } else {
  	sql_updateq('spip_cm_lists', $champs, "lst_id = ".intval(_request('lst_id')));
    spip_log('Modification de la liste « '._request('lst_name').' » (id = '._request('lst_id').')', 'clevermail');
  }

 	return array('message_ok' => 'ok', 'redirect' => generer_url_ecrire('clevermail'));
}
?>