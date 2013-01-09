<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de http://trad.spip.net/tradlang_module/langonet?lang_cible=en
// ** ne pas modifier le fichier **

if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// B
	'bouton_corriger' => 'Retrieve the corrections',
	'bouton_generer' => 'Generate',
	'bouton_langonet' => 'LangOnet',
	'bouton_lister' => 'Display',
	'bouton_rechercher' => 'Search',
	'bouton_verifier' => 'Verify',
	'bulle_afficher_fichier_lang' => 'Display the language file generated on @date@',
	'bulle_afficher_fichier_log' => 'Display the log of @date@',
	'bulle_corriger' => 'Download the corrected language file',
	'bulle_telecharger_fichier_lang' => 'Download the language file generated on @date@',
	'bulle_telecharger_fichier_log' => 'Download the log file of @date@',

	// E
	'entete_log_avertissement_nonmais' => 'WARNING: items not belonging to this module',
	'entete_log_avertissement_peutetre_definition' => 'WARNING: items maybe undefined',
	'entete_log_avertissement_peutetre_utilisation' => 'WARNING: items maybe not used',
	'entete_log_date_creation' => 'File generated on @log_date_jour@ at @log_date_heure@.',
	'entete_log_erreur_definition' => 'ERROR : undefined module items',
	'entete_log_erreur_definition_nonmais' => 'ERROR : undefined items of other modules',
	'entete_log_erreur_fonction_l' => 'ERROR : usage cases of the _L() function',
	'entete_log_erreur_utilisation' => 'ERROR : unused items',

	// I
	'info_arborescence_scannee' => 'Choose the root folder for which all subdirectories will be scanned',
	'info_bloc_langues_generees' => 'Click on one of the links below to download one of the generated language files.',
	'info_bloc_logs_definition' => 'Click on one of the links below to download the latest log file of the verification of missing definitions in a language file.',
	'info_bloc_logs_fonction_l' => 'Click on one of the links below to download the latest log file for the verification of uses of _L() in a given file tree.',
	'info_bloc_logs_utilisation' => 'Click on one of the links below to download the latest log file for the verification of obsolete definitions in a language file.',
	'info_chemin_langue' => 'Folder containing the language file (e.g. <em>plugins/rainette/lang/</em>, or <em>ecrire/lang/</em>)',
	'info_fichier_liste' => 'Choose the language file from those available on the site for which you want to display the items.',
	'info_fichier_verifie' => 'Choose the language file you wish to verify from those available on the site.',
	'info_generer' => 'This option allows you to generate, from a source language, the language file for a module in a target language. If the target file already exists, its content will be reused when creating the new file.',
	'info_langue' => 'Language code (for example: <em>fr</em>, <em>en</em>, <em>es</em>...)',
	'info_lister' => 'This option allows you to display the items in a language file listed in alphabetical order.',
	'info_mode' => 'Corresponds to the string which will be inserted at creation of a new target language item.',
	'info_module' => 'Corresponds to the language file prefix without the language code (eg. <em>rainette</em> for the plugin by that name, or <em>ecrire</em> for SPIP)',
	'info_pattern_item_cherche' => 'Enter a string corresponding entirely or partially to a language item shortcut. The search is allways case insensitive.',
	'info_pattern_texte_cherche' => 'Enter a string corresponding entirely or partially to a French translation of a language item. The search is allways case insensitive.',
	'info_rechercher_item' => 'This option allows you to search for language items in all the language files available on the site. For performance reasons, only French language files will be scanned.',
	'info_rechercher_texte' => 'This option allows you to search for language items by their French translation in the SPIP language files <em>ecrire_fr</em>, <em>public_fr</em> and <em>spip_fr</em>. The goal is to check whether a text already exists in SPIP before creating it yourself.',
	'info_table' => 'You can consult below the alphabetical list of language items of the file "<em>@langue@</em>" (@total@). Each block displays items with the same initial, the bold shortcut and the text displayed next. Hover over an initial to display the corresponding list.',
	'info_verifier' => 'This option allows you, on one hand, to check the language files for a given module in two complementary angles. It\'s possible, whether checking if language items used in a group of files (a plugin, for example) are not defined in the suitable language file, whether some defined language items are no longer used. <br />On the other hand, it is possible to list and correct all uses of the function _L() in PHP files in a given tree.',

	// L
	'label_arborescence_scannee' => 'Directory tree to be scanned',
	'label_avertissement' => 'Warnings',
	'label_chemin_langue' => 'Location of the language file',
	'label_correspondance' => 'Match type',
	'label_correspondance_commence' => 'Begins by',
	'label_correspondance_contient' => 'Contents',
	'label_correspondance_egal' => 'Equal to',
	'label_erreur' => 'Errors',
	'label_fichier_liste' => 'Language file',
	'label_fichier_verifie' => 'Language to verify',
	'label_langue_cible' => 'Target language',
	'label_langue_source' => 'Source language',
	'label_mode' => 'Creation mode of new items',
	'label_module' => 'Module',
	'label_pattern' => 'String to search',
	'label_verification' => 'Type of verification',
	'label_verification_definition' => 'Detection of missing definitions',
	'label_verification_fonction_l' => 'Detection when using the function _L()',
	'label_verification_utilisation' => 'Detection of obsolete definitions',
	'legende_resultats' => 'Verification results',
	'legende_table' => 'List of items of the selected language file',
	'legende_trouves' => 'List of found items (@total@)',

	// M
	'message_nok_aucun_fichier_log' => 'No log file available for download',
	'message_nok_aucune_langue_generee' => 'No language file generated is available for download',
	'message_nok_champ_obligatoire' => 'This field is required',
	'message_nok_ecriture_fichier' => 'Le fichier de langue «<em>@langue@</em>» du module «<em>@module@</em>» n\'a pas été créé car une erreur s\'est produite lors de son écriture !', # NEW
	'message_nok_fichier_langue' => 'La génération a échoué car le fichier de langue «<em>@langue@</em>» du module «<em>@module@</em>» est introuvable dans le répertoire «<em>@dossier@</em>» !', # NEW
	'message_nok_fichier_log' => 'The log file containing the results of the verification could not be created!',
	'message_nok_fichier_script' => 'Le fichier de script contenant les commandes de remplacement des fonctions _L par _T n\'a pas pu être créé!', # NEW
	'message_nok_item_trouve' => 'No language item matches the search!',
	'message_ok_definis_incertains_0' => 'No language item is used in a complex environment, eg, _T(\'@module@:item_\'.$variable).',
	'message_ok_definis_incertains_1' => 'L\'item de langue ci-dessous est utilisé dans un contexte complexe et pourrait être non défini dans le fichier de langue  «<em>@langue@</em>». Nous vous invitons à le vérifier :', # NEW
	'message_ok_definis_incertains_n' => 'Les @nberr@ items de langue ci-dessous sont utilisés dans un contexte complexe et pourraient être non définis dans le fichier de langue  «<em>@langue@</em>». Nous vous invitons à les vérifier un par un :', # NEW
	'message_ok_fichier_genere' => 'Le fichier de langue «<em>@langue@</em>» du module «<em>@module@</em>» a été généré correctement.<br />Vous pouvez récupérer le fichier «<em>@fichier@</em>».', # NEW
	'message_ok_fichier_log' => 'La vérification s\'est correctement déroulée. Vous pouvez consultez les résultats plus bas dans le formulaire.<br />Le fichier «<em>@log_fichier@</em>» a été créé pour sauvegarder ces résultats.', # NEW
	'message_ok_fichier_log_script' => 'La vérification s\'est correctement déroulée. Vous pouvez consultez les résultats plus bas dans le formulaire.<br />Le fichier «<em>@log_fichier@</em>» a été créé pour sauvegarder ces résultats ainsi que le fichier des commandes de remplacement _L en _T, «<em>@script@</em>».', # NEW
	'message_ok_fonction_l_0' => 'Aucun cas d\'utilisation de la fonction _L() n\'a été détecté dans les fichiers PHP du répertoire «<em>@ou_fichier@</em>».', # NEW
	'message_ok_fonction_l_1' => 'Un seul cas d\'utilisation de la fonction _L() a été détecté dans les fichiers PHP du répertoire «<em>@ou_fichier@</em>» :', # NEW
	'message_ok_fonction_l_n' => '@nberr@ cases of use of the function _L() were detected in PHP files from the directory "<em>@ou_fichier@</em>":',
	'message_ok_item_trouve' => 'The search for the string @pattern@ is successful.',
	'message_ok_item_trouve_commence_1' => 'The language item below begins by the search string:',
	'message_ok_item_trouve_commence_n' => 'The @sous_total@ language items below all begin by the search string:',
	'message_ok_item_trouve_contient_1' => 'The language item below contains the searched string:',
	'message_ok_item_trouve_contient_n' => 'The @sous_total@ items below contain all the searched string:',
	'message_ok_item_trouve_egal_1' => 'The item below correspond exactly to the search string:',
	'message_ok_item_trouve_egal_n' => 'The @sous_total@ items below correspond exactly to the search string:',
	'message_ok_non_definis_0' => 'Tous les items de langue du module «<em>@module@</em>» utilisés dans les fichiers du répertoire «<em>@ou_fichier@</em>» sont bien définis dans le fichier de langue «<em>@langue@</em>».', # NEW
	'message_ok_non_definis_1' => 'L\'item de langue du module «<em>@module@</em>» affiché ci-dessous est utilisé dans des fichiers du répertoire «<em>@ou_fichier@</em>» mais n\'est pas défini dans le fichier de langue «<em>@langue@</em>» :', # NEW
	'message_ok_non_definis_n' => 'Les @nberr@ items de langue du module «<em>@module@</em>» affichés ci-dessous sont utilisés dans des fichiers du répertoire «<em>@ou_fichier@</em>» mais ne sont pas définis dans le fichier de langue «<em>@langue@</em>» :', # NEW
	'message_ok_non_utilises_0' => 'Tous les items de langue définis  dans le fichier de langue «<em>@langue@</em>» sont bien utilisés dans les fichiers du répertoire «<em>@ou_fichier@</em>».', # NEW
	'message_ok_non_utilises_0s' => 'All language items defined in the language file "<em>@langue@</em>" are correctly used in the files of the folders "<em>@ou_fichier@</em>".',
	'message_ok_non_utilises_1' => 'L\'item de langue ci-dessous est bien défini dans le fichier de langue «<em>@langue@</em>», mais n\'est pas utilisé dans les fichiers du répertoire «<em>@ou_fichier@</em>» :', # NEW
	'message_ok_non_utilises_1s' => 'L\'item de langue ci-dessous est bien défini dans le fichier de langue «<em>@langue@</em>», mais n\'est pas utilisé dans les fichiers des répertoires «<em>@ou_fichier@</em>» :', # NEW
	'message_ok_non_utilises_n' => 'Les @nberr@ items de langue ci-dessous sont bien définis dans le fichier de langue «<em>@langue@</em>», mais ne sont pas utilisés dans les fichiers du répertoire «<em>@ou_fichier@</em>» :', # NEW
	'message_ok_non_utilises_ns' => 'Les @nberr@ items de langue ci-dessous sont bien définis dans le fichier de langue «<em>@langue@</em>», mais ne sont pas utilisés dans les fichiers des répertoires «<em>@ou_fichier@</em>» :', # NEW
	'message_ok_nonmais_definis_0' => 'Files in the directory "<em>@ou_fichier@</em>" do not use any language item correctly defined in an other module than "<em>@module@</em>".',
	'message_ok_nonmais_definis_1' => 'L\'item de langue ci-dessous est utilisé correctement dans des fichiers du répertoire «<em>@ou_fichier@</em>» mais est défini dans un autre module que «<em>@module@</em>». Nous vous invitons à le vérifier :', # NEW
	'message_ok_nonmais_definis_n' => 'Les @nberr@ items de langue ci-dessous sont utilisés correctement dans des fichiers du répertoire «<em>@ou_fichier@</em>» mais sont définis dans un autre module que «<em>@module@</em>». Nous vous invitons à les vérifier un par un :', # NEW
	'message_ok_nonmaisnok_definis_0' => 'Les fichiers du répertoire «<em>@ou_fichier@</em>» n\'utilisent aucun item de langue incorrectement défini dans un autre module que «<em>@module@</em>».', # NEW
	'message_ok_nonmaisnok_definis_1' => 'L\'item de langue ci-dessous est utilisé dans des fichiers du répertoire «<em>@ou_fichier@</em>» mais pas comme un item du module «<em>@module@</em>». Etant donné qu\'il n\'est pas défini dans son module de rattachement, nous vous invitons à le vérifier :', # NEW
	'message_ok_nonmaisnok_definis_n' => 'Les @nberr@ items de langue ci-dessous sont utilisés dans des fichiers du répertoire «<em>@ou_fichier@</em>» mais pas comme des items du module «<em>@module@</em>». Etant donné qu\'ils ne sont pas définis dans leur module de rattachement, nous vous invitons à les vérifier un par un :', # NEW
	'message_ok_table_creee' => 'The table of items od the language file @langue@ has been correctly created',
	'message_ok_utilises_incertains_0' => 'No language item is used in a complex context (for example:  _T(\'@module@:item_\'.$variable)).',
	'message_ok_utilises_incertains_1' => 'The item language below may be used in a complex context. We invite you to check it:',
	'message_ok_utilises_incertains_n' => 'The @nberr@ language items belowmay be used in a complex context. We invite you to check them one by one:',

	// O
	'onglet_generer' => 'Generate a language',
	'onglet_lister' => 'Display a language',
	'onglet_rechercher' => 'Search for an item',
	'onglet_verifier' => 'Check a language',
	'option_aucun_dossier' => 'no directory tree selected',
	'option_aucun_fichier' => 'no language selected',
	'option_mode_index' => 'Item of the source language',
	'option_mode_new' => ' &lt;NEW&gt; tag only',
	'option_mode_new_index' => 'Item de la langue source précédé de &lt;NEW&gt;', # NEW
	'option_mode_new_valeur' => 'String in the source language preceded by &lt;NEW&gt;',
	'option_mode_pas_item' => 'Do not create the item',
	'option_mode_valeur' => 'String in the source language',
	'option_mode_vide' => 'An empty string',

	// T
	'test' => 'TEST: this language item is used for the search of shortcuts and is equal to test.',
	'test_item_1_variable' => 'TEST : Cet item de langue est bien défini dans le fichier de langue, mais est utilisé sous forme "complexe" dans les fichiers du répertoire scanné.', # NEW
	'test_item_2_variable' => 'TEST: this language item is correctly defined in the language file, but it\'s used in a "complex" formulation in the files of the scanned directory.',
	'test_item_non_utilise_1' => 'TEST: this language item is correctly defined in the language file (), but is not used in the scanned folder ().',
	'test_item_non_utilise_2' => 'TEST: This language item is correctly defined in the language file (), but is not used in the files of the scanned folder ().',
	'texte_item_defini_ou' => '<em>defined in:</em>',
	'texte_item_mal_defini' => '<em>but is not defined in the good module:</em>',
	'texte_item_non_defini' => '<em>but never defined!</em>',
	'texte_item_utilise_ou' => '<em>used in:</em>',
	'titre_bloc_langues_generees' => 'Language files',
	'titre_bloc_logs_definition' => 'Missing definitions',
	'titre_bloc_logs_fonction_l' => 'Uses of _L()',
	'titre_bloc_logs_utilisation' => 'Obsolete definitions',
	'titre_form_generer' => 'Creation of language files',
	'titre_form_lister' => 'Display of language files',
	'titre_form_rechercher_item' => 'Search of shortcuts in the language files',
	'titre_form_rechercher_texte' => 'Search of texts in the SPIP language files',
	'titre_form_verifier' => 'Verification of the language files',
	'titre_page' => 'LangOnet',
	'titre_page_navigateur' => 'LangOnet',

	// Z
	'z_test' => 'TEST: this language item is used to search shortcuts and contains test.'
);

?>
