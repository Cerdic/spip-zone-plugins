<?php

/**
 * Fonctions de lecture, modification, suppression des couleurs d’objets
 */


/**
 * Trouver la couleur d'un objet ou de son parent
 *
 * @uses objet_couleur_parent()
 *
 * @api
 * @param string $objet
 *     Type de l'objet
 * @param int $id_objet
 *     Identifiant de l'objet
 * @param boolean $fallback_parent
 *     true pour chercher la couleur du parent en fallback
 * @param boolean $fallback_recursif
 *     true pour chercher les parents récursivement
 * @return string|false
 *     La couleur ou false si rien trouvé
 */
function objet_lire_couleur($objet, $id_objet, $fallback_parent = false, $fallback_recursif = false) {
	include_spip('base/objets');

	$objet = objet_type($objet);
	$id_objet = intval($id_objet);

	$couleur_objet = sql_getfetsel(
		'couleur_objet',
		'spip_couleur_objet_liens',
		array(
			'objet=' . sql_quote($objet),
			'id_objet=' . sql_quote($id_objet)
		)
	);

	// Si besoin, on prend la couleur du parent
	if (!$couleur_objet and $fallback_parent) {
		$couleur_objet = objet_couleur_parent($objet, $id_objet, $fallback_recursif);
	}

	return $couleur_objet;
}


/**
 * Modifier la couleur d’un objet
 *
 * @api
 * @param string $objet
 *     Type de l'objet
 * @param int $id_objet
 *     Identifiant de l'objet
 * @param string $couleur_objet
 *     Couleur à définir
 * @return bool true si ok.
 */
function objet_modifier_couleur($objet, $id_objet, $couleur_objet) {
	$objet = objet_type($objet);
	$id_objet = intval($id_objet);

	// si la ligne $id_objet / $objet existe, on actualise, sinon on insère
	if (sql_countsel('spip_couleur_objet_liens', array(
		"objet=" . sql_quote($objet),
		"id_objet=" . sql_quote($id_objet),
	))) {
		return (bool) sql_updateq(
			'spip_couleur_objet_liens', 
			array(
				'couleur_objet' => $couleur_objet
			), 
			array(            
				"objet=" . sql_quote($objet),
				"id_objet=" . sql_quote($id_objet)
			)
		);
	} else {
		return (bool) sql_insertq(
			'spip_couleur_objet_liens', 
			array(
				'id_objet' => $id_objet, 
				'objet' => $objet, 
				'couleur_objet' => $couleur_objet
			)
		);
	}
}

/**
 * Supprimer la couleur d’un objet
 *
 * @api
 * @param string $objet
 *     Type de l'objet
 * @param int $id_objet
 *     Identifiant de l'objet
 * @return bool true si une suppression est réalisée.
 */
function objet_supprimer_couleur($objet, $id_objet) {
	$objet = objet_type($objet);
	$id_objet = intval($id_objet);

	return (bool) sql_delete(
		"spip_couleur_objet_liens", 
		array(
			"objet=" . sql_quote($objet),
			"id_objet=" . sql_quote($id_objet)
		)
	);
}

/**
 * Trouver la couleur du parent d'un objet
 *
 * @note
 * Nécéssite l'API de déclaration des parents
 *
 * @uses objet_trouver_parent()
 * 
 * @param string $objet
 *     Type de l'objet
 * @param int $id_objet
 *     Identifiant de l'objet
 * @param boolean $recursif
 *     true pour chercher les parents récursivement
 * @return string|null
 *     La couleur ou null si rien trouvé
 */
function objet_couleur_parent($objet, $id_objet, $recursif = false) {

	$couleur = false;

	// Uniquement si l'API existe
	if (
		include_spip('base/objets_parents')
		and $parent = objet_trouver_parent($objet, $id_objet)
	) {
		$couleur = sql_getfetsel(
			'couleur_objet',
			'spip_couleur_objet_liens',
			array (
				'objet = ' . sql_quote($parent['objet']),
				'id_objet = ' . intval($parent['id_objet']),
			)
		);

		// Si besoin on cherche récursivement
		if (!$couleur and $recursif) {
			$couleur = objet_couleur_parent($parent['objet'], $parent['id_objet'], true);
		}
	}

	return $couleur;
}