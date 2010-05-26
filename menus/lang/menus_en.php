<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de http://www.spip.net/trad-lang/
// ** ne pas modifier le fichier **

if (!defined("_ECRIRE_INC_VERSION")) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// D
	'description_menu_accueil' => 'Link to website\'s home page.',
	'description_menu_deconnecter' => 'Si le visiteur est connect&eacute;, ajoute une entr&eacute;e lui proposant la d&eacute;connexion.', # NEW
	'description_menu_groupes_mots' => 'Affiche automatiquement un menu listant les mots du groupe et les articles liés. Par d&eacute;faut, affiche la liste des groupes de mots et les mots li&eacute;s. Si un squelette groupes_mots.html existe, le lien vers le groupe est utilis&eacute;.', # NEW
	'description_menu_lien' => 'Ajoute un lien arbitraire, en interne (URL relative) ou externe (http://...).', # NEW
	'description_menu_mapage' => 'Si le visiteur est connect&eacute;, ajoute un lien vers sa page auteur.', # NEW
	'description_menu_mots' => 'Affiche automatiquement un menu listant les articles li&eacute;s au mot cl&eacute;.', # NEW
	'description_menu_objet' => 'Cr&eacute;e un lien vers un objet de SPIP : article, rubrique ou autre. Par d&eacute;faut, l\'entr&eacute;e aura le titre de l\'objet.', # NEW
	'description_menu_page_speciale' => 'Ajoute un lien vers un squelette page accessible par une url du type <code>spip.php?page=nom&param1=xx&param2=yyy...</code> Ces pages sont souvent fournies par des plugins.', # NEW
	'description_menu_rubriques' => 'Affiche une liste de rubriques et, si on veut, les sous-rubriques sur plusieurs niveaux. Par d&eacute;faut, affiche toutes les rubriques depuis la racine, tri&eacute;es par titre (num&eacute;riquement puis alphab&eacute;tiquement).', # NEW
	'description_menu_secteurlangue' => 'Cette entr&eacute;e est sp&eacute;cifique aux sites utilisant un secteur par langue. Elle affiche automatiquement un menu listant les rubriques du secteur correspondant &agrave; la langue de la page et, si on veut, les sous-rubriques sur plusieurs niveaux. Par d&eacute;faut, affiche toutes les rubriques depuis la racine, tri&eacute;es par titre (num&eacute;riquement puis alphab&eacute;tiquement).', # NEW

	// E
	'editer_menus_editer' => 'Edit this menu',
	'editer_menus_explication' => 'Create and configure menus for your site.',
	'editer_menus_exporter' => 'Export this menu',
	'editer_menus_nouveau' => 'Create a new menu',
	'editer_menus_titre' => 'Site menus',
	'entree_aucun' => 'Aucun', # NEW
	'entree_choisir' => 'Choose the type of item you want to add:',
	'entree_css' => 'CSS classes of this item',
	'entree_id_groupe' => 'Num&eacute;ro du groupe de mot cl&eacute;', # NEW
	'entree_id_mot' => 'Num&eacute;ro du mot cl&eacute;', # NEW
	'entree_id_objet' => 'Number',
	'entree_id_rubrique' => 'Number of the parent section',
	'entree_infini' => 'To infinity', # MODIF
	'entree_mapage' => 'My page',
	'entree_niveau' => 'Sub-sections level',
	'entree_nombre_articles' => 'Nombre d\'articles au maximum (0 par défaut)', # NEW
	'entree_page' => 'Nom de la page', # NEW
	'entree_parametres' => 'Liste des param&egrave;tres', # NEW
	'entree_sur_n_articles' => '@n@ articles affich&eacute;(s)', # NEW
	'entree_sur_n_mots' => '@n@ mots affich&eacute;(s)', # NEW
	'entree_sur_n_niveaux' => 'On @n@ level(s)',
	'entree_titre' => 'Title',
	'entree_tri_alpha' => 'Crit&egrave;re de tri (alphab&eacute;tique)', # NEW
	'entree_tri_num' => 'Crit&egrave;re de tri (num&eacute;rique)', # NEW
	'entree_type_objet' => 'Object type',
	'entree_url' => 'URL',
	'erreur_aucun_type' => 'No item type was found.',
	'erreur_autorisation' => 'You are not allowed to modify menus.',
	'erreur_identifiant_deja' => 'This identifier is already used by another menu.',
	'erreur_identifiant_forme' => 'Identifier must contain only letters, digits or underscores.',
	'erreur_menu_inexistant' => 'Menu number @id@ doesn\'t exist.',
	'erreur_mise_a_jour' => 'An error occured during database update.',
	'erreur_parametres' => 'Il y a une erreur dans les param&egrave;tres de la page', # NEW
	'erreur_type_menu' => 'Vous devez choisir un type de menu', # NEW

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
	'info_numero_menu' => 'MENU NUMBER:',
	'info_page_speciale' => 'Lien vers la page &#171; @page@ &#187;', # NEW
	'info_tous_groupes_mots' => 'Tous les groupes de mots', # NEW
	'info_tri' => 'Tri :', # NEW
	'info_tri_alpha' => '(alphab&eacute;tique)', # NEW
	'info_tri_num' => '(num&eacute;rique)', # NEW

	// N
	'nom_menu_accueil' => 'Home Page',
	'nom_menu_deconnecter' => 'Se d&eacute;connecter', # NEW
	'nom_menu_groupes_mots' => 'Mots-cl&eacute;s et Articles d\'un Groupes de mots', # NEW
	'nom_menu_lien' => 'Lien arbitraire', # NEW
	'nom_menu_mapage' => 'Ma page', # NEW
	'nom_menu_mots' => 'Articles d\'un Mot-cl&eacute;', # NEW
	'nom_menu_objet' => 'Article, rubrique ou autre objet SPIP', # NEW
	'nom_menu_page_speciale' => 'Lien vers un squelette page', # NEW
	'nom_menu_rubriques' => 'Liste ou arborescence de rubriques', # NEW
	'nom_menu_secteurlangue' => 'Secteurs de langue', # NE
);

?>
