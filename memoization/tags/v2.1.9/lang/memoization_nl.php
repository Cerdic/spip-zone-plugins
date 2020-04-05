<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de https://trad.spip.net/tradlang_module/memoization?lang_cible=nl
// ** ne pas modifier le fichier **

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// C
	'cfg_description' => 'Memoization. Deze configuratie pagina is voorbehouden aan webmasters van deze site.',
	'cfg_titre' => 'Mémoization',

	// E
	'explication_memcache_servers' => 'geef per regel een server aan in het formaat <kbd>host:port</kbd>',
	'explication_redis_dbindex' => 'wijzig de gekozen database',
	'explication_redis_serializer' => 'methode van (de)serialisatie. LETOP ledig de cache wanneer je van methode verandert',
	'explication_redis_serveur' => 'in formaat <kbd>host:port</kbd>',
	'explication_redis_sock' => 'toegangspad naar een socket van een Unix domein',
	'explications_redis_auth' => 'beveilig de verbinding met een wachtwoord',

	// I
	'info_taille_cache_inconnue' => 'Methode @methode@ laat niet toe de grootte van de cache te kennen.',
	'invalider_cache' => 'Cache ongeldig maken',

	// L
	'label_cache_pages' => 'Beheer van cache van pagina’s',
	'label_memcache_serveurs' => 'Memcache server(s):',
	'label_methode' => 'Kies de methode',
	'legend_cache_methode' => 'Methode van memoïlering',
	'legend_cache_pages' => 'Pagina cache',
	'legend_memcache' => 'Instellingen van Memcache',
	'legend_redis' => 'Parameters van Redis',
	'lien_administration_memcache' => 'Memcache beheren',

	// M
	'memcached_donnes' => 'Gegevens van memcached server',
	'memcached_script' => 'Script memcache.php by <a href="http://livebookmark.net">Harun Yayli</a>',
	'memcached_serveur' => 'Server memcached',
	'methodes_cache' => 'De pagina cache wordt beheerd het methode @type@',
	'methodes_grisees' => 'De in grijs aangegeven methodes zijn niet op deze server beschikbaar',

	// O
	'option_methode_apc' => 'APC',
	'option_methode_apcu' => 'APCu',
	'option_methode_defaut' => 'Auto-detectie',
	'option_methode_eaccelerator' => 'EAccelerator',
	'option_methode_filecache' => 'Bestanden (filecache)',
	'option_methode_memcache' => 'Memcache',
	'option_methode_memcached' => 'Memcached',
	'option_methode_nocache' => 'Uitgeschakeld (nocache)',
	'option_methode_redis' => 'Redis',
	'option_methode_xcache' => 'XCache',

	// R
	'redis_auth' => 'Wachtwoord:',
	'redis_dbindex' => 'Database:',
	'redis_erreur_connexion' => 'Fout in verbinding met Redis server',
	'redis_erreur_database' => 'De geselecteerde database kan niet worden gekozen',
	'redis_erreur_password' => 'Het wachtwoord is niet juist',
	'redis_serializer' => 'Serialisatie:',
	'redis_serveur' => 'Server:',
	'redis_sock' => 'Unix socket:',
	'redis_type_serveur' => 'Server',
	'redis_type_sock' => 'Unix socket',

	// T
	'taille_tototale_indisponible' => 'Totale grootte niet beschikbaar'
);
