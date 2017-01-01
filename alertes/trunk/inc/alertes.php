<?php
/*
 * Plugin Alertes
 * Distribué sous licence GPL
 *
 * Fonctions reprise du plugin Mes favoris de Olivier Sallou, Cedric Morin.
 */

/**
 * Cette fonction permet de supprimer une alerte.
 * Il faut lui passer en argument un tableau contenant le `WHERE` de la requête SQL.
 *
 * @example
 *      alertes_supprimer(array('id_alerte' => 2));
 *
 * @param array $paires
 */
function alertes_supprimer($paires) {
	if (is_array($paires) and count($paires)) {
		$cond = array();
		foreach ($paires as $k => $v) {
			$cond[] = "$k=" . sql_quote($v);
		}
		$cond = implode(' AND ', $cond);
		$res = sql_select('id_alerte,objet,id_objet,id_auteur', 'spip_alertes', $cond);
		include_spip('inc/invalideur');
		while ($row = sql_fetch($res)) {
			sql_delete("spip_alertes", "id_alerte=" . intval($row['id_alerte']));
			suivre_invalideur("alerte/" . $row['objet'] . "/" . $row['id_objet']);
			suivre_invalideur("alerte/auteur/" . $row['id_auteur']);
		}
	}
}

/**
 * Ajouter une nouvelle alerte pour un auteur donné.
 *
 * @param int $id_objet
 * @param string $objet
 * @param int $id_auteur
 *
 * @return bool
 *      true : Si l'alerte a bien été ajouté
 *      false : Si l'alerte existe déjà en base ou si les arguments passés en paramètre ne sont pas conforme.
 */
function alertes_ajouter($id_objet, $objet, $id_auteur) {
	include_spip('inc/utils');
	if ($id_auteur
		AND $id_objet = intval($id_objet)
		AND preg_match(",^\w+$,", $objet)
	) {

		if (!alertes_trouver($id_objet, $objet, $id_auteur)) {
			sql_insertq("spip_alertes", array('id_auteur' => $id_auteur, 'id_objet' => $id_objet, 'objet' => $objet));
			include_spip('inc/invalideur');
			suivre_invalideur("alerte/$objet/$id_objet");
			suivre_invalideur("alerte/auteur/$id_auteur");
			spip_log("L'alerte $id_objet-$objet-$id_auteur a été ajouté.", "alertes");

			return true;
		}
		spip_log("Erreur ajouter alerte $id_objet-$objet-$id_auteur, l'alerte existe déjà.", "alertes");

		return false;
	} else {
		spip_log("Erreur ajouter alerte $id_objet-$objet-$id_auteur", "alertes");

		return false;
	}
}

/**
 * Recherche l'alerte lié à l'auteur et l'objet en court.
 *
 * @param int $id_objet
 * @param string $objet
 * @param int $id_auteur
 *
 * @return array|bool
 */
function alertes_trouver($id_objet, $objet, $id_auteur) {
	$row = false;
	if ($id_auteur = intval($id_auteur)
		AND $id_objet = intval($id_objet)
		AND preg_match(",^\w+$,", $objet)
	) {
		$row = sql_fetsel("*", "spip_alertes",
			"id_auteur=" . intval($id_auteur) . " AND id_objet=" . intval($id_objet) . " AND objet=" . sql_quote($objet));
	}

	return $row;
}

