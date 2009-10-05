<?

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * MAJ/Upgrade de la base
 *
 * @param string $nom_meta_base_version
 * @param float $version_cible
 */
function gfc_upgrade($nom_meta_base_version,$version_cible){
	include_spip('inc/meta');
	$current_version = 0.0;
	if ((!isset($GLOBALS['meta'][$nom_meta_base_version]) )
			|| (($current_version = $GLOBALS['meta'][$nom_meta_base_version])!=$version_cible)){
		if (version_compare($current_version,"1.0",'<')){
			include_spip('base/create');
			maj_tables('spip_auteurs');
			ecrire_meta($nom_meta_base_version,$current_version="1.0");
		}
	}
}

/**
 * Suppression des tables lors de la desinstallation
 *
 * @param float $nom_meta_base_version
 */
function gfc_vider_tables($nom_meta_base_version) {
	include_spip('inc/meta');
	include_spip('base/abstract_sql');
	sql_alter('TABLE spip_auteurs drop gfc_uid');
	effacer_meta($nom_meta_base_version);
}

?>