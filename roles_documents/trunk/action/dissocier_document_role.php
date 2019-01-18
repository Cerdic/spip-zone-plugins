<?php
/**
 * Action : Dissocier (et optionnellement supprimer) un document en tenant compte ses rôles
 * 
 * - Si un rôle est précisé, on ne dissocie que ce rôle
 * - Si aucun rôle n'est précisé, on dissocie tous les rôles sauf ceux principaux (=logos)
 *
 * @note
 * Adapation de action/dissocier_document du plugin médias.
 * Ajout d'un argument 'role' supplémentaire.
 * 
 * @plugin     Rôles de documents
 * @copyright  2015-2018
 * @author     tcharlss
 * @licence    GNU/GPL
 * @package    SPIP\Roles_documents\Action
 */

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


/**
 * Dissocier (et optionnellement supprimer) un document en tenant compte ses rôles
 *
 * @param string $arg
 *     fournit les arguments de la fonction dissocier_document_roles
 *     sous la forme `$id_objet-$objet-$id_document-$role-suppr-safe`
 *
 *     - 4eme arg : si `suppr`, le document est supprimé si plus lié à aucun objet
 *     - 5eme arg : si `safe`, on vérifie les documents référencés dans le texte de l'objet et on les associe si pas déjà fait
 *
 * @return void
 */
function action_dissocier_document_role_dist($arg = null) {
	if (is_null($arg)) {
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$arg = $securiser_action();
		//$arg = _request('arg'); // temporaire pendant le dev
	}

	include_spip('inc/autoriser');

	// attention au cas ou id_objet est negatif !
	if (strncmp($arg, '-', 1) == 0) {
		$arg = explode('-', substr($arg, 1));
		list($id_objet, $objet, $document, $role) = $arg;
		$id_objet = -$id_objet;
	} else {
		$arg = explode('-', $arg);
		list($id_objet, $objet, $document, $role) = $arg;
	}

	$suppr = $check = false;
	if (count($arg) > 4 and $arg[4] == 'suppr') {
		$suppr = true;
	}
	if (count($arg) > 5 and $arg[5] == 'safe') {
		$check = true;
	}
	if ($id_objet = intval($id_objet)
		and (
			($id_objet < 0 and $id_objet == -$GLOBALS['visiteur_session']['id_auteur'])
			or autoriser('dissocierdocuments', $objet, $id_objet)
		)
	) {
		dissocier_document_role($document, $objet, $id_objet, $role, $suppr, $check);
	} else {
		spip_log("Interdit de modifier $objet $id_objet", 'spip');
	}
}

/**
 * Supprimer un lien entre un document et un objet
 *
 * @param int $id_document
 * @param string $objet
 * @param int $id_objet
 * @param bool $supprime
 *   si true, le document est supprime si plus lie a aucun objet
 * @param bool $check
 *   si true, on verifie les documents references dans le texte de l'objet
 *   et on les associe si pas deja fait
 * @return bool
 */
function supprimer_lien_document_role($id_document, $objet, $id_objet, $role, $supprime = false, $check = false) {
	if (!$id_document = intval($id_document)) {
		return false;
	}

	// [TODO] le mettre en paramètre de la fonction ?
	$serveur = '';

	// D'abord on dissocie
	include_spip('action/editer_liens');
	include_spip('roles_documents_fonctions');
	// 1) Pas de suppression et rôle : on ne dissocie que ce rôle
	if (!$supprime and $role) {
		objet_dissocier(array('document' => $id_document), array($objet => $id_objet), array('role' => $role));
	// 2) Pas de suppression et pas de rôle : on dissocie tous les rôles non principaux (conserver les logos quoi)
	// l'API editer_liens ne semble pas accespter un tableau de plusieurs rôles dans la condition,
	// on fait dans une requête à la mano.
	} elseif(!$supprime and !$role) {
		if ($roles_non_principaux = roles_documents_presents_sur_objet($objet, $id_objet, 0, false)) {
			include_spip('base/abstract_sql');
			sql_delete(
				'spip_documents_liens',
				array(
					'id_document=' . intval($id_document),
					'objet=' . sql_quote($objet),
					'id_objet=' . intval($id_objet),
					sql_in('role', $roles_non_principaux['possibles']),
				)
			);
		}
	// 3) Suppression : on dissocie tout
	} else {
		objet_dissocier(array('document' => $id_document), array($objet => $id_objet), array('role' => '*'));
	}

	// Si c'est une vignette, l'eliminer du document auquel elle appartient
	// cas tordu peu probable
	sql_updateq('spip_documents', array('id_vignette' => 0), 'id_vignette=' . $id_document);

	// verifier son statut apres une suppression de lien
	include_spip('action/editer_document');
	document_instituer($id_document);

	// Invalider les caches
	include_spip('inc/invalideur');
	suivre_invalideur("id='id_document/$id_document'");

	pipeline(
		'post_edition',
		array(
			'args' => array(
				'operation' => 'delier_document', // compat v<=2
				'action' => 'delier_document',
				'table' => 'spip_documents',
				'id_objet' => $id_document,
				'objet' => $objet,
				'id' => $id_objet,
				'role' => $role,
			),
			'data' => null
		)
	);

	if (
		$check
		and $objet != 'site_spip' // pas de vérif pour le faux objet utilisé pour le logo du site
	) {
		// si demande, on verifie que ses documents vus sont bien lies !
		$spip_table_objet = table_objet_sql($objet);
		$table_objet = table_objet($objet);
		$id_table_objet = id_table_objet($objet, $serveur);
		$champs = sql_fetsel('*', $spip_table_objet, addslashes($id_table_objet) . '=' . intval($id_objet));

		$marquer_doublons_doc = charger_fonction('marquer_doublons_doc', 'inc');
		$marquer_doublons_doc($champs, $id_objet, $objet, $id_table_objet, $table_objet, $spip_table_objet, '', $serveur);
	}

	// On supprime ensuite s'il est orphelin
	// et si demande
	// ici on ne bloque pas la suppression d'un document rattache a un autre
	if ($supprime and !sql_countsel('spip_documents_liens', "objet!='document' AND id_document=" . $id_document)) {
		return;
		$supprimer_document = charger_fonction('supprimer_document', 'action');

		return $supprimer_document($id_document);
	}
}

/**
 * Dissocier un ou des documents
 *
 * @param int|string $document
 *   id_document a dissocier
 *   I/image pour dissocier les images en mode Image
 *   I/document pour dissocier les images en mode document
 *   D/document pour dissocier les documents non image en mode document
 * @param string $objet
 *   objet duquel dissocier
 * @param  $id_objet
 *   id_objet duquel dissocier
 * @param bool $supprime
 *   supprimer les documents orphelins apres dissociation
 * @param bool $check
 *   verifier le texte des documents et relier les documents references dans l'objet
 * @return void
 */
function dissocier_document_role($document, $objet, $id_objet, $role, $supprime = false, $check = false) {
	if ($id_document = intval($document)) {
		supprimer_lien_document_role($id_document, $objet, $id_objet, $role, $supprime, $check);
	} else {
		list($image, $mode) = explode('/', $document);
		$image = ($image == 'I');
		$typdoc = sql_in('docs.extension', array('gif', 'jpg', 'png'), $image ? '' : 'NOT');

		$obj = 'id_objet=' . intval($id_objet) . ' AND objet=' . sql_quote($objet);

		$s = sql_select(
			'docs.id_document',
			'spip_documents AS docs LEFT JOIN spip_documents_liens AS l ON l.id_document=docs.id_document',
			"$obj AND vu='non' AND docs.mode=" . sql_quote($mode) . " AND $typdoc"
		);
		while ($t = sql_fetch($s)) {
			supprimer_lien_document_role($t['id_document'], $objet, $id_objet, $role, $supprime, $check);
		}
	}

	// pas tres generique ca ...
	if ($objet == 'rubrique') {
		include_spip('inc/rubriques');
		depublier_branche_rubrique_if($id_objet);
	}
}
