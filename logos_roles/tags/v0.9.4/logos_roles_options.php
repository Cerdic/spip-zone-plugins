<?php

// On ajoute des presets pour le massicot aux dimensions des logos
if (test_plugin_actif('massicot')) {
	if (! (isset($GLOBALS['presets_format_massicot']) and is_array($GLOBALS['presets_format_massicot']))) {
		$GLOBALS['presets_format_massicot'] = array();
	}

	include_spip('logos_roles_fonctions');
	// On se limite à l'objet dans l'environnement s'il y en a un
	foreach (lister_roles_logos(_request('objet')) as $role => $options) {
		if ($dimensions = get_dimensions_role($role)) {
			$GLOBALS['presets_format_massicot'][] = array(
				'nom' => $options['label'],
				'largeur' => $dimensions['largeur'],
				'hauteur' => $dimensions['hauteur'],
			);
		}
	}
}
