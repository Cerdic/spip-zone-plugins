<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de http://trad.spip.net/tradlang_module/mes_fichiers?lang_cible=en
// ** ne pas modifier le fichier **

if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// B
	'bouton_configurer' => 'My files',
	'bouton_mes_fichiers' => 'Backup my files',
	'bouton_sauver' => 'Backup',
	'bouton_tout_cocher' => 'Select all',
	'bouton_tout_decocher' => 'Deselect all',
	'bouton_voir' => 'Show',
	'bulle_bouton_voir' => 'Visualise archive’s content',

	// C
	'colonne_nom' => 'Filename',

	// E
	'erreur_aucun_fichier_sauver' => 'No file to backup',
	'erreur_repertoire_trop_grand' => 'This folder exceeds the limit of @taille_max@ MB and won’t be saved.',
	'explication_cfg_duree_sauvegarde' => 'Enter the preservation period of the backups (in days)',
	'explication_cfg_frequence' => 'Enter backup frequency (in days)',
	'explication_cfg_notif_mail' => 'Enter email addresses separated by comma ",". Webmaster address is always added to this list.',
	'explication_cfg_prefixe' => 'Enter prefix of the backup filename',
	'explication_cfg_taille_max_rep' => 'Enter maximum size of folders to backup (in MB)',

	// I
	'info_liste_a_sauver' => 'Files and folders ready for backup:',
	'info_sauver_1' => 'This option builds an archive file containing the customization datas of the site like the latest database dump, the templates, the images folder...',
	'info_sauver_2' => 'The archive file is created in <em>tmp/mes_fichiers/</em> with the filename <em>@prefixe@_aaaammjj_hhmmss.zip</em>.',
	'info_sauver_3' => 'Automatic backup option is on (frequency in days : @frequence@).',

	// L
	'label_cfg_duree_sauvegarde' => 'Backup preservation period',
	'label_cfg_frequence' => 'Backup frequency',
	'label_cfg_nettoyage_journalier' => 'Enable daily deletion of obsolete backups',
	'label_cfg_notif_active' => 'Enable notifications',
	'label_cfg_notif_mail' => 'Email addresses to notify',
	'label_cfg_prefixe' => 'Préfix',
	'label_cfg_sauvegarde_reguliere' => 'Enable automatic backups',
	'label_cfg_taille_max_rep' => 'Maximum folder’s size',
	'legende_cfg_generale' => 'Common backup parameters',
	'legende_cfg_notification' => 'Notifications',
	'legende_cfg_sauvegarde_reguliere' => 'Automatic actions',

	// M
	'message_cleaner_sujet' => 'Deleting backups',
	'message_notif_cleaner_intro' => 'Automatic deletion of obsolete backups (creation date former than @duree@ days) was performed successfully. The following files have been deleted : ',
	'message_notif_sauver_intro' => 'New backup is available. It was created by @auteur@.',
	'message_rien_a_sauver' => 'Neither file nor folder to backup.',
	'message_rien_a_telecharger' => 'No backup available to download.',
	'message_sauvegarde_nok' => 'Backup error. The archive file has not been created.',
	'message_sauvegarde_ok' => 'The archive file has been created.',
	'message_sauver_sujet' => 'Backup',
	'message_telechargement_nok' => 'Downloading error.',
	'message_zip_auteur_indetermine' => 'Undetermined',
	'message_zip_propriete_nok' => 'No property available on this archive.',
	'message_zip_sans_contenu' => 'No information available on the contents of this archive.',

	// R
	'resume_zip_auteur' => 'Created by',
	'resume_zip_compteur' => 'Backuped files & folders',
	'resume_zip_contenu' => 'Content summary',
	'resume_zip_statut' => 'Status',

	// T
	'titre_boite_sauver' => 'Create a backup',
	'titre_boite_telecharger' => 'List of backups available to download',
	'titre_page_configurer' => 'Configuration of My files plugin',
	'titre_page_mes_fichiers' => 'Backup my customisation files'
);

?>
