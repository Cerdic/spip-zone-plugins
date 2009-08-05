<?php

// exec/export_auteurs.php

/**
 * Copyright (c) 2009 Christian Paulus
 * Dual licensed under the MIT and GPL licenses.
 * */

// $LastChangedRevision$
// $LastChangedBy$
// $LastChangedDate$

include_spip('inc/exau_api');

/**
 * Version sans javascript/ajax
 * @return string
 * @param string $flux
 */
function exec_exau_export_auteurs_dist () {

	global $connect_id_auteur, $connect_statut;

	$statut = _request('statut');

	if(
		($statut = exau_statut_correct ($statut))
		&& autoriser('voir', 'auteur', $connect_id_auteur)) {
	
		include_spip('inc/exau_api');
		
		exau_exporter($statut);
		
	}

	return(true);
}


?>