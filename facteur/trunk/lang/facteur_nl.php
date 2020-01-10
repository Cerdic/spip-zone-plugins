<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de https://trad.spip.net/tradlang_module/facteur?lang_cible=nl
// ** ne pas modifier le fichier **

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// C
	'config_info_enregistree' => 'De configuratie is opgeslagen',
	'configuration_adresse_envoi' => 'Configuratie van het verzendadres',
	'configuration_facteur' => 'Postbode',
	'configuration_facteur_smtp_tls_allow_self_signed' => 'Validatie van het SSL certificaat',
	'configuration_mailer' => 'Configuratie van de mailer',
	'configuration_smtp' => 'Verzendwijze',
	'configuration_smtp_descriptif' => 'Kies, wanneer je twijfelt voor de functie PHP mail.',
	'corps_email_de_test' => 'Dit is een geaccentueerd testbericht',

	// E
	'email_envoye_par' => 'Verzonden door @site@',
	'email_test_envoye' => 'Het testbericht is verzonden. Ontvang je het niet juist, controleer dan de configuratie van de server.',
	'erreur' => 'Fout',
	'erreur_dans_log' => ': meer details in het logbestand',
	'erreur_envoi_bloque_constante' => 'Verzending wordt geblokkeerd door de constante <tt>_TEST_EMAIL_DEST</tt>.
Controleer het bestand <tt>mes_options.php</tt>',
	'erreur_generale' => 'Een of meerdere fouten in de configuratie. Controleer de inhoud van dit formulier.',
	'erreur_invalid_host' => 'Deze hostnaam is onjuist',
	'erreur_invalid_port' => 'Dit poortnummer is onjuist',

	// F
	'facteur_adresse_envoi_email' => 'Email:',
	'facteur_adresse_envoi_nom' => 'Naam:',
	'facteur_bcc' => 'Verborgen (BCC) :',
	'facteur_cc' => 'Kopie (CC) :',
	'facteur_copies' => 'Kopieën:',
	'facteur_copies_descriptif' => 'Deze adressen worden in kopie van de email gezet. Niet meer dan één adres in kopie en/of in verborgen kopie.',
	'facteur_email_test' => 'Een test email sturen naar:',
	'facteur_filtre_accents' => 'Zet tekens met accenten om in hun html-code (met name voor Hotmail).',
	'facteur_filtre_css' => 'Zet de stijlen binnen &lt;head&gt; en &lt;/head&gt; om in "inline" stijlen, wat zinvol is voor webmails.',
	'facteur_filtre_images' => 'Voeg afbeeldingen in',
	'facteur_filtre_iso_8859' => 'Omzetten in ISO-8859-1',
	'facteur_filtres' => 'Filters',
	'facteur_filtres_descriptif' => 'Bij het verzenden kunnen bepaalde filters worden toegepast.',
	'facteur_smtp_auth' => 'Vereist authentificatie:',
	'facteur_smtp_auth_non' => 'nee',
	'facteur_smtp_auth_oui' => 'ja',
	'facteur_smtp_host' => 'Host:',
	'facteur_smtp_password' => 'Wachtwoord:',
	'facteur_smtp_port' => 'Poort:',
	'facteur_smtp_secure' => 'Beveiligde verbinding:',
	'facteur_smtp_secure_non' => 'nee',
	'facteur_smtp_secure_ssl' => 'SSL (gedeprecieerd)',
	'facteur_smtp_secure_tls' => 'TLS (aanbevolen)',
	'facteur_smtp_sender' => 'Return-Path (optioneel)',
	'facteur_smtp_sender_descriptif' => 'Geef het Return-Path voor de mail aan, bv voor feedback of fouten.',
	'facteur_smtp_tls_allow_self_signed_non' => 'het SSL certificaat van de SMTP server is uitgegeven door een Certificaatautoriteit (aanbevolen).',
	'facteur_smtp_tls_allow_self_signed_oui' => 'het SSL certificaat van de SMTP server is automatisch ondertekend.',
	'facteur_smtp_username' => 'Naam van de gebruiker:',

	// I
	'info_envois_bloques_constante' => 'Verzending wordt volledig geblokkeerd door de constante <tt>_TEST_EMAIL_DEST</tt>.',
	'info_envois_forces_vers_email' => 'Verzending wordt geforceerd naar adres <b>@email@</b> door de  constante <tt>_TEST_EMAIL_DEST</tt>',

	// L
	'label_email_test_from' => 'Verzender',
	'label_email_test_from_placeholder' => 'from@example.org (optioneel)',
	'label_email_test_important' => 'Deze e-mail is belangrijk',
	'label_facteur_forcer_from' => 'Forceer dit verzendadres wanneer de <tt>From</tt> niet tot hetzelfde domein behoort',
	'label_mailjet_api_key' => 'Sleutel API Mailjet',
	'label_mailjet_api_version' => 'API Versie',
	'label_mailjet_secret_key' => 'Geheime sleutel Mailjet',
	'label_message_envoye' => 'Bericht verzonden:',
	'label_utiliser_mailjet' => 'Mailjet gebruiken',

	// M
	'message_identite_email' => 'De <a href="@url@">configuratie van plugin <i>Facteur</i></a> laadt dit e-mailadres met <b>@email@</b> voor het verzenden van mails.',

	// N
	'note_test_configuration' => 'Een e-mail wordt naar het aangegeven adres verzonden.',

	// P
	'personnaliser' => 'Instellingen aanpassen',

	// S
	'sujet_alerte_mail_fail' => '[MAIL] FAIL verzending aan @dest@ (status: @sujet@)',

	// T
	'tester' => 'Testen',
	'tester_la_configuration' => 'Configuratietest',
	'titre_configurer_facteur' => 'Configuratie van Postbode (<i>Facteur</i>)',

	// U
	'utiliser_mail' => 'Gebruik de PHP <tt>mail()</tt> functie',
	'utiliser_reglages_site' => 'Gebruik de instellingen van de SPIP site',
	'utiliser_smtp' => 'Gebruik SMTP',

	// V
	'valider' => 'Bevestigen',
	'version_html' => 'HTML-versie.',
	'version_texte' => 'Tekstversie.'
);
