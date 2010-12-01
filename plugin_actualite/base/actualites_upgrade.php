<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/meta');
//include_spip('inc/utils');
include_spip('base/create');



/** INSTALLATION OU MISE À JOUR DES TABLES SUPPLÉMENTAIRES **/
//
// Notes :  
//	- variable $actualites_base_version : version actuelle de la base
//	- variable $version_cible : nouvelle version de la base, indiquée dans le champ 'version' de plugin.xml

function actualites_upgrade($actualites_base_version,$version_cible){

	$current_version = 0.0;
	
	// Si la version cible est différente de la version actuelle, alors on a des choses à faire.
	if ((!isset($GLOBALS['meta'][$actualites_base_version])) 						// si la variable actualites_base_version est n'est pas renseignée
		|| (($current_version = $GLOBALS['meta'][$actualites_base_version])!=$version_cible)){		// OU si current_version est différent de version_cible

		// Cas d'une première installation (aucune base préexistante)
		if ($current_version==0.0){
			spip_log("[actualites!] -- Première installation du plugin actualites!", "actualites!");
			// On indique où se situent les références de la base
			include_spip('base/actualites_pipelines');
			// On crée la  base (fonction spip)
			creer_base();
			// On met à jour la valeur de la version de la base du plugin installé
			ecrire_meta($actualites_base_version, $current_version=$version_cible, 'non');
			spip_log("[actualites!] Opération terminée : base créée (version $version_cible).", "actualites!");
		}

		// Si la version courante est inférieure à la version 0.1
		if ($current_version<0.1){
			// Note : la version 0.2 de la base correspond à une optimisation et harmonisation 
			// générale des tables principales ; d'où d'importantes modifications.

			spip_log("[actualites!] -- Version actuelle de la base : $current_version. Mise à jour vers la version 0.1 de la base", "actualites!");
			// -- Les champs communs
			// Renommage des champs 'lien_evenement' et 'lien_publication' en 'lien'
			// sql_alter("TABLE vu_evenements CHANGE lien_evenement lien text NOT NULL");
			// Renommage des champs 'date_redac' en 'date'
			// sql_alter("TABLE vu_annonces CHANGE date_redac date text NOT NULL");
			
			// -- Les champs spécifiques
			// Table annonces : on renomme 'date_peremption' en 'peremption'
			// sql_alter("TABLE vu_annonces CHANGE date_peremption peremption date NOT NULL");
			// Table annonces : on ajoute un champ texte 'annonceur'
			// sql_alter("TABLE vu_annonces ADD annonceur text NOT NULL AFTER lien");
			// Table evenements : on renomme 'lieu' en 'lieu_evenement'
			// sql_alter("TABLE vu_evenements CHANGE lieu lieu_evenement text NOT NULL");
			
			// -- Les champs optionnels
			// La table publications doit aussi avoir un champ 'type'
			// sql_alter("TABLE vu_publications ADD type text NOT NULL AFTER date_publication");
			// Toutes les tables doit avoir un champ 'descriptif'	
			// sql_alter("TABLE vu_annonces ADD descriptif text NOT NULL AFTER type");
			// On modifie les champs '*_sources' en 'sources_*' sur toutes les tables
			// sql_alter("TABLE vu_annonces CHANGE nom_source source_nom text NOT NULL");
			
			//-- Ménage
			// On supprime le champs 'date_vue' sur toutes les tables
			// sql_alter("TABLE vu_annonces DROP COLUMN date_vue");
			// On renomme les tables pour une meilleure intégration : vu_* devient spip_vu_* 
			// sql_alter("TABLE vu_annonces RENAME TO spip_vu_annonces");
			// On renomme par la même occasion les tables auxilliaires
			// sql_alter("TABLE vu_annonces_mots RENAME TO spip_mots_vu_annonces");
			
			// -- Le travail est terminé, la base est à jour. Reste à mettre à jour la valeur de la version de la base
			//ecrire_meta($vu_base_version,$current_version=$version_cible, 'non');
			spip_log("[actualites!] Opération terminée : mise à jour de la base vers la version 0.1", "actualites!");
		}

	}

}




/** DÉSINSTALLATION DES TABLES SUPPLÉMENTAIRES **/
//
// Note :
//	- /!\ Supprimer toutes les tables supplémentaires, et les informations contenues. Aucun retour en arrière possible !
//	- Concerne la désinstallation proprement dite, et non une simple désactivation.
//	- Variable $nom_meta_base_version : indique la version actuelle de la base

function actualites_vider_tables($actualites_base_version) {
	
	spip_log("[actualites!] -- Désinstallation définitive de la base", "actualites!");

	// On supprime les tables supplémentaires crées avec le plugin
	sql_drop_table("spip_actualites");
	sql_drop_table("spip_actualites_mots");
	sql_drop_table("spip_actualites_liens");
	
	// Puis on supprime les informations meta liées au plugin
	effacer_meta($actualites_base_version);

	spip_log("[actualites!] Opération terminée : désinstallation de la base.", "actualites!");

}

?>
