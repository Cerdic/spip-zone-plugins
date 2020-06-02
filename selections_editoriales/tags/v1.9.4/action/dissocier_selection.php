<?php

/**
 * Gestion de l'action dissocier_selection
 *
 * @package SPIP\Selections_editoriales\Action
 **/

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


/**
 * Dissocier une sélection
 *
 * @param string $arg
 *     fournit les arguments de la fonction dissocier_selection
 *     sous la forme `$id_objet-$objet-$selection-suppr-safe`
 *
 *     - 4eme arg : suppr = true, false sinon
 *     - 5eme arg : safe = true, false sinon
 *
 * @return void
 */
function action_dissocier_selection_dist($arg = null) {
	if (is_null($arg)) {
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$arg = $securiser_action();
	}

	// attention au cas ou id_objet est negatif !
	if (strncmp($arg, '-', 1) == 0) {
		$arg = explode('-', substr($arg, 1));
		list($id_objet, $objet, $selection) = $arg;
		$id_objet = -$id_objet;
	} else {
		$arg = explode('-', $arg);
		list($id_objet, $objet, $selection) = $arg;
	}

	$suppr = $check = false;
	if (count($arg) > 3 and $arg[3] == 'suppr') {
		$suppr = true;
	}
	if (count($arg) > 4 and $arg[4] == 'safe') {
		$check = true;
	}
	if ($id_objet = intval($id_objet)
		and autoriser('dissocierselections', $objet, $id_objet)
	) {
		dissocier_selection($selection, $objet, $id_objet, $suppr, $check);
	} else {
		spip_log("Interdit de modifier $objet $id_objet", 'spip');
	}
}

/**
 * Supprimer un lien entre une sélection et un objet
 *
 * @param int $id_selection
 * @param string $objet
 * @param int $id_objet
 * @param bool $supprime
 *   si true, la sélection est supprimée si plus liee a aucun objet
 * @param bool $check
 *   si true, on verifie les selections references dans le texte de l'objet
 *   et on les associe si pas deja fait
 * @return bool
 */
function supprimer_lien_selection($id_selection, $objet, $id_objet, $supprime = false, $check = false) {
	if (!$id_selection = intval($id_selection)) {
		return false;
	}

	// [TODO] le mettre en paramètre de la fonction ?
	$serveur = '';

	// D'abord on ne supprime pas, on dissocie
	include_spip('action/editer_liens');
	objet_dissocier(array('selection' => $id_selection), array($objet => $id_objet), array('role' => '*'));

	// Invalider les caches
	include_spip('inc/invalideur');
	suivre_invalideur("id='id_selection/$id_selection'");

	pipeline(
		'post_edition',
		array(
			'args' => array(
				'operation' => 'delier_selection', // compat v<=2
				'action' => 'delier_selection',
				'table' => 'spip_selections',
				'id_objet' => $id_selection,
				'objet' => $objet,
				'id' => $id_objet
			),
			'data' => array()
		)
	);

	if ($check) {
		// si demande, on verifie que ses selections vues sont bien lies !
		$spip_table_objet = table_objet_sql($objet);
		$table_objet = table_objet($objet);
		$id_table_objet = id_table_objet($objet, $serveur);
		$champs = sql_fetsel('*', $spip_table_objet, addslashes($id_table_objet) . '=' . intval($id_objet));

		$marquer_doublons_selection = charger_fonction('marquer_doublons_selection', 'inc');
		$marquer_doublons_selection($champs, $id_objet, $objet, $id_table_objet, $table_objet, $spip_table_objet, '', $serveur);
	}

	// On supprime ensuite s'il est orphelin
	// et si demande
	// ici on ne bloque pas la suppression d'une selection rattachée a un autre
	if ($supprime and !sql_countsel('spip_selections_liens', "objet!='selection' AND id_selection=" . $id_selection)) {
		$supprimer_selection = charger_fonction('supprimer_selection', 'action');

		return $supprimer_selection($id_selection);
	}
}

/**
 * Dissocier une sélection
 *
 * @param int|string $selection
 *   id_selection a dissocier
 * @param  $objet
 *   objet duquel dissocier
 * @param  $id_objet
 *   id_objet duquel dissocier
 * @param bool $supprime
 *   supprimer les sélections orphelines apres dissociation
 * @param bool $check
 *   verifier le texte et relier les sélections referencees dans l'objet
 * @return void
 */
function dissocier_selection($selection, $objet, $id_objet, $supprime = false, $check = false) {
	if ($id_selection = intval($selection)) {
		supprimer_lien_selection($selection, $objet, $id_objet, $supprime, $check);
	}

	// pas tres generique ca ...
	if ($objet == 'rubrique') {
		include_spip('inc/rubriques');
		depublier_branche_rubrique_if($id_objet);
	}
}
