<?php

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(
'editer_menus_titre' => 'Site menus',
'editer_menus_explication' => 'Create and configure menus for your site.',
'editer_menus_nouveau' => 'Create a new menu',
'editer_menus_editer' => 'Edit this menu',
'editer_menus_exporter' => 'Export this menu',

'erreur_autorisation' => 'You are not allowed to modify menus.',
'erreur_identifiant_deja' => 'This identifier is already used by another menu.',
'erreur_identifiant_forme' => 'Identifier must contain only letters, digits or underscores.',
'erreur_menu_inexistant' => 'Menu number @id@ doesn\'t exist.',
'erreur_mise_a_jour' => 'An error occured during database update.',
'erreur_aucun_type' => 'No item type was found.',

'formulaire_nouveau' => 'New menu',
'formulaire_modifier_menu' => 'Modify menu:',
'formulaire_partie_identification' => 'Menu identification',
'formulaire_partie_construction' => 'Menu construction',
'formulaire_titre' => 'Title',
'formulaire_identifiant' => 'Identifier',
'formulaire_identifiant_explication' => 'Give a unique keyword which let you call your menu easly.',
'formulaire_css' => 'CSS classes',
'formulaire_css_explication' => 'You can add to your menu additional CSS classes.',
'formulaire_importer' => 'Import menu',
'formulaire_importer_explication' => 'If you exported a menu in a file, you can import now.',
'formulaire_ajouter_entree' => 'Add a menu item',
'formulaire_modifier_entree' => 'Modify this menu item',
'formulaire_supprimer_entree' => 'Delete this menu item',
'formulaire_supprimer_menu' => 'Delete the menu',
'formulaire_supprimer_sous_menu' => 'Delete this sub-menu',
'formulaire_deplacer_haut' => 'Move up',
'formulaire_deplacer_bas' => 'Move down',
'formulaire_ajouter_sous_menu' => 'Create a sub-menu',
'formulaire_attacher_sous_menu' => 'Attach a sub-menu to this menu item',
'formulaire_facultatif' => 'Optional',

'entree_choisir' => 'Choose the type of item you want to add:',
'entree_css' => 'CSS classes of this item',
'entree_titre' => 'Title',
'entree_url' => 'URL',
'entree_type_objet' => 'Object type',
'entree_id_objet' => 'Number',
'entree_id_rubrique' => 'Number of the parent section',
'entree_niveau' => 'Sub-sections level',
'entree_sur_n_niveaux' => 'On @n@ level(s)',
'entree_infini' => 'To infinity',
'entree_mapage' => 'My page',

'info_numero_menu' => 'MENU NUMBER:',

'nom_menu_accueil' => 'Home Page',

'description_menu_accueil' => 'Link to website\'s home page.',
);

?>
