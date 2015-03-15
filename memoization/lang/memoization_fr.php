<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// Fichier source, a modifier dans svn://zone.spip.org/spip-zone/_plugins_/memoization/lang/
if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// C
	'cfg_description' => 'Mémoization. Cette page de configuration est réservée au webmestre du site.',
	'cfg_titre' => 'Mémoization',

	// E
	'explication_memcache_servers' => 'indiquer un serveur par ligne, sous la forme <kbd>host:port</kbd>',

	// I
	'info_taille_cache_inconnue' => 'La méthode @methode@ ne permet pas de connaître la taille du cache.',
	'invalider_cache' => 'Invalider le cache',

	// L
	'label_cache_pages' => 'Gérer le cache des pages',
	'label_memcache_serveurs' => 'Serveur(s) memcache :',
	'label_methode' => 'Choisir le mode de mémoization',
	'legend_cache_methode' => 'Méthode de mémoization',
	'legend_cache_pages' => 'Cache des pages',
	'legend_memcache' => 'Réglages de memcache',
	'lien_administration_memcache' => 'Administrer memcache',

	// M
	'memcached_donnes' => 'Données de votre serveur memcached',
	'memcached_script' => 'Script memcache.php by <a href="http://livebookmark.net">Harun Yayli</a>',
	'memcached_serveur' => 'Serveur memcached',
	'methodes_cache' => 'Le cache des pages est géré par la méthode @type@',
	'methodes_grisees' => 'Les méthodes indiquées en grisé ne sont pas disponibles sur ce serveur',

	// O
	'option_methode_apc' => 'APC',
	'option_methode_defaut' => 'Auto-détection',
	'option_methode_eaccelerator' => 'EAccelerator',
	'option_methode_filecache' => 'Fichiers (filecache)',
	'option_methode_memcache' => 'Memcache',
	'option_methode_nocache' => 'Désactivé (nocache)',
	'option_methode_xcache' => 'XCache',
	
	// T
	'taille_tototale_indisponible' => 'Taille totale non disponible'
);

?>
