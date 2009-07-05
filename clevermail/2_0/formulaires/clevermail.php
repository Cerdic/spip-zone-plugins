<?php
function formulaires_clevermail_charger_dist($lst_id = 0) {
	$default = array('editable' => ' ', 'lsr_mode' => 1, 'sub_email' => '', 'lst_ids' => array());
	if (intval($lst_id) != 0) {
		if ($lst_id = sql_getfetsel("lst_id", "spip_cm_lists", "lst_id=".intval($lst_id)." AND lst_moderation!='closed'")) {
			$valeurs = $default;
			$valeurs['lst_id'] = array($lst_id);
  		return $valeurs;
		} else {
      return array('editable' => '');
		}
	} else {
	  $nbLists = sql_countsel("spip_cm_lists", "lst_moderation!='closed'");
	  if ($nbLists == 0) {
      return array('editable' => '');
	  } elseif ($nbLists == 1) {
	  	$lst_id = sql_getfetsel("lst_id", "spip_cm_lists", "lst_moderation!='closed'");
      $valeurs = $default;
      $valeurs['lst_id'] = array($lst_id);
      return $valeurs;
	  } else {
	  	// editable, mais le squelette trouvera tout seul la liste de valeurs
	    return $default;
	  }
	}
}

function formulaires_clevermail_verifier_dist($lst_id = 0) {
  $erreurs = array();
  if (!_request('lst_id') && !_request('lst_ids')) {
    $erreurs['lst_ids'] = 'Ce champ est obligatoire.';
  }
  if (!_request('sub_email')) {
    $erreurs['sub_email'] = 'Ce champ est obligatoire.';
  }
  include_spip('inc/filtres');
  if (_request('sub_email') && !email_valide(_request('sub_email'))) {
    $erreurs['sub_email'] = 'Cette adresse e-mail n\'est pas valide.';
  }
	if (count($erreurs)) {
    $erreurs['message_erreur'] = 'Veuillez corriger votre saisie.';
  }
  return $erreurs;
}

function formulaires_clevermail_traiter_dist($lst_id = 0) {
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
  foreach($lists as $list) {
  	$lst_id = intval($list);
    $listData = sql_fetsel("*", "spip_cm_lists", "lst_id=".intval($lst_id));
    if (sql_countsel("spip_cm_lists_subscribers", "lst_id=".intval($lst_id)." AND sub_id=".intval($sub_id)) == 1) {
    	if (sql_getfetsel("lsr_mode", "spip_cm_lists_subscribers", "lst_id=".intval($lst_id)." AND sub_id=".intval($sub_id)) == intval(_request('lsr_mode'))) {
    		// Déjà abonné avec ce mode
    		$message .= (strlen($message) > 0 ? '<br />' : '')._T('clevermail:inscription_deja_abonne_meme_mode').$listData['lst_name']; 
    	} else {
    		// Déjà abonné mais changement de mode
        sql_updateq("spip_cm_lists_subscribers", array('lsr_mode' => intval(_request('lsr_mode'))), "lst_id=".intval($lst_id)." AND sub_id=".intval($sub_id));
    		$message .= (strlen($message) > 0 ? '<br />' : '')._T('clevermail:inscription_deja_abonne_autre_mode').$listData['lst_name']; 
    	}
    } else {
    	// Nouvel abonnement
    	switch ($listData['lst_moderation']) {
    		case 'open':
    			$actionId = md5('subscribe#'.$listId.'#'.$recId.'#'.time());
          sql_insertq("spip_cm_lists_subscribers", array('lst_id' => intval($lst_id), 'sub_id' => intval($sub_id), 'lsr_mode' => intval(_request('lsr_mode')), 'lsr_id' => $actionId));
          $message .= (strlen($message) > 0 ? '<br />' : '')._T('clevermail:inscription_validee').$listData['lst_name'];
    			break;
    		case 'email':
    			// TODO : à faire
    			$message .= (strlen($message) > 0 ? '<br />' : '')._T('clevermail:ok');
    			break;
    		case 'mod':
          // TODO : à faire
    			break;
        case 'closed':
          $message .= (strlen($message) > 0 ? '<br />' : '')._T('clevermail:inscription_nok').$listData['lst_name'];
          $ok = false;
        break;
    	}
    }
  }
	
	return array('message_ok' => $message, 'editable' => '');
}

/*
	if($_POST['cm_sub_return']) {
				switch($list['lst_moderation']) {
					case 'open':
						$actionId = md5('subscribe#'.$listId.'#'.$recId.'#'.time());
						spip_query("INSERT INTO cm_lists_subscribers (lst_id, sub_id, lsr_mode, lsr_id) VALUES ("._q($listId).", "._q($recId).", "._q($mode).", '$actionId')");
						$cm_sub = _T('clevermail:inscription_validee');
					break;

					case 'email':
						$actionId = md5('subscribe#'.$listId.'#'.$recId.'#'.time());
						$result = spip_fetch_array(spip_query("SELECT COUNT(*) AS nb FROM cm_pending WHERE lst_id = "._q($listId)." AND sub_id = "._q($recId)));
						if ($result['nb'] == 0) {
							spip_query("INSERT INTO cm_pending (lst_id, sub_id, pnd_action, pnd_mode, pnd_action_date, pnd_action_id) VALUES ("._q($listId).", "._q($recId).", 'subscribe', "._q($mode).", ".time().", "._q($actionId).")");
						}

						// Composition du message de demande de confirmation
						$list = spip_fetch_array(spip_query("SELECT * FROM cm_lists WHERE lst_id="._q($listId)));
						$subject = ((int)$list['lst_subject_tag'] == 1 ? '['.$list['lst_name'].'] ' : '').$list['lst_subscribe_subject'];
						$template = array();
						$template['@@NOM_LETTRE@@'] = $list['lst_name'];
						$template['@@DESCRIPTION@@'] = $list['lst_comment'];
						$template['@@FORMAT_INSCRIPTION@@']  = ($mode == 1 ? 'HTML' : 'texte');
						$template['@@EMAIL@@'] = $address;
						$template['@@URL_CONFIRMATION@@'] = $GLOBALS['meta']['adresse_site'].'/spip.php?page=clevermail_do&id='.$actionId;
						$message = $list['lst_subscribe_text'];
						while (list($from, $to) = each($template)) {
							$message = str_replace($from, $to, $message);
						}

						$mail = new PHPMailer();
						$mail->Subject = $subject;
						$cm_mail_from = spip_fetch_array(spip_query("SELECT set_value FROM cm_settings WHERE set_name='CM_MAIL_FROM'"));
						$mail->From = $cm_mail_from['set_value'];
						$mail->FromName = $GLOBALS['meta']['nom_site'];
						$mail->AddAddress($address);
						$mail->CharSet = $GLOBALS['meta']['charset'];
						$mail->IsHTML(false);
						$mail->Body = $message;

						 // Envoi du message
						if($mail->Send()) {
							$cm_sub = _T('clevermail:ok');
						} else {
							$cm_sub = _T('clevermail:send_error');
						}
					break;

					case 'mod':
						$actionId = md5('subscribe#'.$listId.'#'.$recId.'#'.time());
						$result = spip_fetch_array(spip_query("SELECT COUNT(*) AS nb FROM cm_pending WHERE lst_id = "._q($listId)." AND sub_id = "._q($recId)));
						if ($result['nb'] == 0) {
							spip_query("INSERT INTO cm_pending (lst_id, sub_id, pnd_action, pnd_mode, pnd_action_date, pnd_action_id) VALUES ("._q($listId).", "._q($recId).", 'subscribe', "._q($mode).", ".time().", "._q($actionId).")");
						}

						// Composition du message de demande de confirmation au moderateur
						$list = spip_fetch_array(spip_query("SELECT * FROM cm_lists WHERE lst_id="._q($listId)));
						$subject = ((int)$list['lst_subject_tag'] == 1 ? '['.$list['lst_name'].'] ' : '').$list['lst_subscribe_subject'];
						$template = array();
						$template['@@NOM_LETTRE@@'] = $list['lst_name'];
						$template['@@DESCRIPTION@@'] = $list['lst_comment'];
						$template['@@FORMAT_INSCRIPTION@@']  = ($mode == 1 ? 'HTML' : 'texte');
						$template['@@EMAIL@@'] = $address;
						$template['@@URL_CONFIRMATION@@'] = $GLOBALS['meta']['adresse_site'].'/spip.php?page=clevermail_do&id='.$actionId;
						$message = $list['lst_subscribe_text'];
						while (list($from, $to) = each($template)) {
							$message = str_replace($from, $to, $message);
						}

						$mail = new PHPMailer();
						$mail->Subject = $subject;
						$mail->From = $address;
						$cm_mail_from = spip_fetch_array(spip_query("SELECT set_value FROM cm_settings WHERE set_name='CM_MAIL_FROM'"));
						$mail->AddAddress($cm_mail_from);
						$mail->CharSet = $GLOBALS['meta']['charset'];
						$mail->IsHTML(false);
						$mail->Body = $message;

						 // Envoi du message
						if($mail->Send()) {
							$cm_sub = _T('clevermail:mok');
						} else {
							$cm_sub = _T('clevermail:send_error');
						}
					break;

				}
		}
	}
*/
?>