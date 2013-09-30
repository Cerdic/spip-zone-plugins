<?php

include_spip('base/create');

function svpstats_upgrade($nom_meta_base_version, $version_cible){

	$maj = array();

	$maj['create'] = array(
		array('maj_tables', array('spip_plugins', 'spip_plugins_stats'))
	);

	// On supprime id_plugin au profit de prefixe plus pérenne et
	// on rajoute des champs pour l'historique.
	$maj['0.2'] = array(
		array('maj02_svpstats')
	);

	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);

	spip_log('MODULE STATS - INSTALLATION BDD', 'svp_actions.' . _LOG_INFO);
}

function svpstats_vider_tables($nom_meta_base_version) {
	sql_alter("TABLE spip_plugins DROP COLUMN nbr_sites");
	sql_alter("TABLE spip_plugins DROP COLUMN popularite");
	sql_drop_table("spip_plugins_stats");
	effacer_meta($nom_meta_base_version);

	spip_log('MODULE STATS - DESINSTALLATION BDD', 'svp_actions.' . _LOG_INFO);
}

/**
 * Migration du schéma 0.1 au 0.2.
 *
 * Suppression de l'id_plugin remplacé par le prfixe du plugin plus pérenne.
 * Ajout d'un champ historique (tableau srialisé des valeurs de chaque mois)
 * et du timestamp.
 * Aucune sauvegarde n'est à faire car cette table n'était pas encore utilisée.
 *
 * @return void
 */
function maj02_svpstats() {

	sql_alter("TABLE spip_plugins_stats ADD prefixe varchar(30) DEFAULT '' NOT NULL AFTER id_plugin");
	sql_alter("TABLE spip_plugins_stats DROP COLUMN id_plugin");
	sql_alter("TABLE spip_plugins_stats ADD historique text DEFAULT '' NOT NULL AFTER popularite");
	sql_alter("TABLE spip_plugins_stats ADD maj TIMESTAMP");

	spip_log('Maj 0.2 des donnees du plugin','boussole' . _LOG_INFO);
}

?>
