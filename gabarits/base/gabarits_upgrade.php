<?php
/**
 * Plugin Gabarits pour Spip 2.0
 * Licence GPL
 * 
 *
 */

include_spip('inc/meta');

/**
 * Installation/maj des tables gabarits
 *
 * @param string $nom_meta_base_version
 * @param string $version_cible
 */
function gabarits_upgrade($nom_meta_base_version,$version_cible){
	$current_version = '0.0';
	if ((!isset($GLOBALS['meta'][$nom_meta_base_version]))
			|| (($current_version = $GLOBALS['meta'][$nom_meta_base_version])!=$version_cible)){
		// installation
		if (version_compare($current_version, '0.0','<=')){
			include_spip('base/gabarits');
			include_spip('base/create');
			// creer les tables
			creer_base();
			ecrire_meta($nom_meta_base_version,$current_version=$version_cible,'non');
		}
		if (version_compare($current_version, '0.2','<')){
			include_spip('base/gabarits');
			include_spip('base/create');
			creer_base();
			include_spip('base/abstract_sql');
			// lier les gabarits existants aux articles
			sql_updateq('spip_gabarits', array('objet' => 'articles'));
			ecrire_meta($nom_meta_base_version,$current_version="0.2",'non');
		}
	}
}

/**
 * Desinstallation/suppression des tables gabarits
 *
 * @param string $nom_meta_base_version
 */
function gabarits_vider_tables($nom_meta_base_version) {
	sql_drop_table('spip_gabarits');
	effacer_meta($nom_meta_base_version);
}
	
?>
