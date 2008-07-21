<?php
/*
 *  Plugin Atelier pour SPIP
 *  Copyright (C) 2008  Polez Kévin
 *
 *    This program is free software: you can redistribute it and/or modify
 *    it under the terms of the GNU General Public License as published by
 *    the Free Software Foundation, either version 3 of the License, or
 *    (at your option) any later version.
 *
 *    This program is distributed in the hope that it will be useful,
 *    but WITHOUT ANY WARRANTY; without even the implied warranty of
 *    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *    GNU General Public License for more details.
 *
 *    You should have received a copy of the GNU General Public License
 *    along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 */
if (!defined("_ECRIRE_INC_VERSION")) return;
$GLOBALS[$GLOBALS['idx_lang']] = array(
	// A
	'atelier' => 'Atelier',
	'action' => 'Actions',
	'administration' => 'Administration',
	'ajouter_tache' => 'Ajouter une tache',
	'ajouter_page_cfg' => 'Ajouter une page CFG',
	'atelier_lang' => 'Gestion des fichiers langue',
	// B
	'bouton_supprimer' => 'Supprimer',
	'bouton_installer_base' => 'Installer la base de donn&eacute;e',
	'bouton_creer_repertoire' => 'Cr&eacute;er le r&eacute;pertoire du plugin',
	'bouton_creer_fichier_lang' => 'Cr&eacute;er un fichier langue',
	'bouton_creer_repertoire_lang' => 'Cr&eacute;er le r&eacute;pertoire lang',
	'bouton_enregistrer' => 'Enregistrer',
	'bouton_generer_todo' => 'G&eacute;n&eacute;rer le fichier TODO.txt',
	'bouton_checkout_projet' => 'Lancer un checkout du projet',
	'bouton_update_projet' => 'Lancer un update du projet',
	// C
	'charger_pipelines' => 'charger_pipelines.php',
	'charger_plugins_fonctions' => 'charger_plugins_fonctions.php',
	'charger_plugins_options' => 'charger_plugins_options.php',
	'choisissez_lang' => 'Choisissez la langue du fichier : ',
	'cfg' => 'CFG : moteur de configuration',
	'creer_repertoire_lang' => 'Cr&eacute;er le r&eacute;pertoire "lang"',
	'creer_projet' => 'Cr&eacute;er le projet ?',
	'contenu_repertoire_lang' => 'Contenu du r&eacute;pertoire "lang". Cliquez sur l\'un des fichiers pour le modifier ou le visualiser.',
	'commande' => '- Commande : ',
	'code_retour' => '- Code retour : ',
	// D
	'documentation' => 'Documentation',
	'documentation_code' => 'Documentation du code SPIP',
	'droit_insuffisant' => 'Vous n\'avez pas les droits necessaires pour ecrire dans le repertoire plugin ...<br />Veuilliez modifier les droits de ce r&eacute;pertoire.',
	'dev' => 'Dev',
	// E
	'explication_creer_fichier_lang' => 'Vous pouvez cr&eacute;er un nouveau fichier langue. Celui-ci sera ajout&eacute; &agrave; votre r&eacute;pertoire "lang". La liste des fichiers d&eacute;j&agrave; existants est situ&eacute;e dans la colonne de gauche.',
	'explication_rep_lang' => 'Le r&eacute;pertoire lang contient tous les fichiers langue de votre plugin.',
	'explication_ajouter_lang' => 'Remplissez les champs ci-dessous pour ajouter une d&eacute;finition à votre fichier langue.',
	'etat_projet' => 'Etat du projet : ',
	'erreur_nom_manquant' => 'Erreur : Nom manquant ! checkout impossible<br />',
	'erreur_deja_present' => 'Erreur : Plugin d&eacute;j&agrave; present ! checkout impossible<br />',
	'erreur_commande' => 'Erreur commande<br />',
	'erreur_svn_pas_installe' => 'Subversion n\'est pas install&eacute;',
	// I
	'installer_base' => 'Le plugin Atelier utilise trois tables suppl&eacute;mentaires dans la base de donn&eacute;e, il faut donc installer ces tables pour pouvoir utiliser le plugin Atelier. Vous pouver &agrave; tout moment supprimer ces nouvelles tables en utilisant l\'action "Supprimer le plugin Atelier".',
	'importer_projet_zone' => 'Importer un projet de la zone',
	'installer_svn' => 'Subversion ne semble pas &ecirc;tre install&eacute; sur votre syst&egrave;me. Pour b&eacute;n&eacute;ficier des options offerts par subversion, installez-le.',
	// L
	'licence' => 'Distribu&eacute; sous licence GNU/GPL',
	'liste_projets' => 'Liste des projets',
	'liste_taches' => 'Liste des taches',
	'liste_taches_ouvertes' => 'Liste des taches ouvertes',
	'liste_taches_fermees' => 'Liste des taches ferm&eacute;es',
	'lang' => 'Internationalisation',
	// M
	'meta_cache' => 'meta_cache.txt',
	'modifier_projet' => 'Modifier le projet',
	'modifier_tache' => 'Modifier la tache',
	// N
	'nouveau_projet' => 'Nouveau projet',
	'nouveau_tache' => 'Nouvelle tache',
	'nom_projet' => 'Nom du projet : ',
	// P
	'page_principale' => 'Page principale',
	'projet_svn' => 'Importer un projet de la "zone". Cette action va provoquer un "checkout" du projet et cr&eacute;er une copie de travail dans votre r&eacute;pertoire plugins. Si vous souhaitez cr&eacute;er un projet du m&ecirc;me nom dans l\'atelier, cochez la case "cr&eacute;er le projet ?".',
	'projet_importer_svn' => 'Projet import&eacute; par svn',
	'projet_ajoute' => 'Projet ajout&eacute; ...',
	'plugins_xml' => 'plugins_xml.cache',
	'plugin_xml' => 'plugin.xml',
	'plugin' => 'Plugin',
	'projets' => 'Projets',
	'presentation' => 'Le plugin atelier se veut &ecirc;tre un framework de developpement pour spip. Il a pour but de simplifier le developpement d\'outils tel qu\'un plugin ou un squelette',
	'prevenir_suppression' => 'Attention, l\'op&eacute;ration que vous vous apprettez &agrave; effectuer supprimera d&eacute;finitivement les tables de la base de donn&eacute;e utilis&eacute; par le plugin Atelier.',
	'page_lang' => 'Gestion des fichiers langue',
	'page_svn' => 'Outils Subversion',
	'pas_definition' => 'Ce fichier langue ne contient aucunes d&eacute;finitions ...',
	// R
	'raccourcis' => 'Raccourcis',
	'repertoire' => 'Repertoire',
	'repertoire_inexistant' => 'Votre plugin ne semble pas posseder de r&eacute;pertoire de travail dans le r&eacute;pertoire "./plugins" de SPIP.',
	'retour_atelier' => 'Retour &agrave; l\'atelier',
	'revenir_projet' => 'Retour au projet',
	// S
	'supprimer_atelier' => 'Supprimer le plugin Atelier',
	'stable' => 'Stable',
	'squelette' => 'Squelette',
	'svn' => 'Subversion',
	// T
	'tache' => 'Tache',
	'test' => 'Test',
	'texte_descriptif' => 'Descriptif',
	'texte_etat' => 'Etat',
	'texte_id_projet' => 'ID',
	'texte_id_tache' => 'ID',
	'texte_prefixe' => 'Prefixe',
	'texte_prefixe_obligatoire' => 'Prefixe de votre projet [Obligatoire]',
	'texte_titre' => 'Titre',
	'texte_type' => 'Type',
	'titre' => 'Atelier',
	'titre_edit_choix_etat' => 'Choisissez l\'etat de votre tache',
	'titre_edit_choix_projet' => 'Choisissez votre projet',
	'titre_edit_choix_type' => 'Choisissez le type de projet',
	'titre_fichiers_temporaires' => 'Fichiers temporaires',
	'titre_infos' => 'Informations',
	'titre_projets' => 'Lecture d\'un projet',
	'titre_projets_edit' => 'Edition d\'un projet',
	'titre_taches' => 'Lecture d\'une tache',
	'titre_taches_edit' => 'Edition d\'une tache',
	'titre_supprimer_atelier' => 'Suppression de l\'atelier',
	'type_projet' => 'Type de projet : ',
	'update_svn' => 'Mettre &agrave; jour votre copie de travail à partir de la "zone". Cette action va provoquer un "update" du projet et mettre &agrave; jour les diff&eacute;rents fichiers de votre projet.',
	// V
	'voir_metas' => 'Visualiser les metas',

);
?>
