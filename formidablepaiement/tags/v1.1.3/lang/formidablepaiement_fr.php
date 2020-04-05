<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP

if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// F
	'formidablepaiement_titre' => 'Formulaires de paiement',

	'traiter_paiement_titre' => 'Paiement',
	'traiter_paiement_description' => 'Demander un paiement après saisie du formulaire',

	'traiter_paiement_option_champ_auteur' => 'Champ pour l\'adresse email du client',
	'traiter_paiement_option_champ_montant_label' => 'Champ pour le montant à payer',
	'traiter_paiement_option_montant_fixe_label' => 'Ou montant fixe',
	'traiter_paiement_option_montant_fixe_explication' => 'Ce montant pourra aussi être utilisé comme montant par défaut si le champ pour le montant à payer est vide',
	'traiter_paiement_option_taxes_non_label' => 'TVA non applicable',
	'traiter_paiement_option_taxes_ht_label' => 'Montant Hors Taxes',
	'traiter_paiement_option_taxes_ttc_label' => 'Montant Toutes Taxes Comprises',
	'traiter_paiement_option_taxes_label' => 'TVA',
	'traiter_paiement_option_tva_label' => 'Taux de T.V.A. (%)',
	'traiter_paiement_option_message_label' => 'Message après paiement réussi',
	'traiter_paiement_necessite_explication' => 'Pour la prise en charge du paiement, il est nécessaire d\'enregistrer les résultats dans la base de données.',

	'traiter_paiement_dsp2_fieldset_legend' => 'Informations concernant le payeur',
	'traiter_paiement_dsp2_explication' => 'Si votre formulaire collecte ces informations, indiquez ci-dessous les champs correspondant pour faciliter le paiement par CB et éviter une authentification renforcée',
	'traiter_paiement_option_champ_nom' => 'Nom',
	'traiter_paiement_option_champ_prenom' => 'Prénom',
	'traiter_paiement_option_champ_adresse' => 'Adresse',
	'traiter_paiement_option_champ_code_postal' => 'Code postal',
	'traiter_paiement_option_champ_ville' => 'Ville',
	'traiter_paiement_option_champ_pays' => 'Pays',

	'titre_reglement' => 'Règlement',
	'titre_reglement_montant' => 'pour un montant de <b>@montant@</b>',
);

?>