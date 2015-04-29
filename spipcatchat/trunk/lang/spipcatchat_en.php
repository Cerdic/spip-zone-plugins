<?php
/***************************************************************************\
 * 				SpipCatChat Salon de Chat autonome pour Spip               *
 *                                                                         *
 *  Copyright (c) 2014                                                     *
 *  Auteur original: Codden Claude			                               *
 *  Traduction anglais: Codden Emmanuel									   *
 *  Pour plus de details voir le fichier licence.txt. 					   *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *                                                                         *
\***************************************************************************/
if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(
/*--A--*/
'catchat_message' => 'No message has been sent.',

/*--C--*/

'catchat_chargement' => 'Loading...',
'copy' => 'Developed for Spip',
'config_catchat' => 'Configuration de SpipCatChat',

/*--E--*/
'error_ajout_salon' => 'Sorry a chat room allready with the same name',
'explication_largeur' => 'Choisissez en pixel ou en pourcentage, la largeur par défaut pour l\'intégration du salon de discussion dans votre site.',
'explication_theme' => 'Vous pouvez choisir ici d\'appliquer un thème préétabli ou de composer votre propre style en harmonie avec votre site.',
'explication_color_primaire' => 'Choisissez la couleur dominante pour votre thème sans le diéser <b>EX : (<font color="red">#</font>FFA500)</b> ou placez un "<font color="red">0</font>" pour un effet de transparence.',
'explication_color_secondaire' => 'Choisissez la couleur secondaire pour votre thème sans le diéser <b>EX : (<font color="red">#</font>FFA500)</b> ou placez un "<font color="red">0</font>" pour un effet de transparence.',
'explication_color_icon' => 'Harmonisez le pack d\'icônes avec votre site.',
'explication_refesh_pub' => 'Réglez ici le taux de rafraîchissement des messages des salons publics en rapport avec la vélocité de votre serveur web',
'explication_refesh_prive' => 'Réglez ici le taux de rafraîchissement des messages du salon privé en rapport avec la vélocité de votre serveur web',
'explication_admin' => 'Voulez-vous activer le salon réserver aux administrateurs de l\'espace privé ?',
'explication_color_fond' => 'Choisissez la couleur du fond du salon de disscussion sans le diéser <b>EX : (<font color="red">#</font>FFA500)</b> ou placez un "<font color="red">X</font>" pour un effet de transparence.',
'explication_smoke' => 'Voulez-vous activer l\'option de "ombre portée" du salon ?<br/><font color="red">Attention incompatible avec les anciens navigateurs !</font>',
'explication_corner' => 'Voulez-vous activer les coins arrondis du salon ?<br/><font color="red">Attention incompatible avec les anciens navigateurs !</font>',
'explication_emoticon' => 'Voulez-vous activer la barre d\'insertion des émoticons dans le salon de l\'espace privé ?',
'explication_historique' => 'Voulez-vous activer l\'option de récupération de l\'historique des discussions du menu des utilisateurs du salon privé ?',

/*--I--*/
'info_en_ligne' => 'Currently online authors :',
'info_statut' => 'Changing status',
'index_attent' => 'Loading chat room...',
'info_configuration' => 'Système de communication instantanée développé pour Spip.<br/> Pour plus d\'information sur la configuration et l\'installation voir la documentation sur <a href="http://contrib.spip.net/Plugin-SpipCatChat">Contribution Spip</a> et pour l\'utilisation voir <a href="http://contrib.spip.net/IMG/pdf/guide_de_l_utilisateur.pdf">le guide de  l\'utilisateur</a>.<br/><br/>Contact : <a href="mailto:claude.codden@nic-nac.org">Claude Codden</a><br/>Développement : <a href="http://zone.spip.org/trac/spip-zone/browser/_plugins_/spipcatchat/trunk">SPIP Zone</a>',
/*--F--*/

'formulaire_addusers' => 'Adding member...',
'formulaire_error_users' => 'Please select a member\'s name you want to add this room',
'formulaire_log' => 'Please insert your login to connect to chat',
'formulaire_nouveau_salon' => 'or insert a room name',
'formulaire_recherche_users' => 'Insert a member name you want to add to this room',
'formulaire_salon' => 'Welcome',
'formulaire_select_salon' => 'Choose your room',
/*--K--*/

'catchat_label' => 'SpipCatChat',

/*--L--*/
'label_admin' => 'Chat room administrator',
'label_catchat_theme_color' => 'Couleur du thème',
'label_catchat_theme_coloricone' => 'Couleur du packs des icônes du salon',
'label_catchat_theme_colorThumbs' => 'Thème du salon automatique ou préétabli',
'label_corner' => 'Activer les coins arrondis (border-radius CSS3)',
'label_fond_color' => 'Couleur de fond de cadre des messages',
'label_emoticon' => 'Afficher la barre des émoticons',
'label_historique' => 'Activer la récupération de l\'historique',
'label_non' => 'Non',
'label_oui' => 'Oui',
'label_primaire' => 'Couleur prédominante du salon',
'label_refresh' => 'Taux de rafraîchissement du salon public',
'label_refreshprive' => 'Taux de rafraîchissement du salon privé',
'label_false' => 'Automatique et préétabli',
'label_secondaire' => 'La couleur secondaire du salon',
'label_smoke' => 'L\'ombrage des bordures du cadre du salon (box-shadow CSS3)',
'label_true' => 'Manuel et personnalisé',
'label_width' => 'La largeur du salon public',

/*--N--*/
'no_file' => 'No file ',
'no_welcom' => 'Private room. Contact room administrator to access',
'noscript' => 'Javascript is required for using Catchat.',

/*--P--*/

'placeholder_nom_salon' => 'Welcome',
'placeholder_user' => 'member\'s name',
'placeholder_salon' => 'new room',
'prive' =>'Configuration de l\'espace privé',
'public' => 'Configuration de l\'espace public',

/*--R--*/

'recup_chat' => 'Recovery of the historical',


/*--T--*/

'title_aide' => 'Help',
'texte_connec'=>'You must be logged',
'texte_recent'=>'You just posted a message right now! Please wait.',
'texte_roman' => 'Want to WRITE A BOOK ...',
'texte_vide'=>'No message.',
'title_add_pepole' => 'Add member to your room !',
'title_add_user' => 'Add member to your room. Warning : Member must exists',
'title_connect' => 'Clic here to connect',
'title_copy' => '[CC-BY-SA-4.0 (http://creativecommons.org/licenses/by-sa/4.0)], via Wikimedia Commons',
'title_del_user' => 'Remove a member out of your room. Warning : It must be selected to be fired',
'title_fin_session' => 'Leave the chat',
'title_login' => 'SpipCatChat connexion',
'title_login2' => 'Inster your login here, mail, name',
'title_logsalon' => 'Connect to rooms',
'title_quit_salon' => 'Leave the room',
'title_public' => 'Public room',
'title_prive' => 'Private room',
'title_prive_invite' => 'You are in this room as a guest',
'title_recherche_users' => 'Looking for member...',
'title_record_salon' => 'Register your room',
'title_retour_salon' => 'Back to room',
'title_select_users' => 'Select a member\'s name to edit',
'title_status' => 'Changing status',
'title_trash_salon' => 'Clic here to remove room',
'title_default' => 'Status',
'title_valid_message' => 'Send your message',
'trash_bad' => 'An error occures while deleting',
'trash_confirm' => 'Do you really want to delete this room ? Warning ! Suppressing will be done when the room will be empty',

/*--S--*/
'salon_discussion' => 'Chat Room',
'select_option' => 'Room list', 
'success_ajout_salon' => 'Room added. It appears now in the room list',
'status_absent'=>'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Off line',
'status_default' => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Status',
'status_occupe'=>'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Busy',
'status_ligne'=>'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;On line',
);
?>
