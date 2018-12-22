<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de https://trad.spip.net/tradlang_module/mailshot?lang_cible=en
// ** ne pas modifier le fichier **

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// C
	'cfg_exemple' => 'Example',
	'cfg_exemple_explication' => 'Explanation of this example',
	'cfg_titre_parametrages' => 'Configure the bulk mailer',

	// E
	'erreur_aucun_service_configure' => 'No configured delivery service . <a Href="@url@"> Configure a service < / a>',
	'erreur_envoi_mail_bloque_debug' => 'Sending email blocked by <tt>_TEST_EMAIL_DEST</tt>', # MODIF
	'erreur_envoi_mail_force_debug' => 'Sending the mail to @email@ forced by <tt>_TEST_EMAIL_DEST</tt>', # MODIF
	'erreur_generation_newsletter' => 'An error has occured in the generation of the newsletter', # MODIF
	'explication_boost_send' => 'In this mode mails are being sent as quickly as possible. Balancing is not taken into account.
										This mode is not recommended as it increases the risk of being classified as SPAM.',
	'explication_purger_historique' => 'For each mass distribution the destinations are saved in the database together with the status.
	This may cause a high volume of data in case of large distribution lists. This is why purging older data is recommended.',
	'explication_rate_limit' => 'Specify the maximum number of mails sent per day, or leave blank to set no limit',

	// I
	'info_1_mailshot' => '1 post',
	'info_1_mailshot_destinataire' => 'One recipient',
	'info_1_mailsubscriber' => '1 subscriber',
	'info_annuler_envoi' => 'Cancel the distribution',
	'info_archiver' => 'Archive',
	'info_aucun_destinataire' => 'No recipient',
	'info_aucun_envoi' => 'No sending',
	'info_envoi_programme_1_destinataire' => 'Sending scheduled to one recipient',
	'info_envoi_programme_nb_destinataires' => 'Sending scheduled to @nb@ recipients',
	'info_mailshot_no' => 'Sending No. @id@',
	'info_nb_mailshots' => '@nb@ posts',
	'info_nb_mailshots_destinataires' => '@nb@ recipients',
	'info_nb_mailsubscribers' => '@nb@ subscribers',
	'info_statut_archive' => 'archived',
	'info_statut_cancel' => 'Canceled',
	'info_statut_destinataire_clic' => 'Hit',
	'info_statut_destinataire_fail' => 'Fail',
	'info_statut_destinataire_read' => 'Open',
	'info_statut_destinataire_sent' => 'Sent',
	'info_statut_destinataire_spam' => '> Spam',
	'info_statut_destinataire_todo' => 'To send',
	'info_statut_end' => 'Finished',
	'info_statut_init' => 'Planned',
	'info_statut_pause' => 'Pause',
	'info_statut_poubelle' => 'Trash',
	'info_statut_processing' => 'In progress',

	// L
	'label_avancement' => 'Progress',
	'label_boost_send_oui' => 'Quick transmission',
	'label_control_pause' => 'Pause',
	'label_control_play' => 'Restart',
	'label_control_stop' => 'Abort',
	'label_date_fin' => 'End Date sending',
	'label_date_start' => 'Date of dispatch',
	'label_envoi' => 'Sending',
	'label_from' => 'Sender',
	'label_html' => 'HTML Version',
	'label_listes' => 'Lists',
	'label_mailer_defaut' => 'Use the same shipping service as other mails',
	'label_mailer_defaut_desactive' => 'Failed : no email sending service configured yet',
	'label_mailer_mailjet' => 'Mailjet',
	'label_mailer_mandrill' => 'Mandrill Service',
	'label_mailer_smtp' => 'SMTP Server',
	'label_mailer_sparkpost' => 'Sparkpost',
	'label_mailjet_api_key' => 'API Mailjet key',
	'label_mailjet_api_version' => 'API Version',
	'label_mailjet_secret_key' => 'Secret Mailjet key',
	'label_mandrill_api_key' => 'Mandrill API Key',
	'label_purger_historique_delai' => 'Older than',
	'label_purger_historique_oui' => 'Purge details of old distributions',
	'label_rate_limit' => 'Limit sending rate',
	'label_sparkpost_api_endpoint' => 'API Endpoint',
	'label_sparkpost_api_key' => 'Sparkpost API Key',
	'label_sujet' => 'Subject',
	'label_texte' => 'Text Version',
	'legend_configuration_adresse_envoi' => 'Shipping Address',
	'legend_configuration_historique' => 'History of distributions',
	'legend_configuration_mailer' => 'Service for sending mails',
	'lien_voir_newsletter' => 'View Newsletter',

	// M
	'mailshot_titre' => 'MailShot',

	// T
	'texte_changer_statut_mailshot' => 'This item is :',
	'texte_statut_archive' => 'archived',
	'texte_statut_cancel' => 'canceled',
	'texte_statut_end' => 'finished',
	'texte_statut_init' => 'Planned',
	'texte_statut_pause' => 'paused',
	'texte_statut_processing' => 'In progress',
	'titre_envois_archives' => 'Distributions archived',
	'titre_envois_destinataires_fail' => 'Failed Mailings',
	'titre_envois_destinataires_init_encours' => 'No recipient programmed (initialization in progress)',
	'titre_envois_destinataires_ok' => 'Shipments successful',
	'titre_envois_destinataires_sent' => 'Shipments successful',
	'titre_envois_destinataires_todo' => 'Mailings to come',
	'titre_envois_en_cours' => 'Sending in progress',
	'titre_envois_planifies' => 'Scheduled distributions',
	'titre_envois_termines' => 'Sending completed',
	'titre_mailshot' => 'Bulk mailing',
	'titre_mailshots' => 'Bulk mailings',
	'titre_menu_mailshots' => 'Track & Trace bulk mailings',
	'titre_page_configurer_mailshot' => 'Mailshot'
);
