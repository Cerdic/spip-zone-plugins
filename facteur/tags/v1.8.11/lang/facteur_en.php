<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

'configuration_facteur' => 'Postman',
'configuration_adresse_envoi' => "Sender's address",
'utiliser_reglages_site' => "Use the site's settings : the email address is the webmaster's one and the name of the website is the name of the sender",
'personnaliser' => "Customize",
'facteur_adresse_envoi_nom' => "Name :",
'facteur_adresse_envoi_email' => "Email :",
'facteur_bcc' => "Blind Carbon Copy (BCC) :",
'facteur_cc' => "Carbon Copy (CC) :",
'facteur_copies' => "Copies :",
'facteur_copies_descriptif' => "An email will be sent to specified adresses. One Carbon Copy and/or one Blind Carbon Copy.",
'configuration_mailer' => 'Mailer\'s configuration',
'configuration_smtp' => 'Choose your mailer',
'configuration_smtp_descriptif' => 'If you\'re not sure about the settings, leave them set to "PHP mail".',
'config_info_enregistree' => 'Postman\'s configuration is now saved',
'utiliser_mail' => 'Use mail function from PHP',
'utiliser_smtp' => 'Use SMTP',
'erreur_invalid_host' => 'This host name is not valid',
'erreur_invalid_port' => 'This port number is not valid',
'facteur_smtp_host' => 'Host :',
'facteur_smtp_port' => 'Port :',
'facteur_smtp_auth' => 'Requires authentication :',
'facteur_smtp_auth_oui' => 'yes',
'facteur_smtp_auth_non' => 'no',
'facteur_smtp_username' => 'Username :',
'facteur_smtp_password' => 'Paswword :',
'facteur_smtp_secure' => 'Secure :',
'facteur_smtp_secure_non' => 'no',
'facteur_smtp_secure_ssl' => 'SSL',
'facteur_smtp_secure_tls' => 'TLS',
'facteur_smtp_sender' => 'Return-Path (optional)',
'facteur_smtp_sender_descriptif' => 'Define the Return-Path in the mail header, useful for error feedback, also in SMTP mode it defines the sender\'s email.',
'facteur_filtres' => "Filters",
'facteur_filtres_descriptif' => "Some filters can be applied before sending an email.",
'facteur_filtre_images' => "Embed images referenced in emails",
'facteur_filtre_css' => "Transform styles present between &lt;head&gt; and &lt;/head&gt; into inline styles, useful for webmails because inline styles overwrite external styles.",
'facteur_filtre_accents' => "Transform accents into their html entities (useful for Hotmail).",
'facteur_filtre_iso_8859' => "Convert to ISO-8859-1",
'valider' => "Submit",
'tester_la_configuration' => "Test the config",
'note_test_configuration' => "A test email will be sent to the \"sender\".",
'corps_email_de_test' => "This is a test email",
'version_html' => "HTML version.",
'version_texte' => "Text version.",
'tester' => "Test",
'erreur' => "Error",
'email_test_envoye' => "The test email has been sent without error!",

'Z' => 'ZZzZZzzz'

);

?>