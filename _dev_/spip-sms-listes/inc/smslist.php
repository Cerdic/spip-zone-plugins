<?php

function smslist_trouver_abonne($telephone){
	include_spip("base/forms_base_api");
	include_spip("base/abstract_sql");
	$telephone = str_replace("."," ",$telephone);
	$telephone = preg_replace(",\s(=?\s),","",$telephone);
	$telephone = str_replace(" ","_",$telephone);

	$liste = Forms_liste_tables('smslist_abonne');
	$in_form = calcul_mysql_in('d.id_form',implode(',',$liste));
	$res = spip_query($r=
	  "SELECT * FROM spip_forms_donnees_champs AS dc 
	  LEFT JOIN spip_forms_donnees AS d ON d.id_donnee=dc.id_donnee 
	  WHERE $in_form AND champ='telephone_1' AND valeur LIKE "._q($telephone));

	if ($row = spip_fetch_array($res)){
		return array($row['id_donnee'],$row['id_form']);
	}

	return false;
}

function smslist_actualiser_abonnements($id_donnee, $c=NULL){
	include_spip("base/forms_base_api");
	include_spip("base/abstract_sql");
	$listes = _request('liste',$c);
	$in_saisie = calcul_mysql_in('id_donnee',implode(',',$listes));
	
	$listef = Forms_liste_tables('smslist_liste');
	$in_form = calcul_mysql_in('id_form',implode(',',$listef));
	
	// supprimer les anciens liens
	Forms_delier_donnee($id_donnee,0,'smslist_liste');
	
	// inserer les nouveaux liens
	$values = array();
	$res = spip_query("SELECT id_donnee FROM spip_forms_donnees WHERE $in_form AND $in_saisie");
	while ($row=spip_fetch_array($res)){		
		$values[] = $row['id_donnee'];
	}
	$inserts = "($id_donnee," . implode("),($id_donnee,",$values) . ")";
	spip_query("INSERT INTO spip_forms_donnees_donnees (id_donnee_liee,id_donnee) VALUES $inserts");
}

?>