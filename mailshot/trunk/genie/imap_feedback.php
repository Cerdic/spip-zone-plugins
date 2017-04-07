<?php
/**
 * Plugin MailShot
 * (c) 2012 Cedric Morin
 * Licence GNU/GPL
 */

if (!defined('_ECRIRE_INC_VERSION')) return;


function genie_imap_feedback_dist($t){
	if ( isset($GLOBALS["imap_feedback_username"]) ){
            include_spip('inc/mailshot');
            include_spip('inc/config');
            $username = $GLOBALS["imap_feedback_username"];
            $password = $GLOBALS["imap_feedback_password"];
            $hostname = $GLOBALS["imap_feedback_hostname"];        

            $return_path_email = lire_config("facteur_smtp_sender");
            $feedback = charger_fonction("feedback","newsletter");
            
            $inbox = imap_open($hostname, $username, $password);
            if ($inbox){
                    spip_log("Récupération des bounce sur $username pour $return_path_email", _LOG_INFO_IMPORTANTE);
                    $emails = imap_search($inbox,'UNSEEN TO "'.$return_path_email.'"');
                    
                    // toutes les campagnes envoyees depuis moins de 10jours (au dela on poll plus les stats)
                    $campagnes = sql_allfetsel("*","spip_mailshots", sql_in('statut',array('processing','end')) . " AND date_start>".sql_quote(date('Y-m-d H:i:s',strtotime("-10 day"))),'','date_start','0,10');
                    foreach($campagnes as $campagne){
                            
                            // recuperer l'id campagne
                            $tracking_id = "mailshot".$campagne['id_mailshot'];
                            
                            // traiter les emails
                            if ($emails){
                                    foreach($emails as $email_number) {   
                                            $message_lines = explode("\n", imap_fetchbody($inbox,$email_number,2));
                                            $return_status_code = $original_recipient = "";
                                            if (is_array($message_lines) && count($message_lines)) {
                                                foreach($message_lines as $l) {
                                                    $l = trim($l);
                                                    if ( preg_match("/^Status:/i", $l) ){
                                                        $tmp = explode(":",$l);
                                                        $return_status_code = trim(str_replace('.', '', $tmp[1]));
                                                        continue;
                                                    }
                                                    else if ( preg_match("/^Original-Recipient: rfc822;/i", $l) ){
                                                        $tmp = explode(";",$l);
                                                        $original_recipient = trim($tmp[1]);
                                                        continue;
                                                    }
                                                }
                                            }
                                            if ($row = sql_fetsel("*","spip_mailshots_destinataires","id_mailshot=".intval($campagne['id_mailshot'])." AND email=".sql_quote($original_recipient))){
                                                    $event = "";
                                                    if ($return_status_code[0] == 4 AND $row['statut']!=='fail'){
                                                            $event = 'reject';
                                                    }
                                                    else if ($return_status_code[0] == 5 AND $row['statut']!=='fail'){
                                                            $event = 'hard_bounce';
                                                    }
                                                    if ($event) {
                                                            $feedback($event,$original_recipient,$tracking_id,true);
                                                    }
                                            }
                                    }
                            }
                    }
                    imap_close($inbox);
            } else {
                spip_log("Impossible de se connecter au serveur : ".imap_last_error(), _LOG_INFO_IMPORTANTE);
            }
        }
	return 0;
}
