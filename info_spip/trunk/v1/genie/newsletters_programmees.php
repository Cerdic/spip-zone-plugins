<?php
/**
 * Plugin Newsletters
 * (c) 2012 Cedric Morin
 * Licence GNU/GPL
 */

if (!defined('_ECRIRE_INC_VERSION')) return;


function genie_newsletters_programmees_dist($t){

	$now = date('Y-m-d H:i:s');
	// trouver une newsletter programmee a envoyer
	if ($row = sql_fetsel("*",'spip_newsletters',"statut=".sql_quote('prog')." AND date<".sql_quote($now)." AND date>".sql_quote('1000-01-01 00:00:00'))){
		// par defaut la date de la newsletter programmee est celle de la programmation
		// sauf si on a rate des echeances : on les rattrape dans une seule NL dans ce cas
		$date = $row["date"];
		include_spip('inc/when');
		include_spip('inc/newsletters');
		list($date_start,$rrule) = newsletter_ics_to_date_rule($row['recurrence']);
		while ($next=when_rule_to_next_date($date_start,$rrule,$date)
			AND $next<$now){
			spip_log("programmer #".$row['id_newsletter']." date : $date manquee, fusionnee avec celle du $next","newsletterprog");
			$date = $next;
		}

		if ($date!=$row['date']){
			// il faut maj en base pour etre coherent au moment de la generation de la NL de test
			sql_updateq("spip_newsletters",array('date'=>$date),"id_newsletter=".intval($row['id_newsletter']));
			$row['date'] = $date;
		}

		spip_log("programmer #".$row['id_newsletter']." date : ".$row['date'],"newsletterprog");
		newsletter_creer_newsletter_programmee($row);
	}
	return 0;
}

function newsletter_update_next_occurence($row, $sent=true){
	// on met a jour la date et date_redac sur la source
	include_spip("action/editer_objet");
	include_spip("inc/autoriser");
	include_spip("inc/when");
	include_spip("inc/newsletters");

	list($date_start,$rule) = newsletter_ics_to_date_rule($row['recurrence']);
	$set = array(
		'date_redac' => $row['date'],
		'date' => when_rule_to_next_date($date_start,$rule,$row['date'])
	);

	// si on a rien envoye on ne touche pas a la date de derniere occurence
	if (!$sent)
		unset($set['date_redac']);

	if (!$set['date']) $set['date'] = "0001-01-01 00:00:00";
	autoriser_exception("modifier","newsletter",$row['id_newsletter']);
	autoriser_exception("instituer","newsletter",$row['id_newsletter']);
	objet_modifier("newsletter",$row['id_newsletter'],$set);
	autoriser_exception("modifier","newsletter",$row['id_newsletter'],false);
	autoriser_exception("instituer","newsletter",$row['id_newsletter'],false);
}

function newsletter_creer_newsletter_programmee($row){

	// verifier deja si il y a qqchose a envoyer
	$generer_newsletter = charger_fonction("generer_newsletter","action");

	$patron = $row['patron'];
	$html = newsletters_recuperer_fond($row['id_newsletter'], $patron, $row['date'], $row['date_redac']);
	if (!strlen(trim($html))){
		spip_log("Rien a envoyer pour programmation #".$row['id_newsletter'],"newsletterprog");
		newsletter_update_next_occurence($row,false);
		return;
	}

	// OK retrouvons ou creons l'objet de base
	$set = array(
		"titre" => $row["titre"],
		"chapo" => $row["chapo"],
		"texte" => $row["texte"],
		"date" => $row["date"],
		"date_redac" => $row['date_redac'], // occurence precedente
		"patron" => $patron,
		"baked" => 1,
		"statut" => "prop",
		"lang" => "lang",
		"recurrence" => $row['id_newsletter'],
	);

	include_spip("action/editer_objet");
	include_spip("inc/autoriser");
	if (
		// retrouver une instance initiee mais pas finie
		// (cas d'une ereur fatale pendant la generation de la lettre)
		// evite de creer un nombre d'instance infini pour rien
		!$id_newsletter = sql_getfetsel("id_newsletter","spip_newsletters","date_redac=".sql_quote($set['date_redac'])." AND recurrence=".sql_quote($set['recurrence'],'','text')." AND statut=".sql_quote('prop'))

		// et sinon on cree la newsletter
		AND !$id_newsletter = objet_inserer("newsletter",0)){
		spip_log("Erreur : impossible de creer une newsletter pour programmation #".$row['id_newsletter']." :".var_export($set,true),"newsletterprog"._LOG_ERREUR);
		return;
	}

	autoriser_exception("modifier","newsletter",$id_newsletter);
	autoriser_exception("instituer","newsletter",$id_newsletter);
	autoriser_exception("generer","newsletter",$id_newsletter);

	objet_modifier("newsletter",$id_newsletter,$set);

	// ensuite on calcule vraiment les 3 versions (html, texte, html_page)
	$generer_newsletter($id_newsletter, true, $row['date_redac']);

	$fixer_newsletter = charger_fonction("fixer_newsletter","action");
	$fixer_newsletter($id_newsletter);

	// verifions au cas ou
	$row2 = sql_fetsel("*","spip_newsletters","id_newsletter=".intval($id_newsletter));
	if (!strlen(trim($row2['html_email']))){
		spip_log("Rien a envoyer (apres tentative de generation) pour programmation #".$row['id_newsletter'],"newsletterprog");
		// du coup on met a jour la prochaine occurence
		newsletter_update_next_occurence($row,false);

		// passer cette tentative ratee a la poubelle ?
		// a priori si on arrive la c'est un bug ou un cas tordu
		// donc pour le moment on laisse la newsletter ratee en prop
		// elle sera recyclee a la prochaine echeance
		return;
	}

	// on passe en publie
	$set = array('statut'=>'publie','date'=>$row2['date']);
	objet_modifier("newsletter",$id_newsletter,$set);

	autoriser_exception("modifier","newsletter",$id_newsletter,false);
	autoriser_exception("instituer","newsletter",$id_newsletter,false);
	autoriser_exception("generer","newsletter",$id_newsletter,false);

	// on met a jour la date et date_redac sur la source
	newsletter_update_next_occurence($row);

	// Les envois

	// un envoi de test sur une adresse ?
	if ($row['email_test']){
		$email = $row['email_test'];
		// recuperer l'abonne si il existe avec cet email
		$subscriber = charger_fonction('subscriber','newsletter');
		$dest = $subscriber($email);

		// si abonne inconnu, on simule (pour les tests)
		if (!$dest)
			$dest = array(
				'email' => $email,
				'nom' => $email,
				'lang' => $row['lang'],
				'status' => 'on',
				'url_unsubscribe' => url_absolue(_DIR_RACINE . "unsubscribe"),
			);

		// ok, maintenant on prepare un envoi
		$send = charger_fonction("send","newsletter");
		$err = $send($dest, $id_newsletter, array('test'=>true));

		if ($err){
			spip_log("Erreur lors de l'envoi de test a $email : $err","newsletterprog"._LOG_ERREUR);
		}
	}

	// un envoi a une liste ?
	if ($row['liste']){
		$listes = array($row['liste']);

		$bulkstart = charger_fonction("bulkstart","newsletter");
		if (!$bulkstart($id_newsletter, $listes))
			spip_log("Erreur lors de l'envoi groupe a la liste ".$row['liste'],"newsletterprog"._LOG_ERREUR);
	}

}