<?php


if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Effacer régulièrement les évènements obsolètes.
 * Si l'évènement possède une répetition, prendre l'évènement récent qui dépasse cette répetition
**/
function genie_agenda_effacer_evenements_passes($t) {
	include_spip('inc/config');
	// Est-ce qu'il faut effacer des choses
	if (!lire_config('agenda/effacer_evenements_passes','')
	  or ($delay = intval(lire_config('agenda/delai_effacer_evenements_passes')))<1
	  ) {
		return 1;
	}

	// Rechercher tous les évènements passés
	$date_before = affdate('Y-m-d H:i:s', strtotime("-{$delay} days"));
	$res = sql_select('id_evenement, date_fin', 'spip_evenements', 'date_fin<' . sql_quote($date_before));
	while ($row = sql_fetch($res)) {
		$id_evenement = $row['id_evenement'];
		$date_fin = $row['date_fin'];

		// Rechercher la première occurence d'une répetition de cet événement qui ne soit pas encore passée
		$premier_occurence_repetition = sql_getfetsel('id_evenement', 'spip_evenements', 'date_fin>=' .sql_quote($date_before). 'AND id_evenement_source='.intval($id_evenement),'','date_debut ASC', '0,1');
		if ($premier_occurence_repetition) {
			sql_updateq('spip_evenements', array('id_evenement_source' => $premier_occurence_repetition), 'id_evenement_source='.intval($id_evenement));
			sql_updateq('spip_evenements', array('id_evenement_source' => ''), 'id_evenement='.intval($premier_occurence_repetition));
			spip_log("Répetition de l'évènement $id_evenement remplacées par des répetitions de l'évènement $premier_occurence_repetition, plus récent", 'agenda'._LOG_INFO_IMPORTANTE);
		}
		sql_delete('spip_evenements', 'id_evenement='.intval($id_evenement));
		spip_log("Effacement de l'évènement $id_evenement passés depuis plus de $delay jours (date de fin : $date_fin)", 'agenda'._LOG_INFO_IMPORTANTE);
	}

	return 1;
}
