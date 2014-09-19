<?php
/**
 * Définit les fonctions du plugin Info Sites
 *
 * @plugin     Info Sites
 * @copyright  2014
 * @author     Teddy Payet
 * @licence    GNU/GPL
 * @package    SPIP\Info_Sites\Fonctions
 */

if (!defined('_ECRIRE_INC_VERSION')) {
    return;
}


include_spip('inc/filtres_ecrire');
include_spip('base/abstract_sql');
include_spip('base/objets');

function lister_tables_liens ()
{
	$tables_auxilaires = lister_tables_auxiliaires();
	$tables_auxilaires_objets = array();

	foreach ($tables_auxilaires as $key => $table_auxilaire) {
		if (isset($table_auxilaire['field']['objet'])) {
			$tables_auxilaires_objets[] = $key;
		}
	}
	return $tables_auxilaires_objets;
}

function nb_elements ($table, $where = '') {
	return sql_countsel($table, $where);
}

function nb_organisations ($where = '')
{
	return nb_elements('spip_organisations', $where);
}

function nb_projets ($where = '')
{
	return nb_elements('spip_projets', $where);
}

function nb_projets_sites ($where = '')
{
	return nb_elements('spip_projets_sites', $where);
}

function nb_projets_sites_types ($type_site = 'prod')
{
	return nb_projets_sites("type_site='" . $type_site . "'");
}

function nb_projets_cadres ($where = '')
{
	return nb_elements('spip_projets_cadres', $where);
}

function nb_contacts ($where = '')
{
	return nb_elements('spip_contacts', $where);
}

?>