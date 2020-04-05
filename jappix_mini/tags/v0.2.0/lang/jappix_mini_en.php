<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// Fichier source, a modifier dans svn://zone.spip.org/spip-zone/_plugins_/_stable_/noie/lang/
if (!defined("_ECRIRE_INC_VERSION")) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

'jappix_mini' => 'Jappix Mini',
'introduction_configuration' => '<p>In this section, you can configure the default values for the mini-chat.</p><p>Anyways, <strong>these value can be overriden from the templates</strong> when you use <code>#MODELE{minichat}</code>.</p>',

// Configuration sections
'section_jappix' => 'Jappix',
'section_jabber' => 'Jabber account',
'section_salons' => 'Rooms',
'section_interface' => 'Interface',

// Section Jappix
'url_jappix' => 'Jappix base URL',
'url_jappix_explication' => 'If you have Jappix installed locally, please give its URL here, or leave the field empty if you wish to use the instance at jappix.com.',

// Section Jabber account
'utilisateur_jabber' => 'Username',
'utilisateur_jabber_explication' => 'Username of the Jabber account to be used (leave empty for anonymous logon).',
'pass_jabber' => 'Password',
'pass_jabber_explication' => 'Password of the Jabber account (leave empty for anonymous logon).',
'domaine_jabber' => 'Host',
'domaine_jabber_explication' => 'Domain of the Jabber account. If empty, the anonymous at jappix.com will be used.',
'ressource' => 'Resource',
'ressource_explication' => 'Resource to be used. If empty, it will be "Jappix Mini".',

// Section Rooms
'liste_salons' => 'List of rooms',
'liste_salons_explication' => 'Please input the list of rooms to be joined by the visitors (one per line).',
'liste_pass_salons' => 'Room passwords',
'liste_pass_salons_explication' => 'If the rooms are password-protected, you\'ll need to give the corresponding passwords here (one per line).',
'entrer_automatiquement' => 'Auto join',
'entrer_automatiquement_explication' => 'Automatically join the room when the page loads',
'pseudo_salons' => 'Predefined nick',
'pseudo_salons_explication' => 'Nick to use for joining the chatrooms. If you leave this field empty, the visitors will be interactively prompted for a nick.',

// Section Interface
'derouler_panneau_auto' => 'Show pane',
'derouler_panneau_auto_explication' => 'Expand the pane when the visitor joins the rooms',
'langue_interface' => 'Language',
'langue_interface_explication' => 'Language of the mini-chat interface.',
'langue_interface_defaut' => 'Current language of the page',

);

?>
