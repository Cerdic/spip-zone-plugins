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
define('_TICKETS_PAGES_CONFIG', 'general:autorisations:typologie');

/**
 * Récupérer dans la base la liste des mots-clés liés au ticket
 * http://marcimat.magraine.net/Crayon-de-mots-pour-un-article
 */
function valeur_champ_mots_ticket($table, $id, $champ) {
	return valeur_champ_mots_objet($table, $id, $champ);
}

/**
 * Récupérer dans la base la liste des mots-clés liés au ticket pour
 * groupe de mots donné
 * http://marcimat.magraine.net/Crayon-de-mots-pour-un-article
 */
function valeur_champ_groupemots_ticket($table, $id, $champ) {
	return valeur_champ_mots_objet($table, $id, $champ);
}

/**
 * Récupérer dans la base la liste des mots-clés liés à l'objet
 * http://marcimat.magraine.net/Crayon-de-mots-pour-un-article
 */
function valeur_champ_mots_objet($table, $ids, $champ) {
	list($id_objet, $id_groupe) = explode('-', $ids);
	list(, $objet) = explode('_', $champ);

	$where = array(
		"m.id_mot = ml.id_mot",
		"ml.id_objet=".sql_quote($id_objet),
		"ml.objet=".sql_quote($objet)
	);
	if ($id_groupe > 0)
		$where[] = "m.id_groupe=" . sql_quote($id_groupe);

	$valeurs = sql_allfetsel("m.id_mot", "spip_mots AS m, spip_mots_liens AS ml", $where);
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
 * Modifier dans la base la liste des mots-clés liés au ticket pour un
 * groupe de mots donné
 * http://marcimat.magraine.net/Crayon-de-mots-pour-un-article
 */
function groupemots_ticket_revision($id, $colonnes, $type_objet) {
	return mots_objet_revision($id, $colonnes, $type_objet, 'groupemots_ticket');
}

/**
 * Modifier dans la base la liste des mots-clés liés à l'objet
 * http://marcimat.magraine.net/Crayon-de-mots-pour-un-article
 */
function mots_objet_revision($ids, $colonnes, $type_objet, $champ = '') {
	if (!$champ) return false;

	list($id_objet, $id_groupe) = explode('-', $ids);
	list(, $type_liaison) = explode('_', $champ);

	/* On vérifie qu'on a le droit d'associer des mots l'objet
	 * il serait mieux de le vérifier pour chaque groupe de mots (voir
	 * les options de la fonction autoriser_associermots_dist)
	 */
	if (!autoriser('associermots',$type_liaison,$id_objet)) return false;

	// actuellement en bdd
	$old = valeur_champ_mots_objet($type_objet, $ids, $champ);
	// ceux qu'on veut maintenant (on vérifie que ce sont des indices)
	$new = array_filter(explode(',', $colonnes[$champ]),'is_numeric');
	// les mots à supprimer
	$del = array_diff($old, $new);
	// les mots à ajouter
	$add = array_diff($new, $old);

	include_spip('action/editer_liens');
	if ($del) {
		objet_dissocier(array('mot'=>$del), array($type_liaison => $id_objet));
	}
	if ($add) {
		objet_associer(array('mot'=>$add), array($type_liaison => $id_objet));
	}

	return true;
}

function ticket_id_assigne_revision($id, $colval = array(), $type = ''){
	$a = crayons_update($id, $colval, $type);

	if ($notifications = charger_fonction('notifications', 'inc')) {
		foreach ($colval as $col => $val) {
			if ($col=="id_assigne") {
				$notifications('assignerticket', $id, array('id_auteur' => $val));
			}
		}
	}
	
	return $a;
}

?>
