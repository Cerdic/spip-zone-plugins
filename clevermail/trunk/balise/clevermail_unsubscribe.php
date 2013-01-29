<?php
include_spip('inc/filtres');
function balise_CLEVERMAIL_UNSUBSCRIBE($p) {
	return calculer_balise_dynamique($p, 'CLEVERMAIL_UNSUBSCRIBE', array());
}

function balise_CLEVERMAIL_UNSUBSCRIBE_dyn() {
	if (isset($_GET['id']) && $_GET['id'] != '') {
	    if (sql_countsel("spip_cm_lists_subscribers", "lsr_id=".sql_quote($_GET['id'])) == 1) {
	    	$abonnement = sql_fetsel("*", "spip_cm_lists_subscribers", "lsr_id=".sql_quote($_GET['id']));

		  // Desinscription a cette liste demandee
		  $actionId = md5('unsubscribe#'.intval($abonnement['lst_id']).'#'.intval($abonnement['sub_id']).'#'.time());
		  if (sql_countsel("spip_cm_pending", "lst_id = ".intval($abonnement['lst_id'])." AND sub_id = ".intval($abonnement['sub_id'])) == 0) {
			sql_insertq("spip_cm_pending", array('lst_id' => intval($abonnement['lst_id']), 'sub_id' => $abonnement['sub_id'], 'pnd_action' => 'unsubscribe', 'pnd_action_date' => time(), 'pnd_action_id' => $actionId));
		  }

	      // Composition du message de demande de confirmation
	      $sub = sql_fetsel("*", "spip_cm_subscribers", "sub_id=".intval($abonnement['sub_id']));
	      $list = sql_fetsel("*", "spip_cm_lists", "lst_id=".intval($abonnement['lst_id']));

	      $template = array();
        if (strpos($list['lst_name'], '/') === false) {
        	$template['@@NOM_LETTRE@@'] = supprimer_numero($list['lst_name']);
        	$template['@@NOM_CATEGORIE@@'] = '';
        	$template['@@NOM_COMPLET@@'] = $template['@@NOM_LETTRE@@'];
        } else {
          $template['@@NOM_LETTRE@@'] = supprimer_numero(substr($list['lst_name'], strpos($list['lst_name'], '/') + 1));
          $template['@@NOM_CATEGORIE@@'] = supprimer_numero(substr($list['lst_name'], 0, strpos($list['lst_name'], '/')));
        	$template['@@NOM_COMPLET@@'] = $template['@@NOM_CATEGORIE@@'].' / '.$template['@@NOM_LETTRE@@'];
        }
	      $template['@@EMAIL@@'] = $sub['sub_email'];
	      $template['@@FORMAT_INSCRIPTION@@']  = ($data['lsr_mode'] == 1 ? 'HTML' : 'texte');
	      //$template['@@URL_CONFIRMATION@@'] = $GLOBALS['meta']['adresse_site'].'/spip.php?page=clevermail_do&id='.$actionId;
	      $template['@@URL_CONFIRMATION@@'] = url_absolue(generer_url_public(_CLEVERMAIL_VALIDATION,'id='.$actionId));

	      $to = $sub['sub_email'];
	      $subject = (intval($list['lst_subject_tag']) == 1 ? '['.$template['@@NOM_COMPLET@@'].'] ' : '').html_entity_decode($list['lst_unsubscribe_subject'], ENT_QUOTES,'UTF-8');
	      $body = $list['lst_unsubscribe_text'];
	      while (list($translateFrom, $translateTo) = each($template)) {
	        $body = str_replace($translateFrom, $translateTo, $body);
	      }
	      $from = sql_getfetsel("set_value", "spip_cm_settings", "set_name='CM_MAIL_FROM'");
	      $return = sql_getfetsel("set_value", "spip_cm_settings", "set_name='CM_MAIL_RETURN'");

	      // TODO : Et le charset ?
	      // TODO : Et le return-path ?
	      $envoyer_mail = charger_fonction('envoyer_mail', 'inc');
	      $envoyer_mail($to, $subject, $body, $from);

				$return = '<p>'._T('clevermail:desinscription_confirmation_debut').' '.$template['@@NOM_COMPLET@@'].' '._T('clevermail:desinscription_confirmation_fin').'</p>';
		} else {
		    $return = '<p>'._T('clevermail:aucune_inscription').'</p>';
	    }
	}
	return $return;
}
?>
