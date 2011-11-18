<?php

if (!defined('_ECRIRE_INC_VERSION')) return;

function inc_notifier_commenter_ticket($id_ticket,$options){

		$id_ticket_forum = $options['id_ticket_forum'];

		//$ancien_auteur =  
		$row = sql_select("*","spip_tickets","id_ticket=$id_ticket");
		$datas = sql_fetch($row);
		
		$nom_auteur = sql_getfetsel("nom","spip_auteurs","id_auteur=".intval($options['id_auteur']));
		$texte = sql_getfetsel("texte","spip_tickets_forum","id_ticket_forum=".intval($id_ticket_forum));
		
		//include_spip('inc/ticketskiss_filtres');
		$envoyer_mail = charger_fonction('envoyer_mail','inc');
		
		$nom_site = $GLOBALS["meta"]["nom_site"];
		$url_site = $GLOBALS["meta"]["adresse_site"];
		$url_ticket = url_absolue(generer_url_ecrire('ticket_afficher',"id_ticket=$id_ticket"));
		
		
		$titre = trim($datas['titre']);
		$titre_message = "[Ticket - $nom_site] $titre - "._T('ticketskiss:comment_mail_titre');
		$titre_message = nettoyer_titre_email($titre_message);
		 

		 include_spip('inc/texte'); // pour couper
		$message = "$titre_message\n
		------------------------------------------\n"
		._T('ticketskiss:mail_texte_message_auto')."\n\n";
		$message .= couper(propre($texte),180)."\n\n";
		
		$message .= $url_ticket;
		
		// Determiner la liste des auteurs a notifier
		include_spip('inc/ticketskiss_autoriser');
		$select = array('email');
		$from = array('spip_auteurs AS t1');
		$autorises = definir_autorisations_ticketskiss('notifier');
		if ($autorises['statut']) 
			$where = array(sql_in('t1.statut', $autorises['statut']), 't1.email LIKE '.sql_quote('%@%'));
		else
			$where = array(sql_in('t1.id_auteur', $autorises['auteur']), 't1.email LIKE '.sql_quote('%@%'));
		$query_auteurs = sql_select($select, $from, $where);
		
		// Envoi des mails
		while ($row_auteur = sql_fetch($query_auteurs)) {
			$recipient = $row_auteur["email"];
			$envoyer_mail($recipient, $titre_message, $message);
			spip_log("notification commenter ticket envoyer mail $recipient");
		}
    }
?>