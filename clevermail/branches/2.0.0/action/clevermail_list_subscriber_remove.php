<?php
function action_clevermail_list_subscriber_remove_dist() {
	$securiser_action = charger_fonction('securiser_action', 'inc');
  $arg = $securiser_action();
  $lsr_id = $arg;

  if (sql_countsel("spip_cm_lists_subscribers", "lsr_id=".sql_quote($lsr_id)) == 1) {
	  include_spip('inc/autoriser');
	  if (autoriser('supprimer','cm_list_subscriber',sql_quote($lsr_id))) {
	    $abonnement = sql_fetsel("sub_id, lst_id", "spip_cm_lists_subscribers", "lsr_id=".sql_quote($lsr_id));
	  	$abonne = sql_getfetsel("sub_email", "spip_cm_subscribers", "sub_id=".intval($abonnement['sub_id']));
	  	$liste = sql_fetsel("lst_moderator_email, lst_name", "spip_cm_lists", "lst_id=".intval($abonnement['lst_id']));
	    sql_delete("spip_cm_lists_subscribers", "lsr_id = ".sql_quote($lsr_id));
	    sql_delete("spip_cm_posts_queued", "sub_id = ".intval($abonnement['sub_id']));
	    if (sql_countsel("spip_cm_lists_subscribers", "sub_id=".intval($abonnement['sub_id'])) == 0) {
	    	// No more subscription, subscriber address is removed
				sql_delete("spip_cm_pending","sub_id = ".intval($abonnement['sub_id']));
    		sql_updateq("spip_cm_subscribers", array('sub_email' => md5($abonne).'@example.com'), "sub_id = ".intval($abonnement['sub_id']));
	    }
	    $destinataire = $liste['lst_moderator_email'];
	    $sujet = '['.$liste['lst_name'].'] Désinscription de '.addslashes($abonne);
	    $corps = _T('clevermail:mail_info_desinscription_corps', array('nom_site' => $GLOBALS['meta']['nom_site'], 'url_site' => $GLOBALS['meta']['adresse_site'], 'sub_email' => addslashes($abonne), 'lst_name' => $liste['lst_name']));
	    $expediteur = sql_getfetsel("set_value", "spip_cm_settings", "set_name='CM_MAIL_FROM'");
	    $envoyer_mail = charger_fonction('envoyer_mail', 'inc');
	    if ($envoyer_mail($destinataire, $sujet, $corps, $expediteur)) {
		       spip_log('Envoie du mail OK','clevermail');
		  } else {
		       spip_log('Envoie du mail KO','clevermail');
		  }
      spip_log('Suppression de l\'abonnement de « '.$abonne.' » à la liste « '.$liste['lst_name'].' » (id='.$abonnement['lst_id'].')', 'clevermail');
	  }
  }
}
?>
