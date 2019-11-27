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
	);
	
	return $saisies;
}
