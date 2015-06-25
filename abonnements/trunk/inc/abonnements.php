<?php
/**
 * Plugin Abonnements
 * (c) 2012 Les Développements Durables
 * Licence GNU/GPL v3
 */

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
 * @param int $id_auteur
 * 		Identifiant de l'utilisateur pour lequel on veut créer un abonnement
 * @param int $id_abonnements_offre
 * 		Identifiant de l'offre d'abonnement voulue
 * @param bool $forcer_creation
 * 		`true` si on veut forcer la création sans chercher à renouveler
 * @return mixed
 */
function abonnements_creer_ou_renouveler($id_auteur, $id_abonnements_offre, $forcer_creation=false) {
	// Si on a bien un auteur et une offre
	if (
		($id_auteur = intval($id_auteur)) > 0
		and ($id_abonnements_offre = intval($id_abonnements_offre)) > 0
	) {
		include_spip('inc/config');
		include_spip('inc/autoriser');
		
		// On cherche la durée limite pour renouveler un abonnement
		$heures_limite = lire_config('abonnements/renouvellement_heures_limite', 48);
		
		// Si on trouve un abonnement de cette offre (le dernier en date)
		// et qu'il n'est pas trop vieux !
		// et qu'on a pas forcé la création…
		if (
			!$forcer_creation
			and $abonnement = sql_fetsel(
				'id_abonnement, date_fin',
				'spip_abonnements',
				array(
					'id_auteur = '.$id_auteur,
					'id_abonnements_offre = '.$id_abonnements_offre,
					'statut != "poubelle"'
				),
				'',
				'date_fin desc',
				'0,1'
			)
			and $abonnement['date_fin'] >= date('Y-m-d H:i:s', strtotime('- '.$heures_limite.' hours'))
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

/**
 * Initialiser les dates d'échéance et de fin pour un abonnement créé
 * 
 * @pipeline_appel abonnement_initialisation_dates
 * @param array $abonnement
 * 		Informations sur l'abonnement à initialiser
 * @param array $offre
 * 		Informations sur l'offre de l'abonnement à initialiser
 * @return array
 * 		Retourne les modifications de dates initialisées
 **/
function abonnements_initialisation_dates($abonnement, $offre){
	$modifs = array();
	
	// De combien doit-on augmenter la date
	$duree = $offre['duree'];
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
			$ajout = '';
			break;
	}
	
	// Par défaut les dates de fin et de la prochaine échéance sont les mêmes
	$modifs['date_echeance'] = date('Y-m-d H:i:s', strtotime($abonnement['date_debut'].$ajout));
	$modifs['date_fin'] = $modifs['date_echeance'];
	
	// Mais si c'est un renouvellement auto avec Commandes et Bank
	if ($date_fin = abonnements_bank_date_fin($abonnement['id_abonnnement'])) {
		$modifs['date_fin'] = $date_fin;
	}
	
	$modifs = pipeline(
		'abonnement_initialisation_dates',
		array(
			'args' => array('abonnement' => $abonnement, 'offre' => $offre),
			'data' => $modifs
		)
	);
	
	return $modifs;
}

/**
 * Trouver la date de fin d'un renouvellement automatique éventuel
 * 
 * @param int $id_abonnement
 * 		Identifiant de l'abonnement dont on veut trouver la date de fin
 * @param int $id_commande
 * 		Possibilité de donner la commande pour éviter une requête SQL
 * @return bool|datetime
 * 		Retourne la date de fin du renouvellement si on trouve, sinon false pour ne rien faire
 **/
function abonnements_bank_date_fin($id_abonnement, $id_commande=0){
	$date_fin = false;
	
	// On teste si on trouve un renouvellement auto
	if (
		_DIR_PLUGIN_COMMANDES
		and _DIR_PLUGIN_BANK
		and (
			// Soit on a déjà une commande sous la main
			(
				$id_commande = intval($id_commande)
				and $id_commande > 0
			)
			// Soit on va chercher une commande liée à l'abonnement
			or
			(
				include_spip('action/editer_liens')
				and $lien_commande = objet_trouver_liens(array('commande' => '*'), array('abonnement' => $id_abonnement))
				and is_array($lien_commande)
				// On prend juste la première commande qu'on trouve
				and $id_commande = intval($lien_commande[0]['id_commande'])
			)
		)
		// On cherche un paiement bien payé pour cette commande
		and $transaction = sql_fetsel(
			'*', 'spip_transactions', array('id_commande = '.$id_commande, 'statut = "ok"')
		)
		// Et que c'est un renouvellement auto !
		and $transaction['abo_uid']
	) {
		// On a trouvé la transaction qui a activé la commande qui a activé l'abonnement
		// Si on détecte un prélèvement SEPA, on annule la date de fin !
		if ($refcb = $transaction['refcb'] and strpos($refcb, 'SEPA') === 0) {
			$date_fin = '0000-00-00 00:00:00';
		}
		// Si ya une fin de validité de carte bleue on en déduit une fin d'abonnement !
		elseif ($validite = $transaction['validite']) {
			include_spip('inc/bank');
			list($year, $month) = explode('-', $validite);
			$date_fin = bank_date_fin_mois($year, $month);
		}
	}
	
	return $date_fin;
}

/*
 * Programmer la désactivation d'un abonnement lors de sa date de fin
 *
 * @param int $id_abonnement
 *	L'identifiant de l'abonnement
 * @param datetime $date_fin
 *	Optionnellement la date de fin si on la connait déjà, ce qui évite une requête
 */
function abonnements_programmer_desactivation($id_abonnement, $date_fin=null){
	include_spip('action/editer_liens');
	$id_abonnement = intval($id_abonnement);
	
	// Si on a pas de date, on va chercher
	if (!$date_fin){
		$date_fin = sql_getfetsel('date_fin', 'spip_abonnements', 'id_abonnement = '.$id_abonnement);
	}
	
	// Dans tous les cas on cherche s'il y des tâches liées à cet abonnement
	$liens = objet_trouver_liens(array('job' => '*'), array('abonnement' => $id_abonnement));
	if ($liens and is_array($liens)){
		// Et on les supprime toutes !
		foreach ($liens as $lien){
			job_queue_remove($lien['id_job']);
		}
	}
	
	// Seulement si on a bien une date de fin, on reprogramme, sans duplication possible
	if ($date_fin and $date_fin != '0000-00-00 00:00:00'){
		$id_job = job_queue_add(
			'abonnements_desactiver',
			_T('abonnement:job_desactivation', array('id'=>$id_abonnement)),
			array($id_abonnement),
			'inc/abonnements',
			true,
			strtotime($date_fin)
		);
		job_queue_link($id_job, array('objet'=>'abonnement', 'id_objet'=>$id_abonnement));
	}
}

/*
 * Désactiver un abonnement en utilisant l'API et sans autorisation
 */
function abonnements_desactiver($id_abonnement){
	include_spip('inc/autoriser');
	include_spip('action/editer_objet');
	// On inhibe les autorisations
	autoriser_exception('modifier', 'abonnement', $id_abonnement);
	autoriser_exception('instituer', 'abonnement', $id_abonnement);
	// On désactive l'abonnement
	objet_modifier('abonnement', $id_abonnement, array('statut' => 'inactif'));
	// On remet les autorisations
	autoriser_exception('instituer', 'abonnement', $id_abonnement, false);
	autoriser_exception('modifier', 'abonnement', $id_abonnement, false);
}

/*
 * Envoyer un courriel à l'abonné pour lui rappeler combien de temps il lui reste
 */
function abonnements_notifier_echeance($id_abonnement, $nom, $email, $duree, $periode){
	$envoyer_mail = charger_fonction('envoyer_mail', 'inc/');
	
	$envoyer_mail(
		$email,
		_T('abonnement:notification_echeance_sujet_'.$periode, array('duree'=>$duree)),
		recuperer_fond(
			'notifications/abonnement_echeance',
			array(
				'id_abonnement' => $id_abonnement,
				'nom' => $nom,
				'email' => $email,
				'duree' => $duree,
				'periode' => $periode
			)
		)
	);
}
