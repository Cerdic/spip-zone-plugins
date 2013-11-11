<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

function generer_url_ecrire_quickvote($id, $args='', $ancre='', $statut='', $connect=''){
	$a = "id_quickvote=" . intval($id);
	$h = (!$statut OR $connect)
	? generer_url_entite_absolue($id, 'quickvote', $args, $ancre, $connect)
	: (generer_url_ecrire('quickvote', $a . ($args ? "&$args" : ''))
		. ($ancre ? "#$ancre" : ''));
	return $h;
}





?>
