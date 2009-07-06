<?php
function balise_CLEVERMAIL_VALIDATION($p) {
	return calculer_balise_dynamique($p, 'CLEVERMAIL_VALIDATION', array());
}

function balise_CLEVERMAIL_VALIDATION_dyn() {
	if (isset($_GET['id']) && $_GET['id'] != '') {
		if (sql_countsel("spip_cm_pending", "pnd_action_id=".sql_quote($_GET['id'])) == 1) {
  		$action = sql_fetsel("*", "spip_cm_pending", "pnd_action_id=".sql_quote($_GET['id']));
      switch ($action['pnd_action']) {
        case 'subscribe':
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
            $body = 'Alerte envoyée par le plugin CleverMail du site '.$GLOBALS['meta']['nom_site'].' ( '.$GLOBALS['meta']['adresse_site'].' ) :'."\n\n";
            $body .= 'Inscription de '.addslashes($sub['sub_email']).' à la liste '.addslashes($list['lst_name']);
            $from = sql_getfetsel("set_value", "spip_cm_settings", "set_name='CM_MAIL_FROM'");
            $envoyer_mail = charger_fonction('envoyer_mail', 'inc');
            $envoyer_mail($to, $subject, $body, $from);
          }
          break;
	      case 'unsubscribe':
          if (sql_countsel("spip_cm_lists_subscribers", "lst_id = ".intval($action['lst_id'])." AND sub_id = ".intval($action['sub_id'])) == 0) {
            $return = '<p>'._T('clevermail:deja_desinscrit').'</p>';
          } else {
          	// remove the subscription to the list
            sql_delete("spip_cm_lists_subscribers", "lst_id = ".intval($action['lst_id'])." AND sub_id = ".intval($action['sub_id']));
            // remove posts from this list already queued
            sql_delete("spip_cm_posts_queued", "sub_id = ".intval($action['sub_id'])." AND pst_id IN (".implode(',', sql_fetsel("lst_id", "spip_cm_posts", "lst_id=".intval($action['lst_id']), "lst_id")).")");
            
            $return = '<p>'._T('clevermail:desinscription_validee').'</p>';

            // E-mail d'alerte envoye au moderateur de la liste
            $sub = sql_fetsel("*", "spip_cm_subscribers", "sub_id = ".intval($action['sub_id']));
            $list = sql_fetsel("*", "spip_cm_lists", "lst_id = ".intval($action['lst_id']));
            $to = $list['lst_moderator_email'];
            $subject = '['.addslashes($list['lst_name']).'] Désinscription de '.addslashes($sub['sub_email']);
            $body = 'Alerte envoyée par le plugin CleverMail du site '.$GLOBALS['meta']['nom_site'].' ( '.$GLOBALS['meta']['adresse_site'].' ) :'."\n\n";
            $body .= 'Désinscription de '.addslashes($sub['sub_email']).' de la liste '.addslashes($list['lst_name']);
            $from = sql_getfetsel("set_value", "spip_cm_settings", "set_name='CM_MAIL_FROM'");
            $envoyer_mail = charger_fonction('envoyer_mail', 'inc');
            $envoyer_mail($to, $subject, $body, $from);
          }
	      	
		      $abonnement = sql_fetsel("sub_id, lst_id", "spip_cm_lists_subscribers", "lsr_id=".sql_quote($lsr_id));
		      $abonne = sql_getfetsel("sub_email", "spip_cm_subscribers", "sub_id=".intval($abonnement['sub_id']));
		      $liste = sql_getfetsel("lst_name", "spip_cm_lists", "lst_id=".intval($abonnement['lst_id']));
		      if (sql_countsel("spip_cm_lists_subscribers", "sub_id=".intval($abonnement['sub_id'])) == 0) {
		        // No more subscription, subscriber address is removed
		        sql_delete("spip_cm_subscribers", "sub_id = ".intval($abonnement['sub_id']));
		      }
		      spip_log('Suppression du l\'abonnement de « '.$abonne.' » à la liste « '.$liste.' » (id='.$abonnement['lst_id'].')', 'clevermail');
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