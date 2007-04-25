<?php
/*	*********************************************************************
	*
	* Copyright (c) 2007
	* Xavier Burot
	*
	* Ce programme est un logiciel libre distribue sous licence GNU/GPL.
	*
	*********************************************************************
*/

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('base/create');
include_spip('base/genea_base');
include_spip('inc/plugin');
include_spip('inc/genea_boucles');

// -- Definition de la table permettant l'affichage du logo d'un individu
include_spip('inc/chercher_logo');
global $table_logos;
$table_logos['id_individu'] = 'indiv';

//
// -- Definition des balises utilisables --------------------------------
//

// -- Balise de traduction des champs -----------------------------------
function balise_GENEA_TRADUC($p) {
	$intitule = champ_sql('type_evt', $p);
	$p->code = "_T(strtolower('genea:'.$intitule))";
	$p->interdire_scripts = true;
	return $p;
}

// -- Balise d'affichage de l'intitule du sexe --------------------------
function balise_INTITULE_SEXE($p) {
	$intitule = champ_sql('sexe', $p);
	$p->code = "_T(strtolower('genea:'.$intitule))";
	$p->interdire_scripts = true;
	return $p;
}

//  -- Balises de datation des evenements -------------------------------
function sql_date_evt($id_individu, $type_evt, $fin=false) {
	$date_evt = '';
	if ($id_individu) {
		$query = "SELECT ";
		if ($fin==false) { 
			$query .= "date";
		} else {
			$query .= "date_fin";
		}
		$query .= " FROM spip_genea_evt, spip_genea_evt_individus WHERE spip_genea_evt_individus.id_individu=$id_individu AND spip_genea_evt_individus.id_genea_evt=spip_genea_evt.id_genea_evt AND type_evt='$type_evt'";
		$result = spip_query($query);
		if ($row= spip_fetch_array($result)){
			if ($fin==false) { 
				$date_evt = $row['date'];
			} else {
				$date_evt = $row['date_fin'];
			}
		}
	}
	return $date_evt;
}

function balise_DATE_NAISSANCE($p){
	$p->code = "sql_date_evt(".champ_sql('id_individu', $p).",'BIRT')";
	#$p->interdire_scripts = true;
	return $p;
}

function balise_DATE_DECES($p){
	$p->code = "sql_date_evt(".champ_sql('id_individu', $p).",'DEAT')";
	#$p->interdire_scripts = true;
	return $p;
}

$table_des_traitements['DATE_NAISSANCE'] = 'vider_date(%s)';
$table_des_traitements['DATE_DECES'] = 'vider_date(%s)';


// -- Balise de calcul du numero SOSA -----------------------------------
function trouve_sosa($id_individu){
	$sosa ='';
	if ($id_individu){
		$query = "SELECT id_sosa FROM spip_genea_sosa WHERE id_individu=$id_individu";
		$result = spip_query($query);
		if ($row= spip_fetch_array($result)){
			$sosa = $row['id_sosa'];
		}
	}
	return $sosa;
}

function balise_SOSA($p){
	$p->code = "trouve_sosa(".champ_sql('id_individu', $p).")";
	#$p->interdire_scripts = true;
	return $p;
}
?>