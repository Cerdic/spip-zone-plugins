<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

include_spip('base/objets');

function formulaires_configurer_duplicator_saisies_dist() {
	$declaration_objets = lister_tables_objets_sql();
	$config = lire_config('duplicator');
	
	$saisies = array(
		array(
			'saisie' => 'choisir_objets',
			'options' => array(
				'nom' => 'objets',
				'label' => _T('duplicator:configurer_objets_label'),
				'defaut' => isset($config['objets']) ? $config['objets'] : array(),
			),
		),
	);
	
	// Pour chaque objet déjà choisi, on ajoute des options
	if (isset($config['objets'])) {
		foreach ($config['objets'] as $table_objet_sql) {
			// Si l'objet fait toujours parti des objets installés
			if (isset($declaration_objets[$table_objet_sql]) and $declaration_objet = $declaration_objets[$table_objet_sql]) {
				$table_objet = table_objet($table_objet_sql);
				$objet = objet_type($table_objet);
				
				$groupe_objet = array(
					'saisie' => 'fieldset',
					'options' => array(
						'nom' => "groupe_$table_objet",
						'label' => _T($declaration_objet['texte_objets']),
					),
					'saisies' => array(),
				);
				
				// Si l'objet a des champs
				if (isset($declaration_objet['field']) and $champs = $declaration_objet['field']) {
					foreach ($champs as $champ=>$sql) {
						$champs[$champ] = $champ;
					}
					$groupe_objet['saisies'][] = array(
						'saisie' => 'case',
						'options' => array(
							'nom' => "${table_objet}[personnaliser_champs]",
							'label_case' => _T('duplicator:configurer_personnaliser_champs_label'),
							'defaut' => (isset($config[$table_objet]['champs']) and $config[$table_objet]['champs']) ? 'on' : '',
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
				
				$saisies[] = $groupe_objet;
			}
		}
	}
	
	return $saisies;
}
