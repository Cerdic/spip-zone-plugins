<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// Fichier source, a modifier dans svn://zone.spip.org/spip-zone/_plugins_/_stable_/cfg/lang/
if (!defined("_ECRIRE_INC_VERSION")) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	'installed' => 'SPIP-Listes-Cleaner has been installed! ',
	'uninstalled' => 'SPIP-Listes-Cleaner has been uninstalled! ',

	'spip_listes_cleaner_name' => 'Spip Lists Cleaner',
	'spip_listes_cleaner_dsc' => 'Configuration of Spip Lists Cleaner',
	'config_email_server' => 'Mail server configuration:',
	'server_address' => 'Server address:',
	'server_type' => 'Server type:',
	'server_security' => 'Security:',
	'server_security_option' => 'Security option:',
	'server_mailbox' => 'Mailbox:',
	'server_username' => 'Username:',
	'server_password' => 'Password:',
	
	'server_address_help' => 'example: pop.myserver.com:110',
	'server_mailbox_help' => 'usually: \'INBOX\'',
	
	'options' => 'Options :',
	'option_delete_bounce' => 'Delete the bounces mails from the mail server:',
	'option_delete_bounce_yes' => 'Yes',
	'option_delete_bounce_no' => 'No',
	'option_delete_row' => 'Delete method for the autors:',
	'option_delete_row_definitive' => 'Full and definitive',
	'option_delete_row_5poubelle' => 'Mark the autors "in the dustbin"',
	
	'statistics' => 'Statistics (number of deleted emails):',
	'nb_deleted_mails' => 'In total:',
	'nb_deleted_mails_last_export' => 'Since the last deleted export:',

	'export' => 'Export :',
	'export_download' => 'Export all deleted emails (CSV):',
	'export_reset' => 'Delete all mails from the export:',
	'export_download_button' => 'Export',
	'export_reset_button' => 'Delete',
);

?>
