<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de https://trad.spip.net/tradlang_module/mailshot?lang_cible=de
// ** ne pas modifier le fichier **

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// C
	'cfg_exemple' => 'Beispiel',
	'cfg_exemple_explication' => 'Erläuterung dieses Beispiels',
	'cfg_titre_parametrages' => 'Mailings konfigurieren',

	// E
	'erreur_aucun_service_configure' => 'Kein Ausgangsserver eingerichtet. <a href="@url@">Ausgangsserver einrichten</a>',
	'erreur_envoi_mail_bloque_debug' => 'Mailing blockiert von <tt>_TEST_EMAIL_DEST</tt>', # MODIF
	'erreur_envoi_mail_force_debug' => 'Versand an @email@ erzwungen von <tt>_TEST_EMAIL_DEST</tt>', # MODIF
	'erreur_generation_newsletter' => 'Beim Erzeugen des Newsletters ist ein Fehler aufgetreten', # MODIF
	'explication_boost_send' => 'In diesem Modus werden die Mails so schnell wie möglich verschickt. Es wird keine Begrenzung berücksichtigt. Der Schnellversand ist nicht empfehlenswert, da er die Wahrscheinlichkeit der Einstufung der Mails als SPAM vergrößert.',
	'explication_purger_historique' => 'Für jeden Massenversand werden die Empfänger in der Datenbank gespeichert, mit Informationen zum Status des jeweiligen Versand.
Dies führt bei zahlreichem Versenden zu einer großen Datenmenge, weshalb es ratsam ist diese Versanddetails nach einer gewissen Zeit zu löschen.',
	'explication_rate_limit' => 'Obergrenze der pro Tag versendeten Mails einstellen oder freilassen um keine Begrenzung einzustellen',

	// I
	'info_1_mailshot' => '1 Auslieferung',
	'info_1_mailshot_destinataire' => '1 Empfänger',
	'info_1_mailsubscriber' => '1 Abonnent',
	'info_annuler_envoi' => 'Versand abrechen',
	'info_archiver' => 'Archivieren',
	'info_aucun_destinataire' => 'Kein Empfänger',
	'info_aucun_envoi' => 'Kein Versand',
	'info_envoi_programme_1_destinataire' => 'Versand an einen Empfänger konfiguriert',
	'info_envoi_programme_nb_destinataires' => 'Versand an @nb@ Empfänger konfiguriert',
	'info_mailshot_no' => 'Auslieferung Nr. @id@',
	'info_nb_mailshots' => '@nb@ Auslieferungen',
	'info_nb_mailshots_destinataires' => '@nb@ Empfänger',
	'info_nb_mailsubscribers' => '@nb@ Abonnenten',
	'info_statut_archive' => 'archiviert',
	'info_statut_cancel' => 'Abgebrochen',
	'info_statut_destinataire_clic' => 'Angeklickt',
	'info_statut_destinataire_fail' => 'Fehlgeschlagen',
	'info_statut_destinataire_read' => 'Offen',
	'info_statut_destinataire_sent' => 'Verschickt',
	'info_statut_destinataire_spam' => '>Spam',
	'info_statut_destinataire_todo' => 'Zu versenden',
	'info_statut_end' => 'Beendet',
	'info_statut_init' => 'Geplant',
	'info_statut_pause' => 'Pause',
	'info_statut_poubelle' => 'Gelöscht',
	'info_statut_processing' => 'In Bearbeitung',

	// L
	'label_avancement' => 'Fortschritt',
	'label_boost_send_oui' => 'Schnellversand',
	'label_control_pause' => 'Pause',
	'label_control_play' => 'Neustart',
	'label_control_stop' => 'Abbruch',
	'label_date_fin' => 'Enddatum des Versands',
	'label_date_start' => 'Anfangsdatum des Versands',
	'label_envoi' => 'Versand',
	'label_from' => 'Absender',
	'label_html' => 'HTML-Version',
	'label_listes' => 'Listen',
	'label_mailer_defaut' => 'Den selben Versandservice wie für alle anderen Mails verwenden',
	'label_mailer_defaut_desactive' => 'Unmöglich: kein Versandservice eingestellt',
	'label_mailer_mailjet' => 'Mailjet',
	'label_mailer_mandrill' => 'Mandrill',
	'label_mailer_smtp' => 'SMTP-Server',
	'label_mailer_sparkpost' => 'Sparkpost',
	'label_mailjet_api_key' => 'Mailjet API Schlüssel',
	'label_mailjet_api_version' => 'API Version',
	'label_mailjet_secret_key' => 'Geheimer Mailjet Schlüssel',
	'label_mandrill_api_key' => 'Mandrill API Key',
	'label_purger_historique_delai' => 'Älter als',
	'label_purger_historique_oui' => 'Details alter Versandvorgänge löschen',
	'label_rate_limit' => 'Versandgeschwindigkeit beschränken',
	'label_sparkpost_api_endpoint' => 'Endpoint API',
	'label_sparkpost_api_key' => 'Sparkpost API Key',
	'label_sujet' => 'Gegenstand',
	'label_texte' => 'Textversion',
	'legend_configuration_adresse_envoi' => 'Absenderadresse',
	'legend_configuration_historique' => 'Versandhistorie',
	'legend_configuration_mailer' => 'Mail-Versandservice',
	'lien_voir_newsletter' => 'Newsletter ansehen',

	// M
	'mailshot_titre' => 'MailShot',

	// T
	'texte_changer_statut_mailshot' => 'Mailingstatus:',
	'texte_statut_archive' => 'archiviert',
	'texte_statut_cancel' => 'Abgebrochen',
	'texte_statut_end' => 'Beendet',
	'texte_statut_init' => 'geplant',
	'texte_statut_pause' => 'Pause',
	'texte_statut_processing' => 'Wird ausgeführt',
	'titre_envois_archives' => 'Versandarchiv',
	'titre_envois_destinataires_fail' => 'Fehlgeschlagene Sendungen',
	'titre_envois_destinataires_init_encours' => 'Kein Empfänger vorprogrammiert (wird initialisiert)',
	'titre_envois_destinataires_ok' => 'Erfolgreiche Sendungen',
	'titre_envois_destinataires_sent' => 'Erfolgreiche Sendungen',
	'titre_envois_destinataires_todo' => 'Anstehende Sendungen',
	'titre_envois_en_cours' => 'Versand wird ausgeführt',
	'titre_envois_planifies' => 'Geplante Versendungen',
	'titre_envois_termines' => 'Versand beeendet',
	'titre_mailshot' => 'Mailing',
	'titre_mailshots' => 'Mailings',
	'titre_menu_mailshots' => 'Mailing-Verwaltung',
	'titre_page_configurer_mailshot' => 'MailShot'
);
