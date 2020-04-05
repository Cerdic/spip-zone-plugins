<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function formulaires_configurer_profils_saisies_dist() {
	$saisies = array(
		array(
			'saisie' => 'profils',
			'options' => array(
				'nom' => 'id_profil_defaut',
				'label' => _T('profils:configurer_id_profil_defaut_label'),
				'defaut' => lire_config('profils/id_profil_defaut'),
			),
		),
		array(
			'saisie' => 'case',
			'options' => array(
				'nom' => 'email_unique',
				'label_case' => _T('profils:configurer_email_unique_label_case'),
				'explication' => _T('profils:configurer_email_unique_explication'),
				'conteneur_class' => 'pleine_largeur',
				'defaut' => lire_config('profils/email_unique'),
			),
		),
	);
	
	return $saisies;
}
