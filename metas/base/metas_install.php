<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/meta');

function metas_install($action){
	include_spip('inc/plugin');

	//recupère les informations de plugin.xml
	$infos = charger_fonction('get_infos','plugins');
	$mes_infos = $infos('metas', false, '_DIR_PLUGIN_METAS');
	$version_cible = $mes_infos['version'];

	switch ($action){
		case 'test':
			//Appel de la fonction d'installation. Lors du clic sur l'icône depuis le panel.
			//quand le plugin est activé et test retourne false
			$current_version = 0.0;
			if ((!isset($GLOBALS['meta']['spip_metas_version'])) || (($current_version = $GLOBALS['meta']['spip_metas_version'])!=$version_cible))
				return false;
			else
				return true;
		break;
		case 'install':
			//Appel de la fonction d'installation. Lors du clic sur l'icône depuis le panel.
			//quand le plugin est activé et test retourne false
			metas_upgrade('spip_metas_version', $version_cible);
		break;
		case 'uninstall':
			//Appel de la fonction de suppression
			//quand l'utilisateur clique sur "supprimer tout" (disponible si test retourne true)
			metas_vider_tables();
		break;
	}
}

// fonction d'installation, mise a jour de la base
function metas_upgrade($nom_meta_base_version, $version_cible){
	$current_version = 0.0;
	if ((!isset($GLOBALS['meta']['spip_metas_version']))
			|| (($current_version = $GLOBALS['meta']['spip_metas_version'])!=$version_cible)){
		include_spip('base/metas');
		if (version_compare($current_version,'0.0','<=')){
			include_spip('base/create');
			include_spip('base/abstract_sql');
			// cette fonction cree les tables declarees manquantes
			// ou ajoute des champs declares, manquants
			creer_base();
			echo "Installation du plugin M&eacute;tas effectu&eacute;e correctement !<br/>";
			ecrire_meta('spip_metas_version',$current_version=$version_cible,'non');
		}
	}
}

// fonction de desinstallation
function metas_vider_tables($nom_meta_base_version) {
	sql_drop_table("spip_metas");
	sql_drop_table("spip_metas_liens");
	effacer_meta('spip_metas_version');
	effacer_meta('spip_metas_title');
	effacer_meta('spip_metas_description');
	effacer_meta('spip_metas_mots_importants');
	effacer_meta('spip_metas_mots_keywords'); // on garde au cas où une table keywords subsisterait encore en DB.
}
?>