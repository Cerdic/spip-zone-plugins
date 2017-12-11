<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de 
// ** ne pas modifier le fichier **

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// A
	'affichage_par' => 'Affichage par :', # NEW
	'afficher_inscription_agenda_explication' => 'Inscriptions via le formulaire du plugin agenda.', # NEW
	'ajouter_lien_reservation' => 'Ajouter cette réservation', # NEW

	// B
	'bonjour' => 'Beste',

	// C
	'choix_precis' => 'Choix précis', # NEW
	'complet' => 'Complet', # NEW
	'cron_explication' => 'Pour tout événement ayant activé la clôture automatique. Quand l’événement de la résérvation est passé, la réservation sera automatiquement clôturée par le système. Si "clôturé" est choisis sous "Déclenchement", un email de clôture sera alors envoyé. Videz le cache afin que cela soit bien activé. ', # NEW
	'cron_fieldset' => 'Clôture automatique', # NEW
	'cron_label' => 'Clôturer automatiquement une réservation', # NEW

	// D
	'designation' => 'Evenement',
	'details_reservation' => 'Details van uw inschrijving :',
	'duree_vie_explication' => 'Saisir la durée de vie (en heures) d’une commande avec le statut "@statut_defaut@". Si aucune valeur ou valeur 0 - la durée de vie est illimitée.', # NEW
	'duree_vie_label' => 'Durée de vie :', # NEW

	// E
	'erreur_email_utilise' => 'Cette adresse email est déjà utilisé, veuillez vous connecter ou utliser une autre adresse', # NEW
	'erreur_pas_evenement' => 'Il n’y a actuellement pas d’événement ouvert à l’inscription.', # NEW
	'evenement_cloture' => 'Évenement clôturé', # NEW
	'evenement_ferme_inscription' => 'Cet événement est actuellement fermé à l’inscription. <br/> Appuyez sur le bouton inscription pour visualiser l’offre actuelle.', # NEW
	'explication_client' => 'Choissisez un client parmis les auteurs ou saisissez les données du client ci-bas', # NEW
	'explication_email_reutilisable' => 'Permettre de réutiliser un email d’un auteur spip lors d’une réservation sans enregistrement', # NEW
	'explication_enregistrement_inscrit' => 'L’enregistrer en tant que auteur spip', # NEW
	'explication_envoi_differe' => 'Le changement de statut d’un Détail de Réservation vers 
    <div><b>"@statuts@"</b></div> provoquera l’envoi d’une notification !', # attic
	'explication_envoi_differe_detail' => 'Le changement de statut vers <div><strong>"@statuts@"</strong></div> provoquera l’envoi d’une notification !', # attic
	'explication_envoi_separe' => 'Le changement de statut d’un Détail de Réservation vers
		<div><b>"@statuts@"</b></div> provoquera l’envoi d’une notification !', # NEW
	'explication_envoi_separe_detail' => 'Le changement de statut vers <div><strong>"@statuts@"</strong></div> provoquera l’envoi d’une notification !', # NEW
	'explication_login' => '<a rel="nofollow" class="login_modal" href="@url@" title="@titre_login@">Meld u aan</a> indien u reeds geregistreerd bent op deze website.',
	'explication_nombre_evenements' => 'Le nombre nécessaire d’événements réservés  pour que la promotion s’applique.', # NEW
	'explication_nombre_evenements_choix' => 'Si rien ou 0, ce nombre sera égal au nombre d’@objet_promotion@s choisis ci-haut', # NEW
	'explication_objet_promotion' => 'Si définit au niveau article, seront compris tous les évenéments disponible por la réservation de cet article.', # NEW

	// F
	'formulaire_public' => 'Formulaire public', # NEW

	// I
	'icone_cacher' => 'Cacher', # NEW
	'icone_creer_reservation' => 'Créer une réservation', # NEW
	'icone_modifier_client' => 'Modifier ce client', # NEW
	'icone_modifier_reservation' => 'Modifier cette réservation', # NEW
	'info_1_client' => 'Un client', # NEW
	'info_1_reservation' => 'Une réservation', # NEW
	'info_1_reservation_liee' => 'Une réservation liée', # NEW
	'info_aucun_client' => 'Aucun client', # NEW
	'info_aucun_reservation' => 'Aucune réservation', # NEW
	'info_nb_clients' => '@nb@ clients', # NEW
	'info_nb_reservations' => '@nb@ réservations', # NEW
	'info_nb_reservations_liees' => '@nb@ réservations liées', # NEW
	'info_reservations_auteur' => 'Les réservations de cet auteur', # NEW
	'info_voir_reservations_poubelle' => 'Voir les Résevations mises à la poubelle', # NEW
	'inscription' => 'inschrijving',
	'inscrire' => 'S’inscrire', # NEW
	'inscrire_liste_attente' => 'Choissisez un autre événement ou inscrivez vous dans la liste d’attente.', # NEW

	// L
	'label_action_cloture' => 'Clôture automatique :', # NEW
	'label_afficher_inscription_agenda' => 'Afficher les résultats d’inscription d’agenda', # NEW
	'label_client' => 'Client :', # NEW
	'label_date' => 'Date :', # NEW
	'label_date_paiement' => 'Date de paiement :', # NEW
	'label_donnees_auteur' => 'Données Auteur :', # NEW
	'label_duree_vie_poubelle' => 'Durée de vie d’une réservation mise à la poubelle', # NEW
	'label_effacer_poubelles_auto' => 'Effacer automatiquement les réservations mises à la poubelle', # NEW
	'label_email' => 'Email :',
	'label_email_reutilisable' => 'Permettre de réutiliser une adresse email :', # NEW
	'label_enregistrement_inscrit' => 'Permettre au visiteur de s’enregistrer lors d’une réservation :', # NEW
	'label_enregistrement_inscrit_obligatoire' => 'Rendre l’enregistrement obligatoire :', # NEW
	'label_enregistrer' => 'Je veux m’enregistrer sur ce site :', # NEW
	'label_id_auteur' => 'Id auteur :', # NEW
	'label_inscription' => 'inschrijving:',
	'label_lang' => 'Langue :', # NEW
	'label_maj' => 'maj :', # NEW
	'label_modifier_identifiants_personnels' => 'Wijzig uw persoonlijke inloggegevens:',
	'label_mot_passe' => 'Mot de passe :', # NEW
	'label_mot_passe2' => 'Répétez le mot de passe :', # NEW
	'label_nom' => 'Nom :', # NEW
	'label_nombre_evenements' => 'Nombre de coincidences :', # NEW
	'label_obets_choix' => 'Articles ou événements disponibles pour la promotion :', # NEW
	'label_objet_article' => 'Choissisez les articles dont les événements seront disponibles pour la promotion :', # NEW
	'label_objet_evenement' => 'Choissisez les les événements disponibles pour la promotion :', # NEW
	'label_objet_promotion' => 'Définir sur quel niveau on applique la promotion :', # NEW
	'label_objets_configuration' => 'Les panneaux disponibles :', # NEW
	'label_objets_navigation' => 'Les éléments disponibles :', # NEW
	'label_reference' => 'Référence :', # NEW
	'label_reservation' => 'Boeking:',
	'label_selection_objets_configuration' => 'Sélectionner les panneaux de configuration à intégrer', # NEW
	'label_selection_objets_navigation' => 'Sélectionner les éléments additionnels du menu de navigation', # NEW
	'label_statut' => 'Statuut:',
	'label_statut_calculer_auto' => 'Calculer automatiquement le statut accepté de la réservation :', # NEW
	'label_statut_calculer_auto_explication' => 'Lors d’un changement de statut vers accepté, vérifier si tous les détails de réservation ont le statut accepté, sinon le statut accepté partiellement sera retenu pour la réservation.', # NEW
	'label_statut_defaut' => 'Statut par défaut :', # NEW
	'label_statuts_complet' => 'Le(s) Statut(s) complet(s) :', # NEW
	'label_type_paiement' => 'Type de paiemement :', # NEW
	'label_type_selection' => 'Type de sélection :', # NEW
	'legend_donnees_auteur' => 'Les données du client', # NEW
	'legend_donnees_reservation' => 'Les données de la réservation', # NEW
	'legend_infos_generales' => 'Algemene informatie inschrijving',

	// M
	'merci_de_votre_reservation' => 'Wij hebben uw inschrijving goed ontvangen en bedanken u voor uw interesse.',
	'message_erreur' => 'Votre saisie contient des erreurs !', # NEW
	'message_evenement_cloture' => 'L’évènement @titre@ vient de se terminer. <br />Nous aimerions vous remercier pour votre participation.', # NEW
	'message_evenement_cloture_vendeur' => 'L’évènement @titre@ vient de se terminer. <br />Le système vient d’envoyer un message de cloture à @client@ - @email@.', # NEW
	'montant' => 'Montant', # NEW

	// N
	'nom_reservation_multiples_evenements' => 'Réservation de plusieurs événements', # NEW
	'notifications_activer_explication' => 'Envoyer par mail des notifications de réservation ?', # NEW
	'notifications_activer_label' => 'Activer', # NEW
	'notifications_cfg_titre' => 'Notifications', # NEW
	'notifications_client_explication' => 'Envoyer les notifications au client ?', # NEW
	'notifications_client_label' => 'Client', # NEW
	'notifications_destinataire_explication' => 'Choisir le(s) destinataire(s) des notifications', # NEW
	'notifications_destinataire_label' => 'Destinataire', # NEW
	'notifications_destinateur_label' => 'Destinateur', # attic
	'notifications_destinateur_label_explication' => 'Choisir le(s) destinataire(s) des notifications', # attic
	'notifications_envoi_differe' => 'Activer l’envoi différé pour le statut :', # attic
	'notifications_envoi_differe_explication' => 'Permet de déclencher l’envoi des notifications pour chaque Détail de Résérvation séparément', # attic
	'notifications_envoi_separe' => 'Activer le mode Envoi Séparé pour le statut :', # NEW
	'notifications_envoi_separe_explication' => 'Permet de déclencher l’envoi des notifications pour chaque Détail de Résérvation séparément', # NEW
	'notifications_expediteur_administrateur_label' => 'Choisir un administrateur :', # NEW
	'notifications_expediteur_choix_administrateur' => 'un administrateur', # NEW
	'notifications_expediteur_choix_email' => 'un email', # NEW
	'notifications_expediteur_choix_facteur' => 'idem plugin Facteur', # NEW
	'notifications_expediteur_choix_webmaster' => 'un webmestre', # NEW
	'notifications_expediteur_email_label' => 'Saisir un email :', # NEW
	'notifications_expediteur_explication' => 'Choisir l’expéditeur des notifications pour le vendeur et l’acheteur', # NEW
	'notifications_expediteur_label' => 'Expéditeur', # NEW
	'notifications_expediteur_webmaster_label' => 'Choisir un webmestre :', # NEW
	'notifications_explication' => 'Les notifications permettent d’envoyer des emails suite aux changements de statut des réservations : Liste d’attente, accepté, refusé, à la poubelle', # NEW
	'notifications_parametres' => 'Paramètres des notifications', # NEW
	'notifications_quand_explication' => 'Quel(s) changement(s) de statut déclenche(nt) l’envoi d’une notification ?', # NEW
	'notifications_quand_label' => 'Déclenchement', # NEW
	'notifications_vendeur_administrateur_label' => 'Choisir un ou plusieurs administrateurs :', # NEW
	'notifications_vendeur_choix_administrateur' => 'un ou des administrateurs', # NEW
	'notifications_vendeur_choix_email' => 'un ou des emails', # NEW
	'notifications_vendeur_choix_webmaster' => 'un ou des webmestres', # NEW
	'notifications_vendeur_email_explication' => 'Saisir un ou plusieurs email séparés par des virgules :', # NEW
	'notifications_vendeur_email_label' => 'Email(s) :', # NEW
	'notifications_vendeur_explication' => 'Choisir le(s) destinataire(s) des notifications pour les envois au vendeur', # attic
	'notifications_vendeur_label' => 'Vendeur', # NEW
	'notifications_vendeur_webmaster_label' => 'Choisir un ou plusieurs webmestres :', # NEW

	// P
	'par_articles' => 'articles', # NEW
	'par_evenements' => 'événements', # NEW
	'par_reservations' => 'réservations', # NEW
	'periodicite_cron_explication' => 'Periode aprês laquelle le système vérifie si des résérvations doivent être clôturées (min 600 : 10 min)', # NEW
	'periodicite_cron_label' => 'Périodicité du cron en secondes', # NEW
	'places_disponibles' => 'Beschikbare plaatsen :',

	// R
	'reaservation_montant' => 'Montant', # attic
	'reaservation_nouvelle' => 'Nouvelle réservation', # attic
	'recapitulatif' => 'Overzicht van uw inschrijving:',
	'remerciement' => 'Wij danken u voor uw inschrijving.<br>Met vriendelijke groet,',
	'reservation_client' => 'Client', # NEW
	'reservation_date' => 'Datum :',
	'reservation_de' => 'Réservation de', # NEW
	'reservation_enregistre' => 'Votre inscription a bien été enregistrée. Vous recevrez un email de confirmation. Si aucun mail ne vous est parvenu, vérifiez dans votre dossier spam.', # NEW
	'reservation_numero' => 'Réservation :', # NEW
	'reservation_reference_numero' => 'Référence n° ', # NEW
	'rubrique_reservation_explication' => 'Permet de restreindre l’application de ce plugin au/x zone/s définie/s', # NEW
	'rubrique_reservation_label' => 'Définir une/des zones pour l’application de ce plugin', # NEW

	// S
	'selection_objets_configuration_explication' => 'si desactivé, tous les panneaux sont intégrés', # NEW
	'simple' => 'Simple', # NEW
	'statuts_complet_explication' => 'Les statuts du détails de résérvation pris en compte pour déterminer si l’événement est complet.', # NEW
	'sujet_une_reservation_accepte' => 'Réservation confirmé sur @nom@', # NEW
	'sujet_une_reservation_accepte_part' => 'Réservation partiellement confirmé sur @nom@', # NEW
	'sujet_une_reservation_cloture' => 'Évènement clôturé sur @nom@', # NEW
	'sujet_votre_reservation_accepte' => '@nom@ : bevestiging van uw inschrijving',
	'sujet_votre_reservation_accepte_part' => '@nom@ : confirmation partielle de votre réservation', # NEW
	'sujet_votre_reservation_cloture' => '@nom@ : clôture de l’évènement', # NEW

	// T
	'texte_ajouter_reservation' => 'Ajouter une réservation', # NEW
	'texte_changer_statut_reservation' => 'Cette réservation est :', # NEW
	'texte_exporter' => 'exporter', # NEW
	'texte_statut_accepte' => 'aanvaard',
	'texte_statut_accepte_part' => ' accepté partiellement', # NEW
	'texte_statut_attente' => ' dans liste d’attente', # NEW
	'texte_statut_attente_paiement' => ' en attente du paiement', # NEW
	'texte_statut_cloture' => ' clôturé', # NEW
	'texte_statut_encours' => ' en cours', # NEW
	'texte_statut_poubelle' => ' à la poubelle', # NEW
	'texte_statut_refuse' => ' refusé', # NEW
	'texte_voir' => 'voir', # NEW
	'titre_client' => 'Client', # NEW
	'titre_clients' => 'Clients', # NEW
	'titre_envoi_differe' => 'Mode différe active', # attic
	'titre_envoi_separe' => 'Mode Envoi Séparé activé', # NEW
	'titre_reservation' => 'Réservation', # NEW
	'titre_reservations' => 'Réservations', # NEW
	'total' => 'Total', # NEW
	'type_lien' => 'Lié avec la réservation @reference@', # NEW

	// U
	'une_reservation_de' => 'Une réservation de : ', # NEW
	'une_reservation_sur' => 'Une réservation sur @nom@', # NEW

	// V
	'votre_reservation_sur' => '@nom@ : votre réservation' # NEW
);

?>