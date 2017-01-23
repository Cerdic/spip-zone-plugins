<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de https://trad.spip.net/tradlang_module/ieconfig?lang_cible=en
// ** ne pas modifier le fichier **

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// E
	'explication_ieconfig_import_fichier' => 'Configuration file in YAML format.',
	'explication_ieconfig_import_local' => 'List of configurations found in a subdirectory <i>ieconfig /</ i> or directory <i>tmp/ieconfig/</i>.',

	// I
	'item_sauvegarder' => 'Save the file',
	'item_telecharger' => 'Download the file',

	// L
	'label_configurations_a_exporter' => 'Configurations to export',
	'label_exporter' => 'Export the configuration ?',
	'label_ieconfig_export' => 'Export general options',
	'label_ieconfig_export_choix' => 'What do you want to do ?',
	'label_ieconfig_export_description' => 'Description :',
	'label_ieconfig_export_nom' => 'Export name :',
	'label_ieconfig_import_choix_fichier' => 'Choose the file to import',
	'label_ieconfig_import_fichier' => 'File to import :',
	'label_ieconfig_import_local' => 'Locally available configurations :',
	'label_importer' => 'Import ?',
	'label_importer_metas' => 'Elements to import',

	// M
	'message_erreur_export' => 'An error occurred while saving the file <i>@filename@</i> in the directory <i>tmp/ieconfig/</i>.',
	'message_erreur_fichier_import_manquant' => 'You must specify a configuration file to import.',
	'message_ok_export' => 'The file <i>@filename@</i> has been saved in the directory <i>tmp/ieconfig/</i>.',
	'message_ok_import' => 'The configuration was successfully imported.',

	// T
	'texte_configuration_identique' => 'The configuration in this file is the same as your current configuration.',
	'texte_description' => 'Description :',
	'texte_ieconfig_export_explication' => 'You can save locally your export  in YAML format in the directory <i>tmp/ieconfig/</i> or download it.',
	'texte_importer_configuration' => 'This option allows you to restore a previously made backup of your configuration or import a configuration provided by a plugin. Be careful with this feature: <strong>changes, potential losses are irreversible</ strong>.',
	'texte_nom' => 'Name :',
	'texte_plugins_manquants' => 'This file contains configurations for the following plugins which are not enabled on your site: <i>@plugins@</ i>. These settings will not be imported.',
	'titre_export' => 'Export a configuration',
	'titre_ieconfig' => 'Configurations Import / Export',
	'titre_import' => 'Import a configuration'
);
