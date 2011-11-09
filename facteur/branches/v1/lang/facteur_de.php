<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// C
	'config_info_enregistree' => 'Die Konfiguration des Briedtr&#228;gers wurde gespeichert',
	'configuration_adresse_envoi' => 'Absendeadresse einstellen',
	'configuration_facteur' => 'Brieftr&#228;ger',
	'configuration_mailer' => 'Konfiguration des Mailers',
	'configuration_smtp' => 'Auswahl der Versandmethode',
	'configuration_smtp_descriptif' => 'Im Zweifel hier die mail() Funktion von PHP eintragen.',
	'corps_email_de_test' => 'Das ist ein Versandtest mit Sondärzeichen: Bär Größe Maß accentué',  // Laisser tel quel, car
	
	// E
	'email_test_envoye' => 'Die Testmail wurde fehlerfrei versand. Falls sie nicht richtig ankommt,
                      bearbeiten sie ihre Serverkonfiguration oder kontaktieren sie den Administrator.',
	'erreur' => 'Fehler',
	'erreur_generale' => 'Mehrere Konfigurationsfehler. Bitte Inhalt des Formulars korrigieren.',
	'erreur_invalid_host' => 'Servername inkorrekt',
	'erreur_invalid_port' => 'Portnummer inkorrekt',
	
	// F
	'facteur_adresse_envoi_email' => 'E-Mail :',
	'facteur_adresse_envoi_nom' => 'Name:',
	'facteur_filtre_accents' => 'Sonderzeichen in HTML-Entit&#228;ten umwandeln (z.B. f&#252;r Hotmail).',
	'facteur_filtre_css' => 'Stile zwischen &lt;head&gt; und &lt;/head&gt; zu "inline" Stilen umwandeln, sinnvoll f&#252;r Webmail die interne Stile externen vorzieht.',
	'facteur_filtre_iso_8859' => 'Nach ISO-8859-1 umwandeln',
	'facteur_filtre_images' => 'Verlinkte Bilder in E-Mail einbetten',
	'facteur_filtres' => 'Filter',
	'facteur_filtres_descriptif' => 'Beim Versand k&#246;nnen mehrere Filter eingesetzt werden.',
	'facteur_smtp_auth' => 'Autorisierung erforderlich:',
	'facteur_smtp_auth_oui' => 'ja',
	'facteur_smtp_auth_non' => 'nein',
	'facteur_smtp_host' => 'Server:',
	'facteur_smtp_password' => 'Passwort:',
	'facteur_smtp_port' => 'Port:',
	'facteur_smtp_secure' => 'Verschl&#252;sselte Verbindung:',
	'facteur_smtp_secure_non' => 'nein',
	'facteur_smtp_secure_ssl' => 'SSL',
	'facteur_smtp_secure_tls' => 'TLS',
	'facteur_smtp_sender' => 'Fehlercodes (optional)',
	'facteur_smtp_sender_descriptif' => 'Legt im Kopf der Mail die Empf&#228;ngeradresse f&#252;r Fehlermeldungen fest (bzw. den Return-Path), bestimmt ebenfalls die Absenderadresse bei Versand per SMTP.',
	'facteur_smtp_username' => 'Benutzername:',
	
	// N
	'note_test_configuration' => 'Eine Mail wird an die Absendeadresse geschickt (oder an den Webmaster).',
	
	// P
	'personnaliser' => 'Individuelle Einstellungen',
	
	// T
	'tester' => 'Testen',
	'tester_la_configuration' => 'Konfiguration testen',
	
	// U
	'utiliser_mail' => 'Funktion mail() von PHP verwenden',
	'utiliser_reglages_site' => 'Einstellungen von SPIP verwenden: als Name wird die Bezeichnung der SPIP-Website verwendet und als Adresse die des Webmasters.',
	'utiliser_smtp' => 'SMTP verwenden',
	
	// V
	'valider' => ' OK ',
	'version_html' => 'HTML-Version.',
	'version_texte' => 'Textversion.',
	
	'Z' => 'ZZzZZzzz'

);

?>