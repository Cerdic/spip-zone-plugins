<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Créer ou renouveler un abonnement
 * 
 * Si l'utilisateur n'a rien de cette offre, on crée un nouvel abonnement.
 * Si l'utilisateur a toujours ou avait précédemment un abonnement de cette offre, on le renouvelle.
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
		// Si on trouve un abonnement (le dernier en date)
		if ($id_abonnement = intval(sql_getfetsel(
			'id_abonnement',
			'spip_abonnements',
			array('id_auteur = '.$id_auteur, 'id_abonnements_offre = '.$id_abonnements_offre, 'statut != "poubelle"'),
			'',
			'date_fin desc',
			'0,1'
		))) {
			// On le renouvelle !
			$renouveler = charger_fonction('renouveler_abonnement', 'action/');
			return $renouveler($id_abonnement);
		}
		// Sinon on en crée un nouveau
		else {
			include_spip('action/editer_objet');
			if ($id_abonnement = objet_inserer('abonnement')) {
				$erreur = objet_modifier(
					'abonnement', $id_abonnement,
					array(
						'id_auteur' => $id_auteur,
						'id_abonnements_offre' => $id_abonnements_offre,
					)
				);
				return array($id_abonnement, $erreur);
			}
		}
	}
	
	return false;
}

