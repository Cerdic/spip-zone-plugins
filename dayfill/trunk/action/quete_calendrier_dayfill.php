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
	$end = _request('end');
	include_spip('action/quete_calendrier_prive');
	include_spip('inc/quete_calendrier');

	// recuperer la liste des evenements au format ics
	$start = date('Y-m-d H:i:s',$start);
	$end = date('Y-m-d H:i:s',$end);
	$limites = array(sql_quote($start),sql_quote($end));

	$duree = quete_calendrier_interval_projets_activites($limites);
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
**/
function quete_calendrier_interval_projets_activites($limites, &$evenements) {
	list($avant, $apres) = $limites;

	$result=sql_select(
		'id_projets_activite, id_projet, descriptif, date_debut, date_fin',
		'spip_projets_activites',
		"date_fin >= $avant AND date_debut <= $apres", '', "date_debut");

	while($row=sql_fetch($result)){
		$amj = date_anneemoisjour($row['date_debut']);
		$id = $row['id_projets_activite'];
		$date_debut = $row["date_debut"];
		$date_fin   = $row["date_fin"];

		if (autoriser('voir','projetsactivite', $id)) {

			// cf quete_calendrier_interval_rv()
			$jour_avant  = substr($avant, 9,2);
			$mois_avant  = substr($avant, 6,2);
			$annee_avant = substr($avant, 1,4);
			$jour_apres  = substr($apres, 9,2);
			$mois_apres  = substr($apres, 6,2);
			$annee_apres = substr($apres, 1,4);
			$ical_apres = date_anneemoisjour("$annee_apres-$mois_apres-".sprintf("%02d",$jour_apres));

			// Calcul pour les semaines a cheval sur deux mois
			$j = 0;
			$amj = date_anneemoisjour("$annee_avant-$mois_avant-".sprintf("%02d", $j+($jour_avant)));

			while ($amj <= $ical_apres) {
				// Ne pas prendre la fin a minuit sur jour precedent
				if (!($amj == date_anneemoisjour($date_fin) AND preg_match(",00:00:00,", $date_fin))) {
					$evenements[$amj][$id] = array(
						'CATEGORIES' => 'calendrier-couleur' . (($row['id_projet']%14)+1),
						'SUMMARY' => $row['titre'],
						'URL' => generer_url_ecrire_objet('projets_activite',$id),
						'DTSTART' => date_ical($date_debut),
						'DTEND'   => date_ical($date_fin),
					);

					$j ++;
					$ladate = date("Y-m-d",mktime (1,1,1,$mois_avant, ($j + $jour_avant), $annee_avant));

					$amj = date_anneemoisjour($ladate);
				}
			}
		}
	}
}
