<?php

/**
 * Saisies du formulaire d'édition
 *
 * @return array
 *	   Tableau des saisies du formulaire
 */
function formulaires_configurer_logos_roles_saisies_dist() {
	$datas_objets = array();
	foreach (lister_tables_objets_sql() as $table => $def) {
		$datas_objets[table_objet($table)] = _T($def['texte_objets']);
	}

	$saisies = array(
		array(
			'saisie' => 'liste',
			'options' => array(
				'nom' => 'roles_logos',
				'label' => _T('logos_roles:titre_saisie_roles'),
				'ordre_fixe' => 'oui',
				'masquer_nouveaux' => 'oui',
				'texte_bouton_ajouter' => _T('logos_roles:texte_bouton_ajouter_type'),
				'texte_bouton_supprimer' => _T('logos_roles:texte_bouton_supprimer_type'),
			),
			'saisies' => array(
				array(
					'saisie' => 'radio',
					'options' => array(
						'nom' => 'etat',
						'label' => _T('logos_roles:label_etat_type'),
						'datas' => array(
							1 => _T('logos_roles:label_etat_actif'),
							0 => _T('logos_roles:label_etat_inactif'),
						),
						'defaut' => 1,
						'conteneur_class' => 'saisie_etat',
					),
				),
				array(
					'saisie' => 'input',
					'options' => array(
						'nom' => 'slug',
						'label' => _T('logos_roles:label_saisie_slug_role'),
						'explication' => _T('logos_roles:explication_saisie_slug_role'),
						'conteneur_class' => 'saisie_slug'
					),
				),
				array(
					'saisie' => 'input',
					'options' => array(
						'nom' => 'titre',
						'label' => _T('logos_roles:label_saisie_titre_role'),
						'explication' => _T('logos_roles:explication_saisie_titre_role'),
						'conteneur_class' => 'saisie_titre'
					),
				),
				array(
					'saisie' => 'fieldset',
					'options' => array(
						'nom' => 'options_avancees',
						'label' => _T('logos_roles:label_fieldset_options_avancees'),
						'pliable' => 'oui',
						'plie' => 'oui',
						'conteneur_class' => 'fieldset_options_avancees',
					),
					'saisies' => array(
						array(
							'saisie' => 'checkbox',
							'options' => array(
								'nom' => 'objets',
								'label' => _T('logos_roles:label_saisie_objets_role'),
								'datas' => $datas_objets,
							),
						),
						array(
							'saisie' => 'fieldset',
							'options' => array(
								'nom' => 'dimensions',
								'label' => _T('logos_roles:label_fieldset_dimensions_role'),
								'explication' => _T('logos_roles:explication_fieldset_dimensions_role'),
								'conteneur_class' => 'fieldset_dimensions',
							),
							'saisies' => array(
								array(
									'saisie' => 'input',
									'options' => array(
										'nom' => 'dimensions[largeur]',
										'label' => _T('logos_roles:label_saisie_largeur_role'),
									),
								),
								array(
									'saisie' => 'input',
									'options' => array(
										'nom' => 'dimensions[hauteur]',
										'label' => _T('logos_roles:label_saisie_hauteur_role'),
									),
								),
							)
						),
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
		$valeurs = lire_config('logos_roles/', array());
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

	// On ne garde que les rôles pour lesquels on a saisi autre chose qu'un
	// état.
	set_request('roles_logos', array_filter(
		_request('roles_logos'),
		function ($role) {
			return ($role['slug'] !== '') or
				($role['titre'] !== '') or
				(isset($role['objets']) and is_array($role['objets'])) or
				($role['dimensions']['largeur'] !== '') or
				($role['dimensions']['hauteur'] !== '');
		}
	));

	$roles = _request('roles_logos');

	foreach ($roles as $i => $role) {
		if ((! isset($role['slug']) or (! $role['slug']))) {
			$erreurs['roles_logos'][$i]['slug'] = _T('info_obligatoire');
		} elseif (! preg_match('/^[a-z_]+$/', $role['slug'])) {
			$erreurs['roles_logos'][$i]['slug'] = _T('logos_roles:erreur_slug_invalide');
		} elseif (1 < count(
			// Vérifier qu'il n'y ait pas de doublons de slugs
			array_keys(
				array_map(
					function ($r) {
						return $r['slug'];
					},
					$roles
				),
				$role['slug']
			)
		)) {
			$erreurs['roles_logos'][$i]['slug'] = _T('logos_roles:erreur_doublon_slug');
		}

		if ((! isset($role['objets'])) or (! count($role['objets']))) {
			$erreurs['roles_logos'][$i]['objets'] = _T('logos_roles:erreur_sans_objets');
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
