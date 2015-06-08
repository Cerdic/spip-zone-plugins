<?php
include_spip('base/abstract_sql');
function formulaires_clevermail_multiple_charger_dist($lst_id = 0, $lsr_mode_force = false) {

	$default = array('editable' => ' ', 'lsr_mode' => 1, 'sub_email' => '', 'lst_ids' => array());
	if ($lsr_mode !== false && in_array($lsr_mode_force, array('texte', 'html'))) {
		$default['lsr_mode_force'] = $lsr_mode_force;
	}
	if (intval($lst_id) != 0) {
		if ($lst_id = sql_getfetsel("lst_id", "spip_cm_lists", "lst_id = ".intval($lst_id)." AND lst_moderation != 'closed'")) {
			$valeurs = $default;
			$valeurs['lst_id'] = array($lst_id);
  		return $valeurs;
		} else {
      return array('editable' => '');
		}
	} else {
	  $nbLists = sql_countsel("spip_cm_lists", "lst_moderation != 'closed'");
	  if ($nbLists == 0) {
      return array('editable' => '');
	  } elseif ($nbLists == 1) {
	  	$lst_id = sql_getfetsel("lst_id", "spip_cm_lists", "lst_moderation != 'closed'");
      $valeurs = $default;
      $valeurs['lst_id'] = array($lst_id);
      return $valeurs;
	  } else {
	  	// editable, mais le squelette trouvera tout seul la liste de valeurs
	    return $default;
	  }
	}
}

function formulaires_clevermail_multiple_verifier_dist($lst_id = 0, $lsr_mode_force = false) {
  $erreurs = array();
  if (!_request('lst_id') && !_request('lst_ids')) {
    $erreurs['lst_ids'] = _T('clevermail:ce_champ_est_obligatoire');
  }
  if (!_request('sub_email')) {
    $erreurs['sub_email'] = _T('clevermail:ce_champ_est_obligatoire');
  }
  include_spip('inc/filtres');
  if (_request('sub_email') && !email_valide(_request('sub_email'))) {
    $erreurs['sub_email'] = _T('clevermail:cette_adresse_email_n_est_pas_valide');
  }
	if (count($erreurs)) {
    $erreurs['message_erreur'] = _T('clevermail:veuillez_corriger_votre_saisie');
  }
  return $erreurs;
}

function formulaires_clevermail_multiple_traiter_dist($lst_id = 0, $lsr_mode_force = false) {
	$ok = true;
	$message = '';
  if ($sub_id = sql_getfetsel("sub_id", "spip_cm_subscribers", "sub_email=".sql_quote(_request('sub_email')))) {
  	$sub_id = intval($sub_id);
  } else {
		// Nouvelle adresse e-mail
		$sub_id = intval(sql_insertq("spip_cm_subscribers", array('sub_email' => _request('sub_email'))));
		sql_updateq("spip_cm_subscribers", array('sub_profile' => md5($sub_id.'#'.sql_quote(_request('sub_email')).'#'.time())), "sub_id=".intval($sub_id));
  }
  if (_request('lst_id')) {
  	$lists[] = intval(_request('lst_id'));
  } elseif (_request('lst_ids')) {
  	$lists = array_map("intval", _request('lst_ids'));
  }
  if (_request('lsr_mode_force')) {
  	$lsr_mode = intval(_request('lsr_mode_force'));
  } else {
  	$lsr_mode = intval(_request('lsr_mode'));
  }

  $actionId = md5('subscribe#'.$sub_id.'#'.time());
  $nbLettre = 1;
  $lists_name = "";
  $lists_name_categorie = "";
  $lists_name_complet = "";
  foreach($lists as $list) {
  	$lst_id = intval($list);
    $listData = sql_fetsel("*", "spip_cm_lists", "lst_id=".intval($lst_id));
    if (sql_countsel("spip_cm_lists_subscribers", "lst_id=".intval($lst_id)." AND sub_id=".intval($sub_id)) == 1) {
    	if (sql_getfetsel("lsr_mode", "spip_cm_lists_subscribers", "lst_id=".intval($lst_id)." AND sub_id=".intval($sub_id)) == intval($lsr_mode)) {
    		// Déjà abonné avec ce mode
    		$message .= (strlen($message) > 0 ? '<br />' : '')._T('clevermail:inscription_deja_abonne_meme_mode', array('lst_name' => $listData['lst_name']));
    	} else {
    		// Déjà abonné mais changement de mode
        sql_updateq("spip_cm_lists_subscribers", array('lsr_mode' => intval($lsr_mode)), "lst_id=".intval($lst_id)." AND sub_id=".intval($sub_id));
    		$message .= (strlen($message) > 0 ? '<br />' : '')._T('clevermail:inscription_deja_abonne_autre_mode', array('lst_name' => $listData['lst_name']));
    	}
    } else {
    	// Nouvel abonnement
    	switch ($listData['lst_moderation']) {
    		case 'open':
    			$actionId = md5('subscribe#'.$lst_id.'#'.$sub_id.'#'.time());
          sql_insertq("spip_cm_lists_subscribers", array('lst_id' => intval($lst_id), 'sub_id' => intval($sub_id), 'lsr_mode' => intval($lsr_mode), 'lsr_id' => $actionId));
          $message .= (strlen($message) > 0 ? '<br />' : '')._T('clevermail:inscription_validee', array('lst_name' => supprimer_numero($listData['lst_name'])));
    			break;
    		case 'email':
    			// TODO : à finir
          		if (sql_countsel("spip_cm_pending", "lst_id=".intval($lst_id)." AND sub_id=".intval($sub_id)) == 0) {
          			sql_insertq("spip_cm_pending", array('lst_id' => intval($lst_id), 'sub_id' => intval($sub_id), 'pnd_action' => 'subscribe', 'pnd_mode' => intval($lsr_mode), 'pnd_action_date' => time(), 'pnd_action_id' => $actionId));
          		} else {
          			sql_updateq("spip_cm_pending", array('pnd_action' => 'subscribe', 'pnd_mode' => intval($lsr_mode), 'pnd_action_date' => time(), 'pnd_action_id' => $actionId), "sub_id=".intval($sub_id)." AND lst_id=".intval($lst_id));
          		}
              if (strpos($listData['lst_name'], '/') === false) {
              	$lettre = supprimer_numero($listData['lst_name']);
              	$categorie = '';
              } else {
              	$lettre = supprimer_numero(substr($listData['lst_name'], strpos($listData['lst_name'], '/') + 1));
              	$categorie = supprimer_numero(substr($listData['lst_name'], 0, strpos($listData['lst_name'], '/')));
              }
          		$lists_name = $lists_name.'- '.$lettre."\n\n";
          		$lists_name_categorie = $lists_name_categorie.'- '.$categorie."\n\n";
          		$lists_name_complet = $lists_name_complet.'- '.$categorie.' / '.$lettre."\n\n";
          		$msgInscription = '';
				if($nbLettre <= count($lists)){
          			if(count($lists) > 1){
          				//Si inscription a plusieurs lettres, on envoie un seul mail avec la liste des lettres
						// Composition du message de demande de confirmation
		          		$template = array();
		          		$template['@@NOM_LETTRE@@'] = $lists_name;
		          		$template['@@NOM_CATEGORIE@@'] = $lists_name_categorie;
		          		$template['@@NOM_COMPLET@@'] = $lists_name_complet;
		          		$template['@@DESCRIPTION@@'] = $listData['lst_comment'];
		          		$template['@@FORMAT_INSCRIPTION@@']  = (intval($lsr_mode) == 1 ? _T('clevermail:choix_version_html') : _T('clevermail:choix_version_texte'));
		          		$template['@@EMAIL@@'] = _request('sub_email');
		          		$template['@@URL_CONFIRMATION@@'] = url_absolue(generer_url_public(_CLEVERMAIL_VALIDATION,'id='.$actionId));
		          		$body = sql_getfetsel("set_value", "spip_cm_settings", "set_name='CM_MAIL_TEXT'");
		          		$subject = sql_getfetsel("set_value", "spip_cm_settings", "set_name='CM_MAIL_SUBJECT'");
		          		$msgInscription = _T('clevermail:inscription_ok_multiple', array('lst_name' => $lists_name_complet));
          			} else {
          				// Composition du message de demande de confirmation
		          		$template = array();
                  if (strpos($listData['lst_name'], '/') === false) {
                  	$template['@@NOM_LETTRE@@'] = supprimer_numero($listData['lst_name']);
                  	$template['@@NOM_CATEGORIE@@'] = '';
                  	$template['@@NOM_COMPLET@@'] = $template['@@NOM_LETTRE@@'];
                  } else {
                    $template['@@NOM_LETTRE@@'] = supprimer_numero(substr($listData['lst_name'], strpos($listData['lst_name'], '/') + 1));
                    $template['@@NOM_CATEGORIE@@'] = supprimer_numero(substr($listData['lst_name'], 0, strpos($listData['lst_name'], '/')));
                  	$template['@@NOM_COMPLET@@'] = $template['@@NOM_CATEGORIE@@'].' / '.$template['@@NOM_LETTRE@@'];
                  }
		          		$template['@@DESCRIPTION@@'] = $listData['lst_comment'];
		          		$template['@@FORMAT_INSCRIPTION@@']  = (intval($lsr_mode) == 1 ? _T('clevermail:choix_version_html') : _T('clevermail:choix_version_texte'));
		          		$template['@@EMAIL@@'] = _request('sub_email');
		          		$template['@@URL_CONFIRMATION@@'] = url_absolue(generer_url_public(_CLEVERMAIL_VALIDATION,'id='.$actionId));
		          		$body = $listData['lst_subscribe_text'];
		          		$subject = (intval($listData['lst_subject_tag']) == 1 ? '['.$template['@@NOM_COMPLET@@'].'] ' : '').$listData['lst_subscribe_subject'];
		          		$msgInscription = _T('clevermail:inscription_ok', array('lst_name' => $template['@@NOM_COMPLET@@']));
          			}
					if($nbLettre == count($lists)){
						while (list($translateFrom, $translateTo) = each($template)) {
		            		$body = str_replace($translateFrom, $translateTo, $body);
		          		}
		          		$to = _request('sub_email');
		          		$from = sql_getfetsel("set_value", "spip_cm_settings", "set_name='CM_MAIL_FROM'");
		          		$return = sql_getfetsel("set_value", "spip_cm_settings", "set_name='CM_MAIL_RETURN'");
						// message removed from queue, we can try to send it
		          		// TODO : Et le charset ?
		          		// TODO : Et le return-path ?
		          		$envoyer_mail = charger_fonction('envoyer_mail', 'inc');
		          		if ($envoyer_mail($to, $subject, $body, $from)) {
		            		$message .= (strlen($message) > 0 ? '<br />' : '').$msgInscription;
		          		} else {
		            		$message .= (strlen($message) > 0 ? '<br />' : '')._T('clevermail:send_error', array('lst_name' => $listData['lst_name']));
		          		}
					}
					$nbLettre++;
          		}
    			break;
    		case 'mod':
          // TODO : à faire
    			break;
        case 'closed':
          $message .= (strlen($message) > 0 ? '<br />' : '')._T('clevermail:inscription_nok', array('lst_name' => $listData['lst_name']));
          $ok = false;
        break;
    	}
    }
  }

	return array('message_ok' => $message, 'editable' => '');
}
?>
