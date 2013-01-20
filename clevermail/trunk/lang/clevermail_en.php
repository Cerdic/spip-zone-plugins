<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de http://trad.spip.net/tradlang_module/clevermail?lang_cible=en
// ** ne pas modifier le fichier **

if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// A
	'a_partir_csv' => 'From a CSV file :',
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
	'auto_erreur_ce_jour_mois_existe_pas' => 'This day doesn\'t exist in the month.',
	'auto_erreur_ce_jour_mois_pas_possible' => 'The choice of this day is not possible.',
	'auto_erreur_ce_jour_semaine_existe_pas' => 'There is no such day in the week.',
	'auto_erreur_ce_mode_automatisation_existe_pas' => 'This automation mode doesn\'t exist.',
	'auto_erreur_cette_heure_existe_pas' => 'This time doesn\'t exist.',
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
	'auto_subscribers_explication' => 'Les adresses contenus dans ce fichier distant seront automatiquement abonnées une fois par jour à cette lettre.', # NEW
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

Please confirmyour deregistration by clicking on the following link :

 @@URL_CONFIRMATION@@

Thanks',
	'confirmation_votre_inscription' => 'Registration confirmation',
	'confirmation_votre_inscription_multiple' => 'Registration confirmation',
	'confirmation_votre_inscription_text' => '
Bonjour,

Pour confirmer votre inscription à la lettre d\'information @@NOM_COMPLET@@ au format @@FORMAT_INSCRIPTION@@, veuillez cliquer sur ce lien :

 @@URL_CONFIRMATION@@

Merci
', # NEW
	'confirmation_votre_inscription_text_multiple' => '
Bonjour,

Pour confirmer votre inscription aux lettres d\'information suivantes :

 @@NOM_COMPLET@@ 

veuillez cliquer sur ce lien :

 @@URL_CONFIRMATION@@

Merci
', # NEW
	'confirme_desabonnement_multiple_lettre' => 'You are about to delete several subscribers from this letter. Are you sure ?',
	'confirme_suppression_multiple_base' => 'You are about to delete subscribers from the database. Are you sure ?',
	'corps_mail_mod' => 'Bonjour,

Vous recevez ce message en tant que modérateur de la lettre @@NOM_LETTRE@@.

Pour valider l\'inscription de @mail@ à cette lettre, veuillez cliquer sur ce lien :

 @@URL_CONFIRMATION@@

Merci', # NEW
	'cree' => 'Created',
	'creer' => 'Create',
	'creer_lettre' => 'Create a newsletter',
	'creer_message' => 'Create a message',
	'creer_nouveau_message' => 'Create a new message',

	// D
	'deja_inscrit' => 'Vous étiez déjà inscrit à la lettre « @lst_name@ ». Votre mode d\'inscription a été mis à jour.', # NEW
	'deja_validee' => 'Sorry. This operation has already been validated.',
	'demande_transmise' => 'Votre demande d\'inscription à la lettre « @lst_name@ » va être examinée dans les plus brefs délais.', # NEW
	'desabonner' => 'Unsubscribe',
	'desabonner2' => 'unsubscribe',
	'desabonner_abonnes' => 'Deregister the selected subscribers',
	'desabonner_confirmer' => 'Êtes-vous certain de vouloir désabonner cette personne ?', # NEW
	'description' => 'Description',
	'desinscription_confirmation_debut' => 'Désinscription de la lettre d\'information', # NEW
	'desinscription_confirmation_fin' => 'demandée. Vous allez recevoir un message demandant confirmation.', # NEW
	'desinscription_validee' => 'Votre désinscription de la lettre « @lst_name@ » est validée. A bientôt.', # NEW

	// E
	'editer_lettre' => 'Edit the newsletter',
	'email_administrateur' => 'Administrator E-mail',
	'email_expediteur' => 'E-mail sender (from et reply-to)',
	'email_moderateur' => 'Moderator E-mail',
	'email_non_valide' => 'Unvalid address.',
	'email_return_path' => 'E-mail des retours d\'erreurs (return-path)', # NEW
	'emails' => 'E-mails',
	'envoye' => 'Sent',
	'envoyer' => 'Send',
	'envoyer_non_aucun_abonne' => 'No subscriber, can not send',
	'erreur' => 'Error',
	'erreur_contenu_vide' => 'Un nouveau message n\'a pas pu être créé faute de contenu.', # NEW
	'et_ou_saisir_des_adresses' => '...et/ou saisir des adresses.', # NEW

	// F
	'front_clevermail_action_validation' => 'Validation d\'une opération', # NEW
	'front_clevermail_unsubscription_query' => 'Deregistration request',

	// I
	'importer' => 'Import',
	'info_parametres' => 'L\'e-mail de l\'administrateur est utilisé par défaut comme l\'e-mail du modérateur lors de la création d\'une newsletter', # NEW
	'infolettres' => 'Newsletters',
	'informations' => 'Informations',
	'inscription_deja_abonne_autre_mode' => 'Vous étiez déjà inscrit à la lettre « @lst_name@ » dans un autre mode, changement opéré.', # NEW
	'inscription_deja_abonne_meme_mode' => 'Vous étiez déjà inscrit à la lettre « @lst_name@ » dans ce même mode.', # NEW
	'inscription_encours' => 'Votre demande d\'inscription à la lettre @nom_lettre@ est en cours d\'examen. Merci de patienter.', # NEW
	'inscription_mok' => 'Votre demande d\'inscription à la lettre « @lst_name@ » a été soumise au modérateur. Vous serez informé de sa décision.', # NEW
	'inscription_nok' => 'Inscription non authorisée pour cette lettre d\'information', # NEW
	'inscription_ok' => 'Vous allez recevoir un message de demande de confirmation de votre inscription à la lettre « @lst_name@ ».', # NEW
	'inscription_ok_multiple' => 'Vous allez recevoir un message de demande de confirmation de votre inscription aux lettre « @lst_name@ ».', # NEW
	'inscription_validee' => 'Votre inscription à la lettre « @lst_name@ » est validée. Merci.', # NEW

	// L
	'label_contenu_html' => 'HTML content',
	'label_contenu_text' => 'Text content',
	'label_inscription_email' => 'Your e-mail address :',
	'label_inscription_lettres' => 'Choose between the newsletters :',
	'label_inscription_version' => 'Message type :',
	'le_format_des_adresses_email_ne_semble_pas_bon' => 'The e-mail address format doesn\'t seem to be right.',
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
	'mail_info_desinscription_corps' => 'Alerte envoyée par le plugin CleverMail du site @nom_site@ ( @url_site@ ) :

Désinscription de @sub_email@ de la lettre « @lst_name@ »', # NEW
	'mail_info_inscription_corps' => 'Alerte envoyée par le plugin CleverMail du site @nom_site@ ( @url_site@ ) :

Inscription de @sub_email@ à la lettre « @lst_name@ »', # NEW
	'mail_info_inscription_sujet' => 'Subscription to @sub_email@',
	'mail_inscription_multiple' => 'Bonjour,

Pour confirmer votre inscription aux lettres d\'information suivantes :

 @@NOM_COMPLET@@

veuillez cliquer sur ce lien :

 @@URL_CONFIRMATION@@

Merci', # NEW
	'maj_inscription' => 'updated subscription',
	'maj_inscriptions' => 'updated subscriptions',
	'mauvais_affichage' => 'Si vous ne visualisez pas cet email, lisez-le sur le site', # NEW
	'mauvais_identifiant_lettre' => 'Mauvais identifiant de lettre d\'information', # NEW
	'message' => 'message',
	'message_queue_attente' => 'message in waiting list',
	'message_queue_envoye' => '@nb@ message sent',
	'messages' => 'messages',
	'messages_attentes' => 'Messages in waiting list',
	'messages_attentes_text' => 'Ici sont listés les messages qui sont en file d\'attente pour être envoyés', # NEW
	'messages_cours_envoi' => 'Messages being send',
	'messages_cours_envoi_text' => 'Ici sont listés les messages qui sont en cours d\'envoi par le facteur, lot par lot', # NEW
	'messages_envoyes' => 'Messages sent',
	'messages_envoyes_text' => 'Ici sont listés les messages qui ont été envoyés avec succès', # NEW
	'messages_queue_attente' => 'messages in waiting list',
	'messages_queue_envoye' => '@nb@ messages sent',
	'mod_closed' => 'Closed',
	'mod_email' => 'E-mail',
	'mod_explication' => 'Les différents modes de modération sont décrits ci-contre.', # NEW
	'mod_explication_closed' => 'Closed : Nobody can subscribe',
	'mod_explication_email' => 'E-mail : tout le monde peut s\'inscrire après confirmation par e-mail', # NEW
	'mod_explication_mod' => 'Modérée : le modérateur doit accepter l\'inscription', # NEW
	'mod_explication_open' => 'Ouverte : tout le monde peut s\'inscrire sans confirmation', # NEW
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
	'nettoyer_abonnement' => 'Vider les abonnements en attente depuis plus d\'un mois', # NEW
	'news1' => 'The n°1 news',
	'news_depuis' => 'The news since',
	'nom' => 'Name',
	'nom_formulaire_clevermail' => 'Formulaire d\'abonnement aux listes de diffusion', # NEW
	'nombre_messages' => 'Number of messages per sending',
	'nouveau_message' => 'new message',
	'nouveaux_abonnes_et' => ' and ',
	'nouveaux_messages' => 'New Messages',
	'nouveaux_messages_text' => 'Ici sont listés les messages qui ne sont pas encore envoyés', # NEW

	// P
	'parametres' => 'Parameters',
	'plusieurs_messages_en_attente' => '@nb@ in waiting list',
	'plusieurs_messages_en_cours_envoi' => '@nb@ being send',
	'plusieurs_messages_envoyes' => '@nb@ sent',
	'prefixer_messages' => 'Prefix',
	'prefixer_messages_explication' => 'Préfixer les sujets des messages avec le nom de la lettre d\'information', # NEW
	'procedure_termine' => 'Click here to end',
	'proprietes' => 'Properties',

	// R
	'resultats' => 'results',

	// S
	'selection_des_listes' => 'Lists selection',
	'send_error' => 'Erreur lors de l\'envoi du message de demande de confirmation, veuillez réessayer.', # NEW
	'source_des_abonnes' => 'Subscribers list',
	'statistiques' => 'Statistics',
	'sujet' => 'Subject',
	'sujet_mail_inscription_multiple' => 'Subscription confirmation',
	'sujet_mail_mod' => 'Modération de la lettre @nom_lettre@', # NEW
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
	'url_templates' => 'URL des templates générés', # NEW
	'url_templates_explication' => 'Précisez l\'URL d\'une page distante, ou le nom d\'un squelette SPIP local, sans l\'extension .html. CleverMail propose le squelette par défaut <code>clevermail_nouveautes_html</code>.', # NEW

	// V
	'version_html' => 'HTML version',
	'version_txt' => 'Raw text version',
	'version_txt_explication' => 'Si vous ne remplissez pas ce paramètre, la version texte sera obtenue automatiquement à partir de la version HTML. CleverMail propose le squelette par défaut <code>clevermail_nouveautes_text</code>.', # NEW
	'veuillez_corriger_votre_saisie' => 'Please correct this entry.',
	'vous_devez_choisir_au_moins_une_liste' => 'You have to choose at least one letter',
	'vous_devez_choisir_un_fichier' => 'You have to choose a file...'
);

?>
