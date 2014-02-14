<?php
/**
 * Plugin Tickets
 * Licence GPL (c) 2008-2013
 *
 * @package SPIP\Tickets\Options
 */
 
if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Activer le plugin no_spam sur les tickets
 */
$GLOBALS['formulaires_no_spam'][] = 'editer_ticket';
// Liste des pages de configuration dans l'ordre de presentation
define('_TICKETS_PAGES_CONFIG', 'general:autorisations');

/**
 * Récupérer dans la base la liste des mots-clés liés au ticket
 * http://marcimat.magraine.net/Crayon-de-mots-pour-un-article
 */
function valeur_champ_mots_ticket($table, $id, $champ) {
	return valeur_champ_mots_objet($table, $id, $champ);
}

/**
 * Récupérer dans la base la liste des mots-clés liés à l'objet
 * http://marcimat.magraine.net/Crayon-de-mots-pour-un-article
 */
function valeur_champ_mots_objet($table, $id, $champ) {
	list(, $objet) = explode('_', $champ);

	$valeurs = sql_allfetsel("m.id_mot", "spip_mots AS m, spip_mots_liens AS ml", array(
		"m.id_mot = ml.id_mot",
		"ml.id_objet=".sql_quote($id),
		"ml.objet=".sql_quote($objet)
	));
	$valeurs = array_map('array_shift', $valeurs);

	return $valeurs;
}

/**
 * Modifier dans la base la liste des mots-clés liés au ticket
 * http://marcimat.magraine.net/Crayon-de-mots-pour-un-article
 */
function mots_ticket_revision($id, $colonnes, $type_objet) {
	return mots_objet_revision($id, $colonnes, $type_objet, 'mots_ticket');
}

/**
 * Modifier dans la base la liste des mots-clés liés à l'objet
 * http://marcimat.magraine.net/Crayon-de-mots-pour-un-article
 */
function mots_objet_revision($id, $colonnes, $type_objet, $champ = '') {
	if (!$champ) return false;

	list(, $type_liaison) = explode('_', $champ);

	// actuellement en bdd
	$old = valeur_champ_mots_objet($type_objet, $id, $champ);
	// ceux qu'on veut maintenant (on vérifie que ce sont des indices)
	$new = array_filter(explode(',', $colonnes[$champ]),'is_numeric');
	// les mots à supprimer
	$del = array_diff($old, $new);
	// les mots à ajouter
	$add = array_diff($new, $old);

	include_spip('action/editer_liens');
	if ($del) {
		objet_dissocier(array('mot'=>$del), array($type_liaison => $id));
	}
	if ($add) {
		objet_associer(array('mot'=>$add), array($type_liaison => $id));
	}

	return true;
}

?>
