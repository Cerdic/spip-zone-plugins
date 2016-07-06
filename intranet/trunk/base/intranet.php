<?php
/**
 * Plugin Intranet
 *
 * (c) 2013-2016 kent1
 * Distribue sous licence GPL
 *
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Déclarer les tables auxiliaires
 *
 * @pipeline declarer_tables_auxiliaires
 *
 * @param array $tables_auxiliaires
 *     Description des tables
 * @return array
 *     Description complétée des tables
 */
function intranet_declarer_tables_auxiliaires($tables_auxiliaires) {
	//-- Table organisations_liens -------------------------------------
	$intranet_ouverts = array(
		'id_objet'        => 'BIGINT(21) NOT NULL',
		'objet'           => 'VARCHAR(25) NOT NULL'
	);
	$intranet_ouverts_key = array(
		'PRIMARY KEY'         => 'id_objet, objet',
		'KEY id_objet'        => 'id_objet',
		'KEY objet'           => 'objet'
	);
	$tables_auxiliaires['spip_intranet_ouverts'] =
		array('field' => &$intranet_ouverts, 'key' => &$intranet_ouverts_key);

	return $tables_auxiliaires;
}
