<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// Fichier source, a modifier dans svn://zone.spip.org/spip-zone/_plugins_/ieconfig/trunk/lang/
if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// E
	'explication_ieconfig_import_fichier' => 'Fichier de configuration au format YAML.',
	'explication_ieconfig_import_local' => 'Liste des configurations détectées dans un sous-répertoire <i>ieconfig/</i> ou dans le répertoire <i>tmp/ieconfig/</i>.',

	// I
	'item_sauvegarder' => 'Sauvegarder le fichier',
	'item_telecharger' => 'Télécharger le fichier',

	// L
	'label_configurations_a_exporter' => 'Configurations à exporter',
	'label_exporter' => 'Exporter la configuration ?',
	'label_ieconfig_export' => 'Options générales d’export',
	'label_ieconfig_export_choix' => 'Que souhaitez-vous ?',
	'label_ieconfig_export_description' => 'Description :',
	'label_ieconfig_export_nom' => 'Nom de l’export :',
	'label_ieconfig_import_choix_fichier' => 'Choix du fichier à importer',
	'label_ieconfig_import_fichier' => 'Fichier à importer :',
	'label_ieconfig_import_local' => 'Configurations disponibles localement :',
	'label_importer' => 'Importer ?',
	'label_importer_metas' => 'Éléments à importer',

	// M
	'message_erreur_export' => 'Une erreur s’est produite lors de l’enregistrement du fichier <i>@filename@</i> dans le répertoire <i>tmp/ieconfig/</i>.',
	'message_erreur_fichier_import_manquant' => 'Vous devez spécifier un fichier de configuration à importer.',
	'message_ok_export' => 'Le fichier <i>@filename@</i> a été sauvegardé dans le répertoire <i>tmp/ieconfig/</i>.',
	'message_ok_import' => 'La configuration a été correctement importée.',

	// T
	'texte_configuration_identique' => 'La configuration contenue dans ce fichier est identique à votre configuration actuelle.',
	'texte_description' => 'Description :',
	'texte_ieconfig_export_explication' => 'Vous pouvez sauvegarder localement votre export au format YAML dans le répertoire <i>tmp/ieconfig/</i> ou bien le télécharger.',
	'texte_importer_configuration' => 'Cette option vous permet de restaurer une sauvegarde précédemment effectuée de votre configuration ou bien importer une configuration fournie par un plugin. Soyez prudent avec cette fonctionnalité : <strong>les modifications, pertes éventuelles, sont irréversibles</strong>.',
	'texte_nom' => 'Nom :',
	'texte_plugins_manquants' => 'Ce fichier contient des configurations pour les plugins suivants qui ne sont pas activés sur votre site : <i>@plugins@</i>. Ces configurations ne seront donc pas importées.',
	'titre_export' => 'Exporter la configuration',
	'titre_ieconfig' => 'Import / Export de configurations',
	'titre_import' => 'Importer une configuration'
);

?>
