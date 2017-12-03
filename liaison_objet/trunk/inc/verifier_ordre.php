<?php

if (!defined("_ECRIRE_INC_VERSION"))
	return;
function inc_verifier_ordre_dist($where) {
	$sql = sql_select("id_liaison_objet", "spip_liaison_objets", $where, '', "ordre,id_liaison_objet");
	$ordre = 0;

	// on vérifie l'ordre des objets déjà enregistrés et on corrige si beliaison_objetin

	while ($row = sql_fetch($sql)) {
		$ordre++;
		$where = array('id_liaison_objet=' . $row['id_liaison_objet'], );

		sql_updateq("spip_liaison_objets", array("ordre" => $ordre), $where);
	}

	include_spip('inc/invalideur');
	suivre_invalideur("id='liaison_objet/$id_liaison_objet'");
	return $ordre;
}
?>