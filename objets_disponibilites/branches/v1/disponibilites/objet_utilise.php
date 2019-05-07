<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Calcul l'utilisation de l'objet
 *
 * @param string $utilise_objet
 *   L'objet pour le quel on calcul la disponibilité.
 * @param array $contexte
 *   Les données du contexte.
 *   les optiosn suivants sont nécessaires:
 *   - objet
 *   - id_objet
 *   - statuts
 *
 * @return array
 *   Les dates utilisées.
 */
function disponibilites_objet_utilise_dist($utilise_objet, $contexte) {
	include_spip('filtres/dates_outils');

	$horaire = isset($contexte['horaire']) ? $contexte['horaire'] : _request('horaire');
	$format = isset($contexte['format']) ? $contexte['format'] : _request('format');
	$objet = $contexte['objet'];
	$id_objet = $contexte['id_objet'];
	$statuts = $contexte['utilise_statuts'];

	if ($contexte['utilise_select']) {
		$select = $contexte['utilise_select'];
	}
	else {
		$select = 'date_debut,date_fin';
	}

	if ($contexte['utilise_where']) {
		$where = $contexte['utilise_where'];
	}
	else {
		if ($statuts) {
			if (!is_array($statuts)) {
				$statuts = explode(',', $statuts);
			}
			$statut = 'statut IN ("' . implode('","', $statuts) . '")';
		}
		else {
			$statut = 'statut NOT LIKE ' . sql_quote('poubelle');
		}
		$where = 'objet LIKE ' . sql_quote($objet) . ' AND id_objet=' . $id_objet . ' AND ' . $statut;
	}
	$dates = [];

	if ($table = table_objet_sql($utilise_objet)) {

	$utilisation = sql_allfetsel($select, $table , $where);

	foreach($utilisation AS $donnees) {

		$date_debut = $donnees['date_debut'];
		$date_fin = $donnees['date_fin'];

		if (isset($contexte['verifier']) AND $contexte['verifier']) {
			if (dates_difference($date_debut, $date_fin, 'jour') == 1) {
				$contexte['utilise_decalage_fin'] = 0;
			}
		}

		if ($date_debut != $date_fin) {

			$intervalle = dates_intervalle(
				$date_debut,
				$date_fin,
				$contexte['utilise_decalage_debut'],
				$contexte['utilise_decalage_fin'],
				$horaire,
				$format
			);

			$dates = array_merge($dates, $intervalle);
			}
		}
	}
	return $dates;
}
