<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de http://www.spip.net/trad-lang/
// ** ne pas modifier le fichier **

if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// A
	'attention' => 'Achtung:',

	// C
	'colonne_date' => 'Datum',
	'colonne_nom' => 'Name',
	'colonne_taille_octets' => 'Größe',

	// E
	'envoi_mail' => 'Backups gesendet',
	'erreur_config_inadaptee_mail' => 'Configuration non-adaptée, votre serveur n\'assure pas les fonctions d\'envoi de mail !', # NEW
	'erreur_impossible_creer_verifier' => 'Impossible de créer le fichier @fichier@, vérifiez les droits d\'écriture du répertoire @rep_bases@.', # NEW
	'erreur_impossible_liste_tables' => 'Impossible de lister les tables de la base.', # NEW
	'erreur_mail_fichier_lourd' => 'Le fichier de sauvegarde est trop lourd pour être envoyé par mail. Vous pouvez le récupérer depuis votre interface d\'administration ou par FTP en suivant le chemin : @fichier@', # NEW
	'erreur_mail_sujet' => 'Erreur de sauvegarde SQL', # NEW
	'erreur_probleme_donnees_corruption' => 'Probleme avec les donnees de @table@, corruption possible !', # NEW
	'erreur_repertoire_inaccessible' => 'Le répertoire @rep@ est inaccessible en écriture.', # NEW
	'erreur_repertoire_inexistant' => 'Le répertoire @rep@ est inexistant. Veuillez vérifier votre configuration.', # NEW
	'erreur_sauvegarde_intro' => 'Le message d\'erreur est le suivant :', # NEW
	'erreurs_config' => 'Erreur(s) dans la configuration', # NEW

	// H
	'help_accepter' => 'Optional: Backup auf Tabellen beschränken, deren Namen eine bestimmte Zeichenkette enthält. (z.B. verzeichnis_, wichtig, digsbums)
Wenn das Feld leer ist, werden alle Tabellen gesichert. Trenen sie die enzelnen Namen mit einem Semikolon von einander.',
	'help_envoi' => 'Optional: Sicherungskopie wird per Mail verschickt, wenn sie eine Adresse angeben.',
	'help_eviter' => 'Optional: Wenn der Name einer Tabelle die angegebene Zeichenkette enthält, wird nur ihre Struktur gesichert.  Trenen sie die enzelnen Namen mit einem Semikolon von einander.',
	'help_gz' => 'Anderenfalls wird ein Backup im Format .sql angelegt.',
	'help_mail_max_size' => 'Manche Datenbanken sind größer als die Maximalgröße für Mail-Anhänge. Prüfen sie, welche Dateigröße ihr Mail-Anbieter zuläßt. Die Standardeinstellung des Plugins ist 2MB.',
	'help_msg' => 'Erfolgsmeldung im Interface anzeigen.',
	'help_obsolete' => 'Bestimmt ab welchem Alter (in Tagen) ein Backup automatisch vom Server gelöscht wird.
Die Einstellung -1 schaltet diese Funktion ab.',
	'help_prefixe' => 'Optional: Prefix für den Namen der Backup-Datei',
	'help_rep' => 'Backup-Verzeichnis (Pfad unterhalb des SPIP-Installationsverzeichnis, z.B. tmp/data/ ) <strong>MUSS</strong> mit einem Slash "/" enden.',
	'help_restauration' => '<strong>Achtung!!!</strong> Die Backups sind <strong>nicht im SPIP-Format</strong>.
Sie können nicht mit dem SPIP-Verwaltungssystem genutzt werden.<br /><br />
Sie müssen für die Wiederherstellung das Interface ihres Servers verwenden (z.B. PHPMyAdmin - unter dem Reiter <strong>"SQL"</strong> klicken sie auf die Schaltfläche <strong>"Datei wählen"</strong> und bestätigen nach Auswahl der Backupdatei. Ggf. markieren sie die Option "gzip").br /><br />
Die Backupdateien <strong>xxxx.gz</strong> bzw. <strong>xxx.sql</strong> enthalten Befehle im SQL-Format, mit denen die vorhandenen SPIP-Tabellen <strong>gelöscht</strong> und durch die Backupdaten <strong>ersetzt</strong> werden. Neuere Daten als die im Backup vorhandenen <strong>gehen bei der Widerherstellung verloren</strong>!',
	'help_titre' => 'Auf dieser Seite können sie die  automatischen Datenbankbackups konfigurieren.',

	// I
	'info_mail_message_mime' => 'Ceci est un message au format MIME.', # NEW
	'info_sauvegardes_obsolete' => 'Une sauvegarde de la base est conservée @nb@ jours à partir du jour de sa réalisation.', # NEW
	'info_sql_base' => 'Base : ', # NEW
	'info_sql_compatible_phpmyadmin' => 'Fichier SQL 100% compatible PHPMyadmin', # NEW
	'info_sql_date' => 'Date : ', # NEW
	'info_sql_debut_fichier' => 'Debut du fichier', # NEW
	'info_sql_donnees_table' => 'Donnees de @table@', # NEW
	'info_sql_fichier_genere' => 'Ce fichier est genere par le plugin saveauto', # NEW
	'info_sql_fin_fichier' => 'Fin du fichier', # NEW
	'info_sql_ipclient' => 'IP Client : ', # NEW
	'info_sql_mysqlversion' => 'Version mySQL : ', # NEW
	'info_sql_os' => 'OS Serveur : ', # NEW
	'info_sql_phpversion' => 'Version PHP : ', # NEW
	'info_sql_plugins_utilises' => '@nb@ plugins utilises :', # NEW
	'info_sql_serveur' => 'Serveur : ', # NEW
	'info_sql_spip_version' => 'Version de SPIP : ', # NEW
	'info_sql_structure_table' => 'Structure de la table @table@', # NEW
	'info_telecharger_sauvegardes' => 'Le tableau ci-dessous liste l\'ensemble des sauvegardes réalisées pour votre site que vous pouvez télécharger.', # NEW

	// L
	'label_adresse' => 'À l\'adresse : ', # NEW
	'label_compression_gz' => 'Zipper le fichier de sauvegarde : ', # NEW
	'label_donnees' => 'Données des tables : ', # NEW
	'label_donnees_ignorees' => 'Données ignorées : ', # NEW
	'label_frequence' => 'Fréquence de la sauvegarde : tous les ', # NEW
	'label_mail_max_size' => 'Taille maximale des fichiers à attacher aux mails (en Mo) :', # NEW
	'label_message_succes' => 'Affiche un message de succès si sauvegarde OK : ', # NEW
	'label_nom_base' => 'Nom de la base SPIP : ', # NEW
	'label_obsolete_jours' => 'Sauvegardes considérées obsolètes après : ', # NEW
	'label_prefixe_sauvegardes' => 'Préfixe pour les sauvegardes : ', # NEW
	'label_repertoire_stockage' => 'Répertoire de stockage : ', # NEW
	'label_restauration' => 'Restauration d\'une sauvegarde :', # NEW
	'label_structure' => 'Structure des tables : ', # NEW
	'label_tables_acceptes' => 'Tables acceptées : ', # NEW
	'legend_structure_donnees' => 'Eléments à sauvegarder : ', # NEW

	// M
	'message_aucune_sauvegarde' => 'Il n\'y a aucune sauvegarde.', # NEW
	'message_pas_envoi' => 'Les sauvegardes ne seront pas envoyées !', # NEW

	// S
	'sauvegarde_erreur_mail' => 'Le plugin "saveauto" a rencontré une erreur lors de la sauvegarde de la base de donnée.', # NEW
	'sauvegarde_ok_mail' => 'Sauvegarde de la base et envoi par mail effectués avec succès !', # NEW
	'saveauto_titre' => 'SQL Backup',

	// T
	'titre_boite_historique' => 'Historique des sauvegardes', # NEW
	'titre_boite_sauver' => 'Plugin Saveauto: sauvegarde SQL de la base de donnée', # NEW
	'titre_page_saveauto' => 'Sauvegarde de base de donnée', # NEW
	'titre_saveauto' => 'Sauvegarde automatique', # NEW

	// V
	'valeur_jours' => ' jours' # NEW
);

?>
