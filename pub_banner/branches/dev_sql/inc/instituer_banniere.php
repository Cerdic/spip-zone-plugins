<?php

/**
 * Création d'un emplacement
 */
function inc_instituer_banniere_dist($datas) {
	if (!is_array($datas)) return;
	include_spip('base/abstract_sql');
	$conf = pubban_recuperer_config();
/*
	if ($conf['adds_ok'] == 'oui')
		$datas = array_merge($datas, array(
			'prix_tranche_1' => $conf['prix_tranche_1'],
			'prix_tranche_2' => $conf['prix_tranche_2'],
			'prix_tranche_3' => $conf['prix_tranche_3'],
			'prix_tranche_4' => $conf['prix_tranche_4'],
		));
*/
	if ( $id_empl = sql_insertq('spip_bannieres', $datas, '') )
		return $id_empl;
	return false;
}

?>