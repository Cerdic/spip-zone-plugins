<?php

if (!defined('_ECRIRE_INC_VERSION')) return;

function formulaires_configurer_datesecteurs_saisies_dist(){
	include_spip('inc/config');
	return array(
		array(
			'saisie' => 'input',
			'options' => array(
				'nom' => 'secteurs',
				'label' => _T('daterubriques:configurer_secteurs'),
				'defaut' => lire_config('datesecteurs/secteurs', 0),
			)
		)
	);
}

?>
