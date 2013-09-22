<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de http://trad.spip.net/tradlang_module/memoization?lang_cible=en
// ** ne pas modifier le fichier **

if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// C
	'cfg_description' => 'Memoization. This configuration page is reserved for the webmaster.',
	'cfg_titre' => 'Memoization',

	// E
	'explication_memcache_servers' => 'indicate a server line, as <kbd>host:port</kbd>',

	// I
	'info_taille_cache_inconnue' => '@methode@ method doesn’t allow to know the size of the cache.',

	// L
	'label_cache_pages' => 'Manage the page cache',
	'label_memcache_serveurs' => 'Memcache server(s) :',
	'label_methode' => 'Select the memoization mode',
	'legend_cache_methode' => 'Memoization method',
	'legend_cache_pages' => 'Page cache',
	'legend_memcache' => 'Memcache settings',
	'lien_administration_memcache' => 'Manage memcache',

	// M
	'methodes_grisees' => 'Greyed-out methods are not available on this server',

	// O
	'option_methode_apc' => 'APC',
	'option_methode_defaut' => 'Auto-detection',
	'option_methode_eaccelerator' => 'EAccelerator',
	'option_methode_filecache' => 'Files (filecache)',
	'option_methode_memcache' => 'Memcache',
	'option_methode_nocache' => 'Disabled (nocache)',
	'option_methode_xcache' => 'XCache'
);

?>
