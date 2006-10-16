<?php
/* csv2spip est un plugin pour cr�er/modifier les r�dacteurs et administrateurs restreints d'un SPIP � partir de fichiers CSV
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
			'resultat_fichier' => 'Resultats de l\'int�gration du fichier ',
			'titre_etape1' => 'Etape 1 : t�l�chargement du fichier sur le serveur',
			'err_etape1.1_debut' => 'Etape 1.1 : le t�l�chargement du fichier ',
			'err_etape1.1_fin' => ' � �chou� : veuillez recommencer. Code d\'erreur :',
			'err_etape1.2_debut' => 'Etape 1.2 : la copie du fichier ',
			'err_etape1.2_fin' => ' � �chou�, veuillez recommencer. Fichier destination : ',
			'ok_etape1' => 'Etape 1 : t�l�chargement r�ussit du fichier ',
			'titre_etape2' => 'Etape 2 : passage des donn�es du fichier dans la base temporaire',
			'err_etape2.1' => 'Etape 2.1 : erreur lors de la cr�ation de la table temporaire "tmp_auteur"',
			'ok_etape2.1' => 'Etape 2.1 : cr�ation de la table temporaire "tmp_auteurs" = OK',
			'err_etape2.1' => 'Etape 2.1 : erreur(s) lors de la lecture de la premi�re ligne du fichier (r&eacute;f&eacute;rence des colonnes) :
										 		 <br>Il manque le(s) nom(s) de colonne(s) suivant(s) : ',
			'err_etape2.2' => 'Etape 2.2 : erreur(s) lors de l\'int�gration des utilisateurs dans la base temporaire "tmp_auteurs" : ',
			'ok_etape2.2' => 'Etape 2.2 : int�gration des auteurs dans la base temporaire = OK',
			'titre_etape3' => 'Etape 3 : cr�ation des rubriques de disciplines',
			'err_etape3' => 'Etape 3 : erreur(s) lors de la cr�ation des rubriques des sous-groupes d\'administrateurs : ',
			'rubrique' => 'rubrique = ',
			'ok_etape3' => 'Etape 3 : cr�ation des rubriques pour les sous-groupes d\'administrateurs = OK',
			'titre_etape4' => 'Etape 4 : traitement des utilisateurs d�ja dans la base spip_auteurs',
			'etape4.1' => 'Etape 4.1 : cr�ation des nouveaux utilisateurs :',
			'etape4.2' => 'Etape 4.2 : mise � jour des mots de passe des utilisateurs existants :',
			'maj_mdp' => 'Mise � jour des mots de passe pour ',
			'etape4.3' => 'Etape 4.3 : suppression des r�dacteurs absents du fichier CSV :',
			'suppr_redac' => 'suppressions de r�dacteurs en erreur :',
			'redac_poubelle' => 'passage � la poubelle de r�dacteurs en erreur :',
			'archivage_debut' => 'Archivage = OK pour ',
			'archivage_fin' => ' articles dans la rubrique ',
			'suppression_debut' => 'Suppression de ',
			'suppression_fin' => 'articles = OK',
			'titre_etape5' => 'Etape 5 : attribution des rubriques aux administrateurs :',
			'titre_etape6' => 'Etape 6 : cr�ation des articles dans les rubriques : ',
			'ok_etape6_debut' => 'Cr�ation des articles pour ',
			'ok_etape6_fin' => ' rubriques = OK',
						
			'err_redac' => 'R�dacteurs en erreur : ',
			'err_admin' => 'Administrateurs en erreur : ',
			'err_admin_rubrique' => 'Administrateur(s) de rubrique en erreur : ',
			'err_article' => 'Articles en erreur : ',
			
			'creation' => 'Cr�ation de ',
			'suppression' => 'Suppression de ',
			'passage_poubelle' => 'Passage � la poubelle de ',
			'comptes_redac_ok' => ' comptes de r�dacteurs = OK',
			'comptes_admin_ok' => ' comptes administrateurs de rubriques = OK',
			'utilisateur' => 'utilisateur = ',
			'redac' => 'r�dacteur',
			'admin' => 'administrateur',
			'erreur' => ' => erreur = ',
			'rubrique_' => ' rubrique = ',
			'oui' => 'oui',
			'non' => 'non',			
			
			'titre_form' => 'Choix du fichier CSV et des options de cr�ation des comptes et rubriques: ',
			'titre_choix_fichier' => 'Choix du fichier CSV : ',
			'choix_fichier' => 'Fichier CSV � importer : ',
			'options_maj' => 'Mise � jour des mots de passe: ',
			'maj_mdp' => 'Mise � jour des mots de passe des utilisateurs: ',
			'suppr_absents' => 'Suppression des absents:',
			'suppr_redac' => 'Supprimer les utilisateurs qui ne sont pas dans le fichier CSV import�: ',
			'help_suppr_redac' => 'Pas de suppression automatique des administrateurs de rubriques !<br>
												    (pour IACA, cela correspond � une "bascule ann�e" des �l�ves d\'un �tablissement)',
			'suprr_articles' => 'Supprimer les articles des auteurs effac�s :',
			'transfert_archive' => 'Transf�rer les articles des r�dacteurs supprim�s dans une rubrique d\'archives: ',
			'nom_rubrique_archives' => 'Nom de la rubrique d\'archives : ',
			'choix_parent_archive' => 'Rubrique parent de la rubrique d\'archives :',
			'racine_site' => 'racine du site',
			'pas_de_rubriques' => 'Il n\'existe pas encore de rubriques dans votre SPIP : toutes les rubriques cr��es seront � la racine du site.',
			'traitement_supprimes' => 'Traitement des comptes supprim�s pour les auteurs d\'articles :',
			'auteurs_poubelle' => 'mettre les auteurs "� la poubelle" sans les supprimer compl�tement',
			'attribuer_articles' => 'supprimer les auteurs et attribuer leurs articles � l\'auteur',
			'passe_egale_login' => '(mot de passe idem login)',
			'creation_rubriques' => 'Cr�ation de rubriques pour les sous-groupes administrateurs: ',
			'rubrique_ss_groupes' => 'Cr�er une rubrique par sous-groupe d\'admins: ',
			'profs_admins' => '(IACA : les profs d\'une discipline seront administrateurs de leur rubrique)',
			'article_rubrique' => 'Cr�er un article dans chaque rubrique admin: ',
			'help_articles' => '(permet de rendre visibles ces rubriques dans la partie publique)',
			'choix_parent_rubriques' => 'Rubrique parent des rubriques � cr�er: ',
			'nom_groupe_admin' => 'Nom du groupe des admins de rubriques utilis� dans la colonne "ss_groupe" du fichier CSV: ',
			'help_nom_groupe_admin' => '(si ce champ est vide, tous les utilisateurs seront simples r�dacteurs)',
			'lancer' => 'Lancer la moulinette',
			
			'titre_help' => 'Caract�ristiques du fichier .CSV � utiliser: '
			
	);
?>