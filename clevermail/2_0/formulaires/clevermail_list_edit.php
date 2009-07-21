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
			'lst_url_text' => "http://",
		  'lst_auto_mode' => 'none',
		  'lst_auto_hour' => 8,
		  'lst_auto_week_day' => 1,
		  'lst_auto_month_day' => 1
		);
	}
	return $valeurs;
}

function formulaires_clevermail_list_edit_verifier_dist($lst_id = -1) {
	$erreurs = array();
	foreach(array('lst_name', 'lst_moderator_email', 'lst_url_html', 'lst_url_text') as $obligatoire) {
		if (!_request($obligatoire)) {
			$erreurs[$obligatoire] = _T('clevermail:ce_champ_est_obligatoire');
		}
	}
	$nb = sql_countsel("spip_cm_lists", "lst_id != ".intval(_request('lst_id'))." AND lst_name = ".sql_quote(_request('lst_name')));
  if ($nb > 0) {
  	$erreurs['lst_name'] = _T('clevermail:lettre_meme_nom');
  }
	include_spip('inc/filtres');
	if (_request('lst_moderator_email') && !email_valide(_request('lst_moderator_email'))) {
		$erreurs['lst_moderator_email'] = _T('clevermail:cette_adresse_email_n_est_pas_valide');
	}
	if (_request('lst_auto_mode') && !in_array(_request('lst_auto_mode'), array('none', 'day', 'week', 'month'))) {
		$erreurs['lst_auto_mode'] = _T('clevermail:auto_erreur_ce_mode_automatisation_existe_pas');
	}
  if (_request('lst_auto_hour') && (intval(_request('lst_auto_hour')) < 0 || intval(_request('lst_auto_hour')) > 23)) {
    $erreurs['lst_auto_hour'] = _T('clevermail:auto_erreur_cette_heure_existe_pas');
  }
  if (_request('lst_auto_week_day') && (intval(_request('lst_auto_week_day')) < 0 || intval(_request('lst_auto_week_day')) > 6)) {
    $erreurs['lst_auto_week_day'] = _T('clevermail:auto_erreur_ce_jour_semaine_existe_pas');
  }
  if (_request('lst_auto_month_day') && (intval(_request('lst_auto_month_day')) < 0 || intval(_request('lst_auto_month_day')) > 31)) {
    $erreurs['lst_auto_month_day'] = _T('clevermail:auto_erreur_ce_jour_mois_existe_pas');
  } elseif (intval(_request('lst_auto_month_day')) > 28) {
    $erreurs['lst_auto_month_day'] = _T('clevermail:auto_erreur_ce_jour_mois_pas_possible');
  }
  if (count($erreurs)) {
		$erreurs['message_erreur'] = _T('clevermail:veuillez_corriger_votre_saisie');
	}
	return $erreurs;
}

function formulaires_clevermail_list_edit_traiter_dist($lst_id = -1) {
  $champs = array(
    'lst_name' => _request('lst_name'),
    'lst_comment' => _request('lst_comment'),
    'lst_moderation' => _request('lst_moderation'),
    'lst_moderator_email' => _request('lst_moderator_email'),
    'lst_subscribe_subject' => _request('lst_subscribe_subject'),
    'lst_subscribe_text' => _request('lst_subscribe_text'),
    'lst_subject' => _request('lst_subject'),
    'lst_unsubscribe_subject' => _request('lst_unsubscribe_subject'),
    'lst_unsubscribe_text' => _request('lst_unsubscribe_text'),
    'lst_subject_tag' => _request('lst_subject_tag'),
    'lst_url_html' => _request('lst_url_html'),
    'lst_url_text' => _request('lst_url_text'),
    'lst_auto_mode' => _request('lst_auto_mode'),
    'lst_auto_hour' => intval(_request('lst_auto_hour')),
    'lst_auto_week_day' => intval(_request('lst_auto_week_day')),
    'lst_auto_month_day' => intval(_request('lst_auto_month_day'))
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