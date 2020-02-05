<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// Fichier source, a modifier dans https://git.spip.net/spip-contrib-extensions/owncloud.git
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// A
	'action' => 'Action',

	// B
	'bouton_activer_synchro' => 'Activer la synchronisation',
	'bouton_desactiver_synchro' => 'Désactiver la synchronisation',
	'bouton_purger_medias' => 'Purger la base',
	'bouton_recuperer_media' => 'Récupérer les documents',

	// C
	'cfg_activer_effacement_distant' => 'Effacer des documents distants',
	'cfg_activer_effacement_distant_explication' => 'En cochant cette case, vous activez l’effacement des fichiers sur le Owncloud distant. La suppression des documents concerne le répertoire renseigné dans les "Paramètres de connexion". <br /><strong>Attention</strong>, cette action est brutale. Vous allez perdre vos données hébergés sur votre Owncloud.',
	'cfg_activer_synchro' => 'Activer la synchronisation des documents',
	'cfg_activer_synchro_explication' => 'En cochant cette case, vous activez la synchronisation des documents depuis Owncloud pour les importer directement dans SPIP.',
	'cfg_configuration' => 'Paramètres de connexion',
	'cfg_directory_remote' => 'Répertoire des documents',
	'cfg_directory_remote_delete' => 'Répertoire des documents pour la synchro',
	'cfg_directory_remote_delete_explication' => 'Renseigner le répertoire de synchronisation distant, si celui-ci est différent répertoire des documents renseignés plus haut.',
	'cfg_directory_remote_explication' => 'Renseigner le répertoire où se trouve vos documents sur Owncloud',
	'cfg_login' => 'Nom d’utilisateur',
	'cfg_login_explication' => 'Renseigner le nom d’utilisateur de votre Owncloud',
	'cfg_password' => 'Mot de passe',
	'cfg_password_explication' => 'Renseigner le mot de passe de votre Owncloud',
	'cfg_synchro' => 'Paramètres de synchronisation',
	'cfg_titre_parametrages' => 'Paramètrage',
	'cfg_url_remote' => 'URL de votre Owncloud',
	'cfg_url_remote_explication' => 'Renseigner l’URL de votre Owncloud (ex : https://owncloud.me/)',
	'connexion_erreur_webdav' => 'La connexion au serveur webdav est inactive.',
	'connexion_ok_webdav' => 'La connexion au serveur webdav est active',
	'connexion_webdav' => 'Connexion au serveur webdav',

	// D
	'date_fichier_recuperer' => 'Liste des fichiers récupérés le :',
	'document_deja_importe' => 'Document déjà importé',

	// F
	'fichier' => 'Fichier',

	// I
	'importer_image' => 'Importer un fichier',
	'importer_tout_image' => 'Importer tous les fichiers',

	// M
	'md5' => 'md5',
	'message_activation_synchro' => 'La synchronisation est activé.',
	'message_confirmation_importer_tout_media' => 'L’importation s’est bien déroulée',
	'message_confirmation_importer_tout_media_erreur' => 'L’importation s’est mal déroulée',
	'message_confirmation_purger_owncloud' => 'Les identifiants uniques ont bien été supprimés de la base',
	'message_confirmation_recuperation_erreur_owncloud' => 'La récupération s’est mal déroulée',
	'message_confirmation_recuperation_owncloud' => 'La récupération s’est bien déroulée',
	'message_importer_tout_media' => 'Importer tous les médias dans la médiathèque',

	// O
	'owncloud' => 'Owncloud',
	'owncloud_importer_explication' => '<strong>Attention</strong> : L’importation sur beaucoup de document peut prendre beaucoup de temps.',
	'owncloud_peupler_explication' => 'En cliquant sur ce bouton, vous récupérez les fichiers présent sur votre Owncloud.',
	'owncloud_peupler_item' => 'Récupération des documents',
	'owncloud_purger_avertissement' => '<p><strong>Attention :</strong> Vous avez activé l’effacement des fichiers distants, il est probable que vos fichiers ne soient plus présents sur votre Owncloud, en cliquant sur ce bouton vous pouvez perdre vos documents.</p>',
	'owncloud_purger_explication' => 'En cliquant sur ce bouton, vous supprimez les identifiants uniques permettant de vous indiquer si un fichier a déjà été importé dans SPIP et vous supprimez également les fichiers importés précédemment dans SPIP.',
	'owncloud_purger_item' => 'Purger les documents',

	// P
	'pas_de_media' => 'Aucuns documents n’est importés pour le moment, cliquez sur le bouton pour récupérer les documents depuis Owncloud.',
	'pas_de_media_erreur' => 'Vérifier que le répertoire est bien renseigné dans la configuration ou que celui-ci existe sur Owncloud.',

	// T
	'taille_fichier' => 'Taille du fichier',
	'titre_liste_owncloud' => 'Liste des fichiers sur votre Owncloud',
	'titre_page_configurer_owncloud' => 'Configurer la connexion à Owncloud'
);
