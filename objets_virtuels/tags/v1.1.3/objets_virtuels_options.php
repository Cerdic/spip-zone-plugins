<?php
/**
 * Options utiles au plugin Objets virtuels
 *
 * @plugin     Objets virtuels
 * @copyright  2017
 * @author     Matthieu Marcillaud
 * @licence    GNU/GPL
 * @package    SPIP\Objets_virtuels\Options
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

// utiliser ces pipelines a part
// afin d'etre certain d'arriver apres les autres plugins
// sinon toutes les tables ne sont pas declarees
// et les champs supplementaires ne peuvent pas se declarer comme il faut

if (!isset($GLOBALS['spip_pipeline']['declarer_tables_objets_sql'])) {
	$GLOBALS['spip_pipeline']['declarer_tables_objets_sql'] = '';
}

$GLOBALS['spip_pipeline']['declarer_tables_objets_sql'] .= '||objets_virtuels_declarer_champs_apres_les_autres';

/**
 * Ajouter les déclaration dechamps extras sur les objets éditoriaux
 *
 * @pipeline declarer_tables_objets_sql
 * @see cextras_declarer_tables_objets_sql()
 * @param array $tables
 *     Description des objets éditoriaux
 * @return array
 *     Description des objets éditoriaux
 **/
function objets_virtuels_declarer_champs_apres_les_autres($tables) {
	include_spip('objets_virtuels_pipelines');
	return objets_virtuels_declarer_tables_objets_sql($tables);
}
