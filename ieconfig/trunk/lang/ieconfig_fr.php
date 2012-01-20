<?php

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(
	
	'explication_ieconfig_import_fichier' => 'Fichier de configuration au format YAML.',
	'explication_ieconfig_import_local' => 'Liste des configurations détectées dans un sous-répertoire <i>ieconfig/</i> ou dans le répertoire <i>tmp/ieconfig/</i>.',
	'item_sauvegarder' => 'Sauvegarder le fichier',
	'item_telecharger' => 'Télécharger le fichier',
	'label_elements_a_exporter' => 'éléments à exporter :',
	'label_exporter' => 'Exporter ?',
	'label_ieconfig_export' => 'Options générales d\'export',
	'label_ieconfig_export_choix' => 'Que souhaitez-vous ?',
	'label_ieconfig_export_description' => 'Description :',
	'label_ieconfig_export_nom' => 'Nom de l\'export :',
	'label_ieconfig_import_choix_fichier' => 'Choix du fichier à importer',
	'label_ieconfig_import_fichier' => 'Fichier à importer :',
	'label_ieconfig_import_local' => 'Configurations disponibles localement :',
	'label_importer' => 'Importer ?',
	'message_erreur_export' => 'Une erreur s\'est produite lors de l\'enregistrement du fichier <i>@filename@</i> dans le répertoire <i>tmp/ieconfig/</i>.',
	'message_erreur_fichier_import_manquant' => 'Vous devez spécifier un fichier de configuration à importer.',
	'message_ok_export' => 'Le fichier <i>@filename@</i> a été sauvegardé dans le répertoire <i>tmp/ieconfig/</i>.',
	'message_ok_import' => 'La configuration a été correctement importée.',
	'texte_configuration_identique' => 'La configuration contenue dans ce fichier est identique à votre configuration actuelle.',
	'texte_description' => 'Description :',
	'texte_ieconfig_export_explication' => 'Vous pouvez sauvegarder localement votre export au format YAML dans le répertoire <i>tmp/ieconfig/</i> ou bien le télécharger.',
	'texte_importer_configuration' => 'Cette option vous permet de restaurer une sauvegarde précédemment effectuée votre configuration ou bien importer une configuration fournie par un plugin. Soyez prudent avec cette fonctionnalité : <strong>les modifications, pertes éventuelles, sont irréversibles</strong>.',
	'texte_nom' => 'Nom :',
	'texte_plugins_manquants' => 'Ce fichier contient des configurations pour les plugins suivants qui ne sont pas activés sur votre site : <i>@plugins@</i>. Ces configurations ne seront donc pas importées.',
	'texte_spip_contenu_export_explication' => 'Vous pouvez exporter la configuration des contenus du site que vous avez défini dans la <a href="./?exec=config_contenu">Configuration du site</a>.',
	'texte_spip_contenu_import_explication' => 'Ce fichier contient des valeurs de configuration pour les contenus de votre site. Si vous l\'importez, les paramètres suivants seront modifiés :',
	'texte_spip_interactivite_export_explication' => 'Vous pouvez exporter les paramètres définis sous l\'onglet <i><a href="./?exec=config_contenu">Interactivité</a></i> dans la Configuration du site.',
	'texte_spip_interactivite_import_explication' => 'Ce fichier contient des valeurs pour l\'onglet <i>Interactivité</i> dans la configuration du site. Si vous l\'importez, les paramètres suivants seront modifiés :',
	'titre_export' => 'Exporter la configuration',
	'titre_ieconfig' => 'Importeur / Exporteur de configuration',
	'titre_import' => 'Importer une configuration',

);

?>
