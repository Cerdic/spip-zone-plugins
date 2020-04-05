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
function commandes_abonnements_formulaire_traiter($flux) {
	$formulaires = pipeline(
		'commandes_generer_apres_formulaires',
		array('editer_auteur', 'inscription', 'profil')
	);
	
	if (
		is_array($formulaires)
		and in_array($flux['args']['form'], $formulaires)
		and $id_auteur = $flux['data']['id_auteur']
	) {
		$flux['data'] += commandes_abonnements_generer_commande($id_auteur);
		
		// On ajoute le référence de la commande créé si on part sur une autre page
		if (
			isset($flux['data']['redirect'])
			and $flux['data']['redirect']
			and isset($flux['data']['reference'])
			and $flux['data']['reference']
		) {
			$flux['data']['redirect'] = parametre_url($flux['data']['redirect'], 'reference', $flux['data']['reference']);
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
	
	// Si on trouve des infos de commande d'abonnement en session
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
			$montant_ht = round($montant * (1 / (1 + $taxe)), 2);
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

		// Il ne peut y avoir qu'une seule commande en cours à la fois :
		// s'il y a déjà une commande en cours précédente, on la supprime.
		// Cf. creer_commande_encours() dans le plugin commandes.
		if (($id_commande = intval(session_get('id_commande'))) > 0) {
			// Si la commande est toujours "encours" il faut la mettre à la poubelle
			// il ne faut pas la supprimer tant qu'il n'y a pas de nouvelles commandes pour etre sur qu'on reutilise pas son numero
			// (sous sqlite la nouvelle commande reprend le numero de l'ancienne si on fait delete+insert)
			if (
				$statut = sql_getfetsel('statut', 'spip_commandes', 'id_commande = ' . intval($id_commande))
				and $statut == 'encours'
			) {
				spip_log("Commande ancienne encours->poubelle en session : $id_commande", 'commandes_abonnements');
				sql_updateq('spip_commandes', array('statut' => 'poubelle'), 'id_commande = ' . intval($id_commande));
			}
			// Dans tous les cas on supprime la valeur de session
			session_set('id_commande');
		}

		// On crée une nouvelle commande, l'abonnement ne sera créé ou renouvelé que lors du paiement !
		if (
			$id_commande = commande_inserer(0, array(
				'id_auteur' => $id_auteur,
				'echeances_type' => $periodicite,
				'echeances' => $echeances,
				'source' => 'abonnementsoffre#' .$offre['id_abonnements_offre'],
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
				// On retourne la référence et l'id
				$retours['id_commande'] = $id_commande;
				$retours['reference'] = sql_getfetsel(
					'reference',
					'spip_commandes',
					'id_commande=' . intval($id_commande)
				);
				// Et on supprime le pseudo-panier de la session, puis on met la vraie commande
				session_set('commande_abonnement', null);
				session_set('id_commande', $id_commande);
			}
		}
	}
	
	return $retours;
}

/**
 * Modifier le résultat de la compilation d'un squelette
 *
 * => Ajouter les nouveaux champs dans la fiche d'une offre d'abonnement
 *
 * @pipeline recuperer_fond
 * @param array $flux
 * 		Flux du pipeline contenant le squelette compilé
 * @return array
 * 		Retourne le flux possiblement modifié
 */
function commandes_abonnements_recuperer_fond($flux) {

	if (isset($flux['args']['fond'])
		and $flux['args']['fond'] == 'prive/objets/contenu/abonnements_offre'
		and isset($flux['args']['contexte'])
		and $complement = recuperer_fond('prive/objets/contenu/abonnements_offre_complement', $flux['args']['contexte'])
	) {
		$flux['data']['texte'] .= $complement;
	}
	
	return $flux;
}