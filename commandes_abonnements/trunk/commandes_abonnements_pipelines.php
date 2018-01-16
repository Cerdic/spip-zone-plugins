<?php

/**
 * Pipelines utilisés par le plugin Commandes d’abonnements
 *
 * @plugin     Commandes d’abonnements
 * @copyright  2018
 * @author     Les Développements Durables
 * @licence    GPL 3
 * @package    SPIP\CommandesAbonements\Pipelines
 */
// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Ajouter des champs aux abonnements qui sont vraiment propres à leur achat en ligne
 * 
 * @pipeline declarer_tables_objets_sql
 * @param array $flux
 * 		Flux du pipeline contenant toutes les tables et leurs infos
 * @return array
 * 		Retourne le flux possiblement modifié
 **/
function commandes_abonnements_declarer_tables_objets_sql($flux) {
	$flux['spip_abonnements_offres']['field']['renouvellement_auto'] = 'varchar(10) not null default ""';
	$flux['spip_abonnements_offres']['field']['montant_perso'] = 'varchar(10) not null default ""';
	$flux['spip_abonnements_offres']['field']['montant_minimum'] = 'varchar(255) not null default""';
	$flux['spip_abonnements_offres']['champs_editables'][] = 'renouvellement_auto';
	$flux['spip_abonnements_offres']['champs_editables'][] = 'montant_perso';
	$flux['spip_abonnements_offres']['champs_editables'][] = 'montant_minimum';
	$flux['spip_abonnements_offres']['champs_versionnes'][] = 'renouvellement_auto';
	$flux['spip_abonnements_offres']['champs_versionnes'][] = 'montant_perso';
	$flux['spip_abonnements_offres']['champs_versionnes'][] = 'montant_minimum';
	
	return $flux;
}

/**
 * Modifier les saisies d'édition d'une offre pour ajouter les nouveaux champs
 * 
 * @pipeline formulaire_saisies
 * @param array $flux
 * 		Flux du pipeline contenant toutes les saisies des formulaires
 * @return array
 * 		Retourne le flux possiblement modifié
 **/
function commandes_abonnements_formulaire_saisies($flux) {
	// Si on est dans le formulaire d'édition d'une offre
	if ($flux['args']['form'] == 'editer_abonnements_offre') {
		include_spip('inc/saisies');
		
		$flux['data'] = saisies_inserer(
			$flux['data'],
			array(
				'saisie' => 'case',
				'options' => array(
					'nom' => 'renouvellement_auto',
					'label' => _T('abonnementsoffre:champ_renouvellement_auto_label'),
					'label_case' => _T('abonnementsoffre:champ_renouvellement_auto_label_case'),
				),
			)
		);
		$flux['data'] = saisies_inserer(
			$flux['data'],
			array(
				'saisie' => 'case',
				'options' => array(
					'nom' => 'montant_perso',
					'label' => _T('abonnementsoffre:champ_montant_perso_label'),
					'label_case' => _T('abonnementsoffre:champ_montant_perso_label_case'),
				),
			)
		);
		$flux['data'] = saisies_inserer(
			$flux['data'],
			array(
				'saisie' => 'input',
				'options' => array(
					'nom' => 'montant_minimum',
					'label' => _T('abonnementsoffre:champ_montant_minimum_label'),
					'afficher_si' => '@montant_perso@ == "on"',
				),
			)
		);
	}
	
	return $flux;
}

/**
 * Créer la commande finale à la fin des inscriptions ou modifs de profils
 * 
 * @pipeline formulaire_traiter
 * @param array $flux
 * 		Flux du pipeline contenant toutes les saisies des formulaires
 * @return array
 * 		Retourne le flux possiblement modifié
 **/
function commandes_abonnements_formulaire_saisies($flux) {
	if (
		$flux['args']['form'] == 'editer_auteur'
		and $id_auteur = $flux['data']['id_auteur']
	) {
		$retours = commandes_abonnements_generer_commande($id_auteur);
		
		// On ajoute les infos
		if (
			isset($flux['data']['redirect'])
			and $id_transaction = $retours['id_transaction']
			and $transaction_hash = $retours['transaction_hash']
		) {
			$flux['data']['redirect'] = parametre_url(parametre_url($flux['data']['redirect'], 'id_transaction', $id_transaction), 'transaction_hash', $transaction_hash);
		}
	}
	
	return $flux;
}

/**
 * Générer la commande suivant ce qu'on a gardé en session
 * 
 * @param int id_auteur
 * 		Identifiant de l'utilisateur qui commande
 * @return array
 * 		Retourne un tableau des retours de création des objets Commande et Transaction
 **/
function commandes_abonnements_generer_commande($id_auteur) {
	include_spip('inc/session');
	include_spip('base/abstract_sql');
	include_spip('action/editer_objet');
	include_spip('action/editer_commande');
	
	$retours = array();
	$commande_abonnement = session_get('commande_abonnement');
	
	// Si on trouve des infos de commande en session
	if (
		$id_auteur = intval($id_auteur)
		and is_array($commande_abonnement)
		and $id_abonnements_offre = intval($commande_abonnement['id_abonnements_offre'])
		and $montant = floatval($commande_abonnement['montant'])
	) {
		$renouvellement_auto = $commande_abonnement['renouvellement_auto'];
		$periodicite = '';
		$echeances = array();
		$montant_ht = $montant;
		$offre = sql_fetsel('*', 'spip_abonnements_offres', 'id_abonnements_offre ='.$id_abonnements_offre);
		
		// Si on trouve une taxe, on regénère un montant HT
		// (car comme le montant peut être personnalisé, ce qu'on a c'est toujours le TTC)
		if ($taxe = floatval($offre['taxe'])) {
			$montant_ht = $montant * (1 / (1 + $taxe));
		}
		
		if ($renouvellement_auto) {
			// Les deux seuls cas qu'on sait gérer pour l'instant
			if ($offre['periode'] == 'mois' and $offre['duree'] == 1) {
				$periodicite = 'mois';
			}
			elseif ($offre['periode'] == 'mois' and $offre['duree'] == 12) {
				$periodicite = 'annee';
			}
			
			$echeances = array(
				array('montant_ht' => $montant_ht, 'montant' => $montant),
			);
		}
		
		// On crée une nouvelle commande, l'abonnement ne sera créé ou renouvelé que lors du paiement !
		if (
			$id_commande = commande_inserer(0, array(
				'id_auteur' => $id_auteur,
				'echeances_type' => $periodicite,
				'echeances' => $echeances,
			))
		) {
			include_spip('inc/filtres');
			$titre_abonnements_offre = generer_info_entite($id_abonnements_offre, 'abonnements_offre', 'titre');
			
			// On remplit la commande avec l'offre d'abonnement demandé
			if ($id_commandes_detail = objet_inserer('commandes_detail', 0, array(
				'id_commande' => $id_commande,
				'descriptif' => $titre_abonnements_offre,
				'objet' => 'abonnements_offre',
				'id_objet' => $id_abonnements_offre,
				'quantite' => 1,
				'prix_unitaire_ht' => $montant_ht,
				'taxe' => $taxe,
			))) {
				$retours['id_commande'] = $id_commande;
				
				// On crée la première transaction pour le premier paiement
				$inserer_transaction = charger_fonction('inserer_transaction', 'bank');
				$options_transaction = array(
					'auteur' => $auteur['email'],
					'id_auteur' => $id_auteur,
					'montant_ht' => $montant_ht,
					'champs' => array(
						'id_commande' => $id_commande,
					),
				);
				if (
					$id_transaction = intval($inserer_transaction($montant, $options_transaction))
					and $transaction_hash = sql_getfetsel('transaction_hash', 'spip_transactions', 'id_transaction='.$id_transaction)
				) {
					$retours['id_transaction'] = $id_transaction;
					$retours['transaction_hash'] = $transaction_hash;
					
					// Et on supprime la session
					session_set('commande_abonnement', null);
				}
			}
		}
	}
	
	return $retours;
}
