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


		'logo_notifications' => "LOGO DES NOTIFICATIONS",
		'configuration_notifications' => 'Notifications',
		'configuration_adresse_envoi' => "Configuration de l'adresse d'envoi",
		'utiliser_reglages_site' => "Utiliser les réglages du site SPIP : le nom affiché sera le nom du site SPIP et l'adresse email sera celle du webmaster",
		'personnaliser' => "Personnaliser ces réglages",
		'spip_notifications_adresse_envoi_nom' => "Nom :",
		'spip_notifications_adresse_envoi_email' => "Email :",
		'configuration_mailer' => 'Configuration du mailer',
		'configuration_smtp' => 'Choix du mailer',
		'configuration_smtp_descriptif' => 'Si vous n\'êtes pas sûrs, choisissez la fonction mail de PHP.',
		'utiliser_mail' => 'Utiliser la fonction mail de PHP',
		'utiliser_smtp' => 'Utiliser SMTP',
		'spip_notifications_smtp_host' => 'Hôte :',
		'spip_notifications_smtp_port' => 'Port :',
		'spip_notifications_smtp_auth' => 'Requiert une authentification :',
		'spip_notifications_smtp_auth_oui' => 'oui',
		'spip_notifications_smtp_auth_non' => 'non',
		'spip_notifications_smtp_username' => 'Nom d\'utilisateur :',
		'spip_notifications_smtp_password' => 'Mot de passe :',
		'spip_notifications_smtp_secure' => 'Connexion sécurisée :',
		'spip_notifications_smtp_secure_non' => 'non',
		'spip_notifications_smtp_secure_ssl' => 'SSL',
		'spip_notifications_smtp_secure_tls' => 'TLS',
		'spip_notifications_smtp_sender' => 'Retour des erreurs (optionnel)',
		'spip_notifications_smtp_sender_descriptif' => 'Définit dans l\'entête du mail l\'adresse email de retour des erreurs (ou Return-Path), et lors d\'un envoi via la méthode SMTP cela définit aussi l\'adresse de l\'envoyeur.',
		'spip_notifications_filtres' => "Filtres",
		'spip_notifications_filtres_descriptif' => "Des filtres peuvent être appliqués aux notifications au moment de l'envoi.",
		'spip_notifications_filtre_images' => "Embarquer les images référencées dans les notifications",
		'spip_notifications_filtre_css' => "Transformer les styles contenus entre &lt;head&gt; et &lt;/head&gt; en des styles \"en ligne\", utile pour les webmails car les styles en ligne ont la priorité sur les styles externes.",
		'spip_notifications_filtre_accents' => "Transformer les accents en leur entités html (utile pour Hotmail notamment).",
		'spip_notifications_filtre_iso_8859' => "Convertir en ISO-8859-1",
		'valider' => "Valider",
		'purger' => "Purger les notifications",
		'notifications_personnalisables' => "NOTIFICATIONS PERSONNALISABLES",
		'tester_la_configuration' => "Tester la configuration",
		'note_test_configuration' => "Une notification sera envoyée à l'adresse d'envoi définie (ou celle du webmaster).",
		'notification_de_test' => "Notification de test",
		'version_html' => "Version HTML.",
		'version_texte' => "Version texte.",
		'tester' => "Tester",
		'erreur' => "Erreur",
		'notification_envoyee' => "Notification envoyée !",

		'Z' => 'ZZzZZzzz'

	);

?>