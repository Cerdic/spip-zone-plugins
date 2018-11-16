<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de https://trad.spip.net/tradlang_module/noizetier?lang_cible=en
// ** ne pas modifier le fichier **

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// A
	'activer_composition' => 'Activate compositions',
	'apercu' => 'Preview',
	'aucun_type_noisette' => 'No type of nut loaded.',

	// B
	'bloc_sans_noisette' => 'Add nuts using the button "add a nut" or by dragging and dropping the desired nut type on this position.
',
	'bulle_activer_composition' => 'Enable compositions on the content type "@type@"',
	'bulle_configurer_objet_noisettes' => 'Configure nuts specific to this content',
	'bulle_configurer_page_noisettes' => 'Configure the nuts of the page',
	'bulle_creer_composition' => 'Create a virtual composition of the page "@page@"',
	'bulle_dupliquer_composition' => 'Create a virtual composition copied from the composition "@page@"',
	'bulle_modifier_composition' => 'Edit composition',
	'bulle_modifier_page' => 'Edit page',

	// C
	'choisir_noisette' => 'Choose the nut you want to add:',
	'compositions_non_installe' => '<b>Plugin Compositions:</b> this plugin isn’t installed on your site. It’s not necessary to the correct working of the nuts. However, when it’s activated, you can create compositions directly inside your Nuts Manager.',
	'configurer_objets_noisettes_explication' => 'On these types of content, it will be allowed to customize the nuts <strong>content by content</strong>.',
	'configurer_objets_noisettes_label' => 'Allow customization by content on:',
	'configurer_titre' => 'NoiZetier configuration',
	'copie_de' => 'Copy of @source@',

	// D
	'description_bloc_contenu' => 'Main content of each page.',
	'description_bloc_extra' => 'Contextual extra information for each page.',
	'description_bloc_navigation' => 'Navigation information specific to each page.',
	'description_bloctexte' => 'The title is optional. For the text, you can use SPIP typographical shortcuts.',

	// E
	'editer_composition' => 'Edit this composition',
	'editer_composition_heritages' => 'Define heritages',
	'editer_configurer_page' => 'Configure the nuts of this page',
	'editer_exporter_configuration' => 'Export the configuration',
	'editer_importer_configuration' => 'Import a configuration.',
	'editer_noizetier_explication' => 'Select the page where you want to configure the nuts.',
	'editer_noizetier_explication_objets' => 'Select the content you want to customize the nuts.',
	'editer_noizetier_titre' => 'Manage the nuts',
	'editer_nouvelle_page' => 'Create a new page / composition',
	'erreur_ajout_noisette' => 'The following nuts have not been added: @noisettes@',
	'erreur_aucune_noisette_selectionnee' => 'You must select a nut!',
	'erreur_doit_choisir_noisette' => 'You have to choose a nut.',
	'erreur_mise_a_jour' => 'An error occurred while updating the database.',
	'erreur_page_inactive' => 'The page is inactive because the following plugins are disabled: @plugins@.',
	'erreur_type_noisette_indisponible' => 'The nut type @type_noisette@ is no longer available because the plugin that provides this type nut must be disabled.',
	'explication_code' => 'ATTENTION: for advanced users. You can specify Spip code (loops and tags) that will be interpreted as in a skeleton. Thus the nut will have access to all variables of the environment of the page.',
	'explication_composition' => 'Composition derived from the page "@type@"',
	'explication_composition_virtuelle' => '<Strong> virtual composition </ strong> derived from the page "@type@"',
	'explication_description_code' => 'For internal purpose. Not published on the public site.',
	'explication_dupliquer_composition_reference' => 'The identifier of the duplicated page is <i>@composition@</i>.
You can choose a new identifier or suffix the reference identifier as follows:
<i>@composition@<strong>_suffixe</strong></i>',
	'explication_dupliquer_composition_suffixer' => '.',
	'explication_glisser_deposer' => 'The types of nuts that can be added to the blocks of the page are listed below.',
	'explication_heritages_composition' => 'The composition being edited is based on the "@type@" content type that has child content types. You can define for each type of child content a composition to apply by default.',
	'explication_noizetier_profondeur_max' => 'You can nest container-type nuts. Choose the number of nesting levels you want.',
	'explication_objet' => 'Type of content "@type@"',
	'explication_raccourcis_typo' => 'You can use the SPIP typographical shortcuts.',

	// F
	'formulaire_ajouter_noisette' => 'Add a nut',
	'formulaire_composition' => 'Identifier of composition',
	'formulaire_composition_explication' => 'Specify a unique keyword (lowercase, no spaces, no dashes (-) and without accents) to identify the composition. ',
	'formulaire_composition_mise_a_jour' => 'Composition updated',
	'formulaire_configurer_bloc' => 'Configure the block:',
	'formulaire_configurer_page' => 'Configure the page:',
	'formulaire_deplacer_bas' => 'Move down',
	'formulaire_deplacer_haut' => 'Move up',
	'formulaire_description' => 'Description',
	'formulaire_description_explication' => 'You can use the usual SPIP shortcuts, especially the &lt;multi&gt; tag.',
	'formulaire_dupliquer_page' => 'Duplicate this composition',
	'formulaire_dupliquer_page_entete' => 'Duplicate a page',
	'formulaire_dupliquer_page_titre' => 'Duplicate the page « @page@ »',
	'formulaire_erreur_format_identifiant' => 'The identifier can only contain lowercase letters without accents, numbers and the "_" (underscore) character.',
	'formulaire_icon' => 'Icon',
	'formulaire_icon_explication' => 'You can enter the relative path to an icon (for example : <i>images/list-item-contenus.png</i>).',
	'formulaire_identifiant_deja_pris' => 'This identifier already exists!',
	'formulaire_import_compos' => 'Import the compositions of the Nuts Manager',
	'formulaire_import_fusion' => 'Merge with the current configuration',
	'formulaire_import_remplacer' => 'Replace the current configuration',
	'formulaire_liste_compos_config' => 'This configuration file defines the following Nuts Manager compositions:',
	'formulaire_liste_pages_config' => 'This configuration file defines
nuts on the following pages:',
	'formulaire_modifier_composition' => 'Edit this composition',
	'formulaire_modifier_composition_heritages' => 'Inherited compositions',
	'formulaire_modifier_noisette' => 'Edit this nut',
	'formulaire_modifier_page' => 'Edit this page',
	'formulaire_noisette_sans_parametre' => 'This nut does not have a configuration parameter of its own.',
	'formulaire_nom' => 'Title',
	'formulaire_nom_explication' => 'You can use the &lt;multi&gt; tag.',
	'formulaire_nouvelle_composition' => 'New composition',
	'formulaire_obligatoire' => 'Required field',
	'formulaire_supprimer_noisette' => 'Delete this nuts',
	'formulaire_supprimer_noisettes_bloc' => 'Remove the nuts from the block',
	'formulaire_supprimer_noisettes_page' => 'Remove all nuts',
	'formulaire_supprimer_page' => 'Remove this composition',
	'formulaire_type' => 'Page type',
	'formulaire_type_explication' => 'The type of content that inherits the composition.',
	'formulaire_type_import' => 'Import type',
	'formulaire_type_import_explication' => 'You can merge the configuration file with your actual configuration (the nuts of each page will be added to your already defined nuts) or you can replace your configuration by this one.',

	// I
	'icone_introuvable' => 'Icon not found!',
	'ieconfig_ne_pas_importer' => 'Do not import',
	'ieconfig_noizetier_export_explication' => 'Will export the configuration of the nuts and compositions of the Nuts Manager.',
	'ieconfig_noizetier_export_option' => 'Included in the export?',
	'ieconfig_non_installe' => '<b>Configuration Import/Export Plugin:</b> this plugin isn’t installed on your site. It is not necessarry to the correct working of the Nuts Manager. However, when it’s activated, you can  export and import some nuts configurations into the Nuts Manager.',
	'ieconfig_probleme_import_config' => 'A problem occured while importing the Nuts Manager configuration.',
	'info_composition' => 'COMPOSITION:',
	'info_page' => 'PAGE:',
	'installation_tables' => 'Installed tables of the Nuts Manager Plugin.<br />',
	'item_titre_perso' => 'custom title',

	// L
	'label_afficher_titre_noisette' => 'Display a title of nut?',
	'label_code' => 'Spip code:',
	'label_description_code' => 'Description:',
	'label_niveau_titre' => 'Title level:',
	'label_noisette_css' => 'CSS classes',
	'label_noizetier_ajax' => 'By default, include each nut in AJAX',
	'label_texte' => 'Text:',
	'label_texte_introductif' => 'Introduction text (optional):',
	'label_titre' => 'Title:',
	'label_titre_noisette' => 'Title of the nut:',
	'label_titre_noisette_perso' => 'Custom title:',
	'liste_icones' => 'Icons list',
	'liste_pages' => 'List of the pages',

	// M
	'masquer' => 'Hide',
	'mode_noisettes' => 'Edit the nuts',
	'modif_en_cours' => 'Ongoing changes',
	'modifier_dans_prive' => 'Modify in the private area',

	// N
	'ne_pas_definir_d_heritage' => 'Do not define inherited composition',
	'noisette_numero' => 'nut number :',
	'noisettes_composition' => 'Specific nuts to the composition <i>@composition@</i>:',
	'noisettes_disponibles' => 'Type of nuts available',
	'noisettes_page' => 'Nut types specific to the page<i>@type@</i>:',
	'noisettes_toutes_pages' => 'Nut types common to all pages:',
	'noizetier' => 'Nuts Manager',
	'nom_bloc_contenu' => 'Content',
	'nom_bloc_extra' => 'Extra',
	'nom_bloc_navigation' => 'Navigation',
	'nom_bloctexte' => 'Block of free text',
	'nom_codespip' => 'Free Spip code',
	'non' => 'No',
	'notice_enregistrer_rang' => 'Clic on Save to store the nuts order.',

	// O
	'operation_annulee' => 'Operation canceled.',
	'oui' => 'Yes',

	// P
	'page' => 'Page',
	'page_autonome' => 'Specific page',
	'probleme_droits' => 'You don’t have the permission to make this change.',

	// Q
	'quitter_mode_noisettes' => 'Quit editing nuts',

	// R
	'recharger_noisettes' => 'Reload the nut types',
	'retour' => 'Back',

	// S
	'suggestions' => 'Suggestions',

	// W
	'warning_noisette_plus_disponible' => 'WARNING: this nut is no longer available.',
	'warning_noisette_plus_disponible_details' => 'The template of the nut (<i>@squelette@</i>) is no longer available. It may be a nut requiring a plugin that you have disabled or uninstalled.'
);
