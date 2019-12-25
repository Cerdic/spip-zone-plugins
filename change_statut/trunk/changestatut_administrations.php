<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/meta');

// Installation et mise à jour
function changestatut_upgrade($nom_meta_version_base, $version_cible){

	$maj = array();

	include_spip('inc/config');
	include_spip('base/create');
   $maj = array();
	$maj['create'] = array(
			array('maj_tables',array('spip_auteurs')),
			array('ecrire_config','changestatut', array(
						'statut' => 'webmestre'
	)));

	include_spip('base/upgrade');
   maj_plugin($nom_meta_version_base, $version_cible, $maj);
}

// Désinstallation
function changestatut_vider_tables($nom_meta_version_base){

	include_spip('base/abstract_sql');
	// On remet le webmestre d'equerre au cas ou
	sql_updateq("spip_auteurs", array("statut" => "0minirezo","webmestre" => "oui"), "id_auteur=".intval($GLOBALS['visiteur_session']['id_auteur']));
	sql_alter("TABLE spip_auteurs DROP statut_orig");
   effacer_meta('changestatut');
	effacer_meta($nom_meta_version_base);
}

?>
