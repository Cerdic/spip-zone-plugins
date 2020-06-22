<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de https://trad.spip.net/tradlang_module/abonnement-abonnements?lang_cible=en
// ** ne pas modifier le fichier **

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// A
	'ajouter_lien_abonnement' => 'Add a subscription',

	// C
	'champ_date_au_label' => 'To',
	'champ_date_debut_label' => 'Subscription start',
	'champ_date_du_label' => 'From',
	'champ_date_echeance_label' => 'Next deadline',
	'champ_date_fin_allonger_label' => 'You can change the end date',
	'champ_date_fin_label' => 'Subscription end date',
	'champ_id_abonnements_offre_label' => 'Subscription offer',
	'champ_id_auteur_label' => 'User',

	// E
	'erreur_id_abonnements_offre' => 'You must create a subscription for an existing offer.',

	// I
	'icone_creer_abonnement' => 'Create a subscription',
	'icone_modifier_abonnement' => 'Edit this subscription',
	'icone_renouveler_abonnement' => 'Renew this subscription',
	'info_1_abonnement' => 'One subscription', # MODIF
	'info_1_abonnement_actif' => 'One active subscription', # MODIF
	'info_1_abonnement_inactif' => 'One inactive subscription', # MODIF
	'info_abonnements_auteur' => 'Subscriptions of this user',
	'info_aucun_abonnement' => 'No subscription',
	'info_aucun_abonnement_actif' => 'No active subscription',
	'info_aucun_abonnement_inactif' => 'No inactive subscription',
	'info_date_fin' => 'fin le @date@', # MODIF
	'info_nb_abonnements' => '@nb@ subscriptions',
	'info_nb_abonnements_actifs' => '@nb@ active subscriptions',
	'info_nb_abonnements_inactifs' => '@nb@ inactive subscriptions',
	'info_numero_abbr' => '#',
	'info_numero_abbr_maj' => '#',

	// J
	'job_desactivation' => 'Deactivation of the subscription @id@',

	// L
	'label_date_a_partir' => 'Starting from', # MODIF
	'label_date_depuis' => 'Since', # MODIF
	'label_dates' => 'Dates', # MODIF
	'label_duree' => 'Duration', # MODIF
	'label_montant' => 'Amount', # MODIF
	'label_statut' => 'Status', # MODIF

	// N
	'notification_echeance_chapo' => '<p>Hello @nom@,</p>',
	'notification_echeance_corps' => '<p>Bonjour @nom@,</p>
		<p>You are receiving this email because you are subscribed to the site @nom_site_spip@ with the offer "@offre@".</p>
		<p>Your subscription expires in: <strong>@echeance@</strong>.<br/>
		We invite you to renew it before it expires.</p>
		<p>Thank you for your trust, and do not hesitate to contact us for any further information.</p>', # MODIF
	'notification_echeance_corps_apres' => '<p>You are receiving this email because you were subscribed to the site @nom_site_spip@ with the offer "@offre@".</p>
	<p>Your subscription has expired since: <strong>@echeance@</strong>.<br/>
	 We invite you to renew it.</p>', # MODIF
	'notification_echeance_corps_avant' => '<p>You are receiving this email because you are subscribed to the site @nom_site_spip@ with the offer "@offre@".</p>
	<p>Your subscription expires in: <strong>@echeance@</strong>.<br/>
	 We invite you to renew it before it expires.</p>', # MODIF
	'notification_echeance_corps_pendant' => '<p>You are receiving this email because you are subscribed to the site @nom_site_spip@ with the offer "@offre@".</p>
<p>Your subscription is expiring today<br/>
We invite you to renew it.</p>', # MODIF
	'notification_echeance_signature' => '<p>Thank you for your trust, and do not hesitate to contact us for any further information.</p>',
	'notification_echeance_sujet_jours_apres' => 'Your subscription has expired since @duree@ day(s)!',
	'notification_echeance_sujet_jours_avant' => 'Your subscription ends in @duree@ day(s)!',
	'notification_echeance_sujet_jours_pendant' => 'Your subscription is expiring today!',
	'notification_echeance_sujet_mois_apres' => 'Your subscription has expired since @duree@ month(s)!',
	'notification_echeance_sujet_mois_avant' => 'Your subscription ends in @duree@ month(s)!',
	'notification_echeance_sujet_mois_pendant' => 'Your subscription is expiring this month!',

	// R
	'retirer_lien_abonnement' => 'Remove this subscription',
	'retirer_tous_liens_abonnements' => 'Remove all subscriptions',

	// S
	'statut_actif' => 'active',
	'statut_actifs' => 'active',
	'statut_inactif' => 'disabled',
	'statut_tous' => 'All',

	// T
	'texte_ajouter_abonnement' => 'Add a subscription',
	'texte_changer_statut_abonnement' => 'This subscription is:',
	'texte_creer_associer_abonnement' => 'Create and assign a subscription',
	'titre_abonnement' => 'Subscription',
	'titre_abonnements' => 'Subscriptions',
	'titre_abonnements_rubrique' => 'Subscriptions of the section',
	'titre_abonnements_suivre' => 'Follow this subscription',
	'titre_langue_abonnement' => 'Language of this subscription',
	'titre_logo_abonnement' => 'Logo of this subscription'
);
