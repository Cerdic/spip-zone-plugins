<?php
/* csv2spip est un plugin pour créer/modifier les rédacteurs et administrateurs restreints d'un SPIP à partir de fichiers CSV
*
* Auteur : cy_altern (cy_altern@yahoo.fr)
*  
* Ce programme est un logiciel libre distribue sous licence GNU/GPL.
*  
**/
//echo "<br>depart fichier de langue...";

	$GLOBALS[$GLOBALS['idx_lang']] = array(
//			'csv2spip' => 'csv2spip',
			'module_titre' => 'csv2spip',
			'version' => 'Version : ',
			'titre_page' => 'CSV2SPIP : gestion des utilisateurs de SPIP &agrave; partir de fichiers CSV',
			'titre_info' => 'plugin CSV2SPIP',
			'help_info' => 'Cette page permet de cr&eacute;er et g&eacute;rer les auteurs et administrateurs restreint d\'un spip &agrave; partir de fichiers CSV',
			'retour_saisie' => 'retour saisie d\'un fichier',
			'resultat_fichier' => 'Resultats de l\'intégration du fichier ',
			'titre_etape1' => 'Etape 1 : téléchargement du fichier sur le serveur',
			'err_etape1.1_debut' => 'Etape 1.1 : le téléchargement du fichier ',
			'err_etape1.1_fin' => ' à échoué : veuillez recommencer. Code d\'erreur :',
			'err_etape1.2_debut' => 'Etape 1.2 : la copie du fichier ',
			'err_etape1.2_fin' => ' à échoué, veuillez recommencer. Fichier destination : ',
			'ok_etape1' => 'Etape 1 : téléchargement réussit du fichier ',
			'titre_etape2' => 'Etape 2 : passage des données du fichier dans la base temporaire',
			'err_etape2.1' => 'Etape 2.1 : erreur lors de la création de la table temporaire "tmp_auteur"',
			'ok_etape2.1' => 'Etape 2.1 : création de la table temporaire "tmp_auteurs" = OK',
			'err_etape2.1' => 'Etape 2.1 : erreur(s) lors de la lecture de la première ligne du fichier (r&eacute;f&eacute;rence des colonnes) :
										 		 <br>Il manque le(s) nom(s) de colonne(s) suivant(s) : ',
			'err_etape2.2' => 'Etape 2.2 : erreur(s) lors de l\'intégration des utilisateurs dans la base temporaire "tmp_auteurs" : ',
			'ok_etape2.2' => 'Etape 2.2 : intégration des auteurs dans la base temporaire = OK',
			'titre_etape3' => 'Etape 3 : création des rubriques de disciplines',
			'err_etape3' => 'Etape 3 : erreur(s) lors de la création des rubriques des sous-groupes d\'administrateurs : ',
			'rubrique' => 'rubrique = ',
			'ok_etape3' => 'Etape 3 : création des rubriques pour les sous-groupes d\'administrateurs = OK',
			'titre_etape4' => 'Etape 4 : traitement des utilisateurs déja dans la base spip_auteurs',
			'etape4.1' => 'Etape 4.1 : création des nouveaux utilisateurs :',
			'etape4.2' => 'Etape 4.2 : mise à jour des mots de passe des utilisateurs existants :',
			'maj_mdp' => 'Mise à jour des mots de passe pour ',
			'etape4.3' => 'Etape 4.3 : suppression des rédacteurs absents du fichier CSV :',
			'suppr_redac' => 'suppressions de rédacteurs en erreur :',
			'redac_poubelle' => 'passage à la poubelle de rédacteurs en erreur :',
			'archivage_debut' => 'Archivage = OK pour ',
			'archivage_fin' => ' articles dans la rubrique ',
			'suppression_debut' => 'Suppression de ',
			'suppression_fin' => 'articles = OK',
			'titre_etape5' => 'Etape 5 : attribution des rubriques aux administrateurs :',
			'titre_etape6' => 'Etape 6 : création des articles dans les rubriques : ',
			'ok_etape6_debut' => 'Création des articles pour ',
			'ok_etape6_fin' => ' rubriques = OK',
						
			'err_redac' => 'Rédacteurs en erreur : ',
			'err_admin' => 'Administrateurs en erreur : ',
			'err_admin_rubrique' => 'Administrateur(s) de rubrique en erreur : ',
			'err_article' => 'Articles en erreur : ',
			
			'creation' => 'Création de ',
			'suppression' => 'Suppression de ',
			'passage_poubelle' => 'Passage à la poubelle de ',
			'comptes_redac_ok' => ' comptes de rédacteurs = OK',
			'comptes_admin_ok' => ' comptes administrateurs de rubriques = OK',
			'utilisateur' => 'utilisateur = ',
			'redac' => 'rédacteur',
			'admin' => 'administrateur',
			'erreur' => ' => erreur = ',
			'rubrique_' => ' rubrique = ',
			'oui' => 'oui',
			'non' => 'non',			
			
			'titre_form' => 'Choix du fichier CSV et des options de création des comptes et rubriques: ',
			'titre_choix_fichier' => 'Choix du fichier CSV : ',
			'choix_fichier' => 'Fichier CSV à importer : ',
			'options_maj' => 'Mise à jour des mots de passe: ',
			'maj_mdp' => 'Mise à jour des mots de passe des utilisateurs: ',
			'suppr_absents' => 'Suppression des absents:',
			'suppr_redac' => 'Supprimer les utilisateurs qui ne sont pas dans le fichier CSV importé: ',
			'help_suppr_redac' => 'Pas de suppression automatique des administrateurs de rubriques !<br>
												    (pour IACA, cela correspond à une "bascule année" des élèves d\'un établissement)',
			'suprr_articles' => 'Supprimer les articles des auteurs effacés :',
			'transfert_archive' => 'Transférer les articles des rédacteurs supprimés dans une rubrique d\'archives: ',
			'nom_rubrique_archives' => 'Nom de la rubrique d\'archives : ',
			'choix_parent_archive' => 'Rubrique parent de la rubrique d\'archives :',
			'racine_site' => 'racine du site',
			'pas_de_rubriques' => 'Il n\'existe pas encore de rubriques dans votre SPIP : toutes les rubriques créées seront à la racine du site.',
			'traitement_supprimes' => 'Traitement des comptes supprimés pour les auteurs d\'articles :',
			'auteurs_poubelle' => 'mettre les auteurs "à la poubelle" sans les supprimer complètement',
			'attribuer_articles' => 'supprimer les auteurs et attribuer leurs articles à l\'auteur',
			'passe_egale_login' => '(mot de passe idem login)',
			'creation_rubriques' => 'Création de rubriques pour les sous-groupes administrateurs: ',
			'rubrique_ss_groupes' => 'Créer une rubrique par sous-groupe d\'admins: ',
			'profs_admins' => '(IACA : les profs d\'une discipline seront administrateurs de leur rubrique)',
			'article_rubrique' => 'Créer un article dans chaque rubrique admin: ',
			'help_articles' => '(permet de rendre visibles ces rubriques dans la partie publique)',
			'choix_parent_rubriques' => 'Rubrique parent des rubriques à créer: ',
			'nom_groupe_admin' => 'Nom du groupe des admins de rubriques utilisé dans la colonne "ss_groupe" du fichier CSV: ',
			'help_nom_groupe_admin' => '(si ce champ est vide, tous les utilisateurs seront simples rédacteurs)',
			'lancer' => 'Lancer la moulinette',
			
			'titre_help' => 'Caractéristiques du fichier .CSV à utiliser: '
			
	);
?>