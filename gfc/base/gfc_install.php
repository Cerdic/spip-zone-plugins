<?

if (!defined("_ECRIRE_INC_VERSION")) return;
function gfc_declarer_tables_principales($tables_principales){
	$tables_principales['spip_auteurs']['field']['gfc_uid'] = "varchar(50) NOT NULL";
	return $tables_principales;
}

	
/**
 * MAJ/Upgrade de la base
 *
 * @param unknown_type $nom_meta_base_version
 * @param unknown_type $version_cible
 */
function gfc_upgrade($nom_meta_base_version,$version_cible){
	include_spip('inc/meta');
	include_spip('base/abstract_sql');
	$current_version = 0.0;
	if (   (!isset($GLOBALS['meta'][$nom_meta_base_version]) )
			|| (($current_version = $GLOBALS['meta'][$nom_meta_base_version])!=$version_cible)){
		if (version_compare($current_version,"1.0",'<')){
			sql_alter('table spip_auteurs ADD gfc_uid varchar(50) NOT NULL');
			ecrire_meta($nom_meta_base_version,$current_version="1.0");
		}
	}
}

/**
 * Suppression des tables lors de la desinstallation
 *
 * @param unknown_type $nom_meta_base_version
 */
function gfc_vider_tables($nom_meta_base_version) {
	include_spip('inc/meta');
	include_spip('base/abstract_sql');
	sql_alter('table spip_auteurs drop gfc_uid');
	effacer_meta($nom_meta_base_version);
}

?>