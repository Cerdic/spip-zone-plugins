<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Action d'abonnement à une notification
 * @param unknown_type $arg
 * @return unknown_type
 */
function action_abonner_notification_dist($arg=null, $modes=array(), $preferences=array()) {
	if (is_null($arg)){
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$arg = $securiser_action();
	}
	
	include_spip('inc/session');
	
	// Pour faire l'abonnement :
	// - $arg doit être de la forme "quoi-id"
	// - le visiteur doit être un auteur OU il doit y avoir un _request('contact')
	if (list($quoi, $id) = explode('-', $arg)
		and $quoi
		and (($id_auteur = session_get('id_auteur')) > 0 or $contact = _request('contact'))
	){
		include_spip('base/abtract_sql');
		
		// On nettoie l'une ou l'autre des informations
		if ($id_auteur) $contact = '';
		else $id_auteur = 0;
		
		// S'il n'y a pas de mode d'envoi dans l'appel, et pas dans l'environnement
		// alors on abonne par email par défaut
		if (!$modes and !$modes = _request('modes'))
			$modes = array('email');
		
		// S'il n'y a pas de préférences dans l'appel, on regarde dans l'environnement
		if (!$preferences and $prefs = _request('preferences') and is_array($prefs)){
			$preferences = $prefs;
		}
		
		// Enfin on insert l'abonnement
		sql_insertq(
			'spip_notifications_abonnements',
			array(
				'id_auteur' => $id_auteur,
				'contact' => $contact,
				'quoi' => $quoi,
				'id' => $id,
				'modes' => $modes,
				'preferences' => $preferences
			)
		);
	}
}

?>
