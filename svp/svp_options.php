<?php


// passer ou pas en mode debug
if (!defined('_SVP_DEBUG')) {
	define('_SVP_DEBUG', false);
}


// Mode d'utilisation de SVP runtime ou pas :
// - En mode runtime (true), on ne charge que les plugins compatibles avec la version courante
// - En mode non runtime (false) on charge tous les plugins : cas du site Plugins SPIP
// Runtime est le mode par defaut
if (!defined('_SVP_MODE_RUNTIME')) {
	define('_SVP_MODE_RUNTIME', true);
}


// Liste des pages publiques d'objet supportees par le squelette (depot, plugin, paquet).
// Par defaut, SVP n'en propose plus.
// Le squelette qui les propose doit definir la constante en suivant l'exemple ci-dessous :
// define('_SVP_PAGES_OBJET_PUBLIQUES', 'depot:plugin');

?>
