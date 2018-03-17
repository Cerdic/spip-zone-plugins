<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

include_spip('base/objets');
include_spip('base/objets_parents');

function formulaires_configurer_duplicator_saisies_dist() {
	$declaration_objets = lister_tables_objets_sql();
	$textes_objets = array_column($declaration_objets, 'texte_objets');
	$textes_objets = array_map('_T', $textes_objets);
	array_multisort($textes_objets, SORT_ASC, $declaration_objets);
	$config = lire_config('duplicator');
	
	$saisies = array(
		array(
			'saisie' => 'choisir_objets',
			'options' => array(
				'nom' => 'objets',
				'label' => _T('duplicator:configurer_objets_label'),
				'explication' => _T('duplicator:configurer_objets_explication'),
				'defaut' => isset($config['objets']) ? $config['objets'] : array(),
			),
		),
	);
	
	// Pour chaque objet déjà choisi, on ajoute des options
	if (isset($config['objets'])) {
		// Une explication de pourquoi l'ensemble des objets
		$saisies[] = array(
			'saisie' => 'explication',
			'options' => array(
				'nom' => 'explication_objets',
				'texte' => _T('duplicator:configurer_explication_objets_texte'),
			),
		);
		
		// On boucle sur tous les objets possibles
		foreach ($declaration_objets as $table_objet_sql=>$declaration_objet) {
			$table_objet = table_objet($table_objet_sql);
			$objet = objet_type($table_objet);
			
			$groupe_objet = array(
				'saisie' => 'fieldset',
				'options' => array(
					'nom' => "groupe_$table_objet",
					'label' => _T($declaration_objet['texte_objets']),
					'pliable' => 'oui',
					'plie' => 'oui',
				),
				'saisies' => array(),
			);
			
			// Si l'objet a des champs
			if (isset($declaration_objet['field']) and $champs = $declaration_objet['field']) {
				// On cherche et vire le champ statut, car il y a une config pour ça ensuite
				if (isset($declaration_objet['statut'][0]['champ']) and $champ_statut = $declaration_objet['statut'][0]['champ']) {
					unset($champs[$champ_statut]);
				}
				
				foreach ($champs as $champ=>$sql) {
					$champs[$champ] = $champ;
				}
				$groupe_objet['saisies'][] = array(
					'saisie' => 'case',
					'options' => array(
						'nom' => "${table_objet}[personnaliser_champs]",
						'label_case' => _T('duplicator:configurer_personnaliser_champs_label'),
						'valeur_forcee' => (isset($config[$table_objet]['champs']) and $config[$table_objet]['champs']) ? 'on' : '',
					),
				);
				$groupe_objet['saisies'][] = array(
					'saisie' => 'checkbox',
					'options' => array(
						'nom' => "${table_objet}[champs]",
						'label' => _T('duplicator:configurer_champs_label'),
						'data' => $champs,
						'defaut' => isset($config[$table_objet]['champs']) ? $config[$table_objet]['champs'] : array(),
						'afficher_si' => "@${table_objet}[personnaliser_champs]@ == 'on'",
					),
				);
			}
			
			// S'il y a des statuts
			if (isset($declaration_objet['statut_textes_instituer']) and $statuts = $declaration_objet['statut_textes_instituer']) {
				foreach ($statuts as $statut=>$lang) {
					$statuts[$statut] = _T($lang);
				}
				$groupe_objet['saisies'][] = array(
					'saisie' => 'selection',
					'options' => array(
						'nom' => "${table_objet}[statut]",
						'label' => _T('duplicator:configurer_statut_label'),
						'option_intro' => _T('duplicator:configurer_statut_option_intro'),
						'data' => $statuts,
						'defaut' => isset($config[$table_objet]['statut']) ? $config[$table_objet]['statut'] : '',
					),
				);
			}
			
			// Les autorisations
			$groupe_objet['saisies'][] = array(
				'saisie' => 'selection',
				'options' => array(
					'nom' => "${table_objet}[autorisation]",
					'label' => _T('duplicator:configurer_autorisation_label'),
					'option_intro' => _T('duplicator:configurer_autorisation_option_intro'),
					'data' => array(
						'webmestre' => _T('duplicator:configurer_autorisation_choix_webmestre'),
						'administrateur' => _T('duplicator:configurer_autorisation_choix_administrateur'),
						'redacteur' => _T('duplicator:configurer_autorisation_choix_redacteur'),
					),
					'defaut' => isset($config[$table_objet]['autorisation']) ? $config[$table_objet]['autorisation'] : '',
				),
			);
			
			// Les enfants à dupliquer
			if ($enfants_possibles = type_objet_info_enfants($objet)) {
				$enfants_possibles = array_map('table_objet_sql', array_keys($enfants_possibles));
				$enfants_exclus = array_diff(array_keys($declaration_objets), $enfants_possibles);
				
				$groupe_objet['saisies'][] = array(
					'saisie' => 'case',
					'options' => array(
						'nom' => "${table_objet}[personnaliser_enfants]",
						'label_case' => _T('duplicator:configurer_personnaliser_enfants_label'),
						'valeur_forcee' => (isset($config[$table_objet]['objets_enfants']) and $config[$table_objet]['objets_enfants']) ? 'on' : '',
					),
				);
				$groupe_objet['saisies'][] = array(
					'saisie' => 'choisir_objets',
					'options' => array(
						'nom' => "${table_objet}[objets_enfants]",
						'exclus' => $enfants_exclus,
						'label' => _T('duplicator:configurer_objets_enfants_label'),
						'defaut' => isset($config[$table_objet]['objets_enfants']) ? $config[$table_objet]['objets_enfants'] : array(),
						'afficher_si' => "@${table_objet}[personnaliser_enfants]@ == 'on'",
					),
				);
			}
			
			
			$saisies[] = $groupe_objet;
		}
	}
	
	return $saisies;
}

function formulaires_configurer_duplicator_verifier_dist() {
	// Pour chaque type d'objets
	$declaration_objets = lister_tables_objets_sql();
	foreach ($declaration_objets as $table_objet_sql=>$declaration_objet) {
		$table_objet = table_objet($table_objet_sql);
		$config_objet = _request($table_objet);
		
		// Si on a décoché la personnalisation des champs, on vide la config des champs
		if (!$config_objet['personnaliser_champs']) {
			unset($config_objet['champs']);
		}
		
		// Si on a décoché la personnalisation des enfants, on vide la config des enfants
		if (!$config_objet['personnaliser_enfants']) {
			unset($config_objet['objets_enfants']);
		}
		
		// On remet dans la course
		set_request($table_objet, $config_objet);
	}
	
	return array();
}
