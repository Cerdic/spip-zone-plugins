<?php

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(
'editer_menus_titre' => 'Menus du site',
'editer_menus_explication' => 'Cr&eacute;ez et configurez ici les menus de votre site.',
'editer_menus_nouveau' => 'Cr&eacute;er un nouveau menu',
'editer_menus_editer' => '&Eacute;diter ce menu',

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
'formulaire_ajouter_entree' => 'Ajouter une entr&eacute;e',
'formulaire_modifier_entree' => 'Modifier cette entr&eacute;e',
'formulaire_supprimer_entree' => 'Supprimer cette entr&eacute;e',
'formulaire_supprimer_sous_menu' => 'Supprimer ce sous-menu',
'formulaire_deplacer_haut' => 'D&eacute;placer vers le haut',
'formulaire_deplacer_bas' => 'D&eacute;placer vers le bas',
'formulaire_ajouter_sous_menu' => 'Cr&eacute;er un sous-menu',
'formulaire_attacher_sous_menu' => 'Attacher un sous-menu &agrave; cette entr&eacute;e',
'formulaire_facultatif' => 'Facultatif',

'entree_choisir' => 'Choisissez le type d\'entr&eacute;e que vous voulez ajouter :',
'entree_css' => 'Classe CSS de l\'entr&eacute;e',
'entree_titre' => 'Titre',
'entree_url' => 'Adresse',
'entree_type_objet' => 'Type de l\'objet',
'entree_id_objet' => 'Num&eacute;ro',
'entree_id_rubrique' => 'Num&eacute;ro de la rubrique parente',
'entree_niveau' => 'Niveau des sous-rubriques',
'entree_sur_n_niveaux' => 'Sur @n@ niveau(x)',
'entree_infini' => '&Agrave l\'infini'
);

?>
