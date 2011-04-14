<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de http://trad.mediaspip.net/spip.php?page=tradlang
// ** ne pas modifier le fichier **

if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// A
	'aucunmodule' => 'No module.',

	// B
	'bouton_supprimer_module' => 'Delete this module',
	'bouton_traduire' => 'Translate >>',

	// C
	'cfg_form_tradlang_autorisations' => 'Authorizations',
	'cfg_inf_type_autorisation' => 'If you choose by status or by author, you will be prompted below your selection of statuses or authors.',
	'cfg_lbl_autorisation_auteurs' => 'Allow a list of authors',
	'cfg_lbl_autorisation_statuts' => 'Allow by authors statuses',
	'cfg_lbl_autorisation_webmestre' => 'Allow webmasters only',
	'cfg_lbl_liste_auteurs' => 'Authors of the website',
	'cfg_lbl_statuts_auteurs' => 'Possible statuses',
	'cfg_lbl_type_autorisation' => 'Authorization method',
	'cfg_legend_autorisation_configurer' => 'Manage the plugin',
	'cfg_legende_autorisation_modifier' => 'Modify translations',
	'cfg_legende_autorisation_voir' => 'See translation interface',
	'codelangue' => 'Language code',

	// E
	'entrerlangue' => 'Add a language code',
	'erreur_aucun_module' => 'There are no modules available in the database.',
	'erreur_autorisation_modifier_modules' => 'You are not allowed to translate the language modules.',
	'erreur_choisir_lang_cible' => 'Choose a target language to translate.',
	'erreur_choisir_lang_orig' => 'Choose an origin language as a basis for translation.',
	'erreur_choisir_module' => 'Choose a module to translate.',
	'erreur_code_langue_existant' => 'This alternative language already exists for this module',
	'erreur_code_langue_invalide' => 'This language code is invalid',
	'erreur_langues_autorisees_insuffisantes' => 'You should at least select two languages',
	'erreur_module_inconnu' => 'This module is not available',
	'erreur_pas_langue_cible' => 'Select a language',
	'erreur_repertoire_local_inexistant' => 'Warning: the directory used to save files localy does not exist: "squelettes/lang"',
	'explication_langues_autorisees' => 'Users will be able to create a new translation only in the selected languages​​.',
	'explication_sauvegarde_locale' => 'Will save the files in the "squelettes" folder of the site',
	'explication_sauvegarde_post_edition' => 'Will save temporary files each time you change a language string',

	// I
	'importer_module' => 'Importing new language module',
	'importermodule' => 'Import a module',
	'info_filtrer_status' => 'Filter by status:',
	'info_status_ok' => 'OK',
	'infos_trad_module' => 'Informations on translations',
	'item_creer_langue_cible' => 'Create a new target language',
	'item_langue_cible' => 'Target language: ',
	'item_langue_origine' => 'Source language:',
	'items_en_trop' => '@nb@ items are over in that language (from the parent language)',
	'items_manquants' => '@nb@ items are missing in that language (from the parent language)',
	'items_modif' => 'Modified items',
	'items_new' => 'New items',
	'items_total_nb' => 'Total number of items',

	// L
	'label_idmodule' => 'Module ID',
	'label_langue_mere' => 'Parent language',
	'label_langues_autorisees' => 'Allow only selected languages',
	'label_nommodule' => 'Customize the module name',
	'label_recherche_module' => 'In the module:',
	'label_recherche_status' => 'With the status:',
	'label_repertoire_module_langue' => 'Folder of the module',
	'label_sauvegarde_locale' => 'Allow to save files locally',
	'label_sauvegarde_post_edition' => 'Save the file with each change',
	'label_synchro_base_fichier' => 'Synchronize the database and local files',
	'label_texte' => 'Description of the module',
	'label_tradlang_status' => 'Status of the translation',
	'label_tradlang_str' => 'Translated string',
	'label_update_langues_cible_mere' => 'Update this language in the database',
	'languesdispo' => 'Languages available',
	'lien_accueil_interface' => 'Home of the translation interface',
	'lien_aide_recherche' => 'Search help',
	'lien_aucun_status' => 'None',
	'lien_bilan' => 'Review of current translations.',
	'lien_code_langue' => 'Invalid language code. The language code must have at least two letter code (ISO-631).',
	'lien_confirm_export' => 'Confirm the export of the current file (ie overwrite the file @fichier@)',
	'lien_export' => 'Automatically export the current file.',
	'lien_page_depart' => 'Back to the main page?',
	'lien_proportion' => 'Proportion of strings displayed',
	'lien_recharger_page' => 'Reload the page.',
	'lien_recherche_avancee' => 'Advanced search',
	'lien_retour' => 'Back',
	'lien_revenir_traduction' => 'Back to the translation page',
	'lien_sauvegarder' => 'Backup / Restore the current file.',
	'lien_telecharger' => '[Download]',
	'lien_traduction_module' => 'Module ',
	'lien_traduction_vers' => ' to ',
	'lien_voir_toute_chaines_module' => 'See all the strings of the module.',

	// M
	'message_aucun_resultat_chaine' => 'No results matching your criteria in the chains of language.',
	'message_aucun_resultat_statut' => 'No string matches the requested status.',
	'message_aucune_nouvelle_langue_dispo' => 'This module is available in all languages',
	'message_demande_update_langues_cible_mere' => 'You can ask an administrator to resynchronize this language with the primary language.
	',
	'message_module_langue_ajoutee' => 'The language "@language@" was added to the module "@module@".',
	'message_module_updated' => 'The language module "@module@" has been updated.',
	'message_passage_trad' => 'We go to the translation',
	'message_passage_trad_creation_lang' => 'Creation of the language @lang@ and move on to the translation',
	'message_suppression_module_ok' => 'The module @module@ has been deleted.',
	'message_suppression_module_trads_ok' => 'The module @module@ has been deleted. @nb@ translation items belonging to it were also removed.',
	'message_synchro_base_fichier_ok' => 'Files and database are sync.',
	'message_synchro_base_fichier_pas_ok' => 'Files and database are not sync.',
	'module_deja_importe' => 'The module "@module@" has already been imported.',
	'moduletitre' => 'Modules available',

	// N
	'nb_items_langue_cible' => 'The target language "@langue@" includes @nb@ items defined in the parent language.',
	'nb_items_langue_en_trop' => '@nb@ items are too much in the language "@langue@".',
	'nb_items_langue_inexistants' => '@nb@ items does not exists in the language "@langue@".',
	'nb_items_langue_mere' => 'The main language of this module includes @nb@ items.',

	// R
	'readme' => 'This plugin allows you to manage the language files',

	// S
	'str_status_modif' => 'New (MODIF)',
	'str_status_traduit' => 'Translated',

	// T
	'texte_erreur' => 'ERROR',
	'texte_erreur_acces' => '<b>Warning: </b>unable to write to the file <tt>@fichier_lang@</tt>. Check access rights.',
	'texte_existe_deja' => ' already exists.',
	'texte_explication_langue_cible' => 'For the target language, you must choose if you are working towards a language that already exists, or if you create a new language.',
	'texte_export_impossible' => 'Unable to export the file. Check write permissions on the file @cible@',
	'texte_filtre' => 'Filter (search)',
	'texte_interface' => 'Translation interface:',
	'texte_interface2' => 'Translation interface',
	'texte_langue' => 'Language:',
	'texte_langue_cible' => '<b>The target language</b> which is the language you are translating.',
	'texte_langue_origine' => '<b>The parent language</b> which is the language from which you translate to an other one;',
	'texte_langue_origine2' => 'translate (surely the French).',
	'texte_langues_differentes' => 'The target and origin language must be different.',
	'texte_modifier' => 'Edit',
	'texte_module' => 'the module to translate;',
	'texte_module_traduire' => 'The module to translate: ',
	'texte_non_traduit' => 'not translated',
	'texte_operation_impossible' => 'Opération impossible. Lorsque la case \'tout sélectionner\' est cochée,<br> il faut faire des opérations de type \'Consulter\'.', # NEW
	'texte_pas_de_reponse' => '... no response',
	'texte_recapitulatif' => 'Summary of the translations',
	'texte_restauration_impossible' => 'impossible to restore the file',
	'texte_sauvegarde' => 'Translation interface, Save / Restore file',
	'texte_sauvegarde_courant' => 'Backup copy of the current file:',
	'texte_sauvegarde_impossible' => 'Unable to backup the file',
	'texte_sauvegarder' => 'Save',
	'texte_selection_langue' => 'To view a translated language file / currently being translated, please select the language:',
	'texte_selectionner' => 'You must choose:',
	'texte_selectionner_version' => 'Choose the version of the file, then click the button below.',
	'texte_seul_admin' => 'Only an administrator account can access this step.',
	'texte_total_chaine' => 'Number of strings:',
	'texte_total_chaine_conflit' => 'Number of unused strings:',
	'texte_total_chaine_modifie' => 'Number of strings to submit updates:',
	'texte_total_chaine_non_traduite' => 'Number of untranslated strings:',
	'texte_total_chaine_traduite' => 'Number of translated strings:',
	'texte_tout_selectionner' => 'Select all',
	'texte_type_operation' => 'Operation type',
	'th_langue' => 'Language',
	'th_langue_origine' => 'Text in the original language',
	'th_module' => 'Module',
	'th_status' => 'Status',
	'th_traduction' => 'Translation',
	'titre_page_configurer_tradlang' => 'Setup of the Trad-lang plugin',
	'titre_recherche_tradlang' => 'Language strings
	',
	'titre_tradlang' => 'Trad-lang',
	'titre_traduction' => 'Translations',
	'titre_traduction_de' => 'Translation of',
	'titre_traduction_module_de_vers' => 'Translation of the module "@module@" from <abbr title="@lang_orig_long@">@lang_orig@</abbr> to <abbr title="@lang_cible_long@">@lang_cible@</abbr>',
	'tradlang' => 'Trad-Lang',
	'traduction' => 'Translation @lang@',
	'traductions' => 'Translations',

	// V
	'visumodule' => 'Module summary'
);

?>
