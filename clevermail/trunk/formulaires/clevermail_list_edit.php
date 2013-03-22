<?php
function formulaires_clevermail_list_edit_charger_dist($lst_id = -1) {
	// Ces define sont mis ici car dans clevermail_options.php, il etait impossible de surcharger avec un plugin ayant :
	// 	<utilise id="clevermail" version="[2.5.0;]" />
	// Pour proposer une URL complète (true) ou juste le chemin du squelette (false) à la création d'une nouvelle lettre
	if (!defined('_CLEVERMAIL_DISTANT')) define("_CLEVERMAIL_DISTANT", true);
	if (!defined('_CLEVERMAIL_NOUVEAUTES_HTML')) define("_CLEVERMAIL_NOUVEAUTES_HTML", 'clevermail_nouveautes_html');
	// _CLEVERMAIL_NOUVEAUTES_HTML_OPTION est facultatif. Il permet de completer l'url amorcee avec _CLEVERMAIL_NOUVEAUTES_HTML.
	// define("_CLEVERMAIL_NOUVEAUTES_HTML_OPTION", 'cat=mot&sujet=1&pied=1&entete=1');
	if (!defined('_CLEVERMAIL_NOUVEAUTES_TEXT')) define("_CLEVERMAIL_NOUVEAUTES_TEXT", 'clevermail_nouveautes_text');
	// _CLEVERMAIL_NOUVEAUTES_TEXT_OPTION est facultatif. Il permet de completer l'url amorcee avec _CLEVERMAIL_NOUVEAUTES_TEXT.
	// define("_CLEVERMAIL_NOUVEAUTES_TEXT_OPTION", 'cat=mot&sujet=1&pied=1&entete=1');
	if ($valeurs = sql_fetsel('*', 'spip_cm_lists', 'lst_id='.intval($lst_id))) {
		$valeurs['lst_auto_week_days'] = explode(',', $valeurs['lst_auto_week_days']);
	} else {
    $cm_mail_admin = sql_getfetsel('set_value', 'spip_cm_settings', 'set_name="CM_MAIL_ADMIN"');
    	if (defined('_CLEVERMAIL_NOUVEAUTES_HTML_OPTION')) {
			if (_CLEVERMAIL_DISTANT) {
				$url_html = generer_url_public(_CLEVERMAIL_NOUVEAUTES_HTML,_CLEVERMAIL_NOUVEAUTES_HTML_OPTION);
			} else {
				$url_html = _CLEVERMAIL_NOUVEAUTES_HTML;
			}
		} else {
			if (_CLEVERMAIL_DISTANT) {
				$url_html = generer_url_public(_CLEVERMAIL_NOUVEAUTES_HTML);
			} else {
				$url_html = _CLEVERMAIL_NOUVEAUTES_HTML;
			}
		}
		if (defined('_CLEVERMAIL_NOUVEAUTES_TEXT_OPTION')) {
			if (_CLEVERMAIL_DISTANT) {
				$url_text = generer_url_public(_CLEVERMAIL_NOUVEAUTES_TEXT,_CLEVERMAIL_NOUVEAUTES_TEXT_OPTION);
			} else {
				$url_text = _CLEVERMAIL_NOUVEAUTES_TEXT;
			}
		} else {
			if (_CLEVERMAIL_DISTANT) {
				$url_text = generer_url_public(_CLEVERMAIL_NOUVEAUTES_TEXT);
			} else {
				$url_text = _CLEVERMAIL_NOUVEAUTES_TEXT;
			}
		}
		$valeurs = array(
			'lst_id' => -1,
			'lst_name' => '',
			'lst_comment' => '',
			'lst_moderation' => 'closed',
			'lst_moderator_email' => $cm_mail_admin,
			'lst_subscribe_subject' => _T('clevermail:confirmation_votre_inscription'),
			'lst_subscribe_text' => _T('clevermail:confirmation_votre_inscription_text'),
			'lst_unsubscribe_subject' => _T('clevermail:confirmation_votre_desinscription'),
			'lst_unsubscribe_text' => _T('clevermail:confirmation_votre_desinscription_text'),
			'lst_subject_tag' => 1,
			'lst_url_html' => $url_html,
			'lst_url_text' => $url_text,
		  'lst_auto_mode' => 'none',
		  'lst_auto_hour' => 8,
			'lst_auto_week_days' => array(1),
		  'lst_auto_month_day' => 1,
		  'lst_auto_subscribers' => '',
		  'lst_auto_subscribers_mode' => 0
		);
	}
	return $valeurs;
}

function formulaires_clevermail_list_edit_verifier_dist($lst_id = -1) {
	$erreurs = array();
	foreach(array('lst_name', 'lst_moderator_email', 'lst_url_html') as $obligatoire) {
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
	if (_request('lst_auto_mode') && _request('lst_auto_mode') != 'none') {
		if (in_array(_request('lst_auto_mode'), array('day', 'week', 'month'))) {
		  if (_request('lst_auto_hour') && (intval(_request('lst_auto_hour')) < 0 || intval(_request('lst_auto_hour')) > 23)) {
		    $erreurs['lst_auto_hour'] = _T('clevermail:auto_erreur_cette_heure_existe_pas');
		  }
			switch(_request('lst_auto_mode')) {
				case 'day':
					break;
			  case 'week':
          if (!_request('lst_auto_week_days') || count(_request('lst_auto_week_days')) == 0) {
            $erreurs['lst_auto_week_days'] = _T('clevermail:auto_erreur_choisir_un_jour_minimum');
          } elseif (min(_request('lst_auto_week_days')) < 0 || max(_request('lst_auto_week_days')) > 6) {
            $erreurs['lst_auto_week_days'] = _T('clevermail:auto_erreur_ce_jour_semaine_existe_pas');
          }
					break;
				case 'month':
				  if (_request('lst_auto_month_day') && (intval(_request('lst_auto_month_day')) < 0 || intval(_request('lst_auto_month_day')) > 31)) {
				    $erreurs['lst_auto_month_day'] = _T('clevermail:auto_erreur_ce_jour_mois_existe_pas');
				  } elseif (intval(_request('lst_auto_month_day')) > 28) {
				    $erreurs['lst_auto_month_day'] = _T('clevermail:auto_erreur_ce_jour_mois_pas_possible');
				  }
					break;
			}
		} else {
      $erreurs['lst_auto_mode'] = _T('clevermail:auto_erreur_ce_mode_automatisation_existe_pas');
		}
	}
  if (_request('lst_auto_subscribers') != '') {
  	include_spip('inc/distant');
    if ($adresses = recuperer_page(_request('lst_auto_subscribers'))) {
	    include_spip('inc/clevermail_abonnes');
	    if (!clevermail_verification_adresses_email($adresses)) {
	      $erreurs['lst_auto_subscribers'] = _T('clevermail:le_format_des_adresses_email_ne_semble_pas_bon');
	    }
    } else {
    	$erreurs['lst_auto_subscribers'] = _T('clevermail:fichier_adresses_distant_impossible_telecharger');
    }
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
    'lst_unsubscribe_subject' => _request('lst_unsubscribe_subject'),
    'lst_unsubscribe_text' => _request('lst_unsubscribe_text'),
    'lst_subject_tag' => _request('lst_subject_tag'),
    'lst_url_html' => _request('lst_url_html'),
    'lst_url_text' => _request('lst_url_text'),
    'lst_auto_mode' => _request('lst_auto_mode'),
    'lst_auto_hour' => intval(_request('lst_auto_hour')),
    'lst_auto_week_days' => implode(',', _request('lst_auto_week_days')),
    'lst_auto_month_day' => intval(_request('lst_auto_month_day')),
    'lst_auto_subscribers' => _request('lst_auto_subscribers'),
    'lst_auto_subscribers_mode' => intval(_request('lst_auto_subscribers_mode'))
  );

  // Handle checkbox value
  if (isset($list['lst_subject_tag']) && ($list['lst_subject_tag'] == 'on' || $list['lst_subject_tag'] == 1)) {
    $champs['lst_subject_tag'] = 1;
  }

  if (_request('lst_id') == -1) {
    sql_insertq('spip_cm_lists', $champs);
    // TODO : log en chaîne de langue
    spip_log('Nouvelle liste « '._request('lst_name').' »', 'clevermail');
  } else {
  	sql_updateq('spip_cm_lists', $champs, "lst_id = ".intval(_request('lst_id')));
  	// TODO : log en chaîne de langue
    spip_log('Modification de la liste « '._request('lst_name').' » (id = '._request('lst_id').')', 'clevermail');
  }

 	return array('message_ok' => 'ok', 'redirect' => generer_url_ecrire('clevermail_lists'));
}
?>