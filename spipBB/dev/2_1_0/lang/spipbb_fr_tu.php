<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de https://trad.spip.net/tradlang_module/spipbb?lang_cible=fr_tu
// ** ne pas modifier le fichier **

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// A
	'a_propos_auteur_dpt' => 'Tout à propos de :',
	'admin_action_01_configuration' => 'Configuration',
	'admin_action_02_etat' => 'Etat des forums',
	'admin_action_ZZ_debug' => 'Debogage',
	'admin_action_effacer' => 'Messages rejetés',
	'admin_action_fromphorum' => 'Import de Phorum',
	'admin_action_fromphpbb' => 'Import de PhpBB',
	'admin_action_gere_ban' => 'Gestion des bans',
	'admin_action_gestion' => 'Gestion',
	'admin_action_inscrits' => 'Membres',
	'admin_action_swconfig' => 'Configuration',
	'admin_action_swforum' => 'Posts marqués',
	'admin_action_swlog' => 'Log du spam',
	'admin_action_swwords' => 'Gestion des mots',
	'admin_afficher_bouton_alerte_abus' => 'Afficher les boutons alerte Abus',
	'admin_afficher_bouton_rss' => 'Afficher les boutons RSS',
	'admin_age_forum' => 'Age du forum Ans/mois',
	'admin_avatar_affiche' => 'Accepter et afficher les avatars (oui par défaut en prem install)',
	'admin_avatar_taille_contact' => 'Taille des avatars (en pixels) sur page contact',
	'admin_avatar_taille_profil' => 'Taille des avatars (en pixels) sur page profil',
	'admin_avatar_taille_sujet' => 'Taille des avatars (en pixels) sur page sujets',
	'admin_average_posts' => 'Moyenne de messages/jour',
	'admin_average_users' => 'Moyenne d’inscriptions/jour',
	'admin_ban_email' => 'Gestion des adresses email bannies',
	'admin_ban_email_info' => 'Pour spécifier plus d’une adresse e-mail, sépare-les par des virgules. Pour spécifier un joker pour le nom d’utilisateur, utilise * ; par exemple *@hotmail.com',
	'admin_ban_email_none' => 'Aucune adresse bannie',
	'admin_ban_ip' => 'Gestion des adresses IP bannies',
	'admin_ban_ip_info' => 'Pour spécifier plusieurs IP ou noms de serveurs différents, sépare-les par des virgules. Pour spécifier un intervalle d’adresses IP, sépare le début et la fin avec un trait d’union (-), pour spécifier un joker, utilise une étoile (*)',
	'admin_ban_ip_none' => 'Aucune adresse bannie',
	'admin_ban_user' => 'Gestion des login bannis',
	'admin_ban_user_info' => 'Tu peux bannir plusieurs utilisateurs en une fois en utilisant la combinaison CTRL ou MAJ avec la souris ou le clavier',
	'admin_ban_user_none' => 'Aucun utilisateur',
	'admin_cat_01_general' => 'Administration',
	'admin_cat_outils' => 'Outils',
	'admin_cat_spam' => 'Anti Spam',
	'admin_config_prerequis' => 'Prérequis',
	'admin_config_spam_words' => 'Configuration de l’anti-spam',
	'admin_config_spipbb' => 'Activation de SpipBB',
	'admin_config_spipbb_info' => 'Cliquer sur Oui pour activer SpipBB',
	'admin_config_tables' => 'Configuration des tables de SpipBB',
	'admin_config_tables_erreur' => 'Problème avec les tables de SpipBB : @tables_erreur@ sont incorrectes (les tables @tables_ok@ semblent correctes).
Consulte la [documentation sur contrib.spip->https://contrib.spip.net/SpipBB-le-forum] ou le [support sur spipbb.spip-zone->http://spipbb.free.fr/spip.php?article11]',
	'admin_config_tables_ok' => 'Table de SpipBB correctement installée',
	'admin_date_ouverture' => 'Date d’ouverture',
	'admin_debug_log' => 'Fichier de log @log_name@',
	'admin_debug_metas' => 'SpipBB METAs',
	'admin_form_action' => 'Action',
	'admin_form_creer_categorie' => 'Créer une catégorie',
	'admin_form_creer_forum' => 'Créer un forum',
	'admin_form_deplacer' => 'Déplacer',
	'admin_form_descendre' => 'Descendre',
	'admin_form_editer' => 'Editer',
	'admin_form_messages' => ' ',
	'admin_form_monter' => 'Monter',
	'admin_form_sujets' => ' ',
	'admin_forums_affiche_membre_defaut' => 'Veux-tu afficher les membres dans la liste de membres lorsqu’il n’ont pas fait de choix ?<br />[ Non par défaut ]',
	'admin_forums_configuration' => 'Configuration de SpipBB',
	'admin_forums_configuration_avatar' => 'Gestion des avatars, réglage général',
	'admin_forums_configuration_options' => 'Options de SpipBB',
	'admin_forums_log_level' => 'Choix du niveau de logs produites par SpipBB.<br />[ 3 (maximum)- Par défaut ]',
	'admin_forums_log_level_0' => 'Pas de logs',
	'admin_forums_log_level_1' => 'Un peu de logs',
	'admin_forums_log_level_2' => 'Beaucoup de logs',
	'admin_forums_log_level_3' => 'Enormément de logs',
	'admin_id_mjsc' => 'N°',
	'admin_infos' => 'SpipBB - Administration - Récapitulatif',
	'admin_interface' => 'Options de l’interface',
	'admin_nombre_lignes_messages' => 'Nombre de lignes de messages',
	'admin_plugin_requis_erreur' => 'Plugin requis manquant',
	'admin_plugin_requis_erreur_balisesession' => 'Installe le plugin Balise SESSION et active le ! [Documentation ici->https://contrib.spip.net/?article1224], [Archive ZIP là->http://files.spip.org/spip-zone/balise_session.zip].',
	'admin_plugin_requis_erreur_cfg' => 'Installe le plugin CFG et active le ! [Documentation ici->https://contrib.spip.net/?article1605], [Archive ZIP là->http://files.spip.org/spip-zone/cfg.zip].',
	'admin_plugin_requis_erreur_s' => 'Les plugins requis suivants manquent. Active-les !',
	'admin_plugin_requis_ok' => 'Plugin requis installé',
	'admin_plugin_requis_ok_balisesession' => '[Plugin BALISE_SESSION->https://contrib.spip.net/?article1224] : fourni les informations sur les visiteurs authentifié.',
	'admin_plugin_requis_ok_cfg' => '[Plugin CFG->https://contrib.spip.net/?article1605] : fourni des fonctions et des balises.',
	'admin_sous_titre' => 'Accèder au panneau d’administration des forums avec SpipBB',
	'admin_spip_config_forums' => 'Configuration de SPIP :',
	'admin_spip_forums_ok' => 'Les forums publics sont bien activés dans SPIP',
	'admin_spip_forums_warn' => '<p>{{Attention}} : tes forums sont désactivés par défaut, il est recommandé d’utiliser la publication immédiate : [voir ici->@config_contenu@].</p><p>Sinon tu devras les activer articles par articles.</p>',
	'admin_spip_mots_cles_ok' => 'Les mot-clefs sont bien activés dans SPIP',
	'admin_spip_mots_cles_warn' => '<p>{{Attention}} : Les mots-clés sont pas actifs dans SPIP, tu ne pourras pas utiliser les fonctions avancées associées.</p><p>Il  est recommandé de les activer : [voir ici->@configuration@].</p>',
	'admin_spip_mots_forums_ok' => 'Les mot-clefs associés aux forums sont bien activés dans SPIP',
	'admin_spip_mots_forums_warn' => '<p>{{Attention}} : Les mots-clés dans les forums du site public sont pas actifs dans SPIP, tu ne pourras pas utiliser les fonctions avancées associées.</p><p>Il est recommandé de permettre leur utilisation : [voir ici->@configuration@].</p>',
	'admin_spipbb_release' => 'Version de SpipBB',
	'admin_statistique' => 'Information',
	'admin_surtitre' => 'Gérer les forums',
	'admin_temps_deplacement' => 'Temps requis avant déplacement par un admin',
	'admin_titre' => 'Administration SpipBB',
	'admin_titre_page_spipbb_admin' => 'Gestion des forums',
	'admin_titre_page_spipbb_admin_anti_spam_config' => 'Configuration générale du filtrage du spam',
	'admin_titre_page_spipbb_admin_anti_spam_forum' => 'Posts marqués',
	'admin_titre_page_spipbb_admin_anti_spam_log' => 'Log du spam',
	'admin_titre_page_spipbb_admin_anti_spam_words' => 'Filtrage de mots',
	'admin_titre_page_spipbb_admin_debug' => 'Debogage',
	'admin_titre_page_spipbb_admin_etat' => 'SpipBB - Administration - Récapitulatif',
	'admin_titre_page_spipbb_admin_gere_ban' => 'Gestion du banissement',
	'admin_titre_page_spipbb_admin_migre' => 'Import de @nom_base@',
	'admin_titre_page_spipbb_configuration' => 'Configuration de SpipBB',
	'admin_titre_page_spipbb_effacer' => 'Gestion des messages rejetés',
	'admin_titre_page_spipbb_inscrits' => 'Gestion des membres',
	'admin_titre_page_spipbb_sujet' => 'Édition d’un fil',
	'admin_total_posts' => 'Nombre total de messages',
	'admin_total_users' => 'Nombre de membres',
	'admin_total_users_online' => 'Membres en ligne',
	'admin_unban_email_info' => 'Tu peux débannir plusieurs adresses en une fois en utilisant la combinaison CTRL ou MAJ avec la souris ou le clavier',
	'admin_unban_ip_info' => 'Tu peux débannir plusieurs adresses en une fois en utilisant la combinaison CTRL ou MAJ avec la souris ou le clavier',
	'admin_unban_user_info' => 'Tu peux débannir plusieurs utilisateurs en une fois en utilisant la combinaison CTRL ou MAJ avec la souris ou le clavier',
	'admin_valeur' => 'Valeur',
	'aecrit' => 'a écrit :',
	'alerter_abus' => 'Signaler ce message comme abusif/injurieux...',
	'alerter_sujet' => 'Message abusif',
	'alerter_texte' => 'Nous attirons ton attention sur le message suivant :',
	'annonce' => 'Annonce',
	'annonce_dpt' => 'Annonce : ',
	'anonyme' => 'Anonyme',
	'auteur' => 'Auteur',
	'avatar' => 'Avatar',

	// B
	'bouton_select_all' => 'Tout sélectionner',
	'bouton_speciaux_sur_skels' => 'Configurer les boutons spécifiques sur les squelettes publics',
	'bouton_unselect_all' => 'Tout dé-sélectionner',

	// C
	'champs_obligatoires' => 'Les champs marqués d’une * sont obligatoires.',
	'chercher' => 'Chercher',
	'choix_mots_annonce' => 'Faire une annonce',
	'choix_mots_creation' => 'Si tu veux créer <strong>automatiquement</strong> les mot-clés dédiés à SpipBB, appuye sur ce bouton. Ces mot-clefs peuvent être modifiés ou supprimés ultérieurement...',
	'choix_mots_creation_submit' => 'Configuration auto des mots-clefs',
	'choix_mots_ferme' => 'Pour fermer un fil',
	'choix_mots_postit' => 'Mettre en post-it',
	'choix_mots_selection' => 'Le groupe de mot doit contenir trois mot-clefs. Normalement, le plugin les a créé au moment de son installation. SpipBB utilise en général les mots {ferme}, {annonce} et {postit}, mais tu peux en choisir d’autres.',
	'choix_rubrique_creation' => 'Si tu veux créer <strong>automatiquement</strong> le secteur contenant les forums SpipBB et un premier forum vide, appuye sur ce bouton. Ce forum et la hiérarchie créés peuvent être modifiés ou supprimés ultérieurement...',
	'choix_rubrique_creation_submit' => 'Configuration auto du secteur',
	'choix_rubrique_selection' => 'Sélectionne un secteur qui sera la base de tes forums. Chaque sous-rubrique de ce secteur sera un groupe de forums, chaque article publié ouvrira un forum.',
	'choix_squelettes' => 'Tu peux en choisir d’autres, mais les fichiers qui remplacent groupeforum.html et filforum.html doivent exister !',
	'citer' => 'Citer',
	'cocher' => 'cocher',
	'col_avatar' => 'Avatar',
	'col_date_crea' => 'Date inscription',
	'col_marquer' => 'Marquer',
	'col_signature' => 'Signature',
	'config_affiche_champ_extra' => 'Afficher le champ : @nom_champ@',
	'config_affiche_extra' => 'Afficher ces champs dans les squelettes',
	'config_champs_auteur' => 'Champs SPIPBB',
	'config_champs_auteurs_plus' => 'Gestion champs auteurs supplémentaires',
	'config_champs_requis' => 'Les champs nécessaires à SpipBB',
	'config_choix_mots' => 'Choisir le groupe de mot-clés',
	'config_choix_rubrique' => 'Choisir la rubrique contenant les forums spipBB',
	'config_choix_squelettes' => 'Choisir les squelettes utilisés',
	'config_orig_extra' => 'Quel support utiliser pour les champs supplémentaires',
	'config_orig_extra_info' => 'Infos champs EXTRA ou autre table, table auteurs_profils.',
	'config_spipbb' => 'Configuration de base de spipBB pour permettre le fonctionnement des forums avec ce plugin.',
	'contacter' => 'Contacter',
	'contacter_dpt' => 'Contacter : ',
	'creer_categorie' => 'Créer Nouvelle Catégorie',
	'creer_forum' => 'Créer Nouveau Forum',

	// D
	'dans_forum' => 'dans le forum',
	'deconnexion_' => 'Déconnexion ',
	'deplacer' => 'Déplacer',
	'deplacer_confirmer' => 'Confirmer le déplacement',
	'deplacer_dans_dpt' => 'À déplacer dans le forum :',
	'deplacer_sujet_dpt' => 'Déplacement de :',
	'deplacer_vide' => 'Pas d’autre forum : déplacement impossible.',
	'dernier' => ' Dernier',
	'dernier_membre' => 'Dernier membre enregistré : ',
	'derniers_messages' => 'Derniers Messages',
	'diviser' => 'Diviser',
	'diviser_confirmer' => 'Confirmer la séparation des messages',
	'diviser_dans_dpt' => 'À mettre dans le forum :',
	'diviser_expliquer' => 'A l’aide du formulaire ci-dessous, tu pourras séparer ce fil en deux, soit : en sélectionnant les messages individuellement ; soit en choisissant le message à partir duquel il faut les diviser en deux.',
	'diviser_selection_dpt' => 'Sélection :',
	'diviser_separer_choisis' => 'Séparer les messages sélectionnés',
	'diviser_separer_suite' => 'Séparer à partir du message sélectionné',
	'diviser_vide' => 'Pas d’autre forum : division impossible.',

	// E
	'ecrirea' => 'Ecrire un email à',
	'effacer' => 'Effacer',
	'email' => 'E-mail',
	'en_ligne' => 'Qui est en ligne ?',
	'en_rep_sujet_' => ' : : : Sujet : ',
	'en_reponse_a' => 'En réponse au message',
	'etplus' => '... et plus ...',
	'extra_avatar_saisie_url' => 'URL de ton avatar (http://... ...)',
	'extra_avatar_saisie_url_info' => 'URL de l’avatar du visiteur',
	'extra_date_crea' => 'Date de premiere saisie profil SpipBB',
	'extra_date_crea_info' => 'Date de premiere saisie profil SpipBB',
	'extra_emploi' => 'Emploi',
	'extra_localisation' => 'Localisation',
	'extra_loisirs' => 'Loisirs',
	'extra_nom_aim' => 'Contacts chat (AIM)',
	'extra_nom_msnm' => 'Contacts chat (MSN Messenger)',
	'extra_nom_yahoo' => 'Contacts chat (Yahoo)',
	'extra_numero_icq' => 'Contacts chat (ICQ)',
	'extra_refus_suivi_thread' => '(refus suivi) Ne pas modifier !',
	'extra_refus_suivi_thread_info' => 'Liste des threads pour lesquels on ne souhaite plus recevoir de notification',
	'extra_signature_saisie_texte' => 'Saisir ici le texte de ta signature',
	'extra_signature_saisie_texte_info' => 'Court texte de signature des messages',
	'extra_visible_annuaire' => 'Apparaitre dans la liste des Inscrits (publique)',
	'extra_visible_annuaire_info' => 'Permet de refuser l’affichage dans l’annuaire des inscrits en zone publique',

	// F
	'fiche_contact' => 'Fiche Contact',
	'fil_annonce_annonce' => 'Passer le Sujet en Annonce',
	'fil_annonce_desannonce' => 'Supprimer le mode Annonce',
	'fil_deplace' => 'Déplacer ce fil',
	'filtrer' => 'Filtrer',
	'forum' => 'Forums',
	'forum_annonce_annonce' => 'Poser le marquage d’annonce',
	'forum_annonce_desannonce' => 'Supprimer le marquage d’annonce',
	'forum_dpt' => 'Forum : ',
	'forum_ferme' => 'Ce forum est fermé',
	'forum_ferme_texte' => 'Ce forum est fermé. Tu ne peux plus y poster.',
	'forum_maintenance' => 'Ce forum est fermé pour maintenance',
	'forum_ouvrir' => 'Ouvrir ce Forum',
	'forums_categories' => 'Divers',
	'forums_spipbb' => 'Forums SpipBB',
	'forums_titre' => 'Mon premier forum créé',
	'fromphpbb_erreur_db_phpbb_config' => 'Impossible de lire la configuration dans la base phpBB',
	'fromphpbb_migre_categories' => 'Import des catégories',
	'fromphpbb_migre_categories_dans_rub_dpt' => 'Implantation des forums dans la rubrique :',
	'fromphpbb_migre_categories_forum' => 'Forum',
	'fromphpbb_migre_categories_groupe' => 'Groupe',
	'fromphpbb_migre_categories_impossible' => 'Impossible de récupérer les catégories',
	'fromphpbb_migre_categories_kw_ann_dpt' => 'Les annonces recevront le mot-clef :',
	'fromphpbb_migre_categories_kw_ferme_dpt' => 'Les sujets clos recevront le mot-clef :',
	'fromphpbb_migre_categories_kw_postit_dpt' => 'Les post-its recevront le mot-clef :',
	'fromphpbb_migre_existe_dpt' => 'existe :',
	'fromphpbb_migre_thread' => 'Import des topics et des posts',
	'fromphpbb_migre_thread_ajout' => 'Ajout thread',
	'fromphpbb_migre_thread_annonce' => 'Annonce',
	'fromphpbb_migre_thread_existe_dpt' => 'Forum existe :',
	'fromphpbb_migre_thread_ferme' => 'Fermé',
	'fromphpbb_migre_thread_impossible_dpt' => 'Impossible de récupérer les posts :',
	'fromphpbb_migre_thread_postit' => 'Post-it',
	'fromphpbb_migre_thread_total_dpt' => 'Nombre total de topics et de posts ajoutés :',
	'fromphpbb_migre_utilisateurs' => 'Import des utilisateurs',
	'fromphpbb_migre_utilisateurs_admin_restreint_add' => 'Ajout admin restreint',
	'fromphpbb_migre_utilisateurs_admin_restreint_already' => 'Déjà admin restreint',
	'fromphpbb_migre_utilisateurs_impossible' => 'Impossible de récupérer les utilisateurs',
	'fromphpbb_migre_utilisateurs_total_dpt' => 'Nombre total d’utilisateurs ajoutés :',

	// H
	'haut_page' => 'Haut de page',

	// I
	'icone_ferme' => 'Fermer',
	'import_base' => 'Nom de la base :',
	'import_choix_test' => 'Réaliser un import de test (choix par défaut) : ',
	'import_choix_test_titre' => 'Import à blanc ou réel',
	'import_erreur_db' => 'Impossible de se connecter à la base @nom_base@',
	'import_erreur_db_config' => 'Impossible de lire la configuration dans la base @nom_base@',
	'import_erreur_db_rappel_connexion' => 'Impossible de se reconnecter à la base @nom_base@',
	'import_erreur_db_spip' => 'Impossible de se connecter à la base SPIP',
	'import_erreur_forums' => 'Impossible de récupérer les forums',
	'import_fichier' => 'Fichier de configuration @nom_base@ trouvé :',
	'import_host' => 'Nom/adresse du serveur',
	'import_login' => 'Identifiant :',
	'import_parametres_base' => 'Choisis soit le chemin vers le fichier de configuration de @nom_base@, soit de renseigner les paramètres d’accès à la base contenant les forums de @nom_base@ :',
	'import_parametres_rubrique' => 'Choisis la rubrique dans laquelle seront importés les forums de @nom_base@',
	'import_parametres_titre' => 'Informations sur la base @nom_base@',
	'import_password' => 'Mot de passe :',
	'import_prefix' => 'Préfixe des tables :',
	'import_racine' => 'Chemin vers @nom_base@ (avatars) :',
	'import_table' => 'Table de configuration @nom_base@ trouvée :',
	'import_titre' => 'Import d’un forum @nom_base@',
	'import_titre_etape' => 'Import d’un forum  @nom_base@ - étape',
	'info' => 'Informations',
	'info_annonce_ferme' => 'Etat Annonce / Fermer',
	'info_confirmer_passe' => 'Confirmer ce nouveau mot de passe :',
	'info_ferme' => 'Etat Fermé',
	'info_inscription_invalide' => 'Inscription impossible',
	'info_plus_cinq_car' => 'plus de 5 caractères',
	'infos_refus_suivi_sujet' => 'Ne plus suivre les sujets',
	'infos_suivi_forum_par_inscription' => 'Suivi du forum par inscription',
	'inscription' => 'Inscription',
	'inscrit_le' => 'Inscrit le',
	'inscrit_le_dpt' => 'Inscrit le :',
	'inscrit_s' => 'Inscrits',
	'ip_adresse_autres' => 'Autres adresses IP à partir desquelles cet auteur a posté',
	'ip_adresse_membres' => 'Membres ayant posté de cette adresse IP',
	'ip_adresse_post' => 'Adresse IP de ce message',
	'ip_informations' => 'Informations sur une adresse IP et un auteur',

	// L
	'le' => 'Le',
	'liste_des_messages' => 'Liste des messages',
	'liste_inscrits' => 'Liste des membres',
	'login' => 'Connexion',

	// M
	'maintenance' => 'Maintenance',
	'maintenance_fermer' => 'a fermé l’article/forum :',
	'maintenance_pour' => 'pour MAINTENANCE.',
	'membres_en_ligne' => 'membres en ligne',
	'membres_inscrits' => 'membres inscrits',
	'membres_les_plus_actifs' => 'Membres les plus actifs',
	'message' => 'Message',
	'message_s' => '>Messages',
	'message_s_dpt' => 'Messages : ',
	'messages' => 'Messages',
	'messages_anonymes' => 'rendre anonymes',
	'messages_derniers' => 'Derniers Messages',
	'messages_laisser_nom' => 'laisser le nom',
	'messages_supprimer_titre_dpt' => 'Pour les messages :',
	'messages_supprimer_tous' => 'les supprimer',
	'messages_voir_dernier' => 'Voir le dernier message',
	'messages_voir_dernier_s' => 'Voir les derniers messages',
	'moderateur' => 'Modérateur',
	'moderateur_dpt' => 'Modérateur : ',
	'moderateurs' => 'Modérateur(s)',
	'moderateurs_dpt' => 'Modérateurs : ',
	'modif_parametre' => 'Modifie tes paramètres',
	'mot_annonce' => 'Annonce
_ Une annonce est située en tête de forum sur toutes les pages.',
	'mot_ferme' => 'Fermé
-* Lorsqu’un article-forum a ce mot-clef, seul les modérateurs peuvent y ajouter des messages.
-* Lorsqu’un sujet de forum est ferm&ecute ;, seuls les modérateurs peuvent y ajouter des réponses.',
	'mot_groupe_moderation' => 'Goupe de mot-clefs utilisé pour la modération de SpipBB',
	'mot_postit' => 'Postit
 _ Un postit est situé en dessous des annonces, avant les messages ordinaires. Il n’apparaît qu’une seule fois dans la liste.',

	// N
	'no_message' => 'Aucun sujet ou message ne correspond à tes critères de recherche',
	'nom_util' => 'Nom d’utilisateur',
	'non' => 'Non',

	// O
	'ordre_croissant' => 'Croissant',
	'ordre_decroissant' => 'Décroissant',
	'ordre_dpt' => 'Ordre :',
	'oui' => 'Oui',

	// P
	'pagine_page_' => ' .. page ',
	'pagine_post_' => ' réponse',
	'pagine_post_s' => ' réponses',
	'pagine_sujet_' => ' sujet',
	'pagine_sujet_s' => ' sujets',
	'par_' => 'par ',
	'plugin_auteur' => 'La SpipBB Team : [voir la liste des contributeurs sur contrib.spip->https://contrib.spip.net/Plugin-Forum-SpipBB#contributeurs]',
	'plugin_description' => 'Le plugin SpipBB permet :
-* De gérer de façon centralisée les forums de SPIP (interface privée),
-* D’utiliser un secteur comme base d’un groupe de forums comme les « Bulletin Board » tels que phpBB. Dans ce secteur, les sous-rubriques sont des groupes de forums, les articles des forums, chaque message dans le forum d’un article y démarre un thread.

{{Consulte :}}
-* •[l’aide et support sur spipbb.spip-zone.info->http://spipbb.free.fr/spip.php?article11],
-* •[La documentation sur contrib.spip->https://contrib.spip.net/SpipBB-le-forum].

_ {{Plugin spipbb en cours de développement. Tu l’utilises à tes risques et périls}}

_ [Accès au panneau d’administration-> .?exec=spipbb_configuration]',
	'plugin_licence' => 'Distribué sous licence GPL',
	'plugin_lien' => '[Consulter la documentation du plugin sur contrib.spip->https://contrib.spip.net/SpipBB-le-forum]',
	'plugin_mauvaise_version' => 'Cette version du plugin n’est pas compatible avec ta version de SPIP !',
	'plugin_nom' => 'SpipBB : Gestion des forums de SPIP',
	'post_aucun_pt' => 'aucun !',
	'post_efface_lui' => 'Ce sujet comprend @nbr_post@ message(s). Effacés avec lui !\\n',
	'post_ip' => 'Messages posté à partie de l’adresse IP',
	'post_propose' => 'Message proposé',
	'post_rejete' => 'Message rejeté',
	'post_titre' => ' : : : Titre : ',
	'post_verifier_sujet' => 'Vérifier ce sujet',
	'poste_valide' => 'Post(s) à valider ...',
	'poster_date_' => 'Posté le : ',
	'poster_message' => 'Poste ton message',
	'postit' => 'Postit',
	'postit_dpt' => 'Postit : ',
	'posts_effaces' => 'Messages effacés !',
	'posts_refuses' => 'Messages refusés, à effacer !',
	'previsualisation' => 'Prévisualisation',
	'profil' => 'Profil de',

	// R
	'raison_clic' => 'clique ici',
	'raison_texte' => 'Pour en connaitre la raison',
	'recherche' => 'Recherche',
	'recherche_elargie' => 'Recherche élargie',
	'redige_post' => 'Ecrire message',
	'reglement' => '<p>Les administrateurs et modérateurs de ce forum s’efforceront de supprimer
    ou éditer tous les messages à caractère répréhensible
    aussi rapidement que possible. Toutefois, il leur est impossible de passer en revue tous
    les messages. Tu admets donc que tous les messages postés sur ces forums
    expriment la vue et opinion de leurs auteurs respectifs, et non pas des administrateurs,
    ou modérateurs, ou webmestres (excepté les messages postés par
    eux-même) et par conséquent ne peuvent pas être tenus pour responsables.</p>

    <p>Tu consens à ne pas poster de messages injurieux, obscènes,
    vulgaires, diffamatoires, menaçants, sexuels ou tout autre message qui violerait les lois applicables. Le faire peut te conduire à être banni
    immédiatement de façon permanente (et ton fournisseur d’accès
    à internet en sera informé). L’adresse IP de chaque message est enregistrée afin d’aider à faire respecter ces conditions.
    Tu es d’accord sur le fait que le webmestre, l’administrateur
    et les modérateurs de ce forum ont le droit de supprimer, éditer,
    déplacer ou verrouiller n’importe quel sujet de discussion à tout moment.
    En tant qu’utilisateur, tu es d’accord sur le fait que toutes les informations
    que tu donneras ci-après seront stockées dans une base de données.
    Cependant, ces informations ne seront divulguées à aucune tierce personne
    ou société sans ton accord. Le webmestre, l’administrateur,
    et les modérateurs ne peuvent pas être tenus pour responsables si une
    tentative de piratage informatique conduit à l’accès de ces données.</p>
    <p>Ce forum utilise les cookies pour stocker des informations sur ton ordinateur.
    Ces cookies ne contiendront aucune information que tu auras entré ci-après,
    ils servent uniquement à améliorer le confort d’utilisation.
    L’adresse e-mail est uniquement utilisée afin de confirmer les détails
    de ton enregistrement ainsi que ton mot de passe (et aussi pour t’envoyer un nouveau
    mot de passe dans la cas où tu l’oublierais).</p>
    <p>En t’enregistrant, tu te portes garant du fait d’être en accord avec le règlement ci-dessus.</p>',
	'repondre' => 'Répondre',
	'reponse_s_' => 'Réponses',
	'resultat_s_pour_' => ' résultats pour ',
	'retour_forum' => 'Retour à l’accueil du forum',

	// S
	's_abonner_a' => 'RSS . S’abonner à : ',
	'secteur_forum' => 'RACINE',
	'selection_efface' => 'Effacer la sélection ..',
	'selection_tri_dpt' => 'Sélectionner la méthode de tri :',
	'sign_admin' => '{{Cette page est uniquement accessible aux responsables du site.}}<p>Elle donne accès à la configuration du plugin « {{<a href="https://contrib.spip.net/Plugin-Forum-SpipBB#contributeurs" class="copyright">SpipBB</a>}} » ainsi qu’à la gestion des forums du site.</p><p>Version : @version@ @distant@</p><p>Consulte :
_ • [La documentation sur contrib.spip->https://contrib.spip.net/?article2460]
_ • [L’aide et support sur spipbb.spip-zone.info->http://spipbb.free.fr/spip.php?article11]</p>@reinit@',
	'sign_maj' => '<br />Version plus récente disponible : @version@',
	'sign_ok' => 'à jour.',
	'sign_reinit' => '<p>Ré-initialisation :
_ • [de tout le plugin->@plugin@]</p>',
	'sign_tempo' => 'Réalisé avec <a href="https://contrib.spip.net/Plugin-Forum-SpipBB#contributeurs" class="copyright">SpipBB</a>',
	'signature' => 'Signature',
	'sinscrire' => 'S’inscrire',
	'site_propose' => 'Site proposé par @auteur_post@',
	'site_web' => 'Site web',
	'squelette_filforum' => 'Base de squelette pour les fils de discussions :',
	'squelette_groupeforum' => 'Base de squelette pour les groupes de discussions :',
	'statut' => 'Statut',
	'statut_admin' => 'Administrateur',
	'statut_redac' => 'Rédacteur',
	'statut_visit' => 'Membre',
	'sujet' => 'Sujet',
	'sujet_auteur' => 'Auteur',
	'sujet_clos_texte' => 'Ce sujet est clos, tu ne peux pas y poster.',
	'sujet_clos_titre' => 'Sujet Clos',
	'sujet_dpt' => 'Sujet : ',
	'sujet_ferme' => 'Sujet : fermé',
	'sujet_nombre' => 'Nombre de Sujets',
	'sujet_nouveau' => 'Nouveau sujet',
	'sujet_rejete' => 'Sujet rejeté',
	'sujet_repondre' => 'Répondre au sujet',
	'sujet_s' => 'Sujets',
	'sujet_valide' => 'Sujet à valider',
	'sujets' => 'Sujets',
	'sujets_aucun' => 'Pas de sujet dans ce forum pour l’instant',
	'support_extra_normal' => 'extra',
	'support_extra_table' => 'table',
	'supprimer' => 'Supprimer',
	'sw_admin_can_spam' => 'Les admins sont autorisés',
	'sw_admin_no_spam' => 'Pas de spam',
	'sw_ban_ip_titre' => 'Bannir l’IP en même temps',
	'sw_config_exceptions' => 'Tu peux activer des exceptions pour des utilisateurs privilégiés ici. Ils pourront quand même publier avec des mots bannis.',
	'sw_config_exceptions_titre' => 'Gestion des exceptions',
	'sw_config_generale' => 'Configuration actuelle du filtrage :',
	'sw_config_generale_titre' => 'Configuration générale du filtrage du spam',
	'sw_config_warning' => 'Tu peux choisir le texte du MP envoyé si tu actives cette option (maxi 255 caractères).',
	'sw_config_warning_titre' => 'Configuration des avertissements par message privé',
	'sw_disable_sw_titre' => '<strong>Active le filtrage</strong><br />Si tu dois te passer du filtrage,<br />clique sur Non',
	'sw_modo_can_spam' => 'Les modérateurs sont autorisés',
	'sw_nb_spam_ban_titre' => 'Nombre de spams avant banissement',
	'sw_pm_spam_warning_message' => 'Ceci est un avertissement. Tu as essayé de poster un message analysé comme du spam sur ce site web. Merci d’éviter de recommencer.',
	'sw_pm_spam_warning_titre' => 'Attention.',
	'sw_send_pm_warning' => '<strong>Envoie un MP à l’utilisateur</strong><br />lorsqu’il poste un message avec un mot interdit',
	'sw_spam_forum_titre' => 'Gestion des messages de spam',
	'sw_spam_titre' => 'Filtrage du spam',
	'sw_spam_words_action' => 'A partir de cette page, tu peux ajouter, éditer et supprimer des mots associés à du spam. Le caractère (*) est accepté dans le mot. Par exemple : {{*tes*}} capturera {détestable}, {{tes*}} capturera {tester}, {{*tes}} capturera {portes}.',
	'sw_spam_words_mass_add' => 'Copier-coller ou saisir tes mots dans cette zone. Séparer chaque mot par une virgule, deux points ou un retour à la ligne.',
	'sw_spam_words_titre' => 'Filtrage de mots',
	'sw_spam_words_url_add' => 'Saisir l’URL d’un fichier contenant une liste de mots formatée comme ci-dessus. Exemple : http://spipbb.free.fr/IMG/csv/spamwordlist.csv .',
	'sw_warning_from_admin' => 'Choisir l’admin auteur du message envoyé',
	'sw_warning_pm_message' => 'Texte du message privé',
	'sw_warning_pm_titre' => 'Sujet du message privé',
	'sw_word' => 'Mot',

	// T
	'title_ferme' => 'Fermer le forum/article',
	'title_libere' => 'Réouvrir le forum/article',
	'title_libere_maintenance' => 'Libérer le verrou de Maintenance',
	'title_maintenance' => 'Fermer le forum/article pour Maintenance',
	'title_sujet_ferme' => 'Fermer ce sujet',
	'title_sujet_libere' => 'Réouvrir ce sujet',
	'titre_spipbb' => 'SpipBB',
	'total_membres' => 'Nous avons un total de ',
	'total_messages_membres' => 'Nos membres ont posté un total de ',
	'tous' => 'Tous',
	'tous_forums' => 'Tous les forums',
	'trier' => 'Trier',
	'trouver_messages_auteur_dpt' => 'Trouver tous les messages de :',

	// V
	'visible_annuaire_forum' => 'Apparaître dans la liste des Inscrits',
	'visites' => 'Vu',
	'voir' => 'VOIR',
	'votre_bio' => 'Courte biographie en quelques mots.',
	'votre_email' => 'Ton adresse email',
	'votre_nouveau_passe' => 'Nouveau mot de passe',
	'votre_signature' => 'Ta signature',
	'votre_site' => 'Le nom de ton site',
	'votre_url_avatar' => 'URL de ton Avatar (http://...)',
	'votre_url_site' => 'L’adresse (URL) de ton site'
);
