<?php
	/**
	 *
	 * CleverMail : plugin de gestion de lettres d'information basé sur CleverMail
	 * Author : Thomas Beaumanoir
	 * Clever Age <http://www.clever-age.com>
	 * Copyright (c) 2006 - Distribue sous licence GNU/GPL
	 *
	 **/

if (!defined("_ECRIRE_INC_VERSION")) return;	#securite

include_spip('phpmailer/class.phpmailer');

function balise_FORMULAIRE_CLEVERMAIL ($p) {
	return calculer_balise_dynamique($p, 'FORMULAIRE_CLEVERMAIL', array('id_liste'));
}

// args[0] indique une liste, mais ne sert pas encore
// args[1] indique un eventuel squelette alternatif
// #FORMULAIRE_CLEVERMAIL{lettreX} permet d'afficher le formulaire d'abonnement a la lettre numero X
function balise_FORMULAIRE_CLEVERMAIL_stat($args, $filtres) {
	if(!$args[1]) $args[1]='formulaire_clevermail';
	if(ereg("^lettre([0-9]+)$", $args[1], $regs)) {
		$args[0] = intval($regs[1]);
		$args[1] = 'formulaire_clevermail_simple';
	}
	$nb_listes_actives = spip_abstract_fetch(spip_query("SELECT count(*) as n FROM cm_lists WHERE lst_moderation!='closed';"));
	if ($nb_listes_actives['n'] == 0) {
		return '';
	} else {
		return array($args[0], $args[1]);
	}
}

function balise_FORMULAIRE_CLEVERMAIL_dyn($id_liste, $formulaire) {
	$formulaire = "formulaires/".$formulaire ;

	if($_POST['cm_sub_return']) {
		$listId = (int)$_POST['cm_sub_list'];
		$address = $_POST['cm_sub_address'];
		$mode = (int)$_POST['cm_sub_mode'];
		$return = $_POST['cm_sub_return'];
		$erreur = '';
		$cm_sub = 'null';
		if (ereg("^[^@ ]+@[^@ ]+\.[^@. ]+$", $address)) {
			$result = spip_fetch_array(spip_query("SELECT sub_id FROM cm_subscribers WHERE sub_email='"._q($address)."'"));
			$recId = $result['sub_id'];
			if (!$recId) {
				// Nouvelle adresse e-mail
				spip_query("INSERT INTO cm_subscribers (sub_id, sub_email, sub_profile) VALUES ('', '"._q($address)."', '')");
				$recId = spip_insert_id();
				spip_query("UPDATE cm_subscribers SET sub_profile = '".md5($recId.'#'._q($address).'#'.time())."' WHERE sub_id='"._q($recId)."'");
			}
			$result = spip_fetch_array(spip_query("SELECT COUNT(*) AS nb FROM cm_lists_subscribers WHERE lst_id = "._q($listId)." AND sub_id = "._q($recId)));
			if ($result['nb'] == 1) {
				// Inscription à cette liste déjà présente
				// On met à jour pour éventuellement changer le mode
				spip_query("UPDATE cm_lists_subscribers SET lsr_mode="._q($mode)." WHERE lst_id = "._q($listId)." AND sub_id = "._q($recId));
				$cm_sub = _T('clevermail:deja_inscrit');
			} else {
				$list = spip_fetch_array(spip_query("SELECT * FROM cm_lists WHERE lst_id = "._q($listId)));
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
							spip_query("INSERT INTO cm_pending (lst_id, sub_id, pnd_action, pnd_mode, pnd_action_date, pnd_action_id) VALUES ("._q($listId).", "._q($recId).", 'subscribe', "._q($mode).", ".time().", '"._q($actionId)."')");
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
							spip_query("INSERT INTO cm_pending (lst_id, sub_id, pnd_action, pnd_mode, pnd_action_date, pnd_action_id) VALUES ("._q($listId).", "._q($recId).", 'subscribe', "._q($mode).", ".time().", '"._q($actionId)."')");
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

					case 'closed':
						$cm_sub = _T('clevermail:nok');
					break;
				}
			}
		} else {
			// Email non valide
			$cm_sub = _T('clevermail:email_non_valide');
		}
	}

	return array($formulaire, $GLOBALS['delais'], array('id_liste' => $id_liste, 'retour' => $cm_sub));
}
?>