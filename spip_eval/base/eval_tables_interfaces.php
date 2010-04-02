<?php
/*
 * Plugin pour SPIP 2.0
 * Auteur Cyril MARION
 * (c) 2010 Ateliers CYM - Paris
 * Distribue sous licence GPL
 */


function eval_declarer_tables_interfaces($interface){
	// definir les jointures possibles
	$interface['table_des_tables']['campagnes'] = 'campagnes';
	
	$interface['tables_jointures']['spip_campagnes'][] = 'rubriques';
	$interface['tables_jointures']['spip_rubriques'][] = 'campagnes';

	$interface['tables_jointures']['spip_mots'][] = 'campagnes';
	$interface['tables_jointures']['spip_campagnes'][] = 'mots';
	return $interface;
}

?>