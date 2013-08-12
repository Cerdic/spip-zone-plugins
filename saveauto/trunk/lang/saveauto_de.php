<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de http://trad.spip.net/tradlang_module/saveauto?lang_cible=de
// ** ne pas modifier le fichier **

if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// B
	'bouton_sauvegarder' => 'Datenbank sichern',

	// C
	'colonne_auteur' => 'Angelegt von',
	'colonne_nom' => 'Name',

	// E
	'erreur_impossible_creer_verifier' => 'Die Datei @fichier@ kann nicht angelegt werden. Bitte überprüfen Sie die Schreibrechte für das Verzeichnis @rep_bases@.',
	'erreur_impossible_liste_tables' => 'Liste der Tabellen in der Datenbank kann nicht angezeigt werden.',
	'erreur_probleme_donnees_corruption' => 'Datenfahler in der Tabelle @table@. Sie ist möglicherweise beschädigt !',
	'erreur_repertoire_inaccessible' => 'In das Verzeichnis @rep@ kann nicht geschrieben werden.',

	// H
	'help_cfg_generale' => 'Die Einstellungen gelten für alle manuellen und automatischen Backups.',
	'help_contenu' => 'Wählen Sie die inhaltlichen Einstellungen ihrer Backups.',
	'help_contenu_auto' => 'Wählen Sie die Inhalte der automatischen Backups aus.',
	'help_frequence' => 'Wählen Sie die Zeitabstände in Tagen zwischen den automatischen Backups.',
	'help_liste_tables' => 'In der Grundeinstellung werden alle Tabellen außer  @noexport@ gesichert. Wenn Sie die zu sichernden Tabellen einzeln auswählen möchten, öffnen Sie die Liste, indem Sie das Häkchen weiter unten entfernen.',
	'help_mail_max_size' => 'Angabe der Maximalgröße für Backupdateien, die per Mail verschickt wird. Der Wert ist abhängig von den Einstellungen Ihres Mailanbieters.',
	'help_max_zip' => 'Die Sicherungsdatei wird automatisch als ZIP komprimiert, wenn sie kleiner als ein bestimmter Wert ist. Geben Sie diesen Wert in Megabyte an.',
	'help_notif_active' => 'Sie werden auf Wunsch über automatische Vorgänge informiert. Bei automatischen backups wird Ihnen die Sicherungsdatei zugeschickt, wenn diese nicht größer als ein bestimmter Wert ist, und wenn das Plugin "Facteur" aktiviert ist.',
	'help_notif_mail' => 'Mehrere Adressen müssen mit Komma "," getrennt werden. Diese Adressen ergänzen die des Webmasters der Site.',
	'help_obsolete' => 'Aufbewahrungszeitraum der Sicherungsdateien in Tagen.',
	'help_prefixe' => 'Präfix des Namens der Backup-Datei.',
	'help_restauration' => '<strong>Achtung!!!</strong> Diese Backups sind <strong>nicht im SPIP-Format</strong>.
Sie können nicht mit dem SPIP-Backupsystem genutzt werden.<br /><br />
Sie müssen für die Wiederherstellung das Interface ihres Servers verwenden. In PHPMyAdmin klicken Sie unter dem Reiter <strong>"SQL"</strong> sie auf die Schaltfläche <strong>"Datei wählen"</strong> suchen die gewünschte Sicherung aus. Falls erforderlich markieren sie die Option "gzip").br /><br />
Die Backupdateien <strong>xxxx.gz</strong> bzw. <strong>xxx.sql</strong> enthalten Befehle im SQL-Format, mit denen die vorhandenen SPIP-Tabellen <strong>gelöscht</strong> und durch die Backupdaten <strong>ersetzt</strong> werden. Neuere Daten als die im Backup vorhandenen <strong>gehen bei der Widerherstellung verloren</strong>!',
	'help_sauvegarde_1' => 'Diese Option ermöglicht Ihnen, Struktur und Inhalt der Datenbank im SQL-Format im Verzeichnis tmp/dump/ zu speichern. Diese Datei wird <em>@prefixe@_aaaammjj_hhmmss.</em> benannt. Die Tabellenpräfixe werden beibehalten.',
	'help_sauvegarde_2' => 'Automatische Backups sind aktiviert. Sie werden alle @frequence@) Tage angelegt.',

	// I
	'info_sql_auteur' => 'Autor: ',
	'info_sql_base' => 'Datenbank: ',
	'info_sql_compatible_phpmyadmin' => 'Vollständig mit PHPMyadmin kompatible SQL-Datei.',
	'info_sql_date' => 'Datum: ',
	'info_sql_debut_fichier' => 'Dateibeginn',
	'info_sql_donnees_table' => 'Daten der Tabelle @table@',
	'info_sql_fichier_genere' => 'Diese Datei wurde vom Plugin saveauto angelegt.',
	'info_sql_fin_fichier' => 'Dateiende',
	'info_sql_ipclient' => 'Client-IP: ',
	'info_sql_mysqlversion' => 'mySQL-Version: ',
	'info_sql_os' => 'Server-Betriebssystem: ',
	'info_sql_phpversion' => 'PHP-Version: ',
	'info_sql_plugins_utilises' => '@nb@ verwendete Plugins:',
	'info_sql_serveur' => 'Server: ',
	'info_sql_spip_version' => 'SPIP-Version: ',
	'info_sql_structure_table' => 'Struktur der Tabelle @table@',

	// L
	'label_donnees' => 'Daten der Tabellen: ',
	'label_frequence' => 'Häufigkeit der Backups',
	'label_mail_max_size' => 'Maximalgröße für Mailversand',
	'label_max_zip' => 'Schwellwert der ZIP-Dateien',
	'label_nettoyage_journalier' => 'Tägliches Aufräumen der Sicherungen',
	'label_notif_active' => 'Benachrichtigungen aktivieren',
	'label_notif_mail' => 'Zu benachrichtigende Adressen',
	'label_obsolete_jours' => 'Aufbewahrungszeit der Sicherungskopien ',
	'label_prefixe_sauvegardes' => 'Präfix',
	'label_sauvegarde_reguliere' => 'Regelmäßige Sicherungen aktivieren',
	'label_structure' => 'Tabellenstruktur',
	'label_toutes_tables' => 'Alle Tabellen sichern',
	'legend_cfg_generale' => 'Allgemeine Sicherungseinstellungen',
	'legend_cfg_notification' => 'Benachrichtigungen',
	'legend_cfg_sauvegarde_reguliere' => 'Automatische Abläufe',

	// M
	'message_aucune_sauvegarde' => 'Es liegt keine herunterladbare Sicherung vor.',
	'message_cleaner_sujet' => 'Sicherungen aufräumen',
	'message_notif_cleaner_intro' => 'Das automatische Löschen überflüssiger Sicherungen (älter als @duree@ Tage) wurde erfolgreich durchgeführt. Folgende Dateien wurden gelöscht: ',
	'message_notif_sauver_intro' => 'Die Sicherung der Datenbank @base@ wurde erfolgreich von @auteur@ durchgeführt.',
	'message_sauvegarde_nok' => 'Fehler bei der Sicherung der SQL-Datenbank.',
	'message_sauvegarde_ok' => 'Die SQL-Datenbank wurde erfolgreich gesichert.',
	'message_sauver_sujet' => 'Sicherung der Datenbank @base@',
	'message_telechargement_nok' => 'Übertragungsfehler',

	// T
	'titre_boite_historique' => 'Sicherungsgeschichte',
	'titre_boite_sauver' => 'MySQL Datensicherung anlegen',
	'titre_page_configurer' => 'Konfiguration des Plugins saveauto',
	'titre_page_saveauto' => 'Datenbank im SQL-Format sichern',
	'titre_saveauto' => 'Automatisches Backup'
);

?>
