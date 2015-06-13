<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Créer ou renouveler un abonnement
 * 
 * Si l'utilisateur n'a rien de cette offre, on crée un nouvel abonnement.
 * Si l'utilisateur a toujours ou avait précédemment un abonnement de cette offre, on le renouvelle.
 * 
 * On s'assure d'avoir les droits pendant les modifs
 * car ce n'est pas un humain avec des droits qui déclanche ça explicitement
 * 
 * @param string|null $arg
 * 		L'argument doit contenir "id_auteur/id_abonnements_offre".
 * @return mixed
 */
function action_creer_ou_renouveler_abonnement_dist($arg=null) {
	if (is_null($arg)){
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$arg = $securiser_action();
	}
	
	list($id_auteur,$id_abonnements_offre) = explode('/', $arg);
	
	// Si on a bien un auteur et une offre
	if (
		($id_auteur = intval($id_auteur)) > 0
		and ($id_abonnements_offre = intval($id_abonnements_offre)) > 0
	) {
		// On cherche la durée limite pour renouveler un abonnement
		include_spip('inc/config');
		$heures_limite = lire_config('abonnements/renouvellement_heures_limite', 48);
		
		// Si on trouve un abonnement de cette offre (le dernier en date)
		// et qu'il n'est pas trop vieux !
		if (
			$abonnement = sql_fetsel(
				'id_abonnement, date_fin',
				'spip_abonnements',
				array('id_auteur = '.$id_auteur, 'id_abonnements_offre = '.$id_abonnements_offre, 'statut != "poubelle"'),
				'',
				'date_fin desc',
				'0,1'
			)
			and $abonnement['date_fin'] <= date('Y-m-d H:i:s', strtotime('-'.$heures_limite.'hours'))
			and $id_abonnement = intval($abonnement['id_abonnement'])
		) {
			autoriser_exception('modifier', 'abonnement', $id_abonnement, true);
			// On le renouvelle !
			$renouveler = charger_fonction('renouveler_abonnement', 'action/');
			$retour = $renouveler($id_abonnement);
			autoriser_exception('modifier', 'abonnement', $id_abonnement, false);
			return $retour;
		}
		// Sinon on en crée un nouveau
		else {
			include_spip('action/editer_objet');
			autoriser_exception('creer', 'abonnement', '', true);
			if ($id_abonnement = objet_inserer('abonnement')) {
				autoriser_exception('creer', 'abonnement', '', false);
				autoriser_exception('modifier', 'abonnement', $id_abonnement, true);
				$erreur = objet_modifier(
					'abonnement', $id_abonnement,
					array(
						'id_auteur' => $id_auteur,
						'id_abonnements_offre' => $id_abonnements_offre,
					)
				);
				autoriser_exception('modifier', 'abonnement', $id_abonnement, false);
				return array($id_abonnement, $erreur);
			}
		}
	}
	
	return false;
}

