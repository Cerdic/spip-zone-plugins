<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP

if (!defined('_ECRIRE_INC_VERSION')) return;
 
$GLOBALS[$GLOBALS['idx_lang']] = array(

	// M
	'mailshot_titre' => 'MailShot',

	// C
	'cfg_exemple' => 'Exemple',
	'cfg_exemple_explication' => 'Explication de cet exemple',
	'cfg_titre_parametrages' => 'Configurer l\'envoi de mails en nombre',

	// E
	'erreur_aucun_service_configure' => 'Aucun service d\'envoi configuré. <a href="@url@">Configurer un service</a>',
	'erreur_envoi_mail_bloque_debug' => 'Envoi du mail bloqué par <tt>_TEST_EMAIL_DEST</tt>',
	'erreur_envoi_mail_force_debug' => 'Envoi du mail forcé vers @email@ par <tt>_TEST_EMAIL_DEST</tt>',

	// I
	'info_aucun_envoi' => 'Aucun envoi',
	'info_envoi_programme_1_destinataire' => 'Envoi programmé vers 1 destinataire',
	'info_envoi_programme_nb_destinataires' => 'Envoi programmé vers @nb@ destinataires',
	'info_1_mailsubscriber' => '1 inscrit',
	'info_nb_mailsubscribers' => '@nb@ inscrits',
	'info_1_mailshot' => '1 envoi',
	'info_nb_mailshot' => '@nb@ envois',

	'info_statut_pause' => 'Pause',
	'info_statut_processing' => 'En cours',
	'info_statut_end' => 'Fini',
	'info_statut_cancel' => 'Annulé',
	'info_statut_poubelle' => 'Poubelle',


	// L
	'label_sujet' => 'Sujet',
	'label_mailer_defaut' => 'Utiliser le même service d\'envoi que pour les autres mails',
	'label_mailer_defaut_desactive' => '(aucun service d\'envoi configuré par défaut)',
	'label_mailer_smtp' => 'Serveur SMTP',
	'label_control_pause' => 'Pause',
	'label_control_play' => 'Relancer',
	'label_control_stop' => 'Abandonner',
	'label_rate_limit' => 'Limiter la cadence d\'envoi',
	'explication_rate_limit' => 'Indiquer le nombre maximum de mail envoyés par jour ou laisser vide pour ne pas fixer de limite',

	'legend_configuration_adresse_envoi' => 'Adresse d\'envoi',
	'legend_configuration_mailer' => 'Service d\'envoi des mails',

	// T
	'titre_page_configurer_mailshot' => 'MailShot',
	'titre_menu_mailshot' => 'Suivi des envois de mails en nombre',
	'titre_envois_en_cours' => 'Envois en cours',
	'titre_envois_termines' => 'Envois terminés',
);

?>