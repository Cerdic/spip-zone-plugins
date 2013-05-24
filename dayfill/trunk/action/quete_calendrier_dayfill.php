<?php

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *                                                                         *
 *  Copyright (c) 2001-2013                                                *
 *  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James  *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

/**
 * Gestion de l'action de quête des activités du calendrier
 *
 * @package SPIP\Dayfill\Action
**/

if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Fournir une liste d'activités de projets entre deux dates start et end
 * au format json
 * 
 * Utilisé pour l'affichage du calendrier des activités
 * 
 * @return void
 */
function action_quete_calendrier_dayfill_dist(){
	$securiser_action = charger_fonction('securiser_action','inc');
	$securiser_action();

	$start = _request('start');
	$end   = _request('end');

	include_spip('action/quete_calendrier_prive');
	include_spip('inc/quete_calendrier');

	// recuperer la liste des evenements au format ics
	$start = date('Y-m-d H:i:s',$start);
	$end   = date('Y-m-d H:i:s',$end);
	$limites = array(sql_quote($start),sql_quote($end));

	$evt = array();
	$duree = quete_calendrier_interval_projets_activites($limites, $evt);
	$evt = convert_fullcalendar_quete_calendrier_interval_rv($duree);

	// format json
	include_spip('inc/json');
	echo json_encode($evt);
}




/**
 * Retourne la liste des activités (format ICS) présentes dans une période donnée
 *
 * @param array $limites
 *     Liste (date de début, date de fin)
 * @param array $evenements
 *     Tableau des événements déjà présents qui sera complété par la fonction.
 *     Format : `$evenements[$amj][] = Tableau de description ICS`
 * @return array
 *     De la forme : `$evenements[date][id] = Tableau des données ICS`
**/
function quete_calendrier_interval_projets_activites($limites, &$evenements) {
	list($avant, $apres) = $limites;

	$result = sql_select(
		'id_projets_activite, id_projet, descriptif, date_debut, date_fin',
		'spip_projets_activites',
		"date_fin >= $avant AND date_debut <= $apres", '', "date_debut");

	while ($row=sql_fetch($result)) {
		$amj = date_anneemoisjour($row['date_debut']);
		$id = $row['id_projets_activite'];
		$date_debut = $row["date_debut"];
		$date_fin   = $row["date_fin"];

		if (autoriser('voir','projets_activite', $id)) {
			// fullcalendar se débrouillera très bien de ça...
			$evenements[$amj][$id] = array(
				'CATEGORIES' => 'calendrier-couleur' . (($row['id_projet']%14)+1),
				'SUMMARY' => $row['descriptif'],
				'URL' => generer_url_ecrire('projets_activite', "id_projets_activite=$id"),
				'DTSTART' => date_ical($date_debut),
				'DTEND'   => date_ical($date_fin),
				'DESCRIPTION' => '',
			);
		}
	}

	return $evenements;
}
