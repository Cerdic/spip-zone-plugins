<?php
/* csv2spip est un plugin pour cr�er/modifier les visiteurs, r�dacteurs et administrateurs restreints d'un SPIP � partir de fichiers CSV
*	 					VERSION : 3.0 => plugin pour spip 1.9
*
* Auteur : cy_altern
*  
* Ce programme est un logiciel libre distribue sous licence GNU/GPL.
*  
**/

	$GLOBALS[$GLOBALS['idx_lang']] = array(
//			'csv2spip' => 'csv2spip',
			'module_titre' => 'csv2spip',
			'version' => 'Version : ',
			'titre_page' => 'CSV2SPIP : gestion des utilisateurs de SPIP &agrave; partir de fichiers CSV',
			'titre_info' => 'plugin CSV2SPIP',
			'help_info' => 'Cette page permet de cr&eacute;er et g&eacute;rer les auteurs et administrateurs restreint d\'un spip &agrave; partir de fichiers CSV',
			'retour_saisie' => 'retour saisie d\'un fichier',
			'resultat_fichier' => 'Resultats de l\'int�gration du fichier ',
			'titre_etape1' => 'Etape 1 :</strong> t�l�chargement du fichier sur le serveur',
			'err_etape1.1_debut' => 'Etape 1.1 :</strong> le t�l�chargement du fichier ',
			'err_etape1.1_fin' => ' � �chou� : veuillez recommencer. Code d\'erreur :',
			'err_etape1.2_debut' => 'Etape 1.2 :</strong> la copie du fichier ',
			'err_etape1.2_fin' => ' � �chou�, veuillez recommencer. Fichier destination : ',
			'ok_etape1' => '<strong>Etape 1 :</strong> t�l�chargement r�ussi du fichier ',
			'titre_etape2' => '<strong>Etape 2 :</strong> passage des donn�es du fichier dans la base temporaire',
			'err_etape2.1' => '<strong>Etape 2.1 :</strong> erreur lors de la cr�ation de la table temporaire "tmp_auteur"',
			'ok_etape2.1' => '<strong>Etape 2.1 :</strong> cr�ation de la table temporaire "tmp_auteurs" = OK',
			'err_etape2.1' => '<strong>Etape 2.1 :</strong> erreur(s) lors de la lecture de la premi�re ligne du fichier (r&eacute;f&eacute;rence des colonnes) :
										 		 <br>Il manque le(s) nom(s) de colonne(s) suivant(s) : ',
			'err_etape2.2' => '<strong>Etape 2.2 :</strong> erreur(s) lors de l\'int�gration des utilisateurs dans la base temporaire "tmp_auteurs" : ',
			'ok_etape2.2' => '<strong>Etape 2.2 :</strong> int�gration des auteurs dans la base temporaire = OK ',
			'titre_etape3' => '<strong>Etape 3 :</strong> gestion des sous-groupes (rubriques et acces_groupes)',
			'err_etape3.1' => '<strong>Etape 3.1 :</strong> erreur(s) lors de la cr�ation des rubriques des sous-groupes d\'administrateurs : ',
			'ok_etape3.1_debut' => '<strong>Etape 3.1 :</strong> cr�ation des rubriques admins = OK ',
			'ok_etape3.1_fin' => ' rubriques cr��es',
			'err_cree_rub_defaut' => 'Erreur lors de la cr�ation de la rubrique par d�faut des admins restreints : ',
			'ok_cree_rub_defaut' => 'Cr�ation de la rubrique par d�faut : ',
			'etape3.2' => '<strong>Etape 3.2 :</strong> cr�ation des groupes pour le plugin acces_groupes',
			'grpe_csv2spip' => 'groupe cr&eacute;&eacute, par csv2spip',
			'err_etape3.2' => '<strong>Etape 3.2 :</strong> erreur(s) lors de la cr�ation des groupes d\'acces : ',
			'ok_etape3.2_debut' => '<strong>Etape 3.2 :</strong> cr�ation des groupes pour le plugin acces_groupes = OK : ',		
			'ok_etape3.2_fin' => ' groupes cr��s',
			'err_integ_accesgroupes' => 'id_grpacces non trouv�e : ce groupe n\'existe pas dans la table _accesgroupes_groupes',
			'err_vider_accesgroupes' => 'Probl�me lors du vidage des auteurs du groupe d\'acc�s ',
			'ok_vider_accesgroupes' => 'Remise � z�ro des utilisateurs des groupes d\'acc�s = OK pour ',
			'titre_etape4' => '<strong>Etape 4 :</strong> traitement des utilisateurs',
			'etape4.1' => '<strong>Etape 4.1 : cr�ation des nouveaux utilisateurs</strong>',
			'etape4.2' => '<strong>Etape 4.2 : mise � jour des utilisateurs existants</strong>',
			'etape4.2.1' => '<strong>Etape 4.2.1 :</strong> mise � jour des informations personnelles : ',
			'ok_etape4.2.1' => 'mise � jour pour ',
			'etape4.2.2' => '<strong>Etape 4.2.2 :</strong> r�initialisation des groupes du plugin acces_groupes  pour les utilisateurs existants : ',
			'err_maj_grpacces' => 'utilisateurs en erreur : ',
			'ok_maj_grpacces' => 'r�initialisation = OK : ',
			'etape4.2.3' => '<strong>Etape 4.2.3 :</strong> r�initialisation des rubriques administr�es par les admins existants : ',
			'err_maj_rub_adm' => 'utilisateurs en erreur : ',
			'ok_maj_rub_adm' => 'r�initialisation = OK : ',
			'etape4.3' => '<strong>Etape 4.3 : g�n�ration des groupes d\'acces_groupes � partir des sous-groupes</strong>',
			'etape4.3.1' => '<strong>Etape 4.3.1 :</strong> int�gration des visiteurs dans leur sous-groupe acces_groupes :',
			'etape4.3.2' => '<strong>Etape 4.3.2 :</strong> int�gration des r�dacteurs dans leur sous-groupe acces_groupes :',
			'etape4.3.3' => '<strong>Etape 4.3.3 :</strong> int�gration des admins dans leur sous-groupe acces_groupes :',
			'etape4.4' => '<strong>Etape 4.4 : suppression des utilisateurs absents du fichier CSV</strong>',
			'etape4.4.1' => '<strong>Etape 4.4.1 :</strong> suppression des visiteurs absents du fichier CSV :',
			'etape4.4.2' => '<strong>Etape 4.4.2 :</strong> suppression des r�dacteurs absents du fichier CSV :',
			'etape4.4.3' => '<strong>Etape 4.4.3 :</strong> suppression des admins restreints absents du fichier CSV :',
			'suppr_redac' => 'suppressions de r�dacteurs en erreur :',
			'redac_poubelle' => 'passage � la poubelle de r�dacteurs en erreur :',
			'suppr_admin' => 'suppressions d\'admins restreints en erreur :',
			'admin_poubelle' => 'passage � la poubelle d\'admins restreints en erreur :',
			'err_eff_adm_accesgroupes' => 'suppression des groupes d\'acc�s en erreur : ',
			'err_eff_adm_rub' => 'suppression de l\'administration des rubriques en erreur : ',
			'archivage_debut' => 'Archivage = OK pour ',
			'archivage_fin' => ' articles dans la rubrique ',
			'suppression_debut' => 'Suppression de ',
			'suppression_fin' => 'articles = OK',
			'poubelle_debut' => 'Passage � la poubelle de ',
			'titre_etape5' => '<strong>Etape 5 :</strong> attribution des rubriques aux administrateurs :',
			'titre_etape6' => '<strong>Etape 6 :</strong> cr�ation des articles dans les rubriques : ',
			'ok_etape6_debut' => 'Cr�ation des articles pour ',
			'ok_etape6_fin' => ' rubriques = OK',
						
			'err_redac' => 'r�dacteurs en erreur : ',
			'err_admin' => 'administrateurs en erreur : ',
			'err_visit' => 'visiteurs en erreur : ',
			'err_admin_rubrique' => 'Administrateur(s) de rubrique en erreur : ',
			'err_article' => 'Articles en erreur : ',
			
			'rubrique' => 'rubrique = ',
			'groupe_' => 'groupe = ',
			'groupe' => 'groupe',
			'creation' => 'Cr�ation de ',
			'suppression' => 'Suppression de ',
			'passage_poubelle' => 'Passage � la poubelle de ',
			'comptes_redac_ok' => ' comptes de r�dacteurs = OK',
			'comptes_admin_ok' => ' comptes administrateurs de rubriques = OK',
			'comptes_visit_ok' => ' comptes de visiteurs = OK',
			'utilisateur' => ' utilisateur = ',
			'utilisateurs' => ' utilisateurs ',
			'redac' => ' r�dacteur ',
			'admin' => ' administrateur ',
			'visit' => ' visiteur ',
			'redacs' => ' r�dacteurs ',
			'admins' => ' administrateurs ',
			'visits' => ' visiteurs ',
			'erreur' => ' => erreur = ',
			'rubrique_' => ' rubrique = ',
			'oui' => 'oui',
			'non' => 'non',			
			
			
			'titre_form' => 'Choix du fichier CSV et des options de cr�ation des comptes et rubriques: ',
			'titre_choix_fichier' => 'Param�tres du fichier CSV : ',
			'nom_groupe_redac' => 'Nom du groupe des r�dacteurs : ',
			'nom_groupe_admin' => 'Nom du groupe des admins de rubriques : ',
			'nom_groupe_visit' => 'Nom du groupe des visiteurs : ',
			'help_nom_groupe_admin' => '(colonne "<strong>groupe</strong>" du fichier CSV, si ces champs sont vides, tous les utilisateurs seront simples r�dacteurs)',
			'choix_fichier' => 'Fichier CSV � importer : ',
			'options_maj' => 'Mise � jour des utilisateurs existant d�ja dans SPIP : ',
			'maj_utils' => 'Mettre � jour les utilisateurs existants :', 
			'maj_mdp' => 'Mise � jour des infos personnelles (mail, pseudo, pass): ',
			'maj_grpes' => 'R�initialisation des groupes d\'acc�s : ',
			'help_maj_grpes' => '(supprimer les utilisateurs existants de tous les groupes du plugin <strong>acces_groupe</strong>)',
			'maj_rub_adm' => 'R�initialisation des rubriques administr�es : ',
			'help_maj_rub_adm' => '(enlever les droits d\'adminstration sur toutes les rubriques des admins restreints)',
			'suppr_absents' => 'Suppression des absents:',
			'suppr_utilis' => 'Supprimer les utilisateurs absents du fichier CSV',
			'suppr_redac' => '<strong>r�dacteurs</strong> absents : ',
			'suppr_admin' => '<strong>administrateurs restreints</strong> absents : ',
			'suppr_visit' => '<strong>visiteurs</strong> absents : ',
			'help_suppr_redac' => '(pour IACA, cela correspond � une "bascule ann�e" des �l�ves/profs d\'un �tablissement)', // Pas de suppression automatique des administrateurs de rubriques !<br>
			'suprr_articles' => 'Supprimer les articles des auteurs effac�s :',
			'transfert_archive' => 'Transf�rer les articles supprim�s dans une rubrique d\'archives: ',
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
			'profs_admins' => '(cr�e une rubrique pour chaque <strong>ss_groupe</strong> d\'admins et affecte l\'utilisateur comme admin de celle-ci. 
										 		  IACA : les profs d\'une discipline seront administrateurs de leur rubrique)',
			'article_rubrique' => 'Cr�er un article dans chaque rubrique admin: ',
			'help_articles' => '(permet de rendre visibles ces rubriques dans la partie publique)',
			'choix_parent_rubriques' => 'Rubrique parent des rubriques � cr�er: ',
			'choix_rub_admin_defaut' => 'Rubrique par d�faut des admins restreints: ',
			'nom_rub_admin_defaut' => 'rubrique des admins CSV2SPIP',
			'choix_parent_rub_admin_defaut' => 'Rubrique parent de la rubrique par d�faut: ',
			'help_rub_admin_defaut' => '(pour tous les admins restreints qui n\'ont pas de sous-groupe d�fini ou si la g�n�ration des rubriques par sous-groupes n\'est pas activ�e.
																  <strong>Attention!</strong> si vous supprimez cette rubrique, <strong>tous</strong> les administrateurs restreints sans sous-groupe deviennent administrateurs de <strong>toutes</strong> les rubriques !)',
//																  afin que ces admins ne soient pas des admins g�n�raux, il faut leur attribuer une rubrique � administrer)',
			'acces_groupes' => 'Connexion avec le plugin acces_groupes',
			'option_acces_groupes' => 'Cr�er un groupe de contr�le d\'acc�s par sous-groupe : ',
			'help_acces_groupes' => '(permet de cr�er un groupe (nom = colonne "ss_groupe"), utilisable dans le plugin acces_groupes, pour chacun des sous-groupes)',
			'ss_groupes_redac' => 'sous-groupes de <strong>r�dacteurs</strong> : ',
			'ss_groupes_admin' => 'sous-groupes d\'<strong>administrateurs</strong> : ',
			'ss_groupes_visit' => 'sous-groupes de <strong>visiteurs</strong> : ',
			'abs_acces_groupes' => 'le plugin acces_groupes n\'est pas activ� sur ce spip : option non disponible',
			'ss_grpes_reinitialiser' => 'R�initialiser les sous-groupes (vider les utilisateurs) : ',
			'help_reinitialiser' => '(pour tous les sous-groupes d�ja pr�sents, supprime tous les membres existants)',  
			'lancer' => 'Lancer la moulinette',
			
			'titre_help' => 'Caract�ristiques du fichier .CSV � utiliser: '
			
	);
?>
