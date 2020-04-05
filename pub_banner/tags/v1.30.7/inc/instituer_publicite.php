<?php

/**
 * Création d'une publicite
 */
function inc_instituer_publicite_dist($data, $statut='1inactif') {
	if (!is_array($data)) return;
	include_spip('base/abstract_sql');
	if (!isset($data['date_add']) ) $data['date_add'] = date('Y-m-d H:i:s');
	$data['statut'] = $statut;
	if ( $id_publicite = sql_insertq('spip_publicites', $data, '') ) {
		// Envoyer aux plugins (notamment media)
		pipeline('post_insertion',
			array(
				'args' => array(
					'table' => 'spip_publicites',
					'id_objet' => $id_publicite
				),
				'data' => $data
			)
		);
		return $id_publicite;
	}
	return false;
}

?>