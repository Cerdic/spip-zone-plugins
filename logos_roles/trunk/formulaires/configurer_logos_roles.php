<?php

/**
 * Saisies du formulaire d'édition
 *
 * @return array
 *	   Tableau des saisies du formulaire
 */
function formulaires_configurer_logos_roles_saisies_dist() {

	$saisies = array(
		array(
			'saisie' => 'liste',
			'options' => array(
				'nom' => 'roles_logos',
				'label' => _T('logos_roles:titre_saisie_roles'),
				'ordre_fixe' => 'oui',
			),
			'saisies' => array(
				array(
					'saisie' => 'input',
					'options' => array(
						'nom' => 'slug',
						'label' => _T('logos_roles:label_saisie_slug_role'),
						'explication' => _T('logos_roles:explication_saisie_slug_role'),
					),
				),
				array(
					'saisie' => 'input',
					'options' => array(
						'nom' => 'titre',
						'label' => _T('logos_roles:label_saisie_titre_role'),
						'explication' => _T('logos_roles:explication_saisie_titre_role'),
					),
				),
			),
		),
	);

	return $saisies;
}

/**
 * Chargement du formulaire d'édition
 *
 * Déclarer les champs postés et y intégrer les valeurs par défaut
 *
 * @return array
 *	   Environnement du formulaire
 */
function formulaires_configurer_logos_roles_charger_dist() {

	if (_request('roles_logos')) {
		$valeurs = array(
			'roles_logos' => _request('roles_logos'),
		);
	} else {
		$valeurs = lire_config('logos_roles');
	}

	return $valeurs;
}

/**
 * Vérifications du formulaire d'édition
 *
 * Vérifier les champs postés et signaler d'éventuelles erreurs
 *
 * @return array
 *	   Tableau des erreurs
 */
function formulaires_configurer_logos_roles_verifier_dist() {

	if (saisies_liste_verifier('roles_logos')) {
		return array();
	}

	$erreurs = array();
	$roles = _request('roles_logos');

	foreach ($roles as $i => $role) {
		if ((! isset($role['slug']) or (! $role['slug']))) {
			$erreurs['roles_logos'][$i]['slug'] = _T('info_obligatoire');
		} elseif (! preg_match('/^[a-z_]+$/', $role['slug'])) {
			$erreurs['roles_logos'][$i]['slug'] = _T('logos_roles:erreur_slug_invalide');
		}
	}

	return $erreurs;
}

/**
 * Traitement du formulaire d'édition
 *
 * Traiter les champs postés
 *
 * @return array
 *	   Retours des traitements
 */
function formulaires_configurer_logos_roles_traiter_dist() {

	if (saisies_liste_traiter('roles_logos')) {
		return array('editable' => 'oui');
	}

	ecrire_config(
		'logos_roles',
		array(
			'roles_logos' => _request('roles_logos'),
		)
	);

	$retour = array(
		'editable' => 'oui',
		'message_ok' => _T('logos_roles:message_conf_ok'),
	);

	return $retour;
}
