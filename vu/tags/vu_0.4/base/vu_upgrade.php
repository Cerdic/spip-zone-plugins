<?php

if (!defined("_ECRIRE_INC_VERSION")) return;


// Gestion d'un mode debug (cependant limité à ce fichier)
/* $DEBUG = 1; // Mode debug activé, si = 1
if($DEBUG)
	spip_log("-- Mode debug activé", "vu!");
else 
	spip_log("[Vu!] -- Mode debug désactivé", "vu!");
*/

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
	if ((!isset($GLOBALS['meta'][$vu_base_version])) 						// si la variable vu_base_version est n'est pas renseignée
		|| (($current_version = $GLOBALS['meta'][$vu_base_version])!=$version_cible)){		// OU si current_version est différent de version_cible

		// Cas d'une première installation (aucune base préexistante)
		if ($current_version==0.0){
			spip_log("[Vu!] -- Première installation du plugin Vu!", "vu!");
			// On indique où se situent les références de la base
			include_spip('base/vu_pipelines');
			// On crée la  base (fonction spip)
			creer_base();
			// On met à jour la valeur de la version de la base du plugin installé
			ecrire_meta($vu_base_version, $current_version=$version_cible, 'non');
			spip_log("[Vu!] Opération terminée : base créée (version $version_cible).", "vu!");
		}

		// Si la version courante est inférieure à la version 0.2
		if ($current_version<0.2){
			// Note : la version 0.2 de la base correspond à une optimisation et harmonisation 
			// générale des tables principales ; d'où d'importantes modifications.

			spip_log("[Vu!] -- Version actuelle de la base : $current_version. Mise à jour vers la version 0.2 de la base", "vu!");
			// -- Les champs communs
			// Renommage des champs 'lien_evenement' et 'lien_publication' en 'lien'
			sql_alter("TABLE vu_evenements CHANGE lien_evenement lien text NOT NULL");
			sql_alter("TABLE vu_publications CHANGE lien_publication lien text NOT NULL");
			// Renommage des champs 'date_redac' en 'date'
			sql_alter("TABLE vu_annonces CHANGE date_redac date text NOT NULL");
			sql_alter("TABLE vu_evenements CHANGE date_redac date text NOT NULL");
			sql_alter("TABLE vu_publications CHANGE date_redac date text NOT NULL");

			// -- Les champs spécifiques
			// Table annonces : on renomme 'date_peremption' en 'peremption'
			sql_alter("TABLE vu_annonces CHANGE date_peremption peremption date NOT NULL");
			// Table annonces : on ajoute un champ texte 'annonceur'
			sql_alter("TABLE vu_annonces ADD annonceur text NOT NULL AFTER lien");
			// Table evenements : on renomme 'lieu' en 'lieu_evenement'
			sql_alter("TABLE vu_evenements CHANGE lieu lieu_evenement text NOT NULL");
			// Table evenements : on ajoute un champ texte 'organisateur'
			sql_alter("TABLE vu_evenements ADD organisateur text NOT NULL AFTER lieu_evenement");
			// Table publications : on ajoute un champ texte 'editeur'
			sql_alter("TABLE vu_publications ADD editeur text NOT NULL AFTER auteur");
			// Table publications : on ajoute un champ date 'date_publication'
			sql_alter("TABLE vu_publications ADD date_publication date NOT NULL AFTER editeur");
			// Table publications : on ajoute un champ texte 'langue'
			sql_alter("TABLE vu_publications ADD langue text NOT NULL AFTER descriptif");

			// -- Les champs optionnels
			// La table publications doit aussi avoir un champ 'type'
			sql_alter("TABLE vu_publications ADD type text NOT NULL AFTER date_publication");
			// Toutes les tables doit avoir un champ 'descriptif'	
			sql_alter("TABLE vu_annonces ADD descriptif text NOT NULL AFTER type");
			sql_alter("TABLE vu_evenements ADD descriptif text NOT NULL AFTER type");
			sql_alter("TABLE vu_publications ADD descriptif text NOT NULL AFTER type");
			// On modifie les champs '*_sources' en 'sources_*' sur toutes les tables
			sql_alter("TABLE vu_annonces CHANGE nom_source source_nom text NOT NULL");
			sql_alter("TABLE vu_annonces CHANGE lien_source source_lien text NOT NULL");
			sql_alter("TABLE vu_evenements CHANGE nom_source source_nom text NOT NULL");
			sql_alter("TABLE vu_evenements CHANGE lien_source source_lien text NOT NULL");
			sql_alter("TABLE vu_publications CHANGE nom_source source_nom text NOT NULL");
			sql_alter("TABLE vu_publications CHANGE lien_source source_lien text NOT NULL");

			//-- Ménage
			// On supprime le champs 'date_vue' sur toutes les tables
			sql_alter("TABLE vu_annonces DROP COLUMN date_vue");
			sql_alter("TABLE vu_evenements DROP COLUMN date_vue");
			sql_alter("TABLE vu_publications DROP COLUMN date_vue");
			// On renomme les tables pour une meilleure intégration : vu_* devient spip_vu_* 
			sql_alter("TABLE vu_annonces RENAME TO spip_vu_annonces");
			sql_alter("TABLE vu_evenements RENAME TO spip_vu_evenements");		
			sql_alter("TABLE vu_publications RENAME TO spip_vu_publications");
			// On renomme par la même occasion les tables auxilliaires
			sql_alter("TABLE vu_annonces_mots RENAME TO spip_mots_vu_annonces");
			sql_alter("TABLE vu_evenements_mots RENAME TO spip_mots_vu_evenements");		
			sql_alter("TABLE vu_publications_mots RENAME TO spip_mots_vu_publications");

			// -- Le travail est terminé, la base est à jour. Reste à mettre à jour la valeur de la version de la base
			//ecrire_meta($vu_base_version,$current_version=$version_cible, 'non');
			spip_log("[Vu!] Opération terminée : mise à jour de la base vers la version 0.2", "vu!");
		}

	}

}




/** DÉSINSTALLATION DES TABLES SUPPLÉMENTAIRES **/
//
// Note :
//	- /!\ Supprimer toutes les tables supplémentaires, et les informations contenues. Aucun retour en arrière possible !
//	- Concerne la désinstallation proprement dite, et non une simple désactivation.
//	- Variable $nom_meta_base_version : indique la version actuelle de la base

function vu_vider_tables($vu_base_version) {
	
	spip_log("[Vu!] -- Désinstallation définitive de la base", "vu!");

	// On supprime les tables supplémentaires crées avec le plugin
	sql_drop_table("spip_vu_annonces");
	sql_drop_table("spip_vu_annonces_mots");
	sql_drop_table('spip_vu_evenements');
	sql_drop_table('spip_vu_evenements_mots');
	sql_drop_table('spip_vu_publications');
	sql_drop_table('spip_vu_publications_mots');
	
	// Puis on supprime les informations meta liées au plugin
	effacer_meta($vu_base_version);

	spip_log("[Vu!] Opération terminée : désinstallation de la base.", "vu!");

}

?>
