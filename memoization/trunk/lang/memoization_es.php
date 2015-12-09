<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de http://trad.spip.net/tradlang_module/memoization?lang_cible=es
// ** ne pas modifier le fichier **

if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// C
	'cfg_description' => 'Memoización. Esta página de configuración esta reservada al webmaster del sitio.',
	'cfg_titre' => 'Memoización',

	// E
	'explication_memcache_servers' => 'indicar un servidor por línea, bajo la forma <kbd>host:port</kbd>',

	// I
	'info_taille_cache_inconnue' => 'El método @methode@ no permite conocer el tamaño del caché.',

	// L
	'label_cache_pages' => 'Administrar el caché de las páginas',
	'label_memcache_serveurs' => 'Servidor(es) memcache:',
	'label_methode' => 'Elegir el modo de memoización',
	'legend_cache_methode' => 'Método de memoización',
	'legend_cache_pages' => 'Caché de las páginas',
	'legend_memcache' => 'Reglas de memcache',
	'lien_administration_memcache' => 'Administrar memcache',

	// M
	'methodes_grisees' => 'Los métodos indicados en gris no están disponibles en este servidor',

	// O
	'option_methode_apc' => 'APC',
	'option_methode_defaut' => 'Auto-detección',
	'option_methode_eaccelerator' => 'EAccelerator',
	'option_methode_filecache' => 'Archivos (filecache)',
	'option_methode_memcache' => 'Memcache',
	'option_methode_nocache' => 'Desactivado (nocache)',
	'option_methode_xcache' => 'XCache'
);

?>
