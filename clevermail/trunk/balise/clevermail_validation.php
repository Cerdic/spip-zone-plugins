<?php
function balise_CLEVERMAIL_VALIDATION($p) {
	return calculer_balise_dynamique($p, 'CLEVERMAIL_VALIDATION', array());
}

function balise_CLEVERMAIL_VALIDATION_dyn() {
  $return = "";
  if (isset($_GET['id']) && $_GET['id'] != '') {
		if (sql_countsel("spip_cm_pending", "pnd_action_id=".sql_quote($_GET['id'])) >= 1) {
			$actions = sql_select("*","spip_cm_pending", "pnd_action_id=".sql_quote($_GET['id']));
			while ($action = sql_fetch($actions)){
				//$action = sql_fetsel("*", "spip_cm_pending", "pnd_action_id=".sql_quote($_GET['id']));
				$list = sql_fetsel("*", "spip_cm_lists", "lst_id = ".intval($action['lst_id']));
	            $pos = strpos($list['lst_name'], "/");
        if (strpos($list['lst_name'], '/') === false) {
        	$lettre = supprimer_numero($list['lst_name']);
        	$categorie = '';
					$lists_name_complet = $lettre;
        } else {
        	$lettre = supprimer_numero(substr($list['lst_name'], strpos($list['lst_name'], '/') + 1));
        	$categorie = supprimer_numero(substr($list['lst_name'], 0, strpos($list['lst_name'], '/')));
					$lists_name_complet = $categorie." / ".$lettre;
        }
				switch ($action['pnd_action']) {
	        case 'subscribe':
	          if (sql_countsel("spip_cm_lists_subscribers", "lst_id = ".intval($action['lst_id'])." AND sub_id = ".intval($action['sub_id'])) == 1) {
	            sql_updateq("spip_cm_lists_subscribers", array('lsr_mode' => $action['pnd_mode'], 'lsr_id' => md5('subscribe#'.intval($action['lst_id']).'#'.intval($action['sub_id']).'#'.time())), "lst_id = ".$action['lst_id']." AND sub_id = ".$action['sub_id']);
	            $return = $return.'<p>'._T('clevermail:deja_inscrit', array('lst_name' => $lists_name_complet)).'</p>';
	          } else {
	            $sub = sql_fetsel("*", "spip_cm_subscribers", "sub_id = ".intval($action['sub_id']));
	            sql_insertq("spip_cm_lists_subscribers", array('lst_id' => $action['lst_id'], 'sub_id' => $action['sub_id'], 'lsr_mode' => $action['pnd_mode'], 'lsr_id' => md5('subscribe#'.intval($action['lst_id']).'#'.intval($action['sub_id']).'#'.time())));
	            $return = $return.'<p>'._T('clevermail:inscription_validee', array('lst_name' => $lists_name_complet)).'</p>';

	            // E-mail d'alerte envoye au moderateur de la liste
	            $destinataire = $list['lst_moderator_email'];
	            $sujet = '['.$list['lst_name'].'] '._T('clevermail:mail_info_inscription_sujet', array('sub_email' => addslashes($sub['sub_email'])));
	            $corps = _T('clevermail:mail_info_inscription_corps', array('nom_site' => $GLOBALS['meta']['nom_site'], 'url_site' => $GLOBALS['meta']['adresse_site'], 'sub_email' => addslashes($sub['sub_email']), 'lst_name' => $list['lst_name']));
	            $expediteur = sql_getfetsel("set_value", "spip_cm_settings", "set_name='CM_MAIL_FROM'");
	            $envoyer_mail = charger_fonction('envoyer_mail', 'inc');
	            $envoyer_mail($destinataire, $sujet, $corps, $expediteur);
	          }
	          break;
      		case 'unsubscribe':
	          if (sql_countsel("spip_cm_lists_subscribers", "lst_id = ".intval($action['lst_id'])." AND sub_id = ".intval($action['sub_id'])) == 0) {
	            $return = $return.'<p>'._T('clevermail:deja_desinscrit').'</p>';
	          } else {
	          	// remove the subscription to the list
	            sql_delete("spip_cm_lists_subscribers", "lst_id = ".intval($action['lst_id'])." AND sub_id = ".intval($action['sub_id']));
	            // remove posts from this list already queued
	            sql_delete("spip_cm_posts_queued", "sub_id = ".intval($action['sub_id'])." AND pst_id IN (".implode(',', sql_fetsel("lst_id", "spip_cm_posts", "lst_id=".intval($action['lst_id']), "lst_id")).")");

	            $lst_name = sql_getfetsel("lst_name", "spip_cm_lists", "lst_id = ".intval($action['lst_id']));
	            $return = $return.'<p>'._T('clevermail:desinscription_validee',array('lst_name' => $lists_name_complet)).'</p>';
	            // E-mail d'alerte envoye au moderateur de la liste
	            $sub = sql_fetsel("*", "spip_cm_subscribers", "sub_id = ".intval($action['sub_id']));
	            $list = sql_fetsel("*", "spip_cm_lists", "lst_id = ".intval($action['lst_id']));
	            $destinataire = $list['lst_moderator_email'];
	            $sujet = '['.$list['lst_name'].'] Désinscription de '.addslashes($sub['sub_email']);
	            $corps = _T('clevermail:mail_info_desinscription_corps', array('nom_site' => $GLOBALS['meta']['nom_site'], 'url_site' => $GLOBALS['meta']['adresse_site'], 'sub_email' => addslashes($sub['sub_email']), 'lst_name' => $list['lst_name']));
	            $expediteur = sql_getfetsel("set_value", "spip_cm_settings", "set_name='CM_MAIL_FROM'");
	            $envoyer_mail = charger_fonction('envoyer_mail', 'inc');
	            $envoyer_mail($destinataire, $sujet, $corps, $expediteur);
	          }
  
  		      $abonnement = sql_fetsel("sub_id, lst_id", "spip_cm_lists_subscribers", "lsr_id=".sql_quote($lsr_id));
  		      $abonne = sql_getfetsel("sub_email", "spip_cm_subscribers", "sub_id=".intval($abonnement['sub_id']));
  		      $liste = sql_getfetsel("lst_name", "spip_cm_lists", "lst_id=".intval($abonnement['lst_id']));
  		      if (sql_countsel("spip_cm_lists_subscribers", "sub_id=".intval($abonnement['sub_id'])) == 0) {
  		        // Plus aucun abonnement, on retire l'adresse complètement
  		        //sql_delete("spip_cm_subscribers", "sub_id = ".intval($abonnement['sub_id']));
  		        sql_updateq("spip_cm_subscribers",
  		        	array('sub_email' => md5(substr($abonne,0,strpos($abonne, '@'))).substr($abonne,strpos($abonne, '@'))),
  					"sub_id = ".intval($abonnement['sub_id']));
  		      }
	      	  spip_log('Suppression du l\'abonnement de « '.$abonne.' » de la liste « '.$liste.' » (id='.$abonnement['lst_id'].')', 'clevermail');
        		break;
    		}
    		sql_delete("spip_cm_pending", "pnd_action_id=".sql_quote($_GET['id']));
			}
		} else {
	    $return = $return.'<p>'._T('clevermail:deja_validee').'</p>';
		}
  }
  return $return;
}
?>