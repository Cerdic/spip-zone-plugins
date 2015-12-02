<?php
/**
 * Options au chargement du plugin Composer
 *
 * @plugin     Composer
 * @copyright  2015
 * @author     Matthieu Marcillaud
 * @licence    GNU/GPL
 * @package    SPIP\Composer\Options
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

if (!defined('_DIR_COMPOSER')) {
	/** @var string Chemin où sera chargé différents éléments pour Composer (dans config/). */
	define('_DIR_COMPOSER', _DIR_ETC);
}

if (!defined('_FILE_COMPOSER_JSON')) {
	/** @var string Chemin où est généré le json de Composer */
	define('_FILE_COMPOSER_JSON', _DIR_COMPOSER . 'composer.json');
}

if (!defined('_ROOT_VENDOR')) {
	/** @var string
	 *     Chemin complet où seront stockés les packages.
	 *     On met un chemin absolu car composer.json ne sera pas forcément
	 *     dans le répertoire parent (mais probablement dans config/).
	 */
	define('_ROOT_VENDOR', _ROOT_RACINE . 'vendor/');
}


// Générer ou régénérer le composer.json
if (test_espace_prive() and in_array(_request('exec'), array('composer', 'admin_plugin'))) {
	include_spip('inc/autoriser');
	if (autoriser('modifier', 'composerjson')) {
		include_spip('inc/composer_php');
		composer_generer_json();
	}
}

// Intégrer l'autoloader s'il est présent
// il faudrait pouvoir l'activer plus tôt aussi, mais bon…
if (file_exists(_ROOT_VENDOR . 'autoload.php')) {
	include_once(_ROOT_VENDOR . 'autoload.php');
}
