<?php
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

include_spip('public/cachelab_balises');

// loger tous les temps des ciblages
if (!defined('LOG_CACHELAB_CHRONO')) {
	define('LOG_CACHELAB_CHRONO', false);
}

// Seuil minimal des temps de ciblage à loger dans cachelab_slow (en ms)
if (!defined('LOG_CACHELAB_SLOW')) {
	define('LOG_CACHELAB_SLOW', 70);
}

// Seuil minimal du nombre de caches invalidés en un seul ciblage pour le loger dans cachelab_toomany_del
if (!defined('LOG_CACHELAB_TOOMANY_DEL')) {
	define('LOG_CACHELAB_TOOMANY_DEL', 100);
}

/* Autres constantes utilisées-définies dans inc/cachelab_invalideur :
 *   LOG_BALISECACHE_FILTRES
 *   LOG_BALISECACHE_DUREES_DYNAMIQUES
*/
