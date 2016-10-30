<?php
// On va créer les champs extras pour spip_projets

function info_sites_declarer_champs_extras($champs = array()) {
	$champs['spip_projets']['identifiant'] = array(
		'saisie' => 'input',
		//Type du champ (voir plugin Saisies)
		'options' => array(
			'nom' => 'identifiant',
			'label' => _T('info_sites:champ_identifiant_label'),
			'sql' => "VARCHAR (255) DEFAULT '' NOT NULL",
			'defaut' => '',
			// Valeur par défaut
			'restrictions' => array(
				'voir' => array('auteur' => ''),
				// Tout le monde peut voir
				'modifier' => array('auteur' => '0minirezo'),
				// Seuls les administrateurs peuvent modifier
			),
		),
	);
	$champs['spip_projets']['url_bug_tracker'] = array(
		'saisie' => 'input',
		//Type du champ (voir plugin Saisies)
		'options' => array(
			'nom' => 'url_bug_tracker',
			'label' => _T('info_sites:champ_url_bug_tracker_label'),
			'sql' => "varchar(255) NOT NULL DEFAULT ''",
			'defaut' => '',
			// Valeur par défaut
			'restrictions' => array(
				'voir' => array('auteur' => ''),
				// Tout le monde peut voir
				'modifier' => array('auteur' => '0minirezo'),
				// Seuls les administrateurs peuvent modifier
			),
		),
	);
	$champs['spip_projets']['url_ged'] = array(
		'saisie' => 'input',
		//Type du champ (voir plugin Saisies)
		'options' => array(
			'nom' => 'url_ged',
			'label' => _T('info_sites:champ_url_ged_label'),
			'sql' => "varchar(255) NOT NULL DEFAULT ''",
			'defaut' => '',
			// Valeur par défaut
			'restrictions' => array(
				'voir' => array('auteur' => ''),
				//Tout le monde peut voir
				'modifier' => array('auteur' => '0minirezo'),
				//Seuls les administrateurs peuvent modifier
			),
		),
	);

	return $champs;
}

