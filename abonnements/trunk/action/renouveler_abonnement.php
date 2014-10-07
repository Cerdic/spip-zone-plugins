<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Action de renouveler un abonnement
 * @param int $arg
 * @return unknown_type
 */
function action_renouveler_abonnement_dist($arg=null) {
	if (is_null($arg)){
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$arg = $securiser_action();
	}
	
	// Si on a bien un abonnement et qu'on a le droit de le modifier
	if (
		$id_abonnement = intval($arg)
		and autoriser('modifier', 'abonnement', $id_abonnement)
		and $abonnement = sql_fetsel('id_abonnements_offre, date_debut, date_fin', 'spip_abonnements', 'id_abonnement = '.$id_abonnement)
	) {
	
		$offre = sql_fetsel('duree, periode', 'spip_abonnements_offres', 'id_abonnements_offre = '.$abonnement['id_abonnements_offre']);
		
		// Si l'offre parente A BIEN une durée
		if (($duree = $offre['duree']) > 0){
			// De combien doit-on augmenter la date ?
			switch ($offre['periode']){
				case 'heures':
					$ajout = " + ${duree} hours";
					break;
				case 'jours':
					$ajout = " + ${duree} days";
					break;
				case 'mois':
					$ajout = " + ${duree} months";
					break;
				default:
					$ajout = NULL;
					break;
			}
		}
		
		$date_depart = $abonnement['date_fin'];
		if ($date_depart == '0000-00-00 00:00:00'){
			$date_depart = $abonnement['date_debut'];
		}
		
		// Si la période existe
		if (isset($ajout)){
			
			// Calcul de la date de fin
			$nouvelle_echeance = date('Y-m-d H:i:s', strtotime($date_depart.$ajout));
			
			// On lance la modification
			include_spip('action/editer_objet');
			$erreur = objet_modifier('abonnement', $id_abonnement, array('date_fin' => $nouvelle_echeance, 'statut' => 'actif'));
			
			return array($id_abonnement, $erreur);

		}
	}
	
	return false;
}


?>
