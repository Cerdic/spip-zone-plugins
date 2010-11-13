<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de http://www.spip.net/trad-lang/
// ** ne pas modifier le fichier **

if (!defined("_ECRIRE_INC_VERSION")) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// A
	'attention' => 'Achtung:',

	// C
	'colonne_date' => 'Datum',
	'colonne_nom' => 'Name',
	'colonne_taille_octets' => 'Gr&ouml;&szlig;e',

	// E
	'envoi_mail' => 'Backups gesendet',
	'erreur_config_inadaptee_mail' => 'Configuration non-adapt&eacute;e, votre serveur n\'assure pas les fonctions d\'envoi de mail !', # NEW
	'erreur_impossible_creer_verifier' => 'Impossible de cr&eacute;er le fichier @fichier@, v&eacute;rifiez les droits d\'&eacute;criture du r&eacute;pertoire @rep_bases@.', # NEW
	'erreur_impossible_liste_tables' => 'Impossible de lister les tables de la base.', # NEW
	'erreur_mail_fichier_lourd' => 'Le fichier de sauvegarde est trop lourd pour &ecirc;tre envoy&eacute; par mail. Vous pouvez le r&eacute;cup&eacute;rer depuis votre interface d\'administration ou par FTP en suivant le chemin : @fichier@', # NEW
	'erreur_mail_sujet' => 'Erreur de sauvegarde SQL', # NEW
	'erreur_probleme_donnees_corruption' => 'Probleme avec les donnees de @table@, corruption possible !', # NEW
	'erreur_repertoire_inaccessible' => 'Le r&eacute;pertoire @rep@ est inaccessible en &eacute;criture.', # NEW
	'erreur_repertoire_inexistant' => 'Le r&eacute;pertoire @rep@ est inexistant. Veuillez v&eacute;rifier votre configuration.', # NEW
	'erreur_sauvegarde_intro' => 'Le message d\'erreur est le suivant :', # NEW
	'erreurs_config' => 'Erreur(s) dans la configuration', # NEW

	// H
	'help_accepter' => 'Optional: Backup auf Tabellen beschr&auml;nken, deren Namen eine bestimmte Zeichenkette enth&auml;lt. (z.B. verzeichnis_, wichtig, digsbums)
Wenn das Feld leer ist, werden alle Tabellen gesichert. Trenen sie die enzelnen Namen mit einem Semikolon von einander.',
	'help_envoi' => 'Optional: Sicherungskopie wird per Mail verschickt, wenn sie eine Adresse angeben.',
	'help_eviter' => 'Optional: Wenn der Name einer Tabelle die angegebene Zeichenkette enth&auml;lt, wird nur ihre Struktur gesichert.  Trenen sie die enzelnen Namen mit einem Semikolon von einander.',
	'help_gz' => 'Anderenfalls wird ein Backup im Format .sql angelegt.',
	'help_mail_max_size' => 'Manche Datenbanken sind gr&ouml;&szlig;er als die Maximalgr&ouml;&szlig;e f&uuml;r Mail-Anh&auml;nge. Pr&uuml;fen sie, welche Dateigr&ouml;&szlig;e ihr Mail-Anbieter zul&auml;&szlig;t. Die Standardeinstellung des Plugins ist 2MB.',
	'help_msg' => 'Erfolgsmeldung im Interface anzeigen.',
	'help_obsolete' => 'Bestimmt ab welchem Alter (in Tagen) ein Backup automatisch vom Server gel&ouml;scht wird.
Die Einstellung -1 schaltet diese Funktion ab.',
	'help_prefixe' => 'Optional: Prefix f&uuml;r den Namen der Backup-Datei',
	'help_rep' => 'Backup-Verzeichnis (Pfad unterhalb des SPIP-Installationsverzeichnis, z.B. tmp/data/ ) <strong>MUSS</strong> mit einem Slash "/" enden.',
	'help_restauration' => '<strong>Achtung!!!</strong> Die Backups sind <strong>nicht im SPIP-Format</strong>.
Sie k&ouml;nnen nicht mit dem SPIP-Verwaltungssystem genutzt werden.<br /><br />
Sie m&uuml;ssen f&uuml;r die Wiederherstellung das Interface ihres Servers verwenden (z.B. PHPMyAdmin - unter dem Reiter <strong>"SQL"</strong> klicken sie auf die Schaltfl&auml;che <strong>"Datei w&auml;hlen"</strong> und best&auml;tigen nach Auswahl der Backupdatei. Ggf. markieren sie die Option "gzip").br /><br />
Die Backupdateien <strong>xxxx.gz</strong> bzw. <strong>xxx.sql</strong> enthalten Befehle im SQL-Format, mit denen die vorhandenen SPIP-Tabellen <strong>gel&ouml;scht</strong> und durch die Backupdaten <strong>ersetzt</strong> werden. Neuere Daten als die im Backup vorhandenen <strong>gehen bei der Widerherstellung verloren</strong>!',
	'help_titre' => 'Auf dieser Seite k&ouml;nnen sie die  automatischen Datenbankbackups konfigurieren.',

	// I
	'info_mail_message_mime' => 'Ceci est un message au format MIME.', # NEW
	'info_sauvegardes_obsolete' => 'Une sauvegarde de la base est conserv&eacute;e @nb@ jours &agrave; partir du jour de sa r&eacute;alisation.', # NEW
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
	'info_telecharger_sauvegardes' => 'Le tableau ci-dessous liste l\'ensemble des sauvegardes r&eacute;alis&eacute;es pour votre site que vous pouvez t&eacute;l&eacute;charger.', # NEW

	// L
	'label_adresse' => '&Agrave; l\'adresse : ', # NEW
	'label_compression_gz' => 'Zipper le fichier de sauvegarde : ', # NEW
	'label_donnees' => 'Donn&eacute;es des tables : ', # NEW
	'label_donnees_ignorees' => 'Donn&eacute;es ignor&eacute;es : ', # NEW
	'label_frequence' => 'Fr&eacute;quence de la sauvegarde : tous les ', # NEW
	'label_mail_max_size' => 'Taille maximale des fichiers &agrave; attacher aux mails (en Mo) :', # NEW
	'label_message_succes' => 'Affiche un message de succ&egrave;s si sauvegarde OK : ', # NEW
	'label_nom_base' => 'Nom de la base SPIP : ', # NEW
	'label_obsolete_jours' => 'Sauvegardes consid&eacute;r&eacute;es obsol&egrave;tes apr&egrave;s : ', # NEW
	'label_prefixe_sauvegardes' => 'Pr&eacute;fixe pour les sauvegardes : ', # NEW
	'label_repertoire_stockage' => 'R&eacute;pertoire de stockage : ', # NEW
	'label_restauration' => 'Restauration d\'une sauvegarde :', # NEW
	'label_structure' => 'Structure des tables : ', # NEW
	'label_tables_acceptes' => 'Tables accept&eacute;es : ', # NEW
	'legend_structure_donnees' => 'El&eacute;ments &agrave; sauvegarder : ', # NEW

	// M
	'message_aucune_sauvegarde' => 'Il n\'y a aucune sauvegarde.', # NEW
	'message_pas_envoi' => 'Les sauvegardes ne seront pas envoy&eacute;es !', # NEW

	// S
	'sauvegarde_erreur_mail' => 'Le plugin "saveauto" a rencontr&eacute; une erreur lors de la sauvegarde de la base de donn&eacute;e.', # NEW
	'sauvegarde_ok_mail' => 'Sauvegarde de la base et envoi par mail effectu&eacute;s avec succ&egrave;s !', # NEW
	'saveauto_titre' => 'SQL Backup',

	// T
	'titre_boite_historique' => 'Historique des sauvegardes', # NEW
	'titre_boite_sauver' => 'Plugin Saveauto: sauvegarde SQL de la base de donn&eacute;e', # NEW
	'titre_page_saveauto' => 'Sauvegarde de base de donn&eacute;e', # NEW
	'titre_saveauto' => 'Sauvegarde automatique', # NEW

	// V
	'valeur_jours' => ' jours', # NE
);

?>
