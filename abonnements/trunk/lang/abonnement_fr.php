<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// Fichier source, a modifier dans svn://zone.spip.org/spip-zone/_plugins_/abonnements/trunk/lang
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// A
	'ajouter_lien_abonnement' => 'Ajouter cet abonnement',

	// C
	'champ_date_au_label' => 'Au',
	'champ_date_debut_label' => 'Début de l’abonnement',
	'champ_date_du_label' => 'Du',
	'champ_date_echeance_label' => 'Prochaine échéance',
	'champ_date_fin_allonger_label' => 'Vous pouvez modifier la date de fin',
	'champ_date_fin_label' => 'Fin de l‘abonnement',
	'champ_dates_debut_label' => 'Débuts des abonnements',
	'champ_dates_fin_label' => 'Fin des abonnements',
	'champ_id_abonnements_offre_label' => 'Offre d’abonnement',
	'champ_id_auteur_label' => 'Utilisateur',
	'champ_notifier_statut_label' => 'Status des abonnements',

	// E
	'erreur_id_abonnements_offre' => 'Vous devez créer un abonnement pour une offre existante.',

	// I
	'icone_creer_abonnement' => 'Créer un abonnement',
	'icone_modifier_abonnement' => 'Modifier cet abonnement',
	'icone_renouveler_abonnement' => 'Renouveler cet abonnement',
	'info_1_abonnement' => 'Un abonnement',
	'info_1_abonnement_actif' => 'Un abonnement actif',
	'info_1_abonnement_inactif' => 'Un abonnement inactif',
	'info_1_abonnement_notifier' => 'Un abonnement à notifier',
	'info_abonnements_auteur' => 'Les abonnements de cet auteur',
	'info_aucun_abonnement' => 'Aucun abonnement',
	'info_aucun_abonnement_actif' => 'Aucun abonnement actif',
	'info_aucun_abonnement_inactif' => 'Aucun abonnement inactif',
	'info_aucun_abonnement_notifier' => 'Aucun abonnement à notifier',
	'info_nb_abonnements' => '@nb@ abonnements',
	'info_nb_abonnements_actifs' => '@nb@ abonnements actifs',
	'info_nb_abonnements_inactifs' => '@nb@ abonnements inactifs',
	'info_nb_abonnements_notifier' => '@nb@ abonnements à notifier',
	'info_numero_abbr' => 'n°',
	'info_numero_abbr_maj' => 'N°',

	// J
	'job_desactivation' => 'Désactivation de l’abonnement @id@',

	// N
	'notification_echeance_chapo' => '<p>Bonjour @nom@,</p>',
	'notification_echeance_corps' => '<p>Bonjour @nom@,</p>
		<p>Vous recevez ce courriel car vous avez un abonnement au site @nom_site_spip@ avec l’offre "@offre@".</p>
		<p>Cet abonnement arrive à échéance dans : <strong>@echeance@</strong>.<br/>
		Nous vous invitons donc à le renouveler avant son expiration.</p>
		<p>Merci de votre confiance, et n’hésitez pas à nous contacter pour toute information complémentaire.</p>',
	'notification_echeance_corps_apres' => '<p>Vous recevez ce courriel car vous aviez un abonnement au site @nom_site_spip@ avec l’offre « @offre@ ».</p>
	<p>Cet abonnement est arrivé à échéance il y a : <strong>@echeance@</strong>.<br/>
	Nous vous invitons donc à le renouveler.</p>',
	'notification_echeance_corps_avant' => '<p>Vous recevez ce courriel car vous avez un abonnement au site @nom_site_spip@ avec l’offre « @offre@ ».</p>
	<p>Cet abonnement arrive à échéance dans : <strong>@echeance@</strong>.<br/>
	Nous vous invitons donc à le renouveler avant son expiration.</p>',
	'notification_echeance_corps_pendant' => '<p>Vous recevez ce courriel car vous êtes abonné.e au site @nom_site_spip@ avec l’offre « @offre@ ».</p>
	<p>Cet abonnement arrive à échéance aujourd’hui.<br/>
	Nous vous invitons donc à le renouveler avant son expiration.</p>',
	'notification_echeance_signature' => '<p>Merci de votre confiance, et n’hésitez pas à nous contacter pour toute information complémentaire.</p>',
	'notification_echeance_sujet_jours_apres' => 'Votre abonnement est terminé depuis @duree@ jour(s) !',
	'notification_echeance_sujet_jours_avant' => 'Votre abonnement se termine dans @duree@ jour(s) !',
	'notification_echeance_sujet_jours_pendant' => 'Votre abonnement se termine aujourd’hui !',
	'notification_echeance_sujet_mois_apres' => 'Votre abonnement est terminé depuis @duree@ mois !',
	'notification_echeance_sujet_mois_avant' => 'Votre abonnement se termine dans @duree@ mois !',
	'notification_echeance_sujet_mois_pendant' => 'Votre abonnement se termine ce mois-ci !',

	// R
	'retirer_lien_abonnement' => 'Retirer cet abonnement',
	'retirer_tous_liens_abonnements' => 'Retirer tous les abonnements',

	// S
	'statut_actif' => 'actif',
	'statut_actifs' => 'actifs',
	'statut_inactif' => 'désactivé',
	'statut_inactifs' => 'désactivés',
	'statut_tous' => 'tous',

	// T
	'texte_ajouter_abonnement' => 'Ajouter un abonnement',
	'texte_changer_statut_abonnement' => 'Cet abonnement est :',
	'texte_creer_associer_abonnement' => 'Créer et associer un abonnement',
	'titre_abonnement' => 'Abonnement',
	'titre_abonnements' => 'Abonnements',
	'titre_abonnements_rubrique' => 'Abonnements de la rubrique',
	'titre_abonnements_suivre' => 'Suivre les abonnements',
	'titre_langue_abonnement' => 'Langue de cet abonnement',
	'titre_logo_abonnement' => 'Logo de cet abonnement'
);
