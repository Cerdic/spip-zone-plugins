<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Création du formulaire de configuration avec saisie
 *
 * @access public
 * @return mixed
 */
function formulaires_configurer_courtjus_saisies_dist() {

	// Saisie de base
	$saisies = array(
		array(
			'saisie' => 'choisir_objets',
			'options' => array(
				'nom' => 'objet_exclu',
				'label' => _T('courtjus:courtjus_objet_exclu'),
				'exclus' => courtjus_rubrique_exclus()
			)
		),
		array(
			'saisie' => 'oui_non',
			'options' => array(
				'nom' => 'rubrique_enfant',
				'label' => _T('courtjus:chercher_rubrique_enfant'),
				'explication' => _T('courtjus:explication_chercher_rubrique_enfant'),
			)
		),
		array(
			'saisie' => 'oui_non',
			'options' => array(
				'nom' => 'squelette_par_rubrique',
				'label' => _T('courtjus:label_squelette_par_rubrique'),
				'explication' => _T('courtjus:explication_squelette_par_rubrique')
			)
		),
		array(
			'saisie' => 'oui_non',
			'options' => array(
				'nom' => 'num_titre',
				'label' => _T('courtjus:label_num_titre'),
				'explication' => _T('courtjus:explication_num_titre')
			)
		),
        array(
            'saisie' => 'oui_non',
            'options' => array(
                'nom' => 'recent',
                'label' => _T('courtjus:label_recent'),
                'explication' => _T('courtjus:explication_recent')
            )
        ),
		array(
			'saisie' => 'mot',
			'options' => array(
				'nom' => 'mot_exclusion',
				'label' => _T('courtjus:label_mot_exclusion'),
				'multiple' => 'oui'
			)
		),
		array(
			'saisie' => 'secteur',
			'options' => array(
				'nom' => 'secteur_exclusion',
				'label' => _T('courtjus:label_secteur_exclusion'),
				'multiple' => 'oui'
			)
		)
	);

	return $saisies;
}

function formulaires_configurer_courtjus_charger_dist() {
	include_spip('inc/config');
	$config = lire_config('courtjus');
	return (empty($config)) ? array() : $config;
}

function courtjus_rubrique_exclus() {
	// On va chercher les différents objets intallés sur SPIP
	$objets = lister_tables_objets_sql();

	// On va filtrer pour avoir les objets qui n'ont pas d'id_rubrique
	$objet_exclus = array();
	foreach ($objets as $table => $data) {
		// Si on ne trouve pas d'"id_rubrique" dans la liste des champs, on garde
		// On garde aussi la table rubrique
		if (!array_key_exists('id_rubrique', $data['field']) or $table = table_objet_sql('rubrique')) {
			$objet_exclus[] = $table;
		}
	}

	return $objet_exclus;
}