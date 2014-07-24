<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP

if (!defined('_ECRIRE_INC_VERSION')) return;


$GLOBALS[$GLOBALS['idx_lang']] = array(

	// B
	"bouton_importer" => "Import",
	'bouton_invitation' => "Invite a friend to subscribe",
	"bouton_previsu_importer" => "Preview",

	// C
	"confirmsubscribe_sujet_email" => "[@nom_site_spip@] Confirmation of subscription to the Newsletter",
	"confirmsubscribe_titre_email" => "Confirmation of subscription to the Newsletter",
	'confirmsubscribe_invite_texte_email_1' => '@invite_email_from@ invite you to subscribe to Newsletter from @nom_site_spip@ with the email @email@.',
	"confirmsubscribe_texte_email_1" => "You asked to subscribe to the newsletter @nom_site_spip@ with the address @email@.",
	"confirmsubscribe_texte_email_2" => " To confirm your subscription , thank you click on the following link: 
@url_confirmsubscribe@",
	"confirmsubscribe_texte_email_3" => "If there is a mistake from our side or if you have changed your mind , you can safely ignore this mail: this request will automatically be canceled.",
	'confirmsubscribe_invite_texte_email_3' => 'If this is a mistake from our side, you can safely ignore this mail: this invitation will be automaticily canceled.',
	"confirmsubscribe_texte_email_envoye" => "An email was sent to this address for confirmation.",

	// D
  'defaut_message_invite_email_subscribe' =>'Hello, I subscribed to newsletter from @nom_site_spip@ and I invite you to join me and subscribe too.' ,

	// E
	"erreur_adresse_existante" => "This email address is already in the list",
	'erreur_technique_subscribe' => 'A technical error made impossible to record your subscription.',
	'explication_to_email' => 'People\'s email to invite to subscribe to newsletter (several address separated by comma. 5 maximum emails)',

	// I
	"icone_creer_mailsubscriber" => "Add a subscribtion",
	"icone_modifier_mailsubscriber" => "Edit this subscribtion",
	'info_email_limite_nombre' => 'Invitation limited to 5 people.',
	"info_1_mailsubscriber" => "1 subscribed user",
	"info_aucun_mailsubscriber" => "No subscribers",
	"info_nb_mailsubscribers" => "@nb@ subscribed users",
	"info_1_adresse_a_importer" => "1 address to import",
	"info_nb_adresses_a_importer" => "@nb@ addresses to import",
	'info_email_obligatoire' => 'Email is mandatory.',
  'info_emails_invalide' => "One email of the list is not correct.",
	"info_statut_prepa" => "not registered",
	"info_statut_prop" => "Waiting",
	"info_statut_valide" => "registered",
	"info_statut_refuse" => "suspended",
	"info_statut_poubelle" => "trash",

	// L
	"label_listes" => "Lists",
	"label_email" => "Email",
	'label_from_email' => 'Invitation from email',
	"label_lang" => "Language",
	"label_nom" => "Name",
	"label_optin" => "Opt-in",
	"label_statut" => "Status",
	"label_mailsubscriber_optin" => "I want to receive the Newsletter",
	'label_message_invite_email_subscribe' =>"Message to send with the invitation",
	"label_file_import" => "File to import",
	'label_listes_import_subscribers' => 'Subscribe to lists',
	'label_to_email' =>'Email to invite',
	"label_toutes_les_listes" => "All",
	"label_desactiver_notif_1" => "Disable notification of entries for this import",
	'label_valid_subscribers_1' => 'Automatic validation of subscription without asking to confirm',
	"label_vider_table_1" => "Delete all addresses in this database before importing",

	// M
	"mailsubscribers_tous" => "All",
	"mailsubscribers_valide" => "Registered",
	"mailsubscribers_prepa" => "Not Registered",
	"mailsubscribers_prop" => "To be confirmed",
	"mailsubscribers_refuse" => "Unsubscribed",
	"mailsubscribers_poubelle" => "Removed",

	// S
	"subscribe_sujet_email" => "[@nom_site_spip@] Subscribe to our Newsletter",
	"subscribe_titre_email" => "Subscribe to Newsletter",
	"subscribe_texte_email_1" => "We have taken into account your signing up for our newsletter with the email address @email@.",
	"subscribe_deja_texte" => "The email address @email@ is already in our mailing list",
	"subscribe_texte_email_2" => "Thank you for the interest you have shown in @nom_site_spip@.",
	"subscribe_texte_email_3" => "In case of error, or if you change your mind, you can unsubcribe at any time using the following link :
@url_unsubscribe@",

	// U
	"unsubscribe_sujet_email" => "[@nom_site_spip@] Unsubscribe from Newsletter",
	"unsubscribe_titre_email" => "Unsubscribe from Newsletter",
	'unsubscribe_texte_confirmer_email_1' => 'Please click on button to confirm unsubscribe of email @email@: ',
	"unsubscribe_texte_email_1" => "The email address @email@ has been removed from our mailing list.",
	"unsubscribe_deja_texte" => "The email address @email@ is not in our mailing list.",
	"unsubscribe_texte_email_2" => "We hope to see you soon on @nom_site_spip@.",
	"unsubscribe_texte_email_3" => "In case of error, or if you change your mind, you can re-enroll at any time using the following link :
@url_subscribe@",

	// T
	"texte_ajouter_mailsubscriber" => "Add subscribes to the newsletter",
	"texte_avertissement_import" => "A <tt>status column</tt> is supplied, the data will be imported as is , overwriting those that may already exist for some email.",
	"texte_changer_statut_mailsubscriber" => "This user is subscribed to the newsletter :",
	"titre_langue_mailsubscriber" => "Language of the registrant",
	"titre_logo_mailsubscriber" => "Logo of the registrant",
	"titre_mailsubscriber" => "Registered to the newsletter",
	"titre_mailsubscribers" => "Registered to shipments by email",
	"titre_export_mailsubscribers" => "Export registered",
	"titre_export_mailsubscribers_all" => "Export all addresses",
	"titre_import_mailsubscribers" => "Import Addresses",
	"titre_listes_de_diffusion" => "Mailing Lists",


	"texte_statut_pas_encore_inscrit" => "not registered",
	"texte_statut_en_attente_confirmation" => "pending confirmation",
	"texte_statut_valide" => "active",
	"texte_statut_refuse" => "suspended",

);

?>