<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de http://trad.spip.net/tradlang_module/tradlang?lang_cible=en
// ** ne pas modifier le fichier **

if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// A
	'aucunmodule' => 'No module.',
	'auteur_revision' => '@nb@ translation modified.',
	'auteur_revision_specifique' => '@nb@ modification de traduction en <abbr title="@lang@">@langue_longue@</abbr>.', # NEW
	'auteur_revisions' => '@nb@ translations modified.',
	'auteur_revisions_langue' => 'Contributions langage :',
	'auteur_revisions_langues' => '@nb@ contributions langage:',
	'auteur_revisions_specifique' => '@nb@ modifications de traductions en <abbr title="@lang@">@langue_longue@</abbr>.', # NEW

	// B
	'bouton_activer_lang' => 'Enable the language "@lang@" for this module',
	'bouton_exporter_fichier' => 'Export the file',
	'bouton_exporter_fichier_langue' => 'Export the language file in "@lang@"',
	'bouton_exporter_fichier_langue_original' => 'Export the original language file ("@lang_mere@")',
	'bouton_exporter_fichier_po' => 'Export the file in .po',
	'bouton_exporter_fichier_zip' => 'Export the files in a zip',
	'bouton_precedent' => 'Previous step',
	'bouton_suivant' => 'Next step',
	'bouton_supprimer_langue_module' => 'Delete this language from the module',
	'bouton_supprimer_module' => 'Delete this module',
	'bouton_traduire' => 'Translate',
	'bouton_upload_langue_module' => 'Upload a language file',
	'bouton_vos_favoris_non' => 'Your not so favourite modules',
	'bouton_vos_favoris_oui' => 'Your favorite modules',
	'bouton_vos_favoris_tous' => 'All modules',

	// C
	'cfg_form_tradlang_autorisations' => 'Authorizations',
	'cfg_inf_type_autorisation' => 'If you choose by status or by author, you will be prompted below for your selection of statuses or authors.',
	'cfg_lbl_autorisation_auteurs' => 'Allow a list of authors',
	'cfg_lbl_autorisation_statuts' => 'Allow by authors\' statuses',
	'cfg_lbl_autorisation_webmestre' => 'Allow webmasters only',
	'cfg_lbl_liste_auteurs' => 'Authors of the website',
	'cfg_lbl_statuts_auteurs' => 'Possible statuses',
	'cfg_lbl_type_autorisation' => 'Authorization method',
	'cfg_legend_autorisation_configurer' => 'Manage the plugin',
	'cfg_legende_autorisation_modifier' => 'Edit translations',
	'cfg_legende_autorisation_voir' => 'Show translation interface',
	'codelangue' => 'Language code',
	'crayon_changer_statut' => 'Warning! You changed the content of the string without changing the status.',
	'crayon_changer_statuts' => 'Warning! You changed the content of one or more strings without changing the status.',

	// E
	'entrerlangue' => 'Add a language code',
	'erreur_aucun_item_langue_mere' => 'Parent language "@lang_mere@" contains no language items.',
	'erreur_aucun_module' => 'There are no modules available in the database.',
	'erreur_aucun_tradlang_a_editer' => 'No language string is seen as untranslated.',
	'erreur_autorisation_modifier_modules' => 'You are not allowed to translate the language modules.',
	'erreur_autoriser_profil' => 'You are not allowed to edit this profile',
	'erreur_choisir_lang_cible' => 'Choose a target language to translate.',
	'erreur_choisir_lang_orig' => 'Choose an source language as a basis for translation.',
	'erreur_choisir_module' => 'Choose a module to translate.',
	'erreur_code_langue_existant' => 'This alternative language already exists for this module',
	'erreur_code_langue_invalide' => 'This language code is invalid',
	'erreur_langue_activer_impossible' => 'The language code "@lang@" does not exist.',
	'erreur_langues_autorisees_insuffisantes' => 'You should at least select two languages',
	'erreur_langues_differentes' => 'Choose a target language different from the parent language',
	'erreur_modif_tradlang_session' => 'You can\'t edit this language item.',
	'erreur_modif_tradlang_session_identifier' => 'Please login.',
	'erreur_module_inconnu' => 'This module is not available',
	'erreur_pas_langue_cible' => 'Select a target language',
	'erreur_repertoire_local_inexistant' => 'Warning: the directory used to save files locally does not exist: "squelettes/lang"',
	'erreur_statut_js' => 'The language string has been changed but not its status',
	'erreur_upload_aucune_modif' => 'Your file presents no modification compared to the database',
	'erreur_upload_choisir_une' => 'You need to validate at least one modification',
	'erreur_upload_fichier_php' => 'Your file "@fichier@" is not the expected file, "@fichier_attendu@".',
	'erreur_variable_manquante' => 'The following part of the string should not be changed:',
	'erreur_variable_manquante_js' => 'One or more required variables were changed',
	'erreur_variable_manquantes' => 'The @nb@ parts of the following string should not be changed:',
	'explication_comm' => 'The comment is in the language file in order to explain, for example, a particular choice of translation.',
	'explication_langue_cible' => 'The language into which you translate.',
	'explication_langue_origine' => 'The language from which you translate (Only 100% complete languages ​​are available).',
	'explication_langues_autorisees' => 'Users will be able to create a new translation only in the selected languages​​.',
	'explication_limiter_langues_bilan' => 'By default, @nb@ languages ​​will be displayed if users do not select preferred languages in their profile.',
	'explication_limiter_langues_bilan_nb' => 'How many languages ​​are displayed by default (the most translated languages ​ will be selected).',
	'explication_sauvegarde_locale' => 'Will save the files in the "squelettes" folder of the site',
	'explication_sauvegarde_post_edition' => 'Will save temporary files each time you change a language string',

	// F
	'favoris_ses_modules' => 'Their favorites modules',
	'favoris_vos_modules' => 'Your favorites modules',

	// I
	'icone_modifier_tradlang' => 'Edit this language string',
	'icone_modifier_tradlang_module' => 'Edit this language module',
	'importer_module' => 'Importing new language module',
	'importermodule' => 'Import a module',
	'info_1_tradlang' => '@nb@ language strings',
	'info_1_tradlang_module' => '@nb@ language module',
	'info_aucun_participant_lang' => 'Aucun auteur du site n\'a encore traduit en <abbr title="@lang@">@langue_longue@</abbr>.', # NEW
	'info_aucun_tradlang_module' => 'No language module',
	'info_auteur_sans_favori' => 'This author has no favorite module.',
	'info_chaine_jamais_modifiee' => 'This string has never been edited.',
	'info_chaine_originale' => 'This string is the source',
	'info_choisir_langue' => 'Dans une langue spécifique', # NEW
	'info_contributeurs' => 'Contributors',
	'info_filtrer_status' => 'Filter by status:',
	'info_langue_mere' => '(parent language)',
	'info_langues_non_preferees' => 'Other languages :',
	'info_langues_preferees' => 'Favorite language(s):',
	'info_module_traduction' => '@total@ @statut@ (@percent@%)',
	'info_module_traduit_langues' => 'This module is translated or partially translated into ​​@nb@ languages.',
	'info_module_traduit_pc' => 'Module @pc@% translated',
	'info_module_traduit_pc_lang' => 'Module "@module@" is @pc@% translated in @lang@ (@langue_longue@)',
	'info_modules_priorite_traduits_pc' => 'The modules of priority "@priorite@" are @pc@% translated in @lang@',
	'info_nb_items_module' => '@nb@ items in the module "@module@"',
	'info_nb_items_module_modif' => '@nb@ items of the module "@module@" modified and need checking in @lang@ (@langue_longue@)"',
	'info_nb_items_module_modif_aucun' => 'No item of the module "@module@" is modified and to verify in @lang@ (@langue_longue@)',
	'info_nb_items_module_modif_un' => 'An item of the module "@module@" is modified and to verify in @lang@ (@langue_longue@)"',
	'info_nb_items_module_new' => '@nb@ items of the module "@module@" are to translate in @lang@ (@langue_longue@)"',
	'info_nb_items_module_new_aucun' => 'No item of the module "@module@" needs translation in @lang@ (@langue_longue@)',
	'info_nb_items_module_new_un' => 'An item of the module "@module@" needs translation in @lang@ (@langue_longue@)"',
	'info_nb_items_module_ok' => '@nb@ items of the module "@module@" needs translation in @lang@ (@langue_longue@)"',
	'info_nb_items_module_ok_aucun' => 'No item of the module "@module@" is translated in @lang@ (@langue_longue@)',
	'info_nb_items_module_ok_un' => 'An item of the module "@module@" is translated in @lang@ (@langue_longue@)"',
	'info_nb_items_priorite' => 'The modules of priority "@priorite@" have @nb@ items',
	'info_nb_items_priorite_modif' => '@pc@% of the items of priority "@priorite@" are modified and need checking in @lang@ (@langue_longue@)',
	'info_nb_items_priorite_new' => '@pc@% of the items of priority "@priorite@" are new in @lang@ (@langue_longue@)',
	'info_nb_items_priorite_ok' => 'The modules of priority "@priorite@" are @pc@% translated in @lang@ (@langue_longue@)',
	'info_nb_modules_favoris' => '@nb@ favorite modules.',
	'info_nb_participant' => '@nb@ auteur inscrit sur ce site a participé au moins une fois à la traduction.', # NEW
	'info_nb_participant_lang' => '@nb@ auteur inscrit sur ce site a participé au moins une fois à la traduction en <abbr title="@lang@">@langue_longue@</abbr>.', # NEW
	'info_nb_participants' => '@nb@ authors listed on this site participated at least once to a translation.',
	'info_nb_participants_lang' => '@nb@ auteurs inscrits sur ce site ont participé au moins une fois à la traduction en <abbr title="@lang@">@langue_longue@</abbr>.', # NEW
	'info_nb_tradlang' => '@nb@ language strings',
	'info_nb_tradlang_module' => '@nb@ language modules',
	'info_percent_chaines' => '@traduites@ / @total@ translated strings',
	'info_revisions_stats' => 'Revisions',
	'info_status_ok' => 'OK',
	'info_str' => 'Text of the language string',
	'info_textarea_readonly' => 'This text field is read only',
	'info_tradlangs_sans_version' => '@nb@ language strings do not have a first revision( first revisions are created by CRON).',
	'info_traduire_module_lang' => 'Translate the module "@module@" in @langue_longue@ (@lang@)',
	'infos_trad_module' => 'Information on translations',
	'item_creer_langue_cible' => 'Create a new target language',
	'item_langue_cible' => 'Target language: ',
	'item_langue_origine' => 'Source language:',
	'item_manquant' => '1 item is missing in this language (compared to the parent language)',
	'items_en_trop' => '@nb@ too many items in this language (compared to the parent language)',
	'items_manquants' => '@nb@ items are missing in that language (from the parent language)',
	'items_modif' => 'Modified items:',
	'items_new' => 'New items:',
	'items_total_nb' => 'Total number of items:',

	// J
	'job_creation_revisions_modules' => 'Creating original revisions of module "@module@"',

	// L
	'label_fichier_langue' => 'Language file to upload',
	'label_id_tradlang' => 'String id',
	'label_idmodule' => 'Module ID',
	'label_lang' => 'Language',
	'label_langue_mere' => 'Parent language',
	'label_langues_autorisees' => 'Allow only some languages',
	'label_langues_preferees_auteur' => 'Your favorite language(s)',
	'label_langues_preferees_autre' => 'Their favorite language(s)',
	'label_limiter_langues_bilan' => 'Limit the number of languages ​​visible on the overview page',
	'label_limiter_langues_bilan_nb' => 'Languages count',
	'label_nommodule' => 'Module name',
	'label_priorite' => 'Priority',
	'label_proposition_google_translate' => 'Google Translate proposal',
	'label_recherche_module' => 'In the module:',
	'label_recherche_status' => 'With the status:',
	'label_repertoire_module_langue' => 'Folder of the module',
	'label_sauvegarde_locale' => 'Allow to save files locally',
	'label_sauvegarde_post_edition' => 'Save the file with each change',
	'label_synchro_base_fichier' => 'Synchronize the database and local files',
	'label_texte' => 'Description of the module',
	'label_tradlang_comm' => 'Comment',
	'label_tradlang_status' => 'Status of the translation',
	'label_tradlang_str' => 'Translated string (@lang@)',
	'label_update_langues_cible_mere' => 'Update this language in the database',
	'label_valeur_fichier' => 'In your file',
	'label_valeur_fichier_valider' => 'Validate the modification of your file',
	'label_valeur_id' => 'Language code:',
	'label_valeur_originale' => 'In the database',
	'label_version_originale' => 'The source string (@lang@)',
	'label_version_originale_choisie' => 'In the selected language (@lang@)',
	'label_version_originale_comm' => 'Comment from the source (@lang@)',
	'label_version_selectionnee' => 'String in the selected language (@lang@)',
	'label_version_selectionnee_comm' => 'Comment in the selected language (@lang@)',
	'languesdispo' => 'Languages available',
	'legend_conf_bilan' => 'Display translation state',
	'lien_accueil_interface' => 'Home of the translation interface',
	'lien_aide_recherche' => 'Search help',
	'lien_aucun_status' => 'None',
	'lien_bilan' => 'Review of current translations.',
	'lien_check_all' => 'Check all',
	'lien_check_none' => 'Uncheck all',
	'lien_code_langue' => 'Invalid language code. The language code must have at least two letter code (ISO-631).',
	'lien_confirm_export' => 'Confirm the export of the current file (ie overwrite the file @fichier@)',
	'lien_editer_chaine' => 'Edit',
	'lien_editer_tous' => 'Edit all untranslated strings',
	'lien_export' => 'Automatically export the current file.',
	'lien_page_depart' => 'Back to the main page?',
	'lien_profil_auteur' => 'Your profile',
	'lien_profil_autre' => 'Their profile',
	'lien_proportion' => 'Proportion of strings displayed',
	'lien_recharger_page' => 'Reload the page.',
	'lien_recherche_avancee' => 'Advanced search',
	'lien_retour' => 'Back',
	'lien_retour_module' => 'Back to the module "@module@"',
	'lien_retour_page_auteur' => 'Back to your page',
	'lien_retour_page_auteur_autre' => 'Back to their page',
	'lien_revenir_traduction' => 'Back to the translation page',
	'lien_sauvegarder' => 'Backup / Restore the current file.',
	'lien_telecharger' => '[Download]',
	'lien_traduction_module' => 'Module ',
	'lien_traduction_vers' => ' to ',
	'lien_traduire_suivant_str_module' => 'Translate the next untranslated string of the module "@module@"',
	'lien_trier_langue_non' => 'Show the global state of translations.',
	'lien_utiliser_google_translate' => 'Use this version',
	'lien_voir_bilan_lang' => 'Display the state of the language @langue_longue@ (@lang@)',
	'lien_voir_bilan_module' => 'Display the translation state of module @nom_mod@ - @module@',
	'lien_voir_toute_chaines_module' => 'See all the strings of the module.',

	// M
	'menu_info_interface' => 'Displays a link to the translation interface',
	'menu_titre_interface' => 'Translation interface',
	'message_afficher_vos_modules' => 'Show modules:',
	'message_aucun_resultat_chaine' => 'No results matching your criteria in the language strings.',
	'message_aucun_resultat_statut' => 'No string matches the requested status.',
	'message_aucune_nouvelle_langue_dispo' => 'This module is available in all languages',
	'message_changement_lang_orig' => 'The original language of translation selected ("@lang_orig@") is not sufficiently translated, it is replaced by the language "@lang_nouvelle@".',
	'message_changement_lang_orig_inexistante' => 'The original language of translation selected ("@lang_orig@") does not exist, it is replaced by the language "@lang_nouvelle@".',
	'message_changement_statut' => 'Modification of status from "@statut_old@" to ""@statut_new@"',
	'message_confirm_redirection' => 'You will be redirected to the modification of the module',
	'message_demande_update_langues_cible_mere' => 'You can ask an administrator to resynchronize this language with the primary language.
	',
	'message_info_choisir_langues_profiles' => 'You can select your favorites languages <a href="@url_profil@">in your profile</a> to use them as default.',
	'message_lang_cible_selectionnee_auto_preferees' => 'The language you are going to translate to was picked up automatically ("@lang@") from your favourite languages. You can change it through the modules selection form.',
	'message_langues_choisies_affichees' => 'Only the languages ​​you have chosen are displayed: @langues@.',
	'message_langues_preferees_affichees' => 'Only your favorites languages are displayed: @langues@.',
	'message_langues_utilisees_affichees' => 'Only the @nb@ most used languages ​​are displayed: @langues@.',
	'message_module_langue_ajoutee' => 'The language "@language@" was added to the module "@module@".',
	'message_module_updated' => 'The language module "@module@" has been updated.',
	'message_passage_trad' => 'We go to the translation',
	'message_passage_trad_creation_lang' => 'Creation of the language @lang@ and move on to the translation',
	'message_suppression_module_ok' => 'The module @module@ has been deleted.',
	'message_suppression_module_trads_ok' => 'The module @module@ has been deleted. @nb@ translation items belonging to it were also removed.',
	'message_synchro_base_fichier_ok' => 'Files and database are in sync.',
	'message_synchro_base_fichier_pas_ok' => 'Files and database are not sync.',
	'message_upload_nb_modifies' => 'You modified @nb@ language strings.',
	'module_deja_importe' => 'The module "@module@" has already been imported.',
	'moduletitre' => 'Modules available',

	// N
	'nb_item_langue_en_trop' => '1 item is too much in the language "@langue_longue@" (@langue@).',
	'nb_item_langue_inexistant' => '1 item doesn\'t exist in the language "@langue_longue@" (@langue@).',
	'nb_item_langue_mere' => 'The main language of this module includes 1 item.',
	'nb_items_langue_cible' => 'The target language "@langue@" includes @nb@ items defined in the parent language.',
	'nb_items_langue_en_trop' => '@nb@ items are too much in the language "@langue_longue@" (@langue@).',
	'nb_items_langue_inexistants' => '@nb@ items don\'t exist in the language "@langue_longue@" (@langue@).',
	'nb_items_langue_mere' => 'The main language of this module includes @nb@ items.',
	'notice_affichage_limite' => 'Only @nb@ untranslated strings are displayed.',
	'notice_aucun_module_favori_priorite' => 'No module of priority "@priorite@" correspond.',

	// R
	'readme' => 'This plugin allows you to manage the language files',

	// S
	'str_status_modif' => 'Edited (MODIF)',
	'str_status_new' => 'New (NEW)',
	'str_status_traduit' => 'Translated',

	// T
	'texte_contacter_admin' => 'Contact an administrator if you wish to participate.',
	'texte_erreur' => 'ERROR',
	'texte_erreur_acces' => '<b>Warning: </b>unable to write to the file <tt>@fichier_lang@</tt>. Check access rights.',
	'texte_existe_deja' => ' already exists.',
	'texte_explication_langue_cible' => 'For the target language, you must choose if you are working towards a language that already exists, or if you create a new language.',
	'texte_export_impossible' => 'Unable to export the file. Check write permissions on the file @cible@',
	'texte_filtre' => 'Filter (search)',
	'texte_inscription_ou_login' => 'You must register on the site or login to access the translation.',
	'texte_interface' => 'Translation interface:',
	'texte_interface2' => 'Translation interface',
	'texte_langue' => 'Language:',
	'texte_langue_cible' => 'the target language which is the language you are translating;',
	'texte_langue_origine' => 'the source language as your model (favor the mother language if you can);',
	'texte_langues_differentes' => 'The target and origin language must be different.',
	'texte_modifier' => 'Edit',
	'texte_module' => 'the language module to translate;',
	'texte_module_traduire' => 'The module to translate: ',
	'texte_non_traduit' => 'not translated',
	'texte_operation_impossible' => 'Impossible operation . When the box \'select all\' is checked,<br /> you have to choose \'View\' operations.',
	'texte_pas_autoriser_traduire' => 'You do not have permission to access the translations.',
	'texte_pas_de_reponse' => '... no response',
	'texte_recapitulatif' => 'Summary of the translations',
	'texte_restauration_impossible' => 'impossible to restore the file',
	'texte_sauvegarde' => 'Translation interface, Save / Restore file',
	'texte_sauvegarde_courant' => 'Backup copy of the current file:',
	'texte_sauvegarde_impossible' => 'Unable to backup the file',
	'texte_sauvegarder' => 'Save',
	'texte_selection_langue' => 'To view a translated language file / currently being translated, please select the language:',
	'texte_selectionner' => 'To begin the translation work, you must choose:',
	'texte_selectionner_version' => 'Choose the version of the file, then click the button below.',
	'texte_seul_admin' => 'Only an administrator account can access this step.',
	'texte_total_chaine' => 'Number of strings:',
	'texte_total_chaine_conflit' => 'Number of unused strings:',
	'texte_total_chaine_modifie' => 'Number of strings to submit updates:',
	'texte_total_chaine_non_traduite' => 'Number of untranslated strings:',
	'texte_total_chaine_traduite' => 'Number of translated strings:',
	'texte_tout_selectionner' => 'Select all',
	'texte_type_operation' => 'Operation type',
	'texte_voir_bilan' => 'See the <a href="@url@" class="spip_in">state of translations</a>.',
	'tfoot_total' => 'Total',
	'th_avancement' => 'Progression',
	'th_comm' => 'Comment',
	'th_date' => 'Date',
	'th_items_modifs' => 'Modified items',
	'th_items_new' => 'New items',
	'th_items_traduits' => 'Translated items',
	'th_langue' => 'Language',
	'th_langue_mere' => 'Parent language',
	'th_langue_origine' => 'Text in the source language',
	'th_langue_voulue' => 'Translation in "@lang@"',
	'th_module' => 'Module',
	'th_status' => 'Status',
	'th_total_items_module' => 'Total number of items',
	'th_traduction' => 'Translation',
	'th_traduction_voulue' => 'Translation in "@lang@"',
	'titre_bilan' => 'Review of translations',
	'titre_bilan_langue' => 'State of translation of the language "@lang@" ',
	'titre_bilan_module' => 'State of translation of the module "@module@"',
	'titre_changer_langue_selection' => 'Change the selected language',
	'titre_changer_langues_affichees' => 'Change the displayed languages',
	'titre_commentaires_chaines' => 'Comments on this string',
	'titre_form_import_step_1' => 'Step 1 : upload your file',
	'titre_form_import_step_2' => 'Step 2 : check your modifications',
	'titre_inscription' => 'Registration',
	'titre_logo_tradlang_module' => 'Module\'s logo',
	'titre_modifications_chaines' => 'Recent changes in this string',
	'titre_modifier' => 'Edit',
	'titre_page_auteurs' => 'Contributors list',
	'titre_page_configurer_tradlang' => 'Setup of the Trad-lang plugin',
	'titre_page_tradlang_module' => 'Module #@id@ : @module@',
	'titre_profil_auteur' => 'Edit your profile',
	'titre_profil_autre' => 'Edit their profile',
	'titre_recherche_tradlang' => 'Language strings
	',
	'titre_revisions_ses' => 'Their contributions',
	'titre_revisions_sommaire' => 'Recent changes',
	'titre_revisions_vos' => 'Your contributions',
	'titre_stats_ses' => 'Her/His statistics',
	'titre_stats_trads_journalieres' => 'Number of daily revisions',
	'titre_stats_trads_mensuelles' => 'Number of monthly revisions',
	'titre_stats_vos' => 'Your statistics',
	'titre_tradlang' => 'Trad-lang',
	'titre_tradlang_chaines' => 'Language string',
	'titre_tradlang_module' => 'Trad-lang language modules',
	'titre_tradlang_modules' => 'Language modules',
	'titre_tradlang_non_traduit' => '1 untranslated language string',
	'titre_tradlang_non_traduits' => '@nb@ untranslated language strings',
	'titre_traduction' => 'Translations',
	'titre_traduction_chaine_de_vers' => 'Translation of the string "@chaine@" of the module "@module@" from <abbr title="@lang_orig_long@">@lang_orig@</abbr> to <abbr title="@lang_cible_long@">@lang_cible@</abbr>',
	'titre_traduction_de' => 'Translation of',
	'titre_traduction_module_de_vers' => 'Translation of the module "@module@" from <abbr title="@lang_orig_long@">@lang_orig@</abbr> to <abbr title="@lang_cible_long@">@lang_cible@</abbr>',
	'titre_traduire' => 'Translate',
	'tradlang' => 'Trad-Lang',
	'traduction' => 'Translation @lang@',
	'traductions' => 'Translations'
);

?>
