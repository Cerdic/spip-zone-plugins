<?php

/*
 *  Plugin Bouquinerie pour SPIP
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
	'a' => ' &agrave; ',
	'actions' => 'Actions',
	'administration' => 'Administration',
	'ajouter_livre' => 'Ajouter un livre',
	'ajouter_catalogue' => 'Ajouter un catalogue',
	'auteur' => 'Auteur :',
	'adresse_bouquinerie' => 'Adresse de la bouquinerie',
	'adresse_siege_social' => 'Adresse du si&egrave;ge social',
	'attention_champs_vide' => 'Les champs de recherche vides seront ignor&eacute;s.',
	'attention_temps' => 'Attention, cette op&eacute;ration peut prendre plusieurs minutes.',
	'attention_supprimer_bouquinerie' => 'Attention, l\'action que vous vous appretez a effectuer supprimera d&eacute;finitivement de votre base de donn&eacute;e toutes les tables de la bouquinerie.<br /><br />N\'effectuez cela que si vous certains de ce que vous faites',
	'attention_supprimer_catalogue' => 'Attention. Si vous cochez la case ci-dessous, la suppression du catalogue provoquera la suppression de tous les livres qui le composent.',

	// B
	'bouton_rechercher' => 'Lancer la recherche',
	'bouton_sur_certain' => 'Oui, je veux supprimer ce catalogue',
	'bouton_orphelins' => 'Ranger les livres orphelins dans ce catalogue',
	'bouton_supprimer_bouquinerie' => 'Oui, je veux supprimer la bouquinerie de la base de donn&eacute;e de SPIP',
	'bouton_exporter' => 'Lancer une exportation',
	'bouquinerie' => 'Bouquinerie',

	// C
	'catalogue' => 'Catalogue :',
	'catalogue_bouquinerie' => 'Un catalogue Bouquinerie',
	'catalogue_priceminister' => 'Un catalogue Price Minister',
	'condition_generale_vente' => 'Conditions g&eacute;n&eacute;rales de vente',
	'configuration' => 'Configuration',
	'critere_doublon_titre' => 'Bas&eacute; sur le crit&egrave;re "Titre"',
	'critere_doublon_isbn' => 'Bas&eacute; sur le crit&egrave;re "Isbn"',
	'creer_motscles' => 'Cr&eacute;er les mots-cl&eacute;s et groupes de mots-cl&eacute;s &agrave; partir des enregistrements que vous importez ?',

	// D
	'date_creation' => 'Date de cr&eacute;ation du livre :',
	'documentation' => 'Documentation',
	'doublons_choix_type_recherche' => 'Pour effectuer une recherche de doublons, selectionnez ci-dessous le type de recherche a effectuer :',

	// E
	'etat_livre' => 'Etat du livre :',
	'exporter_catalogue' => 'Exporter un catalogue',
	'editer_livre' => 'Modifier le livre',
	'edition' => 'Edition :',
	'editer_catalogue' => 'Editer le catalogue',
	'erreur_upload' => 'Une erreur c\'est produite lors de la tentative d\'importation de votre fichier',
	'explication_doublons' => 'Cette page affiche les doublons selon le crit&egrave;re de recherche.',
	'explication_exportation' => 'L\'exportation vous permet de cr&eacute;er un fichier ".ods" contenant les enregistrements de votre base de donn&eacute;e ou d\'un catalogue. Choisissez votre catalogue &agrave; exporter ou bien cochez la case "tout exporter". l\'exportation se fera au format "Bouquinerie" et non dans le format d\'origine du catalogue.',
	'explication_doublons_import' => 'La recherche de doublons s\'effectue aussi bien dans le fichier &agrave; importer que dans votre base de donn&eacute;e',
	'exclusion_toute_bdd' => 'Exclusion bas&eacute; sur la totalit&eacute; de votre base de donn&eacute;e',
	'explication_exclusion_toute_bdd' => 'Si vous d&eacute;cochez cette case, l\'exclusion ne sera bas&eacute; que sur le catalogue choisi.',
	'exclure_doublons' => 'Exclure les enregistrements en double',

	// F
	'format' => 'Format du livre :',
	'fichier_creer' => 'Le fichier <b>@fichier@</b> a &eacute;t&eacute; cr&eacute;er dans le r&eacute;pertoire tmp de spip.',
  
	// G
	'gerant' => 'G&eacute;rant',

	// H
	'horaires_ouvertures' => 'Horaires d\'ouvertures',

	// I
	'id_livre' => 'N°',
	'installer_base_expliq' => 'Votre base de donn&eacute;e ne semble pas &ecirc;tre install&eacute;e. Veuilliez proc&eacute;der &agrave; l\'installation de votre base de donn&eacute;e',
	'installer_base_expliq2' => 'Cette action ajoutera 4 tables &agrave; votre base de donn&eacute;e. Vous pouvez &agrave; tout moment remettre votre base de donn&eacute;e dans l\'&eacute;tat ou elle &eacute;tait en utilisant l\'action "supprimer la bouquinerie".',
	'installer_base' => 'Installer la base de donn&eacute;e',
	'importer_catalogue' => 'Importer un catalogue',
	'import_fichier' => '<b>Choisissez votre fichier</b>',
	'isbn' => 'ISBN :',
	'infos' => 'Informations',
	'infos_generiques' => '<b>utilisation des caract&egrave;res g&eacute;n&eacute;riques : </b><hr />- <b>le Joker </b>: le caract&egrave;re "_" remplace n\'importe quelle lettre. Plac&eacute; dans un mot (par exemple : "al_es") , la recherche renverra les titres contenant aussi bien le mot "alpes" que le mot "Floralies"<br /><br />- <b>le groupe de lettre </b>: le caract&agrave;res "%" remplace n\'importe quel groupe de lettre. Plac&eacute; dans un mot (par exemple : "sp%n"), la recherche renverra les titres contenant aussi bien le mot "spawn" que le mot "sphinx"<br />',
	'importe_le' => 'Catalogue import&eacute; le ',
	'import_image_non' => 'Ne pas importer les images',
	'import_image_oui' => 'Les rechercher, et les uploader (Gourmand en ressource)',
	'import_image_distant' => '..., et les lier comme documents distants (Gourmand en ressource)',
	'import_image_url' => '..., et ne conserver que leurs urls (Gourmand en ressource)',
	'informations_importation' => 'Attention, plus le fichier que vous importez contient de données, et plus vous aurez besoin de mémoire autorisée par votre php.ini.',

	// L
	'licence' => 'Distribu&eacute; sous licence GNU/GPL',
	'liste_catalogue' => 'Liste des catalogues',
	'livres' => 'Lecture d\'un livre',
	'livres_orphelins' => 'Certains de vos livres n\'ont pas de catalogues. Vous pouvez selectionner un catalogue ci-dessous pour le leur assigner.',
	'liste_recherche' => 'Liste des recherches',

	// M
	'mail_gerant' => 'E-mail du g&eacute;rant',
	'modifier_catalogue' => 'Modifier le catalogue :',
	'modifier_livre' => 'Modifier le livre :',
	'modifier' => 'Modifier',

	// N
	'nom_bouquinerie' => 'Nom de la bouquinerie',
	'nouveau_catalogue' => 'Cr&eacute;er un nouveau catalogue',


	// P
	'prix_vente' => 'Prix de vente :',
	'par_titre' => 'Par titre',
	'par_auteur' => 'Par auteur',
	'par_illustrateur' => 'Par illustrateur',
	'par_edition' => 'Par edition',
	'par_prix_vente' => 'Par prix de vente',
	'par_isbn' => 'Par num&eacute;ro ISBN',

	// R
	'rechercher_livre' => 'Rechercher un livre',
	'rechercher_doublons' => 'Rechercher les doublons',
	'reference' => 'R&eacute;f&eacute;rence d\'origine :',
	'registre_commerce' => 'Registre du commerce',
	'revenir_haut' => 'Revenir en haut',
	'resultats' => 'Resultats',
	'raccourcis' => 'Raccourcis',

	// S
	'statut' => 'Statut :',
	'statut_a_vendre' => 'A vendre',
	'statut_vendu' => 'Vendu',
	'statut_reserve' => 'Reserv&eacute;',
	'supprimer_livre' => 'Supprimer le livre',
	'supprimer_catalogue' => 'Supprimer le catalogue',
	'supprimer_bouquinerie' => 'Supprimer la bouquinerie',
	'supprimer_tous_livres' => 'Supprimer tous les livres qui composent ce catalogue',

	// T
	'texte_descriptif' => '<b>Descriptif</b>',
	'texte_auteur' => '<b>Auteur</b>',
	'texte_illustrateur' => '<b>Illustrateur</b>',
	'texte_edition' => '<b>Edition</b>',
	'texte_prix_vente' => '<b>Prix de vente</b>',
	'texte_isbn' => '<b>ISBN</b>',
	'texte_etat_livre' => '<b>Etat du livre</b>',
	'texte_format' => '<b>Format du livre</b>',
	'texte_etat_jaquette' => '<b>Etat de la jaquette</b>',
	'texte_type_livre' => '<b>Type du livre</b>',
	'texte_reliure' => '<b>Reliure</b>',
	'texte_lieu_edition' => '<b>Lieu d\'&eacute;dition</b>',
	'texte_annee_edition' => '<b>Ann&eacute;e d\'&eacute;dition</b>',
	'texte_num_edition' => '<b>Num&eacute;ro d\'&eacute;dition</b>',
	'texte_inscription' => '<b>Inscription</b>',
	'texte_remarque' => '<b>Remarque</b>',
	'texte_commentaire' => '<b>Commentaire</b>',
	'texte_prix_achat' => '<b>Prix d\'achat</b>',
	'texte_lieu' => '<b>Lieu</b>',
	'texte_statut' => '<b>Statut</b>',
	'texte_num_facture' => '<b>Num&eacute;ro de la facture</b>',
	'texte_importer_type' => '<b>Type de fichier &agrave; importer</b>',
	'texte_reference' => '<b>R&eacute;f&eacute;rence</b>',
	'titre' => 'Bouquinerie',
	'titre_admin' => 'Administration',
	'titre_catalogues' => 'Lecture d\'un catalogue',
	'titre_catalogue_import' => 'Importation d\'un catalogue',
	'titre_ajouter_livre' => 'Ajout d\'un livre',
	'titre_ajouter_catalogue' => 'Ajout d\'un catalogue',
	'titre_cadre_actions' => 'ACTIONS :',
	'titre_cadre_interieur_statut' => '<b>Statut</b>',
	'titre_cadre_interieur_catalogue' => '<b>Catalogue</b>',
	'titre_cadre_interieur_catalogue_import' => '<b>Dans le catalogue</b>',
	'titre_cadre_interieur_images_import' => '<b>M&eacute;thode de traitement des images</b>',
	'titre_creer_motscles' => 'Cr&eacute;er les mots-cl&eacute;s',
	'titre_liste_livre' => 'liste des livres',
	'titre_nouveau_catalogue' => 'nouveau catalogue',
	'titre_nouveau_livre' => 'nouveau livre',
	'titre_mentions' => 'Mentions l&eacute;gales',
	'titre_modifier_catalogue' => 'Modification d\'un catalogue',
	'texte_pas_catalogue' => 'Vous n\'avez aucun catalogue, vous devez cr&eacute;er au minimum un catalogue pour pouvoir ajouter des livres',
	'titre_rechercher_livres' => 'Recherche de livres',
	'titre_rechercher_doublons' => 'Recherche de doublons',
	'titre_supprimer_catalogue' => 'Supprimer un catalogue',
	'titre_importation' => 'Importation',
	'titre_catalogue_export' => 'Exportation',
	'type_livre' => 'Type du livre :',
	
	// U
	'url_documentation' => 'http://www.spip-contrib.net/Plugin-Bouquinerie'

);

?>
