<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// Fichier source, a modifier dans svn://zone.spip.org/spip-zone/_plugins_/_stable_/noie/lang/
if (!defined("_ECRIRE_INC_VERSION")) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

'jappix_mini' => 'Jappix Mini',
'introduction_configuration' => '<p>Vous pouvez configurer ici les valeurs par d&eacute;faut du mini-chat.</p><p>Dans tous les cas, <strong>ces valeurs sont surchargeables dans les squelettes</strong> lors de l\'appel &agrave; <code>#MODELE{minichat}</code>.</p>',

// Sections de configuration
'section_jappix' => 'Jappix',
'section_jabber' => 'Compte Jabber',
'section_salons' => 'Salons',
'section_interface' => 'Interface',

// Section Jappix
'url_jappix' => 'URL de Jappix',
'url_jappix_explication' => 'Si vous avez install&eacute; Jappix en local, indiquez ici son URL, ou laissez vide pour utiliser l\'instance du site jappix.com.',

// Section Compte Jabber
'utilisateur_jabber' => 'Utilisateur',
'utilisateur_jabber_explication' => 'Nom d\'utilisateur du compte Jabber &agrave; utiliser (laisser vide pour une connexion anonyme).',
'pass_jabber' => 'Mot de passe',
'pass_jabber_explication' => 'Mot de passe du compte Jabber (laisser vide pour une connexion anonyme).',
'domaine_jabber' => 'Domaine',
'domaine_jabber_explication' => 'Serveur du compte Jabber. Si vide, le serveur anonyme de jappix.com sera utilis&eacute;.',
'ressource' => 'Ressource',
'ressource_explication' => 'Ressource &agrave; utiliser. Si vide, "Jappix Mini" sera utilis&eacute;.',

// Section Salons
'liste_salons' => 'Liste des salons',
'liste_salons_explication' => 'Indiquez ici la liste des salons de discussion &agrave; proposer aux visiteurs (un par ligne).',
'liste_pass_salons' => 'Mots de passe',
'liste_pass_salons_explication' => 'Si les salons sont prot&eacute;g&eacute;s par un mot de passe, indiquez-les ici (un par ligne).',
'entrer_automatiquement' => 'Entrer automatiquement',
'entrer_automatiquement_explication' => 'Entrer automatiquement dans les salons au chargement de la page',
'pseudo_salons' => 'Pseudo pr&eacute;d&eacute;fini',
'pseudo_salons_explication' => 'Pseudo &agrave; utiliser sur les salons. Laissez vide pour laisser le choix aux visiteurs.',

// Section Interface
'derouler_panneau_auto' => 'Ouvrir par d&eacute;faut',
'derouler_panneau_auto_explication' => 'D&eacute;rouler le panneau "chat" lors de l\'entr&eacute;e sur le salon',
'langue_interface' => 'Langue',
'langue_interface_explication' => 'Langue de l\'interface du mini-chat.',
'langue_interface_defaut' => 'Langue du site',

);

?>