<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de http://www.spip.net/trad-lang/
// ** ne pas modifier le fichier **

if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// D
	'description_menu_accueil' => 'Link to website\'s home page.',
	'description_menu_articles_rubrique' => 'Display the list of articles in a section.',
	'description_menu_deconnecter' => 'If the visitor is connected, add an entry offering disconnection.',
	'description_menu_espace_prive' => 'Link enabling the connection to the site if you aren\'t already connected, and then to enter the private space if you are authorised to do so.',
	'description_menu_groupes_mots' => 'Automatically lists the keyword groups and the articles linked to them. By default the list shows keyword groups and the keywords within them. If a groupes_mots.html template exists, the link to the group is used.',
	'description_menu_lien' => 'Adds an individually specified link, either an internal one (relative URL), or an external one (http://...).',
	'description_menu_mapage' => 'If visitors are connected, add a link to their author page.',
	'description_menu_mots' => 'Automatically shows a menu listing the articles linked to a keyword.',
	'description_menu_objet' => 'Creates a link to s SPIP object: article, section or other. By default, the entry will bear the the title of the object.',
	'description_menu_page_speciale' => 'Adds a link to a page template using a URL of the form <code>spip.php?page=name&amp;param1=xx&amp;param2=yyy...</code> Such pages are often used by plugins.',
	'description_menu_page_speciale_zajax' => 'Add a link to a block in a page accessible by a URL of the type <code>spip.php?page=name&amp;param1=xx&amp;param2=yyy...</code> This requires a Z type template and the <a href="http://www.spip-contrib.net/MediaBox">m&eacute;diabox</a> plugin.',
	'description_menu_rubriques' => 'Displays a list of sections and, if desired, the subsections to several levels. By default, all sections are shown from the site root, sorted by title (numerically then alphabetically).',
	'description_menu_secteurlangue' => 'This entry can be used by sites which have one language per sector. It displays a menu which lists the sections of the sector corresponding to the language of the page, and if desired the subsections to several levels. By default, all sections are shown from the site root, sorted by title (numerically then alphabetically).',
	'description_menu_texte_libre' => 'Simplement le texte que vous souhaitez', # NEW

	// E
	'editer_menus_editer' => 'Edit this menu',
	'editer_menus_explication' => 'Create and configure menus for your site.',
	'editer_menus_exporter' => 'Export this menu',
	'editer_menus_nouveau' => 'Create a new menu',
	'editer_menus_titre' => 'Site menus',
	'entree_aucun' => 'None',
	'entree_bloc' => 'Zpip block',
	'entree_choisir' => 'Choose the type of item you want to add:',
	'entree_connexion_objet' => 'Obliger &agrave; &ecirc;tre connect&eacute; (mettre "session") ou d&eacute;connect&eacute; (mettre "nosession") pour voir l\'objet', # NEW
	'entree_contenu' => 'Contenu', # NEW
	'entree_css' => 'CSS classes of this (container) item',
	'entree_css_lien' => 'CSS classes of the link',
	'entree_id_groupe' => 'Number of the keyword group',
	'entree_id_mot' => 'Number of the keyword',
	'entree_id_objet' => 'Number',
	'entree_id_rubrique' => 'Number of the parent section',
	'entree_infini' => 'To infinity',
	'entree_mapage' => 'My page',
	'entree_niveau' => 'Sub-sections level',
	'entree_nombre_articles' => 'Maximum number of articles (0 by default)',
	'entree_page' => 'Name of the page',
	'entree_parametres' => 'List of parameters',
	'entree_sur_n_articles' => '@n@ article(s) shown',
	'entree_sur_n_mots' => '@n@ keyword(s) shown',
	'entree_sur_n_niveaux' => 'On @n@ level(s)',
	'entree_titre' => 'Title',
	'entree_titre_connecter' => 'The title for accessing the identification form',
	'entree_titre_prive' => 'The title for accessing the private zone',
	'entree_traduction_objet' => 'Dans le cas d\'un article, choisir la traduction en fonction du contexte (mettre "trad" pour cela)', # NEW
	'entree_tri_alpha' => 'Sort criterion (alphabetic)',
	'entree_tri_num' => 'Sort criterion (numeric)',
	'entree_type_objet' => 'Object type',
	'entree_url' => 'URL',
	'entree_url_public' => 'Adresse de retour après la connexion', # NEW
	'erreur_aucun_type' => 'No item type was found.',
	'erreur_autorisation' => 'You are not allowed to modify menus.',
	'erreur_identifiant_deja' => 'This identifier is already used by another menu.',
	'erreur_identifiant_forme' => 'Identifier must contain only letters, digits or underscores.',
	'erreur_menu_inexistant' => 'Menu number @id@ doesn\'t exist.',
	'erreur_mise_a_jour' => 'An error occured during database update.',
	'erreur_parametres' => 'There is an error in the parameters of the page',
	'erreur_type_menu' => 'You need to choose a type of menu',

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
	'formulaire_ieconfig_choisir_menus_a_importer' => 'Choisissez quel(s) menu(s) vous souhaitez importer.', # NEW
	'formulaire_ieconfig_importer' => 'Importer', # NEW
	'formulaire_ieconfig_menu_meme_identifiant' => 'ATTENTION&nbsp;: un menu avec le m&ecirc;me identifiant existe d&eacute;j&agrave; sur votre votre site&nbsp;!', # NEW
	'formulaire_ieconfig_menus_a_exporter' => 'Menus &agrave; exporter&nbsp;:', # NEW
	'formulaire_ieconfig_ne_pas_importer' => 'Ne pas importer', # NEW
	'formulaire_ieconfig_remplacer' => 'Remplacer le menu actuel par le menu import&eacute;', # NEW
	'formulaire_ieconfig_renommer' => 'Renommer ce menu avant import', # NEW
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
	'info_connexion_obligatoire' => 'Connexion obligatoire', # NEW
	'info_deconnexion_obligatoire' => 'Uniquement d&eacute;connect&eacute;', # NEW
	'info_numero_menu' => 'MENU NUMBER:',
	'info_page_speciale' => 'Link to the page &laquo; @page@ &raquo;',
	'info_page_speciale_zajax' => 'Modalbox for the "@page@" page for the "@bloc@" block',
	'info_tous_groupes_mots' => 'All keyword groups',
	'info_traduction_recuperee' => 'Le contexte d&eacute;cidera de la traduction choisie', # NEW
	'info_tri' => 'Sort:',
	'info_tri_alpha' => '(alphabetical)',
	'info_tri_num' => '(numerical)',

	// N
	'noisette_description' => 'Ins&egrave;re un menu d&eacute;fini avec le plugin Menus.', # NEW
	'noisette_label_afficher_titre_menu' => 'Afficher le titre du menu&nbsp;?', # NEW
	'noisette_label_identifiant' => 'Menu &agrave; afficher&nbsp;:', # NEW
	'noisette_nom_noisette' => 'Menu', # NEW
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
	'nom_menu_rubriques' => 'List or tree of sections',
	'nom_menu_rubriques_evenements' => 'Section-related events',
	'nom_menu_secteurlangue' => 'Language sectors',
	'nom_menu_texte_libre' => 'Texte libre', # NE
);

?>
