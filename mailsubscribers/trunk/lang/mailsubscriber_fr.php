<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// Fichier source, a modifier dans svn://zone.spip.org/spip-zone/_plugins_/mailsubscribers/trunk/lang/
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// B
	'bouton_actualiser_segments' => 'Actualiser les segments',
	'bouton_importer' => 'Importer',
	'bouton_invitation' => 'Inviter à s’inscrire à la newsletter',
	'bouton_previsu_importer' => 'Prévisualiser',

	// C
	'confirmsubscribe_invite_texte_email_1' => '@invite_email_from@ vous invite à vous inscrire à la Newsletter de @nom_site_spip@ avec l’adresse email @email@.',
	'confirmsubscribe_invite_texte_email_3' => 'S’il s’agit d’une erreur de notre part, vous pouvez ignorer ce mail : cette demande sera automatiquement annulée.',
	'confirmsubscribe_invite_texte_email_liste_1' => '@invite_email_from@ vous invite à vous inscrire à la newsletter « @titre_liste@ » de @nom_site_spip@ avec l’adresse email @email@.',
	'confirmsubscribe_sujet_email' => '[@nom_site_spip@] Confirmation d’inscription à la Newsletter',
	'confirmsubscribe_texte_email_1' => 'Vous avez demandé à vous inscrire à la Newsletter de @nom_site_spip@ avec l’adresse email @email@.',
	'confirmsubscribe_texte_email_2' => 'Pour confirmer votre inscription, merci de cliquer sur le lien suivant :
@url_confirmsubscribe@',
	'confirmsubscribe_texte_email_3' => 'S’il s’agit d’une erreur de notre part ou si vous avez changé d’avis, vous pouvez ignorer ce mail : cette demande sera automatiquement annulée.',
	'confirmsubscribe_texte_email_envoye' => 'Un email a été envoyé à cette adresse pour confirmation.',
	'confirmsubscribe_texte_email_liste_1' => 'Vous avez demandé à vous inscrire à la newsletter « @titre_liste@ » de @nom_site_spip@ avec l’adresse email @email@.',
	'confirmsubscribe_texte_email_listes_1' => 'Vous avez demandé à vous inscrire aux newsletters « @titre_liste@ » de @nom_site_spip@ avec l’adresse email @email@.',
	'confirmsubscribe_titre_email' => 'Confirmation d’inscription à la Newsletter',
	'confirmsubscribe_titre_email_liste' => 'Confirmation d’inscription à la newsletter « <b>@titre_liste@</b> »',
	'confirmsubscribe_titre_email_listes' => 'Confirmation d’inscription aux newsletter ',

	// D
	'defaut_message_invite_email_subscribe' => 'Bonjour, je suis abonné à la newsletter de @nom_site_spip@ et je te propose de t’y inscrire également.',

	// E
	'erreur_adresse_existante' => 'Cette adresse email est déjà dans la liste',
	'erreur_adresse_existante_editer' => 'Cette adresse email est déjà enregistrée - <a href="@url@">Editer cet utilisateur</a>',
	'erreur_technique_subscribe' => 'Une erreur technique a empêché votre inscription.',
	'explication_listes_diffusion_option_defaut' => 'Un ou plusieurs identifiants de listes séparés par des virgules',
	'explication_listes_diffusion_option_statut' => 'Filtrer les listes selon les statuts',
	'explication_to_email' => 'Envoyer un email de pré-inscription aux adresses suivantes (plusieurs adresses séparées par une virgule si besoin).',

	// F
	'force_synchronisation' => 'Synchroniser',

	// I
	'icone_creer_mailsubscriber' => 'Ajouter une inscription',
	'icone_modifier_mailsubscriber' => 'Modifier cette inscription',
	'info_1_adresse_a_importer' => '1 adresse à importer',
	'info_1_mailsubscriber' => '1 inscrit aux envois',
	'info_aucun_mailsubscriber' => 'Aucun inscrit aux envois',
	'info_email_inscriptions' => 'Inscriptions pour @email@ :',
	'info_email_limite_nombre' => 'Invitation limitée à 5 personnes.',
	'info_email_obligatoire' => 'Email obligatoire',
	'info_emails_invalide' => 'L’un des email est invalide',
	'info_nb_adresses_a_importer' => '@nb@ adresses à importer',
	'info_nb_mailsubscribers' => '@nb@ inscrits aux envois',
	'info_statut_poubelle' => 'poubelle',
	'info_statut_prepa' => 'pas inscrit',
	'info_statut_prop' => 'en attente',
	'info_statut_refuse' => 'suspendu',
	'info_statut_valide' => 'inscrit',

	// L
	'label_desactiver_notif_1' => 'Désactiver la notification des inscriptions pour cet import',
	'label_email' => 'Email',
	'label_file_import' => 'Fichier à importer',
	'label_from_email' => 'Email qui invite',
	'label_informations_liees' => 'Informations segmentables',
	'label_inscription' => 'Inscription',
	'label_lang' => 'Langue',
	'label_listes' => 'Listes',
	'label_listes_diffusion_option_statut' => 'Statut',
	'label_listes_import_subscribers' => 'Inscrire aux listes',
	'label_mailsubscriber_optin' => 'Je veux recevoir la Newsletter',
	'label_message_invite_email_subscribe' => 'Message d’accompagnement de l’email envoyé',
	'label_nom' => 'Nom',
	'label_optin' => 'Opt-in',
	'label_statut' => 'Statut',
	'label_to_email' => 'Email à inviter',
	'label_toutes_les_listes' => 'Toutes',
	'label_valid_subscribers_1' => 'Valider directement les inscriptions sans demande de confirmation',
	'label_vider_table_1' => 'Supprimer toutes les adresses en base avant cet import',

	// M
	'mailsubscribers_poubelle' => 'Supprimés',
	'mailsubscribers_prepa' => 'Non inscrits',
	'mailsubscribers_prop' => 'À confirmer',
	'mailsubscribers_refuse' => 'Désinscrits',
	'mailsubscribers_tous' => 'Tous',
	'mailsubscribers_valide' => 'Inscrits',

	// S
	'subscribe_deja_texte' => 'L’adresse email @email@ est déjà inscrite à cette newsletter',
	'subscribe_sujet_email' => '[@nom_site_spip@] Inscription à la Newsletter',
	'subscribe_texte_email_1' => 'Nous avons bien pris en compte votre inscription à notre Newsletter avec l’adresse email @email@.',
	'subscribe_texte_email_2' => 'Nous vous remercions de l’intérêt que vous portez à @nom_site_spip@.',
	'subscribe_texte_email_3' => 'En cas d’erreur de notre part, ou si vous changez d’avis, vous pouvez vous désinscrire à tout moment au moyen du lien suivant :
@url_unsubscribe@',
	'subscribe_texte_email_liste_1' => 'Nous avons bien pris en compte votre inscription à la newsletter « @titre_liste@ » avec l’adresse email @email@.',
	'subscribe_texte_email_listes_1' => 'Nous avons bien pris en compte votre inscription aux newsletters « @titre_liste@ » avec l’adresse email @email@.',
	'subscribe_titre_email' => 'Inscription à la Newsletter',
	'subscribe_titre_email_liste' => 'Inscription à la newsletter « <b>@titre_liste@</b> »',

	// T
	'texte_ajouter_mailsubscriber' => 'Ajouter un inscrit à la newsletter',
	'texte_avertissement_import' => 'Une colonne <tt>statut</tt> est fournie : les données seront importées telles quelles, en ecrasant celles qui peuvent déjà exister pour certains email.',
	'texte_changer_statut_mailsubscriber' => 'Cet inscrit à la newsletter est :',
	'texte_import_export_bonux' => 'Pour importer ou exporter les listes d’inscrits, installez le plugin <a href="https://plugins.spip.net/spip_bonux">SPIP-Bonux</a>',
	'texte_statut_en_attente_confirmation' => 'en attente confirmation',
	'texte_statut_pas_encore_inscrit' => 'pas inscrit',
	'texte_statut_refuse' => 'suspendue',
	'texte_statut_valide' => 'active',
	'texte_vous_avez_clique_vraiment_tres_vite' => 'Vous avez cliqué vraiment très vite sur le bouton de confirmation. Êtes-vous réellement humain ?',
	'titre_bonjour' => 'Bonjour',
	'titre_export_mailsubscribers' => 'Exporter les inscrits',
	'titre_export_mailsubscribers_all' => 'Exporter toutes les adresses',
	'titre_import_mailsubscribers' => 'Importer des adresses',
	'titre_langue_mailsubscriber' => 'Langue de cet inscrit',
	'titre_listes_de_diffusion' => 'Listes de diffusion',
	'titre_logo_mailsubscriber' => 'Logo de cet inscrit',
	'titre_mailsubscriber' => 'Inscrit à la newsletter',
	'titre_mailsubscribers' => 'Inscrits aux envois par email',
	'titre_recherche_email' => 'Email « @email@ »',
	'titre_recherche_envois' => 'Envois à « @email@ »',

	// U
	'unsubscribe_deja_texte' => 'L’adresse email @email@ n’est pas inscrite à cette newsletter',
	'unsubscribe_sujet_email' => '[@nom_site_spip@] Désinscription de la Newsletter',
	'unsubscribe_texte_confirmer_email_1' => 'Veuillez confirmer la désinscription de l’adresse email @email@ en cliquant sur le bouton : ',
	'unsubscribe_texte_confirmer_email_liste_1' => 'Veuillez confirmer la désinscription de l’adresse email @email@ de la newsletter <b>@titre_liste@</b> en cliquant sur le bouton : ',
	'unsubscribe_texte_email_1' => 'L’adresse email @email@ a bien été retirée de cette newsletter.',
	'unsubscribe_texte_email_2' => 'Nous espérons vous revoir bientôt sur @nom_site_spip@.',
	'unsubscribe_texte_email_3' => 'En cas d’erreur de notre part, ou si vous changez d’avis, vous pouvez vous réinscrire à tout moment au moyen du lien suivant :
@url_subscribe@',
	'unsubscribe_texte_email_liste_1' => 'L’adresse email @email@ a bien été retirée de la liste de diffusion de la newsletter <b>@titre_liste@</b>.',
	'unsubscribe_texte_email_listes_1' => 'L’adresse email @email@ a bien été retirée des listes de diffusion aux newsletters <b>@titre_liste@</b>.',
	'unsubscribe_titre_email' => 'Désinscription de la Newsletter',
	'unsubscribe_titre_email_liste' => 'Désinscription de la newsletter <b>@titre_liste@</b>'
);
