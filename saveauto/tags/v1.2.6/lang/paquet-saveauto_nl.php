<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de https://trad.spip.net/tradlang_module/paquet-saveauto?lang_cible=nl
// ** ne pas modifier le fichier **

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// S
	'saveauto_description' => 'Maakt een MySQL backup mogelijk van de hele database die door SPIP wordt gebruikt.
			Het verkregen .zip (of .sql) bestand wordt opgeslagen in een vooraf in te stellen map (standaard /tmp/dump)
			en kan per mail worden toegezonden.

			Verouderde backups (configureerbare instelling)
			worden automatisch verwijderd.

			Een interface is beschikbaar voor handmatige backup en beheer van de bestanden',
	'saveauto_nom' => 'Automatische backup',
	'saveauto_slogan' => 'Automatische MySQL backup van de SPIP database'
);
