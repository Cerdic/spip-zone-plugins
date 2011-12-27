<?php

// definir le chemin des librairies
// que l'on installe
if (!defined('_DIR_LIB')) {
	define('_DIR_LIB', _DIR_RACINE . 'lib/');
}

// passer ou pas en mode debug
if (!defined('_SVP_DEBUG')) {
	define('_SVP_DEBUG', false);
}


// Mode d'utilisation de SVP runtime ou pas :
// - En mode runtime (true), on ne charge que les plugins compatibles avec la version courante
// - En mode non runtime (false) on charge tous les plugins : cas du site Plugins SPIP
// Runtime est le mode par defaut
define('_SVP_MODE_RUNTIME', true);

// Mise a jour automatique des depots (CRON)
// - Flag de declenchement
define('_SVP_CRON_ACTUALISATION_DEPOTS', true);
// - Periode d'actualisation en nombre d'heures (de 1 a 24)
define('_SVP_PERIODE_ACTUALISATION_DEPOTS', 6);


// Liste des categories de plugin
$GLOBALS['categories_plugin'] = array(
	'auteur', 
	'communication', 
	'date', 
	'divers', 
	'edition', 
	'maintenance', 
	'multimedia', 
	'navigation', 
	'outil', 
	'performance', 
	'statistique', 
	'squelette', 
	'theme', 
	'aucune'
);


// Liste des branches significatives de SPIP et de leurs bornes (versions min et max)
// A mettre a jour en fonction des sorties
$GLOBALS['infos_branches_spip'] = array(
	'1.9' => array(_SVP_VERSION_SPIP_MIN,'1.9.2'),
	'2.0' => array('2.0.0','2.0.99'),
	'2.1' => array('2.1.0','2.1.99'),
	'3.0' => array('3.0.0',_SVP_VERSION_SPIP_MAX) 
);

// Liste des pages publiques d'objet supportees par le squelette (depot, plugin, paquet).
// Par defaut, SVP n'en propose plus.
// Le squelette qui les propose doit definir la constante en suivant l'exemple ci-dessous :
// define('_SVP_PAGES_OBJET_PUBLIQUES', 'depot:plugin');

?>
