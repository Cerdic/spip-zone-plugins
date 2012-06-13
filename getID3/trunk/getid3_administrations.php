<?php
/**
 * GetID3
 * Gestion des métadonnées de fichiers sonores directement dans SPIP
 *
 * Auteurs :
 * kent1 (http://www.kent1.info - kent1@arscenic.info), BoOz
 * 2008-2012 - Distribué sous licence GNU/GPL
 *
 * Définition des tables
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/meta');

function getid3_upgrade($nom_meta_base_version,$version_cible){
	$current_version = 0.0;
	if (   (!isset($GLOBALS['meta'][$nom_meta_base_version]) )
			|| (($current_version = $GLOBALS['meta'][$nom_meta_base_version])!=$version_cible)){
		if (version_compare($current_version,'0.0','<=')){
			include_spip('base/create');
			maj_tables('spip_documents');
			$getid3_binaires = charger_fonction('getid3_verifier_binaires','inc');
			$getid3_binaires(true);
			
			ecrire_meta($nom_meta_base_version,$current_version=$version_cible,'non');
			echo '<p>'._T('getid3:install_ajout_champs_documents').'</p>';
		}
		if (version_compare($current_version,'0.1','<')){
			include_spip('base/create');
			maj_tables('spip_documents');
			ecrire_meta($nom_meta_base_version,$current_version=0.1);
			echo _T('getid3:install_mise_a_jour_base',array('version'=>'0.1'));
		}
		if (version_compare($current_version,'0.2','<')){
			include_spip('base/create');
			maj_tables('spip_documents');
			ecrire_meta($nom_meta_base_version,$current_version=0.2);
			echo _T('getid3:install_mise_a_jour_base',array('version'=>'0.2'));
		}
		if (version_compare($current_version,'0.3.1','<')){
			/**
			 * Vérifier si les logiciels sont présents
			 */
			$getid3_binaires = charger_fonction('getid3_verifier_binaires','inc');
			$getid3_binaires(true);
			ecrire_meta($nom_meta_base_version,$current_version='0.3.1');
		}
	}
}

function getid3_vider_tables($nom_meta_base_version) {
	effacer_meta($nom_meta_base_version);
}
?>