<?php

/*
 * P.I.M Agenda
 * Gestion d'un agenda collaboratif
 *
 * Auteur :
 * Cedric Morin, Notre-ville.net
 * (c) 2005,2007 - Distribue sous licence GNU/GPL
 *
 */

include_spip('inc/pim_agenda');
function inc_notifier_pim_agenda_dist($action, $id_agenda, $row_anc, $script){
	spip_log("notification de l'agenda $id_agenda : action $action",'pimagenda');
	
	// Envoi des messages d'invitation par messagerie interne et mail
	$row = PIMAgenda_detailler_agenda($id_agenda, true);

	// lister tous les anciens invites
	$liste_invites_old = array_merge($row_anc['invites'],PIMAgenda_liste_contenu_groupe_auteur($row_anc['groupes_invites']));
	$liste_invites_old = array_merge($liste_invites_old,$row_anc['auteurs']);
	// lister tus les nouveaux invites
	if ($action=='supprimer') {
		$liste_invites_new = array();
		$row = $row_anc;
	}
	else {
		$liste_invites_new = array_merge($row['invites'],PIMAgenda_liste_contenu_groupe_auteur($row['groupes_invites']));
		$liste_invites_new = array_merge($liste_invites_new,$row['auteurs']);
	}
	spip_log("notification de l'agenda $id_agenda : invites_old:".serialize($liste_invites_old),'pimagenda');
	spip_log("notification de l'agenda $id_agenda : invites_new:".serialize($liste_invites_new),'pimagenda');

	$message_titre=_T('pimagenda:texte_agenda');
	$message_auteur=reset($row['auteurs']);
	$message_date_heure=date("Y-m-d H:i:s");
	$redirect_url = parametre_url($script,'id_agenda',$id_agenda);
	$st_date_deb = strtotime($row['date_debut']);
	$st_date_fin = strtotime($row['date_fin']);
	
	$message_new = "Vous &ecirc;tes invit&eacute;s le <a href='$redirect_url'>".date("d-m-Y",$st_date_deb)." &agrave; ".date("H:i",$st_date_deb)."</a> (dur&eacute;e ".date("H:i",$st_date_fin-$st_date_deb).")";
	$message_modif = "";
	if ($action=='modifier'){
		if ($st_date_deb!=($st_last=strtotime($row_anc['date_debut']))){
			$message_modif="L'invitation du ".date("d-m-Y",$st_last)." &agrave; ".date("H:i",$st_last)." a &eacute;t&eacute; deplac&eacute;e le <a href='$redirect_url'>".date("d-m-Y",$st_date_deb)." &agrave; ".date("H:i",$st_date_deb)."</a> (dur&eacute;e ".date("H:i",$st_date_fin-$st_date_deb).")";
		}
		else if ($st_date_fin!=($st_last=strtotime($row_anc['date_fin']))){
			$message_modif="La dur&eacute;e de l'invitation du <a href='$redirect_url'>".date("d-m-Y",$st_date_deb)." &agrave; ".date("H:i",$st_date_deb)."</a> a &eacute;t&eacute; modifi&eacute;e (nouvelle dur&eacute;e ".date("H:i",$st_date_fin-$st_date_deb).")";
		}
	}
	if ($action=='supprimer')
		$message_suppr = "Annulation de La r&eacute;nion du ".date("d-m-Y",$st_date_deb)." &agrave; ".date("H:i",$st_date_deb)." (dur&eacute;e ".date("H:i",$st_date_fin-$st_date_deb).")";
	else
		$message_suppr = "Votre participation &agrave; la r&eacute;nion du ".date("d-m-Y",$st_date_deb)." &agrave; ".date("H:i",$st_date_deb)." (dur&eacute;e ".date("H:i",$st_date_fin-$st_date_deb).") n'est plus necessaire.";
	
	// invitations annullees
	$annulations = array_diff($liste_invites_old,$liste_invites_new);
	spip_log("notification de l'agenda $id_agenda : annulations:".serialize($annulations),'pimagenda');
	notifier_envoi_message($message_titre,$message_auteur,$message_date_heure,"",$redirect_url,$annulations);
	// invitations nouvelles
	$nouveaux = array_diff($liste_invites_new,$liste_invites_old);
	spip_log("notification de l'agenda $id_agenda : nouveaux:".serialize($nouveaux),'pimagenda');
	notifier_envoi_message($message_titre,$message_auteur,$message_date_heure,$message_new,$redirect_url,$nouveaux);
	// invitations modifiees
	if ($message_modif) {
		$modifs = array_diff($liste_invites_new,$nouveaux);
		spip_log("notification de l'agenda $id_agenda : modifs:".serialize($modifs),'pimagenda');
		notifier_envoi_message($message_titre,$message_auteur,$message_date_heure,$message_modif,$redirect_url,$modifs);
	}
}

function notifier_envoi_message($message_titre,$message_auteur,$message_date_heure,$texte,$redirect_url,$destinataires,$interne = true){
	if (!count($destinataires)) return;
	$id_message = 0;
	if ($interne){
		include_spip('inc/abstract_sql');
		$id_message = spip_abstract_insert("spip_messages",
				"(titre,texte,type,date_heure,date_fin,rv,statut,id_auteur,maj)",
				"("._q($message_titre).","._q($message_texte).",'normal','$message_date_heure','$message_date_heure','non','publie',$message_auteur,NOW())");
	}

	$from = "agenda@".$_SERVER["HTTP_HOST"]."\n";
	$message_texte = supprimer_tags($message_texte) . "\n".url_absolue($redirect_url);
	
	// envoyer le message interne
	if ($id_message)
		foreach($destinataires as $id_dest)
			if ($id_dest!==$message_auteur)
				spip_query("INSERT INTO spip_auteurs_messages (id_message, id_auteur, vu) VALUES ($id_message, "._q($id_dest).",'non');");

	$email = "";
	$in_dest = calcul_mysql_in('id_auteur',join(',',array_map('intval',$destinataires)));
	$res = spip_query("SELECT email FROM spip_auteurs WHERE $in_dest");
	while ($row=spip_fetch_array($res))
		if (trim($row['email']))
			$email .= ", " . trim($row['email']);
	if (strlen($email)){
		$email = substr($email,2);
		$envoyer_mail = charger_fonction('envoyer_mail','inc');
		$envoyer_mail($email, $message_titre, $message_texte, $from);
		spip_log("envoyer_mail: $email:$message_titre: $message_texte:$from:",'pimagenda');
	}
}

?>