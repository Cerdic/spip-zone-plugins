<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de http://trad.spip.org
// ** ne pas modifier le fichier **

if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// E
	'explication_ieconfig_import_fichier' => 'Súbor s nastaveniami vo formáte YAML.',
	'explication_ieconfig_import_local' => 'Liste des configurations détectées dans un sous-répertoire <i>ieconfig/</i> ou dans le répertoire <i>tmp/ieconfig/</i>.', # NEW

	// I
	'item_sauvegarder' => 'Zálohovať súbor',
	'item_telecharger' => 'Stiahnuť súbor',

	// L
	'label_configurations_a_exporter' => 'Configurations à exporter', # NEW
	'label_exporter' => 'Exportovať?', # MODIF
	'label_ieconfig_export' => 'Všeobecné možnosti exportu',
	'label_ieconfig_export_choix' => 'Čo chcete?',
	'label_ieconfig_export_description' => 'Popis:',
	'label_ieconfig_export_nom' => 'Názov exportu:',
	'label_ieconfig_import_choix_fichier' => 'Výber súboru na nahratie',
	'label_ieconfig_import_fichier' => 'Súbor na nahratie:',
	'label_ieconfig_import_local' => 'Lokálne dostupné nastavenia:',
	'label_importer' => 'Importovať?',

	// M
	'message_erreur_export' => 'Une erreur s\'est produite lors de l\'enregistrement du fichier <i>@filename@</i> dans le répertoire <i>tmp/ieconfig/</i>.', # NEW
	'message_erreur_fichier_import_manquant' => 'Musíte zadať súbor s nastaveniami, ktorý sa má nahrať.',
	'message_ok_export' => 'Súbor <i>@filename@</i> bol zálohovaný v priečinku <i>tmp/ieconfig/.</i>',
	'message_ok_import' => 'Nastavenia boli úspešne nahraté.',

	// T
	'texte_configuration_identique' => 'Nastavenia v tomto súbore sú rovnaké ako vaše aktuálne nastavenia.',
	'texte_description' => 'Popis:',
	'texte_ieconfig_export_explication' => 'Vous pouvez sauvegarder localement votre export au format YAML dans le répertoire <i>tmp/ieconfig/</i> ou bien le télécharger.', # NEW
	'texte_importer_configuration' => 'Cette option vous permet de restaurer une sauvegarde précédemment effectuée votre configuration ou bien importer une configuration fournie par un plugin. Soyez prudent avec cette fonctionnalité : <strong>les modifications, pertes éventuelles, sont irréversibles</strong>.', # NEW
	'texte_nom' => 'Názov:',
	'texte_plugins_manquants' => 'Ce fichier contient des configurations pour les plugins suivants qui ne sont pas activés sur votre site : <i>@plugins@</i>. Ces configurations ne seront donc pas importées.', # NEW
	'titre_export' => 'Exportovať nastavenia',
	'titre_ieconfig' => 'Nahrať/Exportovať nastavenia',
	'titre_import' => 'Nahrať nastavenia'
);

?>
