<?php
/**
 * Déclarer le champ supplémentaire sur spip_articles
 *
 * @plugin     Pages Uniques
 * @copyright  2013
 * @author     RastaPopoulos
 * @licence    GNU/GPL
 * @package    SPIP\Pages\Pipelines
 * @link       http://contrib.spip.net/Pages-uniques
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

function pages_declarer_tables_objets_sql($tables){

	$tables['spip_articles']['field']['page'] = "VARCHAR(255) DEFAULT '' NOT NULL";
	return $tables;

}

?>
