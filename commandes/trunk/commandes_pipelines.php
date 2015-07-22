<?php
/**
 * Pieplines utilisées par le plugin Commandes
 *
 * @plugin     Commandes
 * @copyright  2014
 * @author     Ateliers CYM, Matthieu Marcillaud, Les Développements Durables
 * @licence    GPL 3
 * @package    SPIP\Commandes\Pipelines
 */

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;


/**
 * Insertion de la feuille de style CSS sur les pages publiques
 *
 * @pipeline insert_head_css
 * @param  array $flux Données du pipeline
 * @return array       Données du pipeline
 */
function commandes_insert_head_css($flux){
	$css = find_in_path('css/commandes.css');
	$flux .= "<link rel='stylesheet' type='text/css' media='all' href='$css' />\n";
	return $flux;
}


/**
 * Optimiser la base de donnée en supprimant toutes les commandes en cours qui sont trop vieilles
 * 
 * Le délai de "péremption" est défini dans les options de configuration du plugin
 *
 * @pipeline optimiser_base_disparus
 * @param  array $flux Données du pipeline
 * @return array       Données du pipeline
 */
function commandes_optimiser_base_disparus($flux){
	include_spip('inc/config');
	// On cherche la date depuis quand on a le droit d'avoir fait la commande (par défaut 1h)
	$depuis = date('Y-m-d H:i:s', time() - 3600*intval(lire_config('commandes/duree_vie', 1)));
	// On récupère les commandes trop vieilles
	$commandes = sql_allfetsel(
		'id_commande',
		'spip_commandes',
		'statut = '.sql_quote('encours').' and date<'.sql_quote($depuis)
	);

	// S'il y a bien des commandes à supprimer
	if ($commandes) {
		$commandes = array_map('reset', $commandes);
		include_spip('inc/commandes');
		commandes_supprimer($commandes);
		$flux['data'] += count($commandes);
	}

	return $flux;
}


/**
 * Ajout de contenu sur certaines pages
 *
 * - Formulaires pour modifier les dates sur la fiche d'une commande
 *
 * @pipeline affiche_milieu
 * @param  array $flux Données du pipeline
 * @return array       Données du pipeline
 */
function commandes_affiche_milieu($flux) {

	if (
		$exec = trouver_objet_exec($flux['args']['exec'])
		and $exec['edition'] == false 
		and $exec['type'] == 'commande'
		and $id_table_objet = $exec['id_table_objet']
		and (isset($flux['args'][$id_table_objet]) and $id_commande = intval($flux['args'][$id_table_objet]))
	) {
		$texte = recuperer_fond('prive/squelettes/contenu/commande_affiche_milieu',array('id_commande'=>$id_commande));
	}

	if (isset($texte)) {
		if ($p=strpos($flux['data'],"<!--affiche_milieu-->"))
			$flux['data'] = substr_replace($flux['data'],$texte,$p,0);
		else
			$flux['data'] .= $texte;
	}

	return $flux;
}


/**
 * Ajout de contenu dans la liste des éléments en attente de validation
 *
 * - Liste des commandes aux statuts définis comme "actifs" dans les options de configuration
 *
 * @pipeline accueil_encours
 * @param  array $flux Données du pipeline
 * @return array       Données du pipeline 
 */
function commandes_accueil_encours($flux) {

	include_spip('inc/config');
	$activer = lire_config('commandes/accueil_encours');
	$statuts = lire_config('commandes/statuts_actifs');
	if ($activer and is_array($statuts)) {
		foreach($statuts as $statut){
			if ($nb_{$statut} = sql_countsel(table_objet_sql('commande'), "statut=".sql_quote($statut))) {
				$titre_{$statut} = singulier_ou_pluriel($nb_{$statut}, 'commandes:info_1_commande_statut_'.$statut, 'commandes:info_nb_commandes_statut_'.$statut);
				$flux .= recuperer_fond('prive/objets/liste/commandes', array(
					'titre' => $titre_{$statut},
					'statut' => $statut,
					'cacher_tri' => true,
					'nb' => 5),
					array('ajax' => true)
				);
			}
		}
	}

	return $flux;
}


/**
 * Ajout de liste sur la vue d'un auteur
 *
 * - Liste des commandes de l'auteur
 *
 * @pipeline affiche_auteurs_interventions
 * @param  array $flux Données du pipeline
 * @return array       Données du pipeline
**/
function commandes_affiche_auteurs_interventions($flux) {

	if ($id_auteur = intval($flux['args']['id_auteur'])) {
		$flux['data'] .= recuperer_fond('prive/objets/liste/commandes', array(
			'id_auteur' => $id_auteur,
			'titre' => _T('commandes:titre_commandes_auteur'),
			'cacher_tri' => true
			),
			array('ajax' => true)
		);
	}

	return $flux;
}


/**
 * Compléter la liste des types d'adresses du plugin Coordonnées
 *
 * Ajout de 2 types d'adresses : facturation et livraison
 *
 * @pipeline types_coordonnees
 * @param  array $liste Données du pipeline
 * @return array        Données du pipeline
**/
function commandes_types_coordonnees($liste) {

	$types_adresses = $liste['adresse'];
	if (!$types_adresses or !is_array($types_adresses)) $types_adresses = array();

	// on définit les couples types + chaînes de langue à ajouter
	$types_adresses_commandes = array(
		'livraison' => _T('commandes:type_adresse_livraison'),
		'facturation' => _T('commandes:type_adresse_facturation')
	);
	// on les rajoute à la liste des types des adresses
	$liste['adresse'] = array_merge($types_adresses, $types_adresses_commandes);

	return $liste;
}


/**
 * Enregistrer le bon reglement d'une commande liee a une transaction du plugin bank
 * 
 * @pipeline bank_traiter_reglement
 * @param array $flux
 * @return array mixed
 */
function commandes_bank_traiter_reglement($flux){
	// Si on est dans le bon cas d'un paiement de commande et qu'il y a un id_commande et que la commande existe toujours
	if (
		$id_transaction = $flux['args']['id_transaction']
		and $transaction = sql_fetsel("*","spip_transactions","id_transaction=".intval($id_transaction))
		and $id_commande = $transaction['id_commande']
		and $commande = sql_fetsel('id_commande, statut, id_auteur, echeances, reference', 'spip_commandes', 'id_commande='.intval($id_commande))
	){
		$statut_commande = $commande['statut'];
		$montant_regle = $transaction['montant_regle'];
		$transaction_mode = $transaction['mode'];
		$statut_nouveau = 'paye';
		
		// Si la commande n'a pas d'échéance, le montant attendu est celui du prix de la commande
		if (!$commande['echeances'] or !$echeances = unserialize($commande['echeances'])) {
			$fonction_prix = charger_fonction('prix', 'inc/');
			$montant_attendu = $fonction_prix('commande', $id_commande);
		}
		// Sinon le montant attendu est celui de la prochaine échéance
		else {
			include_spip('inc/commandes_echeances');
			$montant_attendu = commandes_trouver_prochaine_echeance($id_commande, $echeances);
		}
		spip_log("commande #$id_commande attendu:$montant_attendu regle:$montant_regle", 'commandes');

		// Si le plugin n'était pas déjà en payé et qu'on a pas assez payé
		// (si le plugin était déjà en payé, ce sont possiblement des renouvellements)
		if (
			$statut_commande != 'paye'
			and (floatval($montant_attendu) - floatval($montant_regle)) >= 0.01
		){
			$statut_nouveau = 'partiel';
		}
		
		// S'il y a bien un statut à changer
		if ($statut_nouveau !== $statut_commande){
			spip_log("commandes_bank_traiter_reglement marquer la commande #$id_commande statut=$statut_nouveau mode=$transaction_mode",'commandes');
			// On met a jour la commande
			include_spip("action/editer_commande");
			commande_modifier($id_commande, array('statut'=>$statut_nouveau, 'mode'=>$transaction_mode));
		}

		// un message gentil pour l'utilisateur qui vient de payer, on lui rappelle son numero de commande
		$flux['data'] .= "<br />"._T('commandes:merci_de_votre_commande_paiement',array('reference'=>$commande['reference']));
	}

	return $flux;
}

/**
 * Enregistrer le reglement en attente d'une commande liee a une transaction du plugin bank
 * (cas du reglement par cheque par exemple)
 * 
 * @pipeline trig_bank_reglement_en_attente
 * @param array $flux
 * @return array mixed
 */
function commandes_trig_bank_reglement_en_attente($flux){
	// Si on est dans le bon cas d'un paiement de commande et qu'il y a un id_commande et que la commande existe toujours
	if ($id_transaction = $flux['args']['id_transaction']
	  AND $transaction = sql_fetsel("*","spip_transactions","id_transaction=".intval($id_transaction))
		AND $id_commande = $transaction['id_commande']
		AND $commande = sql_fetsel('id_commande, statut, id_auteur, mode', 'spip_commandes', 'id_commande='.intval($id_commande))){

		$statut_commande = $commande['statut'];
		$transaction_mode = $transaction['mode'];
		$commande_mode = $commande['mode'];
		$statut_nouveau = 'attente';
		if ($statut_nouveau !== $statut_commande OR $transaction_mode !==$commande_mode){
			spip_log("commandes_trig_bank_reglement_en_attente marquer la commande #$id_commande statut=$statut_nouveau mode=$transaction_mode",'commandes');
			//on met a jour la commande
			include_spip("action/editer_commande");
			commande_modifier($id_commande,array('statut'=>$statut_nouveau,'mode'=>$transaction_mode));
		}
	}

	return $flux;
}


/**
 * Enregistrer le reglement en echec d'une commande liee a une transaction du plugin bank
 * (cas du reglement annule ou du refus de carte etc)
 * 
 * @pipeline trig_bank_reglement_en_echec
 * @param array $flux
 * @return array mixed
 */
function commandes_trig_bank_reglement_en_echec($flux){
	// Si on est dans le bon cas d'un paiement de commande et qu'il y a un id_commande et que la commande existe toujours
	if ($id_transaction = $flux['args']['id_transaction']
	  AND $transaction = sql_fetsel("*","spip_transactions","id_transaction=".intval($id_transaction))
		AND $id_commande = $transaction['id_commande']
		AND $commande = sql_fetsel('id_commande, statut, id_auteur', 'spip_commandes', 'id_commande='.intval($id_commande))){

		$statut_commande = $commande['statut'];
		$transaction_mode = $transaction['mode'];
		$statut_nouveau = $statut_commande;

		// on ne passe la commande en erreur que si le reglement a effectivement echoue,
		// pas si c'est une simple annulation (retour en arriere depuis la page de paiement bancaire)
		if (strncmp($transaction['statut'],"echec",5)==0){
			$statut_nouveau = 'erreur';
		}
		if ($statut_nouveau !== $statut_commande){
			spip_log("commandes_trig_bank_reglement_en_attente marquer la commande #$id_commande statut=$statut_nouveau",'commandes');
			//on met a jour la commande
			include_spip("action/editer_commande");
			commande_modifier($id_commande,array('statut'=>$statut_nouveau,'mode'=>$transaction_mode));
		}
	}

	return $flux;
}

/**
 * Déclarer les échéances à la banque
 * 
 * @pipeline bank_abos_decrire_echeance
 **/
function commandes_bank_abos_decrire_echeance($flux) {
	if (
		// si on doit bien faire du prélèvement auto
		$flux['args']['force_auto'] == true
		// et qu'on a une transaction sous la main
		and $id_transaction = intval($flux['args']['id_transaction'])
		// et que cette transaction a un id_commande
		and $id_commande = intval(sql_getfetsel('id_commande', 'spip_transactions', 'id_transaction = '.$id_transaction))
		// et que la commande a des informations d'échéances
		and $commande = sql_fetsel('echeances_type, echeances', 'spip_commandes', 'id_commande = '.$id_commande)
		and $echeances = unserialize($commande['echeances'])
		and $echeances_type = $commande['echeances_type']
		and in_array($echeances_type, array('mois', 'annee'))
	) {
		// On définit la périodicité
		switch($echeances_type) {
			case 'mois':
				$flux['data']['freq'] = 'monthly';
			case 'annee':
				$flux['data']['freq'] = 'yearly';
		}
		
		// Si c'est une seule valeur toute simple
		if (!is_array($echeances)) {
			$echeances = floatval($echeances);
			$flux['data']['montant'] = $echeances;
		}
		// Sinon c'est un peu plus compliqué, et pour l'instant on ne gère que DEUX montants possibles
		elseif (count($echeances) >= 2) {
			// Premier montant d'échéances
			$flux['data']['montant_init'] = $echeances[0]['montant'];
			$flux['data']['count_init'] = $echeances[0]['nb'];
			// Deuxième montant d'échéances
			$flux['data']['montant'] = $echeances[1]['montant'];
			if (isset($echeances[1]['nb'])) {
				$flux['data']['count'] = $echeances[1]['nb'];
			}
		}
	}
	
	return $flux;
}

/**
 * Lier une commande à un identifiant bancaire lorsqu'un prélèvement bancaire est bien validé
 * 
 * @pipeline bank_abos_activer_abonnement
 **/
function commandes_bank_abos_activer_abonnement($flux){
	// Si on a une transaction
	if ($id_transaction = intval($flux['args']['id_transaction'])) {
		$were = 'id_transaction = '.$id_transaction;
	}
	// Sinon on cherche par l'identifiant d'abonnement bancaire
	elseif ($abo_uid = $flux['args']['abo_uid']) {
		$where = 'abo_uid = '.sql_quote($abo_uid);
	}
	
	// On gère d'abord les erreurs possibles si on ne trouve pas la bonne transaction
	if (!$transaction = sql_fetsel('*', 'spip_transactions', $where)) {
		spip_log("Impossible de trouver la transaction ($id_transaction / $abo_uid).", 'commandes.'._LOG_ERREUR);
		$flux['data'] = false;
	}
	elseif ($transaction['statut'] == 'commande') {
		spip_log("La transaction ${transaction['id_transaction']} n’a pas été réglée.", 'commandes.'._LOG_ERREUR);
		$flux['data'] = false;
	}
	elseif (strncmp($transaction['statut'], 'echec',5) == 0) {
		spip_log("La transaction ${transaction['id_transaction']} a echoué.",'commandes.'._LOG_ERREUR);
		$flux['data'] = false;
	}
	// Si on a trouvé ce qu'il faut, on va lier la commande à l'identifiant bancaire
	elseif ($id_commande = intval($transaction['id_commande'])) {
		include_spip('action/editer_objet');
		
		objet_modifier('commande', $id_commande, array('bank_uid' => $flux['args']['abo_uid']));
	}
	
	return $flux;
}

/**
 * Créer la transaction correspondant à la prochaine échéance d'une commande
 * 
 * @pipeline bank_abos_preparer_echeance
 **/
function commandes_bank_abos_preparer_echeance($flux){
	// On commence par chercher la commande dont il s'agit
	// et vérifier qu'elle a des échéances
	if (
		strncmp($id,"uid:",4) == 0
		and $bank_uid = substr($id, 4)
		and $commande = sql_fetsel('*', 'spip_commandes', 'bank_uid = '.sql_quote($bank_uid))
		and $id_commande = intval($commande['id_commande'])
		and $echeances = unserialize($commande['echeances'])
		and $echeances_type = $commande['echeances_type']
	){
		include_spip('inc/commandes_echeances');
		
		// Si on a bien trouvé une prochaine échéance
		if ($montant = commandes_trouver_prochaine_echeance($id_commande, $echeances)) {
			include_spip('action/editer_objet');
			
			// On remet la commande en attente de paiement puisqu'on… attend un paiement !
			objet_modifier('commande', $id_commande, array('statut' => 'attente'));
			
			// On crée la transaction qui testera le vrai paiement
			$inserer_transaction = charger_fonction('inserer_transaction', 'bank');
			$options_transaction = array(
				'id_auteur' => intval($commande['id_auteur']),
				'champs' => array(
					'id_commande' => $id_commande,
				),
			);
			$id_transaction = intval($inserer_transaction($montant, $options_transaction));
			
			$flux['data'] = $id_transaction;
		}
	}
	
	return $flux;
}

/**
 * Mettre en erreur une commande dont le prélèvement automatique aurait échoué
 * 
 * @pipeline bank_abos_resilier
 **/
function commandes_bank_abos_resilier($flux){
	// On commence par chercher la commande dont il s'agit
	// et vérifier qu'elle a des échéances
	if (
		strncmp($id,"uid:",4) == 0
		and $bank_uid = substr($id, 4)
		and $commande = sql_fetsel('*', 'spip_commandes', 'bank_uid = '.sql_quote($bank_uid))
		and $id_commande = intval($commande['id_commande'])
	) {
		include_spip('action/editer_objet');
		
		// Le prélèvement a échoué explicitement, donc la commande d'origine est en erreur
		objet_modifier('commande', $id_commande, array('statut' => 'erreur'));
	}
	
	return $flux;
}
