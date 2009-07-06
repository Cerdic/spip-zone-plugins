<?php
function balise_CLEVERMAIL_VALIDATION($p) {
	return calculer_balise_dynamique($p, 'CLEVERMAIL_VALIDATION', array());
}

function balise_CLEVERMAIL_VALIDATION_dyn() {
	if (isset($_GET['id']) && $_GET['id'] != '') {
		if (sql_countsel("spip_cm_pending", "pnd_action_id=".sql_quote($_GET['id'])) == 1) {
  		$action = sql_fetsel("*", "spip_cm_pending", "pnd_action_id=".sql_quote($_GET['id']));
      switch ($action['pnd_action']) {
        case 'subscribe' :
          if (sql_countsel("spip_cm_lists_subscribers", "lst_id = ".intval($action['lst_id'])." AND sub_id = ".intval($action['sub_id'])) == 1) {
            sql_updateq("spip_cm_lists_subscribers", array('lsr_mode' => $action['pnd_mode'], 'lsr_id' => $action['pnd_action_id']), "lst_id = ".$action['lst_id']." AND sub_id = ".$action['sub_id']);
            $return = '<p>'._T('clevermail:deja_inscrit').'</p>';
          } else {
            sql_insertq("spip_cm_lists_subscribers", array('lst_id' => $action['lst_id'], 'sub_id' => $action['sub_id'], 'lsr_mode' => $action['pnd_mode'], 'lsr_id' => $action['pnd_action_id']));
            $return = '<p>'._T('clevermail:inscription_validee').'</p>';

            // E-mail d'alerte envoye au moderateur de la liste
            $sub = sql_fetsel("*", "spip_cm_subscribers", "sub_id = ".intval($action['sub_id']));
            $list = sql_fetsel("*", "spip_cm_lists", "lst_id = ".intval($action['lst_id']));
            $to = $list['lst_moderator_email'];
            $subject = '['.addslashes($list['lst_name']).'] Inscription de '.addslashes($sub['sub_email']);
            $body = 'Alerte envoyée par le plugin CleverMail du site '.$GLOBALS['meta']['nom_site'].' ( '.$GLOBALS['meta']['adresse_site'].' ) :'."\n\n".'Inscription de '.addslashes($sub['sub_email']).' à la liste '.addslashes($list['lst_name']);
            $from = sql_getfetsel("set_value", "spip_cm_settings", "set_name='CM_MAIL_FROM'");
            $envoyer_mail = charger_fonction('envoyer_mail', 'inc');
            $envoyer_mail($to, $subject, $body, $from);
          }
	        break;
	      case 'unsubscribe' :
	      	/*
		    	spip_query("DELETE FROM cm_pending WHERE sub_id = ".$action['sub_id']);
		     	spip_query("DELETE FROM cm_posts_queued WHERE sub_id = ".$action['sub_id']);
	        spip_query("DELETE FROM cm_lists_subscribers WHERE lst_id = ".$action['lst_id']." AND sub_id = ".$action['sub_id']);
	        $return = '<p>'._T('clevermail:desinscription_validee').'</p>';
          $sub = spip_fetch_array(spip_query("SELECT * FROM cm_subscribers WHERE sub_id = ".$action['sub_id']));
          $list = spip_fetch_array(spip_query("SELECT * FROM cm_lists WHERE lst_id = ".$action['lst_id']));
          $cm_mail_admin = spip_fetch_array(spip_query("SELECT set_value FROM cm_settings WHERE set_name='CM_MAIL_ADMIN'"));
          $cm_mail_from = spip_fetch_array(spip_query("SELECT set_value FROM cm_settings WHERE set_name='CM_MAIL_FROM'"));
      		// E-mail d'alerte envoye au moderateur de la liste s'il est renseigne sinon a l'administrateur de CleverMail
          $mail = new PHPMailer();
					$mail->Subject = '['.addslashes($list['lst_name']).'] D&eacute;sinscription de '.addslashes($sub['sub_email']);
					$mail->From = $cm_mail_from['set_value'];
					$mail->FromName = $GLOBALS['meta']['nom_site'];
					$mail->AddAddress(($list['lst_moderator_email'] != '' ? $list['lst_moderator_email'] : $cm_mail_admin['set_value']));
					$mail->Send();
          */
          break;
      }
      sql_delete("spip_cm_pending", "pnd_action_id=".sql_quote($_GET['id']));
	  } else {
	    $return = '<p>'._T('clevermail:deja_validee').'</p>';
    }
  }
	return $return;
}
?>