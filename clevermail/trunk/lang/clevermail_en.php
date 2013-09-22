<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de http://trad.spip.net/tradlang_module/clevermail?lang_cible=en
// ** ne pas modifier le fichier **

if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// A
	'a_partir_csv' => 'From a CSV file :', # MODIF
	'abonne' => 'subscriber',
	'abonne_ajoute' => 'Added subscriber',
	'abonne_aucune_lettre' => 'Subscribed to no newsletter',
	'abonne_inconnu' => 'Unknown subscriber',
	'abonne_lettres' => 'Subscribe to newslettere',
	'abonne_maj' => 'updated subscriber',
	'abonnes' => 'subscribers',
	'abonnes_ajoutes' => 'Added subscribers',
	'abonnes_maj' => 'Updated subscribers',
	'abonnes_majuscule' => 'Subscribers',
	'actions' => 'Actions',
	'actualiser' => 'Update',
	'administration' => 'CleverMail',
	'ajouter_abonnes' => 'Add subscribers',
	'annuler' => 'Cancel',
	'apercu' => 'Preview',
	'apercu_html' => 'HTML preview',
	'apercu_texte' => 'Text preview',
	'aucun_abonne' => 'no subscriber',
	'aucun_abonne_ajoute' => 'no subscriber added',
	'aucun_message' => 'no message',
	'aucun_message_en_attente' => 'none waiting',
	'aucun_message_en_cours_envoi' => 'none being sent',
	'aucun_message_envoye' => 'none sent',
	'aucun_message_queue_envoye' => 'no message sent',
	'aucun_nouvel_abonne' => 'no new subscriber',
	'aucun_resultat' => 'No result',
	'aucune_inscription' => 'No subscription correspond to this parameter.',
	'aucune_liste' => 'Please select at least one newsletter',
	'auto_erreur_ce_jour_mois_existe_pas' => 'This day doesn’t exist in the month.',
	'auto_erreur_ce_jour_mois_pas_possible' => 'The choice of this day is not possible.',
	'auto_erreur_ce_jour_semaine_existe_pas' => 'There is no such day in the week.',
	'auto_erreur_ce_mode_automatisation_existe_pas' => 'This automation mode doesn’t exist.',
	'auto_erreur_cette_heure_existe_pas' => 'This time doesn’t exist.',
	'auto_erreur_choisir_un_jour_minimum' => 'You must chose one day at least.',
	'auto_heure_creation' => 'Message creation time',
	'auto_heure_creation_explication' => 'Messages will be generated at this time and directly put in the waiting list to be send.',
	'auto_heure_creation_minutes' => ':00',
	'auto_jours_semaine_explication' => 'Messages will be created these days.',
	'auto_mode' => 'Mode',
	'auto_mode_day' => 'Daily sending',
	'auto_mode_month' => 'Monthly sending',
	'auto_mode_none' => 'No automation',
	'auto_mode_week' => 'Weekly  sending',
	'auto_month_day' => 'Day of the month',
	'auto_month_day_explication' => 'The available dayx are only from 1 to 28 to avoid problems for the months with less than 31 days.',
	'auto_subscribers' => 'Automatic subscribers adding',
	'auto_subscribers_explication' => 'Addresses contained in this remote file will be automatically subscribed once per day to this letter.',
	'auto_subscribers_mode' => 'Subscription mode',
	'auto_subscribers_url' => 'URL of the address file',
	'auto_week_day_friday' => 'Friday',
	'auto_week_day_monday' => 'Monday',
	'auto_week_day_saturday' => 'Saturday',
	'auto_week_day_sunday' => 'Sunday',
	'auto_week_day_thursday' => 'Thursday',
	'auto_week_day_tuesday' => 'Tuesday',
	'auto_week_day_wednesday' => 'Wednesday',
	'auto_week_days' => 'Day(s) of the week',
	'automatisation' => 'sending automation',

	// B
	'bouton_inscription' => 'Subscribe',

	// C
	'ce_champ_est_obligatoire' => 'This field is mandatory.',
	'cette_adresse_email_n_est_pas_valide' => 'This e-mail address is not valid.',
	'changements_mode_abonnement' => 'changes in subscribing mode',
	'changer_mode' => 'Mode change',
	'choix_toutes_les_listes' => 'All the lists',
	'choix_version_html' => 'HTML',
	'choix_version_texte' => 'raw text',
	'clevermail' => 'CleverMail',
	'configuration_generale' => 'General configuration',
	'confirmation_desinscription' => 'Deregistration confirmation sent by e-mail',
	'confirmation_inscription' => 'Registration confirmation sent by e-mail',
	'confirmation_inscription_multiple' => 'Multiple registration confirmation sent by e-mail',
	'confirmation_votre_desinscription' => 'Deregistration confirmation',
	'confirmation_votre_desinscription_text' => 'Hello,

Please confirm your deregistration by clicking on the following link :

 @@URL_CONFIRMATION@@

Thanks', # MODIF
	'confirmation_votre_inscription' => 'Registration confirmation',
	'confirmation_votre_inscription_multiple' => 'Registration confirmation',
	'confirmation_votre_inscription_text' => '
Hello,

Please confirm your registration  to the newsletter @@NOM_COMPLET@@ at the format @@FORMAT_INSCRIPTION@@, by clicking on the following link :

 @@URL_CONFIRMATION@@

Thanks
', # MODIF
	'confirmation_votre_inscription_text_multiple' => '
Hello,

Please confirm your registration  to the following newsletters :

 @@NOM_COMPLET@@ 

by clicking on the link :

 @@URL_CONFIRMATION@@

Thanks
', # MODIF
	'confirme_desabonnement_multiple_lettre' => 'You are about to delete several subscribers from this letter. Are you sure ?',
	'confirme_suppression_multiple_base' => 'You are about to delete subscribers from the database. Are you sure ?',
	'corps_mail_mod' => 'Hello,

You receive this message as a moderator of the letter @@NOM_LETTRE@@.

To valid the registration of @mail@ to this letter, click on the link :

 @@URL_CONFIRMATION@@

Thanks', # MODIF
	'cree' => 'Created',
	'creer' => 'Create',
	'creer_lettre' => 'Create a newsletter',
	'creer_message' => 'Create a message',
	'creer_nouveau_message' => 'Create a new message',

	// D
	'deja_inscrit' => 'You have already registered to the letter « @lst_name@ ». Your registration mode has been updated.', # MODIF
	'deja_validee' => 'Sorry. This operation has already been validated.',
	'demande_transmise' => 'Your subscription request to the letter « @lst_name@ » will be examined as soon as possible.', # MODIF
	'desabonner' => 'Unsubscribe',
	'desabonner2' => 'unsubscribe',
	'desabonner_abonnes' => 'Deregister the selected subscribers',
	'desabonner_confirmer' => 'Are you sure you want to unsubscribe this person?',
	'description' => 'Description',
	'desinscription_confirmation_debut' => 'Unsubscribe from the newsletter',
	'desinscription_confirmation_fin' => 'requested. You will receive a message asking for confirmation.',
	'desinscription_validee' => 'Unsubscribing to the letter « @lst_name@ » is enabled. See you soon.', # MODIF

	// E
	'editer_lettre' => 'Edit the newsletter',
	'email_administrateur' => 'Administrator E-mail',
	'email_expediteur' => 'E-mail sender (from et reply-to)',
	'email_moderateur' => 'Moderator E-mail',
	'email_non_valide' => 'Unvalid address.',
	'email_return_path' => 'E-mail error returns (return-path)',
	'emails' => 'E-mails',
	'envoye' => 'Sent',
	'envoyer' => 'Send',
	'envoyer_non_aucun_abonne' => 'No subscriber, can not send',
	'erreur' => 'Error',
	'erreur_contenu_vide' => 'A new message could not be created due to lack of content.',
	'et_ou_saisir_des_adresses' => '...and/or enter addresses.',
	'exporter' => 'Export the list of subscribers',

	// F
	'front_clevermail_action_validation' => 'Validation of an operation',
	'front_clevermail_unsubscription_query' => 'Deregistration request',

	// I
	'importer' => 'Import',
	'info_parametres' => 'The e-mail address of the administrator is used by default e-mail of the moderator at the creation of a newsletter',
	'infolettres' => 'Newsletters',
	'informations' => 'Informations',
	'inscription_deja_abonne_autre_mode' => 'You have already registered to the letter to the « @lst_name@ » in another mode, the modification is made.', # MODIF
	'inscription_deja_abonne_meme_mode' => 'You have already registered to the letter  « @lst_name@ » in the same mode. Nevertheless, we appreciate your enthusiasm.', # MODIF
	'inscription_encours' => 'Your subscription request to the letter @nom_lettre@ is under consideration. Thank you for your patience.',
	'inscription_mok' => 'Your subscription request to the letter « @lst_name@ » was submitted to the moderator. You will be informed of his decision.', # MODIF
	'inscription_nok' => 'Subscription is not authorized for this newsletter',
	'inscription_ok' => 'You will receive a message of subscription confirmation for your registration to the letter « @lst_name@ ».', # MODIF
	'inscription_ok_multiple' => 'You will receive a message asking you to confirm your subscription to the letter « @lst_name@ ».', # MODIF
	'inscription_validee' => 'Your subscribtion to the letter « @lst_name@ » is enabled. Thank you.', # MODIF

	// L
	'label_contenu_html' => 'HTML content',
	'label_contenu_text' => 'Text content',
	'label_inscription_email' => 'Your e-mail address :', # MODIF
	'label_inscription_lettres' => 'Choose between the newsletters :', # MODIF
	'label_inscription_version' => 'Message type :', # MODIF
	'le_format_des_adresses_email_ne_semble_pas_bon' => 'The e-mail address format doesn’t seem to be right.',
	'lettre_meme_nom' => 'Another newsletter already has this name',
	'lettre_sans_nom' => 'A newsletter must have a name',
	'lettres_information' => 'Newsletters',
	'lettres_non_classees' => 'Unclassified letters',
	'lire_en_ligne' => 'Read the message on the website',
	'liste_abonnes' => 'Subscribers list',
	'liste_lettres' => 'Newsletters list',
	'liste_lettres_aucune' => 'You did not yet create a newsletter',
	'liste_messages' => 'Messages',

	// M
	'mail_info_desinscription_corps' => 'Alert sent by the plugin CleverMail form the website @nom_site@ ( @url_site@ ) :

Deregistration from @sub_email@ to the letter « @lst_name@ »', # MODIF
	'mail_info_inscription_corps' => 'Alert sent by the plugin CleverMail form the website @nom_site@ ( @url_site@ ) :

Subscription from @sub_email@ to the letter « @lst_name@ »', # MODIF
	'mail_info_inscription_sujet' => 'Subscription to @sub_email@',
	'mail_inscription_multiple' => 'Hello,

Please confirm your registration to the following newsletters :

 @@NOM_COMPLET@@

by clicking on the link :

 @@URL_CONFIRMATION@@

Thanks', # MODIF
	'maj_inscription' => 'updated subscription',
	'maj_inscriptions' => 'updated subscriptions',
	'mauvais_affichage' => 'If you do not see this email properly, read it on the website',
	'mauvais_identifiant_lettre' => 'Bad identifier for the newsletter',
	'message' => 'message',
	'message_queue_attente' => 'message in waiting list',
	'message_queue_envoye' => '@nb@ message sent',
	'messages' => 'messages',
	'messages_attentes' => 'Messages in waiting list',
	'messages_attentes_text' => 'Here are listed the messages that are queued to be sent',
	'messages_cours_envoi' => 'Messages being send',
	'messages_cours_envoi_text' => 'Here are listed the messages that are being sent by the postman, lot by lot',
	'messages_envoyes' => 'Messages sent',
	'messages_envoyes_text' => 'Here are listed the messages that have been sent successfully',
	'messages_queue_attente' => 'messages in waiting list',
	'messages_queue_envoye' => '@nb@ messages sent',
	'mod_closed' => 'Closed',
	'mod_email' => 'E-mail',
	'mod_explication' => 'The different modes of moderation are described there.',
	'mod_explication_closed' => 'Closed : Nobody can subscribe', # MODIF
	'mod_explication_email' => 'E-mail: anyone can register after confirmation by e-mail', # MODIF
	'mod_explication_mod' => 'Moderated : the moderator must accept the subscription', # MODIF
	'mod_explication_open' => 'Open: anyone can register without confirmation', # MODIF
	'mod_mod' => 'Moderated',
	'mod_open' => 'Open',
	'mode' => 'Mode',
	'moderation' => 'Subscriptions moderation',
	'modifie' => 'Modified',
	'modifier' => 'Modify',
	'modifier_abonne' => 'Modify a subscriber',
	'modifier_message' => 'Modify a message',
	'modifier_submit' => 'Modify',

	// N
	'n_nouveaux_abonnes' => 'new sbscribers',
	'nettoyer_abonnement' => 'Empty subscriptions pending for over a month',
	'news1' => 'The n°1 news',
	'news_depuis' => 'The news since',
	'nom' => 'Name',
	'nom_formulaire_clevermail' => 'Subscription form to mailing lists',
	'nombre_messages' => 'Number of messages per sending',
	'nouveau_message' => 'new message',
	'nouveaux_abonnes_et' => ' and ',
	'nouveaux_messages' => 'New Messages',
	'nouveaux_messages_text' => 'Here are listed the messages that have not yet be sent',

	// P
	'parametres' => 'Parameters',
	'plusieurs_messages_en_attente' => '@nb@ in waiting list',
	'plusieurs_messages_en_cours_envoi' => '@nb@ being send',
	'plusieurs_messages_envoyes' => '@nb@ sent',
	'prefixer_messages' => 'Prefix',
	'prefixer_messages_explication' => 'Subject prefix messages subjects with the name of the newsletter',
	'procedure_termine' => 'Click here to end',
	'proprietes' => 'Properties',

	// R
	'resultats' => 'results',

	// S
	'selection_des_listes' => 'Lists selection',
	'send_error' => 'Error while sending the message of confirmation request, please try again.',
	'source_des_abonnes' => 'Subscribers list',
	'statistiques' => 'Statistics',
	'sujet' => 'Subject',
	'sujet_mail_inscription_multiple' => 'Subscription confirmation',
	'sujet_mail_mod' => 'Moderation of the letter @nom_lettre@',
	'sujet_message' => 'Message subject',
	'sujet_vide' => 'The subject must not be empty',
	'supprimer' => 'Delete',
	'supprimer_abonne_base' => 'Delete permanently this suscriber from the database',
	'supprimer_abonnes' => 'Delete the selected subscribers',
	'supprimer_confirmer' => 'Are you sure you want to delete ?',

	// T
	'tags_specifiques' => 'Specific tags',

	// U
	'un_message_en_attente' => '1 in waiting list',
	'un_message_en_cours_envoi' => '1 being sent',
	'un_message_envoye' => '1 sent',
	'url_templates' => 'URL of the generated templates',
	'url_templates_explication' => 'Specify the URL of a remote page, or the name of a local SPIP skeleton without the extension .html. CleverMail provides the skeleton by default <code>clevermail_nouveautes_html</code>.',

	// V
	'version_html' => 'HTML version',
	'version_txt' => 'Raw text version',
	'version_txt_explication' => 'If you do not fill in this parameter, the text version will be obtained automatically from the HTML version. CleverMail provides the skeleton by default <code>clevermail_nouveautes_text</code>.',
	'veuillez_corriger_votre_saisie' => 'Please correct this entry.',
	'vous_devez_choisir_au_moins_une_liste' => 'You have to choose at least one letter',
	'vous_devez_choisir_un_fichier' => 'You have to choose a file...'
);

?>
