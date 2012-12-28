<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de http://trad.spip.net/tradlang_module/saveauto?lang_cible=en
// ** ne pas modifier le fichier **

if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// A
	'attention' => 'Warning:',

	// B
	'bouton_sauvegarder' => 'Sauvegarder la base', # NEW

	// C
	'colonne_auteur' => 'Créé par', # NEW
	'colonne_nom' => 'Name',

	// E
	'envoi_mail' => 'Backups sent', # MODIF
	'erreur_config_inadaptee_mail' => 'Inappropriate configuration - your server does not offer email dispatch functionality!',
	'erreur_impossible_creer_verifier' => 'Can not create the @fichier@ file: check write access rights are available for the @rep_bases@ directory.',
	'erreur_impossible_liste_tables' => 'Can not list the tables in the database.',
	'erreur_mail_fichier_lourd' => 'The backup file is too large to be sent by email. You may retrieve a copy using your site administration interface or  via FTP using the file path: @fichier@',
	'erreur_mail_sujet' => 'SQL backup error',
	'erreur_probleme_donnees_corruption' => 'Problem with data in the @table@ table, it may be corrupted!',
	'erreur_repertoire_inaccessible' => 'The @rep@ directory is not open for write access.',
	'erreur_repertoire_inexistant' => 'The @rep@ directory does not exist. Please check your site structure configuration.',
	'erreur_sauvegarde_intro' => 'The error message is as follows:',
	'erreurs_config' => 'Configuration error(s) exist',

	// H
	'help_accepter' => 'Optional: only save the tables with a particular string included in the table name, e.g. directory_, important, thing.
             Enter nothing to accept all tables in the database. Separate different specific names with a semi-colon (;)',
	'help_cfg_generale' => 'Ces paramètres de configuration s\'appliquent à toutes les sauvegardes, manuelles ou automatiques.', # NEW
	'help_contenu' => 'Choisissez les paramètres de contenu de votre fichier de sauvegarde.', # NEW
	'help_contenu_auto' => 'Choisir le contenu des sauvegardes automatiques.', # NEW
	'help_envoi' => 'Optional: sends the backup by email if you enter a recipient email address', # MODIF
	'help_eviter' => 'Optional: if the table contains the specified string in its name: the data are ignored (but not the structure). Separate the different names with a semi-colon (;).',
	'help_frequence' => 'Saisir la fréquence des sauvegardes automatiques en jours.', # NEW
	'help_gz' => 'Otherwise the backups will be in .sql format.',
	'help_liste_tables' => 'Par défaut, toutes les tables sont exportées à l\'exception des tables @noexport@. Si vous souhaitez choisir précisément les tables à sauvegarder ouvrez la liste en décochant la case ci-dessous.', # NEW
	'help_mail_max_size' => 'Certain databases may exceed the maximum size for documents attached to an email. Check with your web host to find out the maximum size authorised. The default limit is 2 MB.', # MODIF
	'help_max_zip' => 'Le fichier de sauvegarde est automatiquement zippé si sa taille est inférieure à un seuil. Saisir ce seuil en Mo.', # NEW
	'help_msg' => 'Display a successful completion message on the screen', # MODIF
	'help_notif_active' => 'Activer l\'envoi des sauvegardes par mail', # NEW
	'help_notif_mail' => 'Saisir les adresses en les séparant par des virgules ",". Ces adresses s\'ajoutent à celle du webmestre du site.', # NEW
	'help_obsolete' => 'Based on the number of days old, determine if an archive is considered as obsolete and if so delete it from the server.
             Enter -1 to deactivate this functionality', # MODIF
	'help_prefixe' => 'Optional: enter a prefix for the backup file name', # MODIF
	'help_rep' => 'Directory for storing the files (the path starting from the SPIP <strong>root</strong> directory, tmp/data/ for example). It <strong>MUST</strong> end with a /.',
	'help_restauration' => '<strong>Warning !!!</strong> the backups made are <strong>not in SPIP format</strong> :
                It is useless to try to use them with the SPIP administration tool.<br /><br />
             For any backup restores, you must use the <strong>phpmyadmin</strong> interface of your
             database server: on the <strong>"SQL"</strong> tab, use the button labelled
             <strong>"Location of the text file"</strong> to select the backup file
             (tick the "gzipped" option if necessary) then click on OK.<br /><br />
             The <strong>xxxx.gz</strong> or <strong>xxx.sql</strong> backups contain an SQL formatted file with the commands 
             used to <strong>delete</strong> the existing SPIP tables and to <strong>replace</strong> them with
             archived data. Any data <strong>more recent</strong> than those in the backup will therefore be <strong>LOST</strong>!', # MODIF
	'help_sauvegarde_1' => 'Cette option vous permet de sauvegarder la structure et le contenu de la base dans un fichier au format SQL qui sera stocké dans le répertoire tmp/dump/. La fichier se nomme <em>@prefixe@_aaaammjj_hhmmss.</em>', # NEW
	'help_sauvegarde_2' => 'La sauvegarde automatique est activée (fréquence en jours : @frequence@).', # NEW
	'help_titre' => 'This page is used to configure the automatic database backup options.',

	// I
	'info_mail_message_mime' => 'This is a MIME-formatted message.',
	'info_sauvegardes_obsolete' => 'A backup of the database is stored @nb@ days from the date of its production.',
	'info_sql_auteur' => 'Auteur : ', # NEW
	'info_sql_base' => 'Database:',
	'info_sql_compatible_phpmyadmin' => 'SQL file 100% compatible with PHPMyadmin',
	'info_sql_date' => 'Date:',
	'info_sql_debut_fichier' => 'Start of file',
	'info_sql_donnees_table' => 'Data from @table@',
	'info_sql_fichier_genere' => 'This file is generated by the saveauto plugin',
	'info_sql_fin_fichier' => 'End of file',
	'info_sql_ipclient' => 'Client IP:',
	'info_sql_mysqlversion' => 'MySQL version:',
	'info_sql_os' => 'Server O/S:',
	'info_sql_phpversion' => 'PHP version:',
	'info_sql_plugins_utilises' => '@nb@ plugins used:',
	'info_sql_serveur' => 'Server:',
	'info_sql_spip_version' => 'SPIP version:',
	'info_sql_structure_table' => 'Structure of the @table@ table',
	'info_telecharger_sauvegardes' => 'The table below lists all downloadable backups made for your site.',

	// L
	'label_adresse' => 'To the address:',
	'label_compression_gz' => 'Zip the backup file:',
	'label_donnees' => 'Data from the tables:', # MODIF
	'label_donnees_ignorees' => 'Data ignored:',
	'label_frequence' => 'Frequency of the backup: every', # MODIF
	'label_mail_max_size' => 'Maximum size of files attached to emails (MB):', # MODIF
	'label_max_zip' => 'Seuil des zips', # NEW
	'label_message_succes' => 'Display a successful completion message if the backup runs OK:', # MODIF
	'label_nettoyage_journalier' => 'Activer le nettoyage journalier des archives', # NEW
	'label_nom_base' => 'SPIP database name:',
	'label_notif_active' => 'Activer les notifications', # NEW
	'label_notif_mail' => 'Adresses email à notifier', # NEW
	'label_obsolete_jours' => 'Backups considered as obsolete after:', # MODIF
	'label_prefixe_sauvegardes' => 'Backup prefix:', # MODIF
	'label_repertoire_stockage' => 'Storage directory:',
	'label_restauration' => 'Restore a backup:',
	'label_sauvegarde_reguliere' => 'Activer la sauvegarde régulière', # NEW
	'label_structure' => 'Structure of the tables:', # MODIF
	'label_tables_acceptes' => 'Tables accepted:',
	'label_toutes_tables' => 'Sauvegarder toutes les tables', # NEW
	'legend_cfg_generale' => 'Paramètres généraux des sauvegardes', # NEW
	'legend_cfg_notification' => 'Notifications', # NEW
	'legend_cfg_sauvegarde_reguliere' => 'Traitements automatiques', # NEW
	'legend_structure_donnees' => 'Items to be backed up:',

	// M
	'message_aucune_sauvegarde' => 'There are no backups.', # MODIF
	'message_cleaner_sujet' => 'Nettoyage des sauvegardes', # NEW
	'message_notif_cleaner_intro' => 'La suppression automatique des sauvegardes obsolètes (dont la date est antérieure à @duree@ jours) a été effectuée avec succès. Les fichiers suivants ont été supprimés : ', # NEW
	'message_notif_sauver_intro' => 'La sauvegarde de la base @base@ a été effectuée avec succès par l\'auteur @auteur@.', # NEW
	'message_pas_envoi' => 'The backups will not be emailed!',
	'message_sauvegarde_nok' => 'Erreur lors de la sauvegarde SQL de la base.', # NEW
	'message_sauvegarde_ok' => 'La sauvegarde SQL de la base a été faite avec succès.', # NEW
	'message_sauver_sujet' => 'Sauvegarde de la base @base@', # NEW
	'message_telechargement_nok' => 'Erreur lors du téléchargement.', # NEW

	// S
	'saveauto_titre' => 'SQL backup',

	// T
	'titre_boite_historique' => 'Backup history', # MODIF
	'titre_boite_sauver' => 'Saveauto plugin: SQL database backups', # MODIF
	'titre_page_configurer' => 'Configuration du plugin Sauvegarde automatique', # NEW
	'titre_page_saveauto' => 'Database backups', # MODIF
	'titre_saveauto' => 'Automatic backups',

	// V
	'valeur_jours' => ' days'
);

?>
