<?php

// This is a SPIP module file  --  Ceci est un fichier module de SPIP

$GLOBALS['i18n_spiplistes_en'] = array(


//_ 
'_aide' => '<p>SPIP-Lists allows you to send newsletters or other automated messages to people who have signed up. </p> <p>You can write your message in simple text format, in HTML, or apply a stencil - called "patron" in SPIP language - which can include SPIP-code.</p>
<p>A sign-up form on the webpage frontend allows users to decide themselves to which newsletters they want to subscribe and the format in which they want to receive it (HTML or text).</p> 
<p>Every message is automatically converted from HTML into text format for those who prefer receiving it in this format.</p><p><b>Note:</b><br/>Sending out a newsletter can take several minutes: The batches are sent out message by message while the users browse the site. You can manually speed-up the sending process by clicking on the "Dispatches follow-up" link during the dispatch. </p>',

// A
'abo_1_lettre' => 'Newsletter',
'abonnement_0' => 'Subscription',
'abonnement'=>'You wish to modify your subscription to the newsletter',
'abonnement_bouton'=>'Modify your subscription',
'abonnement_cdt' => '<a href=\'http://bloog.net/spip-listes/\'>SPIP-Lists</a>' ,
'abonnement_change_format'=>'You can change the format in which you will receive the newsletter or cancel your subscription: ',
'abonnement_mail' => 'To modify your subscription, please visit the following webpage: ',
'abonnement_mail_passcookie' => '(this is an automatically generated e-mail)
To modify your subscription to the newsletter of this site:
@nom_site_spip@ (@adresse_site@)

Please visit the following page:

@adresse_site@/spip.php?page=abonnement&d=@cookie@

There, you can confirm any modification to your subscription',
'abonnement_modifie'=>'Your modifications have been registered.',
'abonnement_nouveau_format'=>'The format in which you will receive the newsletter will be from now on: ',
'abonnement_titre_mail'=>'Modify your subscription',
'abonnement_texte_mail'=>'Please specify which e-mail address you used when signing up previously. 
You will receive an e-mail with a link to the page where you can modify your subscription.',
'abonner' => 'Subscribe',
'abonnes_liste_int' => '<br />Subscribers to internal lists: ',
'abonnes_liste_pub' => '<p>Subscribers to public lists: ',
'actualiser' => 'Update',
'a_destination' => 'to ',
'adresse' => 'Enter here the reply-to e-mail address (otherwise the default webmaster\'s address will be used):',
'adresse_envoi' => 'Default sender\'s address',
'adresses_importees' => 'Imported addresses',
'adresse_smtp' => 'e-mail address of SMTP <i>sender</i>',
'aff_redac' => 'Editing in progress',
'aff_encours' => 'Sending in progress',
'aff_envoye' => 'Messages sent',
'aff_lettre_auto' => 'Sent newsletters',
'alerte_edit' => 'The following form can be used to modify the body of a message. You can choose to start by importing a stencil to generate the content of your message.',
'alerte_modif' => '<b>Once your message is displayed, you can modify its content </b>',
'annuler_envoi' => 'Cancel',
'article_entier' => 'Read the whole article',


//B

'bouton_listes' => 'Newsletters',
'bouton_modifier' => 'Modify this mail',

//C
'calcul_patron' => 'Based on a text version stencil',
'calcul_html' => 'Based on a html version of message',
'Cette_liste_est' => 'This list is',
'charger_patron' => 'Choose a stencil:',
'charger_le_patron' => 'Generate the message',
'choix_defini' => 'No specified choice.\n',
'Configuration' => 'Configuration',
'courriers' => 'Messages',

//D
'dans_jours' => 'in',
'definir_squel' => 'Choose a template to display',
'definir_squel_choix' => 'When editing a new message, SPIP-Lists allows you to load a stencil. Pressing a button allows will load the content of one of the stencils folder <b>/patrons</b> (located at the root of the plugin) templates into the message. <p><b>You can edit and modify these templates at will.</b></p> <ul><li>These templates may contain HTML code</li>
<li>This templates may contain SPIP loops</li>
<li>After loading a stencil, you can still modify the message before sending it out (for example to add text)</li>
</ul><p>The function "Load a stencil" allows you to use customised HTML code or to create newsletters by topic with the help of SPIP loops.</p><p>Warning: the template should not contain the tags body, head or html, only the HTML code and the SPIP loops.</p>',
'definir_squel_texte' => 'If you have an FTP access to your website, you can add SPIP templates in the folder /patrons at the root of the SPIP-site.',
'dernier_envoi'=>'Last dispatch was',
'devenir_redac'=>'Become an editor of this website',
'devenir_abonne'=>'Subscribe to this website',
'desabonnement_valid'=>'The following e-mail address is not subscribed anymore' ,
'pass_recevoir_mail'=>'You will receive a confirmation e-mail detailing how to modify your subscription.',
'desabonnement_confirm'=>'You are about to cancel your subscription to the newsletter',
'date_depuis'=>'since @delai@', 
'discussion_intro' => 'Hello, <br />Here are the discussions started on the site',

//E
'email' => 'E-mail',
'email_envoi' => 'Mail sending',
'envoi' => 'Send:',
'envoi_charset' => 'Charset of dispatch',
'envoi_date' => 'Dispatch date: ',
'envoi_debut' => 'Start of dispatch: ',
'envoi_fin' => 'End of dispatch: ',
'envoi_nouv' => 'Newsletter dispatch',
'envoi_patron' => 'stencil dispatch',
'envoi_program' => 'Scheduled dispatch',
'envoi_smtp' => 'When sending via SMTP, this field indicate the sender\'s address.',
'envoi_texte' => 'If this message is all right, you can send it now',
'erreur_envoi' => 'Number of dispatch errors: ',
'erreur_install' => '<h3>error: spip-lists is not installed correctly!</h3>',
'erreur_install2' => '<p>Verify the installation steps, especially whether you renamed <i>mes_options.txt</i> into <i>mes_options.php</i> or not.</p>',
'exporter' => 'Export the subscribers list',

//F
'faq' => 'FAQ',
'forum' => 'Forum',
'ferme' => 'This discussion has been closed',
'form_forum_identifiants' => 'Confirm',
'form_forum_identifiant_confirm'=>'Your subscription has been recorded. You will receive a confirmation e-mail.',
'format' => 'Format',
'format2' => 'Format:',
'format_html' => 'HTML format: ',
'format_texte' => 'Text format: ',

//H
'Historique_des_envois' => 'Summary of dispatches',

//I
'info_auto' => 'SPIP-Lists for SPIP can automatically send messages to the subscribers with the latest news of your website (articles and news items recently published).',
'info_heberg' => 'Some hosts disable automatic sending of e-mail messages from their servers. In this case, the following features of SPIP-Lists will not work',
'info_nouv' => 'You have activated the automatic e-mail messages',
'info_nouv_texte' => 'The next message will be sent in @proch@ days',
'inscription_mail_forum' => 'This is your identification code to login to the website @nom_site_spip@ (@adresse_site@)',
'inscription_mail_redac' => 'This is your identification code to login to the website @nom_site_spip@ (@adresse_site@) and to the private area (@adresse_site@/ecrire)',
'inscription_visiteurs' => 'The subscription allows you to acces to the restricted parts of this website, to post messages in the forum and to receive the newsletters',

'inscription_redacteurs' =>'The editorial backend is open to website visitors after registration. Once registered, you can read and comment on articles submitted for publication, submit your own articles and participate in all forums. The subscription also allows you to access the restricted parts of the website and to receive the newsletters.',
'import_export' => 'Import / Export',

//J
'jours' => 'days',

//L
'la_liste' => 'the list:',
'langue' => '<strong>Language:</strong>&nbsp;',
'lire' => 'Read',
'Listes_de_diffusion' => 'Mailing lists',
'log' => 'Logs',
'login' => 'Login',
'logout' => 'Logout',
'lot_suivant' => 'Send the next batch',
'lieu' => 'Location',
'lettre_d_information' => 'Newsletter',


//M
'mail_format' => 'You are now subscribed to the newsletter of the website @nom_site_spip@. You will receive the newsletter in the format',
'mail_non' => 'Your e-mail address is not subscribed to the newsletter of  @nom_site_spip@',
'message_arch' => 'Message has been archived;',
'messages_auto' => 'Automated messages',
'messages_auto_texte' => '<p>The default configuration of the template will automatically send messages with the list of articles and news published since the last newsletter.</p><p>you can customise the newsletter by adding your own logo and background image for the headings in the file called <b>"nouveautes.html"</b> (located in the /dist folder).</p>',
'message_redac' => 'Editing in progress and ready to send',
'message_en_cours' => 'Sending in progress',
'message_type' => 'E-mail',
'membres_liste' => 'Members list',
'membres_groupes' => 'User groups',
'membres_profil' => 'Profile',
'membres_messages_deconnecte' => 'Connect to check private messages',
'membres_sans_messages_connecte' => 'You dont have any new messages',
'membres_avec_messages_connecte' => 'You have @nombres@ new message(s)',
'message' => 'Message: ',
'message_date' => 'Sent on ',
'message_sujet' => 'subjest ',
'messages' => 'Messages',
'Messages_automatiques' => 'Scheduled automated messages',
'messages_derniers' => 'Lastest messages',
'messages_forum_clos' => 'Forum disabled',
'messages_nouveaux' => 'New messages',
'messages_pas_nouveaux' => 'No new messages',
'messages_non_lus_grand' => 'No new messages',
'messages_repondre' => 'New reply',
'messages_voir_dernier' => 'Read last message',
'methode_envoi' => 'Sending method',
'mettre_a_jour' => '<h3>SPIP-lists will update</h3>',
'moderateurs' => 'Moderators',
'modifier' => 'Modify',
'mis_a_jour' => 'Updated', 

//n
'nb_abonnes_plur' => ' subscribers',
'nb_abonnes_sing' => ' subscriber',
'nbre_abonnes' => 'Number of subscribers: ',
'nom' => 'Username',
'nombre_lot' => 'Number of dispatches per batch',
'Nouveau_courrier' => 'New message',
'nouveaute_intro' => 'Hello, <br />Here are the latest publications in the site',
'nouveaux_messages' => 'New messages',
'Nouvelle_liste_de_diffusion' => 'New list',
'numero' => 'No.&nbsp;',

//P
'par_date' => 'By registration date',
'patron_disponibles' => 'Stencils available',
'Patrons' => 'Stencils',
'pas_sur' => '<p>If you are not sure, choose the PHP mail function.</p>',
'photos' => 'Photos',
'php_mail' => 'Use the PHP mail() function',
'poster' => 'Post a message',
'publie' => 'Published on',

//R
'recherche' => 'Search',
'regulariser' => 'put in order the unsubscribed for lists...<br />',
'revenir_haut' => 'Top of the page',
'reponse' => 'Reply to message',
'reponse_plur' => 'replies',
'reponse_sing' => 'reply',
'retour' => 'E-mail address of the list administrator (reply-to)',

//S
'smtp' => 'Use SMTP',
'spip_ident' => 'Requires identification',
'smtp_hote' => 'Host',
'spip_listes' => 'Spip lists',
'smtp_port' => 'Port',
'suivi' => 'Subscribers',
'Suivi_des_abonnements' => 'Subscriptions follow up',
'sujet_nouveau' => 'New subject',
'sujet_auteur' => 'Author',
'sujet_courrier' => '<b>Message subject</b> (required)<br />',
'sujet_courrier_auto' => 'Subject of automated mail: ',
'sujet_visites' => 'Visits',
'sujets' => 'Sujects',
'sujets_aucun' => 'No Topics in this forum yet',
'site' => 'Website',
'sujet_clos_titre' => 'Topic closed',
'sujet_clos_texte' => 'This topic has been closed. You can not post any further message',
'sur_liste' => 'to the list',

//T
'texte_boite_en_cours' => 'SPIP-List is sending out a newsletter. <p>This message will disappear once the dispatch is completed.</p>',
'texte_courrier' => '<b>Message text</b> (HTML authorised)',
'texte_contenu_pied' => '<br />(Message added at the bottom of each e-mail when sent)<br />',
'texte_lettre_information' => 'This is the newsletter of',
'texte_pied' => '<p><b>Footer text</b>',
'Tous_les' => 'Every',

//V
'version_html' => '<b>HTML version</b>',
'version_texte' => '<b>Text version</b>',
'voir' => 'read',
'vous_pouvez_egalement' => 'You can also',
'vous_inscrire_auteur' => 'sign up as author',
'voir_discussion' => 'Show discussion',

// ====================== spip_listes.php3 ======================
'abon' => 'SUBSCRIBERS',
'abon_ajouter' => 'ADD A SUBSCRIBER &nbsp; ',
'abonees' => 'all subscribers',
'abonne_listes' => 'This contact has been added to the following listes',
'abonne_aucune_liste' => 'Not subscribed to any list',
'abonnement_simple' => '<b>Simple subscription: </b><br /><i>Subscribers receive a confirmation message</i>',
'abonnement_code_acces' => '<b>Subscription with login: </b><br /><i>Additionally subscribers receive a username and password to identify on the website. </i>',
'abonnement_newsletter' => '<b>Subscription to the newsletter. </b>',
'acces_a_la_page' => 'You dont have access to this part of the website.',
'adresse_deja_inclus' => 'Your e-mail address is already subscribed.',
'autorisation_inscription' => 'SPIP-Lists has now activated visitors sign-up. ',

'choisir' => 'Choose',
'choisir_cette' => 'Choose this list',
'confirme_envoi' => 'Please confirm',

'date_act' => 'Data has been saved',
'date_ref' => 'Reference date',
'desabo' => 'unsubscribe',
'desabonnement' => 'Unsubscribe',
'desabonnes' => 'No more subscribed',
'desole' => 'Sorry',
'destinataire' => 'Destination',
'destinataires' => 'Destinations',

'efface' => 'has been deleted from the lists and the database',
'efface_base' => 'has been deleted from the lists and the database',
'email_adresse' => 'Test e-mail address',
'email_collec' => 'Write a message',
'email_test' => 'Send a test message',
'email_test2' => 'the test e-mail: ',
'email_test_liste' => 'Send to a list',
'email_tester' => 'Test by e-mail',
'env_esquel' => 'Scheduled sending of the stencil',
'env_maint' => 'Send now',
'envoyer' => 'send the e-mail',
'envoyer_a' => 'Send to ',
'erreur' => 'Error',
'erreur_import' => 'The import file has an error at line ',

'format_date' => 'Y/m/d',

'html' => 'HTML',

'importer' => 'Import a subscribers list',
'importer_fichier' => 'Import a file',
'importer_fichier_txt' => '<p><b>Your subscribers list needs to be in simple text format that contains only one e-mail address per line</b></p>',
'importer_preciser' => '<p>Please specify the lists to which you want to add the addresses and the message format (HTML or txt)</p>',
'inconnu' => 'not subscribed to the mailing list anymore',

'liste_diff_publiques' => 'Public mailing lists<br /><i>The web page of the public site allows the subscription to these lists.</i>',
'liste_sans_titre' => 'List without title',
'listes_internes' => 'Internal mailing lists<br /><i>When sending a message you can select these lists as destination</i>',
'listes_poubelle' => 'Lists to the dustbin',
'lock' => 'Active lock: ',
'liste_numero' => 'LIST NUMBER',

'mail_a_envoyer' => 'Number of messages to send: ',
'mail_tache_courante' => 'Number of e-mails sent for the current task: ',
'messages_auto_envoye' => 'Automatically sent messages',
'message_en_cours' => 'Editing in progress',
'message_presque_envoye' =>'This message is ready to be sent out',
'mode_inscription' => 'Specify the subscription mode for your visitors',
'modif_envoi' => 'You can modify it or ask to send it out',
'modifier_liste' => 'Modify this list:',

'nb_abonnes' => 'On the list: ',
'nb_inscrits' => 'On the site: ',
'nb_listes' => 'Subscribe to all lists: ',
'non_program' => 'There is no message to send out on this list.',
'nouvelle_abonne' => 'The following subscriber has been added to the list',

'pas_acces' => 'You dont have access to this webpage.',
'plus_abonne' => ' has been unsubscribed from this list ',
'prochain_envoi_aujd' => 'Next message to be sent out today',
'prochain_envoi_prevu' => 'Next message that will be send out',
'prochain_envoi_prevu_dans' => 'Next message that will be sent out in',
'prog_env' => 'Schedule an automated dispatch',
'prog_env_non' => 'Do not schedule a dispatch',
'program' => 'Schedule automated sending of message',
'plein_ecran' => '(Full screen)',

'reinitialiser' => 'initialise',
'remplir_tout' => 'All fields must be filled',
'repartition' => 'Distribution',
'retour_link' => 'Back',

'sans_envoi' => 'Warning, there is no such e-mail address on the subscribers list, <br /> no message could be sent. Please retry<br /><br />',
'squel' => 'Stencil: &nbsp;',
'statut_interne' => 'Internal',
'statut_publique' => 'Public',
'suivi_envois' => 'Dispatches follow-up',
'supprime_contact' => 'Delete this contact permanently',
'supprime_contact_base' => 'Delete permanently from the database',

'tableau_bord' => 'Dashbord',
'texte' => 'Text',
'toutes' => 'All subscribers',
'txt_abonnement' => '(Specify here the subscription text to this list that will be published on the webpage if the list is activated)',
'txt_inscription' => 'Subscription text: ',

'une_inscription' => 'One subscriber found',

'val_texte' => 'Text',
'version' => 'version',
'voir_historique' => 'Show sent messages',



// ====================== inscription-listes.php3 / abonnement.php3 ======================

'abo_listes' => 'Subscription',
'acces_refuse' => 'You dont have access to this site anymore',

'confirmation_format' => ' in format ',
'confirmation_liste_unique_1' => 'You have been subscribed to the mailing list of the site',
'confirmation_liste_unique_2' =>'You have chosen to receive the messages of the following newsletter:',
'confirmation_listes_multiples_1' => 'You have been subscribed to the following newsletters ',
'confirmation_listes_multiples_2' => 'You have chosen to receive the newsletters sent to the following mailing lists:',

'erreur_adresse' => 'Error, the e-mail address you provided is not valid',

'infos_liste' => 'Information on this list',


// ====================== spip-meleuse.php3 ======================

'contacts' => 'Number of contacts',
'contacts_lot' => 'Contacts of this batch',
'editeur' => 'Editor: ',
'envoi_en_cours' => 'Processing dispatch',
'envoi_tous' => 'Send to all subscribers',
'envoi_listes' => 'Send to all subscribers to the list: ',
'envoi_erreur' => 'Error: SPIP-Lists cannot find a destination for this message',
'email_reponse' => 'Reply e-mail: ',
'envoi_annule' => 'Dispatch cancelled;',
'envoi_fini' => 'Dispatches completed',
'erreur_destinataire' => 'Destination error: no message has been sent',
'erreur_sans_destinataire' => 'Error: no e-mail address can be found for this message',
'erreur_mail' => 'Error: sending impossible (make sure that the php mail() is enabled)',

'forcer_lot' => 'Send the next batch',

'non_courrier' => 'No more messages to send',
'non_html' => 'It looks like your e-mail client is unable to correctly display the graphical version (HTML) of this message.',
'sans_adresse' => 'No e-mail has been sent -> please specify a reply-to address',



// ====================== inc_import_patron.php3 ======================

'confirmer' => 'Confirm',

'lettre_info' => 'The newsletter of ',

'patron_detecte' => '<p><strong>Detected stencil for the text version</strong><p>',

'patron_erreur' => 'With the current parameters, the stencil finds no news to be send',



// ====================== listes.html ======================

'abonees_titre' => 'Subscribers',


// ====================== inc-presentation.php3 ======================

'listes_emails' => 'Newsletters',


// ====================== mes-options.php3 ======================


'options' => 'radio|simple|Format :|Html,Text,unsubscribe|html,text,no',

// ====================== mes-options.php3 ======================

'bonjour' => 'Hello,',

'inscription_response' => 'Your e-mail address has been added to the mailing list of ',
'inscription_responses' => 'Your e-mail address has been added to the mailing lists of ',
'inscription_liste' => 'You will receive the newsletter sent to the following mailing list : ',
'inscription_listes' => 'You will receive the newsletters sent to the following mailing lists : ',
'inscription_format' => ', in format ',

'info_1_liste' => '1 list',
'info_liste_2' => 'lists'

);

// English translation: Simon simon@okko.org reviewed by George

?>