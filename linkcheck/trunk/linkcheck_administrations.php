<?php
/**
 * Plugin LinkCheck
 * (c) 2013 Benjamin Grapeloux, Guillaume Wauquier
 * Licence GNU/GPL
 */

if (!defined('_ECRIRE_INC_VERSION')) return;


/**
 * Fonction d'installation du plugin et de mise à jour.
 * Vous pouvez :
 * - créer la structure SQL,
 * - insérer du pre-contenu,
 * - installer des valeurs de configuration,
 * - mettre à jour la structure SQL 
**/
function linkcheck_upgrade($nom_meta_base_version, $version_cible) {
	$maj = array();

	$maj['create'] = array(
		array('maj_tables', array('spip_linkchecks', 'spip_linkchecks_liens')),
		array('ecrire_config', 'linkcheck_dernier_id_objet', 0),
		array('ecrire_config', 'linkcheck_dernier_id_lien', 0),
		array('ecrire_config', 'linkcheck_dernier_objet', 0),
		array('ecrire_config', 'linkcheck_etat_parcours', false),
		array('ecrire_config', 'linkcheck/notifier_courriel', 1),
		array('ecrire_config', 'linkcheck/afficher_alerte', 1)
		
	);

	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}


/**
 * Fonction de désinstallation du plugin.
 * Vous devez :
 * - nettoyer toutes les données ajoutées par le plugin et son utilisation
 * - supprimer les tables et les champs créés par le plugin. 
**/
function linkcheck_vider_tables($nom_meta_base_version) {
	
	sql_drop_table("spip_linkchecks");
	sql_drop_table("spip_linkchecks_liens");

	effacer_meta($nom_meta_base_version);
	effacer_meta('linkcheck_dernier_id_objet');
	effacer_meta('linkcheck_dernier_id_lien');
	effacer_meta('linkcheck_dernier_objet');
	effacer_meta('linkcheck_etat_parcours');
	effacer_meta('linkcheck/notifier_courriel');
	effacer_meta('linkcheck/afficher_alerte');
	
}

?>
