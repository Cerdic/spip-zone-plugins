<?php
/**
 * Plugin Déclinaisons Produit
 * (c) 2012 Rainer Müller
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
function declinaisons_upgrade($nom_meta_base_version, $version_cible) {
	$maj = array();

	$maj['create'] = array(array('maj_tables', array('spip_declinaisons','spip_prix_objets')));
	$maj['1.0.2'] = array(array('maj_tables', array('spip_prix_objets')));
    
	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}


/**
 * Fonction de désinstallation du plugin.
 * Vous devez :
 * - nettoyer toutes les données ajoutées par le plugin et son utilisation
 * - supprimer les tables et les champs créés par le plugin. 
**/
function declinaisons_vider_tables($nom_meta_base_version) {

	sql_drop_table("spip_declinaisons");

	# Nettoyer les versionnages et forums
	sql_delete("spip_versions",              sql_in("objet", array('declinaison')));
	sql_delete("spip_versions_fragments",    sql_in("objet", array('declinaison')));
	sql_delete("spip_forum",                 sql_in("objet", array('declinaison')));

	effacer_meta($nom_meta_base_version);
}

?>