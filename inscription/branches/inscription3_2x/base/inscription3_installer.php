<?php
/**
 * Plugin Inscription3 pour SPIP
 * © 2007-2011 - cmtmt, BoOz, kent1
 * Licence GPL v3
 *
 * Fonctions d'installation et de désinstallation du plugin
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/meta');
include_spip('cfg_options');
include_spip('inc/cextras_gerer');
include_spip('base/inscription3');

/**
 * Fonction d'installation et de mise à jour du plugin
 * @return
 */
function inscription3_upgrade($nom_meta_base_version,$version_cible){
	$exceptions_des_champs_auteurs_elargis = pipeline('i3_exceptions_des_champs_auteurs_elargis',array());

	$current_version = "0.0";
	if (isset($GLOBALS['meta'][$nom_meta_base_version]))
		$current_version = $GLOBALS['meta'][$nom_meta_base_version];

	//insertion des infos par defaut
	$inscription3_meta = $GLOBALS['meta']['inscription3'];

	//Certaines montées de version ont oublié de corriger la meta de I2
	//si ce n'est pas un array alors il faut reconfigurer la meta
	if (isset($inscription3_meta) && !is_array(unserialize($inscription3_meta))) {
		spip_log("INSCRIPTION 3 : effacer la meta inscription3 et relancer l'install","inscription3");
		effacer_meta('inscription3');
		$current_version = 0.0;
	}

	$inscription2_meta = unserialize($GLOBALS['meta']['inscription2']);

	/**
	 * Inscription2 semble installé, on tranfère sa configuration vers inscription3
	 */
	if(isset($inscription2_meta) && is_array($inscription2_meta)){
		ecrire_meta('inscription3', serialize($inscription2_meta));
		$inscription3_meta = $inscription2_meta;
		inscription3_transfert_infos_auteurs();
	}
	//Si c est une nouvelle installation toute fraiche
	if ((!isset($GLOBALS['meta'][$nom_meta_base_version]))
		|| (($current_version = $GLOBALS['meta'][$nom_meta_base_version])!=$version_cible)){
			if (version_compare($current_version,'0.0','<=')){
			spip_log('INSCRIPTION3 : installation neuve','inscription3');
			if(!is_array($inscription3_meta)){
				ecrire_meta(
					'inscription3',
					serialize(array(
						'statut_nouveau' => '6forum',
						'statut_interne' => ''
					))
				);
			}

			include_spip('base/create');
			creer_base();
	
			/**
			 * Installation des tables et champs d'inscription3
			 * - La table spip_geo_pays
			 * - Les champs extras en fonction de la configuration
			 */
			i3_installer_pays();
			$champs = inscription3_declarer_champs_extras();
			installer_champs_extras($champs, $nom_meta_base_version, $version_cible);
	
			/**
			 * Si la meta d'inscription2 est là :
			 * - On transfère les données depuis la table spip_auteurs_elargis vers spip_auteurs
			 * - On supprime la meta d'inscription2
			 * - On supprime la table d'inscription2
			 * - On désactive le plugin
			 */
			if(isset($inscription2_meta)){
				include_spip('inc/plugins');
				$liste_plugin=liste_plugin_actifs();
				if(array_key_exists('INSCRIPTION2',$liste_plugin)){
					//spip_plugin_install('uninstall','inscription2','');
				}
			}
			ecrire_meta($nom_meta_base_version,$current_version=$version_cible,'non');
		}
		if (version_compare($current_version,'3.0.2','<')){
			include_spip('base/create');
			/**
			 * On force la réinstallation de la table spip_geo_pays car on y a ajouté le code ISO
			 */
			i3_installer_pays();
			ecrire_meta($nom_meta_base_version,$current_version="3.0.2",'non');
		}
	}
}


/**
 * Fonction de suppession du plugin
 *
 * Supprime les donnees de la table spip_auteurs_elargis
 * Supprime la table si plus nécessaire
 * Supprime la table des pays si nécessaire
 */
function inscription3_vider_tables($nom_meta_base_version) {
	effacer_meta('inscription3');
	sql_drop_table("spip_auteurs_liens");
	if (!defined('_DIR_PLUGIN_GEOGRAPHIE')) {
		sql_drop_table("spip_geo_pays");
	}
	effacer_meta($nom_meta_base_version);
}


/**
 * Installe ou réinstalle la table des pays
 * Dans tous les cas cette fonction réinstallera le contenu de la table spip_geo_pays
 */
function i3_installer_pays() {
	if (!defined('_DIR_PLUGIN_GEOGRAPHIE')) {
		// 1) suppression de la table existante
		// pour redemarrer les insert a zero
		$descpays = sql_showtable('spip_geo_pays', '', false);
		if(isset($descpays['field'])){
			sql_drop_table("spip_geo_pays");
		}
		// 2) recreation de la table
		creer_base();
		if(($descpays = sql_showtable('spip_geo_pays', '', false)) && isset($descpays['field'])){
			// 3) installation des entrees
			// importer les pays
			include_spip('imports/pays');
			include_spip('inc/charset');
			foreach($GLOBALS['liste_pays'] as $k=>$p)
				sql_insertq('spip_geo_pays',array('id_pays'=>$k,'code_iso' => $p['code_iso'],'nom'=>unicode2charset(html2unicode($p['nom']))));
		}
	}
}

/**
 * Transfère les données de la tables auteurs_elargis vers spip_auteurs
 * @return unknown_type
 */
function inscription3_transfert_infos_auteurs(){
	$config = lire_config('inscription3');
	$exceptions_des_champs_auteurs_elargis = pipeline('i3_exceptions_des_champs_auteurs_elargis',array());

	/**
	 * On récupère un array $champs des champs qui doivent être dans la table
	 */
	$champs = array();
	$champs[] = 'id_auteur';
	if (is_array($config)){
		foreach($config as $clef=>$val){
			$cle = preg_replace("/_(obligatoire|fiche|table).*/", "", $clef);
			if(!in_array($cle,$champs)){
				if(!in_array($cle,$exceptions_des_champs_auteurs_elargis) and !preg_match(",(categories|zone|newsletter).*$,", $cle) and ($val == 'on')){
					$champs[] = $cle;
				}
			}
		}
	}

	$desc_auteurs_elargis = sql_showtable('spip_auteurs_elargis', '', false);
	if(isset($desc_auteurs_elargis['field'])){
		$champs = array_intersect($desc_auteurs_elargis,$champs);
		$auteurs = sql_select($champs,'spip_auteurs_elargis');
		while($auteur = sql_fetch($auteurs)){
			$id_auteur = $auteur['id_auteur'];
			unset($auteur['id_auteur']);
			sql_updateq('spip_auteurs',$auteur,"id_auteur=$id_auteur");
		}
	}
	return;
}
?>