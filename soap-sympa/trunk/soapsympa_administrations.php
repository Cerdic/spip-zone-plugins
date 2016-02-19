<?php
/**
 * Fichier d'installation / upgrade et désinstallation du plugin
 */
if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Fonction d'upgrade/maj
 * On crée une configuration par défaut
 *
 * @param string $nom_meta_base_version
 * @param string $version_cible
 */
function soapsympa_upgrade($nom_meta_base_version,$version_cible){
	//spip_log(debug_backtrace(),'soapsympa');
	include_spip('inc/config');
	$maj = array();
	$maj['create'] = array(
		array('soapsympa_create'),
	);
	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version,$version_cible,$maj);
}

function soapsympa_create(){
	include_spip('inc/config');
	ecrire_config('soapsympa/serveur_distant','');
	ecrire_config('soapsympa/remote_host','');
	ecrire_config('soapsympa/identifiant','');
	ecrire_config('soapsympa/mot_de_passe','');
	ecrire_config('soapsympa/proprietaire','');
}

/**
 * Fonction de desinstallation
 * On efface uniquement la méta d'installation
 *
 * @param float $nom_meta_base_version
 */
function soapsympa_vider_tables($nom_meta_base_version) {
	effacer_meta($nom_meta_base_version);
	effacer_meta("soapsympa");
}

?>
