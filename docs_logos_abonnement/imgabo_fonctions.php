<?php


function generer_url_ecrire_abonnement($id, $args='', $ancre='', $statut='', $connect='') {
	$a = "id_abonnement=" . intval($id);
	$h = ($connect)
	?  generer_url_entite_absolue($id, 'abonnement', $args, $ancre, $connect)
	: (generer_url_ecrire('abonnement_edit', $a . ($args ? "&$args" : ''))
		. ($ancre ? "#$ancre" : ''));
	return $h;
}
?>