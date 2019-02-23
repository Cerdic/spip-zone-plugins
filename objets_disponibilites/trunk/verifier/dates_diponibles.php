<?php

function verifier_dates_diponibles_dist($valeur, $options=array()){
		include_spip('filtres/dates_outils');
	if (is_array($valeur)) {
		$date_debut = isset($valeur['date_debut']) ? $valeur['date_debut'] : (isset($valeur[0]) ? $valeur[0] : '' );
		$date_fin = isset($valeur['date_fin']) ? $valeur['date_fin'] : (isset($valeur[1]) ? $valeur[1] : $date_debut);
	}
	else {
		$date_debut = $date_fin = $valeur;
	}

	$objet = isset($options['objet']) ? $options['objet'] : FALSE;
	$id_objet = isset($options['id_objet']) ?  $options['id_objet'] : FALSE;

	if ($date_debut and $date_fin and $objet and $id_objet) {
		$horaire = isset($options['horaire']) ? $options['horaire'] : FALSE;
		$format = isset($options['format']) ? $options['format'] : ($horaire ? 'd-m-Y H:i:s' : 'd-m-Y');
		$utilisation_squelette = isset($options['utilisation_squelette']) ? $options['utilisation_squelette'] : FALSE;
		$utilisation_id_exclu = isset($options['utilisation_id_exclu']) ? $options['utilisation_id_exclu'] : FALSE;

		$decalage_debut = isset($options['decalage_debut']) ?  $options['decalage_debut'] : 0;
		$decalage_fin = isset($options['decalage_fin']) ?  $options['decalage_fin'] : 0;
		$intervalle = dates_intervalle($date_debut, $date_fin, 1, -1, $horaire, $format);

		$disponible = dates_disponibles(
			array(
					'objet' => $objet ,
					'id_objet' => $id_objet,
					'decalage_debut' => 0,
					'decalage_fin' => 0,
					'date_limite_debut' => $date_debut,
					'date_limite_fin' => $date_fin,
					'utilisation_squelette' => $utilisation_squelette,
					'utilisation_id_exclu' => $utilisation_id_exclu,
					'format' => $format,
				)
			);

		$difference = array_diff($intervalle, $disponible);

		if (count($difference) > 0) {
			return _T('objets_location:erreur_jours_indisponible', array('jours' => implode(', ', $difference)));
		}
		else {
			return '';
		}
	}
	else {
		return 'Problèmes de variables, vérifiez votre fonction';
	}

}
