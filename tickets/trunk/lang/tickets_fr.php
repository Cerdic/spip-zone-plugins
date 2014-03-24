<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// Fichier source, a modifier dans svn://zone.spip.org/spip-zone/_plugins_/tickets/trunk/lang/
if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// A
	'afficher_tickets' => 'Afficher les tickets',
	'ajout_deux_points' => '@texte@ : ',
	'assignation_attribuee_a' => 'Le ticket a été assigné à @nom@.',
	'assignation_attribuee_a_personne' => 'Le ticket n’a été assigné à personne.',
	'assignation_mail_titre' => 'Changement d’assignation du ticket',
	'assignation_modifiee' => 'Assignation mise à jour',
	'assignation_non_modifiee' => 'Assignation non mise à jour',
	'assignation_supprimee' => 'L’assignation de ce ticket a été supprimée.',
	'assigne_a' => 'Assigné à :',
	'assigne_a_th' => 'Assigné à',
	'assignes_a' => 'Assignés à',
	'autres_tickets_ouverts' => 'Les autres tickets ouverts',

	// C
	'cfg_bouton_radio_aucune_colonne' => 'Aucune colonne',
	'cfg_bouton_radio_desactiver_forum_public' => 'Désactiver l’utilisation des forums publics.',
	'cfg_bouton_radio_une_colonne_par_groupe' => 'Une colonne par groupe de mots',
	'cfg_bouton_radio_une_colonne_par_groupe_important' => 'Une colonne par groupe de mots important',
	'cfg_bouton_radio_une_colonne_pour_tous_les_groupes' => 'Une seule colonne contenant tous les groupes de mots',
	'cfg_bouton_tickets' => 'Tickets',
	'cfg_descr_autorisations' => 'Cette page de configuration permet de paramétrer les autorisations pour l’écriture, la modification, l’assignation et le commentaire des tickets. Installer le plugin <a href="@url@">Autorité</a> permet d’accéder à d’autres options de configuration, par exemple pour autoriser l’auteur d’un commentaire à le modifier une fois publié.',
	'cfg_descr_general' => 'Cette page de configuration permet de paraméter les notifications, la modération des commentaires, l’ajout de fichiers, etc.',
	'cfg_descr_typologie' => 'Cette page de configuration permet de contruire le système de classification de vos tickets en utilisant les mots-clés de SPIP.',
	'cfg_explication_cacher_moteurs' => 'Cache les tickets des moteurs de recherche en leur demandant de ne pas indexer leur contenus lorsqu’ils sont affichés dans l’espace public.',
	'cfg_explication_desactiver_public' => 'Seulement afficher les tickets dans l’espace privé',
	'cfg_explication_formats_documents_ticket' => 'Séparez les formats par une virgule',
	'cfg_explication_readonly' => 'Cette partie de la configuration est déjà définie autre part.',
	'cfg_form_tickets_autorisations' => 'Autorisations',
	'cfg_form_tickets_general' => 'Paramètres généraux',
	'cfg_form_tickets_typologie' => 'Classification',
	'cfg_groupe_versions' => 'Groupe de mots de versions',
	'cfg_groupe_versions_aucun' => 'Aucun',
	'cfg_groupe_versions_explication' => 'Sélectionner, s’il existe, le groupe de mots qui contient des versions pour la roadmap',
	'cfg_inf_type_autorisation' => 'Si vous choisissez par statut ou par auteur, il vous sera demandé ci-dessous votre sélection de statuts ou d’auteurs.',
	'cfg_lbl_autorisation_auteurs' => 'Autoriser par liste d’auteurs',
	'cfg_lbl_autorisation_statuts' => 'Autoriser par statut d’auteurs',
	'cfg_lbl_autorisation_webmestre' => 'Autoriser les webmestres uniquement',
	'cfg_lbl_autoriser_modifier' => 'Ceux qui peuvent modifier le ticket',
	'cfg_lbl_autoriser_modifier_case' => 'Autoriser ceux qui ont la possibilité de modifier le ticket (auteur et assigné notamment)',
	'cfg_lbl_cacher_moteurs' => 'Cacher des moteurs de recherche',
	'cfg_lbl_case_joindre_fichiers' => 'Autoriser à joindre un ou plusieurs fichiers aux tickets',
	'cfg_lbl_case_notification_publique' => 'Être notifié dans l’espace public plutôt que dans l’espace privé',
	'cfg_lbl_colonnes_groupesmots' => 'Afficher les mots-clés dans les tableaux',
	'cfg_lbl_colonnes_groupesmots_explication' => 'Choisir comment afficher les groupes de mots associables aux tickets (<strong>@groupes@</strong>) dans les listes de tickets',
	'cfg_lbl_desactiver_public' => 'Désactiver l’accès public',
	'cfg_lbl_formats_documents_ticket' => 'Formats de documents acceptés',
	'cfg_lbl_forums_publics' => 'Commentaires sur les tickets',
	'cfg_lbl_joindre_fichiers' => 'Joindre un ou des fichiers',
	'cfg_lbl_lier_mots' => 'Lier des mots-clés',
	'cfg_lbl_lier_mots_aucun' => 'aucun groupe',
	'cfg_lbl_lier_mots_explication' => 'Aller sur la page de configuration de chaque groupe de mots-clés pour permettre ou non son association avec les tickets.<br/>Les groupes qui peuvent actuellement être liés aux tickets sont les suivants : <strong>@groupes@</strong>',
	'cfg_lbl_liste_auteurs' => 'Auteurs du site',
	'cfg_lbl_notif_destinataires' => 'Destinataires de la notification de création',
	'cfg_lbl_notification_publique' => 'Notification publique',
	'cfg_lbl_selecteur_navigateur' => 'Sélecteur de navigateur',
	'cfg_lbl_statuts_auteurs' => 'Statuts possibles',
	'cfg_lbl_type_autorisation' => 'Méthode d’autorisation',
	'cfg_lgd_autorisation_assigner' => 'Assigner les tickets',
	'cfg_lgd_autorisation_assigneretre' => 'Être assigné à un ticket',
	'cfg_lgd_autorisation_commenter' => 'Commenter les tickets',
	'cfg_lgd_autorisation_ecrire' => 'Écrire les tickets',
	'cfg_lgd_autorisation_epingler' => 'Épingler les tickets',
	'cfg_lgd_autorisation_modifier' => 'Modifier les tickets',
	'cfg_lgd_champs_options_autres' => 'Autres options',
	'cfg_lgd_commentaires' => 'Commentaires',
	'cfg_lgd_fichiers' => 'Fichiers joints',
	'cfg_lgd_mots' => 'Associer des mots-clés',
	'cfg_lgd_notifications' => 'Notifications',
	'cfg_lgd_notifs_forums' => 'Forums et notifications',
	'cfg_lgd_objets' => 'Association avec d’autres objets',
	'cfg_notif_admin' => 'Tous les administrateurs du site',
	'cfg_notif_assigne' => 'Uniquement la personne assignée au ticket',
	'cfg_notif_auteur' => 'Tous les auteurs du site',
	'cfg_notif_liste' => 'Une liste d’auteurs',
	'cfg_notif_liste_auteurs' => 'Liste des auteurs recevant les notifications',
	'cfg_notif_webmestre' => 'Tous les webmestres',
	'cfg_titre_tickets' => 'Tickets - Configuration du plugin',
	'champ_assigne' => 'Assigné à :',
	'champ_assigner' => 'Assigner à :',
	'champ_composant_th' => 'Composant',
	'champ_createur' => 'Créé par :',
	'champ_date' => 'Date :',
	'champ_date_debut' => 'À partir de :',
	'champ_date_fin' => 'Jusqu’à :',
	'champ_date_modif' => 'Modifié le',
	'champ_date_th' => 'Date',
	'champ_description' => 'Description du ticket',
	'champ_exemple' => 'Exemple :',
	'champ_fichier' => 'Joindre un fichier',
	'champ_id' => 'Numéro',
	'champ_id_assigne' => 'Assigné à :',
	'champ_id_auteur' => 'Auteur :',
	'champ_jalon_th' => 'Jalon',
	'champ_maj' => 'MAJ :',
	'champ_maj_long' => 'Date de mise à jour',
	'champ_maj_th' => 'MAJ',
	'champ_mots_th' => 'Mots-clés',
	'champ_navigateur' => 'Navigateur',
	'champ_nouveau_commentaire' => 'Nouveau commentaire',
	'champ_projet_th' => 'Projet',
	'champ_recherche' => 'Recherche :',
	'champ_severite_th' => 'Sévérité',
	'champ_statut' => 'Statut :',
	'champ_statut_th' => 'Statut',
	'champ_sticked' => 'Épinglé :',
	'champ_texte' => 'Texte',
	'champ_titre' => 'Résumé',
	'champ_titre_th' => 'Résumé',
	'champ_titre_ticket' => 'Titre du ticket',
	'champ_type_th' => 'Type',
	'champ_url_exemple' => 'URL d’exemple',
	'champ_version_th' => 'Version',
	'changement_statut_mail' => 'Le statut de ce ticket a été modifié de "@ancien@" à "@nouveau@".',
	'classement_assigne' => 'Tickets par assignation',
	'classement_asuivre' => 'Les tickets à suivre',
	'classement_groupe' => 'Tickets par @groupe@',
	'classement_termine' => 'Tickets terminés',
	'classement_version' => 'Tickets par version',
	'commentaire' => 'commentaire',
	'commentaire_aucun' => 'Aucun commentaire',
	'commentaires' => 'commentaires',
	'commenter_ticket' => 'Commenter ce ticket',
	'creer_et_associer_un_ticket' => 'Créer et associer un ticket',
	'creer_ticket' => 'Créer un ticket',

	// D
	'date_creation' => 'Créé le @date@',
	'date_creation_auteur' => 'Ticket créé le <strong>@date@</strong> par <strong>@nom@</strong>',

	// E
	'erreur_date_saisie' => 'Cette date est invalide',
	'erreur_date_saisie_superieure' => 'La date maximale doit être supérieure à la date minimale',
	'erreur_texte_longueur_mini' => 'La longueur minimale du texte est de @nb@ caractères.',
	'erreur_verifier_formulaire' => 'Vérifiez votre formulaire',
	'explication_autoriser_modifier_base' => 'Automatiquement, le créateur du ticket et l’utilisateur qui y est assigné peut le modifier. La configuration suivante permet de définir les utilisateurs supplémentaires pouvant modifier les tickets.',
	'explication_champ_sticked' => 'Les tickets épinglés sont toujours affichés en premier, quelque soit leur statut.',
	'explication_description_ticket' => 'Décrivez aussi précisément que possible le besoin ou le problème rencontré.
	Indiquez en particulier s’il se produit systématiquement ou occasionnellement.
	S’il s’agit d’un problème d’affichage, précisez avec quel navigateur vous le rencontrez.',
	'explication_description_ticket_ss_nav' => 'Décrivez aussi précisément que possible le besoin ou le problème rencontré.
	Indiquez en particulier s’il se produit systématiquement ou occasionnellement.',
	'explication_fichier' => 'Ajoutez un fichier à votre ticket.',
	'explication_notif_destinataire' => 'L’auteur du ticket et la personne assignée reçoivent systématiquement la notification.',
	'explication_redaction' => 'Quand vous avez terminé la rédaction de votre ticket, sélectionnez le statut « ouvert et discuté ».',
	'explication_url_exemple' => 'Indiquez ici l’URL d’une page concernée par ce ticket.',

	// I
	'icone_modifier_ticket' => 'Modifier ce ticket',
	'icone_retour_ticket' => 'Retour au ticket',
	'info_commentaire' => 'Commentaire #@id@ :',
	'info_demande' => '@nb@ demande',
	'info_demande_fermee' => '@nb@ fermée',
	'info_demande_ouverte' => '@nb@ ouverte',
	'info_demande_resolue' => '@nb@ résolue',
	'info_demandes' => '@nb@ demandes',
	'info_demandes_fermees' => '@nb@ fermées',
	'info_demandes_ouvertes' => '@nb@ ouvertes',
	'info_demandes_resolues' => '@nb@ résolues',
	'info_document_ajoute' => 'Ajouté :',
	'info_liste_tickets' => 'Tickets',
	'info_numero_ticket' => 'TICKET NUMÉRO :',
	'info_page_configurer' => 'Tickets propose plusieurs pages de configuration afin de paramétrer les autorisations, les notifications, les commentaires, le système de classification des tickets ainsi que d’autres options.',
	'info_retirer_ticket' => 'Retirer ce ticket',
	'info_retirer_tickets' => 'Retirer tous les tickets',
	'info_sans' => 'Non défini',
	'info_sans_version' => 'Sans version',
	'info_ticket_1' => '1 ticket',
	'info_ticket_aucun' => 'Aucun ticket',
	'info_ticket_nb' => '@nb@ tickets',
	'info_tickets' => 'Tickets',
	'info_tickets_cles_association' => 'Les tickets peuvent être associés à :',
	'info_tickets_ouvert' => 'ouverts et discutés',
	'info_tickets_redac' => 'en cours de rédaction',

	// L
	'label_mots' => 'Lier des mots-clés au ticket',
	'label_paginer_par' => 'Paginer par :',
	'label_vue_liste_tickets' => 'Changer la vue de la liste :',
	'lien_ajouter_ticket' => 'Ajouter ce ticket',
	'lien_filtrer' => 'Filtrer les tickets',
	'lien_identification' => '<a href="@url@" class="spip_in">Identifiez vous.</a>',
	'lien_reponse_ticket' => 'Réponse au ticket',
	'lien_supprimer_filtres' => 'Enlever tous les filtres',
	'lien_vue_roadmap' => 'Feuille de route',
	'lien_vue_tous' => 'Liste complète',

	// M
	'mail_texte_message_auto' => 'Ceci est un message automatique : n’y repondez pas.',
	'message_aucun_ticket_recherche' => 'Aucun ticket ne correspond à votre recherche',
	'message_automatique' => 'Ceci est un message automatique : n’y repondez pas.',
	'message_page_publique_indisponible' => 'Cette page est indisponible. vérifiez que ZPIP est activé et que votre configuration du plugin Tickets autorise l’accès public.',
	'message_zpip_inactif' => 'Cette option est désactivée car elle nécessite le plugin ZPIP.',
	'mots_aucun' => 'Aucun mot-clé',

	// N
	'no_assignation' => 'Personne',
	'non_assignes' => 'Non assignés',
	'nouveau_commentaire_mail' => 'Nouveau commentaire sur ticket',
	'nouveau_ticket' => 'Nouveau ticket',

	// O
	'option_intro' => '—',
	'option_navigateur_autre' => 'Autre',
	'option_navigateur_tous' => 'Tous les navigateurs',

	// P
	'page_titre' => 'Tickets, système de suivi de bugs',

	// R
	'revenir_gestion' => 'Revenir à la gestion des tickets',

	// S
	'severite_bloquant' => 'Bloquant',
	'severite_important' => 'Important',
	'severite_normal' => 'Normal',
	'severite_peu_important' => 'Peu important',
	'sinscrire' => 'S’inscrire',
	'statut_ferme' => 'Fermé',
	'statut_ferme_long' => 'Tous les tickets fermés',
	'statut_inchange' => 'Le statut n’a pas été modifié.',
	'statut_mis_a_jour' => 'Statut mis à jour',
	'statut_ouvert' => 'Ouvert et discuté',
	'statut_poubelle' => 'À la poubelle',
	'statut_redac' => 'En cours de rédaction',
	'statut_resolu' => 'Résolu',
	'statut_resolu_long' => 'Tous les tickets résolus',
	'suivre_tickets_assignes_a' => 'Tickets assignés à @nom@',
	'suivre_tickets_comments' => 'Suivi des commentaires de tickets',
	'suivre_tickets_comments_rss' => 'Suivre ces commentaires par RSS',
	'suivre_tickets_de' => 'Les tickets de @nom@',
	'suivre_tickets_id' => 'Suivi du ticket #@id@ : @titre@',
	'suivre_tickets_rss' => 'Suivre ces tickets par RSS',
	'suivre_tickets_rss_unique' => 'Suivre ce ticket par RSS',
	'suivre_tickets_statut' => 'Les tickets ayant le ou les statuts :',
	'suivre_tickets_tous' => 'Tous les tickets',
	'syndiquer_ticket' => 'Syndiquer le ticket :',
	'syndiquer_tickets' => 'Syndiquer les tickets du site',

	// T
	'texte_ticket_statut' => 'Statut du ticket :',
	'ticket' => 'Ticket',
	'ticket_enregistre' => 'Ticket enregistré',
	'tickets' => 'Tickets',
	'tickets_autorisations' => 'Autorisations',
	'tickets_derniers_commentaires' => 'Les derniers commentaires',
	'tickets_en_cours_auteur' => 'Les tickets de @nom@ en cours de traitement',
	'tickets_general' => 'Paramètres généraux',
	'tickets_sticked' => 'Tickets épinglés',
	'tickets_sur_inscription' => 'Seules les personnes identifiées peuvent écrire des tickets ou commentaires.',
	'tickets_sur_inscription_droits' => 'Les droits dont vous disposez sont insuffisants.',
	'tickets_traites' => 'Tous les tickets traités',
	'tickets_tries' => 'Tickets correspondant à vos critères',
	'tickets_typologie' => 'Classification',
	'titre' => 'Tickets, suivi de bugs',
	'titre_ajouter_un_ticket' => 'Ajouter un ticket',
	'titre_identification' => 'Identification',
	'titre_liste' => 'Liste des tickets',
	'titre_objets_lies_ticket' => 'Liés à ce ticket :',
	'tous_tickets_en_redaction' => 'Tous les tickets en rédaction',
	'tous_tickets_ouverts' => 'Tous les tickets ouverts',
	'tous_vos_tickets' => 'Tous vos tickets',
	'type_amelioration' => 'Amélioration',
	'type_probleme' => 'Problème',
	'type_tache' => 'Tâche',

	// V
	'vos_tickets_assignes' => 'Les tickets qui vous sont assignés',
	'vos_tickets_assignes_auteur' => 'Les tickets de @nom@ qui vous sont assignés',
	'vos_tickets_en_cours' => 'Vos tickets ouverts'
);

?>
