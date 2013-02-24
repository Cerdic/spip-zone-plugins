<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function boussole_upgrade($nom_meta_base_version, $version_cible){

	$maj = array();

	$maj['create'] = array(
		array('maj_tables', array('spip_boussoles'))
	);

	// On ajoute la table des extras et on supprime toutes les boussoles
	// Seule la boussole SPIP sera réinstallée.
	// Pour les autres il faudra de toute façon adapter la boussole avant de la réinstaller
	$maj['0.2'] = array(
		array('maj_tables', array('spip_boussoles_extras')),
		array('nettoyer_donnees_boussole')
	);

	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);

	// On ajoute la boussole SPIP par defaut.
	// Cependant on ne teste ni la validite du fichier xml fourni ni la bonne insertion en bdd
	if (!isset($GLOBALS['meta']['boussole_infos_spip'])) {
		include_spip('inc/deboussoler');
		$xml = 'http://zone.spip.org/trac/spip-zone/export/HEAD/_galaxie_/boussole.spip.org/boussole_spip.xml';
		$url = boussole_localiser_xml($xml);
		list($ok, $message) = boussole_ajouter($url);
	}
}

function boussole_vider_tables($nom_meta_base_version) {
	// On nettoie les metas de mises a jour des boussoles
	nettoyer_donnees_boussole(true);

	// on efface ensuite la table et la meta habituelle designant la version du plugin
	sql_drop_table("spip_boussoles");
	sql_drop_table("spip_boussoles_extras");
	effacer_meta($nom_meta_base_version);

	spip_log('Désinstallation des tables du plugin Boussole','boussole' . _LOG_INFO);
}

function nettoyer_donnees_boussole($meta=false) {
	$alias = array();

	$akas_boussole = sql_allfetsel('aka_boussole', 'spip_boussoles', array(), 'aka_boussole');
	if ($akas_boussole) {
		foreach (array_map('reset', $akas_boussole) as $_aka_boussole) {
			$alias[] = 'boussole_infos_' . $_aka_boussole;
		}
		sql_delete('spip_meta', sql_in('nom', $alias));
	}

	if (!$meta)
		sql_delete('spip_boussoles');

}
?>
