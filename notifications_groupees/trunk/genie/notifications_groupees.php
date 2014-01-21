<?php
/*
 * Plugin Notifications groupees
 * (c) 2013
 * Distribue sous licence GPL
 *
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

function genie_notifications_groupees_dist($t) {

	$delai = $GLOBALS['meta']['notifications_groupees_periode'];
	if (!isset($GLOBALS['meta']['notifications_groupees_derniere']))
		ecrire_config('notifications_groupees_derniere',date('Y-m-d H:i:s',time() - (3600 * $delai)));

	$ores = date('Y-m-d H:i:s');
	$derniere = $GLOBALS['meta']['notifications_groupees_derniere'];
	$evts = lire_config('notifications_groupees_evenements');

	if (in_array('forum',$evts)) $quiquoi = groupees_forum($ores,$derniere);

	if ($quiquoi) {
		ecrire_config('notifications_groupees_derniere',$ores);
		foreach($quiquoi as $qui => $quoi) {
			$quoi['email'] = $qui;
			$page = recuperer_fond('notifications/groupees',$quoi,array('raw'=>true));
			$envoyer_mail = charger_fonction('envoyer_mail', 'inc');
			$envoyer_mail($qui,$titre,$page['texte']);
		}
	}
	else spip_log('aucune notification depuis le '.$derniere,'notifications_groupees');
}

// Forums publics		
function groupees_forum($ores,$derniere) {

	$abothread = lire_config('notifications/thread_forum');
	$aboforum = lire_config('notifications/forum');
	$abobjet = lire_config('notifications/forum_article');
	$mg = lire_config('notifications/moderateurs_forum');
	
	// messages 'publie', 'prop' et 'spam' depuis les dernieres notifications
	if ($messages = sql_select("*",'spip_forum',"(statut=".sql_quote('publie')." OR statut=".sql_quote('prop')." OR statut=".sql_quote('spam').") AND date_heure<".sql_quote($ores)." AND date_heure>=".sql_quote($derniere),"id_forum")){

		while ($message = sql_fetch($messages)) {
			
			// Ne pas notifier les posteurs de leur propre message
			$pm = $message['email_auteur'];

			// Qui recoit quoi ?
			// I. tous les visiteurs notifiables :
			// messages publies
			if (($abothread OR $aboforum) AND ($message['statut'] == 'publie')) {

				// 1. abonnes au thread
				$c = sql_select("F.email_auteur, F.notification_email, A.email, A.notifications_groupees",
					"spip_forum AS F LEFT JOIN spip_auteurs AS A ON F.id_auteur=A.id_auteur",
					"notification=1 AND F.notifications_groupees=1 AND id_thread=".intval($message['id_thread'])." AND (email_auteur != '' OR notification_email != '' OR A.email IS NOT NULL )");

				// 2. abonnes au forum lie a l'objet
				if ($abobjet){
					$c = sql_select("F.email_auteur, F.notification_email, A.email, A.notifications_groupees",
					"spip_forum AS F LEFT JOIN spip_auteurs AS A ON F.id_auteur=A.id_auteur",
					"notification=1 AND F.notifications_groupees=1 AND objet=".sql_quote($message['objet'])." AND id_objet=".intval($message['id_objet'])." AND (email_auteur != '' OR notification_email != '' OR A.email IS NOT NULL )");
				}

				while ($r = sql_fetch($c)){
					if ($r['notification_email']) $email = $r['notification_email'];
					elseif($r['email_auteur']) $email = $r['email_auteur'];
					elseif($r['email'] AND ($r['notifications_groupees'] == '1')) $email = $r['email'];
					$ids_autres = array();
					if (array_key_exists($email,$quiquoi) AND ($email!=$pm)) {
						array_push($quiquoi[$email]['id_forum'],$message['id_forum']);
						$quiquoi[$email]['id_forum'] = array_unique($quiquoi[$email]['id_forum']);
					}
					elseif (!array_key_exists($email,$quiquoi) AND ($email!=$pm)) {
						$ids_autres[] = $message['id_forum'];
						$quiquoi[$email]['id_forum'] = $ids_autres;
					}
				}
			}

			// II. les auteurs de l'objet lie au forum
			$auteurs = sql_select("auteurs.*","spip_auteurs AS auteurs JOIN spip_auteurs_liens AS lien","lien.objet=".sql_quote($message['objet'])." AND lien.id_objet=".intval($message['id_objet'])." AND auteurs.id_auteur=lien.id_auteur");

			while ($auteur = sql_fetch($auteurs)) {

					// 1. les auteurs non modérateurs :
					// messages publies
					if ($auteur['email'] AND ($auteur['email'] != $pm) AND ($auteur['notifications_groupees'] == '1') AND !autoriser('modererforum', $message['objet'], $message['id_objet'], $auteur['id_auteur']) AND ($message['statut'] == 'publie')) {
						$ids_a = array();
						if (array_key_exists($auteur['email'],$quiquoi)) {
							array_push($quiquoi[$auteur['email']]['id_forum'],$message['id_forum']);
						}
						else {
							$ids_a[] = $message['id_forum'];
							$quiquoi[$auteur['email']]['id_forum'] = $ids_a;
						}
					}

					// 2. les auteurs-moderateurs
					if ($auteur['email'] AND ($auteur['email'] != $pm) AND ($auteur['notifications_groupees'] == '1') AND autoriser('modererforum', $message['objet'], $message['id_objet'], $auteur['id_auteur'])) {

						// quel que soit le statut de moderation du forum :
						// tous les messages
						$ids_am = array();
						if (array_key_exists($auteur['email'],$quiquoi)) {
							array_push($quiquoi[$auteur['email']]['id_forum'],$message['id_forum']);
						}
						else {
							$ids_am[] = $message['id_forum'];
							$quiquoi[$auteur['email']]['id_forum'] = $ids_am;
						}

						// selon le statut de moderation du forum
//						if ($message['objet']=='article' AND $message['id_objet']) {
//							$obj = sql_fetsel("accepter_forum", "spip_articles", "id_article=".intval($message['id_objet']));
//							if ($obj) $mod =  $obj['accepter_forum'];
//						} else {
//							$mod = substr($GLOBALS['meta']["forums_publics"],0,3);
//						}
//
//						// 2.1. forums moderes a priori et sur abonnement :
//						// tous les messages
//						if ($mod != 'pos') {
//							$ids_am = array();
//							if (array_key_exists($auteur['email'],$quiquoi)) {
//								array_push($quiquoi[$auteur['email']]['id_forum'],$message['id_forum']);
//							}
//							else {
//								$ids_am[] = $message['id_forum'];
//								$quiquoi[$auteur['email']]['id_forum'] = $ids_am;
//							}
//						}
//
//						// 2.2. forums moderes a posteriori :
//						// messages de statut 'prop' ou 'spam'
//						// (les notifications de messages publies sont envoyees sans delai)
//						elseif (($mod == 'pos') AND (($message['statut'] == 'prop') OR ($message['statut'] == 'spam'))) {
//							$ids_ampos = array();
//							if (array_key_exists($auteur['email'],$quiquoi)) {
//								array_push($quiquoi[$auteur['email']]['id_forum'],$message['id_forum']);
//							}
//							else {
//								$ids_ampos[] = $message['id_forum'];
//								$quiquoi[$auteur['email']]['id_forum'] = $ids_ampos;
//							}
//						}
//						// 2.2.1. supprimer du tableau les notifications de publication par $abothread etc.
//						elseif (($mod == 'pos') AND ($message['statut'] == 'publie')) {
//							if (in_array($message['id_forum'],$quiquoi[$auteur['email']]['id_forum'])) {
//								$pub[] = $message['id_forum'];
//								$quiquoi[$auteur['email']]['id_forum'] = array_diff($quiquoi[$auteur['email']]['id_forum'],$pub);
//								if (empty($quiquoi[$auteur['email']]['id_forum'])) unset ($quiquoi[$auteur['email']]);
//							}
//						}
					}
					$quiquoi[$auteur['email']]['id_forum'] = array_unique($quiquoi[$auteur['email']]['id_forum']);
					if (empty($quiquoi[$auteur['email']]['id_forum'])) unset ($quiquoi[$auteur['email']]);
				}

			// III. les moderateurs generaux :
			// tous les messages
			if ($mg) {
				$tout[] = $message['id_forum'];
				$modos = explode(",",$mg);
				foreach($modos as $modo) if ($modo != $pm) $quiquoi[$modo]['id_forum'] = $tout;
			}
		}
	}
	return $quiquoi;
}