<?php

/**
 * Création d'une publicite
 */
function inc_instituer_publicite_dist($datas, $statut='1inactif') {
	if (!is_array($datas)) return;
	include_spip('base/abstract_sql');
	if (!isset($datas['date_add']) ) $datas['date_add'] = date('Y-m-d H:i:s');
	$datas['statut'] = $statut;
	if ( $id_publicite = sql_insertq('spip_publicites', $datas, '') )
		return $id_publicite;
	return false;
}

?>