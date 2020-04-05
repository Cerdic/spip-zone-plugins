<?php
/**
 * Fonctions (bis) du plugin Commandes
 *
 * @plugin     Commandes
 * @copyright  2014
 * @author     Ateliers CYM, Matthieu Marcillaud, Les Développements Durables
 * @licence    GPL 3
 * @package    SPIP\Commandes\Fonctions
 */

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')){
	return;
}

/**
 * Créer une commande avec le statut `encours` pour le visiteur actuel.
 *
 * On part du principe qu'il ne peut y avoir qu'une seule commande `encours` par session,
 * aussi on supprime de la base toute ancienne commande `encours` présente en session avant d'en créer une nouvelle.
 * L'identifiant de la nouvelle commande est ensuite placé dans la session.
 *
 * Si le visiteur n'est pas identifie mais connu, on peut passer son id_auteur en argument pour permettre le rattachement de la commande a son compte
 * mais attention dans tous les cas la commande sera associee a la session en cours.
 * C'est utile si par exemple on demande l'email au visiteur dans le processus d'achat mais on veut pas l'obliger a se connecter pour simplifier le workflow :
 * il peut faire tout le processus en restant non connecte, mais la commande sera quand meme rattachee a son compte
 * Et ca permet aussi de faire une commande pour le compte de quelqu'un d'autre sans avoir besoin de ses identifiants (ie payer un abonnement a un ami)
 *
 * @uses commandes_reference()
 * @uses commande_inserer()
 *
 * @param int $id_auteur
 *   permet de preciser l'id_auteur de la session au cas ou le visiteur n'est pas connecte mais connu
 *   (par son email qu'il a rentre dans le processus de commande par exemple)
 * @return int $id_commande
 *     identifiant SQL de la commande
 */
function creer_commande_encours($id_auteur = 0){
	include_spip('inc/session');

	// S'il y a une commande en cours dans la session, on la supprime
	if (($id_commande = intval(session_get('id_commande')))>0){
		// Si la commande est toujours "encours" il faut la mettre a la poubelle
		// il ne faut pas la supprimer tant qu'il n'y a pas de nouvelles commandes pour etre sur qu'on reutilise pas son numero
		// (sous sqlite la nouvelle commande reprend le numero de l'ancienne si on fait delete+insert)
		if ($statut = sql_getfetsel('statut', 'spip_commandes', 'id_commande = ' . intval($id_commande)) AND $statut=='encours'){
			spip_log("Commande ancienne encours->poubelle en session : $id_commande", 'commandes');
			sql_updateq("spip_commandes", array('statut' => 'poubelle'), 'id_commande = ' . intval($id_commande));
		}
		// Dans tous les cas on supprime la valeur de session
		session_set('id_commande');
	}

	// Le visiteur en cours
	if (!$id_auteur and session_get('id_auteur')>0){
		$id_auteur = session_get('id_auteur');
	}

	$champs = array(
		'id_auteur' => $id_auteur
	);

	// Création de la commande
	include_spip('action/editer_commande');
	$id_commande = commande_inserer(null, $champs);
	session_set('id_commande', $id_commande);

	return $id_commande;
}

/**
 * Supprimer une ou plusieurs commandes et leurs données associées
 *
 * La fonction va supprimer :
 *
 * - les détails des commandes
 * - les liens entre les commandes et leurs adresses
 * - les adresses si elles sont devenues orphelines
 *
 * @param int|array $ids_commandes
 *     Identifiant d'une commande ou tableau d'identifiants
 * @return bool
 *     - false si pas d'identifiant de commande transmis
 *     - true sinon
 **/
function commandes_supprimer($ids_commandes){
	if (!$ids_commandes){
		return false;
	}
	if (!is_array($ids_commandes)){
		$ids_commandes = array($ids_commandes);
	}

	spip_log("commandes_effacer : suppression de commande(s) : " . implode(',', $ids_commandes), 'commandes');

	$in_commandes = sql_in('id_commande', $ids_commandes);

	// On supprime ses détails
	sql_delete('spip_commandes_details', $in_commandes);

	// On dissocie les commandes et les adresses, et éventuellement on supprime ces dernières
	include_spip('action/editer_liens');
	if ($adresses_commandes = objet_trouver_liens(array('adresse' => '*'), array('commande' => $ids_commandes))){
		$adresses_commandes = array_unique(array_map('reset', $adresses_commandes));

		// d'abord, on dissocie les adresses et les commandes
		spip_log("commandes_effacer : dissociation des adresses des commandes à supprimer : " . implode(',', $adresses_commandes), 'commandes');
		objet_dissocier(array('adresse' => $adresses_commandes), array('commande' => $ids_commandes));

		// puis si les adresses ne sont plus utilisées nul part, on les supprime
		foreach ($adresses_commandes as $id_adresse)
			if (!count(objet_trouver_liens(array('adresse' => $id_adresse), '*'))){
				sql_delete(table_objet_sql('adresse'), "id_adresse=" . intval($id_adresse));
			}
	}

	// On supprime les commandes
	sql_delete(table_objet_sql('commande'), $in_commandes);

	return true;
}

/**
 * Supprimer des commandes
 *
 * @deprecated Alias de commandes_supprimer() pour rétro-compatibilité
 * @see commandes_supprimer()
 *
 * @param int|array $ids_commandes
 *     Identifiant d'une commande ou tableau d'identifiants
 * @return bool
 *     - false si pas d'identifiant de commande transmis
 *     - true sinon
 */
function commandes_effacer($ids_commandes){
	return commandes_supprimer($ids_commandes);
}

/**
 * Ajouter une ligne de detail dans une commande
 * @param int $id_commande
 * @param array $emplette
 *   objet : type de l'objet ajoute
 *   id_objet : id de l'objet ajoute
 *   quantite : quantite ajoutee
 * @param bool $ajouter
 * @return int
 */
function commandes_ajouter_detail($id_commande, $emplette, $ajouter = true){
	static $fonction_prix, $fonction_prix_ht;
	if (!$fonction_prix OR !$fonction_prix_ht){
		$fonction_prix = charger_fonction('prix', 'inc/');
		$fonction_prix_ht = charger_fonction('ht', 'inc/prix');
	}

	// calculer la taxe
	$prix_ht = $fonction_prix_ht($emplette['objet'], $emplette['id_objet'], 6);
	$prix = $fonction_prix($emplette['objet'], $emplette['id_objet'], 6);
	if ($prix_ht>0){
		$taxe = round(($prix-$prix_ht)/$prix_ht, 3);
	} else {
		$taxe = 0;
	}

	$set = array(
		'id_commande' => $id_commande,
		'objet' => $emplette['objet'],
		'id_objet' => $emplette['id_objet'],
		'descriptif' => generer_info_entite($emplette['id_objet'], $emplette['objet'], 'titre', '*'),
		'quantite' => $emplette['quantite'],
		'prix_unitaire_ht' => $prix_ht,
		'taxe' => $taxe,
		'statut' => 'attente'
	);

	// chercher si une ligne existe deja ou l'ajouter
	$where = array();
	foreach ($set as $k => $w){
		if (in_array($k, array('id_commande', 'objet', 'id_objet'))){
			$where[] = "$k=" . sql_quote($w);
		}
	}

	include_spip('action/editer_objet');
	// est-ce que cette ligne est deja la ?
	if ($ajouter
		or !$id_commandes_detail = sql_getfetsel("id_commandes_detail", "spip_commandes_details", $where)){
		// sinon création et renseignement du détail de la commande
		$id_commandes_detail = objet_inserer('commandes_detail');
	}

	// la mettre a jour
	if ($id_commandes_detail){
		objet_modifier('commandes_detail', $id_commandes_detail, $set);
	}

	return $id_commandes_detail;
}

/**
 * Supprimer un ou plusieurs détails d'une commande
 *
 * On supprime les détails correspondant à commande dans la table `spip_commandes_details`.
 * Si tous ses détails sont supprimés par l'opération, la commande peut également être supprimée en présence du paramètre adéquat.
 *
 * @uses commandes_supprimer()
 *
 * @param int $id_commande
 *     Identifiant de la commande
 * @param int|array $ids_details
 *     Identifiant d'un détail ou tableau d'identifiants
 * @param bool $supprimer_commande
 *     true pour effacer la commande si elle est vide après l'opération
 * @return bool
 *     false si pas d'identifiant de commande transmis, ou si pas autorisé à supprimer
 *     true sinon
 */
function commandes_supprimer_detail($id_commande = 0, $ids_details = array(), $supprimer_commande = false){

	if (!$id_commande){
		return false;
	}
	if (!is_array($ids_details)){
		$ids_details = array($ids_details);
	}

	include_spip('inc/autoriser');
	if (autoriser('supprimerdetail', 'commande', $id_commande)){
		$nb_details = sql_countsel('spip_commandes_details', "id_commande=" . intval($id_commande));
		// suppression des détails
		foreach ($ids_details as $id_detail)
			sql_delete('spip_commandes_details', "id_commande=" . intval($id_commande) . " AND id_commandes_detail=" . intval($id_detail));
		// optionnellement, si la commande est vide, on la supprime
		if ($nb_details==count($ids_details) and $supprimer_commande){
			commandes_supprimer($id_commande);
		}
		return true;
	} else {
		return false;
	}
}


/**
 * Envoyer un mail de notification
 *
 * - On veut envoyer du html pour que le tableau de commandes soit lisible par le client
 * - On peut avoir un expediteur specifique
 * - Mais `notifications_envoyer_mails()` de spip ne peut pas envoyer de mails en html. On ne peut pas non plus y specifier un expediteur.
 * Donc si les plugins notifications_avancees et Facteur sont presents, on prepare un joli mail en html. Sinon un moche en texte.
 *
 * @deprecated Voir traiter_notifications_commande()
 *
 * @param string $qui : vendeur ou client
 * @param string $id_type
 * @param int $id_commande
 * @param string $expediteur
 * @param array $destinataires
 *
 */
function commandes_envoyer_notification($qui, $id_type, $id_commande, $expediteur, $destinataires){
	spip_log("commandes_envoyer_notification qui? $qui, id_type $id_type, id_commande $id_commande, expediteur $expediteur, destinataires " . implode(", ", $destinataires), 'commandes');

	notifications_nettoyer_emails($destinataires);

	if (defined('_DIR_PLUGIN_NOTIFAVANCEES') && defined('_DIR_PLUGIN_FACTEUR')){
		spip_log("commandes_envoyer_notification via Notifications avancées", 'commandes');
		if (
		!notifications_envoyer(
			$destinataires,
			"email",
			"commande_" . $qui,
			$id_commande,
			$options = array('from' => $expediteur))
		){
			spip_log("commandes_envoyer_notification Erreur d'envoi via Notifications avancées", 'commandes');
		}
	} else {
		$texte = recuperer_fond("notifications/commande", array(
			$id_type => $id_commande,
			"id" => $id_commande,
			"format_envoi" => "plain",
			"qui" => $qui));
		if ($qui=="client"){
			$sujet = _T('commandes:votre_commande_sur', array('nom' => $GLOBALS['meta']["nom_site"]));
		} else {
			$sujet = _T('commandes:une_commande_sur', array('nom' => $GLOBALS['meta']["nom_site"]));
		}
		// Si un expediteur est impose, on doit utiliser la fonction envoyer_email pour rajouter l'expediteur
		if ($expediteur){
			$envoyer_mail = charger_fonction('envoyer_mail', 'inc');
			spip_log("commandes_envoyer_notification via $envoyer_mail", 'commandes');
			if (!$envoyer_mail($destinataires, $sujet, $texte, $expediteur)){
				spip_log("commandes_envoyer_notification Erreur d'envoi via $envoyer_mail", 'commandes');
			}

		} else {
			spip_log("commandes_envoyer_notification via notifications_envoyer_mails", 'commandes');
			if (!notifications_envoyer_mails($destinataires, $texte, $sujet)){
				spip_log("commandes_envoyer_notification Erreur d'envoi via notifications_envoyer_mails", 'commandes');
			}
		}
	}
}


/**
 * Traitement des notifications par email d'une commande
 *
 * Selon les options de configuration et le statut de la commande, des emails seront envoyés au(x) vendeur(s) et optionnellement au client.
 * Nécessite le plugin "Notifications avancées" pour fonctionner.
 * Avec le plugin Facteur, les messages seront au format HTML, sinon au format texte.
 *
 * @uses notifications()
 *
 * @param int|string $id_commande
 *     Identifiant de la commande
 * @param string|null $statut_ancien
 * @return void
 */
function commandes_notifier($id_commande = 0, $statut_ancien = null){

	if (intval($id_commande)==0){
		return;
	}

	if (
		include_spip('inc/config')
		and $config = lire_config('commandes')
		and $quand = ($config['quand'] ? $config['quand'] : array())
		and $config['activer'] // les notifications sont activées
		and $statut = sql_getfetsel('statut', table_objet_sql('commande'), "id_commande=" . intval($id_commande))
		and in_array($statut, $quand) // le nouveau statut est valide pour envoyer une notification
		and $notifications = charger_fonction('notifications', 'inc', true) // la fonction est bien chargée
	){

		// Sans les plugins Facteur et Notifications avancées, on ne fait rien
		if (!defined('_DIR_PLUGIN_NOTIFAVANCEES')){
			spip_log("traiter_notifications_commande : notifications impossibles sans le plugins Notifications avancées pour la commande $id_commande", 'commandes.' . _LOG_ERREUR);
			return;
		}

		// Déterminer l'expéditeur
		$options = array(
			'statut' => $statut,
		);
		if (!is_null($statut_ancien)) {
			$options['statut_ancien'] = $statut_ancien;
		}

		if ($config['expediteur']!="facteur"){
			$options['expediteur'] = $config['expediteur_' . $config['expediteur']];
		}

		include_spip('inc/utils');

		// Envoyer au vendeur
		spip_log("commandes_notifier : notification vendeur pour la commande #$id_commande " . json_encode($options), 'commandes.' . _LOG_INFO);
		$notifications('commande_vendeur', $id_commande, $options);

		// Envoyer optionnellement au client
		if ($config['client']){

			spip_log("commandes_notifier : notification client pour la commande #$id_commande " . json_encode($options), 'commandes.' . _LOG_INFO);
			$notifications('commande_client', $id_commande, $options);
		}

	}
}


/**
 * Mettre a jour les taxes d'une commande selon exoneration ou non
 * @param int $id_commande
 * @param string $exoneration_raison
 */
function commandes_appliquer_taxes($id_commande, $exoneration_raison){
	$commande = sql_fetsel('*', 'spip_commandes', 'id_commande=' . intval($id_commande));
	if (!$commande){
		return;
	}

	$exoneration_raison = trim($exoneration_raison);
	if ($commande['taxe_exoneree_raison']!==$exoneration_raison){
		include_spip('action/editer_commande');
		commande_modifier($id_commande, array('taxe_exoneree_raison' => $exoneration_raison));
	}

}


/**
 * legacy
 * @uses commandes_notifier()
 * @param int $id_commande
 */
function traiter_notifications_commande($id_commande = 0){
	return commandes_notifier($id_commande);
}

