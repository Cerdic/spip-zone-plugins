<?php
function genie_clevermail_queue_process_dist($t, $verbose = 'no') {
	// On appelle le facteur
	$envoyer_mail = charger_fonction('envoyer_mail', 'inc');
	$cm_send_number = sql_getfetsel("set_value", "spip_cm_settings", "set_name='CM_SEND_NUMBER'");
	$queued = sql_select("*", "spip_cm_posts_queued", "", "", "psq_date", "0,".intval($cm_send_number));
	while ($message = sql_fetch($queued)) {
		$nb = sql_countsel("spip_cm_posts_done", "pst_id = ".intval($message['pst_id'])." AND sub_id = ".intval($message['sub_id']));
		if ($nb == 0) {
			$post = sql_fetsel("*", "spip_cm_posts", "pst_id = ".intval($message['pst_id']));
			$list = sql_fetsel("*", "spip_cm_lists", "lst_id = ".intval($post['lst_id']));
			$subscriber = sql_fetsel("*", "spip_cm_subscribers", "sub_id = ".intval($message['sub_id']));
			$subscription = sql_fetsel("lsr_mode, lsr_id", "spip_cm_lists_subscribers", "lst_id = ".intval($post['lst_id'])." AND sub_id = ".intval($message['sub_id']));

			// recipient
			$to = $subscriber['sub_email'];

			$from = sql_getfetsel("set_value", "spip_cm_settings", "set_name='CM_MAIL_FROM'");
			$return = sql_getfetsel("set_value", "spip_cm_settings", "set_name='CM_MAIL_RETURN'");

			// message content
			$text = $post['pst_text'];
			$text = str_replace("(\r\n|\n|\n)", CM_NEWLINE, $text);

			$html = $post['pst_html'];
			$html = str_replace("(\r\n|\n|\n)", CM_NEWLINE, $html);

			include_spip('inc/texte');

			$template = array();
			$template['@@SUJET_LETTRE@@'] = $post['pst_subject'];
			$template['@@ID_LETTRE@@'] = $message['pst_id'];
			$template['@@URL_LETTRE@@'] = url_absolue(generer_url_public(_CLEVERMAIL_LETTRE_EN_LIGNE,'id='.$message['pst_id']));
      if (strpos($list['lst_name'], '/') === false) {
      	$template['@@NOM_LETTRE@@'] = supprimer_numero($list['lst_name']);
      	$template['@@NOM_CATEGORIE@@'] = '';
      	$template['@@NOM_COMPLET@@'] = $template['@@NOM_LETTRE@@'];
      } else {
      	$template['@@NOM_LETTRE@@'] = supprimer_numero(substr($list['lst_name'], strpos($list['lst_name'], '/') + 1));
      	$template['@@NOM_CATEGORIE@@'] = supprimer_numero(substr($list['lst_name'], 0, strpos($list['lst_name'], '/')));
      	$template['@@NOM_COMPLET@@'] = $template['@@NOM_CATEGORIE@@']." / ".$template['@@NOM_LETTRE@@'];
      }
	    $template['@@DESCRIPTION@@'] = propre($list['lst_comment']);
			$template['@@FORMAT_INSCRIPTION@@'] = $mode;
			$template['@@EMAIL@@'] = $to;
			$template['@@URL_SITE@@'] = $GLOBALS['meta']['adresse_site'];

			// corrige le lien de desinscription
			$template[dirname($list['lst_url_html']).'/@@URL_DESINSCRIPTION@@'] = '@@URL_DESINSCRIPTION@@';
			$template[dirname($list['lst_url_txt']).'/@@URL_DESINSCRIPTION@@'] = '@@URL_DESINSCRIPTION@@';

			//$template['@@URL_DESINSCRIPTION@@'] = $GLOBALS['meta']['adresse_site'].'/spip.php?page=clevermail_rm&id='.$subscription['lsr_id'];
			$template['@@URL_DESINSCRIPTION@@'] = url_absolue(generer_url_public(_CLEVERMAIL_INVALIDATION,'id='.$subscription['lsr_id']));

			// subject
			$subject = trim(($list['lst_subject_tag'] == 1 ? '['.$template['@@NOM_COMPLET@@'].'] ' : '').html_entity_decode($post['pst_subject'], ENT_QUOTES,'UTF-8'));

			reset($template);
			while (list($templateFrom, $templateTo) = each($template)) {
				$text = str_replace($templateFrom, $templateTo, $text);
				$html = str_replace($templateFrom, $templateTo, $html);
			}

			switch (intval($subscription['lsr_mode'])) {
        case 0:
          $mode = 'text';
        	$body = array('html' => '', 'texte' => $text);
          break;
				case 1:
        default:
					$mode = 'html';
					$body = array('html' => $html, 'texte' => $text);
          break;
      }

			if (sql_delete("spip_cm_posts_queued", "pst_id = ".intval($message['pst_id'])." AND sub_id = ".intval($message['sub_id']))) {
				// message removed from queue, we can try to send it
				// TODO : Et le charset ?
				// TODO : Et le return-path ?
				if ($envoyer_mail($to, $subject, $body, $from)) {
					// message sent
					sql_insertq("spip_cm_posts_done", array('pst_id' => intval($message['pst_id']), 'sub_id' => intval($message['sub_id'])));
					if ($verbose == 'yes') {
						echo "Message from list \"".$list['lst_name']."\" sent to ".$to." in ".$mode." format<br />";
					} else {
						spip_log("Message from list ".$list['lst_name']." sent to ".$to." in ".$mode." format");
					}
				} else {
					if ($verbose == 'yes') {
						echo "Message could not be sent.<br />";
					} else {
  					spip_log("Message could not be sent");
					}
				}
			}
		}
	}
	return 1;
}
?>
