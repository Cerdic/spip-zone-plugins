<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

function genie_parrainage_contacts_dist(){
	include_spip('inc/config');
	$delai = lire_config('parrainage/delai_sans_nouvelles', 30);
	$date_max = date('Y-m-d H:i:s', time() - ($delai*24*3600));
	
	// On modifie le statut de tous les filleuls invités depuis trop longtemps et qui ne se sont toujours pas inscrits
	$ok = sql_updateq(
		'spip_filleuls',
		array(
			'statut' => 'sans_nouvelles'
		),
		array(
			'statut = '.sql_quote('invite'),
			'date_invitation < '.sql_quote($date_max)
		)
	);
	
	if ($ok !== false) return 1;
	else return -3600;
}

?>
