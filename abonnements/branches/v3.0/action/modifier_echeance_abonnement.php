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
		and $abonnement = sql_fetsel('date_debut, date_echeance, date_fin', 'spip_abonnements', 'id_abonnement = '.$id_abonnement)
	) {
		$jourdhui = date('Y-m-d H:i:s');
		
		// Calculons la date de départ du renouvellement
		// Par défaut on part de la dernière échéance
		$date_depart = $abonnement['date_echeance'];
		
		// Si la date d'échéance n'était pas encore définie, on reprend depuis le début
		if ($date_depart == '0000-00-00 00:00:00'){
			$date_depart = $abonnement['date_debut'];
		}
		// Et si la date d'échéance était *déjà passée*, alors on renouvelle *à partir d'aujourd'hui* !
		elseif ($date_depart < $jourdhui) {
			$date_depart = $jourdhui;
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
			$modifications = array();
			
			// Si la durée est positive, on ajoute un + devant (le - est déjà là pour les négatives)
			if ($duree > 0){
				$ajout = ' +'.$ajout;
			}
			// Calcul de la date de fin
			$modifications['date_echeance'] = date('Y-m-d H:i:s', strtotime($date_depart.$ajout));
			
			// Si la date de fin n'est PAS infinie ET qu'elle se retrouve plus petite que l'échéance
			// On la remet au moins au même endroit que la nouvelle échéance
			if (
				$abonnement['date_fin'] != '0000-00-00 00:00:00'
				and $abonnement['date_fin'] < $modifications['date_echeance']
			) {
				$modifications['date_fin'] = $modifications['date_echeance'];
			}
			
			// On lance la modification
			include_spip('action/editer_objet');
			$erreur = objet_modifier('abonnement', $id_abonnement, $modifications);
			
			return array($id_abonnement, $erreur);
		}
	}
	
	return false;
}
