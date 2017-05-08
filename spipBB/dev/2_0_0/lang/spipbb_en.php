<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de https://www.spip.net/trad-lang/
// ** ne pas modifier le fichier **

if (!defined("_ECRIRE_INC_VERSION")) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// A
	'a_propos_auteur_dpt' => 'Everything about:',
	'admin_action_01_configuration' => 'Configure',
	'admin_action_02_etat' => 'Forums overview',
	'admin_action_ZZ_debug' => 'Debug',
	'admin_action_effacer' => 'Rejected posts',
	'admin_action_fromphorum' => 'Import from Phorum',
	'admin_action_fromphpbb' => 'Import from PhpBB',
	'admin_action_gere_ban' => 'Ban list management',
	'admin_action_gestion' => 'Management',
	'admin_action_inscrits' => 'Members',
	'admin_action_swconfig' => 'Configure',
	'admin_action_swforum' => 'Flagged posts',
	'admin_action_swlog' => 'Spam Log',
	'admin_action_swwords' => 'Manage Words',
	'admin_afficher_bouton_alerte_abus' => 'Show Abuse alert buttons',
	'admin_affichier_bouton_rss' => 'Show RSS buttons',
	'admin_age_forum' => 'Forum age Years/months',
	'admin_avatar_affiche' => 'Accept and show the avatars (default is Yes after install)',
	'admin_avatar_taille_contact' => 'Avatars\' size (in pixels) on the contact page',
	'admin_avatar_taille_profil' => 'Avatars\' size (in pixels) on the profile page',
	'admin_avatar_taille_sujet' => 'Avatars\\s size (in pixels) on the topics page',
	'admin_average_posts' => 'Daily posts average',
	'admin_average_users' => 'Daily inscription average',
	'admin_ban_email' => 'Banned email admin',
	'admin_ban_email_info' => 'To write more than one email, separate them using commas. To use a joker for a username use * ; eg *@hotmail.com',
	'admin_ban_email_none' => 'No banned address',
	'admin_ban_ip' => 'Banned IP admin',
	'admin_ban_ip_info' => 'To write more than one IP or server names, separate them using commas. To specify IP intervals, separate the biginning and the end with a dash -, to specify a joker use a star *',
	'admin_ban_ip_none' => 'No banned address',
	'admin_ban_user' => 'Banned logins admin',
	'admin_ban_user_info' => 'You can ban more than one user at a time using CTRL or UP combined with the mouse or the keyboard',
	'admin_ban_user_none' => 'No banned login',
	'admin_cat_01_general' => 'Administration',
	'admin_cat_outils' => 'Tools',
	'admin_cat_spam' => 'Spam Words',
	'admin_config_prerequis' => 'Requirements',
	'admin_config_spam_words' => 'Anti-spam admin',
	'admin_config_spipbb' => 'Enable SpipBB',
	'admin_config_spipbb_info' => 'Choose Yes to enable SpipBB',
	'admin_config_tables' => 'Configuration of SpipBB tables',
	'admin_config_tables_erreur' => 'Problem with SpipBB tables: @tables_erreur@ are incorrect (the tables @tables_ok@ seem to be all right).
 Refer to the [documentation on Spip-Contrib->https://contrib.spip.net/SpipBB-le-forum] or [support on spipbb.spip-zone->http://spipbb.spip-zone.info/spip.php?article11]',
	'admin_config_tables_ok' => 'The SpipBB database tables are installed correctly (@tables_ok@)',
	'admin_date_ouverture' => 'Openning date',
	'admin_debug_log' => 'Log file @log_name@',
	'admin_debug_metas' => 'SpipBB METAs',
	'admin_form_action' => 'Action',
	'admin_form_creer_categorie' => 'Create a category',
	'admin_form_creer_forum' => 'Create a forum',
	'admin_form_deplacer' => 'Move',
	'admin_form_descendre' => 'Down',
	'admin_form_editer' => 'Edit',
	'admin_form_messages' => '&nbsp;',
	'admin_form_monter' => 'Up',
	'admin_form_sujets' => '&nbsp;',
	'admin_forums_affiche_membre_defaut' => 'Do you want to enable report the member names in the members list when they did not make their own choice ?<br />[ Default No ]',
	'admin_forums_configuration' => 'Configure SpipBB',
	'admin_forums_configuration_avatar' => 'General configuration of avatars',
	'admin_forums_configuration_options' => 'SpipBB Options',
	'admin_forums_log_level' => 'Choice of the SpipBB log level.<br />[ Default 3 (maximum) ]',
	'admin_forums_log_level_0' => 'No logs',
	'admin_forums_log_level_1' => 'Few logs',
	'admin_forums_log_level_2' => 'Many logs',
	'admin_forums_log_level_3' => 'Very verbose logs',
	'admin_id_mjsc' => '#',
	'admin_infos' => 'SpipBB - Admin - Summary',
	'admin_interface' => 'Public interface options',
	'admin_nombre_lignes_messages' => 'Number of lines of messages',
	'admin_plugin_requis_erreur' => 'The following required plugin is missing. Activate it!',
	'admin_plugin_requis_erreur_balisesession' => 'Install le "Balise SESSION" plugin  and activate it! [Documentation here->https://contrib.spip.net/?article1224], [ZIP file here->https://files.spip.net/spip-zone/balise_session.zip].',
	'admin_plugin_requis_erreur_cfg' => 'Install the CFG plugin and activate it![Documentation here->https://contrib.spip.net/?article1605], [Zip file here->https://files.spip.net/spip-zone/cfg.zip].',
	'admin_plugin_requis_erreur_s' => 'The following required plugins are missing. Activate them!',
	'admin_plugin_requis_ok' => 'Installed and activated plugin(s):',
	'admin_plugin_requis_ok_balisesession' => '[Plugin BALISE_SESSION->https://contrib.spip.net/?article1224] : gives information on visitors who are logged in.',
	'admin_plugin_requis_ok_cfg' => '[Plugin CFG->https://contrib.spip.net/?article1605] : provides tags and functions.',
	'admin_sous_titre' => 'Go to the SpipBB forums admin panel',
	'admin_spip_config_forums' => 'SPIP configuration:',
	'admin_spip_forums_ok' => 'The public forums are enabled.',
	'admin_spip_forums_warn' => '<p>{{Beware}} : By default, Your forums are not activated. The recommended setting is to use the automatic activation ([see here->@config_contenu@]).</p><p>Otherwise, you will have to activate them article by article.</p>',
	'admin_spip_mots_cles_ok' => 'Keywords are enabled',
	'admin_spip_mots_cles_warn' => '<p>{{Beware}} : The keywords are not activated in SPIP, you won\'t be able to use them for the advanced features.</p><p>It is recommended to activate them. ([see here->@configuration@]).</p>',
	'admin_spip_mots_forums_ok' => 'Forum keywords are enabled',
	'admin_spip_mots_forums_warn' => '<p>{{Beware}} : The keywords for public forums are not activated in SPIP, you won\'t be able to use them for the relevant advanced features.</p><p>It is recommended to activate them. ([see here->@configuration@]).</p>',
	'admin_spipbb_release' => 'SpipBB relase',
	'admin_statistique' => 'Information',
	'admin_surtitre' => 'Forums management',
	'admin_temps_deplacement' => 'Amount of time before an admin can move',
	'admin_titre' => 'SpipBB Admin',
	'admin_titre_page_spipbb_admin' => 'Forums admin',
	'admin_titre_page_spipbb_admin_anti_spam_config' => 'Spam filter general admin',
	'admin_titre_page_spipbb_admin_anti_spam_forum' => 'Marked Posts',
	'admin_titre_page_spipbb_admin_anti_spam_log' => 'Spam Logs',
	'admin_titre_page_spipbb_admin_anti_spam_words' => 'Word Filter',
	'admin_titre_page_spipbb_admin_debug' => 'Debug',
	'admin_titre_page_spipbb_admin_etat' => 'SpipBB - Admin - Summary',
	'admin_titre_page_spipbb_admin_gere_ban' => 'Ban admin',
	'admin_titre_page_spipbb_admin_migre' => 'Migration of @nom_base@',
	'admin_titre_page_spipbb_configuration' => 'SpipBB configuration',
	'admin_titre_page_spipbb_effacer' => 'Admin blocked posts',
	'admin_titre_page_spipbb_inscrits' => 'Members admin',
	'admin_titre_page_spipbb_sujet' => 'Edit a thread',
	'admin_total_posts' => 'Total number of posts',
	'admin_total_users' => 'Number of members',
	'admin_total_users_online' => 'Online members',
	'admin_unban_email_info' => 'You can reinstate multiple addresses at once using combined CTRL and UP with mouse or keyboard',
	'admin_unban_ip_info' => 'You can reinstate multiple addresses at once using combined CTRL and UP with mouse or keyboard',
	'admin_unban_user_info' => 'You can reinstate multiple users at once using combined CTRL and UP with mouse or keyboard',
	'admin_valeur' => 'Value',
	'aecrit' => 'wrote:',
	'alerter_abus' => 'Report this message as being abusive',
	'alerter_sujet' => 'Post reported as abusive',
	'alerter_texte' => 'Please take a look at this post:',
	'annonce' => 'Announcement',
	'annonce_dpt' => 'Announcement: ',
	'anonyme' => 'Anonymous',
	'auteur' => 'Author',
	'avatar' => 'Avatar',

	// B
	'bouton_select_all' => 'Select all',
	'bouton_speciaux_sur_skels' => 'Configure specific buttons on the public templates',
	'bouton_unselect_all' => 'Deselect all',

	// C
	'champs_obligatoires' => 'Fields marked with an * are compulsory.',
	'chercher' => 'Search',
	'choix_mots_annonce' => 'Make an announce',
	'choix_mots_creation' => 'If you want to create the keywords dedicated to SpipBB  <strong>automatically</strong>B, click this button. These keywords can be modified or deleted later...',
	'choix_mots_creation_submit' => 'Automatic keywords configuration',
	'choix_mots_ferme' => 'To close a thread',
	'choix_mots_postit' => 'Set sticky',
	'choix_mots_selection' => 'This keyword group should at least contain 3 keywords. Usually, the plugin will create them while installing. SpipBB needs - in general - the {ferme} (closed), {annonce} (announce) and {postit} (postit) keywords, but you are allowed to choose another one.',
	'choix_rubrique_creation' => 'If you want to create the main section containing the SpipBB forums and the first empty forum <strong>automatically</strong>, click this button. This forum is the created hierarchy can be modified or deleted later...',
	'choix_rubrique_creation_submit' => 'Main section automatic configuration',
	'choix_rubrique_selection' => 'Select the section that will host the base of your forums. Inside, each sub-section will be a forum group, each published article will open a new forum.',
	'choix_squelettes' => 'You are allowed to used other templates, but the selected files replacing groupeforum.html and filforum.html must already exist!',
	'citer' => 'Quote',
	'col_avatar' => 'Avatar',
	'col_date_crea' => 'Registration date',
	'col_marquer' => 'Mark',
	'col_signature' => 'Signature',
	'config_affiche_champ_extra' => 'Show the field: <b>@nom_champ@</b>',
	'config_affiche_extra' => 'Show below field in the templates',
	'config_champs_auteur' => 'SPIPBB Fields',
	'config_champs_auteurs_plus' => 'Author extra fields admin',
	'config_champs_requis' => 'Fields required by SpipBB',
	'config_choix_mots' => 'Choose the keyword group',
	'config_choix_rubrique' => 'Choose the section used by spipBB forums',
	'config_choix_squelettes' => 'Choose the templates',
	'config_orig_extra' => 'Which source used to store the extra fields',
	'config_orig_extra_info' => 'Infos EXTRA fields or other db table, table auteurs_profils.',
	'config_spipbb' => 'Basic SpipBB config in order to use the forums with this plugin.',
	'contacter' => 'Contact',
	'contacter_dpt' => 'Contact: ',
	'creer_categorie' => 'Create a new category',
	'creer_forum' => 'Create a new Forum',

	// D
	'dans_forum' => 'in the forum',
	'deconnexion_' => 'Logout ',
	'deplacer' => 'Move',
	'deplacer_confirmer' => 'Confirm the move',
	'deplacer_dans_dpt' => 'Move to forum:',
	'deplacer_sujet_dpt' => 'Moving of:',
	'deplacer_vide' => 'Not any other forum left: moving this thread is impossible!',
	'dernier' => '&nbsp;Last', # exec/spipbb_admin.php
	'dernier_membre' => 'Last registered member: ',
	'derniers_messages' => 'Last topics',
	'diviser' => 'Split',
	'diviser_confirmer' => 'Confirm the topics splitting',
	'diviser_dans_dpt' => 'Into this Forum:',
	'diviser_expliquer' => 'Using the below form you can split a topic in two, either by selecting the posts individually or by splitting at a selected post.',
	'diviser_selection_dpt' => 'Select:',
	'diviser_separer_choisis' => 'Split selected posts',
	'diviser_separer_suite' => 'Split from selected posts',
	'diviser_vide' => 'Not any other forum left: spliting this thread is impossible!',

	// E
	'ecrirea' => 'Send an email to',
	'effacer' => 'Delete',
	'email' => 'E-mail',
	'en_ligne' => 'Who\'s online?',
	'en_rep_sujet_' => '&nbsp;:::&nbsp;Topic : ',
	'en_reponse_a' => 'Answering the message',
	'etplus' => '...and more...',
	'extra_avatar_saisie_url' => 'Your avatar\'s URL (http://... ...)',
	'extra_avatar_saisie_url_info' => 'Visitor\'s avatar URL',
	'extra_date_crea' => 'Registration date',
	'extra_date_crea_info' => 'SpipBB profile registration date',
	'extra_emploi' => 'Job',
	'extra_localisation' => 'Localization',
	'extra_loisirs' => 'Hobbies',
	'extra_nom_aim' => 'Chat id (AIM)',
	'extra_nom_msnm' => 'Chat id (MSN Messenger)',
	'extra_nom_yahoo' => 'Chat id (Yahoo)',
	'extra_numero_icq' => 'Chat id (ICQ)',
	'extra_refus_suivi_thread' => '(don\'t follow). Do not modify!',
	'extra_refus_suivi_thread_info' => 'Threads you want not to follow any more',
	'extra_signature_saisie_texte' => 'Fill your signature here',
	'extra_signature_saisie_texte_info' => 'Short signature',
	'extra_visible_annuaire' => 'Appear in the (public) members\' list',
	'extra_visible_annuaire_info' => 'Allow you not to appear in the public members\' list',

	// F
	'fiche_contact' => 'Contact form',
	'fil_annonce_annonce' => 'Move this topic into Announcement',
	'fil_annonce_desannonce' => 'Remove the Announcement mode',
	'fil_deplace' => 'Move this thread',
	'filtrer' => 'Filter',
	'forum' => 'Forums',
	'forum_annonce_annonce' => 'Mark as announce',
	'forum_annonce_desannonce' => 'Remove the announce mark',
	'forum_dpt' => 'Forum&nbsp;: ',
	'forum_ferme' => 'This forum is disabled',
	'forum_ferme_texte' => 'This forum is disabled. You cannot post to it anymore.',
	'forum_maintenance' => 'This forum is close for maintenance',
	'forum_ouvrir' => 'Open this Forum',
	'forums_categories' => 'Miscellaneous',
	'forums_spipbb' => 'SpipBB forums',
	'forums_titre' => 'My first forum',
	'fromphpbb_erreur_db_phpbb_config' => 'Impossible to read config value in phpBB database',
	'fromphpbb_migre_categories' => 'Categories migration',
	'fromphpbb_migre_categories_dans_rub_dpt' => 'Implanting the forums into the sector:',
	'fromphpbb_migre_categories_forum' => 'Forum',
	'fromphpbb_migre_categories_groupe' => 'Group',
	'fromphpbb_migre_categories_impossible' => 'Impossible to migrate the categories',
	'fromphpbb_migre_categories_kw_ann_dpt' => 'The announce will use the keyword:',
	'fromphpbb_migre_categories_kw_ferme_dpt' => 'The closed topics will use the keyword:',
	'fromphpbb_migre_categories_kw_postit_dpt' => 'The postits will use the keyword:',
	'fromphpbb_migre_existe_dpt' => 'exist:',
	'fromphpbb_migre_thread' => 'Migration of topics and posts',
	'fromphpbb_migre_thread_ajout' => 'Adding thread',
	'fromphpbb_migre_thread_annonce' => 'Announce',
	'fromphpbb_migre_thread_existe_dpt' => 'Forum already exist:',
	'fromphpbb_migre_thread_ferme' => 'Closed',
	'fromphpbb_migre_thread_impossible_dpt' => 'Impossible to migrate the topics:',
	'fromphpbb_migre_thread_postit' => 'Post-it',
	'fromphpbb_migre_thread_total_dpt' => 'Total number of topics and posts added:',
	'fromphpbb_migre_utilisateurs' => 'Migrate the members',
	'fromphpbb_migre_utilisateurs_admin_restreint_add' => 'Add restricted admin',
	'fromphpbb_migre_utilisateurs_admin_restreint_already' => 'Already restricted admin',
	'fromphpbb_migre_utilisateurs_impossible' => 'Impossible to migrate the members',
	'fromphpbb_migre_utilisateurs_total_dpt' => 'Total number of members added:',

	// H
	'haut_page' => 'Top of page',

	// I
	'icone_ferme' => 'Close',
	'import_base' => 'Database name:',
	'import_choix_test' => 'Make a blank test (default):',
	'import_choix_test_titre' => 'Blank or real migration',
	'import_erreur_db' => 'Impossible to connect to @nom_base@ database',
	'import_erreur_db_config' => 'Impossible to read the configuration of @nom_base@',
	'import_erreur_db_rappel_connexion' => 'Impossible to reconnect tp @nom_base@ database',
	'import_erreur_db_spip' => 'Impossible to connect to SPIP database',
	'import_erreur_forums' => 'Impossible to migrate the forums',
	'import_fichier' => '@nom_base@ configration file found:',
	'import_host' => 'Server\'s name/address',
	'import_login' => 'Login:',
	'import_parametres_base' => 'Please choose the path to the config file of @nom_base@, or the database parameters of @nom_base@:',
	'import_parametres_rubrique' => 'Please choose the sector into which the @nom_base@ forums will be migrated',
	'import_parametres_titre' => 'Informations about @nom_base@',
	'import_password' => 'Password:',
	'import_prefix' => 'Database tables prefix:',
	'import_racine' => 'Path to @nom_base@ (avatars):',
	'import_table' => 'Config table of @nom_base@ found:',
	'import_titre' => 'Migration of @nom_base@ forum',
	'import_titre_etape' => 'Migration of @nom_base@ forum - step',
	'info' => 'Information',
	'info_annonce_ferme' => 'State Announce/Closed',
	'info_confirmer_passe' => 'Confirm this new password:',
	'info_ferme' => 'State closed',
	'info_inscription_invalide' => 'Impossible to register',
	'info_plus_cinq_car' => 'more than 5 characters',
	'infos_refus_suivi_sujet' => 'Don\'t follow these threads',
	'infos_suivi_forum_par_inscription' => 'Follow the thread by registration',
	'inscription' => 'Register',
	'inscrit_le' => 'Registrered on',
	'inscrit_le_dpt' => 'Registrered on',
	'inscrit_s' => 'Registered',
	'ip_adresse_autres' => 'Other IP addresses this user has posted from',
	'ip_adresse_membres' => 'Users posting from this IP address',
	'ip_adresse_post' => 'IP address for this post',
	'ip_informations' => 'IP Informations',

	// L
	'le' => 'The',
	'liste_des_messages' => 'Posts list',
	'liste_inscrits' => 'Member list',
	'login' => 'Connection',

	// M
	'maintenance' => 'Maintenance',
	'maintenance_fermer' => 'closed the news/forum:',
	'maintenance_pour' => 'for MAINTENANCE.',
	'membres_en_ligne' => 'members online',
	'membres_inscrits' => 'registered members',
	'membres_les_plus_actifs' => 'Most active members',
	'message' => 'Message',
	'message_s' => 'Messages',
	'message_s_dpt' => 'Messages: ',
	'messages' => 'Answers',
	'messages_anonymes' => 'anonymize',
	'messages_derniers' => 'Latest Messages',
	'messages_laisser_nom' => 'put the name',
	'messages_supprimer_titre_dpt' => 'For the posts:',
	'messages_supprimer_tous' => 'delete',
	'messages_voir_dernier' => 'Jump to the last message',
	'messages_voir_dernier_s' => 'Watch the last messages',
	'moderateur' => 'Moderator',
	'moderateur_dpt' => 'Moderator: ',
	'moderateurs' => 'Moderator(s)',
	'moderateurs_dpt' => 'Moderators: ',
	'modif_parametre' => 'Change your parameters',
	'mot_annonce' => 'Announcement
 _ An announcement is placed at the head of the forum on all pages.',
	'mot_ferme' => 'Closed
 -* when an article-forum uses this keyword, only moderators can post messages.
 -* when a forum topic is closed, only the moderators will be able to post messages.',
	'mot_groupe_moderation' => 'Keywords group used for SpipBB moderation',
	'mot_postit' => 'Post-it
 _ A post-it is placed underneath announcements, before ordinary messages. It only appears once in the list.',

	// N
	'no_message' => 'No message by that search criterium',
	'nom_util' => 'Member name',
	'non' => 'No',

	// O
	'ordre_croissant' => 'Increasing',
	'ordre_decroissant' => 'Decreasing',
	'ordre_dpt' => 'Order:',
	'oui' => 'Yes',

	// P
	'pagine_page_' => ' .. page ',
	'pagine_post_' => ' reply',
	'pagine_post_s' => ' replies',
	'pagine_sujet_' => 'topic',
	'pagine_sujet_s' => ' topics',
	'par_' => 'by ',
	'plugin_auteur' => 'The SpipBB Team: [See the list of contributors on Spip-Contrib->https://contrib.spip.net/Plugin-SpipBB#contributeurs]',
	'plugin_description' => 'The SpipBB plugin provides the following features:
-* centralizes the forum management in SPIP (in the private area),
-* turns a main section (sector) of the site into a group of forum, "Bulletin Board" style, similar to phpBB. In this sector, sub-sections are used as forum groups, articles are dedicated forums where threads are made of messages posted to an article.

{{Please check:}}
-* [help and support on spipbb.spip-zone.info->http://spipbb.spip-zone.info/spip.php?article11],
-* [the documentation on Spip-Contrib->https://contrib.spip.net/SpipBB-le-forum].

_ {{The SpipBB plugin is still being developed. You use it at your own risk.}}

_ [Access to the management panel-> .?exec=spipbb_configuration]',
	'plugin_licence' => 'Distributed under the GPL licence',
	'plugin_lien' => '[See the documentation of the plugin from Spip-Contrib->https://contrib.spip.net/SpipBB-le-forum]',
	'plugin_mauvaise_version' => 'This version of the plugin cannot be used with your version of SPIP!',
	'plugin_nom' => 'SpipBB: Management of SPIP forums',
	'post_aucun_pt' => 'aucun&nbsp;!',
	'post_efface_lui' => 'This topic contains @
nbr_post@ message(s). Deleted along with it!\\n',
	'post_ip' => 'Posts sent from IP address',
	'post_propose' => 'Suggested post',
	'post_rejete' => 'Rejected post',
	'post_titre' => '&nbsp;:::&nbsp;Title: ',
	'post_verifier_sujet' => 'Check this post',
	'poste_valide' => 'Post(s) to check...',
	'poster_date_' => 'Posted the: ',
	'poster_message' => 'Post a topic',
	'postit' => 'Postit',
	'postit_dpt' => 'Postit: ',
	'posts_effaces' => 'Topics deleted!',
	'posts_refuses' => 'Blocked topics to delete!',
	'previsualisation' => 'Preview',
	'profil' => 'Profile',

	// R
	'raison_clic' => 'clic here',
	'raison_texte' => 'To know the cause',
	'recherche' => 'Search',
	'recherche_elargie' => 'Advanced search',
	'redige_post' => 'Write a topic',
	'reglement' => '<p>The administrators and moderators of this forum will 
 endeavour to delete or edit all the messages with offending content
 as quickly as possible. However, it is impossible to check all the
 messages.You agree that all the messages posted to these forums 
 reflect the opinions of their respective authors and not the 
 opinions of the administrators, moderators or Webmasters (except the messages they post themselves) and consequently 
 cannot be held responsible or liable.</p>
 <p>You agree not to post messages containing
 abusive, illegal, sexually or racially objectionable, defamatory or 
 harassing language of any sort. Offenders may find themselves permanently 
 banned (and their ISP informed). The IP address of each 
 message is recorded in order to help uphold these regulations. You 
 agree that the Webmaster, the administrator and the moderators of 
 this forum have the right to delete, edit, move or lock any topic 
 of discussion at any moment. As a user, you agree that the 
 information you provide below will be stored in a database. 
 However, this information will not be disclosed to any third party 
 or company without your prior consent. The Webmaster, the 
 administrator and the moderators cannot be held liable if a 
 hacking attempt succeeds in accessing this data.</p>
 <p>This forum will log information via cookies stored in your 
 computer. These cookies will not contain any information that you 
 have entered below, their only goal is to enhance the user 
 experience.The e-mail address will be used only to confirm the 
 details of your registration and your password (and also to send 
 you a new password should you forget yours).</p>
 <p>By registering, you guarantee your agreement with the above 
 regulations.</p>',
	'repondre' => 'Reply',
	'reponse_s_' => 'Replies',
	'resultat_s_pour_' => ' results for ',
	'retour_forum' => 'Back to the forum home',

	// S
	's_abonner_a' => 'RSS: subscribe to this thread',
	'secteur_forum' => 'ROOT',
	'selection_efface' => 'Deleted the selection... ',
	'selection_tri_dpt' => 'Choose the sorting method:',
	'sign_admin' => '{{This page can only be seen by the site owner.}}<p>It provides access to the plugin configuration of &laquo;{{<a href="https://contrib.spip.net/Plugin-SpipBB#contributeurs" class="copyright">SpipBB</a>}}&raquo; as well as to the forum management of your site.</p><p>Version : @version@ @distant@</p><p>See&nbsp;:
_ • [The documentation of Spip-Contrib->https://contrib.spip.net/?article2460]
_ • [Help and support on spipbb.spip-zone.info->http://spipbb.spip-zone.info/spip.php?article11]</p>@reinit@',
	'sign_maj' => '<br />update available: @version@',
	'sign_ok' => 'up to date',
	'sign_reinit' => '<p>Reset:
 _ • [the whole plugin->@plugin@]</p>',
	'sign_tempo' => 'Build with <a href="https://contrib.spip.net/Plugin-SpipBB#contributeurs" class="copyright">SpipBB</a>',
	'signature' => 'Signature',
	'sinscrire' => 'Register',
	'site_propose' => 'Proposed Website',
	'site_web' => 'Web site',
	'squelette_filforum' => 'Skeleton base for threads:',
	'squelette_groupeforum' => 'Skeleton base for forum groups:',
	'statut' => 'Status',
	'statut_admin' => 'Admin',
	'statut_redac' => 'Moderator',
	'statut_visit' => 'Member',
	'sujet' => 'Topic',
	'sujet_auteur' => 'Author',
	'sujet_clos_texte' => 'This topic is closed, you cannot post to it.',
	'sujet_clos_titre' => 'Topic closed',
	'sujet_dpt' => 'Topic: ',
	'sujet_ferme' => 'Topic: closed',
	'sujet_nombre' => 'Topic number',
	'sujet_nouveau' => 'New topic',
	'sujet_rejete' => 'Rejected topic',
	'sujet_repondre' => 'Reply',
	'sujet_s' => 'Topics',
	'sujet_valide' => 'Topic to confirm',
	'sujets' => 'Topics',
	'sujets_aucun' => 'No topics in this forum for the time being',
	'support_extra_normal' => 'extra',
	'support_extra_table' => 'table',
	'supprimer' => 'Delete',
	'sw_admin_can_spam' => 'Allow admin to post spam words',
	'sw_admin_no_spam' => 'No spam',
	'sw_ban_ip_titre' => 'Ban IP as well?',
	'sw_config_exceptions' => 'You can set exceptions for privileged members here. The members who meet these criteria will be permitted to post spam words.',
	'sw_config_exceptions_titre' => 'Exceptions',
	'sw_config_generale' => 'Currently activated spam words:',
	'sw_config_generale_titre' => 'General spam filtering settings',
	'sw_config_warning' => 'Here you can define the text to PM your users if you choose to warn them via PM when they posts a spam word (max. 255 characters).',
	'sw_config_warning_titre' => 'Private message warning settings',
	'sw_disable_sw_titre' => '<strong>Enable Spam words filter</strong><br />If you need to do without this filter,<br />then click "No".',
	'sw_modo_can_spam' => 'Allow moderators to post spam words',
	'sw_nb_spam_ban_titre' => 'Number of offenses before user is automatically banned',
	'sw_pm_spam_warning_message' => 'This is a warning. You have tried to post a word that is defined as spam on this website. Please stop.',
	'sw_pm_spam_warning_titre' => 'Warning.',
	'sw_send_pm_warning' => '<strong>Send a PM to the user</strong> to warn them when they submit a post containing a prohibited word',
	'sw_spam_forum_titre' => 'Manage flagged posts',
	'sw_spam_titre' => 'Spam filtering',
	'sw_spam_words_action' => 'From this control panel you can add, edit, and remove spam words. Wildcards (*) are accepted in the word field. For example, *test* will match detestable, test* would match testing, *test would match detest.',
	'sw_spam_words_mass_add' => 'Paste or type your spam words lists into the text area. Separate each spam word by either a comma, semi-colon, or line-break',
	'sw_spam_words_titre' => 'Spam words filtering',
	'sw_spam_words_url_add' => 'Type the URL of a file containing a list of word formatted in the style above. Example: http://spipbb.spip-zone.info/IMG/csv/spamwordlist.csv .',
	'sw_warning_from_admin' => 'Select the administrator that is listed as the Private Message sender',
	'sw_warning_pm_message' => 'Private message text',
	'sw_warning_pm_titre' => 'Private message subject',
	'sw_word' => 'Word',

	// T
	'title_ferme' => 'Close the forum/news',
	'title_libere' => 'Reopen the forum/news',
	'title_libere_maintenance' => 'Remove the Maintenance lock',
	'title_maintenance' => 'Close the forum/news for Maintenance',
	'title_sujet_ferme' => 'Close this topic',
	'title_sujet_libere' => 'Reopen this topic',
	'titre_spipbb' => 'SpipBB',
	'total_membres' => 'We have a total of ',
	'total_messages_membres' => 'Our members posted a total of ',
	'tous' => 'All',
	'tous_forums' => 'All forums',
	'trier' => 'Sort',
	'trouver_messages_auteur_dpt' => 'Find all messages from:',

	// V
	'visible_annuaire_forum' => 'Appear on the member list',
	'visites' => 'Views',
	'voir' => 'SEE',
	'votre_bio' => 'Short birography.',
	'votre_email' => 'Your email',
	'votre_nouveau_passe' => 'New password',
	'votre_signature' => 'Your signature:',
	'votre_site' => 'Your website\'s name',
	'votre_url_avatar' => 'Your avatar\'s URL(http://...)',
	'votre_url_site' => 'Your websites\'s address (URL)'
);

?>
