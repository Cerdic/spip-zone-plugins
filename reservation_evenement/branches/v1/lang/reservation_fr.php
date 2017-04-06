<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// Fichier source, a modifier dans svn://zone.spip.org/spip-zone/_plugins_/reservation_evenement/trunk/lang/
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// A
	'affichage_par' => 'Affichage par :',
	'afficher_inscription_agenda_explication' => 'Inscriptions via le formulaire du plugin agenda.',
	'ajouter_lien_reservation' => 'Ajouter cette réservation',

	// B
	'bonjour' => 'Bonjour',

	// C
	'choix_precis' => 'Choix précis',
	'complet' => 'Complet',
	'cron_explication' => 'Pour tout événement ayant activé la clôture automatique. Quand l’événement de la résérvation est passé, la réservation sera automatiquement clôturée par le système. Si "clôturé" est choisis sous "Déclenchement", un email de clôture sera alors envoyé. Videz le cache afin que cela soit bien activé. ',
	'cron_fieldset' => 'Clôture automatique',
	'cron_label' => 'Clôturer automatiquement une réservation',

	// D
	'designation' => 'Désignation',
	'details_reservation' => 'Détails de la réservation :',
	'duree_vie_explication' => 'Saisir la durée de vie (en heures) d’une commande avec le statut "@statut_defaut@". Si aucune valeur ou valeur 0 - la durée de vie est illimitée.',
	'duree_vie_label' => 'Durée de vie :',

	// E
	'erreur_email_utilise' => 'Cette adresse email est déjà utilisé, veuillez vous connecter ou utliser une autre adresse',
	'erreur_pas_evenement' => 'Il n’y a actuellement pas d’événement ouvert à l’inscription.',
	'evenement_cloture' => 'Évenement clôturé',
	'evenement_ferme_inscription' => 'Cet événement est actuellement fermé à l’inscription. <br/> Appuyez sur le bouton inscription pour visualiser l’offre actuelle.',
	'explication_client' => 'Choissisez un client parmis les auteurs ou saisissez les données du client ci-bas',
	'explication_email_reutilisable' => 'Permettre de réutiliser un email d’un auteur spip lors d’une réservation sans enregistrement',
	'explication_enregistrement_inscrit' => 'L’enregistrer en tant que auteur spip',
	'explication_envoi_separe' => 'Le changement de statut d’un Détail de Réservation vers
		<div><b>"@statuts@"</b></div> provoquera l’envoi d’une notification !',
	'explication_envoi_separe_detail' => 'Le changement de statut vers <div><strong>"@statuts@"</strong></div> provoquera l’envoi d’une notification !',
	'explication_login' => '<a rel="nofollow" class="login_modal" href="@url@" title="@titre_login@">Connectez-vous</a> si vous êtes déjà enregistré sur ce site',
	'explication_nombre_evenements' => 'Le nombre nécessaire d’événements réservés  pour que la promotion s’applique.',
	'explication_nombre_evenements_choix' => 'Si rien ou 0, ce nombre sera égal au nombre d’@objet_promotion@s choisis ci-haut',
	'explication_objet_promotion' => 'Si définit au niveau article, seront compris tous les évenéments disponible por la réservation de cet article.',

	// F
	'formulaire_public' => 'Formulaire public',

	// I
	'icone_cacher' => 'Cacher',
	'icone_creer_reservation' => 'Créer une réservation',
	'icone_modifier_client' => 'Modifier ce client',
	'icone_modifier_reservation' => 'Modifier cette réservation',
	'info_1_client' => 'Un client',
	'info_1_reservation' => 'Une réservation',
	'info_1_reservation_liee' => 'Une réservation liée',
	'info_aucun_client' => 'Aucun client',
	'info_aucun_reservation' => 'Aucune réservation',
	'info_nb_clients' => '@nb@ clients',
	'info_nb_reservations' => '@nb@ réservations',
	'info_nb_reservations_liees' => '@nb@ réservations liées',
	'info_reservations_auteur' => 'Les réservations de cet auteur',
	'info_voir_reservations_poubelle' => 'Voir les Résevations mises à la poubelle',
	'inscription' => 'Inscription',
	'inscrire' => 'S’inscrire',
	'inscrire_liste_attente' => 'Choissisez un autre événement ou inscrivez vous dans la liste d’attente.',

	// L
	'label_action_cloture' => 'Clôture automatique :',
	'label_afficher_inscription_agenda' => 'Afficher les résultats d’inscription d’agenda',
	'label_client' => 'Client :',
	'label_date' => 'Date :',
	'label_date_paiement' => 'Date de paiement :',
	'label_donnees_auteur' => 'Données Auteur :',
	'label_email' => 'Email :',
	'label_email_reutilisable' => 'Permettre de réutiliser une adresse email :',
	'label_enregistrement_inscrit' => 'Permettre au visiteur de s’enregistrer lors d’une réservation :',
	'label_enregistrement_inscrit_obligatoire' => 'Rendre l’enregistrement obligatoire :',
	'label_enregistrer' => 'Je veux m’enregistrer sur ce site :',
	'label_id_auteur' => 'Id auteur :',
	'label_inscription' => 'inscription :',
	'label_lang' => 'Langue :',
	'label_maj' => 'maj :',
	'label_modifier_identifiants_personnels' => 'Modifier les identifiants personnels :',
	'label_mot_passe' => 'Mot de passe :',
	'label_mot_passe2' => 'Répétez le mot de passe :',
	'label_nom' => 'Nom :',
	'label_nombre_evenements' => 'Nombre de coincidences :',
	'label_objet_article' => 'Choissisez les articles dont les événements seront disponibles pour la promotion :',
	'label_objet_evenement' => 'Choissisez les les événements disponibles pour la promotion :',
	'label_objet_promotion' => 'Définir sur quel niveau on applique la promotion :',
	'label_obets_choix' => 'Articles ou événements disponibles pour la promotion :',
	'label_reference' => 'Référence :',
	'label_reservation' => 'Réservation :',
	'label_statut' => 'Statut :',
	'label_statut_calculer_auto' => 'Calculer automatiquement le statut accepté de la réservation :',
	'label_statut_calculer_auto_explication' => 'Lors d’un changement de statut vers accepté, vérifier si tous les détails de réservation ont le statut accepté, sinon le statut accepté partiellement sera retenu pour la réservation.',
	'label_statut_defaut' => 'Statut par défaut :',
	'label_statuts_complet' => 'Le(s) Statut(s) complet(s) :',
	'label_type_paiement' => 'Type de paiemement :',
	'label_type_selection' => 'Type de sélection :',
	'legend_donnees_auteur' => 'Les données du client',
	'legend_donnees_reservation' => 'Les données de la réservation',

	// M
	'merci_de_votre_reservation' => 'Nous avons bien enregistré votre réservation et nous vous remercions de votre confiance.',
	'message_erreur' => 'Votre saisie contient des erreurs !',
	'message_evenement_cloture' => 'L’évènement @titre@ vient de se terminer. <br />Nous aimerions vous remercier pour votre participation.',
	'message_evenement_cloture_vendeur' => 'L’évènement @titre@ vient de se terminer. <br />Le système vient d’envoyer un message de cloture à @client@ - @email@.',
	'montant' => 'Montant',

	// N
	'nom_reservation_multiples_evenements' => 'Réservation de plusieurs événements',
	'notifications_activer_explication' => 'Envoyer par mail des notifications de réservation ?',
	'notifications_activer_label' => 'Activer',
	'notifications_cfg_titre' => 'Notifications',
	'notifications_client_explication' => 'Envoyer les notifications au client ?',
	'notifications_client_label' => 'Client',
	'notifications_destinataire_explication' => 'Choisir le(s) destinataire(s) des notifications',
	'notifications_destinataire_label' => 'Destinataire',
	'notifications_envoi_separe' => 'Activer le mode Envoi Séparé pour le statut :',
	'notifications_envoi_separe_explication' => 'Permet de déclencher l’envoi des notifications pour chaque Détail de Résérvation séparément',
	'notifications_expediteur_administrateur_label' => 'Choisir un administrateur :',
	'notifications_expediteur_choix_administrateur' => 'un administrateur',
	'notifications_expediteur_choix_email' => 'un email',
	'notifications_expediteur_choix_facteur' => 'idem plugin Facteur',
	'notifications_expediteur_choix_webmaster' => 'un webmestre',
	'notifications_expediteur_email_label' => 'Saisir un email :',
	'notifications_expediteur_explication' => 'Choisir l’expéditeur des notifications pour le vendeur et l’acheteur',
	'notifications_expediteur_label' => 'Expéditeur',
	'notifications_expediteur_webmaster_label' => 'Choisir un webmestre :',
	'notifications_explication' => 'Les notifications permettent d’envoyer des emails suite aux changements de statut des réservations : Liste d’attente, accepté, refusé, à la poubelle',
	'notifications_parametres' => 'Paramètres des notifications',
	'notifications_quand_explication' => 'Quel(s) changement(s) de statut déclenche(nt) l’envoi d’une notification ?',
	'notifications_quand_label' => 'Déclenchement',
	'notifications_vendeur_administrateur_label' => 'Choisir un ou plusieurs administrateurs :',
	'notifications_vendeur_choix_administrateur' => 'un ou des administrateurs',
	'notifications_vendeur_choix_email' => 'un ou des emails',
	'notifications_vendeur_choix_webmaster' => 'un ou des webmestres',
	'notifications_vendeur_email_explication' => 'Saisir un ou plusieurs email séparés par des virgules :',
	'notifications_vendeur_email_label' => 'Email(s) :',
	'notifications_vendeur_label' => 'Vendeur',
	'notifications_vendeur_webmaster_label' => 'Choisir un ou plusieurs webmestres :',

	// P
	'par_articles' => 'articles',
	'par_evenements' => 'événements',
	'par_reservations' => 'réservations',
	'periodicite_cron_explication' => 'Periode aprês laquelle le système vérifie si des résérvations doivent être clôturées (min 600 : 10 min)',
	'periodicite_cron_label' => 'Périodicité du cron en secondes',
	'places_disponibles' => 'Places disponibles :',

	// R
	'recapitulatif' => 'Récapitulatif de la réservation :',
	'remerciement' => 'Nous vous remercions pour votre inscription<br/>Cordialement',
	'reservation_client' => 'Client',
	'reservation_date' => 'Date :',
	'reservation_de' => 'Réservation de',
	'reservation_enregistre' => 'Votre inscription a bien été enregistrée. Vous recevrez un email de confirmation. Si aucun mail ne vous est parvenu, vérifiez dans votre dossier spam.',
	'reservation_numero' => 'Réservation :',
	'reservation_reference_numero' => 'Référence n° ',
	'rubrique_reservation_explication' => 'Permet de restreindre l’application de ce plugin au/x zone/s définie/s',
	'rubrique_reservation_label' => 'Définir une/des zones pour l’application de ce plugin',

	// S
	'simple' => 'Simple',
	'statuts_complet_explication' => 'Les statuts du détails de résérvation pris en compte pour déterminer si l’événement est complet.',
	'sujet_une_reservation_accepte' => 'Réservation confirmé sur @nom@',
	'sujet_une_reservation_accepte_part' => 'Réservation partiellement confirmé sur @nom@',
	'sujet_une_reservation_cloture' => 'Évènement clôturé sur @nom@',
	'sujet_votre_reservation_accepte' => '@nom@ : confirmation de votre réservation',
	'sujet_votre_reservation_accepte_part' => '@nom@ : confirmation partielle de votre réservation',
	'sujet_votre_reservation_cloture' => '@nom@ : clôture de l’évènement',

	// T
	'texte_ajouter_reservation' => 'Ajouter une réservation',
	'texte_changer_statut_reservation' => 'Cette réservation est :',
	'texte_exporter' => 'exporter',
	'texte_statut_accepte' => ' accepté',
	'texte_statut_accepte_part' => ' accepté partiellement',
	'texte_statut_attente' => ' dans liste d’attente',
	'texte_statut_attente_paiement' => ' en attente du paiement',
	'texte_statut_cloture' => ' clôturé',
	'texte_statut_encours' => ' en cours',
	'texte_statut_poubelle' => ' à la poubelle',
	'texte_statut_refuse' => ' refusé',
	'texte_voir' => 'voir',
	'titre_client' => 'Client',
	'titre_clients' => 'Clients',
	'titre_envoi_separe' => 'Mode Envoi Séparé activé',
	'titre_reservation' => 'Réservation',
	'titre_reservations' => 'Réservations',
	'total' => 'Total',
	'type_lien' => 'Lié avec la réservation @reference@',

	// U
	'une_reservation_de' => 'Une réservation de : ',
	'une_reservation_sur' => 'Une réservation sur @nom@',

	// V
	'votre_reservation_sur' => '@nom@ : votre réservation'
);
