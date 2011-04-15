<?php

function generer_url_ecrire_organisation($id, $args='', $ancre='', $statut='', $connect=''){
	$a = "id_organisation=" . intval($id);
	$h = (!$statut OR $connect)
	? generer_url_entite_absolue($id, 'organisation', $args, $ancre, $connect)
	: (generer_url_ecrire('organisation', $a . ($args ? "&$args" : ''))
		. ($ancre ? "#$ancre" : ''));
	return $h;
}


function generer_url_ecrire_contact($id, $args='', $ancre='', $statut='', $connect=''){
	$a = "id_contact=" . intval($id);
	$h = (!$statut OR $connect)
	? generer_url_entite_absolue($id, 'contact', $args, $ancre, $connect)
	: (generer_url_ecrire('contact', $a . ($args ? "&$args" : ''))
		. ($ancre ? "#$ancre" : ''));
	return $h;
}



?>
