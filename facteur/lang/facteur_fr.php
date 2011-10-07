<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// C
	'config_info_enregistree' => 'La configuration du facteur a bien été enregistrée',
	'configuration_adresse_envoi' => "Configuration de l'adresse d'envoi",
	'configuration_facteur' => 'Facteur',
	'configuration_mailer' => 'Configuration du mailer',
	'configuration_smtp' => 'Choix de la m&eacute;thode d\'envoi de mail',
	'configuration_smtp_descriptif' => 'Si vous n\'&ecirc;tes pas s&ucirc;rs, choisissez la fonction mail de PHP.',
	'corps_email_de_test' => "Ceci est un email de test accentué",  // Laisser tel quel, car
	
	// E
	'email_test_envoye' => "L'email de test a correctement &eacute;t&eacute; envoy&eacute;. Si vous ne le recevez pas correctement, v&eacute;rifiez la configuration de votre serveur ou contactez un administrateur du serveur.",
	'erreur' => "Erreur",
	'erreur_generale' => 'Il y a une ou plusieurs erreurs de configuration. Veuillez v&eacute;rifier le contenu du formulaire.',
	'erreur_invalid_host' => 'Ce nom d\'h&ocirc;te n\'est pas correct',
	'erreur_invalid_port' => 'Ce num&eacute;ro de port n\'est pas correct',
	
	// F
	'facteur_adresse_envoi_email' => "Email :",
	'facteur_adresse_envoi_nom' => "Nom :",
	'facteur_bcc' => "Copie Cach&eacute;e (BCC) :",
	'facteur_cc' => "Copie (CC) :",
	'facteur_copies' => "Copies :",
	'facteur_copies_descriptif' => "Un email sera envoy&eacute; en copie aux adresses d&eacute;finies. Une seule adresse en copie et/ou une seule adresse en copie cach&eacute;e.",
	'facteur_filtre_accents' => "Transformer les accents en leur entit&eacute;s html (utile pour Hotmail notamment).",
	'facteur_filtre_css' => "Transformer les styles contenus entre &lt;head&gt; et &lt;/head&gt; en des styles \"en ligne\", utile pour les webmails car les styles en ligne ont la priorit&eacute; sur les styles externes.",
	'facteur_filtre_iso_8859' => "Convertir en ISO-8859-1",
	'facteur_filtre_images' => "Embarquer les images r&eacute;f&eacute;renc&eacute;es dans les emails",
	'facteur_filtres' => "Filtres",
	'facteur_filtres_descriptif' => "Des filtres peuvent &ecirc;tre appliqu&eacute;s aux emails au moment de l'envoi.",
	'facteur_smtp_auth' => 'Requiert une authentification :',
	'facteur_smtp_auth_oui' => 'oui',
	'facteur_smtp_auth_non' => 'non',
	'facteur_smtp_host' => 'Hôte :',
	'facteur_smtp_password' => 'Mot de passe :',
	'facteur_smtp_port' => 'Port :',
	'facteur_smtp_secure' => 'Connexion s&eacute;curis&eacute;e :',
	'facteur_smtp_secure_non' => 'non',
	'facteur_smtp_secure_ssl' => 'SSL',
	'facteur_smtp_secure_tls' => 'TLS',
	'facteur_smtp_sender' => 'Retour des erreurs (optionnel)',
	'facteur_smtp_sender_descriptif' => 'D&eacute;finit dans l\'ent&ecirc;te du mail l\'adresse email de retour des erreurs (ou Return-Path), et lors d\'un envoi via la m&eacute;thode SMTP cela d&eacute;finit aussi l\'adresse de l\'envoyeur.',
	'facteur_smtp_username' => 'Nom d\'utilisateur :',
	
	// N
	'note_test_configuration' => "Un email sera envoy&eacute; &agrave; l'adresse d'envoi d&eacute;finie (ou celle du webmaster).",
	
	// P
	'personnaliser' => "Personnaliser ces r&eacute;glages",
	
	// T
	'tester' => "Tester",
	'tester_la_configuration' => "Tester la configuration",
	
	// U
	'utiliser_mail' => 'Utiliser la fonction mail de PHP',
	'utiliser_reglages_site' => "Utiliser les r&eacute;glages du site SPIP : le nom affich&eacute; sera le nom du site SPIP et l'adresse email sera celle du webmaster",
	'utiliser_smtp' => 'Utiliser SMTP',
	
	// V
	'valider' => "Valider",
	'version_html' => "Version HTML.",
	'version_texte' => "Version texte.",
	
	'Z' => 'ZZzZZzzz'

);

?>