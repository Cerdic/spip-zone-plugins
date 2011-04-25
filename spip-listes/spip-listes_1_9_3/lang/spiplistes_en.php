<?php
/**
 * Pack langue anglais
 * 
 * @package spiplistes
 */
 // $LastChangedRevision: 47066 $
 // $LastChangedBy: paladin@quesaco.org $
 // $LastChangedDate: 2011-04-25 19:54:15 +0200 (Lun 25 avr 2011) $
 
$GLOBALS['i18n_spiplistes_en'] = array(

// CP-20081126: classement par scripts
// action/spiplistes_agenda.php
// action/spiplistes_changer_statut_abonne.php
// action/spiplistes_envoi_lot.php
// action/spiplistes_journal.php
// action/spiplistes_lire_console.php
// action/spiplistes_liste_des_abonnes.php
// action/spiplistes_listes_abonner_auteur.php
// action/spiplistes_moderateurs_gerer.php
'voir_historique' => 'See sent messages'
, 'pas_de_liste_prog' => "No list planned."

// action/spiplistes_supprimer_abonne.php
// balise/formulaire_abonnement.php
, 'inscription_liste_f' => 'You will receive the newsletter sent to the following mailing list, in format @f@ : '
, 'inscription_listes_f' => 'You will receive the newsletters sent to the following mailing lists, in format @f@ : '
, 'inscription_reponse_s' => 'Your e-mail address has been added to the mailing list of @s@\n'
, 'inscription_reponses_s' => 'Your e-mail address has been added to the mailing list of @s@\n'
, 'vous_abonne_aucune_liste' => 'You are not subscribed to a mailing list'
, 'liste_dispo_site_' => 'Mailing list available on this site : '
, 'listes_dispos_site_' => 'Mailing lists available on this site : '
, 'desole_pas_de_liste' => 'Sorry, there is no mailing list available yet.'
, 'pour_vous_abonner' => 'To subscribe to mailing lists'
// obsolete
, 'abonnement_mail_passcookie' => "
	<br />
	To change your subscription newsletters site <strong>@nom_site_spip@</strong> (@adresse_site@), 	
	please visit the following address:<br /><br />
	<a href='@adresse_site@/spip.php?page=abonnement&d=@cookie@'>@adresse_site@/spip.php?page=abonnement&d=@cookie@</a><br /><br />
	You can then confirm the change of your subscription.
	<br/>"
, 'bienvenue_sur_la_liste_' => 'Welcome to the mailing lists of site '
, 'vos_abos_sur_le_site_' => 'Your subscription(s) to the site '
, 'votre_format_de_reception_' => 'Your email format '
, '_cliquez_lien_formulaire' => 'Click this link to access the form on the site'
, 'pour_modifier_votre_abo_' => 'To change your subscription '
, 'abonnement_presentation' => '
	Enter your email address in the box below. 
	You will receive at this address a confirmation of registration and a link.
	This link will allow you to select the mailing lists posted here.'
, 'confirmation_inscription' => "Confirmation of the subcription"
, 'souhait_modifier_abo' => 'You want to change your subscription.'
, 'suspendre_abonnement_' => "Suspend my subscription "
, 'vous_etes_redact' => "You are logged in as editor."
, 'vous_etes_membre' => "You are subscribed to mailing lists on this site.
	It is sometimes necessary to login to access these lists."

// balise/formulaire_modif_abonnement.php
, 'abonnement_modifie' => 'Your modifications have been registered.'
, 'abonnement_nouveau_format' => 'The format in which you will receive the newsletter will from now on be: '

// base/spiplistes_init.php
, 'autorisation_inscription' => 'SPIP-Lists has now activated visitor sign-up.'

// base/spiplistes_tables.php
// base/spiplistes_upgrade.php
// docs/spiplistes_aide_fr.html
// exec/spiplistes_abonne_edit.php
, 'adresse_mail_obligatoire' => 'Email address missing. Subscription impossible.'
, 'abonne_sans_format' => 'This account is currently unsubscribed. No e-mail format is
	defined. He can not receive mail. Define a format approval for this account to validate your subscription.'
, 'Desabonner_temporaire' => "Temporarily unsubscribe this account."
, 'Desabonner_definitif' => "Unsubscribe this account of all the mailing lists."
, 'export_etendu_' => "Export extended "
, 'exporter_statut' => "Export status (guest, editor, etc.)."
, 'editer_fiche_abonne' => 'Editing the data from the subscriber'
, 'edition_dun_abonne' => "Editing a subscriber"
, 'format_de_reception' => "Email format" // + formulaire
, 'format_reception' => 'Choose your format:'
, 'format_de_reception_desc' => "You can choose a general format for receipt of letters for that subscriber.<br /><br />
   You can also temporarily unsubscribe the contact.
   He remains registered in the lists as a recipient, but letters will not be sent until you did not have defined a format for receiving emails."
, 'mettre_a_jour' => '<h3>SPIP-lists will update</h3>'
, 'regulariser' => 'put in order the unsubscribed for lists...<br />'
, 'Supprimer_ce_contact' => "Delete this contact"
, 'abonne_listes' => 'This contact has been added to the following listes'
, 'n_duplicata_mail' => "@n@ duplicate(s)"
, 'n_incorrect_mail' => "@n@ incorrect(s)"

// exec/spiplistes_abonnes_tous.php
, 'repartition_abonnes' => "Distribution of subscribers"
, 'abonnes_titre' => 'Subscribers'
, 'chercher_un_auteur' => "Search an author"
, 'une_inscription' => 'One subscriber found'
, 'suivi' => 'Subscribers' // + presentation
, 'abonne_aucune_liste' => 'Not subscribed to any list'
, 'format_aucun' => "none"
, 'repartition_formats' => 'Distribution of formats'

// exec/spiplistes_aide.php
// exec/spiplistes_autocron.php
// exec/spiplistes_config.php
, 'personnaliser_le_courrier' => "Customize the email"
, 'personnaliser_le_courrier_desc' => "You can customize the letter for each subscriber by inserting
    your template tags needed. For example, to insert the name of your subscriber's mail when sending, 
	put in your template _AUTEUR_NOM_ (note the underscore at the start and end tag)."
, 'utiliser_smtp' => "Use SMTP"
, 'requiert_identification' => "Requires an identification"
, 'adresse_smtp' => "Email address of <em>sender</em> SMTP"
, '_aide_install' => "<p>Welcome to SPIP-Listes.</p>
	<p class='verdana2'>By default after installation, SPIP-Listes is in <em>simulation sending mod</em> to help you discover the features and make your first test.</p>
	<p class='verdana2'>To validate the different options of SPIP-Listes, go to <a href='@url_config@'> on the configuration page</a>.</p>"
, 'adresse_envoi_defaut' => 'Default sender\'s address'
, 'adresse_on_error_defaut' => "Default return address for the errors"
, 'pas_sur' => '<p>If you are not sure, choose the mail function in PHP.</p>'
, 'Complement_des_courriers' => "Email complement"
, 'Complement_lien_en_tete' => "Link on the email"
, 'Complement_ajouter_lien_en_tete' => "Add a link in the header of the mail"
, 'Complement_lien_en_tete_desc' => "This option allows you to add at the top of the HTML mail sent the link original mail on your site."
, 'Complement_tampon_editeur' => "Add stamp publisher"
, 'Complement_tampon_editeur_desc' => "This option allows you to add the stamp of the publisher at the end of mail."
, 'Complement_tampon_editeur_label' => "Add the editor stamp at the end of email"
, 'Envoi_des_courriers' => "Send emails"
, 'log_console' => "Console"
, 'log_details_console' => "Details of the console"
, 'log_voir_destinataire' => "List the email addresses of receivers in the console when sending"
, 'log_console_syslog_desc' => "You are on a LAN (@IP_LAN@). If necessary, you can turn on the console instead of syslog logs SPIP (recommended under unix)."
, 'log_console_syslog_texte' => "Enable system log (syslog on referral)"
, 'log_console_syslog' => "Console syslog"
, 'log_voir_le_journal' => "View SPIP-Listes log"
, 'recharger_journal' => "Reload log"
, 'fermer_journal' => "Close log"
, 'methode_envoi' => 'Sending method'
, 'mode_suspendre_trieuse' => "Suspend the processing of sending mailing lists"
, 'Suspendre_le_tri_des_listes' => "This option allows you - in case of congestion - to suspend the processing of mailing lists and programmed to redefine the parameters sending. Remove this option to resume the processing of mailing lists planned."
, 'mode_suspendre_meleuse' => "Suspend sending mail"
, 'suspendre_lenvoi_des_courriers' => "This option allows you - in case of congestion - To cancel the sending of letters. Remove this option to resume shipments underway."
, 'nombre_lot' => 'Number of dispatches per batch'
, 'php_mail' => 'Use the PHP mail() function'
, 'patron_du_tampon_' => "Template of the stamp: "
, 'Patron_de_pied_' => "Template of the footer "
, 'personnaliser_le_courrier_label' => "Enable customization of email"
, 'parametrer_la_meleuse' => "Setting meleuse"
, 'smtp_hote' => 'Host'
, 'smtp_port' => 'Port'
, 'simulation_desactive' => "Simulation mode disabled."
, 'simuler_les_envois' => "Simulating the sending mail"
, 'abonnement_simple' => '<strong>Simple sign-up : </strong><br /><em>Subscribers receive a confirmation message</em>'
, 'abonnement_code_acces' => '<strong>Subscription with login: </strong><br /><i>Additionally subscribers receive a username and password to identify on the website. </i>'
, 'mode_inscription' => 'Specify the subscription mode for your visitors'

// exec/spiplistes_courrier_edit.php
, 'Generer_le_contenu' => "Generate content"
, 'Langue_du_courrier_' => "Language of email:"
, 'generer_Apercu' => "Generate and Preview"
, 'a_partir_de_patron' => "From a template"
, 'avec_introduction' => "With introduction text"
, 'calcul_patron_attention' => "Some template include in their results the following text (text mail). If you are upgrading your mail, remember to empty the box before generating the content."
, 'charger_patron' => 'Choose a template of the email'
, 'Courrier_numero_' => "Email number:" // + _gerer
, 'Creer_un_courrier_' => "Create an email:"
, 'choisir_un_patron_' => "Choose a template "
, 'Courrier_edit_desc' => 'You can choose to automatically generate the contents of mail or simply write your email in the <strong>text box</strong>.'
, 'Contenu_a_partir_de_date_' => "Content from this date "
, 'Cliquez_Generer_desc' => "Click on <strong>@titre_bouton@</strong> to inject the result in the box @titre_champ_texte@."
, 'Lister_articles_de_rubrique' => "And list the articles of the section"
, 'Lister_articles_mot_cle' => "And list the items of the keyword"
, 'edition_du_courrier' => "Email edition" // + gerer
, 'generer_un_sommaire' => "Make a summary"
, 'generer_patron_' => "Make a template "
, 'generer_patron_avant' => "before the summary"
, 'generer_patron_apres' => "after the summary"
, 'introduction_du_courrier_' => "Introduction to the mail before the content from the site"
, 'Modifier_un_courrier__' => "Edit an email :"
, 'Modifier_ce_courrier' => "Edit this email"
, 'sujet_courrier' => '<strong>Message subject</strong> [required]'
, 'texte_courrier' => '<strong>Message text</strong> (HTML authorised)'
, 'avec_patron_pied__' => "With the footer template : "

// exec/spiplistes_courrier_gerer.php
, 'Erreur_Adresse_email_invalide' => 'Error, the e-mail address is not valid'
, 'langue_' => '<strong>Language :</strong>&nbsp;'
, 'calcul_patron' => 'Based on text version template'
, 'calcul_html' => 'Based on text version HTML message'
, 'dupliquer_ce_courrier' => "Duplicate this letter"
, 'destinataire_sans_format_alert' => "Receiver without email format. Apply a format (text or html) for the account or select another receiver."
, 'envoi_date' => 'Dispatch date: '
, 'envoi_debut' => 'Start of dispatch: '
, 'envoi_fin' => 'End of dispatch: '
, 'erreur_envoi' => 'Number of dispatch errors: '
, 'Erreur_liste_vide' => "Error: This list has no subscribers."
, 'Erreur_courrier_introuvable' => "Error: this letter does not exist." // + previsu
, 'Envoyer_ce_courrier' => "Send this letter"
, 'format_html__n' => "Format html : @n@"
, 'format_texte__n' => "Format text : @n@"
, 'message_arch' => 'Letter archived'
, 'message_en_cours' => 'Sending email'
, 'message_type' => 'Email'
, 'sur_liste' => 'To the list' // + casier
, 'Supprimer_ce_courrier' => "Delete this letter"
, 'email_adresse' => 'Test e-mail address' // + liste
, 'email_test' => 'Send a test message'
, 'Erreur_courrier_titre_vide' => "Error: your mail has no title."
, 'message_en_cours' => 'Editing in progress'
, 'modif_envoi' => 'You can modify it or ask to send it out'
, 'message_presque_envoye' =>'This message is ready to be sent out'
, 'Erreur_Adresse_email_inconnue' => 'Note, there is no such e-mail address on the subscribers list, <br /> no message could be sent. Please retry<br /><br />'

// exec/spiplistes_courrier_previsu.php
, 'lettre_info' => 'The newsletter of the web site'

// exec/spiplistes_courriers_casier.php
// exec/spiplistes_import_export.php
, 'Exporter_une_liste_d_abonnes' => "Export a list of subscribers"
, 'Exporter_une_liste_de_non_abonnes' => "Export a list of non-subscribers"
, '_aide_import' => "Here you can import a list of subscribers from your computer. <br />
	This list of subscribers must be in plain text, a line per subscriber. Each line must be composed as follows: <br /> 
	<tt style='display:block;margin:0.75em 0;background-color:#ccc;border:1px solid #999;padding:1ex;'>address@mail<span style='color:#f66'>[separator]</span>login<span style='color:#f66'>[separator]</span>name</tt>
	<tt style='color:#f66'>[separator]</tt> is a tab character or a semicolon.<br /><br />
	The email address must be unique, as well as the login. If this address email or login exist in the base of the site, the line will be rejected.<br />
	The first scope address@mail address is required. The other two fields may be ignored (you can import lists from older versions of SPIP-lists)."
, 'annuler_envoi' => "Cancel" // + _gerer
, 'envoi_patron' => 'Template dispatch'
, 'import_export' => 'Import / Export'
, 'incorrect_ou_dupli' => " (incorrect ou duplicate)"
, 'membres_liste' => 'List of subscribers'
, 'Messages_automatiques' => 'Programmed automatic messages'
, 'Pas_de_liste_pour_import' => "You must create at least one destination list to be able to import your subscribers."
, 'Resultat_import' => "Import result"
, 'Selectionnez_une_liste_pour_import' => "You must select at least one mailing list to be able to import subscribers."
, 'Selectionnez_une_liste_de_destination' => "Select one or several destination lists for your subscribers."
, 'Tous_les_s' => "Every @s@"
, 'Toutes_les_semaines' => "Every week"
, 'Tous_les_mois' => "Every month, "
, 'Tous_les_ans' => "Every year"
, 'version_html' => '<strong>Version HTML</strong>'
, 'version_texte' => '<strong>Version text</strong>'
, 'erreur_import' => 'The import file has an error at line '
, 'envoi_manuel' => "Manual sending"
, 'format_date' => 'Y/m/d'
, 'importer' => 'Import a subscribers list'
, 'importer_fichier' => 'Import a file'
, 'importer_fichier_txt' => '<p><strong>Your subscribers list needs to be in simple text format that contains only one e-mail address per line</strong></p>'
, 'importer_preciser' => '<p>Please specify the lists to which you want to add the addresses and the message format (HTML or txt)</p>'
, 'prochain_envoi_prevu' => 'Next message that will be send out' // + gerer
, 'option_import_' => "Import options "
, 'forcer_abos_' => "Forcing subscriptions (if email address exists in the database, forcing subscription for selection for this subscriber)"
, 'erreur_import_base' => 'Error importing. Incorrect Data or SQL error.'
, 'erreur_n_fois' => "(Error encountered @n@ times)"
, 'Liste_de_destination_s' => "Destination list : @s@"
, 'Listes_de_destination_s' => "Destination lists : @s@"
, 'pas_dimport' => "No import. The file is empty or all addresses are already subscribed."

// exec/spiplistes_liste_edit.php
, 'texte_dinsctription_' => "Subscribe text:"
, 'Creer_une_liste_' => "Create a list "
, 'en_debut_de_semaine' => "At the beginning of the week"
, 'en_debut_de_mois' => "At the beginning of the month"
, 'envoi_non_programme' => "Sending not programmed"
, 'edition_dune_liste' => "Edit a list"
, 'texte_contenu_pied' => '<br />(Message added at the bottom of each e-mail when sent)<br />'
, 'texte_pied' => '<p><strong>Footer text</strong>'
, 'modifier_liste' => 'Modify this list'
, 'txt_abonnement' => '(Specify here the description of this list that will be published on the webpage if the list is activated)'

// exec/spiplistes_liste_gerer.php
, 'forcer_les_abonnement_liste' => "Forcing subscriptions for this list"
, 'periodicite_tous_les_n_s' => "Frequency: every @n@ @s@"
, 'liste_sans_titre' => 'List without title'
, 'statut_interne' => "Private"
, 'statut_publique' => "Public"
, 'adresse' => "Specify here the reply-to e-mail address (otherwise the default address of the webmaster will be used):"
, 'Ce_courrier_ne_sera_envoye_qu_une_fois' => "This letter will be sent once."
, 'adresse_de_reponse' => "Return address"
, 'adresse_mail_retour' => 'Email address list manager (reply-to)'
, 'Attention_action_retire_invites' => "Caution: This action removes the guests of the list of subscribers."
, 'A_partir_de' => "From"
, 'Apercu_plein_ecran' => "Preview fullscreen in a new window"
, 'Attention_suppression_liste' => "Warning! You ask for the removal of a mailing list.
	Subscribers will be removed from this mailing list automatically."
, 'Abonner_tous_les_invites_public' => "Subscribe all members invited to the public list."
, 'Abonner_tous_les_inscrits_prives' => "All members subscribe to this list, except visitors."
, 'boite_confirmez_envoi_liste' => "You have requested the immediate dispatch of this list. <br /> Please confirm your request."
, 'cette_liste_est_' => "This list is: @s@"
, 'Confirmer_la_suppression_de_la_liste' => "Confirm to remove the list "
, 'Confirmez_requete' => "Please confirm the request."
, 'date_expedition_' => "Dispatch date "
, 'Dernier_envoi_le_' => "Last dispatch on:"
, 'forcer_abonnement_desc' => "Here you can force subscriptions to this list, either for all registered members (visitors, writers and directors) or for all visitors."
, 'forcer_abonnement_aide' => "<strong>Attention</strong>: a subscriber will not receive mail from this list until he confirms himself reception format: html or text only.<br />
	You can force the format per subscriber; <a href='@lien_retour@'> on the follow-up subscriptions page.</a>"
, 'forcer_abonnements_nouveaux' => "By selecting the option <strong>Forcing subscriptions format ...</strong>, 
	you confirm the format for receipt of new subscribers. Old subscribers retain their preference of email format."
, 'Forcer_desabonner_tous_les_inscrits' => "Unsubscribe all members registered for this list."
, 'gestion_dune_liste' => "Manage a list"
, 'message_sujet' => 'Subject '
, 'mods_cette_liste' => "The moderators of this list"
, 'nbre_abonnes' => "Number of subscribers: "
, 'nbre_mods' => "Number of moderators: "
, 'patron_manquant_message' => "You must set a template before sending this list."
, 'liste_sans_patron' => "List without template." // courriers_listes
, 'Patron_grand_' => "Template (big) "
, 'sommaire_date_debut' => "Starting from the date specified above"
, 'abos_cette_liste' => "Subscribers to this list"
, 'confirme_envoi' => 'Please confirm the sending'
, 'env_esquel' => 'Programmed sending template'
, 'env_maint' => 'Send now'
, 'date_act' => 'Data updated'
, 'forcer_les_abonnements_au_format_' => "Forcing subscriptions in the format:"
, 'pas_denvoi_auto_programme' => "There is no automatic scheduled for this mailing list."
, 'Pas_de_periodicite' => "No periodicity."
, 'prog_env' => 'Schedule an automatic sending'
, 'prog_env_non' => 'Do not program sending'
, 'conseil_regenerer_pied' => "<br />This template is from an old version of SPIP-Listes.<br />
	Tip: select the template up to take into account multilingualism and / or the 'text only' of the template."
, 'boite_alerte_manque_vrais_abos' => "There are no subscribers to this mailing list, or subscribers are not receiving format<br />
	Correct the format for receiving at least one subscriber before confirming the sending."	

// exec/spiplistes_listes_toutes.php
// exec/spiplistes_maintenance.php
, 'abonnes' => 'subscribers'
, '1_abonne' => 'the subscriber'
, 'annulation_chrono_' => "Cancellation of timer for "
, 'conseil_sauvegarder_avant' => "<strong>Tip</strong>: make a backup of the data before confirming the deletion
   @objet@. The cancellation is impossible."
, 'des_formats' => "of formats"
, 'des_listes' => "of lists"
, 'des_abonnements' => "of subscribers"
, 'confirmer_supprimer_formats' => "Remove email formats of subscribers."
, 'maintenance_objet' => "Maintenance @objet@"
, 'nb_abos' => "qt."
, 'pas_de_liste' => "No list type item not programmed."
, 'pas_de_format' => "No format defined for receiving email."
, 'pas_de_liste_en_auto' => "No list of type 'envoi programmed' (timer)."
, 'forcer_formats_' => "Forcing the email format"
, 'forcer_formats_desc' => "Forcing the email format for all subscribers ..."
, 'modification_objet' => "Modification @objet@"
, 'Suppression_de__s' => "Removing : @s@"
, 'suppression_' => "Removing @objet@"
, 'suppression_chronos_' => "Delete sending scheduled (timer)"
, 'suppression_chronos_desc' => "If you remove the timer, the list is not deleted. It periodicity is kept but the sending is suspended. To turn on the timer, you have to redefine a first date of sending."
, 'Supprimer_les_listes' => "Delete lists"
, 'Supprimer_la_liste' => "Delete list..."
, 'Suspendre_abonnements' => "Suspend subscription for this account"
, 'separateur_de_champ_' => "S&eacute;parator of field "
, 'separateur_tabulation' => "tabulation (<code>\\t</code>)"
, 'separateur_semicolon' => "semi-colon (<code>;</code>)"
, 'nettoyage_' => "Cleaning "
, 'confirmer_nettoyer_abos' => "Confirm clearing the table of subscribers."
, 'pas_de_pb_abonnements' => "No errors on the table subscriptions."
, '_n_abos_' => " @n@ subscribers "
, '_1_abo_' => " the subscriber "
, '_n_auteurs_' => " @n@ autors "
, '_1_auteur_' => " the autor "

// exec/spiplistes_menu_navigation.php
// exec/spiplistes_voir_journal.php
// genie/spiplistes_cron.php
// inc/spiplistes_agenda.php
, 'boite_agenda_titre_' => "Programmed sending "
, 'boite_agenda_legende' => "On @nb_jours@ days"
, 'boite_agenda_voir_jours' => "View for the @nb_jours@ days flowing"

// inc/spiplistes_api.php
// inc/spiplistes_api_abstract_sql.php
// inc/spiplistes_api_courrier.php
// inc/spiplistes_api_globales.php
// inc/spiplistes_api_journal.php
, 'titre_page_voir_journal' => "Log of SPIP-Lists"
, 'mode_debug_actif' => "Mode debug enable"

// inc/spiplistes_api_presentation.php
, '_aide' => '<p>SPIP-Lists allows you to send newsletters or other automated messages to people who have signed up. </p>
	<p>You can write your message in simple text format, in HTML, or apply a stencil - called "patron" (template in English) in SPIP language - that can include SPIP-code.</p>
	<p>A sign-up form on the webpage frontend allows users to decide themselves to which newsletters they want to subscribe and the format in which they want to receive it (HTML or text). </p>
	<p>Every message is automatically converted from HTML into text format for those who prefer receiving it in this format.</p>
	<p><strong>Note :</strong><br />Sending out a newsletter can take several minutes: The messages are sent out in batches that are sent out one by one. You can manually speed-up the sending process.</p>'
, 'envoi_en_cours' => 'Processing sending'
, 'nb_destinataire_sing' => " receiver"
, 'nb_destinataire_plur' => " receivers"
, 'aucun_destinataire' => "no receiver"
, '1_liste' => '@n@ list'
, 'n_listes' => '@n@ lists'
, 'utilisez_formulaire_ci_contre' => "Use the form below to activate / deactivate this option."
, 'texte_boite_en_cours' => 'SPIP-List is sending out a newsletter. <p>This message will disappear once the sending is completed.</p>'
, 'meleuse_suspendue_info' => "Sending letters awaiting dispatch is suspended."
, 'casier_a_courriers' => "Mail Box" // + courriers_casier
, 'Pas_de_donnees' => "Sorry, but the row requested does not exist in the database."
, '_dont_n_sans_format_reception' => ", with @n@ without email format."
, 'mode_simulation' => "Mode simulation"
, 'mode_simulation_info' => "The simulation mode is enabled. SPIP-List pretends to send mail. In fact, no mail is sent."
, 'meleuse_suspendue' => "Meleuse suspended"
, 'Meleuse_reactivee' => "M&egrave;leuse reactivated"
, 'nb_abonnes_sing' => " subscriber"
, 'nb_abonnes_plur' => " subscribers"
, 'nb_moderateur_sing' => " moderator"
, 'nb_moderateur_plur' => " moderators"
, 'aide_en_ligne' => "Help online"

// inc/spiplistes_dater_envoi.php
, 'attente_validation' => "Validation pending"
, 'courrier_en_cours_' => "Mail processing "
, 'date_non_precisee' => "Unspecified date"

// inc/spiplistes_destiner_envoi.php
, 'email_tester' => 'Test by email'
, 'Choix_non_defini' => 'No choice set.'
, 'Destination' => "Destination"
, 'aucune_liste_dispo' => "No list available."

// inc/spiplistes_import.php
// inc/spiplistes_lister_courriers_listes.php
, 'Prochain_envoi_' => "Next sending "

// inc/spiplistes_listes_forcer_abonnement.php
// inc/spiplistes_listes_selectionner_auteur.php
, 'lien_trier_nombre' => "Sort by number of subscriptions"
, 'Abonner_format_html' => "Subscribe with HTML format"
, 'Abonner_format_texte' => "Subscribe with text format"
, 'ajouter_un_moderateur' => "Add a moderator "
, 'Desabonner' => "Unsubscribe"
, 'Pas_adresse_email' => "No email address"
, 'sup_mod' => "Delete this moderator"
, 'supprimer_un_abo' => "Delete a subscriber to this list"
, 'supprimer_cet_abo' => "Delete this subscriber of this list" // + pipeline
, 'abon_ajouter' => "Add a subscriber "

// inc/spiplistes_mail.inc.php
// inc/spiplistes_meleuse.php
, 'erreur_sans_destinataire' => 'Error: no receiver can be found for this letter'
, 'envoi_annule' => 'Send Cancelled'
, 'sans_adresse' => ' No e-mail has been sent -> please specify a reply-to address'
, 'erreur_mail' => 'Error: sending impossible (make sure that  mail() of php is available)'
, 'modif_abonnement_text' => 'To change your subscription, please visit the following webpage: '
, 'msg_abonne_sans_format' => "email format is missing"
, 'modif_abonnement_html' => "<br /> Click here to change your subscription"

// inc/spiplistes_naviguer_paniers.php
// inc/spiplistes_pipeline_I2_cfg_form.php
// inc/spiplistes_pipeline_affiche_milieu.php
, 'Adresse_email_obligatoire' => "An email address is required to subscribe to a mailing lists. If you wish to use this service, thank you to modify your profile by completing this field."
, 'Alert_abonnement_sans_format' => "Your subscription is suspended. You will not receive emails from the mailing lists listed below. To receive new mail from your favorite lists, choose a email format and confirm this form."
, 'abonnements_aux_courriers' => "Subscriptions to emails"
, 'Forcer_abonnement_erreur' => "Reported technical error when editing a list subscribed. Check this list prior to your operation."
, 'Format_obligatoire_pour_diffusion' => "To confirm this subscription, you must select a format type."
, 'Valider_abonnement' => "Validate this subscription"
, 'vous_etes_abonne_aux_listes_selectionnees_' => "You are subscribed to the selected lists  "

// inc/spiplistes_pipeline_ajouter_boutons.php
// inc/spiplistes_pipeline_ajouter_onglets.php
// inc/spiplistes_pipeline_header_prive.php
// inc/spiplistes_pipeline_insert_head.php

// formulaires, patrons, etc.
, 'abo_1_lettre' => 'Mailing list '
, 'abonnement_seule_liste_dispo' => "Subscribe to the only list available "
, 'abo_listes' => 'Subscription'
, 'abonnement_0' => 'Subscription'
, 'abonnement_titre_mail' => 'Change your subscription'
, 'votre_abo_listes' => "Your mailing list subscription"
, 'lire' => 'Read'
, 'listes_de_diffusion_' => "Mailing lists "
, 'jour' => 'day'
, 'jours' => 'days'
, 'abonnement_bouton'=>'Change your subscription'
, 'abonnement_cdt' => "<a href='http://bloog.net/?page=spip-listes'>SPIP-Lists</a>"
, 'abonnement_change_format' => 'You can change the format in which you will receive the newsletter or unsubscribe: '
, 'abonnement_texte_mail' => 'Please specify with which e-mail address you have signed up previously. You will receive an e-mail with a link to the webpage where you can modify your subscription.'
, 'article_entier' => 'Read the whole article'
, 'form_forum_identifiants' => 'Confirm'
, 'form_forum_identifiant_confirm' => 'Your subscription has been registered. You will receive a confirmation e-mail.'
, 'demande_enregistree_retour_mail' => "Your request is registered. You will receive an confirmation email."
, 'effectuez_modif_validez' => "<span>Hello @s@,</span><br />You can make changes to your subscription, and then confirm."
, 'vous_etes_desabonne' => "You are now unsubscribed to the mailing list, but your registration on this site is still valid. To return to this form of change subscription, use the link sent to you or to enter your email address in the registration form."
, 'inscription_mail_forum' => 'This is your identification code to login to the website @nom_site_spip@ (@adresse_site@)'
, 'inscription_mail_redac' => 'This is your identification code to login to the website @nom_site_spip@ (@adresse_site@) and to the private area (@adresse_site@/ecrire)'
, 'inscription_visiteurs' => 'The subscription allows you to acces to the restricted parts of this website, to post messages in the forum and to receive the newsletters'
, 'inscription_redacteurs' => "The editor\'s backend is open to website visitors after registration. Once registered, you can read and comment on articles submitted for publication, submit your own articles and participate in all forums. The subscription also allows you to access to the restricted parts of the website and to receive the newsletters."
, 'mail_non' => 'Your e-mail address is not subscribed to the newsletter of @nom_site_spip@'
, 'messages_auto' => 'Automatic messages'
, 'nouveaute_intro' => 'Hello, <br />Here are the latest publications in the site'
, 'nom' => 'Username'
, 'texte_lettre_information' => 'This is the newsletter of '
, 'vous_pouvez_egalement' => 'You can also'
, 'vous_inscrire_auteur' => 'sign up as author'
, 'voir_discussion' => 'Show discussion'
, 'inconnu' => 'is not on the mailinglist'
, 'infos_liste' => 'Information on this list'
, 'editeur' => 'Editor: '
, 'html_description' => " (rich graphical format)"
, 'texte_brut' => "Plain Text"
, 'vous_etes_abonne_aux_listes_' => "You are subscribed to mailing lists:"
, 'vous_etes_abonne_a_la_liste_' => "You are subscribed to the mailing list:"

// tableau items *_options
, 'Liste_de_destination' => "Destination list"
, 'Listes_1_du_mois' => "Public, 1<sup><small>st</small></sup> of the month."
, 'Liste_diffusee_le_premier_de_chaque_mois' => "List released the first of each month. "
, 'Listes_autre' => "Other frequency"
, 'Listes_autre_periode' => "Public lists other frequency"
, 'Listes_diffusion_prive' => "Lists private"
, 'Liste_hebdo' => "Weekly List"
, 'Publiques_hebdos' => "Public, weekly"
, 'Listes_diffusion_hebdo' => "Weekly public lists"
, 'Liste_mensuelle' => "Monthly list"
, 'Publiques_mensuelles' => "Public, monthly"
, 'Listes_diffusion_mensuelle' => "Monthly public lists"
, 'Listes_diffusion_publiques_desc' => "Subscription to these lists is provided on the public site."
, 'Liste_annuelle' => "Annual List"
, 'Publiques_annuelles' => "Public, annual"
, 'Listes_diffusion_annuelle' => "Lists annual public"
, 'Listes_diffusion_publique' => 'Public mailing lists'
, 'Listes_diffusion_privees' => 'Private mailing lists'
, 'Listes_diffusion_privees_desc' => "Subscription to these lists is restricted to administrators and autors of the site."
, 'Listes_diffusion_suspendue' => 'Mailing Lists suspended'
, 'Listes_diffusion_suspendue_desc' => " "
, 'Courriers_en_cours_de_redaction' => 'Editing in progress'
, 'Courriers_en_cours_denvoi' => 'Sending in progress'
, 'Courriers_prets_a_etre_envoye' => "Letters ready to be sent"
, 'Courriers_publies' => "Letters published"
, 'Courriers_auto_publies' => "Automatic letters published"
, 'Courriers_stope' => "Letters stopped being sent"
, 'Courriers_vides' => "Letters canceled (empty)"
, 'Courriers_sans_destinataire' => "Letters without reciever (empty list)"
, 'Courriers_sans_liste' => "Letters without subscribers (list missing)"
, 'devenir_redac'=>'Become an editor of this website'
, 'devenir_membre'=>'Become a member of this website'
, 'devenir_abonne' => "Subscribe to this website"
, 'desabonnement_valid'=>'The following e-mail address is not subscribed anymore' 
, 'pass_recevoir_mail'=>'You will receive a confirmation e-mail specifying how to change your subscription. '
, 'discussion_intro' => 'Hello, <br />Here are the discussions started on the site'
, 'En_redaction' => "Editing in progress"
, 'En_cours' => "In progress"
, 'editeur_nom' => "Editor name "
, 'editeur_adresse' => "Address "
, 'editeur_rcs' => "N&deg; RCS "
, 'editeur_siret' => "N&deg; SIRET "
, 'editeur_url' => "Editor website URL "
, 'editeur_logo' => "Editor logotype URL or DATA URL sheme"
, 'Envoi_abandonne' => "Sending abandoned"
, 'Liste_prive' => "List private"
, 'Liste_publique' => "List public"
, 'message_redac' => 'Editing in progress and ready to send'
, 'Prets_a_envoi' => "Ready to send"
, 'Publies' => "Published"
, 'publies_auto' => "Published (auto)"
, 'Stoppes' => "Stopped"
, 'Sans_destinataire' => "Without reciever"
, 'Sans_abonnement' => "Wihtout subscription"
, 'sans_abonne' => "without subscriber"
, 'sans_moderateur' => "without moderator"

// raccourcis des paniers
, 'aller_au_panier_' => "Go to basket "
, 'aller_aux_listes_' => "Go to the lists "
, 'Nouveau_courrier' => 'Create a new mail'
, 'Nouvelle_liste_de_diffusion' => 'Create a new mailing list'
, 'trieuse_suspendue' => "Sorter suspended"
, 'trieuse_suspendue_info' => "The processing of programmed mailing lists is suspended."
, 'Trieuse_reactivee' => "Sorter reactivated"

// mots
, 'ajout' => "Add"
, 'aucun' => "no"
, 'Configuration' => 'Configuration'
, 'courriers' => 'Letters'
, 'creation' => "Creation"
, '_de_' => " of "
, 'email' => 'E-mail'
, 'format' => 'Format'
, 'modifier' => 'Edit'
, 'max_' => "Max "
, 'Patrons' => 'Templates'
, 'patron_' => "template : "
, 'spiplistes' => "SPIP-Lists"
, 'recherche' => 'Research'
, 'retablir' => "Restore"
, 'site' => 'Website'
, 'sujets' => 'Subject'
, 'sup_' => "Sup."
, 'total' => "Total "
, 'voir' => 'see'
, 'Vides' => "Empty"
, 'choisir' => 'Choose'
, 'desabo' => 'unsubscribe'
, 'desabonnement' => 'Unsubscribe'
, 'desabonnes' => 'Unsubscribe'
, 'destinataire' => 'reciever'
, 'destinataires' => 'Recievers'
, 'erreur' => 'Error'
, 'html' => 'HTML'
, 'retour_link' => 'Reply'
, 'texte' => 'Text'
, 'version' => 'version'
, 'fichier_' => "File "

, 'jquery_inactif' => "jQuery not detected. Thank you to activate it."

);

// English translation: Pierre ROUSSET : p.rousset@gmail.com from the transalation by Simon simon@okko.org

?>
