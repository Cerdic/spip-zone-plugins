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
'catchat_message' => 'Aucun message n\'a été envoyé pour le moment.',

/*--C--*/

'catchat_chargement' => 'Chargement...',
'copy' => 'Développé pour Spip',
'config_catchat' => 'Configuration de SpipCatChat',
/*--E--*/
'error_ajout_salon' => 'désolé, mais le nom du salon existe déjà',
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
'explication_emoticon_public' => 'Voulez-vous activer la barre d\'insertion des émoticons dans le salon de l\'espace public ?',
'explication_historique' => 'Voulez-vous activer l\'option de récupération de l\'historique des discussions du menu des utilisateurs du salon privé ?',
'explication_label' => 'ne pas faire référence à Spip et SpipCatchat dans l\'espace public du site',
/*--I--*/
'info_en_ligne' => 'En ligne :',
'info_statut' => 'Changer de statut : ',
'info_configuration' => 'Système de communication instantanée développé pour Spip.<br/> Pour plus d\'information sur la configuration et l\'installation voir la documentation sur <a href="http://contrib.spip.net/Plugin-SpipCatChat">Contribution Spip</a> et pour l\'utilisation voir <a href="http://contrib.spip.net/IMG/pdf/guide_de_l_utilisateur.pdf">le guide de  l\'utilisateur</a>.<br/><br/>Contact : <a href="mailto:claude.codden@nic-nac.org">Claude Codden</a><br/>Développement : <a href="http://zone.spip.org/trac/spip-zone/browser/_plugins_/spipcatchat/trunk">SPIP Zone</a>',
'index_attent' => 'Chargement du chat en cours...',

/*--F--*/

'formulaire_addusers' => 'Formulaire d\'ajout de membres',
'formulaire_error_users' => 'Veuillez sélectionner le nom du membre à ajouter au salon', 
'formulaire_log' => 'Indiquez votre pseudo et mot de passe afin de vous connecter au chat',
'formulaire_nouveau_salon' => 'ou entrez le nom du nouveau salon',
'formulaire_recherche_users' => 'Indiquez ici le nom d\'un membre à ajouté au salon',
'formulaire_salon' => 'Entrée des Salons de chat',
'formulaire_select_salon' => 'Choisissez votre salon',
/*--K--*/

'catchat_label' => 'SpipCatChat',

/*--L--*/
'label_admin' => 'Salon des administrateurs',
'label_catchat_theme_color' => 'Couleur du thème',
'label_catchat_theme_coloricone' => 'Couleur du packs des icônes du salon',
'label_catchat_theme_colorThumbs' => 'Thème du salon automatique ou préétabli',
'label_corner' => 'Activer les coins arrondis (border-radius CSS3)',
'label_fond_color' => 'Couleur de fond de cadre des messages',
'label_emoticon' => 'Afficher la barre des émoticons',
'label_emoticon_public' => 'Afficher la barre des émoticons',
'label_historique' => 'Activer la récupération de l\'historique',
'label_non' => 'Non',
'label_oui' => 'Oui',
'label_primaire' => 'Couleur prédominante du salon',
'label_refresh' => 'Taux de rafraîchissement du salon public',
'label_refreshprive' => 'Taux de rafraîchissement du salon privé',
'label_false' => 'Automatique et préétabli',
'label_secondaire' => 'La couleur secondaire du salon',
'label_smoke' => 'L\'ombrage des bordures du cadre du salon (box-shadow CSS3)',
'label_spicatchat_label' => 'Cacher les labels',
'label_true' => 'Manuel et personnalisé',
'label_width' => 'La largeur du salon public',

/*--N--*/
'no_file' => 'Pas de fichier ',
'no_welcom' => 'Salon Privé, pour participer aux discussions veuillez contacter l\'administrateur du salon',
'noscript' => 'Si vous voulez accéder au chat, alors activez la fonction JavaScript dans votre navigateur.',

/*--P--*/

'placeholder_nom_salon' => 'Bienvenue',
'placeholder_user' => 'nom du membre',
'placeholder_salon' => 'nouveau salon',
'prive' =>'Configuration de l\'espace privé',
'public' => 'Configuration de l\'espace public',

/*--R--*/

'recup_chat' => 'Récuperation de l\'historique',

/*--T--*/

'title_aide' => 'Aide',
'texte_connec'=>'Vous devez être connecté',
'texte_recent'=>'Votre dernier message est trop récent',
'texte_roman' => 'Vous voulez écrire un roman ?',
'texte_vide'=>'Votre message est vide.',
'title_add_pepole' => 'Cliquez ici pour ajouter des membres à votre salon',
'title_add_user' => 'Cliquez ici pour ajouter un membre à votre salon. Attention celui-ci doit être pré-sélectionné au préalable ci-dessus',
'title_connect' => 'Cliquez ici pour une connection',
'title_copy' => '[CC-BY-SA-4.0 (http://creativecommons.org/licenses/by-sa/4.0)], via Wikimedia Commons',
'title_del_user' => 'Cliquez ici pour supprimer un membre de votre salon. Attention celui-ci doit être pré-sélectionné au préalable ci-dessous',
'title_fin_session' => 'Cliquez ici pour clôturer votre session de chat',
'title_login' => 'Connection à SpipCatChat',
'title_login2' => 'Entrez ici votre login, mail, nom',
'title_logsalon' => 'Connection aux salons',
'title_quit_salon' => 'Cliquez ici pour quitter le salon',
'title_public' => 'Salon public',
'title_prive' => 'Salon privé',
'title_prive_invite' => 'Vous êtes un invité dans ce salon',
'title_recherche_users' => 'Recherche des membres',
'title_record_salon' => 'Enregistrez votre nouveau salon',
'title_retour_salon' => 'Retour au Salon',
'title_select_users' => 'Séléctionner le nom du membre à éditer',
'title_status' => 'Cliquez ici pour changer de statut',
'title_trash_salon' => 'Cliquez ici pour supprimer le salon',
'title_valid_message' => 'Cliquez ici pour valider le message',
'status_ligne'=>'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;En ligne',
'trash_bad' => 'un problème est survenu lors de la suppression',
'trash_confirm' => 'voulez-vous vraiment supprimer ce salon ? Attention !, la suppression ne sera vraiment effective qu\'une fois que tous les membres auront quitté la salle',

/*--S--*/
'salon_discussion' => 'Salon de discussion instantanée (clavardage)',
'select_option' => 'Liste des salons', 
'success_ajout_salon' => 'Le salon est ajouté, il apparaît à présent dans la liste de selection',
'status_absent'=>'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Absent',
'status_default' => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Statut',
'status_occupe'=>'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Occup&eacute;',
'status_ligne'=>'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;En ligne',
);
?>
