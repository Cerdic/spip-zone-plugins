<?php

if (!defined('_ECRIRE_INC_VERSION')) return;

function formulaires_configurer_daterubriques_saisies_dist(){
	include_spip('inc/config');
	return array(
		array(
			'saisie' => 'secteur',
			'options' => array(
				'nom' => 'secteurs',
				'label' => _T('daterubriques:label_secteurs'),
				'explication' => _T('daterubriques:explication_secteurs'),
				'defaut' => lire_config('daterubriques/secteurs',array(0)),
				'multiple' => 'oui',
			)
		)
	);
}

?>