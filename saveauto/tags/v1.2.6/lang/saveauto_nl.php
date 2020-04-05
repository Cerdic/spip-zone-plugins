<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de https://trad.spip.net/tradlang_module/saveauto?lang_cible=nl
// ** ne pas modifier le fichier **

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// A
	'ajouter_webmestre' => 'Voeg de webmaster toe als bestemming',

	// B
	'bouton_sauvegarder' => 'Bewaar de database',

	// C
	'colonne_auteur' => 'Gemaakt door',
	'colonne_nom' => 'Naam',

	// E
	'erreur_impossible_creer_verifier' => 'Kan bestand niet aanmaken @fichier@, check schrijfrechten in de directory @rep_bases@.',
	'erreur_impossible_liste_tables' => 'Kan de lijst met tabellen in de database.',
	'erreur_probleme_donnees_corruption' => 'Probleem met de gegevens van @table@, corruptie mogelijk !',
	'erreur_repertoire_inaccessible' => 'De map is niet toegankelijk @rep@ voor het schrijven.',
	'erreur_repertoire_perso_inaccessible' => 'De geconfigureerde map @rep@ is niet toegankelijk: gebruik in de plaats de backupmap van SPIP',

	// H
	'help_cfg_generale' => 'Deze configuratie-instellingen zin van toepassing op alles backups, handmatig en automatisch.',
	'help_contenu' => 'Kies de inhouds-instellingen van het backupbestand.',
	'help_contenu_auto' => 'Kies de inhouds-instellingen automatisch.',
	'help_frequence' => 'Vermeld de opslagfrequentie van de backups in dagen.',
	'help_liste_tables' => 'Standaard worden alle SPIP tabellen geëxporteerd, uitgezonderd de @noexport@ tabellen. Wanneer je de tabellen specifiek wilt kiezen (bv ook niet-SPIP tabellen) open dan de lijst door hier te klikken.',
	'help_mail_max_size' => 'Bepaalde gegevens kunnen de maximum grootte van een emailbijlage overschrijden. Controleer dit maximum bij de provider. De standaardwaarde voor het maximum is 2Mb.',
	'help_max_zip' => 'Boven een bepaalde ondergrens wordt een bestand automatisch gezipped. Geef die ondergrens aan in Mb.',
	'help_nbr_garder' => 'Geef het minimum aantal te bewaren backups, ongeacht de bewaarperiode',
	'help_notif_active' => 'Het per mail opsturen van backups activeren',
	'help_notif_mail' => 'Geef hier de adressen op, gescheiden door een komma ",".',
	'help_obsolete' => 'Geef aan na hoeveel dagen een backup als verouderd moet worden beschouwd en automatisch van de server wordt verwijderd.
	 								 		Voer -1 in om deze functie uit te schakelen',
	'help_prefixe' => 'Optioneel: voer een voorvoegsel in voor de naam van het backupbestand',
	'help_repertoire' => 'Om een andere opslagmap te gebruiken dan die van SPIP, geef je hier het adres op vanaf de root van de site (en eindigend met / )',
	'help_restauration' => '<strong>Opgelet!!!</strong> De backups zijn <strong>niet in SPIP formaat</strong>:
   										 		Gebruik ze dus ook niet in de beheersfunctie van SPIP.<br /><br />
													Voor het terugzetten gebruik je de <strong>phpmyadmin</strong> interface van de
													databseserver: in het vakje <strong>"SQL"</strong> gebruik je de knop
													<strong>"Plaatsen van tekstbestand"</strong> om het backupbestand te kiezen
													(selecteer indien nodig de "gzip" optie) en bevestig je keuze.<br /><br />
													De backups <strong>xxxx.gz</strong> of <strong>xxx.sql</strong> bevatten een bestand in SQL formaat met opdrachten
													om de bestaande SPIP tabellen te <strong>wissen</strong> en ze te <strong>vervangen</strong> door de backup gegevens.
													De <strong>meest recente</strong> gegevens (van na de backup) gaan dus <strong>VERLOREN</strong>!',
	'help_sauvegarde_1' => 'Met deze optie bewaar je de structuur en de inhoud van de database in een MySQL formaat dat zal worden opgeslagen in de map tmp/dump/. Het bestand zal de naam <em>@prefixe@_jjjjmmdd_uummss.</em> hebben. De prefix van de tabellen wordt bewaard.',
	'help_sauvegarde_2' => 'De automatische backup is geactiveerd (frequentie in dagen: @frequence@).',

	// I
	'info_sql_auteur' => 'Auteur: ',
	'info_sql_base' => 'Database: ',
	'info_sql_compatible_phpmyadmin' => 'SQL bestand 100% compatibel met PHPMyadmin',
	'info_sql_date' => 'Datum: ',
	'info_sql_debut_fichier' => 'Begin bestand',
	'info_sql_donnees_table' => 'Gegevens van @table@',
	'info_sql_fichier_genere' => 'Dit bestand werd gemaakt met de plugin saveauto',
	'info_sql_fin_fichier' => 'Einde bestand',
	'info_sql_ipclient' => 'IP Client: ',
	'info_sql_mysqlversion' => 'MySQL versie: ',
	'info_sql_os' => 'OS Server: ',
	'info_sql_phpversion' => 'PHP versie: ',
	'info_sql_plugins_utilises' => '@nb@ gebruikte plugins:',
	'info_sql_serveur' => 'Server: ',
	'info_sql_spip_version' => 'Versie van SPIP: ',
	'info_sql_structure_table' => 'Structuur van tabel @table@',

	// L
	'label_donnees' => 'Gegevens van de tabellen: ',
	'label_frequence' => 'Backupfrequentie: alle ',
	'label_mail_max_size' => 'Maximale grootte van emailbijlage (in Mb):',
	'label_max_zip' => 'Grootte voor zips',
	'label_nbr_garder' => 'Hoeveel backups moeten worden bewaard',
	'label_nettoyage_journalier' => 'Dagelijks opschoning van de bestanden activeren',
	'label_notif_active' => 'Meldingen activeren',
	'label_notif_mail' => 'Emailadressen voor melding',
	'label_obsolete_jours' => 'Backup is verouderd na: ',
	'label_prefixe_sauvegardes' => 'Voorvoegsel voor backups: ',
	'label_repertoire_sauvegardes' => 'Map',
	'label_sauvegarde_reguliere' => 'Regelmatige backup activeren',
	'label_structure' => 'Structuure van de tabellen: ',
	'label_tables_non_spip' => 'Niet-SPIP tabellen',
	'label_toutes_tables' => 'Alle tabellen van SPIP bewaren',
	'legend_cfg_generale' => 'Algemene backup parameters',
	'legend_cfg_notification' => 'Meldingen',
	'legend_cfg_sauvegarde_reguliere' => 'Automatische verwerking',

	// M
	'message_aucune_sauvegarde' => 'Er is geen backup.',
	'message_cleaner_sujet' => 'Opschonen van backups',
	'message_notif_cleaner_intro' => 'De automatische verwijdering van verouderde backups (ouder dan @duree@ dagen) werd met succes uitgevoerd. De volgende bestanden werden verwijderd: ',
	'message_notif_sauver_intro' => 'De backup van database @base@ werd met succes uitgevoerd door auteur @auteur@.',
	'message_sauvegarde_nok' => 'Fout tijdens de SQL backup van de database.',
	'message_sauvegarde_ok' => 'De SQL backup van de database werd met succes uitgevoerd.',
	'message_sauver_sujet' => 'Backup van database @base@',
	'message_telechargement_nok' => 'Fout tijdens het downloaden.',

	// T
	'titre_boite_historique' => 'MySQL backups beschikbaar voor download in @dossier@',
	'titre_boite_sauver' => 'Plugin Saveauto: SQL backup van de database',
	'titre_page_configurer' => 'Automatische configuration van de plugin',
	'titre_page_saveauto' => 'Backup van database'
);
