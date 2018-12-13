<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de https://trad.spip.net/tradlang_module/mailsubscriber?lang_cible=de
// ** ne pas modifier le fichier **

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// B
	'bouton_importer' => 'Importieren',
	'bouton_invitation' => 'Zur Anmeldung zum Newsletter einladen',
	'bouton_previsu_importer' => 'Vorschau',

	// C
	'confirmsubscribe_invite_texte_email_1' => '@invite_email_from@ hat sie dazu eingeladen, sich zum Newsletter von @nom_site_spip@ mit der E-Mailadresse @email@ anzumelden.',
	'confirmsubscribe_invite_texte_email_3' => 'Falls sie diese E-Mail fälschlcherweise erhalten haben, können Sie diese ignorieren: Die Anfrage wird automatisch gelöscht.',
	'confirmsubscribe_invite_texte_email_liste_1' => '@invite_email_from@ hat Sie dazu eingeladen, sich für die Liste « @titre_liste@ » der Website @nom_site_spip@ mit der E-Mail_adresse @email@ einzutragen.', # MODIF
	'confirmsubscribe_sujet_email' => '[@nom_site_spip@] Bestätigung Newsletter-Anmeldung',
	'confirmsubscribe_texte_email_1' => 'Sie haben sich mit der Adresse @email@ für den Bezug des Newsletters angemeldet.',
	'confirmsubscribe_texte_email_2' => 'Um die Anmeldung abzuschließen, klicken Sie bitte auf den Link:
@url_confirmsubscribe@',
	'confirmsubscribe_texte_email_3' => 'Sollte es sich um einen Irrtum handeln, oder Sie es sich anders überlegt haben, brauchen Sie nichts zu unternehmen. Die Anmeldung wird automatisch gelöscht.',
	'confirmsubscribe_texte_email_envoye' => 'Eine Bestätigungsmail wurde an die Mailadresse geschickt.',
	'confirmsubscribe_texte_email_liste_1' => 'Sie haben haben sich für die E-Mail-Liste « @titre_liste@ » der Website @nom_site_spip@ mit der E-Mailadresse @email@ eingetragen.', # MODIF
	'confirmsubscribe_titre_email' => 'Bestätigung der Newsletter-Anmeldung',
	'confirmsubscribe_titre_email_liste' => 'Bestätigung der Anmeldung für die Liste « <b>@titre_liste@</b> »', # MODIF

	// D
	'defaut_message_invite_email_subscribe' => 'Hallo, ich abonniere den Newsletter von @nom_site_spip@ und schlage dir vor, diesen ebenfalls zu abonnieren.',

	// E
	'erreur_adresse_existante' => 'Diese Adresse ist bereits in der Liste eingetragen.',
	'erreur_adresse_existante_editer' => 'Diese E-Mailadresse ist bereits registriert - <a href="@url@">Diesen Abonnent bearbeiten</a>',
	'erreur_technique_subscribe' => 'Bei ihrer Anmeldung kam es zu einem technischen Problem.',
	'explication_listes_diffusion_option_defaut' => 'Einer oder mehrere Listenbezeichnungen mit Komma getrennt',
	'explication_listes_diffusion_option_statut' => 'Listen nach ihrem Status filtern',
	'explication_to_email' => 'Eine Anmelde-E-Mail an folgende Adressen senden (mehrere Adressen durch Komma trennen).',

	// F
	'force_synchronisation' => 'Synchronisieren',

	// I
	'icone_creer_mailsubscriber' => 'Abonnement hinzufügen',
	'icone_modifier_mailsubscriber' => 'Abonnement ändern',
	'info_1_adresse_a_importer' => '1 Adresse importieren',
	'info_1_mailsubscriber' => '1 Abonnent',
	'info_aucun_mailsubscriber' => 'Keine Abonnenten',
	'info_email_inscriptions' => 'Anmeldungen für @email@ :',
	'info_email_limite_nombre' => 'Einladungen sind auf maximal fünf Personen begrenzt.',
	'info_email_obligatoire' => 'E-Mail erforderlich',
	'info_emails_invalide' => 'Eine der E-Mails ist ungültig',
	'info_nb_adresses_a_importer' => '@nb@ Adressen importieren',
	'info_nb_mailsubscribers' => '@nb@ Abonnenten',
	'info_statut_poubelle' => 'gelöscht',
	'info_statut_prepa' => 'nicht angemeldet',
	'info_statut_prop' => 'wartend',
	'info_statut_refuse' => 'abgelehnt',
	'info_statut_valide' => 'angemeldet',

	// L
	'label_desactiver_notif_1' => 'Anmeldebenachrichtigungen für diesem Importvorgang deaktivieren',
	'label_email' => 'Email',
	'label_file_import' => 'Dateiimport',
	'label_from_email' => 'E-Mail-Adresse des Einladenden',
	'label_informations_liees' => 'Teilbare Informationen',
	'label_inscription' => 'Anmeldung',
	'label_lang' => 'Sprache',
	'label_listes' => 'Listen',
	'label_listes_diffusion_option_statut' => 'Status',
	'label_listes_import_subscribers' => 'In Verteilerlisten eintragen',
	'label_mailsubscriber_optin' => 'Ich möchten den Newsletter erhalten',
	'label_message_invite_email_subscribe' => 'Nachricht die der E-Mail hinzugefügt wird',
	'label_nom' => 'Name',
	'label_optin' => 'Opt-in',
	'label_statut' => 'Status',
	'label_to_email' => 'Einzuladende E-Mail-Adresse',
	'label_toutes_les_listes' => 'Alle',
	'label_valid_subscribers_1' => 'Abonnementen direkt anmelden (ohne eine Bestätigung zu verlangen)',
	'label_vider_table_1' => 'Alle Adressen in der Datenbank vor dem Import löschen',

	// M
	'mailsubscribers_poubelle' => 'Gelöscht',
	'mailsubscribers_prepa' => 'Nicht angemeldet',
	'mailsubscribers_prop' => 'Unbestätigt',
	'mailsubscribers_refuse' => 'Abgemeldet',
	'mailsubscribers_tous' => 'Alle',
	'mailsubscribers_valide' => 'Angemeldet',

	// S
	'subscribe_deja_texte' => 'Die Mailadresse @email@ befindet sich bereits in der Empfängerliste.', # MODIF
	'subscribe_sujet_email' => '[@nom_site_spip@] Newsletter-Anmeldung',
	'subscribe_texte_email_1' => 'Ihre Newsletter-Anmeldung mit der E-Mailadress @email@ wurde entgegengenommen.',
	'subscribe_texte_email_2' => 'Vielen Dank für Ihr Interesse an @nom_site_spip@.',
	'subscribe_texte_email_3' => 'Falls ein Irrtum vorliegt, oder Sie Ihre Meinung ändern, können Sie sich jederzeit bei der folgenden Adresse abmelden : @url_unsubscribe@',
	'subscribe_texte_email_liste_1' => 'Sie sind jetzt in die Liste « @titre_liste@ » unter der E-Mailadresse  @email@ eingetragen.', # MODIF
	'subscribe_titre_email' => 'Newsletter-Anmeldung',
	'subscribe_titre_email_liste' => 'Anmeldung für die Liste« <b>@titre_liste@</b> »', # MODIF

	// T
	'texte_ajouter_mailsubscriber' => 'Newsletter-Abonnent hinzufügen',
	'texte_avertissement_import' => 'Die Spalte <tt>Status</tt> ist vorhanden: Daten werden unverändert importiert, bei bereits vorhandenen E-MaIadressen wird der Status überschrieben.',
	'texte_changer_statut_mailsubscriber' => 'Dieser Abonnent ist :',
	'texte_import_export_bonux' => 'Um Abonnentenlisten zu im- und exportieren, installieren Sie bitte das Plugin  <a href="https://plugins.spip.net/spip_bonux">SPIP-Bonux</a>.',
	'texte_statut_en_attente_confirmation' => 'unbestätigt',
	'texte_statut_pas_encore_inscrit' => 'nicht angemeldet',
	'texte_statut_refuse' => 'abgelehnt',
	'texte_statut_valide' => 'aktiv',
	'texte_vous_avez_clique_vraiment_tres_vite' => 'Sie haben den Bestätigungsbutton sehr schnell geklickt sind Sie sicher, dass Sie ein Mensch sind?',
	'titre_bonjour' => 'Guten Tag',
	'titre_export_mailsubscribers' => 'Abonnenten exportieren',
	'titre_export_mailsubscribers_all' => 'Alle Adressen exportieren',
	'titre_import_mailsubscribers' => 'Adressen importieren',
	'titre_langue_mailsubscriber' => 'Sprache des Abonnenten',
	'titre_listes_de_diffusion' => 'Newsletter',
	'titre_logo_mailsubscriber' => 'Logo des Abonnenten',
	'titre_mailsubscriber' => 'Abonnent',
	'titre_mailsubscribers' => 'Abonnenten',

	// U
	'unsubscribe_deja_texte' => 'Die Adresse email @email@ befindet sich nicht in der Liste.', # MODIF
	'unsubscribe_sujet_email' => '[@nom_site_spip@] Newsletter Abmeldung',
	'unsubscribe_texte_confirmer_email_1' => 'Bitte bestätigen Sie die Abmeldung der Adresse email @email@ durch einen Klick auf die Schaltfläche: ',
	'unsubscribe_texte_confirmer_email_liste_1' => 'Bestätigen Sie die Abmeldung der E-Mailadresse @email@ von der Liste <b>@titre_liste@</b> durch Klick auf den Button: ', # MODIF
	'unsubscribe_texte_email_1' => 'Die Adresse email @email@ wurde aus der Liste entfernt.', # MODIF
	'unsubscribe_texte_email_2' => 'Bitte besuchen Sie @nom_site_spip@ bald wieder.',
	'unsubscribe_texte_email_3' => 'Falls ein Irrtum vorliegt, oder Sie Ihre Meinung ändern, können Sie sich jederzeit unter der folgenden Adresse wieder anmelden:
@url_subscribe@',
	'unsubscribe_texte_email_liste_1' => 'Die E-Mailadresse @email@ wurde von unserer Verteilerliste <b>@titre_liste@</b> entfernt.', # MODIF
	'unsubscribe_titre_email' => 'Newsletter Abmeldung',
	'unsubscribe_titre_email_liste' => 'Abmeldung von der Liste <b>@titre_liste@</b>' # MODIF
);
