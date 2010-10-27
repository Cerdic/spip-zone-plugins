<?php


function generer_url_ecrire_evenement($id, $args='', $ancre='', $statut='', $connect='') {
	$a = "id_evenement=" . intval($id);
	$h = ($connect)
	?  generer_url_entite_absolue($id, 'evenement', $args, $ancre, $connect)
	: (generer_url_ecrire('evenements_edit', $a . ($args ? "&$args" : ''))
		. ($ancre ? "#$ancre" : ''));
	return $h;
}
?>