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


function inc_smslist_envoyer(){
	include_spip("base/forms_base_api");

	// chercher les envois en attente (statut=prepa)
	smslist_demon_boite_envoi();
	// envoyer un lot
	smslist_spool(10);
	
	// clore les lots finis
	smslist_nettoie_boite_envoi();
}

function smslist_log($texte){
	echo $texte."<br/>";
}


function smslist_spool($nombre){
	# reperer les lots plus valides (en attente de suppression ? ou en pause aussi)
	spip_query("SELECT s.lot_envoi FROM spip_smslist_spool AS s JOIN spip_forms_donnees AS d ON d.id_donnee=s.lot_envoi WHERE d.statut<>'prepa'");
	$bad = "0";
	while ($row = spip_fetch_array($res)) $bad.=",".$row['lot_envoi'];
	$inbad = calcul_mysql_in('lot_envoi',$bad,'NOT');
	//smslist_log("bad : $inbad");
	
	# preparer un lot d'envois
	$id_process = substr(creer_uniqid(),0,5);
	# marquer le lot avec un tampo id_process
	spip_query("UPDATE spip_smslist_spool SET statut="._q($id_process)." WHERE $inbad AND statut='' ORDER BY compte,maj LIMIT ".intval($nombre));

	$res = spip_query("SELECT id_spool,compte,tel_from,tel_to,message,essais FROM spip_smslist_spool WHERE statut="._q($id_process));
	// si moins que possible, on retente des envois echoues il y a plus d'une heure
	if (($n = spip_num_rows($res))<$nombre){
		spip_query("UPDATE spip_smslist_spool SET statut="._q($id_process)." WHERE $inbad AND statut<>'envoye' AND maj<NOW()-1 ORDER BY compte,maj LIMIT ".intval($nombre-$n));
	}
	$res = spip_query("SELECT id_spool,compte,tel_from,tel_to,message,essais FROM spip_smslist_spool WHERE statut="._q($id_process));
	while($row = spip_fetch_array($res)){
		$ok = smslist_envoi_unitaire($row['compte'],$row['tel_from'],$row['tel_to'],$row['message']);
		if ($ok===true) $ok = "envoye";
		smslist_log("envoi $id_spool: $ok");
		spip_query("UPDATE spip_smslist_spool SET statut="._q($ok).",date_envoi=NOW(),essais="._q($row['essais']+1)." WHERE id_spool="._q($row['id_spool']));
	}
}	

function smslist_envoi_unitaire($compte,$from,$to,$texte, $simuler=true){
	static $connexion = array();
	if (!isset($connexion[$compte])){
		$res = spip_query("SELECT id_form FROM spip_forms_donnees WHERE id_donnee="._q($compte));
		if ($row = spip_fetch_array($res)){
			$connexion[$compte] = array(
				'prestataire'=>Forms_les_valeurs($row['id_form'], $compte, "select_1", " ",false),
				'user'=>Forms_les_valeurs($row['id_form'], $compte, "ligne_1", " ",true),
				'password'=>Forms_les_valeurs($row['id_form'], $compte, "password_1", " ",true),
				'api_id'=>Forms_les_valeurs($row['id_form'], $compte, "ligne_2", " ",true),
				'client_id' =>Forms_les_valeurs($row['id_form'], $compte, "ligne_3", " ",true)
			);
		}
	}
	if (!isset($connexion[$compte])) return "Compte SMS $compte introuvable";
	
	$envoyer_sms = charger_fonction('envoyer_sms','inc');
	$message = array('to'=>$to,'from'=>$from,'text'=>$texte,'id'=>$connexion[$compte]['client_id']);
	if (!$simuler)
		return $envoyer_sms($connexion[$compte],$message);
	else {
		smslist_log("SIMU : envoyer_sms(".serialize($connexion[$compte]).",".serialize($message).")");
		return true;
	}
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
		smslist_log("Envoi du lot $lot aux listes $in");
		// prendre le premier compte actif
		$tables = Forms_liste_tables('smslist_compte');
		$in = calcul_mysql_in("id_form",implode(',',$tables));
		$res = spip_query("SELECT id_donnee,id_form FROM spip_forms_donnees WHERE $in AND statut='publie' LIMIT 0,1");
		if (!$row = spip_fetch_array($res)){
			smslist_log("Envoi du lot $lot impossible : pas de compte SMS actif dans les tables $in");
			return;
		}
		$compte = $row['id_donnee'];
		
		$in = calcul_mysql_in('id_donnee',$listes);
		// selectionner les listes
		$res = spip_query("SELECT id_donnee,id_form FROM spip_forms_donnees WHERE $in AND statut='publie'");
		if (!spip_num_rows($res)){
			smslist_log("Envoi du lot $lot impossible : pas de listes de diffusions $in actives trouvees");
			return;
		}
		while ($row = spip_fetch_array($res)){
			$tel_from = Forms_les_valeurs($row['id_form'],$row['id_donnee'],'telephone_1',",",true);
			$abonnes = Forms_les_valeurs($row['id_form'],$row['id_donnee'],'joint_1',",",true);
			smslist_log($abonnes);
			$in_abonnes = calcul_mysql_in("id_donnee",$abonnes);
			$res2 = spip_query("SELECT valeur AS tel_to FROM spip_forms_donnees_champs WHERE champ='telephone_1' AND $in_abonnes");
			smslist_log("SELECT valeur AS tel_to FROM spip_forms_donnees_champs WHERE champ='telephone_1' AND $in_abonnes");
			while ($row2 = spip_fetch_array($res2)){
				$tel_to = $row2['tel_to'];
				$values = implode(',',array_map('_q',array($lot,$tel_to,$message,$tel_from,$compte)));
				spip_query("REPLACE INTO spip_smslist_spool (lot_envoi,tel_to,message,tel_from,compte) VALUES ($values)");
				smslist_log("spool : REPLACE INTO spip_smslist_spool (lot_envoi,tel_to,message,tel_from,compte) VALUES ($values)");
			}
		}
	}
	// on peut changer le statut du lot a 'en cours d'envoi'
	spip_query("UPDATE spip_forms_donnees SET statut='prop' WHERE id_donnee="._q($lot));
	
}

function smslist_demon_boite_envoi(){
	$now = time();
	# scanner les boites d'envoi a la recherche d'envois a declencher
	$liste = Forms_liste_tables("smslist_boiteenvoi");
	$in = calcul_mysql_in('id_form',implode(',',$liste));
	smslist_log("scan $in");
	$res = spip_query("SELECT id_form,id_donnee FROM spip_forms_donnees WHERE $in AND statut='prepa'");
	while ($row = spip_fetch_array($res)){
		$id_donnee = $row['id_donnee'];
		$date = Forms_les_valeurs($row['id_form'], $id_donnee, "date_1", " ",true);
		$heure = Forms_les_valeurs($row['id_form'], $id_donnee, "heure_1", " ",true);
		$message = Forms_les_valeurs($row['id_form'], $id_donnee, "joint_1", ",",true);
		$listes = Forms_les_valeurs($row['id_form'], $id_donnee, "joint_2", ",",true);
		$log = "#$id_donnee:$date:$heure:$message:$listes";
		if (strtotime("$date $heure")<$now){
			$log .= " Top depart";
			smslist_log($log);
			smslist_declencher_envoi($id_donnee,$message,$listes);
		}
		else smslist_log($log);
	}
}
function smslist_nettoie_boite_envoi(){
	# scanner les boites d'envoi a la recherche d'envois finis
	$liste = Forms_liste_tables("smslist_boiteenvoi");
	$in = calcul_mysql_in('id_form',implode(',',$liste));
	smslist_log("scan $in");
	$res = spip_query("SELECT id_donnee FROM spip_forms_donnees WHERE $in AND statut='prop'");
	while ($row = spip_fetch_array($res)){
		$row2 = spip_query("SELECT id_spool FROM spip_smslist_spool WHERE lot_envoi="._q($row['id_donnee'])." AND statut<>'envoye' LIMIT 0,1");
		if (!spip_num_rows($row2)){
			spip_query("UPDATE spip_forms_donnees SET statut='publie' WHERE id_donnee="._q($row['id_donnee']));
		 	smslist_log("envoi du lot ".$row['id_donnee']." fini");
		}
	}
}

?>