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
'heure_locale' => 'Local time',
'personnes_en_ligne' => 'People Online <small>(from you)</small>',
'aucune_personne_en_ligne' => 'No other person Online from you...',
'abrv_administrateur' => 'Administrator',
'abrv_redacteur' => 'Redactor',
'abrv_visiteur' => 'Visitor',
'personnes_en_detail' => 'Detail',
'bouton_envoyer' => '\'S\'end',
'send_accesskey' => 's',



/*
      =========================================================================
      ONGLETS
      =========================================================================
*/
'onglet_mercure_pg' => 'Talk - Chat',
'onglet_mercure_doc' => 'Documentation',
'onglet_mercure_conf' => 'Configuration',
'onglet_mercure_remove' => 'Remove',

/*
      =========================================================================
      PARTIE GAUCHE
      =========================================================================
*/
'date_serveur_mysql' => 'MySQL server date', 
'date_serveur_php' => 'PHP server date', 
'page_phpinfo' => 'Phpinfo page',
'mercure_avertissement' => '<strong>WARNING</strong><br /><br />
                           This application is provided "<em>as is</em>" and does not engage the responsibility of the author in case of malfunction.<br /><br />
                           Thank you.',
'signature_plugin' => '<b>Redactor\'s Tchat - @version@</b><br />(07/2009)<br />
						Little gadget... Chating in the back room.<br />
						By Patrick Kuchard - <a href=\'http://www.encyclopedie-incomplete.com\'>encyclopedie-incomplete.com</a><br />
						<br />It is not necessary...<br />But... this is not useless !',						
'haut_page' => 'Top of page', 
'sound_on' => 'Sound notify On',
'sound_off' => 'Sound notify Off',
'all_messages' => 'List of all messages',

/*
      =========================================================================
      DOCUMENTATION
      =========================================================================
*/
'documentation' => '<strong>Documentation</strong><br /><br />The most recent documentation can be found here :<br /><br /><ul>
<li><strong>On Spip-Contrib</strong><br />&nbsp;&nbsp;&nbsp;<a href="http://contrib.spip.net/article3154" target="_blank" title="Ouvrir dans une nouvelle fen&ecirc;tre">www.spip-contrib.net</a></li><br />
<li><strong>On the author\'s site</strong><br />&nbsp;&nbsp;&nbsp;<a href="http://www.encyclopedie-incomplete.com/?Plugin-Mercure-Redactor-s-Chat" target="_blank" title="Ouvrir dans une nouvelle fen&ecirc;tre">www.encyclopedie-incomplete.com</a></li>
</ul><br /><br />',
'minidoc' => '<strong>Some elements</strong><br /><br />
              <ul>
                <li>Only administrators can configure and uninstall the plugin.</li>
                <li>All editors have access to it and can use it.</li>
              </ul>
              <br />
              <em>Mercure</em> allows a conversation in the form of a chat between editors connected to the private area.<br /><br />
              On the left side of your screen ("<em>Talk - Chat</em>" tab), you have access to editors connected to the same moment that you and which are likely to use <em>Mercure</em>.<br />
              You can by clicking on one of the nicknames, inviting him to a discussion in this chat area.<br /><br />
              If the administrator has selected the sound notification of a new messages, you can enable or disable this in the "<em>Talk - Chat</em>" tab.<br /><br />
              You can also view past discussions by clicking the appropriate button in the "<em>Talk - Chat</em>" tab.<br /><br />
             ',

/*
      =========================================================================
      CONFIGURATION
      =========================================================================
*/
'configuration_first_use' => 'Welcome to the configuration panel of "<strong><em>Mercure</em></strong>".<br /><br />
                              You use this plugin for the first time, take a few moments to configure its operations.<br /> 
                              You will find in the blocks below the elements to customize this plugin.<br /><br /> 
                              You can at will modify this features in the future.                              
                             ',
'configuration_after_use' => 'Use the elements in the blocks below to customize this plugin.',
'configuration_general' => 'General',
'conf_general_menu_question' => 'Where do you want to see "<em>Mercure</em>" ?',
'conf_general_menu_accueil' => 'à suivre',
'conf_general_menu_naviguer' => 'édition',
'conf_general_menu_forum' => 'forum',
'conf_general_menu_auteurs' => 'auteurs',
'conf_general_menu_statistiques_visites' => 'statistiques<br><small>(not accessible to authors)</small>',
'conf_general_menu_configuration' => 'configuration<br><small>(not accessible to authors)</small>',
'conf_general_menu_aide_index' => 'aide',
'conf_general_menu_visiter' => 'visiter',

'conf_maj_connectes_question' => 'Refresh the connected redactors panel every (in minutes)',

'configuration_messages' => 'Messages',
'conf_notify_question' => 'Do you want a sound notifying a new message ?',
'conf_oui' => 'Yes',
'conf_non' => 'No',
'conf_volume_question' => 'Play it at a volume of (0..100) ?',
'conf_notify_sound_question' => 'What sound to play ?',
'conf_notify_avertissement' => '<font color="gray"><br><small><strong>Note</strong> :<br>Browsers that support the notification of new messages are :
                                <ul>
                                  <li>Opera</li>
                                  <li>Internet Explorer</li>
                                  <li>Safari</li>
                                </ul>
                                Those who bear almost :
                                <ul>
                                  <li>Firefox (works sometimes, sometimes not)</li>
                                </ul>
                                And those who do not support at all :
                                <ul>
                                  <li>Konqueror</li>
                                  <li>Google Chrome</li>
                                </ul></small><br></font>',
'conf_refresh_question' => 'Refresh rate of the message board (in milliseconds)',
'conf_nb_lignes_question' => 'How many messages to display in the chat panel ?',

'configuration_bdd' => 'Database',
'conf_bdd_question' => 'A mini-database is created (in the "<em>/mercure/local</em>" folder) to manage the exchanged messages',
'conf_general_bdd_bdd' => 'SQLite format<br><small><small>%info_sqlite%</small></small>',
'conf_general_bdd_txt' => 'Text format',
'conf_bdd_item_limit' => 'Limit the number of messages in the database at<br><small>("0" for no limit)</small>',
'conf_bdd_purge_question' => 'Automatic purge of old messages if they are older than (in days)<br><small>("0" for no purge)</small>',
'conf_bdd_info_sqlite_nok' => '<font color=orange>(SQLite is not active in your PHP)</font>',
'conf_bdd_info_sqlite_ok' => '<font color=green>(SQLite is active in your PHP)</font>',

/*
      =========================================================================
      DESINSTALLATION
      =========================================================================
*/
'procedure_remove' => '<strong>Uninstall procedure</strong><br /><br />
                      <ul>
                        <li><strong>Manual method</strong>
                          <ul>
                            <li>1. Disable the plugin in the back office of SPIP<br>(Menu "Configuration" -> "Manage plugins")</li>
                            <li>2. Connect via FTP to your SPIP site</li>
                            <li>3. Go the plugins floder of SPIP<br>(eg /var/www/your-spip-site/plugins)</li>
                            <li>4. Delete the "<em>mercure</em>" folder</li>
                          </ul>
                        </li>
                        <br /><br />
                        <li><strong>Automatic method</strong>
                          <ul>
                            <li>1. Use the link in the box below<br /><br />Ok, it\'s finished...</li>
                          </ul>
                        </li>
                      </ul>
                      ',                                  
'procedure_remove_reste_conversation' => '<font color=red><strong>CAUTION</strong></font><br /><br />
                                          There is still a conversation file in the /plugin/mercure/local folder !<br /><br />
                                          It may include information that you want to keep.<br />
                                          To do this, you can use the link below to download it locally on your computer.<br />                                          
                                         ',
'lien_remove_reste_conversation' => 'View or download the file',
'lien_remove_reste_conversation_SQLite' => 'View or download the <em>SQLite</em> file',
'lien_remove_reste_conversation_TXT' => 'View or download the <em>Text</em> file',
'procedure_automatique' => '<strong>Automatic uninstall</strong><br /><br />
                            By clicking on the link below, you will remove the "<em>Mercure</em>" plugin and all references and metadata in the database of your SPIP site.<br /><br />',
'destruction_du_plugin' => 'Uninstall the "<em>Mercure</em> plugin"',
'procedure_remove_action_begin' => '<strong>Beginning of the automatic process</strong><br /><br />',
'procedure_remove_action_delete_tmp_mercure_begin' => '<li>Removing the "<em>/plugin/mercure/local</em>" folder... ',
'procedure_remove_action_delete_tmp_mercure_end' => 'finished.</li><br />',
'procedure_remove_action_delete_plugin_mercure_begin' => '<li>Removing the "<em>/plugins/mercure</em>" folder... ', 
'procedure_remove_action_delete_plugin_mercure_end' => 'finished.</li><br />', 
'procedure_remove_action_delete_plugin_mercure_problem' => '<font color="red">not performed</font> : there are files (problem of access rights) that you must delete manually with an FTP access !</li><br />',
'procedure_remove_action_delete_meta_mercure_begin' => '<li>removing of the metadata of "<em>Mercure</em>"... ',
'procedure_remove_action_delete_meta_mercure_end' => 'finished.</li><br />',  
'procedure_remove_action_end' => '<strong>End of the automatic process</strong><br /><br />Uninstalling the "<em>Mercure</em>"plugin is completed.<br /><br />',

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
'derniere_modification' => 'Last modification',
'zzzZZZzzz' => ''
);

?>
