<?php

if (!defined('_ECRIRE_INC_VERSION')) return;

function formulaires_configurer_montants_saisies_dist(){
	include_spip('inc/config');
	return array(
		array(
			'saisie' => 'secteur',
			'options' => array(
				'nom' => 'secteurs',
				'label' => _T('montants:label_secteurs'),
				'explication' => _T('montants:explication_secteurs'),
				'defaut' => lire_config('montants/secteurs',array(0)),
				'multiple' => 'oui',
			)
		)
	);
}

?>
