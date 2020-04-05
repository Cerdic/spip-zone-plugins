<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

// Définir la google api key dans le fichier mes_options
if(!defined('_GOOGLE_API_KEY')) {
	define('_GOOGLE_API_KEY', false);
}
// Définir les subsets appliqués globalements au font request
// sous la forme 'greek, '
if(!defined('_FONTS_SUBSETS')) {
	define('_FONTS_SUBSETS','');
}