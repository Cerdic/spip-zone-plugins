<?php
/**
 * Fonctions relatives  au Plugin Périodes
 *
 * @plugin     Périodes
 * @copyright  2019
 * @author     Rainer Müller
 * @licence    GNU/GPL v3
 * @package    SPIP\Periodes\Fonctions
 */

// Sécurité
if (!defined('_ECRIRE_INC_VERSION'))
	return;

/**
 * Détermine si la date4_debut et la date_fin correspondent à une période
 *
 * @param integer $id_periode
 * @param array $contexte
 * @return boolean
 */
function inc_periode_verifier_dist($id_periode, $contexte = array()) {
	$date = date('Y-m-d H:s:m', time());
	$date_debut_contexte = isset($contexte['date_debut']) ?
		$contexte['date_debut'] :
		(_request('date_debut') ?
			_request('date_debut') :
			$date
		);

	$date_fin_contexte = isset($contexte['date_fin']) ?
		$contexte['date_fin'] :
		(
			_request('date_fin') ?
			_request('date_fin') :
			$date
		);
	$applicable = FALSE;

	$donnees_periode = sql_fetsel('*', 'spip_periodes', 'id_periode=' . $id_periode);

	$type = trim($donnees_periode['type']);
	$criteres = $donnees_periode['criteres'];
	$operateur = !empty($donnees_periode['operateur']) ? $donnees_periode['operateur'] : '==';
	$operateur_2 = !empty($donnees_periode['operateur_2']) ? $donnees_periode['operateur_2'] : '==';

	// Si date complète, on prend les dates tel que enrregistrés
	if ($donnees_periode['date_complete'] == 'oui') {
		$date_debut_periode = $donnees_periode['date_debut'];
		$date_fin_periode = $donnees_periode['date_fin'];
	}
	// Sinon on complète les bouts manquants avec les données de la date actuelle.
	else {
		$date_debut_jour = $donnees_periode['date_debut_jour'] ? $donnees_periode['date_debut_jour'] : date('d');
		$date_debut_mois = $donnees_periode['date_debut_mois'] ? $donnees_periode['date_debut_mois'] : date('m');
		$date_debut_annee = $donnees_periode['date_debut_annee'] ? $donnees_periode['date_debut_annee'] : date('Y');
		$date_fin_jour = $donnees_periode['date_fin_jour'] ? $donnees_periode['date_fin_jour'] : date('d');
		$date_fin_mois = $donnees_periode['date_fin_mois'] ? $donnees_periode['date_fin_mois'] : date('m');
		$date_fin_annee = $donnees_periode['date_fin_annee'] ? $donnees_periode['date_fin_annee'] : date('Y');
		$date_debut_periode = date('Y-m-d H:i:s', strtotime("$date_debut_annee-$date_debut_mois-$date_debut_jour 00:00:00"));
		$date_fin_periode = date('Y-m-d H:i:s', strtotime("$date_fin_annee-$date_fin_mois-$date_fin_jour 00:00:00"));
	}

	switch ($type) {
		case 'date':
			switch ($criteres) {
				case 'coincide' :

					if (($date_debut_contexte <= $date_fin_periode) and ($date_fin_contexte >= $date_debut_periode)) {
						$applicable = TRUE;
					}
					break;
				case 'exclu' :
					if (($date_debut_contexte > $date_fin_periode) or ($date_fin_contexte < $date_debut_periode)) {
						$applicable = TRUE;
					}
					break;
				case 'specifique' :
					if(periodes_condition($date_debut_contexte, $operateur, $date_debut_periode) and
					periodes_condition($date_fin_contexte, $operateur_2, $date_fin_periode)) {
							$applicable = TRUE;
						}
					break;
			}
			break;
		case 'jour_semaine':
			$jour_debut_periode = $donnees_periode['jour_debut'];
			$jour_fin_periode = $donnees_periode['jour_fin'];
			$jour_debut_contexte = date('w', strtotime($date_debut_contexte));
			$jour_fin_contexte = date('w', strtotime($date_fin_contexte));
			$dates_periode = [];//array($jour_debut_periode, $jour_fin_periode);
			if ($jour_fin_periode == 0) {
				$jour_fin_periode = 7;
			}

			$i = $jour_debut_periode;
			while ($i <= $jour_fin_periode) {
				$jour = $i;
				if ($jour == 7) {
					$jour = 0;
				}
				$dates_periode[] = $jour;
				$i++;
			}
			$dates_intervalle = dates_intervalle($date_debut_contexte, $date_fin_contexte, 0, 0, FALSE, 'w');
			$coincidences = array_intersect($dates_periode, $dates_intervalle);
			switch ($criteres) {
				case 'coincide' :
					if(count($coincidences) > 0) {
						$applicable = TRUE;
					}
					break;
				case 'exclu' :
					if(count($coincidences) == 0) {
						$applicable = TRUE;
					}
					break;
				case 'specifique' :
					if($jour_debut_contexte == $jour_debut_periode and
						$jour_fin_contexte == $jour_fin_periode) {
						$applicable = TRUE;
					}
					break;
			}
			break;
		case 'jour_nombre':
			include_spip('filtres/dates_outils');
			$nombre_jours_contexte = dates_difference($date_debut_contexte, $date_fin_contexte, 'jour');
			if ($nombre_jours_contexte >= 0) {
				if (periodes_condition($nombre_jours_contexte, $operateur, $donnees_periode['jour_nombre'])) {
					$applicable = TRUE;
				}
			}

			break;
	}

	return $applicable;
}


function periodes_condition($var1, $op, $var2) {

	switch ($op) {
		case "=":
			return $var1 == $var2;
		case "!=":
			return $var1 != $var2;
		case ">=":
			return $var1 >= $var2;
		case "<=":
			return $var1 <= $var2;
		case ">":
			return $var1 >  $var2;
		case "<":
			return $var1 <  $var2;
		default:
			return true;
	}
}
