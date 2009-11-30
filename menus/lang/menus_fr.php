<?php

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(
'editer_menus_titre' => 'Menus du site',
'editer_menus_explication' => 'Cr&eacute;ez et configurez ici les menus de votre site.',
'editer_menus_nouveau' => 'Cr&eacute;er un nouveau menu',
'editer_menus_editer' => '&Eacute;diter ce menu',
'editer_menus_exporter' => 'Exporter ce menu',

'erreur_autorisation' => 'Vous n\'&ecirc;tes pas autoris&eacute; &agrave; modifier les menus.',
'erreur_identifiant_deja' => 'Cet identifiant est d&eacute;j&agrave; utilis&eacute; par un menu.',
'erreur_identifiant_forme' => 'L\'identifiant ne doit contenir que des lettres, des chiffres ou le caract&egrave;re soulign&eacute;.',
'erreur_menu_inexistant' => 'Le menu demand&eacute; num&eacute;ro @id@ n\'existe pas.',
'erreur_mise_a_jour' => 'Une erreur s\'est produite pendant la mise &agrave; jour de la base de donn&eacute;e.',
'erreur_aucun_type' => 'Aucun type d\'entr&eacute;e n\'a &eacute;t&eacute; trouv&eacute;.',

'formulaire_nouveau' => 'Nouveau menu',
'formulaire_modifier_menu' => 'Modifier le menu :',
'formulaire_partie_identification' => 'Identification du menu',
'formulaire_partie_construction' => 'Construction du menu',
'formulaire_titre' => 'Titre',
'formulaire_identifiant' => 'Identifiant',
'formulaire_identifiant_explication' => 'Donnez un mot-cl&eacute; unique qui vous permettra d\'appeler votre menu facilement.',
'formulaire_css' => 'Classes CSS',
'formulaire_css_explication' => 'Vous pouvez ajouter au menu d\'&eacute;ventuelles classes CSS suppl&eacute;mentaires.',
'formulaire_importer' => 'Importer un menu',
'formulaire_importer_explication' => 'Si vous avez export&eacute; un menu dans un fichier, vous pouvez l\'importer maintenant.',
'formulaire_ajouter_entree' => 'Ajouter une entr&eacute;e',
'formulaire_modifier_entree' => 'Modifier cette entr&eacute;e',
'formulaire_supprimer_entree' => 'Supprimer cette entr&eacute;e',
'formulaire_supprimer_menu' => 'Supprimer le menu',
'formulaire_supprimer_sous_menu' => 'Supprimer ce sous-menu',
'formulaire_deplacer_haut' => 'D&eacute;placer vers le haut',
'formulaire_deplacer_bas' => 'D&eacute;placer vers le bas',
'formulaire_ajouter_sous_menu' => 'Cr&eacute;er un sous-menu',
'formulaire_attacher_sous_menu' => 'Attacher un sous-menu &agrave; cette entr&eacute;e',
'formulaire_facultatif' => 'Facultatif',

'entree_choisir' => 'Choisissez le type d\'entr&eacute;e que vous voulez ajouter :',
'entree_css' => 'Classes CSS de l\'entr&eacute;e',
'entree_titre' => 'Titre',
'entree_url' => 'Adresse',
'entree_type_objet' => 'Type de l\'objet',
'entree_id_objet' => 'Num&eacute;ro',
'entree_id_rubrique' => 'Num&eacute;ro de la rubrique parente',
'entree_niveau' => 'Niveau des sous-rubriques',
'entree_sur_n_niveaux' => 'Sur @n@ niveau(x)',
'entree_infini' => '&Agrave l\'infini',
'entree_mapage' => 'Ma page perso',
'entree_tri_num' => 'Crit&egrave;re de tri (num&eacute;rique)',
'entree_tri_alpha' => 'Crit&egrave;re de tri (alphab&eacute;tique)',

'info_numero_menu' => 'MENU NUM&Eacute;RO :',
'info_tri' => 'Tri :',
'info_tri_alpha' => '(alphab&eacute;tique)',
'info_tri_num' => '(num&eacute;rique)',

'nom_menu_accueil' => 'Accueil',
'nom_menu_deconnecter' => 'Se d&eacute;connecter',
'nom_menu_lien' => 'Lien arbitraire',
'nom_menu_mapage' => 'Ma page',
'nom_menu_objet' => 'Objet de SPIP',
'nom_menu_rubriques' => 'Rubriques dynamiques',
'nom_menu_secteurlangue' => 'Secteurs de langue',

'description_menu_accueil' => 'Lien vers la page d\'accueil du site.',
'description_menu_deconnecter' => 'Si le visiteur est connect&eacute;, ajoute une entr&eacute;e lui proposant la d&eacute;connexion.',
'description_menu_lien' => 'Ajoute un lien arbitraire, en interne (URL relative) ou externe (http://...).',
'description_menu_mapage' => 'Si le visiteur est connect&eacute;, ajoute un lien vers sa page auteur.',
'description_menu_objet' => 'Cr&eacute;e un lien vers un objet de SPIP : article, rubrique ou autre. Par d&eacute;faut, l\'entr&eacute;e aura le titre de l\'objet.',
'description_menu_rubriques' => 'Affiche automatiquement un menu listant les rubriques et, si on veut, les sous-rubriques sur plusieurs niveaux. Par d&eacute;faut, affiche toutes les rubriques depuis la racine, tri&eacute;es par titre (num&eacute;riquement puis alphab&eacute;tiquement).',
'description_menu_secteurlangue' => 'Cette entr&eacute;e est sp&eacute;cifique aux sites utilisant un secteur par langue. Elle affiche automatiquement un menu listant les rubriques du secteur correspondant &agrave; la langue de la page et, si on veut, les sous-rubriques sur plusieurs niveaux. Par d&eacute;faut, affiche toutes les rubriques depuis la racine, tri&eacute;es par titre (num&eacute;riquement puis alphab&eacute;tiquement).',
);

?>
