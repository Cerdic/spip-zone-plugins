<?php
/**
 * Déclaration des champs SQL pour Date de modification manuelle
 *
 * @plugin     Date de modification manuelle
 * @copyright  2017
 * @author     Matthieu Marcillaud
 * @licence    GNU/GPL
 * @package    SPIP\Date_modif_manuelle\Pipelines
 */

/**
 * Déclaration des champs SQL
 *
 * Déclarer les champs additionnels `date_modif_manuelle`
 *
 * @param array $tables
 *     Définition de tous les objets éditoriaux
 * @return array $tables
 *     Définition (complétée) de tous les objets éditoriaux
 */
function date_modif_manuelle_declarer_tables_objets_sql($tables)
{
	$tables_date_modif_manuelle = array('spip_articles');
	include_spip('inc/config');

	foreach($tables_date_modif_manuelle as $table) {
		$tables[$table]['field'] += array(
			'date_modif_manuelle' => "datetime DEFAULT '0000-00-00 00:00:00' NOT NULL",
		);
	}

	return $tables;
}
