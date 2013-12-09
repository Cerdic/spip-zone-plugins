<?php
/**
 * Plugin ORR
 * (c) 2012 tofulm
 * Licence GNU/GPL
 */

if (!defined('_ECRIRE_INC_VERSION')) return;


/**
 * Fonction d'installation du plugin et de mise à jour.
**/
function orr_upgrade($nom_meta_base_version, $version_cible) {
	$maj = array();

	$maj['create'] = array(array('maj_tables', array('spip_orr_ressources', 'spip_orr_reservations','spip_orr_autorisations', 'spip_orr_reservations_liens','spip_orr_autorisations_liens')));

	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);

	//Forcer l'utilisation des mots clefs
	if ($GLOBALS['meta']['articles_mots'] == 'non') ecrire_meta('articles_mots', 'oui');
	// Création du groupe de mots clés : orr et le rattacher à la basse orr_ressources
	if (sql_countsel('spip_groupes_mots', "titre = 'orr'") == 0) {
	$id_groupe = sql_insertq('spip_groupes_mots',array('titre'=>'orr', 'unseul'=>'nom','tables_liees'=>'orr_ressources','minirezo'=>'oui'));
    $id_mot    = sql_insertq('spip_mots',array('titre'=>'orr_defaut','id_groupe'=>$id_groupe,'type'=>'orr'));
	}
}


/**
 * Fonction de désinstallation du plugin.
**/
function orr_vider_tables($nom_meta_base_version) {

	sql_drop_table("spip_orr_ressources");
	sql_drop_table("spip_orr_autorisations");
	sql_drop_table("spip_orr_autorisations_liens");
	sql_drop_table("spip_orr_reservations");
	sql_drop_table("spip_orr_reservations_liens");

	# Nettoyer les versionnages et forums
	sql_delete("spip_versions",              sql_in("objet", array('orr_ressource','orr_autorisation', 'orr_reservation')));
	sql_delete("spip_versions_fragments",    sql_in("objet", array('orr_ressource','orr_autorisation', 'orr_reservation')));
	sql_delete("spip_forum",                 sql_in("objet", array('orr_ressource','orr_autorisation', 'orr_reservation')));

	effacer_meta($nom_meta_base_version);

    #supprimer le mots clef orr_defaut et le groupe de mot orr
    sql_delete('spip_groupes_mots',"titre='orr'");
    sql_delete('spip_mots',"titre='orr_defaut'");
}

?>
