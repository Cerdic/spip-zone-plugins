<?php

/**
 * Saisies du formulaire d'édition des abonnements aux notifications
 *
 * @return array
 *	   Tableau des saisies du formulaire
 */
function formulaires_editer_notifications_abonnement_saisies_dist ($id_notifications_abonnement='new', $id_auteur=null) {

	if (( ! _request('type_notification')) && ($id_notifications_abonnement === 'new')) {

		$saisies = array(
			array(
				'saisie' => 'selection',
				'options' => array(
					'nom' => 'type_notification',
					'label' => _T('notifavancees:label_type_notification'),
					'obligatoire' => 'oui',
					'datas' => array_map(function ($el) {
						return $el['titre'];
					}, notifications_lister_creables()),
				),
			),
		);

	} else {

		$saisies = array(
			array(
				'saisie' => 'hidden',
				'options' => array(
					'nom' => 'type_notification',
					'obligatoire' => 'oui',
					'defaut' => _request('type_notification'),
				),
			),
		);

		$saisies[] = array(
			'saisie' => 'checkbox',
			'options' => array(
				'nom' => 'modes_envoi',
				'label' => _T('notifavancees:label_modes_envoi'),
				'datas' => array_map(function ($el) {
					return $el['titre'];
				}, notifications_modes_lister_disponibles()),
			),
		);

	}

	return $saisies;
}

/**
 * Chargement du formulaire d'édition des abonnements aux notifications
 *
 * Déclarer les champs postés et y intégrer les valeurs par défaut
 *
 * @return array
 *	   Environnement du formulaire
 */
function formulaires_editer_notifications_abonnement_charger_dist ($id_notifications_abonnement='new', $id_auteur=null) {

	$valeurs = array();

	if ($id_notifications_abonnement !== 'new') {

		include_spip('base/abstract_sql');

		$row = sql_allfetsel('quoi, id, preferences, modes', 'spip_notifications_abonnements', 'id_notifications_abonnement='.intval($id_notifications_abonnement));
		$row = array_shift($row);

		$valeurs = array(
			'type_notification' => $row['quoi'],
			'modes_envoi' => unserialize($row['modes']),
		);

	} else {

		$modes = array_keys(notifications_modes_lister_disponibles());

		$valeurs = array(
			'type_notification' => _request('type_notification'),
			// On pré-coche le premier mode disponible
			'modes_envoi' => array(array_shift($modes)),
		);
	}

	return $valeurs;
}

/**
 * Vérifications du formulaire d'édition des abonnements aux notifications
 *
 * Vérifier les champs postés et signaler d'éventuelles erreurs
 *
 * @return array
 *	   Tableau des erreurs
 */
function formulaires_editer_notifications_abonnement_verifier_dist ($id_notifications_abonnement='new', $id_auteur=null) {

	$erreurs = array();

	return $erreurs;

}

/**
 * Traitement du formulaire d'édition des abonnements aux notifications
 *
 * Traiter les champs postés
 *
 * @return array
 *	   Retours des traitements
 */
function formulaires_editer_notifications_abonnement_traiter_dist ($id_notifications_abonnement='new', $id_auteur=null) {

	$quoi		 = _request('type_notification');
	$id			 = _request('id') ? _request('id') : 0;
	$modes		 = _request('modes_envoi');
	$preferences = _request('preferences');

	/* S'il n'y a pas de mode d'envoi, c'est qu'on en est encore à
	   choisir le type de notification. On doit alors repasser dans le
	   formulaire une deuxième fois pour choisir les options */
	if ( ! $modes) {
		return array(
			'editable' => 'oui',
		);
	}

	if (intval($id_notifications_abonnement) > 0) {

		include_spip('base/abstract_sql');
		sql_updateq('spip_notifications_abonnements',
					array(
						'quoi' => $quoi,
						'id' => $id,
						'preferences' => serialize($preferences),
						'modes' => serialize($modes),
					),
					'id_notifications_abonnement='.intval($id_notifications_abonnement));

	} else {

		$abonner = charger_fonction('abonner_notification', 'action');
		$abonner($quoi.'-'.$id, $modes, $preferences);
	}

	$retour = array();

	if ($redirect = _request('redirect')) {
		$retour['redirect'] = $redirect;
	}

	return $retour;
}