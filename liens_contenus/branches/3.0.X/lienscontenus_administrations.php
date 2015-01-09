<?php

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/meta');

/**
 * Installation/maj des tables lienscontenus
 *
 * @param string $nom_meta_base_version
 * @param string $version_cible
 */
function lienscontenus_upgrade($nom_meta_base_version, $version_cible)
{
	// le prefixe est passe des majuscules aux minuscules :
	if (isset($GLOBALS['meta']['lienscontenus_base_version']) AND !isset($GLOBALS['meta'][$nom_meta_base_version]))
	$GLOBALS['meta'][$nom_meta_base_version] = $GLOBALS['meta']['lienscontenus_base_version'];

	$maj = array();
	
	$maj['create'] = array(
		array('maj_tables', array('spip_liens_contenus','spip_liens_contenus_todo')),
	);
	
	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
	
	/*
	$current_version = 0.0;
	if ((!isset($GLOBALS['meta'][$nom_meta_base_version]) )
	|| (($current_version = $GLOBALS['meta'][$nom_meta_base_version]) != $version_cible)) {
		include_spip('base/lienscontenus');
		if (version_compare($current_version,'0.0','<=')) {
			include_spip('base/create');
			include_spip('base/abstract_sql');
			spip_log('Creation de la base', 'liens_contenus');
			creer_base();
			effacer_meta($nom_meta_base_version); // salade de majuscules
			ecrire_meta($nom_meta_base_version, $current_version=$version_cible, 'non');

            include_spip('inc/lienscontenus');
			lienscontenus_initialiser();
		}
		if (version_compare($current_version,'0.2','<')) {
			spip_log('Mise a jour de la base en v0.2', 'liens_contenus');
			include_spip('base/abstract_sql');
			sql_update("spip_liens_contenus", "type_objet_contenant='syndic'", "type_objet_contenant='site'");
			sql_update("spip_liens_contenus", "type_objet_contenu='syndic'", "type_objet_contenu='site'");
			ecrire_meta($nom_meta_base_version, $current_version='0.2', 'non');
		}
        if (version_compare($current_version,'0.3','<')) {
          spip_log('Mise a jour de la base en v0.3', 'liens_contenus');
          include_spip('base/abstract_sql');
          sql_updateq("spip_liens_contenus", array('type_objet_contenu' => 'document'), "type_objet_contenu='IMG'");
          sql_updateq("spip_liens_contenus", array('type_objet_contenu' => 'document'), "type_objet_contenu='DOC'");
          sql_updateq("spip_liens_contenus", array('type_objet_contenu' => 'document'), "type_objet_contenu='EMB'");
          ecrire_meta($nom_meta_base_version, $current_version='0.3', 'non');
        }
        if (version_compare($current_version,'0.4','<')) {
          spip_log('Mise a jour de la base en v0.4', 'liens_contenus');
          include_spip('base/create');
          include_spip('base/abstract_sql');
          creer_base();
          ecrire_meta($nom_meta_base_version, $current_version='0.4', 'non');
        }
	}
	*/
}

/**
 * Desinstallation/suppression des tables lienscontenus
 *
 * @param string $nom_meta_base_version
 */
function lienscontenus_vider_tables($nom_meta_base_version) {
	spip_log('Suppression des tables du plugin', 'liens_contenus');
	sql_drop_table("spip_liens_contenus");
	sql_drop_table("spip_liens_contenus_todo");
	effacer_meta($nom_meta_base_version);
	// Effacer la config
	effacer_meta('lienscontenus');
}
