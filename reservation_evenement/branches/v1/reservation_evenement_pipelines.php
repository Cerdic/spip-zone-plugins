<?php
/**
 * Utilisations de pipelines par Réservation Evénements
 *
 * @plugin     Réservation événements
 * @copyright  2013 - 2018
 * @author     Rainer Müller
 * @licence    GNU/GPL
 * @package    SPIP\Reservation_evenement\Pipelines
 */
if (!defined('_ECRIRE_INC_VERSION'))
	return;

// Afficher les box infos et téléchargement des réservations
function reservation_evenement_affiche_gauche($flux) {
	include_spip('inc/reservation_evenements');
	$exec = $flux['args']['exec'];
	$objets_affichage = array(
		'rubrique',
		'article',
		'evenement'
	);

	if (in_array($exec, $objets_affichage)) {
		include_spip('inc/presentation');
		include_spip('inc/config');
		include_spip('formulaires/selecteur/generique_fonctions');
		include_spip('inc/reservation_evenements');

		$config = lire_config('reservation_evenement/rubrique_reservation');
		$contexte = array();
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

	$definition_objets_navigation = re_objets_navigation();
	$objets_navigation = array_column($definition_objets_navigation, 'objets');

	$objets_navigation = array_reduce($objets_navigation, function ($a, $b) {
		return array_merge($a, (array) $b);
	}, []);

	if (in_array($exec, $objets_navigation)) {
		include_spip('inc/config');
		$config = lire_config('reservation_evenement');
		$selection_objets_navigation = isset($config['selection_objets_navigation']) ? $config['selection_objets_navigation'] : '';
		$objets_navigation = isset($config['objets_navigation']) ? $config['objets_navigation'] : array();
		if (!$selection_objets_navigation or ($selection_objets_navigation and in_array($exec, $objets_navigation))) {
			$soustitre = isset($definition_objets_navigation[$exec]) ? $definition_objets_navigation[$exec]['label'] : (isset($definition_objets_navigation[$exec . 's']) ? $definition_objets_navigation[$exec . 's']['label'] : '');

			$contexte = $flux['args'];
			if ($soustitre) {
				$contexte['soustitre'] = $soustitre;
			}
			$flux['data'] .= recuperer_fond('prive/squelettes/navigation/reservations', $contexte);
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
		$flux['data'] .= '<br class="nettoyeur"/>' . recuperer_fond('prive/objets/liste/reservations', array(
			'id_auteur' => $id_auteur,
			'titre' => _T('reservation:info_reservations_auteur')
		), array(
			'ajax' => true
		));
	}
	return $flux;
}
function reservation_evenement_affiche_milieu($flux) {
	$e = trouver_objet_exec($flux['args']['exec']);
	// reservations sur les événements
	if (!$e['edition'] and in_array($e['type'], array(
		'evenement'
	))) {
		$contexte = calculer_contexte();
		$contexte['id_evenement'] = _request('id_evenement');
		$contexte['par'] = 'id_evenement';
		$texte .= recuperer_fond('prive/objets/liste/reservations_details', $contexte, array(
			'ajax' => 'oui'
		));
		$flux['data'] .= $texte;
	}
	return $flux;
}

// Définitions des notifications pour https://github.com/abelass/notifications_archive
function reservation_evenement_notifications_archive($flux) {
	$flux = array_merge($flux, array(
		'reservation_client' => array(
			'activer' => 'on',
			'duree' => '180'
		),
		'reservation_vendeur' => array(
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
	$config = lire_config('reservation_evenement', array());
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
	$forms = array(
		'editer_article',
		'editer_evenement'
	);
	$contexte = $flux['data'];

	// Charger les valeurs par défaut
	if (in_array($form, $forms)) {
		$action_cloture = $contexte['action_cloture'];
		$id_evenement = isset($contexte['id_evenement']) ? $contexte['id_evenement'] : '0';
		if ($form == $forms[1] and (!$action_cloture or $action_cloture == 0) and $form == 'editer_evenement' and intval($contexte['id_parent'])) {
			$action_cloture = sql_getfetsel('action_cloture', 'spip_articles', 'id_article=' . $contexte['id_parent']);
		}
		if ($action_cloture)
			$flux['data']['action_cloture'] = $action_cloture;
	}
	return $flux;
}
function reservation_evenement_formulaire_traiter($flux) {
	$form = $flux['args']['form'];
	$forms = array(
		'editer_article',
		'editer_evenement'
	);
	if (in_array($form, $forms)) {
		list($edit, $objet) = explode('_', $form);
		sql_updateq('spip_' . $objet . 's', array(
			'action_cloture' => _request('action_cloture')
		), 'id_' . $objet . '=' . $flux['data']['id_' . $objet]);
	}
	return $flux;
}

/**
 * Permet de compléter ou modifier le résultat de la compilation d’un squelette donné.
 *
 * @pipeline recuperer_fond
 *
 * @param array $flux
 *        	Données du pipeline
 * @return array Données du pipeline
 */
function reservation_evenement_recuperer_fond($flux) {
	$fond = $flux['args']['fond'];

	$contexte = $flux['data']['contexte'];
	$fonds = array(
		'formulaires/editer_article' => 'article',
		'formulaires/editer_evenement' => 'evenement'
	);
	// Ajouter le champ action_cloture
	if (isset($fonds[$fond])) {

		include_spip('inc/config');
		include_spip('formulaires/selecteur/generique_fonctions');
		include_spip('inc/reservation_evenements');
		$config = lire_config('reservation_evenement', array());

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

	// Enlever le lien vers résultats de agenda.
	if ($fond == 'prive/objets/contenu/evenement') {
		include_spip('inc/config');
		$afficher_inscription_agenda = lire_config('reservation_evenement/afficher_inscription_agenda', '');

		if (!$afficher_inscription_agenda) {
			$flux['data']['texte'] = preg_replace('/(\<div\ class="champ contenu_places"\>)(.+)(<\/div>)/si', '', $flux['data']['texte']);
		}
	}
	return $flux;
}

// ajouter le champ action_cloture
function reservation_evenement_afficher_contenu_objet($flux) {
	$type = $flux['args']['type'];
	$types = array(
		'article',
		'evenement'
	);

	if (in_array($type, $types)) {
		include_spip('inc/config');
		include_spip('formulaires/selecteur/generique_fonctions');
		include_spip('inc/reservation_evenements');

		$config = lire_config('reservation_evenement', array());
		$rubrique_reservation = isset($config['rubrique_reservation']) ? picker_selected($config['rubrique_reservation'], 'rubrique') : '';
		$id = _request('id_' . $type);
		$zone = rubrique_reservation($id, $type, $rubrique_reservation);
		$cron = isset($config['cron']) ? $config['cron'] : '';

		// Si cron activé et l'objet se trouve dans la zone Reservation Evénement, on affiche
		if ($cron and $zone) {
			$etats = array(
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

/**
 * Optimiser la base de donnée en supprimant toutes les reservations en cours qui sont trop vieilles
 *
 * Le délai de "péremption" est défini dans les options de configuration du plugin
 *
 * @pipeline optimiser_base_disparus
 *
 * @param array $flux
 *        	Données du pipeline
 * @return array Données du pipeline
 */
function reservation_evenement_optimiser_base_disparus($flux) {
	include_spip('inc/config');
	// les config
	$config_reservation = lire_config('reservation_evenement');

	// Les réservations en statut par défaut.
	$heures = isset($config_reservation['duree_vie']) ? $config_reservation['duree_vie'] : 0;

	if ($heures > 0) {
		$statut_defaut = isset($config_reservation['statut_defaut']) ? $config_reservation['statut_defaut'] : 'encours';
		$depuis = date('Y-m-d H:i:s', time() - 3600 * intval($heures));

		// On récupère les réservations trop vieilles
		$reservations = sql_allfetsel('id_reservation', 'spip_reservations', 'statut = ' . sql_quote($statut_defaut) . ' and date<' . sql_quote($depuis));

		// S'il y a bien des réservations à supprimer
		if ($reservations) {
			$reservations = array_map('reset', $reservations);
			include_spip('inc/reservation_evenements');
			reservations_supprimer($reservations);
			$flux['data'] += count($reservations);
		}
	}

	// Les réservations en statut "poubelles".
	$heures = isset($config_reservation['duree_vie_poubelle']) ? $config_reservation['duree_vie_poubelle'] : 0;

	if ($heures > 0) {
		$depuis = date('Y-m-d H:i:s', time() - 3600 * intval($heures));

		// On récupère les réservations trop vieilles
		$reservations = sql_allfetsel('id_reservation', 'spip_reservations', 'statut = ' . sql_quote('poubelle') . ' and maj<' . sql_quote($depuis));

		// S'il y a bien des réservations à supprimer
		if ($reservations) {
			$reservations = array_map('reset', $reservations);
			include_spip('inc/reservation_evenements');
			reservations_supprimer($reservations);
			$flux['data'] += count($reservations);
		}
	}

	return $flux;
}

/**
 * Pipeline de la corbeille, permet de définir les objets à supprimer
 *
 * @param array $param
 *        	Tableau d'objets
 *
 * @return array Tableau d'objets complété
 */
function reservation_evenement_corbeille_table_infos($param) {
	$param['reservations'] = array(
		'statut' => 'poubelle',
		'table' => 'reservations',
		'tableliee' => array(
			'spip_reservations_details'
		)
	);

	return $param;
}
/**
 * Ajoute des valeurs pour les mssages personnalisés
 *
 * @param array $flux
 * @return array
 */
function reservation_evenement_mp_data_objet($flux) {
	$type = $flux['args']['type'];

	// Ajouter les champs extras, stockes dans la table reservation.
	if ($type == 'fond_reservation_body' && isset($flux['data']['reservation_donnees_auteur']) &&
			$reservation_donnees_auteur = unserialize($flux['data']['reservation_donnees_auteur']) and
			is_array($reservation_donnees_auteur)) {
		foreach ($reservation_donnees_auteur as $champ => $valeur) {
			$flux['data']['reservation_' . $champ] = $valeur;
		}
	}
	return $flux;
}
