<?php

/**
 * Plugin Quiz pour Spip 2.0
 * Licence GPL (c) 2010 - Ateliers CYM
 */
define('_TRAITEMENT_TYPO', 'typo(%s, "TYPO", $connect)'); // comme titre
define('_TRAITEMENT_RACCOURCIS', 'propre(%s, $connect)'); // comme texte

function quiz_declarer_tables_interfaces($interface){
	
	// ALIAS
	$interface['table_des_tables']['reponses'] = 'reponses';
	$interface['table_des_tables']['corrections'] = 'corrections';
	
	// JOINTURES
	$interface['tables_jointures']['spip_reponses'][]= 'articles'; // a placer avant la jointure sur articles
	$interface['tables_jointures']['spip_reponses'][] = 'corrections';
	// retours
	$interface['tables_jointures']['spip_articles'][] = 'reponses';
	$interface['tables_jointures']['spip_corrections'][] = 'reponses';
	
	// TRAITEMENTS
	$interface['table_des_traitements']['CORRIGE'][] = _TRAITEMENT_RACCOURCIS;
	$interface['table_des_traitements']['TEXTE'][] = _TRAITEMENT_TYPO;
	return $interface;
}

?>