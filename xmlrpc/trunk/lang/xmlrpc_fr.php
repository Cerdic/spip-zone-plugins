<?php

if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(
	// C
	'cfg_description' => 'Configuration du serveur xml-rpc',
	
	// E
	'erreur_arguments_obligatoires' => 'Erreur : les arguments suivants sont obligatoires "@arguments@"',
	'erreur_lecture' => 'Erreur de lecture de l\'objet (@objet@ #@id_objet@)',
	'erreur_identifiant' => 'Vous devez fournir l\'identifiant numérique de l\'objet (@objet@)',
	'erreur_impossible_lire_objet' => 'Erreur : il est impossible de lire l\'objet "@objet@" #@id_objet@',
	'erreur_objet_inexistant' => 'L\'objet demandé n\'existe pas (@objet@ #@id_objet@)',
	'erreur_mauvaise_identification' => 'Mauvaise identification (login/mot de passe)',
	'erreur_xmlrpc_desactive' => 'Le serveur xml-rpc est désactivé',
	
	// L
	'label_api_preferee' => 'API d\'édition préférée',
	'label_desactiver_rsd' => 'Désactiver le RSD',
	'label_desactiver_rsd_long' => 'Désactive l\'utilisation du fichier RSD dans l\'entête des pages',
	'label_ferme' => 'Désactiver le serveur',
);
?>