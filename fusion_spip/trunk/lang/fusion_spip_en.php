<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de http://trad.spip.net/tradlang_module/fusion_spip?lang_cible=en
// ** ne pas modifier le fichier **

if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// B
	'base' => 'Source site',
	'base_desc' => 'The database of the  source site must be <a href="http:///../ecrire/?exec=admin_tech">declared as an external database</a>.<br/>
	
The database of the source site must be in the same version as the one of the host site',
	'bouton_importer' => 'Start to merge',
	'bouton_supprimer' => 'Delete the merge',

	// C
	'confirme_warning' => 'Confirm the merge of the databases?',

	// D
	'dossier_existe_pas' => 'The directory @dossier@ doesn’t exist',
	'dossier_pas_lisible' => 'The directory @dossier@ cannot be read',

	// E
	'erreur_versions' => 'The host site and the source site are not in the same database version:
		<br/>- host is in version @vhote@
		<br/>- source is in version @vsource@',
	'erreur_versions_impossible' => 'It is not possible to check the imported database (spip_meta table )',

	// I
	'img_dir' => 'Physical path of the documents',
	'img_dir_desc' => 'To copy the documents from the source site in the host site, indicate their physical path (absolute path on the hard disk, for example <code>/home/edgard/www/edgard_spip/IMG</code>). If the field is empty, no document will be imported, you will have to copy them manually.',

	// M
	'maj_base' => 'Update of the database',
	'manque_champs_source' => 'The fields "@diff@" are missing in the table "@table@" of the source database',
	'manque_table_source' => 'The table "@table@" is missing in the source database',
	'message_img_dir_nok' => 'Please precise the path',
	'message_import_nok' => 'Error during the merge',
	'message_import_ok' => 'Fusion ended<br>detailed log: <code>tmp/log/fusion_spip_fusion_spip*.log</code><br><br>Here is a summary of the imported objects:<br>',
	'message_suppression_ok' => 'Deleted objects',

	// R
	'referers' => 'Do not process referrers (inbound links)',

	// S
	'secteur' => 'Sector',
	'secteur_desc' => 'To import the source site  in a sector, otherwise it will be imported at the root',
	'stats' => 'Do not process statistics',

	// T
	'titre_fusion_spip' => 'Spip Websites Fusion',
	'titre_fusion_spip_suppression' => 'Deletion'
);

?>
