<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

include_spip('inc/config');

function formulaires_configurer_coordonnees_saisies_dist() {
	$saisies = array(
		array(
			'saisie' => 'choisir_objets',
			'options' => array(
				'nom' => 'objets',
				'label' => _T('coordonnees:label_objets_actifs'),
				'explication' => _T('coordonnees:explication_objets_actifs'),
				'exclus' => array('spip_adresses', 'spip_numeros', 'spip_emails'),
				'defaut' => lire_config('coordonnees/objets'),
			),
		),
		array(
			'saisie' => 'checkbox',
			'options' => array(
				'nom' => 'adresses_champs_superflus',
				'label' => _T('coordonnees:configuration_adresses_champs_superflus_label'),
				'explication' => _T('coordonnees:configuration_adresses_champs_superflus_explication'),
				'data' => array(
					'voie' => _T('coordonnees:label_voie'),
					'complement' => _T('coordonnees:label_complement'),
					'boite_postale' => _T('coordonnees:label_boite_postale'),
					'code_postal' => _T('coordonnees:label_code_postal'),
					'ville' => _T('coordonnees:label_ville'),
					'zone_administrative' => _T('coordonnees:label_zone_administrative'),
					'pays' => _T('coordonnees:label_pays'),
				),
				'defaut' => lire_config('coordonnees/adresses_champs_superflus'),
			),
		),
	);
	
	return $saisies;
}
