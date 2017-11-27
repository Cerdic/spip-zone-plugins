<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de https://trad.spip.net/tradlang_module/facteur?lang_cible=en
// ** ne pas modifier le fichier **

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// C
	'config_info_enregistree' => 'Postman’s configuration is now saved',
	'configuration_adresse_envoi' => 'Default sending address',
	'configuration_facteur' => 'Postman',
	'configuration_facteur_smtp_tls_allow_self_signed' => 'SSL certificate validation',
	'configuration_mailer' => 'Sending method',
	'configuration_smtp' => 'Choose your mailer',
	'configuration_smtp_descriptif' => 'If you’re not sure about the settings, leave them set to "PHP mail".',
	'corps_email_de_test' => 'This is a test email',

	// E
	'email_envoye_par' => 'Sent by @site@',
	'email_test_envoye' => 'The test email was successfully sent. If you do not receive it correctly, check the configuration of your server or contact a server administrator.',
	'erreur' => 'Error',
	'erreur_dans_log' => ': check the log file for more details',
	'erreur_generale' => 'There are one or more configuration errors. Please check the contents of the form.',
	'erreur_invalid_host' => 'This host name is not valid',
	'erreur_invalid_port' => 'This port number is not valid',

	// F
	'facteur_adresse_envoi_email' => 'Email:',
	'facteur_adresse_envoi_nom' => 'Name:',
	'facteur_bcc' => 'Blind Carbon Copy (BCC):',
	'facteur_cc' => 'Carbon Copy (CC):',
	'facteur_copies' => 'Copies',
	'facteur_copies_descriptif' => 'An email will be sent to specified adresses. One Carbon Copy and/or one Blind Carbon Copy.',
	'facteur_email_test' => 'Send a test email to:',
	'facteur_filtre_accents' => 'Transform accents into their html entities (useful for Hotmail).',
	'facteur_filtre_css' => 'Transform styles present between &lt;head&gt; and &lt;/head&gt; into inline styles, useful for webmails because inline styles overwrite external styles.',
	'facteur_filtre_images' => 'Embed images referenced in emails',
	'facteur_filtre_iso_8859' => 'Convert to ISO-8859-1',
	'facteur_filtres' => 'Filters',
	'facteur_filtres_descriptif' => 'Some filters can be applied before sending an email.',
	'facteur_smtp_auth' => 'Requires authentication:',
	'facteur_smtp_auth_non' => 'no',
	'facteur_smtp_auth_oui' => 'yes',
	'facteur_smtp_host' => 'Host:',
	'facteur_smtp_password' => 'Password:',
	'facteur_smtp_port' => 'Port:',
	'facteur_smtp_secure' => 'Secure:',
	'facteur_smtp_secure_non' => 'no',
	'facteur_smtp_secure_ssl' => 'SSL (depreciated)',
	'facteur_smtp_secure_tls' => 'TLS (recommended)',
	'facteur_smtp_sender' => 'Return-Path (optional)',
	'facteur_smtp_sender_descriptif' => 'Define the Return-Path in the mail header, useful for error feedback.',
	'facteur_smtp_tls_allow_self_signed_non' => 'the SSL certificate of the SMTP server is issued by a Certificate Authority (recommended).',
	'facteur_smtp_tls_allow_self_signed_oui' => 'the SSL certificate of the SMTP server is self-signed.',
	'facteur_smtp_username' => 'Username:',

	// L
	'label_facteur_forcer_from' => 'Force this sending address when <tt>From</tt> is not on the same domain',

	// M
	'message_identite_email' => 'The configuration of the plugin "factor" preset this email address for sending emails.',

	// N
	'note_test_configuration' => 'A test email will be sent to this address.',

	// P
	'personnaliser' => 'Customize',

	// T
	'tester' => 'Test',
	'tester_la_configuration' => 'Test the config',

	// U
	'utiliser_mail' => 'Use mail function from PHP',
	'utiliser_reglages_site' => 'Use the SPIP site’s settings: <br /><tt>@from@</tt>',
	'utiliser_smtp' => 'Use SMTP',

	// V
	'valider' => 'Submit',
	'version_html' => 'HTML version.',
	'version_texte' => 'Text version.'
);
