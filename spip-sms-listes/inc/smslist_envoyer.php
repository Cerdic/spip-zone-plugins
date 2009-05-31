<?php
/*
 * Spip SMS Liste
 * Gestion de liste de diffusion de SMS
 *
 * Auteur :
 * Cedric Morin
 * (c) 2007 - Distribue sous licence GNU/GPL
 *
 */

include_spip("base/forms_base_api");
include_spip("base/abstract_sql");
if (!defined('_SMS_LIST_SPOOL_NOMBRE'))
	define('_SMS_LIST_SPOOL_NOMBRE',10);
if (!defined('_SMS_LIST_DELAI_ESSAIS'))
	define('_SMS_LIST_DELAI_ESSAIS',1);
function inc_smslist_envoyer($test = false){

	// mode test : on regarde si des actions sont a faire, mais on ne fait rien
	if ($test){
		$encore = smslist_demon_boite_envoi(true); // regarder si des envois a declencher
		if (!$encore){
			$res = spip_query("SELECT id_spool FROM spip_smslist_spool WHERE NOT(statut IN ('envoye','annule')) LIMIT 0,1");
			$encore = (spip_num_rows($res)>0);
		}
		return $encore;
	}
	else {
		// chercher les envois en attente (statut=prepa)
		smslist_demon_boite_envoi();
		// envoyer un lot
		smslist_spool(_SMS_LIST_SPOOL_NOMBRE);
		
		// clore les lots finis
		return smslist_nettoie_boite_envoi();
	}
}

function smslist_log($texte){
	if (_request('var_mode')!='test')
		spip_log($texte,'smslist');
	else 
		echo "$texte<br/>";
}

function smslist_compter_spool(){
	# reperer les lots plus valides (en attente de suppression ? ou en pause aussi)
	$res = spip_query("SELECT s.lot_envoi FROM spip_smslist_spool AS s JOIN spip_forms_donnees AS d ON d.id_donnee=s.lot_envoi WHERE d.statut='prop' GROUP BY s.lot_envoi");
	$lots = "0";
	while ($row = spip_fetch_array($res)) $lots.=",".$row['lot_envoi'];
	$inlots = calcul_mysql_in('lot_envoi',$lots);
	$total = 0;
	$restant = 0;
	#total
	$res = spip_query("SELECT COUNT(id_spool) AS n FROM spip_smslist_spool WHERE $inlots");
	if ($row = spip_fetch_array($res))
		$total = $row['n'];
	# restants
	if ($total){
		$res = spip_query("SELECT COUNT(id_spool) AS n FROM spip_smslist_spool WHERE $inlots AND NOT(statut IN ('envoye','annule'))");
		if ($row = spip_fetch_array($res))
			$restant = $row['n'];
	}
	return array($total,$restant);
}

function smslist_spool($nombre){
	# reperer les lots plus valides (en attente de suppression ? ou en pause aussi)
	$res = spip_query("SELECT s.lot_envoi FROM spip_smslist_spool AS s JOIN spip_forms_donnees AS d ON d.id_donnee=s.lot_envoi WHERE d.statut='prop' GROUP BY s.lot_envoi");
	$lots = "0";
	while ($row = spip_fetch_array($res)) $lots.=",".$row['lot_envoi'];
	$inlots = calcul_mysql_in('lot_envoi',$lots);
	//smslist_log("bad : $inbad");
	
	# preparer un lot d'envois
	include_spip('inc/acces');
	$id_process = substr(creer_uniqid(),0,5);
	# marquer le lot avec un tampo id_process
	spip_query("UPDATE spip_smslist_spool SET statut="._q($id_process)." WHERE $inlots AND statut='' ORDER BY compte,maj LIMIT ".intval($nombre));

	$res = spip_query("SELECT id_spool,compte,tel_from,tel_to,message,essais FROM spip_smslist_spool WHERE statut="._q($id_process));
	// si moins que possible, on retente des envois echoues il y a plus d'une heure
	if (($n = spip_num_rows($res))<$nombre){
		spip_query("UPDATE spip_smslist_spool SET statut="._q($id_process)." WHERE $inlots AND NOT(statut IN ('envoye','annule')) AND maj<NOW()-".intval(_SMS_LIST_DELAI_ESSAIS)." ORDER BY compte,maj LIMIT ".intval($nombre-$n));
	}
	$res = spip_query("SELECT id_spool,compte,tel_from,tel_to,message,essais FROM spip_smslist_spool WHERE statut="._q($id_process));
	while($row = spip_fetch_array($res)){
		$ok = smslist_envoi_unitaire($row['compte'],$row['tel_from'],$row['tel_to'],$row['message']);
		if ($ok===true) $ok = "envoye";
		smslist_log("envoi $id_spool: $ok");
		spip_query("UPDATE spip_smslist_spool SET statut="._q($ok).",date_envoi=NOW(),essais="._q($row['essais']+1)." WHERE id_spool="._q($row['id_spool']));
	}
}	

function smslist_envoi_unitaire($compte,$from,$to,$texte, $simuler=false){
	static $prefixe_defaut=array();
	static $connexion = array();
	if (!isset($connexion[$compte])){
		$res = spip_query("SELECT id_form FROM spip_forms_donnees WHERE id_donnee="._q($compte));
		if ($row = spip_fetch_array($res)){
			$connexion[$compte] = array(
				'prestataire'=>Forms_les_valeurs($row['id_form'], $compte, "select_1", " ",false),
				'user'=>Forms_les_valeurs($row['id_form'], $compte, "ligne_1", " ",true),
				'password'=>Forms_les_valeurs($row['id_form'], $compte, "password_1", " ",true,false),
				'api_id'=>Forms_les_valeurs($row['id_form'], $compte, "ligne_2", " ",true),
				'client_id' =>Forms_les_valeurs($row['id_form'], $compte, "ligne_3", " ",true)
			);
			$prefixe_defaut[$compte] = Forms_les_valeurs($row['id_form'], $compte, "ligne_4", " ",true);
		}
	}
	if (!isset($connexion[$compte])) return "Compte SMS $compte introuvable";
	include_spip('inc/charsets');
	$texte = unicode2charset($texte,'iso-8859-1');

	// mettre un prefixe pays si pas precise dans le destinataire
	if ((substr($to,0,1)=='0') && $prefixe_defaut[$compte])
		$to = $prefixe_defaut[$compte] . substr($to,1);
smslist_log("to:$to/prefix_def:".$prefixe_defaut[$compte]);
	
	if (!$envoyer_sms = charger_fonction('envoyer_sms','inc',true))
		return "Interface techniqe SMS introuvable (inc/envoyer_sms)";
	$message = array('to'=>$to,'send_params'=>array('from'=>$from),'text'=>$texte,'id'=>$connexion[$compte]['client_id']);
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
	include_spip('inc/charsets');
	include_spip('inc/filtres');
	$message = charset2unicode(supprimer_tags($message));
	
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

function smslist_demon_boite_envoi($test = false){
	$now = time();
	# scanner les boites d'envoi a la recherche d'envois a declencher
	$liste = Forms_liste_tables("smslist_boiteenvoi");
	$in = calcul_mysql_in('id_form',implode(',',$liste));
	if (!$test) smslist_log("scan $in");
	$res = spip_query("SELECT id_form,id_donnee FROM spip_forms_donnees WHERE $in AND statut='prepa'");
	while ($row = spip_fetch_array($res)){
		$id_donnee = $row['id_donnee'];
		$date = Forms_les_valeurs($row['id_form'], $id_donnee, "date_1", " ",true);
		if (preg_match('#([0-9]{1,2})/([0-9]{1,2})/([0-9]{4}|[0-9]{1,2})#', $date, $regs)) {
			$jour = $regs[1];
			$mois = $regs[2];
			$annee = $regs[3];
			if ($annee < 90){
				$annee = 2000 + $annee;
			} elseif ($annee<100) {
				$annee = 1900 + $annee ;
			}
			$date = "$annee-$mois-$jour";
		}
		$heure = Forms_les_valeurs($row['id_form'], $id_donnee, "heure_1", " ",true);
		$message = Forms_les_valeurs($row['id_form'], $id_donnee, "joint_1", ",",true);
		$listes = Forms_les_valeurs($row['id_form'], $id_donnee, "joint_2", ",",true);
		$log = "#$id_donnee/$date/$heure/$message/$listes";
		if (strtotime("$date $heure")<$now){
			if ($test) return true; // ok il y a des actions a faire, pas la peine de continuer
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
	$encore = false;
	while ($row = spip_fetch_array($res)){
		$row2 = spip_query("SELECT id_spool FROM spip_smslist_spool WHERE lot_envoi="._q($row['id_donnee'])." AND statut<>'envoye' LIMIT 0,1");
		if (!spip_num_rows($row2)){
			spip_query("UPDATE spip_forms_donnees SET statut='publie' WHERE id_donnee="._q($row['id_donnee']));
		 	smslist_log("envoi du lot ".$row['id_donnee']." fini");
		}
		else $encore = true;
	}
	return $encore;
}

?>
