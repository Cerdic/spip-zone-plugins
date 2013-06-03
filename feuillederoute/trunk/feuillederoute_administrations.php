<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/meta');

// Installation et mise à jour sur modele sjcycle
function feuillederoute_upgrade($nom_meta_version_base, $version_cible){

	$maj = array();

	include_spip('inc/config');
	$maj = array();
	$maj['create'] = array(
		array('ecrire_config','feuillederoute', array(
			'titre' => 'Feuille de Route'
	)));
	include_spip('base/upgrade');
	maj_plugin($nom_meta_version_base, $version_cible, $maj);
}

// Désinstallation
function feuillederoute_vider_tables($nom_meta_version_base){
	effacer_meta('feuillederoute');
	effacer_meta($nom_meta_version_base);
	supprimer_fichier(_DIR_IMG . 'feuillederoute.php');
}


?>