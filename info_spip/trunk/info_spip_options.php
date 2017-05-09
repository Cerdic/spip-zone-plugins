<?php
/**
 * Options du plugin Info SPIP au chargement
 *
 * @plugin     Info SPIP
 * @copyright  2013-2017
 * @author     Teddy Payet
 * @licence    GNU/GPL
 * @package    SPIP\Info_SPIP\Options
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

include_spip('inc/config');
include_spip('inc/meta');

$config = lire_config('info_spip');

if (isset($config['type_site']) and intval($config['type_site']) != 0) {
	switch ($config['type_site']) {
		case 'dev':
			$config['type_site'] = '02dev';
			break;
		case 'rec':
			$config['type_site'] = '05rec';
			break;
		case 'prep':
			$config['type_site'] = '06prep';
			break;
		case 'prod':
			$config['type_site'] = '07prod';
			break;
		default:
	}
	ecrire_config('info_spip', serialize($config));
}

?>
