<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de https://trad.spip.net/tradlang_module/mailsubscriber?lang_cible=en
// ** ne pas modifier le fichier **

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// B
	'bouton_importer' => 'Import',
	'bouton_invitation' => 'Invite a friend to subscribe',
	'bouton_previsu_importer' => 'Preview',

	// C
	'confirmsubscribe_invite_texte_email_1' => '@invite_email_from@ invite you to subscribe to Newsletter from @nom_site_spip@ with the email @email@.',
	'confirmsubscribe_invite_texte_email_3' => 'If this is a mistake from our side, you can safely ignore this mail: this invitation will be automaticily canceled.',
	'confirmsubscribe_invite_texte_email_liste_1' => '@invite_email_from@ invites you to subscribe the « @titre_liste@ » of @nom_site_spip@ with email address @email@.', # MODIF
	'confirmsubscribe_sujet_email' => '[@nom_site_spip@] Confirmation of subscription to the Newsletter',
	'confirmsubscribe_texte_email_1' => 'You asked to subscribe to the newsletter @nom_site_spip@ with the address @email@.',
	'confirmsubscribe_texte_email_2' => 'To confirm your subscription, click on the following link:
@url_confirmsubscribe@',
	'confirmsubscribe_texte_email_3' => 'If there is a mistake from our side or if you have changed your mind , you can safely ignore this mail: this request will automatically be canceled.',
	'confirmsubscribe_texte_email_envoye' => 'An email was sent to this address for confirmation.',
	'confirmsubscribe_texte_email_liste_1' => 'You have asked for a subscription to the list « @titre_liste@ » of @nom_site_spip@ with email address @email@.', # MODIF
	'confirmsubscribe_titre_email' => 'Confirmation of subscription to the Newsletter',
	'confirmsubscribe_titre_email_liste' => 'Confirmation of your subscription to list « <b>@titre_liste@</b> »', # MODIF

	// D
	'defaut_message_invite_email_subscribe' => 'Hello, I subscribed to newsletter from @nom_site_spip@ and I invite you to join me and subscribe too.',

	// E
	'erreur_adresse_existante' => 'This email address is already in the list',
	'erreur_adresse_existante_editer' => 'This e-mail address is already registered - <a href="@url@">Edit this user</a>',
	'erreur_technique_subscribe' => 'A technical error made impossible to record your subscription.',
	'explication_listes_diffusion_option_defaut' => 'One or more list identifiers separated by a comma',
	'explication_listes_diffusion_option_statut' => 'Filter the lists by status',
	'explication_to_email' => 'People’s email to invite to subscribe to newsletter (several address separated by comma. 5 maximum emails)',

	// F
	'force_synchronisation' => 'Synchronise',

	// I
	'icone_creer_mailsubscriber' => 'Add a subscriber',
	'icone_modifier_mailsubscriber' => 'Edit this subscriber',
	'info_1_adresse_a_importer' => '1 address to import',
	'info_1_mailsubscriber' => '1 subscriber',
	'info_aucun_mailsubscriber' => 'No subscriber',
	'info_email_inscriptions' => 'Inscriptions for @email@:',
	'info_email_limite_nombre' => 'Invitation limited to 5 people.',
	'info_email_obligatoire' => 'Email is mandatory.',
	'info_emails_invalide' => 'One email of the list is not correct.',
	'info_nb_adresses_a_importer' => '@nb@ addresses to import',
	'info_nb_mailsubscribers' => '@nb@ subscribers',
	'info_statut_poubelle' => 'trash',
	'info_statut_prepa' => 'not subscribed',
	'info_statut_prop' => 'waiting',
	'info_statut_refuse' => 'suspended',
	'info_statut_valide' => 'subscribed',

	// L
	'label_desactiver_notif_1' => 'Disable notification of entries for this import',
	'label_email' => 'Email',
	'label_file_import' => 'File to import',
	'label_from_email' => 'Invitation from email',
	'label_informations_liees' => 'Segmentable information',
	'label_inscription' => 'Subscription',
	'label_lang' => 'Language',
	'label_listes' => 'Lists',
	'label_listes_diffusion_option_statut' => 'Status',
	'label_listes_import_subscribers' => 'Subscribe to lists',
	'label_mailsubscriber_optin' => 'I want to receive the Newsletter',
	'label_message_invite_email_subscribe' => 'Message to send with the invitation',
	'label_nom' => 'Name',
	'label_optin' => 'Opt-in',
	'label_statut' => 'Status',
	'label_to_email' => 'Email to invite',
	'label_toutes_les_listes' => 'All',
	'label_valid_subscribers_1' => 'Automatic validation of subscription without asking to confirm',
	'label_vider_table_1' => 'Delete all addresses in this database before importing',

	// M
	'mailsubscribers_poubelle' => 'Removed',
	'mailsubscribers_prepa' => 'Not Subscribed',
	'mailsubscribers_prop' => 'Waiting',
	'mailsubscribers_refuse' => 'Suspended',
	'mailsubscribers_tous' => 'All',
	'mailsubscribers_valide' => 'Subscribed',

	// S
	'subscribe_deja_texte' => 'The email address @email@ is already in our mailing list', # MODIF
	'subscribe_sujet_email' => '[@nom_site_spip@] Subscribe to our Newsletter',
	'subscribe_texte_email_1' => 'We have recorded your subscription to our newsletter with the email @email@.',
	'subscribe_texte_email_2' => 'Thank you for the interest you have shown in @nom_site_spip@.',
	'subscribe_texte_email_3' => 'In case of error, or if you change your mind, you can unsubscribe at any time using the following link :
@url_unsubscribe@',
	'subscribe_texte_email_liste_1' => 'We have taken you subscription to list « @titre_liste@ » with email address @email@ into account.', # MODIF
	'subscribe_titre_email' => 'Subscribe to Newsletter',
	'subscribe_titre_email_liste' => 'Subscription to list « <b>@titre_liste@</b> »', # MODIF

	// T
	'texte_ajouter_mailsubscriber' => 'Add subscriber to the newsletter',
	'texte_avertissement_import' => 'A <tt>status column</tt> is supplied, the data will be imported as is , overwriting those that may already exist for some email.',
	'texte_changer_statut_mailsubscriber' => 'This user is subscribed to the newsletter :',
	'texte_import_export_bonux' => 'To import or export the lists, please install plugin <a href="https://plugins.spip.net/spip_bonux">SPIP-Bonux</a>',
	'texte_statut_en_attente_confirmation' => 'pending confirmation',
	'texte_statut_pas_encore_inscrit' => 'not registered',
	'texte_statut_refuse' => 'suspended',
	'texte_statut_valide' => 'active',
	'texte_vous_avez_clique_vraiment_tres_vite' => 'You clicked the confirmation button really fast. Are you sure you are human?',
	'titre_bonjour' => 'Hi',
	'titre_export_mailsubscribers' => 'Export subcribers',
	'titre_export_mailsubscribers_all' => 'Export all emails',
	'titre_import_mailsubscribers' => 'Import emails',
	'titre_langue_mailsubscriber' => 'Language of the subcriber',
	'titre_listes_de_diffusion' => 'Mailing Lists',
	'titre_logo_mailsubscriber' => 'Logo of the subcriber',
	'titre_mailsubscriber' => 'Email Subscriber',
	'titre_mailsubscribers' => 'Email Subscribers',

	// U
	'unsubscribe_deja_texte' => 'The email address @email@ is not in our mailing list.', # MODIF
	'unsubscribe_sujet_email' => '[@nom_site_spip@] Unsubscribe from Newsletter',
	'unsubscribe_texte_confirmer_email_1' => 'Please click on button to confirm unsubscribe of email @email@: ',
	'unsubscribe_texte_confirmer_email_liste_1' => 'Please confirm your request to remove @email@ from the list « @titre_liste@ » by clicking this button: ', # MODIF
	'unsubscribe_texte_email_1' => 'The email address @email@ has been removed from our mailing list.', # MODIF
	'unsubscribe_texte_email_2' => 'We hope to see you soon on @nom_site_spip@.',
	'unsubscribe_texte_email_3' => 'In case of error, or if you change your mind, you can re-subscribe at any time using the following link :
@url_subscribe@',
	'unsubscribe_texte_email_liste_1' => 'Email address @email@ has been removed from our distribution list « @titre_liste@ ».', # MODIF
	'unsubscribe_titre_email' => 'Unsubscribe from Newsletter',
	'unsubscribe_titre_email_liste' => 'Removal from list « <b>@titre_liste@</b> »' # MODIF
);
