<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de https://trad.spip.net/tradlang_module/reservation?lang_cible=en
// ** ne pas modifier le fichier **

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// A
	'affichage_par' => ' View by:',
	'afficher_inscription_agenda_explication' => 'Registrations via the form of the plugin agenda ',
	'ajouter_lien_reservation' => 'Add a booking',

	// B
	'bonjour' => 'Hello',

	// C
	'complet' => 'full',
	'cron_fieldset' => 'Automatic closure',
	'cron_label' => 'Automatical closure of an event',

	// D
	'designation' => 'Description',
	'details_reservation' => 'Booking details:',
	'duree_vie_label' => 'Lifetime:',

	// E
	'erreur_email_utilise' => 'This email address is already in use, please log in using this email as login or take another email',
	'erreur_pas_evenement' => 'There is currently no event open for booking',
	'evenement_cloture' => 'Closed Event',
	'evenement_ferme_inscription' => 'Registration closed</br> Click on "registration" to view the current offer.',
	'explication_destinataires_supplementaires' => 'Alerts regarding this booking. A list of email addresses separated by comma.', # MODIF
	'explication_enregistrement_inscrit' => 'Register as an author spip',
	'explication_envoi_separe' => 'The modification of the status of a booking item towards
    <div><strong>"@statuts@"</strong></div> will trigger the sending of an alert.',
	'explication_envoi_separe_detail' => 'The status modification towards <div><strong>"@statuts@"</strong></div> will trigger the sending of a confirmation email!',
	'explication_login' => '<a rel="nofollow" class="login_modal" href="@url@" title="@titre_login@">Please log in</a> if you are already registered on this site',

	// F
	'formulaire_public' => 'Public form',

	// I
	'icone_cacher' => 'Hide',
	'icone_creer_reservation' => 'Create a booking',
	'icone_modifier_client' => 'Modify this client',
	'icone_modifier_reservation' => 'Modify this booking',
	'info_1_reservation' => 'One booking',
	'info_aucun_client' => 'No client',
	'info_aucun_reservation' => 'No booking',
	'info_nb_clients' => '@nb@ clients',
	'info_nb_reservations' => '@nb@ bookings',
	'info_nb_reservations_liees' => '@nb@ linked bookings',
	'info_reservations_auteur' => 'This authors bookings',
	'info_voir_reservations_poubelle' => 'View the Bookings in the dustbin',
	'inscription' => 'Subscription',
	'inscrire' => 'Subscribe',
	'inscrire_liste_attente' => 'Chose another event or register to be added to the waiting list.',

	// L
	'label_action_cloture' => 'Automatical closure:',
	'label_client' => 'Client:',
	'label_date' => 'Date:',
	'label_date_paiement' => 'Payment date:',
	'label_destinataires_supplementaires' => 'Additional recipients:',
	'label_email' => 'Email:',
	'label_enregistrer' => 'I want to register on this site:',
	'label_inscription' => 'registration:',
	'label_lang' => 'Language:',
	'label_modifier_identifiants_personnels' => 'Modify your personal identifiers:',
	'label_mot_passe' => 'Password:',
	'label_mot_passe2' => 'Repeat the password:',
	'label_nom' => 'Name:',
	'label_reference' => 'Reference:',
	'label_reservation' => 'Booking:',
	'label_statut' => 'Status',
	'label_statut_defaut' => 'Default Status:',
	'label_statuts_complet' => 'The "complete" status(es):',
	'label_type_paiement' => 'Payment type:',
	'legend_donnees_auteur' => 'Customer data',
	'legend_donnees_reservation' => 'Booking data',
	'legend_infos_generales' => 'Booking information',

	// M
	'merci_de_votre_reservation' => 'Thank you, your booking has been registered.',
	'message_erreur' => 'Your input has errors!',
	'message_evenement_cloture_vendeur' => 'The event @titre@ just ended. <br />The system has sent a closing message to @client@ - @email@.',
	'montant' => 'Amount',
	'mp_titre_reservation_details' => 'Booking item',

	// N
	'notifications_activer_explication' => 'Send email alerts of bookings?',
	'notifications_activer_label' => 'Activate',
	'notifications_cfg_titre' => 'Alerts',
	'notifications_client_explication' => 'Send the alerts to the customer?',
	'notifications_client_label' => 'Client',
	'notifications_destinataire_label' => 'Recipient',
	'notifications_envoi_separe' => 'Activate the separate sending of confirmation mails for the status:',
	'notifications_envoi_separe_explication' => 'Allows to to trigger the sending of confirmation mail for each Booking item separately.',
	'notifications_expediteur_administrateur_label' => 'Select an administrator:',
	'notifications_expediteur_choix_administrateur' => 'un administrateur',
	'notifications_expediteur_choix_email' => 'an e-mail',
	'notifications_expediteur_choix_facteur' => 'idem plugin Mailman',
	'notifications_expediteur_choix_webmaster' => 'a webmaster',
	'notifications_expediteur_email_label' => 'E-mail of sender:',
	'notifications_expediteur_explication' => 'Choose the alerts’ sender for the seller and buyer',
	'notifications_expediteur_label' => 'Sender',
	'notifications_expediteur_webmaster_label' => 'Select a webmaster:',
	'notifications_explication' => 'Alerts are used to send emails after changes in the booking status : in Waitinglist, accepted, refused, in the dustbin',
	'notifications_parametres' => 'Alert settings',
	'notifications_quand_explication' => 'Which status change triggers the sending of an alert?',
	'notifications_quand_label' => 'Trigger',
	'notifications_vendeur_administrateur_label' => 'Select one or more administrators:',
	'notifications_vendeur_choix_administrateur' => 'one or more administrators',
	'notifications_vendeur_choix_email' => 'one or more emails',
	'notifications_vendeur_choix_webmaster' => 'one or more webmasters',
	'notifications_vendeur_email_explication' => 'Enter one or more email separated by commas:',
	'notifications_vendeur_email_label' => 'Emails for vendor alerts:',
	'notifications_vendeur_label' => 'Vendor',
	'notifications_vendeur_webmaster_label' => 'Select one or more webmasters:',

	// P
	'par_articles' => 'articles',
	'par_evenements' => 'events',
	'par_reservations' => 'bookings',
	'places_disponibles' => 'Available spots:',

	// R
	'recapitulatif' => 'Booking summary:',
	'remerciement' => 'Thank you for registering <br/> Regards',
	'reservation_date' => 'Date:',
	'reservation_de' => 'Booking from',
	'reservation_enregistre' => 'Your Registration has been saved. You will receive a confirmation email. If not, check your spam folder.',
	'reservation_numero' => 'Booking:',

	// S
	'statuts_complet_explication' => 'The status codes in the reservation details that are taken into consideration an event as complete.',
	'sujet_une_reservation_accepte' => 'Confirmation of a booking on @nom@',
	'sujet_une_reservation_accepte_part' => 'Booking partially confirmed on @nom@',
	'sujet_une_reservation_cloture' => 'Event closed on @nom@',
	'sujet_votre_reservation_accepte' => '@nom@: confirmation of your booking',
	'sujet_votre_reservation_accepte_part' => '@nom@: partial confirmation of your reservation',
	'sujet_votre_reservation_cloture' => '@nom@: event closure',

	// T
	'texte_changer_statut_reservation' => 'This booking is:',
	'texte_exporter' => 'export',
	'texte_statut_accepte' => 'accepted',
	'texte_statut_accepte_part' => 'partially accepted',
	'texte_statut_attente' => 'in waiting list',
	'texte_statut_attente_paiement' => 'waiting for payment',
	'texte_statut_cloture' => 'closed',
	'texte_statut_encours' => 'ongoing',
	'texte_statut_poubelle' => 'in the dustbin',
	'texte_statut_refuse' => 'refused',
	'titre_clients' => 'Clients',
	'titre_envoi_separe' => '"Seperate Sending" modus activated',
	'titre_reservation' => 'Booking',
	'titre_reservations' => 'Bookings',
	'total' => 'Total',

	// U
	'une_reservation_de' => 'A booking from: ',
	'une_reservation_sur' => 'A booking on @nom@',

	// V
	'votre_reservation_sur' => '@nom@: your booking'
);
