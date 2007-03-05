<?php

// This is a SPIP module file  --  Ceci est un fichier module de SPIP

$GLOBALS['i18n_spiplistes_en'] = array(


//_
'_aide' => '<p>SPIP-Listes allows you to send newsletters or other automatic messages to people who have signed up. </p> <p>You can write your message in simple text format, in HTML, or apply a master template - called "patron" in SPIP language - that can include SPIP-code.</p>
<p>A sign-up form on the webpage frontend allows users to decide themselves to which newsletters they want to subscribe and the format in which they want to receive it (HTML or text).</p> 
<p>Every message is automatically converted from HTML into text format for those who prefer receiving it in this format.</p>
<p><b>Note:</b><br/>Sending out a newsletter can take several minutes: The messages are sent out in batches that are sent out one by one. You can manually speed-up the sending process. </p>',



// A
'abo_1_lettre' => 'Newsletter',
'abonnement' => 'Subscription',
'abonnement'=>'You would like to change your subscription to the newsletter',
'abonnement_bouton'=>'Change your subscription',
'abonnement_cdt' => '<a href=\'http://bloog.net/spip-listes/\'>SPIP-Listes</a>' ,
'abonnement_change_format'=>'You can change the format in which you will receive the newsletter: ',
'abonnement_mail' => 'To change your subscription, please visit the following webpage: ',
'abonnement_mail_passcookie' => '(this is an automatically generated e-mail)
To change your subscription to the newsletter, please visit the following webpage:
@nom_site_spip@ (@adresse_site@)

To change your subscription, please visit the following webpage:

@adresse_site@/spip.php?page=abonnement&d=@cookie@',

'abonnement_modifie'=>'Your modifications have been registered.',
'abonnement_nouveau_format'=>'The format in which you will receive the newsletter will from now on be: ',
'abonnement_titre_mail'=>'Change your subscription',
'abonnement_texte_mail'=>'Please specify with which e-mail address you have signed up previously. 
You will receive an e-mail with a link to the webpage where you can modify your subscription.',
'abonner' => 'Subscribe',
'actualiser' => 'Update',
'adresse' => 'Specify here the reply-to e-mail address (otherwise the default address of the webmaster will be used):',
'adresses_importees' => 'Imported addresses',
'aff_redac' => 'Editing in progress',
'aff_encours' => 'Sending in progress',
'aff_envoye' => 'Messages sent',
'aff_lettre_auto' => 'Sent newsletters',
'aff_envoye' => 'Sent messages',
'alerte_edit' => 'Note: This message can be modified by every administrator of the website and received by all subscribers. Do use this newsletter only to announce special events of your website.',
'alerte_modif' => '<b>Once your message is saved, you can still modify its content </b>',
'annuler_envoi' => 'Cancel',











//B

//C
'Cette_liste_est' => 'This list is',
'charger_patron' => 'Choose a master template (patron):',
'charger_le_patron' => 'Load a master template (patron)',
'Configuration' => 'Configuration',
'courriers' => 'Message',

//D
'definir_squel' => 'Choose a template to visualise',
'definir_squel_choix' => 'When editing a new message, SPIP-Listes allows you to load a  template. Hitting a button allows you to load the content of a template into the message <b>. <b>/template</b> (located at the root of your SPIP-website). <p><b>You can edit and modify these templates as you like it.</b></p> <ul><li>These templates may contain HTML code</li>
<li>These templates may contain SPIP-loops (boucles)</li>
<li>After loading a master template (patron), you can still modify the message before sending it out (for example to add text)</li>
</ul><p>The function "Load a master template (patron)" allows you to use personalized HTML code or to create specified through the SPIP loops (boucles).</p><p>Note: the template should not include the tags body, head or html, but only the HTML code and the SPIP loops (boucles).</p>',
'definir_squel_texte' => 'If you have an FTP access to your website, you can add SPIP templates in the folder /patrons at the root of the SPIP-site.',
'devenir_redac'=>'Become an editor of this website',
'devenir_abonne'=>'Subscribe to this website',
'desabonnement_valid'=>'The following e-mail address is not subscribed anymore' , 'pass_recevoir_mail'=>'You will receive a confirmation e-mail specifying how to change your subscription. ',
'desabonnement_confirm'=>'You are about to cancel your subscription to the newsletter',
'date_depuis'=>'since @delai@', 



//E
'email' => 'E-mail',
'envoi' => 'Send :',
'envoi_nouv' => 'Send newsletter',
'envoi_program' => 'Send automatic;',
'envoi_texte' => 'If this message is ok, you can send it now',
'exporter' => 'Export the subscribers list',

//F
'faq' => 'FAQ',
'forum' => 'Forum',
'ferme' => 'This list has been closed',
'form_forum_identifiants' => 'Confirm',
'form_forum_identifiant_confirm'=>'Your subscription has been registered. You will receive a confirmation e-mail.',
'format' => 'Format',






//H
'Historique_des_envois' => 'Messages',

//I
'info_auto' => 'SPIP-Listes for SPIP can automatically send messages to the subscribers with the latest news of your website (articles and news items recently published).',
'info_heberg' => 'Certain web service providers deactivate automatic e-mail messages from their servers. In this case, the following features of SPIP-Listes will not work',
'info_nouv' => 'You have activated the automatic e-mail messages',
'info_nouv_texte' => 'The next message will be sent in @proch@ days',
'inscription_mail_forum' => 'This is the identification code to connect yourself to the website @nom_site_spip@ (@adresse_site@)',
'inscription_mail_redac' => 'This is the identification code to connect yourself to the website @nom_site_spip@ (@adresse_site@) and to the editors backend (@adresse_site@/ecrire)',
'inscription_visiteurs' => 'The subscription allows you to acces to the restricted parts of this website, to post messages in the forum and to receive the newsletters',

'inscription_redacteurs' =>'The editor\'s backend is open to website visitors after registration. Once registered, you can read and comment on articles submitted for publication, submit your own articles and participate in all forums. The subscription also allows you to access to the restricted parts of the website and to receive the newsletters.',
'import_export' => 'Import / Export',

//J
'jours' => 'days',

//L
'Listes_de_diffusion' => 'Mailing lists',
'login' => 'Login',
'logout' => 'Logout',
'lot_suivant' => 'Send the next batch',
'lieu' => 'Location',
'lettre_d_information' => 'Newsletter',










//M
'mail_format' => 'You are now subscribed to the newsletter of the website @nom_site_spip@. You will receive the newsletter in the format',
'mail_non' => 'Your e-mail address is not subscribed to the newsletter of  @nom_site_spip@',
'message_arch' => 'Message has been archived;',
'messages_auto' => 'Automatic messages',
'messages_auto_texte' => '<p>The default configuration of the template will automatically send messages with the list of articles and news published since the last newsletter.</p><p>you can change the layout of the newsletter with your own icon and a background image. for the titles in the file called; <b>"nouveautes.html"</b> at the root of your SPIP site.</p>',
'message_redac' => 'Editing in progress and ready to send',
'message_en_cours' => 'Sending in progress',
'message_type' => 'E-mail',
'membres_liste' => 'Subscribers',
'membres_groupes' => 'Subscriber groups',
'membres_profil' => 'Profil',
'membres_messages_deconnecte' => 'Connect to check private messages',
'membres_sans_messages_connecte' => 'You dont have any new messages',
'membres_avec_messages_connecte' => 'You have @nombres@ new message(s)',
'message' => 'Message : ',
'message_date' => 'Sent on ',
'message_sujet' => 'Title ',
'messages' => 'Messages',
'Messages_automatiques' => 'Programmed automatic messages',
'messages_derniers' => 'Last messages',
'messages_forum_clos' => 'Forum deactivated',
'messages_nouveaux' => 'New messages',
'messages_pas_nouveaux' => 'No new messages',
'messages_non_lus_grand' => 'No new messages',
'messages_repondre' => 'Answer',
'messages_voir_dernier' => 'Read previous message',
'moderateurs' => 'Moderators',
'modifier' => 'Modify',

//n
'nom' => 'Username',
'Nouveau_courrier' => 'New message',
'nouveaux_messages' => 'New messages',
'Nouvelle_liste_de_diffusion' => 'New list',

//P
'par_date' => 'By subscription date',
'patron_disponibles' => 'Master templates (patrons) available',
'Patrons' => 'Master template (patrons)',
'poster' => 'Post a message',

//R
'recherche' => 'Search',
'revenir_haut' => 'Top of the page',
'reponse' => 'Answering',
'retour' => 'E-mail address of the list administrator (reply-to)',

//S
'suivi' => 'Subscribers',
'Suivi_des_abonnements' => 'Follow up of subscribers',
'sujet_nouveau' => 'New subject',
'sujet_auteur' => 'Autor',
'sujet_visites' => 'Visits',
'sujets' => 'Sujects',
'sujets_aucun' => 'No message in this forum',
'site' => 'Website',
'sujet_clos_titre' => 'Subject closed',
'sujet_clos_texte' => 'This discussion has been closed. You can not post any further message',

//T
'texte_boite_en_cours' => 'SPIP-List is send out a newsletter. <p> You can accelerate the sending process of the batches with the link below.</p> <p>This message will disappear once the sending is completed.</p>',
'texte_lettre_information' => 'This is the newsletter of',
'Tous_les' => 'Every',

//V
'voir' => 'read',
'vous_pouvez_egalement' => 'You can also',
'vous_inscrire_auteur' => 'sign up as author',















// ====================== spip_listes.php3 ======================
'abon' => 'SUBSCRIBERS',
'abon_ajouter' => 'ADD A SUBSCRIBER &nbsp; ',
'abonees' => 'all subscribers',
'abonne_listes' => 'This contact has been added to the following listes',
'abonne_aucune_liste' => 'Subscribed to no list',
'abonnement_simple' => '<b>Simple sign-up : </b><br /><i>Subscribers receive a confirmation message</i>',
'abonnement_code_acces' => '<b>Subscription with login: </b><br /><i>Subscribers additionally receive a username and password to identify on website. </i>',
'abonnement_newsletter' => '<b>Subscription to the newsletter. </b>',
'acces_a_la_page' => 'You dont have access to this part of the website.',
'adresse_deja_inclus' => 'Your e-mail address is already subscribed.',
'autorisation_inscription' => 'SPIP-Listes has now activated visitor sign-up. ',

'choisir' => 'Choose',
'choisir_cette' => 'Choose this list',
'confirme_envoi' => 'Please confirm',





'date_act' => 'Data has been saved',
'date_ref' => 'Reference date',
'desabo' => 'unsubscribe',
'desabonnement' => 'unsubscribe',
'desabonnes' => 'Unsubscribed',
'desole' => 'Sorry',
'destinataire' => 'Destination',
'destinataires' => 'Destinations',

'efface' => 'has been deleted from the lists and the database',
'efface_base' => 'has been deleted from the lists and the database',
'email_adresse' => 'Test e-mail address',
'email_collec' => 'Collectif e-mail address',
'email_test' => 'Send a test message',
'email_test_liste' => 'Send to a list',
'email_tester' => 'Test by e-mail',
'env_esquel' => 'Programmed sending of the master template (patron)',
'env_maint' => 'Send now',
'envoyer' => 'send the e-mail',
'envoyer_a' => 'Send to',
'erreur' => 'Error',
'erreur_import' => 'The import file has an error at line ',

'format_date' => 'Y/m/d',

'html' => 'HTML',

'importer' => 'Import a subscribers list',
'importer_fichier' => 'Import a file',
'importer_fichier_txt' => '<p><b>Your subscribers list needs to be in simple text format that contains only one e-mail address per line</b></p>',
'importer_preciser' => '<p>Please specify the lists to which you want to add the addresses and the message format (HTML or txt)</p>',
'inconnu' => 'is not on the mailinglist',

'liste_diff_publiques' => 'Public mailinglists<br /><i>The webpage frontend allows the subscription to these lists.</i>',
'liste_sans_titre' => 'List without title',
'listes_internes' => 'Internal mailinglist<br /><i>When sending a message you can select these lists as destination</i>',
'listes_poubelle' => 'Trash lists',
'lock' => 'Lock active : ',
'liste_numero' => 'LIST NUMBER',

'mail_a_envoyer' => 'Number of messages to send: ',
'mail_tache_courante' => 'Number of e-mails sent for the current task: ',
'messages_auto_envoye' => 'Automatically sent messages',
'message_en_cours' => 'Editing in progress',
'message_presque_envoye' =>'This message is ready to be sent out',
'mode_inscription' => 'Specify the subscription mode of your visitors',
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
'prog_env' => 'Book a new automatic message',
'prog_env_non' => 'Don\'t sent out automatically',
'program' => 'Book automatic sending of message',
'plein_ecran' => '(Full screen)',

'reinitialiser' => 'initialise',
'remplir_tout' => 'All fields need to be specified',
'repartition' => 'Allocation',
'retour_link' => 'Back',

'sans_envoi' => 'Note, there is no such e-mail address on the subscribers list, <br /> no message could be sent. Please retry<br /><br />',
'squel' => 'Master template (patron) : &nbsp;',
'statut_interne' => 'Internal',
'statut_publique' => 'Public',
'suivi_envois' => 'Messages',
'supprime_contact' => 'Delete this contact',
'supprime_contact_base' => 'Delete this contact from the database',

'tableau_bord' => 'Dashbord',
'texte' => 'Text',
'toutes' => 'All subscribers',
'txt_abonnement' => '(Specify here the description of this list that will be published on the webpage if the list is activated)',
'txt_inscription' => 'Subscription text : ',

'une_inscription' => 'One subscriber;',

'val_texte' => 'Text',
'version' => 'version',
'voir_historique' => 'See send messages',



// ====================== inscription-listes.php3 / abonnement.php3 ======================

'abo_listes' => 'Subscription',
'acces_refuse' => 'You dont have access to this webpage anymore',

'confirmation_format' => ' in format ',
'confirmation_liste_unique_1' => 'You have been subscribed to the mailinglist of the website',
'confirmation_liste_unique_2' =>'You have chosen to receive the message of the following newsletter:',
'confirmation_listes_multiples_1' => 'You have been subscribed to the following newsletters ',
'confirmation_listes_multiples_2' => 'You have chosen to receive the newsletters sent to the following mailinglists:',

'erreur_adresse' => 'Error, the e-mail address you is not valid',

'infos_liste' => 'Information on this list',


// ====================== spip-meleuse.php3 ======================

'contacts' => 'Number of subscribers',
'contacts_lot' => 'Contacts of this batch',
'editeur' => 'Editor : ',
'envoi_en_cours' => 'Processing message',
'envoi_tous' => 'Send to all subscribers',
'envoi_listes' => 'Send to all subscribers of the list: ',
'envoi_erreur' => 'Error : SPIP-Listes cant find a destination for this message',
'email_reponse' => 'E-mail reply: ',
'envoi_annule' => 'Sending canceled;',
'envoi_fini' => 'Sending completed',
'erreur_destinataire' => 'Destination error: no message has been sent',
'erreur_sans_destinataire' => 'Error: no e-mail address can be found for this message',
'erreur_mail' => 'Error : sending impossible (make sure that  mail() of php is available)',

'forcer_lot' => 'Send the next batch',

'non_courrier' => 'No more message to send',
'non_html' => 'It looks like your e-mail client is unable to correctly display the graphical version (HTML) of this message.',
'sans_adresse' => 'No e-mail has been sent -> please specify a reply-to address',



// ====================== inc_import_patron.php3 ======================

'confirmer' => 'Confirm',

'lettre_info' => 'The newsletter of ',

'patron_erreur' => 'With the current parameters, the master template (patron) finds no news to be send',



// ====================== listes.html ======================

'abonees_titre' => 'Subscribers',


// ====================== inc-presentation.php3 ======================

'listes_emails' => 'Newsletter',


// ====================== mes-options.php3 ======================


'options' => 'radio|simple|Format :|Html,Text,unsubscription|html,text,non',

// ====================== mes-options.php3 ======================

'bonjour' => 'Hello,',

'inscription_response' => 'Your e-mail address has been added to the mailinglist of ',
'inscription_responses' => 'Your e-mail address has been added to the mailinglist of ',
'inscription_liste' => 'You will receive the newsletter sent to the following mailing list : ',
'inscription_listes' => 'You will receive the newsletters sent to the following mailing lists : ',
'inscription_format' => ', in format ',

);

// English translation: Simon simon@okko.org

?>
