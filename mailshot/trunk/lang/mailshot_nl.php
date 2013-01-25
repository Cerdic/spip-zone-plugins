<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP

if (!defined('_ECRIRE_INC_VERSION')) return;
 
$GLOBALS[$GLOBALS['idx_lang']] = array(

	// M
  "mailshot_titre" =>"MailShot",

	// C
  "cfg_exemple" => "Voorbeeld",
	"cfg_exemple_explication" => "Uitleg van dit voorbeeld",
	"cfg_titre_parametrages" => "Configureer de bulk mailer",

	// E
	"erreur_aucun_service_configure" => "Geen geconfigureerd delivery service . <a Href=\"@url@\"> configureren van een dienst </ a>",
	"erreur_envoi_mail_bloque_debug" => "Het verzenden van e-mail geblokkeerd door <tt>_TEST_EMAIL_DEST</tt>",
	"erreur_envoi_mail_force_debug" => "Het versturen van de e-mail naar @email@ gedwongen door <tt>_TEST_EMAIL_DEST</tt>",

	// I
	"info_aucun_envoi" => "No verzenden",
	"info_envoi_programme_1_destinataire" => "Verzenden gepland om een ontvanger",
	"info_envoi_programme_nb_destinataires" => "Verzenden naar verwachting @nb@ ontvangers",
	"info_1_mailsubscriber" => "1 abonnee",
	"info_nb_mailsubscribers" => "@nb@ abonnees",
	"info_1_mailshot" => "1 bericht",
	"info_nb_mailshots" => "@nb@ berichten",
	"info_1_mailshot_destinataire" => "Een ontvanger",
	"info_nb_mailshots_destinataires" => "@nb@ ontvangers",
	"info_aucun_destinataire" => "Geen ontvanger",
	"info_mailshot_no" => "Het versturen van No @id@",

	"info_statut_pause" => "Pauze",
	"info_statut_processing" => "In de vooruitgang",
	"info_statut_end" => "Finished",
	"info_statut_cancel" => "Canceled",
	"info_statut_poubelle" => "Trash",

	"info_statut_destinataire_todo" => "Te verzenden",
	"info_statut_destinataire_sent" => "Verzonden",
	"info_statut_destinataire_read" => "Open",
	"info_statut_destinataire_clic" => "Hit",
	"info_statut_destinataire_fail" => "Fail",
	"info_statut_destinataire_spam" => "> Spam",


	// L
	"label_envoi" => "Bezig met verzenden",
	"label_sujet" => "Onderwerp",
	"label_html" => "HTML-versie",
	"label_texte" => "Tekst versie",
	"label_date_start" => "Datum van verzending",
	"label_date_fin" => "Einddatum versturen",
	"label_listes" => "Lijsten",
	"label_avancement" => "Progress",
	"lien_voir_newsletter" => "View Nieuwsbrief",

	"label_mailer_defaut" => "Gebruik dezelfde verschepende dienst als andere mails",
	"label_mailer_defaut_desactive" => "Mislukt : geen e-mail versturen dienst nog niet geconfigureerd",
	"label_mailer_smtp" => "SMTP-server",
	"label_mailer_mandrill" => "Mandril service",
	"label_mandrill_api_key" => "Mandril API Key",
	"label_control_pause" => "Pauze",
	"label_control_play" => "Herstart",
	"label_control_stop" => "Afbreken",
	"label_rate_limit" => "Beperk het verzenden van tarief",
	"explication_rate_limit" => "Geef het maximum aantal e-mails per dag verstuurd , of laat leeg om geen limiet te stellen",

	"legend_configuration_adresse_envoi" => "Verzendadres",
	"legend_configuration_mailer" => "Service voor het verzenden van e-mails",

	// T
	"titre_page_configurer_mailshot" => "Mailshot",
	"titre_menu_mailshots" => "Track & Trace bulk mailings",
	"titre_envois_en_cours" => "Bezig met verzenden",
	"titre_envois_termines" => "Het versturen van ingevulde",
	"titre_mailshot" => "Bulk Mailing",
	"titre_mailshots" => "Bulk mailings",
	"texte_changer_statut_mailshot" => "Dit item is :",
	"texte_statut_pause" => "onderbroken",
	"texte_statut_processing" => "In de vooruitgang",
	"texte_statut_end" => "afgewerkte",
	"texte_statut_cancel" => "geannuleerd",
	"titre_envois_destinataires_todo" => "Mailings te komen",
	"titre_envois_destinataires_ok" => "Zendingen succesvolle",
	"titre_envois_destinataires_fail" => "Mislukt Mailings",
	"titre_envois_destinataires_init_encours" => "Geen ontvanger geprogrammeerd ( initialisatie in uitvoering)",
);

?>