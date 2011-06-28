<?php

/**
 * Création d'un emplacement
 */
function inc_instituer_emplacement_dist($datas) {
	if( !is_array($datas) ) return;
	$conf = pubban_recuperer_config();
	if($conf['adds_ok'] == 'oui')
		$datas = array_merge($datas, array(
			'prix_tranche_1' => $conf['prix_tranche_1'],
			'prix_tranche_2' => $conf['prix_tranche_2'],
			'prix_tranche_3' => $conf['prix_tranche_3'],
			'prix_tranche_4' => $conf['prix_tranche_4'],
		));
	if( $id_empl = sql_insertq($GLOBALS['_PUBBAN_CONF']['table_empl'], $datas, '', _BDD_PUBBAN) )
		return $id_empl;
	return false;
}

?>