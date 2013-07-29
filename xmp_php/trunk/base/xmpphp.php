<?php
/**
 * XMP php
 * Récupération des métadonnées XMP
 *
 * Auteur : kent1
 * ©2011-2013 - Distribué sous licence GNU/GPL
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

function xmpphp_declarer_tables_principales($tables_principales){
	$tables_principales['spip_documents']['field']['metas'] = "TEXT DEFAULT '' NOT NULL";
	return $tables_principales;
}

?>