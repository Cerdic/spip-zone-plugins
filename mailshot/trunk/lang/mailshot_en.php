<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP

if (!defined('_ECRIRE_INC_VERSION')) return;
 
$GLOBALS[$GLOBALS['idx_lang']] = array(

	// M
  "mailshot_titre" =>"MailShot",

	// C
  "cfg_exemple" => "Example",
	"cfg_exemple_explication" => "Explanation of this example",
	"cfg_titre_parametrages" => "Configure the bulk mailer",

	// E
	"erreur_aucun_service_configure" => "No configured delivery service . <a Href=\"@url@\"> Configure a service < / a>",
	"erreur_envoi_mail_bloque_debug" => "Sending email blocked by <tt>_TEST_EMAIL_DEST</tt>",
	"erreur_envoi_mail_force_debug" => "Sending the mail to @email@ forced by <tt>_TEST_EMAIL_DEST</tt>",

	// I
	"info_aucun_envoi" => "No sending",
	"info_envoi_programme_1_destinataire" => "Sending scheduled to one recipient",
	"info_envoi_programme_nb_destinataires" => "Sending scheduled to @nb@ recipients",
	"info_1_mailsubscriber" => "1 subscriber",
	"info_nb_mailsubscribers" => "@nb@ subscribers",
	"info_1_mailshot" => "1 post",
	"info_nb_mailshots" => "@nb@ posts",
	"info_1_mailshot_destinataire" => "One recipient",
	"info_nb_mailshots_destinataires" => "@nb@ recipients",
	"info_aucun_destinataire" => "No recipient",
	"info_mailshot_no" => "Sending No. @id@",

	"info_statut_pause" => "Pause",
	"info_statut_processing" => "In progress",
	"info_statut_end" => "Finished",
	"info_statut_cancel" => "Canceled",
	"info_statut_poubelle" => "Trash",

	"info_statut_destinataire_todo" => "To send",
	"info_statut_destinataire_sent" => "Sent",
	"info_statut_destinataire_read" => "Open",
	"info_statut_destinataire_clic" => "Hit",
	"info_statut_destinataire_fail" => "Fail",
	"info_statut_destinataire_spam" => "> Spam",


	// L
	"label_envoi" => "Sending",
	"label_sujet" => "Subject",
	"label_html" => "HTML Version",
	"label_texte" => "Text Version",
	"label_date_start" => "Date of dispatch",
	"label_date_fin" => "End Date sending",
	"label_listes" => "Lists",
	"label_avancement" => "Progress",
	"lien_voir_newsletter" => "View Newsletter",

	"label_mailer_defaut" => "Use the same shipping service as other mails",
	"label_mailer_defaut_desactive" => "Failed : no email sending service configured yet",
	"label_mailer_smtp" => "SMTP Server",
	"label_mailer_mandrill" => "Mandrill Service",
	"label_mandrill_api_key" => "Mandrill API Key",
	"label_control_pause" => "Pause",
	"label_control_play" => "Restart",
	"label_control_stop" => "Abort",
	"label_rate_limit" => "Limit sending rate",
	"explication_rate_limit" => "Specify the maximum number of mails sent per day, or leave blank to set no limit",

	"legend_configuration_adresse_envoi" => "Shipping Address",
	"legend_configuration_mailer" => "Service for sending mails",

	// T
	"titre_page_configurer_mailshot" => "Mailshot",
	"titre_menu_mailshots" => "Track & Trace bulk mailings",
	"titre_envois_en_cours" => "Sending in progress",
	"titre_envois_termines" => "Sending completed",
	"titre_mailshot" => "Bulk mailing",
	"titre_mailshots" => "Bulk mailings",
	"texte_changer_statut_mailshot" => "This item is :",
	"texte_statut_pause" => "paused",
	"texte_statut_processing" => "In progress",
	"texte_statut_end" => "finished",
	"texte_statut_cancel" => "canceled",
	"titre_envois_destinataires_todo" => "Mailings to come",
	"titre_envois_destinataires_ok" => "Shipments successful",
	"titre_envois_destinataires_fail" => "Failed Mailings",
	"titre_envois_destinataires_init_encours" => "No recipient programmed (initialization in progress)",
);

?>