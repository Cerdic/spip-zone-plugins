<?php
/**
 * MediaSPIP player
 * Lecteur multimédia HTML5 pour MediaSPIP
 *
 * Auteurs :
 * kent1 (kent1@arscenic.info)
 * 2010-2012 - Distribué sous licence GNU/GPL
 *
 * Définition des tables
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

function mediaspip_player_declarer_tables_principales($tables_principales){
	/**
	 * On ajoute le champ id_orig dans la base qui peut être utilisé par d'autres plugins,
	 * SPIPmotion par exemple. Uniquement pour éviter des contorsions au niveaux de critères de boucle
	 */
	$tables_principales['spip_documents']['field']['id_orig'] = "BIGINT(21) NOT NULL";

	return $tables_principales;
}

?>