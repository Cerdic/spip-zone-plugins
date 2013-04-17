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
	'erreur_generation_newsletter' => 'Une erreur est survenue lors de la génération de la newsletter',

	// I
	'info_aucun_envoi' => 'Aucun envoi',
	'info_envoi_programme_1_destinataire' => 'Envoi programmé vers 1 destinataire',
	'info_envoi_programme_nb_destinataires' => 'Envoi programmé vers @nb@ destinataires',
	'info_1_mailsubscriber' => '1 inscrit',
	'info_nb_mailsubscribers' => '@nb@ inscrits',
	'info_1_mailshot' => '1 envoi',
	'info_nb_mailshots' => '@nb@ envois',
	'info_1_mailshot_destinataire' => '1 destinataire',
	'info_nb_mailshots_destinataires' => '@nb@ destinataires',
	'info_aucun_destinataire' => 'Aucun destinataire',
	'info_mailshot_no' => 'Envoi N°@id@',

	'info_statut_pause' => 'Pause',
	'info_statut_processing' => 'En cours',
	'info_statut_end' => 'Fini',
	'info_statut_cancel' => 'Annulé',
	'info_statut_poubelle' => 'Poubelle',

	'info_statut_destinataire_todo' => 'À envoyer',
	'info_statut_destinataire_sent' => 'Envoyé',
	'info_statut_destinataire_read' => 'Ouvert',
	'info_statut_destinataire_clic' => 'Cliqué',
	'info_statut_destinataire_fail' => 'Échoué',
	'info_statut_destinataire_spam' => '>Spam',


	// L
	'label_envoi' => 'Envoi',
	'label_sujet' => 'Sujet',
	'label_html' => 'Version HTML',
	'label_texte' => 'Version Texte',
	'label_date_start' => 'Date de début d\'envoi',
	'label_date_fin' => 'Date de fin d\'envoi',
	'label_listes' => 'Listes',
	'label_avancement' => 'Avancement',
	'lien_voir_newsletter' => 'Voir l\'infolettre',

	'label_mailer_defaut' => 'Utiliser le même service d\'envoi que pour les autres mails',
	'label_mailer_defaut_desactive' => 'Impossible : aucun service d\'envoi d\'email n\'est configuré',
	'label_mailer_smtp' => 'Serveur SMTP',
	'label_mailer_mandrill' => 'Service Mandrill',
	'label_mandrill_api_key' => 'Mandrill API Key',
	'label_control_pause' => 'Pause',
	'label_control_play' => 'Relancer',
	'label_control_stop' => 'Abandonner',
	'label_rate_limit' => 'Limiter la cadence d\'envoi',
	'label_boost_send_oui' => 'Envoi rapide',
	'explication_rate_limit' => 'Indiquer le nombre maximum de mail envoyés par jour ou laisser vide pour ne pas fixer de limite',
	'explication_boost_send' => 'Dans ce mode, les mails seront envoyés aussi vite que possible. Aucune limite de cadence n\'est prise en compte.
										L\'envoi rapide est déconseillé car il augmente le risque de classement en SPAM.',

	'legend_configuration_adresse_envoi' => 'Adresse d\'envoi',
	'legend_configuration_mailer' => 'Service d\'envoi des mails',

	// T
	'titre_page_configurer_mailshot' => 'MailShot',
	'titre_menu_mailshots' => 'Suivi des envois de mails en nombre',
	'titre_envois_en_cours' => 'Envois en cours',
	'titre_envois_termines' => 'Envois terminés',
	'titre_mailshot' => 'Envoi en nombre',
	'titre_mailshots' => 'Envois en nombre',
	'texte_changer_statut_mailshot' => 'Cet envoi est :',
	'texte_statut_pause' => 'en pause',
	'texte_statut_processing' => 'en cours',
	'texte_statut_end' => 'fini',
	'texte_statut_cancel' => 'annulé',
	'titre_envois_destinataires_todo' => 'Envois a venir',
	'titre_envois_destinataires_ok' => 'Envois réussis',
	'titre_envois_destinataires_fail' => 'Envois echoués',
	'titre_envois_destinataires_init_encours' => 'Aucun destinataire programmé (initialisation en cours)',
);

?>