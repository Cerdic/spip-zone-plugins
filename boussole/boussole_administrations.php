<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function boussole_upgrade($nom_meta_base_version, $version_cible){
	$current_version = "0.0";
	include_spip('base/create');
	
	if ((!isset($GLOBALS['meta'][$nom_meta_base_version]))
		|| (($current_version = $GLOBALS['meta'][$nom_meta_base_version])!=$version_cible)){
		if (version_compare($current_version,'0.0','<=')){
			include_spip('base/boussole_declarer');
			creer_base();
			ecrire_meta($nom_meta_base_version,$current_version=$version_cible,'non');
		}
	}
		
	if (!isset($GLOBALS['meta']['boussole_infos_spip'])) {
		include_spip('inc/deboussoler');
		// On ajoute la boussole SPIP par defaut.
		// Cependant on ne teste ni la validite du fichier xml fourni ni la bonne insertion en bdd
		$xml = 'http://zone.spip.org/trac/spip-zone/export/HEAD/_galaxie_/boussole.spip.org/boussole_spip.xml';
		$url = boussole_localiser_xml($xml);
		list($ok, $message) = boussole_ajouter($url);
	}
}

function boussole_vider_tables($nom_meta_base_version) {
	// On nettoie les metas de mises a jour des boussoles
	$alias = array();
	$akas_boussole = sql_allfetsel('aka_boussole', 'spip_boussoles', array(), 'aka_boussole');
	if ($akas_boussole) {
		foreach (array_map('reset', $akas_boussole) as $_aka_boussole) {
			$alias[] = 'boussole_infos_' . $_aka_boussole;
		}
		sql_delete('spip_meta', sql_in('nom', $alias));
	}
	// on efface ensuite la table et la meta habituelle designant la version du plugin
	sql_drop_table("spip_boussoles");
	effacer_meta($nom_meta_base_version);

	spip_log('DESINSTALLATION BDD','boussole');
}

?>
