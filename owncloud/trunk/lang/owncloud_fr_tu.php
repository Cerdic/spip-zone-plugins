<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de https://trad.spip.net/tradlang_module/owncloud?lang_cible=fr_tu
// ** ne pas modifier le fichier **

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// A
	'action' => 'Action',

	// B
	'bouton_activer_synchro' => 'Active la synchronisation',
	'bouton_desactiver_synchro' => 'Désactive la synchronisation',
	'bouton_purger_medias' => 'Purge la base',
	'bouton_recuperer_media' => 'Récupére les documents',

	// C
	'cfg_activer_effacement_distant' => 'Efface des documents distants',
	'cfg_activer_effacement_distant_explication' => 'En cochant cette case, tu active l’effacement des fichiers sur ton Owncloud distant. La suppression des documents concerne le répertoire que tu as renseigné dans les "Paramètres de connexion". <br /><strong>Attention</strong>, cette action est brutale. Tu va perdre tes données hébergés sur ton Owncloud.',
	'cfg_activer_synchro' => 'Active la synchronisation des documents',
	'cfg_activer_synchro_explication' => 'En cochant cette case, tu active la synchronisation des documents depuis ton Owncloud pour les importer directement dans SPIP.',
	'cfg_configuration' => 'Paramètres de connexion',
	'cfg_directory_remote' => 'Répertoire des documents',
	'cfg_directory_remote_delete' => 'Répertoire des documents pour la synchro',
	'cfg_directory_remote_delete_explication' => 'Renseigne le répertoire de synchronisation distant, si celui-ci est différent répertoire des documents renseignés plus haut.',
	'cfg_directory_remote_explication' => 'Renseigne le répertoire où se trouve tes documents sur Owncloud',
	'cfg_login' => 'Nom d’utilisateur',
	'cfg_login_explication' => 'Renseigne le nom d’utilisateur de votre Owncloud',
	'cfg_password' => 'Mot de passe',
	'cfg_password_explication' => 'Renseigne le mot de passe de votre Owncloud',
	'cfg_synchro' => 'Paramètres de synchronisation',
	'cfg_titre_parametrages' => 'Paramètrage',
	'cfg_url_remote' => 'URL de ton Owncloud',
	'cfg_url_remote_explication' => 'Renseigner l’URL de ton Owncloud (ex : https://owncloud.me/)',
	'connexion_erreur_webdav' => 'La connexion à ton serveur webdav est inactive.',
	'connexion_ok_webdav' => 'La connexion à ton serveur webdav est active',
	'connexion_webdav' => 'Connexion au serveur webdav',

	// D
	'date_fichier_recuperer' => 'Liste des fichiers récupérés le :',
	'document_deja_importe' => 'Document déjà importé',

	// I
	'importer_image' => 'Importe un fichier',

	// M
	'md5' => 'md5',
	'message_activation_synchro' => 'La synchronisation est activé.',
	'message_confirmation_purger_owncloud' => 'Tout les identifiants MD5 sont supprimés de la base', # MODIF
	'message_confirmation_recuperation_owncloud' => 'La récupération s’est bien déroulée',

	// O
	'owncloud' => 'Owncloud',
	'owncloud_peupler_explication' => 'En cliquant sur ce bouton, tu va récupérer les fichiers présent sur ton Owncloud.',
	'owncloud_peupler_item' => 'Récupération des documents',
	'owncloud_purger_avertissement' => '<p><strong>Attention :</strong> Tu as activé l’effacement des fichiers distants, il est probable que tes fichiers ne soient plus présents sur ton Owncloud, en cliquant sur ce bouton tu peux perdre tes documents.</p>',
	'owncloud_purger_explication' => 'En cliquant sur ce bouton, tu supprime les identifiants MD5 permettant de t’indiquer si un fichier a déjà été importé dans SPIP et tu supprime également les fichiers importés précédemment dans SPIP.', # MODIF
	'owncloud_purger_item' => 'Purge les documents',

	// P
	'pas_de_media' => 'Aucuns documents n’est importés pour le moment, clique sur le bouton pour récupérer les documents depuis ton Owncloud', # MODIF

	// T
	'taille_fichier' => 'Taille du fichier',
	'titre_liste_owncloud' => 'Liste des fichiers sur ton Owncloud',
	'titre_page_configurer_owncloud' => 'Configure ta connexion à Owncloud'
);
