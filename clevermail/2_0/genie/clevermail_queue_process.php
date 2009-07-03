<?php
// cf http://programmer.spip.org/Declarer-une-tache

function genie_clevermail_queue_process_dist($verbose = 'no') {
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

			$mode = ($subscription['lsr_mode'] == 1 ? 'html' : 'texte');

			// recipient
			$to = $subscriber['sub_email'];

			// subject
			$subject = trim(($list['lst_subject_tag'] == 1 ? '['.$list['lst_name'].'] ' : '').$post['pst_subject']);

			$from = sql_getfetsel("set_value", "spip_cm_settings", "set_name='CM_MAIL_FROM'");
			$return = sql_getfetsel("set_value", "spip_cm_settings", "set_name='CM_MAIL_RETURN'");

			/*
			$mail = new PHPMailer();
			$mail->Subject = $subject;
			$mail->Sender = $cm_mail_return;
			$mail->From = $cm_mail_from;
			$mail->FromName = $GLOBALS['meta']['nom_site'];
			$mail->AddAddress($to);
			$mail->AddReplyTo($cm_mail_from);
			$mail->CharSet = $GLOBALS['meta']['charset'];
      */
			
			// message content
			$text = $post['pst_text'];
			$text = str_replace("(\r\n|\n|\n)", CM_NEWLINE, $text);

			$html = $post['pst_html'];
			$html = str_replace("(\r\n|\n|\n)", CM_NEWLINE, $html);
			
			include_spip('inc/texte');

			$template = array();
			$template['@@SUJET_LETTRE@@'] = $post['pst_subject'];
			$template['@@ID_LETTRE@@'] = $message['pst_id'];
			$template['@@NOM_LETTRE@@'] = $list['lst_name'];
			$template['@@DESCRIPTION@@'] = propre($list['lst_comment']);
			$template['@@FORMAT_INSCRIPTION@@'] = $mode;
			$template['@@EMAIL@@'] = $to;

			// corrige le lien de desinscription
			$template[dirname($list['lst_url_html']).'/@@URL_DESINSCRIPTION@@'] = '@@URL_DESINSCRIPTION@@';
			$template[dirname($list['lst_url_txt']).'/@@URL_DESINSCRIPTION@@'] = '@@URL_DESINSCRIPTION@@';

			$template['@@URL_DESINSCRIPTION@@'] = $GLOBALS['meta']['adresse_site'].'/spip.php?page=clevermail_rm&id='.$subscription['lsr_id'];
			reset($template);
			while (list($templateFrom, $templateTo) = each($template)) {
				$text = str_replace($templateFrom, $templateTo, $text);
				$html = str_replace($templateFrom, $templateTo, $html);
			}

			switch ($mode) {
        case 'html':
          $body = array('html' => $html, 'texte' => '');
          break;
        case 'texte':
          $body = array('html' => '', 'texte' => $text);
          break;
        default:
          $body = array('html' => $html, 'texte' => $text);
          break;
      }
			
			if (sql_delete("spip_cm_posts_queued", "pst_id = ".intval($message['pst_id'])." AND sub_id = ".intval($message['sub_id']))) {
				// message removed from queue, we can try to send it
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
	return 0;
}
?>