<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de https://trad.spip.net/tradlang_module/mailshot?lang_cible=nl
// ** ne pas modifier le fichier **

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// C
	'cfg_exemple' => 'Voorbeeld',
	'cfg_exemple_explication' => 'Uitleg van dit voorbeeld',
	'cfg_titre_parametrages' => 'Configureer de bulk mailer',

	// E
	'erreur_aucun_service_configure' => 'Geen geconfigureerd delivery service . <a Href="@url@"> configureren van een dienst </ a>',
	'erreur_envoi_mail_bloque_debug' => 'Het verzenden van e-mail geblokkeerd door <tt>_TEST_EMAIL_DEST</tt>.',
	'erreur_envoi_mail_force_debug' => 'Het versturen van de e-mail naar @email@ geforceerd door <tt>_TEST_EMAIL_DEST</tt>.',
	'erreur_envoi_newsletter' => 'Een onbekende fout is opgetreden tijdens het verzenden van de nieuwsbrief.',
	'erreur_generation_newsletter' => 'Bij het aanmaken van de nieuwsbrief is een fout opgetreden.',
	'explication_boost_send' => 'In deze modus worden mails zo snel mogelijk verzonden. Er wordt niet gedoseerd.
										Deze snelle verzendwijze wordt afgeraden omdat de verzending mogelijk als SPAM wordt beschouwd.',
	'explication_purger_historique' => 'Voor iedere meervoudige verzending worden alle geadresseerden bewaard met hun verzendstatus.
	Bij veel verzendingen kan dit een behoorlijk volume inhouden. Er wordt dat ook aanbevolen om deze verzendgegevens te wissen.',
	'explication_rate_limit' => 'Geef het maximum aantal per dag te verzenden e-mails aan, of laat leeg om geen limiet te stellen',

	// I
	'info_1_mailshot' => '1 bericht',
	'info_1_mailshot_destinataire' => 'Een ontvanger',
	'info_1_mailsubscriber' => '1 abonnee',
	'info_annuler_envoi' => 'Verzending annuleren',
	'info_archiver' => 'Archiveren',
	'info_aucun_destinataire' => 'Geen ontvanger',
	'info_aucun_envoi' => 'Geen verzending',
	'info_envoi_programme_1_destinataire' => 'Verzending gepland naar één ontvanger',
	'info_envoi_programme_nb_destinataires' => 'Verzending gepland naar @nb@ ontvangers',
	'info_mailshot_no' => 'Het versturen van No @id@',
	'info_nb_mailshots' => '@nb@ berichten',
	'info_nb_mailshots_destinataires' => '@nb@ ontvangers',
	'info_nb_mailsubscribers' => '@nb@ abonnees',
	'info_statut_archive' => 'gearchiveerd',
	'info_statut_cancel' => 'Geannuleerd',
	'info_statut_destinataire_clic' => 'Hit',
	'info_statut_destinataire_fail' => 'Fout',
	'info_statut_destinataire_read' => 'Open',
	'info_statut_destinataire_sent' => 'Verzonden',
	'info_statut_destinataire_spam' => '> Spam',
	'info_statut_destinataire_todo' => 'Te verzenden',
	'info_statut_end' => 'Beëindigd',
	'info_statut_init' => 'Geplanned',
	'info_statut_pause' => 'Pauze',
	'info_statut_poubelle' => 'Prullenbak',
	'info_statut_processing' => 'In uitvoering',

	// L
	'label_avancement' => 'Vooruitgang',
	'label_boost_send_oui' => 'Snelle verzending',
	'label_control_pause' => 'Pauze',
	'label_control_play' => 'Herstarten',
	'label_control_stop' => 'Afbreken',
	'label_date_fin' => 'Einddatum verzending',
	'label_date_start' => 'Datum van verzending',
	'label_envoi' => 'Bezig met verzenden',
	'label_from' => 'Verzender',
	'label_graceful' => 'Uitsluitend adressen die deze inhoud nog niet hebben ontvangen',
	'label_html' => 'HTML-versie',
	'label_listes' => 'Lijsten',
	'label_mailer_defaut' => 'Gebruik dezelfde dienst als voor andere emails',
	'label_mailer_defaut_desactive' => 'Niet mogelijk: er is nog geen e-mail verzenddienst geconfigureerd',
	'label_mailer_mailjet' => 'Mailjet',
	'label_mailer_mandrill' => 'Mandril',
	'label_mailer_smtp' => 'SMTP-server',
	'label_mailer_sparkpost' => 'Sparkpost',
	'label_mailjet_api_key' => 'Mailjet API key',
	'label_mailjet_api_version' => 'API versie',
	'label_mailjet_secret_key' => 'Mailjet geheime sleutel',
	'label_mandrill_api_key' => 'Mandril API Key',
	'label_purger_historique_delai' => 'Ouder dan',
	'label_purger_historique_oui' => 'De details van oude verzendingen wissen',
	'label_rate_limit' => 'Beperk het verzendvolume',
	'label_sparkpost_api_endpoint' => 'API Endpoint',
	'label_sparkpost_api_key' => 'Sparkpost API Key',
	'label_sujet' => 'Onderwerp',
	'label_texte' => 'Tekst versie',
	'legend_configuration_adresse_envoi' => 'Verzendadres',
	'legend_configuration_historique' => 'Verzendgeschiedenis',
	'legend_configuration_mailer' => 'Service voor het verzenden van e-mails',
	'lien_voir_newsletter' => 'Bekijk de nieuwsbrief',

	// M
	'mailshot_titre' => 'MailShot',

	// T
	'texte_changer_statut_mailshot' => 'Dit item is:',
	'texte_statut_archive' => 'gearchiveerd',
	'texte_statut_cancel' => 'geannuleerd',
	'texte_statut_end' => 'afgewerkt',
	'texte_statut_init' => 'Geplanned',
	'texte_statut_pause' => 'onderbroken',
	'texte_statut_processing' => 'in uitvoering',
	'titre_envois_archives' => 'Gearchiveerde zendingen',
	'titre_envois_destinataires_clic' => 'Mails waarop werd geklikt',
	'titre_envois_destinataires_fail' => 'Mislukte mailings',
	'titre_envois_destinataires_init_encours' => 'Geen ontvanger geprogrammeerd ( initialisatie in uitvoering)',
	'titre_envois_destinataires_ok' => 'Verzending geslaagd',
	'titre_envois_destinataires_read' => 'Mails die werden geopend',
	'titre_envois_destinataires_sent' => 'Verzending geslaagd',
	'titre_envois_destinataires_spam' => 'Mails die als spam werden ontvangen',
	'titre_envois_destinataires_todo' => 'Toekomstige verzendingen',
	'titre_envois_en_cours' => 'Bezig met verzenden',
	'titre_envois_planifies' => 'Geplande verzendingen',
	'titre_envois_termines' => 'Verzending beëindigd',
	'titre_mailshot' => 'Bulk Mailing',
	'titre_mailshots' => 'Bulk mailings',
	'titre_menu_mailshots' => 'Track & Trace bulk mailings',
	'titre_page_configurer_mailshot' => 'Mailshot'
);
