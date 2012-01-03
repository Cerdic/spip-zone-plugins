<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de http://trad.spip.org
// ** ne pas modifier le fichier **

if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// C
	'confirmer_supprimer_entree' => 'Do you really want delete this entry?',

	// D
	'description_menu_accueil' => 'Link to website\'s home page.',
	'description_menu_articles_rubrique' => 'Display the list of articles in a section.',
	'description_menu_deconnecter' => 'If the visitor is connected, add an entry offering disconnection.',
	'description_menu_espace_prive' => 'Link enabling the connection to the site if you aren\'t already connected, and then to enter the private space if you are authorised to do so.',
	'description_menu_groupes_mots' => 'Automatically lists the keyword groups and the articles linked to them. By default the list shows keyword groups and the keywords within them. If a groupes_mots.html template exists, the link to the group is used.', # MODIF
	'description_menu_lien' => 'Adds an individually specified link, either an internal one (relative URL), or an external one (http://...).',
	'description_menu_mapage' => 'If visitors are connected, add a link to their author page.',
	'description_menu_mots' => 'Automatically shows a menu listing the articles linked to a keyword.',
	'description_menu_objet' => 'Creates a link to s SPIP object: article, section or other. By default, the entry will bear the the title of the object.',
	'description_menu_page_speciale' => 'Adds a link to a page template using a URL of the form <code>spip.php?page=name&param1=xx&param2=yyy...</code> Such pages are often used by plugins.',
	'description_menu_page_speciale_zajax' => 'Add a link to a block in a page accessible by a URL of the type <code>spip.php?page=name&param1=xx&param2=yyy...</code> This requires a Z type template and the <a href="http://www.spip-contrib.net/MediaBox">médiabox</a> plugin.', # MODIF
	'description_menu_rubriques' => 'Displays a list of sections and, if desired, the subsections to several levels. By default, all sections are shown from the site root, sorted by title (numerically then alphabetically).',
	'description_menu_rubriques_articles' => 'Display a list of sections, optionally including sub-sections and articles nested to several levels. By default, all sections will be displayed starting from the site root and sorted by title (numerically then alphabetically).Articles in a given section will always be listed after any sub-sections.',
	'description_menu_secteurlangue' => 'This entry can be used by sites which have one language per sector. It displays a menu which lists the sections of the sector corresponding to the language of the page, and if desired the subsections to several levels. By default, all sections are shown from the site root, sorted by title (numerically then alphabetically).',
	'description_menu_texte_libre' => 'Just the text that you would like, or a SPIP language code (<:...:>)',

	// E
	'editer_menus_editer' => 'Edit this menu',
	'editer_menus_explication' => 'Create and configure menus for your site.',
	'editer_menus_exporter' => 'Export this menu',
	'editer_menus_nouveau' => 'Create a new menu',
	'editer_menus_titre' => 'Site menus',
	'entree_afficher_articles' => 'Include articles in the menu? (put "oui" for this)',
	'entree_afficher_item_suite' => 'Include articles in the menu? (put "oui" for this)',
	'entree_articles_max' => 'Si oui, afficher les articles seulement si la rubrique contient au maximum xx articles ? (mettre le nombre maximum d\'articles, laissez vide pour afficher tous les articles)', # NEW
	'entree_articles_max_affiches' => 'Si oui, limiter le nombre d\'articles listés à xx maximum (suivis d\'un item "... Tous les articles" comportant un lien vers la rubrique parente) ? (indiquer le nombre maximum d\'articles, laissez vide pour afficher tous les articles)', # NEW
	'entree_aucun' => 'None',
	'entree_bloc' => 'Zpip block',
	'entree_choisir' => 'Choose the type of item you want to add:',
	'entree_classe_parent' => 'Classe des liens des éléments parents. Cette classe sera rajoutée aux li>a ayant une suite ul/li. Par exemple, si vous saisissez "daddy", cela vous permet d\'utiliser le plugin menu deroulant 2 pour la mise en forme du menu.', # NEW
	'entree_connexion_objet' => 'Requires being connected (insert "session") or disconnected (insert "nosession") in order to see the object',
	'entree_contenu' => 'Content',
	'entree_css' => 'CSS classes of this (container) item',
	'entree_css_lien' => 'CSS classes of the link',
	'entree_id_groupe' => 'Number of the keyword group',
	'entree_id_mot' => 'Number of the keyword',
	'entree_id_objet' => 'Number',
	'entree_id_rubrique' => 'Number of the parent section',
	'entree_id_rubrique_ou_courante' => 'Numéro de la rubrique parente ou "courante" si la rubrique parente est la rubrique courante du contexte', # NEW
	'entree_id_rubriques_exclues' => 'Numéros des rubriques à exclure, séparés par des virgules', # NEW
	'entree_id_secteur_exclus' => 'Numéros des secteurs à exclure, séparés par des virgules', # NEW
	'entree_infini' => 'To infinity',
	'entree_mapage' => 'My page',
	'entree_masquer_articles_uniques' => 'Si oui et si une rubrique contient un seul article, le masquer ? (mettre "oui" pour cela)', # NEW
	'entree_niveau' => 'Sub-sections level',
	'entree_nombre_articles' => 'Maximum number of articles (0 by default)', # MODIF
	'entree_page' => 'Name of the page',
	'entree_parametres' => 'List of parameters',
	'entree_rubriques_max_affichees' => 'Si oui, limiter le nombre de rubriques listés à xx maximum (suivis d\'un item "... Toutes les rubriques" comportant un lien vers la rubrique parente) ? (indiquer le nombre maximum de rubriques, laissez vide pour afficher toutes les rubriques)', # NEW
	'entree_sousrub_cond' => 'Only display the subsections for the current section (enter "oui" (yes), otherwise leave it empty)',
	'entree_sur_n_articles' => '@n@ article(s) shown',
	'entree_sur_n_mots' => '@n@ keyword(s) shown',
	'entree_sur_n_niveaux' => 'On @n@ level(s)',
	'entree_titre' => 'Title',
	'entree_titre_connecter' => 'The title for accessing the identification form',
	'entree_titre_prive' => 'The title for accessing the private zone',
	'entree_traduction_articles_rubriques' => 'Dans la mesure du possible, afficher les articles de la rubrique dans la langue du contexte (mettre "trad" pour cela)', # NEW
	'entree_traduction_objet' => 'For an article, select the translation depending on the context (insert "trad" to accomplish this)',
	'entree_tri_alpha' => 'Sort criterion (alphabetic)', # MODIF
	'entree_tri_alpha_articles' => 'Critère de tri des articles (alphabétique). Si vous saisissez "date", le critère ajouté sera {par date} et les articles seront triés par date', # NEW
	'entree_tri_alpha_articles_inverse' => 'Inverser le critère de tri alphabétique ? (mettre "oui" pour cela)', # NEW
	'entree_tri_alpha_inverse' => 'Reverse the alphabetical sorting? (put "oui" for this)',
	'entree_tri_num' => 'Sort criterion (numeric)', # MODIF
	'entree_tri_num_articles' => 'Critère de tri des articles (numérique). Si vous saisissez "titre", le critère ajouté sera {par num titre} et les articles seront triés par numéro de titre', # NEW
	'entree_tri_num_articles_inverse' => 'Inverser le critère de tri numérique ? (mettre "oui" pour cela)', # NEW
	'entree_tri_num_inverse' => 'Reverse the digital sort order? (put "oui" for this)',
	'entree_type_objet' => 'Object type',
	'entree_url' => 'URL',
	'entree_url_public' => 'Return address after logging in',
	'erreur_aucun_type' => 'No item type was found.',
	'erreur_autorisation' => 'You are not allowed to modify menus.',
	'erreur_identifiant_deja' => 'This identifier is already used by another menu.',
	'erreur_identifiant_forme' => 'Identifier must contain only letters, digits or underscores.',
	'erreur_menu_inexistant' => 'Menu number @id@ doesn\'t exist.',
	'erreur_mise_a_jour' => 'An error occured during database update.',
	'erreur_parametres' => 'There is an error in the parameters of the page',
	'erreur_type_menu' => 'You need to choose a type of menu',
	'erreur_type_menu_inexistant' => 'This kind of menu is not / no longer available',

	// F
	'formulaire_ajouter_entree' => 'Add a menu item',
	'formulaire_ajouter_sous_menu' => 'Create a sub-menu',
	'formulaire_css' => 'CSS classes',
	'formulaire_css_explication' => 'You can add to your menu additional CSS classes.',
	'formulaire_deplacer_bas' => 'Move down',
	'formulaire_deplacer_haut' => 'Move up',
	'formulaire_facultatif' => 'Optional',
	'formulaire_identifiant' => 'Identifier',
	'formulaire_identifiant_explication' => 'Give a unique keyword which let you call your menu easly.',
	'formulaire_ieconfig_choisir_menus_a_importer' => 'Select which menu(s) you would like to import.',
	'formulaire_ieconfig_importer' => 'Import',
	'formulaire_ieconfig_menu_meme_identifiant' => 'WARNING: there is already a menu with the same name on your site!',
	'formulaire_ieconfig_menus_a_exporter' => 'Menus to export:',
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
	'info_afficher_articles' => 'The articles will be included in the menu.',
	'info_articles_max' => 'Only if the section contains more than @max@ articles',
	'info_articles_max_affiches' => 'Display limited to @max@ articles',
	'info_classe_parent' => 'Class of the parent elements:',
	'info_connexion_obligatoire' => 'Connection required',
	'info_deconnexion_obligatoire' => 'Only when disconnected',
	'info_masquer_articles_uniques' => 'Unique articles hidden',
	'info_numero_menu' => 'MENU NUMBER:',
	'info_page_speciale' => 'Link to the page « @page@ »',
	'info_page_speciale_zajax' => 'Modalbox for the "@page@" page for the "@bloc@" block',
	'info_rubrique_courante' => 'Current section',
	'info_rubriques_exclues' => ' / except section(s) @id_rubriques@',
	'info_rubriques_max_affichees' => 'Display limited to @max@ sections',
	'info_secteur_exclus' => ' / except sector(s) @id_secteur@',
	'info_sousrub_cond' => 'Only the subsections of the current section are displayed.',
	'info_tous_groupes_mots' => 'All keyword groups',
	'info_traduction_recuperee' => 'The context will determine the selected translation',
	'info_tri' => 'Sort sections:',
	'info_tri_alpha' => '(alphabetical)',
	'info_tri_articles' => 'Sort articles:',
	'info_tri_num' => '(numerical)',

	// N
	'noisette_description' => 'Insert a menu defined with the Menus plugin.',
	'noisette_label_afficher_titre_menu' => 'Show the menu title?',
	'noisette_label_identifiant' => 'Menu to display:',
	'noisette_nom_noisette' => 'Menu',
	'nom_menu_accueil' => 'Home Page',
	'nom_menu_articles_rubrique' => 'Articles of a section',
	'nom_menu_deconnecter' => 'Disconnect',
	'nom_menu_espace_prive' => 'Login / link to the private zone',
	'nom_menu_groupes_mots' => 'Keywords and Articles of a group of keywords',
	'nom_menu_lien' => 'Individual link',
	'nom_menu_mapage' => 'My page',
	'nom_menu_mots' => 'Articles of a keyword',
	'nom_menu_objet' => 'Article, section or other SPIP object',
	'nom_menu_page_speciale' => 'Link to a page template',
	'nom_menu_page_speciale_zajax' => 'A block in a Zpip page',
	'nom_menu_rubriques' => 'List or tree of sections and articles',
	'nom_menu_rubriques_evenements' => 'Section-related events',
	'nom_menu_secteurlangue' => 'Language sectors',
	'nom_menu_texte_libre' => 'Free text',

	// T
	'tous_les_articles' => '... All articles',
	'toutes_les_rubriques' => '... All sections'
);

?>
