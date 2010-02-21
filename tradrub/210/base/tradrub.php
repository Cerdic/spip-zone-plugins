<?php
/**
 * Plugin tradrub
 * Licence GPL (c) 2008-2010 Stephane Laurent (Bill), Matthieu Marcillaud
 * 
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Ajouter des champs a la table rubriques
 * @param array $tables_principales
 * @return array
 */
function tradrub_declarer_tables_principales($tables_principales){
	// Extension de la table rubriques
	$tables_principales['spip_rubriques']['field']['id_trad'] = "bigint(21) DEFAULT '0' NOT NULL";
		
	return $tables_principales;
}

?>
