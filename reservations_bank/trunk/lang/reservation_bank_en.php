<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de https://trad.spip.net/tradlang_module/reservation_bank?lang_cible=en
// ** ne pas modifier le fichier **

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// L
	'label_cacher_paiement_public' => 'do not display the paiment form on the public site', # MODIF
	'label_fieldset_montant_detail' => 'Specify the amount (in @devise@) for each booking item',
	'label_fieldset_specifier' => 'Specify',
	'label_specifier_montant' => 'Specify the amount',

	// M
	'merci_de_votre_reservation_paiement' => 'Your booking <b>@reference@</b> has been validated.',
	'message_erreur_montant_credit' => 'The amount is higher than your current credit of @credit@ !',
	'message_erreur_montant_reservations_detail' => 'The amount should not be higher than @montant_ouvert@ (open amount)',
	'montant_paye' => 'Paid :',

	// P
	'paiement_reservation' => 'Paiment of the booking #@id_reservation@',

	// R
	'reservation_bank_titre' => 'Booking Bank',

	// T
	'texte_statut_attente_part' => ' partially paid- in waiting list',
	'texte_statut_attente_paye' => ' paid - in waiting list',
	'titre_page_configurer_reservation_bank' => 'Settings Booking Bank',
	'titre_paiement_reservation' => 'Paiment of the Booking',
	'titre_payer_reservation' => 'Pay the booking'
);
