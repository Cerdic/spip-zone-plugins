<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de https://trad.spip.net/tradlang_module/memoization?lang_cible=de
// ** ne pas modifier le fichier **

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// C
	'cfg_description' => 'Mémoization. Diese Konfigurationsseite steht nur dem Webmaster der Site zur Verfügung.',
	'cfg_titre' => 'Mémoization',

	// E
	'explication_memcache_servers' => 'einen Server pro Zeile im Format <kbd>host:port</kbd> eintragen',
	'explication_redis_dbindex' => 'gewählte Datenbank wechseln',
	'explication_redis_serializer' => 'Serialisierungs- bzw. Unseriaisierungs-Methode. ACHTUNG - Cache nach Änderung der Methode leeren.',
	'explication_redis_serveur' => 'im Format <kbd>host:port</kbd>',
	'explication_redis_sock' => 'Pfad zu einem Unix Domain-Socket',
	'explications_redis_auth' => 'geben Sie das Passwort für die Verbindung an',

	// I
	'info_taille_cache_inconnue' => 'Die Methode @methode@ eraubt keine Kenntnis der Cache-Grösse.',
	'invalider_cache' => 'Cache zurücksetzen',

	// L
	'label_cache_pages' => 'Seiten-Cache verwalten',
	'label_memcache_serveurs' => 'Memcache Server:',
	'label_methode' => 'Mémoizations-Modus wählen',
	'legend_cache_methode' => 'Mémoizations-Methode',
	'legend_cache_pages' => 'Seiten-Cache',
	'legend_memcache' => 'Memcache Einstellungen',
	'legend_redis' => 'Redis-Parameter',
	'lien_administration_memcache' => 'Memcache verwalten',

	// M
	'memcached_donnes' => 'Daten Ihres Memcache-Servers',
	'memcached_script' => 'memcache.php Skript von <a href="http://livebookmark.net">Harun Yayli</a>',
	'memcached_serveur' => 'Memcache Server',
	'methodes_cache' => 'Der Seitencache wird mit der Methode @type@ verwaltet.',
	'methodes_grisees' => 'Die ausgegrauten Methoden stehen auf diesem  Server nicht zur Verfügung.',

	// O
	'option_methode_apc' => 'APC',
	'option_methode_defaut' => 'Automatische Erkennung',
	'option_methode_eaccelerator' => 'EAccelerator',
	'option_methode_filecache' => 'Dateien (filecache)',
	'option_methode_memcache' => 'Memcache',
	'option_methode_nocache' => 'Deaktiviert (nocache)',
	'option_methode_redis' => 'Redis',
	'option_methode_xcache' => 'XCache',

	// R
	'redis_auth' => 'Passwort :',
	'redis_dbindex' => 'Datenbank :',
	'redis_erreur_connexion' => 'Fehler bei der Verbindung mit Redis-Server',
	'redis_erreur_database' => 'Verbindung zur angegeben Datenbank nicht möglich',
	'redis_erreur_password' => 'Falsches Passwort',
	'redis_serializer' => 'Serialisierung :',
	'redis_serveur' => 'Server :',
	'redis_sock' => 'Unix Socket :',
	'redis_type_serveur' => 'Server',
	'redis_type_sock' => 'Unix Socket',

	// T
	'taille_tototale_indisponible' => 'Gesamtgröße nicht verfügbar'
);
