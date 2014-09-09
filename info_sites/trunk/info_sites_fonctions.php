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

function lister_tables_liens () {
	$tables_auxilaires = lister_tables_auxiliaires();
	$tables_auxilaires_objets = array();

	foreach ($tables_auxilaires as $key => $table_auxilaire) {
		if (isset($table_auxilaire['field']['objet'])) {
			$tables_auxilaires_objets[] = $key;
		}
	}
	return $tables_auxilaires_objets;
}
?>