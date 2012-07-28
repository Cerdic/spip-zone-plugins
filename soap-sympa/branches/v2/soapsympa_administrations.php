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
	ecrire_config('soapsympa/serveur_distant','http://listes.test.org/sympa/wsdl');
	ecrire_config('soapsympa/remote_host','listes.test.org');
	ecrire_config('soapsympa/identifiant','SPIP_test_org');
	ecrire_config('soapsympa/mot_de_passe','archi@test#ORG');
	ecrire_config('soapsympa/proprietaire','listmaster@nomdomaine.org');
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
