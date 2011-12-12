<?php
/* csv2spip est un plugin pour cr&eacute;er/modifier les visiteurs, r&eacute;dacteurs et administrateurs restreints d'un SPIP &agrave; partir de fichiers CSV
*	 					VERSION : 3.1 => plugin pour spip 2.*
*
* Auteur : cy_altern (cy_altern@yahoo.fr)
*  
* Ce programme est un logiciel libre distribue sous licence GNU/GPL.
*  
**/
  if (!defined("_ECRIRE_INC_VERSION")) return;

	$GLOBALS[$GLOBALS['idx_lang']] = array(
  
  
  // A
    'admin_poubelle' => 'passage &agrave; la poubelle d\'admins restreints en erreur :',  
    'archivage_debut' => 'Archivage = OK pour ',
    'archivage_fin' => ' articles dans la rubrique ',
    'admin' => ' administrateur ',
    'admins' => ' administrateurs ',
    'auteurs_poubelle' => 'mettre les auteurs "&agrave; la poubelle" sans les supprimer compl&egrave;tement',
    'attribuer_articles' => 'supprimer les auteurs et attribuer leurs articles &agrave; l\'auteur',
    'article_rubrique' => 'Cr&eacute;er un article dans chaque rubrique admin: ',
    'acces_groupes' => 'Connexion avec le plugin acces_groupes',
    'abs_acces_groupes' => 'le plugin acces_groupes n\'est pas activ&eacute; sur ce spip : option non disponible',
  
  // C
    'creation' => 'Cr&eacute;ation de ',
    'comptes_redac_ok' => ' comptes de r&eacute;dacteurs = OK',
    'comptes_admin_ok' => ' comptes administrateurs de rubriques = OK',
    'comptes_visit_ok' => ' comptes de visiteurs = OK',
    'choix_fichier' => 'Fichier CSV &agrave; importer : ',
    'choix_parent_archive' => 'Rubrique parent de la rubrique d\'archives :',
    'creation_rubriques' => 'Cr&eacute;ation de rubriques pour les sous-groupes administrateurs: ',
    'choix_parent_rubriques' => 'Rubrique parent des rubriques &agrave; cr&eacute;er: ',
    'choix_rub_admin_defaut' => 'Rubrique par d&eacute;faut des admins restreints: ',
    'choix_parent_rub_admin_defaut' => 'Rubrique parent de la rubrique par d&eacute;faut: ',
    
  // E
    'err_etape1.1_debut' => 'Etape 1.1 :</strong> le t&eacute;l&eacute;chargement du fichier ',
    'err_etape1.1_fin' => ' &agrave; &eacute;chou&eacute; : veuillez recommencer. Code d\'erreur :',
    'err_etape1.2_debut' => 'Etape 1.2 :</strong> la copie du fichier ',
    'err_etape1.2_fin' => ' &agrave; &eacute;chou&eacute;, veuillez recommencer. Fichier destination : ',
    'err_etape2.1' => '<strong>Etape 2.1 :</strong> erreur lors du vidage de la table temporaire "spip_tmp_csv2spip"',
    'err_etape2.2' => '<strong>Etape 2.2 :</strong> erreur(s) lors de l\'int&eacute;gration des utilisateurs dans la base temporaire "spip_tmp_csv2spip" : ',
    'err_etape2.1' => '<strong>Etape 2.1 :</strong> erreur(s) lors de la lecture de la premi&egrave;re ligne du fichier (r&eacute;f&eacute;rence des colonnes) : ',
    'err_etape3.1' => '<strong>Etape 3.1 :</strong> erreur(s) lors de la cr&eacute;ation des rubriques des sous-groupes d\'administrateurs : 
    						 		 <br>Il manque le(s) nom(s) de colonne(s) suivant(s) : ',    
    'err_cree_rub_defaut' => 'Erreur lors de la cr&eacute;ation de la rubrique par d&eacute;faut des admins restreints : ',
    'etape3.2' => '<strong>Etape 3.2 :</strong> cr&eacute;ation des groupes pour le plugin acces_groupes',
    'err_etape3.2' => '<strong>Etape 3.2 :</strong> erreur(s) lors de la cr&eacute;ation des groupes d\'acces : ',
    'err_integ_accesgroupes' => 'id_grpacces non trouv&eacute;e : ce groupe n\'existe pas dans la table _accesgroupes_groupes',
    'err_vider_accesgroupes' => 'Probl&egrave;me lors du vidage des auteurs du groupe d\'acc&egrave;s ',
    'etape4.1' => '<strong>Etape 4.1 : cr&eacute;ation des nouveaux utilisateurs</strong>',
    'etape4.2' => '<strong>Etape 4.2 : mise &agrave; jour des utilisateurs existants</strong>',
    'etape4.2.1' => '<strong>Etape 4.2.1 :</strong> mise &agrave; jour des informations personnelles : ',
    'etape4.2.2' => '<strong>Etape 4.2.2 :</strong> r&eacute;initialisation des groupes du plugin acces_groupes  pour les utilisateurs existants : ',
    'err_maj_grpacces' => 'utilisateurs en erreur : ',
    'etape4.2.3' => '<strong>Etape 4.2.3 :</strong> r&eacute;initialisation des rubriques administr&eacute;es par les admins existants : ',
    'err_maj_rub_adm' => 'utilisateurs en erreur : ',
    'etape4.3' => '<strong>Etape 4.3 : g&eacute;n&eacute;ration des groupes d\'acces_groupes &agrave; partir des sous-groupes</strong>',
    'etape4.3.1' => '<strong>Etape 4.3.1 :</strong> int&eacute;gration des visiteurs dans leur sous-groupe acces_groupes :',
    'etape4.3.2' => '<strong>Etape 4.3.2 :</strong> int&eacute;gration des r&eacute;dacteurs dans leur sous-groupe acces_groupes :',
    'etape4.3.3' => '<strong>Etape 4.3.3 :</strong> int&eacute;gration des admins dans leur sous-groupe acces_groupes :',
    'etape4.4' => '<strong>Etape 4.4 : suppression des utilisateurs absents du fichier CSV</strong>',
    'etape4.4.1' => '<strong>Etape 4.4.1 :</strong> suppression des visiteurs absents du fichier CSV :',
    'etape4.4.2' => '<strong>Etape 4.4.2 :</strong> suppression des r&eacute;dacteurs absents du fichier CSV :',
    'etape4.4.3' => '<strong>Etape 4.4.3 :</strong> suppression des admins restreints absents du fichier CSV :',
    'err_eff_adm_accesgroupes' => 'suppression des groupes d\'acc&egrave;s en erreur : ',
    'err_eff_adm_rub' => 'suppression de l\'administration des rubriques en erreur : ',
    'err_redac' => 'r&eacute;dacteurs en erreur : ',
    'err_admin' => 'administrateurs en erreur : ',
    'err_visit' => 'visiteurs en erreur : ',
    'err_admin_rubrique' => 'Administrateur(s) de rubrique en erreur : ',
    'err_article' => 'Articles en erreur : ',
    'erreur' => ' => erreur = ',
    
  // G
    'grpe_csv2spip' => 'groupe cr&eacute;&eacute, par csv2spip',
    'groupe_' => 'groupe = ',
    'groupe' => 'groupe',
    
  // H
    'help_info' => 'Cette page permet de cr&eacute;er et g&eacute;rer les auteurs et administrateurs restreint d\'un spip &agrave; partir de fichiers CSV',
    'help_nom_groupe_admin' => '(colonne "<strong>groupe</strong>" du fichier CSV, si ces champs sont vides, tous les utilisateurs seront simples r&eacute;dacteurs)',
    'help_maj_grpes' => '(supprimer les utilisateurs existants de tous les groupes du plugin <strong>acces_groupe</strong>)',
    'help_maj_rub_adm' => '(enlever les droits d\'adminstration sur toutes les rubriques des admins restreints)',
    'help_suppr_redac' => '(pour IACA, cela correspond &agrave; une "bascule ann&eacute;e" des &eacute;l&egrave;ves/profs d\'un &eacute;tablissement)', // Pas de suppression automatique des administrateurs de rubriques !<br>
    'help_articles' => '(permet de rendre visibles ces rubriques dans la partie publique)',
    'help_rub_admin_defaut' => '(pour tous les admins restreints qui n\'ont pas de sous-groupe d&eacute;fini ou si la g&eacute;n&eacute;ration des rubriques par sous-groupes n\'est pas activ&eacute;e.
    												  <strong>Attention!</strong> si vous supprimez cette rubrique, <strong>tous</strong> les administrateurs restreints sans sous-groupe deviennent administrateurs de <strong>toutes</strong> les rubriques !)',
    //																  afin que ces admins ne soient pas des admins g&eacute;n&eacute;raux, il faut leur attribuer une rubrique &agrave; administrer)',
    'help_acces_groupes' => '(permet de cr&eacute;er un groupe (nom = colonne "ss_groupe"), utilisable dans le plugin acces_groupes, pour chacun des sous-groupes)',
    'help_reinitialiser' => '(pour tous les sous-groupes d&eacute;ja pr&eacute;sents, supprime tous les membres existants)',  
    
  // L
    'lancer' => 'Lancer la moulinette',
  
	// M
    'module_titre' => 'csv2spip',
    'maj_utils' => 'Mettre &agrave; jour les utilisateurs existants :', 
    'maj_mdp' => 'Mise &agrave; jour des infos personnelles (mail, pseudo, pass): ',
    'maj_grpes' => 'R&eacute;initialisation des groupes d\'acc&egrave;s : ',
    'maj_rub_adm' => 'R&eacute;initialisation des rubriques administr&eacute;es : ',
    
  // N
    'non' => 'non',  
    'nom_groupe_redac' => 'Nom du groupe des r&eacute;dacteurs : ',
    'nom_groupe_admin' => 'Nom du groupe des admins de rubriques : ',
    'nom_groupe_visit' => 'Nom du groupe des visiteurs : ',
    'nom_rubrique_archives' => 'Nom de la rubrique d\'archives : ',
    'nom_rub_admin_defaut' => 'rubrique des admins CSV2SPIP',
    
  // O
    'ok_etape1' => '<strong>Etape 1 :</strong> t&eacute;l&eacute;chargement r&eacute;ussi du fichier ',
    'ok_etape2.1' => '<strong>Etape 2.1 :</strong> remise &agrave; zero de la table temporaire "spip_tmp_csv2spip" = OK',
    'ok_etape2.2' => '<strong>Etape 2.2 :</strong> int&eacute;gration des auteurs dans la table temporaire = OK ',    
    'ok_etape3.1_debut' => '<strong>Etape 3.1 :</strong> cr&eacute;ation des rubriques admins = OK ',
    'ok_etape3.1_fin' => ' rubriques cr&eacute;&eacute;es',
    'ok_cree_rub_defaut' => 'Cr&eacute;ation de la rubrique par d&eacute;faut : ',
    'ok_etape3.2_debut' => '<strong>Etape 3.2 :</strong> cr&eacute;ation des groupes pour le plugin acces_groupes = OK : ',		
    'ok_etape3.2_fin' => ' groupes cr&eacute;&eacute;s',
    'ok_vider_accesgroupes' => 'Remise &agrave; z&eacute;ro des utilisateurs des groupes d\'acc&egrave;s = OK pour ',
    'ok_etape4.2.1' => 'mise &agrave; jour pour ',
    'ok_maj_grpacces' => 'r&eacute;initialisation = OK : ',
    'ok_maj_rub_adm' => 'r&eacute;initialisation = OK : ',
    'ok_etape6_debut' => 'Cr&eacute;ation des articles pour ',
    'ok_etape6_fin' => ' rubriques = OK',
    'oui' => 'oui',
    'options_maj' => 'Mise &agrave; jour des utilisateurs existant d&eacute;ja dans SPIP : ',
    'option_acces_groupes' => 'Cr&eacute;er un groupe de contr&ocirc;le d\'acc&egrave;s par sous-groupe : ',
    
  // P
    'poubelle_debut' => 'Passage &agrave; la poubelle de ',
    'passage_poubelle' => 'Passage &agrave; la poubelle de ',
    'pas_de_rubriques' => 'Il n\'existe pas encore de rubriques dans votre SPIP : toutes les rubriques cr&eacute;&eacute;es seront &agrave; la racine du site.',
    'passe_egale_login' => '(mot de passe idem login)',
    'profs_admins' => '(cr&eacute;e une rubrique pour chaque <strong>ss_groupe</strong> d\'admins et affecte l\'utilisateur comme admin de celle-ci.<br />
    						 		  IACA : les profs d\'une discipline seront administrateurs de leur rubrique)',
    
  // R
    'retour_saisie' => 'retour saisie d\'un fichier',
    'resultat_fichier' => 'Resultats de l\'int&eacute;gration du fichier ',
    'redac_poubelle' => 'passage &agrave; la poubelle de r&eacute;dacteurs en erreur :',
    'rubrique' => 'rubrique = ',
    'redac' => ' r&eacute;dacteur ',
    'redacs' => ' r&eacute;dacteurs ',
    'rubrique_' => ' rubrique = ',
    'racine_site' => 'racine du site',
    'rubrique_ss_groupes' => 'Cr&eacute;er une rubrique par sous-groupe d\'admins: ',
    
  // S
    'suppr_redac' => 'suppressions de r&eacute;dacteurs en erreur :',    
    'suppr_admin' => 'suppressions d\'admins restreints en erreur :',
    'suppression_debut' => 'Suppression de ',
    'suppression_fin' => 'articles = OK',    
    'suppression' => 'Suppression de ',
    'suppr_absents' => 'Suppression des absents:',
    'suppr_utilis' => 'Supprimer les utilisateurs absents du fichier CSV',
    'suppr_redac' => '<strong>r&eacute;dacteurs</strong> absents : ',
    'suppr_admin' => '<strong>administrateurs restreints</strong> absents : ',
    'suppr_visit' => '<strong>visiteurs</strong> absents : ',
    'suprr_articles' => 'Supprimer les articles des auteurs effac&eacute;s :',
    'ss_groupes_redac' => 'sous-groupes de <strong>r&eacute;dacteurs</strong> : ',
    'ss_groupes_admin' => 'sous-groupes d\'<strong>administrateurs</strong> : ',
    'ss_groupes_visit' => 'sous-groupes de <strong>visiteurs</strong> : ',
    'ss_grpes_reinitialiser' => 'R&eacute;initialiser les sous-groupes (vider les utilisateurs) : ',
    
  // T
    'titre_page' => 'CSV2SPIP : gestion des utilisateurs de SPIP &agrave; partir de fichiers CSV',
    'titre_info' => 'plugin CSV2SPIP',
    'titre_etape1' => 'Etape 1 :</strong> t&eacute;l&eacute;chargement du fichier sur le serveur',
    'titre_etape2' => '<strong>Etape 2 :</strong> passage des donn&eacute;es du fichier dans la table temporaire',
    'titre_etape3' => '<strong>Etape 3 :</strong> gestion des sous-groupes (rubriques et acces_groupes)',
    'titre_etape4' => '<strong>Etape 4 :</strong> traitement des utilisateurs',
    'titre_etape5' => '<strong>Etape 5 :</strong> attribution des rubriques aux administrateurs :',
    'titre_etape6' => '<strong>Etape 6 :</strong> cr&eacute;ation des articles dans les rubriques : ',
    'titre_form' => 'Choix du fichier CSV et des options de cr&eacute;ation des comptes et rubriques: ',
    'titre_choix_fichier' => 'Param&egrave;tres du fichier CSV : ',
    'transfert_archive' => 'Transf&eacute;rer les articles des auteurs supprim&eacute;s dans une rubrique d\'archives: ',
    'traitement_supprimes' => 'Traitement des comptes supprim&eacute;s pour les auteurs d\'articles :',
    'titre_help' => 'Caract&eacute;ristiques du fichier .CSV &agrave; utiliser: ',
    
  // U
    'utilisateur' => ' utilisateur = ',
    'utilisateurs' => ' utilisateurs ',

  // V
    'version' => 'Version : ',    
    'visit' => ' visiteur ',    
    'visits' => ' visiteurs '
    			
	);
?>
