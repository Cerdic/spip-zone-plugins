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
	
        $username = "rootmebounce@gmail.com";
        $password = "d5f1ec8ac786109b5564ab4007ec078b";
        $hostname = "{imap.gmail.com:993/imap/ssl}INBOX";        

        $return_path_email = lire_config("facteur_adresse_envoi_email");
        
        $inbox = imap_open($hostname, $username, $password, OP_READONLY);
        
        if ($inbox){
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
                                                    $return_status_code = intval(str_replace('.', '', explode(":",$l)[1]));
                                                }
                                                if ( preg_match("/^Original-Recipient: rfc822;/i", $l) ){
                                                    $original_recipient = explode(";",$l)[1];
                                                }
                                            }
                                        }
                                        if ($row = sql_fetsel("*","spip_mailshots_destinataires","id_mailshot=".intval($campagne['id_mailshot'])." AND email=".sql_quote($original_recipient))){
                                                $event = "";
                                                if ($return_status_code[0] == 4 AND $row['statut']!=='fail'){
                                                        $event = 'reject';
                                                }
                                                elseif ($return_status_code[0] == 5 AND $row['statut']!=='fail'){
                                                        $event = 'hard_bounce';
                                                }
                                                if ($event) {
                                                        #var_dump("$event : ".$email->to_email);
                                                        $feedback($event,$original_recipient,$tracking_id);
                                                }
                                        }
                                }
                        }
                }
                imap_close($inbox);
        }
	return 0;
}
