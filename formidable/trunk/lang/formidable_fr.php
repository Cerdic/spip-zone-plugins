<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// Fichier source, a modifier dans svn://zone.spip.org/spip-zone/_plugins_/formidable/trunk/lang/
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// A
	'activer_pages_explication' => 'Par défaut, les pages publiques des formulaires ne sont pas autorisées',
	'activer_pages_label' => 'Autoriser la création de pages publique pour les formulaires',
	'admin_reponses_auteur' => 'Autoriser les auteurs des formulaires à modifier les réponses',
	'admin_reponses_auteur_explication' => 'Seul les administrateurs peuvent normalement modifier les réponses apportées à un formulaire (à la poubelle, publiée, proposé à l’évaluation). Cette option permet à un auteur d’un formulaire d’en modifier le statut (au risque de fausser d’éventuelles statistiques).',
	'analyse_avec_reponse' => 'Réponses non-vide',
	'analyse_exclure_champs_explication' => 'Mettre le nom des champs à exclure dans l’analyse, séparés par des <code>|</code>. Ne pas mettre les <code>@</code>.',
	'analyse_exclure_champs_label' => 'Champs à exclure',
	'analyse_exporter' => 'Exporter l’analyse',
	'analyse_longueur_moyenne' => 'Longueur moyenne en nombre de mots',
	'analyse_nb_reponses_total' => '@nb@ personnes ont répondu à ce formulaire.',
	'analyse_sans_reponse' => 'Laissés blanc',
	'analyse_une_reponse_total' => 'Une personne a répondu à ce formulaire.',
	'analyse_zero_reponse_total' => 'Aucune personne n’a répondu à ce formulaire.',
	'aucun_traitement' => 'Aucun traitement',
	'autoriser_admin_restreint' => 'Autoriser les administrateurs restreints à créer et modifier les formulaires',
	'autoriser_admin_restreint_explication' => 'Par défaut, seuls les administrateurs ont accès à la création et modification des formulaires',

	// B
	'bouton_formulaires' => 'Formulaires',
	'bouton_revert_formulaire' => 'Revenir à la dernière version enregistrée',

	// C
	'cfg_analyse_classe_explication' => 'Vous pouvez indiquer des classes CSS qui seront ajoutées
	sur le conteneur de chaque graphique, tel que <code>gray</code>,<code>blue</code>,
	<code>orange</code>, <code>green</code> ou tout ce qui vous plairait !',
	'cfg_analyse_classe_label' => 'Classe CSS de la barre de progression',
	'cfg_objets_explication' => 'Choisir les contenus auxquels pourront être liés les formulaires.',
	'cfg_objets_label' => 'Lier les formulaires aux contenus',
	'cfg_titre_page_configurer_formidable' => 'Configurer Formidable',
	'cfg_titre_parametrages_analyse' => 'Paramétrages de l’analyse des réponses',
	'champs' => 'Champs',
	'changer_statut' => 'Ce formulaire est :',
	'creer_dossier_formulaire_erreur_impossible_creer' => 'Impossible de créer le dossier @dossier@,  nécessaire pour stocker les fichiers. Vérifier les droits d’accès.',
	'creer_dossier_formulaire_erreur_impossible_ecrire' => 'Impossible d’écrire dans le  @dossier@,  nécessaire pour stocker les fichiers. Vérifier les droits d’accès.',
	'creer_dossier_formulaire_erreur_possible_lire_exterieur' => 'Il est possible de lire à distance le contenu du dossier  @dossier@. Ceci est problématique en terme de confidentialité des données.',

	// E
	'echanger_formulaire_forms_importer' => 'Forms & Tables (.xml)',
	'echanger_formulaire_wcs_importer' => 'W.C.S. (.wcs)',
	'echanger_formulaire_yaml_importer' => 'Formidable (.yaml)',
	'editer_apres_choix_formulaire' => 'Le formulaire, à nouveau',
	'editer_apres_choix_redirige' => 'Rediriger vers une nouvelle adresse',
	'editer_apres_choix_rien' => 'Rien du tout',
	'editer_apres_choix_stats' => 'Les statistiques des réponses',
	'editer_apres_choix_valeurs' => 'Les valeurs saisies',
	'editer_apres_explication' => 'Après validation, afficher à la place du formulaire :',
	'editer_apres_label' => 'Afficher ensuite',
	'editer_css' => 'Classes CSS',
	'editer_descriptif' => 'Descriptif',
	'editer_descriptif_explication' => 'Une explication du formulaire destinée à l’espace privé.',
	'editer_identifiant' => 'Identifiant',
	'editer_identifiant_explication' => 'Donnez un identifiant textuel unique qui vous permettra d’appeler plus facilement le formulaire. L’identifiant ne peut contenir que des chiffres, lettres latines non accentuées et le caractère "_".',
	'editer_menu_auteurs' => 'Configurer les auteurs',
	'editer_menu_champs' => 'Configurer les champs',
	'editer_menu_formulaire' => 'Configurer le formulaire',
	'editer_menu_traitements' => 'Configurer les traitements',
	'editer_message_erreur_unicite_explication' => 'Si vous laissez ce champ vide, le message d’erreur par défaut de formidable s’affichera',
	'editer_message_erreur_unicite_label' => 'Message d’erreur quand un champ n’est pas unique',
	'editer_message_ok' => 'Message de retour',
	'editer_message_ok_explication' => 'Vous pouvez personnaliser le message qui sera affiché à l’utilisateur après l’envoi d’un formulaire valide. Il est possible d’afficher la valeur de certains champs soumis en utilisant  @raccourci@.',
	'editer_modifier_formulaire' => 'Modifier le formulaire',
	'editer_nouveau' => 'Nouveau formulaire',
	'editer_redirige_url' => 'Adresse de redirection après validation',
	'editer_redirige_url_explication' => 'Laissez vide si vous souhaitez rester sur la même page',
	'editer_titre' => 'Titre',
	'editer_unicite_explication' => 'Enregistrer le formulaire uniquement si la valeur d’un champ spécifique est unique parmi toutes les réponses enregistrées.',
	'editer_unicite_label' => 'Vérifier l’unicité d’un champ',
	'erreur_autorisation' => 'Vous n’avez pas le droit d’éditer les formulaires du site.',
	'erreur_base' => 'Une erreur technique est survenue durant l’enregistrement.',
	'erreur_deplacement_fichier' => 'Le fichier « @nom@ » n’a pas pu être stocké correctement par le système. Contactez le webmestre.',
	'erreur_fichier_expire' => 'Le lien pour télécharger le fichier est trop ancien.',
	'erreur_fichier_introuvable' => 'Le fichier demandé est introuvable.',
	'erreur_generique' => 'Il y a des erreurs dans les champs ci-dessous, veuillez vérifier votre envoi.',
	'erreur_identifiant' => 'Cet identifiant est déjà utilisé.',
	'erreur_identifiant_format' => 'L’identifiant ne peut contenir que des chiffres, lettres latines non accentuées et le caractère "_"',
	'erreur_importer_forms' => 'Erreur durant l’importation du formulaire Forms&Tables',
	'erreur_importer_wcs' => 'Erreur durant l’importation du formulaire W.C.S',
	'erreur_importer_yaml' => 'Erreur durant l’importation du fichier YAML',
	'erreur_inexistant' => 'Le formulaire n’existe pas.',
	'erreur_unicite' => 'Cette valeur est déjà utilisée',
	'exporter_adresses_ip' => 'Inclure les adresses IP dans l’export des réponses',
	'exporter_adresses_ip_explication' => 'Par défaut, les adresses IP ne sont pas incluses dans l’export des réponses',
	'exporter_formulaire_cle_ou_valeur_cle_label' => 'Clés',
	'exporter_formulaire_cle_ou_valeur_label' => 'Pour les boutons radios, listes déroulantes, etc., faut-il exporter les valeurs humainement lisibles (labels) ou bien les clés ?',
	'exporter_formulaire_cle_ou_valeur_valeur_label' => 'Valeurs lisibles (labels)',
	'exporter_formulaire_date_debut_label' => 'À partir de (inclus)',
	'exporter_formulaire_date_erreur' => 'La date de début doit être antérieure à la date de fin',
	'exporter_formulaire_date_fin_label' => 'Jusqu’au (inclus)',
	'exporter_formulaire_format_label' => 'Format du fichier',
	'exporter_formulaire_statut_label' => 'Réponses',

	// F
	'formulaire_anonyme_explication' => 'Ce formulaire est anonyme, c’est à dire que l’identité de l’utilisateur n’est pas enregistrée.',
	'formulaires_aucun' => 'Il n’y a pour l’instant aucun formulaire.',
	'formulaires_aucun_champ' => 'Il n’y a pour l’instant aucun champ de saisie pour ce formulaire.',
	'formulaires_corbeille_tous' => '@nb@ formulaires dans la corbeille',
	'formulaires_corbeille_un' => 'Un formulaire dans la corbeille',
	'formulaires_dupliquer' => 'Dupliquer le formulaire',
	'formulaires_dupliquer_copie' => '(copie)',
	'formulaires_introduction' => 'Créez et configurez ici les formulaires de votre site.',
	'formulaires_nouveau' => 'Créer un nouveau formulaire',
	'formulaires_reponses_corbeille_tous' => '@nb@ réponses de formulaire dans la corbeille',
	'formulaires_reponses_corbeille_un' => 'Une réponse de formulaire dans la corbeille',
	'formulaires_supprimer' => 'Supprimer le formulaire',
	'formulaires_supprimer_confirmation' => 'Attention, cela supprimera aussi tous les résultats. Êtes-vous sûr de vouloir supprimer ce formulaire ?',
	'formulaires_tous' => 'Tous les formulaires',

	// H
	'heures_minutes_secondes' => '@h@h @m@min @s@s',

	// I
	'id_formulaires_reponse' => 'Identifiant de la réponse',
	'identification_par_cookie' => 'Par cookie',
	'identification_par_id_auteur' => 'Par l’identifiant (id_auteur) de la personne authentifiée',
	'importer_formulaire' => 'Importer un formulaire',
	'importer_formulaire_fichier_label' => 'Fichier à importer',
	'importer_formulaire_format_label' => 'Format du fichier',
	'info_1_formulaire' => '1 formulaire',
	'info_1_reponse' => '1 réponse',
	'info_aucun_formulaire' => 'Aucun formulaire',
	'info_aucune_reponse' => 'Aucune réponse',
	'info_formulaire_refuse' => 'Archivé',
	'info_formulaire_utilise_par' => 'Formulaire utilisé par :',
	'info_nb_formulaires' => '@nb@ formulaires',
	'info_nb_reponses' => '@nb@ réponses',
	'info_reponse_proposee' => 'À modérer',
	'info_reponse_proposees' => 'À modérer',
	'info_reponse_publiee' => 'Validée',
	'info_reponse_publiees' => 'Validées',
	'info_reponse_refusee' => 'Refusée',
	'info_reponse_refusees' => 'Refusées',
	'info_reponse_supprimee' => 'À la corbeille',
	'info_reponse_supprimees' => 'À la corbeille',
	'info_reponse_toutes' => 'Toutes',
	'info_utilise_1_formulaire' => 'Formulaire utilisé :',
	'info_utilise_nb_formulaires' => 'Formulaires utilisés :',

	// J
	'jours_heures_minutes_secondes' => '@j@j @h@h @m@min @s@s',

	// L
	'lien_expire' => 'Lien expirant dans @delai@',
	'liens_ajouter' => 'Ajouter un formulaire',
	'liens_ajouter_lien' => 'Ajouter ce formulaire',
	'liens_creer_associer' => 'Créer et associer un formulaire',
	'liens_retirer_lien_formulaire' => 'Retirer ce formulaire',
	'liens_retirer_tous_liens_formulaires' => 'Retirer tous les formulaires',

	// M
	'minutes_secondes' => '@m@min @s@s',
	'modele_label_formulaire_formidable' => 'Quel formulaire ?',
	'modele_nom_formulaire' => 'un formulaire',

	// N
	'noisette_label_afficher_titre_formulaire' => 'Afficher le titre du formulaire ?',
	'noisette_label_identifiant' => 'Formulaire à afficher :',
	'noisette_nom_noisette_formulaire' => 'Formulaire',

	// P
	'pas_analyse_fichiers' => 'Formidable ne propose pas (encore) d’analyse des fichiers envoyés',

	// R
	'reponse_aucune' => 'Aucune réponse',
	'reponse_intro' => '@auteur@ a répondu au formulaire @formulaire@',
	'reponse_maj' => 'Dernière modification',
	'reponse_numero' => 'Réponse numéro :',
	'reponse_statut' => 'Cette réponse est :',
	'reponse_supprimer' => 'Supprimer cette réponse',
	'reponse_supprimer_confirmation' => 'Êtes-vous sûr de vouloir supprimer cette réponse ?',
	'reponse_une' => '1 réponse',
	'reponses_analyse' => 'Analyse des réponses',
	'reponses_anonyme' => 'Anonyme',
	'reponses_auteur' => 'Utilisateur',
	'reponses_exporter' => 'Exporter les réponses',
	'reponses_exporter_format_csv' => 'Tableur .CSV',
	'reponses_exporter_format_xls' => 'Excel .XLS',
	'reponses_exporter_statut_publie' => 'Publiées',
	'reponses_exporter_statut_tout' => 'Toutes',
	'reponses_exporter_telecharger' => 'Télécharger',
	'reponses_ip' => 'Adresse IP',
	'reponses_liste' => 'Liste des réponses',
	'reponses_liste_prop' => 'Réponses en attente de validation',
	'reponses_liste_publie' => 'Toutes les réponses validées',
	'reponses_nb' => '@nb@ réponses',
	'reponses_supprimer' => 'Supprimer toutes les réponses',
	'reponses_supprimer_confirmation' => 'Êtes-vous sûr de vouloir supprimer toutes les réponses à ce formulaire ?',
	'reponses_voir_detail' => 'Voir la réponse',
	'retour_aucun_traitement' => 'Votre réponse a bien été envoyée, mais aucun traitement n’a été défini pour ce formulaire. Il ne fait donc rien. :)',

	// S
	'sans_reponses' => 'Sans réponse',
	'secondes' => '@s@s',

	// T
	'texte_statut_poubelle' => 'à la poubelle',
	'texte_statut_propose_evaluation' => 'proposée',
	'texte_statut_publie' => 'validée',
	'texte_statut_refuse' => 'archivé',
	'texte_statut_refusee' => 'refusée',
	'titre_cadre_raccourcis' => 'Raccourcis',
	'titre_formulaires_archives' => 'Archives',
	'titre_formulaires_poubelle' => 'À la poubelle',
	'titre_reponses' => 'Réponses',
	'traitements_actives' => 'Traitements activés',
	'traitements_aide_memoire' => 'Aide mémoire :',
	'traitements_avertissement_creation' => 'Les modifications sur les champs du formulaire ont été enregistrées avec succès. Vous pouvez maintenant définir quels traitements seront effectués lors de l’utilisation du formulaire.',
	'traitements_avertissement_modification' => 'Les modifications sur les champs du formulaire ont été enregistrées avec succès. <strong>Certains traitements doivent peut-être être reconfigurés en conséquence.</strong>',
	'traitements_champ_aucun' => 'Aucun',
	'traiter_email_AR_label' => 'Accusé de réception',
	'traiter_email_accuse_explication_texte' => 'Pour activer la fonctionnalité d’accusé de réception, vous devez au préalable définir un expéditeur.',
	'traiter_email_contenu_courriel_label' => 'Contenu du courriel',
	'traiter_email_description' => 'Poster par courriel le résultat du formulaire  à une liste de destinataires.',
	'traiter_email_destinataires_courriel_label' => 'Destinataires du courriel',
	'traiter_email_envoyeur_courriel_label' => 'Expéditeur du courriel',
	'traiter_email_horodatage' => 'Formulaire "@formulaire@" posté le @date@ à @heure@.',
	'traiter_email_message_erreur' => 'Une erreur est survenue lors de l’envoi du courriel.',
	'traiter_email_message_ok' => 'Votre message a bien été envoyé par courriel.',
	'traiter_email_option_activer_accuse_label_case' => 'Envoyer aussi un courriel à l’adresse de l’envoyeur avec un message de confirmation.',
	'traiter_email_option_activer_ip_label_case' => 'Envoyer l’adresse IP de l’envoyeur aux destinataires.',
	'traiter_email_option_courriel_envoyeur_accuse_explication' => 'Précisez le courriel utilisé pour envoyer l’accusé de réception. Si vous ne mettez rien, ce sera l’adresse email du webmestre.',
	'traiter_email_option_courriel_envoyeur_accuse_label' => 'Courriel de l’expéditeur de l’accusé de réception',
	'traiter_email_option_destinataires_champ_form_attention' => 'Cette option est déconseillée, car elle constitue un appel au SPAM.
	<br />- Pour envoyer à un auteur du site, utiliser l’option « Destinataire » (plus haut).
	<br />- Pour envoyer à la personne qui remplit le formulaire, configurer l’accusé de réception (plus bas).
<br />
Cette option n’est conservée que pour compatibilité ascendante. Elle n’apparaît pas sur les nouveaux formulaires.
',
	'traiter_email_option_destinataires_champ_form_explication' => 'Si un de vos champ est une adresse email et que vous souhaitez envoyer le formulaire à cette adresse, sélectionnez le champ.',
	'traiter_email_option_destinataires_champ_form_label' => 'Destinaire présent dans un des champ des formulaires',
	'traiter_email_option_destinataires_explication' => 'Choisissez le champ qui correspondra aux destinataires du message. <br />
	Il s’agit d’un champ de type « Destinataires » ou « Champ caché », comprenant l’identifiant numérique d’un auteur du site.',
	'traiter_email_option_destinataires_label' => 'Destinataires',
	'traiter_email_option_destinataires_plus_explication' => 'Une liste d’adresses séparées par des virgules.',
	'traiter_email_option_destinataires_plus_label' => 'Destinataires supplémentaires',
	'traiter_email_option_destinataires_selon_champ_explication' => 'Permet d’indiquer un ou plusieurs destinataires en fonction de la valeur d’un champ.
	Indiquer le champ, sa valeur, et le ou les courriels concernés (séparés par une virgule) suivant ce format, tel que : "@selection_1@/choix1 : mail@example.tld". Vous pouvez indiquer plusieurs tests, en revenant à la ligne entre chaque test.',
	'traiter_email_option_destinataires_selon_champ_label' => 'Destinataires en fonction d’un champ',
	'traiter_email_option_envoyeur_courriel_explication' => 'Choisissez le champ qui contiendra le courriel de l’envoyeur.',
	'traiter_email_option_envoyeur_courriel_label' => 'Courriel de l’envoyeur',
	'traiter_email_option_envoyeur_nom_explication' => 'Construisez ce nom à l’aide des @raccourcis@ (cf. l’aide mémoire). Si vous ne mettez rien, ce sera le nom du site.',
	'traiter_email_option_envoyeur_nom_label' => 'Nom de l’envoyeur',
	'traiter_email_option_exclure_champs_email_explication' => 'Si vous souhaitez que certains champs ne s’affichent pas dans les emails envoyés (par exemple des champs cachés), il suffit de les définir ici, séparés par une virgule.',
	'traiter_email_option_exclure_champs_email_label' => 'Champs à exclure du contenu du message',
	'traiter_email_option_masquer_liens_label_case' => 'Masquer les liens d’administration dans le courriel.',
	'traiter_email_option_nom_envoyeur_accuse_explication' => 'Précisez le nom de l’expéditeur utilisé pour envoyer l’accusé de réception. Si vous ne mettez rien, ce sera le nom du site..',
	'traiter_email_option_nom_envoyeur_accuse_label' => 'Nom de l’expéditeur de l’accusé de réception',
	'traiter_email_option_pj_explication' => 'Si les documents postés pèsent moins de _FORMIDABLE_TAILLE_MAX_FICHIERS_EMAIL Mio (constante modifiable par le·la webmestre).',
	'traiter_email_option_pj_label' => 'Joindre les fichiers dans le courriel',
	'traiter_email_option_sujet_accuse_label' => 'Sujet de l’accusé de réception',
	'traiter_email_option_sujet_explication' => 'Construisez le sujet à l’aide des @raccourcis@. Si vous ne mettez rien, le sujet sera construit automatiquement.',
	'traiter_email_option_sujet_label' => 'Sujet du courriel',
	'traiter_email_option_sujet_valeurs_brutes_label' => 'Valeurs brutes',
	'traiter_email_option_sujet_valeurs_brutes_label_case' => 'Le courriel est destiné à un robot et non pas à un·e humain·e. Dans le sujet du message, mettre les valeurs brutes (compréhensibles par des robots) des champs et non pas les valeurs interprétées (compréhensible par des humain·e·s).',
	'traiter_email_option_vrai_envoyeur_explication' => 'Certains serveurs SMTP ne permettent pas d’utiliser un courriel arbitraire pour le champ "From". Pour cette raison Formidable insère par défaut le courriel de l’envoyeur dans le champ "Reply-To", et utilise le courriel du webmaster dans le champ "From". Cocher ici pour insèrer le courriel dans le champ "From".',
	'traiter_email_option_vrai_envoyeur_label' => 'Insérer le courriel de l’envoyeur dans le champ "From"',
	'traiter_email_page' => '<a href="@url@">Depuis cette page</a>.',
	'traiter_email_sujet' => '@nom@ vous a écrit.',
	'traiter_email_sujet_accuse' => 'Merci de votre réponse.',
	'traiter_email_sujet_courriel_label' => 'Sujet du courriel',
	'traiter_email_titre' => 'Envoyer par courriel',
	'traiter_email_url_enregistrement' => 'Vous pouvez gérer l’ensemble des réponses <a href="@url@">sur cette page</a>.',
	'traiter_email_url_enregistrement_precis' => 'Vous pouvez voir cette réponse <a href="@url@">sur cette page</a>.',
	'traiter_enregistrement_description' => 'Enregistrer les résultats du formulaire dans la base de données',
	'traiter_enregistrement_erreur_base' => 'Une erreur technique est survenue durant l’enregistrement en base de données',
	'traiter_enregistrement_erreur_deja_repondu' => 'Vous avez déjà répondu à ce formulaire.',
	'traiter_enregistrement_erreur_edition_reponse_inexistante' => 'La réponse à éditer est introuvable.',
	'traiter_enregistrement_message_ok' => 'Merci. Vos réponses ont bien été enregistrées.',
	'traiter_enregistrement_option_anonymiser_explication' => 'Ne pas conserver l\'identifiant de la personne connectée.',
	'traiter_enregistrement_option_anonymiser_label' => 'Anonymiser le formulaire',
	'traiter_enregistrement_option_anonymiser_variable_label' => 'Variable d\'anonymisation',
	'traiter_enregistrement_option_anonymiser_variable_explication' => 'Variable système à utiliser pour remplaçer l\'identifiant de l\'auteur. Nécessite une identification par PHP / Apache non intégrée nativement dans SPIP.',
	'traiter_enregistrement_option_auteur' => 'Utiliser les auteurs pour les formulaires',
	'traiter_enregistrement_option_auteur_explication' => 'Attribuer un ou plusieurs auteurs à un formulaire. Si cette option est activée, seuls les auteurs d’un formulaire pourront accéder à leurs données.',
	'traiter_enregistrement_option_effacement_delai_label' => 'Nombre de jours avant effacement',
	'traiter_enregistrement_option_effacement_label' => 'Effacer régulièrement les résultats les plus anciens',
	'traiter_enregistrement_option_identification_explication' => 'Quel procédé utiliser en priorité pour connaître la réponse précédemment apportée par l\'utilisateur·trice ?',
	'traiter_enregistrement_option_identification_label' => 'Identification',
	'traiter_enregistrement_option_invalider_explication' => 'Si les réponses à ce formulaire sont utilisées publiquement, vous pouvez rafraîchir le cache lors d’une nouvelle réponse.',
	'traiter_enregistrement_option_invalider_label' => 'Rafraîchir le cache',
	'traiter_enregistrement_option_ip_label' => 'Enregistrer les IPs (masquées après un délai de garde)',
	'traiter_enregistrement_option_moderation_label' => 'Modération',
	'traiter_enregistrement_option_modifiable_explication' => 'Modifiable : Les visiteurs peuvent modifier leurs réponses après coup.',
	'traiter_enregistrement_option_modifiable_label' => 'Réponses modifiables',
	'traiter_enregistrement_option_multiple_explication' => 'Multiple : Une même personne peut répondre plusieurs fois.',
	'traiter_enregistrement_option_multiple_label' => 'Réponses multiples',
	'traiter_enregistrement_option_resume_reponse_explication' => 'Cette chaîne sera utilisée pour afficher un résumé de chaque réponse dans les listes. Les champs comme <tt>@input_1@</tt> seront remplacés comme indiqué par l’aide mémoire ci-contre.',
	'traiter_enregistrement_option_resume_reponse_label' => 'Affichage résumé de la réponse',
	'traiter_enregistrement_titre' => 'Enregistrer les résultats',
	'traiter_enregistrement_option_remote_user_label' => 'Variable serveur : REMOTE_USER',
	'traiter_enregistrement_option_php_auth_user_label' => 'Variable serveur : PHP_AUTH_USER',

	// V
	'voir_exporter' => 'Exporter le formulaire',
	'voir_numero' => 'Formulaire numéro :',
	'voir_reponses' => 'Voir les réponses',
	'voir_traitements' => 'Traitements'
);
