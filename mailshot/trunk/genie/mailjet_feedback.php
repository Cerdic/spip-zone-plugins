<?php
/**
 * Plugin MailShot
 * (c) 2012 Cedric Morin
 * Licence GNU/GPL
 */

if (!defined('_ECRIRE_INC_VERSION')) return;


function genie_mailjet_feedback_dist($t){
	include_spip('inc/mailshot');
	include_spip('inc/config');
	include_spip('bulkmailer/mailjet');

	$mj = mailjet_api();
	$feedback = charger_fonction("feedback","newsletter");

	// toutes les campagnes envoyees depuis moins de 10jours (au dela on poll plus les stats)
	$campagnes = sql_allfetsel("*","spip_mailshots", sql_in('statut',array('processing','end')) . " AND date_start>".sql_quote(date('Y-m-d H:i:s',strtotime("-10 day"))),'','date_start','0,10');
	foreach($campagnes as $campagne){
		#var_dump($campagne['id_mailshot']);

		// recuperer l'id campagne mailjet
		$tracking_id = "mailshot".$campagne['id_mailshot'];
		$id_mailjet = mailjet_id_from_custom_campaign($tracking_id);

		// recuperer les messages de cette campagne
		// on recupere par lots de 1000
		$start = 0;
		$lot = 1000;
		while($start<$campagne['total']){
			$response = $mj->reportEmailSent(array('campaign_id'=>$id_mailjet,'limit'=>$lot,'start'=>$start));

			// nombre de messages total pour la campagne
			$nb_messages = $response->cnt;
			// nombre de messages dans ce lot
			$nb = count($response->emails);

			// traiter les emails
			if ($nb){
				foreach($response->emails as $email){
					if ($row = sql_fetsel("*","spip_mailshots_destinataires","id_mailshot=".intval($campagne['id_mailshot'])." AND email=".sql_quote($email->to_email))){
						$event = "";
						if ($email->spam AND $row['statut']!=='spam'){
							$event = 'spam';
						}
						elseif ($email->blocked AND $row['statut']!=='fail'){
							$event = 'reject';
						}
						elseif ($email->bounce AND $row['statut']!=='fail'){
							$event = 'hard_bounce';
						}
						elseif ($email->click AND $row['statut']!=='clic'){
							$event = 'clic';
						}
						elseif ($email->open AND $row['statut']!=='read'){
							$event = 'read';
						}
						if ($event) {
							#var_dump("$event : ".$email->to_email);
							$feedback($event,$email->to_email,$tracking_id,true);
						}
					}
				}
			}

			$start += $lot;
		}

	}

	return 0;
}
