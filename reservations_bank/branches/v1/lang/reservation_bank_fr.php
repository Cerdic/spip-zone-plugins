<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// Fichier source, a modifier dans svn://zone.spip.org/spip-zone/_plugins_/reservations_bank/trunk/lang/
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// C
	'cfg_titre_espace_prive' => 'Espace privé',

	// L
	'label_cacher_paiement_public' => 'Ne pas proposer de formulaire de paiement sur le site public',
	'label_choisir_mode_paiement' => 'Choisissez le mode de paiement :',
	'label_definir_presta_defaut' => 'Définir un mode de paiement unique pour le choix de paiement.',
	'label_fieldset_montant_detail' => 'Spécifiez le montant (en @devise@) pour chaque détail de réservation',
	'label_fieldset_specifier' => 'Spécifier',
	'label_preceder_formulaire' => 'Préceder le formulaire de paiment au récapitulatif de la commande.',
	'label_presta_defaut' => 'Prestataire à attribuer',
	'label_specifier_montant' => 'Spécifier le montant',

	// M
	'merci_de_votre_reservation_paiement' => 'Nous avons bien validé votre réservation <b>@reference@</b>.',
	'message_erreur_montant_credit' => 'Vous avez depassé la limite de votre crédit qui es de @credit@ !',
	'message_erreur_montant_reservations_detail' => 'Le montant ne doit pas être supérieure à @montant_ouvert@ (montant encore à payer)',
	'message_paiement_vendeur' => 'Mode de paiement : "@mode@",  voir <a href="@url@">détail</a>',
	'montant_paye' => 'Payé :',

	// P
	'paiement_commande' => 'Paiement de la commande #@id_commande@',
	'paiement_reservation' => 'Paiement de la réservation #@id_reservation@',

	// R
	'reservation_bank_titre' => 'Réservations Bank',
	'reservation_paiement_reference' => 'Référence du paiement : @reference@',

	// T
	'texte_statut_attente_part' => ' payé partiellement- dans liste d’attente',
	'texte_statut_attente_paye' => ' payé - dans liste d’attente',
	'titre_page_configurer_reservation_bank' => 'Configuration Réservations Bank',
	'titre_paiement_reservation' => 'Paiement de la réservation',
	'titre_paiement_vendeur' => 'Paiement :',
	'titre_payer_reservation' => 'Payez la réservation'
);
