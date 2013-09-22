<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// Fichier source, a modifier dans svn://zone.spip.org/spip-zone/_plugins_/clevermail/trunk/lang/
if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// A
	'a_partir_csv' => 'A partir d’un fichier CSV :',
	'abonne' => 'abonné',
	'abonne_ajoute' => 'abonné ajouté',
	'abonne_aucune_lettre' => 'Abonné à aucune newsletter',
	'abonne_inconnu' => 'Abonné inconnu',
	'abonne_lettres' => 'Abonner aux lettres d’information',
	'abonne_maj' => 'abonné mis à jour',
	'abonnes' => 'abonnés',
	'abonnes_ajoutes' => 'abonnés ajoutés',
	'abonnes_maj' => 'abonnés mis à jour',
	'abonnes_majuscule' => 'Abonnés',
	'actions' => 'Actions',
	'actualiser' => 'Actualiser',
	'administration' => 'CleverMail',
	'ajouter_abonnes' => 'Ajouter des abonnés',
	'annuler' => 'Annuler',
	'apercu' => 'Aperçu',
	'apercu_html' => 'Aperçu HTML',
	'apercu_texte' => 'Aperçu texte',
	'aucun_abonne' => 'aucun abonné',
	'aucun_abonne_ajoute' => 'aucun abonné ajouté',
	'aucun_message' => 'aucun message',
	'aucun_message_en_attente' => 'aucun en attente',
	'aucun_message_en_cours_envoi' => 'aucun en cours d’envoi',
	'aucun_message_envoye' => 'aucun envoyé',
	'aucun_message_queue_envoye' => 'aucun message envoyé',
	'aucun_nouvel_abonne' => 'aucun nouvel abonné',
	'aucun_resultat' => 'Aucun résultat',
	'aucune_inscription' => 'Aucune inscription ne correspond à ce paramètre.',
	'aucune_liste' => 'Veuillez choisir un moins une lettre d’information',
	'auto_erreur_ce_jour_mois_existe_pas' => 'Ce jour n’existe pas dans le mois.',
	'auto_erreur_ce_jour_mois_pas_possible' => 'Le choix de ce jour n’est pas possible.',
	'auto_erreur_ce_jour_semaine_existe_pas' => 'Ce jour n’existe pas dans la semaine.',
	'auto_erreur_ce_mode_automatisation_existe_pas' => 'Ce mode d’automatisation n’existe pas.',
	'auto_erreur_cette_heure_existe_pas' => 'Cette heure n’existe pas.',
	'auto_erreur_choisir_un_jour_minimum' => 'Vous devez choisir au moins un jour.',
	'auto_heure_creation' => 'Heure de création du message',
	'auto_heure_creation_explication' => 'Les messages seront créés à cette heure et directement mis en file d’attente pour envoi.',
	'auto_heure_creation_minutes' => 'h00',
	'auto_jours_semaine_explication' => 'Les messages seront créés ces jours là.',
	'auto_mode' => 'Mode',
	'auto_mode_day' => 'Envois quotidiens',
	'auto_mode_month' => 'Envois mensuels',
	'auto_mode_none' => 'Aucune automatisation',
	'auto_mode_week' => 'Envois hebdomadaires',
	'auto_month_day' => 'Jour du mois',
	'auto_month_day_explication' => 'Les jours proposés ne vont que de 1 à 28 pour éviter tout problème lors des mois de moins de 31 jours.',
	'auto_subscribers' => 'Ajout automatique d’abonnés',
	'auto_subscribers_explication' => 'Les adresses contenus dans ce fichier distant seront automatiquement abonnées une fois par jour à cette lettre.',
	'auto_subscribers_mode' => 'Mode d’abonnement',
	'auto_subscribers_url' => 'URL du fichier d’adresses',
	'auto_week_day_friday' => 'Vendredi',
	'auto_week_day_monday' => 'Lundi',
	'auto_week_day_saturday' => 'Samedi',
	'auto_week_day_sunday' => 'Dimanche',
	'auto_week_day_thursday' => 'Jeudi',
	'auto_week_day_tuesday' => 'Mardi',
	'auto_week_day_wednesday' => 'Mercredi',
	'auto_week_days' => 'Jour(s) de la semaine',
	'automatisation' => 'Automatisation des envois',

	// B
	'bouton_inscription' => 'S’inscrire',

	// C
	'ce_champ_est_obligatoire' => 'Ce champ est obligatoire.',
	'cette_adresse_email_n_est_pas_valide' => 'Cette adresse e-mail n’est pas valide.',
	'changements_mode_abonnement' => ' changements de mode d’abonnement',
	'changer_mode' => 'Changer de mode',
	'choix_toutes_les_listes' => 'Toutes les listes',
	'choix_version_html' => 'HTML',
	'choix_version_texte' => 'texte brut',
	'clevermail' => 'CleverMail',
	'configuration_generale' => 'Configuration générale',
	'confirmation_desinscription' => 'Confirmation d’une désinscription envoyé par e-mail',
	'confirmation_inscription' => 'Confirmation d’une inscription envoyé par e-mail',
	'confirmation_inscription_multiple' => 'Confirmation d’une inscription multiple envoyé par e-mail',
	'confirmation_votre_desinscription' => 'Confirmation de votre désinscription',
	'confirmation_votre_desinscription_text' => '
Bonjour,

Veuillez confirmer votre désinscription en cliquant sur ce lien :

 @@URL_CONFIRMATION@@

Merci
',
	'confirmation_votre_inscription' => 'Confirmation de votre inscription',
	'confirmation_votre_inscription_multiple' => 'Confirmation de votre inscription',
	'confirmation_votre_inscription_text' => '
Bonjour,

Pour confirmer votre inscription à la lettre d’information @@NOM_COMPLET@@ au format @@FORMAT_INSCRIPTION@@, veuillez cliquer sur ce lien :

 @@URL_CONFIRMATION@@

Merci
',
	'confirmation_votre_inscription_text_multiple' => '
Bonjour,

Pour confirmer votre inscription aux lettres d’information suivantes :

 @@NOM_COMPLET@@ 

veuillez cliquer sur ce lien :

 @@URL_CONFIRMATION@@

Merci
',
	'confirme_desabonnement_multiple_lettre' => 'Vous êtes sur le point de désabonner plusieurs abonnés de cette lettre. Étes vous sur ?',
	'confirme_suppression_multiple_base' => 'Vous êtes sur le point de supprimer des abonnés de la base. Étes vous sur ?',
	'corps_mail_mod' => 'Bonjour,

Vous recevez ce message en tant que modérateur de la lettre @@NOM_LETTRE@@.

Pour valider l’inscription de @mail@ à cette lettre, veuillez cliquer sur ce lien :

 @@URL_CONFIRMATION@@

Merci',
	'cree' => 'Créé',
	'creer' => 'Créer',
	'creer_lettre' => 'Créer une lettre d’information',
	'creer_message' => 'Créer un message',
	'creer_nouveau_message' => 'Créer un nouveau message',

	// D
	'deja_inscrit' => 'Vous étiez déjà inscrit à la lettre « @lst_name@ ». Votre mode d’inscription a été mis à jour.',
	'deja_validee' => 'Cette opération a déjà été validée. Désolé.',
	'demande_transmise' => 'Votre demande d’inscription à la lettre « @lst_name@ » va être examinée dans les plus brefs délais.',
	'desabonner' => 'Désabonner',
	'desabonner2' => 'désabonner',
	'desabonner_abonnes' => 'Désabonner les abonnés selectionnés',
	'desabonner_confirmer' => 'Êtes-vous certain de vouloir désabonner cette personne ?',
	'description' => 'Description',
	'desinscription_confirmation_debut' => 'Désinscription de la lettre d’information',
	'desinscription_confirmation_fin' => 'demandée. Vous allez recevoir un message demandant confirmation.',
	'desinscription_validee' => 'Votre désinscription de la lettre « @lst_name@ » est validée. A bientôt.',

	// E
	'editer_lettre' => 'Editer la lettre d’information',
	'email_administrateur' => 'E-mail administrateur',
	'email_expediteur' => 'E-mail expéditeur (from et reply-to)',
	'email_moderateur' => 'E-mail du modérateur',
	'email_non_valide' => 'Adresse non valide.',
	'email_return_path' => 'E-mail des retours d’erreurs (return-path)',
	'emails' => 'E-mails',
	'envoye' => 'Envoyé',
	'envoyer' => 'Envoyer',
	'envoyer_non_aucun_abonne' => 'Envoi impossible, aucun abonné',
	'erreur' => 'Erreur',
	'erreur_contenu_vide' => 'Un nouveau message n’a pas pu être créé faute de contenu.',
	'et_ou_saisir_des_adresses' => '...et/ou saisir des adresses.',
	'exporter' => 'Exporter la liste des abonnés',

	// F
	'front_clevermail_action_validation' => 'Validation d’une opération',
	'front_clevermail_unsubscription_query' => 'Demande de désinscription',

	// I
	'importer' => 'Importer',
	'info_parametres' => 'L’e-mail de l’administrateur est utilisé par défaut comme l’e-mail du modérateur lors de la création d’une newsletter',
	'infolettres' => 'Infolettres',
	'informations' => 'Informations',
	'inscription_deja_abonne_autre_mode' => 'Vous étiez déjà inscrit à la lettre « @lst_name@ » dans un autre mode, changement opéré.',
	'inscription_deja_abonne_meme_mode' => 'Vous étiez déjà inscrit à la lettre « @lst_name@ » dans ce même mode. Néanmoins, nous apprécions votre enthousiasme.',
	'inscription_encours' => 'Votre demande d’inscription à la lettre @nom_lettre@ est en cours d’examen. Merci de patienter.',
	'inscription_mok' => 'Votre demande d’inscription à la lettre « @lst_name@ » a été soumise au modérateur. Vous serez informé de sa décision.',
	'inscription_nok' => 'Inscription non authorisée pour cette lettre d’information',
	'inscription_ok' => 'Vous allez recevoir un message de demande de confirmation de votre inscription à la lettre « @lst_name@ ».',
	'inscription_ok_multiple' => 'Vous allez recevoir un message de demande de confirmation de votre inscription aux lettre « @lst_name@ ».',
	'inscription_validee' => 'Votre inscription à la lettre « @lst_name@ » est validée. Merci.',

	// L
	'label_contenu_html' => 'Contenu HTML',
	'label_contenu_text' => 'Contenu texte',
	'label_inscription_email' => 'Votre adresse e-mail :',
	'label_inscription_lettres' => 'Choisissez parmi ces lettres d’information :',
	'label_inscription_version' => 'Type de message :',
	'le_format_des_adresses_email_ne_semble_pas_bon' => 'Le format des adresses e-mail ne semble pas bon.',
	'lettre_meme_nom' => 'Une lettre d’information porte déjà ce nom',
	'lettre_sans_nom' => 'Une lettre d’information doit avoir un nom',
	'lettres_information' => 'Lettres d’information',
	'lettres_non_classees' => 'Lettres non classées',
	'lire_en_ligne' => 'Lire le message sur le site.',
	'liste_abonnes' => 'Liste des abonnés',
	'liste_lettres' => 'Liste des lettres d’information',
	'liste_lettres_aucune' => 'Vous n’avez pas encore créé de lettre d’information.',
	'liste_messages' => 'Messages',

	// M
	'mail_info_desinscription_corps' => 'Alerte envoyée par le plugin CleverMail du site @nom_site@ ( @url_site@ ) :

Désinscription de @sub_email@ de la lettre « @lst_name@ »',
	'mail_info_inscription_corps' => 'Alerte envoyée par le plugin CleverMail du site @nom_site@ ( @url_site@ ) :

Inscription de @sub_email@ à la lettre « @lst_name@ »',
	'mail_info_inscription_sujet' => 'Inscription de @sub_email@',
	'mail_inscription_multiple' => 'Bonjour,

Pour confirmer votre inscription aux lettres d’information suivantes :

 @@NOM_COMPLET@@

veuillez cliquer sur ce lien :

 @@URL_CONFIRMATION@@

Merci',
	'maj_inscription' => 'inscription mise à jour',
	'maj_inscriptions' => 'inscriptions mise à jour',
	'mauvais_affichage' => 'Si vous ne visualisez pas cet email, lisez-le sur le site',
	'mauvais_identifiant_lettre' => 'Mauvais identifiant de lettre d’information',
	'message' => 'message',
	'message_queue_attente' => 'message en attente',
	'message_queue_envoye' => '@nb@ message envoyé',
	'messages' => 'messages',
	'messages_attentes' => 'Messages en attente',
	'messages_attentes_text' => 'Ici sont listés les messages qui sont en file d’attente pour être envoyés',
	'messages_cours_envoi' => 'Messages en cours d’envoi',
	'messages_cours_envoi_text' => 'Ici sont listés les messages qui sont en cours d’envoi par le facteur, lot par lot',
	'messages_envoyes' => 'Messages envoyés',
	'messages_envoyes_text' => 'Ici sont listés les messages qui ont été envoyés avec succès',
	'messages_queue_attente' => 'messages en attente',
	'messages_queue_envoye' => '@nb@ messages envoyés',
	'mod_closed' => 'Fermée',
	'mod_email' => 'E-mail',
	'mod_explication' => 'Les différents modes de modération sont décrits ci-contre.',
	'mod_explication_closed' => 'Fermée : personne ne peut s’inscrire',
	'mod_explication_email' => 'E-mail : tout le monde peut s’inscrire après confirmation par e-mail',
	'mod_explication_mod' => 'Modérée : le modérateur doit accepter l’inscription',
	'mod_explication_open' => 'Ouverte : tout le monde peut s’inscrire sans confirmation',
	'mod_mod' => 'Modérée',
	'mod_open' => 'Ouverte',
	'mode' => 'Mode',
	'moderation' => 'Modération des inscriptions',
	'modifie' => 'Modifié',
	'modifier' => 'Modifier',
	'modifier_abonne' => 'Modifier un abonné',
	'modifier_message' => 'Modifier un message',
	'modifier_submit' => 'Modifier',

	// N
	'n_nouveaux_abonnes' => ' nouveaux abonnés',
	'nettoyer_abonnement' => 'Vider les abonnements en attente depuis plus d’un mois',
	'news1' => 'Les nouveautés n°1',
	'news_depuis' => 'Les nouveautés depuis le ',
	'nom' => 'Nom',
	'nom_formulaire_clevermail' => 'Formulaire d’abonnement aux listes de diffusion',
	'nombre_messages' => 'Nombre de messages par envoi',
	'nouveau_message' => 'nouveau message',
	'nouveaux_abonnes_et' => ' et ',
	'nouveaux_messages' => 'Nouveaux Messages',
	'nouveaux_messages_text' => 'Ici sont listés les messages qui ne sont pas encore envoyés',

	// P
	'parametres' => 'Paramètres',
	'plusieurs_messages_en_attente' => '@nb@ en attente',
	'plusieurs_messages_en_cours_envoi' => '@nb@ en cours d’envoi',
	'plusieurs_messages_envoyes' => '@nb@ envoyés',
	'prefixer_messages' => 'Préfixe',
	'prefixer_messages_explication' => 'Préfixer les sujets des messages avec le nom de la lettre d’information',
	'procedure_termine' => 'Cliquez ici pour terminer',
	'proprietes' => 'Propriétés',

	// R
	'resultats' => 'résultats',

	// S
	'selection_des_listes' => 'Sélection des listes',
	'send_error' => 'Erreur lors de l’envoi du message de demande de confirmation, veuillez réessayer.',
	'source_des_abonnes' => 'Lister les abonnées',
	'statistiques' => 'Statistiques',
	'sujet' => 'Sujet',
	'sujet_mail_inscription_multiple' => 'Confirmation de votre inscription',
	'sujet_mail_mod' => 'Modération de la lettre @nom_lettre@',
	'sujet_message' => 'Sujet du message',
	'sujet_vide' => 'Le sujet ne doit pas être vide',
	'supprimer' => 'Supprimer',
	'supprimer_abonne_base' => 'Supprimer définitivement cet abonné de la base',
	'supprimer_abonnes' => 'Supprimer les abonnés selectionnés',
	'supprimer_confirmer' => 'Êtes-vous certain de vouloir supprimer ?',

	// T
	'tags_specifiques' => 'Tags spécifiques',

	// U
	'un_message_en_attente' => '1 en attente',
	'un_message_en_cours_envoi' => '1 en cours d’envoi',
	'un_message_envoye' => '1 envoyé',
	'url_templates' => 'URL des templates générés',
	'url_templates_explication' => 'Précisez l’URL d’une page distante, ou le nom d’un squelette SPIP local, sans l’extension .html. CleverMail propose le squelette par défaut <code>clevermail_nouveautes_html</code>.',

	// V
	'version_html' => 'Version HTML',
	'version_txt' => 'Version texte brut',
	'version_txt_explication' => 'Si vous ne remplissez pas ce paramètre, la version texte sera obtenue automatiquement à partir de la version HTML. CleverMail propose le squelette par défaut <code>clevermail_nouveautes_text</code>.',
	'veuillez_corriger_votre_saisie' => 'Veuillez corriger votre saisie.',
	'vous_devez_choisir_au_moins_une_liste' => 'Vous devez choisir au moins une lettre.',
	'vous_devez_choisir_un_fichier' => 'Vous devez choisir un fichier...'
);

?>
