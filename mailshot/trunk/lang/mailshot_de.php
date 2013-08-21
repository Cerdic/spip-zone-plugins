<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP

if (!defined('_ECRIRE_INC_VERSION')) return;
 
$GLOBALS[$GLOBALS['idx_lang']] = array(

	// M
	'mailshot_titre' => 'MailShot',

	// C
	'cfg_exemple' => 'Exemple',
	'cfg_exemple_explication' => 'Erläuterung des Beispiels',
	'cfg_titre_parametrages' => 'Mailings konfigurieren',

	// E
	'erreur_aucun_service_configure' => 'Kein Ausgangsserver eingerichtet. <a href="@url@">Ausgangsserver einrichten</a>',
	'erreur_envoi_mail_bloque_debug' => 'Mailing blockiert von <tt>_TEST_EMAIL_DEST</tt>',
	'erreur_envoi_mail_force_debug' => 'Versand an @email@ erzwungen von <tt>_TEST_EMAIL_DEST</tt>',
	'erreur_generation_newsletter' => 'Beim Erzeugen des Newsletters ist ein Fehler aufgetreten',

	// I
	'info_aucun_envoi' => 'Kein Versand',
	'info_envoi_programme_1_destinataire' => 'Versand an einen Empfänger konfiguriert',
	'info_envoi_programme_nb_destinataires' => 'Versand an @nb@ Empfänger konfiguriert',
	'info_1_mailsubscriber' => '1 Abonnent',
	'info_nb_mailsubscribers' => '@nb@ Abonnenten',
	'info_1_mailshot' => '1 Auslieferung',
	'info_nb_mailshots' => '@nb@ Auslieferungen',
	'info_1_mailshot_destinataire' => '1 Empfänger',
	'info_nb_mailshots_destinataires' => '@nb@ Empfänger',
	'info_aucun_destinataire' => 'Kein Empfänger',
	'info_mailshot_no' => 'Auslieferung N°@id@',

	'info_statut_pause' => 'Pause',
	'info_statut_processing' => 'In Bearbeitung',
	'info_statut_end' => 'Beendet',
	'info_statut_cancel' => 'Abgebrochen',
	'info_statut_poubelle' => 'Gelöscht',

	'info_statut_destinataire_todo' => 'Zu versenden',
	'info_statut_destinataire_sent' => 'Verschickt',
	'info_statut_destinataire_read' => 'Offen',
	'info_statut_destinataire_clic' => 'Angeklickt',
	'info_statut_destinataire_fail' => 'Fehlgeschlagen',
	'info_statut_destinataire_spam' => '>Spam',


	// L
	'label_envoi' => 'Versand',
	'label_sujet' => 'Gegenstand',
	'label_html' => 'HTML-Version',
	'label_texte' => 'Textversion',
	'label_date_start' => 'Anfangsdatum des Versands',
	'label_date_fin' => 'Enddatum des Versands',
	'label_listes' => 'Listen',
	'label_avancement' => 'Fortschritt',
	'lien_voir_newsletter' => 'Newsletter ansehen',

	'label_mailer_defaut' => 'Den selben Versandservice wie für alle anderen Mails verwenden',
	'label_mailer_defaut_desactive' => 'Unmöglich: kein Versandservice eingestellt',
	'label_mailer_smtp' => 'SMTP-Server',
	'label_mailer_mandrill' => 'Mandrill-Server',
	'label_mandrill_api_key' => 'Mandrill API Key',
	'label_control_pause' => 'Pause',
	'label_control_play' => 'Neustart',
	'label_control_stop' => 'Abbruch',
	'label_rate_limit' => 'Versand- geschwindigkeit beschränken',
	'label_boost_send_oui' => 'Schnellversand',
	'explication_rate_limit' => 'Obergrenze der pro Tag versendeten Mails einstellen oder freilassen um keine Begrenzung einzustellen',
	'explication_boost_send' => 'In diesem Modus werden die Mails so schnell wie möglich verschickt. Es wird keine Begrenzung berücksichtigt. Der Schnellversand ist nicht empfehlenswert, da er die Wahrscheinlichkeit der Einstufung der Mails als SPAM vergrößert.',
	'legend_configuration_adresse_envoi' => 'Absenderadresse',
	'legend_configuration_mailer' => 'Mail-Versandservice',

	// T
	'titre_page_configurer_mailshot' => 'MailShot',
	'titre_menu_mailshots' => 'Mailing-Verwaltung',
	'titre_envois_en_cours' => 'Versand wird ausgeführt',
	'titre_envois_termines' => 'Versand beeendet',
	'titre_mailshot' => 'Mailing',
	'titre_mailshots' => 'Mailings',
	'texte_changer_statut_mailshot' => 'Mailingstatus:',
	'texte_statut_pause' => 'Pause',
	'texte_statut_processing' => 'Wird ausgeführt',
	'texte_statut_end' => 'Beendet',
	'texte_statut_cancel' => 'Abgebrochen',
	'titre_envois_destinataires_todo' => 'Anstehende Sendungen',
	'titre_envois_destinataires_ok' => 'Erfolgreiche Sendungen',
	'titre_envois_destinataires_fail' => 'Fehlgeschlagene Sendungen',
	'titre_envois_destinataires_init_encours' => 'Kein Empfänger vorprogrammiert (wird initialisiert)',
);

?>
