<?php

include_spip('base/abstract_sql');

function action_spiplistescleaner_dist() {
	// Get the configuration
	$config = lire_config('spiplistescleaner');

	// Vars
	$prefix_spiplog = "SPIPLISTESCLEANER: ";
    spip_log($prefix_spiplog . "Begin cleaning!");

	$nb_deleted_mails = $config['nb_deleted_mails'];
	if ($nb_deleted_mails == "") {
		$nb_deleted_mails = 0;
	}
	
	// Connection configuration
	if ($config['server_security'] == "none") {
		$connection_string = "{". $config['server_address'] ."/". $config['server_type'] ."}". $config['server_mailbox'];
	} else {
		$connection_string = "{". $config['server_address'] ."/". $config['server_type'] ."/". $config['server_security'] ."/". $config['server_security_option'] ."}". $config['server_mailbox'];
	}
	
	$user_id = $config['server_username'];
	$password = $config['server_password'];

	// Connection to the server
	$mbox = @imap_open ($connection_string, $user_id, $password) or die(imap_last_error());
	
	// Read all mail in the mailbox
	$ids_msg = imap_search($mbox, "ALL");
	if (!$ids_msg) {
		spip_log($prefix_spiplog . "No MSG in the mailbox!");
		exit;
	}
	foreach ($ids_msg as $id_msg) {
		// Get the header (string)
		$header = imap_fetchheader($mbox, $id_msg);
		
		// Check if the mail is from a bouncer for failure delivery
		if (stristr($header, "report-type=delivery-status;") || 
			stristr($header, "Subject: failure notice") ||
			stristr($header, "Failed-Recipient") ||
			stristr($header, "X-Failed-Recipient")) {
				
			spip_log($prefix_spiplog . "Looking bounce mail: ". $id_msg);
			
			// Get de the body
			$body = strip_tags(imap_body($mbox, $id_msg));
			
			// Get the email sender
			// first try to look if "Final-Recipient:" exist and if not look for "To:"
			$email = stristr($body, "Final-Recipient:");
			if(!$email) {
				$email = stristr($body, "To:");
			}
			// extract the email adresse (type: name@domain)
			preg_match_all('/[_a-z0-9-]+(\.[_a-z0-9+-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*/mi', $email, $matches);
			$email = $matches[0][0];
			
			// If the email has been extracted
			if (!$email == "") {
				
				// Get the id from the auteur
				$row = sql_fetsel('id_auteur', 'spip_auteurs', 'email = ' . sql_quote($email));
				$id_auteur = $row["id_auteur"];
				
				// Check if the auteur existe
				if ($id_auteur != "") {
					$something_deleted = false;
					
					// Delete the auteur in the spip_auteur_listes tables
					if(sql_countsel('spip_auteurs_listes', 'id_auteur = ' . $id_auteur)) {
						if (sql_delete('spip_auteurs_listes', 'id_auteur = ' . $id_auteur)) {
							$something_deleted = true;
							spip_log($prefix_spiplog . "Auteur id : " . $id_auteur . " has been deleted from all list!");
						}
					}
					
					// Delete the auteur in the spip_auteur table if it's a status 6forum
					if(sql_countsel('spip_auteurs', 'id_auteur = ' . $id_auteur . " AND statut = '6forum'")) {
						if(sql_delete('spip_auteurs', 'id_auteur = ' . $id_auteur . " AND statut = '6forum'")) {
							$something_deleted = true;
							spip_log($prefix_spiplog . "Auteur id : " . $id_auteur . " has been deleted from spip!");
						}
					}
					
					// If something has been deleted we delete the mail
					if ($something_deleted) {
						// Mark the mail "deleted" in the mailbox
						imap_delete($mbox, $id_msg);
						
						// Increment deleted email value
						$nb_deleted_mails++;				
					}
		
				} else {
					spip_log($prefix_spiplog . "Error : auteur " . $email . " doesn't exist!");
				}
			}
		}
	}

	// Update the number of deleted email
	ecrire_config('spiplistescleaner/nb_deleted_mails', $nb_deleted_mails);
	
	// Delete all mails marked as deleted
	imap_expunge($mbox);
	
	// Close the email server connection
	imap_close($mbox);
	
	spip_log($prefix_spiplog . "End of cleaning!");
}

?>
