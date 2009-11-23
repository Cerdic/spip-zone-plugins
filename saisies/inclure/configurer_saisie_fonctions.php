<?php

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;

function construire_configuration_saisie($saisie, $avec_nom='non'){
	include_spip('inc/yaml');
	$config = array();
	
	$configuration_base = yaml_decode_file(find_in_path('saisies/_base.yaml'));
	$configuration_base_plus = yaml_decode_file(find_in_path('saisies/_base_plus.yaml'));
	$configuration_saisie = yaml_decode_file(find_in_path('saisies/'.$saisie.'.yaml'));
	
	if (is_array($configuration_base) and is_array($configuration_base_plus) and is_array($configuration_saisie)){
		// On ne garde le premier champ permettant de configurer le "name" seulement si on le demande explicitement
		if (!$avec_nom or ($avec_nom == 'non'))
			array_shift($configuration_base['options']);
		
		$config = array_merge(
			array(
				array(
					'explication' => $configuration_saisie['explication']
				)
			),
			$configuration_base['options'],
			$configuration_saisie['options'],
			array(
				array(
					'groupe' => $configuration_base_plus['titre'],
					'contenu' => $configuration_base_plus['options']
				)
			)
		);
	}
	
	return $config;
}

?>
