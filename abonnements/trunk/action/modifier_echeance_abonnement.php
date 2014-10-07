<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Action de modif de l'échéance d'un abonnement
 * @param unknown_type $arg
 * @return unknown_type
 */
function action_modifier_echeance_abonnement_dist($arg=null) {
	if (is_null($arg)){
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$arg = $securiser_action();
	}
	
	list($id_abonnement, $duree, $periode) = explode('/', $arg);
	
	// Si on a bien un abonnement et qu'on a le droit de le modifier et qu'on a une durée != 0
	if (
		$duree = intval($duree)
		and $id_abonnement = intval($id_abonnement)
		and autoriser('modifier', 'abonnement', $id_abonnement)
		and $abonnement = sql_fetsel('date_debut, date_fin', 'spip_abonnements', 'id_abonnement = '.$id_abonnement)
	) {
		$date_depart = $abonnement['date_fin'];
		if ($date_depart == '0000-00-00 00:00:00'){
			$date_depart = $abonnement['date_debut'];
		}
		
		// De combien doit-on modifier la date
		switch ($periode){
			case 'heures':
				$ajout = " ${duree} hours";
				break;
			case 'jours':
				$ajout = " ${duree} days";
				break;
			case 'mois':
				$ajout = " ${duree} months";
				break;
			default:
				$ajout ='';
				break;
		}
		
		// Si la période existe
		if ($ajout){
			// Si la durée est positive, on ajoute un + devant (le - est déjà là pour les négatives)
			if ($duree > 0){
				$ajout = ' +'.$ajout;
			}
			
			// Calcul de la date de fin
			$nouvelle_echeance = date('Y-m-d H:i:s', strtotime($date_depart.$ajout));
			
			// On lance la modification
			include_spip('action/editer_objet');
			$erreur = objet_modifier('abonnement', $id_abonnement, array('date_fin' => $nouvelle_echeance));
			
			return array($id_abonnement, $erreur);
		}
	}
	
	return false;
}


?>
