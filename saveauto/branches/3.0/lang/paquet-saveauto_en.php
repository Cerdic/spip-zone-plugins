<?php

// Ceci est un fichier langue de SPIP -- This is a SPIP language file

///  Fichier produit par PlugOnet
// Module: paquet-saveauto
// Langue: en
// Date: 22-05-2012 01:24:49
// Items: 3

if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

// S
	'saveauto_description' => 'Makes a backup of the entire database used by SPIP.
			The .gz or .sql file obtained is stored in a directory (default /tmp, but configurable) and can be sent automatically by email.
			
			The backup is triggered by a cron job (the frequency is configurable).
			The stored backups considered obsolete (according to the corresponding config setting) are automatically destroyed.',
	'saveauto_nom' => 'Automatic backup',
	'saveauto_slogan' => 'Automatic backup of the database used by SPIP',
);
?>