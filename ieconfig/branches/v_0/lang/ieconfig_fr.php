<?php

// S&eacute;curit&eacute;
if (!defined("_ECRIRE_INC_VERSION")) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(
	
	'explication_ieconfig_import_fichier' => 'Fichier de configuration au format YAML.',
	'explication_ieconfig_import_local' => 'Liste des configurations d&eacute;tect&eacute;es dans un sous-r&eacute;pertoire <i>ieconfig/</i> ou dans le r&eacute;pertoire <i>tmp/ieconfig/</i>.',
	'item_sauvegarder' => 'Sauvegarder le fichier',
	'item_telecharger' => 'T&eacute;l&eacute;charger le fichier',
	'label_elements_a_exporter' => '&Eacute;l&eacute;ments &agrave; exporter&nbsp;:',
	'label_exporter' => 'Exporter&nbsp;?',
	'label_ieconfig_export' => 'Options g&eacute;n&eacute;rales d\'export',
	'label_ieconfig_export_choix' => 'Que souhaitez-vous&nbsp;?',
	'label_ieconfig_export_description' => 'Description&nbsp;:',
	'label_ieconfig_export_nom' => 'Nom de l\'export&nbsp;:',
	'label_ieconfig_import_choix_fichier' => 'Choix du fichier &agrave importer',
	'label_ieconfig_import_fichier' => 'Fichier &agrave; importer&nbsp;:',
	'label_ieconfig_import_local' => 'Configurations disponibles localement&nbsp;:',
	'label_importer' => 'Importer&nbsp;?',
	'message_erreur_export' => 'Une erreur s\'est produite lors de l\'enregistrement du fichier <i>@filename@</i> dans le r&eacute;pertoire <i>tmp/ieconfig/</i>.',
	'message_erreur_fichier_import_manquant' => 'Vous devez sp&eacute;cifier un fichier de configuration &agrave; importer.',
	'message_ok_export' => 'Le fichier <i>@filename@</i> a &eacute;t&eacute; sauvegard&eacute; dans le r&eacute;pertoire <i>tmp/ieconfig/</i>.',
	'message_ok_import' => 'La configuration a &eacute;t&eacute; correctement import&eacute;e.',
	'texte_configuration_identique' => 'La configuration contenue dans ce fichier est identique &agrave; votre configuration actuelle.',
	'texte_description' => 'Description&nbsp;:',
	'texte_ieconfig_export_explication' => 'Vous pouvez sauvegarder localement votre export au format YAML dans le r&eacute;pertoire <i>tmp/ieconfig/</i> ou bien le t&eacute;l&eacute;charger.',
	'texte_nom' => 'Nom&nbsp;:',
	'texte_plugins_manquants' => 'Ce fichier contient des configurations pour les plugins suivants qui ne sont pas activ&eacute;s sur votre site&nbsp;: <i>@plugins@</i>. Ces configurations ne seront donc pas import&eacute;es.',
	'texte_spip_contenu_export_explication' => 'Vous pouvez exporter la configuration des contenus du site que vous avez d&eacute;fini dans la <a href="./?exec=config_contenu">Configuration du site</a>.',
	'texte_spip_contenu_import_explication' => 'Ce fichier contient des valeurs de configuration pour les contenus de votre site. Si vous l\'importez, les param&egrave;tres suivants seront modifi&eacute;s&nbsp;:',
	'texte_spip_interactivite_export_explication' => 'Vous pouvez exporter les param&egrave;tres d&eacute;finis sous l\'onglet <i><a href="./?exec=config_contenu">Interactivit&eacute;</a></i> dans la Configuration du site.',
	'texte_spip_interactivite_import_explication' => 'Ce fichier contient des valeurs pour l\'onglet <i>Interactivit&eacute;</i> dans la configuration du site. Si vous l\'importez, les param&egrave;tres suivants seront modifi&eacute;s&nbsp;:',
	'titre_export' => 'Exporter la configuration',
	'titre_ieconfig' => 'Importeur / Exporteur de configuration',
	'titre_import' => 'Importer une configuration',

);

?>