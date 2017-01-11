<?php

// Sécurité
if (! defined('_ECRIRE_INC_VERSION'))
	return;

	// Enregistrement d'une réservation
function inc_reservation_enregistrer_dist($id = '', $id_article = '', $id_auteur = '', $champs_extras_auteurs = '') {
	include_spip('inc/config');
	include_spip('inc/session');

	$config = lire_config('reservation_evenement');
	$statut = $config['statut_defaut'] ? $config['statut_defaut'] : 'rien';
	if ($statut == 'rien') {
		$statut_defaut = charger_fonction('defaut', 'inc/statuts');
		$statut = $statut_defaut($statut);
	}

	// Créer la réservation
	$action = charger_fonction('editer_objet', 'action');

	// La référence
	$fonction_reference = charger_fonction('reservation_reference', 'inc/');

	set_request('statut', $statut);
	$reference = $fonction_reference($id_auteur);
	set_request('reference', $reference);

	if (_request('enregistrer')) {
		include_spip('actions/editer_auteur');

		if (! $id_auteur) {
			include_spip('inc/auth');
			$res = formulaires_editer_objet_traiter('auteur', 'new', '', '', $retour, $config_fonc, $row, $hidden);
			$id_auteur = $res['id_auteur'];
			sql_updateq('spip_auteurs', array(
				'statut' => '6forum'
			), 'id_auteur=' . $id_auteur);
			$auteur = sql_fetsel('*', 'spip_auteurs', 'id_auteur=' . $id_auteur);
			auth_loger($auteur);
			set_request('id_auteur', $id_auteur);
		}
	}
	elseif (intval($id_auteur) and _request('modifier_donnees_auteur')) {
		// les champs extras auteur
		include_spip('cextras_pipelines');
		$valeurs_extras = array();

		if (! is_array($champs_extras_auteurs) and function_exists('champs_extras_objet')) {
			// Charger les définitions pour la création des formulaires
			$champs_extras_auteurs = champs_extras_objet(table_objet_sql('auteur'));
		}

		if (is_array($champs_extras_auteurs)) {
			foreach ($champs_extras_auteurs as $value) {
				$valeurs_extras[$value['options']['nom']] = _request($value['options']['nom']);
				session_set($value['options']['nom'], _request($value['options']['nom']));
			}
		}
		// mettre les valeurs dans la session pour garder les éventuelles modifications
		session_set('nom', _request('nom'));
		session_set('email', _request('email'));

		$valeurs = array_merge(array(
			'nom' => _request('nom'),
			'email' => _request('email')
		), $valeurs_extras);
		sql_updateq('spip_auteurs', $valeurs, 'id_auteur=' . $id_auteur);

		// Enlever pour ne pas les enregistrer dans la réservation
		set_request('nom', '');
		set_request('email', '');
	}

	$id_reservation = $action('new', 'reservation');

	// On ajoute l'id à la session
	$id_reservation = $id_reservation[0];
	if (! _request('id_reservation_source'))
		session_set('id_reservation', $id_reservation);

	$message = '<p>' . _T('reservation:reservation_enregistre') . '</p>';
	$message .= '<h3>' . _T('reservation:details_reservation') . '</h3>';
	$message .= recuperer_fond('inclure/reservation', array(
		'id_reservation' => $id_reservation
	));

	// Ivalider les caches
	include_spip('inc/invalideur');
	suivre_invalideur("id='reservation/$id_reservation'");
	suivre_invalideur("id='reservations_detail/$id_reservations_detail'");
	return array(
		'message_ok' => $message,
		'editable' => false
	);
}
