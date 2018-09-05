<?php

// Ceci est un fichier langue de SPIP -- This is a SPIP language file

// Fichier produit par PlugOnet
// Module: paquet-htmlpurifier
// Langue: fr
// Date: 11-03-2018 11:13:54
// Items: 2

if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// H
	'htmlpurifier_description' => 'Ce plugin propose de remplacer SafeHTML (plus maintenue depuis plusieurs années), par [HTML Purifier->http://htmlpurifier.org/], une librairie moderne et bien maintenue. SPIP utilise en interne la librairie SafeHTML pour sécuriser l\'affichage des textes qui proviennent potentiellement non pas des rédacteurs, mais de visiteurs non enregistrés ou de contributeurs externes, comme les forums ou les contenus des sites syndiqués.
	
	{{Attention : ce plugin nécessite PHP5 !}}',
	'htmlpurifier_slogan' => 'Sécuriser l\'affichage de certains textes',
);
