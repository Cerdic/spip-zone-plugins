<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
if (!defined("_ECRIRE_INC_VERSION")) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(
	// C
	'cfg_titre' => 'Memoization',
	'cfg_description' => 'Memoization. This configuration page is reserved for the webmaster.',

	// E
	'explication_memcache_servers' => 'indicate a server line, as <kbd>host:port</kbd>',

	// L
	'label_cache_pages' => 'Manage the page cache',
	'label_methode' => 'Select the memoization mode',
	'label_memcache_serveurs' => 'Memcache server(s) :',
	'legend_cache_methode' => 'Memoization method',
	'legend_cache_pages' => 'Page cache',
	'legend_memcache' => 'Memcache settings',
	'lien_administration_memcache' => 'Manage memcache',

	// O
	'option_cache_defaut' => 'By the memoization method defined above',
	'option_cache_file' => 'Old method of the core',
	'option_cache_filepage' => 'By filecache memoization',
	'option_methode_apc' => 'APC',
	'option_methode_defaut' => 'Auto-detection',
	'option_methode_eaccelerator' => 'EAccelerator',
	'option_methode_filecache' => 'Files (filecache)',
	'option_methode_memcache' => 'Memcache',
	'option_methode_nocache' => 'Disabled (nocache)',
	'option_methode_xcache' => 'XCache',

);

?>