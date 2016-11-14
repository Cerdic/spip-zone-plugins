<?php
/**
 * Utilisations de pipelines par Réservation Evénements
 *
 * @plugin     Réservation événements
 * @copyright  2013
 * @author     Rainer Müller
 * @licence    GNU/GPL
 * @package    SPIP\Reservation_evenement\Pipelines
 */
if (! defined('_ECRIRE_INC_VERSION'))
	return;
	
	// Afficher les box infos et téléchargement des réservations
function reservation_evenement_affiche_gauche($flux) {
	include_spip('inc/presentation');
	$exec = $flux['args']['exec'];
	$objets_affichage = array (
		'rubrique',
		'article',
		'evenement' 
	);
	
	if (in_array($exec, $objets_affichage)) {
		include_spip('inc/config');
		include_spip('formulaires/selecteur/generique_fonctions');
		include_spip('inc/reservation_evenements');
		
		$config = lire_config('reservation_evenement/rubrique_reservation');
		$contexte = array ();
		$contexte['id_article'] = intval($flux['args']['id_article']) ? $flux['args']['id_article'] : '';
		$contexte['id_rubrique'] = intval($flux['args']['id_rubrique']) ? $flux['args']['id_rubrique'] : '';
		$contexte['id_evenement'] = intval($flux['args']['id_evenement']) ? $flux['args']['id_evenement'] : '';
		$id = $contexte['id_' . $exec];
		$rubrique_reservation = picker_selected($config, 'rubrique');
		$zone = rubrique_reservation($id, $exec, $rubrique_reservation);
		
		// Si l'objet se trouve dans la zone Reservation Evénement, on affiche
		if ($zone) {
			$flux['data'] .= recuperer_fond('inclure/reservations', $contexte);
		}
	}
	return $flux;
}

/**
 * Ajout de liste sur la vue d'un auteur
 *
 * @pipeline affiche_auteurs_interventions
 * 
 * @param array $flux
 *        	Données du pipeline
 * @return array Données du pipeline
 */
function reservation_evenement_affiche_auteurs_interventions($flux) {
	if ($id_auteur = intval($flux['args']['id_auteur'])) {
		$flux['data'] .= '<br class="nettoyeur"/>' . recuperer_fond('prive/objets/liste/reservations', array (
			'id_auteur' => $id_auteur,
			'titre' => _T('reservation:info_reservations_auteur') 
		), array (
			'ajax' => true 
		));
	}
	return $flux;
}
function reservation_evenement_affiche_milieu($flux) {
	$e = trouver_objet_exec($flux['args']['exec']);
	// reservations sur les événements
	if (! $e['edition'] and in_array($e['type'], array (
		'evenement' 
	))) {
		$contexte = calculer_contexte();
		$contexte['id_evenement'] = _request('id_evenement');
		$contexte['par'] = 'id_evenement';
		$texte .= recuperer_fond('prive/objets/liste/reservations_details', $contexte, array (
			'ajax' => 'oui' 
		));
		$flux['data'] .= $texte;
	}
	return $flux;
}

// Définitions des notifications pour https://github.com/abelass/notifications_archive
function reservation_evenement_notifications_archive($flux) {
	$flux = array_merge($flux, array (
		'reservation_client' => array (
			'activer' => 'on',
			'duree' => '180' 
		),
		'reservation_vendeur' => array (
			'duree' => '180' 
		) 
	));
	return $flux;
}

/*
 * Déclencher le cron si prévu dans la configuration
 */
function reservation_evenement_taches_generales_cron($taches) {
	include_spip('inc/config');
	$config = lire_config('reservation_evenement', array ());
	if (isset($config['cron'])) {
		// La périodicité
		if (isset($config['periodicite_cron']) and $config['periodicite_cron'] >= 600)
			$periodicite = $config['periodicite_cron'];
		else
			$periodicite = 24 * 3600;
		
		$taches['reservation_evenement_cloture'] = $periodicite;
	}
	return $taches;
}
function reservation_evenement_formulaire_charger($flux) {
	$form = $flux['args']['form'];
	$forms = array (
		'editer_article',
		'editer_evenement' 
	);
	$contexte = $flux['data'];
	
	// Charger les valeurs par défaut
	if (in_array($form, $forms)) {
		$action_cloture = $contexte['action_cloture'];
		$id_evenement = isset($contexte['id_evenement']) ? $contexte['id_evenement'] : '0';
		if ($form == $forms[1] and (! $action_cloture or $action_cloture == 0) and $form == 'editer_evenement' and intval($contexte['id_parent'])) {
			$action_cloture = sql_getfetsel('action_cloture', 'spip_articles', 'id_article=' . $contexte['id_parent']);
		}
		if ($action_cloture)
			$flux['data']['action_cloture'] = $action_cloture;
	}
	return $flux;
}
function reservation_evenement_formulaire_traiter($flux) {
	$form = $flux['args']['form'];
	$forms = array (
		'editer_article',
		'editer_evenement' 
	);
	if (in_array($form, $forms)) {
		list ($edit, $objet) = explode('_', $form);
		sql_updateq('spip_' . $objet . 's', array (
			'action_cloture' => _request('action_cloture') 
		), 'id_' . $objet . '=' . $flux['data']['id_' . $objet]);
	}
	return $flux;
}
function reservation_evenement_recuperer_fond($flux) {
	$fond = $flux['args']['fond'];
	
	$contexte = $flux['data']['contexte'];
	$fonds = array (
		'formulaires/editer_article' => 'article',
		'formulaires/editer_evenement' => 'evenement' 
	);
	// Ajouter le champ action_cloture
	if (isset($fonds[$fond])) {
		
		include_spip('inc/config');
		include_spip('formulaires/selecteur/generique_fonctions');
		include_spip('inc/reservation_evenements');
		$config = lire_config('reservation_evenement', array ());
		
		$type = $fonds[$fond];
		$rubrique_reservation = isset($config['rubrique_reservation']) ? picker_selected($config['rubrique_reservation'], 'rubrique') : '';
		$id = _request('id_' . $type);
		$zone = rubrique_reservation($id, $type, $rubrique_reservation);
		$cron = isset($config['cron']) ? $config['cron'] : '';
		
		// Si cron activé et l'objet se trouve dans la zone Reservation Evénement, on affiche
		if ($cron and $zone) {
			$action_cloture = '<ul>' . recuperer_fond('formulaires/inc-action_cloture', $contexte) . '</ul>';
			$flux['data']['texte'] = str_replace('<!--extra-->', $action_cloture . '<!--extra-->', $flux['data']['texte']);
		}
	}
	return $flux;
}

// ajouter le champ action_cloture
function reservation_evenement_afficher_contenu_objet($flux) {
	$type = $flux['args']['type'];
	$types = array (
		'article',
		'evenement' 
	);
	
	if (in_array($type, $types)) {
		include_spip('inc/config');
		include_spip('formulaires/selecteur/generique_fonctions');
		include_spip('inc/reservation_evenements');
		
		$config = lire_config('reservation_evenement', array ());
		$rubrique_reservation = isset($config['rubrique_reservation']) ? picker_selected($config['rubrique_reservation'], 'rubrique') : '';
		$id = _request('id_' . $type);
		$zone = rubrique_reservation($id, $type, $rubrique_reservation);
		$cron = isset($config['cron']) ? $config['cron'] : '';
		
		// Si cron activé et l'objet se trouve dans la zone Reservation Evénement, on affiche
		if ($cron and $zone) {
			$etats = array (
				1 => _T('item:oui'),
				2 => _T('item:non'),
				3 => _T('reservation:evenement_cloture') 
			);
			$action_cloture = sql_getfetsel('action_cloture', 'spip_' . $type . 's', 'id_' . $type . '=' . $type = $flux['args']['id_objet']);
			if ($action_cloture != 0)
				$contexte['cloture_etat'] = $etats[$action_cloture];
			$action_cloture = recuperer_fond('prive/objets/contenu/inc-action_cloture', $contexte);
			$flux['data'] .= "\n" . $action_cloture;
		}
	}
	return $flux;
}
