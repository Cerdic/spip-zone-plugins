<?php

// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
/*
 MERCURE 
 TCHAT POUR LES REDACTEURS DANS L'ESPACE PRIVE DE SPIP
 v. 0.10 - 07/2009 - SPIP 1.9.2
 Patrick Kuchard - www.encyclopedie-incomplete.com
*/

$GLOBALS[$GLOBALS['idx_lang']] = array(

/*
      =========================================================================
      GLOBAL
      =========================================================================
*/
'mercure_titre' => 'Mercure Redactor\'s Chat',
'heure_locale' => 'Heure locale',
'personnes_en_ligne' => 'Personnes en ligne <small>(&agrave; part vous)</small>',
'aucune_personne_en_ligne' => 'Aucune autre personne en ligne &agrave; par vous...',
'abrv_administrateur' => 'Administrateur',
'abrv_redacteur' => 'R&eacute;dacteur',
'abrv_visiteur' => 'Visiteur',
'personnes_en_detail' => 'En d&eacute;tail',
'bouton_envoyer' => 'Envoyer [Alt+s]',
'send_accesskey' => 's',

/*
      =========================================================================
      ONGLETS
      =========================================================================
*/
'onglet_mercure_pg' => 'Discuter',
'onglet_mercure_doc' => 'Documentation',
'onglet_mercure_conf' => 'Configuration',
'onglet_mercure_remove' => 'D&eacute;sinstaller',

/*
      =========================================================================
      PARTIE GAUCHE
      =========================================================================
*/
'date_serveur_mysql' => 'Date serveur MySQL', 
'date_serveur_php' => 'Date serveur PHP', 
'page_phpinfo' => 'Page phpinfo',
'mercure_avertissement' => '<strong>AVERTISSEMENT</strong><br /><br />
                            Cette application est fournie "<em>telle quelle</em>" et ne saurait engager la responsabilit&eacute; de l\'auteur en cas de mauvais fonctionnement.<br /><br />
                            Merci.',
'signature_plugin' => '<b>Redactor\'s Tchat - @version@</b><br />(07/2009)<br />
						Petit gadget ... pour discuter dans l\'arri&egrave;re boutique.<br />
						Par Patrick Kuchard - <a href=\'http://www.encyclopedie-incomplete.com\'>encyclopedie-incomplete.com</a><br />
						<br />Ce n\'est pas indispensable...<br />Et c\'est cela qui est bien !',
'haut_page' => 'Haut de page', 
'sound_on' => 'Notification sonore',
'sound_off' => 'Arr&ecirc;t des notifications',
'all_messages' => 'Liste de tous les messages',

/*
      =========================================================================
      DOCUMENTATION
      =========================================================================
*/
'documentation' => '<strong>Documentation</strong><br /><br />La documentation la plus &agrave; jour se trouve l&agrave; :<br /><br /><ul>
<li><strong>Sur Spip-Contrib</strong><br />&nbsp;&nbsp;&nbsp;<a href="http://contrib.spip.net/article3154" target="_blank" title="Ouvrir dans une nouvelle fen&ecirc;tre">http://contrib.spip.net</a></li><br />
<li><strong>Sur le site de l\'auteur</strong><br />&nbsp;&nbsp;&nbsp;<a href="http://www.encyclopedie-incomplete.com/?Plugin-Mercure-Redactor-s-Chat" target="_blank" title="Ouvrir dans une nouvelle fen&ecirc;tre">www.encyclopedie-incomplete.com</a></li>
</ul><br /><br />',
'minidoc' => '<strong>Quelques &eacute;l&eacute;ments</strong><br /><br />
              <ul>
                <li>Seuls les administrateurs peuvent configurer et d&eacute;sinstaller le plugin.</li>
                <li>Tous les r&eacute;dacteurs ont acc&egrave;s &agrave; ce plugin pour l\'utiliser.</li>
              </ul>
              <br />
              <em>Mercure</em> permet une conversation sous la forme d\'un Tchat entre les r&eacute;dacteurs connect&eacute;s &agrave; l\'espace priv&eacute;.<br /><br />
              Sur la partie gauche de votre &eacute;cran (onglet "<em>Discuter</em>"), vous avez acc&egrave;s aux r&eacute;dacteurs connect&eacute;s au m&ecirc;me instant que vous et qui sont susceptibles d\'utiliser <em>Mercure</em>.<br />
              Vous pouvez, en cliquant sur l\'un des pseudos, l\'inviter &agrave; une discussion dans cet espace de Tchat.<br /><br />
              Si l\'administrateur a s&eacute;lectionn&eacute; la notification sonore pr&eacute;venant de l\'arriv&eacute;e d\'un nouveau message, vous avez la possibilit&eacute; d\'activer ou non cette notification dans l\'onglet "<em>Discuter</em>".<br /><br />
              Vous avez aussi la possibilit&eacute; de visualiser les discussions pass&eacute;es en cliquant sur le bouton idoine dans l\'onglet "<em>Discuter</em>".<br /><br />
             ',

/*
      =========================================================================
      CONFIGURATION
      =========================================================================
*/
'configuration_first_use' => 'Bienvenue dans le panneau de configuration de "<strong><em>Mercure</em></strong>".<br /><br />
                              Vous utilisez ce plugin pour la premi&egrave;re fois, aussi prenez quelques instants pour configurer son fonctionnement.<br />
                              Vous trouverez dans les blocs ci-dessous les &eacute;l&eacute;ments qui le personnaliseront.<br /><br />
                              Vous pourrez &agrave; loisir les modifier par la suite.
                             ',
'configuration_after_use' => 'Utilisez les &eacute;l&eacute;ments dans les blocs ci-dessous pour personnaliser le fonctionnement de ce plugin.',
'configuration_general' => 'G&eacute;n&eacute;ral',
'conf_general_menu_question' => 'O&ugrave; souhaitez-vous voir appara&icirc;tre "<em>Mercure</em>" ?',
'conf_general_menu_accueil' => '&agrave; suivre',
'conf_general_menu_naviguer' => '&eacute;dition',
'conf_general_menu_forum' => 'forum',
'conf_general_menu_auteurs' => 'auteurs',
'conf_general_menu_statistiques_visites' => 'statistiques<br><small>(non accessible aux auteurs)</small>',
'conf_general_menu_configuration' => 'configuration<br><small>(non accessible aux auteurs)</small>',
'conf_general_menu_aide_index' => 'aide',
'conf_general_menu_visiter' => 'visiter',

'conf_maj_connectes_question' => 'R&eacute;actualiser le panneau des connect&eacute;s toutes les (en minutes)',

'configuration_messages' => 'Messages',
'conf_notify_question' => 'Voulez-vous un son d\'avertissement &agrave chaque nouveau message ?',
'conf_oui' => 'Oui',
'conf_non' => 'Non',
'conf_volume_question' => 'Le jouer &agrave; quel volume (0..100) ?',
'conf_notify_sound_question' => 'Quel son doit &ecirc;tre jou&eacute; ?',
'conf_notify_avertissement' => '<font color="gray"><br><small><strong>Note</strong> :<br>Les navigateurs qui supportent la notification des nouveaux messages sont :
                                <ul>
                                  <li>Opera</li>
                                  <li>Internet Explorer</li>
                                  <li>Safari</li>
                                </ul>
                                Ceux qui le supportent presque :
                                <ul>
                                  <li>Firefox (marche parfois, parfois pas)</li>
                                </ul>
                                Et ceux qui ne le supportent pas du tout :
                                <ul>
                                  <li>Konqueror</li>
                                  <li>Google Chrome</li>
                                </ul></small><br></font>',
'conf_refresh_question' => 'Quel est le taux de rafra&icirc;chissement des messages (en millisecondes) ?',
'conf_nb_lignes_question' => 'Combien de messages doit-on afficher dans le panneau de discussion ?',

'configuration_bdd' => 'Base de Donn&eacute;es',
'conf_bdd_question' => 'Une mini base de donn&eacute;es est cr&eacute;&eacute;e (dans le dossier "<em>/mercure/local</em>") pour g&eacute;rer les messages &eacute;chang&eacute;s',
'conf_general_bdd_bdd' => 'au format SQLite<br><small>%info_sqlite%</small>',
'conf_general_bdd_txt' => 'au format texte',
'conf_bdd_item_limit' => 'Limiter le nombre de messages dans la base &agrave<br><small>("0" indique pas de limite)</small>',
'conf_bdd_purge_question' => 'Purge automatique des anciens messages s\'ils ont plus de (en jours)<br><small>("0" indique pas de purge automatique)</small>',
'conf_bdd_info_sqlite_nok' => '<font color=orange>(SQLite n\'est pas actif dans votre PHP)</font>',
'conf_bdd_info_sqlite_ok' => '<font color=green>(SQLite est actif dans votre PHP)</font>',


/*
      =========================================================================
      DESINSTALLATION
      =========================================================================
*/
'procedure_remove' => '<strong>Proc&eacute;dure de d&eacute;sinstallation</strong><br /><br />
                      <ul>
                        <li><strong>M&eacute;thode manuelle</strong>
                          <ul>
                            <li>1. D&eacute;sactiver le plugin dans l\'espace priv&eacute; de SPIP<br>(Menu "Configuration" -> "Gestion des plugins")</li>
                            <li>2. Se connecter par FTP &agrave; votre site SPIP</li>
                            <li>3. Se rendre dans le r&eacute;pertoire des plugins<br>(par exemple /var/www/votre-site-spip/plugins)</li>
                            <li>4. D&eacute;truire le dossier "<em>mercure</em>"</li>
                          </ul>
                        </li>
                        <br /><br />
                        <li><strong>M&eacute;thode automatique</strong>
                          <ul>
                            <li>1. Utiliser le lien dans le cadre ci-apr&egrave;s<br /><br />Et voil&agrave;, c\'est fini...</li>
                          </ul>
                        </li>
                      </ul>
                      ',                     
'procedure_remove_reste_conversation' => '<font color=red><strong>ATTENTION</strong></font><br /><br />
                                          Il reste un fichier de conversations dans le r&eacute;pertoire /plugins/mercure/local !<br /><br />
                                          Il comporte peut-&ecirc;tre des informations que vous d&eacute;sirez conserver.<br />
                                          Pour ce faire, vous pouvez utiliser le lien ci-dessous pour le t&eacute;l&eacute;charger localement sur votre ordinateur.<br />
                                         ',
'lien_remove_reste_conversation_SQLite' => 'Visualiser ou t&eacute;l&eacute;charger le fichier <em>SQLite</em>',
'lien_remove_reste_conversation_TXT' => 'Visualiser ou t&eacute;l&eacute;charger le fichier <em>Texte</em>',
'procedure_automatique' => '<strong>Proc&eacute;dure automatique de d&eacute;sinstallation</strong><br /><br />En cliquant sur le lien ci-dessous, vous allez enlever le plugin "<em>Mercure</em>", ainsi que toutes ses r&eacute;f&eacute;rences et m&eacute;ta-donn&eacute;es dans la base de donn&eacute;es de votre site sous SPIP.<br /><br />',
'destruction_du_plugin' => 'D&eacute;sinstaller le plugin "<em>Mercure</em>"',
'procedure_remove_action_begin' => '<strong>D&eacute;but de la proc&eacute;dure automatique</strong><br /><br />',
'procedure_remove_action_delete_tmp_mercure_begin' => '<li>Destruction du dossier "<em>/plugin/mercure/local</em>"... ',
'procedure_remove_action_delete_tmp_mercure_end' => 'effectu&eacute;e.</li><br />',
'procedure_remove_action_delete_plugin_mercure_begin' => '<li>Destruction du dossier "<em>/plugins/mercure</em>"... ', 
'procedure_remove_action_delete_plugin_mercure_end' => 'effectu&eacute;e.</li><br />', 
'procedure_remove_action_delete_plugin_mercure_problem' => '<font color="red">non effectu&eacute;e</font> : il reste des fichiers (probl&egrave;me de droits d\'acc&eacute;s &agrave; priori) que vous devez d&eacute;truire manuellement avec un acc&eacute;s FTP !</li><br />',
'procedure_remove_action_delete_meta_mercure_begin' => '<li>Destruction des m&eacute;ta-donn&eacute;es "<em>Mercure</em>"... ',
'procedure_remove_action_delete_meta_mercure_end' => 'effectu&eacute;e.</li><br />',  
'procedure_remove_action_end' => '<strong>Fin de la proc&eacute;dure automatique</strong><br /><br />La d&eacute;sinstallation du plugin "<em>Mercure</em>" est termin&eacute;e.<br /><br />',

/*
      =========================================================================
      MESSAGES TCHAT
      =========================================================================
*/



/*
      =========================================================================
      MESSAGES DIVERS
      =========================================================================
*/
'derniere_modification' => 'Derni&egrave;re modification',
'zzzZZZzzz' => ''
);

?>
