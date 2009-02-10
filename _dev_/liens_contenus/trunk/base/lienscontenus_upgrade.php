<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/meta');

function lienscontenus_upgrade($nom_meta_base_version, $version_cible)
{
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

			lienscontenus_initialiser();
		}
		if (version_compare($current_version,'0.2','<')) {
			spip_log('Mise a jour de la base en v0.2', 'liens_contenus');
			include_spip('base/abstract_sql');
			sql_update("spip_liens_contenus", "type_objet_contenant='syndic'", "type_objet_contenant='site'");
			sql_update("spip_liens_contenus", "type_objet_contenu='syndic'", "type_objet_contenu='site'");
			ecrire_meta($nom_meta_base_version, $current_version='0.2', 'non');
		}
	}
}

function lienscontenus_vider_tables($nom_meta_base_version)
{
  spip_log('Suppression des tables du plugin', 'liens_contenus');
	sql_drop_table("spip_liens_contenus");
	effacer_meta($nom_meta_base_version);
}
?>