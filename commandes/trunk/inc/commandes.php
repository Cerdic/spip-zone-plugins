<?php
/**
 * Fonctions (bis) du plugin Commandes
 *
 * @plugin     Commandes
 * @copyright  2013
 * @author     Ateliers CYM, Matthieu Marcillaud, Les Développements Durables
 * @licence    GPL 3
 * @package    SPIP\Commandes\Fonctions (bis)
 */

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Créer une commande en cours pour le visiteur actuel.
 *
 * @return int $id_commande Retourne l'identifiant SQL de la commande
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
		'id_auteur' => $id_auteur,
		'date' => date('Y-m-d H:i:s'),
		'statut' => 'encours'
	);
	
	// Envoyer aux plugins avant insertion
	$champs = pipeline('pre_insertion',
		array(
			'args' => array(
				'table' => 'spip_commandes',
			),
			'data' => $champs
		)
	);
	$id_commande = sql_insertq('spip_commandes', $champs);
	// Envoyer aux plugins après insertion
	pipeline('post_insertion',
		array(
			'args' => array(
				'table' => 'spip_commandes',
				'id_objet' => $id_commande
			),
			'data' => $champs
		)
	);
	
	session_set('id_commande', $id_commande);
	
	return $id_commande;
}



/**
 * Suppression d'une ou plusieurs commandes
 * et de ses données associées
 *
 * @param int|array $ids_commande
 *     Identifiant de commande ou tableau d'identifiants
 * @return bool
 *     false si pas d'identifiant de commande transmis
 *     true sinon.
**/
function commandes_effacer($ids_commande) {
	if (!$ids_commande) return false;
	if (!is_array($ids_commande)) $ids_commande = array($ids_commande);

	spip_log("Suppression de commande : " . implode(',', $ids_commande));

	$in_commandes = sql_in('id_commande', $ids_commande);
	$in_objet_commandes = sql_in('id_objet', $ids_commande);

	// On supprime son contenu
	sql_delete('spip_commandes_details', $in_commandes);

	// S'il y a des adresses attachées aux commandes et inutiliséses ailleurs, on les supprime
	if ($adresses_commande = sql_allfetsel('id_adresse', 'spip_adresses_liens', array('objet = '.sql_quote('commande'), $in_objet_commandes))){
		$adresses_commande = array_map('reset', $adresses_commande);
		spip_log("Suppression d'adresses des commandes supprimées : " . implode(',', $adresses_commande));
		$in_adresses = sql_in('id_adresse', $adresses_commande);
		sql_delete('spip_adresses_liens', array($in_adresses, 'objet='.sql_quote('commande'), $in_objet_commandes));

		// si les adresses ne sont plus utilisées nul part, on les supprime
		$adresses_non_orphelines = sql_allfetsel('id_adresse', 'spip_adresses_liens', $in_adresses);
		$adresses_non_orphelines = array_map('reset', $adresses_non_orphelines);
		$adresses_orphelines = array_diff($adresses_commande, $adresses_non_orphelines);
		if ($adresses_orphelines) {
			spip_log("Suppression d'adresses orphelines : " . implode(',', $adresses_orphelines));
			sql_delete('spip_adresses', sql_in('id_adresse', $adresses_orphelines));
		}
	}

	// On supprime les commandes
	sql_delete('spip_commandes', $in_commandes);

	return true;
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
		if( !notifications_envoyer( $destinataires,
											 "email",
											 "commande_".$qui,
											 $id_commande,
											 $options=array('from'=>$expediteur)))
			spip_log("commandes_envoyer_notification Erreur d'envoi via Notifications avancées",'commandes');
	} else {
		$texte = recuperer_fond("notifications/commande",array($id_type=>$id_commande,
																				 "id"=>$id_commande,
																				 "format_envoi"=>"plain",
																				 "qui"=>$qui));
		if( $qui == "client" ) {
			$sujet = _T('commandes:votre_commande_sur', array('nom'=>$GLOBALS['meta']["nom_site"])) ;
		} else {
			$sujet = _T('commandes:une_commande_sur', array('nom'=>$GLOBALS['meta']["nom_site"])) ;
		}
		// Si un expediteur est impose, on doit utiliser la fonction envoyer_email pour rajouter l'expediteur
		if($expediteur) {
			$envoyer_mail = charger_fonction('envoyer_mail','inc');
			spip_log("commandes_envoyer_notification via $envoyer_mail",'commandes');
			if( !$envoyer_mail($destinataires, $sujet, $texte, $expediteur))
				spip_log("commandes_envoyer_notification Erreur d'envoi via $envoyer_mail",'commandes');

		} else {
			spip_log("commandes_envoyer_notification via notifications_envoyer_mails",'commandes');
			if( !notifications_envoyer_mails($destinataires, $texte, $sujet))
				spip_log("commandes_envoyer_notification Erreur d'envoi via notifications_envoyer_mails",'commandes');
		}
	}
}
?>
