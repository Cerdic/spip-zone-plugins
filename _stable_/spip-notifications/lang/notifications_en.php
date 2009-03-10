<?php


	/**
	 * SPIP-Notifications : Notifications au format HTML, mixte ou texte et envoi via mail (PHP) ou SMTP
	 *
	 * Copyright (c) 2006-2009
	 * Agence Artégo http://www.artego.fr
	 *  
	 * Ce programme est un logiciel libre distribue sous licence GNU/GPLv3.
	 * Pour plus de details voir http://www.gnu.org/licenses/gpl-3.0.html
	 *  
	 **/


	$GLOBALS[$GLOBALS['idx_lang']] = array(


		'logo_notifications' => "LOGO OF NOTIFICATIONS",
		'configuration_notifications' => 'Notifications',
		'configuration_adresse_envoi' => "Sender's address",
		'utiliser_reglages_site' => "Use the site's settings : the email address is the webmaster's one and the name of the website is the name of the sender",
		'personnaliser' => "Customize",
		'spip_notifications_adresse_envoi_nom' => "Name :",
		'spip_notifications_adresse_envoi_email' => "Email :",
		'configuration_mailer' => 'Mailer\'s configuration',
		'configuration_smtp' => 'Choose your mailer',
		'configuration_smtp_descriptif' => 'If you\'re not sure about the settings, leave them set to "PHP mail".',
		'utiliser_mail' => 'Use mail function from PHP',
		'utiliser_smtp' => 'Use SMTP',
		'spip_notifications_smtp_host' => 'Host :',
		'spip_notifications_smtp_port' => 'Port :',
		'spip_notifications_smtp_auth' => 'Requires authentication :',
		'spip_notifications_smtp_auth_oui' => 'yes',
		'spip_notifications_smtp_auth_non' => 'no',
		'spip_notifications_smtp_username' => 'Username :',
		'spip_notifications_smtp_password' => 'Paswword :',
		'spip_notifications_smtp_secure' => 'Secure :',
		'spip_notifications_smtp_secure_non' => 'no',
		'spip_notifications_smtp_secure_ssl' => 'SSL',
		'spip_notifications_smtp_secure_tls' => 'TLS',
		'spip_notifications_smtp_sender' => 'Return-Path (optional)',
		'spip_notifications_smtp_sender_descriptif' => 'Define the Return-Path in the mail header, useful for error feedback, also in SMTP mode it defines the sender\'s email.',
		'spip_notifications_filtres' => "Filters",
		'spip_notifications_filtres_descriptif' => "Some filters can be applied before sending a notification.",
		'spip_notifications_filtre_images' => "Embed images referenced in notifications",
		'spip_notifications_filtre_css' => "Transform styles present between &lt;head&gt; and &lt;/head&gt; into inline styles, useful for webmails because inline styles overwrite external styles.",
		'spip_notifications_filtre_accents' => "Transform accents into their html entities (useful for Hotmail).",
		'valider' => "Submit",
		'purger' => "Purge notifications",
		'notifications_personnalisables' => "CUSTOMISABLE NOTIFICATIONS",
		'tester_la_configuration' => "Test the config",
		'note_test_configuration' => "A notification will be sent to the \"sender\".",
		'notification_de_test' => "Test notification",
		'version_html' => "HTML version.",
		'version_texte' => "Text version.",
		'tester' => "Test",
		'erreur' => "Error",
		'notification_envoyee' => "Notification sent !",

		'Z' => 'ZZzZZzzz'

	);

?>