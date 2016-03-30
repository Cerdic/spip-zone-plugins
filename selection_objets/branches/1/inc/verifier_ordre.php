<?php

if (!defined("_ECRIRE_INC_VERSION"))
	return;
function inc_verifier_ordre_dist($where) {
	$sql = sql_select("id_selection_objet", "spip_selection_objets", $where, '', "ordre,id_selection_objet");
	$ordre = 0;

	// on vérifie l'ordre des objets déjà enregistrés et on corrige si beselection_objetin

	while ($row = sql_fetch($sql)) {
		$ordre++;
		$where = array('id_selection_objet=' . $row['id_selection_objet'], );

		sql_updateq("spip_selection_objets", array("ordre" => $ordre), $where);
	}

	include_spip('inc/invalideur');
	suivre_invalideur("id='selection_objet/$id_selection_objet'");
	return $ordre;
}
?>