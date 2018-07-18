<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de https://trad.spip.net/tradlang_module/reservation_bank?lang_cible=en
// ** ne pas modifier le fichier **

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// C
	'cfg_titre_espace_prive' => 'Private space',

	// L
	'label_cacher_paiement_public' => 'Do not display the payment form on the public site',
	'label_choisir_mode_paiement' => 'Choose your payment type :',
	'label_definir_presta_defaut' => 'Define a unique payment type for the payment selection.',
	'label_fieldset_montant_detail' => 'Specify the amount (in @devise@) for each booking item',
	'label_fieldset_specifier' => 'Specify',
	'label_preceder_formulaire' => 'Display the payment form before the booking summary.', # MODIF
	'label_presta_defaut' => 'Available recipients',
	'label_specifier_montant' => 'Specify the amount',

	// M
	'merci_de_votre_reservation_paiement' => 'Your booking <b>@reference@</b> has been validated.',
	'message_erreur_montant_credit' => 'The amount is higher than your current credit of @credit@ !',
	'message_erreur_montant_reservations_detail' => 'The amount should not be higher than @montant_ouvert@ (open amount)',
	'message_paiement_vendeur' => 'Payment mode: "@mode@",  see <a href="@url@">details</a>',
	'montant_paye' => 'Paid :',

	// P
	'paiement_commande' => 'Payment booking #@id_commande@', # MODIF
	'paiement_reservation' => 'Paiment of the booking #@id_reservation@',

	// R
	'reservation_bank_titre' => 'Booking Bank',
	'reservation_paiement_reference' => 'Payment reference: @reference@',

	// T
	'texte_statut_attente_part' => ' partially paid- in waiting list',
	'texte_statut_attente_paye' => ' paid - in waiting list',
	'titre_page_configurer_reservation_bank' => 'Settings Booking Bank',
	'titre_paiement_reservation' => 'Paiment of the Booking',
	'titre_paiement_vendeur' => 'Payment:',
	'titre_payer_reservation' => 'Pay the booking'
);
