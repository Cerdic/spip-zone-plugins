<?php
/**
 * Fonctions (bis) du plugin Commandes
 *
 * @plugin     Commandes
 * @copyright  2014
 * @author     Ateliers CYM, Matthieu Marcillaud, Les Développements Durables
 * @licence    GPL 3
 * @package    SPIP\Commandes\Fonctions (bis)
 */

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Créer une commande en cours pour le visiteur actuel.
 *
 * @return int $id_commande
 *     identifiant SQL de la commande
**/
function creer_commande_encours(){
	include_spip('inc/session');

	// S'il y a une commande en cours dans la session, on la supprime
	if (($id_commande = intval(session_get('id_commande'))) > 0){
		// Si la commande est toujours "encours" on la supprime de la base
		if ($statut = sql_getfetsel('statut', 'spip_commandes', 'id_commande = '.$id_commande) and $statut == 'encours'){
			spip_log("Suppression d'une commande encours ancienne en session : $id_commande");
			commandes_effacer($id_commande);
		}

		// Dans tous les cas on supprime la valeur de session
		session_set('id_commande');
	}

	// Le visiteur en cours
	$id_auteur = session_get('id_auteur') > 0 ? session_get('id_auteur') : 0;

	// La référence
	$fonction_reference = charger_fonction('commandes_reference', 'inc/');

	$champs = array(
		'reference' => $fonction_reference($id_auteur),
		'id_auteur' => $id_auteur
	);

	// Création de la commande
	include_spip('action/editer_commande');
	$id_commande = commande_inserer(null,$champs);
	session_set('id_commande', $id_commande);

	return $id_commande;
}


/**
 * Suppression d'une ou plusieurs commandes
 * et de ses données associées
 *
 * @param int|array $ids_commandes
 *     Identifiant d'une commande ou tableau d'identifiants
 * @return bool
 *     false si pas d'identifiant de commande transmis
 *     true sinon
**/
function commandes_supprimer($ids_commandes) {
	if (!$ids_commandes) return false;
	if (!is_array($ids_commandes)) $ids_commandes = array($ids_commandes);

	spip_log("commandes_effacer : suppression de commande(s) : " . implode(',', $ids_commandes));

	$in_commandes = sql_in('id_commande', $ids_commandes);

	// On supprime ses détails
	sql_delete('spip_commandes_details', $in_commandes);

	// On dissocie les commandes et les adresses, et éventuellement on supprime ces dernières
	include_spip('action/editer_liens');
	if ($adresses_commandes = objet_trouver_liens(array('adresse'=>'*'), array('commande'=>$ids_commandes))) {
		$adresses_commandes = array_unique(array_map('reset',$adresses_commandes));

		// d'abord, on dissocie les adresses et les commandes
		spip_log("commandes_effacer : dissociation des adresses des commandes à supprimer : " . implode(',', $adresses_commandes));
		objet_dissocier(array('adresse'=>$adresses_commandes), array('commande'=>$ids_commandes));

		// puis si les adresses ne sont plus utilisées nul part, on les supprime
		foreach($adresses_commandes as $id_adresse)
			if (!count(objet_trouver_liens(array('adresse'=>$id_adresse), '*')))
				sql_delete(table_objet_sql('adresse'), "id_adresse=".intval($id_adresse));
	}

	// On supprime les commandes
	sql_delete(table_objet_sql('commande'), $in_commandes);

	return true;
}
/**
 * Alias de commandes_supprimer pour rétro compatibilité
 */
function commandes_effacer($ids_commandes) {
	return commandes_supprimer($ids_commandes);
}


/**
 * Suppression d'un ou plusieurs détails d'une commande
 * et éventuellement de la commande si elle est vide après l'opération
 *
 * @param int $id_commande
 *     Identifiant de la commande
 * @param int|array $ids_commandes_details
 *     Identifiant d'un détail ou tableau d'identifiants
 * @param bool $supprimer_commande
 *     true pour effacer la commande si elle est vide après l'opération
 * @return bool
 *     false si pas d'identifiant de commande transmis, ou si pas autorisé à supprimer
 *     true sinon
**/
function commandes_supprimer_detail($id_commande=0, $ids_details=array(), $supprimer_commande=false) {

	if (!$id_commande) return false;
	if (!is_array($ids_details)) $ids_details = array($ids_details);

	include_spip('inc/autoriser');
	if (autoriser('supprimerdetail','commande',$id_commande)) {
		$nb_details = sql_countsel('spip_commandes_details', "id_commande=".intval($id_commande));
		// suppression des détails
		foreach($ids_details as $id_detail)
			sql_delete('spip_commandes_details', "id_commande=".intval($id_commande) . " AND id_commandes_detail=".intval($id_detail));
		// optionnellement, si la commande est vide, on la supprime
		if ($nb_details == count($ids_details) and $supprimer_commande)
			commande_supprimer($id_commande);
		return true;
	} else {
		return false;
	}
}


/*
 * Envoyer un mail de notification
 * => On veut envoyer du html pour que le tableau de commandes soit lisible par le client
 * => On peut avoir un expediteur specifique
 * => Mais notifications_envoyer_mails() de spip ne peut pas envoyer de mails en html. On ne peut pas non plus y specifier un expediteur.
 * Donc si les plugins notifications_avancees et Facteur sont presents, on prepare un joli mail en html. Sinon un moche en texte.
 *
 * @param string $qui : vendeur ou client
 * @param string $id_type
 * @param int $id_commande
 * @param string $expediteur
 * @param array $destinataires
 *
 */
function commandes_envoyer_notification( $qui, $id_type, $id_commande, $expediteur, $destinataires){
	spip_log("commandes_envoyer_notification qui? $qui, id_type $id_type, id_commande $id_commande, expediteur $expediteur, destinataires ".implode(", ", $destinataires),'commandes');

	notifications_nettoyer_emails($destinataires);

	if(defined('_DIR_PLUGIN_NOTIFAVANCEES') && defined('_DIR_PLUGIN_FACTEUR')) {
		spip_log("commandes_envoyer_notification via Notifications avancées",'commandes');
		if (
			!notifications_envoyer(
				$destinataires,
				"email",
				"commande_".$qui,
				$id_commande,
				$options=array('from'=>$expediteur))
		)
			spip_log("commandes_envoyer_notification Erreur d'envoi via Notifications avancées",'commandes');
	} else {
		$texte = recuperer_fond("notifications/commande",array(
			$id_type=>$id_commande,
			"id"=>$id_commande,
			"format_envoi"=>"plain",
			"qui"=>$qui));
		if( $qui == "client" ) {
			$sujet = _T('commandes:votre_commande_sur', array('nom'=>$GLOBALS['meta']["nom_site"])) ;
		} else {
			$sujet = _T('commandes:une_commande_sur', array('nom'=>$GLOBALS['meta']["nom_site"])) ;
		}
		// Si un expediteur est impose, on doit utiliser la fonction envoyer_email pour rajouter l'expediteur
		if ($expediteur) {
			$envoyer_mail = charger_fonction('envoyer_mail','inc');
			spip_log("commandes_envoyer_notification via $envoyer_mail",'commandes');
			if( !$envoyer_mail($destinataires, $sujet, $texte, $expediteur))
				spip_log("commandes_envoyer_notification Erreur d'envoi via $envoyer_mail",'commandes');

		} else {
			spip_log("commandes_envoyer_notification via notifications_envoyer_mails",'commandes');
			if ( !notifications_envoyer_mails($destinataires, $texte, $sujet) )
				spip_log("commandes_envoyer_notification Erreur d'envoi via notifications_envoyer_mails",'commandes');
		}
	}
}


/*
 * Traitement des notifications d'une commande
 * Selon les options de configuration, des emails seront envoyés au(x) vendeur(s) et optionnellement au client
 * 
 * @param int|string $id_commande
 *     identifiant de la commande
 * @return void
 */
function traiter_notifications_commande($id_commande=0){

	if (intval($id_commande)==0) return;

	if (
		include_spip('inc/config')
		and $config = lire_config('commandes')
		and $quand = $config['quand'] ? $config['quand'] : array()
		and $config['activer'] // les notifications sont activées
		and $statut = sql_getfetsel('statut', table_objet_sql('commande'), "id_commande=".intval($id_commande))
		and in_array($statut, $quand) // le nouveau statut est valide pour envoyer une notification
		and $notifications = charger_fonction('notifications', 'inc', true) // la fonction est bien chargée
	) {

		// Sans les plugins Facteur et Notifications avancées, on ne fait rien
		if (!defined('_DIR_PLUGIN_NOTIFAVANCEES')) {
			spip_log("traiter_notifications_commande : notifications impossibles sans le plugins Notifications avancées pour la commande $id_commande",'commandes.' . _LOG_ERREUR);
			return;
		}

		// Déterminer l'expéditeur
		$options = array();
		if( $config['expediteur'] != "facteur" )
			$options['expediteur'] = $config['expediteur_'.$config['expediteur']];

		// Envoyer au vendeur
		spip_log("traiter_notifications_commande : notification vendeur pour la commande $id_commande",'commandes.' . _LOG_INFO);
		$notifications('commande_vendeur', $id_commande, $options);

		// Envoyer optionnellement au client
		if($config['client']) {
			spip_log("traiter_notifications_commande : notification client pour la commande $id_commande",'commandes.' . _LOG_INFO);
			$notifications('commande_client', $id_commande, $options);
		}

	}
}

?>
