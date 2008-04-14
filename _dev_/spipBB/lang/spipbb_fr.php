<?php
#----------------------------------------------------------#
#  Plugin  : spipbb - Licence : GPL                        #
#  File    : spipbb_fr : fichier de langue                 #
#  Authors : Chryjs, 2007 et als                           #
#  http://www.spip-contrib.net/Plugin-SpipBB#contributeurs #
#  Contact : chryjs!@!free!.!fr                            #
#----------------------------------------------------------#

// This is a SPIP-forum module file  --  Ceci est un fichier module de SPIP-forum

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// A

	// Les menus d'admin
	'admin_cat_01_general' => 'Administration',
	'admin_cat_outils' => 'Outils',
	'admin_cat_spam' => 'Anti Spam',
	'admin_action_01_configuration' => 'Configuration',
	'admin_action_02_etat' => 'Etat des forums',
	'admin_action_effacer' => 'Messages rejet&eacute;s',
	'admin_action_fromphpbb' => 'Import de PhpBB',
	'admin_action_fromphorum' => 'Import de Phorum',
	'admin_action_gere_ban' => 'Gestion des bans',
	'admin_action_gestion' => 'Gestion',
	'admin_action_inscrits' => 'Membres',
	'admin_action_swconfig' => 'Configuration',
	'admin_action_swforum' => 'Posts marqu&eacute;s',
	'admin_action_swlog' => 'Log du spam',
	'admin_action_swwords' => 'Gestion des mots',
	'admin_action_ZZ_debug' => 'Debogage',

	'admin_titre_page_spipbb_admin' => 'Gestion des forums',
	'admin_titre_page_spipbb_admin_anti_spam_config' => 'Configuration g&eacute;n&eacute;rale du filtrage du spam',
	'admin_titre_page_spipbb_admin_anti_spam_forum' => 'Posts marqu&eacute;s',
	'admin_titre_page_spipbb_admin_anti_spam_log' => 'Log du spam',
	'admin_titre_page_spipbb_admin_anti_spam_words' => 'Filtrage de mots',
	'admin_titre_page_spipbb_admin_debug' => 'Debogage',
	'admin_titre_page_spipbb_admin_etat' => 'SpipBB - Administration - R&eacute;capitulatif',
	'admin_titre_page_spipbb_admin_migre' => 'Import de @nom_base@',
	'admin_titre_page_spipbb_admin_gere_ban' => 'Gestion du banissement',
	'admin_titre_page_spipbb_configuration' => 'Configuration de SpipBB',
	'admin_titre_page_spipbb_effacer' => 'Gestion des messages rejet&eacute;s',
	'admin_titre_page_spipbb_inscrits' => 'Gestion des membres',

	// Interface d'admin
	'admin_afficher_bouton_alerte_abus' => 'Afficher les boutons alerte Abus',
	'admin_affichier_bouton_rss' => 'Afficher les boutons RSS',
	'admin_age_forum' => 'Age du forum Ans/mois',
	'admin_avatar_affiche' => 'Accepter et afficher les avatars (oui par d&eacute;faut en prem install)',
	'admin_avatar_taille_contact' => 'Taille des avatars (en pixels) sur page contact',
	'admin_avatar_taille_profil' => 'Taille des avatars (en pixels) sur page profil',
	'admin_avatar_taille_sujet' => 'Taille des avatars (en pixels) sur page sujets',
	'admin_average_posts'=> 'Moyenne de messages/jour',
	'admin_average_users' => 'Moyenne d\'inscriptions/jour',
	'admin_ban_email' => 'Gestion des adresses email bannies',
	'admin_ban_email_info' => 'Pour sp&eacute;cifier plus d\'une adresse e-mail, s&eacute;parez-les par des virgules. Pour sp&eacute;cifier un joker pour le nom d\'utilisateur, utilisez * ; par exemple *@hotmail.com',
	'admin_ban_email_none' => 'Aucune adresse bannie',
	'admin_ban_ip' => 'Gestion des adresses IP bannies',
	'admin_ban_ip_info' => 'Pour sp&eacute;cifier plusieurs IP ou noms de serveurs diff&eacute;rents, s&eacute;parez-les par des virgules. Pour sp&eacute;cifier un intervalle d\'adresses IP, s&eacute;parez le d&eacute;but et la fin avec un trait d\'union (-), pour sp&eacute;cifier un joker, utilisez une &eacute;toile (*)',
	'admin_ban_ip_none' => 'Aucune adresse bannie',
	'admin_ban_user' => 'Gestion des login bannis',
	'admin_ban_user_info' => 'Vous pouvez bannir plusieurs utilisateurs en une fois en utilisant la combinaison CTRL ou MAJ avec la souris ou le clavier',
	'admin_ban_user_none' => 'Aucun utilisateur',
	'admin_config_prerequis' => 'Pr&eacute;requis',
	'admin_config_spam_words' => 'Configuration de l\'anti-spam',
	'admin_config_spipbb' => 'Activation de SpipBB',
	'admin_config_spipbb_info' => 'Cliquer sur Oui pour activer SpipBB',
	'admin_config_tables' => 'Configuration des tables de SpipBB',
	'admin_config_tables_ok' => 'Les tables de SpipBB sont correctement install&eacute;es (@tables_ok@)',
	'admin_config_tables_erreur' => 'Probl&egrave;me avec les tables de SpipBB : @tables_erreur@ sont incorrectes (les tables @tables_ok@ semblent correctes).
	Consultez la [documentation sur Spip-Contrib->http://www.spip-contrib.net/SpipBB-le-forum] ou le [support sur spipbb.spip-zone->http://spipbb.spip-zone.info/spip.php?article11]',
	'admin_date_ouverture' => 'Date d\'ouverture',
	'admin_debug_metas' => 'SpipBB METAs',
	'admin_debug_log' => 'Fichier de log @log_name@',
	'admin_form_action' => 'Action',
	'admin_form_creer_categorie' => 'Cr&eacute;er une cat&eacute;gorie',
	'admin_form_creer_forum' => 'Cr&eacute;er un forum',
	'admin_form_deplacer' => 'D&eacute;placer',
	'admin_form_descendre' => 'Descendre',
	'admin_form_editer' => 'Editer',
	'admin_form_messages' => '&nbsp;',
	'admin_form_monter' => 'Monter',
	'admin_form_sujets' => '&nbsp;',
	'admin_forums_affiche_membre_defaut' => 'Voulez-vous afficher les membres dans la liste de membres lorsqu\'il n\'ont pas fait de choix ?<br />[ Non par d&eacute;faut ]',
	'admin_forums_configuration' => 'Configuration de SpipBB',
	'admin_forums_configuration_avatar' => 'Gestion des avatars, r&eacute;glage g&eacute;n&eacute;ral',
	'admin_forums_configuration_options' => 'Options de SpipBB',
	'admin_forums_log_level' => 'Choix du niveau de logs produites par SpipBB.<br />[ 3 (maximum)- Par d&eacute;faut ]',
	'admin_forums_log_level_0' => 'Pas de logs',
	'admin_forums_log_level_1' => 'Un peu de logs',
	'admin_forums_log_level_2' => 'Beaucoup de logs',
	'admin_forums_log_level_3' => 'Enorm&eacute;ment de logs',

	'admin_id_mjsc' => 'N&deg;',
	'admin_infos' => 'SpipBB - Administration - R&eacute;capitulatif',
	'admin_interface' => 'Options de l\'interface',
	'admin_nombre_lignes_messages' =>'Nombre de lignes de messages',
	'admin_plugin_requis_erreur' => 'Le plugin requis suivant manque. Activez-le !',
	'admin_plugin_requis_erreur_s' => 'Les plugins requis suivants manquent. Activez-les !',
	'admin_plugin_requis_erreur_cfg' => 'Installez le plugin CFG et activez le ! [Documentation ici->http://www.spip-contrib.net/?article1605], [Archive ZIP l&agrave;->http://files.spip.org/spip-zone/cfg.zip].',
	'admin_plugin_requis_erreur_balisesession' => 'Installez le plugin Balise SESSION et activez le ! [Documentation ici->http://www.spip-contrib.net/?article1224], [Archive ZIP l&agrave;->http://files.spip.org/spip-zone/balise_session.zip].',
	'admin_plugin_requis_ok' => 'Plugin(s)  install&eacute;(s) et actif(s) :',
	'admin_plugin_requis_ok_cfg' => '[Plugin CFG->http://www.spip-contrib.net/?article1605] : fourni des fonctions et des balises.',
	'admin_plugin_requis_ok_balisession' => '[Plugin BALISE_SESSION->http://www.spip-contrib.net/?article1224] : fourni les informations sur les visiteurs authentifi&eacute;.',
	'admin_sous_titre' => 'Acc&egrave;der au panneau d\'administration des forums avec SpipBB',
	'admin_spipbb_release' => 'Version de SpipBB',
	'admin_spip_config_forums' => 'Configuration de SPIP&nbsp;:',
	'admin_spip_forums_ok' => 'Les forums publics sont bien activ&eacute;s.',
	'admin_spip_forums_warn' => '<p>{{Attention}} : vos forums sont d&eacute;sactiv&eacute;s par d&eacute;faut, il vous est recommand&eacute; d\'utiliser la publication imm&eacute;diate : [voir ici->@config_contenu@].</p><p>Sinon vous devrez les activer articles par articles.</p>',
	'admin_spip_mots_cles_ok' => 'Les mot-clefs sont bien activ&eacute;s.',
	'admin_spip_mots_cles_warn' => '<p>{{Attention}} : Les mots-cl&eacute;s sont pas actifs dans SPIP, vous ne pourrez pas utiliser les fonctions avanc&eacute;es associ&eacute;es.</p><p>Il vous est recommand&eacute; de les activer : [voir ici->@configuration@].</p>',
	'admin_spip_mots_forums_ok' => 'Les mot-clefs associ&eacute;s aux forums sont bien activ&eacute;s.',
	'admin_spip_mots_forums_warn' => '<p>{{Attention}} : Les mots-cl&eacute;s dans les forums du site public sont pas actifs dans SPIP, vous ne pourrez pas utiliser les fonctions avanc&eacute;es associ&eacute;es.</p><p>Il vous est recommand&eacute; de permettre leur utilisation : [voir ici->@configuration@].</p>',
	'admin_statistique' => 'Information',
	'admin_surtitre' => 'G&eacute;rer les forums',
	'admin_temps_deplacement' => 'Temps requis avant d&eacute;placement par un admin',
	'admin_titre' => 'Administration SpipBB',
	'admin_total_posts' => 'Nombre total de messages',
	'admin_total_users' => 'Nombre de membres',
	'admin_total_users_online' =>'Membres en ligne',
	'admin_unban_email_info' => 'Vous pouvez d&eacute;bannir plusieurs adresses en une fois en utilisant la combinaison CTRL ou MAJ avec la souris ou le clavier',
	'admin_unban_ip_info' => 'Vous pouvez d&eacute;bannir plusieurs adresses en une fois en utilisant la combinaison CTRL ou MAJ avec la souris ou le clavier',
	'admin_unban_user_info' => 'Vous pouvez d&eacute;bannir plusieurs utilisateurs en une fois en utilisant la combinaison CTRL ou MAJ avec la souris ou le clavier',
	'admin_valeur' => 'Valeur',

	'aecrit' => 'a &eacute;crit :',
	'alerter_abus' => 'Signaler ce message comme abusif/injurieux...', ## GAF 0.5
	'alerter_sujet' => 'Message abusif', ## GAF 0.5
	'alerter_texte' => 'Nous attirons votre attention sur le message suivant :', ## GAF 0.5
	'annonce' => 'Annonce',
	'annonce_dpt' => 'Annonce&nbsp;: ',
	'anonyme' => 'Anonyme',
	'auteur' => 'Auteur',
	'avatar' => 'Avatar', ## GAF 0.6

	// B
	'bouton_select_all' => 'Tout s&eacute;lectionner',
	'bouton_unselect_all' => 'Tout d&eacute;-s&eacute;lectionner',

	//C

	'champs_obligatoires'=>'Les champs marqu&eacute;s d\'une * sont obligatoires.',
	'chercher' => 'Chercher',
	'choix_mots_annonce' => 'Faire une annonce',
	'choix_mots_ferme' => 'Pour fermer un fil',
	'choix_mots_postit' => 'Mettre en postit',
	'choix_mots_selection' => 'Le groupe de mot doit contenir trois mot-clefs. Normalement, le plugin les a cr&eacute;&eacute; au moment de son installation. SpipBB utilise en g&eacute;n&eacute;ral les mots {ferme}, {annonce} et {postit}, mais vous pouvez en choisir d\'autres.',
	'choix_rubrique_selection' => 'S&eacute;lectionner un secteur qui sera la base de vos forums. Dedans, chaque sous-rubrique sera un groupe de forums, chaque article publi&eacute; ouvrira un forum.',
	'choix_squelettes' => 'Vous pouvez en choisir d\'autres, mais les fichiers qui remplacent groupeforum.html et filforum.html doivent exister !',
	'citer' => 'Citer',
	'col_avatar' => 'Avatar',
	'col_date_crea' => 'Date inscription',
	'col_marquer' => 'Marquer',
	'col_signature' => 'Signature',
	'config_affiche_champ_extra' => 'Afficher le champ : @nom_champ@',
	'config_affiche_extra' => 'Afficher ces champs dans les squelettes',
	'config_champs_auteur' => 'Champs SPIPBB',
	'config_champs_auteurs_plus' => 'Gestion champs auteurs suppl&eacute;mentaires',
	'config_champs_requis' => 'Les champs n&eacute;cessaires &agrave; SpipBB',
	'config_choix_mots' => 'Choisir le groupe de mot-cl&eacute;s',
	'config_choix_rubrique' => 'Choisir la rubrique contenant les forums spipBB',
	'config_choix_squelettes' => 'Choisir les squelettes utilis&eacute;s',
	'config_orig_extra' => 'Quel support utiliser pour les champs suppl&eacute;mentaires',
	'config_orig_extra_info' => 'Infos champs EXTRA ou autre table, table auteurs_profils.',
	'config_spipbb' => 'Configuration de base de spipBB pour permettre le fonctionnement des forums avec ce plugin.',
	'contacter' => 'Contacter',
	'contacter_dpt' => 'Contacter&nbsp;: ',
	'creer_categorie' => 'Cr&eacute;er Nouvelle Cat&eacute;gorie',
	'creer_forum' => 'Cr&eacute;er Nouveau Forum', ## GAF 0.6

	//D
	'dans_forum' => 'dans le forum',
	'deconnexion_' => 'D&eacute;connexion ',
	'deplacer' => 'D&eacute;placer', ## deplacer.html
	'deplacer_confirmer' => 'Confirmer le d&eacute;placement',
	'deplacer_dans_dpt' => '&Agrave; deplacer dans le forum&nbsp;:',
	'deplacer_sujet_dpt' => 'D&eacute;placement de&nbsp;:',
	'deplacer_vide' => 'Pas d\'autre forum : d&eacute;placement impossible.',
	'dernier' => '&nbsp;Dernier' , # exec/spipbb_admin.php�GAF 0.6
	'dernier_membre' => 'Dernier membre enregistr&eacute; : ',
	'derniers_messages' => 'Derniers Messages',
	'diviser' => 'Diviser', ## diviser.html
	'diviser_confirmer' => 'Confirmer la s&eacute;paration des messages',
	'diviser_dans_dpt' => '&Agrave; mettre dans le forum&nbsp;:',
	'diviser_expliquer' => 'A l\'aide du formulaire ci-dessous, vous pourrez s&eacute;parer ce fil en deux, soit : en s&eacute;lectionnant les messages individuellement; soit en choissant le message &agrave; partir duquel il faut les diviser en deux.',
	'diviser_selection_dpt' => 'S&eacute;lection&nbsp;:',
	'diviser_separer_choisis' => 'S&eacute;parer les messages s&eacute;lectionn&eacute;s',
	'diviser_separer_suite' => 'S&eacute;parer &agrave; partir du message s&eacute;lectionn&eacute;',
	'diviser_vide' => 'Pas d\'autre forum : division impossible.',

	//E

	'ecrirea' => 'Ecrire un email &agrave;',
	'effacer' => 'Effacer',
	'email' => 'E-mail',
	'en_ligne' => 'Qui est en ligne ?',
	'en_reponse_a' => 'En r&eacute;ponse au message',
	'en_rep_sujet_' => '&nbsp;:::&nbsp;Sujet : ',
	'etplus' => '... et plus ...', ## GAF 0.6
	'extra_avatar_saisie_url' => 'URL de votre avatar (http://... ...)', ## GAF 0.6
	'extra_avatar_saisie_url_info' => 'URL de l\'avatar du visiteur', ## GAF 0.6
	'extra_date_crea' => 'Date de premiere saisie profil SpipBB',
	'extra_date_crea_info' => 'Date de premiere saisie profil SpipBB',
	'extra_emploi' => 'Emploi',
	'extra_localisation' => 'Localisation',
	'extra_loisirs' => 'Loisirs',
	'extra_nom_aim'=>'Contacts chat (AIM)',
	'extra_nom_msnm'=>'Contacts chat (MSN Messenger)',
	'extra_nom_yahoo'=>'Contacts chat (Yahoo)',
	'extra_numero_icq'=>'Contacts chat (ICQ)',
	'extra_refus_suivi_thread' => '(refus suivi) Ne pas modifier !', ## GAF 0.6
	'extra_refus_suivi_thread_info' => 'Liste des threads pour lesquels on ne souhaite plus recevoir de notification',
	'extra_signature_saisie_texte' => 'Saisir ici le texte de votre signature', ## GAF 0.6+
	'extra_signature_saisie_texte_info' => 'Court texte de signature des messages',
	'extra_visible_annuaire' => 'Apparaitre dans la liste des Inscrits (publique)', ## GAF 0.6
	'extra_visible_annuaire_info' => 'Permet de refuser l\'affichage dans l\'annuaire des inscrits en zone publique',

	//F

	'fiche_contact' => 'Fiche Contact',
	'fil_annonce_annonce' => 'Passer le Sujet en Annonce',
	'fil_annonce_desannonce' => 'Supprimer le mode Annonce',
	'fil_deplace' => 'D&eacute;placer ce fil', ## inc/spipbb_presentation.php ## GAF 0.6
	'filtrer' => 'Filtrer',
	'forum' => 'Forums',
	'forum_annonce_annonce' => 'Poser le marquage d\'annonce', ## inc/spipbb_presentation.php
	'forum_annonce_desannonce' => 'Supprimer le marquage d\'annonce', ## inc/spipbb_presentation.php
	'forum_dpt' => 'Forum&nbsp;: ',
	'forum_ferme' => 'Ce forum est ferm&eacute;',
	'forum_ferme_texte' => 'Ce forum est ferm&eacute;. Vous ne pouvez plus y poster.',
	'forum_maintenance' => 'Ce forum est ferm&eacute; pour maintenance', ## GAF 0.6
	'forum_ouvrir' => 'Ouvrir ce Forum', ##�exec/spipbb_admin.php ## GAF 0.6

	'fromphpbb_erreur_db_phpbb_config' => 'Impossible de lire la configuration dans la base phpBB',
	'fromphpbb_migre_categories' => 'Import des cat&eacute;gories',
	'fromphpbb_migre_categories_dans_rub_dpt' => 'Implantation des forums dans la rubrique&nbsp;:',
	'fromphpbb_migre_categories_forum' => 'Forum',
	'fromphpbb_migre_categories_groupe' => 'Groupe',
	'fromphpbb_migre_categories_impossible' => 'Impossible de r&eacute;cup&eacute;rer les cat&eacute;gories',
	'fromphpbb_migre_categories_kw_ann_dpt' => 'Les annonces recevront le mot-clef&nbsp;:',
	'fromphpbb_migre_categories_kw_ferme_dpt' => 'Les sujets clos recevront le mot-clef&nbsp;:',
	'fromphpbb_migre_categories_kw_postit_dpt' => 'Les post its recevront le mot-clef&nbsp;:',
	'fromphpbb_migre_existe_dpt' => 'existe&nbsp;:',
	'fromphpbb_migre_thread' => 'Import des topics et des posts',
	'fromphpbb_migre_thread_ajout' => 'Ajout thread',
	'fromphpbb_migre_thread_annonce' => 'Annonce',
	'fromphpbb_migre_thread_existe_dpt' => 'Forum existe&nbsp;:',
	'fromphpbb_migre_thread_ferme' => 'Ferm&eacute;',
	'fromphpbb_migre_thread_impossible_dpt' => 'Impossible de r&eacute;cup&eacute;rer les posts&nbsp;:',
	'fromphpbb_migre_thread_postit' => 'Post-it',
	'fromphpbb_migre_thread_total_dpt' => 'Nombre total de topics et de posts ajout&eacute;s&nbsp;:',
	'fromphpbb_migre_utilisateurs' => 'Import des utilisateurs',
	'fromphpbb_migre_utilisateurs_admin_restreint_add' => 'Ajout admin restreint',
	'fromphpbb_migre_utilisateurs_admin_restreint_already' => 'Deja admin restreint',
	'fromphpbb_migre_utilisateurs_impossible' => 'Impossible de r&eacute;cup&eacute;rer les utilisateurs',
	'fromphpbb_migre_utilisateurs_total_dpt' => 'Nombre total d\'utilisateurs ajout&eacute;s&nbsp;:',

	//G

	//H
	'haut_page' =>  'Haut de page', ##�GAF 0.6

	//I

	'icone_ferme' => 'Fermer', ##�GAF 0.6
	'import_base' => 'Nom de la base&nbsp;:',
	'import_choix_test' => 'R&eacute;aliser un import de test (choix par d&eacute;faut)&nbsp;:',
	'import_choix_test_titre' => 'Import &agrave; blanc ou r&eacute;el',
	'import_erreur_db'=> 'Impossible de se connecter &agrave; la base @nom_base@',
	'import_erreur_db_config' => 'Impossible de lire la configuration dans la base @nom_base@',
	'import_erreur_db_rappel_connexion' => 'Impossible de se reconnecter &agrave; la base @nom_base@',
	'import_erreur_db_spip' => 'Impossible de se connecter &agrave; la base SPIP',
	'import_erreur_forums' => 'Impossible de recuperer les forums',
	'import_fichier' => 'Fichier de configuration @nom_base@ trouv&eacute;&nbsp;:',
	'import_host' => 'Nom/adresse du serveur',
	'import_login' => 'Identifiant&nbsp;:',
	'import_parametres_base' => 'Choisissez soit le chemin vers le fichier de configuration de @nom_base@, soit de renseigner les param&egrave;tres d\'acc&egrave;s &agrave; la base contenant les forums de @nom_base@&nbsp;:',
	'import_parametres_rubrique' => 'Choisissez la rubrique dans laquelle seront import&eacute;s les forums de @nom_base@',
	'import_parametres_titre' => 'Informations sur la base @nom_base@',
	'import_password' => 'Mot de passe&nbsp;:',
	'import_prefix' => 'Pr&eacute;fixe des tables&nbsp;:',
	'import_racine' => 'Chemin vers @nom_base@ (avatars)&nbsp;:',
	'import_table' => 'Table de configuration @nom_base@ trouv&eacute;e&nbsp;:',
	'import_titre' => 'Import d\'un forum @nom_base@',
	'import_titre_etape' => 'Import d\'un forum  @nom_base@ - &eacute;tape',
	'info' => 'Informations',
	'info_annonce_ferme' => 'Etat Annonce / Fermer', ## 0.6
	'info_confirmer_passe' => 'Confirmer ce nouveau mot de passe&nbsp;:',
	'info_ferme' => 'Etat Ferm&eacute;', ##�inc/spipbb_presentation.php ## 0.6
	'info_inscription_invalide' => 'Inscription impossible',
	'info_plus_cinq_car' => 'plus de 5 caract&egrave;res',
	'infos_refus_suivi_sujet' => 'Ne plus suivre les sujets',
	'infos_suivi_forum_par_inscription' => 'Suivi du forum par inscription',
	'inscription' => 'Inscription',
	'inscrit_s' => 'Inscrits',
	'ip_adresse_autres' => 'Autres adresses IP &agrave; partir desquelles cet auteur a post&eacute;',
	'ip_adresse_membres' => 'Membres ayant post&eacute; de cette adresse IP',
	'ip_adresse_post' => 'Adresse IP de ce message',
	'ip_informations' => 'Informations sur une adresse IP et un auteur',

	//L

	'le' => 'Le',
	'liste_des_messages' => 'Liste des messages', ## liste_messages.html
	'liste_inscrits' => 'Liste des membres',
	'login' => 'Connexion',

	//M
	'maintenance' => 'Maintenance', ## GAF 0.6
	'maintenance_fermer' => 'a ferm&eacute; l\'article/forum :',
	'maintenance_pour' => 'pour MAINTENANCE.',
	'membres_inscrits' => 'membres inscrits',
	'membres_les_plus_actifs' => 'Membres les plus actifs',
	'message' => 'Message',
	'messages' => 'R&eacute;ponses',
	'message_s' => 'Messages',
	'messages_anonymes' => 'rendre anonymes',
	'messages_derniers' => 'Derniers Messages',
	'message_s_dpt' => 'Messages&nbsp;: ',
	'messages_laisser_nom' => 'laisser le nom',
	'messages_supprimer_titre_dpt' => 'Pour les messages&nbsp;:',
	'messages_supprimer_tous' => 'les supprimer',
	'messages_voir_dernier' => 'Voir le dernier message',
	'messages_voir_dernier_s' => 'Voir les derniers messages',
	'moderateur' => 'Mod&eacute;rateur',
	'moderateur_dpt' => 'Mod&eacute;rateur&nbsp;: ', ## GAF 0.6
	'moderateurs' => 'Mod&eacute;rateur(s)',
	'moderateurs_dpt' => 'Mod&eacute;rateurs&nbsp;: ', ## 0.5
	'modif_parametre' => 'Modifiez vos param&egrave;tres',
	'mot_annonce' => 'Annonce
	_ Une annonce est situ&eacute;e en t&ecirc;te de forum sur toutes les pages.',
	'mot_ferme' => 'Ferm&eacute;
	-* Lorsqu\'un article-forum a ce mot-clef, seul les mod&eacute;rateurs peuvent y ajouter des messages.
	-* Lorsqu\'un sujet de forum est ferm&ecute;, seuls les mod&eacute;rateurs peuvent y ajouter des r&eacute;ponses.',
	'mot_postit' => 'Postit
	_ Un postit est situ� en dessous des annonces, avant les messages ordinaires. Il n\'appara&icirc;t qu\'une seule fois dans la liste.',
	'mot_groupe_moderation' => 'Goupe de mot-clefs utilis&eacute; pour la mod&eacute;ration de SpipBB',

	//N
	'no_message' => 'Aucun sujet ou message ne correspond &agrave; vos crit&egrave;res de recherche',
	'nom_util' => 'Nom d\'utilisateur',
	'non' => 'Non',

	//O
	'oui' => 'Oui',


	//P
	'pagine_page_' => ' .. page ',
	'pagine_post_s' => ' posts',
	'pagine_sujet_s' => ' sujets',
	'par_' => 'par ',
	'post_aucun_pt' => 'aucun&nbsp;!',
	'post_efface_lui' => 'Ce sujet comprend @$nbr_post@ message(s). Effac&eacute;s avec lui&nbsp;!\n',
	'post_ip' => 'Messages post&eacute; &agrave; partie de l\'adresse IP',
	'post_propose' => 'Message propos&eacute;', ##�GAF 0.6
	'post_rejete' => 'Message rejet&eacute;',
	'post_titre' => '&nbsp;:::&nbsp;Titre : ',
	'post_verifier_sujet' => 'V&eacute;rifier ce sujet',
	'poste_valide' => 'Post(s) &agrave; valider ...',
	'poster_date_' => 'Post&eacute; le : ',
	'poster_message' => 'Poster un message',
	'postit' => 'Postit',
	'postit_dpt' => 'Postit&nbsp;: ',
	'posts_effaces' => 'Messages effac&eacute;s&nbsp;!',
	'posts_refuses' => 'Messages refus&eacute;s, &agrave; effacer&nbsp;!',
	'previsualisation' => 'Pr&eacute;visualisation',
	'profil' => 'Profil',

	'plugin_nom' => 'SpipBB : Gestion des forums de SPIP', #�Pour faciliter les traductions de plugin.xml
	'plugin_auteur' =>  'La SpipBB Team : [voir la liste des contributeurs sur Spip-contrib->http://www.spip-contrib.net/Plugin-SpipBB#contributeurs]',
	'plugin_licence' => 'Distribu&eacute; sous licence GPL',
	'plugin_description' => 'Le plugin SpipBB permet :
	-* De g&eacute;rer de fa&ccedil;on centralis&eacute;e les forums de SPIP (interface priv&eacute;e),
	-* D\'utiliser un secteur comme base d\'un groupe de forums comme les &laquo;Bulletin Board&raquo; tels que phpBB. Dans ce secteur, les sous-rubriques sont des groupes de forums, les articles des forums, chaque message dans le forum d\'un article y d&eacute;marre un thread.

	{{Consultez :}}
	-* &bull;[l\'aide et support sur spipbb.spip-zone.info->http://spipbb.spip-zone.info/spip.php?article11],
	-* &bull;[La documentation sur Spip-contrib->http://www.spip-contrib.net/SpipBB-le-forum].

	_ {{Plugin spipbb en cours de developpement. Vous l\'utilisez &agrave; vos risques et p&eacute;rils}}

	_ [Acc&egrave;s au panneau d\'administration-> .?exec=spipbb_configuration]',
	'plugin_lien' => '[Consulter la documentation du plugin sur Spip-contrib->http://www.spip-contrib.net/SpipBB-le-forum]',

	//R

	'raison_clic' => 'cliquez ici',
	'raison_texte' => 'Pour en connaitre la raison',
	'recherche' => 'Recherche',
	'recherche_elargie' => 'Recherche &eacute;largie',
	'redige_post' => 'Ecrire message', ##�GAF 0.6
	'reglement' => '<p>Les administrateurs et mod&eacute;rateurs de ce forum s\'efforceront de supprimer
					ou &eacute;diter tous les messages &agrave; caract&egrave;re r&eacute;pr&eacute;hensible
					aussi rapidement que possible. Toutefois, il leur est impossible de passer en revue tous
					les messages. Vous admettez donc que tous les messages post&eacute;s sur ces forums
					expriment la vue et opinion de leurs auteurs respectifs, et non pas des administrateurs,
					ou mod&eacute;rateurs, ou webmestres (except&eacute; les messages post&eacute;s par
					eux-m&ecirc;me) et par cons&eacute;quent ne peuvent pas &ecirc;tre tenus pour responsables.</p>
					<p>Vous consentez &agrave; ne pas poster de messages injurieux, obsc&egrave;nes,
					vulgaires, diffamatoires, mena&ccedil;ants, sexuels ou tout autre message qui violerait
					les lois applicables. Le faire peut vous conduire &agrave; &ecirc;tre banni
					imm&eacute;diatement de fa&ccedil;on permanente (et votre fournisseur d\'acc&egrave;s
					&agrave; internet en sera inform&eacute;). L\'adresse IP de chaque message est
					enregistr&eacute;e afin d\'aider &agrave; faire respecter ces conditions.
					Vous &ecirc;tes d\'accord sur le fait que le webmestre, l\'administrateur
					et les mod&eacute;rateurs de ce forum ont le droit de supprimer, &eacute;diter,
					d&eacute;placer ou verrouiller n\'importe quel sujet de discussion &agrave; tout moment.
					En tant qu\'utilisateur, vous &ecirc;tes d\'accord sur le fait que toutes les informations
					que vous donnerez ci-apr&egrave;s seront stock&eacute;es dans une base de donn&eacute;es.
					Cependant, ces informations ne seront divulgu&eacute;es &agrave; aucune tierce personne
					ou soci&eacute;t&eacute; sans votre accord. Le webmestre, l\'administrateur,
					et les mod&eacute;rateurs ne peuvent pas &ecirc;tre tenus pour responsables si une
					tentative de piratage informatique conduit &agrave; l\'acc&egrave;s de ces donn&eacute;es.</p>
					<p>Ce forum utilise les cookies pour stocker des informations sur votre ordinateur.
					Ces cookies ne contiendront aucune information que vous aurez entr&eacute; ci-apr&egrave;s,
					ils servent uniquement &agrave; am&eacute;liorer le confort d\'utilisation.
					L\'adresse e-mail est uniquement utilis&eacute;e afin de confirmer les d&eacute;tails
					de votre enregistrement ainsi que votre mot de passe (et aussi pour vous envoyer un nouveau
					mot de passe dans la cas o&ugrave; vous l\'oublieriez).</p>
					<p>En vous enregistrant, vous vous portez garant du fait d\'&ecirc;tre en accord avec le
					r&egrave;glement ci-dessus.</p>',
	'repondre' => 'R&eacute;pondre', ##�GAF 0.6
	'reponse_s_' => 'R&eacute;ponses',
	'resultat_s_pour_' => ' r&eacute;sultats pour ',
	'retour_forum' => 'Retour &agrave; l\'accueil du forum',

	//S

	's_abonner_a' => 'RSS . S\'abonner &agrave; : ',
	'secteur_forum'=> 'RACINE',
	'selection_efface'=> 'Effacer la s&eacute;lection .. ',
	'sign_tempo' => 'R&eacute;alis&eacute; avec <a href="http://www.spip-contrib.net/Plugin-SpipBB#contributeurs" class="copyright">SpipBB</a>',
	'sign_admin' => '{{Cette page est uniquement accessible aux responsables du site.}}<p>Elle donne acc&egrave;s &agrave; la configuration du plugin &laquo;{{<a href="http://www.spip-contrib.net/Plugin-SpipBB#contributeurs" class="copyright">SpipBB</a>}}&raquo; ainsi qu\'&agrave; la gestion des forums du site.</p><p>Version : @version@ @distant@</p><p>Consultez&nbsp;:
	_ &bull; [La documentation sur Spip-Contrib->http://www.spip-contrib.net/?article2460]
	_ &bull; [L\'aide et support sur spipbb.spip-zone.info->http://spipbb.spip-zone.info/spip.php?article11]</p>@reinit@',
	'sign_maj' => '<br />Version plus r&eacute;cente disponible&nbsp;: @version@',
	'sign_ok' => '&agrave; jour.',
	'sign_reinit' => '<p>R&eacute;-initialisation&nbsp;:
	_ &bull; [de tout le plugin->@plugin@]</p>',
	'signature' => 'Signature',
	'sinscrire'=> 'S\'inscrire',
	'site_propose' => 'Site propos&eacute; par @auteur_post@',
	'site_web' => 'Site web',
	'squelette_filforum' => 'Base de squelette pour les fils de discussions :',
	'squelette_groupeforum' => 'Base de squelette pour les groupes de discussions :',
	'statut' => 'Statut',
	'statut_admin' => 'Administrateur',
	'statut_redac' => 'R&eacute;dacteur',
	'statut_visit' => 'Membre',
	'sujet' => 'Sujet', ## GAF 0.6
	'sujet_auteur' => 'Auteur',
	'sujet_clos_texte' => 'Ce sujet est clos, vous ne pouvez pas y poster.',
	'sujet_clos_titre' => 'Sujet Clos',
	'sujet_dpt' => 'Sujet&nbsp;: ',
	'sujet_ferme' => 'Sujet : ferm&eacute;',  ## GAF 0.6
	'sujet_nombre' => 'Nombre de Sujets',  ## GAF 0.6
	'sujet_nouveau' => 'Nouveau sujet',
	'sujet_rejete' => 'Sujet rejet&eacute;',  ## GAF 0.6
	'sujet_repondre' => 'R&eacute;pondre au sujet',
	'sujets' => 'Sujets',
	'sujet_s' => 'Sujets',
	'sujets_aucun' => 'Pas de sujet dans ce forum pour l\'instant',
	'sujet_valide' => 'Sujet &agrave; valider',
	'supprimer' => 'Supprimer',
	'sw_admin_can_spam' => 'Les admins sont autoris&eacute;s',
	'sw_admin_no_spam' => 'Pas de spam',
	'sw_ban_ip_titre' => 'Bannir l\'IP en m&ecirc;me temps',
	'sw_config_exceptions' => 'Vous pouvez activer des exceptions pour des utilisateurs privil&eacute;gi&eacute;s ici. Ils pourront quand m&ecirc;me publier avec des mots bannis.',
	'sw_config_exceptions_titre' => 'Gestion des exceptions',
	'sw_config_generale' => 'Configuration actuelle du filtrage :',
	'sw_config_generale_titre' => 'Configuration g&eacute;n&eacute;rale du filtrage du spam',
	'sw_config_warning' => 'Vous pouvez choisir le texte du MP envoy&eacute; si vous activez cette option (maxi 255 caract&egrave;res).',
	'sw_config_warning_titre' => 'Configuration des avertissements par message priv&eacute;',
	'sw_disable_sw_titre' => '<strong>Active le filtrage</strong><br />Si vous devez vous passer du filtrage,<br />cliquez sur Non',
	'sw_modo_can_spam' => 'Les mod&eacute;rateurs sont autoris&eacute;s',
	'sw_nb_spam_ban_titre' => 'Nombre de spams avant banissement',
	'sw_pm_spam_warning_message' => 'Ceci est un avertissement. Vous avez essay&eacute; de poster un message analys&eacute; comme du spam sur ce site web. Merci d\'&eacute;viter de recommencer.',
	'sw_pm_spam_warning_titre' => 'Attention.',
	'sw_send_pm_warning' => '<strong>Envoie un MP &agrave; l\'utilisateur</strong><br />lorsqu\'il poste un message avec un mot interdit',
	'sw_spam_forum_titre' => 'Gestion des messages de spam',
	'sw_spam_titre' => 'Filtrage du spam',
	'sw_spam_words_action' => 'A partir de cette page, vous pouvez ajouter, &eacute;diter et supprimer des mots associ&eacute;s &agrave; du spam. Le caract&egrave;re (*) est accept&eacute; dans le mot. Par exemple&nbsp;: {{*tes*}} capturera {d&eacute;testable}, {{tes*}} capturera {tester}, {{*tes}} capturera {portes}.',
	'sw_spam_words_mass_add' => 'Copier-coller ou saisir vos mots dans cette zone. S&eacute;parer chaque mot par une virgule, deux points ou un retour &agrave; la ligne.',
	'sw_spam_words_titre' => 'Filtrage de mots',
	'sw_spam_words_url_add' => 'Saisir l\'URL d\'un fichier contenant une liste de mots format&eacute;e comme ci-dessus. Exemple&nbsp;: http://spipbb.spip-zone.info/plugins/spipBB/base/spamwordlist.csv .',
	'sw_warning_from_admin' => 'Choisir l\'admin auteur du message envoy&eacute;',
	'sw_warning_pm_message' => 'Texte du message priv&eacute;',
	'sw_warning_pm_titre' => 'Sujet du message priv&eacute;',
	'sw_word' => 'Mot',


	//T

	'title_ferme' => 'Fermer le forum/article',
	'title_libere' => 'R&eacute;ouvrir le forum/article',
	'title_libere_maintenance' => 'Lib&eacute;rer le verrou de Maintenance',
	'title_maintenance' => 'Fermer le forum/article pour Maintenance',
	'title_sujet_ferme' => 'Fermer ce sujet',
	'title_sujet_libere' => 'R&eacute;ouvrir ce sujet',
	'titre_spipbb' => 'SpipBB',
	'total_membres' => 'Nous avons un total de ',
	'total_messages_membres' => 'Nos membres ont post&eacute; un total de ',
	'tous' => 'Tous',
	'tous_forums' => 'Tous les forums',


	//V
	'visible_annuaire_forum' => 'Apparaitre dans la liste des Inscrits', ## GAF 0.6
	'visites' => 'Vu',
	'voir' => 'VOIR',
	'votre_bio' => 'Courte biographie en quelques mots.',
	'votre_email' => 'Votre adresse email',
	'votre_nouveau_passe' => 'Nouveau mot de passe',
	'votre_signature' => 'Votre signature', ## GAF 0.6
	'votre_site' => 'Le nom de votre site',
	'votre_url_avatar' => 'URL de votre Avatar (http://...)', ## GAF 0.6
	'votre_url_site' => 'L\'adresse (URL) de votre site'

);

?>
