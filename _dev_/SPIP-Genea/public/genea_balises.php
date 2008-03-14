<?php

/*******************************************************************
 *
 * Copyright (c) 2007-2008
 * Xavier BUROT
 * fichier : public/genea_balises
 *
 * Ce programme est un logiciel libre distribue sous licence GNU/GPL
 *
 * *******************************************************************/

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('base/genea_base');
include_spip('base/create');
include_spip('inc/filtres');
include_spip('inc/genea_date');

// -- Recherche le numero SOSA d'un invidu ------------------------------
function trouve_sosa($id_individu){
	global $table_prefix;
	$sosa='';
	if($id_individu){
		$q = "SELECT id_sosa FROM ".$table_prefix."_genea_sosa WHERE id_individu=$id_individu";
		$res = spip_query($q);
		if ($row = spip_fetch_array($res)) $sosa = $row['id_sosa'];
	}
	return $sosa;
}

// -- Affiche le numero SOSA d'un individu ------------------------------
function balise_SOSA($p){
	$p->code = "trouve_sosa(".champ_sql('id_individu', $p).")";
	$p->interdire_scripts = true;
	return $p;
}
?>