<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function disponibilites_objet_utilise_dist($utilisation_objet, $contexte) {
	include_spip('filtres/dates_outils');

	$horaire = isset($contexte['horaire']) ? $contexte['horaire'] : _request('horaire');
	$format = isset($contexte['format']) ? $contexte['format'] : _request('format');
	$objet = $contexte['objet'];
	$id_objet = $contexte['id_objet'];

	if (isset($options['select'])) {
		$select = $options['select'];
	}
	else {
		$select = 'date_debut,date_fin';
	}

	if (isset($options['where'])) {
		$where = $options['where'];
	}
	else {
		$where = 'objet LIKE ' . sql_quote($objet) . ' AND id_objet=' . $id_objet . ' AND statut NOT LIKE ' . sql_quote('poubelle');
	}
	$dates = [];

	if ($table = table_objet_sql($utilisation_objet)) {
	$utilisation = sql_allfetsel($select, $table , $where);

	foreach($utilisation AS $donnees) {

		$date_debut = $donnees['date_debut'];
		$date_fin = $donnees['date_fin'];

		if (isset($contexte['verifier']) AND $contexte['verifier']) {
			if (dates_difference($date_debut, $date_fin, 'jour') == 1) {
				//$contexte['utilise_decalage_debut'] = 0;
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
