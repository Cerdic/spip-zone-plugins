<?php

// On donne des logos et logos de survol à tous les objets éditoriaux.
if (! isset($GLOBALS['roles_logos']['logo'])) {
	$GLOBALS['roles_logos']['logo'] = array(
		'label' => 'Logo',
		'objets' => array_map('table_objet', array_keys(lister_tables_objets_sql())),
	);
}

if (! isset($GLOBALS['roles_logos']['logo_survol'])) {
	$GLOBALS['roles_logos']['logo_survol'] = array(
		'label' => 'Logo de survol',
		'objets' => array_map('table_objet', array_keys(lister_tables_objets_sql())),
	);
}

// On crée des presets pour le massicot aux dimensions des logos
include_spip('inc/plugin');
if (plugin_est_installe('massicot')) {

	if (! (isset($GLOBALS['presets_format_massicot']) and is_array($GLOBALS['presets_format_massicot']))) {
		$GLOBALS['presets_format_massicot'] = array();
	}

	foreach (lister_logos_roles() as $role => $label) {

		if (isset($GLOBALS['roles_logos'][$role]['dimensions'])) {
			$dimensions = $GLOBALS['roles_logos'][$role]['dimensions'];

			$GLOBALS['presets_format_massicot'][] = array(
				'nom' => $label,
				'largeur' => $dimensions['largeur'],
				'hauteur' => $dimensions['hauteur'],
			);
		}
	}
}