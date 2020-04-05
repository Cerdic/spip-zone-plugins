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


if (!defined('CONTACTS_SUPPRESSIONS_RECIPROQUES_AVEC_AUTEURS')) {
/**
 * Option pour supprimer les auteurs si un contact est supprimé et réciproquement.
 *
 * Il peut être demandé que si un auteur est mis à la poubelle, sa fiche contact (ou organisation)
 * associée soit supprimée (par cron, après la suppression effective de l'auteur en base). 
 * Inversement, lorsqu'un contact (ou organisation) est supprimée, si l'option est activée,
 * l'auteur associé est alors mis à la poubelle.
 *
 * Déclarer à `true` pour l'activer.
 * 
 * @note
 *     Ce define est simplement actif dans la branche SPIP 2.1.
 *     Le plugin C&O pour SPIIP 3 a un véritable formulaire de configuration interfacé.
**/
define('CONTACTS_SUPPRESSIONS_RECIPROQUES_AVEC_AUTEURS', false);
}

?>
