<?php

// Le filtre [(#ID_RUBRIQUE|titre_parent)]
function titre_parent($id_rubrique) {
	$fetch = function_exists('sql_fetch') ? 'sql_fetch' : 'spip_fetch_array';
	if(!($id_rubrique = intval($id_rubrique))) return '';
	$q = 'SELECT titre FROM spip_rubriques WHERE id_rubrique='.$id_rubrique;
	if($r = spip_query($q))
		if($row = $fetch($r))
			return $row['titre'];
	return '';
}

// La balise
function balise_TITRE_PARENT_dist($p) {
	$id_rubrique = champ_sql('id_rubrique', $p);
	$p->code = "titre_parent(".$id_rubrique.")";
	return $p;
}

// Positionner les filtres standards en recopiant ceux de #TITRE
// attention, ca ne positionne pas la langue_typo (mais tant pis)
include_spip('public/interfaces');
global $table_des_traitements;
if (!isset($table_des_traitements['TITRE_PARENT'])) {
	$table_des_traitements['TITRE_PARENT'] = $table_des_traitements['TITRE'];
}

?>