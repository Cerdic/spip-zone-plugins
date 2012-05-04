<?php

/**
 * Création d'une banniere
 */
function inc_instituer_banniere_dist($datas) {
	if (!is_array($datas)) return;
	include_spip('base/abstract_sql');
	if ( $id_banniere = sql_insertq('spip_bannieres', $datas, '') )
		return $id_banniere;
	return false;
}

?>