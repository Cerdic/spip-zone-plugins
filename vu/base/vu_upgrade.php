<?php

if (!defined("_ECRIRE_INC_VERSION")) return;


// Gestion d'un mode debug (cependant limité à ce fichier)
$DEBUG = 1; // Mode debug activé, si = 1
if($DEBUG)
	spip_log("-- Mode debug activé", "vu!");
else 
	spip_log("-- Mode debug désactivé", "vu!");


include_spip('inc/meta');
//include_spip('inc/utils');
include_spip('base/create');


/** INSTALLATION OU MISE À JOUR DES TABLES SUPPLÉMENTAIRES **/
//
// Notes :  
//	- variable $vu_base_version : version actuelle de la base
//	- variable $version_cible : nouvelle version de la base, indiquée dans le champ 'version' de plugin.xml

function vu_upgrade($vu_base_version,$version_cible){

	$current_version = 0.0;
	
	// Si la version cible est différente de la version actuelle, alors on a des choses à faire.
	if ((!isset($GLOBALS['meta'][$vu_base_version]))
	|| (($current_version = $GLOBALS['meta'][$vu_base_version])!=$version_cible)){

		// Cas d'une première installation (aucune base préexistante)
		if ($current_version==0.0){

			spip_log("-- Première installation du plugin Vu!", "vu!");

			// On indique où se situent les fonctions de creations de base
			include_spip('base/vu_pipelines');
			// On crée les tables (fonction spip)
			maj_tables('vu_annonces');
			maj_tables('vu_annonces_mots');
			maj_tables('vu_evenements');
			maj_tables('vu_evenements_mots');
			maj_tables('vu_publications');
			maj_tables('vu_publications_mots');
			spip_log("Fait : installation des nouvelles tables", "vu!");
			// On met à jour la valeur de la version de la base du plugin installé
			ecrire_meta($vu_base_version, $current_version=$version_cible, 'non');
			spip_log("Fait : mise à jour du champ vu_base_version", "vu!");

			spip_log("Opération terminée : installation du plugin Vu!", "vu!");
		}

		// Cas d'une migration vers la version 0.x 
		// à venir...
	}

}




/** DÉSINSTALLATION DES TABLES SUPPLÉMENTAIRES **/
//
// Note :
//	- /!\ Supprimer toutes les tables supplémentaires, et les informations contenues. Aucun retour en arrère possible !
//	- Concerne la désinstallation proprement dite, et non une simple désactivation.
//	- Variable $nom_meta_base_version : indique la version actuelle de la base

function vu_vider_tables($vu_base_version) {
	
	spip_log("-- Désinstallation du plugin Vu!", "vu!");

	// On supprime les tables supplémentaires crées avec le plugin
	sql_drop_table("vu_annonces");
	sql_drop_table("vu_annonces_mots");
	sql_drop_table('vu_evenements');
	sql_drop_table('vu_evenements_mots');
	sql_drop_table('vu_publications');
	sql_drop_table('vu_publications_mots');
	spip_log("Fait : suppression des tables supplémentaires", "vu!");
	
	// Puis on supprime les informations meta liées au plugin
	effacer_meta($vu_base_version);
	spip_log("Fait : effacement du champ vu_base_version", "vu!");

	spip_log("Opération terminée : désinstallation du plugin Vu!", "vu!");

}

?>
