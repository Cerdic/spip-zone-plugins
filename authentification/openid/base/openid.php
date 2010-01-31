<?php
/**
 * Plugin OpenID
 * Licence GPL (c) 2007-2009 Edouard Lafargue, Mathieu Marcillaud, Cedric Morin, Fil
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Ajouter des champs a la table auteurs
 * @param array $tables_principales
 * @return array
 */
function openid_declarer_tables_principales($tables_principales){
	// Extension de la table auteurs
	$tables_principales['spip_auteurs']['field']['openid'] = "text DEFAULT '' NOT NULL";
		
	return $tables_principales;
}

?>
