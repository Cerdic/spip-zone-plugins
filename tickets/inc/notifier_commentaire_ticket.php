<?php
/**
 * Notification d'un nouveau mesage de forum 
 */
    function inc_notifier_commentaire_ticket($id_ticket,$statut_nouveau='',$statut_ancien=''){
    	
		$row = sql_select("a.titre, b.*","spip_tickets AS a, spip_tickets_forum AS b","a.id_ticket = b.id_ticket AND a.id_ticket=$id_ticket","","date DESC","1");
		$datas = sql_fetch($row);
		
		include_spip('tickets_fonctions');
		$envoyer_mail = charger_fonction('envoyer_mail','inc');
		
		$nom_site = $GLOBALS["meta"]["nom_site"];
		$url_site = $GLOBALS["meta"]["adresse_site"];
		$url_ticket = url_absolue(generer_url_ecrire('ticket_afficher',"id_ticket=$id_ticket"));
		
		
		$titre = trim($datas['titre']);
		$titre_message = "[Ticket - $nom_site] $titre - "._T('tickets:champ_nouveau_commentaire');
		$titre_message = nettoyer_titre_email($titre_message);
		 
		$message = "$titre_message\n\n";
		$message .= _T('tickets:nouveau_commentaire_mail')."\n\n";
		$message .= "------------------------------------------\n";
		$message .= _T('tickets:message_automatique')."\n\n";
		$message .= $datas['texte']."\n\n";
		$message .= $url_ticket;
		
		// Determiner la liste des auteurs a notifier
		include_spip('inc/tickets_autoriser');
		$select = array('email');
		$from = array('spip_auteurs AS t1');
		$autorises = definir_autorisations_tickets('notifier');
		if ($autorises['statut']) 
			$where = array(sql_in('t1.statut', $autorises['statut']), 't1.email LIKE '.sql_quote('%@%'));
		else
			$where = array(sql_in('t1.id_auteur', $autorises['auteur']), 't1.email LIKE '.sql_quote('%@%'));
		$query_auteurs = sql_select($select, $from, $where);
		
		// Envoi des mails
		while ($row_auteur = sql_fetch($query_auteurs)) {
			$recipient = $row_auteur["email"];
			$envoyer_mail($recipient, $titre_message, $message);
			spip_log("notification commentaire ticket envoyer mail $recipient");
		}
    }
?>
