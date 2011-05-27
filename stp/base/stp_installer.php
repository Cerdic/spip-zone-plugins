<?php

include_spip('base/create');
include_spip('base/svp_declarer');

function stp_upgrade($nom_meta_base_version, $version_cible) {
	$current_version = "0.0";
		
	if (isset($GLOBALS['meta'][$nom_meta_base_version]))
		$current_version = $GLOBALS['meta'][$nom_meta_base_version];
		
	if ($current_version=="0.0") {
		// Mise a jour de la table paquets pour gerer les paquets locaux
		maj_tables('spip_paquets');
		ecrire_meta($nom_meta_base_version,$current_version=$version_cible);

		// Initialiseation des paquets locaux
//		include_spip('inc/step');
//		step_actualiser_plugins_locaux();

		spip_log('INSTALLATION BDD', 'stp_actions.' . _LOG_INFO);
	}
}

function stp_vider_tables($nom_meta_base_version) {
	global $stp_paquets;
	
	foreach (array_keys($stp_paquets['field']) as $_champ)
		sql_alter("TABLE spip_paquets DROP $_champ");
	effacer_meta($nom_meta_base_version);

	spip_log('DESINSTALLATION BDD', 'stp_actions.' . _LOG_INFO);
}

?>
