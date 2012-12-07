<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP

if (!defined('_ECRIRE_INC_VERSION')) return;
 
$GLOBALS[$GLOBALS['idx_lang']] = array(

	// M
	'mailshot_titre' => 'MailShot',

	// C
	'cfg_exemple' => 'Exemple',
	'cfg_exemple_explication' => 'Explication de cet exemple',
	'cfg_titre_parametrages' => 'Paramétrages',

	// E
	'erreur_aucun_service_configure' => 'Aucun service d\'envoi configuré. <a href="@url@">Configurer un service</a>',
	'erreur_envoi_mail_bloque_debug' => 'Envoi du mail bloqué par <tt>_TEST_EMAIL_DEST</tt>',
	'erreur_envoi_mail_force_debug' => 'Envoi du mail forcé vers @email@ par <tt>_TEST_EMAIL_DEST</tt>',

	// L
	'label_mailer_defaut' => 'Utiliser le même service d\'envoi que pour les autres mails',
	'label_mailer_defaut_desactive' => '(aucun service d\'envoi configuré par défaut)',
	'label_mailer_smtp' => 'Serveur SMTP',

	'legend_configuration_adresse_envoi' => 'Adresse d\'envoi',
	'legend_configuration_mailer' => 'Service d\'envoi des mails',

	// T
	'titre_page_configurer_mailshot' => 'MailShot',
);

?>