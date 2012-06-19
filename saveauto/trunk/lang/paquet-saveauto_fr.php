<?php

// Ceci est un fichier langue de SPIP -- This is a SPIP language file

///  Fichier produit par PlugOnet
// Module: paquet-saveauto
// Langue: fr
// Date: 22-05-2012 01:24:49
// Items: 3

if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

// S
	'saveauto_description' => 'Permet de réaliser une sauvegarde de toute la base de données utilisée par SPIP.
			Le fichier .zip (ou .sql) obtenu est stocké dans un répertoire (par défaut /tmp/dump, configurable)
			et peut être envoyé par mail.
			
			Les sauvegardes stockées considérées comme obsolètes (en fonction du paramètre de config correspondant)
			sont automatiquement détruites.
			
			Une interface permet de déclencher manuellement les sauvegardes et de gérer les fichiers générés',
	'saveauto_nom' => 'sauvegarde automatique',
	'saveauto_slogan' => 'Sauvegarde automatique de la base de données de SPIP',
);
?>