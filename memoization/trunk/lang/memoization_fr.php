<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// Fichier source, a modifier dans https://git.spip.net/spip-contrib-extensions/memoization.git
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// C
	'cfg_description' => 'Mémoization. Cette page de configuration est réservée au webmestre du site.',
	'cfg_titre' => 'Mémoization',

	// E
	'explication_memcache_servers' => 'indiquer un serveur par ligne, sous la forme <kbd>host:port</kbd>',
	'explication_redis_dbindex' => 'changer la base de données sélectionnée',
	'explication_redis_serializer' => 'méthode de sérialisation / désérialisation. ATTENTION vider le cache en cas de changement de méthode',
	'explication_redis_serveur' => 'sous la forme <kbd>host:port</kbd>',
	'explication_redis_sock' => 'chemin d’accès à un socket de domaine Unix',
	'explications_redis_auth' => 'authentifiez la connexion en utilisant un mot de passe',

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
	'legend_redis' => 'Paramètres de Redis',
	'lien_administration_memcache' => 'Administrer memcache',

	// M
	'memcached_donnes' => 'Données de votre serveur memcached',
	'memcached_script' => 'Script memcache.php by <a href="http://livebookmark.net">Harun Yayli</a>',
	'memcached_serveur' => 'Serveur memcached',
	'methodes_cache' => 'Le cache des pages est géré par la méthode @type@',
	'methodes_grisees' => 'Les méthodes indiquées en grisé ne sont pas disponibles sur ce serveur',

	// O
	'option_methode_apc' => 'APC',
	'option_methode_apcu' => 'APCu',
	'option_methode_defaut' => 'Auto-détection',
	'option_methode_eaccelerator' => 'EAccelerator',
	'option_methode_filecache' => 'Fichiers (filecache)',
	'option_methode_memcache' => 'Memcache',
	'option_methode_memcached' => 'Memcached',
	'option_methode_nocache' => 'Désactivé (nocache)',
	'option_methode_redis' => 'Redis',
	'option_methode_xcache' => 'XCache',

	// R
	'redis_auth' => 'Mot de passe :',
	'redis_dbindex' => 'Base de données :',
	'redis_erreur_connexion' => 'Erreur de connexion au serveur redis',
	'redis_erreur_database' => 'Impossible de sélectionner la base de données demandée',
	'redis_erreur_password' => 'Le mot de passe renseigné est incorrect',
	'redis_serializer' => 'Sérialisation :',
	'redis_serveur' => 'Serveur :',
	'redis_sock' => 'Socket Unix :',
	'redis_type_serveur' => 'Serveur',
	'redis_type_sock' => 'Socket Unix',

	// T
	'taille_tototale_indisponible' => 'Taille totale non disponible'
);
