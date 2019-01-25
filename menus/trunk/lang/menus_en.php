<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de https://trad.spip.net/tradlang_module/menus?lang_cible=en
// ** ne pas modifier le fichier **

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// A
	'ajouter_lien_menu' => 'Add this menu',

	// C
	'configurer_entrees_masquees_explication' => 'Check the entries you want to <strong>hide</strong> during a menu creation.',
	'configurer_objets_explication' => 'Choose contents we should be able to link menus with',
	'configurer_objets_label' => 'Linked contents',
	'configurer_titre' => 'Configure ’Menus’ plugin’s entries',
	'confirmer_supprimer_entree' => 'Do you really want to delete this entry?',
	'confirmer_supprimer_menu' => 'Are you sure you want to delete this menu?',
	'confirmer_supprimer_sous_menu' => 'Are you sure you want to delete this sub-level menu?',

	// D
	'description_menu_accueil' => 'Link to website’s home page.',
	'description_menu_articles_rubrique' => 'Display a section’s articles list.',
	'description_menu_deconnecter' => 'If the visitor is logged in, will add a "log out" link.',
	'description_menu_espace_prive' => 'Link to log in the website if you aren’t already, and go to admin area if you’re authorized to proceed.',
	'description_menu_groupes_mots' => 'Automatically displays a menu listing the group’s keywords and linked articles. By default it will list keywords groups and keywords within. If a groupes_mots.html template exists, group is linked to, as same.',
	'description_menu_lien' => 'Adds a specific link, either internal (relative URL), or external (https://...).',
	'description_menu_mapage' => 'If visitors are logged in, adds a link to their author’s page.',
	'description_menu_mots' => 'Automatically shows a menu listing articles tagged with keyword.',
	'description_menu_objet' => 'Adds link to any SPIP object: article, section, ... By default, the entry will be named after the object’s title. The entry will only be visible if the object is published.',
	'description_menu_page_speciale' => 'Adds a link to a page template using <code>spip.php?page=name&param1=xx&param2=yyy...</code>-like URL. Such pages are often provided by plugins.',
	'description_menu_page_speciale_zajax' => 'Adds link to a specific block on a page accessible with a <code>spip.php?page=name&param1=xx&param2=yyy...</code>-like URL. This requires Z-powered templates and <a href="https://contrib.spip.net/MediaBox">Mediabox</a> plugin.',
	'description_menu_rubriques_articles' => 'Displays a sections list, optionally including sub-level sections and articles,  nested on several levels. By default, all sections would be displayed, starting from the website’s root and ordered by title (numeric then alphabetic order). Articles are always listed after sections.',
	'description_menu_rubriques_completes' => 'Displays a sections list and, if needed, sub-level sections on several levels. By default, will display all sections, starting from the website’s root, sorted by title (alphabetic and numeric order).',
	'description_menu_secteurlangue' => 'This entry is dedicated to websites that provides different languages per top-level section. It automatically displays a menu, listing all sub-sections of the Page’s matching top-level language section, and if needed, sub-sections on several levels. By default, will display all top-level sections, ordered by title (numeric first, then alphabetic order).',
	'description_menu_texte_libre' => 'Type in the text you’d like to appear, or any SPIP language tag (<:...:>)',

	// E
	'editer_menus_editer' => 'Edit this menu',
	'editer_menus_entrees_editer' => 'Edit this menu entry',
	'editer_menus_entrees_nouveau' => 'Create a new menu entry',
	'editer_menus_entrees_titre' => 'Website’s menu entries',
	'editer_menus_explication' => 'Create and configure your website’s menus.',
	'editer_menus_exporter' => 'Export this menu',
	'editer_menus_nouveau' => 'Create a new menu',
	'editer_menus_titre' => 'Website’s menus',
	'entree_afficher_articles' => 'Include articles in the menu? (type "oui"  to proceed)',
	'entree_afficher_item_suite' => 'Include articles in the menu? (type "oui" to proceed)',
	'entree_ancre' => 'Anchor',
	'entree_articles_max' => 'If so, show the articles only if the section contains a maximum of xx articles? (put the maximum number of articles, leave blank to display all articles)',
	'entree_articles_max_affiches' => 'Listed articles max. number (followed by a "... All articles" link to the parent section) - Leave blank to list all of them',
	'entree_aucun' => 'None',
	'entree_bloc' => 'Zpip block',
	'entree_choisir' => 'Choose the type of item you want to add:',
	'entree_classe_parent' => 'CSS class for the links to the parent elements. This class will be added to the li>a elements having a subsequent ul / li elements. For example, if you type "daddy", it lets you use the plugin "menu deroulant 2" to style the menu.',
	'entree_connexion_objet' => 'Requires to be logged in (type "session") or logged out (type "nosession") to see the object',
	'entree_contenu' => 'Content',
	'entree_css' => 'CSS classes for this (container) item',
	'entree_css_lien' => 'CSS classes for the link',
	'entree_id_groupe' => 'Keyword group ID',
	'entree_id_mot' => 'Keyword ID',
	'entree_id_objet' => 'Number',
	'entree_id_rubrique' => 'Number of the parent section',
	'entree_id_rubrique_ou_courante' => 'Parent or "current" section ID if the parent section is the current section in the context',
	'entree_id_rubriques_exclues' => 'Section IDs to be excluded, separated by commas',
	'entree_id_secteur_exclus' => 'Top-level section IDs to be excluded, separated by commas',
	'entree_infini' => 'To infinity',
	'entree_lien_direct_articles_uniques' => 'If yes, and if single articles are hidden, when a section contains only one single article, link to the article? (type "oui" to proceed)',
	'entree_mapage' => 'My page',
	'entree_masquer_articles_uniques' => 'If so and if a section contains only one single article, shall we hide it? (type "oui" to proceed)',
	'entree_niveau' => 'Sub-sections level',
	'entree_nombre_articles' => 'Maximum number of articles (0 by default)',
	'entree_page' => 'Name of the page',
	'entree_parametres' => 'List of parameters',
	'entree_rubriques_max_affichees' => 'Listed sections max. number (followed by a "... All sections" link to the parent section) - Leave blank to list all of them',
	'entree_sousrub_cond' => 'Only display the subsections for the current section (enter "oui" (yes), otherwise leave it empty)',
	'entree_suivant_connexion' => 'Restrict this entry according to the connection (put "connecte" to display it only if the visitor is connected, "deconnecte" in the opposite case, put "admin" if the author is administrator or leave blank to always display it)',
	'entree_suivant_connexion_connecte' => 'only if connected',
	'entree_suivant_connexion_deconnecte' => 'only if disconnected',
	'entree_sur_n_articles' => '@n@ article(s) shown',
	'entree_sur_n_mots' => '@n@ keyword(s) shown',
	'entree_sur_n_niveaux' => 'On @n@ level(s)',
	'entree_titre' => 'Title',
	'entree_titre_connecter' => 'The title for accessing the identification form',
	'entree_titre_prive' => 'The title for accessing the admin area',
	'entree_traduction_articles_rubriques' => 'If possible, show the section’s articles in context’s language (type "trad" to proceed)',
	'entree_traduction_objet' => 'Select the translation depending on the context (type "trad" to proceed)',
	'entree_tri' => 'Sections order ("titre" to sort by title, "num titre" to sort by title number. Prefix with an " !" to reverse order)',
	'entree_tri_articles' => 'Articles order ("titre" to sort by title, "num titre" to sort by title number. Prefix with a "!" to reverse order)',
	'entree_type_objet' => 'Object type',
	'entree_url' => 'URL',
	'entree_url_public' => 'Return address after login',
	'erreur_aucun_type' => 'No item type was found.',
	'erreur_autorisation' => 'You are not allowed to modify menus.',
	'erreur_identifiant_deja' => 'This identifier is already used by another menu.',
	'erreur_identifiant_forme' => 'Identifier must contain only letters, digits or underscores.',
	'erreur_menu_inexistant' => 'Menu ID @id@ doesn’t exist.',
	'erreur_mise_a_jour' => 'An error occured during database update.',
	'erreur_parametres' => 'There is an error in the page’s parameters',
	'erreur_type_menu' => 'You have to choose a menu type',
	'erreur_type_menu_inexistant' => 'This kind of menu is not / no longer available',

	// F
	'formulaire_ajouter_sous_menu' => 'Create a sub-level menu',
	'formulaire_css' => 'CSS classes',
	'formulaire_css_explication' => 'You can provide your menu with additional CSS classes.',
	'formulaire_deplacer_bas' => 'Move down',
	'formulaire_deplacer_haut' => 'Move up',
	'formulaire_facultatif' => 'Optional',
	'formulaire_identifiant' => 'Identifier',
	'formulaire_identifiant_explication' => 'Type in a unique keyword which will let you easily call your menu.',
	'formulaire_ieconfig_choisir_menus_a_importer' => 'Select which menu(s) you would like to import.',
	'formulaire_ieconfig_importer' => 'Import',
	'formulaire_ieconfig_menu_meme_identifiant' => 'WARNING: there’s already a menu by that same identifier on your website!',
	'formulaire_ieconfig_menus_a_exporter' => 'Menus to be exported:',
	'formulaire_ieconfig_ne_pas_importer' => 'Do not import',
	'formulaire_ieconfig_remplacer' => 'Overwrite the current menu with the imported menu',
	'formulaire_ieconfig_renommer' => 'Rename this menu before importing',
	'formulaire_importer' => 'Import menu',
	'formulaire_importer_explication' => 'If you exported a menu in a file, you can import now.',
	'formulaire_modifier_entree' => 'Modify this menu item',
	'formulaire_modifier_menu' => 'Modify menu:',
	'formulaire_nouveau' => 'New menu',
	'formulaire_partie_construction' => 'Menu construction',
	'formulaire_partie_identification' => 'Menu identification',
	'formulaire_supprimer_entree' => 'Delete this menu item',
	'formulaire_supprimer_menu' => 'Delete the menu',
	'formulaire_supprimer_sous_menu' => 'Delete this sub-menu',
	'formulaire_titre' => 'Title',

	// I
	'info_1_menu' => 'A menu',
	'info_1_menu_entree' => 'One menu entry',
	'info_afficher_articles' => 'The articles will be included in the menu.',
	'info_articles_max' => 'Only if the section contains more than @max@ articles',
	'info_articles_max_affiches' => 'Display limited to @max@ articles',
	'info_aucun_menu' => 'No menu',
	'info_aucun_menu_entree' => 'No menu entries',
	'info_classe_parent' => 'Parent elements’ class:',
	'info_connexion_obligatoire' => 'Connection required',
	'info_deconnexion_obligatoire' => 'Only when logged out',
	'info_masquer_articles_uniques' => 'Singles articles hidden',
	'info_nb_menus' => '@nb@ menus',
	'info_nb_menus_entrees' => '@nb@ menu entries',
	'info_numero_menu' => 'MENU ID:',
	'info_page_speciale' => 'Link to page « @page@ »',
	'info_page_speciale_zajax' => '"@page@" page’s modal box, for the "@bloc@" block',
	'info_rubrique_courante' => 'Current section',
	'info_rubriques_exclues' => ' / except section(s) @id_rubriques@',
	'info_rubriques_max_affichees' => 'Display limited to @max@ sections',
	'info_secteur_exclus' => ' / except top-level section(s) @id_secteur@',
	'info_sousrub_cond' => 'Only current section’s sub-sections are listed.',
	'info_tous_groupes_mots' => 'All keyword groups',
	'info_traduction_recuperee' => 'The context will set the chosen translation',
	'info_tri' => 'Sort sections:',
	'info_tri_alpha' => '(alphabetic order)',
	'info_tri_articles' => 'Sort articles:',
	'info_tri_num' => '(numeric order)',

	// N
	'noisette_description' => 'Insert a menu defined with the Menus plugin.',
	'noisette_label_afficher_titre_menu' => 'Display menu title?',
	'noisette_label_identifiant' => 'Menu to display:',
	'noisette_nom_noisette' => 'Menu',
	'nom_menu_accueil' => 'Home Page',
	'nom_menu_articles_rubrique' => 'Articles for a given section',
	'nom_menu_deconnecter' => 'Log out',
	'nom_menu_espace_prive' => 'Login / link to the admin area',
	'nom_menu_groupes_mots' => 'Articles and keywords for a given keywords group',
	'nom_menu_lien' => 'Simple hypertext link',
	'nom_menu_mapage' => 'My page',
	'nom_menu_mots' => 'Articles for a given keyword',
	'nom_menu_objet' => 'Article, section or other SPIP object',
	'nom_menu_page_speciale' => 'Link to a ’?page=’ template',
	'nom_menu_page_speciale_zajax' => 'A Zpip page’s block',
	'nom_menu_rubriques_completes' => 'Sections and/or articles tree display (with options)',
	'nom_menu_rubriques_evenements' => 'Section-related events',
	'nom_menu_secteurlangue' => 'Languages’ top-level sections',
	'nom_menu_texte_libre' => 'Free text',

	// R
	'retirer_lien_menu' => 'Remove this menu',
	'retirer_lien_objet' => 'Dissociate',
	'retirer_tous_liens_menus' => 'Remove all menus',

	// T
	'texte_ajouter_menu' => 'Add a menu',
	'texte_ajouter_menu_entree' => 'Add a menu entry',
	'texte_creer_associer_menu' => 'Create and associate a menu',
	'texte_creer_associer_menu_entree' => 'Create and associate a menu entry',
	'titre_menu' => 'Menu',
	'titre_menu_entrée' => 'Menu entry',
	'titre_objets_lies_menu' => 'Linked to this menu',
	'tous_les_articles' => '... All articles',
	'toutes_les_rubriques' => '... All sections',

	// U
	'utiles_explication' => 'The website’s current templates can use following menus.',
	'utiles_generer_menu' => 'Create <strong>@titre@ (<em>@identifiant@</em>)</strong> menu',
	'utiles_generer_menus' => 'Create <strong>all</strong> useful menus',
	'utiles_titre' => 'Useful menus'
);
