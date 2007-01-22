<?php
	/**
	 *
	 * CleverMail : plugin de gestion de lettres d'information bas� sur CleverMail
	 * Author : Thomas Beaumanoir
	 * Clever Age <http://www.clever-age.com>
	 * Copyright (c) 2006 - Distribue sous licence GNU/GPL
	 *
	 **/

include_spip('phpmailer/class.phpmailer');

function balise_CLEVERMAIL_VALIDATION($p) {
	return calculer_balise_dynamique($p, 'CLEVERMAIL_VALIDATION', array());
}

function balise_CLEVERMAIL_VALIDATION_dyn() {
	if (isset($_GET['id']) && $_GET['id'] != '') {
		$result = spip_query("SELECT * FROM cm_pending WHERE pnd_action_id="._q($_GET['id']));
	    if (spip_num_rows($result)==1) {
	    	$action = spip_fetch_array($result);
	        switch ($action['pnd_action']) {
	            case 'subscribe' :
	            	$result = spip_fetch_array(spip_query("SELECT COUNT(*) AS nb FROM cm_lists_subscribers WHERE lst_id = ".$action['lst_id']." AND sub_id = ".$action['sub_id']));
	                if ($result['nb'] == 1) {
	                    spip_query("UPDATE cm_lists_subscribers SET lsr_mode = ".$action['pnd_mode'].", lsr_id = '".$action['pnd_action_id']."' WHERE lst_id = ".$action['lst_id']." AND sub_id = ".$action['sub_id']);
	                    $return = '<p>'._T('clevermail:deja_inscrit').'</p>';
	                } else {
	                    spip_query("INSERT INTO cm_lists_subscribers (lst_id, sub_id, lsr_mode, lsr_id) VALUES (".$action['lst_id'].", ".$action['sub_id'].", ".$action['pnd_mode'].", '".$action['pnd_action_id']."')");
	                    $return = '<p>'._T('clevermail:inscription_validee').'</p>';

	                    $sub = spip_fetch_array(spip_query("SELECT * FROM cm_subscribers WHERE sub_id = ".$action['sub_id']));
	                    $list = spip_fetch_array(spip_query("SELECT * FROM cm_lists WHERE lst_id = ".$action['lst_id']));
	                    $cm_mail_admin = spip_fetch_array(spip_query("SELECT set_value FROM cm_settings WHERE set_name='CM_MAIL_ADMIN'"));
	                    $cm_mail_from = spip_fetch_array(spip_query("SELECT set_value FROM cm_settings WHERE set_name='CM_MAIL_FROM'"));

						// E-mail d'alerte envoye au moderateur de la liste s'il est renseigne sinon a l'administrateur de CleverMail
	                    $mail = new PHPMailer();
						$mail->Subject = '['.addslashes($list['lst_name']).'] Inscription de '.addslashes($sub['sub_email']);
						$mail->From = $cm_mail_from['set_value'];
						$mail->FromName = $GLOBALS['meta']['nom_site'];
						$mail->AddAddress(($list['lst_moderator_email'] != '' ? $list['lst_moderator_email'] : $cm_mail_admin['set_value']));
						$mail->Send();
	                }
	                break;
	            case 'unsubscribe' :
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
	                break;
	        }
	        spip_query("DELETE FROM cm_pending WHERE pnd_action_id="._q($_GET['id']));
	    } else {
	        $return = '<p>'._T('clevermail:deja_validee').'</p>';
	    }
	}
	return $return;
}
?>