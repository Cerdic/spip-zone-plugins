<?
/*
 * Spip SMS Liste
 * Gestion de liste de diffusion de SMS
 *
 * Auteur :
 * Cedric Morin
 * (c) 2007 - Distribue sous licence GNU/GPL
 *
 */

function smslist_log($texte){
	echo $texte."<br/>";
}

function smslist_declencher_envoi($lot,$message,$listes){
	// recuperer le texte du message
	$in = calcul_mysql_in('id_donnee',$message);
	$res = spip_query("SELECT id_form,id_donnee FROM spip_forms_donnees WHERE $in LIMIT 0,1");
	if (!$row = spip_fetch_array($res)
	OR !strlen($message = Forms_les_valeurs($row['id_form'],$row['id_donnee'],'texte_1'," ",true))){
		smslist_log("Envoi du lot $lot impossible : message $in introuvable");
		return;
	}
	
	// y a -t-il des listes selectionnes ?
	if ($listes=='0' OR !strlen($listes)){
		smslist_log("Envoi du lot $lot a tous les abonnes");
		// prendre le premier compte actif
		$tables = Forms_liste_tables('smslist_compte');
		$in = calcul_mysql_in("id_form",implode(',',$tables));
		$res = spip_query("SELECT id_donnee,id_form FROM spip_forms_donnees WHERE $in AND statut='publie' LIMIT 0,1");
		if (!$row = spip_fetch_array($res)){
			smslist_log("Envoi du lot $lot impossible : pas de compte SMS actif dans les tables $in");
			return;
		}
		$compte = $row['id_donnee'];
		$tel_from = Forms_les_valeurs($row['id_form'],$compte,'telephone_1');
		// selectionner tous les abonnes !
		$tables = Forms_liste_tables('smslist_abonne');
		$in = calcul_mysql_in("d.id_form",implode(',',$tables));
		$res = spip_query("SELECT dc.valeur AS tel_to FROM spip_forms_donnees AS d JOIN spip_forms_donnees_champs AS dc ON dc.id_donnee=d.id_donnee WHERE dc.champ='telephone_1' AND d.statut='publie' AND $in");
		#smslist_log("select : SELECT dc.valeur AS tel_to FROM spip_forms_donnees AS d JOIN spip_forms_donnees_champs AS dc ON dc.id_donnee=d.id_donnee WHERE dc.champ='telephone_1' AND d.statut='publie' AND $in");
		while ($row = spip_fetch_array($res)){
			$tel_to = $row['tel_to'];
			$values = implode(',',array_map('_q',array($lot,$tel_to,$message,$tel_from,$compte)));
			spip_query("REPLACE INTO spip_smslist_spool (lot_envoi,tel_to,message,tel_from,compte) VALUES ($values)");
			#smslist_log("spool : REPLACE INTO spip_smslist_spool (lot_envoi,tel_to,message,tel_from,compte) VALUES ($values)");
		}
	}
	else{
		$in = calcul_mysql_in('id_donnee',$listes);
		smslist_log("Envoi du $lot aux listes $in");
		// selectionner les listes
	}
	// on peut changer le statut du lot a 'en cours d'envoi'
	spip_query("UPDATE spip_forms_donnees SET statut='prop' WHERE id_donnee="._q($lot));
	
}

function smslist_etat_boite_envoi(){
	$now = time();
	$liste = Forms_liste_tables("smslist_boiteenvoi");
	foreach($liste as $id_form){
		smslist_log("scan $id_form");
		$res = spip_query("SELECT * FROM spip_forms_donnees WHERE id_form="._q($id_form)." AND statut='prepa'");
		while ($row = spip_fetch_array($res)){
			$id_donnee = $row['id_donnee'];
			$date = Forms_les_valeurs($id_form, $id_donnee, "date_1", " ",true);
			$heure = Forms_les_valeurs($id_form, $id_donnee, "heure_1", " ",true);
			$message = Forms_les_valeurs($id_form, $id_donnee, "joint_1", ",",true);
			$listes = Forms_les_valeurs($id_form, $id_donnee, "joint_2", ",",true);
			$log = "#$id_donnee:$date:$heure:$message:$listes";
			if (strtotime("$date $heure")<$now){
				$log .= " Top depart";
				smslist_log($log);
				smslist_declencher_envoi($id_donnee,$message,$listes);
			}
			else smslist_log($log);
		}
	}
}


function inc_smslist_envoyer(){
	include_spip("base/forms_base_api");

	// chercher les envois en attente (statut=prepa)
	smslist_etat_boite_envoi();
}

?>