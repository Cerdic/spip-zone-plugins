<?php
/**
 * Options du plugin Césure
 *
 * @plugin     Césure
 * @copyright  2016
 * @author     Michel @ Vertige ASBL
 * @licence    GNU/GPL
 */

define('_PB_HYPHEN', '&#173;');

$p = explode(
	basename(_DIR_PLUGINS).'/',
	str_replace(
		'\\',
		'/',
		realpath(dirname(__FILE__))
	)
);

/* Cette constante est définie automatiquement dans le cache (constaté sur 3.1) */
if (! defined('_DIR_PLUGIN_PB_CESURE')) {
	define('_DIR_PLUGIN_PB_CESURE', (_DIR_PLUGINS . end($p)));
}
define('_PB_PATH_TO_PATTERNS', _DIR_PLUGIN_PB_CESURE. 'patterns/');
define('_PB_DICTIONARY', 'dictionary.txt');
define('_PB_EXCLUDE_TAGS', 'code,pre,script,style,pbperso');
